<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Contracts;

interface Account
{
    public function getCorpId(): string;

    public function getSecret(): string;

    public function getToken(): string;

    public function getAesKey(): string;
}
