<?php
namespace app\docs\controller;

use think\Controller;
use think\Config;
use app\docs\lib\DocParser;
use think\Session;
use app\lib\Log;

class Home extends Controller
{
    public function index() {
        $list = getApiList();
        $list = array_chunk($list, 3);

        return $this->fetch('', [
            'list' => $list,
        ]);
    }

    public function login() {
        $post = $this->request->post();
        if(isset($post['_n'])) {
            if( $post['_n'] == 'test11' && $post['_p'] == '1qaz1qaz' ) {
                Session::set('user', 'test11');
            }
        }
        if(Session::has('user')) {
            Log::info('文档后台登录成功');
            $this->success('登录成功', '/api/docs');
        }
        return $this->fetch();
    }

    protected function editAble() {
        $debug = Config::get('app_debug');
        return ($debug && file_exists(ROOT_PATH.'API_EDIT.lock'));
    }

    public function doc() {
        return $this->fetch();
    }
}
