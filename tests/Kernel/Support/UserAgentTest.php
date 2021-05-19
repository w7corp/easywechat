<?php

namespace EasyWeChat\Tests\Kernel\Support;

use Composer\InstalledVersions;
use EasyWeChat\Kernel\Support\UserAgent;
use PHPUnit\Framework\TestCase;

class UserAgentTest extends TestCase
{
    public function test_it_can_generate_user_agent()
    {
        $this->assertSame(
            \sprintf(
                'easywechat-sdk/%s OS/%s curl/%s custom-part custom-part2',
                InstalledVersions::getVersion('w7corp/easywechat'),
                php_uname('s').'/'.php_uname('r'),
                \curl_version()['version']
            ),
            UserAgent::create(['custom-part', 'custom-part2'])
        );
    }
}
