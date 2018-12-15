<?php
namespace app\api\controller\v1;
use app\api\controller\BaseController;
use app\lib\exception\NotFoundException;
use app\lib\QiniuManager;
use app\extra\WxPayBaseConfig;
use think\Config;

/**
 * @title 其他
 */
class Other extends BaseController
{

    /**
     * @title 0-1.七牛上传凭证
     * @doc \app\api\param\other\QiniuToken
     */
    public function qiniuToken() {
        $bucket = $this->request->param('bucket');
        $key = $this->request->param('key');
        $expires = $this->request->param('expires');
        $key == '' && $key = null;
        is_numeric($expires) || $expires == 3600;

        $manager = new QiniuManager($bucket);
        $token = $manager->uploadToken(null, $key, $expires);
        return $this->response(['token'=>$token]);
    }

    /**
     * @title 0-2.视频列表
     * @doc \app\api\param\other\VideoList
     */
    public function videoList() {
        $data = $this->s()->videoList();
        return $this->response( $data );
    }

    /**
     * @title 0-3.H5链接地址
     * @doc \app\api\param\other\Urls
     */
    public function urls() {
        $domain = $this->request->server('HTTP_HOST');
        $data = [
            'help' => url('/app/help', '', false, $domain),
            'about' => url('/app/about', '', false, $domain),
            'talent_agree' => url('/app/agree', '', false, $domain)
        ];
        return $this->response($data);
    }

    /**
     * @title 0-4.微信小程序openid
     * @doc \app\api\param\other\WxMiniOpenid
     */
    public function wxMiniOpenid() {
        $code = $this->request->param('code');
        $urlGetOpenid = "https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code";
        $urlGetOpenid = sprintf($urlGetOpenid,WxPayBaseConfig::APPID,WxPayBaseConfig::APPSECRET,$code);
        $data = json_decode(file_get_contents($urlGetOpenid),true);
        $data['token'] = isset($data['errcode']) ? 0 : (isset($data['unionid']) ? $data['openid'] : $data['openid']);
        return $this->response($data);
    }

    /**
     * @title 0-5.获取游戏内的账号信息
     * @doc \app\api\param\other\UserGameInfo
     */
    public function userGameInfo(){
        $role_id = $this->request->param('role_id');
        if($role_id == 0){
            $data = [
                'userid'   => $role_id,
                'nickname'   => '',
            ];
            return $this->response($data);
        }
        $json = '{"para":{"uid":"","nick":"","idServer":"100068"},"time":1529647471,"appkey":"991d40dcc9a327444250d75c4aa8fa1d"}';
        $arrServerEmail = json_decode($json, true);
        $config = Config::get('cmswat');
        $arrServerEmail['para']['idServer'] = $config['serverId'];
        $arrServerEmail['para']['uid'] = $role_id;
        $urlEmailSend = $config['serverUrl']."/user/getUserServerExist.php?" . http_build_query($arrServerEmail, NULL, "&");
        $callBack = json_decode(file_get_contents($urlEmailSend), true);
        if(empty($callBack) || empty($callBack['data']['exist'])){
            throw new NotFoundException(['msg'=> '游戏账号不存在']);
        }
        $data = $callBack['data']['userInfo'];
        return $this->response($data);
    }

}