<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\ShakeAround;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

class MaterialClient extends BaseClient
{
    /**
     * Upload image material.
     *
     * @param string $path
     * @param string $type
     *
     * @return string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadImage(string $path, string $type = 'icon')
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new InvalidArgumentException(sprintf('File does not exist, or the file is unreadable: "%s"', $path));
        }

        return $this->httpUpload('shakearound/material/add', ['media' => $path], [], ['type' => strtolower($type)]);
    }
}
