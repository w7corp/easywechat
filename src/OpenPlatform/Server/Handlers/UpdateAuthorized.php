<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Server\Handlers;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;

/**
 * Class UpdateAuthorized.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class UpdateAuthorized implements EventHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($payload = null)
    {
        // Do nothing for the time being.
    }
}
