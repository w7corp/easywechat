<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform\Authorizer\MiniProgram\Account;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Account\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function setUp()
    {
        $this->client = $this->mockApiClient(
            Client::class,
            [],
            new ServiceContainer(['app_id' => 'app-id'])
        );
    }

    public function testGetBasicInfo()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/account/getaccountbasicinfo')
            ->andReturn('mock-result');
        $this->assertSame('mock-result', $this->client->getBasicInfo());
    }

    public function testUpdateAvatar()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/account/modifyheadimage', [
                'head_img_media_id' => 'media-id',
                'x1' => 0, 'y1' => 0, 'x2' => 1, 'y2' => 1,
            ])->andReturn('mock-result');
        $this->assertSame(
            'mock-result',
            $this->client->updateAvatar('media-id')
        );
    }

    public function testUpdateSignature()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/account/modifysignature', [
                'signature' => 'signature',
            ])->andReturn('mock-result');
        $this->assertSame(
            'mock-result',
            $this->client->updateSignature('signature')
        );
    }
}
