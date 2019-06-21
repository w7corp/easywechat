<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\ExternalContact;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

/**
 * Class ExternalContact.
 *
 * @author milkmeowo <milkmeowo@gmail.com>
 *
 * @property \EasyWeChat\Work\ExternalContact\ContactWayClient $contact_way
 * @property \EasyWeChat\Work\ExternalContact\DataCubeClient $data_cube
 * @property \EasyWeChat\Work\ExternalContact\DimissionClient $dimission
 * @property \EasyWeChat\Work\ExternalContact\MessageClient $msg
 */
class ExternalContact extends Client
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
        if (isset($this->app["external_contact.{$property}"])) {
            return $this->app["external_contact.{$property}"];
        }

        throw new InvalidArgumentException(sprintf('No external_contact service named "%s".', $property));
    }
}
