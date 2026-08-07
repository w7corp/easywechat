<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OfficialAccount;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Support\Xml;
use EasyWeChat\OfficialAccount\Server;
use EasyWeChat\Tests\TestCase;
use Nyholm\Psr7\ServerRequest;

class ServerTest extends TestCase
{
    public const TOKEN = 'test-token';

    protected function plainMessageXml(): string
    {
        return '<xml>
          <ToUserName><![CDATA[toUser]]></ToUserName>
          <FromUserName><![CDATA[fromUser]]></FromUserName>
          <CreateTime>1348831860</CreateTime>
          <MsgType><![CDATA[text]]></MsgType>
          <Content><![CDATA[this is a test]]></Content>
          <MsgId>1234567890123456</MsgId>
        </xml>';
    }

    protected function createEncryptor(): Encryptor
    {
        return new Encryptor('wx5823bf96d3bd56c7', self::TOKEN, 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C');
    }

    public function test_it_will_handle_validation_request()
    {
        $query = $this->createPlainSignatureQuery(self::TOKEN, ['echostr' => 'abcdefghijklmn']);
        $request = (new ServerRequest('GET', 'http://easywechat.com/'))->withQueryParams($query);
        $server = new Server($request, token: self::TOKEN);

        $response = $server->serve();

        $this->assertSame('abcdefghijklmn', \strval($response->getBody()));
    }

    public function test_it_will_reject_validation_request_without_signature()
    {
        $request = (new ServerRequest('GET', 'http://easywechat.com/'))->withQueryParams(['echostr' => 'abcdefghijklmn']);
        $server = new Server($request, token: self::TOKEN);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Request signature must not be empty.');

        $server->serve();
    }

    public function test_it_will_reject_validation_request_with_invalid_signature()
    {
        $query = $this->createPlainSignatureQuery(self::TOKEN, ['echostr' => 'abcdefghijklmn']);
        $query['signature'] = 'invalid-signature';

        $request = (new ServerRequest('GET', 'http://easywechat.com/'))->withQueryParams($query);
        $server = new Server($request, token: self::TOKEN);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Invalid request signature.');

        $server->serve();
    }

    public function test_it_will_response_success_without_handlers()
    {
        $request = $this->createPlainXmlMessageRequest($this->plainMessageXml(), self::TOKEN);
        $server = new Server($request, token: self::TOKEN);

        $response = $server->serve();

        $this->assertSame('success', \strval($response->getBody()));
    }

    public function test_it_will_respond_from_message_handlers()
    {
        $request = $this->createPlainXmlMessageRequest($this->plainMessageXml(), self::TOKEN);
        $server = new Server($request, token: self::TOKEN);

        $response = $server
            ->addMessageListener(
                'text',
                function ($message) {
                    return 'hello';
                }
            )->addEventListener(
                'subscribe',
                function ($message) {
                    return 'world';
                }
            )->serve();

        $response = Xml::parse(\strval($response->getBody()));

        $this->assertSame('toUser', $response['FromUserName']);
        $this->assertSame('fromUser', $response['ToUserName']);
        $this->assertSame('text', $response['MsgType']);
        $this->assertSame('hello', $response['Content']);
    }

    public function test_it_will_respond_from_event_handlers()
    {
        $body = '<xml>
          <ToUserName><![CDATA[toUser]]></ToUserName>
          <FromUserName><![CDATA[fromUser]]></FromUserName>
          <CreateTime>123456789</CreateTime>
          <MsgType><![CDATA[event]]></MsgType>
          <Event><![CDATA[subscribe]]></Event>
          <EventKey><![CDATA[qrscene_123123]]></EventKey>
          <Ticket><![CDATA[TICKET]]></Ticket>
        </xml>';
        $request = $this->createPlainXmlMessageRequest($body, self::TOKEN);
        $server = new Server($request, token: self::TOKEN);

        $response = $server
            ->addMessageListener(
                'text',
                function ($message) {
                    return 'hello';
                }
            )->addEventListener(
                'subscribe',
                function ($message) {
                    return 'world';
                }
            )->serve();

        $response = Xml::parse(\strval($response->getBody()));

        $this->assertSame('toUser', $response['FromUserName']);
        $this->assertSame('fromUser', $response['ToUserName']);
        $this->assertSame('text', $response['MsgType']);
        $this->assertSame('world', $response['Content']);
    }

    /**
     * @see https://github.com/w7corp/easywechat/issues/2988
     */
    public function test_it_will_reject_forged_plaintext_message_when_encryptor_presents()
    {
        $handled = false;

        // The account is in secure mode, the attacker pushes a plaintext message
        // without any signature to bypass the decryption.
        $request = new ServerRequest('POST', 'http://easywechat.com/server', [], $this->plainMessageXml());
        $server = new Server($request, $this->createEncryptor(), self::TOKEN);
        $server->addMessageListener('text', function ($message) use (&$handled) {
            $handled = true;

            return 'hello';
        });

        try {
            $server->serve();
            $this->fail('The forged plaintext message should be rejected.');
        } catch (BadRequestException $e) {
            $this->assertSame('Request signature must not be empty.', $e->getMessage());
        }

        $this->assertFalse($handled, 'The message handler must not be called.');
    }

    public function test_it_will_reject_plaintext_message_with_invalid_signature()
    {
        $request = $this->createPlainXmlMessageRequest($this->plainMessageXml(), 'another-token');
        $server = new Server($request, token: self::TOKEN);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Invalid request signature.');

        $server->serve();
    }

    public function test_it_will_reject_encrypted_message_without_msg_signature()
    {
        $encryptor = $this->createEncryptor();
        $request = $this->createEncryptedXmlMessageRequest($this->plainMessageXml(), $encryptor);

        $query = $request->getQueryParams();
        unset($query['msg_signature']);

        $server = new Server($request->withQueryParams($query), $encryptor, self::TOKEN);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Request signature must not be empty.');

        $server->serve();
    }

    public function test_it_will_reject_encrypted_message_with_tampered_ciphertext()
    {
        $encryptor = $this->createEncryptor();
        $request = $this->createEncryptedXmlMessageRequest($this->plainMessageXml(), $encryptor);

        $tampered = new ServerRequest(
            'POST',
            'http://easywechat.com/server',
            [],
            '<xml><Encrypt><![CDATA[dGFtcGVyZWQ=]]></Encrypt></xml>'
        );

        $server = new Server($tampered->withQueryParams($request->getQueryParams()), $encryptor, self::TOKEN);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Invalid request signature.');

        $server->serve();
    }

    public function test_it_can_handle_encrypted_message()
    {
        $encryptor = $this->createEncryptor();
        $request = $this->createEncryptedXmlMessageRequest($this->plainMessageXml(), $encryptor);
        $server = new Server($request, $encryptor, self::TOKEN);

        $content = null;
        $server->addMessageListener('text', function ($message) use (&$content) {
            $content = $message->Content;

            return 'hello';
        });

        $server->serve();

        $this->assertSame('this is a test', $content);
    }

    public function test_it_can_handle_compatible_mode_message()
    {
        $encryptor = $this->createEncryptor();
        $encrypted = $encryptor->encryptAsArray(
            plaintext: $this->plainMessageXml(),
            nonce: '1372623149',
            timestamp: '1409659813'
        );

        // In compatible mode the plaintext and the ciphertext are pushed together.
        $body = '<xml>
          <ToUserName><![CDATA[toUser]]></ToUserName>
          <FromUserName><![CDATA[fromUser]]></FromUserName>
          <CreateTime>1348831860</CreateTime>
          <MsgType><![CDATA[text]]></MsgType>
          <Content><![CDATA[this is a test]]></Content>
          <MsgId>1234567890123456</MsgId>
          <Encrypt><![CDATA['.$encrypted['ciphertext'].']]></Encrypt>
        </xml>';

        $request = (new ServerRequest('POST', 'http://easywechat.com/server', [], $body))->withQueryParams([
            'signature' => 'the-plain-signature-should-be-ignored',
            'msg_signature' => $encrypted['signature'],
            'encrypt_type' => 'aes',
            'timestamp' => $encrypted['timestamp'],
            'nonce' => $encrypted['nonce'],
        ]);

        $server = new Server($request, $encryptor, self::TOKEN);

        $content = null;
        $server->addMessageListener('text', function ($message) use (&$content) {
            $content = $message->Content;

            return 'hello';
        });

        $server->serve();

        $this->assertSame('this is a test', $content);
    }

    public function test_it_will_reject_plaintext_message_when_encryption_is_required()
    {
        $request = $this->createPlainXmlMessageRequest($this->plainMessageXml(), self::TOKEN);
        $server = new Server($request, $this->createEncryptor(), self::TOKEN, requireEncryption: true);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Encrypted message is required, plaintext message rejected.');

        $server->serve();
    }

    public function test_it_will_throw_exception_without_token()
    {
        $request = $this->createPlainXmlMessageRequest($this->plainMessageXml(), self::TOKEN);
        $server = new Server($request);

        $this->expectException(InvalidConfigException::class);

        $server->serve();
    }

    public function test_it_will_use_the_token_of_the_encryptor_as_fallback()
    {
        $request = $this->createPlainXmlMessageRequest($this->plainMessageXml(), self::TOKEN);
        $server = new Server($request, $this->createEncryptor());

        $this->assertSame('success', \strval($server->serve()->getBody()));
    }

    public function test_it_can_decrypt_json_mode_messages()
    {
        $plaintext = json_encode([
            'ToUserName' => 'wx5823bf96d3bd56c7',
            'FromUserName' => 'mycreate',
            'CreateTime' => '1409659813',
            'MsgType' => 'text',
            'Content' => 'hello',
            'MsgId' => '4561255354251345929',
        ], JSON_UNESCAPED_UNICODE);

        $this->assertIsString($plaintext);

        $encryptor = new Encryptor('wx5823bf96d3bd56c7', 'QDG6eK', 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C');
        $encrypted = $encryptor->encryptAsArray(
            plaintext: $plaintext,
            nonce: '1372623149',
            timestamp: '1409659813'
        );

        $body = json_encode([
            'ToUserName' => 'wx5823bf96d3bd56c7',
            'Encrypt' => $encrypted['ciphertext'],
        ], JSON_UNESCAPED_UNICODE);

        $this->assertIsString($body);

        $request = (new ServerRequest('POST', 'http://easywechat.com/server', [], $body))->withQueryParams([
            'msg_signature' => $encrypted['signature'],
            'encrypt_type' => 'aes',
            'timestamp' => $encrypted['timestamp'],
            'nonce' => $encrypted['nonce'],
        ]);

        $server = new Server($request, $encryptor);

        $message = $server->getDecryptedMessage();

        $this->assertSame('hello', $message->Content);
        $this->assertSame('mycreate', $message->FromUserName);
    }

    public function test_it_will_reject_forged_plaintext_message_on_get_decrypted_message()
    {
        $request = new ServerRequest('POST', 'http://easywechat.com/server', [], $this->plainMessageXml());
        $server = new Server($request, $this->createEncryptor(), self::TOKEN);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Request signature must not be empty.');

        $server->getDecryptedMessage();
    }

    public function test_it_can_get_plaintext_message_with_valid_signature()
    {
        $request = $this->createPlainXmlMessageRequest($this->plainMessageXml(), self::TOKEN);
        $server = new Server($request, token: self::TOKEN);

        $message = $server->getDecryptedMessage();

        $this->assertSame('this is a test', $message->Content);
    }
}
