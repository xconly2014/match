<?php
namespace app\api\param\other;
use think\Validate;

class Urls extends Validate
{
    public $rule = [
    ];

    public $doc = [
        'description' => '',
        'url' => '/api/v1/other/urls',
        'method' => 'GET',
        'param' => [
        ],
    ];
}