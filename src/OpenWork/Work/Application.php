<?php
/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenWork\Work;

use EasyWeChat\OpenWork\Work\Auth\AccessToken;
use EasyWeChat\Work\Application as Work;
use \EasyWeChat\OpenWork\Application as OpenWork;

/**
 * Application.
 *
 * @author xiaomin <keacefull@gmail.com>
 */
class Application extends Work
{

    /**
     * Application constructor.
     *
     * @param string   $auth_corpid
     * @param string   $permanent_code
     * @param OpenWork $component
     * @param array    $prepends
     */
    public function __construct(string $auth_corpid, string $permanent_code, OpenWork $component, array $prepends = [])
    {
        parent::__construct($component->getConfig(), $prepends + [
                'access_token' => function ($app) use ($auth_corpid, $permanent_code, $component) {
                    return new AccessToken($app, $auth_corpid, $permanent_code, $component);
                }
            ]);
    }

}