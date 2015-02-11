<?php

namespace Overtrue\Wechat\Messages;

use Closure;

class News extends BaseMessage
{
    protected $items = array();

    /**
     * 添加图文消息内容
     *
     * @return News
     */
    public function item(NewsItem $item)
    {
        array_push($this->items, $item);

        return $this;
    }

    /**
     * 添加多条图文消息
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
     * 生成主动消息数组
     */
    public function toStaff()
    {
        $articles = array();

        foreach ($this->items as $item) {
            $articles [] = array(
                            "title"       => $item->title,
                            "description" => $item->description,
                            "url"         => $item->url,
                            "picurl"      => $item->picurl,
                           );
        }

        return array('news' => array('articles' => $articles));
    }

    /**
     * 生成回复消息数组
     */
    public function toReply()
    {
        $articles = array();

        foreach ($this->items as $item) {
            $articles [] = array(
                            "Title"       => $item->title,
                            "Description" => $item->description,
                            "Url"         => $item->url,
                            "PicUrl"      => $item->picurl,
                           );
        }

        return array('ArticleCount' => count($articles), 'Articles' => $articles);
    }

}