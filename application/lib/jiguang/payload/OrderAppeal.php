<?php
# 用户发起申诉

namespace app\lib\jiguang\payload;


class OrderAppeal extends abstractPushPayload
{
    protected $type = 300;

    public function init($order) {
        $user = $order->user;
        $talent = $order->talent;
        $content = '订单'. $order->order_id .'提交了申诉，审核结果于3天内完成，对错自有正义。点击前往查看';
        $extras = [
            'user_id' => $user->uid,
            'icon' => $user->avatar ? $user->avatar : ($user->icon ? $user->icon : ''),
            'nick' => $user->nick,
            'sex' => $user->sex,
            'age' => $user->age,
            'order_id' => $order->order_id,
        ];
        $this->setContent($content)
            ->setExtras($extras);
        $this->saveToOrderMessage($talent->user, $order);
        if($talent->user->reg_id) {
            $this->addAlias( $talent->user->uid );
        }
    }
}