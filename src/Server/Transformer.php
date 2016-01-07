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
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */
namespace EasyWeChat\Server;

use EasyWeChat\Message\AbstractMessage;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Text;

/**
 * Class Transformer.
 */
class Transformer
{
    /**
     * transform message to XML.
     *
     * @param array|string|AbstractMessage $message
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

        $handle = 'transform'.substr($class, strlen('EasyWeChat\Message\\'));

        return method_exists($this, $handle) ? $this->$handle($message) : [];
    }

    /**
     * Transform text message.
     *
     * @return array
     */
    public function transformText(AbstractMessage $message)
    {
        return [
                'Content' => $message->content,
               ];
    }

    /**
     * Transform image message.
     *
     * @return array
     */
    public function transformImage(AbstractMessage $message)
    {
        return [
                'Image' => [
                            'MediaId' => $message->media_id,
                           ],
               ];
    }

    /**
     * Transform video message.
     *
     * @return array
     */
    public function transformVideo(AbstractMessage $message)
    {
        $response = [
                     'Video' => [
                                 'MediaId' => $message->media_id,
                                 'Title' => $message->title,
                                 'Description' => $message->description,
                                ],
                    ];

        return $response;
    }

    /**
     * Transform voice message.
     *
     * @return array
     */
    public function transformVoice(AbstractMessage $message)
    {
        return [
                'Voice' => [
                            'MediaId' => $message->media_id,
                           ],
               ];
    }

    /**
     * Transform transfer message.
     *
     * @return array
     */
    public function transformTransfer(AbstractMessage $message)
    {
        $response = [];

        // 指定客服
        if (!empty($message->account)) {
            $response['TransInfo'] = [
                                      'KfAccount' => $message->account,
                                     ];
        }

        return $response;
    }

    /**
     * Transform news message.
     *
     * @param array|EasyWeChat\Message\News $news
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
                           'Title' => $item->title,
                           'Description' => $item->description,
                           'Url' => $item->url,
                           'PicUrl' => $item->pic_url,
                          ];
        }

        return [
                'ArticleCount' => count($articles),
                'Articles' => $articles,
               ];
    }
}
