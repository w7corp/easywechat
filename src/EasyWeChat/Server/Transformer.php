<?php

/**
 * Transformer.php.
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

namespace EasyWeChat\Server;

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
     * Transform articles message.
     *
     * @return array
     */
    public function transformArticles(AbstractMessage $message)
    {
        $articles = [];

        foreach ($message->all() as $item) {
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
