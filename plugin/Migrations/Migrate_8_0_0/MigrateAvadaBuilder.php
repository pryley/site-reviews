<?php

namespace GeminiLabs\SiteReviews\Migrations\Migrate_8_0_0;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;

class MigrateAvadaBuilder implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        if (!defined('FUSION_BUILDER_VERSION')) {
            return false;
        }
        $sql = "
            SELECT ID, post_content
            FROM table|posts
            WHERE 1=1
            AND post_type != 'revision'
            AND post_content LIKE %s
            AND (post_content LIKE %s OR post_content LIKE %s)
        ";
        $results = glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql(
                $sql,
                '%[fusion_builder_container%',
                '%assigned_posts_custom=%',
                '%assigned_users_custom=%'
            )
        );
        $updated = false;
        foreach ($results as $post) {
            $pattern = '/assigned_(posts|users)="([^"]*)"\s*assigned_\1_custom="([^"]*)"/';
            $replace = function (array $matches): string {
                $type = $matches[1];
                $custom = explode(',', (string) $matches[3]);
                $values = explode(',', (string) $matches[2]);
                $values = array_filter($values, fn ($value) => 'custom' !== trim($value));
                $values = array_filter(array_merge($custom, $values));
                $values = implode(',', $values);
                return sprintf('assigned_%s="%s"', $type, $values);
            };
            $count = 0;
            $content = preg_replace_callback($pattern, $replace, $post->post_content, -1, $count);
            if ($count > 0) {
                $result = wp_update_post([
                    'ID' => $post->ID,
                    'post_content' => $content,
                ], true);
                if (is_wp_error($result)) {
                    glsr_log()->error("Failed to migrate Fusion Builder Post {$post->ID}: {$result->get_error_message()}");
                } else {
                    $updated = true;
                }
            }
        }
        return $updated;
    }
}
