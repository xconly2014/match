<?php
# config.php

return [
    'template'  =>  [
        'layout_on'     =>  false,
    ],
    // +----------------------------------------------------------------------
    // | 文档设置
    // +----------------------------------------------------------------------
    'controller' => [
        //需要生成文档的类
        'app\api\controller\v1\User',
        'app\api\controller\v1\Match',
        'app\api\controller\v1\Order',
        'app\api\controller\v1\Pay',
//        'app\api\controller\v1\Coupon',
//        'app\api\controller\v1\Vip',
        'app\api\controller\v1\Other',
    ],
    'filter_method' => [
        //过滤 不解析的方法名称
        '_empty'
    ],
    'public_header' => [
        //全局公共头部参数
        //如：['name'=>'version', 'require'=>1, 'default'=>'', 'desc'=>'版本号(全局)']
        ['name'=>'We-Param', 'require'=>1, 'default'=>'Android/1.0.0/1507706385', 'desc'=>'设备号/版本号/时间戳'],
        ['name'=>'Authorization', 'require'=>1, 'default'=>'We test123456:Sf2md1axbVmASByV+/9QZGMr46w=', 'desc'=>'We <accessToken>'],
    ],
    'public_param' => [
        //全局公共请求参数，设置了所以的接口会自动增加次参数
        //如：['name'=>'token', 'type'=>'string', 'require'=>1, 'default'=>'', 'other'=>'' ,'desc'=>'验证（全局）')']
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------
    'session'                => [
        'id'             => '',
        'name'           => 'Api_Docs',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'docs',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
        // 过期时间
        'expire' => 86400 * 7,
    ],
];