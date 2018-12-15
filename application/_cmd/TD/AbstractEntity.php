<?php
# AbstractFaker.php

namespace app\_cmd\TD;

use think\console\Input;
use think\console\Output;
use think\Loader;

abstract class AbstractEntity
{
    protected $input;
    protected $output;
    protected $faker;

    protected $heros = ['梦奇', '苏烈', '百里玄策', '百里守约', '铠', '鬼谷子', '干将莫邪', '东皇太一', '大乔', '黄忠',
        '诸葛亮', '哪吒', '太乙真人', '蔡文姬', '雅典娜', '杨戬', '成吉思汗', '钟馗', '虞姬', '李元芳', '张飞',
        '刘备', '后羿', '牛魔', '孙悟空', '亚瑟', '橘右京', '娜可露露', '不知火舞', '张良', '花木兰', '兰陵王',
        '王昭君', '韩信', '刘邦', '姜子牙', '露娜', '程咬金', '安琪拉', '貂蝉', '关羽', '老夫子', '武则天', '项羽',
        '达摩', '狄仁杰', '马可波罗', '李白', '宫本武藏', '典韦', '曹操', '甄姬', '夏侯惇', '周瑜', '吕布', '芈月',
        '白起', '扁鹊', '孙膑', '钟无艳', '阿轲', '高渐离', '刘禅', '庄周', '鲁班七号', '孙尚香', '嬴政', '妲己',
        '墨子', '赵云', '小乔', '廉颇'];

    protected $position = ['上单', '中单', '打野', 'ADC', '辅助'];

    public function __construct(Input $input, Output $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->faker = \Faker\Factory::create('zh_CN');
    }

    abstract function run($length);

    protected function getArgument($name, $default=null) {
        return $this->input->hasArgument($name) ? $this->input->getArgument($name) : $default;
    }

    protected function output($messages) {
        $this->output->writeln($messages);
        return $this;
    }

    /**
     * @param string $name
     * @param bool $appendSuffix
     * @param string $common
     * @return \think\Model
     */
    protected function m($name = '', $appendSuffix = false, $common = 'api') {
        if(!$name) {
            $class = get_class($this);
            $name = substr(strrchr($class, "\\"), 1);
        }
        return Loader::model($name, 'model', $appendSuffix, $common);
    }

    /**
     * 随机取出擅长英雄
     * @return string
     */
    protected function hero() {
        shuffle($this->heros);
        $hero = array_slice($this->heros, 0, mt_rand(1, 5));
        return implode('、', $hero);
    }

    protected function honour() {
        return $this->faker->state . '第一' . $this->heros[ array_rand($this->heros) ];
    }
}