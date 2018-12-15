<?php

namespace app\api\controller\v1;


use app\api\controller\BaseController;

/**
 * @title VIP会员
 */
class Vip extends BaseController
{

    /**
     * @title 6-1.商家会员列表
     * @doc \app\api\param\vip\Lister
     */
    public function lister(){
        $this->s()->lister();
        return $this->response();
    }

    /**
     * @title 6-2.用户会卡
     * @doc \app\api\param\vip\UserList
     */
    public function userLister(){
        $this->s()->lister();
        return $this->response();
    }
}