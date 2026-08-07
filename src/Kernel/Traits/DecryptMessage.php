<?php

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Message;
use EasyWeChat\Kernel\Support\MessageParser;

trait DecryptMessage
{
    /**
     * Decrypt message (automatically detects XML or JSON format).
     *
     * @throws RuntimeException
     * @throws BadRequestException
     */
    public function decryptMessage(
        Message $message,
        Encryptor $encryptor,
        string $signature,
        int|string $timestamp,
        string $nonce
    ): Message {
        $ciphertext = $message->Encrypt ?? $message->encrypt ?? null;

        if (! is_string($ciphertext) || $ciphertext === '') {
            throw new BadRequestException('Request ciphertext must not be empty.');
        }

        $this->validateSignature($encryptor->getToken(), $ciphertext, $signature, $timestamp, $nonce);

        $plaintext = $encryptor->decrypt(
            ciphertext: $ciphertext,
            msgSignature: $signature,
            nonce: $nonce,
            timestamp: $timestamp
        );

        $attributes = MessageParser::parse($plaintext);

        $message->merge($attributes);

        return $message;
    }

    /**
     * Validate the request signature of an encrypted message.
     *
     * @throws BadRequestException
     */
    protected function validateSignature(
        string $token,
        string $ciphertext,
        string $signature,
        int|string $timestamp,
        string $nonce
    ): void {
        $this->assertSignatureMatches([$token, $timestamp, $nonce, $ciphertext], $signature);
    }

    /**
     * Validate the request signature of a plaintext message.
     *
     * The plaintext signature only covers the token, timestamp and nonce, it does NOT
     * cover the message body. It is the only signature available in plaintext mode.
     *
     * @throws BadRequestException
     */
    protected function validatePlainSignature(
        string $token,
        string $signature,
        int|string $timestamp,
        string $nonce
    ): void {
        $this->assertSignatureMatches([$token, $timestamp, $nonce], $signature);
    }

    /**
     * Read a query param as string, non-scalar values (e.g. `?signature[]=foo`) are discarded.
     *
     * @param  array<string,mixed>  $query
     */
    protected function getQueryValue(array $query, string $key): string
    {
        $value = $query[$key] ?? '';

        return is_scalar($value) ? strval($value) : '';
    }

    /**
     * @param  array<int, int|string>  $params
     *
     * @throws BadRequestException
     */
    protected function assertSignatureMatches(array $params, string $signature): void
    {
        if (empty($signature)) {
            throw new BadRequestException('Request signature must not be empty.');
        }

        sort($params, SORT_STRING);

        if (! hash_equals(sha1(implode($params)), $signature)) {
            throw new BadRequestException('Invalid request signature.');
        }
    }
}
