<?php

use EasyWeChat\Support\XML;
use EasyWeChat\Encryption\Encryptor;

class EncryptionEncryptorTest extends PHPUnit_Framework_TestCase
{
    public function getEncryptor()
    {
        return new Encryptor('wxb11529c136998cb6', 'pamtest', 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG');
    }

    public function testDecrypt()
    {
        $encrypted = '<xml>
<Encrypt><![CDATA[LNa1abdO4/gDi4KsEBFYTkUNxX2305W30BnbbCOaO7NSgbK7x0TRfipWgvOq5iduE5O0ReCJPhvVWN/m5BRSWNiEUj9fF/lmqf03MvPz+fI7Pnz1a4JlIPItIsgZ3nlpweWF3jFqA+6TYtyOvp0IuuyyPllovLanwGdjjF5KQhRTJh+PvdRD7k4ZYa010qsUhoOwWuFfbJ3L4dlnAC7hmn7AbcQoyaRi1YitINwcqbyxiFnXO/WzPM3YIvg1WaxDjczZwLPl1Nk6FbpbKy/qzatuEUefnPEqWbN5VFgIC34otxBfdMVLPApbTlJn1/NzaZgA//4eYNrUwE/WAjuff6tpuCHcs0xeIpUbUzf11n60RcFZ0cZb9meVhpBCrYElbQwS6UIA7RqJlgmID7W3i4xrPRp0ZYUCY6SAp8uPpd3UJG11OPplODZNym8caZh7JDbsTegyco/SonO0Db+EM0rDUdosC/UxfdKTY6XAlYDA3lwQQ/vKg0o6S7XcIJijK3xDakotfeo44FmLM147NlP6/ozcqAMscRVcExJv7pArcNBazEJTnyU5iS3GhWRrUnLHrUzu3/cg0ZF/XBkrpJox1NB+beyw/KZkx9vKvL8=]]></Encrypt>
<MsgSignature><![CDATA[fe770a375568b46d29aed2d7071846524a289b32]]></MsgSignature>
<TimeStamp>1409304348</TimeStamp>
<Nonce><![CDATA[xxxxxx]]></Nonce>
</xml>';

        $decrypted = $this->getEncryptor()->decryptMsg('fe770a375568b46d29aed2d7071846524a289b32', 'xxxxxx', '1409304348', $encrypted);
        $this->assertEquals('测试中文', $decrypted['ToUserName']);
        $this->assertEquals('gh_7f083739789a', $decrypted['FromUserName']);
        $this->assertEquals('1407743423', $decrypted['CreateTime']);
        $this->assertEquals('eYJ1MbwPRJtOvIEabaxHs7TX2D-HV71s79GUxqdUkjm6Gs2Ed1KF3ulAOA9H1xG0', $decrypted['Video']['MediaId']);
    }

    public function testEncryptAndDecrypt()
    {
        $raw = array(
                'ToUserName' => '测试中文',
                'FromUserName' => 'gh_7f083739789a',
                'CreateTime' => '1407743423',
                'MsgType' => 'video',
                'Video' =>
                  array(
                    'MediaId' => 'eYJ1MbwPRJtOvIEabaxHs7TX2D-HV71s79GUxqdUkjm6Gs2Ed1KF3ulAOA9H1xG0',
                    'Title' => 'testCallBackReplyVideo',
                    'Description' => 'testCallBackReplyVideo',
                  ));
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