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
 * Trait VerifyTicket.php.
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

namespace EasyWeChat\OpenPlatform\Traits;

use EasyWeChat\OpenPlatform\VerifyTicket as ComponentVerifyTicket;

trait VerifyTicket
{
    /**
     * Verify Ticket.
     *
     * @var \EasyWeChat\OpenPlatform\VerifyTicket
     */
    protected $verifyTicket;

    /**
     * Set verify ticket instance.
     *
     * @param \EasyWeChat\OpenPlatform\VerifyTicket $verifyTicket
     *
     * @return $this
     */
    public function setVerifyTicket(ComponentVerifyTicket $verifyTicket)
    {
        $this->verifyTicket = $verifyTicket;

        return $this;
    }

    /**
     * Get verify ticket instance.
     *
     * @return \EasyWeChat\OpenPlatform\VerifyTicket
     */
    public function getVerifyTicket()
    {
        return $this->verifyTicket;
    }
}
