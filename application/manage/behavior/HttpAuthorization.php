<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/11
 * Time: 14:24
 */

namespace app\manage\behavior;

use think\Request;
use think\Session;

class HttpAuthorization
{
    /*
     * 行为 校验请求接口凭证
     */
    public function run(Request &$request) {
        $this->chkSession($request);
        return true;
    }

    protected function chkSession(Request $request) {
        if ($request->module() == 'manage' && $request->action() != 'login') {
            if(!Session::get('user')) {
                $next = urlencode( '/'.$request->path() );
                header("Location: /manage/login?next=".$next);
                exit;
            }
        }
    }
}