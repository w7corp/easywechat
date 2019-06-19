<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Crm;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Application;
use EasyWeChat\Work\Crm\Client;
use EasyWeChat\Work\Crm\ContactWayClient;
use EasyWeChat\Work\Crm\Crm;
use EasyWeChat\Work\Crm\DataCubeClient;
use EasyWeChat\Work\Crm\DimissionClient;
use EasyWeChat\Work\Crm\MessageClient;

class CrmTest extends TestCase
{
    public function testBasicProperties()
    {
        $app = new Application();
        $crm = new Crm($app);

        $this->assertInstanceOf(Client::class, $crm);
        $this->assertInstanceOf(ContactWayClient::class, $crm->contact_way);
        $this->assertInstanceOf(DataCubeClient::class, $crm->data_cube);
        $this->assertInstanceOf(DimissionClient::class, $crm->dimission);
        $this->assertInstanceOf(MessageClient::class, $crm->msg);

        try {
            $crm->foo;
            $this->fail('No expected exception thrown.');
        } catch (\Exception $e) {
            $this->assertSame('No crm service named "foo".', $e->getMessage());
        }
    }
}
