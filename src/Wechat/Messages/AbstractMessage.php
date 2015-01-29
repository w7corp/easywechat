<?php namespace Overtrue\Wechat\Messages;

abstract class Abstract {

    /**
     * 发送者
     *
     * @var string
     */
    protected $from;

    /**
     * 接收人
     *
     * @var mixed
     */
    protected $to;

    /**
     * 是否群发
     *
     * @var boolean
     */
    protected $toAll = false;

    /**
     * 消息属性
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * 允许设置的属性名称
     *
     * @var array
     */
    protected $properties = array();


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
        # TODO
    }

    public function toGroup($groupId)
    {
        # TODO
    }

    public function toAll()
    {
        # TODO
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