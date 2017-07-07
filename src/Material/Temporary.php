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
 * Temporary.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Material;

use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Support\File;

/**
 * Class Temporary.
 */
class Temporary extends AbstractAPI
{
    /**
     * Allow media type.
     *
     * @var array
     */
    protected $allowTypes = ['image', 'voice', 'video', 'thumb'];

    const API_GET = 'https://api.weixin.qq.com/cgi-bin/media/get';
    const API_UPLOAD = 'https://api.weixin.qq.com/cgi-bin/media/upload';

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

        $stream = $this->getStream($mediaId);

        $filename .= File::getStreamExt($stream);

        file_put_contents($directory.'/'.$filename, $stream);

        return $filename;
    }

    /**
     * Fetch item from WeChat server.
     *
     * @param string $mediaId
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Core\Exceptions\RuntimeException
     */
    public function getStream($mediaId)
    {
        $response = $this->getHttp()->get(self::API_GET, ['media_id' => $mediaId]);

        $response->getBody()->rewind();

        $body = $response->getBody()->getContents();

        $json = json_decode($body, true);

        if (JSON_ERROR_NONE === json_last_error()) {
            $this->checkAndThrow($json);
        }

        return $body;
    }

    /**
     * Upload temporary material.
     *
     * @param string $type
     * @param string $path
     *
     * @return \EasyWeChat\Support\Collection
     *
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function upload($type, $path)
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new InvalidArgumentException("File does not exist, or the file is unreadable: '$path'");
        }

        if (!in_array($type, $this->allowTypes, true)) {
            throw new InvalidArgumentException("Unsupported media type: '{$type}'");
        }

        return $this->parseJSON('upload', [self::API_UPLOAD, ['media' => $path], [], ['type' => $type]]);
    }

    /**
     * Upload image.
     *
     * @param $path
     *
     * @return \EasyWeChat\Support\Collection
     *
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function uploadImage($path)
    {
        return $this->upload('image', $path);
    }

    /**
     * Upload video.
     *
     * @param $path
     *
     * @return \EasyWeChat\Support\Collection
     *
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function uploadVideo($path)
    {
        return $this->upload('video', $path);
    }

    /**
     * Upload voice.
     *
     * @param $path
     *
     * @return \EasyWeChat\Support\Collection
     *
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function uploadVoice($path)
    {
        return $this->upload('voice', $path);
    }

    /**
     * Upload thumb.
     *
     * @param $path
     *
     * @return \EasyWeChat\Support\Collection
     *
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function uploadThumb($path)
    {
        return $this->upload('thumb', $path);
    }
}
