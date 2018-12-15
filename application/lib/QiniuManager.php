<?php
# QiniuManager.php

namespace app\lib;
use think\Config;
use Qiniu\Auth;


class QiniuManager
{
    protected $auth;

    protected $bucket;

    public function __construct($default_bucket = '')
    {
        $config = Config::get('qiniu');
        $this->auth = new Auth($config['accessKey'], $config['secretKey']);
        $this->bucket = $default_bucket ? $default_bucket : $config['default_bucket'];
    }

    /**
     * 上传凭证
     * @param $bucket
     * @param null|string $key
     * @param int $expires
     * @param null|array $policy
     * @param bool $strictPolicy
     * @return string
     */
    public function uploadToken($bucket = '', $key = null, $expires = 36000, $policy = null, $strictPolicy = true) {
        $bucket || $bucket = $this->bucket;
        return $this->auth->uploadToken($bucket, $key, $expires, $policy, $strictPolicy);
    }
}