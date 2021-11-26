<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork\Contracts;

interface Account
{
    public function getCorpId(): string;

    public function getProviderSecret(): string;

    public function getSuiteId(): string;

    public function getSuiteSecret(): string;

    public function getToken(): string;

    public function getAesKey(): string;
}
