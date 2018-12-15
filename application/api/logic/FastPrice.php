<?php
# FastPrice.php

namespace app\api\logic;

use think\Model;

class FastPrice extends Model
{
    /**
     * @param Model[] $rows
     * @return array
     */
    public function formatRows($rows) {
        $datas = [];
        $voice_price = 0;
        foreach($rows as $row) {
            if($row->getData('type') == 0) {
                $voice_price = $row->price;
            } else {
                $datas[] = [
                    'title' => $row->getAttr('type'),
                    'price' => $row->price,
                    'icon' => $row->icon
                ];
            }
        }
        return ['voice_price' => $voice_price, 'price' => $datas];
    }
}