<?php
/**
 * 支付宝
 * 构建并获取APP支付所需要的参数和签名
 */

namespace alipay;

use think\Loader;

Loader::import('alipay.aop.biz.AlipayTradeAppPayBiz');
Loader::import('alipay.aop.request.AlipayTradeAppPayRequest');


class TradeAppPayParam extends AbstractTradeService
{
    /**
     * 主入口
     * @param array  $params 支付参数, 具体如下
     *  string $params['subject'] 订单标题
     *  string $params['out_trade_no'] 订单商户号
     *  float  $params['total_amount'] 订单金额
     * @return array
     */
    public function totalParams($params)
    {
        // 1.校检参数
        $this->checkParams($params);

        // 2.构造参数
        $biz = new \AlipayTradeAppPayBiz();
        $biz->setSubject($params['subject']);
        $biz->setOutTradeNo($params['out_trade_no']);
        $biz->setTotalAmount($params['total_amount']); // 单位元
        $biz->setTimeExpress('1h');
        if(isset($params['passback_params'])) {
            $biz->setPassbackParams($params['passback_params']);
        }
        $bizContent = $biz->getBizContent();

        $request = new \AlipayTradeAppPayRequest();
        $request->setBizContent($bizContent);

        $config = config('alipay');
        isset($config['notify_url']) && $config['notify_url'] && $request->setNotifyUrl($config['notify_url']);
        isset($config['return_url']) && $config['return_url'] && $request->setNotifyUrl($config['return_url']);

        $aop = parent::getAopClient();
        //echo $aop->pageExecute($request); exit;
        $params = $aop->totalParams( $request );
        $params['preString'] = $aop->getSignContentUrlencode($params);
        return $params;
    }


    /**
     * 校检参数
     */
    private function checkParams($params)
    {
        if (empty(trim($params['out_trade_no']))) {
            parent::throwError('商户订单号(out_trade_no)必填');
        }

        if (empty(trim($params['subject']))) {
            parent::throwError('商品标题(subject)必填');
        }

        if (floatval(trim($params['total_amount'])) <= 0) {
            parent::throwError('退款金额(total_amount)为大于0的数');
        }
    }
}