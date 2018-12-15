<?php

namespace wxpay;

use think\Loader;

require_once 'lib/WxPayException.php';
Loader::import('wxpay.lib.WxPayApi');
Loader::import('wxpay.lib.WxPayNotify');
use app\api\model\WxNotify;
use app\api\logic\Bill;

/**
 * 异步通知处理类
 *
 * $notify = new PayNotifyCallBack();
$  $notify->Handle(false);
*/
class PayNotifyCallBack extends \WxPayNotify
{

    /**
     * 此为主函数, 即处理自己业务的函数, 重写后, 框架会自动调用
     *
     * @param array $data 微信传递过来的参数数组
     * @param string $msg 错误信息, 用于记录日志
     * @return bool
     */
    public function NotifyProcess($data, &$msg)
    {
        // 1.校检参数
        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }

        // 2.微信服务器查询订单，判断订单真实性(可不需要)
        $qOrder = $this->Queryorder($data["transaction_id"]);
        if(!$qOrder){
            $msg = "订单查询失败";
            return false;
        }

        // 写入DB
        $model = new WxNotify();
        $data['trade_state'] = $qOrder['trade_state'];
        $data['trade_state_desc'] = $qOrder['trade_state_desc'];
        $model->data($data)->save();

        try {
            $bill =  new Bill();
            $bill->paySuccess(1, $data['out_trade_no'], $data["transaction_id"]);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return false;
        }

        return true;
    }

    // 去微信服务器查询是否有此订单
    public function Queryorder($transaction_id)
    {
        $input = new \WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = \WxPayApi::orderQuery($input);
        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return $result;
        }
        return false;
    }

}

