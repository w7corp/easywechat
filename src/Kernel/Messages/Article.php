<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

class Article extends Message
{
    /**
     * @var string
     */
    protected string  $type = 'mpnews';

    /**
     * Properties.
     *
     * @var array
     */
    protected array $properties = [
        'thumb_media_id',
        'author',
        'title',
        'content',
        'digest',
        'source_url',
        'show_cover',
    ];

    /**
     * Aliases of attribute.
     *
     * @var array
     */
    protected array $jsonAliases = [
        'content_source_url' => 'source_url',
        'show_cover_pic' => 'show_cover',
    ];

    /**
     * @var array
     */
    protected array $required = [
        'thumb_media_id',
        'title',
        'content',
        'show_cover',
    ];
}
