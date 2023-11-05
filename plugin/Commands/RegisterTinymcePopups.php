<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Helper;

class RegisterTinymcePopups extends AbstractCommand
{
    public function handle(): void
    {
        $dir = glsr()->path('plugin/Tinymce');
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
            $tinymceClass = Helper::buildClassName($className, 'Tinymce');
            if (class_exists($tinymceClass) && !(new \ReflectionClass($tinymceClass))->isAbstract()) {
                glsr($tinymceClass)->register();
            }
        }
    }
}
