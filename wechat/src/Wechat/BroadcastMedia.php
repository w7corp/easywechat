<?php
/**
 * BroadcastMedia.php
 *
 * Part of LGC119\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace MasApi\Wechat;

use MasApi\Wechat\Utils\JSON;
use MasApi\Wechat\Utils\Bag;

/**
 * 媒体素材
 *
 * @method string news($path)
 * @method string video($path)
 */
class BroadcastMedia
{
    const API_NEWS_UPLOAD    = 'https://api.weixin.qq.com/cgi-bin/media/uploadnews';
    const API_VIDEO_UPLOAD      = 'https://file.api.weixin.qq.com/cgi-bin/media/uploadvideo';
    /**
     * 允许上传的类型
     *
     * @var array
     */
    protected $allowTypes = array(
                             'news',
                             'video',
                            );

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }

    /**
     * 上传视频
     *
     * 有点不一样。。。
     *
     * @param string $path
     * @param string $title
     * @param string $description
     *
     * @return string
     */
    public function video($params)
    {
        $response = $this->http->jsonPost(self::API_VIDEO_UPLOAD, $params);

        return $response['media_id'];
    }

    /**
     * 新增图文素材
     *
     * @param array $articles
     *
     * @return string
     */
    public function news(array $articles)
    {
        $params = array('articles' => $articles);

        $response = $this->http->jsonPost(self::API_NEWS_UPLOAD, $params);

        return $response['media_id'];
    }

    /**
     * 魔术调用
     *
     * <pre>
     * $media->image($path); // $media->upload('image', $path);
     * </pre>
     *
     * @param string $method
     * @param array  $args
     *
     * @return string
     */
    public function __call($method, $args)
    {
        throw new Exception('请求的方法不存在！');
    }

}
