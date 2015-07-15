<?php

/**
 * Transfer.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
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
}//end class

