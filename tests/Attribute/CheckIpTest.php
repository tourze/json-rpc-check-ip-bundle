<?php

namespace Tourze\JsonRPCCheckIPBundle\Tests\Attribute;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPCCheckIPBundle\Attribute\CheckIp;

/**
 * @internal
 */
#[CoversClass(CheckIp::class)]
final class CheckIpTest extends TestCase
{
    public function testConstructorWithDefaultValue(): void
    {
        // 测试默认值
        $checkIp = new CheckIp();
        $this->assertSame('', $checkIp->envKey);
    }

    public function testConstructorWithCustomValue(): void
    {
        // 测试自定义值
        $checkIp = new CheckIp('CUSTOM_IP_KEY');
        $this->assertSame('CUSTOM_IP_KEY', $checkIp->envKey);
    }
}
