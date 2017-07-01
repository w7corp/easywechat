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
 * Url.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Url;

use EasyWeChat\Core\AbstractAPI;

/**
 * Class Url.
 */
class Url extends AbstractAPI
{
    const API_SHORTEN_URL = 'https://api.weixin.qq.com/cgi-bin/shorturl';

    /**
     * Shorten the url.
     *
     * @param string $url
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function shorten($url)
    {
        $params = [
                   'action' => 'long2short',
                   'long_url' => $url,
                  ];

        return $this->parseJSON('json', [self::API_SHORTEN_URL, $params]);
    }
}
