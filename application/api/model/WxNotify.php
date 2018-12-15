<?php
# WxNotify.php

namespace app\api\model;


class WxNotify extends AbstractModel
{
    // 自动设置 create_time
    protected $autoWriteTimestamp = true;

    protected $updateTime = false;

    public function data($data, $value = null) {
        if(!is_array($data)) {
            $data = [ $data => $value ];
        }

        $columns = ['return_code', 'return_msg', 'result_code', 'err_code', 'err_code_des', 'trade_type', 'total_fee', 'trade_state', 'trade_state_desc',
            'settlement_total_fee', 'transaction_id', 'out_trade_no', 'attach', 'time_end'];
        $row_data = [];
        foreach($data as $key => $val) {
            if($key == 'attach') {
                $val = is_array( $val ) ? json_encode($val) : $val;
            }
            if(in_array($key, $columns)) {
                $row_data[ $key ] = $val;
            }
        }
        return parent::data($row_data);
    }
}