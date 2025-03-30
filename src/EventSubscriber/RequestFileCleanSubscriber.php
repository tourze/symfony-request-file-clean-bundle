<?php

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
            // 在一些未知情景中，这里拿到的貌似是数组。。
            if (is_array($item)) {
                if (isset($item['tmp_name']) && is_file($item['tmp_name'])) {
                    @unlink($item['tmp_name']);
                }
            } else {
                /** @var UploadedFile $item */
                if (!is_file($item->getPathname())) {
                    continue;
                }
                @unlink($item->getPathname());
            }
        }
    }
}
