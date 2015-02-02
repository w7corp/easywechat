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

        return Wechat::post(Wechat::makeUrl('user.remark'), $params);
    }

    /**
     * 获取/设置用户所在分组
     *
     * @param integer $groupId 是否返回group
     *
     * @return Overtrue\Wechat\Group
     */
    public function group($groupId = null)
    {
        return $groupId ? $this->moveTo($groupId) : $this->getGroup();
    }

    /**
     * 移动用户到分组
     *
     * @param integer $groupId
     *
     * @return boolean
     */
    public function toGroup($groupId)
    {
        $params = array(
                   'openid'     => $this->openId,
                   'to_groupid' => ($groupId instanceof Group) ? $groupId->id : $groupId;
                  );

        Wechat::post(Wechat::makeUrl('group.member.update'), $params);

        return true;
    }

    /**
     * 获取用户所在的组
     *
     * @return \Overtrue\Wechat\Group
     */
    protected function getGroup()
    {
        $params = array(
                   'openid' => $this->openId,
                  );

        $response = Wechat::post(Wechat::makeUrl('user.group'), $params);

        return new Group($response['groupid']);
    }

    /**
     * 重写生成数组
     *
     * @return array
     */
    public function toArray()
    {
        if (!$this->has('openid'))) {
            $this->merge($this->getUser($this->openId));
        }

        return parent::toArray();
    }

    /**
     * 重写生成json
     *
     * @return array
     */
    public function toJson()
    {
        if (!$this->has('openid'))) {
            $this->merge($this->getUser($this->openId));
        }

        return parent::toJson();
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
                   'openid' => $this->openId,
                   'lang'   => $this->lang
                  );

        $url = Wechat::makeUrl('user.get', $params);

        return Wechat::get($url);
    }
}