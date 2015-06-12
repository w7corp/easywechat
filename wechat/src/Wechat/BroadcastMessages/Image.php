<?php
/**
 * Image.php
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

use MasApi\Wechat\Media;

/**
 * 图片消息
 *
 * @property string $media_id
 */
class Image extends BaseMessage
{

    /**
     * 属性
     *
     * @var array
     */
    protected $properties = array('media_id');

    /**
     * 生成图片群发
     */
    public function toBroadcast()
    {
        $image = array(
            'image' => array(
                'media_id' => $this->media_id,
                )
            );

        return $image;
    }
}
