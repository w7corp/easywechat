<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\ExternalContact;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\ExternalContact\Client;

class ClientTest extends TestCase
{
    public function testGetExternalContact()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('cgi-bin/externalcontact/get', ['external_userid' => 'mock-userid'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->get('mock-userid'));
    }

    public function testListExternalContact()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('cgi-bin/externalcontact/list', ['userid' => 'mock-userid'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->list('mock-userid'));
    }

    public function testGetFollowUsers()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('cgi-bin/externalcontact/get_follow_user_list')->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getFollowUsers());
    }

    public function testList(): void
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'userid' => 'zhangsan'
        ];
        $client->expects()->httpGet('cgi-bin/externalcontact/list', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list('zhangsan'));
    }

    public function testGet(): void
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'external_userid' => 'woAJ2GCAAAXtWyujaWJHDDGi0mACH71w',
        ];
        $client->expects()->httpGet('cgi-bin/externalcontact/get', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get('woAJ2GCAAAXtWyujaWJHDDGi0mACH71w'));
    }


    public function testBatchGet(): void
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'userid_list' => ['rocky'],
            'cursor' => '',
            'limit' => 100,
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/batch/get_by_user', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->batchGet(['rocky'], '', 100));
    }

    public function testRemark(): void
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'userid' => '员工id',
            'external_userid' => '客户id',
            'remark' => '新备注',
            'description' => '新描述',
            'remark_company' => '新公司',
            'remark_mobiles' => [ '电话1','电话2'],
            'remark_pic_mediaid' => 'MEDIAID'
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/remark', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->remark($params));
    }

    public function testGetUnassigned()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'page_id' => 1,
            'page_size' => 1000,
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_unassigned_list', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getUnassigned(1));
    }

    public function testTransfer()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'external_userid' => 'mock-external-userid',
            'handover_userid' => 'mock-handover-userid',
            'takeover_userid' => 'mock-takeover-userid',
            'transfer_success_msg' => 'message',
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/transfer', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->transfer('mock-external-userid', 'mock-handover-userid', 'mock-takeover-userid', 'message'));
    }

    public function testTransferCustomer()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'external_userid' => ['mock-external-userid'],
            'handover_userid' => 'mock-handover-userid',
            'takeover_userid' => 'mock-takeover-userid',
            'transfer_success_msg' => 'message',
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/transfer_customer', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->transferCustomer(['mock-external-userid'], 'mock-handover-userid', 'mock-takeover-userid', 'message'));
    }

    public function testResignedTransferCustomer()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'external_userid' => ['mock-external-userid'],
            'handover_userid' => 'mock-handover-userid',
            'takeover_userid' => 'mock-takeover-userid',
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/resigned/transfer_customer', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->resignedTransferCustomer(['mock-external-userid'], 'mock-handover-userid', 'mock-takeover-userid'));
    }

    public function testTransferGroupChat(): void
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'chat_id_list' => ['群聊id1', '群聊id2'],
            'new_owner' => '接替群主userid'
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/groupchat/transfer', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->transferGroupChat(['群聊id1', '群聊id2'], '接替群主userid'));
    }

    public function testTransferResult(): void
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'handover_userid' => 'zhangsan',
            'takeover_userid' => 'lisi',
            'cursor' => 'cursor',
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/resigned/transfer_result', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->transferResult('zhangsan', 'lisi', 'cursor'));
    }

    public function testGetTransferResult(): void
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'external_userid' => 'woAJ2GCAAAXtWyujaWJHDDGi0mACH71w',
            'handover_userid' => 'zhangsan',
            'takeover_userid' => 'lisi',
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_transfer_result', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getTransferResult('woAJ2GCAAAXtWyujaWJHDDGi0mACH71w', 'zhangsan', 'lisi'));
    }

    public function testGetGroupChats(): void
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'status_filter' => 0,
            'owner_filter' => [
                'userid_list' => ['abel'],
                'partyid_list' => [7]
            ],
            'offset' => 0,
            'limit' => 100
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/groupchat/list', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->GetGroupChats($params));
    }

    public function testGetGroupChat(): void
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'chat_id' => 'CHAT_ID_1',
            'need_name' => 0
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/groupchat/get', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getGroupChat('CHAT_ID_1'));

        $params = [
            'chat_id' => 'CHAT_ID_1',
            'need_name' => 1
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/groupchat/get', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getGroupChat('CHAT_ID_1', 1));
    }

    public function testGetCorpTags(): void
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'tag_id' => ['TAG_ID_1', 'TAG_ID_2'],
            'group_id' => ['GROUP_ID_1', 'GROUP_ID_2']
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_corp_tag_list', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getCorpTags(['TAG_ID_1', 'TAG_ID_2'], ['GROUP_ID_1', 'GROUP_ID_2']));
    }

    public function testAddCorpTag(): void
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'group_id' => 'GROUP_ID',
            'group_name' => 'GROUP_NAME',
            'order' => 1,
            'tag' => [
                [
                    'name' => 'TAG_NAME_1',
                    'order' => 1
                ],
                [
                    'name' => 'TAG_NAME_2',
                    'order' => 2
                ]
            ]
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/add_corp_tag', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->addCorpTag($params));
    }

    public function testUpdateCorpTag(): void
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'id' => 'id1',
            'name' => 'name',
            'order' => 10000,
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/edit_corp_tag', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->updateCorpTag('id1', 'name', 10000));
    }

    public function testDeleteCorpTag(): void
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'tag_id' => ['tagid1', 'tagid2'],
            'group_id' => ['groupid1', 'groupid2']
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/del_corp_tag', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->deleteCorpTag(['tagid1', 'tagid2'], ['groupid1', 'groupid2']));
    }

    public function testMarkTags(): void
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'userid' => 'zhangsan',
            'external_userid' => 'woAJ2GCAAAd1NPGHKSD4wKmE8Aabj9AAA',
            'add_tag' => ['TAGID1', 'TAGID2'],
            'remove_tag' => ['TAGID3', 'TAGID4']
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/mark_tag', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->markTags($params));
    }

    public function testUnionidToExternalUserid(): void
    {
        $client = $this->mockApiClient(Client::class);
        $unionid = 'unionid';
        $openid = 'openid';
        $client->expects()->httpPostJson('cgi-bin/externalcontact/unionid_to_external_userid', ['unionid' => $unionid, 'openid' => $openid])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->unionidToExternalUserid($unionid, $openid));
    }

    public function testToServiceExternalUserid(): void
    {
        $client = $this->mockApiClient(Client::class);
        $externalUserid = 'externalUserid';
        $client->expects()->httpPostJson('cgi-bin/externalcontact/to_service_external_userid', ['external_userid' => $externalUserid])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->toServiceExternalUserid($externalUserid));
    }

    /**
     * testGetNewExternalUserid.
     *
     * @return void
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function testGetNewExternalUserid(): void
    {
        $client = $this->mockApiClient(Client::class);

        $externalUserIds = ['externalUserid1'];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_new_external_userid', ['external_userid_list' => $externalUserIds])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getNewExternalUserid($externalUserIds));
    }

    /**
     * testFinishExternalUseridMigration.
     *
     * @return void
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function testFinishExternalUseridMigration(): void
    {
        $client = $this->mockApiClient(Client::class);

        $corpid = 'xxxx1323';

        $client->expects()->httpPostJson('cgi-bin/externalcontact/finish_external_userid_migration', ['corpid' => $corpid])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->finishExternalUseridMigration($corpid));
    }

    /**
     * testUnionidToexternalUserid3rd.
     *
     * @return void
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function testUnionidToexternalUserid3rd(): void
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'unionid' => 'unionid-test',
            'openid' => 'openid-test',
            'corpid' => 'corpid-test'
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/unionid_to_external_userid_3rd', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->unionidToexternalUserid3rd('unionid-test', 'openid-test', 'corpid-test'));
    }

    public function testOpengidToChatid()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/externalcontact/opengid_to_chatid', [
            'opengid' => 'msg2MgBEgAATurBYDPgS32DfSt5vdzaHA'
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->opengidToChatid('msg2MgBEgAATurBYDPgS32DfSt5vdzaHA'));
    }

    public function testUploadAttachment()
    {
        $client = $this->mockApiClient(Client::class);

        $query = [
            'media_type' => 'image',
            'attachment_type' => 1,
        ];

        $client->expects()->httpUpload('cgi-bin/media/upload_attachment', [
            'media' => '/foo/bar/image.jpg'
        ], [], $query)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadAttachment('/foo/bar/image.jpg', 'image', 1));
    }
}
