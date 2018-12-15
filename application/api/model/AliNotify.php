<?php
# AliNotify.php

namespace app\api\model;


class AliNotify extends AbstractModel
{
    // 自动设置 create_time
    protected $autoWriteTimestamp = true;

    protected $updateTime = false;

    public function data($data, $value = null) {
        if(!is_array($data)) {
            $data = [ $data => $value ];
        }

        $columns = ['notify_time', 'notify_type', 'notify_id', 'trade_no', 'out_trade_no', 'out_biz_no', 'buyer_id', 'buyer_logon_id',
            'trade_status', 'total_amount', 'receipt_amount', 'refund_fee', 'subject', 'body', 'gmt_create', 'gmt_payment', 'gmt_refund', 'gmt_close', 'passback_params'];
        $row_data = [];
        foreach($data as $key => $val) {
            if($key == 'passback_params') $val = urldecode($val);
            if(in_array($key, $columns)) {
                $row_data[ $key ] = $val;
            }
        }
        return parent::data($row_data);
    }
}