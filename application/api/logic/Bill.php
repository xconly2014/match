<?php
/**
 * 个人账户 和 个人收益处理类
 */
namespace app\api\logic;

use app\api\model\AliNotify;
use app\api\model\Balance AS BalanceModel;
use app\api\model\Income AS IncomeModel;
use app\api\model\IncOrder;
use app\api\model\TalOrder;
use app\api\model\User AS UserModel;
use app\api\model\Order AS OrderModel;
use app\api\model\WxNotify;
use app\api\model\WxPrepay;
use app\lib\exception\BaseException;
use app\lib\exception\ForbiddenException;
use app\lib\exception\NotFoundException;
use app\lib\jiguang\JG;
use think\Config;
use wxpay\JsapiPay;
use wxpay\WxPayOrderOutput;
use wxpay\WxPayUnifiedOrderOutput;
use alipay\TradeAppPayParam;
use wxpay\WxWithDraw;

class Bill
{
    protected $_balance;

    protected $_income;

    protected $_jm;

    public function getBalance($data = []) {
        if(!$this->_balance) {
            $this->_balance = new BalanceModel($data);
        }
        return $this->_balance;
    }

    public function getIncome($data = []) {
        if(!$this->_income) {
            $this->_income = new IncomeModel($data);
        }
        return $this->_income;
    }

    /**
     * 增加 微信充值记录
     * @param integer $total_fee 单位分
     * @param $trade_no
     * @param $uid
     * @return bool
     */
    public function topUpByWx($total_fee, $trade_no, $uid) {
        $this->getBalance()->add('WX_TOP', $total_fee, $trade_no, $uid);
        return $this->getBalance()->id;
    }

    /**
     * 增加 支付宝充值
     * @param integer $total_fee 单位分
     * @param $trade_no
     * @param $uid
     * @return bool
     */
    public function topUpByAli($total_fee, $trade_no, $uid) {
        $this->getBalance()->add('ALI_TOP', $total_fee, $trade_no, $uid);
        return $this->getBalance()->id;
    }

    /**
     * 使用余额支付订单
     * @param UserModel $user
     * @param OrderModel $order
     * @return bool
     * @throws ForbiddenException
     */
    public function balancePayForOrder(UserModel $user, OrderModel $order) {
        if($order->getData('pay_type') > 0) {
            throw new ForbiddenException(['msg'=> '订单已支付']);
        }
        if($user->balance < $order->price) {
            throw new ForbiddenException(['msg'=> '余额不足!']);
        }

        $total_fee = 0 - $order->price * 100;
        $trade_no = $order->order_id;
        $balance = $this->getBalance()->add('ORDER_PAY', $total_fee, $trade_no, $user->uid);
        return $balance;
    }

    /**
     * 使用余额支付大神认证
     * @param UserModel $user
     * @param TalOrder $talOrder
     * @return mixed|string
     * @throws ForbiddenException
     */
    public function balancePayForTalOrder(UserModel $user, TalOrder $talOrder) {
        if($talOrder->getData('pay_type') > 0) {
            throw new ForbiddenException(['msg'=> '订单已支付']);
        }
        if($user->balance < $talOrder->price) {
            throw new ForbiddenException(['msg'=> '余额不足!']);
        }

        $total_fee = 0 - $talOrder->price * 100;
        $trade_no = $talOrder->order_id;

        $balance = $this->getBalance()->add('TAL_PAY', $total_fee, $trade_no, $user->uid);
        return $balance;
    }

    /**
     * 订单完成结算
     * @param OrderModel $order
     * @return null|integer
     */
    public function finishOrder(OrderModel $order) {
        if($order->getData('pay_type') > 0) {
            $income = $this->getIncome();
            $real_total_fee = $order->price * 100;
            $total_fee = (int) $real_total_fee * 0.8;
            $service_fee = $real_total_fee - $total_fee;
            $trade_no = $order->order_id;

            $income->add('ORDER_FINISH', $total_fee, $service_fee, $trade_no, $order->talent->user->uid);
            if($income->id) {
                $payload = JG::instance()->payload('incomeCollected');
                $payload->init($income);
                JG::instance()->push($payload);
            }
            return $income->id;
        }
        return true;
    }

    /**
     * 提现
     */
    public function withdraw(IncOrder $incOrder, $withdraw_fee) {
        $income = $this->getIncome()->add('WITHDRAW', 0 - $withdraw_fee, 0, $incOrder->order_id, $incOrder->user->uid);
//        if($income->id) {
//            $payload = JG::instance()->payload('withdraw');
//            $payload->init($income, $withdraw_fee/100);
//            JG::instance()->push($payload);
//        }
        return $income;
    }
    /**
     * 订单申诉， 大神被退款
     * @param OrderModel $order
     * @param integer $refund_fee 退款金额， 正数， 单位：分
     * @return bool|mixed|string
     */
    public function takeOutIncomeByAppeal(OrderModel $order, $refund_fee) {
        if($order->getData('pay_type') > 0) {
            $income = $this->getIncome()->add('APPEAL_REFUND', 0 - $refund_fee, 0, $order->order_id, $order->talent->user->uid);
            if($income && $income->id) {
                $payload = JG::instance()->payload('incomeRefund');
                $payload->init($income);
                JG::instance()->push($payload);
            }
            return $income;
        }
        return true;
    }
    /**
     * 订单取消结算
     * @param OrderModel $order
     * @return null|integer
     */
    public function cancelOrder(OrderModel $order) {
        if($order->getData('pay_type') > 0) {
            $total_fee = $order->price * 100;
            $trade_no = $order->order_id;
            $balance = $this->getBalance()->add('ORDER_REFUND', $total_fee, $trade_no, $order->user->uid);
            if($balance->id) {
                $payload = JG::instance()->payload('orderReturn');
                $payload->init($order, $total_fee/100);
                JG::instance()->push($payload);
            }
            return $balance;
        }
        return true;
    }

    /**
     * 大神审核失败退款
     * @param TalOrder $talOrder
     * @return BalanceModel|bool|false
     */
    public function cancelTalOrder(TalOrder $talOrder) {
        if($talOrder->getData('pay_type') > 0) {
            $total_fee = $talOrder->price * 100;
            $trade_no = $talOrder->order_id;
            $balance = $this->getBalance()->add('TAL_REFUND', $total_fee, $trade_no, $talOrder->talent->user);
            if($balance->id) {
                $payload = JG::instance()->payload('talReturn');
                $payload->init($talOrder, $total_fee/100);
                JG::instance()->push($payload);
            }
            return $balance;
        }
        return true;
    }


    /**
     * 计算充值需要费用
     * @param float $gold_num
     * @return float
     */
    public function calcPrice($gold_num) {
        if($gold_num < 7) {
            $relif = 0;
        } elseif($gold_num < 14) {
            $relif = 1;
        } elseif ($gold_num < 33) {
            $relif = 2;
        } elseif ($gold_num < 55) {
            $relif = 3;
        } elseif ($gold_num < 135) {
            $relif = 5;
        } elseif ($gold_num < 530) {
            $relif = 7;
        } else {
            $relif = 12;
        }

        return $gold_num - $relif;
    }

    /**
     * 获取微信支付参数
     * @param $out_trade_no
     * @param $price
     * @param $device_info
     * @param $spbill_create_ip
     * @param null $attach
     * @return mixed
     * @throws
     */
    public function wxPayParam($out_trade_no, $price, $device_info, $spbill_create_ip, $attach=null) {
        $spbill_create_ip || $spbill_create_ip = $_SERVER['REMOTE_ADDR'];
        $data = [
            'body' => '订单支付',
            'out_trade_no' => $out_trade_no,
            'total_fee' => $price * 100,
            'device_info' => $device_info,
            'spbill_create_ip' => $spbill_create_ip
        ];
        $device_info && $data['device_info'] = $device_info;
        $attach && $data['attach'] = $attach;
        try {
            $wxUO  = new WxPayUnifiedOrderOutput();
            $wxO = new WxPayOrderOutput();
            $pM = new WxPrepay();
            $prepay = $pM->getByOutTradeNo($out_trade_no);
            $prepay_id = $prepay ? $prepay->prepay_id : null;
            $wx_param = $wxUO->totalParams($data);
            $wx_param['pay_params'] = $wxO->totalParams($wxUO, $prepay_id);
            if(isset($wx_param['pay_params']['prepayid']) && $wx_param['pay_params']['prepayid'] != $prepay_id) {
                $pM->data([
                    'out_trade_no' => $out_trade_no,
                    'prepay_id' => $wx_param['pay_params']['prepayid'],
                ])->save();
            }
            $wx_param['pay_params']['_package'] = $wx_param['pay_params']['package']; // package 在安卓端语法冲突
            unset($wx_param['pay_params']['package']);

            unset($wx_param['appid']);
            unset($wx_param['mch_id']);
            unset($wx_param['pay_params']['appid']);
        } catch (\Exception $e) {
            throw new BaseException(['msg'=> $e->getMessage()]);
        }
        return $wx_param;
    }

    /**
     *  获取微信小程序支付参数
     * @param $out_trade_no
     * @param $price
     * @param $device_info
     * @param $spbill_create_ip
     * @param null $attach
     * @return array
     */
    public function wxPayParamMP($out_trade_no, $price, $device_info, $spbill_create_ip, $open_id,$attach=null) {
        $spbill_create_ip || $spbill_create_ip = $_SERVER['REMOTE_ADDR'];
        $config = Config::get('developer');
        $price = $config['isReal'] == 1 ? $price  : $price;
        $data = [
            'body' => '订单支付',
            'out_trade_no' => $out_trade_no,
            'total_fee' => $price,
            'device_info' => $device_info,
            'spbill_create_ip' => $spbill_create_ip
        ];
        $device_info && $data['device_info'] = $device_info;
        $attach && $data['attach'] = $attach;

        try{
            $wxPayJs = new JsapiPay();
            $wxParams = $wxPayJs->getParams($data,$open_id);
        }catch (\Exception $e){
            throw new BaseException(['msg'=> $e->getMessage()]);
        }
        return $wxParams;
    }

    /**
     * 微信提现
     * @param $out_trade_no
     * @param $price
     * @param $device_info
     * @param $spbill_create_ip
     * @param $open_id
     * @param null $attach
     * @return array
     * @throws BaseException
     */
    public function wxWithDrawParam($out_trade_no, $price, $open_id) {
        $spbill_create_ip = $_SERVER['REMOTE_ADDR'];
        $data = [
            'partner_trade_no' => $out_trade_no,
            'amount' => $price,
            'check_name' => 'NO_CHECK',
            'desc' => '提现',
            'spbill_create_ip' => $spbill_create_ip
        ];
        try{
            $wxPayJs = new WxWithDraw();
            $wxCallBack = $wxPayJs->getParams($data,$open_id);
        }catch (\Exception $e){
            throw new BaseException(['msg'=> $e->getMessage()]);
        }
        return $wxCallBack;
    }

    /**
     * 获取支付宝支付参数
     * @param $out_trade_no
     * @param $price
     * @param null $attach
     * @return mixed
     */
    public function alipayParam($out_trade_no, $price, $attach = null) {
        $ali = new TradeAppPayParam();
        $data = [
            'subject' => '订单支付',
            'out_trade_no' =>$out_trade_no,
            'total_amount' => round($price, 2),
        ];
        $attach && $data['passback_params'] = $attach;
        $ali_param = $ali->totalParams($data);
        unset($ali_param['app_id']);
        return $ali_param;
    }

    /**
     * 主动查询更新订单支付
     * @param $order
     * @param int $type
     * @return array
     */
    public function queryPayByOrder($order, $type=0) {
        if($order->getData('pay_type') > 0) {
            $result = [
                'type' => $order->getData('pay_type'),
                'trade_no' => $order->trade_no,
                'amount' => $order->price
            ];
        } else {
            $result = $this->queryPay($order->order_id, $type);
            if($result['type'] > 0) {
                $order->pay_type = $result['type'];
                $order->trade_no = $result['trade_no'];
                $order->save();
                //修改报名入场券的状态
//                $ticket = \app\api\model\UserTicket::get(['user_id'=>$order->user_id,'match_id'=>$order->match_id]);
//                $ticket->order_id = $result['trade_no'];
//                $ticket->status = 2;
//                $ticket->save();
//                $payload = JG::instance()->payload('orderPaySuccess');
//                $payload->init($order);
//                JG::instance()->push($payload);
            }
        }
        return $result;
    }

    /**
     * 主动查询更新大神认证经费订单支付
     * @param $talOrder
     * @param int $type
     * @return array
     */
    public function queryPayByTalOrder($talOrder, $type=0) {
        if($talOrder->getData('pay_type') > 0) {
            $result = [
                'type' => $talOrder->getData('pay_type'),
                'trade_no' => $talOrder->trade_no,
                'amount' => $talOrder->price
            ];
        } else {
            $result = $this->queryPay($talOrder->order_id, $type);
            if($result['type'] > 0) {
                $talOrder->pay_type = $result['type'];
                $talOrder->trade_no = $result['trade_no'];
                $talOrder->save();
            }
        }
        return $result;
    }

    /**
     * 主动查询支付订单3次
     * @param $out_trade_no
     * @param int $type 查询支付方式 0 全部， 1.微信， 2.支付宝
     * @return array
     */
    public function queryPay($out_trade_no, $type=0) {
        $result_type = 0;
        $result_trade_no = '';
        $result_amount = 0;  // 支付金额 单位： 元
        for($i=0; $i < 3; $i++) {
            # 查微信
            if($type == 0 || $type == 1) {
                try {
                    $wxInfo = \wxpay\Query::exec($out_trade_no);
                    if($wxInfo['trade_state'] == 'SUCCESS') {
                        $result_type = 1;
                        $result_trade_no = $wxInfo['transaction_id'];
                        $result_amount = $wxInfo['total_fee'] / 100;
                        $wxNotify = new WxNotify();
                        $wxNotify->data($wxInfo)->save();
                        break;
                    }
                } catch (\Exception $e) {
                    // pass
                }
            }

            # 查支付宝
            if($type == 0 || $type == 2) {
                try {
                    $query = new \alipay\Query();
                    $aliInfo = $query->query($out_trade_no);
                    if($aliInfo && $aliInfo['trade_status'] ==  'TRADE_SUCCESS') {
                        $result_type = 2;
                        $result_trade_no = $aliInfo['trade_no'];
                        $result_amount = $aliInfo['total_amount'];
                        $aliNotify = new AliNotify();
                        $aliNotify->data($aliInfo)->save();
                    }
                    if(isset($aliInfo['trade_status']) && $aliInfo['trade_status'] != 'WAIT_BUYER_PAY') { //订单存在，且不是等待买家付款
                        break;
                    }
                } catch(\Exception $e) {
                    // pass
                }
            }
            sleep(1);
        }
        return [
            'type' => $result_type,
            'trade_no' => $result_trade_no,
            'amount' => $result_amount
        ];
    }

    /**
     * 订单支付成功处理
     * @param integer $pay_type 支付方式 1.微信 。 2.支付宝
     * @param string $out_trade_no 本地订单号
     * @param string $trade_no 支付交易订单号
     * @throws NotFoundException
     * @return bool
     */
    public function orderPaySuccessByOutTradeNo($pay_type, $out_trade_no, $trade_no) {
        $prefix = substr($out_trade_no, 0 , 1);
        if($prefix != 'O') {
            return false;
        }
        // 本地检查订单
        $order = \app\api\model\Order::get(['order_id'=>$out_trade_no]);
        if(!$order) {
            throw new NotFoundException(['msg'=> '本地订单不存在']);
        }
        return $this->orderPaySuccess($order, $pay_type, $trade_no);
    }

    public function orderPaySuccess($order, $pay_type, $trade_no) {
        // 检查订单状态
        if( $order->getData('pay_type') == $pay_type) {
            return true;
        }
        // 订单状态未修改情况下, 进行处理业务
        $order->pay_type = $pay_type;
        $order->trade_no = $trade_no;
        $order->save();
        //修改报名入场券的状态
//        $ticket = \app\api\model\UserTicket::get(['user_id'=>$order->user_id,'match_id'=>$order->match_id]);
//        $ticket->order_id = $trade_no;
//        $ticket->status = 2;
//        $ticket->save();
        return true;
    }

    /**
     * 充值成功处理
     * @param integer $pay_type 支付方式 1.微信 。 2.支付宝
     * @param string $out_trade_no 本地订单号
     * @param string $trade_no 支付交易订单号
     * @throws NotFoundException
     * @return bool
     */
    public function balanceTopSuccess($pay_type, $out_trade_no, $trade_no) {
        $prefix = substr($out_trade_no, 0 , 1);
        if($prefix != 'B') {
            return false;
        }
        $balOrder = \app\api\model\BalOrder::get(['order_id'=>$out_trade_no]);
        // 本地检查订单
        if(!$balOrder) {
            throw new NotFoundException(['msg'=> '本地订单不存在']);
        }
        // 检查订单状态
        if( $balOrder->getData('pay_type') == $pay_type) {
            return true;
        }

        $balOrder->pay_type = $pay_type;
        $balOrder->trade_no = $trade_no;
        $balOrder->save();

        $bool = false;
        $gold_num = $balOrder->gold_num;
        if($pay_type == 1) {
            $bool = $this->topUpByWx($gold_num * 100, $out_trade_no, $balOrder->user->uid);
        } elseif($this->pay_type == 2) {
            $bool = $this->topUpByAli($gold_num * 100, $out_trade_no, $balOrder->user->uid);
        }
        if(!$bool) {
            $this->error_msg = '数据更新失败';
            return false;
        }
    }

    /**
     * 订单支付或充值成功
     * @param $pay_type
     * @param $out_trade_no
     * @param $trade_no
     * @throws
     */
    public function paySuccess($pay_type, $out_trade_no, $trade_no) {
        $prefix = substr($out_trade_no, 0, 1);
        if($prefix == 'O') {
            $this->orderPaySuccessByOutTradeNo($pay_type, $out_trade_no, $trade_no);
        } elseif($prefix == 'B') {
            $this->balanceTopSuccess($pay_type, $out_trade_no, $trade_no);
        } else {
            throw new NotFoundException(['msg'=>'未知的订单号']);
        }
    }
}