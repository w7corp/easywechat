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
 * Guard.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Server\Guard as ServerGuard;
use EasyWeChat\Support\Arr;

class Guard extends ServerGuard
{
    /**
     * Wechat push event types.
     *
     * @var array
     */
    protected $eventTypeMappings = [
        'component_verify_ticket' => EventHandlers\ComponentVerifyTicket::class,
        'authorized' => EventHandlers\Authorized::class,
        'unauthorized' => EventHandlers\Unauthorized::class,
        'updateauthorized' => EventHandlers\UpdateAuthorized::class,
    ];

    /**
     * Listen for wechat push event.
     *
     * @param $type
     * @param callable $callback
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function listenFor($type, callable $callback)
    {
        $message = $this->getCollectedMessage();

        if (!$class = Arr::get($this->eventTypeMappings, $type)) {
            throw new InvalidArgumentException("Event Info Type \"$type\" does not exists.");
        }

        $callback(
            call_user_func([new $class(), 'forward'], $message)
        );

        return call_user_func([new $class(), 'handle'], $message);
    }
}
