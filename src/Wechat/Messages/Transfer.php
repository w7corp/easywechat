<?php

namespace Overtrue\Wechat\Messages;

use Exception;

/**
 * @property string $to
 * @property string $account
 */
class Transfer extends BaseMessage
{

    protected $properties = array('account', 'to');

    /**
     * 生成主动消息数组
     */
    public function toStaff()
    {
        throw new Exception("转发类型不允许主动发送");
    }

    /**
     * 生成回复消息数组
     */
    public function toReply()
    {
        $response = array(
                     'MsgType' => 'transfer_customer_service',
                    );

        // 指定客服
        if (!empty($this->account) || !empty($this->to)) {
            $response['TransInfo'] = array(
                                      'KfAccount' => $this->account ? : $this->to,
                                     );
        }

        return $response;
    }

}