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
        is_callable($handler) && $this->errorHandler = $handler;
    }

    /**
     * 发送消息
     *
     * @param Message $message
     *
     * @return $this
     */
    public function send(Message $message)
    {
        # code...
        # TODO:发送消息并返回状态
    }

    /**
     * 设置缓存写入器
     *
     * @param callable $handler
     *
     * @return void
     */
    public function cacheWriter($handler)
    {
        is_callable($handler) && $this->cacheWriter = $handler;
    }

    /**
     * 设置缓存读取器
     *
     * @param callable $handler
     *
     * @return void
     */
    public function cacheReader($handler)
    {
        is_callable($handler) && $this->cacheReader = $handler;
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
        $value && $handler = $this->cacheWriter ? : array($this, 'fileCacheWriter');

        $handler = $this->cacheReader ? : array($this, 'fileCacheReader');

        return call_user_func_array($handler, func_get_args());
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

    /**
     * 处理魔术调用
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {

    }
}