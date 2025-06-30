<?php

namespace Tourze\JsonRPCCheckIPBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\JsonRPCCheckIPBundle\DependencyInjection\JsonRPCCheckIPExtension;

/**
 * JsonRPCCheckIPExtension 扩展类的测试
 */
class JsonRPCCheckIPExtensionTest extends TestCase
{
    private JsonRPCCheckIPExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new JsonRPCCheckIPExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoad(): void
    {
        $configs = [];
        $this->extension->load($configs, $this->container);

        // 验证服务是否被加载
        $this->assertTrue($this->container->hasDefinition('Tourze\\JsonRPCCheckIPBundle\\EventSubscriber\\CheckIpSubscriber'));
    }
}