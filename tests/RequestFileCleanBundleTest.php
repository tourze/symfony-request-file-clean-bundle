<?php

declare(strict_types=1);

namespace Tourze\RequestFileCleanBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use Tourze\RequestFileCleanBundle\RequestFileCleanBundle;

/**
 * @internal
 */
#[CoversClass(RequestFileCleanBundle::class)]
#[RunTestsInSeparateProcesses]
final class RequestFileCleanBundleTest extends AbstractBundleTestCase
{
}
