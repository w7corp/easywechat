<?php

namespace EasyWeChat\Tests\Kernel\Traits;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Message;
use EasyWeChat\Kernel\Traits\DecryptMessage;
use EasyWeChat\Tests\TestCase;
use Nyholm\Psr7\ServerRequest;

class DecryptJsonMessageTest extends TestCase
{
    use DecryptMessage;

    public function test_it_can_decrypt_json_message(): void
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
            'AgentID' => '218',
        ], JSON_UNESCAPED_UNICODE);

        $this->assertIsString($body);

        $message = JsonDummyMessage::createFromRequest(new ServerRequest('POST', 'http://easywechat.com/server', [], $body));

        $message = $this->decryptMessage(
            message: $message,
            encryptor: $encryptor,
            signature: $encrypted['signature'],
            timestamp: $encrypted['timestamp'],
            nonce: $encrypted['nonce']
        );

        $this->assertSame([
            'ToUserName' => 'wx5823bf96d3bd56c7',
            'Encrypt' => $encrypted['ciphertext'],
            'AgentID' => '218',
            'FromUserName' => 'mycreate',
            'CreateTime' => '1409659813',
            'MsgType' => 'text',
            'Content' => 'hello',
            'MsgId' => '4561255354251345929',
        ], $message->toArray());
    }
}

class JsonDummyMessage extends Message
{
}
