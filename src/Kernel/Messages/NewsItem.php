<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

class NewsItem extends Message
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'news';

    /**
     * Properties.
     *
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

    public function toXmlArray()
    {
        return [
            'Title' => $this->get('title'),
            'Description' => $this->get('description'),
            'Url' => $this->get('url'),
            'PicUrl' => $this->get('image'),
        ];
    }
}
