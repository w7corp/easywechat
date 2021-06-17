<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Server;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\OfficialAccount\Contracts\Message as MessageInterface;
use EasyWeChat\OfficialAccount\Contracts\Account as AccountInterface;
use EasyWeChat\OfficialAccount\Contracts\Server as ServerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Server implements ServerInterface
{
    use InteractWithHandlers;

    /**
     * @throws \Throwable
     */
    public function __construct(
        protected AccountInterface $account,
        protected ServerRequestInterface $request,
        protected ?Encryptor $encryptor = null,
    ) {
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException|\EasyWeChat\Kernel\Exceptions\BadRequestException|\Throwable
     */
    public function process(): ResponseInterface
    {
        if (
            'GET' === \strtoupper($this->request->getMethod())
            &&
            !!($str = $this->request->getQueryParams()['echostr'])
        ) {
            return new Response(200, $str);
        }

        $this->withMessageValidationHandler();

        $message = Message::createFromRequest($this->request, $this->encryptor);

        $response = $this->normalizeResponse($this->handle(Response::SUCCESS_EMPTY_RESPONSE, $this->request, $message));

        $currentTime = \time();

        return Response::xml(
            \array_merge(
                    [
                        'ToUserName' => $message->to,
                        'FromUserName' => $message->from,
                        'CreateTime' => $currentTime,
                    ],
                    $response
                ),
            $this->encryptor
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

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \Throwable
     */
    public function withMessageValidationHandler(): static
    {
        return $this->withHandler(function (MessageInterface $message, \Closure $next) {
            $query = $this->request->getQueryParams();

            if (!isset($query['signature']) || 'aes' !== ($query['encrypt_type'] ?? '')) {
                return $next($message);
            }

            $params = [$this->account->getToken(), $query['timestamp'], $query['nonce']];

            sort($params, SORT_STRING);

            if ($query['signature'] !== sha1(implode($params))) {
                throw new BadRequestException('Invalid request signature.');
            }
        });
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \Throwable
     */
    public function addMessageListener(string $type, $handler): static
    {
        $this->withHandler(
            function (MessageInterface $message, \Closure $next) use ($type, $handler) {
                return $message->MsgType === $type ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \Throwable
     */
    public function addEventListener(string $event, $handler): static
    {
        $this->withHandler(function (MessageInterface $message, \Closure $next) use ($event, $handler) {
            return $message->Event === $event ? $handler($message) : $next($message);
        });

        return $this;
    }
}
