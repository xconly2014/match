<?php
# Pay.php

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use wxpay\WxPayUnifiedOrderOutput;
use alipay\TradeAppPayParam;

/**
 * @title 支付
 * @package app\api\controller\v1
 */
class Pay extends BaseController
{
    /**
     * @title 4-1.微信统一下单参数
     * @doc \app\api\param\pay\WxPay
     */
    public function wxPay() {
        $data = $this->s()->wxPay();
        return $this->response( $data );
    }

    /**
     * @title 4-2.微信小程序下单参数
     * @doc \app\api\param\pay\WxPayMP
     */
    public function wxPayMP(){
//        return 1;
        $data = $this->s()->wxPayMP();
        return $this->response($data);
    }

//    /**
//     * @title 微信支付回调
//     * 该方法只做文档展示用
//     * @url /notify/wx
//     * @method POST
//     */
    public function wxNotify() {}

//    /**
//     * @title 4-2.支付宝统一下单参数
//     * @doc \app\api\param\pay\AliPay
//     */
    public function aliPay() {
        $data = $this->s()->aliPay();
        return $this->response( $data );
    }

//    /**
//     * @title 支付宝支付回调
//     * 该方法只做文档展示用
//     * @url /notify/ali
//     * @method POST
//     */
    public function aliNotify() {}

//    /**
//     * @title 4-3.<span class="text-danger">余额支付-订单or大神认证申请</span>
//     * @doc \app\api\param\pay\Balance
//     */
    public function balance() {
        $this->s()->balance();
        return $this->response();
    }

    /**
     * @title 4-3.余额充值
     * @doc \app\api\param\pay\BalanceTop
     */
    public function balanceTop() {
        $data = $this->s()->balanceTop();
        return $this->response($data);
    }

    /**
     * @title 4-4余额充值完成
     * @doc \app\api\param\pay\BalanceTopEnd
     */
    public function balanceTopEnd() {
        $this->s()->balanceTopEnd();
        return $this->response();
    }

    /**
     * @title 4-5.申请提现
     * @doc \app\api\param\pay\Wd
     */
    public function wd() {
        $data = $this->s()->wd();
        return $this->response($data);
    }

    /**
     * @title 4-6.完成提现
     * @doc \app\api\param\pay\WdPass
     */
    public function wdPass() {
        $data = $this->s()->wdPass();
        return $this->response($data);
    }

    /**
     * @title 4-7.取消提现
     * @doc \app\api\param\pay\WdRefused
     */
    public function wdRefused() {
        $data = $this->s()->wdRefused();
        return $this->response($data);
    }



    /**
     * @title 4-8.收益提现记录
     * @doc \app\api\param\pay\IncList
     */
    public function incList() {
        $data = $this->s()->incWdList();
        return $this->response($data);
    }

    /**
     * @title 4-9.提现列表
     * @doc \app\api\param\pay\WdPriceList
     */
    public function wdPriceList()
    {
//        $data['wdPriceList'] = ['2','5','10', '20', '50', '100', '150', '200'];
        $data['wdPriceList'] = ['2','5','10', '20', '50', '100', '150', '200'];
        return $this->response($data);
    }


//    /**
//     * @title <b class="text-danger">4-7. 大神认证费</b>
//     * @doc \app\api\param\pay\Cert
//     */
    public function cert() {
        $data = $this->s()->cert();
        return $this->response($data);
    }
}