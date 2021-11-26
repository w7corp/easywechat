<?php

declare(strict_types=1);

namespace EasyWeChat\Tests;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Support\Xml;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * Tear down the test case.
     */
    public function tearDown(): void
    {
        parent::tearDown();
        if ($container = \Mockery::getContainer()) {
            $this->addToAssertionCount($container->Mockery_getExpectationCount());
        }
        \Mockery::close();
    }

    public function createEncryptedXmlMessageRequest($plainMessageXml, Encryptor $encryptor, array $query = []): ServerRequest
    {
        $body = $encryptor->encrypt($plainMessageXml);

        $xml = Xml::parse($body);

        return (new ServerRequest('POST', 'http://easywechat.com/server', [], $body))->withQueryParams([
            'msg_signature' => $xml['MsgSignature'],
            'timestamp' => $xml['TimeStamp'],
            'nonce' => $xml['Nonce'],
            ...$query,
        ]);
    }
}
