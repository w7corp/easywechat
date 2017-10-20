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

use EasyWeChat\Kernel\Exceptions\Exception;
use EasyWeChat\Kernel\Support;
use EasyWeChat\Kernel\Support\XML;
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
    protected $respondWith = [];

    /**
     * @var bool
     */
    protected $enableSign = false;

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
     * @param callable $callback
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    abstract public function handle(callable $callback): Response;

    /**
     * @param string $message
     *
     * @return $this
     */
    public function fail(string $message)
    {
        $this->fail = $message;

        return $this;
    }

    /**
     * @param array $attributes
     * @param bool  $sign
     *
     * @return $this
     */
    public function respondWith(array $attributes, bool $sign = false)
    {
        $this->respondWith = $attributes;
        $this->enableSign = $sign;

        return $this;
    }

    /**
     * Build xml and return the response to WeChat.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse(): Response
    {
        $base = [
            'return_code' => is_null($this->fail) ? static::SUCCESS : static::FAIL,
            'return_msg' => $this->fail,
        ];

        $attributes = array_merge($base, $this->respondWith);

        if ($this->enableSign) {
            $attributes['sign'] = Support\generate_sign($attributes, $this->app['config']->key);
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
    public function getMessage()
    {
        if ($this->message) {
            return $this->message;
        }

        try {
            $xml = XML::parse(strval($this->app['request']->getContent()));
        } catch (\Throwable $e) {
            throw new Exception('Invalid request XML: '.$e->getMessage(), 400);
        }

        if (!is_array($xml) || empty($xml)) {
            throw new Exception('Invalid request XML.', 400);
        }

        return $this->message = $xml;
    }

    /**
     * Validate the request params.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $sign = Support\generate_sign($this->getMessage()->except('sign')->all(), $this->merchant->key, 'md5');

        return $sign === $this->getMessage()->get('sign');
    }

    /**
     * Decrypt req_info in refund Notify.
     *
     * @return mixed
     */
    public function decryptMessage(string $key)
    {
        $message = $this->getMessage();

        if (empty($message[$key])) {
            return null;
        }

        return AES::decrypt(base64_decode($message[$key], true), md5($this->app['config']->key), substr(md5($this->app['config']->key), 0, 16));
    }
}
