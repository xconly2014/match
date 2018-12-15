<?php
# WxPayDataOutput.php

namespace wxpay;

require_once 'lib/WxPayData.php';


class WxPayUnifiedOrderOutput extends \WxPayUnifiedOrder
{
    /**
     * @param $params
     *  string $params['body'] 订单标题
     *  string $params['out_trade_no'] 订单商户号
     *  float  $params['total_fee'] 订单金额
     *  string $params['device_info']  设备信息
     *  string $params['spbill_create_ip'] 终端IP
     * @return array
     */
    public function totalParams($params) {
        $this->SetBody($params['body']);
        $this->SetOut_trade_no($params['out_trade_no']);
        $this->SetTotal_fee($params['total_fee']); // 单位分
        $this->SetTrade_type('APP');

        $this->SetSpbill_create_ip($params['spbill_create_ip']);
        if( isset($params['device_info']) && $params['device_info'] ) {
            $this->SetDevice_info($params['device_info']);
        }
        if(isset($params['attach'])) {
            $this->SetAttach($params['attach']); // 商家数据包，原样返回
        }

        return $this->getValues();
    }


    public function getValues() {
        $this->autoUnifiedOrderParam();
        $values = $this->values;
        return $values;
    }

    /**
     * @throws \WxPayException
     */
    public function autoUnifiedOrderParam() {
        //检测必填参数
        if(!$this->IsOut_trade_noSet()) {
            throw new \WxPayException("缺少统一支付接口必填参数out_trade_no！");
        }else if(!$this->IsBodySet()){
            throw new \WxPayException("缺少统一支付接口必填参数body！");
        }else if(!$this->IsTotal_feeSet()) {
            throw new \WxPayException("缺少统一支付接口必填参数total_fee！");
        }else if(!$this->IsTrade_typeSet()) {
            throw new \WxPayException("缺少统一支付接口必填参数trade_type！");
        }

        //关联参数
        if($this->GetTrade_type() == "JSAPI" && !$this->IsOpenidSet()){
            throw new \WxPayException("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！");
        }
        if($this->GetTrade_type() == "NATIVE" && !$this->IsProduct_idSet()){
            throw new \WxPayException("统一支付接口中，缺少必填参数product_id！trade_type为JSAPI时，product_id为必填参数！");
        }

        //异步通知url未设置，则使用配置文件中的url
        if(!$this->IsNotify_urlSet()){
            $this->SetNotify_url(\WxPayConfig::NOTIFY_URL);//异步通知url
        }

        $this->SetAppid(\WxPayConfig::APPID);//公众账号ID
        $this->SetMch_id(\WxPayConfig::MCHID);//商户号
        if(\WxPayConfig::WXPAY_DEBUG) {
            $this->SetSpbill_create_ip("1.1.1.1");
        }
        $this->SetNonce_str($this->getNonceStr());//随机字符串

        //签名
        $this->SetSign();
    }

    /**
     *
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return  string 产生的随机字符串
     */
    public function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }
}