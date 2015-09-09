<?php

use EasyWeChat\Core\Http;
use EasyWeChat\Notice\Notice;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;

class NoticeNoticeTest extends TestCase
{
    public function getNotice($callback = null)
    {
        $http = Mockery::mock(Http::class);
        $http->shouldReceive('setExpectedException')->andReturn($http);

        is_callable($callback) && $callback($http);

        return new Notice($http);
    }

    /**
     * Test setIndustry()
     */
    public function testSetIndustry()
    {
        $notice = $this->getNotice(function($http){
            $http->shouldReceive('json')->andReturnUsing(function($api, $params){
                return compact('api', 'params');
            });
        });

        $response = $notice->setIndustry('foo', 'bar');

        $this->assertEquals(Notice::API_SET_INDUSTRY, $response['api']);
        $this->assertEquals('foo', $response['params']['industry_id1']);
        $this->assertEquals('bar', $response['params']['industry_id2']);
    }

    /**
     * Test addTemplate()
     */
    public function testAddTemplate()
    {
        $notice = $this->getNotice(function($http){
            $http->shouldReceive('json')->andReturnUsing(function($api, $params){
                return ['template_id' => compact('api', 'params')];
            });
        });

        $response = $notice->addTemplate('foo');

        $this->assertEquals(Notice::API_ADD_TEMPLATE, $response['api']);
        $this->assertEquals('foo', $response['params']['template_id_short']);
    }

    /**
     * Test send()
     */
    public function testSend()
    {
        $notice = $this->getNotice(function($http){
            $http->shouldReceive('json')->andReturnUsing(function($api, $params){
                return ['msgid' => compact('api', 'params')];
            });
        });

        try {
            $notice->send();
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertContains(' can not be empty!', $e->getMessage());
        }

        $response = $notice->send('foo', 'bar');

        $this->assertEquals(Notice::API_SEND_NOTICE, $response['api']);
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
     * Test formatData()
     */
    public function testFormatData()
    {
        $notice = $this->getNotice(function($http){
            $http->shouldReceive('json')->andReturnUsing(function($api, $params){
                return ['msgid' => compact('api', 'params')];
            });
        });

        $data = array(
            "first"    => "恭喜你购买成功！",
            "keynote1" => "巧克力",
            "keynote2" => "39.8元",
            "keynote3" => "2014年9月16日",
            "remark"   => "欢迎再次购买！",
        );
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
        $data = array(
            "first"    => array("恭喜你购买成功！", '#555555'),
            "keynote1" => array("巧克力", "#336699"),
            "keynote2" => array("39.8元"),
            "keynote3" => array("2014年9月16日", "#888888"),
            "remark"   => '欢迎再次购买！',
            "abc"      => new stdClass(),
        );

        $response = $notice->to('anzhengchao')->color('color1')->template('overtrue')->data($data)->send();

        $this->assertEquals(['value' => '恭喜你购买成功！', 'color' => '#555555'], $response['params']['data']['first']);
        $this->assertEquals(['value' => '巧克力', 'color' => '#336699'], $response['params']['data']['keynote1']);
        $this->assertEquals(['value' => '39.8元', 'color' => '#173177'], $response['params']['data']['keynote2']);
        $this->assertEquals(['value' => '2014年9月16日', 'color' => '#888888'], $response['params']['data']['keynote3']);
        $this->assertEquals(['value' => '欢迎再次购买！', 'color' => '#173177'], $response['params']['data']['remark']);
        $this->assertEquals(['value' => 'error data item.', 'color' => '#173177'], $response['params']['data']['abc']);


        // format3
        $data = array(
            "first"    => array("value" => "恭喜你购买成功！", "color" => '#555555'),
            "keynote1" => array("value" => "巧克力", "color" => "#336699"),
            "keynote2" => array("value" => "39.8元", "color" => "#FF0000"),
            "keynote3" => array("value" => "2014年9月16日", "color" => "#888888"),
            "remark"   => array("value" => "欢迎再次购买！", "color" => "#5599FF"),
        );
        $response = $notice->to('anzhengchao')->color('color1')->template('overtrue')->data($data)->send();

        $this->assertEquals(['value' => '恭喜你购买成功！', 'color' => '#555555'], $response['params']['data']['first']);
        $this->assertEquals(['value' => '巧克力', 'color' => '#336699'], $response['params']['data']['keynote1']);
        $this->assertEquals(['value' => '39.8元', 'color' => '#FF0000'], $response['params']['data']['keynote2']);
        $this->assertEquals(['value' => '2014年9月16日', 'color' => '#888888'], $response['params']['data']['keynote3']);
        $this->assertEquals(['value' => '欢迎再次购买！', 'color' => '#5599FF'], $response['params']['data']['remark']);
    }
}