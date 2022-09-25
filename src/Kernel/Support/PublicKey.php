<?php

namespace EasyWeChat\Kernel\Support;

use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use function file_exists;
use function file_get_contents;
use function openssl_x509_parse;
use function str_starts_with;
use function strtoupper;

class PublicKey
{
    public function __construct(public string $certificate)
    {
        if (file_exists($certificate)) {
            $this->certificate = "file://{$certificate}";
        }
    }

    /**
     * @throws InvalidConfigException
     */
    public function getSerialNo(): string
    {
        $info = openssl_x509_parse($this->certificate);

        if (false === $info || ! isset($info['serialNumberHex'])) {
            throw new InvalidConfigException('Read the $certificate failed, please check it whether or nor correct');
        }

        return strtoupper($info['serialNumberHex'] ?? '');
    }

    public function __toString(): string
    {
        if (str_starts_with($this->certificate, 'file://')) {
            return file_get_contents($this->certificate) ?: '';
        }

        return $this->certificate;
    }
}
