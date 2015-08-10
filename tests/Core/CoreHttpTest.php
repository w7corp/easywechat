<?php

use EasyWeChat\Core\Http;

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
        $guzzle = Mockery::mock('GuzzleHttp\Client');
        $response = Mockery::mock('GuzzleHttp\Psr7\Response');
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

        $token = Mockery::mock('EasyWeChat\Core\AccessToken');
        $http = new Http($this->getGuzzleWithResponse(), $token);
        $this->assertEquals($token, $http->getToken());
    }

    /**
     * Test setExpectedException().
     *
     * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function testSetExpectedException()
    {
        $http = new Http($this->getGuzzleWithResponse());

        $this->assertEquals('EasyWeChat\Core\Exceptions\HttpException', $http->getExpectedException());

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
        $token = \Mockery::mock('EasyWeChat\Core\AccessToken');
        $http->setToken($token);

        $response = $http->request('http://overtrue.me', 'GET');

        $this->assertEquals(['foo' => 'bar'], $response);

        $http = new Http($this->getGuzzleWithResponse('non-json content'));
        $response = $http->request('http://overtrue.me', 'GET');

        $this->assertEquals('non-json content', $response);
    }
}

class OvertrueException extends Exception
{
}

