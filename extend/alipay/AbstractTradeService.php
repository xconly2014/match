<?php
# AbstractClass.php

namespace alipay;

use think\Loader;

Loader::import('alipay.aop.AopClient');
Loader::import('alipay.aop.SignData');
Loader::import('alipay.aop.EncryptParseItem');
Loader::import('alipay.aop.EncryptResponseData');

abstract class AbstractTradeService
{
    protected static $_client;

    protected static function getAopClient() {
        if(!self::$_client) {
            $alipay_config = config('alipay');
            $aop = new \AopClient();

            $aop->gatewayUrl = $alipay_config['gatewayUrl'];
            $aop->appId = $alipay_config['app_id'];
            $aop->rsaPrivateKey = $alipay_config['rsa_private_key'];
            $aop->alipayrsaPublicKey= $alipay_config['alipay_public_key'];
            $aop->apiVersion = '1.0';
            $aop->signType = $alipay_config['sign_type'];
            $aop->postCharset= $alipay_config['charset'];
            $aop->format= $alipay_config['format'];

            if(empty($aop->appId)||trim($aop->appId)==""){
                self::throwError("appid should not be NULL!");
            }
            if(empty($aop->rsaPrivateKey)||trim($aop->rsaPrivateKey)==""){
                self::throwError("private_key should not be NULL!");
            }
            if(empty($aop->alipayrsaPublicKey)||trim($aop->alipayrsaPublicKey)==""){
                self::throwError("alipay_public_key should not be NULL!");
            }
            if(empty($aop->postCharset)||trim($aop->postCharset)==""){
                self::throwError("charset should not be NULL!");
            }
            if(empty($aop->gatewayUrl)||trim($aop->gatewayUrl)==""){
                self::throwError("gateway_url should not be NULL!");
            }

            self::$_client = $aop;
        }
        return self::$_client;
    }

    /**
     * 统一错误处理接口
     * @param $msg
     * @throws \think\Exception
     */
    protected static function throwError($msg)
    {
        throw new \think\Exception($msg);
    }

    protected function chkSign($params) {
        $aop = self::getAopClient();
        $result = $aop->rsaCheckV1($params, null);
        return $result;
    }
}