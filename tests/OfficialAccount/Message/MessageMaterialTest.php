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

use EasyWeChat\Applications\OfficialAccount\Message\Material;
use EasyWeChat\Tests\TestCase;

class MessageMaterialTest extends TestCase
{
    /**
     * Test get attributes.
     */
    public function testAttributes()
    {
        $material = new Material('text', 'mediaId');

        $this->assertSame('text', $material->type);
        $this->assertSame('mediaId', $material->media_id);
    }
}
