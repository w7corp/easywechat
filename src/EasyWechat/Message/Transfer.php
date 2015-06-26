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

use EasyWeChat\Exception;

/**
 * Class Transfer.
 *
 * @property string $to
 * @property string $account
 */
class Transfer extends Attribute
{
    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
                             'account',
                             'to',
                            ];
}//end class
