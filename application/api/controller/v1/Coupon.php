<?php

namespace app\api\controller\v1;

use app\api\controller\BaseController;

/**
 * @title 优惠券
 */
class Coupon extends BaseController
{
    /**
     * @title 5-1.商家优惠券列表
     * @doc \app\api\param\coupon\Lister
     */
    public function lister() {
        $this->s()->lister();
        return $this->response();
    }

    /**
     * @title 5-2.优惠券详情
     * @doc \app\api\param\coupon\Detail
     */
    public function detail(){
        $this->s()->detail();
        return $this->response();
    }

    /**
     * @title 5-3.用户优惠券列表
     * @doc \app\api\param\coupon\UserLister
     */
    public function userLister(){
        $this->s()->userLister();
        return $this->response();
    }
}