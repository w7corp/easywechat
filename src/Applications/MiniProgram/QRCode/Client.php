<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\MiniProgram\QRCode;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Create mini program qrcode.
     *
     * @param string $path
     * @param int    $width
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     */
    public function create($path, $width = 430)
    {
        return $this->httpPostJson('cgi-bin/wxaapp/createwxaqrcode', compact('path', 'width'));
    }
}
