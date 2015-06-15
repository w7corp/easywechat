<?php
/**
 * AdapterInterface.php
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

namespace EasyWeChat\Cache\Adapters;


/**
 * Interface AdapterInterface
 * 
 * @package EasyWeChat\Cache\Adapters
 */
interface AdapterInterface
{
    /**
     * Set app id.
     *
     * @param string $appId
     *
     * @return mixed
     */
    public function setAppId($appId);

    /**
     * Get cache content.
     *
     * @param string     $key
     * @param mixed|null $default
     *
     * @return string
     */
    public function get($key, $default = null);

    /**
     * Set cache content.
     *
     * @param string $key
     * @param string $value
     * @param int    $lifetime
     *
     * @return int
     */
    public function set($key, $value, $lifetime = 7200);
}//end class
