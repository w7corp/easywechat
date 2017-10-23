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

class Paid extends Handler
{
    /**
     * @param \Closure $closure
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Closure $closure)
    {
        $this->strict(
            $closure->bindTo($this)->__invoke($this->getMessage(), [$this, 'fail'])
        );

        return $this->toResponse();
    }
}
