<?php
/**
 * BaseMessage.php
 *
 * Part of MasApi\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace MasApi\Wechat\BroadcastMessages;

use MasApi\Wechat\Utils\MagicAttributes;

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
 * @method array       buildForStaff()
 * @method string      buildForReply()
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
                                 'to',
                                 'to_group',
                                 'to_all',
                                );

    /**
     * 生成群发的数据
     *
     * @return array
     */
    public function buildForBroadcast()
    {
        if (!method_exists($this, 'toBroadcast')) {
            throw new Exception(__CLASS__.'未实现此方法：toBroadcast()');
        }
        if (is_array($this->to)) {
            $base = array(
                     'touser'  => $this->to,
                     'msgtype' => $this->getDefaultMessageType(),
                    );
        }elseif ($this->to == 'all') {
            $base = array(
                     'filter'  => array(
                        'is_to_all' => true,
                        ),
                     'msgtype' => $this->getDefaultMessageType(),
                    );
        }else {
            $base = array(
                     'filter'  => array(
                        'is_to_all' => false,
                        'group_id' => $this->to,
                        ),
                     'msgtype' => $this->getDefaultMessageType(),
                    );
        }

        return array_merge($base, $this->toBroadcast());
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
