<?php
# 收益增加

namespace app\lib\jiguang\payload;


class IncomeCollected extends abstractPushPayload
{
    protected $type = 511;

    public function init($income) {
        $user= $income->user;
        $price = ($income->total_fee/100);
        $content = '您的订单已经完成，佣金'. $price .'元已打入您的账户。点击前往查看';
        $extras = [
            'order_id' => $income->trade_no,
            'price' => $price
        ];

        $this->setExtras($extras)
            ->setContent($content);
        $this->saveToSysMessage($user);
        if($user->reg_id) {
            $this->addAlias( $user->uid );
        }
    }
}