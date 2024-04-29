<?php

namespace GeminiLabs\SiteReviews\Database\Tables;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;

class TableAssignedUsers extends AbstractTable
{
    public string $name = 'assigned_users';

    public function addForeignConstraints(): void
    {
        $this->addForeignConstraint('rating_id', $this->table('ratings'), 'ID');
        $this->addForeignConstraint('user_id', $this->table('users'), 'ID');
    }

    public function dropForeignConstraints(): void
    {
        $this->dropForeignConstraint('rating_id', $this->table('ratings'));
        $this->dropForeignConstraint('user_id', $this->table('users'));
    }

    public function removeInvalidRows(): void
    {
        glsr(Database::class)->dbSafeQuery(
            glsr(Query::class)->sql("
                DELETE t
                FROM {$this->tablename} AS t
                LEFT JOIN table|ratings AS r ON (r.ID = t.rating_id)
                LEFT JOIN table|users AS u ON (u.ID = t.user_id)
                WHERE (r.ID IS NULL OR u.ID IS NULL)
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
                rating_id bigint(20) unsigned NOT NULL,
                user_id bigint(20) unsigned NOT NULL,
                PRIMARY KEY  (rating_id,user_id)
            ) ENGINE=InnoDB {$this->db->get_charset_collate()};
        ");
    }
}
