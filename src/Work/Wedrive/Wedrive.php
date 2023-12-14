<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Wedrive;

use Pimple\Container;

/**
 * Class Wedrive
 *
 * @property \EasyWeChat\Work\Wedrive\Client                 $base
 * @property \EasyWeChat\Work\Wedrive\SpaceClient            $space
 * @property \EasyWeChat\Work\Wedrive\FileClient             $file
 *
 * @author lio990527 <lio990527@163.com>
 */
class Wedrive extends Container
{
    public function __get($key)
    {
        return $this->offsetGet($key);
    }
}
