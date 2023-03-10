<?php defined('ABSPATH') || exit;
    $files = [];
    $iterator = new DirectoryIterator(trailingslashit(__DIR__).'whatsnew');
    foreach ($iterator as $fileinfo) {
        if ($fileinfo->isFile() && 'php' === $fileinfo->getExtension()) {
            $files[] = $fileinfo->getPathname();
        }
    }
    rsort($files, SORT_NATURAL);
?>
<p class="about-description">We hope you love the changes in this new release!</p>
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
