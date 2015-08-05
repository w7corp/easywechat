<?php
/**
 * MenuServiceProvider.php
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Menu;

use EasyWeChat\Core\Application;


/**
 * Class MenuServiceProvider.
 */
class MenuServiceProvider
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
        $app->singleton('menu', function($app){
            return new Menu($app['http']);
        });
    }
}
