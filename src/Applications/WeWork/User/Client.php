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

use EasyWeChat\Support\HasHttpRequests;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client
{
    use HasHttpRequests {
        get as httpGet;
    }

    /**
     * Create a user.
     *
     * @param array $data
     *
     * @return TODO
     */
    public function create(array $data)
    {
        return $this->parseJSON($this->post('https://qyapi.weixin.qq.com/cgi-bin/user/create', $data));
    }

    /**
     * Update an exist user.
     *
     * @param array $data
     *
     * @return TODO
     */
    public function update(array $data)
    {
        return $this->parseJSON($this->post('https://qyapi.weixin.qq.com/cgi-bin/user/update', $data));
    }

    /**
     * Delete a user.
     *
     * @param string $userId
     *
     * @return TODO
     */
    public function delete($userId)
    {
        return $this->parseJSON($this->httpGet('https://qyapi.weixin.qq.com/cgi-bin/user/delete', ['userid' => $userId]));
    }

    /**
     * Batch delete users.
     *
     * @param array $userIds
     *
     * @return TODO
     */
    public function batchDelete(array $userIds)
    {
        return $this->parseJSON($this->post('https://qyapi.weixin.qq.com/cgi-bin/user/batchdelete', ['useridlist' => $userIds]));
    }

    /**
     * Get user.
     *
     * @param string $userId
     *
     * @return TODO
     */
    public function get($userId)
    {
        return $this->parseJSON($this->httpGet('https://qyapi.weixin.qq.com/cgi-bin/user/get', ['userid' => $userId]));
    }

    /**
     * DESC.
     *
     * @param int $departmentId
     * @param int $fetchChild
     * @param int $status
     *
     * @return TODO
     */
    public function simpleLists(int $departmentId, int $fetchChild = 1, int $status = 4)
    {
        return $this->getLists(
            'https://qyapi.weixin.qq.com/cgi-bin/user/simplelist',
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
     * @return TODO
     */
    public function lists(int $departmentId, int $fetchChild = 1, int $status = 4)
    {
        return $this->getLists(
            'https://qyapi.weixin.qq.com/cgi-bin/user/list',
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
     * @return TODO
     */
    protected function getLists(string $endpoint, int $departmentId, int $fetchChild, int $status)
    {
        $params = [
            'department_id' => $departmentId,
            'fetch_child' => $fetchChild,
            'status' => $status,
        ];

        return $this->parseJSON($this->httpGet($endpoint, $params));
    }
}
