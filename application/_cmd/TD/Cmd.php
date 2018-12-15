<?php
/*
 * 用于生成测试数据， 例如：
 * TD User 100 向User表插入100个用户
 */

namespace app\_cmd\TD;
require_once APP_PATH . 'api/common.php';

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Config;

class Cmd extends Command
{
    protected function configure()
    {
        $this->setName('TD')
            ->setDescription('测试数据生成')
            ->addArgument('entity')
            ->addArgument('num');
    }

    protected function execute(Input $input, Output $output) {
        if(Config::get('app_debug') ) {
            $name = ucfirst($input->getArgument('entity'));
            $class = "\\app\\_cmd\\TD\\".$name;
            $num = $input->getArgument('num');
            if(class_exists($class)) {
                $entity = new $class($input, $output);
                $entity->run($num);
//                while ($num > 0) {
//                    $length = min($num, 100);
//                    $num -= 100;
//                    $entity->run($length);
//                }
            }
        }
    }
}