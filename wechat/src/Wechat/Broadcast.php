<?php
/**
 * Broadcast.php
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

namespace MasApi\Wechat;

use MasApi\Wechat\BroadcastMessages\BaseMessage;

/**
 * 高级群发
 */
class Broadcast
{

    /**
     * 消息
     *
     * @var \MasApi\Wechat\BroadcastMessages\BaseMessage;
     */
    protected $message;

    /**
     * 指定消息群发
     *
     * @var string
     */
    protected $by;

    /**
     * 请求的headers
     *
     * @var array
     */
    protected $headers = array('content-type:application/json');

    const API_BROADCAST_SEND_GROUP           = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall';
    const API_BROADCAST_SEND_OPENID        = 'https://api.weixin.qq.com/cgi-bin/message/mass/send';
    const API_BROADCAST_DELETE        = 'https://api.weixin.qq.com/cgi-bin/message/mass/delete';
    const API_BROADCAST_PREVIEW        = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview';
    const API_BROADCAST_GET       = 'https://api.weixin.qq.com/cgi-bin/message/mass/get';

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }

    /**
     * 准备消息
     *
     * @param \MasApi\Wechat\BroadcastMessages\BaseMessage $message
     *
     * @return Broadcast
     */
    public function send($message)
    {
        is_string($message) && $message = BroadcastMessage::make('text')->with('content', $message);

        if (!$message instanceof BaseMessage) {
            throw new Exception("消息必须继承自 'MasApi\Wechat\BaseMessage'");
        }

        $this->message = $message;

        return $this;
    }

    /**
     * 群发消息给openid
     *
     * @param arrary $openId
     *
     * @return bool
     */
    public function to($openId = array())
    {
        if (empty($this->message)) {
            throw new Exception('未设置要发送的消息');
        }

        $this->message->to = $openId;

        return $this->http->jsonPost(self::API_BROADCAST_SEND_OPENID, $this->message->buildForBroadcast());
    }

    /**
     * 群发消息all
     *
     * @return bool
     */
    public function to_all()
    {
        if (empty($this->message)) {
            throw new Exception('未设置要发送的消息');
        }

        $this->message->to = 'all';

        return $this->http->jsonPost(self::API_BROADCAST_SEND_GROUP, $this->message->buildForBroadcast());
    }

    /**
     * 群发消息给用户组
     *
     * @param string $groupId
     *
     * @return bool
     */
    public function to_group($groupId = '')
    {
        if (empty($this->message)) {
            throw new Exception('未设置要发送的消息');
        }

        $this->message->to = $groupId;

        return $this->http->jsonPost(self::API_BROADCAST_SEND_GROUP, $this->message->buildForBroadcast());
    }

    /**
     * 删除群发
     *
     * @param string $groupId
     *
     * @return bool
     */
    public function delete($msg_id = '')
    {

        return $this->http->jsonPost(self::API_BROADCAST_DELETE, $msg_id);
    }

    /**
     * 预览群发
     *
     * @param array $openId
     *
     * @return bool
     */
    public function preview($openId = '')
    {
        if (empty($this->message)) {
            throw new Exception('未设置要发送的消息');
        }

        $this->message->to = $openId;

        return $this->http->jsonPost(self::API_BROADCAST_PREVIEW, $this->message->buildForPreview());
    }

    /**
     * 查询群发消息发送状态
     *
     * @param string $msg_id
     *
     * @return bool
     */
    public function get($msg_id = '')
    {

        return $this->http->jsonPost(self::API_BROADCAST_SEND_GET, $msg_id);
    }
}
