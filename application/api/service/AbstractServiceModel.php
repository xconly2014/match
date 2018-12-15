<?php
# AbstractModel.php

namespace app\api\service;

use think\Model;
use think\Loader;
use think\Request;
use app\api\model\User;

abstract class AbstractServiceModel extends Model
{
    protected $request;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->request = Request::instance();
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

    protected function getUser($uid=null) {
        $user = null;
        $request = Request::instance();

        if($uid) {
            $user = User::get(['uid'=>$uid]);
        } elseif(isset($request->user)) { // HttpAuthorization 行为监听中在request上绑定了user
            $user = $request->user;
        }
        return $user;
    }

    protected function getRequest() {
        return Request::instance();
    }
}