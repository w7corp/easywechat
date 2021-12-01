<?php

/*
 * This file is part of the overtrue/wechat.
 *
 */

namespace EasyWeChat\MiniProgram\Business;

use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\Raw as RawMessage;
use EasyWeChat\Kernel\Messages\Text;

/**
 * Class MessageBuilder.
 *
 * @author wangdongzhao <elim051@163.com>
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
     * @var \EasyWeChat\MiniProgram\Business\Client
     */
    protected $client;

    /**
     * Messages businessId
     *
     * @var int
     */
    protected $businessId;

    /**
     * MessageBuilder constructor.
     *
     * @param \EasyWeChat\MiniProgram\Business\Client $client
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
     * @return Messenger
     */
    public function by(string $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
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
     * Set target business id.
     *
     * @param int $businessId
     *
     * @return Messenger
     */
    public function business($businessId)
    {
        $this->businessId = $businessId;

        return $this;
    }

    /**
     * Send the message.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
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
            if ($this->businessId) {
                $prepends['businessid'] = $this->businessId;
            }
            $message = $this->message->transformForJsonRequest($prepends);
        }

        return $this->client->send($message);
    }

    /**
     * Return property.
     *
     * @return mixed
     */
    public function __get(string $property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        return null;
    }
}
