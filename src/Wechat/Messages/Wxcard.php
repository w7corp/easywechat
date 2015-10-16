<?php
/**
 * Created by PhpStorm.
 * User: pjxh
 * Date: 15-10-16
 * Time: 下午1:47
 */

namespace Overtrue\Wechat\Messages;


/**
 * Class Wxcard
 *
 * @property string $card_id
 * @property string $card_ext
 */
class Wxcard extends BaseMessage
{
    protected $properties = array(
        'card_id','card_ext'
    );

    public function card_id($card_id)
    {
        $this->setAttribute('card_id',$card_id);

        return $this;
    }

    public function card_ext($card_ext)
    {
        $this->setAttribute('card_ext',$card_ext);

        return $this;
    }

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