<?php defined('ABSPATH') || exit;

$sections = [ // order is intentional
    trailingslashit(__DIR__).'hooks/readme.php',
    trailingslashit(__DIR__).'hooks/filter-form-field-order.php',
    trailingslashit(__DIR__).'hooks/filter-star-images.php',
    trailingslashit(__DIR__).'hooks/filter-flyoutmenu.php',
    trailingslashit(__DIR__).'hooks/filter-scripts.php',
    trailingslashit(__DIR__).'hooks/filter-styles.php',
    trailingslashit(__DIR__).'hooks/do-something-after-submission.php',
    trailingslashit(__DIR__).'hooks/filter-verification.php',
    trailingslashit(__DIR__).'hooks/filter-schema.php',
    trailingslashit(__DIR__).'hooks/filter-submitted-review-values.php',
    trailingslashit(__DIR__).'hooks/modify-request.php',
    trailingslashit(__DIR__).'hooks/filter-optimize.php',
];
$filename = pathinfo(__FILE__, PATHINFO_FILENAME);
$sections = glsr()->filterArrayUnique("documentation/{$filename}", $sections);
foreach ($sections as $section) {
    include $section;
}
