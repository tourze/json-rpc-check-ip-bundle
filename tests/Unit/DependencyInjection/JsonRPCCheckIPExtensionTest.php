<?php

namespace Tourze\JsonRPCCheckIPBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\JsonRPCCheckIPBundle\DependencyInjection\JsonRPCCheckIPExtension;
use Tourze\JsonRPCCheckIPBundle\EventSubscriber\CheckIpSubscriber;

class JsonRPCCheckIPExtensionTest extends TestCase
{
    /**
     * 测试加载服务配置
     */
    public function testLoad(): void
    {
        $container = new ContainerBuilder();
        $extension = new JsonRPCCheckIPExtension();

        $extension->load([], $container);

        // 验证服务定义是否正确加载
        $this->assertTrue($container->has(CheckIpSubscriber::class));

        // 验证自动配置和自动注入设置
        $checkIpSubscriberDef = $container->findDefinition(CheckIpSubscriber::class);
        $this->assertTrue($checkIpSubscriberDef->isAutowired());
        $this->assertTrue($checkIpSubscriberDef->isAutoconfigured());
    }
}
