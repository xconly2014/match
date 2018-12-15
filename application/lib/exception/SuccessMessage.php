<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/10 0010
 * Time: 07:01:07
 */

namespace app\lib\exception;


class SuccessMessage extends  BaseException
{
    public $code = 201;
    public $msg = ERR::SUCCESS_MSG;
    public $errorCode = ERR::SUCCESS_CODE;
}