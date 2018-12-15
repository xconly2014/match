<?php
# 微信调起支付参数，

namespace wxpay;

require_once 'lib/WxPayData.php';
require_once 'lib/WxPayApi.php';


class WxPayOrderOutput extends \WxPayDataBase
{
    public function totalParams(WxPayUnifiedOrderOutput $input, $prepay_id = null) {
        if(!$prepay_id) {
            $resp = \WxPayApi::unifiedOrder($input);
            $this->checkResult($resp);
            $prepay_id = $resp['prepay_id'];
        }
        $this->values['prepayid'] = $prepay_id;
        $this->values['package'] = 'Sign=WXPay';
        return $this->getValues();
    }


    public function getValues() {
        $this->autoOrderParam();
        $values = $this->values;
        return $values;
    }

    /**
     * @throws \WxPayException
     */
    public function autoOrderParam() {
        $this->values['appid'] = \WxPayConfig::APPID;
        $this->values['partnerid'] = \WxPayConfig::MCHID;
        $this->values['noncestr'] = $this->getNonceStr();
        $this->values['timestamp'] = (string) time();
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

    // 结果检测
    protected function checkResult($result)
    {
        if(!(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS"))
        {
            if(empty($result['return_msg']) || $result['return_msg'] == 'OK') {
                throw new \WxPayException('微信支付错误: '.$result['err_code']."  原因:".$result['err_code_des']);
            } else {
                throw new \WxPayException('微信支付错误: '.$result['return_msg']);
            }
        }
    }
}