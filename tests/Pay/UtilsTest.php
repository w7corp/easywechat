<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Pay;

use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Support\PrivateKey;
use EasyWeChat\Kernel\Support\PublicKey;
use EasyWeChat\Pay\Exceptions\EncryptionFailureException;
use EasyWeChat\Pay\Merchant;
use EasyWeChat\Pay\Utils;
use EasyWeChat\Tests\TestCase;

class UtilsTest extends TestCase
{
    /**
     * @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=20_1
     * MD5签名名方式
     * 1.对参数按照key=value的格式，并按照参数名ASCII字典序排序生成字符串：
     * appId=mock-appid&nonceStr=mock-nonce&package=prepay_id=mock-prepay-id&signType=MD5&timeStamp=1601234567
     *
     * 2.连接密钥key：
     * appId=mock-appid&nonceStr=mock-nonce&package=prepay_id=mock-prepay-id&signType=MD5&timeStamp=1601234567&key=mock-v2-secret-key
     *
     * 3.生成sign并转成大写：
     * sign=C52D6B09E8A039D6E8696A014BB37160
     *
     * HMAC-SHA256签名方式
     * 1.对参数按照key=value的格式，并按照参数名ASCII字典序排序生成字符串：
     * appId=mock-appid&nonceStr=mock-nonce&package=prepay_id=mock-prepay-id&signType=HMAC-SHA256&timeStamp=1601234567
     *
     * 2.连接密钥key：
     * appId=mock-appid&nonceStr=mock-nonce&package=prepay_id=mock-prepay-id&signType=HMAC-SHA256&timeStamp=1601234567&key=mock-v2-secret-key
     *
     * 3.生成sign并转成大写：
     * sign=BAC9240577E86EDC7753264E502196C61F78F24777E9E7CCE82A7BD97F906EED
     */
    public function test_create_v2_signature()
    {
        $params = [
            'appId' => 'mock-appid',
            'timeStamp' => 1601234567,
            'nonceStr' => 'mock-nonce',
            'package' => 'prepay_id=mock-prepay-id',
            'signType' => 'MD5',
        ];

        $merchant = \Mockery::mock(Merchant::class);
        $merchant->allows()->getV2SecretKey()->andReturn('mock-v2-secret-key');

        $utils = new Utils(merchant: $merchant);

        $this->assertSame('C52D6B09E8A039D6E8696A014BB37160', $utils->createV2Signature($params));

        $params['signType'] = 'HMAC-SHA256';
        $this->assertSame('BAC9240577E86EDC7753264E502196C61F78F24777E9E7CCE82A7BD97F906EED', $utils->createV2Signature($params));
    }

    public function test_encrypt_with_rsa_public_key()
    {
        $privateKey = new PrivateKey('mock-private-key');
        $publicKey = \Mockery::mock(PublicKey::class);

        $merchant = new Merchant(
            mchId: 100001,
            privateKey: $privateKey,
            certificate: $publicKey,
            secretKey: 'v3SecretKey',
            v2SecretKey: 'v2SecretKey',
            platformCerts: [
                'PUB_KEY_ID_MOCK' => '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlReZ1YnfAohRIfUqIeyP
aO0PlkMw1RLPdZbEZmldbGrIrOh/0XqSzNZ+mtB6H0eB7TSaoGFtdp/AWy3tb67m
1T62OrEhz6bnSKMcZkYVmODyxZvcwsCZ3zqCaFo7FrGmh1o9M0/Xfa5SOX4jVGni
3iM7r7YD/NiW2RCYDtjMoLTmVgrzv45Mzu2XpJqtNbUJIRRhVSnjsAZRC6spWH+b
QpYIkVd4qmYE0qdpIQBMYOV1w7v1pYn6Z5QdKG4keemADTn4QaZZHrryTcHNYVsZ
2OZ3aybrevSV3wDGnYGk2nt2xtkdfaNfFn4dGW+p4an5M4fRK+CnYpeTgI6POABk
pwIDAQAB
-----END PUBLIC KEY-----',
            ]
        );
        $utils = new Utils(merchant: $merchant);
        $this->assertIsString($utils->encryptWithRsaPublicKey('mock-plaintext', 'PUB_KEY_ID_MOCK'));
        $this->assertIsString($utils->encryptWithRsaPublicKey('mock-plaintext'));

        $this->expectException(InvalidConfigException::class);
        $utils->encryptWithRsaPublicKey('mock-plaintext', 'PUB_KEY_ID_OTHER');

        $this->expectException(EncryptionFailureException::class);
        $utils->encryptWithRsaPublicKey('', 'PUB_KEY_ID_MOCK');

    }
}
