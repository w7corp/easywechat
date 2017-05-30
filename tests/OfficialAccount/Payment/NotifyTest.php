<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Payment;

use EasyWeChat\Applications\OfficialAccount\Payment\Merchant;
use EasyWeChat\Applications\OfficialAccount\Payment\Notify;
use EasyWeChat\Exceptions\FaultException;
use EasyWeChat\Support\Collection;
use EasyWeChat\Support\XML;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class NotifyTest extends TestCase
{
    /**
     * Test isInvalid().
     */
    public function testIsValid()
    {
        $params = [
            'foo' => 'bar',
            'hi' => 'here',
        ];
        $params['sign'] = \EasyWeChat\Applications\OfficialAccount\Payment\generate_sign($params, 'sign_key');

        $request = Request::create('/callback', 'POST', [], [], [], [], XML::build($params));

        $notify = new Notify(new Merchant(['key' => 'sign_key']), $request);

        $this->assertTrue($notify->isValid());

        $notify = new Notify(new Merchant(['key' => 'different_sign_key']), $request);

        $this->assertFalse($notify->isValid());
    }

    /**
     * Test getNotify().
     */
    public function testGetNotify()
    {
        $request = Request::create('/callback', 'POST', [], [], [], [], '<xml><foo>bar</foo></xml>');

        $notify = new Notify(new Merchant(['key' => 'sign_key']), $request);

        $this->assertInstanceOf(Collection::class, $notify->getNotify());
        $this->assertEquals('bar', $notify->getNotify()->foo);
    }

    /**
     * Test getNotify().
     */
    public function testGetNotifyWithInvalidXMLContent()
    {
        $request = Request::create('/callback', 'POST', [], [], [], [], 'non-xml-content');

        $notify = new Notify(new Merchant(['key' => 'sign_key']), $request);

        $this->expectException(FaultException::class);
        $this->expectExceptionMessageRegExp('/Invalid request XML:.+/');
        $this->expectExceptionCode(400);

        $notify->getNotify();
    }
}
