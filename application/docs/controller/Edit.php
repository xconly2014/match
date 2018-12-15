<?php
# Info.php

namespace app\docs\controller;

use think\Controller;
use app\docs\lib\DocParser;
use app\lib\exception\MissException;
use think\Config;
use think\Route;

class Edit extends Controller
{
    /**
     * 接口详情页
     * @throws  MissException
     * @return mixed
     */
    public function index() {
        if(!editAble()) {
            throw new MissException();
        }

        $name = $this->request->get('name');
        if( $name ) {
            list($class, $action) = explode("::", $name);
            $info = $this->getInfo($class, $action);
        } else {
            $info = null;
        }

        if($this->onSubmitValidate()) {
            return $this->save($info);
        }


        return $this->fetch('', [
            'name' =>  $name,
            'info' => $info,
            'groups' => $this->getGroups()
        ]);
    }

    protected function save($info) {
        if($info) {
            return $this->update($info);
        } else {
            return $this->insert();
        }
    }

    protected function update($info) {
        $post = $this->request->post();
        // 修改标题
        $file_arr = file($info['class']->getFileName());
        if($info['title'] != $post['title']) {
            foreach($file_arr as $index => $line) {
                if(strpos($line, '@title '.$info['title'])) {
                    $file_arr[ $index ] = str_replace('@title '.$info['title'], '@title '.$post['title'], $line);
                }
            }
        }
        // 修改 方法名
        if(strtolower($post['action']) != strtolower($info['action']) ) {
            $file_arr[ $info['method']->getStartLine() -1 ] = "    public function ".$post['action']."() {\r\n";
        }
        file_put_contents($info['class']->getFileName(), implode('', $file_arr));

        // 修改 route
        $rule = 'api/:version/'.$post['uri'];
        $old_route = 'api/:version.'.strtolower($info['controller']).'/'.lcfirst($info['action']);
        $route = 'api/:version.'.strtolower($post['controller']).'/'.lcfirst($post['action']);
        $file_arr =  file(APP_PATH. 'api/route.php');
        $cache_time_str = $post['cache_time'] !== ''
            ? ", ['api_cache' => ".$post['cache_time']."]" : ''; // 缓存时间
        $new = "Route::".strtolower($post['method'])."('".$rule."', '".$route."'".$cache_time_str."); //".$post['title']."\r\n";
        $exist_route = false;
        foreach($file_arr as $index => $line) {
            if(stripos($line, $old_route)) {
                $exist_route = true;
                if($new != $line) {
                    $file_arr[ $index ] = $new;
                    break;
                }
            }
        }
        if(!$exist_route) {
            $file_arr[] = "\r\n".$new;
        }
        file_put_contents(APP_PATH. 'api/route.php', implode('', $file_arr));


        // 修改参数
        $post['version'] = $info['version'];
        $this->reWriteParam($post,  new \ReflectionClass($info['doc_entity']) );
        return json( ['result'=>1] );
    }

    protected function insert() {
        $post = $this->request->post();
        $post['controller'] = ucfirst($post['controller']);

        $class_dir = APP_PATH.'api/controller/'.$post['version'].'/';
        $class_filename = $class_dir . $post['controller'].'.php';
        $class_name = "\\app\\api\\controller\\".$post['version']."\\". $post['controller'];

        // 创建控制器
        if(!file_exists($class_filename)) {
            if( !file_exists( $class_dir ) ) {
                mkdir($class_dir);
            }
            $content = "<?php
namespace app\\api\\controller\\". $post['version'] .";
use app\\api\\controller\\BaseController;

/**
 * @title ".$post['group']."
 */
class ".$post['controller']." extends BaseController
{
}";
        } else {
            $class = new \ReflectionClass( $class_name );
            if( $class->hasMethod( $post['action'] ) ) {
                return json(['result'=>0, 'msg'=> '控制器方法已存在']);
            }
            $content = file_get_contents( $class_filename );
        }
        $content = trim( $content );
        $content = rtrim( $content, '}' );
        $content .= "\r\n    /**
     * @title ". $post['title'] ."
     * @doc \\app\\api\\param\\".strtolower($post['controller'])."\\".ucfirst($post['action'])."
     */
    public function ".$post['action']."() {
        return \$this->response();
    }
}";
        file_put_contents( $class_filename, $content );
        // 创建参数文件
        $param_dir = APP_PATH . "api/param/" . strtolower($post['controller']) . "/";
        $param_filename = $param_dir . ucfirst($post['action']) . '.php';
        $param_class_name =  "app\\api\\param\\". strtolower($post['controller'])."\\".ucfirst($post['action']);
        if(!file_exists($param_dir)) {
            mkdir( $param_dir );
        }
        $p_content ="<?php
namespace app\\api\\param\\". strtolower($post['controller']) .";
use think\Validate;

class ". ucfirst($post['action']) ." extends Validate
{
}";
        file_put_contents($param_filename, $p_content);
        $this->reWriteParam($post, new \ReflectionClass($param_class_name) );
        // 插入config.controller
        $controller = Config::get('controller');
        if(!in_array(ltrim($class_name, "\\"), $controller)) {
            $config_content = file_get_contents( APP_PATH."docs/config.php" );
            $config_content = str_replace("'controller' => [", "'controller' => [\r\n        '" . ltrim($class_name, "\\") . "',", $config_content);
            file_put_contents(APP_PATH."docs/config.php", $config_content);
        }
        // 新建 route
        $rule = 'api/:version/'.$post['uri'];
        $route = 'api/:version.'.strtolower($post['controller']).'/'.lcfirst($post['action']);
        $cache_time_str = $post['cache_time'] !== ''
            ? ", ['api_cache' => ".$post['cache_time']."]" : ''; // 缓存时间
        $new_line = "\r\nRoute::".strtolower($post['method'])."('".$rule."', '".$route."'".$cache_time_str."); //".$post['title'];
        file_put_contents(APP_PATH . 'api/route.php', $new_line, FILE_APPEND);

        return json( ['result'=>1, 'name' => ltrim($class_name, "\\")."::".$post['action']] );
    }

    protected function reWriteParam($post, \ReflectionClass $entityRef) {
        $new_param = $new_rule = [];
        if(isset($post['param'][0])) {
            foreach($post['param'] as $param) {
                $key = $param['name'];
                $new_rule[ $key ] = $param['rule'];
                $new_param[ $key ] = [
                    'default' => $param['default'],
                    'desc' => $param['desc']
                ];
            }
        }
        $e_file_arr = file( $entityRef->getFileName() );
        $start_line = $entityRef->getStartLine();
        strpos($e_file_arr[ $start_line - 1 ], '{') || $e_file_arr[ $start_line - 1 ] .= "{\r\n";
        $new = array_slice($e_file_arr, 0, $start_line);
        $new[] = "    public \$rule = [\r\n";
        foreach($new_rule as $key => $r) {
            $new[] = "        '{$key}' => '{$r}',\r\n";
        }
        $new[] = "    ];\r\n\r\n";
        $new[] = "    public \$doc = [\r\n";
        $new[] = "        'description' => '{$post['desc']}',\r\n";
        $new[] = "        'url' => '/api/".$post['version'].'/'.$post['uri']."',\r\n";
        $new[] = "        'method' => '".strtoupper($post['method'])."',\r\n";
        $new[] = "        'param' => [\r\n";
        foreach($new_param as $key => $p) {
            $new[] = "            '{$key}' => ['default'=>'{$p['default']}', 'desc'=> '".addslashes($p['desc'])."'],\r\n";
        }
        $new[] = "        ],\r\n    ];\r\n}";
        file_put_contents( $entityRef->getFileName() , implode('', $new));
    }

    protected function onSubmitValidate() {
        if($this->request->method() == 'POST') {
            return true;
        }
    }

    protected function getGroups() {
        $controller = Config::get('controller');
        $groups = [];
        foreach ($controller as $class) {
            $reflection = new \ReflectionClass($class);
            $doc_str = $reflection->getDocComment();
            $doc = new DocParser();
            $class_doc = $doc->parse($doc_str);
            $groups[] = $class_doc['title'];
        }
        return $groups;
    }

    /**
     * 获取类中指导方法注释详情
     * @param $class
     * @param $action
     * @return array
     */
    protected function getInfo($class, $action)
    {
        $result = [
            'controller' => ltrim( strrchr($class, "\\") , "\\"),
            'action' => $action
        ];
        if($class && class_exists($class)){
            $result['class'] = $reflection = new \ReflectionClass($class);
            $doc = new DocParser();
            $class_doc = $doc->parse($reflection->getDocComment());
            $result['group'] = $class_doc['title'];
            if($reflection->hasMethod($action)) {
                $result['method'] = $method = $reflection->getMethod($action);
                $doc = new DocParser();
                $action_doc = $doc->parse($method->getDocComment());
                isset($action_doc['title']) && $result['title'] = $action_doc['title'];

                if(isset($action_doc['doc'])) {
                    $result['doc'] = ltrim( $action_doc['doc'], "\\");
                    $result['doc_entity'] = $entity = new $action_doc['doc'];
                    if(isset($entity->doc) && !empty($entity->doc)) {
                        $chunk = explode('/', $entity->doc['url']);
                        $result['version'] = $chunk[2];
                        $result['uri'] = implode("/", array_slice($chunk, 3));
                        if(isset($entity->doc['param'])) {
                            foreach($entity->doc['param'] as $key => $arr) {
                                $entity->doc['param'][$key]['name'] = $key;
                                $entity->doc['param'][$key]['rule'] = isset($entity->rule[$key]) ? $entity->rule[$key] : '';
                            }
                        }
                        $result['form_method'] = $entity->doc['method'];
                        # 获取路由配置
                        $routes = Route::rules($result['form_method']);
                        $option = isset( $routes['api/:version/'.$result['uri']]['option'] ) ? $routes['api/:version/'.$result['uri']]['option'] : [];
                        $result['cache_time'] = in_array('api_cache', array_keys($option) ) ? $option['api_cache'] : '';
                        $result['cache_time'] === null &&  $result['cache_time'] = 'null';
                        $result += $entity->doc;
                    }
                }
            }
        }
        return $result;
    }
}