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
        $handle = 'transform'.substr(get_class($message), strlen('EasyWeChat\Message'));

        return method_exists($this, $handle) ? $this->$handle($message) : [];
    }

    /**
     * Transform text message.
     *
     * @return array
     */
    public function tranformText(AbstractMessage $message)
    {
        return array(
                'text' => array(
                           'content' => $message->content,
                          ),
               );
    }

    /**
     * Transform image message.
     *
     * @return array
     */
    public function tranformImage(AbstractMessage $message)
    {
        return array(
                'image' => array(
                            'media_id' => $message->media_id,
                           ),
               );
    }

    /**
     * Transform video message.
     *
     * @return array
     */
    public function tranformVideo(AbstractMessage $message)
    {
        return array(
                'video' => array(
                            'title' => $message->title,
                            'media_id' => $message->media_id,
                            'description' => $message->description,
                            'thumb_media_id' => $message->thumb_media_id,
                           ),
               );
    }

    /**
     * Transform voice message.
     *
     * @return array
     */
    public function tranformVoice(AbstractMessage $message)
    {
        return array(
                'voice' => array(
                            'media_id' => $message->media_id,
                           ),
               );
    }

    /**
     * Transform articles message.
     *
     * @return array
     */
    public function tranformArticles(AbstractMessage $message)
    {
        $articles = array();

        foreach ($message->items as $item) {
            $articles[] = array(
                           'title' => $item->title,
                           'description' => $item->description,
                           'url' => $item->url,
                           'picurl' => $item->pic_url,
                          );
        }

        return array('news' => array('articles' => $articles));
    }
}
