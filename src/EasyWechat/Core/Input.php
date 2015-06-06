<?php
/**
 * Input.php
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Core;

use EasyWeChat\Support\Collection;

class Input extends Collection
{

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct(array_merge($_GET, $_POST));
    }
}
