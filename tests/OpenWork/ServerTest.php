<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OpenWork;

use EasyWeChat\OpenWork\Application;
use EasyWeChat\OpenWork\Encryptor;
use EasyWeChat\OpenWork\Server;
use EasyWeChat\OpenWork\SuiteEncryptor;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;

class ServerTest extends TestCase
{
    public function test_it_will_handle_suite_ticket_refresh_event()
    {
        $body = '<xml>
            <SuiteId>suite-id</SuiteId>
            <InfoType>suite_ticket</InfoType>
            <SuiteTicket>mock-suite-ticket</SuiteTicket>
        </xml>';

        $suiteEncryptor = $this->createSuiteEncryptor();
        $request = $this->createEncryptedXmlMessageRequest($body, $suiteEncryptor);

        $server = new Server(
            encryptor: $suiteEncryptor,
            providerEncryptor: $this->createProviderEncryptor(),
            request: $request,
        );

        $handled = null;
        $response = $server->handleSuiteTicketRefreshed(function ($message) use (&$handled) {
            $handled = $message->SuiteTicket;
        })->serve();

        $this->assertSame('mock-suite-ticket', $handled);
        $this->assertSame('success', (string) $response->getBody());
    }

    public function test_it_will_handle_auth_created_event()
    {
        $body = '<xml>
            <SuiteId>suite-id</SuiteId>
            <InfoType>create_auth</InfoType>
            <AuthCorpId>mock-corp-id</AuthCorpId>
        </xml>';

        $suiteEncryptor = $this->createSuiteEncryptor();
        $request = $this->createEncryptedXmlMessageRequest($body, $suiteEncryptor);

        $server = new Server(
            encryptor: $suiteEncryptor,
            providerEncryptor: $this->createProviderEncryptor(),
            request: $request,
        );

        $handled = null;
        $response = $server->handleAuthCreated(function ($message) use (&$handled) {
            $handled = $message->InfoType;
        })->serve();

        $this->assertSame('create_auth', $handled);
        $this->assertSame('success', (string) $response->getBody());
    }

    public function test_application_server_persists_suite_ticket_by_default()
    {
        $app = new Application([
            'corp_id' => 'wx3cf0f39249000060',
            'provider_secret' => 'mock-provider-secret',
            'suite_id' => 'suite-id',
            'suite_secret' => 'mock-suite-secret',
            'token' => 'mock-token',
            'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);
        $app->setCache(new Psr16Cache(new ArrayAdapter));

        $request = $this->createEncryptedXmlMessageRequest('<xml>
            <SuiteId>suite-id</SuiteId>
            <InfoType>suite_ticket</InfoType>
            <SuiteTicket>persisted-suite-ticket</SuiteTicket>
        </xml>', $app->getSuiteEncryptor());

        $app->setRequest($request);

        $response = $app->getServer()->serve();

        $this->assertSame('success', (string) $response->getBody());
        $this->assertSame('persisted-suite-ticket', $app->getSuiteTicket()->getTicket());
    }

    public function test_default_suite_ticket_handler_is_replaced_instead_of_duplicated()
    {
        $body = '<xml>
            <SuiteId>suite-id</SuiteId>
            <InfoType>suite_ticket</InfoType>
            <SuiteTicket>mock-suite-ticket</SuiteTicket>
        </xml>';

        $suiteEncryptor = $this->createSuiteEncryptor();
        $request = $this->createEncryptedXmlMessageRequest($body, $suiteEncryptor);

        $server = new Server(
            encryptor: $suiteEncryptor,
            providerEncryptor: $this->createProviderEncryptor(),
            request: $request,
        );

        $firstHandlerCalls = 0;
        $secondHandlerCalls = 0;

        $server->withDefaultSuiteTicketHandler(function ($message, $next) use (&$firstHandlerCalls) {
            $firstHandlerCalls++;

            return $next($message);
        });
        $server->withDefaultSuiteTicketHandler(function ($message, $next) use (&$secondHandlerCalls) {
            $secondHandlerCalls++;

            return $next($message);
        });

        $response = $server->serve();

        $this->assertSame(0, $firstHandlerCalls);
        $this->assertSame(1, $secondHandlerCalls);
        $this->assertSame('success', (string) $response->getBody());
    }

    private function createSuiteEncryptor(): SuiteEncryptor
    {
        return new SuiteEncryptor(
            suiteId: 'suite-id',
            token: 'mock-token',
            aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        );
    }

    private function createProviderEncryptor(): Encryptor
    {
        return new Encryptor(
            corpId: 'wx3cf0f39249000060',
            token: 'mock-token',
            aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        );
    }
}
