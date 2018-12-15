<?php
require_once __DIR__ . '/AbstractBiz.php';

/**
 * Class AlipayTradeQueryBiz
 * 统一收单线下交易查询
 * https://docs.open.alipay.com/api_1/alipay.trade.query
 */
class AlipayTradeQueryBiz extends AbstractBiz
{
    public function setOutTradeNo($outTradeNo)
    {
        $this->biz['out_trade_no'] = $outTradeNo;
    }

    public function setTradeNo($tradeNo)
    {
        $this->biz['trade_no'] = $tradeNo;
    }
}