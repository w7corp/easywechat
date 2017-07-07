<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\WeWork\Department;

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
     */
    public function create(array $data)
    {
        return $this->httpPostJson('department/create', $data);
    }

    /**
     * Update a department.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function update(array $data)
    {
        return $this->httpPostJson('department/update', $data);
    }

    /**
     * Delete a department.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        return $this->httpGet('department/delete', compact('id'));
    }

    /**
     * Get department lists.
     *
     * @param int|null $id
     *
     * @return mixed
     */
    public function lists($id = null)
    {
        return $this->httpGet('department/list', compact('id'));
    }
}
