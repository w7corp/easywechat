<?php


/**
 * class TestCase
 */
class TestCase extends PHPUnit_Framework_TestCase
{

    /**
     * Tear down the test case.
     *
     * @return void
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
     *
     * @return void
     */
    protected function finish()
    {
        // call more tear down methods
    }
}