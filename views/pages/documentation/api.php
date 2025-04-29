<?php defined('ABSPATH') || exit;

$dir = pathinfo(__FILE__, PATHINFO_FILENAME);
$files = [];
$iterator = new DirectoryIterator(trailingslashit(__DIR__).$dir);
foreach ($iterator as $fileinfo) {
    if ($fileinfo->isFile() && 'php' === $fileinfo->getExtension()) {
        $filename = str_replace('.php', '', $fileinfo->getFilename());
        $files[$filename] = $fileinfo->getPathname();
    }
}
$files = glsr()->filterArrayUnique("documentation/{$dir}", $files);
ksort($files, SORT_NATURAL);
foreach ($files as $file) {
    include $file;
}
