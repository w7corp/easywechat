<?php

/**
 * MessageServiceProvider.php.
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

use EasyWeChat\Core\Application;
use EasyWeChat\Support\ServiceProvider;
use EasyWeChat\Support\Str;

/**
 * Class MessageServiceProvider.
 */
class MessageServiceProvider extends ServiceProvider
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
        $app->singleton('message', function ($app) {
            return new MessageFactory($app);
        });

        $messages = ['Text', 'Articles', 'Article', 'Image', 'Link', 'Location', 'Music', 'Transfer', 'ShortVideo', 'Video', 'Voice'];

        foreach ($messages as $message) {
            $app->bind('message.'.Str::snake($message), function ($app) use ($message) {
                $class = __NAMESPACE__.'\\'.$message;

                return new $class();
            }, false);
        }
    }
}
