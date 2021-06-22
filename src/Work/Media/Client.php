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
     * @return array|\EasyWeChat\Kernel\Http\Response|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $mediaId)
    {
        return $this->getResources($mediaId, 'cgi-bin/media/get');
    }

    /**
     * Upload Image.
     *
     * @param string $path
     * @param array $form
     *
     * @return mixed
     */
    public function uploadImage(string $path, array $form = [])
    {
        return $this->upload('image', $path, $form);
    }

    /**
     * Upload Voice.
     *
     * @param string $path
     * @param array $form
     *
     * @return mixed
     */
    public function uploadVoice(string $path, array $form = [])
    {
        return $this->upload('voice', $path, $form);
    }

    /**
     * Upload Video.
     *
     * @param string $path
     * @param array $form
     *
     * @return mixed
     */
    public function uploadVideo(string $path, array $form = [])
    {
        return $this->upload('video', $path, $form);
    }

    /**
     * Upload File.
     *
     * @param string $path
     * @param array $form
     *
     * @return mixed
     */
    public function uploadFile(string $path, array $form = [])
    {
        return $this->upload('file', $path, $form);
    }

    /**
     * Upload media.
     *
     * @param string $type
     * @param string $path
     * @param array $form
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function upload(string $type, string $path, array $form = [])
    {
        $files = [
            'media' => $path,
        ];

        return $this->httpUpload('cgi-bin/media/upload', $files, $form, compact('type'));
    }

    /**
     * Upload permanently valid images
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/90256
     * @param string $path
     * @param array $form
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadImg(string $path, array $form = [])
    {
        $files = [
            'media' => $path,
        ];

        return $this->httpUpload('cgi-bin/media/uploadimg', $files, $form);
    }


    /**
     * Get HD voice material
     *
     * @see https://work.weixin.qq.com/api/doc/90000/90135/90255
     * @param string $mediaId
     *
     * @return array|\EasyWeChat\Kernel\Http\Response|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getHdVoice(string $mediaId)
    {
        return $this->getResources($mediaId, 'cgi-bin/media/get/jssdk');
    }

    /**
     * @param string $mediaId
     * @param string $uri
     *
     * @return array|\EasyWeChat\Kernel\Http\Response|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getResources(string $mediaId, string $uri)
    {
        $response = $this->requestRaw($uri, 'GET', [
            'query' => [
                'media_id' => $mediaId,
            ],
        ]);

        if (false !== stripos($response->getHeaderLine('Content-Type'), 'text/plain')) {
            return $this->castResponseToType($response, $this->app['config']->get('response_type'));
        }

        return StreamResponse::buildFromPsrResponse($response);
    }
}
