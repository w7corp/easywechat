<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Wedrive;

use EasyWeChat\Kernel\Support\Arr;
use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Wedrive\SpaceClient;

class SpaceClientTest extends TestCase
{
    public function testCreate()
    {
        $client = $this->mockApiClient(SpaceClient::class);

        $client->expects()->httpPostJson('cgi-bin/wedrive/space_create', [
            'userid' => 'test_userid',
            'space_name' => 'new_space',
            'auth_info' => [],
            'space_sub_type' => 0,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->create('test_userid', 'new_space'));

        $authInfo = [
            "type" => 1,
            "userid" => "USERID",
            "auth" => 2
        ];
        $client->expects()->httpPostJson('cgi-bin/wedrive/space_create', [
            'userid' => 'test_userid',
            'space_name' => 'new_space',
            'auth_info' => [$authInfo],
            'space_sub_type' => 1,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->create('test_userid', 'new_space', $authInfo, 1));

        $authInfos = [
            [
                "type" => 1,
                "userid" => "USERID",
                "auth" => 2
            ],
            [
                "type" => 2,
                "departmentid" => "DEPARTMENTID",
                "auth" => 200,
                "customize_auth" => [
                    "enable_operation_upload" => true,
                    "enable_operation_delete" => true,
                ]
            ]
        ];
        $client->expects()->httpPostJson('cgi-bin/wedrive/space_create', [
            'userid' => 'test_userid',
            'space_name' => 'new_space',
            'auth_info' => $authInfos,
            'space_sub_type' => 1,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->create('test_userid', 'new_space', $authInfos, 1));
    }

    public function testRename()
    {
        $client = $this->mockApiClient(SpaceClient::class);

        $client->expects()->httpPostJson('cgi-bin/wedrive/space_rename', [
            'userid' => 'test_userid',
            'spaceid' => 'SPACEID',
            'space_name' => 'space_new_name'
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->rename('test_userid', 'SPACEID', 'space_new_name'));
    }

    public function testDismiss()
    {
        $client = $this->mockApiClient(SpaceClient::class);

        $client->expects()->httpPostJson('cgi-bin/wedrive/space_dismiss', [
            'userid' => 'test_userid',
            'spaceid' => 'SPACEID',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->dismiss('test_userid', 'SPACEID'));
    }

    public function testInfo()
    {
        $client = $this->mockApiClient(SpaceClient::class);

        $client->expects()->httpPostJson('cgi-bin/wedrive/space_info', [
            'userid' => 'test_userid',
            'spaceid' => 'SPACEID',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->info('test_userid', 'SPACEID'));
    }

    public function testShare()
    {
        $client = $this->mockApiClient(SpaceClient::class);

        $client->expects()->httpPostJson('cgi-bin/wedrive/space_share', [
            'userid' => 'test_userid',
            'spaceid' => 'SPACEID',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->share('test_userid', 'SPACEID'));
    }

    public function testSetting()
    {
        $client = $this->mockApiClient(SpaceClient::class);

        $settings = [
            'enable_watermark' => true,
            'share_url_no_approve' => true,
            'default_file_scope' => 1
        ];
        $client->expects()->httpPostJson('cgi-bin/wedrive/space_setting', array_merge([
            'userid' => 'test_userid',
            'spaceid' => 'SPACEID',
        ], $settings))->andReturn('mock-result');
        $this->assertSame('mock-result', $client->setting('test_userid', 'SPACEID', $settings));
    }

    public function testAclAdd()
    {
        $client = $this->mockApiClient(SpaceClient::class);

        $authInfo = [
            'type' => 1,
            'userid' => 'USERID1',
            'auth' => 1
        ];
        $client->expects()->httpPostJson('cgi-bin/wedrive/space_acl_add', [
            'userid' => 'test_userid',
            'spaceid' => 'SPACEID',
            'auth_info' => Arr::isAssoc($authInfo) ? [$authInfo] : $authInfo
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->aclAdd('test_userid', 'SPACEID', $authInfo));

        $authInfos = [
            [
                'type' => 1,
                'userid' => 'USERID1',
                'auth' => 1
            ],
            [
                'type' => 2,
                'userid' => 'DEPARTMENTID1',
                'auth' => 2
            ]
        ];
        $client->expects()->httpPostJson('cgi-bin/wedrive/space_acl_add', [
            'userid' => 'test_userid',
            'spaceid' => 'SPACEID',
            'auth_info' => $authInfos
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->aclAdd('test_userid', 'SPACEID', $authInfos));
    }

    public function testAclDel()
    {
        $client = $this->mockApiClient(SpaceClient::class);

        $authInfo = [
            'type' => 1,
            'userid' => 'USERID1',
        ];
        $client->expects()->httpPostJson('cgi-bin/wedrive/space_acl_del', [
            'userid' => 'test_userid',
            'spaceid' => 'SPACEID',
            'auth_info' => Arr::isAssoc($authInfo) ? [$authInfo] : $authInfo
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->aclDel('test_userid', 'SPACEID', $authInfo));

        $authInfos = [
            [
                'type' => 1,
                'userid' => 'USERID1',
            ],
            [
                'type' => 2,
                'userid' => 'DEPARTMENTID1',
            ]
        ];
        $client->expects()->httpPostJson('cgi-bin/wedrive/space_acl_del', [
            'userid' => 'test_userid',
            'spaceid' => 'SPACEID',
            'auth_info' => $authInfos
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->aclDel('test_userid', 'SPACEID', $authInfos));
    }

}
