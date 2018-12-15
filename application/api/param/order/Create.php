<?php
# Login.php
namespace app\api\param\order;

use think\Validate;

class Create extends Validate
{
    public $rule = [
        'uid'  =>  'require|alphaDash|max:32',
        'match_id' => 'require|integer',
        'ticket_id' => 'require|integer',
//        'talent_id' => 'require|integer',
//        'date_type' => 'require|integer|between:0,7',
//        'area' => 'require|chsAlphaNum|max:20',
//        'system' => 'require|chsAlphaNum|max:20',
//        'round' => 'require|integer|>=:1',
//        'call_id' => 'alphaDash|max:63',
        'device_info' => 'max:32',
        'spbill_create_ip' => 'ip',
    ];

    public $doc = [
        'url' => '/api/v1/order',
        'method' => 'POST',
        'param' => [
            'uid' => [ 'default'=>'','desc'=> '用户ID'],
            'match_id' => [ 'default'=>'','desc'=> '賽事ID'],
            'ticket_id' => [ 'default'=>'','desc'=> '入场券ID'],
//            'talent_id' => [ 'default'=>'','desc'=> '大神ID'],
//            'date_type' => [ 'default'=>'','desc'=> '0.语音陪玩，7.王者局,6.星耀,5.钻石,4.铂金,3.黄金,2.白银,1.娱乐局,'],
//            'area' => [ 'default'=>'','desc'=> '服务区服:0.双区服，1.QQ，2.微信.'],
//            'system' => [ 'default'=>'','desc'=> '操作系统:0.双系统，1.安卓，2.IOS'],
//            'round' => [ 'default'=>'','desc'=> '游戏局数或语音小时数'],
//            'call_id' => [ 'default'=>'','desc'=> '如果是从呼叫老司机创建订单， 请传此参数'],
            'device_info' => [ 'default'=>'APP','desc'=> '微信支付参数 - 终端设备号(门店号或收银设备ID)，默认请传"WEB"'],
            'spbill_create_ip' => [ 'default'=>'','desc'=> '微信支付参数 - 用户端实际ip'],
        ],
    ];
}