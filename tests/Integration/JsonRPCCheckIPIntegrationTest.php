<?php

namespace Tourze\JsonRPCCheckIPBundle\Tests\Integration;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Tourze\IntegrationTestKernel\IntegrationTestKernel;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCCheckIPBundle\Attribute\CheckIp;
use Tourze\JsonRPCCheckIPBundle\EventSubscriber\CheckIpSubscriber;
use Tourze\JsonRPCCheckIPBundle\JsonRPCCheckIPBundle;

/**
 * 由于底层库的限制，我们将集成测试转换为高级单元测试
 */
class JsonRPCCheckIPIntegrationTest extends TestCase
{
    private RequestStack $requestStack;
    private array $envBackup;

    protected function setUp(): void
    {
        $this->requestStack = new RequestStack();
        $this->envBackup = $_ENV;
    }

    protected function tearDown(): void
    {
        $_ENV = $this->envBackup;
    }

    /**
     * 测试IP在白名单中的情况
     */
    public function testCheckIp_withAllowedIp(): void
    {
        // 创建请求并设置客户端IP
        $request = Request::create('/', 'GET');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');
        // 模拟请求设置
        $this->requestStack->push($request);

        // 设置环境变量
        $_ENV['TEST_IP_ENV'] = '127.0.0.1';

        // 创建订阅器
        $subscriber = new CheckIpSubscriber($this->requestStack);

        // 使用反射直接测试checkIp方法
        $reflectionClass = new ReflectionClass(CheckIpSubscriber::class);
        $method = $reflectionClass->getMethod('checkIp');
        $method->setAccessible(true);

        // 调用私有方法
        $method->invoke($subscriber, $request, new CheckIp('TEST_IP_ENV'));

        // 如果没有异常抛出，测试通过
        $this->assertTrue(true);
    }

    /**
     * 测试IP不在白名单中的情况
     */
    public function testCheckIp_withDisallowedIp(): void
    {
        // 创建请求并设置客户端IP
        $request = Request::create('/', 'GET');
        $request->server->set('REMOTE_ADDR', '10.0.0.1');
        // 模拟请求设置
        $this->requestStack->push($request);

        // 设置环境变量
        $_ENV['TEST_IP_ENV'] = '127.0.0.1';

        // 创建订阅器
        $subscriber = new CheckIpSubscriber($this->requestStack);

        // 验证异常抛出
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('IP不合法，请检查网络环境');

        // 使用反射直接测试checkIp方法
        $reflectionClass = new ReflectionClass(CheckIpSubscriber::class);
        $method = $reflectionClass->getMethod('checkIp');
        $method->setAccessible(true);

        // 调用私有方法
        $method->invoke($subscriber, $request, new CheckIp('TEST_IP_ENV'));
    }

    protected static function getKernelClass(): string
    {
        return IntegrationTestKernel::class;
    }

    protected static function createKernel(array $options = []): IntegrationTestKernel
    {
        $appendBundles = [
            FrameworkBundle::class => ['all' => true],
            JsonRPCCheckIPBundle::class => ['all' => true],
        ];
        
        $entityMappings = [];

        return new IntegrationTestKernel(
            $options['environment'] ?? 'test',
            $options['debug'] ?? true,
            $appendBundles,
            $entityMappings
        );
    }
}
