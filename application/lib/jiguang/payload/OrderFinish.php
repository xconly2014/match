<?php
# 订单完成

namespace app\lib\jiguang\payload;


class OrderFinish extends abstractPushPayload
{
    protected $type = 1002;

    public function init($order) {
        $user= $order->user;
        $content = '您的订单'.$order->order_id.'已完成，请给大神评价。';
        $extras = [
            'order_id' => $order->order_id
        ];
        $this->setContent($content)
            ->setExtras($extras);
        $this->saveToOrderMessage($user, $order);
        if($user->reg_id) {
            $this->addAlias( $user->uid );
        }
    }
}