<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Support;

use EasyWeChat\Kernel\Contracts\Encryption;

/**
 * Class AES.
 *
 * @author overtrue <i@overtrue.me>
 */
class AES implements Encryption
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $iv;

    /**
     * @var string
     */
    protected $mode = '';

    /**
     * @var array
     */
    protected $option;

    public function __construct($key)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt(string $text): string
    {
        return openssl_encrypt($text, $this->mode, $this->getKey(), $this->option, $this->getIv());
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt(string $cipherText): string
    {
        return openssl_decrypt($cipherText, $this->mode, $this->getKey(), $this->option, $this->getIv());
    }

    /**
     * @param string $key
     */
    public function setKey(string $key)
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException('Key must be a string');
        }
        if (!in_array(strlen($key), [16, 24, 32], true)) {
            throw new \InvalidArgumentException('Key length must be 16, 24, or 32 bytes; got key len ('.strlen($key).')');
        }

        $this->key = $key;
        $this->mode = 'aes-'.(8 * strlen($key)).'-cbc';
        $this->option = defined('OPENSSL_RAW_DATA') ? OPENSSL_RAW_DATA : true;
    }

    /**
     * @param string $iv
     *
     * @throws \InvalidArgumentException
     */
    public function setIv($iv)
    {
        if (!is_string($iv)) {
            throw new \InvalidArgumentException('IV must be a string');
        }
        if (strlen($iv) !== 16) {
            throw new \InvalidArgumentException('IV length must be 16 bytes');
        }
        $this->iv = $iv;
    }

    /**
     * @throws \LogicException
     *
     * @return string
     */
    public function getIv()
    {
        if (!isset($this->iv)) {
            throw new \LogicException('The iv is not set, call setIv() prior to usage');
        }

        return $this->iv;
    }

    /**
     * @throws \LogicException
     *
     * @return string
     */
    public function getKey()
    {
        if (!isset($this->key)) {
            throw new \LogicException('The key is not set, call setKey() prior to usage');
        }

        return $this->key;
    }
}
