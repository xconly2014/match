<?php
# 呼叫老司机

namespace app\lib\jiguang\payload;


class CallTalent extends abstractPushPayload
{
    protected $type = 201;
    protected $content = '有一条新的用户呼叫';

    public function init($user, $call)
    {
        $extras = [
            'id' => $call->id,
            'user_id' => $user->uid,
            'icon' => $user->avatar ? $user->avatar : ($user->icon ? $user->icon : ''),
            'nick' => $user->nick,
            'sex' => $user->sex,
            'age' => $user->age,
            'max_level' => $call->max_level,
            'area' => $call->area,
            'system' => $call->system,
            'round' => $call->round,
            'create_time' => $call->create_time
        ];
        $this->setExtras($extras);

        $tag_level = [];
        for ($i = $call->getData('max_level'); $i <= 7; $i++) {
            $tag_level[] = 'T_MAX_LEVEL_' . $i;
        }
        if (!empty($tag_level)) {
            $this->addTag($tag_level);
        }
        if ($call->getData('area') > 0) {
            $this->addTagAnd('T_AREA_' . $call->getData('area'));
        }
        if ($call->getData('system') > 0) {
            $this->addTagAnd('T_SYSTEM_' . $call->getData('system'));
        }

        $this->addTagAnd('T_SWITCH_ON');
    }
}