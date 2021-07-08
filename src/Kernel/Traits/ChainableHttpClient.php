<?php

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Support\Str;

trait ChainableHttpClient
{
    protected string $uri = '/';

    public function withUri(string $uri): static
    {
        $clone = clone $this;

        if (\str_starts_with($uri, 'http://') || \str_starts_with($uri, 'https://')) {
            $clone->uri = $uri;
        } else {
            $uri = \preg_match('~\$[a-z0-9_]+~i', $uri) ? $uri : Str::kebab($uri);
            $clone->uri = \trim(\sprintf('/%s/%s', \trim($this->uri, '/'), \trim($uri, '/')), '/');
        }

        return $clone;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function __get(string | int $name)
    {
        return $this->withUri(\strval($name));
    }

    public function __call(string $name, array $arguments)
    {
        if (\in_array(\strtoupper($name), ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $this->callWithShortcuts(\strtoupper($name), ...$arguments);
        }

        return \call_user_func_array([$this->client, $name], $arguments);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    protected function callWithShortcuts(
        string $method,
        string | array $uri = [],
        array $options = []
    ): \Symfony\Contracts\HttpClient\ResponseInterface {
        if (\is_string($uri)) {
            $uri = $this->withUri($uri)->getUri();
        } else {
            $options = $uri;
            $uri = $this->getUri();
        }

        [$uri, $options] = $this->replaceUriVariables($uri, $options);

        return $this->request(\strtoupper($method), $uri, $options);
    }

    public function replaceUriVariables(string $uri, array $options): array
    {
        return [
            \preg_replace_callback(
                pattern: '~\$(?<name>[a-z0-9_]+)~i',
                callback: function ($matches) use (&$options) {
                    if (empty($options[$matches['name']])) {
                        throw new InvalidArgumentException(\sprintf('Missing url variables "%s".', $matches['name']));
                    }

                    $value = $options[$matches['name']];

                    unset($options[$matches['name']]);

                    return $value;
                },
                subject: $uri
            ),
            $options,
        ];
    }
}
