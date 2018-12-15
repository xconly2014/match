<?php
namespace app\index\controller;

use think\Controller;
use wxpay\PayNotifyCallBack;
use alipay\TradeNotify;
use app\lib\Log;

class Notify extends Controller
{
    public function wx() {
        $notify = new PayNotifyCallBack();
        $log_msg = '';
        if(isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            $log_msg .= 'Notify: ' .json_encode( $notify->FromXml($GLOBALS['HTTP_RAW_POST_DATA']), JSON_UNESCAPED_UNICODE ) . "\r\n";
        }
        $notify->Handle();
        if($notify->GetReturn_code() == 'FAIL') {
            $log_msg .= 'Resp: ' . $notify->GetReturn_msg();
        }
        Log::api($log_msg);
    }

    public function ali() {
        $notify = new TradeNotify();
        $log_msg = '';
        $log_msg .= 'Notify: ' .json_encode( $_POST, JSON_UNESCAPED_UNICODE ) . "\r\n";
        $notify->Handle();
        $log_msg .= 'Resp: ' . $notify->returnMsg();
        Log::api($log_msg);
        return $notify->returnMsg();
    }
}
