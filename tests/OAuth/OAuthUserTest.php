<?php

use EasyWeChat\Core\Http;
use EasyWeChat\OAuth\Client;
use EasyWeChat\OAuth\User;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Core\Exceptions\RuntimeException;
use EasyWeChat\Support\Collection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OAuthClientTest extends TestCase
{
    /**
     * Mock http.
     *
     * @return Http
     */
    public function getHttp()
    {
        $http = Mockery::mock(Http::class);
        $http->shouldReceive('setExpectedException')->andReturn($http);

        return $http;
    }

    /**
     * Test redirect()
     */
    public function testRedirect()
    {
        $request = Mockery::mock(Request::class);
        $session = Mockery::mock(SessionInterface::class);
        $session->shouldReceive('set')->andReturn(true);
        $request->shouldReceive('getSession')->andReturn($session);

        $client = new Client('foo', 'bar', $request, $this->getHttp());

        $response = $client->redirect('http://easywechat.org');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $uri = parse_url($response->getTargetUrl());
         parse_str($uri['query'], $queries);

        $this->assertEquals(Client::AUTHORIZE_URL, $uri['scheme']."://".$uri['host'].$uri['path']);
        $this->assertEquals('wechat_redirect', $uri['fragment']);
        $this->assertEquals('foo', $queries['appid']);
        $this->assertEquals('http://easywechat.org', $queries['redirect_uri']);
        $this->assertEquals('snsapi_userinfo', $queries['scope']);
        $this->assertEquals('code', $queries['response_type']);
        $this->assertEquals(40, strlen($queries['state']));

        try {
            $response = $client->redirect('http://easywechat.org', 'error_scope');
        } catch (Exception $e) {

        }

        $this->assertInstanceOf(InvalidArgumentException::class, $e);
        $this->assertEquals("Invalid oauth scope:'error_scope'", $e->getMessage());
    }

    /**
     * Test silentRedirect()
     */
    public function testSilentRedirect()
    {
        $request = Mockery::mock(Request::class);
        $session = Mockery::mock(SessionInterface::class);
        $session->shouldReceive('set')->andReturn(true);
        $request->shouldReceive('getSession')->andReturn($session);

        $client = new Client('foo', 'bar', $request, $this->getHttp());

        $response = $client->silentRedirect('http://easywechat.org');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $uri = parse_url($response->getTargetUrl());
         parse_str($uri['query'], $queries);

        $this->assertEquals(Client::AUTHORIZE_URL, $uri['scheme']."://".$uri['host'].$uri['path']);
        $this->assertEquals('wechat_redirect', $uri['fragment']);
        $this->assertEquals('foo', $queries['appid']);
        $this->assertEquals('http://easywechat.org', $queries['redirect_uri']);
        $this->assertEquals('snsapi_base', $queries['scope']);
        $this->assertEquals('code', $queries['response_type']);
        $this->assertEquals(40, strlen($queries['state']));
    }

    /**
     * Test user()
     */
    public function testUser()
    {
        // make get('state') === input('state')
        $request = Mockery::mock(Request::class);
        $session = Mockery::mock(SessionInterface::class);
        $session->shouldReceive('get')->andReturn('state_mock_value');
        $request->shouldReceive('getSession')->andReturn($session);
        $request->shouldReceive('get')->twice()->andReturnValues(['state_mock_not_equals', 'state_mock_value']);

        $http = $this->getHttp();

        $client = Mockery::mock(Client::class.'[getUserByAccessToken,getAccessToken]', ['foo', 'bar', $request, $http]);

        try {
            $client->user();
        } catch (Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertEquals('Invalid state.', $e->getMessage());
        }


        $client->shouldReceive('getAccessToken')->andReturn([
                'access_token' => 'test_access_token',
                'refresh_token' => 'test_refresh_token',
                'openid' => 'foo',
            ]);
        // mock getUserByAccessToken();
        $client->shouldReceive('getUserByAccessToken')->andReturn([
            'openid' => 'foo',
            'nickname' => 'overtrue',
        ]);

        $response = $client->user();

        $this->assertEquals('foo', $response->getOpenId());
        $this->assertEquals('test_access_token', $response->getToken());
        $this->assertEquals('test_refresh_token', $response->getRefreshToken());
    }

    /**
     * Test getAccessToken()
     */
    public function testGetAccessToken()
    {
        $http = $this->getHttp();
        $http->shouldReceive('get')->andReturnUsing(function($api, $params){
            return compact('api', 'params');
        });
        $client = new Client('foo', 'bar', Mockery::mock(Request::class), $http);

        $response = $client->getAccessToken('test_code');

        $this->assertEquals(Client::ACCESS_TOKEN_URL, $response['api']);
        $this->assertEquals('foo', $response['params']['appid']);
        $this->assertEquals('bar', $response['params']['secret']);
        $this->assertEquals('test_code', $response['params']['code']);
        $this->assertEquals('authorization_code', $response['params']['grant_type']);
    }

    /**
     * Test refresh()
     */
    public function testRefresh()
    {
        $http = $this->getHttp();
        $http->shouldReceive('get')->andReturnUsing(function($api, $params){
            return compact('api', 'params');
        });
        $client = new Client('foo', 'bar', Mockery::mock(Request::class), $http);

        $response = $client->refresh('test_refresh_code');

        $this->assertEquals(Client::TOKEN_REFRESH_URL, $response['api']);
        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals('foo', $response['params']['appid']);
        $this->assertEquals('test_refresh_code', $response['params']['refresh_token']);
        $this->assertEquals('refresh_token', $response['params']['grant_type']);
    }

    /**
     * Test getUserByAccessToken()
     */
    public function testGetUserByAccessToken()
    {
        $http = $this->getHttp();
        $http->shouldReceive('get')->andReturnUsing(function($api, $params){
            return compact('api', 'params');
        });
        $client = new Client('foo', 'bar', Mockery::mock(Request::class), $http);

        $response = $client->getUserByAccessToken('test_openid', 'test_access_token');

        $this->assertEquals(Client::USER_URL, $response['api']);
        $this->assertEquals('test_openid', $response['params']['openid']);
        $this->assertEquals('test_access_token', $response['params']['access_token']);
        $this->assertEquals('zh_CN', $response['params']['lang']);
    }
}