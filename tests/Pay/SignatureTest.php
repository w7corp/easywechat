<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Pay;

use EasyWeChat\Kernel\Support\PrivateKey;
use EasyWeChat\Kernel\Support\PublicKey;
use EasyWeChat\Pay\Exceptions\SignatureFailureException;
use EasyWeChat\Pay\Merchant;
use EasyWeChat\Pay\Signature;
use EasyWeChat\Tests\TestCase;

class SignatureTest extends TestCase
{
    public function test_create_header_throws_when_private_key_is_invalid_without_warnings()
    {
        $certificate = \Mockery::mock(PublicKey::class);

        $merchant = new Merchant(
            mchId: 100001,
            privateKey: new PrivateKey('not-a-private-key'),
            certificate: $certificate,
            secretKey: 'v3SecretKey',
            v2SecretKey: 'v2SecretKey',
            platformCerts: []
        );
        $signature = new Signature($merchant);
        $errors = [];

        set_error_handler(function (int $severity, string $message) use (&$errors): bool {
            $errors[] = [$severity, $message];

            return true;
        });

        try {
            $signature->createHeader('GET', 'https://example.com/v3/test', []);
            $this->fail('Expected signature failure exception.');
        } catch (SignatureFailureException $e) {
            $this->assertSame('Sign failed.', $e->getMessage());
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $errors);
    }
}
