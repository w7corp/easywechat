<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform\Authorizer\MiniProgram\Setting;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Setting\Client;
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

    public function testGetAllCategories()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/wxopen/getallcategories')
            ->andReturn('mock-result');
        $this->assertSame(
            'mock-result',
            $this->client->getAllCategories()
        );
    }

    public function testAddCategories()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/wxopen/addcategory', [
                'categories' => [[
                    'first' => 1, 'second' => 2,
                    'certicates' => [
                        ['key' => 'name', 'value' => 'media_id'],
                    ],
                ]],
            ])->andReturn('mock-result');
        $this->assertSame('mock-result', $this->client->addCategories([[
            'first' => 1, 'second' => 2,
            'certicates' => [
                ['key' => 'name', 'value' => 'media_id'],
            ],
        ]]));
    }

    public function testDeleteCategories()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/wxopen/deletecategory', [
                'first' => 1, 'second' => 2,
            ])->andReturn('mock-result');
        $this->assertSame('mock-result', $this->client->deleteCategories(1, 2));
    }

    public function testGetCategories()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/wxopen/getcategory')
            ->andReturn('mock-result');
        $this->assertSame('mock-result', $this->client->getCategories());
    }

    public function testUpdateCategory()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/wxopen/modifycategory', [
                'first' => 1, 'second' => 2, 'categories' => [
                    ['key' => 'name', 'value' => 'media_id'],
                ],
            ])->andReturn('mock-result');
        $this->assertSame('mock-result', $this->client->updateCategory([
            'first' => 1, 'second' => 2, 'categories' => [
                ['key' => 'name', 'value' => 'media_id'],
            ],
        ]));
    }

    public function testSetNickname()
    {
        $this->client->expects()->httpPostJson('wxa/setnickname', [
            'nick_name' => 'name',
            'id_card' => 'card_no',
            'license' => 'media_id',
            'naming_other_stuff_1' => 'stuff_01',
            'naming_other_stuff_2' => 'stuff_02',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $this->client->setNickname(
            'name',
            'card_no',
            'media_id',
            ['stuff_01', 'stuff_02']
        ));
    }

    public function testGetNicknameAuditStatus()
    {
        $this->client->expects()->httpPostJson('wxa/api_wxa_querynickname', [
            'audit_id' => 'audit-id',
        ])->andReturn('mock-result');
        $this->assertSame(
            'mock-result',
            $this->client->getNicknameAuditStatus('audit-id')
        );
    }

    public function testIsAvailableNickname()
    {
        $this->client->expects()->httpPostJson('cgi-bin/wxverify/checkwxverifynickname', [
            'nick_name' => 'name',
        ])->andReturn('mock-result');
        $this->assertSame(
            'mock-result',
            $this->client->isAvailableNickname('name')
        );
    }

    public function testGetSearchStatus()
    {
        $this->client->expects()->httpGet('wxa/getwxasearchstatus')->andReturn('mock-result');
        $this->assertSame('mock-result', $this->client->getSearchStatus());
    }

    public function testSetSearchable()
    {
        $this->client->expects()->httpPostJson('wxa/changewxasearchstatus', [
            'status' => 0,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $this->client->setSearchable());
    }

    public function testSetUnsearchable()
    {
        $this->client->expects()->httpPostJson('wxa/changewxasearchstatus', [
            'status' => 1,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $this->client->setUnsearchable());
    }

    public function testGetDisplayedOfficialAccount()
    {
        $this->client->expects()->httpGet('wxa/getshowwxaitem')->andReturn('mock-result');
        $this->assertSame('mock-result', $this->client->getDisplayedOfficialAccount());
    }

    public function testSetDisplayedOfficialAccount()
    {
        $this->client->expects()->httpPostJson('wxa/updateshowwxaitem', [
            'appid' => 'app-id',
            'wxa_subscribe_biz_flag' => 1,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $this->client->setDisplayedOfficialAccount('app-id'));
    }

    public function testSetOfficialAccountCantNotDisplayed()
    {
        $this->client->expects()->httpPostJson('wxa/updateshowwxaitem', [
            'appid' => null,
            'wxa_subscribe_biz_flag' => 0,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $this->client->setDisplayedOfficialAccount(false));
    }

    public function testGetDisplayableOfficialAccounts()
    {
        $this->client->expects()->httpGet('wxa/getwxamplinkforshow', [
            'page' => 1,
            'num' => 10,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $this->client->getDisplayableOfficialAccounts(1, 10));
    }
}
