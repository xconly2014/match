<?php
# 提现

namespace app\lib\jiguang\payload;


class Withdraw extends abstractPushPayload
{
    protected $type = 513;

    public function init($income, $price) {
        $user= $income->user;
        $content = '您的个人收益提现成功，此次提现金额为'.$price.'元';
        $extras = [
            'price' => $price
        ];
        $this->setContent($content)
            ->setExtras($extras);
        $this->saveToSysMessage($user, null, $extras + [
                'order_id' => $income->trade_no,
            ]);
        if($user->reg_id) {
            $this->addAlias( $user->uid );
        }
    }
}