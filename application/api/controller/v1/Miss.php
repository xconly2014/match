<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/10 0010
 * Time: 07:19:30
 */

namespace app\api\controller\v1;


use app\lib\exception\MissException;
use think\Controller;

class Miss extends Controller
{
    public function miss()
    {
        throw new MissException();
    }
}