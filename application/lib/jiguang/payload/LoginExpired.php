<?php
# 登录失效

namespace app\lib\jiguang\payload;


class LoginExpired extends abstractPushPayload
{
    protected $type = 0;
    protected $content = '登录已失效!';

    public function init($user) {
        if($user->reg_id && $user->is_online) {
            $this->addAlias($user->uid);
        }
    }
}