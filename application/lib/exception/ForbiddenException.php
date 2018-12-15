<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/10 0010
 * Time: 07:26:11
 */

namespace app\lib\exception;


class ForbiddenException extends BaseException
{
    public $code = 403;
    public $msg = ERR::FORBIDDEN_MSG;
    public $errorCode = ERR::FORBIDDEN_CODE;
}