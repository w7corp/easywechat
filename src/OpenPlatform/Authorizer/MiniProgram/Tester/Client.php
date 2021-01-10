<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Tester;

use EasyWeChat\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * 绑定小程序体验者.
     *
     * @param string $wechatId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function bind(string $wechatId)
    {
        return $this->httpPostJson('wxa/bind_tester', [
            'wechatid' => $wechatId,
        ]);
    }

    /**
     * 解绑小程序体验者.
     *
     * @param string $wechatId
     * @param string $userStr
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unbind(string $wechatId = null, string $userStr = null)
    {
        return $this->httpPostJson('wxa/unbind_tester', [
                ($userStr ? 'userstr' : 'wechatid') => $userStr ?? $wechatId,
            ]);
    }

    public function unbindWithUserStr(string $userStr)
    {
        return $this->httpPostJson('wxa/unbind_tester', [
                'userstr' => $userStr,
            ]);
    }

    /**
     * 获取体验者列表.
     *
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list()
    {
        return $this->httpPostJson('wxa/memberauth', [
            'action' => 'get_experiencer',
        ]);
    }
}
