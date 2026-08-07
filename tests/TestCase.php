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
    protected function tearDown(): void
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

        return (new ServerRequest('POST', 'http://easywechat.com/server', [], $body))->withQueryParams(array_merge([
            'msg_signature' => $xml['MsgSignature'],
            'encrypt_type' => 'aes',
            'timestamp' => $xml['TimeStamp'],
            'nonce' => $xml['Nonce'],
        ], $query));
    }

    /**
     * Create a plaintext mode request signed with the given token.
     */
    public function createPlainXmlMessageRequest(string $plainMessageXml, string $token, array $query = []): ServerRequest
    {
        $request = new ServerRequest('POST', 'http://easywechat.com/server', [], $plainMessageXml);

        return $request->withQueryParams($this->createPlainSignatureQuery($token, $query));
    }

    /**
     * Build the query params of a plaintext mode request, with a valid `signature`.
     */
    public function createPlainSignatureQuery(string $token, array $query = []): array
    {
        $timestamp = $query['timestamp'] ?? '1348831860';
        $nonce = $query['nonce'] ?? '1543311492';

        $params = [$token, $timestamp, $nonce];

        sort($params, SORT_STRING);

        return array_merge([
            'signature' => sha1(implode($params)),
            'timestamp' => $timestamp,
            'nonce' => $nonce,
        ], $query);
    }
}
