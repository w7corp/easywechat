<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Support\AES;
use EasyWeChat\Kernel\Support\XML;
use Throwable;
use function EasyWeChat\Kernel\Support\str_random;

/**
 * Class Encryptor.
 *
 * @author overtrue <i@overtrue.me>
 */
class Encryptor
{
    const ERROR_INVALID_SIGNATURE = -40001; // Signature verification failed
    const ERROR_PARSE_XML = -40002; // Parse XML failed
    const ERROR_CALC_SIGNATURE = -40003; // Calculating the signature failed
    const ERROR_INVALID_AES_KEY = -40004; // Invalid AESKey
    const ERROR_INVALID_APP_ID = -40005; // Check AppID failed
    const ERROR_ENCRYPT_AES = -40006; // AES Encryption failed
    const ERROR_DECRYPT_AES = -40007; // AES decryption failed
    const ERROR_INVALID_XML = -40008; // Invalid XML
    const ERROR_BASE64_ENCODE = -40009; // Base64 encoding failed
    const ERROR_BASE64_DECODE = -40010; // Base64 decoding failed
    const ERROR_XML_BUILD = -40011; // XML build failed
    const ILLEGAL_BUFFER = -41003; // Illegal buffer

    /**
     * App id.
     *
     * @var string
     */
    protected $appId;

    /**
     * App token.
     *
     * @var string
     */
    protected $token;

    /**
     * AES key.
     *
     * @var \EasyWeChat\Kernel\Support\AES
     */
    protected $aes;

    /**
     * Block size.
     *
     * @var int
     */
    protected $blockSize = 32;

    /**
     * Constructor.
     *
     * @param string $appId
     * @param string $token
     * @param string $aesKey
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function __construct($appId, $token, $aesKey)
    {
        $this->appId = $appId;
        $this->token = $token;

        if (empty($aesKey)) {
            throw new InvalidConfigException("Mission config 'aes_key'.");
        }

        if (strlen($aesKey) !== 43) {
            throw new InvalidConfigException("The length of 'aes_key' must be 43.");
        }

        $this->aes = new AES(base64_decode($aesKey.'=', true));
        $this->aes->setIv(substr($aesKey, 0, 16));
    }

    /**
     * Encrypt the message and return XML.
     *
     * @param string $xml
     * @param string $nonce
     * @param int    $timestamp
     *
     * @return string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function encrypt($xml, $nonce = null, $timestamp = null)
    {
        try {
            $xml = $this->pkcs7Pad(str_random(16).pack('N', strlen($xml)).$xml.$this->appId);

            $encrypted = base64_encode($this->aes->encrypt($xml));
        } catch (Throwable $e) {
            throw new RuntimeException($e->getMessage(), self::ERROR_ENCRYPT_AES);
        }

        !is_null($nonce) || $nonce = substr($this->appId, 0, 10);
        !is_null($timestamp) || $timestamp = time();

        $response = [
            'Encrypt' => $encrypted,
            'MsgSignature' => $this->signature($this->token, $timestamp, $nonce, $encrypted),
            'TimeStamp' => $timestamp,
            'Nonce' => $nonce,
        ];

        //生成响应xml
        return XML::build($response);
    }

    /**
     * Decrypt message.
     *
     * @param string $msgSignature
     * @param string $nonce
     * @param string $timestamp
     * @param string $postXML
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function decrypt($msgSignature, $nonce, $timestamp, $postXML)
    {
        try {
            $array = XML::parse($postXML);
        } catch (Throwable $e) {
            throw new RuntimeException('Invalid xml.', self::ERROR_PARSE_XML);
        }

        $signature = $this->signature($this->token, $timestamp, $nonce, $array['Encrypt']);

        if ($signature !== $msgSignature) {
            throw new RuntimeException('Invalid Signature.', self::ERROR_INVALID_SIGNATURE);
        }

        $decrypted = $this->aes->decrypt(base64_decode($array['Encrypt'], true));
        $result = $this->pkcs7Unpad($decrypted, $this->blockSize);
        $content = substr($result, 16, strlen($result));
        $xmlLen = unpack('N', substr($content, 0, 4))[1];

        if (trim(substr($content, $xmlLen + 4)) !== $this->appId) {
            throw new RuntimeException('Invalid appId.', self::ERROR_INVALID_APP_ID);
        }

        return XML::parse(substr($content, 4, $xmlLen));
    }

    /**
     * Get SHA1.
     *
     * @return string
     *
     * @throws self
     */
    public function signature()
    {
        $array = func_get_args();
        sort($array, SORT_STRING);

        return sha1(implode($array));
    }

    /**
     * PKCS#7 pad.
     *
     * @param string $text
     * @param int    $blockSize
     *
     * @return string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function pkcs7Pad(string $text, int $blockSize)
    {
        if ($blockSize > 256) {
            throw new RuntimeException('$blockSize may not be more than 256');
        }
        $padding = $blockSize - (strlen($text) % $blockSize);
        $pattern = chr($padding);

        return $text.str_repeat($pattern, $padding);
    }

    /**
     * PKCS#7 unpad.
     *
     * @param string $text
     *
     * @return bool|string
     */
    public function pkcs7Unpad(string $text)
    {
        $padChar = substr($text, -1);
        $padLength = ord($padChar);

        return substr($text, 0, -$padLength);
    }
}
