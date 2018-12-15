<?php
# WriteApiLog.php

namespace app\api\behavior;

use app\lib\Log;
use think\Request;

class WriteApiLog
{
    public function run(&$response) {
        $request = Request::instance();
        if($request->module() == 'api' && $request->action() != 'miss') {
            $method = strtolower($request->method());
            $header['We-Param'] = $request->header('We-Param');
            $header['Authorization'] = $request->header('Authorization');
            $msg = 'Header: ' . json_encode($header) . "\r\n";
            $msg.= 'Req: ' . json_encode( $request->$method() ) . "\r\n";
            $msg.= 'Resp: ' . $response->getContent() . "\r\n";
            Log::api($msg);
        }
        return true;
    }
}