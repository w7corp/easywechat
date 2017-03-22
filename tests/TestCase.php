<?php

namespace {
    /**
     * class TestCase.
     */
    class TestCase extends PHPUnit_Framework_TestCase
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
}

/**
 * Static methods autoloading.
 */
namespace EasyWeChat\Support {
    class Url
    {
        public static function current()
        {
            return 'http://current.org';
        }

        public static function encode($url)
        {
            return urlencode($url);
        }
    }
}

