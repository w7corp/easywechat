<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\WeWork\Department;

use EasyWeChat\Applications\Base\Core\AbstractAPI;

/**
 * This is WeWork Department Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends AbstractAPI
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
        return $this->parseJSON('json', ['https://qyapi.weixin.qq.com/cgi-bin/department/create', $data]);
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
        return $this->parseJSON('json', ['https://qyapi.weixin.qq.com/cgi-bin/department/update', $data]);
    }

    /**
     * Delete a department.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function delete(int $id)
    {
        return $this->parseJSON('get', ['https://qyapi.weixin.qq.com/cgi-bin/department/delete', compact('id')]);
    }

    /**
     * Get department lists.
     *
     * @param int|null $id
     *
     * @return mixed
     */
    public function lists(int $id = null)
    {
        return $this->parseJSON('get', ['https://qyapi.weixin.qq.com/cgi-bin/department/list', compact('id')]);
    }
}
