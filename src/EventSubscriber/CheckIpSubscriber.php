<?php

namespace Tourze\JsonRPCCheckIPBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Tourze\DoctrineHelper\ReflectionHelper;
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
        foreach (ReflectionHelper::getClassAttributes(
            ReflectionHelper::getClassReflection($event->getMethod()),
            CheckIp::class,
        ) as $attribute) {
            if (empty($attribute->envKey)) {
                return;
            }
            $this->checkIp($this->requestStack->getMainRequest(), $attribute);
        }
    }

    private function checkIp(Request $request, CheckIp $checkIp): void
    {
        if (empty($checkIp->envKey)) {
            return;
        }

        $envIps = $_ENV[$checkIp->envKey] ?? '';
        if (empty($envIps)) {
            // 没配置的话，我们跳过吧，当做默认放行了
            return;
        }

        $envIps = explode(',', (string) $envIps);

        $currentIp = $request->getClientIp();
        $in = IpUtils::checkIp($currentIp, $envIps);
        if (!$in) {
            throw new ApiException('IP不合法，请检查网络环境');
        }
    }
}
