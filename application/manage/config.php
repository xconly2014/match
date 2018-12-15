<?php
# config.php

return [
    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------
    'session'                => [
        'id'             => '',
        'name'           => 'Manage',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'manage_',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
        // 过期时间
        'expire' => 86400 * 7,
    ],
];