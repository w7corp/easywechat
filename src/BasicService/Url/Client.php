<?php

declare(strict_types=1);

namespace EasyWeChat\BasicService\Url;

use EasyWeChat\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @var string
     */
    protected string  $baseUri = 'https://api.weixin.qq.com/';

    /**
     * Shorten the url.
     *
     * @param string $url
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function shorten(string $url)
    {
        $params = [
            'action' => 'long2short',
            'long_url' => $url,
        ];

        return $this->httpPostJson('cgi-bin/shorturl', $params);
    }
}
