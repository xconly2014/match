<?php
namespace app\api\param\user;
use think\Validate;

class WdBind extends Validate
{
    public $rule = [
        'account_type' => 'require|in:1,2',
        'real_name' => 'max:63',
        'account' => 'require|max:32',
    ];

    public $doc = [
        'description' => '微信参考文档：https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=14_2
支付宝参考文档：https://docs.open.alipay.com/api_28/alipay.fund.trans.toaccount.transfer',
        'url' => '/api/v1/user/:uid/wd_bind',
        'method' => 'POST',
        'param' => [
            'account_type' => ['default'=>'', 'desc'=> '1.微信openid, 2.支付宝登录账号'],
            'real_name' => ['default'=>'', 'desc'=> '第三方钱包实名认证姓名'],
            'account' => ['default'=>'', 'desc'=> '微信openid或支付宝登录账号'],
        ],
    ];
}