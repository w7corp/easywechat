<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount;

use EasyWeChat\OfficialAccount\Application;
use EasyWeChat\Tests\TestCase;

class ApplicationTest extends TestCase
{
    public function testProperties()
    {
        $app = new Application();

        $this->assertInstanceOf(\EasyWeChat\BasicService\Media\Client::class, $app->media);
        $this->assertInstanceOf(\EasyWeChat\BasicService\Url\Client::class, $app->url);
        $this->assertInstanceOf(\EasyWeChat\BasicService\QrCode\Client::class, $app->qrcode);
        $this->assertInstanceOf(\EasyWeChat\BasicService\Jssdk\Client::class, $app->jssdk);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\Auth\AccessToken::class, $app->access_token);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\Server\Guard::class, $app->server);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\User\UserClient::class, $app->user);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\User\TagClient::class, $app->user_tag);
        $this->assertInstanceOf(\Overtrue\Socialite\Providers\WeChat::class, $app->oauth);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\Menu\Client::class, $app->menu);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\TemplateMessage\Client::class, $app->template_message);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\Material\Client::class, $app->material);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\CustomerService\Client::class, $app->customer_service);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\Semantic\Client::class, $app->semantic);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\DataCube\Client::class, $app->data_cube);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\AutoReply\Client::class, $app->auto_reply);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\Broadcasting\Client::class, $app->broadcasting);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\Card\Client::class, $app->card);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\Device\Client::class, $app->device);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\ShakeAround\Client::class, $app->shake_around);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\Base\Client::class, $app->base);
    }
}
