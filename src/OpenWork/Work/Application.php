<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork\Work;

use EasyWeChat\OpenWork\Application as OpenWork;
use EasyWeChat\OpenWork\Work\Auth\AccessToken;
use EasyWeChat\Work\Application as Work;

/**
 * Application.
 *
 */
class Application extends Work
{
    /**
     * @param string   $authCorpId
     * @param string   $permanentCode
     * @param OpenWork $component
     * @param array    $prepends
     */
    public function __construct(string $authCorpId, string $permanentCode, OpenWork $component, array $prepends = [])
    {
        parent::__construct(\array_merge($component->getConfig(), ['corp_id' => $authCorpId]), $prepends + [
                'access_token' => function ($app) use ($authCorpId, $permanentCode, $component) {
                    return new AccessToken($app, $authCorpId, $permanentCode, $component);
                },
            ]);
    }
}
