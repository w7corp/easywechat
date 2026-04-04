<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Kernel\Support;

use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
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

    public function test_create_from_relative_path()
    {
        $cwd = getcwd();
        chdir(dirname(__DIR__, 3));

        try {
            $contents = file_get_contents('tests/fixtures/cert.pem') ?: '';
            $cert = new PublicKey('tests/fixtures/cert.pem');

            $this->assertSame($contents, \strval($cert));
        } finally {
            chdir($cwd ?: dirname(__DIR__, 3));
        }
    }

    public function test_get_serial_no()
    {
        $contents = file_get_contents(__DIR__.'/../../fixtures/cert.pem') ?: '';
        $cert = new PublicKey($contents);

        $this->assertSame('0DC0DF83', $cert->getSerialNo());
    }

    public function test_get_serial_no_throws_for_invalid_certificate()
    {
        $cert = new PublicKey('not-a-cert');

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Read the $certificate failed, please check it whether or nor correct');

        $cert->getSerialNo();
    }

    public function test_get_serial_no_throws_when_serial_number_is_missing_without_warnings()
    {
        $cert = new MissingSerialNumberPublicKey('mock-certificate');
        $errors = [];

        set_error_handler(function (int $severity, string $message) use (&$errors): bool {
            $errors[] = [$severity, $message];

            return true;
        });

        try {
            try {
                $cert->getSerialNo();
                $this->fail('Expected getSerialNo() to throw.');
            } catch (InvalidConfigException $e) {
                $this->assertSame('Certificate serial number is missing.', $e->getMessage());
            }
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $errors);
    }
}

class MissingSerialNumberPublicKey extends PublicKey
{
    /**
     * @return array<mixed>|false
     */
    protected function parseCertificate(): array|false
    {
        return ['subject' => ['CN' => 'mock']];
    }
}
