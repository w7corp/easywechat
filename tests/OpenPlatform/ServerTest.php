<?php

namespace EasyWeChat\Tests\OpenPlatform;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\OpenPlatform\Account;
use EasyWeChat\OpenPlatform\Server;
use EasyWeChat\Tests\TestCase;

class ServerTest extends TestCase
{
    public function test_it_will_handle_authorized_event()
    {
        $body = '<xml>
              <AppId>第三方平台appid</AppId>
              <CreateTime>1413192760</CreateTime>
              <InfoType>authorized</InfoType>
              <AuthorizerAppid>公众号appid</AuthorizerAppid>
              <AuthorizationCode>授权码</AuthorizationCode>
              <AuthorizationCodeExpiredTime>过期时间</AuthorizationCodeExpiredTime>
              <PreAuthCode>预授权码</PreAuthCode>
            </xml>
        ';
        $encryptor = new Encryptor('wx5823bf96d3bd56c7', 'QDG6eK', 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C');
        $request = $this->createEncryptedXmlMessageRequest($body, $encryptor);

        $server = new Server(account: \Mockery::mock(Account::class), encryptor: $encryptor, request: $request);

        $handleResult = null;
        $response = $server->handleAuthorized(function ($message) use (&$handleResult) {
            $handleResult = 'authorized-event-handled';
        })->serve();

        $this->assertSame('authorized-event-handled', $handleResult);
        $this->assertSame('success', \strval($response->getBody()));
    }

    public function test_it_will_handle_unauthorized_event()
    {
        $body = '<xml>
              <AppId>第三方平台appid</AppId>
              <CreateTime>1413192760</CreateTime>
              <InfoType>unauthorized</InfoType>
              <AuthorizerAppid>公众号appid</AuthorizerAppid>
            </xml>
        ';
        $encryptor = new Encryptor('wx5823bf96d3bd56c7', 'QDG6eK', 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C');
        $request = $this->createEncryptedXmlMessageRequest($body, $encryptor);

        $server = new Server(account: \Mockery::mock(Account::class), encryptor: $encryptor, request: $request);

        $handleResult = null;
        $response = $server->handleUnauthorized(function ($message) use (&$handleResult) {
            $handleResult = 'unauthorized-event-handled';
        })->serve();

        $this->assertSame('unauthorized-event-handled', $handleResult);
        $this->assertSame('success', \strval($response->getBody()));
    }

    public function test_it_will_handle_authorize_updated_event()
    {
        $body = '<xml>
              <AppId>第三方平台appid</AppId>
              <CreateTime>1413192760</CreateTime>
              <InfoType>updateauthorized</InfoType>
              <AuthorizerAppid>公众号appid</AuthorizerAppid>
              <AuthorizationCode>授权码</AuthorizationCode>
              <AuthorizationCodeExpiredTime>过期时间</AuthorizationCodeExpiredTime>
              <PreAuthCode>预授权码</PreAuthCode>
            </xml>
        ';
        $encryptor = new Encryptor('wx5823bf96d3bd56c7', 'QDG6eK', 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C');
        $request = $this->createEncryptedXmlMessageRequest($body, $encryptor);

        $server = new Server(account: \Mockery::mock(Account::class), encryptor: $encryptor, request: $request);

        $handleResult = null;
        $response = $server->handleAuthorizeUpdated(function ($message) use (&$handleResult) {
            $handleResult = 'authorized-updated-event-handled';
        })->serve();

        $this->assertSame('authorized-updated-event-handled', $handleResult);
        $this->assertSame('success', \strval($response->getBody()));
    }

    public function test_it_will_handle_verify_ticket_refresh_event()
    {
        $body = '<xml>
            <AppId>some_appid</AppId>
            <CreateTime>1413192605</CreateTime>
            <InfoType>component_verify_ticket</InfoType>
            <ComponentVerifyTicket>some_verify_ticket</ComponentVerifyTicket>
            </xml>
        ';
        $encryptor = new Encryptor('wx5823bf96d3bd56c7', 'QDG6eK', 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C');
        $request = $this->createEncryptedXmlMessageRequest($body, $encryptor);

        $server = new Server(
            account: \Mockery::mock(Account::class),
            encryptor: $encryptor,
            request: $request
        );

        $handleResult = null;
        $response = $server->handleVerifyTicketRefreshed(function ($message) use (&$handleResult) {
            $handleResult = 'verify-ticket-refreshed-event-handled';
        })->serve();

        $this->assertSame('verify-ticket-refreshed-event-handled', $handleResult);
        $this->assertSame('success', \strval($response->getBody()));
    }
}
