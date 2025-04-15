<?php

namespace Tourze\JsonRPCCheckIPBundle\Attribute;

/**
 * 如果方法做了这个标记，就会检查来路IP是否符合需求
 *
 * 一般来讲，我们只有服务端对服务端的接口才需要做这个检测
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class CheckIp
{
    public function __construct(
        // 读取IP配置的环境变量Key
        public readonly string $envKey = '',
    ) {
    }
}
