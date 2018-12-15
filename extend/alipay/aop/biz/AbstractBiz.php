<?php
# AbstractBiz.php

abstract class AbstractBiz
{
    protected $biz = array();

    protected $bizContent = NULL;

    public function __get($name)
    {
        return isset($this->biz[ $name ]) ? $this->biz[ $name ] : null;
    }

    public function getBizContent()
    {
        if(!empty($this->biz)){
            $this->bizContent = json_encode($this->biz,JSON_UNESCAPED_UNICODE);
        }
        return $this->bizContent;
    }
}