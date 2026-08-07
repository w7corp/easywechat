<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount;

use Closure;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\ServerResponse;
use EasyWeChat\Kernel\Traits\DecryptMessage;
use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Kernel\Traits\InteractWithServerRequest;
use EasyWeChat\Kernel\Traits\RespondXmlMessage;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class Server implements ServerInterface
{
    use DecryptMessage;
    use InteractWithHandlers;
    use InteractWithServerRequest;
    use RespondXmlMessage;

    public function __construct(
        ?ServerRequestInterface $request = null,
        protected ?Encryptor $encryptor = null,
        protected ?string $token = null,
        protected bool $requireEncryption = false,
    ) {
        $this->request = $request;
    }

    /**
     * @throws BadRequestException
     * @throws InvalidConfigException
     */
    public function serve(): ResponseInterface
    {
        $query = $this->getRequest()->getQueryParams();

        if ($str = $this->getQueryValue($query, 'echostr')) {
            $this->validatePlainRequest($query);

            return new Response(200, [], $str);
        }

        $message = $this->getRequestMessage($this->getRequest());

        if ($this->encryptor && $this->isEncryptedRequest($query, $message)) {
            $this->prepend($this->decryptRequestMessage($query));
        } else {
            if ($this->requireEncryption) {
                throw new BadRequestException('Encrypted message is required, plaintext message rejected.');
            }

            $this->validatePlainRequest($query);
        }

        $response = $this->handle(new Response(200, [], 'success'), $message);

        if (! ($response instanceof ResponseInterface)) {
            $response = $this->transformToReply($response, $message, $this->encryptor);
        }

        return ServerResponse::make($response);
    }

    /**
     * @throws Throwable
     */
    public function addMessageListener(string $type, callable|string $handler): static
    {
        $handler = $this->makeClosure($handler);
        $this->withHandler(
            function (Message $message, Closure $next) use ($type, $handler): mixed {
                return $message->MsgType === $type ? $handler($message, $next) : $next($message);
            }
        );

        return $this;
    }

    public function addEventListener(string $event, callable|string $handler): static
    {
        $handler = $this->makeClosure($handler);
        $this->withHandler(
            function (Message $message, Closure $next) use ($event, $handler): mixed {
                return $message->Event === $event ? $handler($message, $next) : $next($message);
            }
        );

        return $this;
    }

    /**
     * @param  array<string,string>  $query
     *
     * @psalm-suppress PossiblyNullArgument
     */
    protected function decryptRequestMessage(array $query): Closure
    {
        return function (Message $message, Closure $next) use ($query): mixed {
            if (! $this->encryptor) {
                return null;
            }

            $this->decryptIncomingMessage($message, $query);

            return $next($message);
        };
    }

    public function getRequestMessage(?ServerRequestInterface $request = null): \EasyWeChat\Kernel\Message
    {
        return Message::createFromRequest($request ?? $this->getRequest());
    }

    /**
     * @throws BadRequestException
     * @throws InvalidConfigException
     */
    public function getDecryptedMessage(?ServerRequestInterface $request = null): \EasyWeChat\Kernel\Message
    {
        $request = $request ?? $this->getRequest();
        $message = $this->getRequestMessage($request);
        $query = $request->getQueryParams();

        if ($this->encryptor && $this->isEncryptedRequest($query, $message)) {
            return $this->decryptIncomingMessage($message, $query);
        }

        if ($this->requireEncryption) {
            throw new BadRequestException('Encrypted message is required, plaintext message rejected.');
        }

        $this->validatePlainRequest($query);

        return $message;
    }

    /**
     * Whether the incoming request carries an encrypted message.
     *
     * Both the secure mode and the compatible mode push a ciphertext along with
     * `encrypt_type=aes`, only the plaintext mode has neither.
     *
     * @param  array<string,mixed>  $query
     */
    protected function isEncryptedRequest(array $query, \EasyWeChat\Kernel\Message $message): bool
    {
        return ($query['encrypt_type'] ?? '') === 'aes'
            || ! empty($message->Encrypt)
            || ! empty($message->encrypt);
    }

    /**
     * Validate the signature of a plaintext request.
     *
     * @param  array<string,mixed>  $query
     *
     * @throws BadRequestException
     * @throws InvalidConfigException
     */
    protected function validatePlainRequest(array $query): void
    {
        $this->validatePlainSignature(
            token: $this->getToken(),
            signature: $this->getQueryValue($query, 'signature'),
            timestamp: $this->getQueryValue($query, 'timestamp'),
            nonce: $this->getQueryValue($query, 'nonce')
        );
    }

    /**
     * @throws InvalidConfigException
     */
    protected function getToken(): string
    {
        $token = $this->token ?? $this->encryptor?->getToken();

        if (empty($token)) {
            throw new InvalidConfigException(
                'The token is required to validate the request signature, '
                .'please pass it to the server or configure the `token` of the application.'
            );
        }

        return $token;
    }

    /**
     * @param  array<string,mixed>  $query
     */
    protected function decryptIncomingMessage(\EasyWeChat\Kernel\Message $message, array $query): \EasyWeChat\Kernel\Message
    {
        if (! $this->encryptor) {
            return $message;
        }

        $signature = $this->getQueryValue($query, 'msg_signature');
        $timestamp = $this->getQueryValue($query, 'timestamp');
        $nonce = $this->getQueryValue($query, 'nonce');

        return $this->decryptMessage(
            message: $message,
            encryptor: $this->encryptor,
            signature: $signature,
            timestamp: $timestamp,
            nonce: $nonce
        );
    }
}
