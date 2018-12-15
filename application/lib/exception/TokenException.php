<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/10 0010
 * Time: 07:02:02
 */

namespace app\lib\exception;


class TokenException extends BaseException
{
    public $code = 401;
    public $msg = ERR::TOKEN_MSG;
    public $errorCode = ERR::TOKEN_CODE;
}