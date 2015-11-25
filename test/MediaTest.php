<?php
/**
 * MediaTest.php
 *
 * Part of Overtrue\Wechat
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    a9396 <a939638621@hotmail.com>
 * @copyright 2015 a939638621 <a939638621@hotmail.com>
 * @link      https://github.com/a939638621
 */

namespace Overtrue\Wechat\Test;


use Overtrue\Wechat\Media;


class MediaTest extends TestBase
{
    public function testLists()
    {
        $media = new Media($this->config->appId,$this->config->appSecret);
        $response = $media->lists('image');

        $this->assertTrue(is_array($response));
    }
}
