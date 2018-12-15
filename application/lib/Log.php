<?php
# Log.php

namespace app\lib;

use think\Log AS Tlog;
use think\Config;

class Log extends Tlog
{
    public static function init($config = []) {
        Config::load(APP_PATH.'api/config.php');
        empty($config) && $config = Config::get('log');
        parent::init($config);
    }

    public static function api($msg) {
        $_SERVER['REQUEST_URI'] = $_SERVER['PATH_INFO'];
        $_SERVER['SERVER_ADDR'] = '';
        self::record($msg, 'api');
    }

    public static function pay($msg, $api_uri) {
        $_SERVER['REQUEST_URI'] = $api_uri;
        $_SERVER['SERVER_ADDR'] = '';
        self::record($msg, 'pay');
    }

    public static function cmd($msg){
        $_SERVER['REQUEST_URI'] = 'CMD';
        $_SERVER['SERVER_ADDR'] = 'CMD';
        self::record($msg,'cmd');
    }
}