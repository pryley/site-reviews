<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Str;

class SqlSchema
{
    protected $constraints;
    protected $db;
    protected $tables;

    public function __construct()
    {
        require_once ABSPATH.'wp-admin/includes/upgrade.php';
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * @return void
     */
    public function addAssignedPostsTableConstraints()
    {
        if (!$this->tableConstraintExists($ratingIdConstraint = $this->prefixConstraint('assigned_posts_rating_id_foreign'))) {
            glsr(Database::class)->dbQuery(glsr(Query::class)->sql("
                ALTER TABLE {$this->table('assigned_posts')}
                ADD CONSTRAINT {$ratingIdConstraint}
                FOREIGN KEY (rating_id)
                REFERENCES {$this->table('ratings')} (ID)
                ON DELETE CASCADE
            "));
        }
        if (!$this->isInnodb('posts')) {
            return;
        }
        if (!$this->tableConstraintExists($postIdConstraint = $this->prefixConstraint('assigned_posts_post_id_foreign'))) {
            glsr(Database::class)->dbQuery(glsr(Query::class)->sql("
                ALTER TABLE {$this->table('assigned_posts')}
                ADD CONSTRAINT {$postIdConstraint}
                FOREIGN KEY (post_id)
                REFERENCES {$this->db->posts} (ID)
                ON DELETE CASCADE
            "));
        }
    }

    /**
     * @return void
     */
    public function addAssignedTermsTableConstraints()
    {
        if (!$this->tableConstraintExists($ratingIdConstraint = $this->prefixConstraint('assigned_terms_rating_id_foreign'))) {
            glsr(Database::class)->dbQuery(glsr(Query::class)->sql("
                ALTER TABLE {$this->table('assigned_terms')}
                ADD CONSTRAINT {$ratingIdConstraint}
                FOREIGN KEY (rating_id)
                REFERENCES {$this->table('ratings')} (ID)
                ON DELETE CASCADE
            "));
        }
        if (!$this->isInnodb('terms')) {
            return;
        }
        if (!$this->tableConstraintExists($termIdConstraint = $this->prefixConstraint('assigned_terms_term_id_foreign'))) {
            glsr(Database::class)->dbQuery(glsr(Query::class)->sql("
                ALTER TABLE {$this->table('assigned_terms')}
                ADD CONSTRAINT {$termIdConstraint}
                FOREIGN KEY (term_id)
                REFERENCES {$this->db->terms} (term_id)
                ON DELETE CASCADE
            "));
        }
    }

    /**
     * @return void
     */
    public function addAssignedUsersTableConstraints()
    {
        if (!$this->tableConstraintExists($ratingIdConstraint = $this->prefixConstraint('assigned_users_rating_id_foreign'))) {
            glsr(Database::class)->dbQuery(glsr(Query::class)->sql("
                ALTER TABLE {$this->table('assigned_users')}
                ADD CONSTRAINT {$ratingIdConstraint}
                FOREIGN KEY (rating_id)
                REFERENCES {$this->table('ratings')} (ID)
                ON DELETE CASCADE
            "));
        }
        if (!$this->isInnodb('users')) {
            return;
        }
        if (!$this->tableConstraintExists($userIdConstraint = $this->prefixConstraint('assigned_users_user_id_foreign'))) {
            glsr(Database::class)->dbQuery(glsr(Query::class)->sql("
                ALTER TABLE {$this->table('assigned_users')}
                ADD CONSTRAINT {$userIdConstraint}
                FOREIGN KEY (user_id)
                REFERENCES {$this->db->users} (ID)
                ON DELETE CASCADE
            "));
        }
    }

    /**
     * @return void
     */
    public function addReviewsTableConstraints()
    {
        if (!$this->isInnodb('posts')) {
            return;
        }
        if (!$this->tableConstraintExists($reviewIdConstraint = $this->prefixConstraint('assigned_posts_review_id_foreign'))) {
            glsr(Database::class)->dbQuery(glsr(Query::class)->sql("
                ALTER TABLE {$this->table('ratings')}
                ADD CONSTRAINT {$reviewIdConstraint}
                FOREIGN KEY (review_id)
                REFERENCES {$this->db->posts} (ID)
                ON DELETE CASCADE
            "));
        }
    }

    /**
     * @return void
     */
    public function addTableConstraints()
    {
        if (!defined('GLSR_UNIT_TESTS')) {
            $this->addAssignedPostsTableConstraints();
            $this->addAssignedTermsTableConstraints();
            $this->addAssignedUsersTableConstraints();
            $this->addReviewsTableConstraints();
        }
    }

    /**
     * @return bool
     */
    public function createAssignedPostsTable()
    {
        if ($this->tableExists('assigned_posts')) {
            return false;
        }
        dbDelta(glsr(Query::class)->sql("
            CREATE TABLE {$this->table('assigned_posts')} (
                rating_id bigint(20) unsigned NOT NULL,
                post_id bigint(20) unsigned NOT NULL,
                is_published tinyint(1) NOT NULL DEFAULT '1',
                UNIQUE KEY {$this->prefix('assigned_posts')}_rating_id_post_id_unique (rating_id,post_id)
            ) ENGINE=InnoDB {$this->db->get_charset_collate()};
        "));
        return true;
    }

    /**
     * @return bool
     */
    public function createAssignedTermsTable()
    {
        if ($this->tableExists('assigned_terms')) {
            return false;
        }
        dbDelta(glsr(Query::class)->sql("
            CREATE TABLE {$this->table('assigned_terms')} (
                rating_id bigint(20) unsigned NOT NULL,
                term_id bigint(20) unsigned NOT NULL,
                UNIQUE KEY {$this->prefix('assigned_terms')}_rating_id_term_id_unique (rating_id,term_id)
            ) ENGINE=InnoDB {$this->db->get_charset_collate()};
        "));
        return true;
    }

    /**
     * @return bool
     */
    public function createAssignedUsersTable()
    {
        if ($this->tableExists('assigned_users')) {
            return false;
        }
        dbDelta(glsr(Query::class)->sql("
            CREATE TABLE {$this->table('assigned_users')} (
                rating_id bigint(20) unsigned NOT NULL,
                user_id bigint(20) unsigned NOT NULL,
                UNIQUE KEY {$this->prefix('assigned_users')}_rating_id_user_id_unique (rating_id,user_id)
            ) ENGINE=InnoDB {$this->db->get_charset_collate()};
        "));
        return true;
    }

    /**
     * WordPress codex says there must be two spaces between PRIMARY KEY and the key definition.
     * @return bool
     * @see https://codex.wordpress.org/Creating_Tables_with_Plugins
     */
    public function createRatingTable()
    {
        if ($this->tableExists('ratings')) {
            return false;
        }
        dbDelta(glsr(Query::class)->sql("
            CREATE TABLE {$this->table('ratings')} (
                ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                review_id bigint(20) unsigned NOT NULL,
                rating int(11) NOT NULL DEFAULT '0',
                type varchar(20) DEFAULT 'local',
                is_approved tinyint(1) NOT NULL DEFAULT '0',
                is_pinned tinyint(1) NOT NULL DEFAULT '0',
                name varchar(250) DEFAULT NULL,
                email varchar(100) DEFAULT NULL,
                avatar varchar(200) DEFAULT NULL,
                ip_address varchar(100) DEFAULT NULL,
                url varchar(250) DEFAULT NULL,
                PRIMARY KEY  (ID),
                UNIQUE KEY {$this->prefix('ratings')}_review_id_unique (review_id),
                KEY {$this->prefix('ratings')}_rating_type_is_pinned_index (rating,type,is_pinned)
            ) ENGINE=InnoDB {$this->db->get_charset_collate()};
        "));
        return true;
    }

    /**
     * @return void
     */
    public function createTables()
    {
        $this->createAssignedPostsTable();
        $this->createAssignedTermsTable();
        $this->createAssignedUsersTable();
        $this->createRatingTable();
        add_option(glsr()->prefix.'db_version', glsr()->version('minor'));
    }

    /**
     * @return bool
     */
    public function isInnodb($table)
    {
        $engine = $this->db->get_var("
            SELECT ENGINE
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = '{$this->db->dbname}' AND TABLE_NAME = '{$this->table($table)}'
        ");
        if (empty($engine)) {
            glsr_log()->warning(sprintf('The %s database table does not exist.', $this->table($table)));
            return false;
        }
        return 'innodb' === strtolower($engine);
    }

    /**
     * @return string
     */
    public function prefix($table)
    {
        return Str::prefix($table, glsr()->prefix);
    }

    /**
     * @return string
     */
    public function prefixConstraint($constraint)
    {
        $constraint = Str::prefix($constraint, glsr()->prefix);
        if (is_multisite() && $this->db->blogid > 1) {
            return $constraint.'_'.$this->db->blogid;
        }
        return $constraint;
    }

    /**
     * @return string
     */
    public function table($table)
    {
        if (Str::endsWith(['ratings', 'assigned_posts', 'assigned_terms', 'assigned_users'], $table)) {
            $table = $this->prefix($table);
            return $this->db->get_blog_prefix().$table;
        }
        if (array_key_exists($table, $this->db->tables())) {
            return $this->db->{$table};
        }
        glsr_log()->error(sprintf('$wpdb->%s does not exist.', $table));
    }

    /**
     * @return bool
     */
    public function tableExists($table)
    {
        if (!isset($this->tables)) {
            $prefix = $this->db->get_blog_prefix().glsr()->prefix;
            $this->tables = $this->db->get_col(
                $this->db->prepare("SHOW TABLES LIKE %s", $this->db->esc_like($prefix).'%')
            );
        }
        return in_array($this->table($table), $this->tables);
    }

    /**
     * @return bool
     */
    public function tableConstraintExists($constraint)
    {
        if (!isset($this->constraints)) {
            $this->constraints = $this->db->get_col("
                SELECT CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = '{$this->db->dbname}'
            ");
        }
        return in_array($constraint, $this->constraints);
    }

    /**
     * @return string[]
     */
    public function tableEngines()
    {
        $results = $this->db->get_results("
            SELECT TABLE_NAME, ENGINE
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = '{$this->db->dbname}'
            AND TABLE_NAME IN ('{$this->db->options}','{$this->db->posts}','{$this->db->terms}','{$this->db->users}')
        ");
        $engines = [];
        foreach ($results as $result) {
            if (!array_key_exists($result->ENGINE, $engines)) {
                $engines[$result->ENGINE] = [];
            }
            $engines[$result->ENGINE][] = Str::removePrefix($result->TABLE_NAME, $this->db->get_blog_prefix());
        }
        $tableEngines = [];
        foreach ($engines as $engine => $tables) {
          $tableEngines[] = sprintf('%s (%s)', $engine, implode('|', $tables));
        }
        return $tableEngines;
    }
}
