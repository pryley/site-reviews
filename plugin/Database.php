<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Search\SearchAssignedPosts;
use GeminiLabs\SiteReviews\Database\Search\SearchAssignedUsers;
use GeminiLabs\SiteReviews\Database\Search\SearchPosts;
use GeminiLabs\SiteReviews\Database\Search\SearchUsers;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * @property array $mappedDeprecatedMethods
 */
class Database
{
    use Deprecated;

    protected $db;

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
     * Use this before bulk insert (see: $this->finishTransaction()).
     * @param string $table
     * @return void
     */
    public function beginTransaction($table)
    {
        $sql = glsr(Tables::class)->isInnodb($table)
            ? 'START TRANSACTION;'
            : 'SET autocommit = 0;';
        $this->dbQuery($sql);
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
    public function dbGetResults($sql, $output = 'OBJECT')
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
     * @param string $sql
     * @return int|bool
     */
    public function dbSafeQuery($sql)
    {
        $this->db->query("SET GLOBAL foreign_key_checks = 0");
        $result = $this->logErrors($this->db->query($sql));
        $this->db->query("SET GLOBAL foreign_key_checks = 1");
        return $result;
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
     * @return int|bool
     */
    public function deleteInvalidFields()
    {
        return $this->dbSafeQuery(
            glsr(Query::class)->sql(sprintf("
                DELETE f
                FROM %s AS f
                LEFT JOIN %s AS r ON f.rating_id = r.ID
                WHERE (r.ID IS NULL)
            ",
                glsr(Query::class)->table('fields'),
                glsr(Query::class)->table('ratings')
            ))
        );
    }

    /**
     * @return int|bool
     */
    public function deleteInvalidPostAssignments()
    {
        return $this->dbSafeQuery(
            glsr(Query::class)->sql(sprintf("
                DELETE ap
                FROM %s AS ap
                LEFT JOIN %s AS r ON ap.rating_id = r.ID
                LEFT JOIN {$this->db->posts} AS p ON ap.post_id = p.ID
                WHERE (r.ID IS NULL OR p.ID IS NULL)
            ",
                glsr(Query::class)->table('assigned_posts'),
                glsr(Query::class)->table('ratings')
            ))
        );
    }

    /**
     * @return int|bool
     */
    public function deleteInvalidReviews()
    {
        return $this->dbSafeQuery(
            glsr(Query::class)->sql(sprintf("
                DELETE r
                FROM %s AS r
                LEFT JOIN {$this->db->posts} AS p ON r.review_id = p.ID
                WHERE (p.post_type IS NULL OR p.post_type != '%s')
            ",
                glsr(Query::class)->table('ratings'),
                glsr()->post_type
            ))
        );
    }

    /**
     * @return int|bool
     */
    public function deleteInvalidTermAssignments()
    {
        return $this->dbSafeQuery(
            glsr(Query::class)->sql(sprintf("
                DELETE at
                FROM %s AS at
                LEFT JOIN %s AS r ON at.rating_id = r.ID
                LEFT JOIN {$this->db->term_taxonomy} AS tt ON at.term_id = tt.term_id
                WHERE (r.ID IS NULL OR tt.term_id IS NULL) OR tt.taxonomy != '%s'
            ",
                glsr(Query::class)->table('assigned_terms'),
                glsr(Query::class)->table('ratings'),
                glsr()->taxonomy
            ))
        );
    }

    /**
     * @return int|bool
     */
    public function deleteInvalidUserAssignments()
    {
        return $this->dbSafeQuery(
            glsr(Query::class)->sql(sprintf("
                DELETE au
                FROM %s AS au
                LEFT JOIN %s AS r ON au.rating_id = r.ID
                LEFT JOIN {$this->db->users} AS u ON au.user_id = u.ID
                WHERE (r.ID IS NULL OR u.ID IS NULL)
            ",
                glsr(Query::class)->table('assigned_users'),
                glsr(Query::class)->table('ratings')
            ))
        );
    }

    /**
     * @param string|string[] $keys
     * @param string $table
     * @return int|bool
     */
    public function deleteMeta($keys, $table = 'postmeta')
    {
        $table = glsr(Query::class)->table($table);
        $metaKeys = glsr(Query::class)->escValuesForInsert(Arr::convertFromString($keys));
        $sql = glsr(Query::class)->sql("
            DELETE FROM {$table} WHERE meta_key IN {$metaKeys}
        ");
        return $this->dbQuery($sql);
    }

    /**
     * Use this after bulk insert (see: $this->beginTransaction()).
     * @param string $table
     * @return void
     */
    public function finishTransaction($table)
    {
        $sql = glsr(Tables::class)->isInnodb($table)
            ? 'COMMIT;'
            : 'SET autocommit = 1;';
        $this->dbQuery($sql);
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
        $postTypes = wp_count_posts(glsr()->post_type);
        $postCount = Arr::get($postTypes, 'publish');
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
            glsr_trace();
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
     * @return SearchAssignedPosts
     */
    public function searchAssignedPosts($searchTerm)
    {
        return glsr(SearchAssignedPosts::class)->search($searchTerm);
    }

    /**
     * @param string $searchTerm
     * @return SearchAssignedUsers
     */
    public function searchAssignedUsers($searchTerm)
    {
        return glsr(SearchAssignedUsers::class)->search($searchTerm);
    }

    /**
     * @param string $searchTerm
     * @return SearchPosts
     */
    public function searchPosts($searchTerm)
    {
        return glsr(SearchPosts::class)->search($searchTerm);
    }

    /**
     * @param string $searchTerm
     * @return SearchUsers
     */
    public function searchUsers($searchTerm)
    {
        return glsr(SearchUsers::class)->search($searchTerm);
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
            glsr_trace();
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

    /**
     * @return array
     */
    public function users(array $args = [])
    {
        $args = wp_parse_args($args, [
            'fields' => ['ID', 'display_name'],
            'number' => 50, // only get the first 50 users!
            'orderby' => 'display_name',
        ]);
        $users = get_users($args);
        return wp_list_pluck($users, 'display_name', 'ID');
    }

    /**
     * @param string $compareToVersion
     * @return bool|string
     */
    public function version($compareToVersion = null)
    {
        $dbVersion = Cast::toString(get_option(glsr()->prefix.'db_version'));
        if (version_compare($dbVersion, Application::DB_VERSION, '>')) { // version should never be higher than plugin database version
            update_option(glsr()->prefix.'db_version', '1.0');
            $dbVersion = '1.0';
        }
        return isset($compareToVersion)
            ? version_compare($dbVersion, Cast::toString($compareToVersion), '>=')
            : $dbVersion;
    }
}
