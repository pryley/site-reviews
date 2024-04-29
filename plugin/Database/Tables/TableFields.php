<?php

namespace GeminiLabs\SiteReviews\Database\Tables;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;

class TableFields extends AbstractTable
{
    public string $name = 'fields';

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
        // Indexes have a maximum size of 767 bytes and utf8mb4 uses 4 bytes per character.
        $maxIndexLength = 191; // floor(767/4) = 191 characters
        return glsr(Query::class)->sql("
            CREATE TABLE {$this->tablename} (
                ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                rating_id bigint(20) unsigned NOT NULL,
                field_name varchar(255) NOT NULL,
                field_value longtext DEFAULT NULL,
                PRIMARY KEY  (ID),
                KEY glsr_fields_rating_id_index (rating_id),
                KEY glsr_fields_field_name_index (field_name($maxIndexLength))
            ) ENGINE=InnoDB {$this->db->get_charset_collate()};
        ");
    }
}
