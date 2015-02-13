<?php

namespace Overtrue\Wechat\Messages;

use Closure;
use InvalidArgumentException;
use Overtrue\Wechat\Utils\XML;
use Overtrue\Wechat\Utils\MagicAttributes;

/**
 * @method   array  buildForStaff() buildForStaff()
 * @method   string buildForReply() buildForReply()
 * @method   array  toStaff()       toStaff()
 * @method   array  toReply()       toReply()
 * @method   array  toBroadcast()   toBroadcast()
 * @property \Overtrue\Wechat\Messages\BaseMessage from() from()
 * @property \Overtrue\Wechat\Messages\BaseMessage to()   to()
 * @property string $from
 * @property string $to
 * @property string $staff
 */
abstract class BaseMessage extends MagicAttributes
{
    /**
     * 基础属性
     *
     * @var array
     */
    protected $baseAttributes = array('from', 'to', 'to_group', 'to_all', 'staff');

    /**
     * 生成用于主动推送的数据
     *
     * @return array
     */
    public function buildForStaff()
    {
        if (!method_exists($this, 'toStaff')) {
            throw new Exception(__CLASS__ . "未实现此方法：toStaff()");
        }

        $base = array(
                 'touser'  => $this->to,
                 'msgtype' => $this->getDefaultMessageType(),
                );
        if (!empty($this->staff)) {
            $base['customservice'] = array('kf_account' => $this->staff);
        }

        return array_merge($base, $this->toStaff());
    }

    /**
     * 生成用于回复的数据
     *
     * @return array
     */
    public function buildForReply()
    {
        if (!method_exists($this, 'toReply')) {
            throw new Exception(__CLASS__ . "未实现此方法：toReply()");
        }

         $base = array(
                     'ToUserName'   => $this->to,
                     'FromUserName' => $this->from,
                     'CreateTime'   => time(),
                     'MsgType'      => $this->getDefaultMessageType(),
                    );

        return XML::build(array_merge($base, $this->toReply()));
    }

    /**
     * 生成群发的数据
     *
     * @return array
     */
    public function buildForBroadcast()
    {
        if (!method_exists($this, 'toBroadcast')) {
            throw new Exception(__CLASS__ . "未实现此方法：toBroadcast()");
        }

        //TODO
    }

    /**
     * 获取默认的消息类型名称
     *
     * @return string
     */
    public function getDefaultMessageType()
    {
        $class = explode('\\', get_class($this));

        return strtolower(array_pop($class));
    }
}