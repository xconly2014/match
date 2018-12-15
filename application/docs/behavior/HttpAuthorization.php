<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/11
 * Time: 14:24
 */

namespace app\docs\behavior;

use think\Request;
use think\Session;

class HttpAuthorization
{
    /*
     * 行为 校验登录
     */
    public function run(Request &$request) {
        $this->chkSession($request);
        return true;
    }

    protected function chkSession(Request $request) {
        if ($request->module() == 'docs' && $request->action() != 'login') {
            if(!Session::get('user')) {
                header("Location: /api/docs/login");
                exit;
            }
        }
    }
}