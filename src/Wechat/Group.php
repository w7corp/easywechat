<?php namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Bag;

class Group extends Bag {

    /**
     * 组 id
     *
     * @var string
     */
    protected $groupId;

    /**
     * 实例化组
     *
     * @param string $groupId
     */
    public function __construct($groupId, $properties = [])
    {
        $this->groupId = $groupId;

        parent::__construct($properties);
    }

    /**
     * 创建分组
     *
     * @param string $name
     *
     * @return Overtrue\Wechat\Group
     */
    public function create($name)
    {
        $params = array(
                    'group' => array(
                                'name' => $name,
                               ),
                  );

        $response = Wechat::post(Wechat::makeUrl('group.create'), $params);

        $this->merge($response['group']);
        $this->groupId = $this->id;

        return $this;
    }

    /**
     * 更新分钟名称
     *
     * @param string $name
     *
     * @return Overtrue\Wechat\Group
     */
    public function update($name)
    {
        $params = array(
                    'group' => array(
                                'id'   => $this->groupId,
                                'name' => $name,
                               ),
                  );

        Wechat::post(Wechat::makeUrl('group.update'), $params);

        $this->name = $name;

        return $this;
    }

    /**
     * 批量移动用户
     *
     * @param array $users
     *
     * @return boolean
     */
    public function users(array $users)
    {
         $params = array(
                    'openid_list' => $users,
                    'to_groupid'  => $this->groupId,
                  );

        Wechat::post(Wechat::makeUrl('group.member.batch_update'), $params);

        return true;
    }

    /**
     * 重写生成数组
     *
     * @return array
     */
    public function toArray()
    {
        if (!$this->has('id'))) {
            $this->merge($this->getUser($this->groupId));
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
        if (!$this->has('id'))) {
            $this->merge($this->getUser($this->groupId));
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
        if (!$this->has('id'))) {
            $this->merge($this->getUser($this->groupId));
        }

        return parent::get($property, $default, $callback);
    }

}