<?php
# Balance.php

namespace app\api\model;

use think\Db;

class Balance extends AbstractModel
{
    // 自动设置 create_time
    protected $autoWriteTimestamp = true;

    protected $updateTime = false;

    /**
     * @var User
     */
    protected $_user;

    public function user()
    {
        return $this->belongsTo('user', 'user_id');
    }

    public function getTradeTypeAttr($value) {
        $tuple = [
            'ORDER_PAY' => '支付订单',
            'ORDER_REFUND' => '订单退款',
            'WX_TOP' => '微信充值',
            'ALI_TOP' => '支付宝充值',
            'TAL_PAY' => '大神认证支付',
            'TAL_REFUND' => '大神认证退款',
        ];
        return isset( $tuple[$value] ) ? $tuple[$value] : $value;
    }

    /**
     * @param $trade_type
     * @param integer $total_fee 单位： 分
     * @param $trade_no
     * @param $uid
     * @return false|balance
     */
    public function add($trade_type, $total_fee, $trade_no, $uid) {
        Db::startTrans();
        try {
            $exist = $this->where('trade_no',$trade_no)
                ->where('trade_type', $trade_type)
                ->find();
            if($exist)  {
                Db::commit();
                return $exist;
            }

            if($uid instanceof User) {
                $user = $uid;
            } else {
                $user = User::get(['uid'=> $uid]);
            }

            $cur_balance = $user->balance * 100 + $total_fee;
            if($cur_balance < 0) {
                throw new \Exception('余额不足');
            }

            $data = [
                'user_id' => $user->id,
                'trade_type' => $trade_type,
                'total_fee' => $total_fee,
                'cur_balance' => $cur_balance,
                'trade_no' => $trade_no,
            ];
            $this->data($data)->save();
            $affect = $user->uodateBalance($cur_balance/100);
            if(!$affect) {
                throw new \Exception('USER数据已更改');
            }

            $user->save();
            Db::commit();
            return $this;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
        return false;
    }
}