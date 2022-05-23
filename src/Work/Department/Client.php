<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Department;

use EasyWeChat\Kernel\BaseClient;

/**
 * This is WeWork Department Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Create a department.
     *
     * @param array $data
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $data)
    {
        return $this->httpPostJson('cgi-bin/department/create', $data);
    }

    /**
     * Update a department.
     *
     * @param int   $id
     * @param array $data
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(int $id, array $data)
    {
        return $this->httpPostJson('cgi-bin/department/update', array_merge(compact('id'), $data));
    }

    /**
     * Delete a department.
     *
     * @param int $id
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function delete(int $id)
    {
        return $this->httpGet('cgi-bin/department/delete', compact('id'));
    }

    /**
     * Get department lists.
     *
     * @param int|null $id
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function list(?int $id = null)
    {
        return $this->httpGet('cgi-bin/department/list', compact('id'));
    }

    /**
     * Get sub department lists.
     *
     * @param null|int $id
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function simpleList(?int $id = null)
    {
        return $this->httpGet('cgi-bin/department/simplelist', compact('id'));
    }

    /**
     * Get department details.
     *
     * @param int $id
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function get(int $id)
    {
        return $this->httpGet('cgi-bin/department/get', compact('id'));
    }
}
