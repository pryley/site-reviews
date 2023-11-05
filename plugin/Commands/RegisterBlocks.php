<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Helper;

class RegisterBlocks extends AbstractCommand
{
    public function handle(): void
    {
        $dir = glsr()->path('plugin/Blocks');
        if (!is_dir($dir)) {
            $this->fail();
            return;
        }
        $iterator = new \DirectoryIterator($dir);
        foreach ($iterator as $fileinfo) {
            if ('file' !== $fileinfo->getType()) {
                continue;
            }
            $className = str_replace('.php', '', $fileinfo->getFilename());
            $blockClass = Helper::buildClassName($className, 'Blocks');
            if (class_exists($blockClass) && !(new \ReflectionClass($blockClass))->isAbstract()) {
                glsr($blockClass)->register();
            }
        }
    }
}
