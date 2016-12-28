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
use EasyWeChat\OpenPlatform\Traits\VerifyTicket;
use EasyWeChat\Server\Guard as ServerGuard;
use EasyWeChat\Support\Arr;

class Guard extends ServerGuard
{
    use VerifyTicket;

    /**
     * Wechat push event types.
     *
     * @var array
     */
    protected $eventTypeMappings = [
        'authorized' => EventHandlers\Authorized::class,
        'unauthorized' => EventHandlers\Unauthorized::class,
        'updateauthorized' => EventHandlers\UpdateAuthorized::class,
        'component_verify_ticket' => EventHandlers\ComponentVerifyTicket::class,
    ];

    /**
     * Return for laravel-wechat.
     *
     * @return array
     */
    public function listServe()
    {
        $class = $this->getHandleClass();

        $message = $this->getCollectedMessage();

        call_user_func([new $class($this->verifyTicket), 'handle'], $message);

        return [
            $message->get('InfoType'), $message,
        ];
    }

    /**
     * Listen for wechat push event.
     *
     * @param callable|null $callback
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function listen($callback = null)
    {
        $message = $this->getCollectedMessage();

        $class = $this->getHandleClass();

        if (is_callable($callback)) {
            $callback(
                call_user_func([new $class($this->verifyTicket), 'forward'], $message)
            );
        }

        return call_user_func([new $class($this->verifyTicket), 'handle'], $message);
    }

    /**
     * Get handler class.
     *
     * @return \EasyWeChat\OpenPlatform\EventHandlers\EventHandler
     *
     * @throws InvalidArgumentException
     */
    private function getHandleClass()
    {
        $message = $this->getCollectedMessage();

        $type = $message->get('InfoType');

        if (!$class = Arr::get($this->eventTypeMappings, $type)) {
            throw new InvalidArgumentException("Event Info Type \"$type\" does not exists.");
        }

        return $class;
    }
}
