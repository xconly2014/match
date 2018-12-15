<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;

/**
 * @title 大神
 */
class Talent extends BaseController
{
    /**
     * @title 2-1.申请成为大神
     * @doc \app\api\param\talent\Create
     */
    public function apply() {
        $this->s()->apply();
        return $this->response();
    }

    /**
     * @title 2-2.大神or美女列表
     * @doc \app\api\param\talent\Lister
     */
    public function lister() {
        $data = $this->s()->lister();
        return $this->response($data);
    }

    /**
     * @title 2-3.大神详情
     * @doc \app\api\param\talent\Read
     */
    public function read() {
        $data = $this->s()->read();
        return $this->response($data);
    }

    /**
     * @title 2-4.大神审核详情
     * @doc \app\api\param\talent\Audit
     */
    public function simpleInfo() {
        $data = $this->s()->simpleInfo();
        return $this->response($data);
    }

    /**
     * @title 2-5.大神接单开关
     * @doc \app\api\param\talent\SwitchOn
     */
    public function switchOn() {
        $this->s()->switchOn();
        return $this->response();
    }

    /**
     * @title 2-6.呼叫老司机
     * @doc \app\api\param\talent\Call
     */
    public function call() {
        $data = $this->s()->call();
        return $this->response($data);
    }

    /**
     * @title 2-7.抢单
     * @doc \app\api\param\talent\Race
     */
    public function race() {
        $this->s()->race();
        return $this->response();
    }

    /**
     * @title 2-8.取消呼叫
     * @doc \app\api\param\talent\CancelCall
     */
    public function cancelCall() {
        $this->s()->cancelCall();
        return $this->response();
    }

    /**
     * @title 2-9.抢单信息
     * @doc \app\api\param\talent\RaceDetail
     */
    public function raceDetail() {
        $data = $this->s()->raceDetail();
        return $this->response($data);
    }

    /**
     * @title 2-10.随机大神列表
     * @doc \app\api\param\talent\Random
     */
    public function random() {
        $data = $this->s()->random();
        return $this->response($data);
    }

    /**
     * @title 2-11.商家活动详情页
     * @doc \app\api\param\talent\ActivityDetail
     */
    public function  activityDetail(){
        $data = $this->s()->activityDetail();
        return $this->response($data);
    }

    /**
     * @title 2-12.商家活动列表
     * @doc \app\api\param\talent\ActivityList
     */
    public function activityList(){
        $data = $this->s()->activityList();
        return $this->response($data);
    }

    /**
     * @title 2-12.商家评论列表
     * @doc \app\api\param\talent\CommentList
     */
    public function commentList(){
        $data = $this->s()->commentList();
        return $this->response($data);
    }
}