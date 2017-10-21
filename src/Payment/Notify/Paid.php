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

use Symfony\Component\HttpFoundation\Response;

class Paid extends Handler
{
    /**
     * @param callable $callback
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(callable $callback): Response
    {
        $this->strict(
            $callback->__invoke($this->getMessage(), [$this, 'fail'])
        );

        return $this->toResponse();
    }
}
