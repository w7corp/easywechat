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
use EasyWeChat\Kernel\Support\Arr;
use EasyWeChat\Kernel\Support\Str;

/**
 * Trait Attributes.
 */
trait HasAttributes
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var bool
     */
    protected $snakeable = true;

    /**
     * Set Attributes.
     *
     * @return $this
     */
    public function setAttributes(array $attributes = [])
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Set attribute.
     *
     * @param string $attribute
     * @param string $value
     *
     * @return $this
     */
    public function setAttribute($attribute, $value)
    {
        Arr::set($this->attributes, $attribute, $value);

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
    public function getAttribute($attribute, $default = null)
    {
        return Arr::get($this->attributes, $attribute, $default);
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function isRequired($attribute)
    {
        return in_array($attribute, $this->getRequired(), true);
    }

    /**
     * @return array|mixed
     */
    public function getRequired()
    {
        return property_exists($this, 'required') ? $this->required : [];
    }

    /**
     * Set attribute.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return $this
     */
    public function with($attribute, $value)
    {
        $this->snakeable && $attribute = Str::snake($attribute);

        $this->setAttribute($attribute, $value);

        return $this;
    }

    /**
     * Override parent set() method.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($attribute, $value)
    {
        $this->setAttribute($attribute, $value);

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
        return $this->getAttribute($attribute, $default);
    }

    /**
     * @return bool
     */
    public function has(string $key)
    {
        return Arr::has($this->attributes, $key);
    }

    /**
     * @return $this
     */
    public function merge(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * @param array|string $keys
     *
     * @return array
     */
    public function only($keys)
    {
        return Arr::only($this->attributes, $keys);
    }

    /**
     * Return all items.
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function all()
    {
        $this->checkRequiredAttributes();

        return $this->attributes;
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
        if (0 === stripos($method, 'with')) {
            return $this->with(substr($method, 4), array_shift($args));
        }

        throw new \BadMethodCallException(sprintf('Method "%s" does not exists.', $method));
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
        return isset($this->attributes[$key]);
    }

    /**
     * Check required attributes.
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    protected function checkRequiredAttributes()
    {
        foreach ($this->getRequired() as $attribute) {
            if (is_null($this->get($attribute))) {
                throw new InvalidArgumentException(sprintf('"%s" cannot be empty.', $attribute));
            }
        }
    }
}
