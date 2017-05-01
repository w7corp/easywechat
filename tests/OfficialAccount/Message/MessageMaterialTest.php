<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Message;

use EasyWeChat\OfficialAccount\Message\Material;
use EasyWeChat\Tests\TestCase;

class MessageMaterialTest extends TestCase
{
    /**
     * Test get attributes.
     */
    public function testAttributes()
    {
        $material = new Material('text', 'mediaId');

        $this->assertEquals('text', $material->type);
        $this->assertEquals('mediaId', $material->media_id);
    }
}
