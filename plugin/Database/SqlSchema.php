<?php

namespace GeminiLabs\SiteReviews\Database;

class SqlSchema
{
    protected $db;

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
        if (!$this->tableConstraintExists($constraint = $this->prefix('assigned_posts').'_rating_id_foreign')) {
        $this->db->query("
            ALTER TABLE {$this->table('assigned_posts')}
                ADD CONSTRAINT {$constraint}
            FOREIGN KEY (rating_id)
            REFERENCES {$this->table('ratings')} (ID)
            ON DELETE CASCADE
        ");
        }
        if (!$this->tableConstraintExists($constraint = $this->prefix('assigned_posts').'_post_id_foreign')) {
        $this->db->query("
            ALTER TABLE {$this->table('assigned_posts')}
                ADD CONSTRAINT {$constraint}
            FOREIGN KEY (post_id)
            REFERENCES {$this->db->posts} (ID)
            ON DELETE CASCADE
        ");
    }
    }

    /**
     * @return void
     */
    public function addAssignedTermsTableConstraints()
    {
        if (!$this->tableConstraintExists($constraint = $this->prefix('assigned_terms').'_rating_id_foreign')) {
        $this->db->query("
            ALTER TABLE {$this->table('assigned_terms')}
                ADD CONSTRAINT {$constraint}
            FOREIGN KEY (rating_id)
            REFERENCES {$this->table('ratings')} (ID)
            ON DELETE CASCADE
        ");
        }
        if (!$this->tableConstraintExists($constraint = $this->prefix('assigned_terms').'_term_id_foreign')) {
        $this->db->query("
            ALTER TABLE {$this->table('assigned_terms')}
                ADD CONSTRAINT {$constraint}
            FOREIGN KEY (term_id)
            REFERENCES {$this->db->terms} (term_id)
            ON DELETE CASCADE
        ");
    }
    }

    /**
     * @return void
     */
    public function addReviewsTableConstraints()
    {
        if (!$this->tableConstraintExists($constraint = $this->prefix('assigned_posts').'_review_id_foreign')) {
        $this->db->query("
            ALTER TABLE {$this->table('ratings')}
                ADD CONSTRAINT {$constraint}
            FOREIGN KEY (review_id)
            REFERENCES {$this->db->posts} (ID)
            ON DELETE CASCADE
        ");
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
        dbDelta("CREATE TABLE {$this->table('assigned_posts')} (
            rating_id bigint(20) unsigned NOT NULL,
            post_id bigint(20) unsigned NOT NULL,
            is_published tinyint(1) NOT NULL DEFAULT '1',
            UNIQUE KEY {$this->prefix('assigned_posts')}_rating_id_post_id_unique (rating_id,post_id)
        ) {$this->db->get_charset_collate()};");
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
        dbDelta("CREATE TABLE {$this->table('assigned_terms')} (
            rating_id bigint(20) unsigned NOT NULL,
            term_id bigint(20) unsigned NOT NULL,
            UNIQUE KEY {$this->prefix('assigned_terms')}_rating_id_term_id_unique (rating_id,term_id)
        ) {$this->db->get_charset_collate()};");
        return true;
    }

    /**
     * @return bool
     */
    public function createRatingTable()
    {
        if ($this->tableExists('ratings')) {
            return false;
        }
        dbDelta("CREATE TABLE {$this->table('ratings')} (
            ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            review_id bigint(20) unsigned NOT NULL,
            rating int(11) NOT NULL DEFAULT '0',
            type varchar(20) DEFAULT 'local',
            is_approved tinyint(1) NOT NULL DEFAULT '0',
            is_pinned tinyint(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (ID),
            UNIQUE KEY {$this->prefix('ratings')}_review_id_unique (review_id),
            KEY {$this->prefix('ratings')}_rating_type_is_pinned_index (rating,type,is_pinned)
        ) {$this->db->get_charset_collate()};");
        return true;
    }

    /**
     * @return void
     */
    public function createTables()
    {
        $this->createAssignedPostsTable();
        $this->createAssignedTermsTable();
        $this->createRatingTable();
    }

    /**
     * @return string
     */
    public function prefix($table)
    {
        return glsr()->prefix.$table;
    }

    /**
     * @return string
     */
    public function table($table)
    {
        return glsr(Query::class)->getTable($table);
    }

    /**
     * @return bool
     */
    public function tableExists($table)
    {
        return !empty($this->db->get_var("SHOW TABLES LIKE '{$this->table($table)}'"));
    }

    /**
     * @return bool
     */
    public function tableConstraintExists($constraint)
    {
        return $this->db->query(
            $this->db->prepare("SELECT * FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_NAME = '%s'", $constraint)
        );
    }
}
