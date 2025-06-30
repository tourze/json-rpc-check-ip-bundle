<?php

namespace Tourze\JsonRPCCheckIPBundle;

use PHPUnit\Framework\TestCase;
use Tourze\JsonRPCCheckIPBundle\Attribute\CheckIp;

/**
 * 测试 CheckIp 属性类
 */
class CheckIpTest extends TestCase
{
    public function testConstructor_withDefaultValue(): void
    {
        // 测试默认值
        $checkIp = new CheckIp();
        $this->assertSame('', $checkIp->envKey);
    }

    public function testConstructor_withCustomValue(): void
    {
        // 测试自定义值
        $checkIp = new CheckIp('CUSTOM_IP_KEY');
        $this->assertSame('CUSTOM_IP_KEY', $checkIp->envKey);
    }
}