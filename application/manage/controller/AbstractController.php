<?php
# AbstractController.php

namespace app\manage\controller;

use think\Controller;
use app\api\model\User;
use think\Loader;

class AbstractController extends  Controller
{
    protected function s($name = '', $appendSuffix = false, $common = 'api') {
        if(!$name) {
            $class = get_class($this);
            $name = substr(strrchr($class, "\\"), 1);
        }
        return Loader::model($name, 'service', $appendSuffix, $common);
    }

    protected function m($name = '', $appendSuffix = false, $common = 'api') {
        if(!$name) {
            $class = get_class($this);
            $name = substr(strrchr($class, "\\"), 1);
        }

        return Loader::model($name, 'model', $appendSuffix, $common);
    }

    protected function l($name = '', $appendSuffix = false, $common = 'api') {
        if(!$name) {
            $class = get_class($this);
            $name = substr(strrchr($class, "\\"), 1);
        }

        return Loader::model($name, 'logic', $appendSuffix, $common);
    }

    /**
     * @param null|integer $uid
     * @return User|null
     */
    protected function getUser($uid=null) {
        $user = null;
        if($uid) {
            $user = User::get(['uid'=>$uid]);
        } elseif(isset($request->user)) { // HttpAuthorization 行为监听中在request上绑定了user
            $user = $this->request->user;
        }
        return $user;
    }
}