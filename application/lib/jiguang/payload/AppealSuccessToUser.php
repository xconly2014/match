<?php
# 申诉成功

namespace app\lib\jiguang\payload;


class AppealSuccessToUser extends abstractPushPayload
{
    protected $type = 301;

    public function init($order) {
        $user= $order->user;
        $content = '感谢您对“一起玩”环境的维护与贡献，您的申诉订单'.$order->order_id.'申诉审核已经通过，系统将于24小时内完成退款。';
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