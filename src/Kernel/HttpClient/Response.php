<?php

namespace EasyWeChat\Kernel\HttpClient;

use function array_key_exists;
use ArrayAccess;
use function base64_encode;
use Closure;
use EasyWeChat\Kernel\Contracts\Arrayable;
use EasyWeChat\Kernel\Contracts\Jsonable;
use EasyWeChat\Kernel\Exceptions\BadMethodCallException;
use EasyWeChat\Kernel\Exceptions\BadResponseException;
use EasyWeChat\Kernel\Support\Xml;
use function file_put_contents;
use Http\Discovery\Exception\NotFoundException;
use Http\Discovery\Psr17FactoryDiscovery;
use function json_encode;
use const JSON_UNESCAPED_UNICODE;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use function sprintf;
use function str_contains;
use function str_starts_with;
use function strtolower;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Response\StreamableInterface;
use Symfony\Component\HttpClient\Response\StreamWrapper;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

/**
 * @implements \ArrayAccess<array-key, mixed>
 *
 * @see \Symfony\Contracts\HttpClient\ResponseInterface
 */
class Response implements Jsonable, Arrayable, ArrayAccess, ResponseInterface, StreamableInterface
{
    public function __construct(
        protected ResponseInterface $response,
        protected ?Closure $failureJudge = null,
        protected bool $throw = true
    ) {
    }

    public function throw(bool $throw = true): static
    {
        $this->throw = $throw;

        return $this;
    }

    public function throwOnFailure(): static
    {
        return $this->throw(true);
    }

    public function quietly(): static
    {
        return $this->throw(false);
    }

    public function judgeFailureUsing(callable $callback): static
    {
        $this->failureJudge = $callback instanceof Closure ? $callback : fn (Response $response) => $callback($response);

        return $this;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function isSuccessful(): bool
    {
        return ! $this->isFailed();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function isFailed(): bool
    {
        if ($this->is('text') && $this->failureJudge) {
            return (bool) ($this->failureJudge)($this);
        }

        try {
            return 400 <= $this->getStatusCode();
        } catch (Throwable $e) {
            return true;
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws BadResponseException
     */
    public function toArray(?bool $throw = null): array
    {
        $throw ??= $this->throw;

        if ('' === $content = $this->response->getContent($throw)) {
            throw new BadResponseException('Response body is empty.');
        }

        $contentType = $this->getHeaderLine('content-type', $throw);

        if (str_contains($contentType, 'text/xml')
            || str_contains($contentType, 'application/xml')
            || str_starts_with($content, '<xml>')) {
            try {
                return Xml::parse($content) ?? [];
            } catch (Throwable $e) {
                throw new BadResponseException('Response body is not valid xml.', 400, $e);
            }
        }

        return $this->response->toArray($throw);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws BadResponseException
     */
    public function toJson(?bool $throw = null): string|false
    {
        return json_encode($this->toArray($throw), JSON_UNESCAPED_UNICODE);
    }

    /**
     * {@inheritdoc}
     */
    public function toStream(?bool $throw = null)
    {
        if ($this->response instanceof StreamableInterface) {
            return $this->response->toStream($throw ?? $this->throw);
        }

        if ($throw) {
            throw new BadMethodCallException(sprintf('%s does\'t implements %s', \get_class($this->response), StreamableInterface::class));
        }

        return StreamWrapper::createResource(new MockResponse());
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function toDataUrl(): string
    {
        return 'data:'.$this->getHeaderLine('content-type').';base64,'.base64_encode($this->getContent());
    }

    public function toPsrResponse(ResponseFactoryInterface $responseFactory = null, StreamFactoryInterface $streamFactory = null): \Psr\Http\Message\ResponseInterface
    {
        $streamFactory ??= $responseFactory instanceof StreamFactoryInterface ? $responseFactory : null;

        if (null === $responseFactory || null === $streamFactory) {
            if (! class_exists(Psr17Factory::class) && ! class_exists(Psr17FactoryDiscovery::class)) {
                throw new \LogicException('You cannot use the "Symfony\Component\HttpClient\Psr18Client" as no PSR-17 factories have been provided. Try running "composer require nyholm/psr7".');
            }

            try {
                $psr17Factory = class_exists(Psr17Factory::class, false) ? new Psr17Factory() : null;
                $responseFactory ??= $psr17Factory ?? Psr17FactoryDiscovery::findResponseFactory(); /** @phpstan-ignore-line */
                $streamFactory ??= $psr17Factory ?? Psr17FactoryDiscovery::findStreamFactory(); /** @phpstan-ignore-line */

                /** @phpstan-ignore-next-line */
            } catch (NotFoundException $e) {
                throw new \LogicException('You cannot use the "Symfony\Component\HttpClient\HttplugClient" as no PSR-17 factories have been found. Try running "composer require nyholm/psr7".', 0, $e);
            }
        }

        $psrResponse = $responseFactory->createResponse($this->getStatusCode());

        foreach ($this->getHeaders(false) as $name => $values) {
            foreach ($values as $value) {
                $psrResponse = $psrResponse->withAddedHeader($name, $value);
            }
        }

        $body = $this->response instanceof StreamableInterface ? $this->toStream(false) : StreamWrapper::createResource($this->response);
        $body = $streamFactory->createStreamFromResource($body);

        if ($body->isSeekable()) {
            $body->seek(0);
        }

        return $psrResponse->withBody($body);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws BadResponseException
     */
    public function saveAs(string $filename): string
    {
        try {
            file_put_contents($filename, $this->response->getContent(true));
        } catch (Throwable $e) {
            throw new BadResponseException(sprintf(
                'Cannot save response to %s: %s',
                $filename,
                $this->response->getContent(false)
            ), $e->getCode(), $e);
        }

        return '';
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws BadResponseException
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->toArray());
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws BadResponseException
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->toArray()[$offset] ?? null;
    }

    /**
     * @throws BadMethodCallException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new BadMethodCallException('Response is immutable.');
    }

    /**
     * @throws BadMethodCallException
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new BadMethodCallException('Response is immutable.');
    }

    /**
     * @param  array<array-key, mixed>  $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->response->{$name}(...$arguments);
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function getHeaders(?bool $throw = null): array
    {
        return $this->response->getHeaders($throw ?? $this->throw);
    }

    public function getContent(?bool $throw = null): string
    {
        return $this->response->getContent($throw ?? $this->throw);
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
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws BadResponseException
     */
    public function __toString(): string
    {
        return $this->toJson() ?: '';
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function hasHeader(string $name, ?bool $throw = null): bool
    {
        return isset($this->getHeaders($throw)[$name]);
    }

    /**
     * @return array<array-key, mixed>
     *
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getHeader(string $name, ?bool $throw = null): array
    {
        $name = strtolower($name);
        $throw ??= $this->throw;

        return $this->hasHeader($name, $throw) ? $this->getHeaders($throw)[$name] : [];
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getHeaderLine(string $name, ?bool $throw = null): string
    {
        $name = strtolower($name);
        $throw ??= $this->throw;

        return $this->hasHeader($name, $throw) ? implode(',', $this->getHeader($name, $throw)) : '';
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function is(string $type): bool
    {
        $contentType = $this->getHeaderLine('content-type');

        return match (strtolower($type)) {
            'json' => str_contains($contentType, '/json'),
            'xml' => str_contains($contentType, '/xml'),
            'html' => str_contains($contentType, '/html'),
            'image' => str_contains($contentType, 'image/'),
            'audio' => str_contains($contentType, 'audio/'),
            'video' => str_contains($contentType, 'video/'),
            'text' => str_contains($contentType, 'text/')
                || str_contains($contentType, '/json')
                || str_contains($contentType, '/xml'),
            default => false,
        };
    }
}
