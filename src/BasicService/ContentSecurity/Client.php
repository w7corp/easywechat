<?php

declare(strict_types=1);

namespace EasyWeChat\BasicService\ContentSecurity;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

class Client extends BaseClient
{
    /**
     * @var string
     */
    protected string  $baseUri = 'https://api.weixin.qq.com/wxa/';

    /**
     * Text content security check.
     *
     * @param string $text
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkText(string $text)
    {
        $params = [
            'content' => $text,
        ];

        return $this->httpPostJson('msg_sec_check', $params);
    }

    /**
     * Image security check.
     *
     * @param string $path
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkImage(string $path)
    {
        return $this->httpUpload('img_sec_check', ['media' => $path]);
    }

    /**
     * Media security check.
     *
     * @param string $mediaUrl
     * @param int    $mediaType
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function checkMediaAsync(string $mediaUrl, int $mediaType)
    {
        /*
         * 1:音频;2:图片
         */
        $mediaTypes = [1, 2];

        if (!in_array($mediaType, $mediaTypes, true)) {
            throw new InvalidArgumentException('media type must be 1 or 2');
        }

        $params = [
            'media_url' => $mediaUrl,
            'media_type' => $mediaType,
        ];

        return $this->httpPostJson('media_check_async', $params);
    }

    /**
     * Image security check async.
     *
     * @param string $mediaUrl
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkImageAsync(string $mediaUrl)
    {
        return $this->checkMediaAsync($mediaUrl, 2);
    }

    /**
     * Audio security check async.
     *
     * @param string $mediaUrl
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkAudioAsync(string $mediaUrl)
    {
        return $this->checkMediaAsync($mediaUrl, 1);
    }
}
