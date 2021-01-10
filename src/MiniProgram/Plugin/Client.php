<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\Plugin;

use EasyWeChat\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @param string $appId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function apply($appId)
    {
        return $this->httpPostJson('wxa/plugin', [
            'action' => 'apply',
            'plugin_appid' => $appId,
        ]);
    }

    /**
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list()
    {
        return $this->httpPostJson('wxa/plugin', [
            'action' => 'list',
        ]);
    }

    /**
     * @param string $appId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unbind($appId)
    {
        return $this->httpPostJson('wxa/plugin', [
            'action' => 'unbind',
            'plugin_appid' => $appId,
        ]);
    }
}
