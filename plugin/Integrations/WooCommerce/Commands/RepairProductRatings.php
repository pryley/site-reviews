<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands;

use GeminiLabs\SiteReviews\Commands\AbstractCommand;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class RepairProductRatings extends AbstractCommand
{
    public const PER_PAGE = 50;

    public \wpdb $db;

    public function __construct()
    {
        $this->db = glsr(Query::class)->db;
    }

    public function handle(): void
    {
        if ('yes' !== glsr_get_option('addons.woocommerce.enabled')) {
            return;
        }
        $ttids = $this->termIds();
        if (!empty($ttids)) {
            $this->deleteTerms($ttids);
            $this->insertTerms($ttids);
            clean_term_cache($ttids, 'product_visibility');
            if (function_exists('flrt_get_post_ids_transient_key')) {
                $key = flrt_get_post_ids_transient_key('rated');
                delete_transient($key);
            }
        }
    }

    public function response(): array
    {
        $notice = esc_html_x('Repaired WooCommerce product ratings', 'admin-text', 'site-reviews');
        if ('yes' !== glsr_get_option('addons.woocommerce.enabled')) {
            $notice = esc_html_x('Skipped the repair of WooCommerce product ratings because the integration is disabled.', 'admin-text', 'site-reviews');
        }
        return compact('notice');
    }

    protected function deleteTerms(array $ttids): int
    {
        $in = implode(',', $ttids);
        $sql = glsr(Query::class)->sql("
            DELETE tr
            FROM {$this->db->term_relationships} AS tr
            INNER JOIN {$this->db->posts} AS p ON (p.ID = tr.object_id)
            WHERE 1=1
            AND p.post_type = 'product'
            AND tr.term_taxonomy_id IN ({$in})
        ");
        $results = glsr(Database::class)->dbQuery($sql);
        return Cast::toInt($results);
    }

    // @todo optimize this for cases when a site has thousands of products...
    protected function insertTerms(array $ttids): int
    {
        $metaKey = '_glsr_average'; // _wc_average_rating
        $sql = glsr(Query::class)->sql("
            SELECT p.ID as object_id, CONCAT('rated-', ROUND(pm.meta_value, 0)) AS term_taxonomy_id
            FROM {$this->db->posts} AS p
            INNER JOIN {$this->db->postmeta} AS pm ON (p.ID = pm.post_id)
            WHERE 1=1
            AND p.post_type = 'product'
            AND pm.meta_key = %s
            AND pm.meta_value > 0
            AND pm.meta_value < 6
        ", $metaKey);
        $rows = glsr(Database::class)->dbGetResults($sql, ARRAY_A);
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
            FROM {$this->db->term_taxonomy} AS tt
            INNER JOIN {$this->db->terms} AS t ON (t.term_id = tt.term_id)
            WHERE 1=1
            AND tt.taxonomy = 'product_visibility'
            AND t.name LIKE 'rated-%'
        ");
        $terms = glsr(Database::class)->dbGetResults($sql);
        return wp_list_pluck($terms, 'ttid', 'name');
    }
}
