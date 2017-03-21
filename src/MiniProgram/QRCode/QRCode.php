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
 * QRCode.php.
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

namespace EasyWeChat\MiniProgram\QRCode;

use EasyWeChat\MiniProgram\Core\AbstractMiniProgram;

class QRCode extends AbstractMiniProgram
{
    /**
     * API.
     */
    const API_CREATE_QRCODE = 'https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode';

    /**
     * Create mini program qrcode.
     *
     * @param $path
     * @param int $width
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function create($path, $width = 430)
    {
        return $this->parseJSON('JSON', [self::API_CREATE_QRCODE, compact('path', 'width')]);
    }
}
