<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\WeWork\Agent;

use EasyWeChat\Kernel\BaseClient;

/**
 * This is WeWork Agent Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Get agent.
     *
     * @return mixed
     */
    public function get()
    {
        $params = [
            'agentid' => $this->app['config']['agent_id'],
        ];

        return $this->httpGet('agent/get', $params);
    }

    /**
     * Set agent.
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function set(array $attributes)
    {
        return $this->httpPostJson('agent/set', array_merge(['agentid' => $this->app['config']['agent_id']], $attributes));
    }

    /**
     * Get agent list.
     *
     * @return mixed
     */
    public function list()
    {
        return $this->httpGet('agent/list');
    }
}
