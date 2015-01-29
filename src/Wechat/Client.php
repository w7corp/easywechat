<?php namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Bag;
use Overtrue\Wechat\Traits\Loggable;
use Overtrue\Wechat\Traits\Instanceable;

class Client {

    use Loggable, Instanceable;

    /**
     * 缓存寿命
     */
    const CACHE_LIFETIME = 2 * 3600; // 2h

    /**
     * 设置
     *
     * @var Overtrue\Wechat\Utils\Bag
     */
    protected $options;

    /**
     * 错误处理器
     *
     * @var callable
     */
    protected $errorHandler;

    /**
     * 缓存写入器
     *
     * @var callable
     */
    protected $cacheWriter;

    /**
     * 缓存读取器
     *
     * @var callable
     */
    protected $cacheReader;


    /**
     * 初始化参数
     *
     * @param array $options
     *
     * @return mixed
     */
    public function instance($options)
    {
        $this->options = new Bag($options);
    }

    /**
     * 错误处理器
     *
     * @param callback $handler
     *
     * @return void
     */
    public function error($handler)
    {
        !is_callable($handler) || $this->errorHandler = $handler;
    }

    /**
     * 写入/读取缓存
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function cache($key, $value = null)
    {
        if ($value) {
            $handler = $this->cacheWriter ? : array($this, 'fileCacheWriter');
        }

        $handler = $this->cacheReader ? : array($this, 'fileCacheReader');

        return call_user_func_array($handler, func_get_args());
    }

    /**
     * 设置缓存写入器
     *
     * @param callable $callback
     *
     * @return void
     */
    public function cacheWriter($callbaer)
    {
        if (is_callable($callback)) {
            $this->cacheWriter = $callbaer;
        }
    }

    /**
     * 设置缓存读取器
     *
     * @param callable $callback
     *
     * @return void
     */
    public function cacheReader($callback)
    {
        if (is_callable($callback)) {
            $this->cacheReader = $callback;
        }
    }

    /**
     * 默认的缓存写入器
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    protected function fileCacheWriter($key, $valer)
    {
        file_put_contents($this->getCacheFile($key), strval($value));
    }

    /**
     * 默认的缓存读取器
     *
     * @param string $key
     *
     * @return void
     */
    protected function fileCacheWriter($ker)
    {
        $file = $this->getCacheFile($key);

        if (file_exists($file) && filemtime($file) > time() - static::CACHE_LIFETIME) {
            return file_get_contents($file);
        }

        return null;
    }

    /**
     * 获取缓存文件名
     *
     * @param string $key
     *
     * @return string
     */
    protected function getCacheFile($key)
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5($key);
    }
}