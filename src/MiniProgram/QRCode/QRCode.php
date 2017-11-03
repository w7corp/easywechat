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
    const API_GET_WXACODE = 'https://api.weixin.qq.com/wxa/getwxacode';
    const API_GET_WXACODE_UNLIMIT = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit';
    const API_CREATE_QRCODE = 'https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode';

    /**
     * Get WXACode.
     *
     * @param string $path
     * @param int    $width
     * @param bool   $autoColor
     * @param array  $lineColor
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getAppCode($path, $width = 430, $autoColor = false, $lineColor = ['r' => 0, 'g' => 0, 'b' => 0])
    {
        $params = [
            'path' => $path,
            'width' => $width,
            'auto_color' => $autoColor,
            'line_color' => $lineColor,
        ];

        return $this->getStream(self::API_GET_WXACODE, $params);
    }

    /**
     * Get WXACode unlimit.
     *
     * @param string $scene
     * @param int    $width
     * @param bool   $autoColor
     * @param array  $lineColor
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getAppCodeUnlimit($scene, $width = 430, $autoColor = false, $lineColor = ['r' => 0, 'g' => 0, 'b' => 0])
    {
        $params = [
            'scene' => $scene,
            'width' => $width,
            'auto_color' => $autoColor,
            'line_color' => $lineColor,
        ];

        return $this->getStream(self::API_GET_WXACODE_UNLIMIT, $params);
    }

    /**
     * Get app code unlimit.
     *
     * @param string $scene
     * @param string $page
     * @param int    $width
     * @param bool   $autoColor
     * @param array  $lineColor
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function appCodeUnlimit($scene, $page = null, $width = null, $autoColor = null, $lineColor = null)
    {
        $params = [
            'scene' => $scene,
            'page' => $page,
            'width' => $width,
            'auto_color' => $autoColor,
            'line_color' => $lineColor,
        ];

        return $this->getStream(self::API_GET_WXACODE_UNLIMIT, $params);
    }

    /**
     * Create QRCode.
     *
     * @param string $path
     * @param int    $width
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function createQRCode($path, $width = 430)
    {
        return $this->getStream(self::API_CREATE_QRCODE, compact('path', 'width'));
    }

    /**
     * Get stream.
     *
     * @param string $endpoint
     * @param array  $params
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    protected function getStream($endpoint, $params)
    {
        return $this->getHttp()->json($endpoint, $params)->getBody();
    }
}
