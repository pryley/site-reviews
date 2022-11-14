<?php

namespace GeminiLabs\SiteReviews\Database\Tables;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;

class TableAssignedPosts extends AbstractTable
{
    public $name = 'assigned_posts';

    public function addForeignConstraints(): void
    {
        glsr(Database::class)->deleteInvalidPostAssignments();
        $this->addForeignConstraint('rating_id', $this->table('ratings'), 'ID');
        $this->addForeignConstraint('post_id', $this->table('posts'), 'ID');
    }

    public function dropForeignConstraints(): void
    {
        $this->dropForeignConstraint('rating_id', $this->table('ratings'));
        $this->dropForeignConstraint('post_id', $this->table('posts'));
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
                post_id bigint(20) unsigned NOT NULL,
                is_published tinyint(1) NOT NULL DEFAULT '1',
                PRIMARY KEY  (rating_id,post_id)
            ) ENGINE=InnoDB {$this->db->get_charset_collate()};
        ");
    }
}
