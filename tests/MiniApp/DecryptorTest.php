<?php

namespace EasyWeChat\Tests\MiniApp;

use EasyWeChat\Kernel\Exceptions\DecryptException;
use EasyWeChat\MiniApp\Decryptor;
use EasyWeChat\Tests\TestCase;

class DecryptorTest extends TestCase
{
    public function test_it_can_decrypt_message()
    {
        $encryptedData = 'CiyLU1Aw2KjvrjMdj8YKliAjtP4gsMZMQmRzooG2xrDcvSnxIMXFufNstNGTyaGS9uT5geRa0W4oTOb1WT7fJlAC+oNPdbB+3hVbJSRgv+4lGOETKUQz6OYStslQ142d
                NCuabNPGBzlooOmB231qMM85d2/fV6ChevvXvQP8Hkue1poOFtnEtpyxVLW1zAo6/1Xx1COxFvrc2d7UL/lmHInNlxuacJXwu0fjpXfz/YqYzBIBzD6WUfTIF9GRHpOn
                /Hz7saL8xz+W//FRAUid1OksQaQx4CMs8LOddcQhULW4ucetDf96JcR3g0gfRK4PC7E/r7Z6xNrXd2UIeorGj5Ef7b1pJAYB6Y5anaHqZ9J6nKEBvB4DnNLIVWSgARns
                /8wR2SiRS7MNACwTyrGvt9ts8p12PKFdlqYTopNHR1Vf7XjfhQlVsAJdNiKdYmYVoKlaRv85IfVunYzO0IKXsyl7JCUjCpoG20f0a04COwfneQAGGwd5oa+T8yO5hzuyDb/XcxxmK01EpqOyuxINew==';

        $decrypted = Decryptor::decrypt('tiihtNczf5v6AKRyjwEUhQ==', 'r7BXXKkLb8qrSNn05n0qiA==', $encryptedData);

        $this->assertSame([
            'openId' => 'oGZUI0egBJY1zhBYw2KhdUfwVJJE',
            'nickName' => 'Band',
            'gender' => 1,
            'language' => 'zh_CN',
            'city' => 'Guangzhou',
            'province' => 'Guangdong',
            'country' => 'CN',
            'avatarUrl' => 'http://wx.qlogo.cn/mmopen/vi_32/aSKcBBPpibyKNicHNTMM0qJVh8Kjgiak2AHWr8MHM4WgMEm7GFhsf8OYrySdbvAMvTsw3mo8ibKicsnfN5pRjl1p8HQ/0',
            'unionId' => 'ocMvos6NjeKLIBqg5Mr9QjxrP1FA',
            'watermark' => [
                'timestamp' => 1477314187,
                'appid' => 'wx4f4bc4dec97d474b',
            ],
        ], $decrypted);
    }

    public function test_it_will_throw_exception_when_payload_is_invalid()
    {
        $encryptedData = 'aaaaCiyLU1Aw2KjvrjMdj8YKliAjtP4gsMZMQmRzooG2xrDcvSnxIMXFufNstNGTyaGS9uT5geRa0W4oTOb1WT7fJlAC+oNPdbB+3hVbJSRgv+4lGOETKUQz6OYStslQ142d
                NCuabNPGBzlooOmB231qMM85d2/fV6ChevvXvQP8Hkue1poOFtnEtpyxVLW1zAo6/1Xx1COxFvrc2d7UL/lmHInNlxuacJXwu0fjpXfz/YqYzBIBzD6WUfTIF9GRHpOn
                /Hz7saL8xz+W//FRAUid1OksQaQx4CMs8LOddcQhULW4ucetDf96JcR3g0gfRK4PC7E/r7Z6xNrXd2UIeorGj5Ef7b1pJAYB6Y5anaHqZ9J6nKEBvB4DnNLIVWSgARns
                /8wR2SiRS7MNACwTyrGvt9ts8p12PKFdlqYTopNHR1Vf7XjfhQlVsAJdNiKdYmYVoKlaRv85IfVunYzO0IKXsyl7JCUjCpoG20f0a04COwfneQAGGwd5oa+T8yO5hzuyDb/XcxxmK01EpqOyuxINew==';

        try {
            Decryptor::decrypt('tiihtNczf5v6AKRyjwEUhQ==', 'r7BXXKkLb8qrSNn05n0qiA==', $encryptedData);
        } catch (\Throwable $e) {
            $this->assertInstanceOf(DecryptException::class, $e);
            $this->assertStringStartsWith('The given payload is invalid:', $e->getMessage());
        }
    }
}
