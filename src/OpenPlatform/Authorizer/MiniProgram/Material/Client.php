<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Material;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Http\StreamResponse;

class Client extends BaseClient
{
    /**
     * Allow media type.
     *
     * @var array
     */
    protected array $allowTypes = ['image', 'voice', 'video', 'thumb', 'news_image'];

    /**
     * Fetch material.
     *
     * @param string $mediaId
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $mediaId)
    {
        $response = $this->requestRaw('cgi-bin/material/get_material', 'POST', ['json' => ['media_id' => $mediaId]]);

        if (false !== stripos($response->getHeaderLine('Content-disposition'), 'attachment')) {
            return StreamResponse::buildFromPsrResponse($response);
        }

        return $this->castResponseToType($response, $this->app['config']->get('response_type'));
    }
}
