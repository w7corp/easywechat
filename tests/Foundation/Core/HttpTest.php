<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Foundation\Core {
    use EasyWeChat\Applications\Base\Core\Http;
    use EasyWeChat\Tests\TestCase;
    use GuzzleHttp\Client;
    use GuzzleHttp\HandlerStack;
    use GuzzleHttp\Middleware;
    use GuzzleHttp\Psr7\Request;
    use GuzzleHttp\Psr7\Response;
    use Psr\Http\Message\RequestInterface;

    class HttpTest extends TestCase
    {
        public function testConstruct()
        {
            $http = new Http();

            $this->assertInstanceOf(Client::class, $http->getClient());
        }

        /**
         * Get guzzle mock client.
         *
         * @param null $expected
         *
         * @return GuzzleHttp\Client
         */
        public function getGuzzleWithResponse($expected = null)
        {
            $guzzle = \Mockery::mock(Client::class);
            $response = \Mockery::mock(Response::class.'[getBody]');

            $status = 200;
            $headers = ['X-Foo' => 'Bar'];
            $body = $expected;
            $protocol = '1.1';
            $response = new Response($status, $headers, $body, $protocol);

            $guzzle->shouldReceive('request')->andReturn($response);

            return $guzzle;
        }

        /**
         * Test request() with json response.
         */
        public function testRequestWithJsonResponse()
        {
            $http = new Http();
            $http->setClient($this->getGuzzleWithResponse(json_encode(['errcode' => '0', 'errmsg' => 'ok'])));
            $this->assertSame(['errcode' => '0', 'errmsg' => 'ok'], json_decode($http->request('http://overtrue.me', 'GET')->getBody(), true));

            $http->setClient($this->getGuzzleWithResponse(json_encode(['foo' => 'bar'])));

            $response = $http->request('http://overtrue.me', 'GET');

            $this->assertSame(json_encode(['foo' => 'bar']), $response->getBody());

            $http->setClient($this->getGuzzleWithResponse('non-json content'));
            $response = $http->request('http://overtrue.me', 'GET');

            $this->assertSame('non-json content', $response->getBody());
        }

        /**
         * Test parseJSON().
         */
        public function testParseJSON()
        {
            $http = new Http();
            $http->setClient($this->getGuzzleWithResponse('{"foo:"bar"}'));
            try {
                $http->parseJSON($http->request('http://overtrue.me', 'GET'));
                $this->assertFail('Invalid json body check fail.');
            } catch (\Exception $e) {
                $this->assertInstanceOf("\EasyWeChat\Exceptions\HttpException", $e);
            }

            $http->setClient($this->getGuzzleWithResponse('{"foo":"bar"}'));
            $this->assertSame(['foo' => 'bar'], $http->parseJSON($http->request('http://overtrue.me', 'GET')));

            $http = new Http();
            $http->setClient($this->getGuzzleWithResponse(''));
            $this->assertSame(null, $http->parseJSON($http->request('http://overtrue.me', 'GET')));
        }

        /**
         * Test get().
         */
        public function testGet()
        {
            $guzzle = \Mockery::mock(Client::class);
            $http = \Mockery::mock(Http::class.'[request]');
            $http->setClient($guzzle);

            $http->shouldReceive('request')->andReturnUsing(function ($url, $method, $body) {
                return compact('url', 'method', 'body');
            });

            $response = $http->get('http://easywechat.org', ['foo' => 'bar']);

            $this->assertSame('http://easywechat.org', $response['url']);
            $this->assertSame('GET', $response['method']);
            $this->assertSame(['query' => ['foo' => 'bar']], $response['body']);
        }

        /**
         * Test post().
         */
        public function testPost()
        {
            $guzzle = \Mockery::mock(Client::class);
            $http = \Mockery::mock(Http::class.'[request]');
            $http->setClient($guzzle);

            $http->shouldReceive('request')->andReturnUsing(function ($url, $method, $body) {
                return compact('url', 'method', 'body');
            });

            // array
            $response = $http->post('http://easywechat.org', ['foo' => 'bar']);

            $this->assertSame('http://easywechat.org', $response['url']);
            $this->assertSame('POST', $response['method']);
            $this->assertSame(['form_params' => ['foo' => 'bar']], $response['body']);

            // string
            $response = $http->post('http://easywechat.org', 'hello here.');

            $this->assertSame('http://easywechat.org', $response['url']);
            $this->assertSame('POST', $response['method']);
            $this->assertSame(['body' => 'hello here.'], $response['body']);
        }

        /**
         * Test json().
         */
        public function testJson()
        {
            $guzzle = \Mockery::mock(Client::class);
            $http = \Mockery::mock(Http::class.'[request]');
            $http->setClient($guzzle);

            $http->shouldReceive('request')->andReturnUsing(function ($url, $method, $body) {
                return compact('url', 'method', 'body');
            });

            $response = $http->json('http://easywechat.org', ['foo' => 'bar']);

            $this->assertSame('http://easywechat.org', $response['url']);
            $this->assertSame('POST', $response['method']);

            $this->assertSame([], $response['body']['query']);
            $this->assertSame(json_encode(['foo' => 'bar']), $response['body']['body']);
            $this->assertSame(['content-type' => 'application/json'], $response['body']['headers']);

            $response = $http->json('http://easywechat.org', ['foo' => 'bar'], JSON_UNESCAPED_UNICODE);

            $this->assertSame('http://easywechat.org', $response['url']);
            $this->assertSame('POST', $response['method']);

            $this->assertSame([], $response['body']['query']);
            $this->assertSame(json_encode(['foo' => 'bar']), $response['body']['body']);
            $this->assertSame(['content-type' => 'application/json'], $response['body']['headers']);
        }

        /**
         * Test upload().
         */
        public function testUpload()
        {
            $guzzle = \Mockery::mock(Client::class);
            $http = \Mockery::mock(Http::class.'[request]');
            $http->setClient($guzzle);

            $http->shouldReceive('request')->andReturnUsing(function ($url, $method, $body) {
                return compact('url', 'method', 'body');
            });

            $response = $http->upload('http://easywechat.org', ['foo' => 'bar', 'hello' => 'world'], ['overtrue' => 'easywechat']);

            $this->assertSame('http://easywechat.org', $response['url']);
            $this->assertSame('POST', $response['method']);
            $this->assertContains(['name' => 'overtrue', 'contents' => 'easywechat'], $response['body']['multipart']);
            $this->assertSame('foo', $response['body']['multipart'][0]['name']);
            $this->assertSame('bar', $response['body']['multipart'][0]['contents']);
            $this->assertSame('hello', $response['body']['multipart'][1]['name']);
            $this->assertSame('world', $response['body']['multipart'][1]['contents']);
        }

        public function testUserHandler()
        {
            $oldDefaultOptions = Http::getDefaultOptions();

            $statistics = [];
            Http::setDefaultOptions([
                'timeout' => 3,
                'handler' => Middleware::tap(function (RequestInterface $request) use (&$statistics) {
                    $api = $request->getUri()->getPath();
                    if (!isset($statistics[$api])) {
                        $statistics[$api] = 0;
                    }
                    ++$statistics[$api];
                }),
            ]);

            $httpClient = \Mockery::mock(Client::class);
            $httpClient->shouldReceive('request')->andReturnUsing(function ($method, $url, $options) {
                $request = new Request($method, $url);
                if (isset($options['handler']) && ($options['handler'] instanceof HandlerStack)) {
                    $options['handler']($request, $options);
                }

                return new Response();
            });

            $http = new Http();
            $http->setClient($httpClient);
            $http->request('http://overtrue.me/domain/action', 'GET');
            $this->assertSame(1, $statistics['/domain/action']);

            Http::setDefaultOptions($oldDefaultOptions);
        }
    }
}

namespace EasyWeChat\Applications\Base\Core {
    function fopen($file, $mode = 'r')
    {
        return $file;
    }
}
