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

use EasyWeChat\OpenWork\Application as OpenWork;
use EasyWeChat\OpenWork\Work\Auth\AccessToken;
use EasyWeChat\Work\Application as Work;

/**
 * Application.
 *
 * @author xiaomin <keacefull@gmail.com>
 */
class Application extends Work
{
    /**
     * Application constructor.
     */
    public function __construct(string $authCorpId, string $permanentCode, OpenWork $component, array $prepends = [])
    {
        parent::__construct($component->getConfig(), $prepends + [
                'access_token' => function ($app) use ($authCorpId, $permanentCode, $component) {
                    return new AccessToken($app, $authCorpId, $permanentCode, $component);
                },
            ]);
    }
}
