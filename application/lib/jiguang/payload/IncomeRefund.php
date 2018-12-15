<?php
# 订单申诉， 大神被退款

namespace app\lib\jiguang\payload;


class IncomeRefund extends abstractPushPayload
{
    protected $type = 512;

    public function init($income) {
        $user= $income->user;
        $price = ($income->total_fee/100);
        $content = '您的佣金因申诉处理被扣除金额'.$price;
        $extras = [
            'order_id' => $income->trade_no,
            'price' => $price
        ];
        $this->setContent($content)
            ->setExtras($extras);
        $this->saveToSysMessage($user);
        if($user->reg_id) {
            $this->addAlias( $user->uid );
        }
    }
}