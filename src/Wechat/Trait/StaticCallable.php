<?php namespace Overtrue\Wechat\Trait;

class StaticCallable {

    protected static $instance  = null;

    /**
     * 处理静态访问
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __callStatic($method, $args)
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        if (is_callable(static::$instance, $method)) {
            return call_user_func_array(array(static::$instance, $method), $args);
        }
    }
}