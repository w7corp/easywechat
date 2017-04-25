<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Encryptor.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\MiniProgram\Encryption;

use EasyWeChat\Core\Exceptions\InvalidConfigException;
use EasyWeChat\Encryption\EncryptionException;
use EasyWeChat\Encryption\Encryptor as BaseEncryptor;
use EasyWeChat\Support\Collection;
use Exception as BaseException;

class Encryptor extends BaseEncryptor
{
    /**
     * A non-NULL Initialization Vector.
     *
     * @var string
     */
    protected $iv;

    /**
     * Encryptor constructor.
     *
     * @param string $sessionKey
     * @param string $iv
     */
    public function __construct($sessionKey, $iv)
    {
        $this->iv = base64_decode($iv, true);

        parent::__construct(null, null, $sessionKey);
    }

    /**
     * Decrypt data.
     *
     * @param $encrypted
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function decryptData($encrypted)
    {
        return new Collection(
            $this->decrypt($encrypted)
        );
    }

    /**
     * Decrypt data.
     *
     * @param string $encrypted
     *
     * @return array
     *
     * @throws EncryptionException
     */
    private function decrypt($encrypted)
    {
        try {
            $key = $this->getAESKey();
            $ciphertext = base64_decode($encrypted, true);
            $decrypted = openssl_decrypt($ciphertext, 'aes-128-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, $this->iv);
        } catch (BaseException $e) {
            throw new EncryptionException($e->getMessage(), EncryptionException::ERROR_DECRYPT_AES);
        }

        $result = json_decode($this->decode($decrypted), true);

        if (is_null($result)) {
            throw new EncryptionException('ILLEGAL_BUFFER', EncryptionException::ILLEGAL_BUFFER);
        }

        return $result;
    }

    /**
     * Return AESKey.
     *
     * @return string
     *
     * @throws InvalidConfigException
     */
    protected function getAESKey()
    {
        if (empty($this->AESKey)) {
            throw new InvalidConfigException("Configuration mission, 'aes_key' is required.");
        }

        return base64_decode($this->AESKey, true);
    }
}
