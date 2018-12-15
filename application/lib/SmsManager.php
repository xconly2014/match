<?php

namespace app\lib;
use Think\Config;
use Qcloud\Sms\SmsSingleSender;


class SmsManager
{
    protected $SmsSender;
    protected $type = 0;
    protected $nationCode = "86";
    protected $msg = "【腾讯云】您的验证码是: 5678";

    public  function  __construct()
    {
        $config =  Config::get('qcloudsms');
        $this->SmsSender = new SmsSingleSender($config['appId'],$config['appKey']);
    }

    public function send($phponeNumber){
        return $this->SmsSender->send($this->type,$this->nationCode,$phponeNumber,$this->msg,"","");
    }
}