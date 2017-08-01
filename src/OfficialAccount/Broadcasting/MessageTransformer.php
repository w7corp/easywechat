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
     * transform message to XML.
     *
     * @param array|string|Message $message
     *
     * @return array
     */
    public function transform($message)
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
     * @param Message $message
     *
     * @return array
     */
    public function transformText(Message $message)
    {
        return [
            'msgtype' => 'text',
            'text' => [
                'content' => $message->get('content'),
            ],
        ];
    }

    /**
     * Transform image message.
     *
     * @param Message $message
     *
     * @return array
     */
    public function transformImage(Message $message)
    {
        return [
            'msgtype' => 'image',
            'image' => [
                'media_id' => $message->get('media_id'),
            ],
        ];
    }

    /**
     * Transform video message.
     *
     * @param Message $message
     *
     * @return array
     */
    public function transformVideo(Message $message)
    {
        return [
            'msgtype' => 'mpvideo',
            'mpvideo' => [
                'media_id' => $message->get('media_id'),
                'title' => $message->get('title'),
                'description' => $message->get('description'),
            ],
        ];
    }

    /**
     * Transform voice message.
     *
     * @param Message $message
     *
     * @return array
     */
    public function transformVoice(Message $message)
    {
        return [
            'msgtype' => 'voice',
            'voice' => [
                'media_id' => $message->get('media_id'),
            ],
        ];
    }

    /**
     * Transform articles message.
     *
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array
     */
    public function transformNews(Message $message)
    {
        return [
            'msgtype' => 'mpnews',
            'mpnews' => [
                'media_id' => $message->get('media_id'),
            ],
        ];
    }

    /**
     * Transform card message.
     *
     * @param Message $message
     *
     * @return array
     */
    public function transformCard(Message $message)
    {
        return [
            'msgtype' => 'wxcard',
            'wxcard' => [
                'card_id' => $message->get('card_id'),
            ],
        ];
    }
}
