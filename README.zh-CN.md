# JsonRPC 检查IP组件

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/json-rpc-check-ip-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/json-rpc-check-ip-bundle)
[![Build Status](https://img.shields.io/travis/tourze/json-rpc-check-ip-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/json-rpc-check-ip-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/json-rpc-check-ip-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/json-rpc-check-ip-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/json-rpc-check-ip-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/json-rpc-check-ip-bundle)

一个用于 JsonRPC 服务端接口的 IP 检查 Symfony 组件。通过属性注解方式，可以基于环境变量配置，限制接口仅允许特定 IP 段访问。

## 功能特性

- 按客户端 IP 限制 JsonRPC 方法访问
- 简单易用的 `CheckIp` 属性注解
- 支持通过环境变量配置允许的 IP 白名单
- 与 Symfony、JsonRPC-Core 无缝集成

## 安装说明

```bash
composer require tourze/json-rpc-check-ip-bundle
```

## 快速开始

1. **在环境变量中配置允许的 IP**（如 `.env` 文件）：

```env
ALLOWED_RPC_IPS=127.0.0.1,192.168.1.0/24
```

2. **在服务类上加上 `CheckIp` 属性注解：**

```php
use Tourze\JsonRPCCheckIPBundle\Attribute\CheckIp;

#[CheckIp(envKey: 'ALLOWED_RPC_IPS')]
class MyRpcService
{
    // ...
}
```

当有 JsonRPC 调用时，组件会校验客户端 IP 是否在允许范围内，否则会抛出异常。

## 配置说明

- `envKey`：用于读取允许 IP 列表的环境变量名（逗号分隔，支持 CIDR）。

## 高级特性

- 基于 Symfony 的 `RequestStack` 和 `IpUtils`，IP 匹配更安全可靠
- 仅对加了 `CheckIp` 注解的类/方法生效
- 若未配置 IP，则默认全部放行

## 贡献指南

欢迎提交 Issue 或 PR，贡献代码请确保通过测试并遵循项目代码规范。

## 版权和许可

本组件遵循 [MIT 协议](LICENSE) 开源。

## 更新日志

详见 [CHANGELOG](CHANGELOG.md) 获取版本历史与升级说明。
