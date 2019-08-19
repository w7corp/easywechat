<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment\Notify;

use Closure;
use EasyWeChat\Kernel\Support\XML;

class Refunded extends Handler
{
    protected $check = false;

    /**
     * @param \Closure $closure
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Payment\Kernel\Exceptions\InvalidSignException
     * @throws \Safe\Exceptions\ArrayException
     * @throws \Safe\Exceptions\OpensslException
     * @throws \Safe\Exceptions\PcreException
     * @throws \Safe\Exceptions\SimplexmlException
     * @throws \Safe\Exceptions\StringsException
     * @throws \Safe\Exceptions\UrlException
     */
    public function handle(Closure $closure)
    {
        $this->strict(
            \call_user_func($closure, $this->getMessage(), $this->reqInfo(), [$this, 'fail'])
        );

        return $this->toResponse();
    }

    /**
     * Decrypt the `req_info` from request message.
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Payment\Kernel\Exceptions\InvalidSignException
     * @throws \Safe\Exceptions\ArrayException
     * @throws \Safe\Exceptions\OpensslException
     * @throws \Safe\Exceptions\PcreException
     * @throws \Safe\Exceptions\SimplexmlException
     * @throws \Safe\Exceptions\StringsException
     * @throws \Safe\Exceptions\UrlException
     */
    public function reqInfo()
    {
        return XML::parse($this->decryptMessage('req_info'));
    }
}
