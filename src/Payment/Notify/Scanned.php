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

class Scanned extends Handler
{
    protected $check = false;

    /**
     * @var string|null
     */
    protected $alert;

    public function alert(string $message)
    {
        $this->alert = $message;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function handle(Closure $closure)
    {
        $result = \call_user_func($closure, $this->getMessage(), [$this, 'fail'], [$this, 'alert']);

        $attributes = [
            'result_code' => is_null($this->alert) && is_null($this->fail) ? static::SUCCESS : static::FAIL,
            'err_code_des' => $this->alert,
        ];

        if (is_null($this->alert) && is_string($result)) {
            $attributes += [
                'appid' => $this->app['config']->app_id,
                'mch_id' => $this->app['config']->mch_id,
                'nonce_str' => uniqid(),
                'prepay_id' => $result,
            ];
        }

        return $this->respondWith($attributes, true)->toResponse();
    }
}
