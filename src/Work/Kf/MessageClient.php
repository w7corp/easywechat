<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Kf;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class MessageClient.
 *
 * @package EasyWeChat\Work\Kf
 *
 * @author 读心印 <aa24615@qq.com>
 */
class MessageClient extends BaseClient
{
    /**
     * 获取会话状态.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/94669
     *
     * @param string $openKfId
     * @param string $externalUserId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function state(string $openKfId, string $externalUserId)
    {
        $params = [
            'open_kfid' => $openKfId,
            'external_userid' => $externalUserId
        ];

        return $this->httpPostJson('cgi-bin/kf/service_state/get', $params);
    }

    /**
     * 变更会话状态.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/94669
     *
     * @param string $openKfId
     * @param string $externalUserId
     * @param int $serviceState
     * @param string $serviceUserId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateState(string $openKfId, string $externalUserId, int $serviceState, string $serviceUserId)
    {
        $params = [
            'open_kfid' => $openKfId,
            'external_userid' => $externalUserId,
            'service_state' => $serviceState,
            'servicer_userid' => $serviceUserId
        ];

        return $this->httpPostJson('cgi-bin/kf/service_state/trans', $params);
    }

    /**
     * 读取消息.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/94670
     *
     * @param string $cursor
     * @param string $token
     * @param int $limit
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sync(string $cursor, string $token, int $limit)
    {
        $params = [
            'cursor' => $cursor,
            'token' => $token,
            'limit' => $limit
        ];

        return $this->httpPostJson('cgi-bin/kf/sync_msg', $params);
    }

    /**
     * 发送消息.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/94677
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(array $params)
    {
        return $this->httpPostJson('cgi-bin/kf/send_msg', $params);
    }

    /**
     * 发送事件响应消息.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/94677
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function event(array $params)
    {
        return $this->httpPostJson('cgi-bin/kf/send_msg_on_event', $params);
    }
}
