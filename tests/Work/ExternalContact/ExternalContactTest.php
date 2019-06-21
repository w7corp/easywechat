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
use EasyWeChat\Work\Application;
use EasyWeChat\Work\ExternalContact\Client;
use EasyWeChat\Work\ExternalContact\ContactWayClient;
use EasyWeChat\Work\ExternalContact\ExternalContact;
use EasyWeChat\Work\ExternalContact\DataCubeClient;
use EasyWeChat\Work\ExternalContact\DimissionClient;
use EasyWeChat\Work\ExternalContact\MessageClient;

class ExternalContactTest extends TestCase
{
    public function testBasicProperties()
    {
        $app = new Application();
        $externalContact = new ExternalContact($app);

        $this->assertInstanceOf(Client::class, $externalContact);
        $this->assertInstanceOf(ContactWayClient::class, $externalContact->contact_way);
        $this->assertInstanceOf(DataCubeClient::class, $externalContact->data_cube);
        $this->assertInstanceOf(DimissionClient::class, $externalContact->dimission);
        $this->assertInstanceOf(MessageClient::class, $externalContact->msg);

        try {
            $externalContact->foo;
            $this->fail('No expected exception thrown.');
        } catch (\Exception $e) {
            $this->assertSame('No external_contact service named "foo".', $e->getMessage());
        }
    }
}
