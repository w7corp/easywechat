<?php

/**
 * News.php.
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

namespace EasyWeChat\Message;

use Closure;

/**
 * Class News.
 */
class News extends AbstractMessage implements MessageInterface
{
    /**
     * Properties.
     *
     * @var array
     */
    protected $items = [];

    /**
     * 添加图文消息内容.
     *
     * @param NewsItem $item
     *
     * @return News
     */
    public function item(NewsItem $item)
    {
        array_push($this->items, $item);

        return $this;
    }

    /**
     * 添加多条图文消息.
     *
     * @param array|Closure $items
     *
     * @return News
     */
    public function items($items)
    {
        if ($items instanceof Closure) {
            $items = $items();
        }

        array_map([$this, 'item'], (array) $items);

        return $this;
    }

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
}
