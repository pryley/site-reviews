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
    $files = glsr()->filterArray("documentation/shortcode/{$dir}", $files);
    ksort($files, SORT_NATURAL);
?>
<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="shortcode-site_reviews_form">
            <span class="title">Display the review form</span>
            <span class="badge code">[site_reviews_form]</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="shortcode-site_reviews_form" class="inside">
        <h3>This shortcode displays the review form.</h3>
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">Each example below demonstrates a different shortcode option. If you need to use multiple options, simply combine the options together (separated with a space) in the same shortcode.</p>
        </div>
        <?php
            foreach ($files as $file) {
                include $file;
            }
        ?>
    </div>
</div>
