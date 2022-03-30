<?php

namespace EasyWeChat\Kernel\HttpClient;

use ArrayAccess;
use Closure;
use EasyWeChat\Kernel\Contracts\Arrayable;
use EasyWeChat\Kernel\Contracts\Jsonable;
use EasyWeChat\Kernel\Exceptions\BadMethodCallException;
use EasyWeChat\Kernel\Exceptions\BadResponseException;
use EasyWeChat\Kernel\Support\Xml;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @implements \ArrayAccess<array-key, mixed>
 * @see \Symfony\Contracts\HttpClient\ResponseInterface
 */
class Response implements Jsonable, Arrayable, ArrayAccess, ResponseInterface
{
    public function __construct(protected ResponseInterface $response, protected ?Closure $failureJudge = null)
    {
    }

    public function judgeFailureUsing(callable $callback): static
    {
        $this->failureJudge = $callback instanceof Closure ? $callback : fn (Response $response) => $callback($response);

        return $this;
    }

    public function isSuccessful(): bool
    {
        return !$this->isFailed();
    }

    public function isFailed(): bool
    {
        if ($this->failureJudge) {
            return (bool) ($this->failureJudge)($this);
        }

        try {
            return 400 <= $this->getStatusCode();
        } catch (\Throwable $e) {
            return true;
        }
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \EasyWeChat\Kernel\Exceptions\BadResponseException
     */
    public function toArray(bool $throw = true): array
    {
        if ('' === $content = $this->response->getContent($throw)) {
            throw new BadResponseException('Response body is empty.');
        }

        $contentType = $this->getHeaderLine('content-type', $throw);

        if (\str_contains($contentType, 'text/xml') || \str_contains($contentType, 'application/xml') || \str_starts_with($content, '<xml>')) {
            try {
                return Xml::parse($content) ?? [];
            } catch (\Throwable $e) {
                throw new BadResponseException('Response body is not valid xml.', 400, $e);
            }
        }

        return $this->response->toArray($throw);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \EasyWeChat\Kernel\Exceptions\BadResponseException
     */
    public function offsetExists(mixed $offset): bool
    {
        return \array_key_exists($offset, $this->toArray());
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \EasyWeChat\Kernel\Exceptions\BadResponseException
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->toArray()[$offset] ?? null;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\BadMethodCallException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new BadMethodCallException('Response is immutable.');
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\BadMethodCallException
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new BadMethodCallException('Response is immutable.');
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \EasyWeChat\Kernel\Exceptions\BadResponseException
     */
    public function toJson(): string|false
    {
        return \json_encode($this->toArray(), \JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param array<array-key, mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->response->{$name}(...$arguments);
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function getHeaders(bool $throw = true): array
    {
        return $this->response->getHeaders($throw);
    }

    public function getContent(bool $throw = true): string
    {
        return $this->response->getContent($throw);
    }

    public function cancel(): void
    {
        $this->response->cancel();
    }

    public function getInfo(string $type = null): mixed
    {
        return $this->response->getInfo($type);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \EasyWeChat\Kernel\Exceptions\BadResponseException
     */
    public function __toString(): string
    {
        return $this->toJson() ?: '';
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function hasHeader(string $name, bool $throw = true): bool
    {
        return isset($this->getHeaders($throw)[$name]);
    }

    /**
     * @return array<array-key, mixed>
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function getHeader(string $name, bool $throw = true): array
    {
        return $this->hasHeader($name, $throw) ? $this->getHeaders($throw)[$name] : [];
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function getHeaderLine(string $name, bool $throw = true): string
    {
        return $this->hasHeader($name, $throw) ? implode(',', $this->getHeader($name)) : '';
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \EasyWeChat\Kernel\Exceptions\BadResponseException
     */
    public function saveAs(string $filename): string
    {
        try {
            \file_put_contents($filename, $this->response->getContent(true));
        } catch (\Throwable $e) {
            throw new BadResponseException(\sprintf('Cannot save response to %s: %s', $filename, $this->response->getContent(false)), $e->getCode(), $e);
        }

        return '';
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function toDataUrl(): string
    {
        return 'data:'.$this->getHeaderLine('Content-Type').';base64,'.\base64_encode($this->getContent());
    }
}
