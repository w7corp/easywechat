<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\CorpGroup;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author 读心印 <aa24615@qq.com>
 */
class Client extends BaseClient
{
    /**
     * 获取应用共享信息.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93403
     *
     * @param int $agentId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAppShareInfo(int $agentId)
    {
        $params = [
            'agentid' => $agentId
        ];

        return $this->httpPostJson('cgi-bin/corpgroup/corp/list_app_share_info', $params);
    }

    /**
     * 获取下级企业的access_token.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93359
     *
     * @param string $corpId
     * @param int $agentId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getToken(string $corpId, int $agentId)
    {
        $params = [
            'corpid' => $corpId,
            'agentid' => $agentId
        ];

        return $this->httpPostJson('cgi-bin/corpgroup/corp/gettoken', $params);
    }

    /**
     * 获取下级企业的小程序session.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93355
     *
     * @param string $userId
     * @param string $sessionKey
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMiniProgramTransferSession(string $userId, string $sessionKey)
    {
        $params = [
            'userid' => $userId,
            'session_key' => $sessionKey
        ];

        return $this->httpPostJson('cgi-bin/miniprogram/transfer_session', $params);
    }

    /**
     * 将明文corpid转换为第三方应用获取的corpid（仅限第三方服务商，转换已获授权企业的corpid）
     *
     * @see https://open.work.weixin.qq.com/api/doc/90001/90143/95327#1.4%20corpid%E8%BD%AC%E6%8D%A2
     *
     * @param string $corpId 获取到的企业ID
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOpenCorpid(string $corpId)
    {
        return $this->httpPostJson('cgi-bin/corp/to_open_corpid', ['corpid' => $corpId]);
    }

    /**
     * 将自建应用获取的userid转换为第三方应用获取的userid（仅代开发自建应用或第三方应用可调用）
     *
     * @see https://open.work.weixin.qq.com/api/doc/90001/90143/95327#2.4%20userid%E7%9A%84%E8%BD%AC%E6%8D%A2
     *
     * @param array $useridList 获取到的成员ID
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function batchUseridToOpenUserid(array $useridList)
    {
        return $this->httpPostJson('cgi-bin/batch/userid_to_openuserid', ['userid_list' => $useridList]);
    }
}
