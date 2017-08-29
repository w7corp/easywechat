<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Messages;

/**
 * Class News.
 *
 * @author overtrue <i@overtrue.me>
 */
class News extends Message
{
    /**
     * @var string
     */
    protected $type = 'news';

    /**
     * @var array
     */
    protected $properties = [
        'items',
    ];

    /**
     * News constructor.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct(compact('items'));
    }

    public function toXmlArray()
    {
        $items = [];

        foreach ($this->get('items') as $article) {
            if ($article instanceof NewsItem) {
                $items[] = $article->toXmlArray();
            }
        }

        return [
            'ArticleCount' => count($items),
            'Articles' => $items,
        ];
    }
}
