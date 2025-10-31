<?php

declare(strict_types=1);

namespace Tourze\JsonRPCCheckIPBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPCCheckIPBundle\JsonRPCCheckIPBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(JsonRPCCheckIPBundle::class)]
#[RunTestsInSeparateProcesses]
final class JsonRPCCheckIPBundleTest extends AbstractBundleTestCase
{
}
