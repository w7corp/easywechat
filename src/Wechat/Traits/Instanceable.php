<?php namespace Overtrue\Wechat\Traits;

trait Instanceable {

    protected static $instance  = null;

    private function __construct() {}
    private function __clone() {}

    /**
     * 创建实例
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    static public function make()
    {
        !is_null(static::$instance) || static::$instance = new static;

        if (is_callable(array(static::$instance, 'instance'))) {
            call_user_func_array(array(static::$instance, 'instance'), func_get_args());
        }

        return static::$instance;
    }
}