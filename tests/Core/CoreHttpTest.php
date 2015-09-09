<?php

namespace {
    use EasyWeChat\Core\AccessToken;
    use EasyWeChat\Core\Http;
    use GuzzleHttp\Client;
    use GuzzleHttp\Psr7\Response;
    use EasyWeChat\Core\Exceptions\HttpException;

    class CoreHttpTest extends TestCase
    {
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
            $response = Mockery::mock(Response::class);
            $response->shouldReceive('getBody')->andReturn($expected);
            $guzzle->shouldReceive('request')->andReturn($response);

            return $guzzle;
        }
        /**
         * Test setToken().
         */
        public function testConstruct()
        {
            $http = new Http($this->getGuzzleWithResponse());
            $this->assertEquals(null, $http->getToken());

            $token = Mockery::mock(AccessToken::class);
            $http = new Http($this->getGuzzleWithResponse(), $token);
            $this->assertEquals($token, $http->getToken());
        }

        /**
         * Test setExpectedException().
         *
         * @expectedException \EasyWeChat\Core\Exceptions\InvalidArgumentException
         */
        public function testSetExpectedException()
        {
            $http = new Http($this->getGuzzleWithResponse());

            $this->assertEquals(HttpException::class, $http->getExpectedException());

            $exception = Mockery::namedMock('FooException', 'Exception');
            $http->setExpectedException($exception);
            $this->assertEquals(get_class($exception), $http->getExpectedException());

            $http->setExpectedException('NotExistsException');
        }

        /**
         * Test request() with json response.
         */
        public function testRequestWithJsonResponse()
        {
            $http = new Http($this->getGuzzleWithResponse(json_encode(['errcode' => '0', 'errmsg' => 'ok'])));
            $this->assertTrue($http->request('http://overtrue.me', 'GET'));

            $http = new Http($this->getGuzzleWithResponse(json_encode(['errcode' => '0', 'errmsg' => 'ok'])));
            $this->assertTrue($http->request('http://overtrue.me', 'GET'));
        }

        /**
         * Test request() with error response.
         *
         * @expectedExceptionCode 40010
         * @expectedException EasyWeChat\Core\Exceptions\HttpException
         */
        public function testRequestWithErrorResponse()
        {
            $http = new Http($this->getGuzzleWithResponse(json_encode(['errcode' => '40010', 'errmsg' => '不合法的语音文件大小'])));
            $http->request('http://overtrue.me', 'GET');
        }

        /**
         * Test request() with error response.
         *
         * @expectedExceptionCode 40010
         * @expectedExceptionMessage Unknow
         * @expectedException EasyWeChat\Core\Exceptions\HttpException
         */
        public function testRequestWithoutErrorMessage()
        {
            $http = new Http($this->getGuzzleWithResponse(json_encode(['errcode' => '40010'])));
            $http->request('http://overtrue.me', 'GET');
        }

        /**
         * Test request() with error response.
         *
         * @expectedExceptionCode 40010
         * @expectedException OvertrueException
         */
        public function testResponseExpectedException()
        {
            $http = new Http($this->getGuzzleWithResponse(json_encode(['errcode' => '40010', 'errmsg' => '不合法的语音文件大小'])));
            $http->setExpectedException('OvertrueException');
            $http->request('http://overtrue.me', 'GET');
        }

        /**
         * Test request() with error response.
         *
         * @expectedExceptionCode -1
         * @expectedException EasyWeChat\Core\Exceptions\HttpException
         */
        public function testRequestWithoutResponse()
        {
            $http = new Http($this->getGuzzleWithResponse());
            $http->request('http://overtrue.me', 'GET');
        }

        /**
         * Test request() with token.
         */
        public function testRequestWithToken()
        {
            $http = new Http($this->getGuzzleWithResponse(json_encode(['foo' => 'bar'])));
            $token = \Mockery::mock(EasyWeChat\Core\AccessToken::class);
            $http->setToken($token);

            $response = $http->request('http://overtrue.me', 'GET');

            $this->assertEquals(['foo' => 'bar'], $response);

            $http = new Http($this->getGuzzleWithResponse('non-json content'));
            $response = $http->request('http://overtrue.me', 'GET');

            $this->assertEquals('non-json content', $response);
        }

        /**
         * Test get()
         */
        public function testGet()
        {
            $guzzle = Mockery::mock(Client::class);
            $http = Mockery::mock(Http::class.'[request]', [$guzzle]);

            $http->shouldReceive('request')->andReturnUsing(function($url, $method, $body){
                return compact('url', 'method', 'body');
            });

            $response = $http->get('http://easywechat.org', ['foo' => 'bar']);

            $this->assertEquals('http://easywechat.org', $response['url']);
            $this->assertEquals('GET', $response['method']);
            $this->assertEquals(['query' => ['foo' => 'bar']], $response['body']);
        }

        /**
         * Test post()
         */
        public function testPost()
        {
            $guzzle = Mockery::mock(Client::class);
            $http = Mockery::mock(Http::class.'[request]', [$guzzle]);

            $http->shouldReceive('request')->andReturnUsing(function($url, $method, $body){
                return compact('url', 'method', 'body');
            });

            $response = $http->post('http://easywechat.org', ['foo' => 'bar']);

            $this->assertEquals('http://easywechat.org', $response['url']);
            $this->assertEquals('POST', $response['method']);
            $this->assertEquals(['body' => ['foo' => 'bar']], $response['body']);
        }

        /**
         * Test json()
         */
        public function testJson()
        {
            $guzzle = Mockery::mock(Client::class);
            $http = Mockery::mock(Http::class.'[request]', [$guzzle]);

            $http->shouldReceive('request')->andReturnUsing(function($url, $method, $body){
                return compact('url', 'method', 'body');
            });

            $response = $http->json('http://easywechat.org', ['foo' => 'bar']);

            $this->assertEquals('http://easywechat.org', $response['url']);
            $this->assertEquals('POST', $response['method']);
            $this->assertEquals(['json' => ['foo' => 'bar']], $response['body']);
        }

        /**
         * Test upload()
         */
        public function testUpload()
        {
            $guzzle = Mockery::mock(Client::class);
            $http = Mockery::mock(Http::class.'[request]', [$guzzle]);

            $http->shouldReceive('request')->andReturnUsing(function($url, $method, $body){
                return compact('url', 'method', 'body');
            });

            $response = $http->upload('http://easywechat.org', ['foo' => 'bar', 'hello' => 'world'], ['overtrue' => 'easywechat']);

            $this->assertEquals('http://easywechat.org', $response['url']);
            $this->assertEquals('POST', $response['method']);
            $this->assertEquals(['overtrue' => 'easywechat'], $response['body']['form_params']);
            $this->assertEquals('foo', $response['body']['multipart'][0]['name']);
            $this->assertEquals('bar', $response['body']['multipart'][0]['contents']);
            $this->assertEquals('hello', $response['body']['multipart'][1]['name']);
            $this->assertEquals('world', $response['body']['multipart'][1]['contents']);
        }
    }

    class OvertrueException extends Exception
    {
    }
}

namespace EasyWeChat\Core {
    function fopen($file, $mode = 'r') {
        return $file;
    }
}
