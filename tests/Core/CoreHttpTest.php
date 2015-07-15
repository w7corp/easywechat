<?php

namespace {

use Mockery as m;
use EasyWeChat\Core\Http;
use EasyWeChat\Support\Http as HttpClient;

class CoreHttpTest extends TestCase
{
    /**
     * Test setToken().
     */
    public function testConstruct()
    {
        $http = new Http();
        $this->assertEquals(null, $http->getToken());

        $token = m::mock('EasyWeChat\Core\AccessToken');
        $http = new Http($token);
        $this->assertEquals($token, $http->getToken());
    }

    /**
     * Test setExpectedException().
     */
    public function testSetExpectedException()
    {
        $http = new Http();

        $this->assertEquals('EasyWeChat\Core\Exceptions\HttpException', $http->getExpectedException());

        $exception = m::namedMock('FooException', 'Exception');
        $http->setExpectedException($exception);
        $this->assertEquals(get_class($exception), $http->getExpectedException());
    }

    /**
     * Test request() with json response.
     */
    public function testRequestWithJsonResponse()
    {
        HttpClient::$response = [
            'content_type' => 'application/json;charset=utf8;',
            'data' => json_encode(['errcode' => '0', 'errmsg' => 'ok']),
        ];

        $http = new Http();
        $this->assertTrue($http->request('http://overtrue.me', 'GET'));

        HttpClient::$response = [
            'content_type' => 'text/json',
            'data' => json_encode(['errcode' => '0', 'errmsg' => 'ok']),
        ];

        $http = new Http();
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
        HttpClient::$response = [
            'content_type' => 'application/json;charset=utf8;',
            'data' => json_encode(['errcode' => '40010', 'errmsg' => '不合法的语音文件大小']),
        ];

        $http = new Http();
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
        HttpClient::$response = [
            'content_type' => 'application/json;charset=utf8;',
            'data' => json_encode(['errcode' => '40010', 'errmsg' => '不合法的语音文件大小']),
        ];

        $http = new Http();
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
        HttpClient::$response = [];

        $http = new Http();
        $http->request('http://overtrue.me', 'GET');
    }
}

class OvertrueException extends Exception
{
}

}

namespace EasyWeChat\Support {
    class Http
    {
        public static $request = [];
        public static $response = [];

        const GET = 'GET';
        const POST = 'POST';
        const PUT = 'PUT';
        const PATCH = 'PATCH';
        const DELETE = 'DELETE';

        public function __construct()
        {
            # code...
        }

        public function request($url, $method = self::GET, $params = [], $options = [])
        {
            self::$request = func_get_args();

            return self::$response;
        }
    }
}
