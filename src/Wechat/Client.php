<?php namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Bag;
use Overtrue\Wechat\Traits\Loggable;
use Overtrue\Wechat\Traits\Instanceable;

class Client {

    use Loggable, Instanceable;

    protected $options;
    protected $errorHandler;

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
}