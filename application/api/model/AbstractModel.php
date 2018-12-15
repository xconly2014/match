<?php
namespace app\api\model;


use think\Model;
use think\Loader;

abstract class AbstractModel extends Model
{
    protected $columns_filter = [];

    public function data($data, $value = null)
    {
        if (!is_string($data)) {
            if(!empty($this->columns_filter)) {
                $row_data = [];
                foreach($data as $key => $val) {
                    if(in_array($key, $this->columns_filter)) {
                        $row_data[ $key ] = $val;
                    }
                }
                $data = $row_data;
            }
        }
        null === $value && $value = true;
        return parent::data($data, $value);
    }


    public function getInstance($data = []) {
        $class = get_class($this);
        return new $class($data);
    }

    protected function  prefixImgUrl($value, $data){
        $finalUrl = $value;
        if($data['from'] == 1){
            $finalUrl = config('setting.img_prefix').$value;
        }
        return $finalUrl;
    }

    /**
     * 读取时，抑制不存在的数据错误直接返回空字符
     * @param string $name
     * @return mixed|string
     */
    public function __get($name)
    {
        try {
            $value = parent::__get($name);
        } catch (\Exception $e) {
            $value = '';
        }
        return $value;
    }

    public function l() {
        $class = get_class($this);
        $name = substr(strrchr($class, "\\"), 1);
        return Loader::model($name, 'logic', false, 'api');
    }
}