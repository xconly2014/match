<?php
/*
 * 大神接单开关冻结时间记录
 */

namespace app\api\model;


class TalentLock extends BaseModel
{
    protected $columns_filter = ['talent_id', 'reason', 'start_time', 'end_time'];

    public function talent()
    {
        return $this->belongsTo('Talent', 'talent_id');
    }
}