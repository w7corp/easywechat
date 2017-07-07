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
 */
class News extends Message
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
    protected $properties = [
        'title',
        'description',
        'url',
        'image',
    ];

    /**
     * Aliases of attribute.
     *
     * @var array
     */
    protected $aliases = [
        'image' => 'pic_url',
    ];
}
