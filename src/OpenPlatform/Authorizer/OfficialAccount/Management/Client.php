<?php
/**
 * Created by PhpStorm.
 * User: keal
 * Date: 2018/4/3
 * Time: 上午10:51
 */

namespace EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\Management;


use EasyWeChat\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * 获取公众号关联的小程序.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function list()
    {
        return $this->httpPostJson('cgi-bin/wxopen/wxamplinkget');
    }

    /**
     * 关联小程序.
     *
     * @param string $miniProgramAppId
     * @param bool $notifyUsers
     * @param bool $showProfile
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function link(string $miniProgramAppId, bool $notifyUsers = true, bool $showProfile = false)
    {
        $params = [
            'appid' => $miniProgramAppId,
            'notify_users' => $notifyUsers ? '1' : '0',
            'show_profile' => $showProfile ? '1' : '0'
        ];

        return $this->httpPostJson('cgi-bin/wxopen/wxamplink', $params);
    }

    /**
     * 解除已关联的小程序.
     *
     * @param $miniProgramAppId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function unlink($miniProgramAppId)
    {
        $params = [
            'appid' => $miniProgramAppId
        ];

        return $this->httpPostJson('cgi-bin/wxopen/wxampunlink', $params);
    }

    /**
     * 小程序快速注册.
     *
     * @param string $ticket
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function register(string $ticket)
    {
        $params = [
            'ticket' => $ticket
        ];

        return $this->httpPostJson('cgi-bin/account/fastregister', $params);
    }
}