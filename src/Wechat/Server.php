<?php namespace Overtrue\Wechat;

use Overtrue\Wechat\Util\Arr;

class Server {

    const SEC_MODE_PLAIN_TEXT = 1; //明文模式
    const SEC_MODE_COMPATIBLE = 2; //兼容模式
    const SEC_MODE_SECURITY   = 3; //安全模式

    protected $appId;
    protected $options = array();
    protected $securityMode;
    protected $listeners = array();

    public function _construct(array $options = array())
    {
        $this->options = $options;
    }

    /**
     * 设置安全模式
     *
     * @param integer $mode
     */
    public function setSecurityMode($mode)
    {
        $this->securityMode = $mode;
    }

    /**
     * 监听事件
     *
     * @param string   $event
     * @param callback $function
     *
     * @return mixed
     */
    public function listen($event, callback $function)
    {
        Arr::add($this->listeners, $event, $function);
    }

    public function authorize(callback $callback)
    {
        # code...
    }
}