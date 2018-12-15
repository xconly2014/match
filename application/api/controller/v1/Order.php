<?php
# Order.php

namespace app\api\controller\v1;

use app\api\controller\BaseController;

/**
 * @title 订单
 */
class Order extends BaseController
{
    /**
     * @title 3-1.快车价格表
     * @doc \app\api\param\order\Fast
     */
    public function fast() {
        $data = $this->s()->fast();
        return $this->response($data);
    }

    /**
     * @title 3-2.快车订单详情
     * @doc \app\api\param\order\Read
     * @return \think\Response
     */
    public function read() {
        $data = $this->s()->read();
        return $this->response($data);
    }

    /**
     * @title 3-3.快车约单
     * @doc \app\api\param\order\Create
     */
    public function newOrder() {
        $data = $this->s()->newOrder();
        return $this->response($data);
    }

    /**
     * @title 3-4.大神接收订单
     * @doc \app\api\param\order\Receive
     */
    public function receive() {
        $this->s()->receive();
        return $this->response();
    }

    /**
     * @title 3-5.大神开始游戏
     * @doc \app\api\param\order\Start
     */
    public function start() {
        $this->s()->start();
        return $this->response();
    }

    /**
     * @title 3-6.大神完成订单
     * @doc \app\api\param\order\Finish
     */
    public function finish() {
        $this->s()->finish();
        return $this->response();
    }

    /**
     * @title 3-7.买家取消订单
     * @doc \app\api\param\order\Cancel
     */
    public function cancel() {
        $this->s()->cancel();
        return $this->response();
    }

    /**
     * @title 3-8.买家评论订单
     * @doc \app\api\param\order\Comment
     */
    public function comment() {
        $service =  $this->s('comment');
        $data = $service->commentOrder();
        return $this->response($data);
    }

    /**
     * @title 3-9.申诉
     * @doc \app\api\param\order\Appeal
     */
    public function appeal() {
        $this->s()->appeal();
        return $this->response();
    }
}