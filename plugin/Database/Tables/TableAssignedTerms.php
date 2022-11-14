<?php

namespace GeminiLabs\SiteReviews\Database\Tables;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;

class TableAssignedTerms extends AbstractTable
{
    public $name = 'assigned_terms';

    public function addForeignConstraints(): void
    {
        glsr(Database::class)->deleteInvalidTermAssignments();
        $this->addForeignConstraint('rating_id', $this->table('ratings'), 'ID');
        $this->addForeignConstraint('term_id', $this->table('terms'), 'term_id');
    }

    public function dropForeignConstraints(): void
    {
        $this->dropForeignConstraint('rating_id', $this->table('ratings'));
        $this->dropForeignConstraint('term_id', $this->table('terms'));
    }

    /**
     * WordPress codex says there must be two spaces between PRIMARY KEY and the key definition.
     * @see https://codex.wordpress.org/Creating_Tables_with_Plugins
     */
    public function structure(): string
    {
        return glsr(Query::class)->sql("
            CREATE TABLE {$this->tablename} (
                rating_id bigint(20) unsigned NOT NULL,
                term_id bigint(20) unsigned NOT NULL,
                PRIMARY KEY  (rating_id,term_id)
            ) ENGINE=InnoDB {$this->db->get_charset_collate()};
        ");
    }
}
