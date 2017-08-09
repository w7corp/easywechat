<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\CustomerService;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\Raw as RawMessage;
use EasyWeChat\Kernel\Messages\Text;

/**
 * Class MessageBuilder.
 *
 * @author overtrue <i@overtrue.me>
 */
class Messenger
{
    /**
     * Messages to send.
     *
     * @var \EasyWeChat\Kernel\Messages\Message;
     */
    protected $message;

    /**
     * Messages target user open id.
     *
     * @var string
     */
    protected $to;

    /**
     * Messages sender staff id.
     *
     * @var string
     */
    protected $account;

    /**
     * Customer service instance.
     *
     * @var \EasyWeChat\OfficialAccount\CustomerService\Client
     */
    protected $client;

    /**
     * MessageBuilder constructor.
     *
     * @param \EasyWeChat\OfficialAccount\CustomerService\Client $client
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
     * @return Messenger
     *
     * @throws InvalidArgumentException
     */
    public function message($message)
    {
        if (is_string($message)) {
            $message = new Text($message);
        }

        $this->message = $message;

        return $this;
    }

    /**
     * Set staff account to send message.
     *
     * @param string $account
     *
     * @return Messenger
     */
    public function by(string $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @param string $account
     *
     * @return Messenger
     */
    public function from(string $account)
    {
        return $this->by($account);
    }

    /**
     * Set target user open id.
     *
     * @param string $openid
     *
     * @return Messenger
     */
    public function to($openid)
    {
        $this->to = $openid;

        return $this;
    }

    /**
     * Send the message.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws RuntimeException
     */
    public function send()
    {
        if (empty($this->message)) {
            throw new RuntimeException('No message to send.');
        }

        if ($this->message instanceof RawMessage) {
            $message = json_decode($this->message->get('content'), true);
        } else {
            $prepends = [
                'touser' => $this->to,
            ];
            if ($this->account) {
                $prepends['customservice'] = ['kf_account' => $this->account];
            }
            $message = $this->message->transformForJsonRequest($prepends);
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

        return null;
    }
}
