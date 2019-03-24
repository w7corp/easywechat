<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\OCR;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

/**
 * Class Client.
 *
 * @author joyeekk <xygao2420@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Allow image parameter type.
     *
     * @var array
     */
    protected $allowTypes = ['photo', 'scan'];

    /**
     * ID card OCR.
     *
     * @param string $path
     * @param string $type
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function idCard(string $path, string $type = 'photo')
    {
        if (!\in_array($type, $this->allowTypes, true)) {
            throw new InvalidArgumentException(sprintf("Unsupported type: '%s'", $type));
        }

        return $this->httpGet('cv/ocr/idcard', [
            'type' => $type,
            'img_url' => $path,
        ]);
    }

    /**
     * Bank card OCR.
     *
     * @param string $path
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function bankCard(string $path)
    {
        return $this->httpGet('cv/ocr/bankcard', [
            'img_url' => $path,
        ]);
    }

    /**
     * Vehicle license OCR.
     *
     * @param string $path
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function vehicleLicense(string $path)
    {
        return $this->httpGet('cv/ocr/driving', [
            'img_url' => $path,
        ]);
    }
}
