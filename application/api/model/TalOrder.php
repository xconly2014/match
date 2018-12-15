<?php
# 大神认证费

namespace app\api\model;


class TalOrder extends BaseModel
{
    protected $columns_filter = ['talent_id', 'order_id', 'price', 'gold_num', 'pay_type'];

    protected static function init()
    {
        self::event('after_insert', function ($order) {
            if(!$order->order_id) {
                $sn = 'T'.date('ymdHis').str_pad(($order->id%1000), 3, '0', STR_PAD_LEFT);
                $order->setAttr('order_id', $sn);
                $order->save();
            }
        });
    }

    public function talent()
    {
        return $this->belongsTo('talent', 'talent_id');
    }
}