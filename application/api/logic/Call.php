<?php
# Call.php

namespace app\api\logic;

use app\api\service\AbstractServiceModel;
use app\lib\exception\NotFoundException;

class Call extends AbstractServiceModel
{
    public function getCall($id) {
        $call = $this->m()->with('user')->getById($id);
        if( !$call ) {
            throw new NotFoundException(['msg' => '呼叫不存在或已过期']);
        }
        if( $call->order_id ) {
            throw new NotFoundException(['msg' => '您来晚啦']);
        }
        return $call;
    }
}