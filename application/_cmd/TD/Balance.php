<?php
# Pay.php
# 生成 微信或支付宝充值记录

namespace app\_cmd\TD;

use app\api\model\Balance AS BalanceModel;
use app\api\model\User AS UserModel;
use app\api\model\WxNotify;
use app\api\model\AliNotify;

class Balance extends AbstractEntity
{
    protected $b_t_t = [ 'ORDER_PAY', 'ORDER_REFUND', 'WX_TOP', 'ALI_TOP' ];

    protected $wx_rows;
    protected $ali_rows;
    protected $bal_rows;
    protected $balance = [];
    protected $uids = [];

    public function run($length)
    {
        $this->wx_rows = $this->ali_rows =$this->bal_rows = $this->balance = $this->uids = [];
        for($i=0; $i<$length; $i++) {
            $user_id = $this->getUserId();
            if($user_id) {
                $this->balancePay($user_id, $i); // 充值
            }
        }
        if( !empty($this->bal_rows) ) {
            $balModel = new BalanceModel;
            $balModel->saveAll($this->bal_rows);
            $this->output('Insert Balance: ' . count($this->bal_rows));
        }
        if( !empty($this->wx_rows) ) {
            $model = new WxNotify();
            $model->saveAll($this->wx_rows);
            $this->output('Insert Wx: ' . count($this->wx_rows));
        }
        if( !empty($this->ali_rows) ) {
            $model = new AliNotify();
            $model->saveAll($this->ali_rows);
            $this->output('Insert Ali: ' . count($this->ali_rows));
        }

        if( !empty($this->balance)) {
            foreach($this->balance as $user_id => $balance) {
                $uModel = new UserModel;
                $uModel->where('id', $user_id)->update(['balance'=>$balance]);
            }
            $this->output('Update User Balance');
        }

    }

    protected function balancePay($user_id, $i) {
        $time = time();
        $order_id = 'BT'.date('mdHis').str_pad(($i%10000), 4, '0', STR_PAD_LEFT);
        $price = mt_rand(100, 1000) * 10;
        isset($this->balance[ $user_id ]) || $this->balance[ $user_id ]=0;
        $this->balance[ $user_id ] += $price;
        $attach = json_encode(['pay_for' => 'balance_top', 'uid'=>$user_id, 'gold_num'=>$price]);

        $trade_type = $this->b_t_t[ mt_rand(2, 3) ];
        if($trade_type == 'WX_TOP') {
            $this->wx_rows[] = [
                'out_trade_no' => $order_id,
                'total_fee' => $price * 100,
                'attach' => $attach,
                'create_time' => $time,
            ];
        } else {
            $this->ali_rows[] = [
                'out_trade_no' => $order_id,
                'total_amount' => $price,
                'passback_params' => $attach,
            ];
        }
        $this->bal_rows[] = [
            'user_id'=> $user_id,
            'trade_type' => $trade_type,
            'total_fee' => $price * 100,
            'cur_balance' => $this->balance[ $user_id ] * 100,
            'trade_no' => $order_id,
            'create_time' => $time
        ];

    }

    protected function getUserId() {
        if(empty($this->uids)) {
            $model = new UserModel;
            $rows = $model->field('id')->where('balance', 0)->select();
            if($rows) {
                foreach($rows as $row) {
                    $this->uids[] = $row->id;
                }
            }
        }
        if(empty($this->uids) ) {
            return null;
        } else {
            return $this->uids[ array_rand($this->uids) ];
        }
    }
}