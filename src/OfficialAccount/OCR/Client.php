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

        return $this->httpPost('cv/ocr/idcard', [
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
        return $this->httpPost('cv/ocr/bankcard', [
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
        return $this->httpPost('cv/ocr/drivinglicense', [
            'img_url' => $path,
        ]);
    }

    /**
     * Driving OCR.
     *
     * @param string $path
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function driving(string $path)
    {
        return $this->httpPost('cv/ocr/driving', [
            'img_url' => $path,
        ]);
    }

    /**
     * Biz License OCR.
     *
     * @param string $path
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function bizLicense(string $path)
    {
        return $this->httpPost('cv/ocr/bizlicense', [
            'img_url' => $path,
        ]);
    }

    /**
     * Common OCR.
     *
     * @param string $path
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function common(string $path)
    {
        return $this->httpPost('cv/ocr/comm', [
            'img_url' => $path,
        ]);
    }

    /**
     * Plate Number OCR.
     *
     * @param string $path
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function plateNumber(string $path)
    {
        return $this->httpPost('cv/ocr/platenum', [
            'img_url' => $path,
        ]);
    }
}
