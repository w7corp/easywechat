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
     */
    public function __construct(array $items = [])
    {
        parent::__construct(compact('items'));
    }

    public function propertiesToArray(array $data, array $aliases = []): array
    {
        return ['articles' => array_map(function ($item) {
            if ($item instanceof NewsItem) {
                return $item->toJsonArray();
            }
        }, $this->get('items'))];
    }

    public function toXmlArray()
    {
        $items = [];

        foreach ($this->get('items') as $item) {
            if ($item instanceof NewsItem) {
                $items[] = $item->toXmlArray();
            }
        }

        return [
            'ArticleCount' => count($items),
            'Articles' => $items,
        ];
    }
}
