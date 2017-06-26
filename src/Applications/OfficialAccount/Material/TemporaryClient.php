<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OfficialAccount\Material;

use EasyWeChat\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Support\File;

/**
 * Class TemporaryClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class TemporaryClient extends BaseClient
{
    /**
     * Allow media type.
     *
     * @var array
     */
    protected $allowTypes = ['image', 'voice', 'video', 'thumb'];

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
     * @throws \EasyWeChat\Exceptions\RuntimeException
     */
    public function getStream($mediaId)
    {
        $response = $this->requestRaw('cgi-bin/media/get', 'GET', [
            'query' => [
                'media_id' => $mediaId,
            ],
        ]);

        return $response->getBody();
    }

    /**
     * Upload image.
     *
     * @param $path
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Exceptions\InvalidArgumentException
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Exceptions\InvalidArgumentException
     */
    public function uploadVideo($path)
    {
        return $this->upload('video', $path);
    }

    /**
     * @param string $path
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Exceptions\InvalidArgumentException
     */
    public function uploadVoice($path)
    {
        return $this->upload('voice', $path);
    }

    /**
     * @param $path
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Exceptions\InvalidArgumentException
     */
    public function uploadThumb($path)
    {
        return $this->upload('thumb', $path);
    }

    /**
     * Upload temporary material.
     *
     * @param string $type
     * @param string $path
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Exceptions\InvalidArgumentException
     */
    public function upload($type, $path)
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new InvalidArgumentException("File does not exist, or the file is unreadable: '$path'");
        }

        if (!in_array($type, $this->allowTypes, true)) {
            throw new InvalidArgumentException("Unsupported media type: '{$type}'");
        }

        return $this->httpUpload('cgi-bin/media/upload', ['media' => $path], ['type' => $type]);
    }
}
