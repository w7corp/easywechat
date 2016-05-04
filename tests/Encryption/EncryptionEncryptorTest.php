<?php

use EasyWeChat\Encryption\Encryptor;
use EasyWeChat\Support\XML;

class EncryptionEncryptorTest extends TestCase
{
    public function getEncryptor()
    {
        return new Encryptor('wxb11529c136998cb6', 'pamtest', 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG');
    }

    public function testDecrypt()
    {
        $encrypted = "<xml>\n    <ToUserName><![CDATA[asdasdasd]]></ToUserName>\n    <Encrypt><![CDATA[rTNFcsut4LfGuAFKEUVVpwcaCOTJzOd9twZdIW910jb3k+iicx2uvhttIZ3Qg9Qgty3BEF2xbOrz6boTfb30dMomcgrkTqdFPwnhqbk+kIQ7rZiwny9D7NUrTgA5kpX3KsZvrXzUZyP2x9YOlxbgm572lmxKvM7HAQQhIQ/p6HBmoY30bGXFK0BtIu1pW9TjhOYrLQoU18nWYjWqDA1ynkmOytpv7QRI1P1+0NoxL0q2zO1DgeSvnE8CZGo/o5Ap/WHK5W2RAsinpzN4/LjPnmB6U01I5XCoJoC0GK/yMZycd2Oh8Nq6+wBkC1U85oy0ktOY4nLvsQMLrourmMGdZHuTbqpeJ8Ao/5PRYJ+WBvRUwPfGKBL2+2IKZF49vAJqkcGWSHGE76ZN2erXeuNazf/o9o3lIE3q739o4c8t9QGPe31GT2Go/rOz1BsrASwvauNulCh+++yz+CQzBIuikA==]]></Encrypt>\n</xml>\n";

        $decrypted = $this->getEncryptor()->decryptMsg('4f3ad57b6989f09f4eb392acce4f9e93942ed890', '260774613', '1458300676', $encrypted);
        $this->assertEquals('asdasdasd', $decrypted['ToUserName']);
        $this->assertEquals('asdasdasdsadasd', $decrypted['FromUserName']);
        $this->assertEquals('1234567898', $decrypted['CreateTime']);
        $this->assertEquals('hello', $decrypted['Content']);
    }

    public function testEncryptAndDecrypt()
    {
        $raw = array(
                'ToUserName' => '测试中文',
                'FromUserName' => 'gh_7f083739789a',
                'CreateTime' => '1407743423',
                'MsgType' => 'video',
                'Video' => array(
                    'MediaId' => 'eYJ1MbwPRJtOvIEabaxHs7TX2D-HV71s79GUxqdUkjm6Gs2Ed1KF3ulAOA9H1xG0',
                    'Title' => 'testCallBackReplyVideo',
                    'Description' => 'testCallBackReplyVideo',
                  ), );
        $xml = XML::build($raw);
        $encrypted = $this->getEncryptor()->encryptMsg($xml, 'xxxxxx', '1407743423');
        $array = XML::parse($encrypted);

        $this->assertEquals('1407743423', $array['TimeStamp']);
        $this->assertEquals('xxxxxx', $array['Nonce']);
        $this->assertNotEmpty($array['Encrypt']);
        $this->assertNotEmpty($array['MsgSignature']);

        $this->assertEquals($raw, $this->getEncryptor()->decryptMsg($array['MsgSignature'], $array['Nonce'], $array['TimeStamp'], $encrypted));
    }
}
