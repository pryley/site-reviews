<?php

namespace GeminiLabs\SiteReviews\Database\Tables;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Helpers\Str;

abstract class AbstractTable
{
    /**
     * @var \wpdb
     */
    public $db;
    /**
     * @var string
     */
    public $dbname;
    /**
     * @var string
     */
    public $dbprefix;
    /**
     * @var string
     */
    public $name = '';
    /**
     * @var string
     */
    public $tablename = '';

    abstract public function addForeignConstraints(): void;

    abstract public function dropForeignConstraints(): void;

    /**
     * WordPress codex says there must be two spaces between PRIMARY KEY and the key definition.
     * @see https://codex.wordpress.org/Creating_Tables_with_Plugins
     */
    abstract public function structure(): string;

    public function __construct()
    {
        require_once ABSPATH.'wp-admin/includes/upgrade.php'; // used for dbDelta()
        global $wpdb;
        $this->db = $wpdb;
        $this->dbname = $wpdb->dbname;
        $this->dbprefix = $wpdb->get_blog_prefix();
        $this->tablename = sprintf('%s%s%s', $this->dbprefix, glsr()->prefix, $this->name);
    }

    public function addForeignConstraint(string $column, string $foreignTable, string $foreignColumn): bool
    {
        $constraint = $this->foreignConstraint($column);
        if ($this->foreignConstraintExists($constraint, $foreignTable)) {
            return false;
        }
        $this->deleteOrphanedRows($column, $foreignTable, $foreignColumn);
        return (bool) glsr(Database::class)->dbQuery(glsr(Query::class)->sql("
            ALTER TABLE {$this->tablename}
            ADD CONSTRAINT {$constraint}
            FOREIGN KEY ({$column})
            REFERENCES {$foreignTable} ({$foreignColumn})
            ON DELETE CASCADE
        "));
    }

    public function create(): bool
    {
        if ($this->exists()) {
            return false;
        }
        dbDelta($this->structure());
        glsr(Database::class)->logErrors();
        return true;
    }

    public function deleteOrphanedRows(string $column, string $foreignTable, string $foreignColumn): void
    {
        glsr(Database::class)->dbQuery(glsr(Query::class)->sql("
            DELETE t
            FROM {$this->tablename} AS t
            LEFT JOIN {$foreignTable} AS ft ON t.{$column} = ft.{$foreignColumn}
            WHERE ft.{$foreignColumn} IS NULL
        "));
    }

    public function dropForeignConstraint(string $column, string $foreignTable): bool
    {
        $constraint = $this->foreignConstraint($column);
        if (!$this->foreignConstraintExists($constraint, $foreignTable)) {
            return false;
        }
        return (bool) glsr(Database::class)->dbQuery("
            ALTER TABLE {$this->tablename} DROP FOREIGN KEY {$constraint};
        ");
    }

    public function exists(): bool
    {
        $query = $this->db->prepare('SHOW TABLES LIKE %s', $this->db->esc_like($this->tablename));
        return $this->tablename === $this->db->get_var($query);
    }

    public function foreignConstraint(string $column): string
    {
        $constraint = Str::prefix($column, glsr()->prefix.$this->name.'_');
        $constraint = Str::suffix($constraint, '_foreign');
        if (is_multisite() && $this->db->blogid > 1) {
            return Str::suffix($constraint, '_'.$this->db->blogid);
        }
        return $constraint;
    }

    public function foreignConstraintExists(string $constraint, string $foreignTable = ''): bool
    {
        if (!empty($foreignTable) && !glsr(Tables::class)->isInnodb($foreignTable)) {
            glsr_log()->debug("Cannot check for a foreign constraint because [{$foreignTable}] does not use the InnoDB engine.");
            return true; // we cannot create foreign contraints on MyISAM tables
        }
        $constraints = $this->db->get_col("
            SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = '{$this->dbname}'
        ");
        return in_array($constraint, $constraints);
    }

    public function table(string $name = ''): string
    {
        if (empty($name)) {
            $name = $this->name;
        }
        return glsr(Tables::class)->table($name);
    }
}
