<?php
/**
 * Transformer.php
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Server;

use EasyWeChat\Message\BaseMessage;

/**
 * Class Transformer.
 */
class Transformer
{
    /**
     * transform message to XML.
     *
     * @param BaseMessage $message
     *
     * @return array
     */
    public function transform(BaseMessage $message)
    {
        $handle = 'transform'.substr(get_class($message), strlen('EasyWeChat\Message'));

        return method_exists($this, $handle) ? $this->$handle($message) : [];
    }

    /**
     * Transform text message.
     *
     * @return array
     */
    public function tranformText(BaseMessage $message)
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
    public function tranformImage(BaseMessage $message)
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
    public function tranformVideo(BaseMessage $message)
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
    public function tranformVoice(BaseMessage $message)
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
    public function tranformTransfer(BaseMessage $message)
    {
        $response = [];

        // 指定客服
        if (!empty($message->account))) {
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
    public function tranformArticles(BaseMessage $message)
    {
        $articles = [];

        foreach ($message->all() as $item) {
            $articles[] = [
                           'Title'       => $item->title,
                           'Description' => $item->description,
                           'Url'         => $item->url,
                           'PicUrl'      => $item->pic_url,
                          ];
        }

        return [
                'ArticleCount' => count($articles),
                'Articles'     => $articles,
               ];
    }
}//end class