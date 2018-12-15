<?php

namespace alipay;

use think\Loader;

Loader::import('alipay.aop.biz.AlipayTradeQueryBiz');
Loader::import('alipay.aop.request.AlipayTradeQueryRequest');

class Query extends AbstractTradeService
{
    public function query($out_trade_no = null, $trade_no = null) {
        $aop = self::getAopClient();
        $biz = new \AlipayTradeQueryBiz();
        $out_trade_no || $trade_no || parent::throwError('确缺少必要参数');
        $out_trade_no && $biz->setOutTradeNo($out_trade_no);
        $trade_no && $biz->setTradeNo($trade_no);

        $request = new \AlipayTradeQueryRequest ();
        $request->setBizContent( $biz->getBizContent() );
        $result = $aop->execute ( $request);
        return json_decode(json_encode($result->alipay_trade_query_response), true);
    }
}