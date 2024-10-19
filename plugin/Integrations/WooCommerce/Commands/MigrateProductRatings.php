<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands;

use GeminiLabs\SiteReviews\Commands\AbstractCommand;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;

class MigrateProductRatings extends AbstractCommand
{
    public const PER_PAGE = 50;

    public \wpdb $db;
    public bool $revert = false;

    public function __construct(Request $request)
    {
        $this->db = glsr(Query::class)->db;
        $this->revert = wp_validate_boolean($request->alt);
    }

    public function handle(): void
    {
        if ('yes' !== glsr_get_option('integrations.woocommerce.enabled') && false === $this->revert) {
            glsr(Notice::class)->addWarning(
                esc_html_x('Skipped migrating of WooCommerce product ratings because the integration is disabled.', 'admin-text', 'site-reviews')
            );
            return;
        }
        $ttids = $this->termIds();
        if (!empty($ttids)) {
            $this->deleteTerms($ttids);
            $this->insertTerms($ttids);
            wp_update_term_count_now($ttids, 'product_visibility');
            if (function_exists('flrt_get_post_ids_transient_key')) {
                $key = flrt_get_post_ids_transient_key('rated');
                delete_transient($key);
            }
        }
        $this->fixProductReviewCount();
        $notice = $this->revert
            ? esc_html_x('Reverted the WooCommerce product ratings.', 'admin-text', 'site-reviews')
            : esc_html_x('Migrated the product ratings.', 'admin-text', 'site-reviews');
        glsr(Notice::class)->addSuccess($notice);
    }

    public function response(): array
    {
        return [
            'notices' => glsr(Notice::class)->get(),
        ];
    }

    /**
     * Delete all existing "rated-*"" product_visibility terms from Products.
     */
    protected function deleteTerms(array $ttids): int
    {
        $in = implode(',', $ttids);
        $sql = glsr(Query::class)->sql("
            DELETE tr
            FROM table|term_relationships AS tr
            INNER JOIN table|posts AS p ON (p.ID = tr.object_id)
            WHERE 1=1
            AND p.post_type = 'product'
            AND tr.term_taxonomy_id IN ({$in})
        ");
        $results = glsr(Database::class)->dbQuery($sql);
        return Cast::toInt($results);
    }

    protected function fixProductReviewCount(): void
    {
        if (!$this->revert) {
            return;
        }
        if (!defined('WC_ABSPATH')) {
            return;
        }
        if (file_exists(WC_ABSPATH.'includes/wc-update-functions.php')) {
            include_once WC_ABSPATH.'includes/wc-update-functions.php';
            wc_update_500_fix_product_review_count();
        }
    }

    /**
     * Bulk-insert the "rated-*"" product_visibility terms for Products.
     *
     * @todo optimize this for cases when a site has thousands of products...
     */
    protected function insertTerms(array $ttids): int
    {
        $metaKey = $this->revert ? '_wc_average_rating' : '_glsr_average';
        $sql = "
            SELECT p.ID as object_id, CONCAT('rated-', ROUND(pm.meta_value, 0)) AS term_taxonomy_id
            FROM table|posts AS p
            INNER JOIN table|postmeta AS pm ON (p.ID = pm.post_id)
            WHERE 1=1
            AND p.post_type = 'product'
            AND pm.meta_key = %s
            AND pm.meta_value > 0
            AND pm.meta_value < 6
        ";
        $rows = glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql($sql, $metaKey),
            ARRAY_A
        );
        $values = [];
        foreach ($rows as $row) {
            $rating = $row['term_taxonomy_id'];
            if ($ttid = Arr::getAs('int', $ttids, $rating)) {
                $row['term_taxonomy_id'] = $ttid;
                $values[] = $row;
            }
        }
        if (empty($values)) {
            return 0;
        }
        $results = glsr(Database::class)->insertBulk('term_relationships', $values, [
            'object_id',
            'term_taxonomy_id',
        ]);
        return $results;
    }

    protected function termIds(): array
    {
        $sql = glsr(Query::class)->sql("
            SELECT tt.term_taxonomy_id AS ttid, t.name
            FROM table|term_taxonomy AS tt
            INNER JOIN table|terms AS t ON (t.term_id = tt.term_id)
            WHERE 1=1
            AND tt.taxonomy = 'product_visibility'
            AND t.name LIKE 'rated-%'
        ");
        $terms = glsr(Database::class)->dbGetResults($sql);
        return wp_list_pluck($terms, 'ttid', 'name');
    }
}
