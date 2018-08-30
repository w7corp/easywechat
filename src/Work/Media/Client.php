<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Media;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Http\StreamResponse;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Get media.
     *
     * @param string $mediaId
     *
     * @return mixed
     */
    public function get(string $mediaId)
    {
        $response = $this->requestRaw('cgi-bin/media/get', 'GET', [
            'query' => [
                'media_id' => $mediaId,
            ],
        ]);

        if (false !== stripos($response->getHeaderLine('Content-Type'), 'text/plain')) {
            return $this->castResponseToType($response, $this->app['config']->get('response_type'));
        }

        return StreamResponse::buildFromPsrResponse($response);
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
        return $this->upload('image', $path);
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
        return $this->upload('voice', $path);
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
        return $this->upload('video', $path);
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
        return $this->upload('file', $path);
    }

    /**
     * Upload media.
     *
     * @param string $type
     * @param string $path
     *
     * @return mixed
     */
    public function upload(string $type, string $path)
    {
        $files = [
            'media' => $path,
        ];

        return $this->httpUpload('cgi-bin/media/upload', $files, [], compact('type'));
    }
}
