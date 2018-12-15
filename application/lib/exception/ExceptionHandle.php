<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/10 0010
 * Time: 07:02:47
 */

namespace app\lib\exception;

use think\exception\Handle;
use think\Log;
use think\Hook;
use think\Request;
use Exception;

class ExceptionHandle extends Handle
{
    private $code;
    private $msg;
    private $errorCode;

    public function render(Exception $e)
    {
        if($e instanceof BaseException)
        {
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->errorCode = $e->errorCode;
        }
        else
        {
            if(config('app_debug')){
                return parent::render($e);
            }

            $this->code = 500;
            $this->msg = ERR::SYS_MSG;
            $this->errorCode = ERR::SYS_CODE;
            $this->recordErrorLog($e);
        }

        $request = Request::instance();
        $result = [
            'code' => $this->errorCode,
            'message'  => $this->msg,
            //'request_url' => $request = $request->url(),
            'data' => ['_time'=>Request::instance()->server('REQUEST_TIME')],
        ];
        $response = json($result, 200);

        Hook::listen('app_end', $response);

        return $response;
    }

    /*
     * 将异常写入日志
     */

    private function recordErrorLog(Exception $e)
    {
        Log::init([
            'type'  =>  'File',
            'path'  =>  LOG_PATH,
            'level' => ['error']
        ]);
//        Log::record($e->getTraceAsString());
        Log::record($e->getMessage(),'error');
    }
}