<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Agent;

use EasyWeChat\Kernel\BaseClient;

/**
 * This is WeWork Agent WorkbenchClient.
 *
 * @author 读心印 <aa24615@qq.com>
 */
class WorkbenchClient extends BaseClient
{
    /**
     * 设置应用在工作台展示的模版.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/92535#设置应用在工作台展示的模版
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException|\GuzzleHttp\Exception\GuzzleException
     */
    public function setWorkbenchTemplate(array $params)
    {
        return $this->httpPostJson('cgi-bin/agent/set_workbench_template', $params);
    }

    /**
     * 获取应用在工作台展示的模版.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/92535#获取应用在工作台展示的模版
     *
     * @param int $agentId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException|\GuzzleHttp\Exception\GuzzleException
     */
    public function getWorkbenchTemplate(int $agentId)
    {
        $params = [
            'agentid' => $agentId
        ];

        return $this->httpPostJson('cgi-bin/agent/get_workbench_template', $params);
    }

    /**
     * 设置应用在用户工作台展示的数据.
     *
     * @see https://open.work.weixin.qq.com/api/doc/90000/90135/92535#设置应用在用户工作台展示的数据
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException|\GuzzleHttp\Exception\GuzzleException
     */
    public function setWorkbenchData(array $params)
    {
        return $this->httpPostJson('cgi-bin/agent/set_workbench_data', $params);
    }
}
