<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\Broadcasting;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;

/**
 * Class MessageBuilder.
 *
 * @author overtrue <i@overtrue.me>
 */
class MessageBuilder
{
    /**
     * Messages target user or group.
     *
     * @var mixed
     */
    protected $to;

    /**
     * Messages type.
     *
     * @var string
     */
    protected $msgType;

    /**
     * Messages.
     *
     * @var mixed
     */
    protected $message;

    /**
     * Messages types.
     *
     * @var array
     */
    private $msgTypes = [
        Client::MSG_TYPE_TEXT,
        Client::MSG_TYPE_NEWS,
        Client::MSG_TYPE_IMAGE,
        Client::MSG_TYPE_VIDEO,
        Client::MSG_TYPE_VOICE,
        Client::MSG_TYPE_CARD,
    ];

    /**
     * Preview bys.
     *
     * @var array
     */
    private $previewBys = [
        Client::PREVIEW_BY_OPENID,
        Client::PREVIEW_BY_NAME,
    ];

    /**
     * Set message type.
     *
     * @param string $msgType
     *
     * @return MessageBuilder
     *
     * @throws InvalidArgumentException
     */
    public function msgType($msgType)
    {
        if (!in_array($msgType, $this->msgTypes, true)) {
            throw new InvalidArgumentException('This message type not exist.');
        }

        $this->msgType = $msgType;

        return $this;
    }

    /**
     * Set message.
     *
     * @param string|array $message
     *
     * @return MessageBuilder
     */
    public function message($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set target user or group.
     *
     * @param mixed $to
     *
     * @return MessageBuilder
     */
    public function to($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Build message.
     *
     * @return bool
     *
     * @throws RuntimeException
     */
    public function build()
    {
        if (empty($this->msgType)) {
            throw new RuntimeException('message type not exist.');
        }

        if (empty($this->message)) {
            throw new RuntimeException('No message content to send.');
        }

        // 群发视频消息给用户列表时，视频消息格式需要另外处理，具体见文档
        if ($this->msgType === Client::MSG_TYPE_VIDEO) {
            if (is_array($this->message)) {
                $this->message = array_shift($this->message);
            }
            $this->msgType = 'mpvideo';
        }

        $content = (new MessageTransformer($this->msgType, $this->message))->transform();

        $group = isset($this->to) ? $this->to : null;

        $message = array_merge($this->buildGroup($group), $content);

        return $message;
    }

    /**
     * Build preview message.
     *
     * @param string $by
     *
     * @return array
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function buildPreview($by)
    {
        if (!in_array($by, $this->previewBys, true)) {
            throw new InvalidArgumentException('This preview by not exist.');
        }

        if (empty($this->msgType)) {
            throw new RuntimeException('Message type not exist.');
        } elseif ($this->msgType === Client::MSG_TYPE_VIDEO) {
            if (is_array($this->message)) {
                $this->message = array_shift($this->message);
            }
            $this->msgType = 'mpvideo';
        }

        if (empty($this->message)) {
            throw new RuntimeException('No message content to send.');
        }

        if (empty($this->to)) {
            throw new RuntimeException('No to.');
        }

        $content = (new MessageTransformer($this->msgType, $this->message))->transform();

        $message = array_merge($this->buildTo($this->to, $by), $content);

        return $message;
    }

    /**
     * Build group.
     *
     * @param mixed $group
     *
     * @return array
     */
    protected function buildGroup($group)
    {
        if (is_null($group)) {
            $group = [
                'filter' => [
                    'is_to_all' => true,
                ],
            ];
        } elseif (is_array($group)) {
            $group = [
                'touser' => $group,
            ];
        } else {
            $group = [
                'filter' => [
                    'is_to_all' => false,
                    'group_id' => $group,
                ],
            ];
        }

        return $group;
    }

    /**
     * Build to.
     *
     * @param string $to
     * @param string $by
     *
     * @return array
     */
    protected function buildTo($to, $by)
    {
        return [
            $by => $to,
        ];
    }

    /**
     * Return property.
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
    }
}
