<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Support\Xml;
use Psr\Http\Message\ServerRequestInterface;

class Message extends \EasyWeChat\Kernel\Message
{
    /**
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public static function createFromRequest(
        ServerRequestInterface $request,
        ?Encryptor $encryptor = null
    ): static
    {
        $attributes = self::format($originContent = strval($request->getBody()));

        $query = $request->getQueryParams();

        if (
            isset($query['msg_signature'])
            &&
            'aes' === ($query['encrypt_type'] ?? '')
            &&
            $ciphertext = $attributes['Encrypt'] ?? null
        ) {
            if (!$encryptor) {
                throw new InvalidArgumentException('$encryptor could not be empty in safety mode.');
            }
            $attributes = Xml::parse(
                $encryptor->decrypt(
                    $ciphertext,
                    $query['msg_signature'],
                    $query['nonce'],
                    $query['timestamp']
                )
            );
        }

        return new static($attributes, $originContent);
    }
}
