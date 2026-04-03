<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Pay;

use EasyWeChat\Pay\Application;
use EasyWeChat\Pay\Client;
use EasyWeChat\Pay\Contracts\Merchant;
use EasyWeChat\Pay\Contracts\Validator;
use EasyWeChat\Pay\Server;
use EasyWeChat\Tests\TestCase;
use Nyholm\Psr7\ServerRequest;

class ApplicationTest extends TestCase
{
    public function test_get_merchant()
    {
        $app = new Application(
            [
                'mch_id' => 101111111,
                'secret_key' => 'mock-secret-key',
                'private_key' => 'mock-private-key',
                'certificate' => '/path/to/certificate.cert',
                'certificate_serial_no' => 'MOCK-CERTIFICATE-SERIAL-NO',
            ]
        );

        $this->assertInstanceOf(Merchant::class, $app->getMerchant());
        $this->assertSame($app->getMerchant(), $app->getMerchant());
    }

    public function test_get_client()
    {
        $app = new Application(
            [
                'mch_id' => 101111111,
                'secret_key' => 'mock-secret-key',
                'private_key' => 'mock-private-key',
                'certificate' => '/path/to/certificate.cert',
                'certificate_serial_no' => 'MOCK-CERTIFICATE-SERIAL-NO',
            ]
        );

        $this->assertInstanceOf(Client::class, $app->getClient());
        $this->assertSame($app->getHttpClient(), $app->getHttpClient());
    }

    public function test_get_server()
    {
        $app = new Application(
            [
                'mch_id' => 101111111,
                'secret_key' => 'mock-secret-key',
                'private_key' => 'mock-private-key',
                'certificate' => '/path/to/certificate.cert',
                'certificate_serial_no' => 'MOCK-CERTIFICATE-SERIAL-NO',
            ]
        );

        $this->assertInstanceOf(Server::class, $app->getServer());
        $this->assertSame($app->getServer(), $app->getServer());
    }

    public function test_application_server_uses_updated_request_after_server_is_resolved()
    {
        $app = new Application(
            [
                'mch_id' => 101111111,
                'secret_key' => 'key',
                'private_key' => 'mock-private-key',
                'certificate' => '/path/to/certificate.cert',
                'certificate_serial_no' => 'MOCK-CERTIFICATE-SERIAL-NO',
            ]
        );

        $this->assertInstanceOf(Server::class, $app->getServer());

        $app->setRequest(new ServerRequest(
            'POST',
            'http://easywechat.com/',
            [
                'Content-Type' => 'application/json',
            ],
            \fopen(__DIR__.'/../fixtures/files/pay_demo.json', 'r')
        ));

        $response = $app->getServer()->serve();

        $this->assertSame('{"code":"SUCCESS","message":"成功"}', (string) $response->getBody());
    }

    public function test_get_and_set_validator()
    {
        $app = new Application(
            [
                'mch_id' => 101111111,
                'secret_key' => 'mock-secret-key',
                'private_key' => 'mock-private-key',
                'certificate' => '/path/to/certificate.cert',
                'certificate_serial_no' => 'MOCK-CERTIFICATE-SERIAL-NO',
            ]
        );

        $this->assertInstanceOf(Validator::class, $app->getValidator());
        $this->assertSame($app->getValidator(), $app->getValidator());

        $validator = \Mockery::mock(Validator::class);

        $app->setValidator($validator);

        $this->assertSame($validator, $app->getValidator());
    }
}
