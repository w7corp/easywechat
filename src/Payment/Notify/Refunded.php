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

class Refunded extends Handler
{
    /**
     * @param callable $callback
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(callable $callback): Response
    {
        $message = $this->getMessage();
        $message['decrypted'] = [
            'req_info' => $this->decryptMessage('req_info'),
        ];

        $result = $callback->__invoke($message, [$this, 'fail']);

        if ($result !== true) {
            $this->fail(strval($result));
        }

        return $this->toResponse();
    }
}
