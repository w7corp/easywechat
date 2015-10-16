<?php
/**
 * Created by PhpStorm.
 * User: pjxh
 * Date: 15-10-16
 * Time: 下午1:47
 */

namespace Overtrue\Wechat\Messages;


/**
 * Class Card
 * @package Overtrue\Wechat\Messages
 * @property string $card_id
 * @property string $card_ext
 */
class Card extends BaseMessage
{
    protected $properties = array(
        'card_id','card_ext'
    );

    /**
     * 生产客服接口
     *
     * @return array
     */
    public function toStaff()
    {
        return array(
            'wxcard'=>array(
                'card_id'=>$this->card_id,
                'card_ext'=>$this->card_ext
            )
        );
    }
}