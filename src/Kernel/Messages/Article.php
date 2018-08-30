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
 * Class Article.
 */
class Article extends Message
{
    /**
     * @var string
     */
    protected $type = 'mpnews';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
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
    protected $jsonAliases = [
        'content_source_url' => 'source_url',
        'show_cover_pic' => 'show_cover',
    ];

    /**
     * @var array
     */
    protected $required = [
        'thumb_media_id',
        'title',
        'content',
        'show_cover',
    ];
}
