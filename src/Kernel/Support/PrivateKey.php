<?php

namespace EasyWeChat\Kernel\Support;

use JetBrains\PhpStorm\Pure;

use function file_exists;
use function file_get_contents;
use function is_string;
use function realpath;
use function str_starts_with;

class PrivateKey
{
    public function __construct(protected string $key, protected ?string $passphrase = null)
    {
        $path = realpath($key);

        if (is_string($path) && file_exists($path)) {
            $this->key = "file://{$path}";
        }
    }

    public function getKey(): string
    {
        if (str_starts_with($this->key, 'file://')) {
            return file_get_contents($this->key) ?: '';
        }

        return $this->key;
    }

    public function getPassphrase(): ?string
    {
        return $this->passphrase;
    }

    #[Pure]
    public function __toString(): string
    {
        return $this->getKey();
    }
}
