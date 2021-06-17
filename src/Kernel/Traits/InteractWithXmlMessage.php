<?php

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Config;
use EasyWeChat\Kernel\Contracts\Config as ConfigInterface;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Response;
use EasyWeChat\OfficialAccount\Contracts\Message as MessageInterface;
use EasyWeChat\OfficialAccount\Message;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

trait InteractWithXmlMessage
{
    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException|\EasyWeChat\Kernel\Exceptions\BadRequestException|\Throwable
     */
    public function process(): ResponseInterface
    {
        if (!!($str = $this->request->getQueryParams()['echostr'] ?? '')) {
            return new Response(200, [], $str);
        }

        $this->withMessageValidationHandler();

        $message = Message::createFromRequest($this->request, $this->encryptor);

        $response = $this->handle(Response::success(), $message);

        if ($response instanceof ResponseInterface) {
            return $response;
        }

        return $this->transformResponse($response, $message);
    }

    /**
     * @throws \Throwable
     */
    public function withMessageValidationHandler(): static
    {
        return $this->withHandler(
            function ($message, \Closure $next) {
                $query = $this->request->getQueryParams();

                if (!isset($query['signature']) || 'aes' !== ($query['encrypt_type'] ?? '')) {
                    return $next($message);
                }

                $params = [$this->account->getToken(), $query['timestamp'], $query['nonce']];

                sort($params, SORT_STRING);

                if ($query['signature'] !== sha1(implode($params))) {
                    throw new BadRequestException('Invalid request signature.');
                }
            }
        );
    }

    /**
     * @throws \Throwable
     */
    public function addMessageListener(string $type, $handler): static
    {
        $this->withHandler(
            function ($message, \Closure $next) use ($type, $handler) {
                return $message->MsgType === $type ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    /**
     * @throws \Throwable
     */
    public function addEventListener(string $event, $handler): static
    {
        $this->withHandler(
            function ($message, \Closure $next) use ($event, $handler) {
                return $message->Event === $event ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function transformResponse($response, Message $message): Response
    {
        $response = $this->normalizeResponse($response);
        $currentTime = \time();

        return Response::xml(
            attributes: \array_merge(
                            [
                                'ToUserName' => $message->FromUserName,
                                'FromUserName' => $message->ToUserName,
                                'CreateTime' => $currentTime,
                            ],
                            $response
                        ),
            encryptor: $this->encryptor
        );
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function normalizeResponse($response): array
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
}