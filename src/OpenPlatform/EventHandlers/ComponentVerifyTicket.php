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
 * ComponentVerifyTicket.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @author    lixiao <leonlx126@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\OpenPlatform\EventHandlers;

use EasyWeChat\OpenPlatform\Traits\VerifyTicketTrait;
use EasyWeChat\OpenPlatform\VerifyTicket;
use EasyWeChat\Support\Collection;

class ComponentVerifyTicket implements EventHandler
{
    use VerifyTicketTrait;

    public function __construct(VerifyTicket $verifyTicket)
    {
        $this->setVerifyTicket($verifyTicket);
    }

    /**
     * {@inheritdoc}.
     */
    public function handle(Collection $message)
    {
        $this->getVerifyTicket()->cache($message);

        return $message;
    }
}
