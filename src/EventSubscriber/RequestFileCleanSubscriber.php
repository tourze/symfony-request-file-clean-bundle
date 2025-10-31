<?php

declare(strict_types=1);

namespace Tourze\RequestFileCleanBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * 在一些比较特殊的运行环境中，我们上传并存储文件到临时目录后，系统不会自动清除一些文件的，为此我们在这里人肉处理一次
 */
class RequestFileCleanSubscriber
{
    #[AsEventListener(event: KernelEvents::TERMINATE, priority: -9999)]
    public function onTerminated(TerminateEvent $event): void
    {
        foreach ($event->getRequest()->files->all() as $item) {
            $this->cleanFileItem($item);
        }
    }

    private function cleanFileItem(mixed $item): void
    {
        if (is_array($item)) {
            $this->cleanArrayFile($item);

            return;
        }

        $this->cleanUploadedFile($item);
    }

    /**
     * @param array<string, mixed> $item
     */
    private function cleanArrayFile(array $item): void
    {
        if (isset($item['tmp_name']) && is_file($item['tmp_name'])) {
            unlink($item['tmp_name']);

            return;
        }

        // 递归处理嵌套数组
        foreach ($item as $value) {
            if (is_array($value)) {
                $this->cleanArrayFile($value);
            }
        }
    }

    private function cleanUploadedFile(mixed $item): void
    {
        /** @var UploadedFile $item */
        if (!is_file($item->getPathname())) {
            return;
        }
        unlink($item->getPathname());
    }
}
