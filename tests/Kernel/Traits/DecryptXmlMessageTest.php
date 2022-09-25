<?php

namespace EasyWeChat\Tests\Kernel\Traits;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Message;
use EasyWeChat\Kernel\Traits\DecryptXmlMessage;
use EasyWeChat\Tests\TestCase;
use Nyholm\Psr7\ServerRequest;

class DecryptXmlMessageTest extends TestCase
{
    use DecryptXmlMessage;

    public function test_it_can_decrypt_message()
    {
        $body = '<xml>
                <ToUserName><![CDATA[wx5823bf96d3bd56c7]]></ToUserName>
                <Encrypt><![CDATA[RypEvHKD8QQKFhvQ6QleEB4J58tiPdvo+rtK1I9qca6aM/wvqnLSV5zEPeusUiX5L5X/0lWfrf0QADHHhGd3QczcdCUpj911L3vg3W/sYYvuJTs3TUUkSUXxaccAS0qhxchrRYt66wiSpGLYL42aM6A8dTT+6k4aSknmPj48kzJs8qLjvd4Xgpue06DOdnLxAUHzM6+kDZ+HMZfJYuR+LtwGc2hgf5gsijff0ekUNXZiqATP7PF5mZxZ3Izoun1s4zG4LUMnvw2r+KqCKIw+3IQH03v+BCA9nMELNqbSf6tiWSrXJB3LAVGUcallcrw8V2t9EL4EhzJWrQUax5wLVMNS0+rUPA3k22Ncx4XXZS9o0MBH27Bo6BpNelZpS+/uh9KsNlY6bHCmJU9p8g7m3fVKn28H3KDYA5Pl/T8Z1ptDAVe0lXdQ2YoyyH2uyPIGHBZZIs2pDBS8R07+qN+E7Q==]]></Encrypt>
                <AgentID><![CDATA[218]]></AgentID>
                </xml>';
        $message = DummyMessage::createFromRequest(new ServerRequest('POST', 'http://easywechat.com/server', [], $body));

        $encryptor = new Encryptor('wx5823bf96d3bd56c7', 'QDG6eK', 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C');
        $message = $this->decryptMessage($message, $encryptor, '477715d11cdb4164915debcba66cb864d751f3e6', '1409659813', '1372623149');

        $this->assertSame([
            'ToUserName' => 'wx5823bf96d3bd56c7',
            'Encrypt' => 'RypEvHKD8QQKFhvQ6QleEB4J58tiPdvo+rtK1I9qca6aM/wvqnLSV5zEPeusUiX5L5X/0lWfrf0QADHHhGd3QczcdCUpj911L3vg3W/sYYvuJTs3TUUkSUXxaccAS0qhxchrRYt66wiSpGLYL42aM6A8dTT+6k4aSknmPj48kzJs8qLjvd4Xgpue06DOdnLxAUHzM6+kDZ+HMZfJYuR+LtwGc2hgf5gsijff0ekUNXZiqATP7PF5mZxZ3Izoun1s4zG4LUMnvw2r+KqCKIw+3IQH03v+BCA9nMELNqbSf6tiWSrXJB3LAVGUcallcrw8V2t9EL4EhzJWrQUax5wLVMNS0+rUPA3k22Ncx4XXZS9o0MBH27Bo6BpNelZpS+/uh9KsNlY6bHCmJU9p8g7m3fVKn28H3KDYA5Pl/T8Z1ptDAVe0lXdQ2YoyyH2uyPIGHBZZIs2pDBS8R07+qN+E7Q==',
            'AgentID' => '218',
            'FromUserName' => 'mycreate',
            'CreateTime' => '1409659813',
            'MsgType' => 'text',
            'Content' => 'hello',
            'MsgId' => '4561255354251345929',
        ], $message->toArray());
    }
}

class DummyMessage extends Message
{
}
