<?php

/**
 * CacheServiceProvider.php.
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

namespace EasyWeChat\Cache;

use EasyWeChat\Cache\Adapters\FileAdapter;
use EasyWeChat\Support\ServiceProvider;
use EasyWeChat\Core\Bootstrapper;

/**
 * Class CacheServiceProvider.
 */
class CacheServiceProvider extends ServiceProvider
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
        $sdk->bind('cache', function ($sdk) {
            return new Manager($sdk);
        });

        // default adapter
        $sdk->bind('cache.adapter', function ($sdk) {
            return new FileAdapter();
        });
    }
}//end class

