<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/10 0010
 * Time: 06:59:19
 */

namespace app\lib\exception;


class MissException extends BaseException
{
    public $code = 404;
    public $msg = ERR::MISS_MSG;
    public $errorCode = ERR::MISS_CODE;
}