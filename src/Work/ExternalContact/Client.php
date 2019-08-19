<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\ExternalContact;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * 获取配置了客户联系功能的成员列表.
     *
     * @see https://work.weixin.qq.com/api/doc#90000/90135/91554
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getFollowUsers()
    {
        return $this->httpGet('cgi-bin/externalcontact/get_follow_user_list');
    }

    /**
     * 获取外部联系人列表.
     *
     * @see https://work.weixin.qq.com/api/doc#90000/90135/91555
     *
     * @param string $userId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function list(string $userId)
    {
        return $this->httpGet('cgi-bin/externalcontact/list', [
            'userid' => $userId,
        ]);
    }

    /**
     * 获取外部联系人详情.
     *
     * @see https://work.weixin.qq.com/api/doc#90000/90135/91556
     *
     * @param string $externalUserId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function get(string $externalUserId)
    {
        return $this->httpGet('cgi-bin/externalcontact/get', [
            'external_userid' => $externalUserId,
        ]);
    }

    /**
     * 获取离职成员的客户列表.
     *
     * @see https://work.weixin.qq.com/api/doc#90000/90135/91563
     *
     * @param int $pageId
     * @param int $pageSize
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUnassigned(int $pageId = 0, int $pageSize = 1000)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/get_unassigned_list', [
            'page_id' => $pageId,
            'page_size' => $pageSize,
        ]);
    }

    /**
     * 离职成员的外部联系人再分配.
     *
     * @see https://work.weixin.qq.com/api/doc#90000/90135/91564
     *
     * @param string $externalUserId
     * @param string $handoverUserId
     * @param string $takeoverUserId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function transfer(string $externalUserId, string $handoverUserId, string $takeoverUserId)
    {
        $params = [
            'external_userid' => $externalUserId,
            'handover_userid' => $handoverUserId,
            'takeover_userid' => $takeoverUserId,
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/transfer', $params);
    }
}
