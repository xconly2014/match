<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/11
 * Time: 14:24
 */

namespace app\api\behavior;

use think\Request;
use think\Config;
use app\lib\exception\AccessException;
use app\lib\exception\TimeOutException;

class HttpAuthorization
{
    /*
     * 行为 校验请求接口凭证
     */
    public function run(Request &$request) {
        $this->chkHeader($request);
        return true;
    }

    protected function chkHeader(Request $request) {
        if($request->module() == 'api' && $request->action() != 'miss') {
            $we_param = $request->header('We-Param');

            //判断时间戳
            $param = explode('/', $we_param);
            if(!$param || !isset($param[2]) || !is_numeric($param[2])) {
                throw new AccessException(['msg'=>'无效的头部参数']);
            }
            if($param[2] != '1507706385' &&  time() - $param[2] >= 300) {
                throw new TimeOutException(); // 超时
            }

            $auth = $request->header('Authorization');
            if(!$we_param || !$auth) {
                throw new AccessException();
            }
            if(strpos($auth,'We ') !== 0) {
                throw new AccessException();
            }
            $accessToken = str_replace('We ', '', $auth);
            list($AccessKey, $encodedSign) = explode(':', $accessToken);
            if(!$encodedSign) {
                throw new AccessException(['msg'=>'验签失败']);
            }

            $api_key_storage = Config::get('api_key_storage');
            if(!isset($api_key_storage[ $AccessKey ])) {// 公私钥不存在
                throw new AccessException(['msg'=>'无效的密钥']);
            }
            $SecretKey = $api_key_storage[ $AccessKey ];

            //$AccessKey = 'test123456';
            //$SecretKey = '654321test';
            //$we_param = 'Android/1.0.0/1507706385';
            //$accessToken = 'test123456:Sf2md1axbVmASByV+/9QZGMr46w=';

            $hmac = base64_encode(hash_hmac('sha1',$we_param, $SecretKey, true));
            $hmac2 = base64_encode(hash_hmac('sha1',$we_param, $SecretKey));
            if( !($hmac == $encodedSign || $hmac2 == $encodedSign) ) {// 验证签名错误
                throw new AccessException(['msg'=>'验签失败!']);
            }
        }
    }
}