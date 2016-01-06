<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Wxcard.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    a939638621 <a939638621@hotmail.com>
 * @copyright 2015 a939638621 <a939638621@hotmail.com>
 *
 * @link      https://github.com/a939638621
 */

namespace Overtrue\Wechat\Messages;

/**
 * 卡券消息.
 *
 * @property string $card_id
 * @property string $card_ext
 */
class Wxcard extends BaseMessage
{
    /**
     * 属性.
     *
     * @var array
     */
    protected $properties = array(
        'card_id', 'card_ext',
    );

    /**
     * 设置 card_id.
     *
     * @param string $cardId
     *
     * @return $this
     */
    public function cardId($cardId)
    {
        $this->setAttribute('card_id', $cardId);

        return $this;
    }

    /**
     * 设置 card_ext.
     *
     * @param string $cardExt
     *
     * @return $this
     */
    public function cardExt($cardExt)
    {
        $this->setAttribute('card_ext', $cardExt);

        return $this;
    }

    /**
     * 生产客服接口.
     *
     * @return array
     */
    public function toStaff()
    {
        return array(
            'wxcard' => array(
                'card_id' => $this->card_id,
                'card_ext' => $this->card_ext,
            ),
        );
    }
}
