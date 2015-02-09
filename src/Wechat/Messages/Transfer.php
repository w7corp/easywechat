<?php

namespace Overtrue\Wechat\Messages;

use Exception;
use Overtrue\Wechat\Utils\XML;

/**
 * @property string $account
 */
class Transfer extends AbstractMessage
{

    protected $properties = array('account');

    /**
     * @see Overtrue\Wechat\Messages\AbstractMessage::buildForStaff();
     */
    public function buildForStaff()
    {
        throw new Exception("转发类型不允许主动发送");
    }

    /**
     * @see Overtrue\Wechat\Messages\AbstractMessage::buildForReply();
     */
    public function buildForReply()
    {
        $response = array(
                     'ToUserName'   => $this->to,
                     'FromUserName' => $this->from,
                     'CreateTime'   => time(),
                     'MsgType'      => 'transfer_customer_service',
                    );

        // 指定客服
        if (!empty($this->account)) {
            $response['TransInfo'] = array(
                                      'KfAccount' => $this->account,
                                     );
        }

        return XML::build($response);
    }

}