<?php

namespace Tourze\RequestFileCleanBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\RequestFileCleanBundle\RequestFileCleanBundle;

class RequestFileCleanBundleTest extends TestCase
{
    /**
     * 测试Bundle类是否正确继承了Symfony的Bundle基类
     */
    public function testBundleInstance(): void
    {
        $bundle = new RequestFileCleanBundle();

        $this->assertInstanceOf(Bundle::class, $bundle);
    }
}
