<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Notice\Notice;

class NoticeNoticeTest extends PHPUnit_Framework_TestCase
{
    public function getNotice($mockHttp = false)
    {
        if ($mockHttp) {
            $accessToken = Mockery::mock('EasyWeChat\Core\AccessToken');
            $accessToken->shouldReceive('getQueryFields')->andReturn(['access_token' => 'foo']);
            $notice = new Notice($accessToken);
            $http = Mockery::mock('EasyWeChat\Core\Http[json]');
            $http->shouldReceive('json')->andReturnUsing(function ($api, $params) {
                return json_encode(compact('api', 'params'));
            });
            $notice->setHttp($http);

            return $notice;
        }
        $notice = Mockery::mock('EasyWeChat\Notice\Notice[parseJSON]', [Mockery::mock('EasyWeChat\Core\AccessToken')]);
        $notice->shouldReceive('parseJSON')->andReturnUsing(function ($api, $params) {
            return [
                'api' => $params[0],
                'params' => $params[1],
            ];
        });

        return $notice;
    }

    /**
     * Test setIndustry().
     */
    public function testSetIndustry()
    {
        $notice = $this->getNotice();

        $response = $notice->setIndustry('foo', 'bar');

        $this->assertStringStartsWith(Notice::API_SET_INDUSTRY, $response['api']);
        $this->assertEquals('foo', $response['params']['industry_id1']);
        $this->assertEquals('bar', $response['params']['industry_id2']);
    }

    /**
     * Test addTemplate().
     */
    public function testAddTemplate()
    {
        $notice = $this->getNotice();

        $response = $notice->addTemplate('foo');

        $this->assertStringStartsWith(Notice::API_ADD_TEMPLATE, $response['api']);
        $this->assertEquals('foo', $response['params']['template_id_short']);
    }

    /**
     * Test send().
     */
    public function testSend()
    {
        $notice = $this->getNotice(true);

        try {
            $notice->send();
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertContains(' can not be empty!', $e->getMessage());
        }

        $response = $notice->send(['touser' => 'foo', 'template_id' => 'bar']);

        $this->assertStringStartsWith(Notice::API_SEND_NOTICE, $response['api']);
        $this->assertEquals('foo', $response['params']['touser']);
        $this->assertEquals('bar', $response['params']['template_id']);
        $this->assertEquals('#FF0000', $response['params']['topcolor']);
        $this->assertEquals([], $response['params']['data']);

        $response = $notice->withTo('anzhengchao1')->withTemplateId('test_tpl_id')->withUrl('url')->withColor('color')->send();

        $this->assertEquals('anzhengchao1', $response['params']['touser']);
        $this->assertEquals('test_tpl_id', $response['params']['template_id']);
        $this->assertEquals('url', $response['params']['url']);
        $this->assertEquals('color', $response['params']['topcolor']);

        $response = $notice->foo('bar')->withReceiver('anzhengchao2')->withTemplate('tpl1')->withLink('link')->andColor('andColor')->send();

        $this->assertEquals('anzhengchao2', $response['params']['touser']);
        $this->assertEquals('tpl1', $response['params']['template_id']);
        $this->assertEquals('link', $response['params']['url']);
        $this->assertEquals('andColor', $response['params']['topcolor']);
    }

    /**
     * Test formatData().
     */
    public function testFormatData()
    {
        $notice = $this->getNotice(true);

        $data = [
            'first' => '恭喜你购买成功！',
            'keynote1' => '巧克力',
            'keynote2' => '39.8元',
            'keynote3' => '2014年9月16日',
            'remark' => '欢迎再次购买！',
        ];
        $response = $notice->to('anzhengchao')->color('color1')->template('overtrue')->data($data)->send();

        $this->assertEquals('anzhengchao', $response['params']['touser']);
        $this->assertEquals('color1', $response['params']['topcolor']);
        $this->assertEquals('overtrue', $response['params']['template_id']);

        // format1
        $this->assertEquals(['value' => '恭喜你购买成功！', 'color' => '#173177'], $response['params']['data']['first']);
        $this->assertEquals(['value' => '巧克力', 'color' => '#173177'], $response['params']['data']['keynote1']);
        $this->assertEquals(['value' => '39.8元', 'color' => '#173177'], $response['params']['data']['keynote2']);
        $this->assertEquals(['value' => '2014年9月16日', 'color' => '#173177'], $response['params']['data']['keynote3']);
        $this->assertEquals(['value' => '欢迎再次购买！', 'color' => '#173177'], $response['params']['data']['remark']);

        // format2
        $data = [
            'first' => ['恭喜你购买成功！', '#555555'],
            'keynote1' => ['巧克力', '#336699'],
            'keynote2' => ['39.8元'],
            'keynote3' => ['2014年9月16日', '#888888'],
            'remark' => '欢迎再次购买！',
            'abc' => new stdClass(),
        ];

        $response = $notice->to('anzhengchao')->color('color1')->template('overtrue')->data($data)->send();

        $this->assertEquals(['value' => '恭喜你购买成功！', 'color' => '#555555'], $response['params']['data']['first']);
        $this->assertEquals(['value' => '巧克力', 'color' => '#336699'], $response['params']['data']['keynote1']);
        $this->assertEquals(['value' => '39.8元', 'color' => '#173177'], $response['params']['data']['keynote2']);
        $this->assertEquals(['value' => '2014年9月16日', 'color' => '#888888'], $response['params']['data']['keynote3']);
        $this->assertEquals(['value' => '欢迎再次购买！', 'color' => '#173177'], $response['params']['data']['remark']);
        $this->assertEquals(['value' => 'error data item.', 'color' => '#173177'], $response['params']['data']['abc']);

        // format3
        $data = [
            'first' => ['value' => '恭喜你购买成功！', 'color' => '#555555'],
            'keynote1' => ['value' => '巧克力', 'color' => '#336699'],
            'keynote2' => ['value' => '39.8元', 'color' => '#FF0000'],
            'keynote3' => ['value' => '2014年9月16日', 'color' => '#888888'],
            'remark' => ['value' => '欢迎再次购买！', 'color' => '#5599FF'],
        ];
        $response = $notice->to('anzhengchao')->color('color1')->template('overtrue')->data($data)->send();

        $this->assertEquals(['value' => '恭喜你购买成功！', 'color' => '#555555'], $response['params']['data']['first']);
        $this->assertEquals(['value' => '巧克力', 'color' => '#336699'], $response['params']['data']['keynote1']);
        $this->assertEquals(['value' => '39.8元', 'color' => '#FF0000'], $response['params']['data']['keynote2']);
        $this->assertEquals(['value' => '2014年9月16日', 'color' => '#888888'], $response['params']['data']['keynote3']);
        $this->assertEquals(['value' => '欢迎再次购买！', 'color' => '#5599FF'], $response['params']['data']['remark']);
    }
}
