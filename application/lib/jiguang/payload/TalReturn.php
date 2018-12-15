<?php
# 退款,大神审核经费退款

namespace app\lib\jiguang\payload;


class TalReturn extends abstractPushPayload
{
    protected $type = 502;

    public function init($talOrder, $refund_amount) {
        $user= $talOrder->talent->user;
        $refund_amount = round($refund_amount, 2);
        $content = '您有'.$refund_amount.'元退款金额已放入钱包，请查收。点击前往查看';
        $extras = [
            'order_id' => $talOrder->order_id,
            'price' => $refund_amount
        ];
        $this->setContent($content)
            ->setExtras($extras);
        $this->saveToSysMessage($user);

        if($user->reg_id) {
            $this->addAlias( $user->uid );
        }
    }
}