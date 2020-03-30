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
use EasyWeChat\Kernel\Exceptions\Exception;
use EasyWeChat\Kernel\Support;
use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\Payment\Kernel\Exceptions\InvalidSignException;
use Symfony\Component\HttpFoundation\Response;

abstract class Handler
{
    const SUCCESS = 'SUCCESS';
    const FAIL = 'FAIL';

    /**
     * @var \EasyWeChat\Payment\Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $message;

    /**
     * @var string|null
     */
    protected $fail;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Check sign.
     * If failed, throws an exception.
     *
     * @var bool
     */
    protected $check = true;

    /**
     * Respond with sign.
     *
     * @var bool
     */
    protected $sign = false;

    /**
     * @param \EasyWeChat\Payment\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Handle incoming notify.
     *
     * @param \Closure $closure
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    abstract public function handle(Closure $closure);

    /**
     * @param string $message
     */
    public function fail(string $message)
    {
        $this->fail = $message;
    }

    /**
     * @param array $attributes
     * @param bool  $sign
     *
     * @return $this
     */
    public function respondWith(array $attributes, bool $sign = false)
    {
        $this->attributes = $attributes;
        $this->sign = $sign;

        return $this;
    }

    /**
     * Build xml and return the response to WeChat.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function toResponse(): Response
    {
        $base = [
            'return_code' => is_null($this->fail) ? static::SUCCESS : static::FAIL,
            'return_msg' => $this->fail,
        ];

        $attributes = array_merge($base, $this->attributes);

        if ($this->sign) {
            $attributes['sign'] = Support\generate_sign($attributes, $this->app->getKey());
        }

        return new Response(XML::build($attributes));
    }

    /**
     * Return the notify message from request.
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function getMessage(): array
    {
        if (!empty($this->message)) {
            return $this->message;
        }

        try {
            $message = XML::parse(strval($this->app['request']->getContent()));
        } catch (\Throwable $e) {
            throw new Exception('Invalid request XML: '.$e->getMessage(), 400);
        }

        if (!is_array($message) || empty($message)) {
            throw new Exception('Invalid request XML.', 400);
        }

        if ($this->check) {
            $this->validate($message);
        }

        return $this->message = $message;
    }

    /**
     * Decrypt message.
     *
     * @param string $key
     *
     * @return string|null
     *
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function decryptMessage(string $key)
    {
        $message = $this->getMessage();
        if (empty($message[$key])) {
            return null;
        }

        return Support\AES::decrypt(
            base64_decode($message[$key], true),
            md5($this->app['config']->key),
            '',
            OPENSSL_RAW_DATA,
            'AES-256-ECB'
        );
    }

    /**
     * Validate the request params.
     *
     * @param array $message
     *
     * @throws \EasyWeChat\Payment\Kernel\Exceptions\InvalidSignException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    protected function validate(array $message)
    {
        $sign = $message['sign'];
        unset($message['sign']);

        if (Support\generate_sign($message, $this->app->getKey()) !== $sign) {
            throw new InvalidSignException();
        }
    }

    /**
     * @param mixed $result
     */
    protected function strict($result)
    {
        if (true !== $result && is_null($this->fail)) {
            $this->fail(strval($result));
        }
    }
}
