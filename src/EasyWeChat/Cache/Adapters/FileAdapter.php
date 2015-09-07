<?php

/**
 * FileAdapter.php.
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

namespace EasyWeChat\Cache\Adapters;

use EasyWeChat\Core\Exceptions\RuntimeException;

/**
 * Class FileAdapter.
 */
class FileAdapter
{
    /**
     * appId.
     *
     * @var string
     */
    protected $appId;

    /**
     * @{inheritdoc}
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;

        return $this;
    }

    /**
     * @{inheritdoc}
     */
    public function set($key, $value, $lifetime = 7200)
    {
        $data = [
                 'data' => $value,
                 'expired_at' => time() + $lifetime - 100, //XXX: -100 will be safe.
                ];

        if (!$length = file_put_contents($this->getCacheFile($key), serialize($data))) {
            throw new RuntimeException('Access toekn cache failed.');
        }

        return $length;
    }

    /**
     * @{inheritdoc}
     */
    public function get($key, $default = null)
    {
        $return = null;

        $file = $this->getCacheFile($key);

        if (file_exists($file) && ($data = unserialize(file_get_contents($file)))) {
            $return = $data['expired_at'] > time() ? $data['data'] : null;
        }

        if (!$return) {
            $return = is_callable($default) ? $default($key) : $default;
        }

        return $return;
    }

    /**
     * Get filename of cache.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getCacheFile($key)
    {
        return sys_get_temp_dir().DIRECTORY_SEPARATOR.md5($this->appId.$key);
    }
}
