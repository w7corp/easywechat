<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\QRCode\QRCode;

class QRCodeQRCodeTest extends TestCase
{
    public function getQRCode()
    {
        $accessToken = Mockery::mock('EasyWeChat\Core\AccessToken');
        $accessToken->shouldReceive('getQueryFields')->andReturn(['access_token' => 'foo']);
        $qrcode = Mockery::mock('EasyWeChat\QRCode\QRCode[parseJSON]', [$accessToken]);
        $qrcode->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => empty($params[1]) ? null : $params[1],
            ];
        });

        return $qrcode;
    }

    /**
     * Test forever();.
     */
    public function testForever()
    {
        $qrcode = $this->getQRCode();

        // normal
        $params = $qrcode->forever(5)['params'];
        $this->assertEquals(QRCode::SCENE_QR_FOREVER, $params['action_name']);
        $this->assertEquals(['scene_id' => 5], $params['action_info']['scene']);

        // <= 0
        $params = $qrcode->forever(0)['params'];
        $this->assertEquals(QRCode::SCENE_QR_FOREVER_STR, $params['action_name']);
        $this->assertEquals(['scene_str' => 0], $params['action_info']['scene']);

        // > QRCode::SCENE_MAX_VALUE
        $params = $qrcode->forever(QRCode::SCENE_MAX_VALUE)['params'];
        $this->assertEquals(QRCode::SCENE_QR_FOREVER_STR, $params['action_name']);
        $this->assertEquals(['scene_str' => QRCode::SCENE_MAX_VALUE], $params['action_info']['scene']);
    }

    /**
     * Test temporary();.
     */
    public function testTemporary()
    {
        $qrcode = $this->getQRCode();

        // empty expire_seconds
        $params = $qrcode->temporary(5)['params'];
        $this->assertEquals(QRCode::DAY * 7, $params['expire_seconds']);

        // expire_seconds = 2400
        $params = $qrcode->temporary(5, 2400)['params'];
        $this->assertEquals(2400, $params['expire_seconds']);

        // $senceId = 'str'
        $params = $qrcode->temporary('str')['params'];
        $this->assertEquals(QRCode::SCENE_QR_TEMPORARY, $params['action_name']);
        $this->assertEquals(['scene_id' => 0], $params['action_info']['scene']);

        // $senceId = 1
        $params = $qrcode->temporary(1)['params'];
        $this->assertEquals(QRCode::SCENE_QR_TEMPORARY, $params['action_name']);
        $this->assertEquals(['scene_id' => 1], $params['action_info']['scene']);
    }

    /**
     * Test card().
     */
    public function testCard()
    {
        $qrcode = $this->getQRCode();

        // $card = ['foo' => 'bar']
        $params = $qrcode->card(['foo' => 'bar'])['params'];
        $this->assertEquals(QRCode::SCENE_QR_CARD, $params['action_name']);
        $this->assertEquals(['card' => ['foo' => 'bar']], $params['action_info']['scene']);
    }

    /**
     * Test url().
     */
    public function testUrl()
    {
        $qrcode = $this->getQRCode();

        $ticket = 'foo';

        $this->assertEquals(QRCode::API_SHOW."?ticket={$ticket}", $qrcode->url($ticket));
    }
}
