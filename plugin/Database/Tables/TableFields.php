<?php

namespace GeminiLabs\SiteReviews\Database\Tables;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;

class TableFields extends AbstractTable
{
    public $name = 'fields';

    public function addForeignConstraints(): void
    {
        glsr(Database::class)->deleteInvalidFields();
        $this->addForeignConstraint('rating_id', $this->table('ratings'), 'ID');
    }

    public function dropForeignConstraints(): void
    {
        $this->dropForeignConstraint('rating_id', $this->table('ratings'));
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
                rating_id bigint(20) unsigned NOT NULL,
                field_name varchar(255) NOT NULL,
                field_value longtext DEFAULT NULL,
                PRIMARY KEY  (ID),
                KEY glsr_fields_rating_id_index (rating_id),
                KEY glsr_fields_field_name_index (field_name(191))
            ) ENGINE=InnoDB {$this->db->get_charset_collate()};
        ");
    }
}
