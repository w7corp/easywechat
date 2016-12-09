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
 * Raw.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Message;

/**
 * Class Raw.
 */
class Raw extends AbstractMessage
{
    /**
     * @var string
     */
    protected $type = 'raw';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = ['content'];

    /**
     * Constructor.
     *
     * @param string $content
     */
    public function __construct($content)
    {
        parent::__construct(['content' => strval($content)]);
    }
}
