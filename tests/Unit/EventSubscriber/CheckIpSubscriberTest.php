<?php

namespace Tourze\JsonRPCCheckIPBundle\Tests\Unit\EventSubscriber;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCCheckIPBundle\Attribute\CheckIp;
use Tourze\JsonRPCCheckIPBundle\EventSubscriber\CheckIpSubscriber;

class CheckIpSubscriberTest extends TestCase
{
    private RequestStack|MockObject $requestStack;
    private Request|MockObject $request;
    private CheckIpSubscriber $subscriber;
    private array $envBackup;
    private ReflectionMethod $checkIpMethod;

    protected function setUp(): void
    {
        $this->request = $this->createMock(Request::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->requestStack->method('getMainRequest')->willReturn($this->request);
        $this->subscriber = new CheckIpSubscriber($this->requestStack);

        // 备份环境变量
        $this->envBackup = $_ENV;

        // 使用反射获取私有方法
        $reflectionClass = new ReflectionClass(CheckIpSubscriber::class);
        $this->checkIpMethod = $reflectionClass->getMethod('checkIp');
        $this->checkIpMethod->setAccessible(true);
    }

    protected function tearDown(): void
    {
        // 恢复环境变量
        $_ENV = $this->envBackup;
    }

    /**
     * 测试 envKey 为空的情况
     */
    public function testCheckIp_withEmptyEnvKey(): void
    {
        $checkIp = new CheckIp('');

        // 调用私有方法
        $this->checkIpMethod->invoke($this->subscriber, $this->request, $checkIp);

        // 如果没有异常抛出，测试通过
        $this->assertTrue(true);
    }

    /**
     * 测试环境变量未设置的情况
     */
    public function testCheckIp_withNoEnvValue(): void
    {
        $checkIp = new CheckIp('TEST_IP_ENV');
        unset($_ENV['TEST_IP_ENV']);

        // 调用私有方法
        $this->checkIpMethod->invoke($this->subscriber, $this->request, $checkIp);

        // 如果没有异常抛出，测试通过
        $this->assertTrue(true);
    }

    /**
     * 测试环境变量值为空的情况
     */
    public function testCheckIp_withEmptyEnvValue(): void
    {
        $checkIp = new CheckIp('TEST_IP_ENV');
        $_ENV['TEST_IP_ENV'] = '';

        // 调用私有方法
        $this->checkIpMethod->invoke($this->subscriber, $this->request, $checkIp);

        // 如果没有异常抛出，测试通过
        $this->assertTrue(true);
    }

    /**
     * 测试IP在白名单中的情况
     */
    public function testCheckIp_withAllowedIp(): void
    {
        $checkIp = new CheckIp('TEST_IP_ENV');
        $_ENV['TEST_IP_ENV'] = '127.0.0.1,192.168.1.1';
        $this->request->method('getClientIp')->willReturn('127.0.0.1');

        // 调用私有方法
        $this->checkIpMethod->invoke($this->subscriber, $this->request, $checkIp);

        // 如果没有异常抛出，测试通过
        $this->assertTrue(true);
    }

    /**
     * 测试IP不在白名单中的情况
     */
    public function testCheckIp_withDisallowedIp(): void
    {
        $checkIp = new CheckIp('TEST_IP_ENV');
        $_ENV['TEST_IP_ENV'] = '127.0.0.1,192.168.1.1';
        $this->request->method('getClientIp')->willReturn('10.0.0.1');

        // 验证异常抛出
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('IP不合法，请检查网络环境');

        // 调用私有方法
        $this->checkIpMethod->invoke($this->subscriber, $this->request, $checkIp);
    }

    /**
     * 测试使用CIDR表示法的IP范围
     */
    public function testCheckIp_withCIDRNotation(): void
    {
        $checkIp = new CheckIp('TEST_IP_ENV');
        $_ENV['TEST_IP_ENV'] = '192.168.1.0/24';
        $this->request->method('getClientIp')->willReturn('192.168.1.100');

        // 调用私有方法
        $this->checkIpMethod->invoke($this->subscriber, $this->request, $checkIp);

        // 如果没有异常抛出，测试通过
        $this->assertTrue(true);
    }

    /**
     * 测试使用CIDR表示法且IP不在范围内的情况
     */
    public function testCheckIp_withCIDRNotation_ipNotInRange(): void
    {
        $checkIp = new CheckIp('TEST_IP_ENV');
        $_ENV['TEST_IP_ENV'] = '192.168.1.0/24';
        $this->request->method('getClientIp')->willReturn('192.168.2.100');

        // 验证异常抛出
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('IP不合法，请检查网络环境');

        // 调用私有方法
        $this->checkIpMethod->invoke($this->subscriber, $this->request, $checkIp);
    }

    /**
     * 测试多个IP地址和CIDR混合的情况
     */
    public function testCheckIp_withMixedIPTypes(): void
    {
        $checkIp = new CheckIp('TEST_IP_ENV');
        $_ENV['TEST_IP_ENV'] = '127.0.0.1,192.168.1.0/24,10.0.0.5';
        $this->request->method('getClientIp')->willReturn('10.0.0.5');

        // 调用私有方法
        $this->checkIpMethod->invoke($this->subscriber, $this->request, $checkIp);

        // 如果没有异常抛出，测试通过
        $this->assertTrue(true);
    }
}
