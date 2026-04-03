<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OpenWork;

use EasyWeChat\OpenWork\Authorization;
use EasyWeChat\Tests\TestCase;

class AuthorizationTest extends TestCase
{
    public function test_get_app_id_and_corp_id()
    {
        $authorization = new Authorization([
            'auth_corp_info' => [
                'corpid' => 'mock-corp-id',
            ],
        ]);

        $this->assertSame('mock-corp-id', $authorization->getAppId());
        $this->assertSame('mock-corp-id', $authorization->getCorpId());
    }

    public function test_missing_keys_return_empty_strings_without_warnings()
    {
        $authorization = new Authorization([]);

        $errors = [];
        $handler = static function (int $severity, string $message) use (&$errors): bool {
            $errors[] = [$severity, $message];

            return true;
        };

        \set_error_handler($handler);

        try {
            $this->assertSame('', $authorization->getAppId());
            $this->assertSame('', $authorization->getCorpId());
        } finally {
            \restore_error_handler();
        }

        $this->assertSame([], $errors);
    }
}
