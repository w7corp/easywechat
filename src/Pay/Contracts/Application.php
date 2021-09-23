<?php

declare(strict_types=1);

namespace EasyWeChat\Pay\Contracts;

use EasyWeChat\Kernel\Contracts\Config;
use EasyWeChat\Pay\Jssdk;
use Symfony\Contracts\HttpClient\HttpClientInterface;

interface Application
{
    public function getMerchant(): Merchant;
    public function getConfig(): Config;
    public function getHttpClient(): HttpClientInterface;
    public function getClient(): HttpClientInterface;
    public function getV2Client(): HttpClientInterface;
    public function getJssdk(): Jssdk;
}
