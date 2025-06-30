<?php

namespace Tourze\JsonRPCCheckIPBundle\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Tourze\JsonRPCCheckIPBundle\Tests\Integration\TestController;

/**
 * TestController 的测试
 */
class TestControllerTest extends TestCase
{
    public function testTestMethod(): void
    {
        $controller = new TestController();
        $result = $controller->testMethod();
        
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }
}