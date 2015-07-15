<?php
/**
 * BaseMessage.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat\Messages;

use Overtrue\Wechat\Utils\MagicAttributes;
use Overtrue\Wechat\Utils\XML;

/**
 * 消息基类
 *
 * @property string      $from
 * @property string      $to
 * @property string      $staff
 *
 * @method BaseMessage to($to)
 * @method BaseMessage from($from)
 * @method BaseMessage staff($staff)
 * @method array       toStaff()
 * @method array       toReply()
 * @method array       toBroadcast()
 */
abstract class BaseMessage extends MagicAttributes
{

    /**
     * 允许的属性
     *
     * @var array
     */
    protected $properties = array();

    /**
     * 基础属性
     *
     * @var array
     */
    protected $baseProperties = array(
                                 'from',
                                 'to',
                                 'to_group',
                                 'to_all',
                                 'staff',
                                );

    /**
     * 生成用于主动推送的数据
     *
     * @return array
     */
    public function buildForStaff()
    {
        if (!method_exists($this, 'toStaff')) {
            throw new \Exception(__CLASS__.'未实现此方法：toStaff()');
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
            throw new \Exception(__CLASS__.'未实现此方法：toReply()');
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
            throw new \Exception(__CLASS__.'未实现此方法：toBroadcast()');
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

    /**
     * 验证
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    protected function validate($attribute, $value)
    {
        $properties = array_merge($this->baseProperties, $this->properties);

        return in_array($attribute, $properties, true);
    }
}
