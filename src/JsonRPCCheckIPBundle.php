<?php

declare(strict_types=1);

namespace Tourze\JsonRPCCheckIPBundle;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class JsonRPCCheckIPBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            FrameworkBundle::class => ['all' => true],
        ];
    }
}
