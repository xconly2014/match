<?php
# User.php

namespace app\manage\controller;


class User extends AbstractController
{
    public function detail($uid) {
        $user = $this->m('user')->with('talent')->where('uid', $uid)->find();

        $user->last_login_timer = $user->last_login_timer ? date('Y-m-d H:i:s', $user->last_login_timer) : '';
        $user->last_logout_timer = $user->last_logout_timer ? date('Y-m-d H:i:s', $user->last_logout_timer) : '';
        $user->register = $user->register ? date('Y-m-d H:i:s', $user->register) : '';

        if( $user->talent ) {
            $user->talent->tag = $this->l('talent')->getTagsInfo($user->talent);
        }

        return $this->fetch('', [
            'user' => $user
        ]);
    }
}