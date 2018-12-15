<?php
# 大神认证审核成功

namespace app\lib\jiguang\payload;


class TalentAuditSuccess extends abstractPushPayload
{
    protected $type = 401;
    protected $content = '恭喜您已经通过认证审核，可以愉快的去接单啦！';

    public function init($user) {
        $this->saveToSysMessage($user);
        if($user->reg_id) {
            $this->addAlias($user->uid);
        }
    }
}