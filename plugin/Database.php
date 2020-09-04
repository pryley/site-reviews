<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\SqlSchema;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use WP_Query;
use WP_User_Query;

class Database
{
    use Deprecated;

    protected $db;
    protected $mappedDeprecatedMethods;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->mappedDeprecatedMethods = [
            'get' => 'meta',
            'getTerms' => 'terms',
            'set' => 'metaSet',
        ];
    }

    /**
     * @return void
     */
    public function createTables()
    {
        glsr(SqlSchema::class)->createTables();
        glsr(SqlSchema::class)->addTableConstraints();
    }

    /**
     * @param string $sql
     * @return array
     */
    public function dbGetCol($sql)
    {
        return $this->logErrors($this->db->get_col($sql));
    }

    /**
     * @param string $sql
     * @param string $output
     * @return array|object|null
     */
    public function dbGetResults($sql, $output)
    {
        $output = Str::restrictTo(['ARRAY_A', 'ARRAY_N', 'OBJECT', 'OBJECT_K'], $output, OBJECT);
        return $this->logErrors($this->db->get_results($sql, $output));
    }

    /**
     * @param string $sql
     * @param string $output
     * @return array|object|void|null
     */
    public function dbGetRow($sql, $output)
    {
        $output = Str::restrictTo(['ARRAY_A', 'ARRAY_N', 'OBJECT'], $output, OBJECT);
        return $this->logErrors($this->db->get_row($sql, $output));
    }

    /**
     * @param string $sql
     * @return string|null
     */
    public function dbGetVar($sql)
    {
        return $this->logErrors($this->db->get_var($sql));
    }

    /**
     * @param string $sql
     * @return int|bool
     */
    public function dbQuery($sql)
    {
        return $this->logErrors($this->db->query($sql));
    }

    /**
     * @param string $table
     * @return int|false
     */
    public function delete($table, array $where)
    {
        $result = $this->db->delete(glsr(Query::class)->table($table), $where);
        glsr(Query::class)->sql($this->db->last_query); // for logging use only
        return $this->logErrors($result);
    }

    /**
     * @param string|string[] $keys
     * @param string $table
     * @return int|false
     */
    public function deleteMeta($keys, $table = 'postmeta')
    {
        $table = glsr(Query::class)->table($table);
        $metaKeys = glsr(Query::class)->escValuesForInsert(Arr::convertFromString($keys));
        $sql = glsr(Query::class)->sql("DELETE FROM {$table} WHERE meta_key IN {$metaKeys}");
        return $this->dbQuery($sql);
    }

    /**
     * Search SQL filter for matching against post title only.
     * @see http://wordpress.stackexchange.com/a/11826/1685
     * @param string $search
     * @return string
     * @filter posts_search
     */
    public function filterSearchByTitle($search, WP_Query $query)
    {
        if (empty($search) || empty($query->get('search_terms'))) {
            return $search;
        }
        $n = empty($query->get('exact'))
            ? '%'
            : '';
        $search = [];
        foreach ((array) $query->get('search_terms') as $term) {
            $search[] = $this->db->prepare("{$this->db->posts}.post_title LIKE %s", $n.$this->db->esc_like($term).$n);
        }
        if (!is_user_logged_in()) {
            $search[] = "{$this->db->posts}.post_password = ''";
        }
        return ' AND '.implode(' AND ', $search);
    }

    /**
     * @param string $table
     * @return int|bool
     */
    public function insert($table, array $data)
    {
        $this->db->insert_id = 0;
        $table = glsr(Query::class)->table($table);
        $fields = glsr(Query::class)->escFieldsForInsert(array_keys($data));
        $values = glsr(Query::class)->escValuesForInsert($data);
        $sql = glsr(Query::class)->sql("INSERT IGNORE INTO {$table} {$fields} VALUES {$values}");
        $result = $this->dbQuery($sql);
        return empty($result) ? false : $result;
    }

    /**
     * @param string $table
     * @return int|false
     */
    public function insertBulk($table, array $values, array $fields)
    {
        $this->db->insert_id = 0;
        $data = [];
        foreach ($values as $value) {
            $value = array_intersect_key($value, array_flip($fields)); // only keep field values
            if (count($value) === count($fields)) {
                $value = array_merge(array_flip($fields), $value); // make sure the order is correct
                $data[] = glsr(Query::class)->escValuesForInsert($value);
            }
        }
        $table = glsr(Query::class)->table($table);
        $fields = glsr(Query::class)->escFieldsForInsert($fields);
        $values = implode(',', $data);
        $sql = glsr(Query::class)->sql("INSERT IGNORE INTO {$table} {$fields} VALUES {$values}");
        return $this->dbQuery($sql);
    }

    /**
     * @return bool
     */
    public function isMigrationNeeded()
    {
        $table = glsr(Query::class)->table('ratings');
        $postCount = wp_count_posts(glsr()->post_type)->publish;
        if (empty($postCount)) {
            return false;
        }
        $sql = glsr(Query::class)->sql("SELECT COUNT(*) FROM {$table} WHERE is_approved = 1");
        return empty($this->dbGetVar($sql));
    }

    /**
     * @param mixed $result
     * @return mixed
     */
    public function logErrors($result = null)
    {
        if ($this->db->last_error) {
            glsr_log()->error($this->db->last_error);
        }
        return $result;
    }

    /**
     * @param int $postId
     * @param string $key
     * @param bool $single
     * @return mixed
     */
    public function meta($postId, $key, $single = true)
    {
        $key = Str::prefix($key, '_');
        $postId = Cast::toInt($postId);
        return get_post_meta($postId, $key, $single);
    }

    /**
     * @param int $postId
     * @param string $key
     * @param mixed $value
     * @return int|bool
     */
    public function metaSet($postId, $key, $value)
    {
        $key = Str::prefix($key, '_');
        $postId = Cast::toInt($postId);
        return update_metadata('post', $postId, $key, $value); // update_metadata works with revisions
    }

    /**
     * @param string $searchTerm
     * @return void|string
     */
    public function searchPosts($searchTerm)
    {
        $args = [
            'post_status' => 'publish',
            'post_type' => 'any',
        ];
        if (is_numeric($searchTerm)) {
            $args['post__in'] = [$searchTerm];
        } else {
            $args['orderby'] = 'relevance';
            $args['posts_per_page'] = 10;
            $args['s'] = $searchTerm;
        }
        add_filter('posts_search', [$this, 'filterSearchByTitle'], 500, 2);
        $search = new WP_Query($args);
        remove_filter('posts_search', [$this, 'filterSearchByTitle'], 500);
        if ($search->have_posts()) {
            $results = '';
            while ($search->have_posts()) {
                $search->the_post();
                $results .= glsr()->build('partials/editor/search-result', [
                    'ID' => get_the_ID(),
                    'permalink' => esc_url((string) get_permalink()),
                    'title' => esc_attr(get_the_title()),
                ]);
            }
            wp_reset_postdata();
            return $results;
        }
    }

    /**
     * @param string $searchTerm
     * @return void|string
     */
    public function searchUsers($searchTerm)
    {
        $args = [
            'fields' => ['ID', 'user_login', 'display_name'],
            'number' => 10,
            'orderby' => 'display_name',
        ];
        if (is_numeric($searchTerm)) {
            $args['include'] = [$searchTerm];
        } else {
            $args['search'] = '*'.$searchTerm.'*';
            $args['search_columns'] = ['user_login', 'user_nicename', 'display_name'];
        }
        $users = (new WP_User_Query($args))->get_results();
        if (!empty($users)) {
            return array_reduce($users, function ($carry, $user) {
                return $carry.glsr()->build('partials/editor/search-result', [
                    'ID' => $user->ID,
                    'permalink' => esc_url(get_author_posts_url($user->ID)),
                    'title' => esc_attr($user->display_name.' ('.$user->user_login.')'),
                ]);
            });
        }
    }

    /**
     * @return array
     */
    public function terms(array $args = [])
    {
        $args = wp_parse_args($args, [
            'count' => false,
            'fields' => 'id=>name',
            'hide_empty' => false,
            'taxonomy' => glsr()->taxonomy,
        ]);
        $terms = get_terms($args);
        if (is_wp_error($terms)) {
            glsr_log()->error($terms->get_error_message());
            return [];
        }
        return $terms;
    }

    /**
     * @param string $table
     * @return int|bool
     */
    public function update($table, array $data, array $where)
    {
        $result = $this->db->update(glsr(Query::class)->table($table), $data, $where);
        glsr(Query::class)->sql($this->db->last_query); // for logging use only
        return $this->logErrors($result);
    }
}
