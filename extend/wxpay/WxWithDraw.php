<?php

namespace wxpay;

use think\Loader;

Loader::import('wxpay.lib.WxPayJsPay');

class WxWithDraw extends  WxPayBase
{

    public static function getParams($params, $openId='')
    {
        // 1.校检参数
        $that = new self();
//        $that->checkParams($params);

        // 2.组装参数
        $input = $that->getPostData($params, $openId);

        // 3.获取预支付信息
        $order = \WxPayApi::withdraw($input);

        // 4.进行结果检验
//        $that->checkResult($order);

        // 5.组装支付参数
//        $tools = new \WxPayJsPay();
//        $jsApiParameters = $tools->GetJsApiParameters($order);

        // 6.返回支付参数
        return $order;
    }

    // 组装请求参数
    private function getPostData($params, $openId)
    {
        $input  = new \WxPayWithDraw();
        $input->SetOpenid($openId);
//        $input->SetTrade_type("JSAPI");
        $input->SetTotal_fee($params['amount']);
        $input->SetOut_trade_no($params['partner_trade_no']);
        $input->SetDes($params['desc']);
        $input->SetCheck_name($params['check_name']);
        return $input;
    }

}

