<?php

use GeminiLabs\SiteReviews\Database;

defined('ABSPATH') || exit;

/**
 * A missing version means "we do not know", and the only safe answer to that is
 * to leave the data alone: a current database (which is what a site with no
 * recorded version almost always has) keeps its column, and a genuinely pre-1.1
 * database has a version recorded, because that is what the migrations write.
 *
 * @return array
 *
 * @since 5.9
 */
function glsr_migration_5_9_db_version_1_1(array $values)
{
    $version = glsr(Database::class)->version();
    if ('' === $version) {
        return $values; // no version recorded is not the same as an old version
    }
    if (version_compare($version, '1.1', '<')) {
        unset($values['terms']);
    }
    return $values;
}
add_filter('site-reviews/config/forms/metabox-fields', 'glsr_migration_5_9_db_version_1_1');
add_filter('site-reviews/defaults/rating', 'glsr_migration_5_9_db_version_1_1');
add_filter('site-reviews/defaults/reviews', 'glsr_migration_5_9_db_version_1_1');
