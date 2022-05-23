<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\User;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Create a user.
     *
     * @param array $data
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $data)
    {
        return $this->httpPostJson('cgi-bin/user/create', $data);
    }

    /**
     * Update an exist user.
     *
     * @param string $id
     * @param array  $data
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(string $id, array $data)
    {
        return $this->httpPostJson('cgi-bin/user/update', array_merge(['userid' => $id], $data));
    }

    /**
     * Delete a user.
     *
     * @param string|array $userId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete($userId)
    {
        if (is_array($userId)) {
            return $this->batchDelete($userId);
        }

        return $this->httpGet('cgi-bin/user/delete', ['userid' => $userId]);
    }

    /**
     * Batch delete users.
     *
     * @param array $userIds
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function batchDelete(array $userIds)
    {
        return $this->httpPostJson('cgi-bin/user/batchdelete', ['useridlist' => $userIds]);
    }

    /**
     * Get user.
     *
     * @param string $userId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function get(string $userId)
    {
        return $this->httpGet('cgi-bin/user/get', ['userid' => $userId]);
    }

    /**
     * Get simple user list.
     *
     * @param int  $departmentId
     * @param bool $fetchChild
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getDepartmentUsers(int $departmentId, bool $fetchChild = false)
    {
        $params = [
            'department_id' => $departmentId,
            'fetch_child' => (int) $fetchChild,
        ];

        return $this->httpGet('cgi-bin/user/simplelist', $params);
    }

    /**
     * Get user list.
     *
     * @param int  $departmentId
     * @param bool $fetchChild
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getDetailedDepartmentUsers(int $departmentId, bool $fetchChild = false)
    {
        $params = [
            'department_id' => $departmentId,
            'fetch_child' => (int) $fetchChild,
        ];

        return $this->httpGet('cgi-bin/user/list', $params);
    }

    /**
     * Convert userId to openid.
     *
     * @param string   $userId
     * @param int|null $agentId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function userIdToOpenid(string $userId, int $agentId = null)
    {
        $params = [
            'userid' => $userId,
            'agentid' => $agentId,
        ];

        return $this->httpPostJson('cgi-bin/user/convert_to_openid', $params);
    }

    /**
     * Convert openid to userId.
     *
     * @param string $openid
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function openidToUserId(string $openid)
    {
        $params = [
            'openid' => $openid,
        ];

        return $this->httpPostJson('cgi-bin/user/convert_to_userid', $params);
    }

    /**
     * Convert mobile to userId.
     *
     * @param string $mobile
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function mobileToUserId(string $mobile)
    {
        $params = [
            'mobile' => $mobile,
        ];

        return $this->httpPostJson('cgi-bin/user/getuserid', $params);
    }

    /**
     * @param string $userId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function accept(string $userId)
    {
        $params = [
            'userid' => $userId,
        ];

        return $this->httpGet('cgi-bin/user/authsucc', $params);
    }

    /**
     * Batch invite users.
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function invite(array $params)
    {
        return $this->httpPostJson('cgi-bin/batch/invite', $params);
    }

    /**
     * Get invitation QR code.
     *
     * @param int $sizeType
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getInvitationQrCode(int $sizeType = 1)
    {
        if (!\in_array($sizeType, [1, 2, 3, 4], true)) {
            throw new InvalidArgumentException('The sizeType must be 1, 2, 3, 4.');
        }

        return $this->httpGet('cgi-bin/corp/get_join_qrcode', ['size_type' => $sizeType]);
    }

    /**
     * @param string|null $cursor
     * @param int $limit
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMemberAuthList(?string $cursor = null, int $limit = 1000)
    {
        return $this->httpPostJson('cgi-bin/user/list_member_auth', [
            'cursor' => $cursor,
            'limit' => $limit
        ]);
    }

    /**
     * @param string $openUserId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkMemberAuth(string $openUserId)
    {
        return $this->httpPostJson('cgi-bin/user/check_member_auth', [
            'open_userid' => $openUserId
        ]);
    }

    /**
     * @param string $selectedTicket
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSelectedTicketUserList(string $selectedTicket)
    {
        return $this->httpPostJson('cgi-bin/user/list_selected_ticket_user', [
            'selected_ticket' => $selectedTicket
        ]);
    }

    /**
     * userid转换为open_userid
     *
     * @param array $user_ids
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function userIdToOpenUserId(array $user_ids)
    {
        return $this->httpPostJson('cgi-bin/batch/userid_to_openuserid', [
            'userid_list' => $user_ids
        ]);
    }
}
