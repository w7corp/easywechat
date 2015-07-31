<?php

/**
 * Article.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Message;

use EasyWeChat\Support\Attribute;

/**
 * Class Article.
 */
class Article extends Attribute
{
    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
                                'thumb_media_id',
                                'author',
                                'title',
                                'description',
                                'content',
                                'digest',
                                'url',
                                'pic_url',
                                'source_url',
                                'show_cover_pic',
                            ];
}
