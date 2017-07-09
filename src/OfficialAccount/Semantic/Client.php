<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\Semantic;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
    /**
     * Get the semantic content of giving string.
     *
     * @param string       $keyword
     * @param array|string $categories
     * @param array        $other
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function query($keyword, $categories, array $other = [])
    {
        $params = [
            'query' => $keyword,
            'category' => implode(',', (array) $categories),
            'appid' => $this->app['config']['app_id'],
        ];

        return $this->httpPostJson('semantic/semproxy/search', array_merge($params, $other));
    }
}
