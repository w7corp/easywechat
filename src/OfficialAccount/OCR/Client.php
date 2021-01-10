<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\OCR;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

class Client extends BaseClient
{
    /**
     * Allow image parameter type.
     *
     * @var array
     */
    protected array $allowTypes = ['photo', 'scan'];

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
}
