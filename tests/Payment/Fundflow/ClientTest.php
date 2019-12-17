<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment\Fundflow;

use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testGet()
    {
        //单元测试未完成
        //检测返回结果
        $this->assertArraySubset([
                                     'return_code' => 'FAIL',
                                     'return_msg' => 'invalid bill_date',
                                     'error_code' => 20001,
                                 ], [
            'return_code' => 'FAIL',
            'return_msg' => 'invalid bill_date',
            'error_code' => 20001,
        ]);
    }
}
