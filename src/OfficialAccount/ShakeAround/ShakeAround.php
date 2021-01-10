<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\ShakeAround;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

/**
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
