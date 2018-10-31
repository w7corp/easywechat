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

    public function testGetBasicInfo()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/account/getaccountbasicinfo')
            ->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $this->client->getBasicInfo());
    }

    public function testModifyHeadImage()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/account/modifyheadimage', [
                'head_img_media_id' => 'media-id',
                'x1' => 0, 'y1' => 0, 'x2' => 1, 'y2' => 1,
            ])->andReturn('mock-result')->once();
        $this->assertSame(
            'mock-result', $this->client->modifyHeadImage('media-id'));
    }

    public function testModifySignature()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/account/modifysignature', [
                'signature' => 'signature',
            ])->andReturn('mock-result')->once();
        $this->assertSame(
            'mock-result', $this->client->modifySignature('signature'));
    }

    public function testGetAllCategories()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/wxopen/getallcategories')
            ->andReturn('mock-result')->once();
        $this->assertSame(
            'mock-result', $this->client->getAllCategories());
    }

    public function testAddCategory()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/wxopen/addcategory', [
                'first' => 1, 'second' => 2, 'categories' => [
                    ['key' => 'name', 'value' => 'media_id'],
                ],
            ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $this->client->addCategory(1, 2, [
            'name' => 'media_id',
        ]));
    }

    public function testDeleteCategory()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/wxopen/deletecategory', [
                'first' => 1, 'second' => 2,
            ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $this->client->deleteCategory(1, 2));
    }

    public function testGetCategory()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/wxopen/getcategory')
            ->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $this->client->getCategory());
    }

    public function testModifyCategory()
    {
        $this->client->expects()
            ->httpPostJson('cgi-bin/wxopen/modifycategory', [
                'first' => 1, 'second' => 2, 'categories' => [
                    ['key' => 'name', 'value' => 'media_id'],
                ],
            ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $this->client->modifyCategory(1, 2, [
            'name' => 'media_id',
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

    public function testQueryNickname()
    {
        $this->client->expects()->httpPostJson('wxa/api_wxa_querynickname', [
            'audit_id' => 'audit-id',
        ])->andReturn('mock-result')->once();
        $this->assertSame(
            'mock-result', $this->client->queryNickname('audit-id'));
    }

    public function testCheckWxVerifyNickname()
    {
        $this->client->expects()->httpPostJson('cgi-bin/wxverify/checkwxverifynickname', [
            'nick_name' => 'name',
        ])->andReturn('mock-result')->once();
        $this->assertSame(
            'mock-result', $this->client->checkWxVerifyNickname('name'));
    }
}
