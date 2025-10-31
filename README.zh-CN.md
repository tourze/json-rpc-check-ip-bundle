# JsonRPC 检查IP组件

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/json-rpc-check-ip-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/json-rpc-check-ip-bundle)
[![Build Status](https://img.shields.io/travis/tourze/json-rpc-check-ip-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/json-rpc-check-ip-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/json-rpc-check-ip-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/json-rpc-check-ip-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/json-rpc-check-ip-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/json-rpc-check-ip-bundle)
[![PHP Version Require](https://img.shields.io/packagist/php-v/tourze/json-rpc-check-ip-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/json-rpc-check-ip-bundle)
[![License](https://img.shields.io/packagist/l/tourze/json-rpc-check-ip-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/json-rpc-check-ip-bundle)
[![Coverage Status](https://img.shields.io/coveralls/tourze/json-rpc-check-ip-bundle/master.svg?style=flat-square)](https://coveralls.io/github/tourze/json-rpc-check-ip-bundle?branch=master)

一个用于 JsonRPC 服务端接口的 IP 访问控制 Symfony 组件。通过在服务类上添加 `CheckIp` 属性注解，可以基于环境变量配置，限制接口仅允许特定 IP 段访问。

## 功能特性

- **IP 访问控制**：按客户端 IP 地址限制 JsonRPC 方法访问
- **属性注解配置**：简单的 `#[CheckIp]` 注解即可保护服务类
- **环境变量驱动**：通过环境变量配置允许的 IP 列表
- **CIDR 支持**：支持单个 IP 和 CIDR 网段记法（如 `192.168.1.0/24`）
- **Symfony 集成**：与 Symfony 的 RequestStack 和 IpUtils 无缝集成
- **优雅降级**：如果未配置 IP，则默认允许所有请求

## 安装说明

```bash
composer require tourze/json-rpc-check-ip-bundle
```

## 快速开始

1. **在环境变量中配置允许的 IP**（如 `.env` 文件）：

```env
# 允许特定 IP 和 CIDR 网段
ALLOWED_RPC_IPS=127.0.0.1,192.168.1.0/24,10.0.0.100
```

2. **在 JsonRPC 服务类上添加 `CheckIp` 属性注解**：

```php
<?php

use Tourze\JsonRPCCheckIPBundle\Attribute\CheckIp;

#[CheckIp(envKey: 'ALLOWED_RPC_IPS')]
class MyRpcService
{
    public function getUserInfo(int $userId): array
    {
        // 此方法现在受到 IP 检查保护
        return ['id' => $userId, 'name' => 'John Doe'];
    }
}
```

3. **注册组件**到您的 Symfony 应用程序（如果不使用 Flex）：

```php
// config/bundles.php
return [
    // ... 其他组件
    Tourze\JsonRPCCheckIPBundle\JsonRPCCheckIPBundle::class => ['all' => true],
];
```

当对带注解的服务进行 JsonRPC 调用时，组件将：
- 从请求中提取客户端 IP
- 检查是否匹配配置的允许 IP/网段
- 如果 IP 不被允许，则抛出 `ApiException`
- 如果 IP 有效，则允许请求继续

## 配置说明

### CheckIp 属性

`CheckIp` 属性支持以下参数：

- `envKey` (string)：用于读取允许 IP 列表的环境变量名。IP 应该用逗号分隔，可以包含 CIDR 记法。

### 环境变量

在环境变量中配置您的允许 IP：

```env
# 单个 IP
ADMIN_IPS=127.0.0.1

# 多个 IP
API_SERVER_IPS=10.0.0.1,10.0.0.2,10.0.0.3

# CIDR 网段
INTERNAL_NETWORK=192.168.0.0/16,10.0.0.0/8

# 混合格式
ALLOWED_SOURCES=127.0.0.1,192.168.1.0/24,10.0.0.100
```

## 高级用法

### 多个 IP 配置

您可以为不同的服务使用不同的 IP 配置：

```php
#[CheckIp(envKey: 'ADMIN_IPS')]
class AdminService
{
    // 仅可从管理员 IP 访问
}

#[CheckIp(envKey: 'API_SERVER_IPS')]
class ApiService
{
    // 仅可从 API 服务器 IP 访问
}
```

### 错误处理

当 IP 不被允许时，组件会抛出 `ApiException`：

```php
try {
    $result = $rpcService->someMethod();
} catch (ApiException $e) {
    // 处理 IP 拒绝："IP不合法，请检查网络环境"
    logger->error('IP access denied', ['ip' => $request->getClientIp()]);
}
```

### 安全考虑

- 组件使用 Symfony 的 `IpUtils::checkIp()` 进行安全的 IP 匹配
- 仅对带有 `CheckIp` 注解的类/方法进行保护
- 如果环境变量为空或未设置，则默认允许所有请求
- 组件通过 Symfony 的可信代理配置与代理服务器和负载均衡器配合工作

## 贡献指南

欢迎贡献！请：

1. Fork 仓库
2. 创建功能分支
3. 为您的更改编写测试
4. 确保所有测试通过：`./vendor/bin/phpunit`
5. 运行静态分析：`./vendor/bin/phpstan analyse`
6. 提交 Pull Request

## 版权和许可

本组件遵循 [MIT 协议](LICENSE) 开源。

## 更新日志

详见 [CHANGELOG.md](CHANGELOG.md) 获取版本历史与升级说明。
