<?php

namespace Tourze\RequestFileCleanBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\RequestFileCleanBundle\DependencyInjection\RequestFileCleanExtension;
use Tourze\RequestFileCleanBundle\EventSubscriber\RequestFileCleanSubscriber;

class RequestFileCleanExtensionTest extends TestCase
{
    /**
     * 测试扩展是否正确注册服务
     */
    public function testLoad(): void
    {
        $container = new ContainerBuilder();
        $extension = new RequestFileCleanExtension();

        $extension->load([], $container);

        // 验证订阅者服务已注册
        $this->assertTrue($container->has(RequestFileCleanSubscriber::class));

        // 验证订阅者服务具有正确的参数
        $definition = $container->getDefinition(RequestFileCleanSubscriber::class);
        $this->assertTrue($definition->isAutowired());
        $this->assertTrue($definition->isAutoconfigured());
        $this->assertFalse($definition->isPublic());
    }
}
