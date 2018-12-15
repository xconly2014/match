<?php
namespace app\api\model;


use traits\model\SoftDelete;

class BaseModel extends AbstractModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    protected $hidden = ['delete_time'];

    // 自动设置 create_time
    protected $autoWriteTimestamp = true;

    protected $updateTime = false;

    public function getCreateTimeAttr($value)
    {
        return (int) $value;
    }
}