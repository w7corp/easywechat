<?php

/*
 * This file is part of the EasyWeChat.
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

namespace EasyWeChat\Staff;

use EasyWeChat\Message\AbstractMessage;

/**
 * Class Transformer.
 */
class Transformer
{
    /**
     * transform message to XML.
     *
     * @param AbstractMessage $message
     *
     * @return array
     */
    public function transform(AbstractMessage $message)
    {
        $handle = 'transform'.substr(get_class($message), strlen('EasyWeChat\Message\\'));

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
                'text' => [
                           'content' => $message->content,
                          ],
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
                'image' => [
                            'media_id' => $message->media_id,
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
        return [
                'video' => [
                            'title' => $message->title,
                            'media_id' => $message->media_id,
                            'description' => $message->description,
                            'thumb_media_id' => $message->thumb_media_id,
                           ],
               ];
    }

    /**
     * Transform voice message.
     *
     * @return array
     */
    public function transformVoice(AbstractMessage $message)
    {
        return [
                'voice' => [
                            'media_id' => $message->media_id,
                           ],
               ];
    }

    /**
     * Transform articles message.
     *
     * @return array
     */
    public function transformArticles(AbstractMessage $message)
    {
        $articles = [];

        foreach ($message->items as $item) {
            $articles[] = [
                           'title' => $item->title,
                           'description' => $item->description,
                           'url' => $item->url,
                           'picurl' => $item->pic_url,
                          ];
        }

        return ['news' => ['articles' => $articles]];
    }
}
