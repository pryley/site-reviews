<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedPosts;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedTerms;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedUsers;
use GeminiLabs\SiteReviews\Database\Tables\TableFields;
use GeminiLabs\SiteReviews\Database\Tables\TableRatings;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

class Tables
{
    /**
     * @var \wpdb
     */
    protected $db;
    /**
     * @var string
     */
    protected $dbname;
    /**
     * @var string
     */
    protected $dbprefix;
    /**
     * @var array
     */
    protected $dbtables;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->dbname = $wpdb->dbname;
        $this->dbprefix = $wpdb->get_blog_prefix();
        $this->dbtables = wp_parse_args($this->customTables(), $wpdb->tables());
    }

    public function addForeignConstraints(): void
    {
        foreach ($this->tables() as $table) {
            glsr($table)->addForeignConstraints();
        }
    }

    public function columnExists(string $table, string $column): bool
    {
        $result = glsr(Database::class)->dbQuery(
            glsr(Query::class)->sql("SHOW COLUMNS FROM {$this->table($table)} LIKE '{$column}'")
        );
        return Cast::toBool($result);
    }

    public function convertTableEngine(string $table): int
    {
        $table = $this->table($table);
        if (!$this->isMyisam($table)) {
            return -1;
        }
        $result = glsr(Database::class)->dbQuery(glsr(Query::class)->sql("
            ALTER TABLE {$this->dbname}.{$table} ENGINE = InnoDB;
        "));
        if (true === $result) {
            update_option(glsr()->prefix.'engine_'.$table, 'innodb');
            $this->addForeignConstraints(); // apply InnoDB constraints
            return 1;
        }
        return 0;
    }

    public function createTables(): void
    {
        foreach ($this->tables() as $table) {
            glsr($table)->create();
        }
    }

    public function customTables(): array
    {
        $tables = [];
        foreach ($this->tables() as $table) {
            $tables[glsr($table)->name] = glsr($table)->tablename;
        }
        return $tables;
    }

    public function dropForeignConstraints(): void
    {
        $constraints = $this->db->get_results("
            SELECT CONSTRAINT_NAME, TABLE_NAME
            FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = '{$this->dbname}'
        ");
        foreach ($this->tables() as $table) {
            $tablename = glsr($table)->tablename;
            foreach ($constraints as $constraint) {
                if ($tablename !== $constraint->TABLE_NAME) {
                    continue;
                }
                $this->db->query("
                    ALTER TABLE {$constraint->TABLE_NAME} DROP FOREIGN KEY {$constraint->CONSTRAINT_NAME};
                "); // true if constraint exists, false if it doesn't exist
            }
        }
    }

    public function isInnodb(string $table): bool
    {
        if (defined('GLSR_UNIT_TESTS')) {
            return false;
        }
        return 'innodb' === $this->tableEngine($table);
    }

    public function isMyisam(string $table): bool
    {
        if (defined('GLSR_UNIT_TESTS')) {
            return true;
        }
        return 'myisam' === $this->tableEngine($table);
    }

    public function table(string $name = ''): string
    {
        if (in_array($name, $this->dbtables)) {
            return $name;
        }
        if (array_key_exists($name, $this->dbtables)) {
            return $this->dbtables[$name];
        }
        glsr_log()->error("The [{$name}] table was not found.");
        return $name; // @todo maybe throw an exception here instead?
    }

    public function tableEngine(string $table): string
    {
        $table = $this->table($table);
        $option = sprintf('%sengine_%s', glsr()->prefix, $table);
        $engine = get_option($option);
        if (empty($engine)) {
            $engine = $this->db->get_var("
                SELECT ENGINE
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = '{$this->dbname}' AND TABLE_NAME = '{$table}'
            ");
            if (empty($engine)) {
                glsr_log()->warning(sprintf('DB Table Engine: The %s table does not exist in %s.', $table, $this->dbname));
                return '';
            }
            $engine = strtolower($engine);
            update_option($option, $engine);
        }
        return $engine;
    }

    public function tableEngines(bool $removeDBPrefix = false): array
    {
        $results = $this->db->get_results("
            SELECT TABLE_NAME, ENGINE
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = '{$this->dbname}'
            AND TABLE_NAME IN ('{$this->db->options}','{$this->db->posts}','{$this->db->terms}','{$this->db->users}')
        ");
        $engines = [];
        foreach ($results as $result) {
            if (!array_key_exists($result->ENGINE, $engines)) {
                $engines[$result->ENGINE] = [];
            }
            if ($removeDBPrefix) {
                $result->TABLE_NAME = Str::removePrefix($result->TABLE_NAME, $this->dbprefix);
            }
            $engines[$result->ENGINE][] = $result->TABLE_NAME;
        }
        return $engines;
    }

    public function tables(): array
    {
        // @todo add the fields table
        return [ // order is intentional
            TableAssignedPosts::class,
            TableAssignedTerms::class,
            TableAssignedUsers::class,
            // TableFields::class,
            TableRatings::class,
        ];
    }

    public function tablesExist(): bool
    {
        $prefix = $this->dbprefix.glsr()->prefix;
        $tables = $this->db->get_col(
            $this->db->prepare('SHOW TABLES LIKE %s', $this->db->esc_like($prefix).'%')
        );
        foreach ($this->tables() as $table) {
            if (!in_array(glsr($table)->tablename, $tables)) {
                return false;
            }
        }
        return true;
    }
}
