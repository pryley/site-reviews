<?php

namespace GeminiLabs\SiteReviews\Database\Tables;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Helpers\Str;

abstract class AbstractTable
{
    public string $database;
    public \wpdb $db;
    public string $name = '';
    public string $prefix;
    public string $tablename;

    public function __construct()
    {
        require_once ABSPATH.'wp-admin/includes/upgrade.php'; // used for dbDelta()
        global $wpdb;
        $this->database = $wpdb->dbname ?: \DB_NAME;
        $this->db = $wpdb;
        $this->prefix = $wpdb->get_blog_prefix();
        $this->tablename = $wpdb->get_blog_prefix().glsr()->prefix.$this->name;
    }

    public function addForeignConstraint(string $column, string $foreignTable, string $foreignColumn): bool
    {
        if (!glsr(Tables::class)->isInnodb($foreignTable)) {
            return false;
        }
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

    public function drop(): bool
    {
        if ($this->exists()) {
            $this->dropForeignConstraints();
            return (bool) glsr(Database::class)->dbQuery("DROP TABLE IF EXISTS {$this->tablename}");
        }
        return false;
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

    public function empty(): bool
    {
        if ($this->exists()) {
            return (bool) glsr(Database::class)->dbQuery("TRUNCATE TABLE {$this->tablename}");
        }
        return false;
    }

    public function exists(): bool
    {
        return glsr(Tables::class)->tableExists($this->tablename);
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
        if (!glsr(Tables::class)->tableExists($foreignTable)) {
            return false;
        }
        if (!glsr(Tables::class)->isInnodb($foreignTable)) {
            return false;
        }
        $constraints = $this->db->get_col("
            SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = '{$this->database}'
        ");
        return in_array($constraint, $constraints);
    }

    public function name(bool $prefixName = false): string
    {
        if (!$prefixName) {
            return $this->name;
        }
        return Str::prefix($this->name, glsr()->prefix);
    }

    abstract public function removeInvalidRows(): void;

    /**
     * WordPress codex says there must be two spaces between PRIMARY KEY and the key definition.
     *
     * @see https://codex.wordpress.org/Creating_Tables_with_Plugins
     */
    abstract public function structure(): string;

    public function table(string $name = ''): string
    {
        return glsr(Tables::class)->table($name ?: $this->name);
    }
}
