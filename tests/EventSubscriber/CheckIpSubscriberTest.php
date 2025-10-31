<?php

namespace Tourze\JsonRPCCheckIPBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Tourze\JsonRPC\Core\Domain\JsonRpcMethodInterface;
use Tourze\JsonRPC\Core\Event\BeforeMethodApplyEvent;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCCheckIPBundle\Attribute\CheckIp;
use Tourze\JsonRPCCheckIPBundle\EventSubscriber\CheckIpSubscriber;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(CheckIpSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class CheckIpSubscriberTest extends AbstractEventSubscriberTestCase
{
    private RequestStack $requestStack;

    private Request $request;

    private CheckIpSubscriber $subscriber;

    /** @var array<array-key, mixed> */
    private array $envBackup;

    private \ReflectionMethod $checkIpMethod;

    protected function onSetUp(): void
    {
        // 备份环境变量
        $this->envBackup = $_ENV;

        // 获取服务实例
        $container = self::getContainer();

        /** @var CheckIpSubscriber $subscriber */
        $subscriber = $container->get(CheckIpSubscriber::class);
        /** @var RequestStack $requestStack */
        $requestStack = $container->get(RequestStack::class);

        $this->subscriber = $subscriber;
        $this->requestStack = $requestStack;

        // 创建真实的Request对象
        $this->request = Request::create('/', 'GET');

        // 使用反射获取私有方法
        $reflectionClass = new \ReflectionClass(CheckIpSubscriber::class);
        $this->checkIpMethod = $reflectionClass->getMethod('checkIp');
        $this->checkIpMethod->setAccessible(true);
    }

    protected function onTearDown(): void
    {
        // 恢复环境变量
        $_ENV = $this->envBackup;
    }

    /**
     * 测试 envKey 为空的情况
     */
    public function testCheckIpWithEmptyEnvKey(): void
    {
        $checkIp = new CheckIp('');

        // 调用私有方法，验证不抛出异常
        $this->expectNotToPerformAssertions();
        $this->checkIpMethod->invoke($this->subscriber, $this->request, $checkIp);
    }

    /**
     * 测试环境变量未设置的情况
     */
    public function testCheckIpWithNoEnvValue(): void
    {
        $checkIp = new CheckIp('TEST_IP_ENV');
        unset($_ENV['TEST_IP_ENV']);

        // 调用私有方法，验证不抛出异常
        $this->expectNotToPerformAssertions();
        $this->checkIpMethod->invoke($this->subscriber, $this->request, $checkIp);
    }

    /**
     * 测试环境变量值为空的情况
     */
    public function testCheckIpWithEmptyEnvValue(): void
    {
        $checkIp = new CheckIp('TEST_IP_ENV');
        $_ENV['TEST_IP_ENV'] = '';

        // 调用私有方法，验证不抛出异常
        $this->expectNotToPerformAssertions();
        $this->checkIpMethod->invoke($this->subscriber, $this->request, $checkIp);
    }

    /**
     * 测试IP在白名单中的情况
     */
    public function testCheckIpWithAllowedIp(): void
    {
        $checkIp = new CheckIp('TEST_IP_ENV');
        $_ENV['TEST_IP_ENV'] = '127.0.0.1,192.168.1.1';

        // 设置请求的IP地址
        $this->request->server->set('REMOTE_ADDR', '127.0.0.1');

        // 调用私有方法，验证不抛出异常
        $this->expectNotToPerformAssertions();
        $this->checkIpMethod->invoke($this->subscriber, $this->request, $checkIp);
    }

    /**
     * 测试IP不在白名单中的情况
     */
    public function testCheckIpWithDisallowedIp(): void
    {
        $checkIp = new CheckIp('TEST_IP_ENV');
        $_ENV['TEST_IP_ENV'] = '127.0.0.1,192.168.1.1';

        // 设置请求的IP地址（不在白名单中）
        $this->request->server->set('REMOTE_ADDR', '10.0.0.1');

        // 验证异常抛出
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('IP不合法，请检查网络环境');

        // 调用私有方法
        $this->checkIpMethod->invoke($this->subscriber, $this->request, $checkIp);
    }

    /**
     * 测试使用CIDR表示法的IP范围
     */
    public function testCheckIpWithCIDRNotation(): void
    {
        $checkIp = new CheckIp('TEST_IP_ENV');
        $_ENV['TEST_IP_ENV'] = '192.168.1.0/24';

        // 设置请求的IP地址（在CIDR范围内）
        $this->request->server->set('REMOTE_ADDR', '192.168.1.100');

        // 调用私有方法，验证不抛出异常
        $this->expectNotToPerformAssertions();
        $this->checkIpMethod->invoke($this->subscriber, $this->request, $checkIp);
    }

    /**
     * 测试使用CIDR表示法且IP不在范围内的情况
     */
    public function testCheckIpWithCIDRNotationIpNotInRange(): void
    {
        $checkIp = new CheckIp('TEST_IP_ENV');
        $_ENV['TEST_IP_ENV'] = '192.168.1.0/24';

        // 设置请求的IP地址（不在CIDR范围内）
        $this->request->server->set('REMOTE_ADDR', '192.168.2.100');

        // 验证异常抛出
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('IP不合法，请检查网络环境');

        // 调用私有方法
        $this->checkIpMethod->invoke($this->subscriber, $this->request, $checkIp);
    }

    /**
     * 测试多个IP地址和CIDR混合的情况
     */
    public function testCheckIpWithMixedIPTypes(): void
    {
        $checkIp = new CheckIp('TEST_IP_ENV');
        $_ENV['TEST_IP_ENV'] = '127.0.0.1,192.168.1.0/24,10.0.0.5';

        // 设置请求的IP地址（在混合列表中）
        $this->request->server->set('REMOTE_ADDR', '10.0.0.5');

        // 调用私有方法，验证不抛出异常
        $this->expectNotToPerformAssertions();
        $this->checkIpMethod->invoke($this->subscriber, $this->request, $checkIp);
    }

    /**
     * 测试订阅者是否正确注册
     */
    public function testSubscriberIsRegistered(): void
    {
        $this->assertInstanceOf(CheckIpSubscriber::class, $this->subscriber);

        $reflection = new \ReflectionClass($this->subscriber);
        $method = $reflection->getMethod('beforeMethodApply');
        $attributes = $method->getAttributes(AsEventListener::class);

        $this->assertNotEmpty($attributes, 'beforeMethodApply method should have AsEventListener attribute');
    }

    /**
     * 测试beforeMethodApply方法的核心逻辑
     */
    public function testBeforeMethodApply(): void
    {
        // 创建请求并设置IP
        $request = Request::create('/', 'GET');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');
        $this->requestStack->push($request);

        // 设置环境变量允许该IP
        $_ENV['TEST_IP_ENV'] = '127.0.0.1';

        // 创建包含CheckIp属性的模拟方法类
        $mockMethod = new #[CheckIp(envKey: 'TEST_IP_ENV')] class implements JsonRpcMethodInterface {
            public function __invoke(JsonRpcRequest $request): mixed
            {
                return null;
            }

            /** @return array<string, mixed> */
            public function execute(): array
            {
                return [];
            }
        };

        // 创建真实的事件对象
        $event = new BeforeMethodApplyEvent();
        $event->setMethod($mockMethod);

        // 调用beforeMethodApply方法，验证不抛出异常
        $this->subscriber->beforeMethodApply($event);

        // 验证方法执行后事件对象仍然包含正确的方法
        $this->assertSame($mockMethod, $event->getMethod());
    }

    /**
     * 测试beforeMethodApply方法当IP不在白名单时抛出异常
     */
    public function testBeforeMethodApplyWithDisallowedIp(): void
    {
        // 创建请求并设置不允许的IP
        $request = Request::create('/', 'GET');
        $request->server->set('REMOTE_ADDR', '10.0.0.1');
        $this->requestStack->push($request);

        // 设置环境变量，不包含当前IP
        $_ENV['TEST_IP_ENV'] = '127.0.0.1';

        // 创建包含CheckIp属性的模拟方法类
        $mockMethod = new #[CheckIp(envKey: 'TEST_IP_ENV')] class implements JsonRpcMethodInterface {
            public function __invoke(JsonRpcRequest $request): mixed
            {
                return null;
            }

            /** @return array<string, mixed> */
            public function execute(): array
            {
                return [];
            }
        };

        // 创建真实的事件对象
        $event = new BeforeMethodApplyEvent();
        $event->setMethod($mockMethod);

        // 验证异常抛出
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('IP不合法，请检查网络环境');

        // 调用beforeMethodApply方法
        $this->subscriber->beforeMethodApply($event);
    }

    /**
     * 测试beforeMethodApply方法当没有CheckIp属性时正常执行
     */
    public function testBeforeMethodApplyWithoutCheckIpAttribute(): void
    {
        // 创建请求
        $request = Request::create('/', 'GET');
        $this->requestStack->push($request);

        // 创建没有CheckIp属性的模拟方法类
        $mockMethod = new class implements JsonRpcMethodInterface {
            public function __invoke(JsonRpcRequest $request): mixed
            {
                return null;
            }

            /** @return array<string, mixed> */
            public function execute(): array
            {
                return [];
            }
        };

        // 创建真实的事件对象
        $event = new BeforeMethodApplyEvent();
        $event->setMethod($mockMethod);

        // 调用beforeMethodApply方法，验证不抛出异常
        $this->subscriber->beforeMethodApply($event);

        // 验证方法执行后事件对象仍然包含正确的方法
        $this->assertSame($mockMethod, $event->getMethod());
    }
}
