<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\WeWork\Menu;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client
{
    use HasHttpRequests {
        get as httpGet;
    }

    /**
     * Get menu.
     *
     * @param int $agentId
     *
     * @return mixed
     */
    public function get($agentId)
    {
        return $this->parseJSON(
            $this->httpGet('https://qyapi.weixin.qq.com/cgi-bin/menu/get', ['agentid' => $agentId])
        );
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
        return $this->parseJSON(
            $this->postJson('https://qyapi.weixin.qq.com/cgi-bin/menu/create', $data, JSON_UNESCAPED_UNICODE, ['agentid' => $agentId])
        );
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
        return $this->parseJSON(
            $this->httpGet('https://qyapi.weixin.qq.com/cgi-bin/menu/delete', ['agentid' => $agentId])
        );
    }
}
