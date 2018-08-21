<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Kernel;

use EasyWeChat\Kernel\AccessToken;
use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Http\Response;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use Monolog\Logger;

class BaseClientTest extends TestCase
{
    public function makeClient($methods = [], ServiceContainer $app = null, AccessToken $accessToken = null)
    {
        $methods = implode(',', (array) $methods);

        return \Mockery::mock(BaseClient::class."[{$methods}]", [
            $app ?? \Mockery::mock(ServiceContainer::class),
            $accessToken ?? \Mockery::mock(AccessToken::class),
        ]);
    }

    public function testHttpGet()
    {
        $client = $this->makeClient('request');
        $url = 'http://easywechat.org';
        $query = ['foo' => 'bar'];
        $client->expects()->request($url, 'GET', ['query' => $query])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->httpGet($url, $query));
    }

    public function testHttpPost()
    {
        $client = $this->makeClient('request');
        $url = 'http://easywechat.org';

        $data = ['foo' => 'bar'];
        $client->expects()->request($url, 'POST', ['form_params' => $data])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->httpPost($url, $data));
    }

    public function testHttpPostJson()
    {
        $client = $this->makeClient('request');
        $url = 'http://easywechat.org';

        $data = ['foo' => 'bar'];
        $query = ['appid' => 1234];
        $client->expects()->request($url, 'POST', ['query' => $query, 'json' => $data])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->httpPostJson($url, $data, $query));
    }

    public function testHttpUpload()
    {
        $client = $this->makeClient('request');
        $url = 'http://easywechat.org';
        $path = STUBS_ROOT.'/files/image.jpg';
        $files = [
            'media' => $path,
        ];
        $form = [
            'type' => 'image',
        ];
        $query = ['appid' => 1234];
        $client->expects()->request($url, 'POST', \Mockery::on(function ($params) use ($query, $path) {
            $this->assertArrayHasKey('query', $params);
            $this->assertArrayHasKey('multipart', $params);
            $this->assertSame($query, $params['query']);
            $this->assertSame('media', $params['multipart'][0]['name']);
            $this->assertInternalType('resource', $params['multipart'][0]['contents']);

            return true;
        }))->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->httpUpload($url, $files, $form, $query));
    }

    public function testAccessToken()
    {
        $client = $this->makeClient();
        $this->assertInstanceOf(AccessToken::class, $client->getAccessToken());

        $accessToken = \Mockery::mock(AccessToken::class);
        $client->setAccessToken($accessToken);

        $this->assertSame($accessToken, $client->getAccessToken());
    }

    public function testRequest()
    {
        $url = 'http://easywechat.org';
        $app = new ServiceContainer([
            'response_type' => 'array',
        ]);
        $client = $this->makeClient(['registerHttpMiddlewares', 'performRequest'], $app)
            ->shouldAllowMockingProtectedMethods();

        // default value
        $client->expects()->registerHttpMiddlewares()->once();
        $client->expects()->performRequest($url, 'GET', [])->andReturn(new Response(200, [], '{"mock":"result"}'));
        $this->assertSame(['mock' => 'result'], $client->request($url));

        // return raw with custom arguments
        $options = ['foo' => 'bar'];
        $response = new Response(200, [], '{"mock":"result"}');
        $client->expects()->registerHttpMiddlewares()->once();
        $client->expects()->performRequest($url, 'POST', $options)->andReturn($response);
        $this->assertSame($response, $client->request($url, 'POST', $options, true));
    }

    public function testRequestRaw()
    {
        $url = 'http://easywechat.org';
        $response = new Response(200, [], '{"mock":"result"}');
        $client = $this->makeClient('request');
        $client->expects()->request($url, 'GET', [], true)->andReturn($response)->once();

        $this->assertInstanceOf(Response::class, $client->requestRaw($url));
    }

    public function testHttpClient()
    {
        // default
        $app = new ServiceContainer();
        $client = $this->makeClient('request', $app);
        $this->assertInstanceOf(Client::class, $client->getHttpClient());

        // custom client
        $http = new Client(['base_uri' => 'http://easywechat.com']);
        $app = new ServiceContainer([], [
            'http_client' => $http,
        ]);

        $client = $this->makeClient('request', $app);
        $this->assertSame($http, $client->getHttpClient());
    }

    public function testRegisterMiddlewares()
    {
        $client = $this->makeClient(['retryMiddleware', 'accessTokenMiddleware', 'logMiddleware', 'pushMiddleware'])
            ->shouldAllowMockingProtectedMethods()
            ->shouldDeferMissing();
        $retryMiddleware = function () {
            return 'retry';
        };
        $logMiddleware = function () {
            return 'log';
        };
        $accessTokenMiddleware = function () {
            return 'access_token';
        };
        $client->expects()->retryMiddleware()->andReturn($retryMiddleware)->once();
        $client->expects()->accessTokenMiddleware()->andReturn($accessTokenMiddleware)->once();
        $client->expects()->logMiddleware()->andReturn($logMiddleware)->once();
        $client->expects()->pushMiddleware($retryMiddleware, 'retry')->once();
        $client->expects()->pushMiddleware($accessTokenMiddleware, 'access_token')->once();
        $client->expects()->pushMiddleware($logMiddleware, 'log')->once();

        $client->registerHttpMiddlewares();
    }

    public function testAccessTokenMiddleware()
    {
        $app = new ServiceContainer([]);
        $accessToken = \Mockery::mock(AccessToken::class.'[applyToRequest]', [$app]);
        $client = $this->makeClient(['accessTokenMiddleware'], $app, $accessToken)
            ->shouldAllowMockingProtectedMethods()
            ->shouldDeferMissing();

        $func = $client->accessTokenMiddleware();

        $request = new Request('GET', 'http://easywechat.com');
        $options = ['foo' => 'bar'];
        $accessToken->expects()->applyToRequest($request, $options)->andReturn($request)->once();

        $middleware = $func(function ($request, $options) {
            return compact('request', 'options');
        });
        $result = $middleware($request, $options);

        $this->assertSame($request, $result['request']);
        $this->assertSame($options, $result['options']);
    }

    public function testLogMiddleware()
    {
        $app = new ServiceContainer([
            'http' => [
                'log_template',
            ],
        ]);
        $app['logger'] = new Logger('logger');
        $client = $this->makeClient(['accessTokenMiddleware'], $app)
            ->shouldAllowMockingProtectedMethods()
            ->shouldDeferMissing();

        $this->assertInstanceOf('Closure', $client->logMiddleware());
    }

    public function testRetryMiddleware()
    {
        // no retries configured
        $app = new ServiceContainer([]);
        $app['logger'] = $logger = \Mockery::mock(Logger::class, ['easywechat']);
        $accessToken = \Mockery::mock(AccessToken::class, [$app]);
        $client = $this->makeClient(['retryMiddleware'], $app, $accessToken)
            ->shouldAllowMockingProtectedMethods()
            ->shouldDeferMissing();

        $func = $client->retryMiddleware();

        // default once with right response
        $logger->expects()->debug('Retrying with refreshed access token.')->once();
        $accessToken->expects()->refresh()->once();
        $handler = new MockHandler([
            new Response(200, [], '{"errcode":40001}'),
            new Response(200, [], '{"success": true}'),
        ]);
        $handler = $func($handler);
        $c = new Client(['handler' => $handler]);
        $p = $c->sendAsync(new Request('GET', 'http://easywechat.com'), []);
        $response = $p->wait();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('{"success": true}', $response->getBody()->getContents());

        // default once with error response
        $logger->expects()->debug('Retrying with refreshed access token.')->once();
        $accessToken->expects()->refresh()->once();
        $handler = new MockHandler([
            new Response(200, [], '{"errcode":40001}'),
            new Response(200, [], '{"errcode":42001}'),
            new Response(200, [], '{"success": true}'),
        ]);
        $handler = $func($handler);
        $c = new Client(['handler' => $handler]);
        $p = $c->sendAsync(new Request('GET', 'http://easywechat.com'), []);
        $response = $p->wait();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('{"errcode":42001}', $response->getBody()->getContents());

        // default once with configured retries
        $app['config']['http'] = ['max_retries' => 0];
        $logger->expects()->debug('Retrying with refreshed access token.')->never();
        $accessToken->expects()->refresh()->never();
        $handler = new MockHandler([
            new Response(200, [], '{"errcode":40001}'),
            new Response(200, [], '{"errcode":42001}'),
            new Response(200, [], '{"success": true}'),
        ]);
        $handler = $func($handler);
        $c = new Client(['handler' => $handler]);
        $p = $c->sendAsync(new Request('GET', 'http://easywechat.com'), []);
        $response = $p->wait();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('{"errcode":40001}', $response->getBody()->getContents());

        // 3 times
        $app['config']['http'] = [
            'max_retries' => 3,
            'retry_delay' => 1,
        ];
        $logger->expects()->debug('Retrying with refreshed access token.')->times(3);
        $accessToken->expects()->refresh()->times(3);
        $handler = new MockHandler([
            new Response(200, [], '{"errcode":40001}'),
            new Response(200, [], '{"errcode":42001}'),
            new Response(200, [], '{"errcode":40001}'),
            new Response(200, [], '{"success":true}'),
        ]);
        $handler = $func($handler);
        $c = new Client(['handler' => $handler]);
        $s = microtime(true);
        $p = $c->sendAsync(new Request('GET', 'http://easywechat.com'), []);
        $response = $p->wait();

        $this->assertTrue(microtime(true) - $s >= 3 * ($app['config']['http']['retry_delay'] / 1000), 'delay time'); // times * delay
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('{"success":true}', $response->getBody()->getContents());
    }
}
