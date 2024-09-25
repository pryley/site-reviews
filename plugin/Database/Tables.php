<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedPosts;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedTerms;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedUsers;
use GeminiLabs\SiteReviews\Database\Tables\TableFields;
use GeminiLabs\SiteReviews\Database\Tables\TableRatings;
use GeminiLabs\SiteReviews\Database\Tables\TableTmp;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

class Tables
{
    public string $database;
    public \wpdb $db;
    public string $engine;
    public string $prefix;
    public array $tables;

    public function __construct()
    {
        global $wpdb;
        $this->database = $wpdb->dbname ?: \DB_NAME;
        $this->db = $wpdb;
        $this->engine = defined('DB_ENGINE') && 'sqlite' === \DB_ENGINE ? 'sqlite' : 'mysql';
        $this->prefix = $wpdb->get_blog_prefix();
        $this->tables = wp_parse_args($this->customTables(), $wpdb->tables());
    }

    public function addForeignConstraints(): void
    {
        foreach ($this->tables() as $table) {
            glsr($table)->addForeignConstraints();
        }
    }

    public function columnExists(string $table, string $column): bool
    {
        if ($this->isSqlite()) {
            $result = $this->db->get_col(
                glsr(Query::class)->sql("SHOW COLUMNS FROM table|{$table}")
            );
            return in_array($column, $result);
        }
        $result = $this->db->query(
            glsr(Query::class)->sql("SHOW COLUMNS FROM table|{$table} LIKE %s", $column)
        );
        return Cast::toBool($result);
    }

    public function convertTableEngine(string $table): int
    {
        if (!$this->isMyisam($table)) {
            return -1;
        }
        $table = $this->table($table);
        $result = glsr(Database::class)->dbQuery(
            glsr(Query::class)->sql("ALTER TABLE {$table} ENGINE = InnoDB;")
        );
        if (true === $result) {
            update_option(glsr()->prefix."engine_{$table}", 'innodb');
            $this->addForeignConstraints(); // apply InnoDB constraints
            return 1;
        }
        return 0;
    }

    public function createTables(): void
    {
        array_map(fn ($table) => glsr($table)->create(), $this->tables());
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
        if ($this->isSqlite()) {
            return;
        }
        $constraints = $this->db->get_results("
            SELECT CONSTRAINT_NAME, TABLE_NAME
            FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = '{$this->database}'
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
        return 'innodb' === $this->tableEngine($table);
    }

    public function isMyisam(string $table): bool
    {
        return 'myisam' === $this->tableEngine($table);
    }

    public function isSqlite(): bool
    {
        return 'sqlite' === $this->engine;
    }

    public function table(string $name = '', bool $logError = true): string
    {
        if (in_array($name, $this->tables)) {
            return $name;
        }
        if (array_key_exists($name, $this->tables)) {
            return $this->tables[$name];
        }
        if ($logError) {
            glsr_log()->error("The [{$name}] table was not found.");
        }
        return $name; // @todo maybe throw an exception here instead?
    }

    public function tableEngine(string $table): string
    {
        if (defined('GLSR_UNIT_TESTS') || $this->isSqlite()) {
            return '';
        }
        $table = $this->table($table);
        $option = sprintf('%sengine_%s', glsr()->prefix, $table);
        if (empty($engine = get_option($option))) {
            $sql = "
                SELECT ENGINE
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s
            ";
            $engine = $this->db->get_var($this->db->prepare($sql, $this->database, $table));
            if (empty($engine)) {
                glsr_log()->warning("DB Table Engine: The {$table} table does not exist in {$this->database}.");
                return '';
            }
            $engine = strtolower($engine);
            update_option($option, $engine);
        }
        return $engine;
    }

    public function tableEngines(bool $removePrefix = false): array
    {
        if ($this->isSqlite()) {
            return [];
        }
        $results = $this->db->get_results("
            SELECT TABLE_NAME as table_name, ENGINE as engine
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = '{$this->database}'
            AND TABLE_NAME IN ('{$this->db->options}','{$this->db->posts}','{$this->db->terms}','{$this->db->users}')
        ");
        $engines = [];
        foreach ($results as $result) {
            if (!array_key_exists($result->engine, $engines)) {
                $engines[$result->engine] = [];
            }
            if ($removePrefix) {
                $result->table_name = Str::removePrefix((string) $result->table_name, $this->prefix);
            }
            $engines[$result->engine][] = $result->table_name;
        }
        return $engines;
    }

    public function tableExists(string $table): bool
    {
        $tablename = $this->table($table, false);
        $query = $this->db->prepare('SHOW TABLES LIKE %s', $this->db->esc_like($tablename));
        return !empty($this->db->get_var($query));
    }

    public function tables(): array
    {
        return glsr()->filterArray('database/tables', [ // order is intentional
            TableAssignedPosts::class,
            TableAssignedTerms::class,
            TableAssignedUsers::class,
            // TableFields::class, // @todo add the fields table
            TableRatings::class,
            TableTmp::class,
        ]);
    }

    public function tablesExist(): bool
    {
        $prefix = $this->db->esc_like($this->prefix.glsr()->prefix);
        $tables = $this->db->get_col(
            $this->db->prepare("SHOW TABLES LIKE %s", "{$prefix}%")
        );
        foreach ($this->tables() as $table) {
            if (!in_array(glsr($table)->tablename, $tables)) {
                return false;
            }
        }
        return true;
    }
}
