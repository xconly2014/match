<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/10 0010
 * Time: 07:21:18
 */

namespace app\api\controller\v1;


class Token
{
    public function getToken($code=''){
        (new TokenGet())->goCheck();
    }
}