<?php

function editAble() {
    $debug = \think\Config::get('app_debug');
    return ($debug && file_exists(ROOT_PATH.'API_EDIT.lock'));
}


function getApiList() {
    $controller = \think\Config::get('controller');
    $list = [];
    foreach ($controller as $class) {
        if(class_exists($class)) {
            $reflection = new \ReflectionClass($class);
            $doc_str = $reflection->getDocComment();
            $doc = new \app\docs\lib\DocParser();
            $class_doc = $doc->parse($doc_str);
            $moudel =  $class_doc;
            $moudel['class'] = $class;

            $method = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

            $filter_conf = \think\Config::get('filter_method');
            $filter_conf || $filter_conf = [];
            $filter_method = array_merge(['__construct'], $filter_conf);

            $moudel['actions'] = [];
            foreach ($method as $action){
                if(!in_array($action->name, $filter_method)) {
                    $doc = new \app\docs\lib\DocParser();
                    $doc_str = $action->getDocComment();
                    if($doc_str) {
                        $action_doc = $doc->parse($doc_str);
                        $action_doc['name'] = $class."::".$action->name;
                        array_push($moudel['actions'], $action_doc);
                    }
                }
            }
            array_push($list, $moudel);
        }
    }
    return $list;
}
