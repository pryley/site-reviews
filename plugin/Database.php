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
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Database
{
    use Deprecated;

    protected \wpdb $db;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * Use this before bulk insert (see: $this->finishTransaction()).
     */
    public function beginTransaction(string $table): void
    {
        if (glsr(Tables::class)->isSqlite()) {
            $this->dbQuery("BEGIN TRANSACTION;");
        } elseif (glsr(Tables::class)->isInnodb($table)) {
            $this->dbQuery("START TRANSACTION;");
        } elseif (glsr(Tables::class)->isMyisam($table)) {
            $this->dbQuery("SET autocommit = 0;");
        }
    }

    public function dbGetCol(string $sql): array
    {
        return $this->logErrors($this->db->get_col($sql));
    }

    /**
     * Query result in format specified by $output or null on failure.
     *
     * @return array|object|null
     */
    public function dbGetResults(string $sql, string $output = 'OBJECT')
    {
        $output = Str::restrictTo(['ARRAY_A', 'ARRAY_N', 'OBJECT', 'OBJECT_K'], $output, OBJECT);
        return $this->logErrors($this->db->get_results($sql, $output));
    }

    /**
     * Query result in format specified by $output or null on failure.
     *
     * @return array|object|void|null
     */
    public function dbGetRow(string $sql, string $output)
    {
        $output = Str::restrictTo(['ARRAY_A', 'ARRAY_N', 'OBJECT'], $output, OBJECT);
        return $this->logErrors($this->db->get_row($sql, $output));
    }

    /**
     * Query result as string, or null on failure.
     */
    public function dbGetVar(string $sql): ?string
    {
        return $this->logErrors($this->db->get_var($sql));
    }

    /**
     * True for CREATE, ALTER, TRUNCATE and DROP queries.
     * Number of rows affected/selected for all other queries.
     * False on error.
     *
     * @return int|bool
     */
    public function dbQuery(string $sql)
    {
        return $this->logErrors($this->db->query($sql));
    }

    /**
     * True for CREATE, ALTER, TRUNCATE and DROP queries.
     * Number of rows affected/selected for all other queries.
     * False on error.
     *
     * @return int|bool
     */
    public function dbSafeQuery(string $sql)
    {
        if (glsr(Tables::class)->isSqlite()) {
            return $this->dbQuery($sql);
        }
        $this->db->query('SET foreign_key_checks = 0');
        $result = $this->logErrors($this->db->query($sql));
        $this->db->query('SET foreign_key_checks = 1');
        return $result;
    }

    /**
     * Number of rows deleted. False on error.
     *
     * @return int|false
     */
    public function delete(string $table, array $where)
    {
        $result = $this->db->delete(glsr(Tables::class)->table($table), $where);
        glsr(Query::class)->sql($this->db->last_query); // for logging use only
        return $this->logErrors($result);
    }

    /**
     * Number of rows deleted. False on error.
     *
     * @param string|string[] $keys
     *
     * @return int|false
     */
    public function deleteMeta($keys, string $table = 'postmeta')
    {
        $table = glsr(Tables::class)->table($table);
        $metaKeys = glsr(Query::class)->escValuesForInsert(Arr::convertFromString($keys));
        $sql = glsr(Query::class)->sql("
            DELETE FROM {$table} WHERE meta_key IN {$metaKeys}
        ");
        return $this->dbQuery($sql);
    }

    /**
     * Use this after bulk insert (see: $this->beginTransaction()).
     */
    public function finishTransaction(string $table): void
    {
        if (glsr(Tables::class)->isSqlite()) {
            $this->dbQuery("COMMIT;");
        } elseif (glsr(Tables::class)->isInnodb($table)) {
            $this->dbQuery("COMMIT;");
        } elseif (glsr(Tables::class)->isMyisam($table)) {
            $this->dbQuery("SET autocommit = 1;");
        }
    }

    /**
     * Number of rows inserted. False on error.
     *
     * @return int|false
     */
    public function insert(string $table, array $data)
    {
        $this->db->insert_id = 0;
        $table = glsr(Tables::class)->table($table);
        $fields = glsr(Query::class)->escFieldsForInsert(array_keys($data));
        $values = glsr(Query::class)->escValuesForInsert($data);
        if (glsr(Tables::class)->isSqlite()) {
            $sql = glsr(Query::class)->sql("INSERT OR IGNORE INTO {$table} {$fields} VALUES {$values}");
        } else {
            $sql = glsr(Query::class)->sql("INSERT IGNORE INTO {$table} {$fields} VALUES {$values}");
        }
        $result = $this->dbQuery($sql);
        return $result;
    }

    /**
     * Number of rows inserted. False on error.
     *
     * @return int|false
     */
    public function insertBulk(string $table, array $values, array $fields)
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
        $table = glsr(Tables::class)->table($table);
        $fields = glsr(Query::class)->escFieldsForInsert($fields);
        $values = implode(',', $data);
        if (glsr(Tables::class)->isSqlite()) {
            $sql = glsr(Query::class)->sql("INSERT OR IGNORE INTO {$table} {$fields} VALUES {$values}");
        } else {
            $sql = glsr(Query::class)->sql("INSERT IGNORE INTO {$table} {$fields} VALUES {$values}");
        }
        return $this->dbQuery($sql);
    }

    public function isMigrationNeeded(): bool
    {
        $postTypes = wp_count_posts(glsr()->post_type);
        $postCount = Arr::get($postTypes, 'publish');
        if (empty($postCount)) {
            return false;
        }
        $sql = glsr(Query::class)->sql("SELECT COUNT(*) FROM table|ratings WHERE is_approved = 1");
        return empty($this->dbGetVar($sql));
    }

    /**
     * @param mixed $result
     *
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
     * An array of values if $single is false.
     * The value of the meta field if $single is true.
     * False for an invalid $post_id (non-numeric, zero, or negative value).
     * An empty string if a valid but non-existing post ID is passed.
     *
     * @return mixed
     */
    public function meta(int $postId, string $key, bool $single = true)
    {
        $key = Str::prefix($key, '_');
        $postId = Cast::toInt($postId);
        return get_post_meta($postId, $key, $single);
    }

    /**
     * The new meta field ID if a field with the given key didn't exist and was therefore added.
     * True on successful update. False on failure or if the value passed to the function
     * is the same as the one that is already in the database.
     *
     * @param mixed $value
     *
     * @return int|bool
     */
    public function metaSet(int $postId, string $key, $value)
    {
        $key = Str::prefix($key, '_');
        $postId = Cast::toInt($postId);
        return update_metadata('post', $postId, $key, $value); // update_metadata works with revisions
    }

    public function searchAssignedPosts(string $searchTerm): SearchAssignedPosts
    {
        return glsr(SearchAssignedPosts::class)->search($searchTerm);
    }

    public function searchAssignedUsers(string $searchTerm): SearchAssignedUsers
    {
        return glsr(SearchAssignedUsers::class)->search($searchTerm);
    }

    public function searchPosts(string $searchTerm): SearchPosts
    {
        return glsr(SearchPosts::class)->search($searchTerm);
    }

    public function searchUsers(string $searchTerm): SearchUsers
    {
        return glsr(SearchUsers::class)->search($searchTerm);
    }

    public function terms(array $args = []): array
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
     * Number of rows updated. False on error.
     *
     * @return int|false
     */
    public function update(string $table, array $data, array $where)
    {
        $result = $this->db->update(glsr(Tables::class)->table($table), $data, $where);
        glsr(Query::class)->sql($this->db->last_query); // for logging use only
        return $this->logErrors($result);
    }

    public function users(array $args = []): array
    {
        $args = wp_parse_args($args, [
            'fields' => ['ID', 'display_name', 'user_nicename'],
            'number' => 50, // only get the first 50 users!
            'orderby' => 'display_name',
        ]);
        $results = [];
        $users = get_users($args);
        foreach ($users as $user) {
            $name = glsr(Sanitizer::class)->sanitizeUserName(
                $user->display_name,
                $user->user_nicename
            );
            $results[$user->ID] = $name;
        }
        return $results;
    }

    public function version(): string
    {
        $dbVersion = Cast::toString(get_option(glsr()->prefix.'db_version'));
        if (version_compare($dbVersion, Application::DB_VERSION, '>')) { // version should never be higher than plugin database version
            update_option(glsr()->prefix.'db_version', '1.0'); // setting it to a low version will trigger the plugin migration
            $dbVersion = '1.0';
        }
        return $dbVersion;
    }
}
