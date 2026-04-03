<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Pay;

use EasyWeChat\Kernel\Config;
use EasyWeChat\Pay\Application;
use EasyWeChat\Pay\Client;
use EasyWeChat\Pay\Contracts\Merchant;
use EasyWeChat\Pay\Contracts\Validator;
use EasyWeChat\Pay\Server;
use EasyWeChat\Tests\TestCase;
use Nyholm\Psr7\ServerRequest;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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

    public function test_set_http_client_refreshes_default_client()
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

        $firstClient = $app->getClient();

        $app->setHttpClient(new MockHttpClient);

        $secondClient = $app->getClient();

        $this->assertInstanceOf(Client::class, $secondClient);
        $this->assertNotSame($firstClient, $secondClient);
    }

    public function test_set_http_client_preserves_custom_client()
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

        $client = \Mockery::mock(HttpClientInterface::class);

        $app->setClient($client);
        $app->setHttpClient(\Mockery::mock(HttpClientInterface::class));

        $this->assertSame($client, $app->getClient());
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

    public function test_set_config_refreshes_default_dependencies()
    {
        $app = new Application(
            [
                'mch_id' => 101111111,
                'secret_key' => 'mock-secret-key',
                'private_key' => 'mock-private-key',
                'certificate' => '/path/to/certificate.cert',
                'certificate_serial_no' => 'MOCK-CERTIFICATE-SERIAL-NO',
                'http' => [
                    'timeout' => 5,
                ],
            ]
        );

        $firstMerchant = $app->getMerchant();
        $firstValidator = $app->getValidator();
        $firstServer = $app->getServer();
        $firstClient = $app->getClient();
        $firstHttpClient = $app->getHttpClient();

        $app->setConfig(new Config(
            [
                'mch_id' => 202222222,
                'secret_key' => 'mock-secret-key-2',
                'private_key' => 'mock-private-key-2',
                'certificate' => '/path/to/certificate-2.cert',
                'certificate_serial_no' => 'MOCK-CERTIFICATE-SERIAL-NO-2',
                'http' => [
                    'timeout' => 10,
                ],
            ]
        ));

        $secondMerchant = $app->getMerchant();
        $secondValidator = $app->getValidator();
        $secondServer = $app->getServer();
        $secondClient = $app->getClient();
        $secondHttpClient = $app->getHttpClient();

        $this->assertNotSame($firstMerchant, $secondMerchant);
        $this->assertNotSame($firstValidator, $secondValidator);
        $this->assertNotSame($firstServer, $secondServer);
        $this->assertNotSame($firstClient, $secondClient);
        $this->assertNotSame($firstHttpClient, $secondHttpClient);
        $this->assertSame(202222222, $secondMerchant->getMerchantId());
    }

    public function test_set_config_preserves_custom_dependencies()
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

        $validator = \Mockery::mock(Validator::class);
        $server = \Mockery::mock(Server::class);
        $client = \Mockery::mock(HttpClientInterface::class);
        $httpClient = \Mockery::mock(HttpClientInterface::class);

        $app->setValidator($validator);
        $app->setServer($server);
        $app->setClient($client);
        $app->setHttpClient($httpClient);

        $app->setConfig(new Config(
            [
                'mch_id' => 202222222,
                'secret_key' => 'mock-secret-key-2',
                'private_key' => 'mock-private-key-2',
                'certificate' => '/path/to/certificate-2.cert',
                'certificate_serial_no' => 'MOCK-CERTIFICATE-SERIAL-NO-2',
            ]
        ));

        $this->assertSame(202222222, $app->getMerchant()->getMerchantId());
        $this->assertSame($validator, $app->getValidator());
        $this->assertSame($server, $app->getServer());
        $this->assertSame($client, $app->getClient());
        $this->assertSame($httpClient, $app->getHttpClient());
    }
}
