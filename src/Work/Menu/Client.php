<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Menu;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Get menu.
     *
     * @return mixed
     */
    public function get()
    {
        return $this->httpGet('cgi-bin/menu/get', ['agentid' => $this->app['config']['agent_id']]);
    }

    /**
     * Create menu for the given agent.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->httpPostJson('cgi-bin/menu/create', $data, ['agentid' => $this->app['config']['agent_id']]);
    }

    /**
     * Delete menu.
     *
     * @return mixed
     */
    public function delete()
    {
        return $this->httpGet('cgi-bin/menu/delete', ['agentid' => $this->app['config']['agent_id']]);
    }
}
