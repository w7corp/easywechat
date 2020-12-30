<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\ShakeAround;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

/**
 * Class Card.
 *
 * @author overtrue <i@overtrue.me>
 *
 * @property \EasyWeChat\OfficialAccount\ShakeAround\DeviceClient   $device
 * @property \EasyWeChat\OfficialAccount\ShakeAround\GroupClient    $group
 * @property \EasyWeChat\OfficialAccount\ShakeAround\MaterialClient $material
 * @property \EasyWeChat\OfficialAccount\ShakeAround\RelationClient $relation
 * @property \EasyWeChat\OfficialAccount\ShakeAround\StatsClient    $stats
 * @property \EasyWeChat\OfficialAccount\ShakeAround\PageClient     $page
 */
class ShakeAround extends Client
{
    /**
     * @param string $property
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function __get($property)
    {
        if (isset($this->app["shake_around.{$property}"])) {
            return $this->app["shake_around.{$property}"];
        }

        throw new InvalidArgumentException(sprintf('No shake_around service named "%s".', $property));
    }
}
