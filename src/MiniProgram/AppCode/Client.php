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
     * @param string     $path
     * @param int|null   $width
     * @param bool|null  $autoColor
     * @param array|null $lineColor
     *
     * @return \EasyWeChat\Kernel\Http\Response
     */
    public function get(string $path, int $width = null, bool $autoColor = null, array $lineColor = null)
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
     * @param string      $scene
     * @param string|null $page
     * @param int|null    $width
     * @param bool|null   $autoColor
     * @param array|null  $lineColor
     *
     * @return \EasyWeChat\Kernel\Http\Response
     */
    public function getUnlimit(string $scene, string $page = null, int $width = null, bool $autoColor = null, array $lineColor = null)
    {
        $params = [
            'scene' => $scene,
            'page' => $page,
            'width' => $width,
            'auto_color' => $autoColor,
            'line_color' => $lineColor,
        ];

        return $this->getStream('wxa/getwxacodeunlimit', $params);
    }

    /**
     * Create QrCode.
     *
     * @param string   $path
     * @param int|null $width
     *
     * @return \EasyWeChat\Kernel\Http\Response
     */
    public function qrcode(string $path, int $width = null)
    {
        return $this->getStream('cgi-bin/wxaapp/createwxaqrcode', compact('path', 'width'));
    }

    /**
     * Get stream.
     *
     * @param string $endpoint
     * @param array  $params
     *
     * @return \EasyWeChat\Kernel\Http\Response
     */
    protected function getStream(string $endpoint, array $params)
    {
        return $this->requestRaw($endpoint, 'POST', ['json' => $params]);
    }
}
