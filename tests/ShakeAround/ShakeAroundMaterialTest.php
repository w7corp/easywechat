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
 * ShakeAroundMaterialTest.php.
 *
 * @author    allen05ren <allen05ren@outlook.com>
 * @copyright 2016 overtrue <i@overtrue.me>
 *
 * @see       https://github.com/overtrue
 * @see       http://overtrue.me
 */

namespace EasyWeChat\Tests\ShakeAround;

use EasyWeChat\ShakeAround\Material;
use EasyWeChat\Tests\TestCase;

class ShakeAroundMaterialTest extends TestCase
{
    /**
     * Test uploadImage().
     *
     * @expectedException \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function testUploadImage()
    {
        $material = \Mockery::mock('EasyWeChat\ShakeAround\Material[parseJSON]', [\Mockery::mock('EasyWeChat\Core\AccessToken')]);
        $material->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'media' => $params[1]['media'],
                'type' => $params[3]['type'],
            ];
        });

        $result = $material->uploadImage(__DIR__.'/stubs/image.jpg');

        $this->assertStringStartsWith(Material::API_MATERIAL_ADD, $result['api']);
        $this->assertContains('stubs/image.jpg', $result['media']);
        $this->assertSame('icon', $result['type']);

        $result = $material->uploadImage(__DIR__.'/stubs/image.jpg', 'license');

        $this->assertSame('license', $result['type']);

        $material->uploadImage(__DIR__.'/stubs/foo.jpg');
    }
}
