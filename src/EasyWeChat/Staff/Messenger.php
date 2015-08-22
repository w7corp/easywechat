<?php

/**
 * Messenger.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Staff;

use EasyWeChat\Core\Http;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Core\Exceptions\RuntimeException;
use EasyWeChat\Message\AbstractMessage;
use EasyWeChat\Message\Text;

/**
 * Class Messenger.
 */
class Messenger
{
    /**
     * Message transformer.
     *
     * @var Transformer
     */
    protected $transformer;

    /**
     * Message to send.
     *
     * @var AbstractMessage;
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
     * Http client.
     *
     * @var Http
     */
    protected $http;

    const API_MESSAGE_SEND = 'https://api.weixin.qq.com/cgi-bin/message/custom/send';

    /**
     * Constructor.
     *
     * @param Http        $http
     * @param Transformer $transformer
     */
    public function __construct(Http $http, Transformer $transformer)
    {
        $this->http = $http->setExpectedException(StaffHttpException::class);
        $this->transformer = $transformer;
    }

    /**
     * Set message to send.
     *
     * @param AbstractMessage $message
     *
     * @return Messenger
     *
     * @throws InvalidArgumentException
     */
    public function message($message)
    {
        if (is_string($message)) {
            $message = new Text(['content' => $message]);
        }

        if (!$message instanceof AbstractMessage) {
            throw new InvalidArgumentException("Message must be a instanceof 'EasyWeChat\\Message\\AbstractMessage'.");
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
     * @return bool|null
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

        $content = $this->transformer->transform($this->message);

        $message = [
            'touser' => $this->to,
            'msgtype' => $this->message->type,
            $this->message->type => $content,
            'customservice' => ['kf_account' => $this->account],
        ];

        return $this->http->json(self::API_MESSAGE_SEND, $message);
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
