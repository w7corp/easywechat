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
 * Semantic.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Bag;

/**
 * 语义理解.
 */
class Semantic
{
    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * 应用ID.
     *
     * @var string
     */
    protected $appId;

    const API_SEARCH = 'https://api.weixin.qq.com/semantic/semproxy/search';

    /**
     * constructor.
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }

    /**
     * 语义理解.
     *
     * @param string         $keyword
     * @param array | string $categories
     * @param array          $other
     *
     * @return Bag
     */
    public function query($keyword, $categories, array $other = array())
    {
        $params = array(
                   'query' => $keyword,
                   'category' => implode(',', (array) $categories),
                   'appid' => $this->appId,
                  );

        return new Bag($this->http->jsonPost(self::API_SEARCH, array_merge($params, $other)));
    }
}
