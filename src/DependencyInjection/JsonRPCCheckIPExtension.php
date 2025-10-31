<?php

declare(strict_types=1);

namespace Tourze\JsonRPCCheckIPBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class JsonRPCCheckIPExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
