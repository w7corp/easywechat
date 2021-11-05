<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Message;
use EasyWeChat\Kernel\Support\Xml;
use Nyholm\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait InteractWithXmlMessage
{
    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException|\EasyWeChat\Kernel\Exceptions\BadRequestException|\Throwable
     */
    public function serve(): ResponseInterface
    {
        if (!!($str = $this->request->getQueryParams()['echostr'] ?? '')) {
            return $this->handleUrlValidate($this->request, $str);
        }

        $this->withMessageValidationHandler();

        $message = $this->createMessageFromRequest();

        $response = $this->handle($this->defaultResponse(), $message);

        if ($response instanceof ResponseInterface) {
            return $response;
        }

        return $this->resolveResponse($response, $message);
    }

    /**
     * @throws \Throwable
     */
    public function withMessageValidationHandler(): static
    {
        return $this->withHandler(
            function (\EasyWeChat\Kernel\Message $message, \Closure $next) {
                $query = $this->request->getQueryParams();
                $signature = $query['signature'] ?? $query['msg_signature'] ?? null;

                if (!isset($signature)) {
                    throw new BadRequestException('Invalid request signature.');
                }
                $attributes = $message->format($message->getOriginalContents());
                $params = [$this->account->getToken(), $query['timestamp'], $query['nonce'], $attributes['Encrypt'] ?? ''];

                sort($params, SORT_STRING);

                if ($signature !== sha1(implode($params))) {
                    throw new BadRequestException('Invalid request signature.');
                }

                return $next($message);
            }
        );
    }

    public function transformResponse(array $response, Message $message): ResponseInterface
    {
        $currentTime = \time();

        return $this->createXmlResponse(
            attributes: array_filter(
                \array_merge(
                    [
                        'ToUserName' => $message->FromUserName,
                        'FromUserName' => $message->ToUserName,
                        'CreateTime' => $currentTime,
                    ],
                    $response
                )
            ),
            encryptor: $this->encryptor
        );
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    protected function normalizeResponse(mixed $response): array
    {
        if (\is_array($response)) {
            if (!isset($response['MsgType'])) {
                throw new InvalidArgumentException('MsgType cannot be empty.');
            }

            return $response;
        }

        if (is_string($response) || is_numeric($response)) {
            return [
                'MsgType' => 'text',
                'Content' => $response,
            ];
        }

        throw new InvalidArgumentException(
            sprintf('Invalid Response type "%s".', gettype($response))
        );
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    protected function createXmlResponse(array $attributes, ?Encryptor $encryptor = null): ResponseInterface
    {
        $xml = Xml::build($attributes);

        if ($encryptor) {
            $time = $attributes['CreateTime'] ?? \time();
            $nonce = $attributes['nonce'] ?? \uniqid();

            $xml = $encryptor->encrypt($xml, $nonce, $time);
        }

        return new Response(200, ['Content-Type' => 'application/xml'], $xml);
    }

    protected function getEncryptor(RequestInterface $request)
    {
        return $this->encryptor;
    }

    /**
     * @return mixed
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    protected function createMessageFromRequest(): mixed
    {
        $messageClass = \sprintf('%s\Message', (new \ReflectionClass($this))->getNamespaceName());

        if (!\class_exists($messageClass)) {
            throw new RuntimeException($messageClass . ' not found.');
        }

        $message = \call_user_func_array(
            [$messageClass, 'createFromRequest',],
            [
                $this->request,
                $this->getEncryptor($this->request),
            ]
        );

        return $message;
    }

    protected function handleUrlValidate(RequestInterface $request, mixed $str): Response
    {
        return new Response(200, [], $str);
    }

    /**
     * @param mixed $response
     * @param mixed $message
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    protected function resolveResponse(mixed $response, mixed $message): ResponseInterface
    {
        $response = $this->transformResponse($this->normalizeResponse($response), $message);

        $response->getBody()->rewind();

        return $response;
    }

    public function defaultResponse()
    {
        return new Response(200, [], 'SUCCESS');
    }
}
