<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Js.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */
namespace EasyWeChat\Js;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Support\Str;
use EasyWeChat\Support\Url as UrlHelper;

/**
 * Class Js.
 */
class Js extends AbstractAPI
{
    /**
     * Cache.
     *
     * @var Cache
     */
    protected $cache;

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
     * @return string
     */
    public function ticket()
    {
        $key = self::TICKET_CACHE_PREFIX.$this->getAccessToken()->getAppId();

        if ($ticket = $this->getCache()->fetch($key)) {
            return $ticket;
        }

        $result = $this->parseJSON('get', [self::API_TICKET, ['type' => 'jsapi']]);

        $this->getCache()->save($key, $result['ticket'], $result['expires_in'] - 500);

        return $result['ticket'];
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
        $url = $url ? $url : $this->getUrl();
        $nonce = $nonce ? $nonce : Str::quickRandom(10);
        $timestamp = $timestamp ? $timestamp : time();
        $ticket = $this->ticket();

        $sign = [
                 'appId' => $this->getAccessToken()->getAppId(),
                 'nonceStr' => $nonce,
                 'timestamp' => $timestamp,
                 'url' => $url,
                 'signature' => $this->getSignature($ticket, $nonce, $timestamp, $url),
                ];

        return $sign;
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
     * @return Js
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

        return UrlHelper::current();
    }

    /**
     * Set cache manager.
     *
     * @param \Doctrine\Common\Cache\Cache $cache
     *
     * @return $this
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Return cache manager.
     *
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache ?: $this->cache = new FilesystemCache(sys_get_temp_dir());
    }
}
