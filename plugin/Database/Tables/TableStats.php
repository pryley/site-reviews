<?php

namespace GeminiLabs\SiteReviews\Database\Tables;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables\AbstractTable;

class TableStats extends AbstractTable
{
    public string $name = 'stats';

    public function addForeignConstraints(): void
    {
        $this->addForeignConstraint('rating_id', $this->table('ratings'), 'ID');
    }

    public function dropForeignConstraints(): void
    {
        $this->dropForeignConstraint('rating_id', $this->table('ratings'));
    }

    public function removeInvalidRows(): void
    {
        glsr(Database::class)->dbSafeQuery(
            glsr(Query::class)->sql("
                DELETE t
                FROM {$this->tablename} AS t
                LEFT JOIN table|ratings AS r ON (r.ID = t.rating_id)
                WHERE r.ID IS NULL
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
                rating_id bigint(20) unsigned NOT NULL,
                continent varchar(10) NOT NULL,
                country varchar(10) NOT NULL,
                region varchar(10) NOT NULL,
                city varchar(250) NOT NULL,
                PRIMARY KEY  (ID),
                UNIQUE KEY glsr_stats_rating_id_unique (rating_id)
            ) ENGINE=InnoDB {$this->db->get_charset_collate()};
        ");
    }
}
