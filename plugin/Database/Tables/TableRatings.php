<?php

namespace GeminiLabs\SiteReviews\Database\Tables;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;

class TableRatings extends AbstractTable
{
    public $name = 'ratings';

    public function addForeignConstraints(): void
    {
        glsr(Database::class)->deleteInvalidReviews();
        $this->addForeignConstraint('review_id', $this->table('posts'), 'ID');
    }

    public function dropForeignConstraints(): void
    {
        $this->dropForeignConstraint('review_id', $this->table('posts'));
    }

    /**
     * WordPress codex says there must be two spaces between PRIMARY KEY and the key definition.
     * @see https://codex.wordpress.org/Creating_Tables_with_Plugins
     */
    public function structure(): string
    {
        return glsr(Query::class)->sql("
            CREATE TABLE {$this->tablename} (
                ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                review_id bigint(20) unsigned NOT NULL,
                rating int(11) NOT NULL DEFAULT '0',
                type varchar(20) DEFAULT 'local',
                is_approved tinyint(1) NOT NULL DEFAULT '0',
                is_pinned tinyint(1) NOT NULL DEFAULT '0',
                is_verified tinyint(1) NOT NULL DEFAULT '0',
                name varchar(250) DEFAULT NULL,
                email varchar(100) DEFAULT NULL,
                avatar varchar(200) DEFAULT NULL,
                ip_address varchar(100) DEFAULT NULL,
                url varchar(250) DEFAULT NULL,
                terms tinyint(1) NOT NULL DEFAULT '1',
                score int(11) NOT NULL DEFAULT '0',
                PRIMARY KEY  (ID),
                UNIQUE KEY glsr_ratings_review_id_unique (review_id),
                KEY glsr_ratings_rating_type_is_pinned_index (rating,type,is_pinned)
            ) ENGINE=InnoDB {$this->db->get_charset_collate()};
        ");
    }
}
