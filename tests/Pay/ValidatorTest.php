<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Pay;

use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Support\PrivateKey;
use EasyWeChat\Kernel\Support\PublicKey;
use EasyWeChat\Pay\Contracts\Merchant;
use EasyWeChat\Pay\Exceptions\InvalidSignatureException;
use EasyWeChat\Pay\Merchant as PayMerchant;
use EasyWeChat\Pay\Validator;
use EasyWeChat\Tests\TestCase;
use Nyholm\Psr7\Response;
use Psr\Http\Message\MessageInterface;

class ValidatorTest extends TestCase
{
    public function test_validate_throws_when_header_is_missing()
    {
        $merchant = \Mockery::mock(Merchant::class);
        $validator = new Validator($merchant);

        $message = new Response(200, [
            Validator::HEADER_TIMESTAMP => '1712300000',
            Validator::HEADER_NONCE => 'mock-nonce',
            Validator::HEADER_SERIAL => 'mock-serial',
        ], 'body');

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Missing Header: Wechatpay-Signature');

        $validator->validate($message);
    }

    public function test_validate_with_empty_header_values_throws_without_warnings()
    {
        $merchant = \Mockery::mock(Merchant::class);
        $validator = new Validator($merchant);

        $message = \Mockery::mock(MessageInterface::class);
        $message->shouldReceive('hasHeader')->andReturn(true);
        $message->shouldReceive('getHeader')->with(Validator::HEADER_TIMESTAMP)->andReturn([]);
        $message->shouldReceive('getBody')->never();

        $errors = [];
        set_error_handler(function (int $severity, string $message) use (&$errors): bool {
            $errors[] = [$severity, $message];

            return true;
        });

        try {
            try {
                $validator->validate($message);
                $this->fail('Expected validate() to throw.');
            } catch (InvalidSignatureException $e) {
                $this->assertSame('Invalid Header: Wechatpay-Timestamp', $e->getMessage());
            }
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $errors);
    }

    public function test_validate_throws_when_platform_certificate_is_missing()
    {
        $merchant = \Mockery::mock(Merchant::class);
        $merchant->shouldReceive('getPlatformCert')->with('mock-serial')->andReturn(null);

        $validator = new Validator($merchant);

        $message = new Response(200, [
            Validator::HEADER_TIMESTAMP => (string) time(),
            Validator::HEADER_NONCE => 'mock-nonce',
            Validator::HEADER_SERIAL => 'mock-serial',
            Validator::HEADER_SIGNATURE => base64_encode('mock-signature'),
        ], 'body');

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('No platform certs found for serial: mock-serial');

        $validator->validate($message);
    }

    public function test_validate_throws_when_timestamp_is_too_old()
    {
        $merchant = \Mockery::mock(Merchant::class);
        $merchant->shouldReceive('getPlatformCert')->never();

        $validator = new Validator($merchant);
        $message = new Response(200, [
            Validator::HEADER_TIMESTAMP => (string) (time() - Validator::MAX_ALLOWED_CLOCK_OFFSET - 1),
            Validator::HEADER_NONCE => 'mock-nonce',
            Validator::HEADER_SERIAL => 'mock-serial',
            Validator::HEADER_SIGNATURE => base64_encode('mock-signature'),
        ], 'body');

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Clock Offset Exceeded');

        $validator->validate($message);
    }

    public function test_validate_throws_when_timestamp_is_too_far_in_future()
    {
        $merchant = \Mockery::mock(Merchant::class);
        $merchant->shouldReceive('getPlatformCert')->never();

        $validator = new Validator($merchant);
        $message = new Response(200, [
            Validator::HEADER_TIMESTAMP => (string) (time() + Validator::MAX_ALLOWED_CLOCK_OFFSET + 1),
            Validator::HEADER_NONCE => 'mock-nonce',
            Validator::HEADER_SERIAL => 'mock-serial',
            Validator::HEADER_SIGNATURE => base64_encode('mock-signature'),
        ], 'body');

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Clock Offset Exceeded');

        $validator->validate($message);
    }

    public function test_validate_throws_when_platform_certificate_is_invalid_without_warnings()
    {
        $merchant = new PayMerchant(
            mchId: 100001,
            privateKey: new PrivateKey('mock-private-key'),
            certificate: new PublicKey(__DIR__.'/../fixtures/cert.pem'),
            secretKey: 'secret',
            v2SecretKey: 'v2secret',
            platformCerts: ['mock-serial' => 'not-a-public-key']
        );
        $validator = new Validator($merchant);
        $message = new Response(200, [
            Validator::HEADER_TIMESTAMP => (string) time(),
            Validator::HEADER_NONCE => 'mock-nonce',
            Validator::HEADER_SERIAL => 'mock-serial',
            Validator::HEADER_SIGNATURE => base64_encode('mock-signature'),
        ], 'body');
        $errors = [];

        set_error_handler(function (int $severity, string $message) use (&$errors): bool {
            $errors[] = [$severity, $message];

            return true;
        });

        try {
            try {
                $validator->validate($message);
                $this->fail('Expected validate() to throw.');
            } catch (InvalidConfigException $e) {
                $this->assertSame('Invalid platform certificate for serial: mock-serial.', $e->getMessage());
            }
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $errors);
    }

    public function test_validate_with_invalid_signature_encoding_throws_without_warnings()
    {
        $cwd = getcwd();
        chdir(dirname(__DIR__, 2));

        try {
            $merchant = new PayMerchant(
                mchId: 100001,
                privateKey: new PrivateKey('tests/fixtures/private.key', 'overtrue'),
                certificate: new PublicKey('tests/fixtures/cert.pem'),
                secretKey: 'secret',
                v2SecretKey: 'v2secret',
                platformCerts: ['mock-serial' => 'tests/fixtures/cert.pem']
            );
            $validator = new Validator($merchant);
            $message = new Response(200, [
                Validator::HEADER_TIMESTAMP => (string) time(),
                Validator::HEADER_NONCE => 'mock-nonce',
                Validator::HEADER_SERIAL => 'mock-serial',
                Validator::HEADER_SIGNATURE => '@@@',
            ], 'body');
            $errors = [];

            set_error_handler(function (int $severity, string $message) use (&$errors): bool {
                $errors[] = [$severity, $message];

                return true;
            });

            try {
                try {
                    $validator->validate($message);
                    $this->fail('Expected validate() to throw.');
                } catch (InvalidSignatureException $e) {
                    $this->assertSame('Invalid Signature', $e->getMessage());
                }
            } finally {
                restore_error_handler();
            }

            $this->assertSame([], $errors);
        } finally {
            chdir($cwd ?: dirname(__DIR__, 2));
        }
    }
}
