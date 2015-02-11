<?php

namespace Overtrue\Wechat\Messages;

use Closure;
use InvalidArgumentException;
use Overtrue\Wechat\Utils\XML;

/**
 * @method   array  buildForStaff() buildForStaff()
 * @method   string buildForReply() buildForReply()
 * @method   array  toStaff()       toStaff()
 * @method   array  toReply()       toReply()
 * @method   array  toBroadcast()   toBroadcast()
 * @property \Overtrue\Wechat\Messages\BaseMessage from() from()
 * @property \Overtrue\Wechat\Messages\BaseMessage to()   to()
 * @property string $from
 * @property string $to
 */
abstract class BaseMessage
{
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
     * 基础属性
     *
     * @var array
     */
    protected $baseProperties = array('from', 'to', 'to_group', 'to_all', 'staff');

    /**
     * 方法名转换缓存
     *
     * @var array
     */
    static protected $snakeCache = array();


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

        if (!in_array($attribute, array_merge($this->baseProperties, $this->properties))) {
            throw new InvalidArgumentException("不存在的属性‘{$attribute}’");
        }

        $this->attributes[$attribute] = $value;

        return $this;
    }

    /**
     * 生成用于主动推送的数据
     *
     * @return array
     */
    public function buildForStaff()
    {
        if (!method_exists($this, 'toStaff')) {
            throw new Exception(__CLASS__ . "未实现此方法：toStaff()");
        }

        $base = array(
                 'touser'  => $this->to,
                 'msgtype' => $this->getDefaultMessageType(),
                );
        if (!empty($this->staff)) {
            $base['customservice'] = array('kf_account' => $this->staff);
        }

        return array_merge($base, $this->toStaff());
    }

    /**
     * 生成用于回复的数据
     *
     * @return array
     */
    public function buildForReply()
    {
        if (!method_exists($this, 'toReply')) {
            throw new Exception(__CLASS__ . "未实现此方法：toReply()");
        }

         $base = array(
                     'ToUserName'   => $this->to,
                     'FromUserName' => $this->from,
                     'CreateTime'   => time(),
                     'MsgType'      => $this->getDefaultMessageType(),
                    );

        return XML::build(array_merge($base, $this->toReply()));
    }

    /**
     * 生成群发的数据
     *
     * @return array
     */
    public function buildForBroadcast()
    {
        if (!method_exists($this, 'toBroadcast')) {
            throw new Exception(__CLASS__ . "未实现此方法：toBroadcast()");
        }

        //TODO
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
        if (stripos($method, 'with') === 0) {
            $method = substr($method, 4);
        }

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
     * 获取默认的消息类型名称
     *
     * @return string
     */
    public function getDefaultMessageType()
    {
        $class = explode('\\', get_class($this));

        return strtolower(array_pop($class));
    }

    /**
     * 转换为下划线模式字符串
     *
     * @param string $value
     * @param string $delimiter
     *
     * @return string
     */
    protected function snake($value, $delimiter = '_')
    {
        $key = $value . $delimiter;

        if (isset(static::$snakeCache[$key])) {
            return static::$snakeCache[$key];
        }

        if (!ctype_lower($value)) {
            $value = strtolower(preg_replace('/(.)(?=[A-Z])/', '$1' . $delimiter, $value));
        }

        return static::$snakeCache[$key] = $value;
    }
}