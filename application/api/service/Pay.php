<?php
# Balance.php
# 钱包账户


namespace app\api\service;

use app\lib\exception\BaseException;
use app\lib\exception\ForbiddenException;
use app\lib\exception\NotFoundException;
use app\lib\exception\ParameterException;
use think\Config;

class Pay extends AbstractServiceModel
{
    /**
     * 余额支付
     */
    public function balance()
    {
        $user = $this->getUser();
        $order_id = $this->request->param('order_id');

        $order_prefix = substr($order_id, 0, 1);
        if ($order_prefix == 'O') { // 支付订单
            $order = $this->l('order')->getOrder();
            $this->l('order')->chkOrderUserId($order, $user);
            $balance = $this->l('bill')->balancePayForOrder($user, $order);
            if ($balance && $balance->id) {
                $this->l('bill')->orderPaySuccess($order, 3, $balance->id);
            } else {
                throw new BaseException(['msg' => '数据更新失败']);
            }
        } elseif ($order_prefix == 'T') { // 支付大神认证申请
            $talOrder = $this->m('talOrder')->where('order_id', $order_id)->find();
            $balance = $this->l('bill')->balancePayForTalOrder($user, $talOrder);
            if ($balance && $balance->id) {
                $talOrder->pay_type = 3;
                $talOrder->trade_no = $balance->id;
                $talOrder->save();
                $talOrder->talent->pay_flag = 1;
                $talOrder->talent->save();
            }
        } else {
            throw new ForbiddenException(['msg' => '错误的订单号']);
        }
    }

    /**
     * 提现申请
     */
    public function wd()
    {
        $user = $this->getUser();
        $amount = $this->request->param('amount');

//        if(!$user->wdAccount) {
//            throw new ForbiddenException(['msg'=>'未绑定提现账号']);
//        }
/*        $today = strtotime(date("Ymd"));
        $tomorrow = strtotime(date("Ymd",strtotime("+1 day")));
        $total = $this->m('incOrder')
            ->where('user_id', $user->id)
            ->where('auth_status',2)
            ->where('create_time','>=',$today)
            ->where('create_time','<=',$tomorrow)
            ->sum('settle_amount');
        if($total > 20000){
            throw new ParameterException(['msg' => '单日提现不得超出200元.']);
        }*/
        $exist = $this->m('incOrder')
            ->where('user_id', $user->id)
            ->where('auth_status', '<=', 1)
            ->count();
        if ($exist) {
            throw new ParameterException(['msg' => '不可重复申请提现，请等待上次提现申请完成']);
        }

        if (!$this->l('user')->applyWithdrawAble($user, $amount)) {
            throw new ParameterException(['msg' => '申请失败，余额不足']);
        }
        $incOrder = $this->m('IncOrder');
        $incOrder->data([
            'user_id' => $user->id,
            'apply_amount' => $amount * 100,
        ]);
        $incOrder->save();
        return $incOrder;
    }

    /**
     *  取消提现
     */
    public function wdRefused()
    {
        $user = $this->getUser();
        $amount = $this->request->param('amount');
        $incOrder = $this->m('incOrder')->where('user_id', $user->id)->where('auth_status', '<=', 1)->find();
        $incOrder->auth_status = 3;
        $incOrder->reject_reason = '用户取消提现';
        $incOrder->save();
        $user->income -= $amount;
    }

    /**
     * 完成提现
     */
    public function wdPass()
    {
        $user = $this->getUser();
        $amount = $this->request->param('amount');
        $incOrder = $this->m('incOrder')->where('user_id', $user->id)->where('auth_status', '<=', 1)->find();
//        $amount = $amount * 100;
        if (!$incOrder || $incOrder->apply_amount <> $amount * 100) {
            throw new ParameterException(['msg' => '请求失败']);
        }
        if ($incOrder->user->income < $amount) {
            throw new ParameterException(['msg' => '申请失败，余额不足']);
        }
        if (!$this->l('bill')->withdraw($incOrder, $amount * 100)) {
            throw new ParameterException(['msg' => '数据更新失败']);
        }
        $config = Config::get('developer');
        $price = $config['isReal'] == 1 ? $amount * 100 : $amount;
        $wxCallBack = $this->l('bill')->wxWithDrawParam($incOrder->order_id, $price, $user->wxid);
        if ($wxCallBack && $wxCallBack['result_code'] == 'SUCCESS') {
            $incOrder->auth_status = 2;
            $incOrder->trade_no = $wxCallBack['payment_no'];
            $incOrder->auth_time = time();
            $incOrder->settle_amount += $amount * 100;
            $incOrder->cur_income = $incOrder->user->income * 100;
            $incOrder->save();
            $user->income -= $amount;
        }
        return $this->l('user')->baseInfo($user);
    }

    /**
     * 微信统一下单参数
     */
    public function wxPay()
    {
        $order = $this->l('order')->getOrder();
        $this->l('order')->chkIsPayed($order);
        $device_info = $this->request->param('device_info');
        $spbill_create_ip = $this->request->param('spbill_create_ip');
        return $this->l('bill')->wxPayParam($order->order_id, $order->price, $device_info, $spbill_create_ip);
    }

    /**
     *  微信小程序下单参数
     */
    public function wxPayMP()
    {
        $order = $this->l('order')->getOrder();
        $this->l('order')->chkIsPayed($order);
        $device_info = $this->request->param('device_info');
        $spbill_create_ip = $this->request->param('spbill_create_ip');
        $open_id = $order->user->wxid;
        return $this->l('bill')->wxPayParamMP($order->order_id, $order->price, $device_info, $spbill_create_ip, $open_id, $open_id);
    }

    /**
     * 支付宝统一下单参数
     */
    public function aliPay()
    {
        $order = $this->l('order')->getOrder();
        $this->l('order')->chkIsPayed($order);
        return $this->l('bill')->alipayParam($order->order_id, $order->price);
    }

    /**
     * 余额充值
     */
    public function balanceTop()
    {
        $gold_num = $this->request->param('gold_num');
        $user = $this->getUser();
        $price = $this->l('bill')->calcPrice($gold_num);
        $attach = urlencode(json_encode(['pay_for' => 'balance_top', 'uid' => $user->uid, 'gold_num' => $gold_num]));

        # 保存充值记录
        $balOrder = $this->m('balOrder');
        $balOrder->data([
            'user_id' => $user->id,
            'price' => $price,
            'gold_num' => $gold_num
        ])->save();
        $out_trade_no = $balOrder->order_id;

        # 微信参数
        $device_info = $this->request->param('device_info');
        $spbill_create_ip = $this->request->param('spbill_create_ip');
        $wx_param = $this->l('bill')->wxPayParam($out_trade_no, $price, $device_info, $spbill_create_ip, $attach);
        # 支付宝参数
        $ali_param = $this->l('bill')->alipayParam($out_trade_no, $price, $attach);

        $data = [
            'price' => $price,
            'order_id' => $out_trade_no,
            'wx_param' => $wx_param,
            'ali_param' => $ali_param,
        ];
        return $data;
    }

    /**
     * 余额充值完成， （查询订单）
     */
    public function balanceTopEnd()
    {
        $out_trade_no = $this->request->param('order_id');
        if (substr($out_trade_no, 0, 1) == 'B') {
            $type = $this->request->param('pay_type');
            $pay_info = $this->l('bill')->queryPay($out_trade_no, $type);
            if ($pay_info['type'] > 0) {
                $this->l('bill')->balanceTopSuccess($pay_info['type'], $out_trade_no, $pay_info['trade_no']);
            }
        } else {
            throw new ForbiddenException(['msg' => '错误的订单好']);
        }
    }

    /**
     * 收益提现记录
     */
    public function incWdList()
    {
        $user = $this->getUser();
        $param = $this->request->param();
        $since_id = $param['fresh_type'] == 1 ? $param['since_id'] : 0;
        $list = $this->m('incOrder')->getListByUser($user, $since_id);
        $data['list'] = [];
        foreach ($list as $item) {
            $since_id = $item->id;
            $data['list'][] = [
                'order_id' => $item->order_id,
                'inc_o_auth_status' => $item->getData('auth_status'),
                'reject_reason' => $item->reject_reason ? $item->reject_reason : '',
                'apply_amount' => $item->apply_amount / 100,
                'settle_amount' => $item->settle_amount / 100,
                'cur_income' => $item->cur_income,
                'create_time' => $item->create_time
            ];
        }
        $data['count'] = count($data['list']);
        $data['since_id'] = $since_id;
        return $data;
    }

    /**
     * 大神认证费
     */
    public function cert()
    {
        $user = $this->getUser();
        $attach = urlencode(json_encode(['pay_for' => 'talent_cert_fee', 'uid' => $user->uid]));

        if (!$user->talent) {
            throw new NotFoundException(['msg' => '未发现大神申请资料']);
        }
        $talent = $user->talent;
        if ($talent->getData('auth_status') != 1) {
            throw new ForbiddenException(['msg' => '状态错误']);
        }
        $talOrder = $talent->talOrder;
        if (!$talOrder) {
            $price = 10;

            # 保存充值记录
            $talOrder = $this->m('talOrder');
            $talOrder->data([
                'talent_id' => $talent->id,
                'price' => $price,
            ])->save();
        } else {
            if ($talOrder->getData('pay_type') > 0) {
                throw new ForbiddenException(['msg' => '已支付或未退款']);
            }
        }

        $out_trade_no = $talOrder->order_id;

        # 微信参数
        $device_info = $this->request->param('device_info');
        $spbill_create_ip = $this->request->param('spbill_create_ip');
        $wx_param = $this->l('bill')->wxPayParam($out_trade_no, $talOrder->price, $device_info, $spbill_create_ip, $attach);
        # 支付宝参数
        $ali_param = $this->l('bill')->alipayParam($out_trade_no, $talOrder->price, $attach);

        $data = [
            'price' => $talOrder->price,
            'order_id' => $out_trade_no,
            'wx_param' => $wx_param,
            'ali_param' => $ali_param,
        ];
        return $data;
    }
}