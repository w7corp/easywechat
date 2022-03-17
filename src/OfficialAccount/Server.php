<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\HttpClient\RequestUtil;
use EasyWeChat\Kernel\ServerResponse;
use EasyWeChat\Kernel\Traits\DecryptXmlMessage;
use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Kernel\Traits\RespondXmlMessage;
use EasyWeChat\OfficialAccount\Contracts\Account as AccountInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Server implements ServerInterface
{
    use RespondXmlMessage;
    use DecryptXmlMessage;
    use InteractWithHandlers;

    protected ServerRequestInterface $request;

    /**
     * @throws \Throwable
     */
    public function __construct(
        protected AccountInterface $account,
        ?ServerRequestInterface $request = null,
        protected ?Encryptor $encryptor = null,
    ) {
        $this->request = $request ?? RequestUtil::createDefaultServerRequest();
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function serve(): ResponseInterface
    {
        if (!!($str = $this->request->getQueryParams()['echostr'] ?? '')) {
            return new Response(200, [], $str);
        }

        $message = $this->getRequestMessage($this->request);
        $query = $this->request->getQueryParams();

        if ($this->encryptor && !empty($query['msg_signature'])) {
            $this->prepend($this->decryptRequestMessage($query));
        }

        $response = $this->handle(new Response(200, [], 'success'), $message);

        if (!($response instanceof ResponseInterface)) {
            $response = $this->transformToReply($response, $message, $this->encryptor);
        }

        return ServerResponse::make($response);
    }

    /**
     * @throws \Throwable
     */
    public function addMessageListener(string $type, callable $handler): static
    {
        $this->withHandler(
            function (Message $message, \Closure $next) use ($type, $handler): mixed {
                return $message->MsgType === $type ? $handler($message, $next) : $next($message);
            }
        );

        return $this;
    }

    /**
     * @throws \Throwable
     */
    public function addEventListener(string $event, callable $handler): static
    {
        $this->withHandler(
            function (Message $message, \Closure $next) use ($event, $handler): mixed {
                return $message->Event === $event ? $handler($message, $next) : $next($message);
            }
        );

        return $this;
    }

    /**
     * @param array<string,string> $query
     * @psalm-suppress PossiblyNullArgument
     */
    protected function decryptRequestMessage(array $query): \Closure
    {
        return function (Message $message, \Closure $next) use ($query): mixed {
            if (!$this->encryptor) {
                return null;
            }

            $this->decryptMessage(
                $message,
                $this->encryptor,
                $query['msg_signature'] ?? '',
                $query['timestamp'] ?? '',
                $query['nonce'] ?? ''
            );

            return $next($message);
        };
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     */
    public function getRequestMessage(?ServerRequestInterface $request = null): \EasyWeChat\Kernel\Message
    {
        return Message::createFromRequest($request ?? $this->request);
    }
}
