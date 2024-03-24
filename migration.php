<?php

use GeminiLabs\SiteReviews\Database;

defined('ABSPATH') || exit;

/**
 * @return array
 *
 * @since 5.9
 */
function glsr_migration_5_9_db_version_1_1(array $values)
{
    if (version_compare(glsr(Database::class)->version(), '1.1', '<')) {
        unset($values['terms']);
    }
    return $values;
}
add_filter('site-reviews/config/forms/metabox-fields', 'glsr_migration_5_9_db_version_1_1');
add_filter('site-reviews/defaults/rating', 'glsr_migration_5_9_db_version_1_1');
add_filter('site-reviews/defaults/reviews', 'glsr_migration_5_9_db_version_1_1');
