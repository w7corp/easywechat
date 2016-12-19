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
 * Attributes.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Support;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;

/**
 * Class Attributes.
 */
abstract class Attribute extends Collection
{
    /**
     * Attributes alias.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * Auto snake attribute name.
     *
     * @var bool
     */
    protected $snakeable = true;

    /**
     * Required attributes.
     *
     * @var array
     */
    protected $requirements = [];

    /**
     * Constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Set attribute.
     *
     * @param string $attribute
     * @param string $value
     *
     * @return Attribute
     */
    public function setAttribute($attribute, $value)
    {
        $this->set($attribute, $value);

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
        return $this->get($attribute, $default);
    }

    /**
     * Set attribute.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return Attribute
     *
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function with($attribute, $value)
    {
        $this->snakeable && $attribute = Str::snake($attribute);

        if (!$this->validate($attribute, $value)) {
            throw new InvalidArgumentException("Invalid attribute '{$attribute}'.");
        }

        $this->set($attribute, $value);

        return $this;
    }

    /**
     * Attribute validation.
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
     */
    public function set($attribute, $value = null)
    {
        parent::set($this->getRealKey($attribute), $value);
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
        return parent::get($this->getRealKey($attribute), $default);
    }

    /**
     * Magic call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return Attribute
     */
    public function __call($method, $args)
    {
        if (stripos($method, 'with') === 0) {
            $method = substr($method, 4);
        }

        return $this->with($method, array_shift($args));
    }

    /**
     * Magic set.
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return Attribute
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
        return parent::__isset($this->getRealKey($key));
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
     * @throws InvalidArgumentException
     */
    protected function checkRequiredAttributes()
    {
        foreach ($this->requirements as $attribute) {
            if (!isset($this->$attribute)) {
                throw new InvalidArgumentException(" '{$attribute}' cannot be empty.");
            }
        }
    }

    /**
     * Return all items.
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function all()
    {
        $this->checkRequiredAttributes();

        return parent::all();
    }
}
