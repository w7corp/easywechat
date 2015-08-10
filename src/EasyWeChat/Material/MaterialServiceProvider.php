<?php

/**
 * MaterialServiceProvider.php.
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

namespace EasyWeChat\Material;

use EasyWeChat\Core\Application;

/**
 * Class MaterialServiceProvider.
 */
class MaterialServiceProvider
{
    /**
     * Register service.
     *
     * @param Application $app
     *
     * @return mixed
     */
    public function register(Application $app)
    {
        $app->singleton('material', function ($app) {
            return new Material($app['http']);
        });

        $app->singleton('material.temporary', function ($app) {
            return new Temporary($app['http']);
        });
    }
}
