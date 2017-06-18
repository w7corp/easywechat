<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\WeWork\User;

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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
     */
    public function get($userId)
    {
        return $this->httpGet('user/get', ['userid' => $userId]);
    }

    /**
     * DESC.
     *
     * @param int $departmentId
     * @param int $fetchChild
     * @param int $status
     *
     * @return array
     */
    public function simpleLists(int $departmentId, int $fetchChild = 1, int $status = 4)
    {
        return $this->getLists(
            'user/simplelist',
            $departmentId, $fetchChild, $status
        );
    }

    /**
     * DESC.
     *
     * @param int $departmentId
     * @param int $fetchChild
     * @param int $status
     *
     * @return array
     */
    public function lists(int $departmentId, int $fetchChild = 1, int $status = 4)
    {
        return $this->getLists(
            'user/list',
            $departmentId, $fetchChild, $status
        );
    }

    /**
     * Get user lists.
     *
     * @param string $endpoint
     * @param int    $departmentId
     * @param int    $fetchChild
     * @param int    $status
     *
     * @return array
     */
    protected function getLists(string $endpoint, int $departmentId, int $fetchChild, int $status)
    {
        $params = [
            'department_id' => $departmentId,
            'fetch_child' => $fetchChild,
            'status' => $status,
        ];

        return $this->httpGet($endpoint, $params);
    }
}
