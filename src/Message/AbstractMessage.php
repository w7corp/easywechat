<?php

/*
 * This file is part of the EasyWeChat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * AbstractMessage.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Message;

use EasyWeChat\Support\Arr;
use EasyWeChat\Support\Attribute;

/**
 * Class AbstractMessage.
 */
abstract class AbstractMessage extends Attribute
{
    /**
     * Message id.
     *
     * @var int
     */
    protected $id;

    /**
     * Message target user open id.
     *
     * @var string
     */
    protected $to;

    /**
     * Message sender open id.
     *
     * @var string
     */
    protected $from;

    /**
     * Message attributes.
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct(Arr::only($attributes, $this->properties));
    }

    /**
     * Return type name message.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Magic getter.
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        return parent::__get($property);
    }

    /**
     * Magic setter.
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return AbstractMessage
     */
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        } else {
            parent::__set($property, $value);
        }

        return $this;
    }
}
