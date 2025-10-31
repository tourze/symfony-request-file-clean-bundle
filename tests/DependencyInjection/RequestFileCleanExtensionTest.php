<?php

namespace Tourze\RequestFileCleanBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use Tourze\RequestFileCleanBundle\DependencyInjection\RequestFileCleanExtension;
use Tourze\RequestFileCleanBundle\EventSubscriber\RequestFileCleanSubscriber;
use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

/**
 * @internal
 */
#[CoversClass(RequestFileCleanExtension::class)]
final class RequestFileCleanExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private RequestFileCleanExtension $extension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = new RequestFileCleanExtension();
    }

    public function testExtensionExtendsSymfonyExtension(): void
    {
        $this->assertInstanceOf(AutoExtension::class, $this->extension);
    }

    public function testExtensionIsInstantiable(): void
    {
        $this->assertInstanceOf(RequestFileCleanExtension::class, $this->extension);
    }

    public function testLoadDoesNotThrowException(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');

        $this->expectNotToPerformAssertions();
        $this->extension->load([], $container);
    }

    public function testServiceIsRegistered(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');

        $this->extension->load([], $container);

        $this->assertTrue($container->hasDefinition(RequestFileCleanSubscriber::class));
        $definition = $container->getDefinition(RequestFileCleanSubscriber::class);
        $this->assertTrue($definition->isAutowired());
        $this->assertTrue($definition->isAutoconfigured());
        $this->assertTrue($definition->isPublic());
    }

    public function testGetAliasReturnsCorrectAlias(): void
    {
        $this->assertSame('request_file_clean', $this->extension->getAlias());
    }
}
