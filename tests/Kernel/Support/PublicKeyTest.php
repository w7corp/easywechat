<?php

namespace EasyWeChat\Tests\Kernel\Support;

use EasyWeChat\Kernel\Support\PublicKey;
use EasyWeChat\Tests\TestCase;

class PublicKeyTest extends TestCase
{
    public function test_create_from_contents()
    {
        $contents = file_get_contents(__DIR__.'/../../fixtures/cert.pem') ?: '';
        $cert = new PublicKey($contents);

        $this->assertSame($contents, \strval($cert));
    }

    public function test_create_from_path()
    {
        $path = __DIR__.'/../../fixtures/cert.pem';
        $contents = file_get_contents($path) ?: '';
        $cert = new PublicKey($path);

        $this->assertSame($contents, \strval($cert));
    }
}
