<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * MagicAttributes.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat\Utils;

use InvalidArgumentException;

/**
 * 用于操作通用数组式属性的工具类.
 */
abstract class MagicAttributes
{
    /**
     * 允许设置的属性名称.
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * 方法名转换缓存.
     *
     * @var array
     */
    protected static $snakeCache = array();

    /**
     * 设置属性.
     *
     * @param string $attribute
     * @param string $value
     */
    public function setAttribute($attribute, $value)
    {
        return $this->with($attribute, $value);
    }

    /**
     * 设置属性.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return MagicAttributes
     */
    public function with($attribute, $value)
    {
        $attribute = $this->snake($attribute);

        if (!$this->validate($attribute, $value)) {
            throw new InvalidArgumentException("错误的属性值'{$attribute}'");
        }

        $this->attributes[$attribute] = $value;

        return $this;
    }

    /**
     * 生成数组.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * 验证
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    protected function validate($attribute, $value)
    {
        return true;
    }

    /**
     * 调用不存在的方法.
     *
     * @param string $method
     * @param array  $args
     *
     * @return MagicAttributes
     */
    public function __call($method, $args)
    {
        if (stripos($method, 'with') === 0) {
            $method = substr($method, 4);
        }

        return $this->with($method, array_shift($args));
    }

    /**
     * 魔术读取.
     *
     * @param string $property
     */
    public function __get($property)
    {
        return !isset($this->attributes[$property]) ? null : $this->attributes[$property];
    }

    /**
     * 魔术写入.
     *
     * @param string $property
     * @param mixed  $value
     */
    public function __set($property, $value)
    {
        return $this->with($property, $value);
    }

    /**
     * 转换为下划线模式字符串.
     *
     * @param string $value
     * @param string $delimiter
     *
     * @return string
     */
    protected function snake($value, $delimiter = '_')
    {
        $key = $value.$delimiter;

        if (isset(static::$snakeCache[$key])) {
            return static::$snakeCache[$key];
        }

        if (!ctype_lower($value)) {
            $value = strtolower(preg_replace('/(.)(?=[A-Z])/', '$1'.$delimiter, $value));
        }

        return static::$snakeCache[$key] = $value;
    }
}
