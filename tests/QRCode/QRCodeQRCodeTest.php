<?php

use EasyWeChat\Core\Http;
use EasyWeChat\QRCode\QRCode;
use EasyWeChat\Support\Collection;

class QRCodeQRCodeTest extends TestCase
{
    /**
     * Test forever();
     */
    public function testForever()
    {
        $http = Mockery::mock(Http::class);
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('json')->andReturnUsing(function($api, $params){
            return compact('api', 'params');
        });

        $qrcode = new QRCode($http);

        // test create
        $this->assertInstanceOf(Collection::class, $qrcode->forever(56));

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
     * Test temporary();
     */
    public function testTemporary()
    {
        $http = Mockery::mock(Http::class);
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('json')->andReturnUsing(function($api, $params){
            return compact('api', 'params');
        });

        $qrcode = new QRCode($http);

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
     * Test card()
     */
    public function testCard()
    {
        $http = Mockery::mock(Http::class);
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('json')->andReturnUsing(function($api, $params){
            return compact('api', 'params');
        });

        $qrcode = new QRCode($http);

        // $card = ['foo' => 'bar']
        $params = $qrcode->card(['foo' => 'bar'])['params'];
        $this->assertEquals(QRCode::SCENE_QR_CARD, $params['action_name']);
        $this->assertEquals(['card' => ['foo' => 'bar']], $params['action_info']['scene']);
    }

    /**
     * Test show()
     */
    public function testShow()
    {
        $http = Mockery::mock(Http::class);
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('json')->andReturnUsing(function($api, $params){
            return compact('api', 'params');
        });

        $qrcode = new QRCode($http);

        $ticket = 'foo';

        $this->assertEquals(QRCode::API_SHOW."?ticket={$ticket}", $qrcode->show($ticket));
    }
}