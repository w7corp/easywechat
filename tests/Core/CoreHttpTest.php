<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace {
    use EasyWeChat\Core\Http;
    use GuzzleHttp\Client;
    use GuzzleHttp\Psr7\Response;

    class CoreHttpTest extends TestCase
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
            $guzzle = Mockery::mock(Client::class);
            $response = Mockery::mock(Response::class.'[getBody]');

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
            $this->assertEquals(['errcode' => '0', 'errmsg' => 'ok'], json_decode($http->request('http://overtrue.me', 'GET')->getBody(), true));

            $http->setClient($this->getGuzzleWithResponse(json_encode(['foo' => 'bar'])));

            $response = $http->request('http://overtrue.me', 'GET');

            $this->assertEquals(json_encode(['foo' => 'bar']), $response->getBody());

            $http->setClient($this->getGuzzleWithResponse('non-json content'));
            $response = $http->request('http://overtrue.me', 'GET');

            $this->assertEquals('non-json content', $response->getBody());
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
                $this->assertInstanceOf("\EasyWeChat\Core\Exceptions\HttpException", $e);
            }

            $http->setClient($this->getGuzzleWithResponse('{"foo":"bar"}'));
            $this->assertEquals(['foo' => 'bar'], $http->parseJSON($http->request('http://overtrue.me', 'GET')));

            $http = new Http();
            $http->setClient($this->getGuzzleWithResponse(''));
            $this->assertEquals(null, $http->parseJSON($http->request('http://overtrue.me', 'GET')));
        }

        /**
         * Test get().
         */
        public function testGet()
        {
            $guzzle = Mockery::mock(Client::class);
            $http = Mockery::mock(Http::class.'[request]');
            $http->setClient($guzzle);

            $http->shouldReceive('request')->andReturnUsing(function ($url, $method, $body) {
                return compact('url', 'method', 'body');
            });

            $response = $http->get('http://easywechat.org', ['foo' => 'bar']);

            $this->assertEquals('http://easywechat.org', $response['url']);
            $this->assertEquals('GET', $response['method']);
            $this->assertEquals(['query' => ['foo' => 'bar']], $response['body']);
        }

        /**
         * Test post().
         */
        public function testPost()
        {
            $guzzle = Mockery::mock(Client::class);
            $http = Mockery::mock(Http::class.'[request]');
            $http->setClient($guzzle);

            $http->shouldReceive('request')->andReturnUsing(function ($url, $method, $body) {
                return compact('url', 'method', 'body');
            });

            // array
            $response = $http->post('http://easywechat.org', ['foo' => 'bar']);

            $this->assertEquals('http://easywechat.org', $response['url']);
            $this->assertEquals('POST', $response['method']);
            $this->assertEquals(['form_params' => ['foo' => 'bar']], $response['body']);

            // string
            $response = $http->post('http://easywechat.org', 'hello here.');

            $this->assertEquals('http://easywechat.org', $response['url']);
            $this->assertEquals('POST', $response['method']);
            $this->assertEquals(['body' => 'hello here.'], $response['body']);
        }

        /**
         * Test json().
         */
        public function testJson()
        {
            $guzzle = Mockery::mock(Client::class);
            $http = Mockery::mock(Http::class.'[request]');
            $http->setClient($guzzle);

            $http->shouldReceive('request')->andReturnUsing(function ($url, $method, $body) {
                return compact('url', 'method', 'body');
            });

            $response = $http->json('http://easywechat.org', ['foo' => 'bar']);

            $this->assertEquals('http://easywechat.org', $response['url']);
            $this->assertEquals('POST', $response['method']);
            $this->assertEquals(['body' => json_encode(['foo' => 'bar']), 'headers' => ['content-type' => 'application/json']], $response['body']);

            $response = $http->json('http://easywechat.org', ['foo' => 'bar'], JSON_UNESCAPED_UNICODE);

            $this->assertEquals('http://easywechat.org', $response['url']);
            $this->assertEquals('POST', $response['method']);
            $this->assertEquals(['body' => json_encode(['foo' => 'bar'], JSON_UNESCAPED_UNICODE), 'headers' => ['content-type' => 'application/json']], $response['body']);
        }

        /**
         * Test upload().
         */
        public function testUpload()
        {
            $guzzle = Mockery::mock(Client::class);
            $http = Mockery::mock(Http::class.'[request]');
            $http->setClient($guzzle);

            $http->shouldReceive('request')->andReturnUsing(function ($url, $method, $body) {
                return compact('url', 'method', 'body');
            });

            $response = $http->upload('http://easywechat.org', ['foo' => 'bar', 'hello' => 'world'], ['overtrue' => 'easywechat']);

            $this->assertEquals('http://easywechat.org', $response['url']);
            $this->assertEquals('POST', $response['method']);
            $this->assertContains(['name' => 'overtrue', 'contents' => 'easywechat'], $response['body']['multipart']);
            $this->assertEquals('foo', $response['body']['multipart'][0]['name']);
            $this->assertEquals('bar', $response['body']['multipart'][0]['contents']);
            $this->assertEquals('hello', $response['body']['multipart'][1]['name']);
            $this->assertEquals('world', $response['body']['multipart'][1]['contents']);
        }
    }
}

namespace EasyWeChat\Core {
    function fopen($file, $mode = 'r')
    {
        return $file;
    }
}
