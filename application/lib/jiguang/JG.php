<?php
# Client.php

namespace app\lib\jiguang;

use app\api\model\User;
use app\lib\exception\BaseException;
use app\lib\jiguang\payload\abstractPushPayload;
use JPush\Client;
use think\config;


class JG
{
    protected $config;

    protected $client;

    private static $_manager;

    public static function instance() {
        if(!self::$_manager) {
            self::$_manager = new self;
        }
        return self::$_manager;
    }

    public function __construct()
    {
        $this->config = $config = Config::get('jpush');
        if(isset($config['log_path']) && $config['log_path']) {
            $this->client = new Client($config['appKey'], $config['masterSecret'], $config['log_path']);
        } else {
            $this->client = new Client($config['appKey'], $config['masterSecret']);
        }
    }

    /**
     * @param $name
     * @return abstractPushPayload
     * @throws \Exception
     */
    public function payload($name)
    {
        $class = "\\app\lib\\jiguang\\payload\\" . ucfirst($name);
        if(class_exists($class)) {
            return new $class;
        } else {
            throw new \Exception('Jiguang Payload Is Not Found :' . $name);
        }
    }

    /**
     * IOS 通知消息
     * @param $push
     * @param $content
     * @param $type
     * @param array $extras
     * @return self
     */
    protected function iosNotification($push, $content, $type, array $extras = []) {
        $extras['type'] = $type;
        $notification = ['extras' => $extras, 'badge' => '+1'];
        $push->iosNotification($content, $notification);
        return $this;
    }

    /**
     * 安卓 通知消息
     * @param $push
     * @param $content
     * @param $type
     * @param array $extras
     * @param null $title
     * @return self
     */
    protected function androidNotification($push, $content, $type, array $extras = [], $title=null) {
        $extras['type'] = $type;
        $notification = ['extras' => $extras];
        $title && $notification['title'] = $title;

        $push->androidNotification($content, $notification);
        return $this;
    }

    /**
     * 透传消息
     * @param $push
     * @param $content
     * @param $type
     * @param array $extras
     * @param null $title
     * @param string $content_type
     * @return self
     */
    protected function message ($push, $content, $type, array $extras = [], $title=null, $content_type = 'text') {
        $extras['type'] = $type;
        $msg = ['extras' => $extras];
        $title && $msg['title'] = $title;
        $content_type && $msg['content_type'] = $content_type;
        $push->message($content, $msg);
        return $this;
    }

    /**
     * 执行推送
     * @param abstractPushPayload $payload
     * @param bool $isNotification 是否为通知。若否，则表示透传消息
     * @return array|false
     * @throws BaseException
     */
    public function push(abstractPushPayload $payload) {
        if( $payload->hasAudience() ) {
            $option = $payload->getOption();
            if( !isset($option['apns_production']) && isset($this->config['apns_production']) ) {
                $payload->setOption('apns_production', $this->config['apns_production']);
                $option = $payload->getOption();
            }

            $push = $this->client->push();
            $push->setPlatform($payload->getPlatform());
            $push->options($option);

            $alias = $payload->getAlias();
            $tag = $payload->getTag();
            $tagAnd = $payload->getTagAnd();

            $alias && $push->addAlias($alias);
            $tag && $push->addTag($tag);
            $tagAnd && $push->addTagAnd($tagAnd);

            if($payload->isNotification()) {
                $this->iosNotification($push, $payload->getContent(), $payload->getType(), $payload->getExtras());
                $this->androidNotification($push, $payload->getContent(), $payload->getType(), $payload->getExtras(), $payload->getTitle());
            }
            $this->message($push, $payload->getContent(), $payload->getType(), $payload->getExtras());

            try {
                $response = $push->send();
            } catch (\Exception $e) {
                throw new BaseException(['msg'=>$e->getMessage()]);
            }
            return $response;
        }
        return false;
    }

    /**
     * 重新设置 用户UID为别名 用户标签
     * @param User $user
     * @throws BaseException
     */
    public function resetDevice(User $user) {
        $reg_id = $user->reg_id;
        if($reg_id && $user->uid) {
            try {
                $this->resetTags($user);
                $this->resetAlias($user);
            } catch (\Exception $e) {
                throw new BaseException(['msg'=>$e->getMessage()]);
            }
        }
    }

    public function resetTags(User $user) {
        $logic = $user->l();
        $tags = $logic->getTags($user);
        if($user->reg_id && !empty( $tags )) {
            $this->client->device()->removeTags($user->reg_id, ''); // 清空
            $this->client->device()->addTags($user->reg_id, $tags);
        }
    }

    public function resetAlias(User $user) {
        if($user->reg_id) {
            $this->client->device()->updateAlias($user->reg_id, $user->uid);
        }
    }
}