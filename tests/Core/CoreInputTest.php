<?php

namespace EasyWeChat\Core {
    class PHPInput
    {
        public static $content = null;
    }

    function file_get_contents()
    {
        return PHPInput::$content;
    }
}

namespace {

    use EasyWeChat\Core\Input;
    use EasyWeChat\Core\PHPInput;
    use EasyWeChat\Support\Collection;
    use EasyWeChat\Support\XML;

    class CoreInputTest extends TestCase
    {
        public function setUp()
        {
            parent::setUp();
            PHPInput::$content = '<xml>
                                 <ToUserName><![CDATA[toUser]]></ToUserName>
                                 <FromUserName><![CDATA[fromUser]]></FromUserName>
                                 <CreateTime>1348831860</CreateTime>
                                 <MsgType><![CDATA[text]]></MsgType>
                                 <Content><![CDATA[this is a test]]></Content>
                                 <MsgId>1234567890123456</MsgId>
                                 </xml>';
        }

        public function testGet()
        {
            $cryptor = Mockery::mock('EasyWeChat\Encryption\Cryptor');
            $_REQUEST = ['encrypt_type' => 'none'];
            $input = new Input('token', $cryptor);

            $this->assertEquals('text', $input->get('MsgType'));
            $this->assertEquals('1234567890123456', $input->get('MsgId'));
            $this->assertEquals('this is a test', $input->get('Content'));
            $this->assertFalse($input->isEncrypted());
        }

        public function testGetWithEncrypted()
        {
            $cryptor = Mockery::mock('EasyWeChat\Encryption\Cryptor');
            $cryptor->shouldReceive('decryptMsg')->andReturnUsing(function () {
                $args = func_get_args();

                return XML::parse(array_pop($args));
            });
            $_REQUEST = [
                'encrypt_type' => 'aes',
                'msg_signature' => '8d9521e63f84b2cd2e0daa124eb7eb0c34b6204a',
                'nonce' => '1351554359',
                'timestamp' => 1411034505,
            ];
            $input = new Input('token', $cryptor);

            $this->assertEquals('text', $input->get('MsgType'));
            $this->assertEquals('1234567890123456', $input->get('MsgId'));
            $this->assertEquals('this is a test', $input->get('Content'));
            $this->assertTrue($input->isEncrypted());
        }

        /**
         * Test validate().
         *
         * @expectedException EasyWeChat\Core\Exceptions\FaultException
         * @expectedExceptionMessage Invalid request signature.
         */
        public function testValidate()
        {
            $cryptor = Mockery::mock('EasyWeChat\Encryption\Cryptor');

            $_REQUEST = [
                'encrypt_type' => 'none',
                'nonce' => '1351554359',
                'timestamp' => 1411034505,
                'signature' => '2dd9c1d69cb87402cdd39c7d00a825acce44378c', // right signature
            ];

            $input = new Input('token', $cryptor);
            $this->assertEquals('text', $input->get('MsgType'));

            $_REQUEST = [
                'encrypt_type' => 'none',
                'nonce' => '1351554359',
                'timestamp' => 1411034505,
                'signature' => '8d9521e63f84b2cd2e0daa124eb7eb0c34b6204a', // error signature
            ];
            $input = new Input('token', $cryptor);
        }

        /**
         * Test setInput().
         */
        public function testSetInput()
        {
            $custom = [
                'foo' => 'bar',
                'overtrue' => 'me',
            ];

            $_REQUEST = [
                'encrypt_type' => 'none',
                'nonce' => '1351554359',
                'timestamp' => 1411034505,
                'signature' => '2dd9c1d69cb87402cdd39c7d00a825acce44378c', // right signature
            ];
            $cryptor = Mockery::mock('EasyWeChat\Encryption\Cryptor');

            $input = new Input('token', $cryptor);

            $input->setInput($custom);

            $this->assertNull($input->get('MsgType'));
            $this->assertEquals(['foo' => 'bar', 'overtrue' => 'me'], $input->all());
            $this->assertEquals('bar', $input->get('foo'));

            $input = new Input('token', $cryptor);
            $input->setInput(new Collection($custom));

            $this->assertNull($input->get('MsgType'));
            $this->assertEquals(['foo' => 'bar', 'overtrue' => 'me'], $input->all());
            $this->assertEquals('bar', $input->get('foo'));
        }
    }
}
