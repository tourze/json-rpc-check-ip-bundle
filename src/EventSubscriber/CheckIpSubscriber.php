<?php

declare(strict_types=1);

namespace Tourze\JsonRPCCheckIPBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Tourze\JsonRPC\Core\Event\BeforeMethodApplyEvent;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCCheckIPBundle\Attribute\CheckIp;

/**
 * 读取主请求的IP并判断是否符合要求
 */
class CheckIpSubscriber
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    #[AsEventListener]
    public function beforeMethodApply(BeforeMethodApplyEvent $event): void
    {
        $method = $event->getMethod();
        $reflectionClass = new \ReflectionClass($method);

        // 检查类上的属性
        foreach ($reflectionClass->getAttributes(CheckIp::class) as $attributeReflection) {
            $attribute = $attributeReflection->newInstance();
            if ('' === $attribute->envKey) {
                return;
            }
            $request = $this->requestStack->getMainRequest();
            if (null === $request) {
                throw new ApiException('无法获取请求对象');
            }
            $this->checkIp($request, $attribute);
        }
    }

    private function checkIp(Request $request, CheckIp $checkIp): void
    {
        if ('' === $checkIp->envKey) {
            return;
        }

        $envIps = $_ENV[$checkIp->envKey] ?? '';
        if ('' === $envIps) {
            // 没配置的话，我们跳过吧，当做默认放行了
            return;
        }

        $envIps = explode(',', (string) $envIps);

        $currentIp = $request->getClientIp();
        if (null === $currentIp) {
            throw new ApiException('无法获取客户端IP地址');
        }
        $in = IpUtils::checkIp($currentIp, $envIps);
        if (!$in) {
            throw new ApiException('IP不合法，请检查网络环境');
        }
    }
}
