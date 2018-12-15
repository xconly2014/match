<?php
# Balance.php

namespace app\api\model;

use app\lib\exception\BaseException;
use think\Db;

class Income extends AbstractModel
{
    // 自动设置 create_time
    protected $autoWriteTimestamp = true;

    protected $updateTime = false;

    public function user()
    {
        return $this->belongsTo('user', 'user_id');
    }

    public function getTradeTypeAttr($value) {
        $tuple = [
            'ORDER_FINISH' => '订单完成',
            'WITHDRAW' => '提现',
            'APPEAL_REFUND' => '申诉退款',
        ];
        return isset( $tuple[$value] ) ? $tuple[$value] : $value;
    }

    /**
     * @param $trade_type
     * @param integer $total_fee 单位： 分
     * @param integer $service_fee 手续费 单位： 分
     * @param $trade_no
     * @param $uid
     * @return false|income
     * @throws
     */
    public function add($trade_type, $total_fee, $service_fee, $trade_no, $uid) {
        Db::startTrans();
        try {
            $exist = $this->where('trade_no',$trade_no)
                ->where('trade_type', $trade_type)
                ->find();
            if($exist)  {
                Db::commit();
                return $exist;
            }

            $user = User::get(['uid'=> $uid]);
            $cur_income = $user->income * 100 + $total_fee;
            if($cur_income < 0) {
                throw new BaseException( ['msg'=>'余额不足'] );
            }

            $data = [
                'user_id' => $user->id,
                'trade_type' => $trade_type,
                'total_fee' => $total_fee,
                'service_fee' => $service_fee,
                'cur_income' => $cur_income,
                'trade_no' => $trade_no,
            ];
            $this->data($data)->save();
            $affect = $user->updateIncome($cur_income/100);
            if(!$affect) {
                throw new BaseException( ['msg'=>'USER数据已更改'] );
            }

            $user->save();
            Db::commit();
            return $this;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            throw $e;
        }
    }
}