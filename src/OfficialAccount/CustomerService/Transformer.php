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

use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\Text;

/**
 * Class MessageTransformer.
 *
 * @author overtrue <i@overtrue.me>
 */
class Transformer
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
     * Transform music message.
     *
     * @param Message $message
     *
     * @return array
     */
    public function transformMusic(Message $message)
    {
        return [
            'msgtype' => 'music',
            'music' => [
                'title' => $message->get('title'),
                'description' => $message->get('description'),
                'musicurl' => $message->get('url'),
                'hqmusicurl' => $message->get('hq_url'),
                'thumb_media_id' => $message->get('thumb_media_id'),
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
            'msgtype' => 'video',
            'video' => [
                'title' => $message->get('title'),
                'media_id' => $message->get('media_id'),
                'description' => $message->get('description'),
                'thumb_media_id' => $message->get('thumb_media_id'),
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
     * @param $news
     *
     * @return array
     */
    public function transformNews($news)
    {
        $articles = [];

        if (!is_array($news)) {
            $news = [$news];
        }

        foreach ($news as $item) {
            $articles[] = [
                'title' => $item->get('title'),
                'description' => $item->get('description'),
                'url' => $item->get('url'),
                'picurl' => $item->get('pic_url'),
            ];
        }

        return ['msgtype' => 'news', 'news' => ['articles' => $articles]];
    }

    /**
     * Transform material message.
     *
     * @param Message $message
     *
     * @return array
     */
    public function transformMaterial(Message $message)
    {
        $type = $message->getType();

        return [
            'msgtype' => $type,
            $type => [
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
        $type = $message->getType();

        return [
            'msgtype' => $type,
            $type => [
                'card_id' => $message->get('card_id'),
            ],
        ];
    }
}
