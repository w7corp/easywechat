<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenWork\Work\Auth;

use EasyWeChat\Kernel\AccessToken as BaseAccessToken;
use EasyWeChat\OpenWork\Application;
use Pimple\Container;

/**
 * AccessToken.
 *
 * @author xiaomin <keacefull@gmail.com>
 */
class AccessToken extends BaseAccessToken
{
    /**
     * @var string
     */
    protected $requestMethod = 'POST';

    /**
     * @var string 授权方企业ID
     */
    protected $authCorpid;

    /**
     * @var string 授权方企业永久授权码，通过get_permanent_code获取
     */
    protected $permanentCode;

    protected $component;

    /**
     * AccessToken constructor.
     */
    public function __construct(Container $app, string $authCorpId, string $permanentCode, Application $component)
    {
        $this->authCorpid = $authCorpId;
        $this->permanentCode = $permanentCode;
        $this->component = $component;
        parent::__construct($app);
    }

    /**
     * Credential for get token.
     */
    protected function getCredentials(): array
    {
        return [
            'auth_corpid' => $this->authCorpid,
            'permanent_code' => $this->permanentCode,
        ];
    }

    public function getEndpoint(): string
    {
        return 'cgi-bin/service/get_corp_token?'.http_build_query([
                'suite_access_token' => $this->component['suite_access_token']->getToken()['suite_access_token'],
            ]);
    }
}
