<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/10 0010
 * Time: 06:53:14
 */

namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Response;
use think\Loader;
use app\lib\exception\ERR;


class BaseController extends Controller
{
    /**
     * 返回封装后的API数据到客户端
     * @access protected
     * @param mixed     $data 要返回的数据
     * @param integer   $code 返回的code
     * @param mixed     $msg 提示信息
     * @param string    $type 返回数据格式
     * @param array     $header 发送的Header信息
     * @return Response
     */
    protected function response($data = '', $code = ERR::SUCCESS_CODE, $msg = ERR::SUCCESS_MSG, $type = '', array $header = [])
    {
        isset($data['_time']) || $data['_time'] = Request::instance()->server('REQUEST_TIME');
        $result = [
            'code' => $code,
            'message'  => $msg,
            'data' => $data,
        ];

        $type     = $type ?: $this->getResponseType();
        $response = Response::create($result, $type)->header($header);
        return $response;
    }

    protected function s($name = '', $appendSuffix = false, $common = 'api') {
        if(!$name) {
            $class = get_class($this);
            $name = substr(strrchr($class, "\\"), 1);
        }
        return Loader::model($name, 'service', $appendSuffix, $common);
    }

    protected function run($name) {
        if (strpos($name, '/')) {
            $_arr = explode('/', $name);
            $name = array_pop($_arr);
            $layer = implode("\\", $_arr);
        } else {
            $class = get_class($this);
            $layer = substr(strrchr($class, "\\"), 1);
        }
        $layer = $layer ? "service\\" . $layer : 'service';
        $model = Loader::model($name, $layer);
        return $model->run();
    }
}