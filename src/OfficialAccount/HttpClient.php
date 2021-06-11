<?php

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\Kernel\Support\UserAgent;
use EasyWeChat\OfficialAccount\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\OfficialAccount\Contracts\Account as AccountInterface;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class HttpClient implements HttpClientInterface
{
    use HttpClientTrait;

    protected HttpClientInterface $client;

    protected array $defaultOptions = [];

    public function __construct(
        protected ?AccessTokenInterface $accessToken = null,
        ?HttpClientInterface $client = null,
        ?array $defaultOptions = []
    ) {
        $this->client = $client ?? SymfonyHttpClient::create();

        $defaultOptions = \array_merge(
            self::OPTIONS_DEFAULTS,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]
        );

        [, $this->defaultOptions] = self::prepareRequest(null, null, $defaultOptions, $this->defaultOptions);
    }

    public function withAccessToken(AccessTokenInterface $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $options['headers']['User-Agent'] = UserAgent::create([$options['headers']['User-Agent'] ?? '']);

        if ($this->accessToken) {
            $options['query'] = \array_merge($options['query'] ?? [], ['access_token' => $this->accessToken->getToken()]);
        }

        return $this->client->request($method, $url, $options);
    }

    public function __call(string $name, array $arguments)
    {
        return \call_user_func_array([$this->client, $name], $arguments);
    }

    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        return $this->client->stream($responses, $timeout);
    }
}
