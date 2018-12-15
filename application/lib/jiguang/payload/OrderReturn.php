<?php
# 退款,返还到个人账户推送 （订单取消 | 订单申诉后被取消 ）

namespace app\lib\jiguang\payload;


class OrderReturn extends abstractPushPayload
{
    protected $type = 501;

    public function init($order, $refund_amount) {
        $user= $order->user;
        $refund_amount = round($refund_amount, 2);
        $content = '您有'.$refund_amount.'元退款金额已放入钱包，请查收。点击前往查看';
        $extras = [
            'order_id' => $order->order_id,
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