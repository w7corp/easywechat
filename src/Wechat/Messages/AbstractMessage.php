<?php

namespace Overtrue\Wechat\Messages;

use Closure;
use InvalidArgumentException;

/**
 * @method array  formatToClient() formatToClient()
 * @method string formatToServer() formatToServer()
 */
abstract class AbstractMessage
{

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
     * 用户组ID
     *
     * @var boolean
     */
    protected $groupId;

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
     * 方法名转换缓存
     *
     * @var array
     */
    static protected $snakeCache = array();


    /**
     * 设置发送者
     *
     * @param string $account
     *
     * @return Overtrue\Wechat\Message
     */
    public function from($account)
    {
        $this->from = $account;

        return $this;
    }

    /**
     * 发送消息
     *
     * @return [type] [description]
     */
    public function send()
    {
        var_dump('abaksndsa');
        var_dump($this->formatToClient());
        $response = $this->postRequest(self::API_SEND, $this->formatToClient());
        var_dump($response);
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

        $attribute = $this->snake($attribute);

        if (!in_array($attribute, $this->properties)) {
            throw new InvalidArgumentException("不存在的属性‘{$attribute}’");
        }

        $this->attributes[$attribute] = $value;

        return $this;
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
        return !isset($this->attributes[$property]) ? null : $this->attributes[$property];
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

    /**
     * 转换为下划线模式字符串
     *
     * @param string $value
     * @param string $delimiter
     *
     * @return string
     */
    public function snake($value, $delimiter = '_')
    {
        $key = $value . $delimiter;

        if (isset(static::$snakeCache[$key]))
        {
            return static::$snakeCache[$key];
        }

        if ( ! ctype_lower($value))
        {
            $value = strtolower(preg_replace('/(.)(?=[A-Z])/', '$1'.$delimiter, $value));
        }

        return static::$snakeCache[$key] = $value;
    }
}