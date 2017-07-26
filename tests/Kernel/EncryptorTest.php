<?php


namespace EasyWeChat\Tests\Kernel;


use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Support\AES;
use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\Tests\TestCase;


class EncryptorTest extends TestCase
{

    public function getEncryptor()
    {
        return new Encryptor('wxb11529c136998cb6', 'pamtest', 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG');
    }

    public function testErrorParams()
    {
        try {
            new Encryptor('', '', '');
            $this->fail('No exception thrown.');
        } catch (\Exception $e) {
            $this->assertSame('Mission config \'aes_key\'.', $e->getMessage());
        }

        try {
            new Encryptor('', '', '1111111111');
            $this->fail('No expected exception thrown.');
        } catch (\Exception $e) {
            $this->assertSame('The length of \'aes_key\' must be 43.', $e->getMessage());
        }

        try {
            new Encryptor('', '', []);
            $this->fail('No expected exception thrown.');
        } catch (\Exception $e) {
            $this->assertSame('The $aesKeyOrAes must be a string or an instance of \EasyWeChat\Kernel\Support\AES.', $e->getMessage());
        }


    }

    public function testDecrypt()
    {
        $encrypted = "<xml>\n    <ToUserName><![CDATA[asdasdasd]]></ToUserName>\n    <Encrypt><![CDATA[rTNFcsut4LfGuAFKEUVVpwcaCOTJzOd9twZdIW910jb3k+iicx2uvhttIZ3Qg9Qgty3BEF2xbOrz6boTfb30dMomcgrkTqdFPwnhqbk+kIQ7rZiwny9D7NUrTgA5kpX3KsZvrXzUZyP2x9YOlxbgm572lmxKvM7HAQQhIQ/p6HBmoY30bGXFK0BtIu1pW9TjhOYrLQoU18nWYjWqDA1ynkmOytpv7QRI1P1+0NoxL0q2zO1DgeSvnE8CZGo/o5Ap/WHK5W2RAsinpzN4/LjPnmB6U01I5XCoJoC0GK/yMZycd2Oh8Nq6+wBkC1U85oy0ktOY4nLvsQMLrourmMGdZHuTbqpeJ8Ao/5PRYJ+WBvRUwPfGKBL2+2IKZF49vAJqkcGWSHGE76ZN2erXeuNazf/o9o3lIE3q739o4c8t9QGPe31GT2Go/rOz1BsrASwvauNulCh+++yz+CQzBIuikA==]]></Encrypt>\n</xml>\n";
        $decrypted = $this->getEncryptor()->decrypt('4f3ad57b6989f09f4eb392acce4f9e93942ed890', '260774613', '1458300676', $encrypted);
        $this->assertEquals('asdasdasd', $decrypted['ToUserName']);
        $this->assertEquals('asdasdasdsadasd', $decrypted['FromUserName']);
        $this->assertEquals('1234567898', $decrypted['CreateTime']);
        $this->assertEquals('hello', $decrypted['Content']);
    }

    public function testEncryptAndDecrypt()
    {
        $raw = [
            'ToUserName' => '测试中文',
            'FromUserName' => 'gh_7f083739789a',
            'CreateTime' => '1407743423',
            'MsgType' => 'video',
            'Video' => [
                'MediaId' => 'eYJ1MbwPRJtOvIEabaxHs7TX2D-HV71s79GUxqdUkjm6Gs2Ed1KF3ulAOA9H1xG0',
                'Title' => 'testCallBackReplyVideo',
                'Description' => 'testCallBackReplyVideo',
            ], ];
        $xml = XML::build($raw);
        $encrypted = $this->getEncryptor()->encrypt($xml, 'xxxxxx', '1407743423');
        $array = XML::parse($encrypted);
        $this->assertEquals('1407743423', $array['TimeStamp']);
        $this->assertEquals('xxxxxx', $array['Nonce']);
        $this->assertNotEmpty($array['Encrypt']);
        $this->assertNotEmpty($array['MsgSignature']);
        $this->assertEquals($raw, $this->getEncryptor()->decrypt($array['MsgSignature'], $array['Nonce'], $array['TimeStamp'], $encrypted));
    }

    public function testEncryptException()
    {
        $aes = \Mockery::mock(AES::class);
        $aes->allows()->encrypt(\Mockery::type('string'))->andThrow(new \Exception('encrypt fail.'));
        $encryptor = new Encryptor('appid', 'token', $aes);

        try {
            $encryptor->encrypt('<foo>foo</foo>');
            $this->fail('No expected exception thrown.');
        } catch (RuntimeException $e) {
            $this->assertSame('encrypt fail.', $e->getMessage());
            $this->assertSame(Encryptor::ERROR_ENCRYPT_AES, $e->getCode());
        }
    }

    public function testDecryptExceptions()
    {
        // Invalid xml
        try {
            $this->getEncryptor()->decrypt('asadasd', 'nonce', time(), '123');
            $this->fail('No expected exception thrown.');
        } catch (RuntimeException $e) {
            $this->assertSame('Invalid xml.', $e->getMessage());
            $this->assertSame(Encryptor::ERROR_PARSE_XML, $e->getCode());
        }

        // invalid signature
        try {
            $xml = $encrypted = "<xml>\n    <ToUserName><![CDATA[asdasdasd]]></ToUserName>\n    <Encrypt><![CDATA[rTNFcsut4LfGuAFKEUVVpwcaCOTJzOd9twZdIW910jb3k+iicx2uvhttIZ3Qg9Qgty3BEF2xbOrz6boTfb30dMomcgrkTqdFPwnhqbk+kIQ7rZiwny9D7NUrTgA5kpX3KsZvrXzUZyP2x9YOlxbgm572lmxKvM7HAQQhIQ/p6HBmoY30bGXFK0BtIu1pW9TjhOYrLQoU18nWYjWqDA1ynkmOytpv7QRI1P1+0NoxL0q2zO1DgeSvnE8CZGo/o5Ap/WHK5W2RAsinpzN4/LjPnmB6U01I5XCoJoC0GK/yMZycd2Oh8Nq6+wBkC1U85oy0ktOY4nLvsQMLrourmMGdZHuTbqpeJ8Ao/5PRYJ+WBvRUwPfGKBL2+2IKZF49vAJqkcGWSHGE76ZN2erXeuNazf/o9o3lIE3q739o4c8t9QGPe31GT2Go/rOz1BsrASwvauNulCh+++yz+CQzBIuikA==]]></Encrypt>\n</xml>\n";
            $this->getEncryptor()->decrypt('invalid-signature-here', '260774613', '1458300676', $xml);
            $this->fail('No expected exception thrown.');
        } catch (RuntimeException $e) {
            $this->assertSame('Invalid Signature.', $e->getMessage());
            $this->assertSame(Encryptor::ERROR_INVALID_SIGNATURE, $e->getCode());
        }

        // invalid appid
        try {
            $xml = $encrypted = "<xml>\n    <ToUserName><![CDATA[asdasdasd]]></ToUserName>\n    <Encrypt><![CDATA[rTNFcsut4LfGuAFKEUVVpwcaCOTJzOd9twZdIW910jb3k+iicx2uvhttIZ3Qg9Qgty3BEF2xbOrz6boTfb30dMomcgrkTqdFPwnhqbk+kIQ7rZiwny9D7NUrTgA5kpX3KsZvrXzUZyP2x9YOlxbgm572lmxKvM7HAQQhIQ/p6HBmoY30bGXFK0BtIu1pW9TjhOYrLQoU18nWYjWqDA1ynkmOytpv7QRI1P1+0NoxL0q2zO1DgeSvnE8CZGo/o5Ap/WHK5W2RAsinpzN4/LjPnmB6U01I5XCoJoC0GK/yMZycd2Oh8Nq6+wBkC1U85oy0ktOY4nLvsQMLrourmMGdZHuTbqpeJ8Ao/5PRYJ+WBvRUwPfGKBL2+2IKZF49vAJqkcGWSHGE76ZN2erXeuNazf/o9o3lIE3q739o4c8t9QGPe31GT2Go/rOz1BsrASwvauNulCh+++yz+CQzBIuikA==]]></Encrypt>\n</xml>\n";
            $encryptor = new Encryptor('invalid-appid', 'pamtest', 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG');
            $encryptor->decrypt('4f3ad57b6989f09f4eb392acce4f9e93942ed890', '260774613', '1458300676', $xml);
            $this->fail('No expected exception thrown.');
        } catch (RuntimeException $e) {
            $this->assertSame('Invalid appId.', $e->getMessage());
            $this->assertSame(Encryptor::ERROR_INVALID_APP_ID, $e->getCode());
        }
    }

    public function testPkcs7padWithToolongBlockSize()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('$blockSize may not be more than 256');
        $this->getEncryptor()->pkcs7Pad('xxx', 257);
    }
}
