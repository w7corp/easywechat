<?php

/**
 * Image.php.
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

namespace EasyWeChat\Card;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Core\Http;

/**
 * 图片上传服务
 */
class Image
{
    const API_UPLOAD = 'https://file.api.weixin.qq.com/cgi-bin/media/uploadimg';

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * Constructor.
     *
     * @param Http        $http
     */
    public function __construct(Http $http)
    {
        $this->http = $http;
    }

    /**
     * 上传媒体文件.
     *
     * @param string $path
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function upload($path)
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new InvalidArgumentException("文件不存在或不可读 '$path'");
        }

        $options = [
                    'files' => ['media' => $path],
                   ];

        $contents = $this->http->post(self::API_UPLOAD, [], $options);

        return $contents['url'];
    }
}//end class
