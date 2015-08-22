<?php

/**
 * Url.php.
 *
 * Part of EasyWeChat.
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

namespace EasyWeChat\Url;

use EasyWeChat\Core\Http;

/**
 * Class Url.
 */
class Url
{
    /**
     * Http client.
     *
     * @var Http
     */
    protected $http;

    const API_SHORTEN_URL = 'https://api.weixin.qq.com/cgi-bin/shorturl';

    /**
     * Constructor.
     *
     * @param Http $http
     */
    public function __construct(Http $http)
    {
        $this->http = $http->setExpectedException(UrlHttpException::class);
    }

    /**
     * Shorten the url.
     *
     * @param string $url
     *
     * @return string
     */
    public function shorten($url)
    {
        $params = [
                   'action' => 'long2short',
                   'long_url' => $url,
                  ];

        $response = $this->http->json(self::API_SHORTEN_URL, $params);

        return $response['short_url'];
    }
}
