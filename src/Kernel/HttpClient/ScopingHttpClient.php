<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\HttpClient;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;
use Symfony\Contracts\Service\ResetInterface;

class ScopingHttpClient implements HttpClientInterface, LoggerAwareInterface, ResetInterface
{
    use HttpClientTrait;

    private HttpClientInterface $client;

    private array $defaultOptionsByRegexp;

    public function __construct(HttpClientInterface $client, array $defaultOptionsByRegexp)
    {
        $this->client = $client;
        $this->defaultOptionsByRegexp = $defaultOptionsByRegexp;
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        foreach ($this->defaultOptionsByRegexp as $regexp => $defaultOptions) {
            if (preg_match($regexp, $url)) {
                $options = self::mergeDefaultOptions($options, $defaultOptions, true);
                break;
            }
        }

        return $this->client->request($method, $url, $options);
    }

    public function stream(ResponseInterface|iterable $responses, ?float $timeout = null): ResponseStreamInterface
    {
        return $this->client->stream($responses, $timeout);
    }

    /**
     * @return void
     */
    public function reset()
    {
        if ($this->client instanceof ResetInterface) {
            $this->client->reset();
        }
    }

    public function setLogger(LoggerInterface $logger): void
    {
        if ($this->client instanceof LoggerAwareInterface) {
            $this->client->setLogger($logger);
        }
    }
}
