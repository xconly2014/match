<?php
# 申诉失败

namespace app\lib\jiguang\payload;


class AppealFailToTalent extends abstractPushPayload
{
    protected $type = 312;

    public function init($order) {
        $talent= $order->talent;
        $content = '订单'. $order->order_id .'申诉完成，经审核认证您的接单服务不存在问题，别让不愉快影响接单的心情请再接再厉。点击前往查看';
        $extras = [
            'order_id' => $order->order_id,
        ];

        $this->setContent($content)->setExtras($extras);
        $this->saveToOrderMessage($talent->user, $order);
        if($talent->user->reg_id) {
            $this->addAlias( $talent->user->uid );
        }
    }
}