# Symfony Request File Clean Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/symfony-request-file-clean-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-request-file-clean-bundle)
[![License](https://img.shields.io/github/license/tourze/symfony-request-file-clean-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-request-file-clean-bundle)

一个 Symfony 扩展包，用于在特殊环境中自动清理请求完成后的临时上传文件，解决系统不会自动删除这些文件的问题。

## 功能特性

- 请求完成后自动删除临时上传文件
- 同时支持数组形式的文件上传和 UploadedFile 对象
- 高优先级事件监听器确保在所有其他进程之后进行清理
- 零配置，开箱即用

## 安装

使用 Composer 安装此扩展包：

```bash
composer require tourze/symfony-request-file-clean-bundle
```

## 快速开始

无需任何配置。该扩展包会自动注册事件订阅器，在请求终止后处理文件清理。

如果您使用的是 Symfony Flex，扩展包将自动注册。如果不是，则需要将其添加到您的 `config/bundles.php` 文件中：

```php
<?php

return [
    // ...
    Tourze\RequestFileCleanBundle\RequestFileCleanBundle::class => ['all' => true],
];
```

## 工作原理

该扩展包注册了一个事件订阅器，监听 `KernelEvents::TERMINATE` 事件，并设置了非常低的优先级 (-9999)。当请求终止时，它会遍历请求中的所有上传文件，并从文件系统中正确删除临时文件。

这有助于防止在临时文件不会被系统自动清理的环境中造成文件系统臃肿。

## 系统要求

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本

## 贡献

欢迎贡献！请随时提交 Pull Request。

## 许可证

此扩展包基于 MIT 许可证发布。有关更多信息，请参阅 [License File](LICENSE)。
