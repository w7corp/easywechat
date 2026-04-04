<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OpenWork;

use EasyWeChat\Kernel\Encryptor as KernelEncryptor;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\OpenWork\Encryptor;
use EasyWeChat\OpenWork\SuiteEncryptor;
use EasyWeChat\Tests\TestCase;

class EncryptorTest extends TestCase
{
    public function test_provider_encryptor_validates_receive_id_against_corp_id()
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

    public function test_suite_encryptor_validates_receive_id_against_suite_id()
    {
        $encryptor = new KernelEncryptor(
            appId: 'wrong-suite-id',
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

        (new SuiteEncryptor(
            suiteId: 'suite-id',
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
