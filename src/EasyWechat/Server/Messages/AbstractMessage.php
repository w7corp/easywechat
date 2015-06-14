<?php
/**
 * AbstractMessage.php
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Server\Messages;

use EasyWeChat\Support\MagicAttributes;
use EasyWeChat\Support\XML;

/**
 * Class AbstractMessage
 *
 * @property string      $from
 * @property string      $to
 * @property string      $staff
 *
 * @method AbstractMessage to($to)
 * @method AbstractMessage from($from)
 * @method AbstractMessage staff($staff)
 * @method array       toStaff()
 * @method array       toReply()
 * @method array       toBroadcast()
 *
 * @package EasyWeChat\Server\Messages
 */
abstract class AbstractMessage extends MagicAttributes
{

    /**
     * Message properties.
     *
     * @var array
     */
    protected $properties = array();

    /**
     * Base properties.
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
     * Build message for Staff.
     *
     * @return array
     */
    public function buildForStaff()
    {
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
     * Build message for reply.
     *
     * @return array
     */
    public function buildForReply()
    {
        $base = array(
                 'ToUserName'   => $this->to,
                 'FromUserName' => $this->from,
                 'CreateTime'   => time(),
                 'MsgType'      => $this->getDefaultMessageType(),
                );

        return XML::build(array_merge($base, $this->toReply()));
    }

    /**
     * Return default message type.
     *
     * @return string
     */
    public function getDefaultMessageType()
    {
        $class = explode('\\', get_class($this));

        return strtolower(array_pop($class));
    }

    /**
     * Validate attribute.
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
}//end class
