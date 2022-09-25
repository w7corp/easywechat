<?php

namespace EasyWeChat\Tests\MiniApp;

use EasyWeChat\MiniApp\Application;
use EasyWeChat\MiniApp\Utils;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class UtilsTest extends TestCase
{
    public function test_code_to_session()
    {
        $response = [
            'openid' => 'o6_bmjrPTlm6_2sgVt7hMZOPxxxx',
            'session_key' => 'tiihtNczf5v6AKRyjwExxxx=',
            'unionid' => 'o6_bmasdasdsad6_2sgVt7hMZOxxxx',
            'errcode' => 0,
            'errmsg' => 'ok',
        ];

        $httpClient = new MockHttpClient(new MockResponse(json_encode($response)));

        $app = new Application([
            'app_id' => 'mock-appid',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'mock-aes_key',
        ]);
        $app->setHttpClient($httpClient);

        $utils = new Utils($app);

        $result = $utils->codeToSession('mock-js-code');

        $this->assertSame($response, $result);
    }

    public function test_decrypt_session()
    {
        $sessionKey = 'tiihtNczf5v6AKRyjwEUhQ==';

        $encryptedData = 'CiyLU1Aw2KjvrjMdj8YKliAjtP4gsMZM
                QmRzooG2xrDcvSnxIMXFufNstNGTyaGS
                9uT5geRa0W4oTOb1WT7fJlAC+oNPdbB+
                3hVbJSRgv+4lGOETKUQz6OYStslQ142d
                NCuabNPGBzlooOmB231qMM85d2/fV6Ch
                evvXvQP8Hkue1poOFtnEtpyxVLW1zAo6
                /1Xx1COxFvrc2d7UL/lmHInNlxuacJXw
                u0fjpXfz/YqYzBIBzD6WUfTIF9GRHpOn
                /Hz7saL8xz+W//FRAUid1OksQaQx4CMs
                8LOddcQhULW4ucetDf96JcR3g0gfRK4P
                C7E/r7Z6xNrXd2UIeorGj5Ef7b1pJAYB
                6Y5anaHqZ9J6nKEBvB4DnNLIVWSgARns
                /8wR2SiRS7MNACwTyrGvt9ts8p12PKFd
                lqYTopNHR1Vf7XjfhQlVsAJdNiKdYmYV
                oKlaRv85IfVunYzO0IKXsyl7JCUjCpoG
                20f0a04COwfneQAGGwd5oa+T8yO5hzuy
                Db/XcxxmK01EpqOyuxINew==';

        $iv = 'r7BXXKkLb8qrSNn05n0qiA==';

        $app = new Application([
            'app_id' => 'mock-appid',
            'secret' => 'mock-secret',
            'token' => 'mock-token',
            'aes_key' => 'mock-aes_key',
        ]);

        $utils = new Utils($app);

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
        ], $utils->decryptSession($sessionKey, $iv, $encryptedData));
    }
}
