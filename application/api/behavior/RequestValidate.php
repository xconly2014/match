<?php
# RequestValidate.php

namespace app\api\behavior;

use think\Loader;
use think\Request;
use app\lib\exception\ParameterException;
use app\api\model\User;

class RequestValidate
{
    /*
     * 行为 校验接口参数
     */
    public function run(&$action) {
        $request = Request::instance();
        list($controller, $method) = $action;
        $validate = $this->getValidator($controller, $method);
        if($validate && !$validate->check($request->param())){
            $error = $validate->getError();
            throw new ParameterException(['msg'=> $error]);
        }
        $this->bindUser($request);
    }

    /**
     * 绑定user到Request
     * @param Request $request
     * @return mixed
     */
    protected function bindUser(Request $request) {
        $uid = $request->param('uid');
        $user = null;
        if($uid) {
            $model = Loader::model('user');
            $user =$model->getByUid($uid);
        }
        return $request->bind('user', $user);
    }

    /**
     * 生成对应校验器
     * 校验器查找：app\api\validate\控制器名小写\方法名首字母大写
     * @param $controller
     * @param $method
     * @return false|null|object
     */
    protected function getValidator($controller, $method) {
        $c_arr = explode('\\', get_class($controller));
        if($c_arr[0] == 'app' && $c_arr[1] == 'api' && $c_arr[2] == 'controller' && $c_arr[3] && $c_arr[4]) {
            $v = $c_arr[3];
            $class_name = $c_arr[4];
            if($v == 'v1') {}
            $entity_name = "app\\api\\param\\".strtolower($class_name)."\\".ucfirst($method);
            if(class_exists($entity_name)) {
                return Loader::validate($entity_name);
            }
        }
        return Null;
    }
}