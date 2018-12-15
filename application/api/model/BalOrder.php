<?php
# 订单充值表

namespace app\api\model;


class BalOrder extends BaseModel
{
    protected $columns_filter = ['user_id', 'order_id', 'price', 'gold_num', 'pay_type'];

    protected static function init()
    {
        self::event('after_insert', function ($order) {
            if(!$order->order_id) {
                $sn = 'B'.date('ymdHis').str_pad(($order->id%1000), 3, '0', STR_PAD_LEFT);
                $order->setAttr('order_id', $sn);
                $order->save();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo('user', 'user_id');
    }
}