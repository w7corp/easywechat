<?php namespace Overtrue\Wechat;

use Overtrue\Wechat\Util\Arr;

class Client {

    protected $appId;
    protected $options = array();

    public function _construct(array $options = array())
    {
        $this->options = $options;
    }
}