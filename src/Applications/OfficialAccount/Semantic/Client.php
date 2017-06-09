<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Application Semantic Client.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Applications\OfficialAccount\Semantic;

use EasyWeChat\Applications\Base\Core\AbstractAPI;

class Client extends AbstractAPI
{
    const API_SEARCH = 'https://api.weixin.qq.com/semantic/semproxy/search';

    /**
     * Get the semantic content of giving string.
     *
     * @param string       $keyword
     * @param array|string $categories
     * @param array        $other
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function query($keyword, $categories, array $other = [])
    {
        $params = [
                   'query' => $keyword,
                   'category' => implode(',', (array) $categories),
                   'appid' => $this->getAccessToken()->getClientId(),
                  ];

        return $this->parseJSON('json', [self::API_SEARCH, array_merge($params, $other)]);
    }
}
