<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment\Fundflow;

use EasyWeChat\Kernel\Http\StreamResponse;
use EasyWeChat\Payment\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * Download fundflow history as a table file.
     *
     * @param string $date
     * @param string $type
     *
     * @return \EasyWeChat\Kernel\Http\StreamResponse|\Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function get(string $date, string $type = 'Basic', $options = [])
    {

        $params   = [
            'appid' => $this->app['config']->app_id,
            'bill_date' => $date,
            'account_type' => $type,
            'sign_type' => 'HMAC-SHA256',
            'nonce_str' => uniqid('micro'),
        ];
        $options  = array_merge(
            [
                'cert' => $this->app['config']->get('cert_path'),
                'ssl_key' => $this->app['config']->get('key_path'),
            ],
            $options
        );
        $response = $this->requestRaw('pay/downloadfundflow', $params, 'post', $options);

        $Contents = $response->getBody()
                             ->getContents();
        if (0 === strpos($res, '<xml>')) {
            return $this->castResponseToType($response, $this->app['config']->get('response_type'));
        }

        return StreamResponse::buildFromPsrResponse($response);
    }
}
