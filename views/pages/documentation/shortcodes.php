<?php defined('ABSPATH') || exit;

$sections = [ // order is intentional
    trailingslashit(__DIR__).'shortcodes/site_review.php',
    trailingslashit(__DIR__).'shortcodes/site_reviews_summary.php',
    trailingslashit(__DIR__).'shortcodes/site_reviews.php',
    trailingslashit(__DIR__).'shortcodes/site_reviews_form.php',
];
$filename = pathinfo(__FILE__, PATHINFO_FILENAME);
$sections = glsr()->filterArrayUnique('documentation/'.$filename, $sections);
foreach ($sections as $section) {
    include $section;
}
