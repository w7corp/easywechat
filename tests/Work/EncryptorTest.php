<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Work;

use EasyWeChat\Kernel\Encryptor as KernelEncryptor;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Encryptor;

class EncryptorTest extends TestCase
{
    public function test_it_validates_receive_id_against_corp_id()
    {
        $encryptor = new KernelEncryptor(
            appId: 'wrong-corp-id',
            token: 'mock-token',
            aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        );
        $encrypted = $encryptor->encryptAsArray(
            plaintext: '<xml><Content>hello</Content></xml>',
            nonce: 'mock-nonce',
            timestamp: '1714112445',
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid appId.');

        (new Encryptor(
            corpId: 'wx3cf0f39249000060',
            token: 'mock-token',
            aesKey: 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ))->decrypt(
            $encrypted['ciphertext'],
            $encrypted['signature'],
            (string) $encrypted['nonce'],
            (string) $encrypted['timestamp'],
        );
    }
}
