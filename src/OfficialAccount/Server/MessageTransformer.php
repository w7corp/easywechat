<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\Server;

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
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array
     */
    public function transformText(Message $message)
    {
        return [
            'Content' => $message->get('content'),
        ];
    }

    /**
     * Transform image message.
     *
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array
     */
    public function transformImage(Message $message)
    {
        return [
            'Image' => [
                'MediaId' => $message->get('media_id'),
            ],
        ];
    }

    /**
     * Transform video message.
     *
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array
     */
    public function transformVideo(Message $message)
    {
        return [
            'Video' => [
                'MediaId' => $message->get('media_id'),
                'Title' => $message->get('title'),
                'Description' => $message->get('description'),
            ],
        ];
    }

    /**
     * Transform music message.
     *
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array
     */
    public function transformMusic(Message $message)
    {
        return [
            'Music' => [
                'Title' => $message->get('title'),
                'Description' => $message->get('description'),
                'MusicUrl' => $message->get('url'),
                'HQMusicUrl' => $message->get('hq_url'),
                'ThumbMediaId' => $message->get('thumb_media_id'),
            ],
        ];
    }

    /**
     * Transform voice message.
     *
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array
     */
    public function transformVoice(Message $message)
    {
        return [
            'Voice' => [
                'MediaId' => $message->get('media_id'),
            ],
        ];
    }

    /**
     * Transform transfer message.
     *
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array
     */
    public function transformTransfer(Message $message)
    {
        $response = [];

        // 指定客服
        if ($message->get('account')) {
            $response['TransInfo'] = [
                'KfAccount' => $message->get('account'),
            ];
        }

        return $response;
    }

    /**
     * Transform news message.
     *
     * @param array|\EasyWeChat\Kernel\Messages\News $news
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
                'Title' => $item->get('title'),
                'Description' => $item->get('description'),
                'Url' => $item->get('url'),
                'PicUrl' => $item->get('pic_url'),
            ];
        }

        return [
            'ArticleCount' => count($articles),
            'Articles' => $articles,
        ];
    }

    /**
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array
     */
    public function transformDeviceText(Message $message)
    {
        return [
            'DeviceType' => $message->get('device_type'),
            'DeviceID' => $message->get('device_id'),
            'SessionID' => $message->get('session_id'),
            'Content' => base64_encode($message->get('content')),
        ];
    }
}
