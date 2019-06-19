<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Crm;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

/**
 * Class Crm.
 *
 * @author milkmeowo <milkmeowo@gmail.com>
 *
 * @property \EasyWeChat\Work\Crm\ContactWayClient $contact_way
 * @property \EasyWeChat\Work\Crm\DataCubeClient $data_cube
 * @property \EasyWeChat\Work\Crm\DimissionClient $dimission
 * @property \EasyWeChat\Work\Crm\MessageClient $msg
 */
class Crm extends Client
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
        if (isset($this->app["crm.{$property}"])) {
            return $this->app["crm.{$property}"];
        }

        throw new InvalidArgumentException(sprintf('No crm service named "%s".', $property));
    }
}
