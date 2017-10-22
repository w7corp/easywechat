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

use EasyWeChat\Kernel\Support\XML;
use Symfony\Component\HttpFoundation\Response;

class Refunded extends Handler
{
    protected $check = false;

    /**
     * @param callable $callback
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(callable $callback): Response
    {
        $this->strict(
            call_user_func_array($callback, [$this->getMessage(), [$this, 'fail']])
        );

        return $this->toResponse();
    }

    public function decryptedInfo()
    {
        return XML::parse($this->decryptMessage('req_info'));
    }
}
