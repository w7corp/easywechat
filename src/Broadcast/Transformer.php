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
 * Transformer.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Broadcast;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;

/**
 * Class Transformer.
 */
class Transformer
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $msgType;

    /**
     * message.
     *
     * @var mixed
     */
    protected $message;

    /**
     * Transformer constructor.
     *
     * @param $msgType
     * @param $message
     */
    public function __construct($msgType, $message)
    {
        $this->msgType = $msgType;

        $this->message = $message;
    }

    /**
     * Transform message.
     *
     * @return array
     */
    public function transform()
    {
        $handle = sprintf('transform%s', ucfirst($this->msgType));

        return method_exists($this, $handle) ? $this->$handle($this->message) : [];
    }

    /**
     * Transform text message.
     *
     * @param string $message
     *
     * @return array
     */
    public function transformText($message)
    {
        return [
            'text' => [
                'content' => $message,
            ],
            'msgtype' => 'text',
        ];
    }

    /**
     * Transform news message.
     *
     * @param string $message
     *
     * @return array
     */
    public function transformNews($message)
    {
        return [
            'mpnews' => [
                'media_id' => $message,
            ],
            'msgtype' => 'mpnews',
        ];
    }

    /**
     * Transform image message.
     *
     * @param string $message
     *
     * @return array
     */
    public function transformImage($message)
    {
        return [
            'image' => [
                'media_id' => $message,
            ],
            'msgtype' => 'image',
        ];
    }

    /**
     * Transform video message.
     *
     * @param array $message
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function transformVideo(array $message)
    {
        if (3 !== count($message)) {
            throw new InvalidArgumentException('send message to openids, the message must be three arguments.');
        }

        return [
            'mpvideo' => [
                'media_id' => $message[0],
                'title' => $message[1],
                'description' => $message[2],
            ],
            'msgtype' => 'mpvideo',
        ];
    }

    /**
     * Transform mpvideo message.
     *
     * @param string $message
     *
     * @return array
     */
    public function transformMpvideo($message)
    {
        return [
            'mpvideo' => [
                'media_id' => $message,
            ],
            'msgtype' => 'mpvideo',
        ];
    }

    /**
     * Transform voice message.
     *
     * @param string $message
     *
     * @return array
     */
    public function transformVoice($message)
    {
        return [
            'voice' => [
                'media_id' => $message,
            ],
            'msgtype' => 'voice',
        ];
    }

    /**
     * Transform card message.
     *
     * @param $message
     *
     * @return array
     */
    public function transformCard($message)
    {
        return [
            'wxcard' => [
                'card_id' => $message,
            ],
            'msgtype' => 'wxcard',
        ];
    }
}
