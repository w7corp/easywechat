<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram\AppCode;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Get AppCode.
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

        return $this->getStream('wxa/getwxacode', $params);
    }

    /**
     * Get AppCode unlimit.
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

        return $this->getStream('wxa/getwxacodeunlimit', $params);
    }

    /**
     * Create QrCode.
     *
     * @param string $path
     * @param int    $width
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function createQrCode($path, $width = 430)
    {
        return $this->getStream('cgi-bin/wxaapp/createwxaqrcode', compact('path', 'width'));
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
        return $this->requestRaw($endpoint, 'POST', ['json' => $params])->getBody();
    }
}
