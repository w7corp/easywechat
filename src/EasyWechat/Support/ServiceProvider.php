<?php

/**
 * ServiceProvider.php.
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

namespace EasyWeChat\Support;

use EasyWeChat\Core\Bootstrapper;

/**
 * Class ServiceProvider.
 */
abstract class ServiceProvider
{
    /**
     * Register service.
     *
     * @param Bootstrapper $sdk
     *
     * @return mixed
     */
    abstract public function register(Bootstrapper $sdk);
}//end class

