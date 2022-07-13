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

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Wedrive\FileClient;

class FileClientTest extends TestCase
{
    public function testCreate()
    {
        $client = $this->mockApiClient(FileClient::class);
        $client->expects()->httpPostJson('cgi-bin/wedrive/file_create', [
            'userid' => 'test',
            'spaceid' => 'test_spaceid',
            'fatherid' => 'test_fatherid',
            'file_type' => 1,
            'file_name' => 'test_field'
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->create('test', 'test_spaceid', 'test_fatherid', 1, 'test_field'));
    }

    public function testRename()
    {
        $client = $this->mockApiClient(FileClient::class);
        $client->expects()->httpPostJson('cgi-bin/wedrive/file_rename', [
            'userid' => 'test_user',
            'fileid' => 'test_fileid',
            'new_name' => 'new_name'
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->rename('test_user', 'test_fileid', 'new_name'));
    }

    public function testMove()
    {
        $client = $this->mockApiClient(FileClient::class);

        $client->expects()->httpPostJson('cgi-bin/wedrive/file_move', [
            'userid' => 'test_user',
            'fileid' => ['test_fileid'],
            'fatherid' => 'test_fatherid',
            'replace' => false,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->move('test_user', 'test_fileid', 'test_fatherid'));

        $client->expects()->httpPostJson('cgi-bin/wedrive/file_move', [
            'userid' => 'test_user',
            'fileid' => ['test_fileid1', 'test_fileid2'],
            'fatherid' => 'test_fatherid',
            'replace' => false,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->move('test_user', ['test_fileid1', 'test_fileid2'], 'test_fatherid'));

        $client->expects()->httpPostJson('cgi-bin/wedrive/file_move', [
            'userid' => 'test_user',
            'fileid' => ['test_fileid'],
            'fatherid' => 'test_fatherid',
            'replace' => true,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->move('test_user', ['test_fileid'], 'test_fatherid', true));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(FileClient::class);

        $client->expects()->httpPostJson('cgi-bin/wedrive/file_delete', [
            'userid' => 'test_user',
            'fileid' => ['test_fileid'],
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->delete('test_user', 'test_fileid'));

        $client->expects()->httpPostJson('cgi-bin/wedrive/file_delete', [
            'userid' => 'test_user',
            'fileid' => ['test_fileid1', 'test_fileid2'],
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->delete('test_user', ['test_fileid1', 'test_fileid2']));
    }

    public function testList()
    {
        $client = $this->mockApiClient(FileClient::class);

        $client->expects()->httpPostJson('cgi-bin/wedrive/file_list', [
            'userid' => 'test_userid',
            'spaceid' => 'test_spaceid',
            'fatherid' => 'test_spaceid',
            'start' => 0,
            'limit' => 1000,
            'sort_type' => 1
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->list('test_userid', 'test_spaceid'));

        $client->expects()->httpPostJson('cgi-bin/wedrive/file_list', [
            'userid' => 'test_userid',
            'spaceid' => 'test_spaceid',
            'fatherid' => 'test_fatherid',
            'start' => 0,
            'limit' => 1000,
            'sort_type' => 1
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->list('test_userid', 'test_spaceid', 'test_fatherid'));

        $client->expects()->httpPostJson('cgi-bin/wedrive/file_list', [
            'userid' => 'test_userid',
            'spaceid' => 'test_spaceid',
            'fatherid' => 'test_fatherid',
            'start' => 10,
            'limit' => 1000,
            'sort_type' => 1
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->list('test_userid', 'test_spaceid', 'test_fatherid', 10));

        $client->expects()->httpPostJson('cgi-bin/wedrive/file_list', [
            'userid' => 'test_userid',
            'spaceid' => 'test_spaceid',
            'fatherid' => 'test_fatherid',
            'start' => 10,
            'limit' => 100,
            'sort_type' => 1
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->list('test_userid', 'test_spaceid', 'test_fatherid', 10, 100));

        $client->expects()->httpPostJson('cgi-bin/wedrive/file_list', [
            'userid' => 'test_userid',
            'spaceid' => 'test_spaceid',
            'fatherid' => 'test_fatherid',
            'start' => 10,
            'limit' => 1000,
            'sort_type' => 2
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->list('test_userid', 'test_spaceid', 'test_fatherid', 10, 2000, 2));
    }

    public function testInfo()
    {
        $client = $this->mockApiClient(FileClient::class);

        $client->expects()->httpPostJson('cgi-bin/wedrive/file_info', [
            'userid' => 'test_userid',
            'fileid' => 'test_fileid',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->info('test_userid', 'test_fileid'));
    }

    public function testUpload()
    {
        $client = $this->mockApiClient(FileClient::class);

        $client->expects()->httpPostJson('cgi-bin/wedrive/file_upload', [
            'userid' => 'test_userid',
            'spaceid' => 'test_spaceid',
            'fatherid' => 'test_fatherid',
            'file_name' => 'test_filename',
            'file_base64_content' => base64_encode('file_content')
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->upload('test_userid', 'test_spaceid', 'test_fatherid', 'test_filename', 'file_content'));
    }

    public function testDownload()
    {
        $client = $this->mockApiClient(FileClient::class);

        $client->expects()->httpPostJson('cgi-bin/wedrive/file_download', [
            'userid' => 'test_userid',
            'fileid' => 'test_fileid',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->download('test_userid', 'test_fileid'));
    }

    public function testShare()
    {
        $client = $this->mockApiClient(FileClient::class);

        $client->expects()->httpPostJson('cgi-bin/wedrive/file_share', [
            'userid' => 'test_userid',
            'fileid' => 'test_fileid',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->share('test_userid', 'test_fileid'));
    }

    public function testSetting()
    {
        $client = $this->mockApiClient(FileClient::class);

        $client->expects()->httpPostJson('cgi-bin/wedrive/file_setting', [
            'userid' => 'test_userid',
            'fileid' => 'test_fileid',
            'auth_scope' => 1,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->setting('test_userid', 'test_fileid', 1));

        $client->expects()->httpPostJson('cgi-bin/wedrive/file_setting', [
            'userid' => 'test_userid',
            'fileid' => 'test_fileid',
            'auth_scope' => 1,
            'auth' => 1
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->setting('test_userid', 'test_fileid', 1, 1));
    }

    public function testAclAdd()
    {
        $client = $this->mockApiClient(FileClient::class);

        $authInfo = [
            'type' => '1',
            'userid' => 'test_userid2',
            'auth' => 1
        ];
        $client->expects()->httpPostJson('cgi-bin/wedrive/file_acl_add', [
            'userid' => 'test_userid',
            'fileid' => 'test_fileid',
            'auth_info' => [$authInfo],
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->aclAdd('test_userid', 'test_fileid', $authInfo));

        $authInfos = [
            [
                'type' => '1',
                'userid' => 'test_userid2',
                'auth' => 1
            ],
            [
                'type' => '2',
                'departmentid' => 'test_department',
                'auth' => 1
            ]
        ];
        $client->expects()->httpPostJson('cgi-bin/wedrive/file_acl_add', [
            'userid' => 'test_userid',
            'fileid' => 'test_fileid',
            'auth_info' => $authInfos,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->aclAdd('test_userid', 'test_fileid', $authInfos));
    }

    public function testAclDel()
    {
        $client = $this->mockApiClient(FileClient::class);

        $authInfo = [
            'type' => '1',
            'userid' => 'test_userid2',
        ];
        $client->expects()->httpPostJson('cgi-bin/wedrive/file_acl_del', [
            'userid' => 'test_userid',
            'fileid' => 'test_fileid',
            'auth_info' => [$authInfo],
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->aclDel('test_userid', 'test_fileid', $authInfo));

        $authInfos = [
            [
                'type' => '1',
                'userid' => 'test_userid2',
            ],
            [
                'type' => '2',
                'departmentid' => 'test_department',
            ]
        ];
        $client->expects()->httpPostJson('cgi-bin/wedrive/file_acl_del', [
            'userid' => 'test_userid',
            'fileid' => 'test_fileid',
            'auth_info' => $authInfos,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->aclDel('test_userid', 'test_fileid', $authInfos));
    }
}
