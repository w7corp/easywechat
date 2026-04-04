<?php

namespace EasyWeChat\Kernel\Support;

use EasyWeChat\Kernel\Exceptions\InvalidConfigException;

use function file_exists;
use function file_get_contents;
use function is_array;
use function is_string;
use function openssl_x509_parse;
use function realpath;
use function str_starts_with;
use function strtoupper;

class PublicKey
{
    public function __construct(public string $certificate)
    {
        $path = realpath($certificate);

        if (is_string($path) && file_exists($path)) {
            $this->certificate = "file://{$path}";
        }
    }

    /**
     * @throws InvalidConfigException
     */
    public function getSerialNo(): string
    {
        $info = $this->parseCertificate();

        if ($info === false) {
            throw new InvalidConfigException('Read the $certificate failed, please check it whether or nor correct');
        }

        $serialNumber = $info['serialNumberHex'] ?? null;

        if (! is_string($serialNumber) || $serialNumber === '') {
            throw new InvalidConfigException('Certificate serial number is missing.');
        }

        return strtoupper($serialNumber);
    }

    public function __toString(): string
    {
        if (str_starts_with($this->certificate, 'file://')) {
            return file_get_contents($this->certificate) ?: '';
        }

        return $this->certificate;
    }

    /**
     * @return array<mixed>|false
     */
    protected function parseCertificate(): array|false
    {
        $info = openssl_x509_parse($this->certificate);

        return is_array($info) ? $info : false;
    }
}
