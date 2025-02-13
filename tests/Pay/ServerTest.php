<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Pay;

use EasyWeChat\Kernel\Support\AesEcb;
use EasyWeChat\Kernel\Support\AesGcm;
use EasyWeChat\Kernel\Support\Xml;
use EasyWeChat\Pay\Contracts\Merchant;
use EasyWeChat\Pay\Message;
use EasyWeChat\Pay\Server;
use EasyWeChat\Tests\TestCase;
use Mockery\LegacyMockInterface;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

use function bin2hex;
use function fopen;
use function md5;
use function random_bytes;

class ServerTest extends TestCase
{
    public function test_it_will_handle_validation_request()
    {
        $request = (new ServerRequest(
            'POST',
            'http://easywechat.com/',
            [
                'Content-Type' => 'application/json',
            ],
            fopen(__DIR__.'/../fixtures/files/pay_demo.json', 'r')
        ));

        /** @var Merchant&LegacyMockInterface $merchant */
        $merchant = \Mockery::mock(Merchant::class);
        $merchant->shouldReceive('getSecretKey')->andReturn('key');

        $server = new Server($merchant, $request);

        $response = $server->serve();
        $this->assertSame('{"code":"SUCCESS","message":"成功"}', \strval($response->getBody()));
    }

    public function test_legacy_encryped_by_aesecb_refund_request()
    {
        /** @var Merchant&LegacyMockInterface $merchant */
        $merchant = \Mockery::mock(Merchant::class);
        $merchant->shouldReceive(['getV2SecretKey' => random_bytes(32)]);
        $symmtricKey = $merchant->getV2SecretKey();

        $server = new Server($merchant, new ServerRequest(
            'POST',
            'http://easywechat.com/sample-webhook-handler',
            [
                'Content-Type' => 'text/xml',
            ],
            Xml::build([
                'return_code' => 'SUCCESS',
                'req_info' => AesEcb::encrypt(Xml::build([
                    'refund_id' => '50000408942018111907145868882',
                    'transaction_id' => '4200000215201811190261405420',
                ]), md5($symmtricKey), ''),
            ])
        ));

        $response = $server->with(function (Message $message): ResponseInterface {
            $source = $message->getOriginalContents();
            $parsed = $message->toArray();

            $this->assertStringContainsString('<xml>', $source);
            $this->assertStringContainsString('<req_info>', $source);
            $this->assertStringNotContainsString('<refund_id>', $source);
            $this->assertStringNotContainsString('<transaction_id>', $source);
            $this->assertArrayNotHasKey('return_code', $parsed);
            $this->assertArrayNotHasKey('req_info', $parsed);
            $this->assertArrayHasKey('refund_id', $parsed);
            $this->assertArrayHasKey('transaction_id', $parsed);

            return new Response(
                200,
                ['Content-Type' => 'text/xml'],
                '<xml><return_code>SUCCESS</return_code></xml>'
            );
        })->serve();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame('<xml><return_code>SUCCESS</return_code></xml>', \strval($response->getBody()));
    }

    public function test_legacy_encryped_by_aesgcm_notification_request()
    {
        /** @var Merchant&LegacyMockInterface $merchant */
        $merchant = \Mockery::mock(Merchant::class);
        $merchant->shouldReceive(['getSecretKey' => random_bytes(32)]);
        $symmtricKey = $merchant->getSecretKey();

        $server = new Server($merchant, new ServerRequest(
            'POST',
            'http://easywechat.com/sample-webhook-handler',
            [
                'Content-Type' => 'text/xml',
            ],
            Xml::build([
                'event_type' => 'TRANSACTION.SUCCESS',
                'event_algorithm' => 'AEAD_AES_256_GCM',
                'event_nonce' => $nonce = bin2hex(random_bytes(6)),
                'event_associated_data' => $aad = '',
                'event_ciphertext' => AesGcm::encrypt(Xml::build([
                    'state' => 'USER_PAID',
                    'service_id' => '1234352342',
                ]), $symmtricKey, iv: $nonce, aad: $aad),
            ])
        ));

        $response = $server->with(function (Message $message): ResponseInterface {
            $source = $message->getOriginalContents();
            $parsed = $message->toArray();

            $this->assertStringContainsString('<xml>', $source);
            $this->assertStringContainsString('<event_type>', $source);
            $this->assertStringContainsString('<event_algorithm>', $source);
            $this->assertStringContainsString('<event_nonce>', $source);
            $this->assertStringContainsString('<event_associated_data>', $source);
            $this->assertStringContainsString('<event_ciphertext>', $source);
            $this->assertStringNotContainsString('<state>', $source);
            $this->assertStringNotContainsString('<service_id>', $source);
            $this->assertArrayHasKey('event_type', $parsed);
            $this->assertArrayHasKey('event_algorithm', $parsed);
            $this->assertArrayHasKey('event_nonce', $parsed);
            $this->assertArrayHasKey('event_associated_data', $parsed);
            $this->assertArrayHasKey('event_ciphertext', $parsed);
            $this->assertArrayHasKey('state', $parsed);
            $this->assertArrayHasKey('service_id', $parsed);

            return new Response(
                500,
                ['Content-Type' => 'text/xml'],
                '<xml><code>ERROR_NAME</code><message>ERROR_DESCRIPTION</message></xml>'
            );
        })->serve();

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertSame('<xml><code>ERROR_NAME</code><message>ERROR_DESCRIPTION</message></xml>', \strval($response->getBody()));
    }
}
