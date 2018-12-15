<?php
# ApiCacheDirector.php

namespace app\api\behavior;


use think\Config;
use think\Request;

class ApiCacheDirector
{
    public function run(Request $request) {
        $routeInfo = $request->routeInfo();
        $api_cache_time = in_array('api_cache',array_keys($routeInfo['option']) ) ? $routeInfo['option']['api_cache'] : Config::get('api_default_cache_time');
        if($api_cache_time !== null) {
            $cache_key = $request->controller() . '-' . $request->action() . '-' . md5(serialize( $request->param() ));
            $request->cache($cache_key, $api_cache_time);
        }
    }
}