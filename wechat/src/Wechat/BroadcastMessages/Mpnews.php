<?php
/**
 * News.php
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

use Closure;

/**
 * 图文消息
 */
class Mpnews extends BaseMessage
{

    /**
     * 属性
     *
     * @var array
     */
    protected $properties = array(
                             'media_id',
                            );


    /**
     * 生成按用户组群发数组
     */
    public function toBroadcast()
    {
        $news = array(
            'mpnews' => array(
                'media_id' => $this->media_id,
                )
            );

        return $news;
    }

}
