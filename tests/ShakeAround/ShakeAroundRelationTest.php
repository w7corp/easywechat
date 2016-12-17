<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * ShakeAroundRelationTest.php.
 *
 * @author    allen05ren <allen05ren@outlook.com>
 * @copyright 2016 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */
use EasyWeChat\ShakeAround\Relation;
use EasyWeChat\Support\Collection;

class ShakeAroundRelationTest extends TestCase
{
    public function getRelation()
    {
        $relation = Mockery::mock('EasyWeChat\ShakeAround\Relation[parseJSON]', [Mockery::mock('EasyWeChat\Core\AccessToken')]);
        $relation->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => $params[1],
            ];
        });

        return $relation;
    }

    /**
     * Test bindPage().
     */
    public function testBindPage()
    {
        $relation = $this->getRelation();

        $expected = [
            'device_identifier' => [
                'device_id' => 10100,	
                'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',	
                'major' => 10001,
                'minor' => 10002,
            ],
            'page_ids' => [12345, 23456, 334567],
        ];

        $result = $relation->bindPage([
            'device_id' => 10100,	
            'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',	
            'major' => 10001,
            'minor' => 10002,
        ], [12345, 23456, 334567]);

        $this->assertStringStartsWith(Relation::API_DEVICE_BINDPAGE, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }

    /**
     * Test getPageByDeviceId().
     */
    public function testGetPageByDeviceId()
    {

        $relation = $this->getRelation();

        $expected = [
            'type' => 1,
            'device_identifier' => [
                'device_id' => 10100,	
                'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',	
                'major' => 10001,
                'minor' => 10002,
            ],
        ];

        $result = $relation->getPageByDeviceId([
            'device_id' => 10100,	
            'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',	
            'major' => 10001,
            'minor' => 10002,
        ], true);

        $this->assertStringStartsWith(Relation::API_RELATION_SEARCH, $result['api']);
        $this->assertEquals($expected, $result['params']);

        $relation = Mockery::mock('EasyWeChat\ShakeAround\Relation[parseJSON]', [Mockery::mock('EasyWeChat\Core\AccessToken')]);
        $relation->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return new Collection([
                'data' => [
                    'relations' => [],
                ],
                'errcode' => 0,
                'errmsg' => 'success.',
            ]);
        });

        $expected = [];

        $result = $relation->getPageByDeviceId([
            'device_id' => 10100,	
            'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',	
            'major' => 10001,
            'minor' => 10002,
        ]);
        $this->assertEquals($expected, $result);

        $relation = Mockery::mock('EasyWeChat\ShakeAround\Relation[parseJSON]', [Mockery::mock('EasyWeChat\Core\AccessToken')]);
        $relation->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return new Collection([
                'data' => [
                    'relations' => [
                        [
                            'device_id' => 10100,	
                            'major' => 10001,
                            'minor' => 10002,
                            'page_id' => 1234,
                            'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                        ],
                        [
                            'device_id' => 10100,	
                            'major' => 10001,
                            'minor' => 10002,
                            'page_id' => 5678,
                            'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                        ],
                    ],
                    'total_count' => 2,
                ],
                'errcode' => 0,
                'errmsg' => 'success.',
            ]);
        });

        $expected = [1234, 5678];

        $result = $relation->getPageByDeviceId([
            'device_id' => 10100,	
            'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',	
            'major' => 10001,
            'minor' => 10002,
        ]);

        $this->assertEquals($expected, $result);
    }

    /**
     * Test getDeviceByPageId().
     */
    public function testGetDeviceByPageId()
    {
        $relation = $this->getRelation();

        $expected = [
            'type' => 2,
            'page_id' => 1234,
            'begin' => 0,
            'count' => 10,
        ];

        $result = $relation->getDeviceByPageId(1234, 0, 10);

        $this->assertStringStartsWith(Relation::API_RELATION_SEARCH, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }
}
