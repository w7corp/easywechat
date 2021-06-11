<?php

namespace EasyWeChat\OfficialAccount\Contracts;

use EasyWeChat\Kernel\ApiBuilder;
use EasyWeChat\Kernel\Config;
use EasyWeChat\Kernel\Encryptor;
use Psr\SimpleCache\CacheInterface;

interface Application
{
    public function getAccount(): Account;
    public function getEncryptor(): Encryptor;
    public function getServer(): Server;
    public function getRequest(): Request;
    public function getClient(): ApiBuilder;
    public function initConfig(array $config): Config;
    public function setConfig(Config $config): Config;
    public function getConfig(): Config;
    public function getAccessToken(): AccessToken;
    public function getCache(): CacheInterface;
}
