<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Semantic;

use EasyWeChat\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * Get the semantic content of giving string.
     *
     * @param string $keyword
     * @param string $categories
     * @param array  $optional
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function query(string $keyword, string $categories, array $optional = [])
    {
        $params = [
            'query' => $keyword,
            'category' => $categories,
            'appid' => $this->app['config']['app_id'],
        ];

        return $this->httpPostJson('semantic/semproxy/search', array_merge($params, $optional));
    }
}
