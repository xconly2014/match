<?php
# Err.php

namespace app\lib\exception;


class ERR
{
    const SUCCESS_CODE = 1;
    const SUCCESS_MSG = 'ok';

    const SYS_CODE = 0;
    const SYS_MSG = 'sorry，we make a mistake.';

    const BASE_CODE = 10000;
    const BASE_MSG = 'invalid request';

    const MISS_CODE = 10001;
    const MISS_MSG = 'global:your required resource are not found';

    const ACCESS_CODE = 10002;
    const ACCESS_MSG = 'Access rights authentication failed';// 无接口访问权限

    const FORBIDDEN_CODE = 10002;
    const FORBIDDEN_MSG = '权限不够';

    const TOKEN_CODE = 10002;
    const TOKEN_MSG = 'Token已过期或无效Token';

    const PARAMETER_CODE = 10003;
    const PARAMETER_MSG = 'invalid parameters';

    const NO_EXIST_CODE = 10003;
    const NO_EXIST_MSG = '资源不存在';


    const TIME_OUT_CODE = 10004;
    const TIME_OUT_MSG = '请求超时';
}