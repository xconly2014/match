<?php
# 申诉成功

namespace app\lib\jiguang\payload;


class AppealSuccessToTalent extends abstractPushPayload
{
    protected $type = 311;

    public function init($order) {
        $talent= $order->talent;
        $content = '订单'.$order->order_id.'申诉完成，经审核认证您的接单服务存在问题，停止接单1天，请提高您的服务态度。点击前往查看';
        $extras = [
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