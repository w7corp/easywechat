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
            Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
    }

    public function testGetAllCategories()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/wxopen/getallcategories')
            ->andReturn('mock-result')->once();
        $this->assertSame(
            'mock-result', $this->client->getAllCategories());
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
            ])->andReturn('mock-result')->once();
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
            ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $this->client->deleteCategories(1, 2));
    }

    public function testGetCategories()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/wxopen/getcategory')
            ->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $this->client->getCategories());
    }

    public function testUpdateCategory()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/wxopen/modifycategory', [
                'first' => 1, 'second' => 2, 'categories' => [
                    ['key' => 'name', 'value' => 'media_id'],
                ],
            ])->andReturn('mock-result')->once();
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
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $this->client->setNickname(
            'name', 'card_no', 'media_id', ['stuff_01', 'stuff_02']));
    }

    public function testGetNicknameAuditStatus()
    {
        $this->client->expects()->httpPostJson('wxa/api_wxa_querynickname', [
            'audit_id' => 'audit-id',
        ])->andReturn('mock-result')->once();
        $this->assertSame(
            'mock-result', $this->client->getNicknameAuditStatus('audit-id'));
    }

    public function testIsAvailableNickname()
    {
        $this->client->expects()->httpPostJson('cgi-bin/wxverify/checkwxverifynickname', [
            'nick_name' => 'name',
        ])->andReturn('mock-result')->once();
        $this->assertSame(
            'mock-result', $this->client->isAvailableNickname('name'));
    }
}
