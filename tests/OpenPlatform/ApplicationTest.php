<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OpenPlatform;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\MiniApp\Application as MiniAppApplication;
use EasyWeChat\OfficialAccount\Application as OfficialAccountApplication;
use EasyWeChat\OpenPlatform\Account;
use EasyWeChat\OpenPlatform\Account as AccountInterface;
use EasyWeChat\OpenPlatform\Application;
use EasyWeChat\OpenPlatform\Authorization;
use EasyWeChat\OpenPlatform\AuthorizerAccessToken;
use EasyWeChat\OpenPlatform\ComponentAccessToken;
use EasyWeChat\OpenPlatform\Contracts\Application as ApplicationInterface;
use EasyWeChat\OpenPlatform\Contracts\VerifyTicket as VerifyTicketInterface;
use EasyWeChat\OpenPlatform\Server;
use EasyWeChat\OpenPlatform\VerifyTicket;
use EasyWeChat\Tests\TestCase;
use Overtrue\Socialite\Providers\WeChat;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ApplicationTest extends TestCase
{
    public function test_get_and_set_account()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'mock-aes_key',
        ]);

        $this->assertInstanceOf(ApplicationInterface::class, $app);
        $this->assertInstanceOf(AccountInterface::class, $app->getAccount());
        $this->assertSame($app->getAccount(), $app->getAccount());

        // set
        $account = new Account(appId: 'wx3cf0f39249000060', secret: 'mock-secret', token: 'mock-token', aesKey: 'mock-aes_key');
        $app->setAccount($account);
        $this->assertSame($account, $app->getAccount());
    }

    public function test_get_and_set_encryptor()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $this->assertInstanceOf(Encryptor::class, $app->getEncryptor());
        $this->assertSame($app->getEncryptor(), $app->getEncryptor());

        // set
        $encryptor = new Encryptor(
            appId: 'wx3cf0f39249000060',
            token: 'mock-token',
            aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG'
        );
        $app->setEncryptor($encryptor);
        $this->assertSame($encryptor, $app->getEncryptor());
    }

    public function test_get_and_set_server()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $this->assertInstanceOf(ServerInterface::class, $app->getServer());
        $this->assertSame($app->getServer(), $app->getServer());

        // set
        $server = new Server(
            encryptor: \Mockery::mock(Encryptor::class),
            request: \Mockery::mock(ServerRequestInterface::class)
        );
        $app->setServer($server);
        $this->assertSame($server, $app->getServer());
    }

    public function test_get_and_set_component_access_token()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $this->assertInstanceOf(AccessTokenInterface::class, $app->getAccessToken());
        // set
        $accessToken = new ComponentAccessToken('wx3cf0f39249000060', 'mock-secret', $app->getVerifyTicket());
        $app->setComponentAccessToken($accessToken);
        $this->assertSame($accessToken, $app->getAccessToken());
    }

    public function test_get_and_set_verify_ticket()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $this->assertInstanceOf(VerifyTicketInterface::class, $app->getVerifyTicket());

        // set
        $verifyTicket = new VerifyTicket('wx3cf0f39249000060');
        $app->setVerifyTicket($verifyTicket);
        $this->assertSame($verifyTicket, $app->getVerifyTicket());
    }

    public function test_get_authorization()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $verifyTicket = new VerifyTicket('wx3cf0f39249000060');
        $verifyTicket->setTicket('mock-verify-ticket');
        $app->setVerifyTicket($verifyTicket);
        $tokenResponse = [
            'component_access_token' => 'mock-access-token',
            'expires_in' => 2700,
        ];
        // token http client
        $mockTokenResponse = new MockResponse(\json_encode($tokenResponse));
        $tokenHttpClient = new MockHttpClient($mockTokenResponse, 'https://api.weixin.qq.com/');
        $token = new ComponentAccessToken('wx3cf0f39249000060', 'mock-secret', $app->getVerifyTicket(), httpClient: $tokenHttpClient);
        $app->setComponentAccessToken($token);
        $mockResponse = new MockResponse(
            \json_encode([
                'authorization_info' => ['authorizer_appid' => 'mock-appid'],
            ]),
            [
                'http_code' => 201,
                'response_headers' => ['Content-Type: application/json'],
            ]
        );
        $httpClient = new MockHttpClient($mockResponse, 'https://api.weixin.qq.com/');
        $app->setHttpClient($httpClient);
        $this->assertInstanceOf(Authorization::class, $app->getAuthorization('mock-auth-code'));

        $this->assertSame('POST', $mockResponse->getRequestMethod());
        $this->assertSame(
            'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=mock-access-token',
            $mockResponse->getRequestUrl()
        );
        $this->assertSame(
            \json_encode([
                'component_appid' => 'wx3cf0f39249000060',
                'authorization_code' => 'mock-auth-code',
            ]),
            $mockResponse->getRequestOptions()['body']
        );
    }

    public function test_get_authorization_exception()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $verifyTicket = new VerifyTicket('wx3cf0f39249000060');
        $verifyTicket->setTicket('mock-verify-ticket');
        $app->setVerifyTicket($verifyTicket);
        $tokenResponse = [
            'component_access_token' => 'mock-access-token',
            'expires_in' => 2700,
        ];
        // token http client
        $mockTokenResponse = new MockResponse(\json_encode($tokenResponse));
        $tokenHttpClient = new MockHttpClient($mockTokenResponse, 'https://api.weixin.qq.com/');
        $token = new ComponentAccessToken('wx3cf0f39249000060', 'mock-secret', $app->getVerifyTicket(), httpClient: $tokenHttpClient);
        $app->setComponentAccessToken($token);

        // exception
        $mockResponse = new MockResponse(
            \json_encode([
                'error_code' => 100029,
            ]),
            [
                'http_code' => 201,
                'response_headers' => ['Content-Type: application/json'],
            ]
        );
        $httpClient = new MockHttpClient($mockResponse, 'https://api.weixin.qq.com/');
        $app->setHttpClient($httpClient);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Failed to get authorization_info: {"error_code":100029}');
        $app->getAuthorization('mock-auth-code');
    }

    public function test_refresh_authorizer_token()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $verifyTicket = new VerifyTicket('wx3cf0f39249000060');
        $verifyTicket->setTicket('mock-verify-ticket');
        $app->setVerifyTicket($verifyTicket);

        $tokenResponse = [
            'component_access_token' => 'mock-access-token',
            'expires_in' => 2700,
        ];
        // token http client
        $mockTokenResponse = new MockResponse(\json_encode($tokenResponse));
        $tokenHttpClient = new MockHttpClient($mockTokenResponse, 'https://api.weixin.qq.com/');
        $token = new ComponentAccessToken('wx3cf0f39249000060', 'mock-secret', $app->getVerifyTicket(), httpClient: $tokenHttpClient);

        $app->setComponentAccessToken($token);
        $mockResponse = new MockResponse(
            \json_encode([
                'authorizer_access_token' => 'mock-access-token',
            ]),
            [
                'http_code' => 201,
                'response_headers' => ['Content-Type: application/json'],
            ]
        );
        $httpClient = new MockHttpClient($mockResponse, 'https://api.weixin.qq.com/');
        $app->setHttpClient($httpClient);
        $this->assertSame([
            'authorizer_access_token' => 'mock-access-token',
        ], $app->refreshAuthorizerToken('mock-authorizer-appid', 'mock-refresh-token'));

        $this->assertSame('POST', $mockResponse->getRequestMethod());
        $this->assertSame(
            'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=mock-access-token',
            $mockResponse->getRequestUrl()
        );
        $this->assertSame(
            \json_encode([
                'component_appid' => 'wx3cf0f39249000060',
                'authorizer_appid' => 'mock-authorizer-appid',
                'authorizer_refresh_token' => 'mock-refresh-token',
            ]),
            $mockResponse->getRequestOptions()['body']
        );
    }

    public function test_refresh_authorizer_token_exception()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $verifyTicket = new VerifyTicket('wx3cf0f39249000060');
        $verifyTicket->setTicket('mock-verify-ticket');
        $app->setVerifyTicket($verifyTicket);

        $tokenResponse = [
            'component_access_token' => 'mock-access-token',
            'expires_in' => 2700,
        ];
        // token http client
        $mockTokenResponse = new MockResponse(\json_encode($tokenResponse));
        $tokenHttpClient = new MockHttpClient($mockTokenResponse, 'https://api.weixin.qq.com/');

        $token = new ComponentAccessToken('wx3cf0f39249000060', 'mock-secret', $app->getVerifyTicket(), httpClient: $tokenHttpClient);

        $app->setComponentAccessToken($token);

        // exception
        $mockResponse = new MockResponse(
            \json_encode([
                'error_code' => 100029,
            ]),
            [
                'http_code' => 201,
                'response_headers' => ['Content-Type: application/json'],
            ]
        );
        $httpClient = new MockHttpClient($mockResponse, 'https://api.weixin.qq.com/');
        $app->setHttpClient($httpClient);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Failed to get authorizer_access_token: {"error_code":100029}');
        $app->refreshAuthorizerToken('mock-authorizer-appid', 'mock-refresh-token');
    }

    public function test_get_oauth()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $this->assertInstanceOf(WeChat::class, $app->getOAuth());
    }

    public function test_get_official_account()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $this->assertInstanceOf(
            OfficialAccountApplication::class,
            $app->getOfficialAccount(new AuthorizerAccessToken('mock-app-id', 'mock-access-token'), [
                'secret' => 'mock-secret',
            ])
        );
    }

    public function test_get_mini_app()
    {
        $app = new Application([
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        $this->assertInstanceOf(
            MiniAppApplication::class,
            $app->getMiniApp(new AuthorizerAccessToken('mock-app-id', 'mock-access-token'), [
                'secret' => 'mock-secret',
            ])
        );
    }
}
