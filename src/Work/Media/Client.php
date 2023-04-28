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
     * @return array|\EasyWeChat\Kernel\Http\Response|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     * @return mixed
     */
    public function uploadImage(string $path)
    {
        return $this->upload('image', $path);
    }

    /**
     * Upload Voice.
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
     * @return mixed
     */
    public function uploadVideo(string $path)
    {
        return $this->upload('video', $path);
    }

    /**
     * Upload File.
     *
     * @return mixed
     */
    public function uploadFile(string $path)
    {
        return $this->upload('file', $path);
    }

    /**
     * Upload Attachment Resources
     *
     * @return mixed
     */
    public function uploadAttachmentResources(string $path, string $mediaType = 'image', int $attachmentType = 1)
    {
        return $this->uploadAttachment($path, $mediaType, $attachmentType);
    }

    /**
     * Upload media.
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function upload(string $type, string $path)
    {
        $files = [
            'media' => $path,
        ];

        return $this->httpUpload('cgi-bin/media/upload', $files, [], compact('type'));
    }

    /**
     * Upload media
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadAttachment(string $path, string $mediaType, int $attachmentType)
    {
        $files = [
            'media' => $path,
        ];

        $query = [
            'media_type' => $mediaType,
            'attachment_type' => $attachmentType,
        ];

        return $this->httpUpload('cgi-bin/media/upload_attachment', $files, [], $query);
    }
}
