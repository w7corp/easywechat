<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Messages;

use EasyWeChat\Kernel\Traits\HasAttributes;

/**
 * Class Messages.
 */
abstract class Message
{
    use HasAttributes;

    const TEXT = 2;
    const IMAGE = 4;
    const VOICE = 8;
    const VIDEO = 16;
    const SHORT_VIDEO = 32;
    const LOCATION = 64;
    const LINK = 128;
    const DEVICE_EVENT = 256;
    const DEVICE_TEXT = 512;
    const FILE = 1024;
    const TEXT_CARD = 2048;
    const EVENT = 1048576;
    const ALL = 1049598;

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
     * Message constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setAttributes($attributes);
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

        return $this->getAttribute($property);
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
            $this->setAttribute($property, $value);
        }

        return $this;
    }
}
