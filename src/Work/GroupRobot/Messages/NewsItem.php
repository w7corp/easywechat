<?php

declare(strict_types=1);

namespace EasyWeChat\Work\GroupRobot\Messages;

class NewsItem extends Message
{
    /**
     * @var string
     */
    protected string  $type = 'news';

    /**
     * @var array
     */
    protected array $properties = [
        'title',
        'description',
        'url',
        'image',
    ];

    public function toJsonArray()
    {
        return [
            'title' => $this->get('title'),
            'description' => $this->get('description'),
            'url' => $this->get('url'),
            'picurl' => $this->get('image'),
        ];
    }
}
