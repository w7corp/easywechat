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
 * Class Markdown.
 *
 * @author her-cat <i@her-cat.com>
 */
class Markdown extends Message
{
    /**
     * @var string
     */
    protected $type = 'markdown';

    /**
     * @var array
     */
    protected $properties = ['content'];

    /**
     * Markdown constructor.
     */
    public function __construct(string $content)
    {
        parent::__construct(compact('content'));
    }
}
