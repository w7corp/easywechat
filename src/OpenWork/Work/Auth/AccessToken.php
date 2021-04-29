<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork\Work\Auth;

use EasyWeChat\Kernel\AccessToken as BaseAccessToken;
use EasyWeChat\OpenWork\Application;
use Pimple\Container;

class AccessToken extends BaseAccessToken
{
    /**
     * @var string
     */
    protected string $requestMethod = 'POST';

    /**
     * @param Container   $app
     * @param string      $authCorpId
     * @param string      $permanentCode
     * @param Application $component
     */
    public function __construct(
        Container $app,
        public string $authCorpId,
        public string $permanentCode,
        public Application $component
    ) {
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
            'auth_corpid' => $this->authCorpId,
            'permanent_code' => $this->permanentCode,
        ];
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        $query = http_build_query([
            'suite_access_token' => $this->component['suite_access_token']?->getToken()['suite_access_token'],
        ]);

        return 'cgi-bin/service/get_corp_token?'.$query;
    }
}
