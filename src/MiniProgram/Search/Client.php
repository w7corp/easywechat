<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\Search;

use EasyWeChat\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * Submit applet page URL and parameter information.
     *
     * @param array $pages
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function submitPage(array $pages)
    {
        return $this->httpPostJson('wxa/search/wxaapi_submitpages', compact('pages'));
    }
}
