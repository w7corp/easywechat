<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\WeWork\User;

use EasyWeChat\Kernel\BaseClient;

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
     */
    public function create(array $data)
    {
        return $this->httpPost('user/create', $data);
    }

    /**
     * Update an exist user.
     *
     * @param array $data
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function update(array $data)
    {
        return $this->httpPost('user/update', $data);
    }

    /**
     * Delete a user.
     *
     * @param string $userId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function delete($userId)
    {
        return $this->httpGet('user/delete', ['userid' => $userId]);
    }

    /**
     * Batch delete users.
     *
     * @param array $userIds
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function batchDelete(array $userIds)
    {
        return $this->httpPost('user/batchdelete', ['useridlist' => $userIds]);
    }

    /**
     * Get user.
     *
     * @param string $userId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function get($userId)
    {
        return $this->httpGet('user/get', ['userid' => $userId]);
    }

    /**
     * Get simple user list.
     *
     * @param int  $departmentId
     * @param bool $fetchChild
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function getDepartmentUsers(int $departmentId, bool $fetchChild = false)
    {
        $params = [
            'department_id' => $departmentId,
            'fetch_child' => (int) $fetchChild,
        ];

        return $this->httpGet('user/simplelist', $params);
    }

    /**
     * Get user list.
     *
     * @param int  $departmentId
     * @param bool $fetchChild
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function getDepartmentUsersWithDetail(int $departmentId, bool $fetchChild = false)
    {
        $params = [
            'department_id' => $departmentId,
            'fetch_child' => (int) $fetchChild,
        ];

        return $this->httpGet('user/list', $params);
    }

    /**
     * Convert userId to openId.
     *
     * @param string   $userId
     * @param int|null $agentId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function userIdToOpenId($userId, $agentId = null)
    {
        $params = [
            'userid' => $userId,
            'agentid' => $agentId,
        ];

        return $this->httpPostJson('user/convert_to_openid', $params);
    }

    /**
     * Convert openId to userId.
     *
     * @param string $openId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function openIdToUserId($openId)
    {
        $params = [
            'openid' => $openId,
        ];

        return $this->httpPostJson('user/convert_to_userid', $params);
    }

    /**
     * @param string $userId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function accept($userId)
    {
        $params = [
            'userid' => $userId,
        ];

        return $this->httpGet('user/authsucc', $params);
    }
}
