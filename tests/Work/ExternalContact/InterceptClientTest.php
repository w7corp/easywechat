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
use EasyWeChat\Work\ExternalContact\InterceptClient;

/**
 * Class InterceptClientTest.
 *
 * @package EasyWeChat\Tests\Work\ExternalContact
 *
 * @author 读心印 <aa24615@qq.com>
 */
class InterceptClientTest extends TestCase
{
    /**
     * testCreateInterceptRule.
     *
     * @return void
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function testCreateInterceptRule()
    {
        $client = $this->mockApiClient(InterceptClient::class);

        $params = [
            'rule_name' => 'rulename',
            'word_list' => [
                '敏感词1', '敏感词2'
            ],
            'semantics_list' => [1, 2, 3],
            'intercept_type' => 1,
            'applicable_range' => [
                'user_list' => ['zhangshan'],
                'department_list' => [2, 3]
            ]
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/add_intercept_rule', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->createInterceptRule($params));
    }

    /**
     * testGetInterceptRules.
     *
     * @return void
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function testGetInterceptRules()
    {
        $client = $this->mockApiClient(InterceptClient::class);

        $client->expects()->httpGet('cgi-bin/externalcontact/get_intercept_rule_list')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getInterceptRules());
    }

    /**
     * testGetInterceptRuleDetails.
     *
     * @return void
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function testGetInterceptRuleDetails()
    {
        $client = $this->mockApiClient(InterceptClient::class);

        $params = [
            'rule_id' => 'test_id'
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_intercept_rule', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getInterceptRuleDetails('test_id'));
    }

    /**
     * testDeleteInterceptRule.
     *
     * @return void
     *
     * @author 读心印 <aa24615@qq.com>
     */
    public function testDeleteInterceptRule()
    {
        $client = $this->mockApiClient(InterceptClient::class);

        $params = [
            'rule_id' => 'test_id'
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/del_intercept_rule', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->deleteInterceptRule('test_id'));
    }
}
