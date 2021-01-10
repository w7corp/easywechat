<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Utils;
use Psr\Http\Message\ResponseInterface;

//TODO: 重新设计
trait HasHttpRequests
{
    use ResponseCastable;

    protected ClientInterface $httpClient;
    protected array $middlewares = [];
    protected HandlerStack $handlerStack;
    protected static array $defaults = [
        'curl' => [
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ],
    ];

    public static function setDefaultOptions($defaults = [])
    {
        self::$defaults = $defaults;
    }

    public static function getDefaultOptions(): array
    {
        return self::$defaults;
    }

    public function setHttpClient(ClientInterface $httpClient): static
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    public function getHttpClient(): ClientInterface
    {
        if (!($this->httpClient instanceof ClientInterface)) {
            if (property_exists($this, 'app') && $this->app['http_client']) {
                $this->httpClient = $this->app['http_client'];
            } else {
                $this->httpClient = new Client(['handler' => HandlerStack::create($this->getGuzzleHandler())]);
            }
        }

        return $this->httpClient;
    }

    public function pushMiddleware(callable $middleware, string $name = null): static
    {
        if (!is_null($name)) {
            $this->middlewares[$name] = $middleware;
        } else {
            array_push($this->middlewares, $middleware);
        }

        return $this;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function request($url, $method = 'GET', $options = []): ResponseInterface
    {
        $method = strtoupper($method);

        $options = array_merge(self::$defaults, $options, ['handler' => $this->getHandlerStack()]);

        $options = $this->fixJsonIssue($options);

        if (property_exists($this, 'baseUri') && !is_null($this->baseUri)) {
            $options['base_uri'] = $this->baseUri;
        }

        $response = $this->getHttpClient()->request($method, $url, $options);
        $response->getBody()->rewind();

        return $response;
    }

    public function setHandlerStack(HandlerStack $handlerStack): static
    {
        $this->handlerStack = $handlerStack;

        return $this;
    }

    public function getHandlerStack(): HandlerStack
    {
        if ($this->handlerStack) {
            return $this->handlerStack;
        }

        $this->handlerStack = HandlerStack::create($this->getGuzzleHandler());

        foreach ($this->middlewares as $name => $middleware) {
            $this->handlerStack->push($middleware, $name);
        }

        return $this->handlerStack;
    }

    protected function fixJsonIssue(array $options): array
    {
        if (isset($options['json']) && is_array($options['json'])) {
            $options['headers'] = array_merge($options['headers'] ?? [], ['Content-Type' => 'application/json']);

            if (empty($options['json'])) {
                $options['body'] = \GuzzleHttp\json_encode($options['json'], JSON_FORCE_OBJECT);
            } else {
                $options['body'] = \GuzzleHttp\json_encode($options['json'], JSON_UNESCAPED_UNICODE);
            }

            unset($options['json']);
        }

        return $options;
    }

    protected function getGuzzleHandler(): mixed
    {
        if (property_exists($this, 'app') && isset($this->app['guzzle_handler'])) {
            return is_string($handler = $this->app->raw('guzzle_handler'))
                        ? new $handler()
                        : $handler;
        }

        return Utils::chooseHandler();
    }
}
