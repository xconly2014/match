<?php
# 创建订单支付成功

namespace app\lib\jiguang\payload;

class OrderPaySuccess extends abstractPushPayload
{
    protected $type = 1001;

    public function init($order) {
        $user= $order->user;
        $content = '感谢使用“一起玩”，您已经成功下单，订单号：'.$order->order_id.'，价格：'.$order->price.'元，整等待大神抢单。';
        $extras = [
            'order_id' => $order->order_id,
            'price' => $order->price
        ];
        $this->setContent($content)
            ->setExtras($extras);
        $this->saveToOrderMessage($user, $order);
        if($user->reg_id) {
            $this->addAlias( $user->uid );
        }
    }
}