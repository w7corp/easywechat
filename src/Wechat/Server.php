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

    /**
     * 应答微信的验证请求
     *
     * @param boolean $return 是否返回echostr,默认直接输出
     *
     * @return void|string
     */
    public function validation($return = false)
    {
        if ($this->checkSignature()) {
            return false;
        }

        $msg = Arr::get($_GET, 'echostr');

        if ($return) {
            return $msg;
        }

        exit($msg);
    }

    /**
     * 检查签名有效性
     */
    public function checkSignature()
    {
        $inputSignature = Arr::get($_GET, 'signature');
        $timestamp      = Arr::get($_GET, 'timestamp');
        $nonce          = Arr::get($_GET, 'nonce');

        $token  = Arr::get($this->options, 'token');
        $arr    = array($token, $timestamp, $nonce);

        sort($arr, SORT_STRING);

        $signature = implode($arr);
        $signature = sha1($signature);

        return $signature === $inputSignature;
    }
}