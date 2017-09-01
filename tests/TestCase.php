<?php

namespace Verja\Test;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Performs assertions shared by all tests of a test case. This method is
     * called before execution of a test ends and before the tearDown method.
     */
    protected function assertPostConditions()
    {
        $this->addMockeryExpectationsToAssertionCount();
        $this->closeMockery();

        parent::assertPostConditions();
    }

    protected function addMockeryExpectationsToAssertionCount()
    {
        $container = \Mockery::getContainer();
        if ($container != null) {
            $count = $container->mockery_getExpectationCount();
            $this->addToAssertionCount($count);
        }
    }

    protected function closeMockery()
    {
        \Mockery::close();
    }
}
