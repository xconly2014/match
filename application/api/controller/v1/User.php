<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use think\Response;


/**
 * @title 用户
 */
class User extends BaseController
{
    /**
     * @title 1-1.登录
     * @doc \app\api\param\user\Login
     */
    public function login() {
        $data = $this->s()->login();
        return $this->response($data);
    }

    /**
     * @return Response
     *
     * @title 1-2.退出登录
     * @doc \app\api\param\user\Logout
     */
    public function logout() {
        $this->s()->logout();
        return $this->response();
    }

    /**
     * @return Response
     *
     * @title 1-3.我的主页
     * @doc \app\api\param\user\Home
     */
    public function home() {
        $data = $this->s()->homeDate();
        return $this->response($data);
    }


    /**
     * @return Response
     *
     * @title 1-4.获取个人资料
     * @doc \app\api\param\user\Read
     */
    public function read() {
        $data = $this->s()->detailData();
        return $this->response($data);
    }

    /**
     * @return Response
     *
     * @title 1-5.1.编辑个人资料
     * @doc \app\api\param\user\Update
     */
    public function update() {
        $data = $this->s()->updateByRequest();
        return $this->response($data);
    }
    /**
     * @return Response
     *
     * @title 1-5.2.绑定用户手机号码
     * @doc \app\api\param\user\UpdatePhone
     */
    public function updatePhone() {
        $data = $this->s()->updatePhoneByRequest();
        return $this->response($data);
    }

    /**
     * @return Response
     *
     * @title 1-6.我的快车订单
     * @doc \app\api\param\user\Order
     */
    public function orderList() {
        $data = $this->s()->orderList();
        return $this->response($data);
    }

    /**
     * @title 1-7.我的钱包账户
     * @doc \app\api\param\user\Balance
     */
    public function balance() {
        $data = $this->s()->balance();
        return $this->response($data);
    }

    /**
     * @title 1-8.我的收益
     * @doc \app\api\param\user\Income
     */
    public function income() {
        $data = $this->s()->income();
        return $this->response($data);
    }

    /**
     * @title 1-9.提现账号绑定
     * @doc \app\api\param\user\WdBind
     */
    public function wdBind() {
        $this->s()->wdBind();
        return $this->response();
    }

    /**
     * @title 1-10.订单消息列表
     * @doc \app\api\param\user\OrderMsg
     */
    public function orderMsg() {
        $data = $this->s()->orderMsg();
        return $this->response($data);
    }

    /**
     * @title <b class="text-danger">1-11.系统消息列表</b>
     * @doc \app\api\param\user\SysMsg
     */
    public function sysMsg() {
        $data = $this->s()->sysMsg();
        return $this->response($data);
    }


    /**
     * @title 1-12.用户的分级邀请
     * @doc \app\api\param\user\UserInvitedList
     */
    public function userInvitedList() {
        $data = $this->s()->userInvitedList();
        return $this->response($data);
    }

}