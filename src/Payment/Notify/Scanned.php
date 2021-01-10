<?php

declare(strict_types=1);

namespace EasyWeChat\Payment\Notify;

use Closure;

class Scanned extends Handler
{
    protected $check = false;

    /**
     * @var string|null
     */
    protected $alert;

    /**
     * @param string $message
     */
    public function alert(string $message)
    {
        $this->alert = $message;
    }

    /**
     * @param \Closure $closure
     *
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
