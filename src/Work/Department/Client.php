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
    public function delete($id)
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
    public function list($id = null)
    {
        return $this->httpGet('cgi-bin/department/list', compact('id'));
    }
}
