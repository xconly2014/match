<?php
# 申诉失败

namespace app\lib\jiguang\payload;


class AppealFailToUser extends abstractPushPayload
{
    protected $type = 302;

    public function init($order) {
        $user= $order->user;
        $content = '您的申诉订单'. $order->order_id .'审核没有通过，我们将持续跟踪次订单情况，若有异议可以联系客服反馈。';
        $extras = [
            'order_id' => $order->order_id,
        ];
        $this->setContent($content)
            ->setExtras($extras);
        $this->saveToOrderMessage($user, $order);

        if($user->reg_id) {
            $this->addAlias( $user->uid );
        }
    }
}