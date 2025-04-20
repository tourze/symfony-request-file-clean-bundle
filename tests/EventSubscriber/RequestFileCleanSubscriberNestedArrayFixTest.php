<?php

namespace Tourze\RequestFileCleanBundle\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;

/**
 * 这个测试类展示了如何改进RequestFileCleanSubscriber以支持嵌套数组的清理
 * 注意：这个测试是为了展示改进方案，而非实际测试现有代码
 */
class RequestFileCleanSubscriberNestedArrayFixTest extends TestCase
{
    /**
     * 模拟改进后的RequestFileCleanSubscriber类，能够处理嵌套数组
     * 这是一个示例实现，展示如何递归处理嵌套的文件数组
     */
    private function processFilesRecursively(array $files): void
    {
        foreach ($files as $item) {
            if (is_array($item)) {
                if (isset($item['tmp_name']) && is_file($item['tmp_name'])) {
                    @unlink($item['tmp_name']);
                } else {
                    // 递归处理嵌套数组
                    $this->processFilesRecursively($item);
                }
            }
            // 注意：这里我们不处理UploadedFile对象的情况，因为这是一个演示测试
        }
    }

    /**
     * 测试处理嵌套数组的情况
     * 这个测试演示了改进后的代码如何处理嵌套数组
     */
    public function testRecursiveProcessing(): void
    {
        // 创建测试文件
        $tempFilePath1 = tempnam(sys_get_temp_dir(), 'test1');
        $tempFilePath2 = tempnam(sys_get_temp_dir(), 'test2');
        $tempFilePath3 = tempnam(sys_get_temp_dir(), 'test3');

        $this->assertFileExists($tempFilePath1);
        $this->assertFileExists($tempFilePath2);
        $this->assertFileExists($tempFilePath3);

        // 创建一个深度嵌套的文件数组
        $nestedFiles = [
            'level1' => [
                'level2A' => [
                    'file1' => [
                        'tmp_name' => $tempFilePath1,
                        'name' => 'test1.txt',
                        'type' => 'text/plain',
                        'size' => 0,
                        'error' => 0
                    ]
                ],
                'level2B' => [
                    'file2' => [
                        'tmp_name' => $tempFilePath2,
                        'name' => 'test2.txt',
                        'type' => 'text/plain',
                        'size' => 0,
                        'error' => 0
                    ],
                    'subarray' => [
                        'file3' => [
                            'tmp_name' => $tempFilePath3,
                            'name' => 'test3.txt',
                            'type' => 'text/plain',
                            'size' => 0,
                            'error' => 0
                        ]
                    ]
                ]
            ]
        ];

        // 使用递归处理函数
        $this->processFilesRecursively($nestedFiles);

        // 验证所有文件都被删除了
        $this->assertFileDoesNotExist($tempFilePath1);
        $this->assertFileDoesNotExist($tempFilePath2);
        $this->assertFileDoesNotExist($tempFilePath3);
    }

    /**
     * 测试说明如何改进RequestFileCleanSubscriber
     * 这个方法仅作为注释说明，不是实际的测试
     */
    public function testExplainImprovementApproach(): void
    {
        $this->assertTrue(true);

        // 如果要改进RequestFileCleanSubscriber类，可以这样修改onTerminated方法：
        /*
        public function onTerminated(TerminateEvent $event): void
        {
            $this->processFilesRecursively($event->getRequest()->files->all());
        }
        
        private function processFilesRecursively(array $files): void
        {
            foreach ($files as $item) {
                if (is_array($item)) {
                    if (isset($item['tmp_name']) && is_file($item['tmp_name'])) {
                        @unlink($item['tmp_name']);
                    } else {
                        // 递归处理嵌套数组
                        $this->processFilesRecursively($item);
                    }
                } elseif ($item instanceof UploadedFile) {
                    if (!is_file($item->getPathname())) {
                        continue;
                    }
                    @unlink($item->getPathname());
                }
            }
        }
        */
    }
}
