<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Cache.php.
 *
 * Part of Overtrue\Wechat.
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

namespace Overtrue\Wechat;

/**
 * 缓存服务
 */
class Cache
{
    /**
     * 缓存文件前缀
     *
     * @var string
     */
    protected $prefix;

    /**
     * 缓存写入器.
     *
     * @var callable
     */
    protected static $cacheSetter;

    /**
     * 缓存读取器.
     *
     * @var callable
     */
    protected static $cacheGetter;

    /**
     * 设置缓存文件前缀
     *
     * @param string $prefix 缓存文件前缀
     */
    public function __construct($prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * 默认的缓存写入器.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $lifetime
     */
    public function set($key, $value, $lifetime = 7200)
    {
        if (self::$cacheSetter) {
            return call_user_func_array(self::$cacheSetter, func_get_args());
        }

        $data = array(
                 'data' => $value,
                 'expired_at' => time() + $lifetime - 500, //XXX: 微信API是世界上最烂的，没有之一
                );

        if (!file_put_contents($this->getCacheFile($key), serialize($data))) {
            throw new Exception('Access toekn 缓存失败');
        }
    }

    /**
     * 默认的缓存读取器.
     *
     * @param string $key
     * @param mixed  $default
     */
    public function get($key, $default = null)
    {
        $return = null;

        if (self::$cacheGetter) {
            $return = call_user_func_array(self::$cacheGetter, func_get_args());
        } else {
            $file = $this->getCacheFile($key);

            if (file_exists($file) && ($data = unserialize(file_get_contents($file)))) {
                $return = $data['expired_at'] > time() ? $data['data'] : null;
            }
        }

        if (!$return) {
            $return = is_callable($default) ? $default($key) : $default;
        }

        return $return;
    }

    /**
     * 删除缓存.
     *
     * @return bool
     */
    public function forget($key)
    {
        try {
            unlink($this->getCacheFile($key));
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * 设置缓存写入器.
     *
     * @param callable $handler
     */
    public static function setter($handler)
    {
        is_callable($handler) && self::$cacheSetter = $handler;
    }

    /**
     * 设置缓存读取器.
     *
     * @param callable $handler
     */
    public static function getter($handler)
    {
        is_callable($handler) && self::$cacheGetter = $handler;
    }

    /**
     * 获取缓存文件名.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getCacheFile($key)
    {
        return sys_get_temp_dir().DIRECTORY_SEPARATOR.md5($this->prefix.$key);
    }
}
