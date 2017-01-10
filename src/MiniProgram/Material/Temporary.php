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
 * Temporary.php.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\MiniProgram\Material;

use EasyWeChat\Material\Temporary as BaseTemporary;

/**
 * Class Temporary.
 */
class Temporary extends BaseTemporary
{
    public function __construct()
    {
        $accessToken = func_get_args()[0];

        parent::__construct($accessToken);
    }
}
