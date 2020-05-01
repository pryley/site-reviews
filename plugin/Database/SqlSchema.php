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
     * @return bool
     */
    public function createAssignedPostsTable()
    {
        if ($this->tableExists('assigned_posts')) {
            return false;
        }
        $prefix = glsr()->prefix.'assigned_posts';
        $sql = "CREATE TABLE {$this->table('assigned_posts')} (
            rating_id bigint(20) unsigned NOT NULL,
            post_id bigint(20) unsigned NOT NULL,
            is_published tinyint(1) NOT NULL DEFAULT '1',
            UNIQUE KEY {$prefix}_rating_id_post_id_unique (rating_id,post_id),
            CONSTRAINT {$prefix}_rating_id_foreign
                FOREIGN KEY (rating_id)
                REFERENCES {$this->table('ratings')} (ID)
                ON DELETE CASCADE,
            CONSTRAINT {$prefix}_post_id_foreign
                FOREIGN KEY (post_id)
                REFERENCES {$this->db->posts} (ID)
                ON DELETE CASCADE
        ) {$this->db->get_charset_collate()};";
        dbDelta($sql);
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
        $prefix = glsr()->prefix.'assigned_terms';
        $sql = "CREATE TABLE {$this->table('assigned_terms')} (
            rating_id bigint(20) unsigned NOT NULL,
            term_id bigint(20) unsigned NOT NULL,
            UNIQUE KEY {$prefix}_rating_id_term_id_unique (rating_id,term_id),
            CONSTRAINT {$prefix}_rating_id_foreign
                FOREIGN KEY (rating_id)
                REFERENCES {$this->table('ratings')} (ID)
                ON DELETE CASCADE,
            CONSTRAINT {$prefix}_term_id_foreign
                FOREIGN KEY (term_id)
                REFERENCES {$this->db->terms} (term_id)
                ON DELETE CASCADE
        ) {$this->db->get_charset_collate()};";
        dbDelta($sql);
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
        $prefix = glsr()->prefix.'ratings';
        $sql = "CREATE TABLE {$this->table('ratings')} (
            ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            review_id bigint(20) unsigned NOT NULL,
            rating int(11) NOT NULL DEFAULT '0',
            type varchar(20) DEFAULT 'local',
            is_approved tinyint(1) NOT NULL DEFAULT '0',
            is_pinned tinyint(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (ID),
            UNIQUE KEY {$prefix}_review_id_unique (review_id),
            KEY {$prefix}_rating_type_is_pinned_index (rating,type,is_pinned),
            CONSTRAINT {$prefix}_review_id_foreign
                FOREIGN KEY (review_id)
                REFERENCES {$this->db->posts} (ID)
                ON DELETE CASCADE
        ) {$this->db->get_charset_collate()};";
        dbDelta($sql);
        return true;
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
}
