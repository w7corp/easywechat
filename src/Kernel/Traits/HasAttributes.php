<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Support\Collection;

/**
 * Trait Attributes.
 */
trait HasAttributes
{
    /**
     * @var \EasyWeChat\Kernel\Support\Collection
     */
    protected $attributes;

    /**
     * @var array
     */
    protected $aliases = [];

    /**
     * @var array
     */
    protected $requirements = [];

    /**
     * @var bool
     */
    protected $snakeable = true;

    /**
     * Set Attributes.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes = [])
    {
        $this->attributes = new Collection($attributes);

        return $this;
    }

    /**
     * Set attribute.
     *
     * @param string $attribute
     * @param string $value
     *
     * @return HasAttributes
     */
    public function setAttribute($attribute, $value)
    {
        $this->attributes->set($attribute, $value);

        return $this;
    }

    /**
     * Get attribute.
     *
     * @param string $attribute
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getAttribute($attribute, $default)
    {
        return $this->attributes->get($attribute, $default);
    }

    /**
     * Set attribute.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return HasAttributes
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function with($attribute, $value)
    {
        $this->snakeable && $attribute = Str::snake($attribute);

        if (!$this->validate($attribute, $value)) {
            throw new InvalidArgumentException("Invalid attribute '{$attribute}'.");
        }

        $this->setAttribute($attribute, $value);

        return $this;
    }

    /**
     * HasAttributes validation.
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
     * Override parent set() method.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($attribute, $value = null)
    {
        $this->setAttribute($this->getRealKey($attribute), $value);

        return $this;
    }

    /**
     * Override parent get() method.
     *
     * @param string $attribute
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($attribute, $default = null)
    {
        return $this->getAttribute($this->getRealKey($attribute), $default);
    }

    /**
     * Return all items.
     *
     * @return array
     */
    public function all()
    {
        $this->checkRequiredAttributes();

        return $this->attributes->all();
    }

    /**
     * Magic call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return $this
     */
    public function __call($method, $args)
    {
        if (stripos($method, 'with') === 0) {
            return $this->with(substr($method, 4), array_shift($args));
        }

        return call_user_func_array([$this->attributes, $method], $args);
    }

    /**
     * Magic get.
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->get($property);
    }

    /**
     * Magic set.
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return $this
     */
    public function __set($property, $value)
    {
        return $this->with($property, $value);
    }

    /**
     * Whether or not an data exists by key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$this->getRealKey($key)]);
    }

    /**
     * Return the raw name of attribute.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getRealKey($key)
    {
        if ($alias = array_search($key, $this->aliases, true)) {
            $key = $alias;
        }

        return $key;
    }

    /**
     * Check required attributes.
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    protected function checkRequiredAttributes()
    {
        foreach ($this->requirements as $attribute) {
            if (!isset($this->attributes, $attribute)) {
                throw new InvalidArgumentException("'{$attribute}' cannot be empty.");
            }
        }
    }
}
