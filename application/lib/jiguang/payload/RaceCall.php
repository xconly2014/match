<?php
# 老司机抢单

namespace app\lib\jiguang\payload;


class RaceCall extends abstractPushPayload
{
    protected $type = 202;
    protected $content = '有一条新的接单';

    public function init($call, $user, $price) {
        $talent = $user->talent;
        $talent->setRelation('user', $user);
        $extras =$talent->l()->singleData($talent);
        $extras['price'] = $price;

        $this->addAlias( $call->user->uid )
            ->setExtras($extras);
    }
}