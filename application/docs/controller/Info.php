<?php
# Info.php

namespace app\docs\controller;

use think\Controller;
use think\Config;
use app\docs\lib\DocParser;

class Info extends Controller
{

    /**
     * 接口详情页
     * @return mixed
     */
    public function index() {
        $name = $this->request->get('name');
        list($class, $action) = explode("::", $name);
        $action_doc = $this->getInfo($class, $action);
        if($action_doc)
        {
            $public_header = Config::get('public_header');
            $public_header || $public_header = [];
            $public_param  = Config::get('public_param');
            $public_param || $public_param = [];

            $action_doc['header'] = isset($action_doc['header']) ? array_merge($public_header, $action_doc['header']) : $public_header;
            $action_doc['param'] = isset($action_doc['param']) ? array_merge($public_param, $action_doc['param']) : $public_param;
        }
        $fields = include APP_PATH.'api/param/field.php';
        return $this->fetch('', [
            'root' => '',
            'fields' => $fields,
            'doc'=>$action_doc,
        ]);
    }


    /**
     * 获取类中指导方法注释详情
     * @param $class
     * @param $action
     * @return array
     */
    protected function getInfo($class, $action)
    {
        $result = [];
        if($class && class_exists($class)){
            $reflection = new \ReflectionClass($class);
            if($reflection->hasMethod($action)) {
                $method = $reflection->getMethod($action);
                $doc = new DocParser();
                $result = $action_doc = $doc->parse($method->getDocComment());
                if(isset($action_doc['doc'])) {
                    $entity = new $action_doc['doc'];
                    if(isset($entity->doc) && !empty($entity->doc)) {
                        if(isset($entity->doc['param'])) {
                            foreach($entity->doc['param'] as $key => $arr) {
                                $entity->doc['param'][$key]['name'] = $key;
                                $rule = isset($entity->rule[$key]) ? $entity->rule[$key] : '';
                                $entity->doc['param'][$key]['require'] = stripos($rule, 'require') === false ? 0 : 1;
                                $entity->doc['param'][$key]['type'] = stripos($rule, 'number') === false && stripos($rule, 'integer') === false ? 'String' : 'Number';
                            }
                        }
                        $result = $entity->doc + $result;
                    }
                }
            }
        }
        return $result;
    }
}