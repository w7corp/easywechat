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
use EasyWeChat\Kernel\Http\StreamResponse;

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
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function get(string $path, array $optional = [])
    {
        $params = array_merge([
            'path' => $path,
        ], $optional);

        return $this->getStream('wxa/getwxacode', $params);
    }

    /**
     * Get AppCode unlimit.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function getUnlimit(string $scene, array $optional = [])
    {
        $params = array_merge([
            'scene' => $scene,
        ], $optional);

        return $this->getStream('wxa/getwxacodeunlimit', $params);
    }

    /**
     * Create QrCode.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function getQrCode(string $path, int $width = null)
    {
        return $this->getStream('cgi-bin/wxaapp/createwxaqrcode', compact('path', 'width'));
    }

    /**
     * Get stream.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getStream(string $endpoint, array $params)
    {
        $response = $this->requestRaw($endpoint, 'POST', ['json' => $params]);

        if (false !== stripos($response->getHeaderLine('Content-disposition'), 'attachment')) {
            return StreamResponse::buildFromPsrResponse($response);
        }

        return $this->castResponseToType($response, $this->app['config']->get('response_type'));
    }
}
