<?php
namespace app\api\model;

/**
 * 用户绑定提现账户
 * Class UserWdAccount
 * @package app\api\model
 */
class UserWdAccount extends AbstractModel
{
    protected $columns_filter = ['user_id', 'account_type', 'real_name', 'account', 'app_id'];

    public function user()
    {
        return $this->belongsTo('user', 'user_id');
    }

    public function getAccountTypeAttr($value) {
        $tuple = [
            1 => '微信', // 微信openid
            2 => '支付宝' // 支付宝登录账号
        ];
        return isset($tuple[ $value ]) ? $tuple[ $value ] : $value;
    }
}