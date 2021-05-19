<?php

namespace EasyWeChat\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * Tear down the test case.
     */
    public function tearDown(): void
    {
        parent::tearDown();
        if ($container = \Mockery::getContainer()) {
            $this->addToAssertionCount($container->Mockery_getExpectationCount());
        }
        \Mockery::close();
    }
}
