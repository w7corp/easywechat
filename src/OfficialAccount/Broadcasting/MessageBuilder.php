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

use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\OfficialAccount\Application;

/**
 * Class MessageBuilder.
 *
 * @author overtrue <i@overtrue.me>
 */
class MessageBuilder
{
    /**
     * @var mixed
     */
    protected $to;

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var \EasyWeChat\OfficialAccount\Application
     */
    protected $app;

    /**
     * MessageBuilder constructor.
     *
     * @param \EasyWeChat\OfficialAccount\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Set message.
     *
     * @param array|\EasyWeChat\Kernel\Messages\Message $message
     *
     * @return \EasyWeChat\OfficialAccount\Broadcasting\MessageBuilder
     */
    public function message(Message $message)
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
     * @param array $prepends
     *
     * @return bool
     *
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function build(array $prepends = [])
    {
        if (empty($this->msgType)) {
            throw new RuntimeException('message type not exist.');
        }

        if (empty($this->message)) {
            throw new RuntimeException('No message content to send.');
        }

        $content = $this->message->transformForJsonRequest();

        if (empty($prepends)) {
            $prepends = $this->buildGroup($this->to);
        }

        $message = array_merge($prepends, $content);

        return $message;
    }

    /**
     * Build preview message.
     *
     * @param string $by
     *
     * @return array
     */
    public function buildForPreview($by)
    {
        return $this->build($this->buildTo($this->to, $by));
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
