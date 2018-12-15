<?php

/**
 *  自定义CLi命令行模式
 *  如 php think Crontab Match getRankSportList
 */

namespace app\_cmd\TD;
require_once APP_PATH . 'api/common.php';

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Config;

class Crontab extends Command
{
    protected function configure()
    {
        $this->setName('Crontab')
            ->setDescription('自定义定时计划')
            ->addArgument('entity')
            ->addArgument('action');
    }

    protected function execute(Input $input, Output $output) {
        if(Config::get('app_debug') ) {
            $name = ucfirst($input->getArgument('entity'));
            $class = "\\app\\_cmd\\TD\\".$name;
            $action = $input->getArgument('action');
            if(class_exists($class)) {
                $entity = new $class($input, $output);
                $entity->run($action);
            }
        }
    }
}