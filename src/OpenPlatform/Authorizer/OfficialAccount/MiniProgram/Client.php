<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\MiniProgram;

use EasyWeChat\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * 获取公众号关联的小程序.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list()
    {
        return $this->httpPostJson('cgi-bin/wxopen/wxamplinkget');
    }

    /**
     * 关联小程序.
     *
     * @param string $appId
     * @param bool   $notifyUsers
     * @param bool   $showProfile
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function link(string $appId, bool $notifyUsers = true, bool $showProfile = false)
    {
        $params = [
            'appid' => $appId,
            'notify_users' => (string) $notifyUsers,
            'show_profile' => (string) $showProfile,
        ];

        return $this->httpPostJson('cgi-bin/wxopen/wxamplink', $params);
    }

    /**
     * 解除已关联的小程序.
     *
     * @param string $appId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unlink(string $appId)
    {
        $params = [
            'appid' => $appId,
        ];

        return $this->httpPostJson('cgi-bin/wxopen/wxampunlink', $params);
    }
}
