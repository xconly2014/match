<?php
# Notify.php

namespace alipay;

use app\api\logic\Bill;
use think\Request;
use app\api\model\AliNotify;

class TradeNotify extends AbstractTradeService
{
    protected $return = "success";

    public function Handle() {
        $params = Request::instance()->param();
        if ( !$this->chkSign($params) ) {
            $this->returnMsg('验签失败');
            return false;
        }
        // 写入DB
        $model = new AliNotify();
        $model->data($params)->save();
        try {
            $bill =  new Bill();
            $bill->paySuccess(2, $params['out_trade_no'], $params["trade_no"]);
        } catch (\Exception $e) {
            $this->returnMsg($e->getMessage());
            return false;
        }

        return true;
    }

    public function returnMsg($msg=null) {
        $msg && $this->return = $msg;
        return $this->return;
    }
}