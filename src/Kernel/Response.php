<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Exceptions\RuntimeException;
use Symfony\Contracts\HttpClient\ResponseInterface as SymfonyResponseInterface;

class Response extends \Nyholm\Psr7\Response implements SymfonyResponseInterface
{
    private ?SymfonyResponseInterface $symfonyResponse = null;

    public function getHeaders(bool $throw = true): array
    {
        if ($this->symfonyResponse) {
            return $this->symfonyResponse->getHeaders($throw);
        }

        return parent::getHeaders();
    }

    public function getContent(bool $throw = true): string
    {
        if ($this->symfonyResponse) {
            return $this->symfonyResponse->getContent($throw);
        }

        return $this->getBody()->getContents();
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function toArray(bool $throw = true): array
    {
        if ($this->symfonyResponse) {
            return $this->symfonyResponse->toArray($throw);
        }

        $content =
            json_decode(
                $this->getContent(), true, 512, \JSON_BIGINT_AS_STRING | (\PHP_VERSION_ID >= 70300 ? \JSON_THROW_ON_ERROR : 0)
            );

        if (
            \PHP_VERSION_ID < 70300
            &&
            \JSON_ERROR_NONE !== json_last_error()
        ) {
            throw new RuntimeException(json_last_error_msg());
        }

        if (!\is_array($content)) {
            throw new RuntimeException(sprintf('JSON content was expected to decode to an array, "%s" returned.', get_debug_type($content)));
        }

        return $content;
    }

    public function cancel(): void
    {
        if ($this->symfonyResponse) {
            $this->symfonyResponse->cancel();
        }
    }

    public function getInfo(string $type = null): mixed
    {
        if ($this->symfonyResponse) {
            return $this->symfonyResponse->getInfo($type);
        }

        return [];
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public static function createFromSymfonyResponse(SymfonyResponseInterface $symfonyResponse): Response
    {
        $response = new self(
            $symfonyResponse->getStatusCode(),
            $symfonyResponse->getHeaders(),
            $symfonyResponse->getContent()
        );

        $response->symfonyResponse = $symfonyResponse;

        return $response;
    }
}
