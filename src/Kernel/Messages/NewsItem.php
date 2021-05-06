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
    protected string $type = 'news';

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

    /**
     * @return array
     */
    public function toJsonArray(): array
    {
        return [
            'title' => $this->get('title'),
            'description' => $this->get('description'),
            'url' => $this->get('url'),
            'picurl' => $this->get('image'),
        ];
    }

    /**
     * @return array
     */
    public function toXmlArray(): array
    {
        return [
            'Title' => $this->get('title'),
            'Description' => $this->get('description'),
            'Url' => $this->get('url'),
            'PicUrl' => $this->get('image'),
        ];
    }
}
