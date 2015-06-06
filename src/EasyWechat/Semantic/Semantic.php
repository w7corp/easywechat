<?php
/**
 * Semantic.php
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Semantic;

use EasyWeChat\Support\Collection;

/**
 * 语义理解
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
     * 应用ID
     *
     * @var string
     */
    protected $appId;

    const API_SEARCH = 'https://api.weixin.qq.com/semantic/semproxy/search';

    /**
     * constructor
     *
     * <pre>
     * $config:
     *
     * array(
     *  'app_id' => YOUR_APPID,  // string mandatory;
     *  'secret' => YOUR_SECRET, // string mandatory;
     * )
     * </pre>
     *
     * @param array $config configuration array
     */
    public function __construct(array $config)
    {
        $this->appId = $config['app_id'];
        $this->http = new Http(new AccessToken($config));
    }

    /**
     * 语义理解
     *
     * @param string         $keyword
     * @param array | string $categories
     * @param array          $other
     *
     * @return Collection
     */
    public function query($keyword, $categories, array $other = array())
    {
        $params = array(
                   'query'    => $keyword,
                   'category' => implode(',', (array) $categories),
                   'appid'    => $this->appId,
                  );

        return new Collection($this->http->jsonPost(self::API_CREATE, array_merge($params, $other)));
    }
}
