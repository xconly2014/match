<?php
# Access.php

namespace app\manage\controller;

use think\Session;
use app\lib\Log;
use think\Validate;
use think\Db;

class Access extends AbstractController
{
    public function login() {
        $post = $this->request->post();
        $v = new Validate([
            '_n' => 'require|alphaDash|min:4|max:63',
            '_p' => 'require|alphaDash|min:6|max:32',
        ]);

        $manager = null;
        if(isset($post['_n'])) {
            if($v->check($post)) {
                $post['_p'] = md5(md5($post['_p']). 'We');
                $manager = Db::table('dl_manager')
                    ->where('username', $post['_n'])
                    ->find();
                $post['re_p'] = $manager ? $manager['password'] : '';
                $v->rule('re_p', 'length:32|confirm:_p');
                $v->message('re_p.length', '管理员不存在或账号密码错误！');
                $v->message('re_p.confirm', '管理员不存在或账号密码错误');
                if($v->check($post)) {
                    Db::table('dl_manager')
                        ->where('id', $manager['id'])
                        ->update([
                        'last_login_time' => time(),
                        'last_login_ip' => ip2long( $this->request->ip() ),
                    ]);

                    Session::set('user', $manager);
                    Log::info('后台登录成功');
                    $url = ltrim($this->request->param('next'), "/");
                    $url = '/' . ( $url ? urldecode($url) : 'manage' );
                    $this->success('登录成功', $url);
                }
            }
        }
        return $this->fetch('', [
            'error' => $v->getError()
        ]);
    }

    public function logout() {
        Session::delete('user');
        return redirect('/manage/login');
    }
}