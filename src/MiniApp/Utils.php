<?php

namespace EasyWeChat\MiniApp;

use EasyWeChat\Kernel\Exceptions\DecryptException;
use EasyWeChat\Kernel\Exceptions\HttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Utils
{
    public function __construct(protected Application $app)
    {
    }

    /**
     * @throws HttpException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function codeToSession(string $code): array
    {
        $response = $this->app->getHttpClient()->request('GET', '/sns/jscode2session', [
            'query' => [
                'appid' => $this->app->getAccount()->getAppId(),
                'secret' => $this->app->getAccount()->getSecret(),
                'js_code' => $code,
                'grant_type' => 'authorization_code',
            ],
        ])->toArray(false);

        if (empty($response['openid'])) {
            throw new HttpException('code2Session error: '.json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        return $response;
    }

    /**
     * @throws DecryptException
     */
    public function decryptSession(string $sessionKey, string $iv, string $ciphertext): array
    {
        return Decryptor::decrypt($sessionKey, $iv, $ciphertext);
    }
}
