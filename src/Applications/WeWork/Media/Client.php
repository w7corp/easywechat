<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\WeWork\Media;

use EasyWeChat\Support\HasHttpRequests;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client
{
    use HasHttpRequests {
        get as httpGet;
    }

    /**
     * Get media.
     *
     * @param string $mediaId
     *
     * @return mixed
     */
    public function get($mediaId)
    {
        $this->parseJSON($this->httpGet('https://qyapi.weixin.qq.com/cgi-bin/media/get', ['media_id' => $mediaId]));
    }

    /**
     * Upload Image.
     *
     * @param string $path
     *
     * @return mixed
     */
    public function uploadImage(string $path)
    {
        return $this->uploadMedia('image', $path);
    }

    /**
     * Upload Voice.
     *
     * @param string $path
     *
     * @return mixed
     */
    public function uploadVoice(string $path)
    {
        return $this->uploadMedia('voice', $path);
    }

    /**
     * Upload Video.
     *
     * @param string $path
     *
     * @return mixed
     */
    public function uploadVideo(string $path)
    {
        return $this->uploadMedia('video', $path);
    }

    /**
     * Upload File.
     *
     * @param string $path
     *
     * @return mixed
     */
    public function uploadFile(string $path)
    {
        return $this->uploadMedia('file', $path);
    }

    /**
     * Upload media.
     *
     * @param string $type
     * @param mixed  $media
     *
     * @return mixed
     */
    protected function uploadMedia(string $type, string $path)
    {
        $files = [
            'media' => $path,
        ];

        return $this->parseJSON(
            $this->upload('https://qyapi.weixin.qq.com/cgi-bin/media/upload', $files, [], compact('type'))
        );
    }
}
