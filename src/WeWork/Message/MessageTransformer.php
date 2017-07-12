<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\WeWork\Message;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\Text;

/**
 * Class MessageTransformer.
 *
 * @author overtrue <i@overtrue.me>
 */
class MessageTransformer
{
    /**
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array
     */
    public function transform(Message $message)
    {
        if (is_array($message)) {
            $class = News::class;
        } else {
            if (is_string($message)) {
                $message = new Text(['content' => $message]);
            }

            $class = get_class($message);
        }

        $handle = 'transform'.substr($class, strlen('EasyWeChat\Kernel\Messages\\'));

        return method_exists($this, $handle) ? $this->$handle($message) : [];
    }

    /**
     * Transform text message.
     *
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array
     */
    public function transformText($message)
    {
        return [
            'text' => [
                'content' => $message->get('content'),
            ],
            'msgtype' => 'text',
        ];
    }

    /**
     * Transform news message.
     *
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array
     */
    public function transformNews($message)
    {
        return [
            'mpnews' => [
                'media_id' => $message->get('media_id'),
            ],
            'msgtype' => 'mpnews',
        ];
    }

    /**
     * Transform image message.
     *
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array
     */
    public function transformImage($message)
    {
        return [
            'image' => [
                'media_id' => $message->get('media_id'),
            ],
            'msgtype' => 'image',
        ];
    }

    /**
     * Transform video message.
     *
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function transformVideo($message)
    {
        return [
            'video' => [
                'media_id' => $message->get('media_id'),
                'title' => $message->get('title'),
                'description' => $message->get('description'),
            ],
            'msgtype' => 'video',
        ];
    }

    /**
     * Transform mpvideo message.
     *
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array
     */
    public function transformMpvideo($message)
    {
        return [
            'mpvideo' => [
                'media_id' => $message->get('media_id'),
            ],
            'msgtype' => 'mpvideo',
        ];
    }

    /**
     * Transform voice message.
     *
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array
     */
    public function transformVoice($message)
    {
        return [
            'voice' => [
                'media_id' => $message->get('media_id'),
            ],
            'msgtype' => 'voice',
        ];
    }

    /**
     * Transform voice message.
     *
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array
     */
    public function transformFile($message)
    {
        return [
            'file' => [
                'media_id' => $message->get('media_id'),
            ],
            'msgtype' => 'file',
        ];
    }

    /**
     * Transform card message.
     *
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array
     */
    public function transformTextCard($message)
    {
        return [
            'textcard' => [
                'title' => $message->get('title'),
                'description' => $message->get('description'),
                'url' => $message->get('url'),
            ],
            'msgtype' => 'textcard',
        ];
    }
}
