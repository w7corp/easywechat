<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OfficialAccount\Jssdk;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Support;
use EasyWeChat\Support\InteractsWithCache;

/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
    use InteractsWithCache;

    /**
     * Current URI.
     *
     * @var string
     */
    protected $url;

    /**
     * Ticket cache prefix.
     */
    const TICKET_CACHE_PREFIX = 'overtrue.wechat.jsapi_ticket.';

    /**
     * Api of ticket.
     */
    const API_TICKET = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket';

    /**
     * Get config json for jsapi.
     *
     * @param array $APIs
     * @param bool  $debug
     * @param bool  $beta
     * @param bool  $json
     *
     * @return array|string
     */
    public function config(array $APIs, $debug = false, $beta = false, $json = true)
    {
        $signPackage = $this->signature();

        $base = [
            'debug' => $debug,
            'beta' => $beta,
        ];
        $config = array_merge($base, $signPackage, ['jsApiList' => $APIs]);

        return $json ? json_encode($config) : $config;
    }

    /**
     * Return jsapi config as a PHP array.
     *
     * @param array $APIs
     * @param bool  $debug
     * @param bool  $beta
     *
     * @return array
     */
    public function getConfigArray(array $APIs, $debug = false, $beta = false)
    {
        return $this->config($APIs, $debug, $beta, false);
    }

    /**
     * Get jsticket.
     *
     * @param bool $fresh
     *
     * @return string
     */
    public function ticket(bool $fresh = false)
    {
        $key = self::TICKET_CACHE_PREFIX.$this->app['config']['app_id'];
        $ticket = $this->getCache()->get($key);

        if (!$fresh && !is_null($ticket)) {
            return $ticket;
        }

        $result = $this->httpGet(self::API_TICKET, ['type' => 'jsapi']);

        $this->getCache()->set($key, $ticket = $result['ticket'], $result['expires_in'] - 500);

        return $ticket;
    }

    /**
     * Build signature.
     *
     * @param string $url
     * @param string $nonce
     * @param int    $timestamp
     *
     * @return array
     */
    public function signature($url = null, $nonce = null, $timestamp = null)
    {
        $url = $url ?: $this->getUrl();
        $nonce = $nonce ?: Support\Str::quickRandom(10);
        $timestamp = $timestamp ?: time();

        return [
            'appId' => $this->app['config']['app_id'],
            'nonceStr' => $nonce,
            'timestamp' => $timestamp,
            'url' => $url,
            'signature' => $this->getSignature($this->ticket(), $nonce, $timestamp, $url),
        ];
    }

    /**
     * Sign the params.
     *
     * @param string $ticket
     * @param string $nonce
     * @param int    $timestamp
     * @param string $url
     *
     * @return string
     */
    public function getSignature($ticket, $nonce, $timestamp, $url)
    {
        return sha1("jsapi_ticket={$ticket}&noncestr={$nonce}&timestamp={$timestamp}&url={$url}");
    }

    /**
     * Set current url.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get current url.
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->url) {
            return $this->url;
        }

        return Support\Url::current();
    }
}
