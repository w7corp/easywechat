<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Kernel\Support;

use EasyWeChat\Kernel\Support\AesEcb;
use EasyWeChat\Kernel\Support\Xml;
use EasyWeChat\Tests\TestCase;

class AesEcbTest extends TestCase
{
    public function test_it_can_encrypt_and_decrypt()
    {
        $key = md5('1234567890abcdef1234567890abcdef');
        $plaintext = '<root><out_refund_no><![CDATA[123]]></out_refund_no><out_trade_no><![CDATA[abc]]></out_trade_no><refund_account><![CDATA[REFUND_SOURCE_UNSETTLED_FUNDS]]></refund_account><refund_fee><![CDATA[1]]></refund_fee><refund_id><![CDATA[50000303712017072701466990000]]></refund_id><refund_recv_accout><![CDATA[用户零钱]]></refund_recv_accout><refund_request_source><![CDATA[API]]></refund_request_source><refund_status><![CDATA[SUCCESS]]></refund_status><settlement_refund_fee><![CDATA[1]]></settlement_refund_fee><settlement_total_fee><![CDATA[1]]></settlement_total_fee><success_time><![CDATA[2017-07-27 00:33:09]]></success_time><total_fee><![CDATA[1]]></total_fee><transaction_id><![CDATA[4005302001201707272838220000]]></transaction_id></root>';
        $ciphertext = 'm+cqC5OWNek/jGSeSFjMwxqoQmt9x/XQEPRosjYHR2a6kosdkuS2hFlt1QP/ykVOCm6PXnC0tOc7gsPzP3aG4Hhn7wJwJAvMhSUJhOAOlnXtHupbyiwb9vNgqAwcSr04U1yoI8UemDCz+TnIsbldurZh6UKtDqSutp/KiutgJ/9crss+fh9hy9UcBKO60JkJgka79q6lkQoOKZh3kIHrFEZGFKcvCOJzx/heVtz8AHGoB/IuNV4Mh280FZM1TTe8V54eXgqHNAOdJCoYQuKu34tepA+a4sjCcPOmNU5wLCjEFQ/+w7Ad8U2i3bfaA713DPk5qV8IVSB1cMGZj+zZBGPT4OWBg0vZD4ZJCydf93e95CbxV7FuSPiFnZwjvsHBCA7DNGoAfSx72p5ZBcyCTFV9y4O9xTukHUmJNI8XK+JhR5Imz9u5422lfN5FcM6g4WdLDTVO/DiN4chaTUk9uqEiMqD2Bn3+ZWe/R91YDW8koG3qd7m/9y7sckptNQWU9fi+zk/AbCLHETiUIj4dtFxsZRTBUFIEmSl2ebcPEdowOLzjUe2uW/Qr8dDwFuWGsSYawYnsbsxliNc5DthzhcdB9MDkOab6hckUIC7639s6DKFP44Olgjc+tt5EfDpNxK0rHh4rhlCz9h5ZhzI7CVqJRx5pCLEBaJKntPeF9IWfj92VYMY7o8TesmqhiDWnGVGSK8vXDsQMAWIhHi2STvdVAZkaOTzF+cxVJsUgy2zVlGhNzlqjQZmLoXZ/Kiid7cfUQvg/Bqw4We6KRrAfTHplwjbghjVzqWsgJv8KI3cYjFJEtTy19a0z3yBrcthtjszmBEUyUG/d4O0DzGEG+JNB4VsMz/jWUJ2d2uJmpyvngyt5RkafRH4mCHWkNTPz5UCBZtJbvFzQ/VB/X077Apwgk7lfgULu1uVF9N108kRKQcrqoGEH/oZ6lo0wIXbITx+7lr91eMvu4JgGPSQW4MKbluSa28iwkE5YcnQbYd0=';

        // encrypt
        $this->assertSame($ciphertext, AesEcb::encrypt($plaintext, $key));

        // decrypt
        $xml = Xml::parse(AesEcb::decrypt($ciphertext, $key));
        $this->assertSame('123', $xml['out_refund_no']);
    }
}
