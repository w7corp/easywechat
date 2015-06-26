<?php

/**
 * Articles.php.
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

namespace EasyWeChat\Server\Messages;

use Closure;
use EasyWeChat\Message\Articles as BaseArticles;

/**
 * Class Articles.
 */
class Articles extends BaseArticles implements MessageInterface
{
    /**
     * 生成主动消息数组.
     */
    public function toStaff()
    {
        $articles = [];

        foreach ($this->items as $item) {
            $articles[] = [
                           'title' => $item->title,
                           'description' => $item->description,
                           'url' => $item->url,
                           'picurl' => $item->pic_url,
                          ];
        }

        return ['news' => ['articles' => $articles]];
    }

    /**
     * 生成回复消息数组.
     */
    public function toReply()
    {
        $articles = [];

        foreach ($this->items as $item) {
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
}//end class
