<?php

namespace Overtrue\Wechat\Messages;

use Exception;
use Overtrue\Wechat\Utils\XML;

/**
 * @property string $content
 */
class Transfer extends AbstractMessage
{

    protected $properties = array('account');

    public function formatToClient()
    {
        throw new Exception("转发类型不允许主动发送");
    }

    public function formatToServer()
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