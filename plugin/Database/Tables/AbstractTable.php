<?php

namespace GeminiLabs\SiteReviews\Database\Tables;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Helpers\Str;

abstract class AbstractTable
{
    public \wpdb $db;
    public string $dbname;
    public string $dbprefix;
    public string $name = '';
    public string $tablename;

    public function __construct()
    {
        require_once ABSPATH.'wp-admin/includes/upgrade.php'; // used for dbDelta()
        global $wpdb;
        $this->db = $wpdb;
        $this->dbname = $wpdb->dbname;
        $this->dbprefix = $wpdb->get_blog_prefix();
        $this->tablename = $wpdb->get_blog_prefix().glsr()->prefix.$this->name;
    }

    public function addForeignConstraint(string $column, string $foreignTable, string $foreignColumn): bool
    {
        $constraint = $this->foreignConstraint($column);
        if ($this->foreignConstraintExists($constraint, $foreignTable)) {
            return false;
        }
        $this->removeInvalidRows();
        return (bool) glsr(Database::class)->dbQuery(glsr(Query::class)->sql("
            ALTER TABLE {$this->tablename}
            ADD CONSTRAINT {$constraint}
            FOREIGN KEY ({$column})
            REFERENCES {$foreignTable} ({$foreignColumn})
            ON DELETE CASCADE
        "));
    }

    abstract public function addForeignConstraints(): void;

    public function create(): bool
    {
        if ($this->exists()) {
            return false;
        }
        dbDelta($this->structure());
        glsr(Database::class)->logErrors();
        return true;
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

    abstract public function dropForeignConstraints(): void;

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
            return Str::suffix($constraint, "_{$this->db->blogid}");
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

    abstract public function removeInvalidRows(): void;

    /**
     * WordPress codex says there must be two spaces between PRIMARY KEY and the key definition.
     * @see https://codex.wordpress.org/Creating_Tables_with_Plugins
     */
    abstract public function structure(): string;

    public function table(string $name = ''): string
    {
        if (empty($name)) {
            $name = $this->name;
        }
        return glsr(Tables::class)->table($name);
    }
}
