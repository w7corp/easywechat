<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\Kernel\Traits\InteractWithCache;
use EasyWeChat\Kernel\Traits\InteractWithHttpClient;
use EasyWeChat\OpenPlatform\Contracts\VerifyTicket as VerifyTicketInterface;
use JetBrains\PhpStorm\ArrayShape;

class ComponentAccessToken implements AccessTokenInterface
{
    use InteractWithCache;
    use InteractWithHttpClient;

    public function __construct(
        protected string $appId,
        protected string $secret,
        protected VerifyTicketInterface $verifyTicket,
        protected ?string $key = null,
    ) {
    }

    public function getKey(): string
    {
        return $this->key ?? $this->key = \sprintf('open_platform.component_access_token.%s', $this->appId);
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getToken(): string
    {
        $key = $this->getKey();

        if ($token = $this->getCache()->get($key)) {
            return $token;
        }

        $response = $this->getHttpClient()->request(
            'POST',
            'cgi-bin/component/api_component_token',
            [
                'json' => [
                    'component_appid' => $this->appId,
                    'component_appsecret' => $this->secret,
                    'component_verify_ticket' => $this->verifyTicket->getTicket(),
                ],
            ]
        )->toArray(false);

        if (empty($response['component_access_token'])) {
            throw new HttpException('Failed to get component_access_token: '.\json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        $this->getCache()->set($key, $response['component_access_token'], \abs(\intval($response['expires_in']) - 100));

        return $response['component_access_token'];
    }


    /**
     * @return array<string, string>
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    #[ArrayShape(['component_access_token' => "string"])]
    public function toQuery(): array
    {
        return ['component_access_token' => $this->getToken()];
    }
}
