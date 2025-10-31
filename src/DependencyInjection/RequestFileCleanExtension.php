<?php

declare(strict_types=1);

namespace Tourze\RequestFileCleanBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class RequestFileCleanExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
