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
     * @var String 授权方企业ID
     */
    protected $auth_corpid;

    /**
     * @var String 授权方企业永久授权码，通过get_permanent_code获取
     */
    protected $permanent_code;

    protected $component;

    /**
     * AccessToken constructor.
     *
     * @param Container   $app
     * @param String      $auth_corpid
     * @param String      $permanent_code
     * @param Application $component
     */
    public function __construct(Container $app, String $auth_corpid, String $permanent_code, Application $component)
    {
        $this->auth_corpid = $auth_corpid;
        $this->permanent_code = $permanent_code;
        parent::__construct($app);
    }

    /**
     * Credential for get token.
     *
     * @return array
     */
    protected function getCredentials(): array
    {
        return [
            'auth_corpid'    => $this->auth_corpid,
            'permanent_code' => $this->permanent_code,
        ];
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return 'cgi-bin/service/get_corp_token?' . http_build_query([
                'suite_access_token' => $this->component['suite_access_token']->getToken()['suite_access_token'],
            ]);
    }
}