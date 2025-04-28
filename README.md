# JsonRPC Check IP Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/json-rpc-check-ip-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/json-rpc-check-ip-bundle)
[![Build Status](https://img.shields.io/travis/tourze/json-rpc-check-ip-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/json-rpc-check-ip-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/json-rpc-check-ip-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/json-rpc-check-ip-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/json-rpc-check-ip-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/json-rpc-check-ip-bundle)

A Symfony bundle that provides IP checking for JsonRPC server-to-server interfaces. By annotating your service classes, you can restrict access to specific IP ranges using environment variable configuration.

## Features

- Restrict JsonRPC method access by client IP
- Simple attribute-based configuration (`CheckIp` Attribute)
- Environment variable driven IP whitelist
- Easy integration with Symfony and JsonRPC-Core

## Installation

```bash
composer require tourze/json-rpc-check-ip-bundle
```

## Quick Start

1. **Configure allowed IPs** in your environment variables (e.g., `.env`):

```env
ALLOWED_RPC_IPS=127.0.0.1,192.168.1.0/24
```

2. **Annotate your service class** with the `CheckIp` attribute:

```php
use Tourze\JsonRPCCheckIPBundle\Attribute\CheckIp;

#[CheckIp(envKey: 'ALLOWED_RPC_IPS')]
class MyRpcService
{
    // ...
}
```

When a JsonRPC call is made, the bundle will check if the client's IP is within the allowed range. If not, an exception is thrown.

## Configuration

- `envKey`: The environment variable key used to read the allowed IP list (comma separated, supports CIDR).

## Advanced

- Integrates with Symfony's `RequestStack` and `IpUtils` for robust IP matching.
- Only methods/classes annotated with `CheckIp` are protected.
- If no IPs are configured, all requests are allowed by default.

## Contributing

Contributions are welcome! Please submit issues or pull requests. Make sure your code passes tests and follows the project's coding standards.

## License

This bundle is open-sourced software licensed under the [MIT license](LICENSE).

## Changelog

See [CHANGELOG](CHANGELOG.md) for version history and upgrade notes.
