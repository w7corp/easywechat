<?php

/**
 * ServerServiceProvider.php.
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

namespace EasyWeChat\Server;

use EasyWeChat\Support\ServiceProvider;
use EasyWeChat\Core\Bootstrapper;

/**
 * Class ServerServiceProvider.
 */
class ServerServiceProvider extends ServiceProvider
{
    /**
     * Register Server.
     *
     * @param Bootstrapper $sdk
     *
     * @return mixed|void
     */
    public function register(Bootstrapper $sdk)
    {
        $sdk->bind('server', function ($sdk) {
            return new Guard($sdk['input'], $sdk['cryptor']);
        });
    }
}//end class

