<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Jssdk;

use EasyWeChat\BasicService\Jssdk\Client as BaseClient;
use EasyWeChat\Kernel\Exceptions\RuntimeException;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    protected $ticketEndpoint = 'https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket';

    /**
     * @return string
     */
    protected function getAppId()
    {
        return $this->app['config']->get('corp_id');
    }

    /**
     * @return string
     */
    protected function getAgentId()
    {
        return $this->app['config']->get('agent_id');
    }

    /**
     * @param bool   $refresh
     * @param string $type
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|mixed|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws RuntimeException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getAgentTicket(bool $refresh = false, string $type = 'agent_config')
    {
        $cacheKey = sprintf('easywechat.work.jssdk.ticket.%s.%s', $type, $this->getAppId());

        if (!$refresh && $this->getCache()->has($cacheKey)) {
            return $this->getCache()->get($cacheKey);
        }

        /** @var array<string, mixed> $result */
        $result = $this->castResponseToType(
            $this->requestRaw('cgi-bin/ticket/get', 'GET', ['query' => ['type' => $type]]),
            'array'
        );

        $this->getCache()->set($cacheKey, $result, $result['expires_in'] - 500);

        if (!$this->getCache()->has($cacheKey)) {
            throw new RuntimeException('Failed to cache jssdk ticket.');
        }

        return $result;
    }

    /**
     * 获取应用的agentConfig
     * @param array $jsApiList
     * @param bool $debug
     * @param bool $beta
     * @param bool $json
     * @return array|false|string
     * @throws RuntimeException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function buildAgentConfig(array $jsApiList, bool $debug = false, bool $beta = false, bool $json = true)
    {
        $config = array_merge(compact('debug', 'beta', 'jsApiList'), $this->configAgentSignature());

        return $json ? json_encode($config) : $config;
    }

    /**
     * 获取应用签名数据
     * @param string|null $url
     * @param string|null $nonce
     * @param null $timestamp
     * @return array
     * @throws RuntimeException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    protected function configAgentSignature(string $url = null, string $nonce = null, $timestamp = null): array
    {
        $url = $url ?: $this->getUrl();
        $nonce = $nonce ?: Support\Str::quickRandom(10);
        $timestamp = $timestamp ?: time();

        return [
            'appId' => $this->getAppId(),
            'nonceStr' => $nonce,
            'timestamp' => $timestamp,
            'url' => $url,
            'signature' => $this->getTicketSignature($this->getAgentTicket()['ticket'], $nonce, $timestamp, $url),
        ];
    }
}
