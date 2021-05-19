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

/**
 * Class Client.
 *
 * @author 读心印 <aa24615@qq.com>
 */
class LinkedCorpClient extends BaseClient
{
    /**
     * 获取应用的可见范围.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93172
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAgentPermissions()
    {
        return $this->httpPostJson('cgi-bin/linkedcorp/agent/get_perm_list');
    }

    /**
     * 获取互联企业成员详细信息.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93171
     *
     * @param string $userId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUser(string $userId)
    {
        $params = [
            'userid' => $userId
        ];

        return $this->httpPostJson('cgi-bin/linkedcorp/user/get', $params);
    }

    /**
     * 获取互联企业部门成员.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93168
     *
     * @param string $departmentId
     * @param bool $fetchChild
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUsers(string $departmentId, bool $fetchChild = true)
    {
        $params = [
            'department_id' => $departmentId,
            'fetch_child' => $fetchChild
        ];

        return $this->httpPostJson('cgi-bin/linkedcorp/user/simplelist', $params);
    }

    /**
     * 获取互联企业部门成员详情.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93169
     *
     * @param string $departmentId
     * @param bool $fetchChild
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDetailedUsers(string $departmentId, bool $fetchChild = true)
    {
        $params = [
            'department_id' => $departmentId,
            'fetch_child' => $fetchChild
        ];

        return $this->httpPostJson('cgi-bin/linkedcorp/user/list', $params);
    }

    /**
     * 获取互联企业部门列表.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93170
     *
     * @param string $departmentId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDepartments(string $departmentId)
    {
        $params = [
            'department_id' => $departmentId,
        ];

        return $this->httpPostJson('cgi-bin/linkedcorp/department/list', $params);
    }
}
