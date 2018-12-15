<?php
namespace app\api\param\pay;
use think\Validate;

class WdPriceList extends Validate
{
    public $rule = [
    ];

    public $doc = [
        'description' => '',
        'url' => '/api/v1/income/wdPriceList',
        'method' => 'GET',
        'param' => [],
    ];
}