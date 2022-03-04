<?php

namespace EasyWeChat\Tests\Kernel\Support;

use EasyWeChat\Kernel\Support\PrivateKey;
use EasyWeChat\Tests\TestCase;

class PrivateKeyTest extends TestCase
{
    public function test_create_from_contents()
    {
        $contents = file_get_contents(__DIR__.'/../../fixtures/private.key') ?: '';
        $key = new PrivateKey($contents, 'overtrue');

        $this->assertSame($contents, $key->getKey());
        $this->assertSame('overtrue', $key->getPassphrase());
    }

    public function test_create_from_path()
    {
        $path = __DIR__.'/../../fixtures/private.key';
        $contents = file_get_contents($path) ?: '';
        $key = new PrivateKey($path, 'overtrue');

        $this->assertSame($contents, $key->getKey());
        $this->assertSame('overtrue', $key->getPassphrase());
    }
}
