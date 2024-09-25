<?php

namespace GeminiLabs\SiteReviews\Database\Tables;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;

class TableRatings extends AbstractTable
{
    public string $name = 'ratings';

    public function addForeignConstraints(): void
    {
        $this->addForeignConstraint('review_id', $this->table('posts'), 'ID');
    }

    public function dropForeignConstraints(): void
    {
        $this->dropForeignConstraint('review_id', $this->table('posts'));
    }

    public function removeInvalidRows(): void
    {
        $type = glsr()->post_type;
        glsr(Database::class)->dbSafeQuery(
            glsr(Query::class)->sql("
                DELETE t
                FROM {$this->tablename} AS t
                LEFT JOIN table|posts AS p ON (p.ID = t.review_id)
                WHERE (p.post_type IS NULL OR p.post_type != '{$type}')
            ")
        );
    }

    /**
     * WordPress codex says there must be two spaces between PRIMARY KEY and the key definition.
     *
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
                is_flagged tinyint(1) NOT NULL DEFAULT '0',
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
                KEY glsr_ratings_rating_type_is_pinned_index (rating,type,is_pinned),
                KEY glsr_ratings_rating_type_is_approved_index (rating,type,is_approved),
                KEY glsr_ratings_is_flagged_index (is_flagged)
            ) ENGINE=InnoDB {$this->db->get_charset_collate()};
        ");
    }
}
