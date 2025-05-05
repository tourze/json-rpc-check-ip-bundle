<?php

namespace Tourze\JsonRPCCheckIPBundle\Tests\Integration;

use Tourze\JsonRPCCheckIPBundle\Attribute\CheckIp;

/**
 * 测试控制器，用于集成测试
 */
#[CheckIp(envKey: 'TEST_IP_ENV')]
class TestController
{
    /**
     * 测试方法
     */
    public function testMethod(): array
    {
        return ['success' => true];
    }
}
