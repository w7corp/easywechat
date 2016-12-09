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
 * Transfer.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Message;

/**
 * Class Transfer.
 *
 * @property string $to
 * @property string $account
 */
class Transfer extends AbstractMessage
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $type = 'transfer_customer_service';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
                             'account',
                            ];
}
