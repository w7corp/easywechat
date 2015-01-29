<?php namespace Overtrue\Wechat;

use Closure;
use InvalidArgumentException;

class Message {

    /**
     * 消息类型
     */
    const TEXT  = 'text';
    const IMAGE = 'image';
    const VOICE = 'voice';
    const VIDEO = 'video';
    const MUSIC = 'music';
    const NEWS  = 'news';

    /**
     * 消息属性
     *
     * @var array
     */
    protected $attributes = array();


    /**
     * constructor
     *
     * @param string $type
     */
    public function __construct($type)
    {
        $this->attributes['MsgType'] = $type;
    }

    /**
     * 创建消息实例
     *
     * @param string $type
     *
     * @return Overtrue\Wechat\Message
     */
    static public function make($type)
    {
        if (!defined(__CLASS__ . '::' . strtoupper($type)) {
            throw new InvalidArgumentException("Error Message Type '{$type}'");
        }

        return new static(strtolower($type));
    }

    /**
     * 设置属性
     *
     * @param string $attribute
     * @param string $value
     *
     * @return Overtrue\Wechat\Message
     */
    public function with($attribute, $value)
    {
        if (!is_scalar($value)) {
            throw new InvalidArgumentException("属性值只能为标量");
        }

        $this->attributes[$method] = $value;

        return $this;
    }

    /**
     * 添加图文消息内容
     *
     * @return void
     */
    public function item()
    {
        $args    = func_get_args();
        $argsLen = func_num_args();

        if ($argsLen && $args[0] instanceof Closure) {
            return $args($this);
        }

        if ($argsLen < 3) {
            throw new InvalidArgumentException("item方法要求至少3个参数：标题，描述，图片");
        }

        list($title, $description, $image, $url = '') = $args;

        $item = array(
            'Title'       => $title,
            'Description' => $description,
            'PicUrl'      => $image,
            'Url'         => $url,
        );

        !empty($this->attributes['items']) || $this->attributes['items'] = array();

        array_push($this->attributes['items'], $item);
    }

    /**
     * 设置发送者
     *
     * @param string $account
     *
     * @return Overtrue\Wechat\Message
     */
    public function from($account)
    {
        //TODO:
        return $this;
    }

    /**
     * 设置接收者
     *
     * @param string|array $openId
     *
     * @return Overtrue\Wechat\Message
     */
    public function to($openId)
    {
        # code...
    }

    public function toGroup($groupId)
    {
        # code...
    }

    public function toAll()
    {
        # code...
    }

    public function build()
    {
        return $this->attributes;
    }

    /**
     * 调用不存在的方法
     *
     * @param string $method
     * @param array  $args
     *
     * @return Overtrue\Wechat\Message
     */
    public function __call($method, $args)
    {
        return $this->with($method, array_shift($args));
    }

    /**
     * 魔术读取
     *
     * @param string $property
     */
    public function __get($property)
    {
        return !isset($this->attributes[$property]) ? null : $property;
    }

    /**
     * 魔术写入
     *
     * @param string $property
     * @param mixed  $value
     */
    public function __set($property, $value)
    {
        return $this->with($property, $value);
    }
}