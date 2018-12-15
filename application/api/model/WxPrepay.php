<?php
# WxPrepay.php

namespace app\api\model;


class WxPrepay extends AbstractModel
{
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;

    public function getByOutTradeNo($out_trade_no, $valid = true) {
        $this->where('out_trade_no', $out_trade_no);
        $valid && $this->where('create_time', '>=', time() - 7200);
        return $this->order('id DESC')->find();
    }
}