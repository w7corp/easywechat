<?php

namespace EasyWeChat\MiniApp;

use EasyWeChat\Kernel\Exceptions\HttpException;

class Utils
{
    public function __construct(protected Application $app)
    {
    }

    /**
     * @throws HttpException
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

    public function decryptSession(string $sessionKey, string $iv, string $ciphertext): array
    {
        return Decryptor::decrypt($sessionKey, $iv, $ciphertext);
    }

    /**
     * @throws HttpException
     */
    public function getPhoneNumber(string $code): array
    {
        $response = $this->app->createClient()->request('POST', '/wxa/business/getuserphonenumber', [
            'json' => [
                'code' => $code,
            ],
        ])->toArray(false);

        if (isset($response['errcode']) && $response['errcode'] !== 0) {
            throw new HttpException('getPhoneNumber error: '.json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        if (empty($response['phone_info'])) {
            throw new HttpException('getPhoneNumber error: '.json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        return $response;
    }
}
