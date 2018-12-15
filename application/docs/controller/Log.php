<?php
# Log.php

namespace app\docs\controller;

use think\Config;
use think\Controller;
use think\Request;

class Log extends Controller
{
    protected $path;

    public function __construct(Request $request = null)
    {
        $config = Config::get('log');
        $this->path = $config['path'];
        parent::__construct($request);
    }


    public function index() {
        $files = array_reverse($this->traverseDir());
        $content = empty($files) ? '' : $this->replaceStr(file_get_contents($this->path.$files[0].'.log'));
        return $this->fetch('', [
            'content' => $content,
            'files' => $files,
        ]);
    }

    /**
     * js请求直接返回日志内容
     * @param $dir
     * @param $file
     * @return bool|string
     */
    public function content($dir, $file) {
        $file = $this->path . $dir . DS . $file .'.log';
        $content = '';
        if(file_exists($file)) {
            $content = $this->replaceStr( file_get_contents($file) );
        }
        header("Content-Type: text/plain");
        return $content;
    }

    protected function replaceStr($content) {
        if($content) {
            $content = htmlspecialchars($content);
            # 倒叙
            $c_arr = explode("---------------------------------------------------------------\r\n",$content);
            $c_arr = array_reverse($c_arr);
            //$content = implode("\r\n-", $c_arr);
            $content = '<ul class="list-group">';
            foreach($c_arr as $k => $b) {
                $content .= '<li class="list-group-item" style="word-break: break-all; word-wrap: break-word;"><b class="text-danger">' . $b . '</li>';
            }
            $content .= '</ul>';
            # 替换
            $content = str_replace(["\r\n", "\n", "\r"], '<br />', $content);
            $content = str_replace(["[ api ]", "Header:", "Req:", "Resp:", 'Notify:', ']  ', ' GET ', ' POST ', ' PUT ', ' DELETE '],
                [
                    "",
                    "<span class='label label-default'>Header</span>",
                    "<span class='label label-danger'>Req</span>",
                    "<span class='label label-primary'>Resp</span>",
                    "<span class='label label-warning'>Notify</span>",
                    "]</b>  ",
                    " <span class='label label-info'>GET</span> ",
                    " <span class='label label-info'>POST</span> ",
                    " <span class='label label-info'>PUT</span> ",
                    " <span class='label label-info'>DELETE</span> "
                ], $content);
        }
        return $content;
    }

    protected function traverseDir($root="") {
        $files = scandir($this->path.$root);
        $list = [];
        foreach($files as $f) {
            if($f != '.' && $f != '..') {
                $loop = $root.$f;
                if(is_dir($this->path.$loop)) {
                    $list = array_merge($list, $this->traverseDir($loop . '/'));
                } else {
                    if(strpos($loop ,'.log')/*&& strpos($loop ,'_api')*/) {
                        $list[] = str_replace('.log', '', $loop);
                    }
                    if(count($list) >= 50) {
                        break;
                    }
                }
            }
        }
        return $list;
    }
}