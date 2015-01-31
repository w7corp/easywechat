<?php namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Bag;

class User extends Bag {

    /**
     * 用户open id
     *
     * @var string
     */
    protected $openId;

    /**
     * 用户资料语言
     *
     * @var string
     */
    protected $lang;


    /**
     * 实例化用户
     *
     * @param string $openId
     * @param array  $properties
     */
    public function __construct($openId, $lang = 'zh_CN', $properties = [])
    {
        $this->openId = $openId;
        $this->lang   = $lang;

        parent::__construct($properties);
    }


    /**
     * 修改用户备注
     *
     * @param string $id     open id
     * @param string $remark 备注
     *
     * @return boolean
     */
    public function remark($remark)
    {
        $params = array(
                   'openid' => $this->openId,
                   'remark' => $remark,
                  );

        return Wechat::get(Wechat::makeUrl('user.remark'), $params);
    }

    /**
     * 获取用户信息
     *
     * @param string $openId
     *
     * @return array
     */
    private function getUser($openId)
    {
        $params = array(
                   'openid' => $openId,
                   'lang'   => $lang
                  );

        $url = Wechat::makeUrl('user.get', $params);

        return Wechat::get($url);
    }

    /**
     * 处理属性访问
     *
     * @param string $property
     *
     * @return string
     */
    public function get($property, $default = null, Closure $callback = null)
    {
        if (!$this->has('openid'))) {
            $this->merge($this->getUser($this->openId));
        }

        return parent::get($property, $default, $callback);
    }
}