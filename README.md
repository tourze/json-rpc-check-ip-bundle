# JsonRPC Check IP Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/json-rpc-check-ip-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/json-rpc-check-ip-bundle)
[![Build Status](https://img.shields.io/travis/tourze/json-rpc-check-ip-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/json-rpc-check-ip-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/json-rpc-check-ip-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/json-rpc-check-ip-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/json-rpc-check-ip-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/json-rpc-check-ip-bundle)
[![PHP Version Require](https://img.shields.io/packagist/php-v/tourze/json-rpc-check-ip-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/json-rpc-check-ip-bundle)
[![License](https://img.shields.io/packagist/l/tourze/json-rpc-check-ip-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/json-rpc-check-ip-bundle)
[![Coverage Status](https://img.shields.io/coveralls/tourze/json-rpc-check-ip-bundle/master.svg?style=flat-square)](https://coveralls.io/github/tourze/json-rpc-check-ip-bundle?branch=master)

A Symfony bundle that provides IP-based access control for JsonRPC server-to-server interfaces. By annotating your service classes with the `CheckIp` attribute, you can restrict access to specific IP ranges using environment variables.

## Features

- **IP-based access control**: Restrict JsonRPC method access by client IP address
- **Attribute-based configuration**: Simple `#[CheckIp]` annotation on service classes
- **Environment variable driven**: Configure allowed IPs via environment variables
- **CIDR support**: Supports both individual IPs and CIDR notation (e.g., `192.168.1.0/24`)
- **Symfony integration**: Seamlessly integrates with Symfony's RequestStack and IpUtils
- **Graceful defaults**: If no IPs are configured, all requests are allowed by default

## Installation

```bash
composer require tourze/json-rpc-check-ip-bundle
```

## Quick Start

1. **Configure allowed IPs** in your environment variables (e.g., `.env`):

```env
# Allow specific IPs and CIDR ranges
ALLOWED_RPC_IPS=127.0.0.1,192.168.1.0/24,10.0.0.100
```

2. **Annotate your JsonRPC service class** with the `CheckIp` attribute:

```php
<?php

use Tourze\JsonRPCCheckIPBundle\Attribute\CheckIp;

#[CheckIp(envKey: 'ALLOWED_RPC_IPS')]
class MyRpcService
{
    public function getUserInfo(int $userId): array
    {
        // This method is now protected by IP checking
        return ['id' => $userId, 'name' => 'John Doe'];
    }
}
```

3. **Register the bundle** in your Symfony application (if not using Flex):

```php
// config/bundles.php
return [
    // ... other bundles
    Tourze\JsonRPCCheckIPBundle\JsonRPCCheckIPBundle::class => ['all' => true],
];
```

When a JsonRPC call is made to an annotated service, the bundle will:
- Extract the client IP from the request
- Check if it matches any of the configured allowed IPs/ranges
- Throw an `ApiException` if the IP is not allowed
- Allow the request to proceed if the IP is valid

## Configuration

### CheckIp Attribute

The `CheckIp` attribute supports the following parameters:

- `envKey` (string): The environment variable key used to read the allowed IP list. IPs should be comma-separated and can include CIDR notation.

### Environment Variables

Configure your allowed IPs in environment variables:

```env
# Single IP
ADMIN_IPS=127.0.0.1

# Multiple IPs
API_SERVER_IPS=10.0.0.1,10.0.0.2,10.0.0.3

# CIDR ranges
INTERNAL_NETWORK=192.168.0.0/16,10.0.0.0/8

# Mixed format
ALLOWED_SOURCES=127.0.0.1,192.168.1.0/24,10.0.0.100
```

## Advanced Usage

### Multiple IP Configurations

You can use different IP configurations for different services:

```php
#[CheckIp(envKey: 'ADMIN_IPS')]
class AdminService
{
    // Only accessible from admin IPs
}

#[CheckIp(envKey: 'API_SERVER_IPS')]
class ApiService
{
    // Only accessible from API server IPs
}
```

### Error Handling

When an IP is not allowed, the bundle throws an `ApiException`:

```php
try {
    $result = $rpcService->someMethod();
} catch (ApiException $e) {
    // Handle IP rejection: "IP不合法，请检查网络环境"
    logger->error('IP access denied', ['ip' => $request->getClientIp()]);
}
```

### Security Considerations

- The bundle uses Symfony's `IpUtils::checkIp()` for secure IP matching
- Only classes/methods annotated with `CheckIp` are protected
- If the environment variable is empty or not set, all requests are allowed by default
- The bundle works with proxy servers and load balancers through Symfony's trusted proxy configuration

## Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Write tests for your changes
4. Ensure all tests pass: `./vendor/bin/phpunit`
5. Run static analysis: `./vendor/bin/phpstan analyse`
6. Submit a pull request

## License

This bundle is open-sourced software licensed under the [MIT license](LICENSE).

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and upgrade notes.
