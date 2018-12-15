<?php
namespace app\lib\jiguang\payload;

use app\api\model\OrderMessage;
use app\api\model\SysMessage;

/**
 * Class abstractPushPayload
 * @package app\lib\jiguang\payload
 * @method mixed init()
 */
class abstractPushPayload
{
    protected $option = [];
    protected $platform = 'all';

    protected $content;
    protected $type;
    protected $title = null;
    protected $extras = [];

    protected $alias = [];
    protected $tag = [];
    protected $tagAnd = [];

    protected $isNotification = true; // 是否为通知， false 表示透传消息

    /**
     * @return bool
     */
    public function isNotification()
    {
        return $this->isNotification;
    }

    /**
     * @param bool $isNotification
     * @return abstractPushPayload
     */
    public function setIsNotification($isNotification)
    {
        $this->isNotification = $isNotification;
        return $this;
    }


    /**
     * @param string|array $platform
     * @param string|true $value
     * @return abstractPushPayload
     */
    public function setOption($option, $value=null) {
        $this->option = $this->capSet($this->option, $option, $value);
        return $this;
    }

    /**
     * @return array
     */
    public function getOption() {
        return $this->option;
    }

    /**
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param string $platform
     * @return abstractPushPayload
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return abstractPushPayload
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param integer $type
     * @return abstractPushPayload
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return array
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * @param array|string $extras
     * @param string|true $value
     * @return abstractPushPayload
     */
    public function setExtras($extras, $value=null)
    {
        $this->extras = $this->capSet($this->extras, $extras, $value);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return abstractPushPayload
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param array|string $alias
     * @return abstractPushPayload
     */
    public function addAlias($alias)
    {
        if($alias) {
            is_array($alias) || $alias = array($alias);
            $this->alias = array_merge( $this->alias, $alias);
            $this->alias = array_unique($this->alias);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param array|string $tag
     * @return abstractPushPayload
     */
    public function addTag($tag)
    {
        is_array($tag) || $tag = array($tag);
        $this->tag = array_merge( $this->tag, $tag);
        $this->tag = array_unique($this->tag);
        return $this;
    }

    /**
     * @return array
     */
    public function getTagAnd()
    {
        return $this->tagAnd;
    }

    /**
     * @param array|string $tagAnd
     * @return abstractPushPayload
     */
    public function addTagAnd($tagAnd)
    {
        is_array($tagAnd) || $tagAnd = array($tagAnd);
        $this->tagAnd = array_merge( $this->tagAnd, $tagAnd);
        $this->tagAnd = array_unique($this->tagAnd);
        return $this;
    }

    protected function capSet(array $data , $name, $value=null) {
        if(is_string($name)) {
            $data[ $name ] = $value;
        } elseif(is_array($name)) {
            if($value === true) {
                $data = $name;
            } else {
                $data = array_merge($data, $name);
            }
        }
        foreach($data as $key => $value) {
            if($value === null) {
                unset($data[ $key ]);
            }
        }
        return $data;
    }

    /**
     * 判断存在接收方
     */
    public function hasAudience() {
        $emp = empty($this->getAlias()) && empty($this->getTag()) && empty($this->getTagAnd());
        return !$emp;
    }

    /**
     * 保存到订单消息表
     * @param $user
     * @param $order
     * @param $content
     * @return OrderMessage
     */
    public function saveToOrderMessage($user, $order, $content=null) {
        $data = [
            'order_id' => $order->id,
            'user_id' => $user->id,
            'type' => $this->getType(),
            'content' => $content ? $content : $this->getContent(),
            'extras' => serialize($this->getExtras())
        ];

        $message = new OrderMessage();
        $message->data($data)->save();
        return $message;
    }

    public function saveToSysMessage($user, $content=null, $extras=null) {
        $extras || $extras = $this->getExtras();
        $data = [
            'user_id' => $user->id,
            'type' => $this->getType(),
            'content' => $content ? $content : $this->getContent(),
            'extras' => serialize($extras),
        ];

        $message = new SysMessage();
        $message->data($data)->save();
        return $message;
    }
}