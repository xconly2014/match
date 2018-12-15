<?php
# 订单取消

namespace app\lib\jiguang\payload;


class OrderCancel extends abstractPushPayload
{
    protected $type = 1003;

    public function init($order) {
        $user= $order->user;
        $content = '您的订单'.$order->order_id.'已取消，系统将在24小时内完成退款。';
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