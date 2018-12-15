<?php
# 大神认证审核成功

namespace app\lib\jiguang\payload;


class TalentAuditFail extends abstractPushPayload
{
    protected $type = 402;
    protected $content = '您的大神申请已经被拒绝，请详细阅读认证条件，有异议可咨询客服。';

    public function init($user) {
        $this->saveToSysMessage($user);
        if($user->reg_id) {
            $this->addAlias($user->uid);
        }
    }
}