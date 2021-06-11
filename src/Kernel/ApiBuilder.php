<?php

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Support\Str;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @method \Symfony\Contracts\HttpClient\ResponseInterface get(string|array $uri = [], array $options = [])
 * @method \Symfony\Contracts\HttpClient\ResponseInterface post(string|array $uri = [], array $options = [])
 * @method \Symfony\Contracts\HttpClient\ResponseInterface patch(string|array $uri = [], array $options = [])
 * @method \Symfony\Contracts\HttpClient\ResponseInterface put(string|array $uri = [], array $options = [])
 * @method \Symfony\Contracts\HttpClient\ResponseInterface delete(string|array $uri = [], array $options = [])
 * @method \Symfony\Contracts\HttpClient\ResponseInterface request(string $method, string $url, array $options = [])
 */
class ApiBuilder implements HttpClientInterface
{
    use DecoratorTrait;

    public function __construct(HttpClientInterface $client, protected string $uri = '/')
    {
        $this->client = $client;
    }

    public function append(string $segment): ApiBuilder
    {
        if (\str_starts_with($segment, 'http://') || \str_starts_with($segment, 'https://')) {
            return new ApiBuilder($this->client, $segment);
        }

        $segment = Str::kebab($segment);

        $uri = \rtrim(\sprintf('/%s/%s', \trim($this->uri, '/'), \trim($segment, '/')), '/');

        return new ApiBuilder($this->client, $uri);
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function __get($name)
    {
        return $this->append($name);
    }

    public function __call(string $name, array $arguments)
    {
        if (\in_array(\strtoupper($name), ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $this->callWithShortcuts(\strtoupper($name), ...$arguments);
        }

        return \call_user_func_array([$this->client, $name], $arguments);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function callWithShortcuts(string $method, string | array $uri = [], array $options = []): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        if (\is_string($uri)) {
            $uri = $this->append($uri)->getUri();
        } else {
            $options = $uri;
            $uri = $this->getUri();
        }

        return $this->client->request(\strtoupper($method), $uri, $options);
    }
}
