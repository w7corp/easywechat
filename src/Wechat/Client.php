<?php namespace Overtrue\Wechat;

use Overtrue\Wechat\Util\Bag;
use Overtrue\Wechat\Trait\Loggable;
use Overtrue\Wechat\Trait\StaticCallable;

class Client {

    use Loggable, StaticCallable;

    protected $options = array();

    /**
     * 开始运行
     *
     * @param array $options
     *
     * @return mixed
     */
    public function run($options)
    {
        $this->options = new Bag($options);
    }
}