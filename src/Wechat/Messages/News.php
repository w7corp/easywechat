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
 * News.php.
 *
 * Part of Overtrue\Wechat.
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

namespace Overtrue\Wechat\Messages;

use Closure;

/**
 * 图文消息.
 */
class News extends BaseMessage
{
    /**
     * 属性.
     *
     * @var array
     */
    protected $items = array();

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

        array_map(array($this, 'item'), (array) $items);

        return $this;
    }

    /**
     * 生成主动消息数组.
     */
    public function toStaff()
    {
        $articles = array();

        foreach ($this->items as $item) {
            $articles[] = array(
                           'title' => $item->title,
                           'description' => $item->description,
                           'url' => $item->url,
                           'picurl' => $item->pic_url,
                          );
        }

        return array('news' => array('articles' => $articles));
    }

    /**
     * 生成回复消息数组.
     */
    public function toReply()
    {
        $articles = array();

        foreach ($this->items as $item) {
            $articles[] = array(
                           'Title' => $item->title,
                           'Description' => $item->description,
                           'Url' => $item->url,
                           'PicUrl' => $item->pic_url,
                          );
        }

        return array(
                'ArticleCount' => count($articles),
                'Articles' => $articles,
               );
    }
}
