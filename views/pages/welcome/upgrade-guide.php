<?php defined('ABSPATH') || exit;
    $files = [];
    $iterator = new DirectoryIterator(trailingslashit(__DIR__).'upgrade');
    foreach ($iterator as $fileinfo) {
        if ($fileinfo->isFile() && 'php' === $fileinfo->getExtension()) {
            $files[] = $fileinfo->getPathname();
        }
    }
    rsort($files, SORT_NATURAL);
?>
<p class="about-description">Please take some time to read this upgrade guide.</p>
<div class="is-fullwidth">
    <div class="glsr-flex-row">
        <div class="glsr-column">
            <?php
                foreach ($files as $file) {
                    include $file;
                }
            ?>
        </div>
    </div>
</div>
