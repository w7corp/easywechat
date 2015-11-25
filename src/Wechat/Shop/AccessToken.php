<?php
/**
 * AccessToken.php
 *
 * Part of Overtrue\Wechat\Shop
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    a9396 <a939638621@hotmail.com>
 * @copyright 2015 a939638621 <a939638621@hotmail.com>
 * @link      https://github.com/a939638621
 */

namespace Overtrue\Wechat\Shop;


class AccessToken extends \Overtrue\Wechat\AccessToken
{
    public function __construct(Config $config)
    {
        parent::__construct($config->appId, $config->appSecret);
    }
}