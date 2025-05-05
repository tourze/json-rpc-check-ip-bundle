<?php

namespace Tourze\JsonRPCCheckIPBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Tourze\JsonRPCCheckIPBundle\JsonRPCCheckIPBundle;

class IntegrationTestKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new JsonRPCCheckIPBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config.yaml');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/json_rpc_check_ip_bundle/cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/json_rpc_check_ip_bundle/log';
    }
}
