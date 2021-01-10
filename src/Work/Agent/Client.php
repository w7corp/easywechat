<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Agent;

use EasyWeChat\Kernel\BaseClient;

/**
 * This is WeWork Agent Client.
 *
 */
class Client extends BaseClient
{
    /**
     * Get agent.
     *
     * @param int $agentId
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function get(int $agentId)
    {
        $params = [
            'agentid' => $agentId,
        ];

        return $this->httpGet('cgi-bin/agent/get', $params);
    }

    /**
     * Set agent.
     *
     * @param int   $agentId
     * @param array $attributes
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function set(int $agentId, array $attributes)
    {
        return $this->httpPostJson('cgi-bin/agent/set', array_merge(['agentid' => $agentId], $attributes));
    }

    /**
     * Get agent list.
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function list()
    {
        return $this->httpGet('cgi-bin/agent/list');
    }
}
