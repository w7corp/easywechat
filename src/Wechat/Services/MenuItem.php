<?php

namespace Overtrue\Wechat\Services;

use Closure;

class MenuItem
{
    /**
     * 菜单属性
     *
     * @var array
     */
    protected $attributes;

    /**
     * 实例化菜单
     *
     * @param string $name
     * @param string $type
     * @param string $property
     */
    public function __construct($name, $type = null, $property = null)
    {
        $this->attributes['name'] = $name;
        $type && $this->attributes['type'] = $type;

        if ($property) {
            $key = ($type == 'view') ? 'url' : 'key';
            $this->attributes[$key] = $property;
        }
    }

    /**
     * 魔术调用
     *
     * @param string $method
     * @param array  $args
     *
     * @return MenuItem
     */
    public function __call($method, $args)
    {
        if (stripos($method, 'with') === 0) {
            $method = substr($method, 4);
        }

        $this->attributes[$method] = array_shift($args);

        return $this;
    }

    /**
     * 设置子菜单
     *
     * @param array $buttons
     *
     * @return MenuItem;
     */
    public function buttons($buttons)
    {
        if ($buttons instanceof Closure) {
            $buttons = $buttons($this);
        }

        if (!is_array($buttons)) {
            throw new Exception("子菜单必须是数组或者匿名函数返回数组", 1);
        }

        $this->attributes['sub_button'] = $buttons;

        return $this;
    }

    /**
     * 生成数组
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * 获取属性
     *
     * @param string $property
     *
     * @return string
     */
    public function __get($property)
    {
        return empty($this->attributes[$property]) ? null : $this->attributes[$property];
    }

    /**
     * 设置属性
     *
     * @param string $property
     * @param string $value
     */
    public function __set($property, $value)
    {
        $this->attributes[$property] = strval($value);
    }
}