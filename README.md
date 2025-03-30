# Symfony Request File Clean Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/symfony-request-file-clean-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-request-file-clean-bundle)
[![License](https://img.shields.io/github/license/tourze/symfony-request-file-clean-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-request-file-clean-bundle)

A Symfony bundle that automatically cleans up temporary uploaded files after request completion in special environments where the system doesn't automatically remove them.

## Features

- Automatically removes temporary uploaded files after request completion
- Works with both array-style file uploads and UploadedFile objects
- High priority event listener ensures cleanup happens after all other processes
- Zero configuration required

## Installation

Install the bundle using Composer:

```bash
composer require tourze/symfony-request-file-clean-bundle
```

## Quick Start

There's no configuration required. The bundle automatically registers the event subscriber that handles the file cleanup after request termination.

If you're using Symfony Flex, the bundle will be automatically registered. If not, you need to add it to your `config/bundles.php`:

```php
<?php

return [
    // ...
    Tourze\RequestFileCleanBundle\RequestFileCleanBundle::class => ['all' => true],
];
```

## How It Works

The bundle registers an event subscriber that listens to the `KernelEvents::TERMINATE` event with a very low priority (-9999). When the request terminates, it iterates through all uploaded files in the request and properly removes the temporary files from the filesystem.

This helps prevent file system bloat in environments where temporary files aren't automatically cleaned up by the system.

## Requirements

- PHP 8.1 or higher
- Symfony 6.4 or higher

## Contributing

Contributions are welcome! Feel free to submit a Pull Request.

## License

This bundle is released under the MIT License. See the [License File](LICENSE) for more information.
