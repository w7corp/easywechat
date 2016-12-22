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
 * MessageBuilder.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Staff;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Core\Exceptions\RuntimeException;
use EasyWeChat\Message\AbstractMessage;
use EasyWeChat\Message\Raw as RawMessage;
use EasyWeChat\Message\Text;

/**
 * Class MessageBuilder.
 */
class MessageBuilder
{
    /**
     * Message to send.
     *
     * @var \EasyWeChat\Message\AbstractMessage;
     */
    protected $message;

    /**
     * Message target user open id.
     *
     * @var string
     */
    protected $to;

    /**
     * Message sender staff id.
     *
     * @var string
     */
    protected $account;

    /**
     * Staff instance.
     *
     * @var \EasyWeChat\Staff\Staff
     */
    protected $staff;

    /**
     * MessageBuilder constructor.
     *
     * @param \EasyWeChat\Staff\Staff $staff
     */
    public function __construct(Staff $staff)
    {
        $this->staff = $staff;
    }

    /**
     * Set message to send.
     *
     * @param string|AbstractMessage $message
     *
     * @return MessageBuilder
     *
     * @throws InvalidArgumentException
     */
    public function message($message)
    {
        if (is_string($message)) {
            $message = new Text(['content' => $message]);
        }

        $this->message = $message;

        return $this;
    }

    /**
     * Set staff account to send message.
     *
     * @param string $account
     *
     * @return MessageBuilder
     */
    public function by($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Set target user open id.
     *
     * @param string $openId
     *
     * @return MessageBuilder
     */
    public function to($openId)
    {
        $this->to = $openId;

        return $this;
    }

    /**
     * Send the message.
     *
     * @return bool
     *
     * @throws RuntimeException
     */
    public function send()
    {
        if (empty($this->message)) {
            throw new RuntimeException('No message to send.');
        }

        $transformer = new Transformer();

        if ($this->message instanceof RawMessage) {
            $message = $this->message->get('content');
        } else {
            $content = $transformer->transform($this->message);
            $message = [
                'touser' => $this->to,
            ];

            if ($this->account) {
                $message['customservice'] = ['kf_account' => $this->account];
            }

            $message = array_merge($message, $content);
        }

        return $this->staff->send($message);
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
