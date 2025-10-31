<?php

namespace Tourze\RequestFileCleanBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;
use Tourze\RequestFileCleanBundle\EventSubscriber\RequestFileCleanSubscriber;

/**
 * @internal
 */
#[CoversClass(RequestFileCleanSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class RequestFileCleanSubscriberTest extends AbstractEventSubscriberTestCase
{
    protected function onSetUp(): void
    {
    }

    /**
     * 测试处理普通上传文件的情况
     */
    public function testOnTerminatedWithUploadedFile(): void
    {
        // 创建临时文件
        $tempFilePath = tempnam(sys_get_temp_dir(), 'test');
        $this->assertFileExists($tempFilePath);

        // 创建UploadedFile对象
        // 在这里必须使用具体的 UploadedFile 类进行 mock，因为：
        // 1. 测试需要验证 getPathname() 方法的具体行为
        // 2. UploadedFile 类没有对应的接口，这是 Symfony 框架的设计
        // 3. 模拟文件上传的真实场景必须使用具体的文件对象
        $uploadedFile = $this->createMock(UploadedFile::class);
        $uploadedFile->method('getPathname')->willReturn($tempFilePath);

        // 创建Request对象
        $request = new Request();
        $request->files = new FileBag(['test_file' => $uploadedFile]);

        // 创建Response对象
        $response = new Response();

        // 创建TerminateEvent对象
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new TerminateEvent($kernel, $request, $response);

        // 执行订阅器方法
        /** @var RequestFileCleanSubscriber $subscriber */
        $subscriber = self::getContainer()->get(RequestFileCleanSubscriber::class);
        $subscriber->onTerminated($event);

        // 验证文件已被删除
        $this->assertFileDoesNotExist($tempFilePath);
    }

    /**
     * 测试处理数组形式上传文件的情况
     */
    public function testOnTerminatedWithArrayItem(): void
    {
        // 创建临时文件
        $tempFilePath = tempnam(sys_get_temp_dir(), 'test');
        $this->assertFileExists($tempFilePath);

        // 创建Request对象和FileBag
        $request = new Request();
        $fileBag = new FileBag();

        // 使用反射来设置内部的parameters属性，模拟array形式的上传数据
        $reflection = new \ReflectionClass($fileBag);
        $parametersProperty = $reflection->getProperty('parameters');
        $parametersProperty->setAccessible(true);
        $parametersProperty->setValue($fileBag, [
            'test_file' => [
                'tmp_name' => $tempFilePath,
                'name' => 'test.txt',
                'type' => 'text/plain',
                'size' => 0,
                'error' => 0,
            ],
        ]);

        $request->files = $fileBag;

        // 创建Response对象
        $response = new Response();

        // 创建TerminateEvent对象
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new TerminateEvent($kernel, $request, $response);

        // 执行订阅器方法
        /** @var RequestFileCleanSubscriber $subscriber */
        $subscriber = self::getContainer()->get(RequestFileCleanSubscriber::class);
        $subscriber->onTerminated($event);

        // 验证文件已被删除
        $this->assertFileDoesNotExist($tempFilePath);
    }

    /**
     * 测试上传文件已不存在的情况
     */
    public function testOnTerminatedWithNonExistentFile(): void
    {
        // 创建一个不存在的文件路径
        $nonExistentPath = sys_get_temp_dir() . '/non_existent_' . uniqid();

        // 创建UploadedFile对象
        // 在这里必须使用具体的 UploadedFile 类进行 mock，因为：
        // 1. 测试需要验证 getPathname() 方法的具体行为
        // 2. UploadedFile 类没有对应的接口，这是 Symfony 框架的设计
        // 3. 模拟文件上传的真实场景必须使用具体的文件对象
        $uploadedFile = $this->createMock(UploadedFile::class);
        $uploadedFile->method('getPathname')->willReturn($nonExistentPath);

        // 创建Request对象
        $request = new Request();
        $request->files = new FileBag(['test_file' => $uploadedFile]);

        // 创建Response对象
        $response = new Response();

        // 创建TerminateEvent对象
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new TerminateEvent($kernel, $request, $response);

        // 执行订阅器方法 - 不应该抛出异常
        /** @var RequestFileCleanSubscriber $subscriber */
        $subscriber = self::getContainer()->get(RequestFileCleanSubscriber::class);
        $subscriber->onTerminated($event);

        // 验证不存在的文件依然不存在（测试方法处理不存在文件的鲁棒性）
        $this->assertFileDoesNotExist($nonExistentPath);
    }

    /**
     * 测试处理空的文件袋
     */
    public function testOnTerminatedWithEmptyFileBag(): void
    {
        // 创建Request对象
        $request = new Request();
        $request->files = new FileBag([]);

        // 创建Response对象
        $response = new Response();

        // 创建TerminateEvent对象
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new TerminateEvent($kernel, $request, $response);

        // 执行订阅器方法
        /** @var RequestFileCleanSubscriber $subscriber */
        $subscriber = self::getContainer()->get(RequestFileCleanSubscriber::class);
        $subscriber->onTerminated($event);

        // 验证空的 FileBag 处理正常，没有副作用
        $this->assertEmpty($request->files->all());
    }

    /**
     * 测试处理多个上传文件的情况
     */
    public function testOnTerminatedWithMultipleFiles(): void
    {
        // 创建多个临时文件
        $tempFilePath1 = tempnam(sys_get_temp_dir(), 'test1');
        $tempFilePath2 = tempnam(sys_get_temp_dir(), 'test2');
        $this->assertFileExists($tempFilePath1);
        $this->assertFileExists($tempFilePath2);

        // 创建UploadedFile对象
        // 在这里必须使用具体的 UploadedFile 类进行 mock，因为：
        // 1. 测试需要验证 getPathname() 方法的具体行为
        // 2. UploadedFile 类没有对应的接口，这是 Symfony 框架的设计
        // 3. 模拟文件上传的真实场景必须使用具体的文件对象
        $uploadedFile1 = $this->createMock(UploadedFile::class);
        $uploadedFile1->method('getPathname')->willReturn($tempFilePath1);

        // 在这里必须使用具体的 UploadedFile 类进行 mock，因为：
        // 1. 测试需要验证 getPathname() 方法的具体行为
        // 2. UploadedFile 类没有对应的接口，这是 Symfony 框架的设计
        // 3. 模拟文件上传的真实场景必须使用具体的文件对象
        $uploadedFile2 = $this->createMock(UploadedFile::class);
        $uploadedFile2->method('getPathname')->willReturn($tempFilePath2);

        // 创建Request对象
        $request = new Request();
        $request->files = new FileBag([
            'test_file1' => $uploadedFile1,
            'test_file2' => $uploadedFile2,
        ]);

        // 创建Response对象
        $response = new Response();

        // 创建TerminateEvent对象
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new TerminateEvent($kernel, $request, $response);

        // 执行订阅器方法
        /** @var RequestFileCleanSubscriber $subscriber */
        $subscriber = self::getContainer()->get(RequestFileCleanSubscriber::class);
        $subscriber->onTerminated($event);

        // 验证文件已被删除
        $this->assertFileDoesNotExist($tempFilePath1);
        $this->assertFileDoesNotExist($tempFilePath2);
    }

    /**
     * 测试处理嵌套数组上传文件的情况
     */
    public function testOnTerminatedWithNestedArrayItems(): void
    {
        // 创建多个临时文件
        $tempFilePath1 = tempnam(sys_get_temp_dir(), 'test1');
        $tempFilePath2 = tempnam(sys_get_temp_dir(), 'test2');
        $this->assertFileExists($tempFilePath1);
        $this->assertFileExists($tempFilePath2);

        // 创建Request对象和FileBag
        $request = new Request();
        $fileBag = new FileBag();

        // 使用反射来设置内部的parameters属性，模拟嵌套array形式的上传数据
        $reflection = new \ReflectionClass($fileBag);
        $parametersProperty = $reflection->getProperty('parameters');
        $parametersProperty->setAccessible(true);
        $parametersProperty->setValue($fileBag, [
            'files' => [
                'file1' => [
                    'tmp_name' => $tempFilePath1,
                    'name' => 'test1.txt',
                    'type' => 'text/plain',
                    'size' => 0,
                    'error' => 0,
                ],
                'file2' => [
                    'tmp_name' => $tempFilePath2,
                    'name' => 'test2.txt',
                    'type' => 'text/plain',
                    'size' => 0,
                    'error' => 0,
                ],
            ],
        ]);

        $request->files = $fileBag;

        // 创建Response对象
        $response = new Response();

        // 创建TerminateEvent对象
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new TerminateEvent($kernel, $request, $response);

        // 执行订阅器方法
        /** @var RequestFileCleanSubscriber $subscriber */
        $subscriber = self::getContainer()->get(RequestFileCleanSubscriber::class);
        $subscriber->onTerminated($event);

        // 验证嵌套数组中的文件已被正确清理
        $this->assertFileDoesNotExist($tempFilePath1);
        $this->assertFileDoesNotExist($tempFilePath2);
    }
}
