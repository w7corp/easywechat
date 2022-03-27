<?php

namespace EasyWeChat\MiniApp;

use EasyWeChat\Kernel\Exceptions\HttpException;

class Utils
{
    public function __construct(protected Application $app)
    {
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
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
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     */
    public function decryptSession(string $sessionKey, string $iv, string $ciphertext): array
    {
        return Decryptor::decrypt($sessionKey, $iv, $ciphertext);
    }
}
