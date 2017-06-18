<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Messages;

use EasyWeChat\Support\HasAttributes;

/**
 * Class Messages.
 */
abstract class Message
{
    use HasAttributes;

    /**
     * Messages type.
     *
     * @var string
     */
    protected $type;

    /**
     * Messages id.
     *
     * @var int
     */
    protected $id;

    /**
     * Messages target user open id.
     *
     * @var string
     */
    protected $to;

    /**
     * Messages sender open id.
     *
     * @var string
     */
    protected $from;

    /**
     * Messages attributes.
     *
     * @var array
     */
    protected $properties = [];

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
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
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
     * @return Message
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
