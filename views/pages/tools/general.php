<?php defined('ABSPATH') || exit;

$sections = [
    trailingslashit(__DIR__).'general/configure-ip-detection.php',
    trailingslashit(__DIR__).'general/export-reviews.php',
    trailingslashit(__DIR__).'general/export-settings.php',
    trailingslashit(__DIR__).'general/import-reviews.php',
    trailingslashit(__DIR__).'general/import-settings.php',
    trailingslashit(__DIR__).'general/migrate-plugin.php',
    trailingslashit(__DIR__).'general/optimise-db-tables.php',
    trailingslashit(__DIR__).'general/repair-permissions.php',
    trailingslashit(__DIR__).'general/repair-review-relations.php',
    trailingslashit(__DIR__).'general/reset-assigned-meta.php',
    trailingslashit(__DIR__).'general/rollback-plugin.php',
];
$filename = pathinfo(__FILE__, PATHINFO_FILENAME);
$sections = glsr()->filterArrayUnique("tools/{$filename}", $sections);
foreach ($sections as $section) {
    include $section;
}
