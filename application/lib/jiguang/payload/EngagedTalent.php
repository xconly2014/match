<?php
# 大神被约

namespace app\lib\jiguang\payload;


class EngagedTalent extends abstractPushPayload
{
    protected $type = 203;

    public function init($order) {
        $user = $order->user;
        $talent = $order->talent;
        $content = $user->nick . '已向您发起订单邀请。点击前往查看';
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
        $this->saveToSysMessage($talent->user);

        if($talent->user->reg_id) {
            $this->addAlias( $talent->user->uid );
        }
    }
}