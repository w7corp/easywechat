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
 * Class SyncClient.
 *
 * @author 读心印 <aa24615@qq.com>
 */
class SyncClient extends BaseClient
{
    /**
     * 增量更新成员.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/90980
     *
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function batchUpdateUser(array $params)
    {
        return $this->httpPostJson('cgi-bin/batch/syncuser', $params);
    }

    /**
     * 全量覆盖成员.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/90981
     *
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function batchReplaceUser(array $params)
    {
        return $this->httpPostJson('cgi-bin/batch/replaceuser', $params);
    }

    /**
     * 全量覆盖部门.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/90982
     *
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function batchReplaceParty(array $params)
    {
        return $this->httpPostJson('cgi-bin/batch/replaceparty', $params);
    }

    /**
     * 获取异步任务结果.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/93169
     *
     * @param string $jobId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getResult(string $jobId)
    {
        $params = [
            'jobid' => $jobId
        ];

        return $this->httpGet('cgi-bin/batch/getresult', $params);
    }
}