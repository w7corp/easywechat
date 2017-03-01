<?php

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * class TestCase.
 */
class TestCase extends PHPUnitTestCase
{
    /**
     * Tear down the test case.
     */
    public function tearDown()
    {
        $this->finish();
        parent::tearDown();
        if ($container = Mockery::getContainer()) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }
        Mockery::close();
    }

    /**
     * Run extra tear down code.
     */
    protected function finish()
    {
        // call more tear down methods
    }
}
