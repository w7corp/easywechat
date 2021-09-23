<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Support\Pkcs7;
use EasyWeChat\Kernel\Support\Xml;
use Throwable;

class Encryptor
{
    public const ERROR_INVALID_SIGNATURE = -40001; // Signature verification failed
    public const ERROR_PARSE_XML = -40002; // Parse XML failed
    public const ERROR_CALC_SIGNATURE = -40003; // Calculating the signature failed
    public const ERROR_INVALID_AES_KEY = -40004; // Invalid AESKey
    public const ERROR_INVALID_APP_ID = -40005; // Check AppID failed
    public const ERROR_ENCRYPT_AES = -40006; // AES EncryptionInterface failed
    public const ERROR_DECRYPT_AES = -40007; // AES decryption failed
    public const ERROR_INVALID_XML = -40008; // Invalid XML
    public const ERROR_BASE64_ENCODE = -40009; // Base64 encoding failed
    public const ERROR_BASE64_DECODE = -40010; // Base64 decoding failed
    public const ERROR_XML_BUILD = -40011; // XML build failed
    public const ILLEGAL_BUFFER = -41003; // Illegal buffer

    protected string $appId;
    protected ?string $token;
    protected string $aesKey;
    protected int $blockSize = 32;

    public function __construct(string $appId, string $token = null, string $aesKey = null)
    {
        $this->appId = $appId;
        $this->token = $token;
        $this->aesKey = base64_decode($aesKey.'=', true);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function encrypt(string $plaintext, string | null $nonce = null, int | string $timestamp = null): string
    {
        try {
            $plaintext = Pkcs7::padding(\random_bytes(16).pack('N', strlen($plaintext)).$plaintext.$this->appId, 32);
            $ciphertext = base64_encode(
                \openssl_encrypt(
                    $plaintext,
                    "aes-256-cbc",
                    $this->aesKey,
                    \OPENSSL_NO_PADDING,
                    substr($this->aesKey, 0, 16)
                )
            );
        } catch (Throwable $e) {
            throw new RuntimeException($e->getMessage(), self::ERROR_ENCRYPT_AES);
        }

        !is_null($nonce) || $nonce = substr($this->appId, 0, 10);
        !is_null($timestamp) || $timestamp = time();

        $response = [
            'Encrypt' => $ciphertext,
            'MsgSignature' => $this->createSignature($this->token, $timestamp, $nonce, $ciphertext),
            'TimeStamp' => $timestamp,
            'Nonce' => $nonce,
        ];

        return Xml::build($response);
    }

    public function createSignature(...$attributes): string
    {
        sort($attributes, \SORT_STRING);

        return sha1(implode($attributes));
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function decrypt(string $ciphertext, string $msgSignature, string $nonce, int | string $timestamp): string
    {
        $signature = $this->createSignature($this->token, $timestamp, $nonce, $ciphertext);

        if ($signature !== $msgSignature) {
            throw new RuntimeException('Invalid Signature.', self::ERROR_INVALID_SIGNATURE);
        }

        $plaintext = Pkcs7::unpadding(
            \openssl_decrypt(
                \base64_decode($ciphertext, true),
                "aes-256-cbc",
                $this->aesKey,
                \OPENSSL_NO_PADDING,
                substr($this->aesKey, 0, 16)
            ),
            32
        );
        $plaintext = substr($plaintext, 16);
        $contentLength = unpack('N', substr($plaintext, 0, 4))[1];

        if (trim(substr($plaintext, $contentLength + 4)) !== $this->appId) {
            throw new RuntimeException('Invalid appId.', self::ERROR_INVALID_APP_ID);
        }

        return substr($plaintext, 4, $contentLength);
    }
}
