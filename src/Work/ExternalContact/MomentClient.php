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
 * Class MomentClient.
 *
 * @author 读心印 <aa24615@qq.com>
 */
class MomentClient extends BaseClient
{
    /**
     * 创建发表任务
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/95094#%E5%88%9B%E5%BB%BA%E5%8F%91%E8%A1%A8%E4%BB%BB%E5%8A%A1
     * @param array $param
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createTask(array $param)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/add_moment_task', $param);
    }

    /**
     * 获取任务创建结果
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/95094#%E8%8E%B7%E5%8F%96%E4%BB%BB%E5%8A%A1%E5%88%9B%E5%BB%BA%E7%BB%93%E6%9E%9C
     *
     * @param string $jobId 异步任务id
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTask(string $jobId)
    {
        return $this->httpGet('cgi-bin/externalcontact/get_moment_task_result', ['jobid' => $jobId]);
    }


    /**
     * 获取企业全部的发表列表.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93333#获取企业全部的发表列表
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException|\GuzzleHttp\Exception\GuzzleException
     */
    public function list(array $params)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/get_moment_list', $params);
    }

    /**
     * 获取客户朋友圈企业发表的列表.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93333#获取客户朋友圈企业发表的列表
     *
     * @param string $momentId
     * @param string $cursor
     * @param int $limit
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException|\GuzzleHttp\Exception\GuzzleException
     */
    public function getTasks(string $momentId, string $cursor = '', int $limit = 500)
    {
        $params = [
            'moment_id' => $momentId,
            'cursor' => $cursor,
            'limit' => $limit
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/get_moment_task', $params);
    }

    /**
     * 获取客户朋友圈发表时选择的可见范围.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93333#获取客户朋友圈发表时选择的可见范围
     *
     * @param string $momentId
     * @param string $userId
     * @param string $cursor
     * @param int $limit
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException|\GuzzleHttp\Exception\GuzzleException
     */
    public function getCustomers(string $momentId, string $userId, string $cursor, int $limit)
    {
        $params = [
            'moment_id' => $momentId,
            'userid' => $userId,
            'cursor' => $cursor,
            'limit' => $limit
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/get_moment_customer_list', $params);
    }

    /**
     * 获取客户朋友圈发表后的可见客户列表.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93333#获取客户朋友圈发表后的可见客户列表
     *
     * @param string $momentId
     * @param string $userId
     * @param string $cursor
     * @param int $limit
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException|\GuzzleHttp\Exception\GuzzleException
     */
    public function getSendResult(string $momentId, string $userId, string $cursor, int $limit)
    {
        $params = [
            'moment_id' => $momentId,
            'userid' => $userId,
            'cursor' => $cursor,
            'limit' => $limit
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/get_moment_send_result', $params);
    }

    /**
     * 获取客户朋友圈的互动数据.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93333#获取客户朋友圈的互动数据
     *
     * @param string $momentId
     * @param string $userId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException|\GuzzleHttp\Exception\GuzzleException
     */
    public function getComments(string $momentId, string $userId)
    {
        $params = [
            'moment_id' => $momentId,
            'userid' => $userId
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/get_moment_comments', $params);
    }
}
