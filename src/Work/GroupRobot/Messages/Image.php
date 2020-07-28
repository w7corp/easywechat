<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\GroupRobot\Messages;

/**
 * Class Image.
 *
 * @author her-cat <i@her-cat.com>
 */
class Image extends Message
{
    /**
     * @var string
     */
    protected $type = 'image';

    /**
     * @var array
     */
    protected $properties = ['base64', 'md5'];

    /**
     * Image constructor.
     */
    public function __construct(string $base64, string $md5)
    {
        parent::__construct(compact('base64', 'md5'));
    }
}
