<?php
// 应用行为扩展定义文件
return [
    // 模块初始化
    'module_init'  => [
        "app\\api\\behavior\\HttpAuthorization",
        "app\\api\\behavior\\ApiCacheDirector"
    ],
    // 操作开始执行
    'action_begin' => [
        "app\\api\\behavior\\RequestValidate",
    ],
    // 应用结束
    'app_end'      => [
        "app\\api\\behavior\\WriteApiLog"
    ],
];
