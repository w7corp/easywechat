<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\WeWork\Message;

use EasyWeChat\Exceptions\InvalidArgumentException;
use EasyWeChat\Exceptions\RuntimeException;
use EasyWeChat\Messages\Message;
use EasyWeChat\Messages\Raw as RawMessage;
use EasyWeChat\Messages\Text;

/**
 * Class MessageBuilder.
 *
 * @author overtrue <i@overtrue.me>
 */
class MessageBuilder
{
    /**
     * @var \EasyWeChat\Messages\Message;
     */
    protected $message;

    /**
     * @var array
     */
    protected $to = [];

    /**
     * @var int
     */
    protected $agentId;

    /**
     * @var bool
     */
    protected $secretive = false;

    /**
     * @var \EasyWeChat\Applications\WeWork\Message\Client
     */
    protected $client;

    /**
     * MessageBuilder constructor.
     *
     * @param \EasyWeChat\Applications\WeWork\Message\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Set message to send.
     *
     * @param string|Message $message
     *
     * @return \EasyWeChat\Applications\WeWork\Message\MessageBuilder
     *
     * @throws \EasyWeChat\Exceptions\InvalidArgumentException
     */
    public function message($message)
    {
        if (is_string($message)) {
            $message = new Text(['content' => $message]);
        }

        if (!($message instanceof Message)) {
            throw new InvalidArgumentException('Invalid message.');
        }

        $this->message = $message;

        return $this;
    }

    /**
     * @param int $agentId
     *
     * @return \EasyWeChat\Applications\WeWork\Message\MessageBuilder
     */
    public function ofAgent(int $agentId)
    {
        $this->agentId = $agentId;

        return $this;
    }

    /**
     * @param array|string $userIds
     *
     * @return \EasyWeChat\Applications\WeWork\Message\MessageBuilder
     */
    public function toUser($userIds)
    {
        return $this->setRecipients($userIds, 'touser');
    }

    /**
     * @param array|string $partyIds
     *
     * @return \EasyWeChat\Applications\WeWork\Message\MessageBuilder
     */
    public function toParty($partyIds)
    {
        return $this->setRecipients($partyIds, 'toparty');
    }

    /**
     * @param array|string $tagIds
     *
     * @return \EasyWeChat\Applications\WeWork\Message\MessageBuilder
     */
    public function toTag($tagIds)
    {
        return $this->setRecipients($tagIds, 'totag');
    }

    /**
     * Keep secret.
     *
     * @return \EasyWeChat\Applications\WeWork\Message\MessageBuilder
     */
    public function secretive()
    {
        $this->secretive = true;

        return $this;
    }

    /**
     * @param array|string $ids
     * @param string       $key
     *
     * @return \EasyWeChat\Applications\WeWork\Message\MessageBuilder
     */
    protected function setRecipients($ids, string $key): MessageBuilder
    {
        if (is_array($ids)) {
            $ids = implode('|', $ids);
        }

        $this->to = [$key => $ids];

        return $this;
    }

    /**
     * @param \EasyWeChat\Messages\Message|string|null $message
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Exceptions\RuntimeException
     */
    public function send($message = null)
    {
        if ($message) {
            $this->message($message);
        }

        if (empty($this->message)) {
            throw new RuntimeException('No message to send.');
        }

        if (empty($this->to)) {
            throw new RuntimeException('Unspecified message recipients.');
        }

        $transformer = new MessageTransformer();

        if ($this->message instanceof RawMessage) {
            $message = $this->message->get('content');
        } else {
            $message = array_merge([
                'agentid' => $this->agentId,
                'safe' => intval($this->secretive),
            ], $this->to, $transformer->transform($this->message));
        }

        return $this->client->send($message);
    }

    /**
     * Return property.
     *
     * @param $property
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
