<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\Mall;

use EasyWeChat\Kernel\BaseClient;

class MediaClient extends BaseClient
{
    /**
     * 更新或导入媒体信息.
     *
     * @param array $params
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function import($params)
    {
        return $this->httpPostJson('mall/importmedia', $params);
    }
}
