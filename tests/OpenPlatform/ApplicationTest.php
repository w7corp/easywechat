<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OpenPlatform;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\OpenPlatform\Account;
use EasyWeChat\OpenPlatform\Account as AccountInterface;
use EasyWeChat\OpenPlatform\Application;
use EasyWeChat\OpenPlatform\Authorization;
use EasyWeChat\OpenPlatform\ComponentAccessToken;
use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\OpenPlatform\Contracts\Application as ApplicationInterface;
use EasyWeChat\OpenPlatform\Contracts\VerifyTicket as VerifyTicketInterface;
use EasyWeChat\OpenPlatform\VerifyTicket;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\OpenPlatform\Server;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class ApplicationTest extends TestCase
{
    public function test_get_and_set_account()
    {
        $app = new Application(
            [
                'app_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
            ]
        );

        $this->assertInstanceOf(ApplicationInterface::class, $app);
        $this->assertInstanceOf(AccountInterface::class, $app->getAccount());
        $this->assertSame($app->getAccount(), $app->getAccount());

        // set
        $account = new Account(appId: 'wx3cf0f39249000060', secret: 'mock-secret', token: 'mock-token');
        $app->setAccount($account);
        $this->assertSame($account, $app->getAccount());
    }

    public function test_get_and_set_encryptor()
    {
        $app = new Application(
            [
                'app_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $this->assertInstanceOf(Encryptor::class, $app->getEncryptor());
        $this->assertSame($app->getEncryptor(), $app->getEncryptor());

        // set
        $encryptor = new Encryptor(appId: 'wx3cf0f39249000060', token: 'mock-token', aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG');
        $app->setEncryptor($encryptor);
        $this->assertSame($encryptor, $app->getEncryptor());
    }

    public function test_get_and_set_server()
    {
        $app = new Application(
            [
                'app_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $this->assertInstanceOf(ServerInterface::class, $app->getServer());
        $this->assertSame($app->getServer(), $app->getServer());

        // set
        $server = new Server(\Mockery::mock(Account::class), \Mockery::mock(ServerRequestInterface::class));
        $app->setServer($server);
        $this->assertSame($server, $app->getServer());
    }

    public function test_get_and_set_component_access_token()
    {
        $app = new Application(
            [
                'app_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $this->assertInstanceOf(AccessTokenInterface::class, $app->getAccessToken());

        // set
        $accessToken = new ComponentAccessToken('wx3cf0f39249000060', 'mock-secret', $app->getVerifyTicket());
        $app->setComponentAccessToken($accessToken);
        $this->assertSame($accessToken, $app->getAccessToken());
    }

    public function test_get_and_set_verify_ticket()
    {
        $app = new Application(
            [
                'app_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $this->assertInstanceOf(VerifyTicketInterface::class, $app->getVerifyTicket());

        // set
        $verifyTicket = new VerifyTicket('wx3cf0f39249000060');
        $app->setVerifyTicket($verifyTicket);
        $this->assertSame($verifyTicket, $app->getVerifyTicket());
    }

    public function test_get_authorization()
    {
        $app = new Application(
            [
                'app_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $mockResponse = new MockResponse(\json_encode([
            'authorization_info' => ['authorizer_appid' => 'mock-appid']
        ]), [
            'http_code' => 201,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClient = new MockHttpClient($mockResponse, 'https://api.weixin.qq.com/');
        $app->setHttpClient($httpClient);
        $this->assertInstanceOf(Authorization::class, $app->getAuthorization('mock-auth-code'));

        $this->assertSame('POST', $mockResponse->getRequestMethod());
        $this->assertSame('https://api.weixin.qq.com/cgi-bin/component/api_query_auth', $mockResponse->getRequestUrl());
        $this->assertSame(\json_encode([
                'component_appid' => 'wx3cf0f39249000060',
                'authorization_code' => 'mock-auth-code',
            ]), $mockResponse->getRequestOptions()['body']);


        // exception
        $mockResponse = new MockResponse(\json_encode([
            'error_code' => 100029
        ]), [
            'http_code' => 201,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClient = new MockHttpClient($mockResponse, 'https://api.weixin.qq.com/');
        $app->setHttpClient($httpClient);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Failed to get authorization_info.');
        $app->getAuthorization('mock-auth-code');
    }

    public function test_refresh_authorizer_token()
    {
        $app = new Application(
            [
                'app_id' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
                'token' => 'mock-token',
                'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
            ]
        );

        $mockResponse = new MockResponse(\json_encode([
            'authorizer_access_token' => 'mock-access-token'
        ]), [
            'http_code' => 201,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClient = new MockHttpClient($mockResponse, 'https://api.weixin.qq.com/');
        $app->setHttpClient($httpClient);
        $this->assertSame([
            'authorizer_access_token' => 'mock-access-token'
        ], $app->refreshAuthorizerToken('mock-authorizer-appid', 'mock-refresh-token'));

        $this->assertSame('POST', $mockResponse->getRequestMethod());
        $this->assertSame('https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token', $mockResponse->getRequestUrl());
        $this->assertSame(\json_encode([
            'component_appid' => 'wx3cf0f39249000060',
            'authorizer_appid' => 'mock-authorizer-appid',
            'authorizer_refresh_token' => 'mock-refresh-token',
        ]), $mockResponse->getRequestOptions()['body']);


        // exception
        $mockResponse = new MockResponse(\json_encode([
            'error_code' => 100029
        ]), [
            'http_code' => 201,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClient = new MockHttpClient($mockResponse, 'https://api.weixin.qq.com/');
        $app->setHttpClient($httpClient);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Failed to get authorizer_access_token.');
        $app->refreshAuthorizerToken('mock-authorizer-appid', 'mock-refresh-token');
    }
}
