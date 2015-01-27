<?php namespace Overtrue\Wechat\Trait;

trait Loggable {

    /**
     * 日志记录器
     *
     * @var callback
     */
    protected $logger = null;


    /**
     * 设置日志记录器
     *
     * @param callback $logger
     *
     * @return
     */
    public function logger(callback $logger)
    {
        $this->logger = $logger;
    }

    /**
     * 记日志
     *
     * @param string $string
     *
     * @return void
     */
    public function log($string)
    {
        is_null($this->logger) || {$this->logger}($string);
    }
}