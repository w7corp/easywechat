<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Contracts;

use Psr\Http\Message\RequestInterface;

/**
 * Interface AuthorizerAccessToken.
 *
 * @author overtrue <i@overtrue.me>
 */
interface AccessTokenInterface
{
    public function getToken(): array;

    /**
     * @return \EasyWeChat\Kernel\Contracts\AccessTokenInterface
     */
    public function refresh(): self;

    public function applyToRequest(RequestInterface $request, array $requestOptions = []): RequestInterface;
}
