<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram;

use EasyWeChat\MiniProgram\Encryption\Encryptor;
use EasyWeChat\MiniProgram\Server\Guard;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class MiniProgramServerGuardTest extends TestCase
{
    public function getServer($queries = [], $content = null)
    {
        $queries = array_merge([
            'signature' => 'cdfbda2936babf735d1b07a5995d3b9968445a9f',
            'timestamp' => '1496847943',
            'nonce' => '1801816940',
        ], $queries);

        $guard = new Guard('MMAA6n6V5g8GkKUUirtJFDCiPFvjXTuA', new Request($queries, [], [], [], [], [], $content));
        $encryptor = new Encryptor('wx94c788233e761510', 'MMAA6n6V5g8GkKUUirtJFDCiPFvjXTuA', 'FE7ZQktQ0kTlxI0gYAMtbO2OuooCHzKmhCDbSGMwpAW');
        $guard->setEncryptor($encryptor);

        return $guard;
    }

    public function testValidateRequest()
    {
        $result = $this->getServer(['echostr' => '3804283725124844375'])->serve();

        $this->assertSame('3804283725124844375', $result->getContent());
    }

    public function testDecryptMsg()
    {
        $json = '{"ToUserName":"gh_8f8e866d31ea","Encrypt":"mSHmxAtI0rrRAQBMA8s5q3oLBhbygmwEkY60MWCVf7Nlp9emfwDTSa20Phk9qdvFbfn8izKcehqQGBOn+7MBp8L/PMGHK8Rc/KESOu+ITB8JYTAgp6yL6Ld+tURg9JLR+qfNmYawUmmO+undQbYLh0XSylKJzSPNhzjCFtijBUZqXJZmrMjISkRHzTG+mLu6p6PMHqqMIig15BqT3yk7/m8aBTmtaMNpMGKQTNoUFwu2AV1kxUZvm9VTtdFXA+EU0VSekgyWsQgspisR5+eTuiCC0GXAaSKmL4bCDi2FNtI="}';
        $server = $this->getServer([
            'encrypt_type' => 'aes',
            'msg_signature' => '885573aacfe510e21a433c8e6cc54325544aa452',
        ], $json);

        $result = $server->setMessageHandler(function ($message) {
            $this->assertSame('gh_8f8e866d31ea', $message->ToUserName);
            $this->assertSame('oCdsX0cP7_xE_49eP4zUnC1CtmzU', $message->FromUserName);
            $this->assertSame('1496847943', $message->CreateTime);
            $this->assertSame('user_enter_tempsession', $message->Event);
        })->serve();
        $this->assertSame('success', $result->getContent());
    }
}
