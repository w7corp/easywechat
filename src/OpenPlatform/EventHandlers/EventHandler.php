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
 * EventHandler.php.
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

namespace EasyWeChat\OpenPlatform\EventHandlers;

use EasyWeChat\OpenPlatform\VerifyTicket;
use EasyWeChat\Support\Collection;

abstract class EventHandler
{
    /**
     * Component verify ticket instance.
     *
     * @var \EasyWeChat\OpenPlatform\VerifyTicket
     */
    protected $verifyTicket;

    /**
     * EventHandler constructor.
     *
     * @param VerifyTicket $verifyTicket
     */
    public function __construct(VerifyTicket $verifyTicket)
    {
        $this->verifyTicket = $verifyTicket;
    }

    /**
     * Handle event.
     *
     * @param Collection $message
     *
     * @return mixed
     */
    abstract public function handle(Collection $message);

    /**
     * Forward handle.
     *
     * @param Collection $message
     *
     * @return Collection
     */
    public function forward(Collection $message)
    {
        //
        return $message;
    }
}
