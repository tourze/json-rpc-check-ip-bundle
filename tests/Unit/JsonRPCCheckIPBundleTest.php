<?php

namespace Tourze\JsonRPCCheckIPBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\JsonRPCCheckIPBundle\JsonRPCCheckIPBundle;

class JsonRPCCheckIPBundleTest extends TestCase
{
    /**
     * 测试Bundle实例化
     */
    public function testInstantiation(): void
    {
        $bundle = new JsonRPCCheckIPBundle();
        $this->assertInstanceOf(JsonRPCCheckIPBundle::class, $bundle);
        $this->assertInstanceOf(Bundle::class, $bundle);
    }
}
