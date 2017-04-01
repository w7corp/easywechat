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
 * Notice.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\MiniProgram\Notice;

use EasyWeChat\Notice\Notice as BaseNotice;

class Notice extends BaseNotice
{
    /**
     * {@inheritdoc}.
     */
    protected $message = [
        'touser' => '',
        'template_id' => '',
        'page' => '',
        'form_id' => '',
        'data' => [],
        'emphasis_keyword' => '',
    ];

    /**
     * {@inheritdoc}.
     */
    protected $defaults = [
        'touser' => '',
        'template_id' => '',
        'form_id' => '',
        'data' => [],
    ];

    /**
     * {@inheritdoc}.
     */
    protected $required = ['touser', 'template_id', 'form_id'];

    /**
     * Send notice message.
     */
    const API_SEND_NOTICE = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send';
}
