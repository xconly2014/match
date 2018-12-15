<?php

namespace app\index\controller;
use think\Controller;
use app\lib\SmsManager;

class Sms extends Controller
{
    public function Index(){
        $request = $this->request->request();
        $phoneNum = array_key_exists("phoneNum",$request)  ? $request["phoneNum"] : 1;
        return $phoneNum;
    }

    public function Send(){
        $request = $this->request->request();
        $phoneNum = array_key_exists("phoneNum",$request)  ? $request["phoneNum"] : 1;
        $SmsManager = new SmsManager();
//        return $phoneNum;
        return $SmsManager->send($phoneNum);
    }
}