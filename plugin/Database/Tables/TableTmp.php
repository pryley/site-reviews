<?php

namespace GeminiLabs\SiteReviews\Database\Tables;

use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;

class TableTmp extends AbstractTable
{
    public string $name = 'tmp';

    public function addForeignConstraints(): void
    {
    }

    public function dropForeignConstraints(): void
    {
    }

    public function removeInvalidRows(): void
    {
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
                type varchar(20) NOT NULL,
                data longtext NOT NULL,
                PRIMARY KEY  (ID),
                KEY type (type)
            ) ENGINE=InnoDB {$this->db->get_charset_collate()};
        ");
    }
}
