<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/10 0010
 * Time: 07:00:23
 */

namespace app\lib\exception;


class AccessException extends BaseException
{
    public $code = 400;
    public $errorCode = ERR::ACCESS_CODE;
    public $msg = ERR::ACCESS_MSG;
}