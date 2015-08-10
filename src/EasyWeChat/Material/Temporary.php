<?php

/**
 * Temporary.php.
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

namespace EasyWeChat\Material;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Core\Http;
use EasyWeChat\Support\File;

/**
 * Class Temporary.
 */
class Temporary
{
    /**
     * Http client.
     *
     * @var Http
     */
    protected $http;

    /**
     * Allow media type.
     *
     * @var array
     */
    protected $allowTypes = ['image', 'voice', 'video', 'thumb'];

    const API_GET = 'https://api.weixin.qq.com/cgi-bin/media/get';
    const API_UPLOAD = 'https://api.weixin.qq.com/cgi-bin/media/upload';

    /**
     * Constructor.
     *
     * @param Http $http
     */
    public function __construct(Http $http)
    {
        $this->http = $http->setExpectedException('EasyWeChat\Material\MaterialHttpException');
    }

    /**
     * Download temporary material.
     *
     * @param string $mediaId
     * @param string $directory
     * @param string $filename
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function download($mediaId, $directory, $filename = '')
    {
        if (!is_dir($directory) || !is_writable($directory)) {
            throw new InvalidArgumentException("Directory does not exist or is not writable: '$directory'.");
        }

        $filename = $filename ?: $mediaId;

        $stream = $this->http->get(self::API_GET, ['media_id' => $mediaId]);

        $ext = File::getStreamExt($stream);

        file_put_contents($directory.'/'.$filename.'.'.$ext, $stream);

        return $filename.'.'.$ext;
    }

    /**
     * Upload temporary material.
     *
     * @param string $type
     * @param string $path
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function upload($type, $path)
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new InvalidArgumentException("File does not exist, or the file is unreadable: '$path'");
        }

        if (!in_array($type, $this->allowTypes, true)) {
            throw new InvalidArgumentException("Unsupported media type: '{$type}'");
        }

        return $this->http->upload(self::API_UPLOAD, ['media' => $path]);
    }

    /**
     * Magic access.
     *
     * <pre>
     * $media->uploadImage($path); // $media->upload('image', $path);
     * </pre>
     *
     * @param string $method
     * @param array  $args
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function __call($method, $args)
    {
        if (0 !== stripos($method, 'upload')) {
            throw new InvalidArgumentException("Undefined method '$method'.");
        }

        $args = [substr($method, strlen('upload')), array_shift($args)];

        return call_user_func_array([__CLASS__, 'upload'], $args);
    }
}
