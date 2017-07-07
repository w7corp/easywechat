<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\WeWork\Menu;

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
     * @param int $agentId
     *
     * @return mixed
     */
    public function get($agentId)
    {
        return $this->httpGet('menu/get', ['agentid' => $agentId]);
    }

    /**
     * Create menu for the given agent.
     *
     * @param int   $agentId
     * @param array $data
     *
     * @return mixed
     */
    public function create($agentId, array $data)
    {
        return $this->httpPostJson('menu/create', $data, ['agentid' => $agentId]);
    }

    /**
     * Delete menu.
     *
     * @param int $agentId
     *
     * @return mixed
     */
    public function delete($agentId)
    {
        return $this->httpGet('menu/delete', ['agentid' => $agentId]);
    }
}
