<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Server;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\OfficialAccount\Contracts\Request as RequestInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use function EasyWeChat\Kernel\throw_if;

class Request extends SymfonyRequest implements RequestInterface
{
    public function isValidation(): bool
    {
        return !is_null($this->get('echostr'));
    }

    public function isSafeMode(): bool
    {
        return $this->get('signature') && 'aes' === $this->get('encrypt_type');
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function getMessage(Encryptor $encryptor = null): Message
    {
        $originContent = $this->getContent();

        $attributes = $this->parse($originContent);

        if (
            $this->isSafeMode()
            &&
            $encrypt = $attributes['Encrypt'] ?? null
        ) {
            throw_if(!$encryptor, InvalidArgumentException::class, 'Encryptor cannot be empty.');

            $attributes = $this->parse(
                $encryptor->decrypt(
                    $encrypt,
                    $this->get('msg_signature'),
                    $this->get('nonce'),
                    $this->get('timestamp')
                )
            );
        }

        return new Message($attributes, $originContent);
    }

    public static function capture(): static
    {
        return static::createFromBase(SymfonyRequest::createFromGlobals());
    }

    public static function createFromBase(SymfonyRequest $symfonyRequest): static
    {
        $request = (new static())->duplicate(
            $symfonyRequest->query->all(),
            $symfonyRequest->request->all(),
            $symfonyRequest->attributes->all(),
            $symfonyRequest->cookies->all(),
            $symfonyRequest->files->all(),
            $symfonyRequest->server->all()
        );

        $request->headers->replace($symfonyRequest->headers->all());
        $request->content = $symfonyRequest->content;
        $request->request =
            in_array(
                $symfonyRequest->getRealMethod(),
                ['GET', 'HEAD']
            ) ? $symfonyRequest->query : $symfonyRequest->request;

        return $request;
    }

    protected function parse(string $content): ?array
    {
        if (0 === stripos($content, '<')) {
            return XML::parse($content);
        }

        // Handle JSON format.
        $dataSet = json_decode($content, true);

        if (
            JSON_ERROR_NONE === json_last_error()
            &&
            $content
        ) {
            $content = $dataSet;
        }

        return $content ?? null;
    }
}
