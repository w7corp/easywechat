<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MicroMerchant\Kernel;

use EasyWeChat\Kernel\Http\Response;
use EasyWeChat\Kernel\Support;
use EasyWeChat\MicroMerchant\Application;
use EasyWeChat\MicroMerchant\Certficates\Client as Certficates;
use EasyWeChat\MicroMerchant\Kernel\BaseClient;
use EasyWeChat\Tests\TestCase;

class BaseClientTest extends TestCase
{
    public function testRequest()
    {
        $app = new Application(['key' => '88888888888888888888888888888888']);

        $client = $this->mockApiClient(BaseClient::class, ['performRequest', 'castResponseToType'], $app)->shouldDeferMissing();

        $api = 'http://easywechat.org';
        $params = ['foo' => 'bar', 'nonce_str' => '112'];
        $method = \Mockery::anyOf(['get', 'post']);
        $options = ['foo' => 'bar'];

        $mockResponse = new Response(200, [], 'response-content');

        $client->expects()->performRequest($api, $method, \Mockery::on(function ($options) {
            $this->assertSame('bar', $options['foo']);
            $this->assertInternalType('string', $options['body']);

            $bodyInOptions = Support\XML::parse($options['body']);

            $this->assertSame($bodyInOptions['foo'], $options['foo']);
            $this->assertInternalType('string', $bodyInOptions['nonce_str']);
            $this->assertInternalType('string', $bodyInOptions['sign']);

            return true;
        }))->times(3)->andReturn($mockResponse);

        $client->expects()->castResponseToType()
            ->with($mockResponse, \Mockery::any())->times(3)
            ->andReturn(['foo' => 'mock-bar', 'return_code' => '1212']);

        // $returnResponse = false
        $this->assertSame(['foo' => 'mock-bar', 'return_code' => '1212'], $client->request($api, $params, $method, $options, false));

        // $returnResponse = true
        $this->assertInstanceOf(Response::class, $client->request($api, $params, $method, $options, true));
        $this->assertSame('response-content', $client->request($api, $params, $method, $options, true)->getBodyContents());
    }

    public function testRequestRaw()
    {
        $app = new Application();

        $client = $this->mockApiClient(BaseClient::class, ['request', 'requestRaw'], $app)->makePartial();

        $api = 'http://easywechat.org';
        $params = ['foo' => 'bar'];
        $method = \Mockery::anyOf(['get', 'post']);
        $options = [];

        $client->expects()->request($api, $params, $method, $options, true)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->requestRaw($api, $params, $method, $options));
    }

    public function testSafeRequest()
    {
        $app = new Application([
            'app_id' => 'wx123456',
            'cert_path' => 'foo',
            'key_path' => 'bar',
        ]);

        $client = $this->mockApiClient(BaseClient::class, ['safeRequest'], $app)->makePartial();

        $api = 'http://easywechat.org';
        $params = ['foo' => 'bar'];
        $method = \Mockery::anyOf(['get', 'post']);

        $client->expects()->request($api, $params, $method, \Mockery::on(function ($options) use ($app) {
            $this->assertSame($options['cert'], $app['config']->get('cert_path'));
            $this->assertSame($options['ssl_key'], $app['config']->get('key_path'));

            return true;
        }))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->safeRequest($api, $params, $method));
    }

    /**
     * @dataProvider bodySignProvider
     */
    public function testBodySign($signType, $nonceStr, $sign)
    {
        $app = new Application([
            'key' => '88888888888888888888888888888888',
        ]);

        $client = $this->mockApiClient(BaseClient::class, ['performRequest'], $app)->shouldDeferMissing();

        $api = 'http://easywechat.org';
        $params = [
            'foo' => 'bar',
            'nonce_str' => $nonceStr,
            'sign_type' => $signType,
        ];
        $method = \Mockery::anyOf(['get', 'post']);
        $options = [];

        $mockResponse = new Response(200, [], 'response-content');

        $client->expects()->performRequest($api, $method, \Mockery::on(function ($options) use ($sign) {
            $bodyInOptions = Support\XML::parse($options['body']);

            $this->assertSame($sign, $bodyInOptions['sign']);

            return true;
        }))->andReturn($mockResponse);

        $this->assertSame('response-content', $client->requestRaw($api, $params, $method, $options)->getBodyContents());
    }

    public function bodySignProvider()
    {
        return [
            ['', '5c3bfd3227348', '82125D68D3C25B2B78D53F66E12EC89A'],
            ['MD5', '5c3bfe0343bab', 'A9237F1A2DF77FF900CFFB7B432CD1A9'],
            ['HMAC-SHA256', '5c3bfe6716023', 'A890BD78E9B1563C546D07F21E8C8D96B146CFE5B18941C312678B5636263DE6'],
        ];
    }

    public function testProcessParams()
    {
        $app = new Application([
            'mch_id' => 'mock-mch_id',
            'key' => '88888888888888888888888888888888',
        ]);

        $client = $this->mockApiClient(BaseClient::class, ['performRequest'], $app)->shouldDeferMissing();
        $this->setCache();
        $this->assertArrayHasKey('cert_sn', $client->processParams(['email' => 'dddd']));
        $this->clearCache();
    }

    public function setCache()
    {
        $app = new Application(['mch_id' => 'mock-mch_id']);
        $client = $this->mockApiClient(Certficates::class, [], $app);
        $certificates = [
            'serial_no' => '121*******************************3835AA',
            'effective_time' => '2018-08-21 16:38:49',
            'expire_time' => '2023-08-20 16:38:49',
            'certificates' => '-----BEGIN CERTIFICATE-----
MIIEaTCCA1GgAwIBAgILBAAAAAABRE7wQkcwDQYJKoZIhvcNAQELBQAwVzELMAkG
A1UEBhMCQkUxGTAXBgNVBAoTEEdsb2JhbFNpZ24gbnYtc2ExEDAOBgNVBAsTB1Jv
b3QgQ0ExGzAZBgNVBAMTEkdsb2JhbFNpZ24gUm9vdCBDQTAeFw0xNDAyMjAxMDAw
MDBaFw0yNDAyMjAxMDAwMDBaMGYxCzAJBgNVBAYTAkJFMRkwFwYDVQQKExBHbG9i
YWxTaWduIG52LXNhMTwwOgYDVQQDEzNHbG9iYWxTaWduIE9yZ2FuaXphdGlvbiBW
YWxpZGF0aW9uIENBIC0gU0hBMjU2IC0gRzIwggEiMA0GCSqGSIb3DQEBAQUAA4IB
DwAwggEKAoIBAQDHDmw/I5N/zHClnSDDDlM/fsBOwphJykfVI+8DNIV0yKMCLkZc
C33JiJ1Pi/D4nGyMVTXbv/Kz6vvjVudKRtkTIso21ZvBqOOWQ5PyDLzm+ebomchj
SHh/VzZpGhkdWtHUfcKc1H/hgBKueuqI6lfYygoKOhJJomIZeg0k9zfrtHOSewUj
mxK1zusp36QUArkBpdSmnENkiN74fv7j9R7l/tyjqORmMdlMJekYuYlZCa7pnRxt
Nw9KHjUgKOKv1CGLAcRFrW4rY6uSa2EKTSDtc7p8zv4WtdufgPDWi2zZCHlKT3hl
2pK8vjX5s8T5J4BO/5ZS5gIg4Qdz6V0rvbLxAgMBAAGjggElMIIBITAOBgNVHQ8B
Af8EBAMCAQYwEgYDVR0TAQH/BAgwBgEB/wIBADAdBgNVHQ4EFgQUlt5h8b0cFilT
HMDMfTuDAEDmGnwwRwYDVR0gBEAwPjA8BgRVHSAAMDQwMgYIKwYBBQUHAgEWJmh0
dHBzOi8vd3d3Lmdsb2JhbHNpZ24uY29tL3JlcG9zaXRvcnkvMDMGA1UdHwQsMCow
KKAmoCSGImh0dHA6Ly9jcmwuZ2xvYmFsc2lnbi5uZXQvcm9vdC5jcmwwPQYIKwYB
BQUHAQEEMTAvMC0GCCsGAQUFBzABhiFodHRwOi8vb2NzcC5nbG9iYWxzaWduLmNv
bS9yb290cjEwHwYDVR0jBBgwFoAUYHtmGkUNl8qJUC99BM00qP/8/UswDQYJKoZI
hvcNAQELBQADggEBAEYq7l69rgFgNzERhnF0tkZJyBAW/i9iIxerH4f4gu3K3w4s
32R1juUYcqeMOovJrKV3UPfvnqTgoI8UV6MqX+x+bRDmuo2wCId2Dkyy2VG7EQLy
XN0cvfNVlg/UBsD84iOKJHDTu/B5GqdhcIOKrwbFINihY9Bsrk8y1658GEV1BSl3
30JAZGSGvip2CTFvHST0mdCF/vIhCPnG9vHQWe3WVjwIKANnuvD58ZAWR65n5ryA
SOlCdjSXVWkkDoPWoC209fN5ikkodBpBocLTJIg1MGCUF7ThBCIxPTsvFwayuJ2G
K1pp74P1S8SqtCr4fKGxhZSM9AyHDPSsQPhZSZg=
-----END CERTIFICATE-----',
        ];
        $client->getCache()->set('mock-mch_id_micro_certificates', $certificates);

        $this->assertSame($certificates, $client->get());
    }

    public function clearCache()
    {
        $app = new Application(['mch_id' => 'mock-mch_id']);
        $client = $this->mockApiClient(Certficates::class, [], $app);
        $client->getCache()->delete('mock-mch_id_micro_certificates');

        $this->assertSame(null, $client->getCache()->get('mock-mch_id_micro_certificates'));
    }
}
