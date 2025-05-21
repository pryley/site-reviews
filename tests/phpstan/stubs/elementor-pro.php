<?php

namespace ElementorPro\Core\Compatibility {
    class Compatibility
    {
        public static function register_actions()
        {
        }
        public static function on_init()
        {
        }
    }
}
namespace ElementorPro\Core\Database {
    abstract class Base_Migration
    {
        /*
         * @see https://github.com/WordPress/WordPress/blob/d2694aa46647af48d1bcaff48a4f6cac7f5cf470/wp-admin/includes/schema.php#L49
         */
        const MAX_INDEX_LENGTH = 191;
        /**
         * @var \wpdb
         */
        protected $wpdb;
        /**
         * @param \wpdb|null $wpdb_instance
         */
        public function __construct(\wpdb $wpdb_instance = null)
        {
        }
        /**
         * Runs when upgrading the database
         *
         * @return void
         */
        public abstract function up();
        /**
         * Runs when downgrading the database.
         *
         * @return void
         */
        public abstract function down();
        /**
         * A util to run SQL for creating tables.
         *
         * @param       $table_name
         * @param array $columns
         */
        protected function create_table($table_name, array $columns)
        {
        }
        /**
         * Add columns.
         *
         * @param       $table_name
         * @param array $columns
         */
        protected function add_columns($table_name, array $columns)
        {
        }
        /**
         * Drop columns
         *
         * @param       $table_name
         * @param array $columns
         */
        protected function drop_columns($table_name, array $columns)
        {
        }
        /**
         * A util to run SQL for dropping tables.
         *
         * @param $table_name
         */
        protected function drop_table($table_name)
        {
        }
        /**
         * A util to run SQL for creating indexes.
         *
         * @param       $table_name
         * @param array $column_names
         */
        protected function create_indexes($table_name, array $column_names)
        {
        }
        /**
         * @param $table_name
         *
         * @return array
         */
        protected function get_column_definition($table_name)
        {
        }
        /**
         * Runs global dbDelta function (wrapped into method to allowing mock for testing).
         *
         * @param $query
         *
         * @return array
         */
        protected function run_db_delta($query)
        {
        }
    }
    abstract class Base_Database_Updater
    {
        /**
         * Run all the 'up' method of the migrations classes if needed, and update the db version.
         *
         * @param bool $force When passing true, it ignores the current version and run all the up migrations.
         */
        public function up($force = false)
        {
        }
        /**
         * Run all the 'down' method of the migrations classes if can, and update the db version.
         *
         * @param bool $force When passing true, it ignores the current version and run all the down migrations.
         */
        public function down($force = false)
        {
        }
        /**
         * Register hooks to activate the migrations.
         */
        public function register()
        {
        }
        /**
         * Update the version in the users DB.
         *
         * @param $version
         */
        protected function update_db_version_option($version)
        {
        }
        /**
         * Get the version that already installed.
         *
         * @return int
         */
        protected function get_installed_version()
        {
        }
        /**
         * Get all migrations inside a Collection.
         *
         * @return Collection
         */
        protected function get_collected_migrations()
        {
        }
        /**
         * The most updated version of the DB.
         *
         * @return numeric
         */
        protected abstract function get_db_version();
        /**
         * The name of the option that saves the current user DB version.
         *
         * @return string
         */
        protected abstract function get_db_version_option_name();
        /**
         * Array of migration classes.
         *
         * @return Base_Migration[]
         */
        protected abstract function get_migrations();
    }
    class Query_Builder
    {
        // Relation types.
        const RELATION_AND = 'AND';
        const RELATION_OR = 'OR';
        // Column types.
        const COLUMN_BASIC = 'basic';
        // Regular column - will be automatically escaped.
        const COLUMN_RAW = 'raw';
        // Raw column - SHOULD BE ESCAPED BY THE DEVELOPER.
        const COLUMN_SUB_SELECT = 'sub-select';
        // Sub select - will be automatically bind & escaped.
        const COLUMN_COUNT = 'count';
        // Count - wrap the column with a COUNT function.
        // WHERE types.
        const WHERE_BASIC = 'basic';
        const WHERE_NULL = 'null';
        const WHERE_COLUMN = 'column';
        const WHERE_IN = 'in';
        const WHERE_NOT_IN = 'not-in';
        const WHERE_SUB = 'sub';
        const WHERE_NESTED = 'nested';
        const WHERE_EXISTS = 'exists';
        const WHERE_NOT_EXISTS = 'not-exists';
        // HAVING types.
        const HAVING_RAW = 'raw';
        /**
         * MySQL connection.
         *
         * @var \wpdb
         */
        protected $connection;
        /**
         * Current query value binding.
         *
         * @var array[]
         */
        protected $bindings = ['select' => [], 'join' => [], 'where' => []];
        /**
         * Current query columns to return.
         *
         * @var array
         */
        protected $columns = [['type' => self::COLUMN_RAW, 'column' => '*', 'as' => null]];
        /**
         * Table to select from.
         *
         * @var array
         */
        protected $from = [];
        /**
         * Current query joins.
         *
         * @var array
         */
        protected $joins = [];
        /**
         * The where constraints for the query.
         *
         * @var array
         */
        protected $wheres = [];
        /**
         * The having constraints for the query.
         *
         * @var array
         */
        protected $havings = [];
        /**
         * The groupings for the query.
         *
         * @var array
         */
        protected $groups = [];
        /**
         * The orderings for the query.
         *
         * @var array
         */
        protected $orders = [];
        /**
         * The maximum number of records to return.
         *
         * @var int
         */
        protected $limit;
        /**
         * The number of records to skip.
         *
         * @var int
         */
        protected $offset;
        /**
         * Aggregations.
         *
         * @var array
         */
        protected $with = [];
        /**
         * Query_Builder constructor.
         *
         * @param \wpdb|null $connection - The Mysql connection instance to use.
         */
        public function __construct(\wpdb $connection = null)
        {
        }
        /**
         * Add columns to the SELECT clause.
         *
         * @param string[] $columns - Array of column names.
         * @param string $type - Select type.
         *
         * @return $this
         */
        public function select($columns = ['*'], $type = self::COLUMN_BASIC)
        {
        }
        /**
         * @shortcut `$this->select()`.
         */
        public function select_raw($raw_columns = ['*'])
        {
        }
        /**
         * Add a `(SELECT ...) AS alias` statement to the SELECT clause.
         *
         * @param callable $callback - Callback that gets a `Query_Builder` and modifies it.
         * @param string $as - Alias for the sub select.
         *
         * @return $this
         */
        public function add_sub_select(callable $callback, $as)
        {
        }
        /**
         * Add a `COUNT({col}) AS {alias}` statement to the SELECT clause.
         *
         * @param $column_name
         * @param $as
         *
         * @return $this
         */
        public function add_count_select($column_name, $as = null)
        {
        }
        /**
         * Set the table to select from.
         *
         * @param string $table - Table name.
         * @param string|null $as - Table alias.
         *
         * @return $this
         */
        public function from($table, $as = null)
        {
        }
        /**
         * @shortcut $this->from()
         *
         * Used for readability with UPDATE / INSERT / DELETE statements.
         */
        public function table($table, $as = null)
        {
        }
        /**
         * Execute a query operation only on specific condition.
         * For example:
         *
         * $query->when( 1 === $a, function( Query_Builder $builder ) {
         *      // Runs if $a = 1.
         *      $builder->where( ... );
         * }, function( Query_Builder $builder ) {
         *      // Runs if $a != 1.
         *      $builder->where( ... );
         * } )
         *
         * @param mixed $condition - Condition to check.
         * @param callable $true_callback - Callback if the condition is truthy.
         * @param callable|null $false_callback - Callback if the condition is falsy. Optional.
         *
         * @return $this
         */
        public function when($condition, callable $true_callback, callable $false_callback = null)
        {
        }
        /**
         * Add a `WHERE` statement.
         *
         * @param string|callable $column - Column name to check.
         * @param string $operator - Statement operator.
         * @param string|callable $value - Value as string or callback.
         * @param string $and_or - Boolean relation, one of `and` / `or`.
         *
         * @return $this
         */
        public function where($column, $operator = null, $value = null, $and_or = self::RELATION_AND)
        {
        }
        /**
         * Add an `OR WHERE` statement.
         *
         * @shortcut $this->where().
         */
        public function or_where($column, $operator = null, $value = null)
        {
        }
        /**
         * @shortcut `$this->where()`.
         */
        public function where_null($column, $and_or = self::RELATION_AND)
        {
        }
        /**
         * @shortcut `$this->where_null()`.
         */
        public function or_where_null($column)
        {
        }
        /**
         * Add a `WHERE col1 = col2` statement.
         *
         * @param string $first - First column name to check.
         * @param string $operator - Statement operator.
         * @param string $second - Second column name to check.
         * @param string $and_or - Boolean relation, one of `and` / `or`.
         *
         * @return $this
         */
        public function where_column($first, $operator, $second, $and_or = self::RELATION_AND)
        {
        }
        /**
         * Add an `OR WHERE col1 = col2` statement.
         *
         * @shortcut $this->where_column().
         */
        public function or_where_column($first, $operator, $second)
        {
        }
        /**
         * Add a `WHERE IN()` statement.
         *
         * @param string $column - Column name to check.
         * @param string[]|callable $values - Array of values.
         * @param string $and_or - Boolean relation, one of `and` / `or`.
         * @param boolean $in - Whether it's `IN` or `NOT IN`.
         *
         * @return $this
         */
        public function where_in($column, $values, $and_or = self::RELATION_AND, $in = true)
        {
        }
        /**
         * Add an `OR WHERE IN()` statement.
         *
         * @shortcut $this->where_in().
         */
        public function or_where_in($column, $values)
        {
        }
        /**
         * Add a `WHERE NOT IN()` statement.
         *
         * @shortcut $this->where_in().
         */
        public function where_not_in($column, $values, $and_or = self::RELATION_AND)
        {
        }
        /**
         * Add an `OR WHERE NOT IN()` statement.
         *
         * @shortcut $this->where_in().
         */
        public function or_where_not_in($column, $values)
        {
        }
        /**
         * Add a `WHERE EXISTS()` statement.
         *
         * @param callable $callback - Callback that gets a `Query_Builder` and modifies it.
         * @param string $and_or - Boolean relation, one of `and` / `or`.
         * @param bool $exists - Whether to use `EXISTS` or `NOT EXISTS` statement.
         *
         * @return $this
         */
        public function where_exists(callable $callback, $and_or = self::RELATION_AND, $exists = true)
        {
        }
        /**
         * Add an `OR WHERE EXISTS()` statement.
         *
         * @shortcut $this->where_exists().
         */
        public function or_where_exists(callable $callback, $exists = true)
        {
        }
        /**
         * Add a `WHERE NOT EXISTS()` statement.
         *
         * @shortcut $this->where_exists().
         */
        public function where_not_exists(callable $callback, $and_or = self::RELATION_AND)
        {
        }
        /**
         * Add an `OR WHERE NOT EXISTS()` statement.
         *
         * @shortcut $this->where_exists().
         */
        public function or_where_not_exists(callable $callback)
        {
        }
        /**
         * Add a sub query.
         *
         * @param string $column - Column name to check.
         * @param string $operator - Statement operator.
         * @param callable $callback - Callback that gets a `Query_Builder` and modifies it.
         * @param string $and_or - Boolean relation, one of `and` / `or`.
         *
         * @return $this
         */
        public function where_sub($column, $operator, callable $callback, $and_or = self::RELATION_AND)
        {
        }
        /**
         * Add a nested `WHERE` query.
         *
         * @param callable $callback - Callback that gets a `Query_Builder` and modifies it.
         * @param string   $and_or - Boolean relation, one of `and` / `or`.
         *
         * @return $this
         */
        public function where_nested(callable $callback, $and_or = self::RELATION_AND)
        {
        }
        /**
         * Add `HAVING` statement.
         *
         * @param string $sql - RAW SQL having clause.
         * @param string $and_or - Boolean relation, one of `and` / `or`.
         *
         * @return $this
         */
        public function having_raw($sql, $and_or = self::RELATION_AND)
        {
        }
        /**
         * Add `OR HAVING` statement.
         *
         * @param string $sql - RAW SQL having clause.
         *
         * @return $this
         */
        public function or_having_raw($sql)
        {
        }
        /**
         * Add a `JOIN ... ON` statement.
         *
         * @param callable $callback - Closure that builds the JOIN clause.
         * @param string $type - JOIN type.
         *
         * @return $this
         */
        public function join(callable $callback, $type = \ElementorPro\Core\Database\Join_Clause::TYPE_INNER)
        {
        }
        /**
         * @shortcut `$this->join()`
         */
        public function left_join(callable $callback)
        {
        }
        /**
         * @shortcut `$this->join()`
         */
        public function right_join(callable $callback)
        {
        }
        /**
         * Creates a new Query Builder instance using the same connection as the initiator.
         *
         * @return self
         */
        public function new_query()
        {
        }
        /**
         * Creates a new Join Clause instance using the same connection as the initiator.
         *
         * @param string $type - JOIN type.
         *
         * @return Join_Clause
         */
        public function new_join_clause($type)
        {
        }
        /**
         * Limit the returned results.
         * Adds a `LIMIT` statement.
         *
         * @param int $limit - Max count of results to return.
         *
         * @return $this
         */
        public function limit($limit)
        {
        }
        /**
         * Add and `OFFSET` statement.
         *
         * @param int $offset - Count of results to skip.
         *
         * @return $this
         */
        public function offset($offset)
        {
        }
        /**
         * Adds an `ORDER BY` statement.
         * NOTE: `$column` IS NOT ESCAPED & SHOULD BE WHITELISTED!
         *
         * @param string $column - Column to order by.
         * @param string $direction - Direction (`asc` / `desc`).
         *
         * @return $this
         */
        public function order_by($column, $direction = 'asc')
        {
        }
        /**
         * Adds a `GROUP BY` statement.
         * NOTE: `$column` IS NOT ESCAPED & SHOULD BE WHITELISTED!
         *
         * @param string $column - Column to group by.
         *
         * @return $this
         */
        public function group_by($column)
        {
        }
        /**
         * Get the raw bindings array.
         *
         * @return array[]
         */
        public function get_raw_bindings()
        {
        }
        /**
         * Get the columns to use inside the SELECT statement.
         * Defaults to `*` if non are selected.
         *
         * @return string
         */
        public function compile_columns()
        {
        }
        /**
         * Get the raw columns array.
         *
         * @return string[]
         */
        public function get_raw_columns()
        {
        }
        /**
         * Compile the `columns` & `from` attributes into an actual `SELECT` statement.
         *
         * @return string
         */
        public function compile_select()
        {
        }
        /**
         * Compile the table name and alias.
         *
         * @return string
         */
        public function compile_from()
        {
        }
        /**
         * Compile the `joins` array into an actual `JOIN` statement.
         *
         * @return string
         */
        public function compile_joins()
        {
        }
        /**
         * Compile the `wheres` array into an actual `WHERE` statement.
         *
         * @return string
         */
        public function compile_wheres()
        {
        }
        /**
         * Compile the `havings` array into an actual `HAVING` statement.
         * TODO: Add more types.
         *
         * @return string
         */
        public function compile_having()
        {
        }
        /**
         * Compile the `groups` array into an actual `GROUP BY` statement.
         *
         * @return string
         */
        public function compile_group_by()
        {
        }
        /**
         * Compile the `orders` array into an actual `ORDER BY` statement.
         *
         * @return string
         */
        public function compile_order_by()
        {
        }
        /**
         * Compile the `limit` attribute into an actual `LIMIT` statement.
         *
         * @return string
         */
        public function compile_limit()
        {
        }
        /**
         * Compile the `offset` attribute into an actual `OFFSET` statement.
         *
         * @return string
         */
        public function compile_offset()
        {
        }
        /**
         * Get the final SQL of the query, with bindings placeholders.
         *
         * @return string
         */
        public function to_sql()
        {
        }
        /**
         * Find & get by id.
         *
         * @param int $id - ID to search for.
         * @param string $field - Field name. Defaults to `id`.
         *
         * @return array|null
         */
        public function find($id, $field = 'id')
        {
        }
        /**
         * Return the first matching row or null otherwise.
         *
         * @return array|null
         */
        public function first()
        {
        }
        /**
         * Pluck a specific column from the query results.
         *
         * @param string $column - The column to pluck.
         *
         * @return Collection
         */
        public function pluck($column)
        {
        }
        /**
         * Return the count of rows based on the query.
         *
         * @param string $column
         *
         * @return int
         */
        public function count($column = '*')
        {
        }
        /**
         * Get the query result.
         *
         * @return Collection
         */
        public function get()
        {
        }
        /**
         * Insert data to a table.
         *
         * @param array $values - Array of [ `column` => `value` ] pairs. Non-escaped.
         *
         * @return int
         * @throws \Exception
         */
        public function insert(array $values)
        {
        }
        /**
         * Update data in the table.
         *
         * @param array $values - Array of [ `column` => `value` ] pairs. Non-escaped.
         *
         * @return bool|int
         */
        public function update(array $values)
        {
        }
        /**
         * Delete data from the table.
         *
         * @return bool|int
         */
        public function delete()
        {
        }
        /**
         * Add an eager loaded relation.
         *
         * @param string $key - Array key to store the resolver in.
         * @param callable $resolver - Resolve function that gets the results and adds the eager loaded relation.
         *
         * @return $this
         */
        protected function add_with($key, callable $resolver)
        {
        }
        /**
         * Escape a value for `LIKE` statement.
         *
         * @param string $value - Value to escape.
         *
         * @return string
         */
        protected function escape_like($value)
        {
        }
        /**
         * Get a flat array of the current bindings.
         *
         * @param null|string $type - The binding type to get.
         *
         * @return array
         */
        protected function get_bindings($type = null)
        {
        }
        /**
         * Add a binding to the bindings array by a sector.
         *
         * @param string|array $value - Raw value that needs to be bind.
         * @param string $type - Bind type (the sector in the SQL query).
         *
         * @return $this
         */
        protected function add_binding($value, $type)
        {
        }
        /**
         * Get the type of the binding type for SQL `prepare` function.
         *
         * @param array|string|numeric $value - The value to get the binding for.
         *
         * @return string - One of `%d` / `%f` / `%s`.
         */
        protected function get_binding_type($value)
        {
        }
        /**
         * Wrap a value with backticks.
         *
         * @param numeric|string|string[] $value - Value to wrap.
         *
         * @return string|string[]
         */
        protected function wrap_with_backticks($value)
        {
        }
        /**
         * Concatenate an array of segments, removing empties.
         *
         * @param array $segments - Segments to concatenate.
         * @param array $separator - Separator string. Defaults to empty space.
         *
         * @return string
         */
        protected function concatenate(array $segments, $separator = ' ')
        {
        }
        /**
         * Parse a column by splitting it to table & column names, and wrapping it with backticks.
         *
         * @param $column - Column to parse.
         *
         * @return string
         */
        protected function parse_column($column)
        {
        }
        protected function parse_as($as)
        {
        }
        /**
         * Determine if a column is already selected.
         *
         * @param string $name - Column name to check.
         *
         * @return mixed|null
         */
        protected function is_column_selected($name)
        {
        }
    }
    class Model_Query_Builder extends \ElementorPro\Core\Database\Query_Builder
    {
        /**
         * The Query Builder associated model.
         *
         * @var string
         */
        public $model;
        /**
         * Whether the returned value should be hydrated into a model.
         *
         * @var bool
         */
        public $return_as_model = true;
        /**
         * Model_Query_Builder constructor.
         *
         * @param string $model_classname - Model to use inside the builder.
         * @param \wpdb|null $connection - MySQL connection.
         */
        public function __construct($model_classname, \wpdb $connection = null)
        {
        }
        /**
         * Set the model the generated from the query builder.
         *
         * @param $model_classname
         *
         * @return $this
         */
        public function set_model($model_classname)
        {
        }
        /**
         * Disable model hydration.
         *
         * @return $this
         */
        public function disable_model_initiation()
        {
        }
        /**
         * Disable hydration before calling the original count.
         *
         * @param string $column
         *
         * @return int
         */
        public function count($column = '*')
        {
        }
        /**
         * Disable hydration before calling the original pluck.
         *
         * @inheritDoc
         */
        public function pluck($column = null)
        {
        }
        /**
         * Override the parent `get()` and make Models from the results.
         *
         * @return \ElementorPro\Core\Utils\Collection
         */
        public function get()
        {
        }
    }
    abstract class Model_Base implements \JsonSerializable
    {
        // Casting types.
        const TYPE_BOOLEAN = 'boolean';
        const TYPE_COLLECTION = 'collection';
        const TYPE_INTEGER = 'integer';
        const TYPE_STRING = 'string';
        const TYPE_JSON = 'json';
        const TYPE_DATETIME = 'datetime';
        const TYPE_DATETIME_GMT = 'datetime_gmt';
        /**
         * Casts array.
         * Used to automatically cast values from DB to the appropriate property type.
         *
         * @var array
         */
        protected static $casts = [];
        /**
         * Model_Base constructor.
         *
         * @param array $fields - Fields from the DB to fill.
         *
         * @return void
         */
        public function __construct(array $fields)
        {
        }
        /**
         * Get the model's table name.
         * Throws an exception by default in order to require implementation,
         * since abstract static functions are not allowed.
         *
         * @return string
         */
        public static function get_table()
        {
        }
        /**
         * Create a Query Builder for the model's table.
         *
         * @param \wpdb|null $connection - MySQL connection to use.
         *
         * @return Query_Builder
         */
        public static function query(\wpdb $connection = null)
        {
        }
        /**
         * Cast value into specific type.
         *
         * @param $value - Value to cast.
         * @param $type - Type to cast into.
         *
         * @return mixed
         */
        protected static function cast($value, $type)
        {
        }
        /**
         * Cast a model property value into a JSON compatible data type.
         *
         * @param $value - Value to cast.
         * @param $type - Type to cast into.
         * @param $property_name - The model property name.
         *
         * @return mixed
         */
        protected static function json_serialize_property($value, $type, $property_name)
        {
        }
        /**
         * @return array
         */
        #[\ReturnTypeWillChange]
        public function jsonSerialize()
        {
        }
    }
    /**
     * JOIN clause builder.
     *
     * Essentially, it uses the regular Builder's capabilities while wrapping some method
     * for syntactic sugar and better readability.
     */
    class Join_Clause extends \ElementorPro\Core\Database\Query_Builder
    {
        // JOIN types.
        const TYPE_INNER = 'inner';
        const TYPE_LEFT = 'left';
        const TYPE_RIGHT = 'right';
        /**
         * JOIN type.
         *
         * @var string
         */
        public $type;
        /**
         * Join_Clause constructor.
         *
         * @param string $type - JOIN type.
         * @param \wpdb|null $connection - MySQL connection to use.
         *
         * @return void
         */
        public function __construct($type, \wpdb $connection = null)
        {
        }
        /**
         * @uses `$this->where()`.
         *
         * @return Join_Clause
         */
        public function on($column, $operator, $value, $and_or = self::RELATION_AND)
        {
        }
        /**
         * @shortcut `$this->on()`.
         *
         * @return Join_Clause
         */
        public function or_on($first, $operator, $second)
        {
        }
        /**
         * @uses `$this->where_column()`.
         *
         * @return Join_Clause
         */
        public function on_column($first, $operator, $second, $and_or = self::RELATION_AND)
        {
        }
        /**
         * @shortcut `$this->on_column()`.
         *
         * @return Join_Clause
         */
        public function or_on_column($first, $operator, $second)
        {
        }
    }
}
namespace ElementorPro\Core\Upgrade {
    class Upgrades
    {
        public static $typography_control_names = [
            'typography',
            // The popover toggle ('starter_name').
            'font_family',
            'font_size',
            'font_weight',
            'text_transform',
            'font_style',
            'text_decoration',
            'line_height',
            'letter_spacing',
        ];
        public static function _on_each_version($updater)
        {
        }
        public static function _v_1_3_0()
        {
        }
        public static function _v_1_4_0()
        {
        }
        public static function _v_1_12_0()
        {
        }
        /**
         * Replace 'sticky' => 'yes' with 'sticky' => 'top' in sections.
         */
        public static function _v_2_0_3()
        {
        }
        public static function _v_2_5_0_form($updater)
        {
        }
        public static function _v_2_5_0_woocommerce_menu_cart($updater)
        {
        }
        public static function _v_3_7_2_woocommerce_rename_related_to_related_products($updater)
        {
        }
        public static function _slider_to_border_settings($element, $args)
        {
        }
        /**
         * @param $element
         * @param $args
         *
         * @return mixed
         */
        public static function _rename_repeater_settings($element, $args)
        {
        }
        public static function _v_2_5_0_posts($updater)
        {
        }
        public static function _v_2_5_0_portfolio($updater)
        {
        }
        public static function _v_2_5_0_products($updater)
        {
        }
        /**
         * @param $updater
         *
         * @return bool Should run again.
         */
        public static function _v_2_5_0_sitemap($updater)
        {
        }
        /**
         * @param Updater $updater
         *
         * @return bool
         */
        public static function _v_2_5_0_popup_border_radius($updater)
        {
        }
        public static function _v_2_5_4_posts($updater)
        {
        }
        public static function _v_2_5_4_portfolio($updater)
        {
        }
        public static function _v_2_5_4_products($updater)
        {
        }
        public static function _v_2_5_4_form($updater)
        {
        }
        public static function _v_3_1_0_media_carousel($updater)
        {
        }
        public static function _v_3_1_0_reviews($updater)
        {
        }
        public static function _v_3_1_0_testimonial_carousel($updater)
        {
        }
        public static function _v_3_1_0_slides($updater)
        {
        }
        public static function _v_3_3_0_nav_menu_icon($updater)
        {
        }
        public static function _v_3_3_0_recalc_usage_data($updater)
        {
        }
        public static function _v_3_5_0_price_list($updater)
        {
        }
        /**
         * $changes is an array of arrays in the following format:
         * [
         *   'control_ids' => array of control ids
         *   'callback' => user callback to manipulate the control_ids
         * ]
         *
         * @param       $widget_id
         * @param       $updater
         * @param array $changes
         *
         * @return bool
         */
        public static function _update_widget_settings($widget_id, $updater, $changes)
        {
        }
        /**
         * @param $element
         * @param $args
         *
         * @return mixed
         */
        public static function _rename_widget_settings($element, $args)
        {
        }
        /**
         * @param $element
         * @param $args
         *
         * @return mixed
         */
        public static function _rename_widget_settings_value($element, $args)
        {
        }
        /**
         * @param $element
         * @param $args
         *
         * @return mixed
         */
        public static function _add_widget_settings_to_array($element, $args)
        {
        }
        /**
         * @param $element
         * @param $args
         *
         * @return mixed
         */
        public static function _merge_widget_settings($element, $args)
        {
        }
        /**
         * Possible scenarios:
         * 1) custom_id is not empty --> do nothing
         * 2) Existing _id: Empty or Missing custom_id --> create custom_id and set the value to the value of _id
         * 3) Missing _id: Empty or Missing custom_id --> generate a unique key and set it as custom_id value
         * @param $element
         * @param $args
         *
         * @return mixed
         */
        public static function _missing_form_custom_id_settings($element, $args)
        {
        }
        /**
         * Migrates the value saved for the 'indicator' SELECT control in the Nav Menu Widget to the new replacement
         * 'submenu_icon' ICONS control.
         *
         * @param $element
         * @param $args
         *
         * @return mixed;
         */
        public static function _migrate_indicator_control_to_submenu_icon($element, $args)
        {
        }
        /**
         * @param $element
         * @param $args
         *
         * @return mixed
         */
        public static function _convert_term_id_to_term_taxonomy_id($element, $args)
        {
        }
        /**
         * Convert 'progress' to 'progressbar'
         *
         * Before Elementor 2.2.0, the progress bar option key was 'progress'. In Elementor 2.2.0,
         * it was changed to 'progressbar'. This upgrade script migrated the DB data for old websites using 'progress'.
         *
         * @param $element
         * @param $args
         * @return mixed
         */
        public static function _convert_progress_to_progressbar($element, $args)
        {
        }
        /**
         * Migrate Slides Button Color Settings
         *
         * Move Slides Widget's 'button_color' settings to 'button_text_color' and 'button_border_color' as necessary,
         * to allow for removing the redundant control.
         *
         * @param $element
         * @param $args
         * @return mixed
         */
        public static function _migrate_slides_button_color_settings($element, $args)
        {
        }
        /**
         * Copy Title Styles to New Price Controls
         *
         * Copy the values from the  Price List widget's Title Style controls to new Price Style controls.
         *
         * @param $element
         * @param $args
         * @return mixed
         * @since 3.4.0
         *
         */
        public static function _copy_title_styles_to_new_price_controls($element, $args)
        {
        }
        public static function _remove_remote_info_api_data()
        {
        }
        /**
         * @param $element
         * @param $to
         * @param $control_id
         * @param $args
         * @return array
         */
        protected static function set_new_value($element, $to, $control_id, $args)
        {
        }
        /**  *
         * @param $change
         * @param array $element
         * @param $args
         * @return array
         */
        protected static function replace_value_if_found($change, array $element, $args)
        {
        }
        /**
         * @param $element
         * @param $widget_id
         * @return bool
         */
        protected static function is_widget_matched($element, $widget_id)
        {
        }
        /**
         * @param $changes
         * @param $element
         * @param $args
         * @return array|mixed
         */
        protected static function apply_rename($changes, $element, $args)
        {
        }
        /**
         * @param $element
         * @param $control_id
         * @return bool
         */
        protected static function is_control_exist_in_settings($element, $control_id)
        {
        }
        /**
         * @param $element
         * @param $new
         * @return bool
         */
        protected static function is_need_to_replace_value($element, $control_id, $value_to_replace)
        {
        }
        /**
         * @return array[]
         */
        public static function get_woocommerce_rename_related_to_related_products_changes()
        {
        }
    }
    class Manager extends \Elementor\Core\Upgrade\Manager
    {
        public function get_action()
        {
        }
        public function get_plugin_name()
        {
        }
        public function get_plugin_label()
        {
        }
        public function get_updater_label()
        {
        }
        public function get_new_version()
        {
        }
        public function get_version_option_name()
        {
        }
        public function get_upgrades_class()
        {
        }
        public static function get_install_history_meta()
        {
        }
    }
}
namespace ElementorPro\Core {
    /**
     * This class is responsible for the interaction with PHP Core API.
     * The main benefit is making it easy to mock in testing.
     */
    class PHP_Api
    {
        /**
         * @param $from
         * @param $to
         *
         * @return bool
         */
        public function move_uploaded_file($from, $to)
        {
        }
    }
    final class Modules_Manager
    {
        public function __construct()
        {
        }
        /**
         * @param string $module_name
         *
         * @return Module_Base|Module_Base[]
         */
        public function get_modules($module_name)
        {
        }
    }
}
namespace ElementorPro\Core\App {
    class App extends \Elementor\Core\Base\App
    {
        /**
         * Get module name.
         *
         * Retrieve the module name.
         *
         * @since 3.0.0
         * @access public
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
        public function init()
        {
        }
        public function set_menu_url()
        {
        }
        protected function get_init_settings()
        {
        }
        protected function get_assets_base_url()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Core\App\Modules\SiteEditor {
    class Render_Mode_Template_Preview extends \Elementor\Core\Frontend\RenderModes\Render_Mode_Base
    {
        /**
         * @return string
         */
        public static function get_name()
        {
        }
        public function filter_template()
        {
        }
        public function prepare_render()
        {
        }
        /**
         * disable all the interactions in the preview render mode.
         */
        public function render_pointer_event_style()
        {
        }
        public function is_static()
        {
        }
    }
    /**
     * Site Editor Module
     *
     * Responsible for initializing Elementor Pro App functionality
     */
    class Module extends \Elementor\Core\Base\Module
    {
        /**
         * Get name.
         *
         * @access public
         *
         * @return string
         */
        public function get_name()
        {
        }
        /**
         * @throws \Exception
         */
        public function get_template_types()
        {
        }
        /**
         * Register ajax actions.
         *
         * @access public
         *
         * @param Ajax $ajax
         */
        public function register_ajax_actions(\Elementor\Core\Common\Modules\Ajax\Module $ajax)
        {
        }
        /**
         * @param Render_Mode_Manager $manager
         *
         * @throws \Exception
         */
        public function register_render_mode(\Elementor\Core\Frontend\Render_Mode_Manager $manager)
        {
        }
        protected function get_init_settings()
        {
        }
        /**
         * Module constructor.
         *
         * @access public
         */
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Core\App\Modules\SiteEditor\Data {
    class Controller extends \Elementor\Data\Base\Controller
    {
        public function get_name()
        {
        }
        public function register_endpoints()
        {
        }
        public function get_permission_callback($request)
        {
        }
    }
}
namespace ElementorPro\Core\App\Modules\SiteEditor\Data\Endpoints {
    abstract class Base_Endpoint extends \Elementor\Data\Base\Endpoint
    {
        /**
         * Check if post is lock.
         *
         * @param $post_id
         *
         * @return bool|false|int
         */
        protected function is_post_lock($post_id)
        {
        }
    }
    class Templates_Conditions extends \ElementorPro\Core\App\Modules\SiteEditor\Data\Endpoints\Base_Endpoint
    {
        /**
         * @return string
         */
        public function get_name()
        {
        }
        protected function register()
        {
        }
        public function get_item($template_id, $request)
        {
        }
        public function update_item($template_id, $request)
        {
        }
        protected function get_conditions($post_id)
        {
        }
        protected function save_conditions($post_id, $conditions)
        {
        }
    }
    class Template_Types extends \ElementorPro\Core\App\Modules\SiteEditor\Data\Endpoints\Base_Endpoint
    {
        /**
         * @return string
         */
        public function get_name()
        {
        }
        public function get_items($request)
        {
        }
    }
    class Templates extends \ElementorPro\Core\App\Modules\SiteEditor\Data\Endpoints\Base_Endpoint
    {
        public function __construct($controller)
        {
        }
        /**
         * @return string
         */
        public function get_name()
        {
        }
        protected function register()
        {
        }
        public function get_items($request)
        {
        }
        public function create_items($request)
        {
        }
        public function update_item($id, $request)
        {
        }
        public function delete_item($id, $request)
        {
        }
    }
    class Conditions_Config extends \ElementorPro\Core\App\Modules\SiteEditor\Data\Endpoints\Base_Endpoint
    {
        /**
         * @return string
         */
        public function get_name()
        {
        }
        public function get_items($request)
        {
        }
    }
    class Templates_Conditions_Conflicts extends \ElementorPro\Core\App\Modules\SiteEditor\Data\Endpoints\Base_Endpoint
    {
        /**
         * @return string
         */
        public function get_name()
        {
        }
        public function get_items($request)
        {
        }
    }
}
namespace ElementorPro\Core\App\Modules\SiteEditor\Data\Responses {
    class Lock_Error_Response extends \WP_Error
    {
        public function __construct($user_id)
        {
        }
    }
}
namespace ElementorPro\Core\App\Modules\ImportExport {
    class Module extends \Elementor\Core\Base\Module
    {
        public function get_name()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Core\App\Modules\ImportExport\Runners\Revert {
    class Templates extends \Elementor\App\Modules\ImportExport\Runners\Revert\Revert_Runner_Base
    {
        public static function get_name() : string
        {
        }
        public function should_revert(array $data) : bool
        {
        }
        public function revert(array $data)
        {
        }
    }
}
namespace ElementorPro\Core\App\Modules\ImportExport\Runners\Export {
    class Templates extends \Elementor\App\Modules\ImportExport\Runners\Export\Export_Runner_Base
    {
        public static function get_name() : string
        {
        }
        public function should_export(array $data)
        {
        }
        public function export(array $data)
        {
        }
    }
}
namespace ElementorPro\Core\App\Modules\ImportExport\Runners\Import {
    class Templates extends \Elementor\App\Modules\ImportExport\Runners\Import\Import_Runner_Base
    {
        public static function get_name() : string
        {
        }
        public function should_import(array $data)
        {
        }
        public function import(array $data, array $imported_data)
        {
        }
        public function get_import_session_metadata() : array
        {
        }
    }
}
namespace ElementorPro\Core\App\Modules\KitLibrary {
    class Module extends \Elementor\Core\Base\Module
    {
        /**
         * Get name.
         *
         * @access public
         *
         * @return string
         */
        public function get_name()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Core\App\Modules\Onboarding {
    class Module extends \Elementor\Core\Base\Module
    {
        /**
         * Get name
         *
         * @since 3.6.0
         * @access public
         *
         * @return string
         */
        public function get_name()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Core\Security {
    class Capability
    {
        /**
         * 'edit_post' is one of the meta-capabilities which is the combination of
         *  edit_posts and edit_others_posts primitive capabilities
         *
         *  https://wordpress.org/documentation/article/roles-and-capabilities/
         *  https://learn.wordpress.org/tutorial/custom-post-types-and-capabilities/
         */
        const EDIT_POST_META = 'edit_post';
        const EDIT_POSTS = 'edit_posts';
        const READ_PRIVATE_POSTS = 'read_private_posts';
    }
    class Access_Control
    {
        public static function user_can_edit(int $post_id) : bool
        {
        }
        public static function user_can_edit_posts() : bool
        {
        }
        public static function user_can_access_private_posts() : bool
        {
        }
        /**
         * @throws \Exception
         */
        public static function verify_post_edit_access(int $post_id) : void
        {
        }
        /**
         * @throws \Exception
         */
        public static function verify_user_editing_capability() : void
        {
        }
    }
}
namespace ElementorPro\Core\Admin {
    class Action_Links
    {
        public static function get_links(array $links) : array
        {
        }
        public static function get_pro_links(array $links) : array
        {
        }
    }
    class Canary_Deployment extends \Elementor\Core\Admin\Canary_Deployment
    {
        const CURRENT_VERSION = ELEMENTOR_PRO_VERSION;
        const PLUGIN_BASE = ELEMENTOR_PRO_PLUGIN_BASE;
        protected function get_canary_deployment_remote_info($force)
        {
        }
    }
    class Post_Status
    {
        public const PUBLISH = 'publish';
        public const PRIVATE = 'private';
        public const DRAFT = 'draft';
    }
    class Admin extends \Elementor\Core\Base\App
    {
        /**
         * Get module name.
         *
         * Retrieve the module name.
         *
         * @since 2.3.0
         * @access public
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
        /**
         * Enqueue admin styles.
         *
         * @since 1.0.0
         * @return void
         */
        public function enqueue_styles()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function remove_go_pro_menu()
        {
        }
        public function register_admin_tools_fields(\Elementor\Tools $tools)
        {
        }
        public function post_elementor_pro_rollback()
        {
        }
        public function plugin_row_meta($plugin_meta, $plugin_file)
        {
        }
        public function add_finder_items(array $categories)
        {
        }
        public function register_ajax_actions($ajax_manager)
        {
        }
        public function handle_hints_cta($request)
        {
        }
        public function handle_send_app_campaign($request)
        {
        }
        /**
         * Admin constructor.
         */
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Core\Utils {
    /**
     * Basic items registrar.
     *
     * TODO: Move to Core.
     */
    class Registrar
    {
        /**
         * Registrar constructor.
         *
         * @return void
         */
        public function __construct()
        {
        }
        /**
         * Register a new item.
         *
         * @param           $instance - Item instance.
         * @param string    $id - Optional - For BC - Deprecated.
         *
         * @return boolean - Whether the item was registered.
         */
        public function register($instance, $id = null)
        {
        }
        /**
         * Get an item by ID.
         *
         * @param string $id
         *
         * @return array|null
         */
        public function get($id = null)
        {
        }
    }
}
namespace ElementorPro\core\utils {
    /**
     * Class Hints
     */
    class Hints extends \Elementor\Core\Utils\Hints
    {
        public static function should_show_hint($hint_id) : bool
        {
        }
        public static function get_hints($hint_key = null) : array
        {
        }
    }
}
namespace ElementorPro\Core\Utils {
    class Abtest
    {
        const PREFIX_CACHE_KEY = '_elementor_ab_test_';
        const CACHE_TTL = 90 * DAY_IN_SECONDS;
        public static function get_variation($test_name) : int
        {
        }
    }
    // TODO: Move to Core.
    class Collection extends \Elementor\Core\Utils\Collection implements \JsonSerializable
    {
        /**
         * Change the items key by an item field.
         *
         * @param string $key
         *
         * @return Collection
         */
        public function key_by($key)
        {
        }
        /**
         * Flatten the items recursively.
         *
         * @return array
         */
        public function flatten_recursive()
        {
        }
        /**
         * Run array_diff between the collection and other array or collection.
         *
         * @param $filter
         *
         * @return $this
         */
        public function diff($filter)
        {
        }
        /**
         * Reverse the array
         *
         * @param false $preserve_keys
         *
         * @return $this
         */
        public function reverse($preserve_keys = false)
        {
        }
        /**
         * Return a JSON serialized representation of the Collection.
         *
         * @return array
         */
        #[\ReturnTypeWillChange]
        public function jsonSerialize()
        {
        }
    }
}
namespace ElementorPro\Core\Integrations {
    class Integrations_Manager
    {
        /**
         * Registered action types.
         *
         * @var Registrar
         */
        protected $actions_registrar;
        /**
         * Integrations_Manager constructor.
         *
         * @return void
         */
        public function __construct()
        {
        }
        /**
         * Get an action instance.
         *
         * @shortcut `Registrar->get()`.
         *
         * @return \ElementorPro\Core\Integrations\Actions\Action_Base|null
         */
        public function get_action($id)
        {
        }
        /**
         * Run an action for a selected payload.
         *
         * @param array|mixed $payloads - Payloads instances to run the actions on.
         * @param null|string $id - If `$payloads` is not an array, a custom action ID can be provided.
         *
         * @return void
         */
        public function run($payloads, $id = null)
        {
        }
        /**
         * Initialize the manager actions.
         *
         * @return void
         */
        protected function init_actions()
        {
        }
        /**
         * Determine if the manager is initialized.
         *
         * @return boolean
         */
        protected function is_initialized()
        {
        }
    }
}
namespace ElementorPro\Core\Integrations\Exceptions {
    abstract class Exception_Base extends \Exception
    {
        /**
         * @var string
         */
        protected $action;
        /**
         * @var array
         */
        protected $meta = [];
        /**
         * Get a formatted message specific to the current exception type.
         *
         * @param string $message
         *
         * @return string
         */
        protected abstract function format_message($message);
        /**
         * Exception_Base constructor.
         *
         * @param string $action - Action name that failed (ideally the class name, e.g. Email::class).
         * @param string $message - Message to show.
         * @param array  $meta   - Exception meta data. Used for logging.
         *
         */
        public function __construct($action, $message = '', $meta = [])
        {
        }
        /**
         * Log the exception to Elementor's log.
         *
         * @return void
         */
        public function log()
        {
        }
        /**
         * Get the error format.
         *
         * @return string
         */
        public function __toString()
        {
        }
    }
    class Action_Validation_Failed_Exception extends \ElementorPro\Core\Integrations\Exceptions\Exception_Base
    {
        protected function format_message($message)
        {
        }
    }
    class Action_Failed_Exception extends \ElementorPro\Core\Integrations\Exceptions\Exception_Base
    {
        protected function format_message($message)
        {
        }
    }
}
namespace ElementorPro\Core\Integrations\Actions {
    abstract class Action_Base
    {
        /**
         * Validate a payload.
         *
         * @param mixed $payload - Payload object instance.
         *
         * @throws \ElementorPro\Core\Integrations\Exceptions\Action_Validation_Failed_Exception
         *
         * @return mixed
         */
        public abstract function validate($payload);
        /**
         * Apply the action.
         *
         * @param mixed $payload - Payload object instance.
         *
         * @throws \ElementorPro\Core\Integrations\Exceptions\Action_Failed_Exception
         *
         * @return void
         */
        public abstract function apply($payload);
        /**
         * Run the action.
         *
         * @param mixed $payload - Payload object instance.
         *
         * @throws \ElementorPro\Core\Integrations\Exceptions\Action_Validation_Failed_Exception
         * @throws \ElementorPro\Core\Integrations\Exceptions\Action_Failed_Exception
         *
         * @return void
         */
        public function run($payload)
        {
        }
    }
}
namespace ElementorPro\Core\Integrations\Actions\Email {
    class Email extends \ElementorPro\Core\Integrations\Actions\Action_Base
    {
        /**
         * @param Email_Message $payload
         *
         * @return void
         * @throws \Exception
         */
        public function apply($payload)
        {
        }
        /**
         * @alias `$this->run()`
         *
         * @param Email_Message $payload
         *
         * @return void
         *@throws \Exception
         *
         */
        public function send(\ElementorPro\Core\Integrations\Actions\Email\Email_Message $payload)
        {
        }
        /**
         * Validate the email message DTO.
         *
         * @param Email_Message $payload
         *
         * @throws \ElementorPro\Core\Integrations\Exceptions\Action_Validation_Failed_Exception
         *
         * @return void
         */
        public function validate($payload)
        {
        }
        /**
         * Calls `wp_mail()`. Used for testing.
         *
         * @param mixed ...$args
         *
         * @return void
         */
        protected function send_mail(...$args)
        {
        }
        /**
         * Throw exception on `wp_mail()` error.
         *
         * @param \WP_Error $error
         *
         * @throws \ElementorPro\Core\Integrations\Exceptions\Action_Failed_Exception
         *
         * @return void
         */
        public function on_wp_mail_error(\WP_Error $error)
        {
        }
    }
    class Email_Message
    {
        /**
         * Email sender.
         *
         * @var Email_Address
         */
        public $from;
        /**
         * Email recipient.
         *
         * @var Email_Address
         */
        public $to;
        /**
         * Email reply to address.
         *
         * @var Email_Address[]
         */
        public $reply_to = [];
        /**
         * Email CC recipient.
         *
         * @var Email_Address[]
         */
        public $cc = [];
        /**
         * Email BCC recipient.
         *
         * @var Email_Address[]
         */
        public $bcc = [];
        /**
         * Email subject.
         *
         * @var string
         */
        public $subject;
        /**
         * Email content type.
         *
         * @var string
         */
        public $content_type;
        /**
         * Email body.
         *
         * @var string
         */
        public $body;
        /**
         * Email attachments.
         *
         * @var array
         */
        public $attachments = [];
        /**
         * Email_Message constructor.
         *
         * @return void
         */
        public function __construct()
        {
        }
        /**
         * Set the email sender.
         *
         * @param string $email
         * @param string|null $name
         *
         * @return $this
         */
        public function from($email, $name = null)
        {
        }
        /**
         * Set the email recipient.
         *
         * @param string $email
         * @param string|null $name
         *
         * @return $this
         */
        public function to($email, $name = null)
        {
        }
        /**
         * Add a reply to.
         *
         * @param string $email
         * @param string|null $name
         *
         * @return $this
         */
        public function reply_to($email, $name = null)
        {
        }
        /**
         * Add a CC.
         *
         * @param string $email
         * @param string|null $name
         *
         * @return $this
         */
        public function cc($email, $name = null)
        {
        }
        /**
         * Add a BCC.
         *
         * @param string $email
         * @param string|null $name
         *
         * @return $this
         */
        public function bcc($email, $name = null)
        {
        }
        /**
         * Set the email subject.
         *
         * @param string $subject
         *
         * @return $this
         */
        public function subject($subject)
        {
        }
        /**
         * Set the email content type.
         *
         * @param string $content_type
         *
         * @return $this
         */
        public function content_type($content_type)
        {
        }
        /**
         * Set the email body using plain text.
         *
         * @param string $body
         * @param string $content_type
         *
         * @return $this
         */
        public function body($body, $content_type = 'text/html')
        {
        }
        /**
         * Set the email body using a view.
         *
         * @param string $path - View path,
         * @param array  $data - Data that will be passes to the view.
         *
         * @return $this
         * @throws \Exception
         */
        public function view($path, $data = [])
        {
        }
        /**
         * Add an attachment.
         *
         * @param string $path - Attachment path on the server.
         *
         * @return $this
         */
        public function attach($path)
        {
        }
    }
    class Email_Address
    {
        /**
         * Recipient email address.
         *
         * @var array
         */
        public $address;
        /**
         * Recipient name.
         *
         * @var string
         */
        public $name;
        /**
         * Email_Address constructor.
         *
         * @param string $address
         * @param string $name
         *
         * @return void
         */
        public function __construct($address, $name)
        {
        }
        /**
         * Format an email to be ready for header (e.g. `Recipient Name <user@email.com>` or `user@email.com`)
         *
         * @return string
         */
        public function format()
        {
        }
    }
}
namespace ElementorPro\Core\Container {
    /**
     * Elementor Container.
     *
     * Elementor container handler class is responsible for the containerization
     * of manager classes and their dependencies.
     *
     * @since 3.25.0
     */
    class Container
    {
        /**
         * @throws Exception
         */
        public static function get_instance() : \ElementorProDeps\DI\Container
        {
        }
    }
}
namespace ElementorPro\Core\Behaviors {
    // TODO: Used here for testing. Should be removed when it'll be available in the Core.
    interface Temp_Lock_Behavior
    {
        /**
         * @return bool
         */
        public function is_locked();
        /**
         * @return array {
         *
         *    @type bool $is_locked
         *
         *    @type array $badge {
         *         @type string $icon
         *         @type string $text
         *     }
         *
         *    @type array $content {
         *         @type string $heading
         *         @type string $description
         *   }
         *
         *    @type array $button {
         *         @type string $text
         *         @type string $url
         *   }
         *
         * }
         */
        public function get_config();
    }
    class Feature_Lock implements \ElementorPro\Core\Behaviors\Temp_Lock_Behavior
    {
        public function __construct($config = [])
        {
        }
        public function is_locked()
        {
        }
        public function get_config()
        {
        }
    }
}
namespace ElementorPro\Core\Isolation {
    interface Wordpress_Adapter_Interface
    {
        public function has_post_thumbnail();
        public function get_comments_number();
        public function is_author($author = '') : bool;
        public function wp_get_attachment_caption($attachment_id) : string;
        public function get_the_title($post_id) : string;
        public function current_user_can($capability, ...$args) : bool;
    }
    class Wordpress_Adapter implements \ElementorPro\Core\Isolation\Wordpress_Adapter_Interface
    {
        public function has_post_thumbnail() : bool
        {
        }
        public function get_comments_number()
        {
        }
        public function is_author($author = '') : bool
        {
        }
        public function wp_get_attachment_caption($attachment_id) : string
        {
        }
        public function get_the_title($post_id) : string
        {
        }
        public function current_user_can($capability, ...$args) : bool
        {
        }
    }
}
namespace ElementorPro\Core\Preview {
    class Preview extends \Elementor\Core\Base\App
    {
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        public function enqueue_styles()
        {
        }
        protected function get_assets_base_url()
        {
        }
    }
}
namespace ElementorPro\Core {
    class Utils
    {
        public static function get_public_post_types($args = [])
        {
        }
        public static function get_client_ip()
        {
        }
        public static function get_site_domain()
        {
        }
        public static function get_current_post_id()
        {
        }
        public static function get_the_archive_url()
        {
        }
        public static function get_page_title($include_context = true)
        {
        }
        public static function set_global_authordata()
        {
        }
        /**
         * Used to overcome core bug when taxonomy is in more then one post type
         *
         * @see https://core.trac.wordpress.org/ticket/27918
         *
         * @global array $wp_taxonomies The registered taxonomies.
         *
         *
         * @param array  $args
         * @param string $output
         * @param string $operator
         *
         * @return array
         */
        public static function get_taxonomies($args = [], $output = 'names', $operator = 'and')
        {
        }
        public static function get_ensure_upload_dir($path)
        {
        }
        /**
         * Remove words from a sentence.
         *
         * @param string  $text
         * @param integer $length
         *
         * @return string
         */
        public static function trim_words($text, $length)
        {
        }
        /**
         * Get a user option with default value as fallback.
         * TODO: Use `\Elementor\User::get_user_option_with_default()` after this PR is merged:
         *  https://github.com/elementor/elementor/pull/17745
         *
         * @param string $option  - Option key.
         * @param int    $user_id - User ID
         * @param mixed  $default - Default fallback value.
         *
         * @return mixed
         */
        public static function get_user_option_with_default($option, $user_id, $default)
        {
        }
        /**
         * TODO: Use core method instead (after merging PR of the original function in core).
         *  PR URL: https://github.com/elementor/elementor/pull/18670.
         *
         * @param $file
         * @param mixed ...$args
         * @return false|string
         */
        public static function _unstable_file_get_contents($file, ...$args)
        {
        }
        /**
         * TODO: Use core method instead (after Pro minimum requirements is updated).
         * PR URL: https://github.com/elementor/elementor/pull/24092
         */
        public static function _unstable_get_super_global_value($super_global, $key)
        {
        }
        /**
         * TODO: Use a core method instead (after Pro minimum requirements is updated).
         * @throws \Exception
         */
        public static function _unstable_get_document_for_edit($id)
        {
        }
        public static function format_control_condition($name, $operator, $value)
        {
        }
        public static function create_widget_instance_from_db($post_id, $widget_id)
        {
        }
        public static function has_invalid_post_permissions($post) : bool
        {
        }
    }
}
namespace ElementorPro\Core\Connect {
    class Manager
    {
        /**
         * @param \Elementor\Core\Common\Modules\Connect\Module $apps_manager
         */
        public function register_apps($apps_manager)
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Core\Connect\Apps {
    class Activate extends \Elementor\Core\Common\Modules\Connect\Apps\Common_App
    {
        public function get_title()
        {
        }
        public function get_slug()
        {
        }
        protected function after_connect()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function action_authorize()
        {
        }
        public function action_activate_pro()
        {
        }
        public function action_switch_license()
        {
        }
        public function action_deactivate()
        {
        }
        public function action_activate_license()
        {
        }
        public function action_reset()
        {
        }
        protected function get_popup_success_event_data()
        {
        }
        protected function get_app_info()
        {
        }
    }
}
namespace ElementorPro\Core\Data {
    abstract class Controller
    {
        public abstract function get_name();
        public function get_namespace()
        {
        }
        protected abstract function register_endpoints();
        protected function register_endpoint($endpoint)
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Core\Data\Interfaces {
    interface Endpoint
    {
        /**
         * @return string The interface ID
         */
        public function get_name() : string;
        /**
         * @return string The route slug which will be used to access the endpoint by URL.
         */
        public function get_route() : string;
    }
}
namespace ElementorPro\Core\Data\Endpoints {
    class Base
    {
        protected $controller;
        protected function register()
        {
        }
        /**
         * Endpoint constructor.
         *
         * runs `$this->register()`.
         *
         * @param Controller $controller
         */
        public function __construct(\ElementorPro\Core\Data\Controller $controller)
        {
        }
    }
    abstract class Refresh_Base extends \ElementorPro\Core\Data\Endpoints\Base implements \ElementorPro\Core\Data\Interfaces\Endpoint
    {
        protected $is_edit_mode;
        public abstract function get_name() : string;
        public abstract function get_route() : string;
        protected function permission_callback($request, $widget_name = '') : bool
        {
        }
        protected function is_widget_model_valid($widget_model)
        {
        }
        /**
         * The widget ID can only be 7 characters long, and contain only letters and numbers.
         *
         * @param $data
         * @return bool
         */
        protected function is_widget_id_valid($widget_id)
        {
        }
        protected function create_widget_instance_from_db($post_id, $widget_id)
        {
        }
        protected function is_edit_mode($post_id)
        {
        }
    }
}
namespace ElementorPro\Core\Notifications\Traits {
    trait Notifiable
    {
        /**
         * Notify a Model with a notification.
         * Syntactic sugar for sending notifications via the `Notifications_Manager`.
         *
         * Usage:
         *  $model->notify( new User_Created_Notification( $new_user ) );
         *
         * @param Notification $notification - Notification to send.
         *
         * @throws \Exception
         *
         * @return void
         */
        public function notify(\ElementorPro\Core\Notifications\Notification $notification)
        {
        }
    }
}
namespace ElementorPro\Core\Notifications {
    abstract class Notification
    {
        /**
         * Get the payloads of the notification data shape (e.g. `Email_Message`, `Database_Message`). Those will automatically
         * be sent over to the appropriate `Actions` under the `Integration_Manager` (using the `notify()` method).
         * This method is also used to determine notification channels based on user ($notifiable) preferences.
         *
         * Returned shape:
         * [
         *  $payload1_instance,
         *  $payload2_instance,
         * ]
         *
         * @param \ElementorPro\Core\Notifications\Traits\Notifiable $notifiable - The notified model.
         *
         * @return array
         */
        public function get_payloads($notifiable)
        {
        }
    }
    class Notifications_Manager
    {
        /**
         * Send a notification.
         *
         * @param \ElementorPro\Core\Notifications\Notification $notification
         * @param $notifiable
         *
         * @throws \Exception
         *
         * @return $this
         */
        public function send(\ElementorPro\Core\Notifications\Notification $notification, $notifiable)
        {
        }
    }
}
namespace ElementorPro\Core\Editor {
    class Notice_Bar extends \Elementor\Core\Editor\Notice_Bar
    {
        const ELEMENTOR_PRO_EDITOR_GO_PRO_TRIAL_ABOUT_TO_EXPIRE_LICENSE_NOTICE_DISMISSED = '_elementor_pro_editor_go_pro_trial_about_to_expire_license_notice_dismissed';
        const ELEMENTOR_PRO_EDITOR_GO_PRO_TRIAL_EXPIRED_LICENSE_NOTICE_DISMISSED = '_elementor_pro_editor_go_pro_trial_expired_license_notice_dismissed';
        const ELEMENTOR_PRO_EDITOR_RENEW_LICENSE_NOTICE_DISMISSED = '_elementor_pro_editor_renew_license_notice_dismissed';
        const ELEMENTOR_PRO_EDITOR_ACTIVATE_LICENSE_NOTICE_DISMISSED = '_elementor_pro_editor_activate_license_notice_dismissed';
        const ELEMENTOR_PRO_EDITOR_RENEW_ABOUT_TO_EXPIRE_LICENSE_NOTICE_DISMISSED = '_elementor_pro_editor_renew_about_to_expire_license_notice_dismissed';
        protected function get_init_settings()
        {
        }
    }
    class Promotion extends \Elementor\Core\Editor\Promotion
    {
        public function get_elements_promotion()
        {
        }
    }
    class Editor extends \Elementor\Core\Base\App
    {
        const EDITOR_V2_PACKAGES = ['editor-documents-extended', 'editor-site-navigation-extended'];
        /**
         * Get app name.
         *
         * Retrieve the app name.
         *
         * @return string app name.
         * @since  2.6.0
         * @access public
         *
         */
        public function get_name()
        {
        }
        public function __construct()
        {
        }
        public function get_init_settings()
        {
        }
        public function enqueue_editor_styles()
        {
        }
        public function enqueue_editor_scripts()
        {
        }
        public function enqueue_editor_v2_scripts()
        {
        }
        public function localize_settings(array $settings)
        {
        }
        public function on_elementor_init()
        {
        }
        public function on_elementor_editor_init()
        {
        }
        protected function get_assets_base_url()
        {
        }
    }
}
namespace ElementorPro\License\Notices {
    class Trial_Expired_Notice extends \Elementor\Core\Admin\Notices\Base_Notice
    {
        /**
         * Notice ID.
         */
        const ID = 'elementor_trial_expired_promote';
        /**
         * @inheritDoc
         */
        public function should_print()
        {
        }
        /**
         * @inheritDoc
         */
        public function get_config()
        {
        }
    }
    class Trial_Period_Notice extends \Elementor\Core\Admin\Notices\Base_Notice
    {
        /**
         * Notice ID.
         */
        const ID = 'elementor_trial_period_promote';
        /**
         * @inheritDoc
         */
        public function should_print()
        {
        }
        /**
         * @inheritDoc
         */
        public function get_config()
        {
        }
    }
}
namespace ElementorPro\License {
    class Updater
    {
        public $plugin_version;
        public $plugin_name;
        public $plugin_slug;
        public function __construct()
        {
        }
        public function delete_transients()
        {
        }
        public function check_update($_transient_data)
        {
        }
        public function plugins_api_filter($_data, $_action = '', $_args = null)
        {
        }
        public function show_update_notification($file, $plugin)
        {
        }
        protected function get_transient($cache_key)
        {
        }
        protected function set_transient($cache_key, $value, $expiration = 0)
        {
        }
        protected function delete_transient($cache_key)
        {
        }
        protected function is_elementor_pro_rollback() : bool
        {
        }
    }
    class API
    {
        const PRODUCT_NAME = 'Elementor Pro';
        /**
         * @deprecated 3.8.0
         */
        const STORE_URL = 'https://my.elementor.com/api/v1/licenses/';
        const BASE_URL = 'https://my.elementor.com/api/v2/';
        const RENEW_URL = 'https://go.elementor.com/renew/';
        // License Statuses
        const STATUS_EXPIRED = 'expired';
        const STATUS_SITE_INACTIVE = 'site_inactive';
        const STATUS_CANCELLED = 'cancelled';
        const STATUS_REQUEST_LOCKED = 'request_locked';
        const STATUS_MISSING = 'missing';
        const STATUS_HTTP_ERROR = 'http_error';
        /**
         * @deprecated 3.8.0
         */
        const STATUS_VALID = 'valid';
        /**
         * @deprecated 3.8.0
         */
        const STATUS_INVALID = 'invalid';
        /**
         * @deprecated 3.8.0
         */
        const STATUS_DISABLED = 'disabled';
        /**
         * @deprecated 3.8.0
         */
        const STATUS_REVOKED = 'revoked';
        // Features
        const FEATURE_PRO_TRIAL = 'pro_trial';
        // Requests lock config.
        const REQUEST_LOCK_TTL = MINUTE_IN_SECONDS;
        const REQUEST_LOCK_OPTION_NAME = '_elementor_pro_api_requests_lock';
        const TRANSIENT_KEY_PREFIX = 'elementor_pro_remote_info_api_data_';
        const LICENCE_TIER_KEY = 'tier';
        const LICENCE_GENERATION_KEY = 'generation';
        // Tiers.
        const TIER_ESSENENTIAL = 'essential';
        const TIER_ADVANCED = 'advanced';
        const TIER_EXPERT = 'expert';
        const TIER_AGENCY = 'agency';
        // Generations.
        const GENERATION_ESSENTIAL_OCT2023 = 'essential-oct2023';
        const GENERATION_EMPTY = 'empty';
        const BC_VALIDATION_CALLBACK = 'should_allow_all_features';
        protected static $transient_data = [];
        public static function activate_license($license_key)
        {
        }
        public static function deactivate_license()
        {
        }
        public static function set_transient($cache_key, $value, $expiration = '+12 hours')
        {
        }
        public static function set_license_data($license_data, $expiration = null)
        {
        }
        /**
         * Check if another request is in progress.
         *
         * @param string $name Request name
         *
         * @return bool
         */
        public static function is_request_running($name)
        {
        }
        public static function get_license_data($force_request = false)
        {
        }
        public static function get_version($force_update = true)
        {
        }
        public static function get_plugin_package_url($version)
        {
        }
        public static function get_previous_versions()
        {
        }
        public static function get_errors()
        {
        }
        public static function get_error_message($error)
        {
        }
        public static function is_license_active()
        {
        }
        public static function is_license_expired()
        {
        }
        public static function is_licence_pro_trial()
        {
        }
        public static function is_licence_has_feature($feature_name, $license_check_validator = null)
        {
        }
        public static function is_need_to_show_upgrade_promotion()
        {
        }
        public static function filter_active_features($features)
        {
        }
        public static function get_promotion_widgets()
        {
        }
        /*
         * Check if the Licence is not Expired and also has a Feature.
         * Needed because even Expired Licences keep the features array for BC.
         */
        public static function active_licence_has_feature($feature_name)
        {
        }
        public static function is_license_about_to_expire()
        {
        }
        /**
         * @param string $library_type
         *
         * @return int
         */
        public static function get_library_access_level($library_type = 'template')
        {
        }
        /**
         * The license API uses "tiers" and "generations".
         * Because we don't use the same logic, and have a flat list of prioritized tiers & generations,
         * we take the generation if exists and fallback to the tier otherwise.
         *
         * For example:
         *   [ 'tier' => 'essential', 'generation' => 'essential-oct2023' ] => 'essential-oct2023'
         *   [ 'tier' => 'essential', 'generation' => 'empty' ] => 'essential'
         *   [ 'tier' => '', 'generation' => '' ] => 'essential-oct2023'
         *   [] => 'essential-oct2023'
         *
         * @return string
         */
        public static function get_access_tier()
        {
        }
    }
    class Admin
    {
        const PAGE_ID = 'elementor-license';
        const LICENSE_KEY_OPTION_NAME = 'elementor_pro_license_key';
        const LICENSE_DATA_OPTION_NAME = '_elementor_pro_license_v2_data';
        const LICENSE_DATA_FALLBACK_OPTION_NAME = self::LICENSE_DATA_OPTION_NAME . '_fallback';
        /**
         * @deprecated 3.6.0 Use `Plugin::instance()->updater` instead.
         */
        public static $updater = null;
        public static function get_errors_details()
        {
        }
        public static function deactivate()
        {
        }
        /**
         * @deprecated 3.6.0 Use `Plugin::instance()->updater` instead.
         *
         * @return \ElementorPro\License\Updater
         */
        public static function get_updater_instance()
        {
        }
        public static function get_license_key()
        {
        }
        public static function set_license_key($license_key)
        {
        }
        public function action_activate_license()
        {
        }
        protected function safe_redirect($url)
        {
        }
        public function action_deactivate_license()
        {
        }
        public function register_page()
        {
        }
        public static function get_url()
        {
        }
        public function display_page()
        {
        }
        public function admin_license_details()
        {
        }
        public function filter_library_get_templates_args($body_args)
        {
        }
        public function handle_tracker_actions()
        {
        }
        public function get_installed_time()
        {
        }
        public function plugin_action_links($links)
        {
        }
        public function plugin_auto_update_setting_html($html, $plugin_file)
        {
        }
        public function add_finder_item(array $categories)
        {
        }
        public function on_deactivate_plugin($plugin)
        {
        }
        public function get_connect_url($params = [])
        {
        }
        public function register_actions()
        {
        }
    }
}
namespace ElementorPro {
    /**
     * Main class plugin
     */
    class Plugin
    {
        /**
         * @var Modules_Manager
         */
        public $modules_manager;
        /**
         * @var UpgradeManager
         */
        public $upgrade;
        /**
         * @var Editor
         */
        public $editor;
        /**
         * @var Preview
         */
        public $preview;
        /**
         * @var Admin
         */
        public $admin;
        /**
         * @var App
         */
        public $app;
        /**
         * @var License\Admin
         */
        public $license_admin;
        /**
         * @var \ElementorPro\Core\Integrations\Integrations_Manager
         */
        public $integrations;
        /**
         * @var \ElementorPro\Core\Notifications\Notifications_Manager
         */
        public $notifications;
        /**
         * @var \ElementorPro\License\Updater
         */
        public $updater;
        /**
         * @var PHP_Api
         */
        public $php_api;
        /**
         * Throw error on object clone
         *
         * The whole idea of the singleton design pattern is that there is a single
         * object therefore, we don't want the object to be cloned.
         *
         * @since 1.0.0
         * @return void
         */
        public function __clone()
        {
        }
        /**
         * Disable unserializing of the class
         *
         * @since 1.0.0
         * @return void
         */
        public function __wakeup()
        {
        }
        /**
         * @return \Elementor\Plugin
         */
        public static function elementor()
        {
        }
        /**
         * @return Plugin
         * @throws Exception
         */
        public static function instance() : \ElementorPro\Plugin
        {
        }
        /**
         * Get the Elementor Pro container or resolve a dependency.
         */
        public function get_elementor_pro_container($abstract = null) : \ElementorProDeps\DI\Container
        {
        }
        public function autoload($class)
        {
        }
        public static function get_frontend_file_url($frontend_file_name, $custom_file)
        {
        }
        public static function get_frontend_file_path($frontend_file_name, $custom_file)
        {
        }
        /**
         * @deprecated 3.26.0
         * @return void
         */
        public function enqueue_styles() : void
        {
        }
        public function enqueue_frontend_scripts()
        {
        }
        public function register_frontend_scripts()
        {
        }
        public function register_preview_scripts()
        {
        }
        public function get_responsive_stylesheet_templates($templates)
        {
        }
        public function on_elementor_init()
        {
        }
        /**
         * @param \Elementor\Core\Base\Document $document
         */
        public function on_document_save_version($document)
        {
        }
        public static final function get_title()
        {
        }
    }
}
namespace ElementorPro\Base {
    abstract class Module_Base extends \Elementor\Core\Base\Module
    {
        // This class was needed in the past and not being removed for future functionalities that might be needed for all classes that still extend it.
    }
}
namespace ElementorPro\Modules\ElementManager {
    class Module extends \ElementorPro\Base\Module_Base
    {
        const LICENSE_FEATURE_NAME = 'element-manager-permissions';
        public function get_name()
        {
        }
        public function __construct()
        {
        }
    }
    class Options
    {
        public static function get_role_restrictions()
        {
        }
        public static function update_role_restrictions($role_restrictions)
        {
        }
    }
}
namespace ElementorPro\Modules\MegaMenu\Traits {
    trait Url_Helper_Trait
    {
        public function parse_url($url)
        {
        }
        public function get_permalink_for_current_page()
        {
        }
    }
}
namespace ElementorPro\Modules\MegaMenu\Controls {
    class Control_Menu_Dropdown_Animation extends \Elementor\Control_Hover_Animation
    {
        const TYPE = 'animation_menu_dropdown';
        public function get_type() : string
        {
        }
        public static function get_animations() : array
        {
        }
    }
}
namespace ElementorPro\Modules\MegaMenu {
    class Module extends \ElementorPro\Base\Module_Base
    {
        const EXPERIMENT_NAME = 'mega-menu';
        public function __construct()
        {
        }
        public function get_widgets()
        {
        }
        public function get_name()
        {
        }
        public static function is_active()
        {
        }
        /**
         * Add to the experiments
         *
         * @return array
         */
        public static function get_experimental_data()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/mega-menu/assets/scss/frontend.scss`
         * to `/assets/css/widget-mega-menu.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Base {
    trait Base_Widget_Trait
    {
        public function is_editable()
        {
        }
        public function get_categories()
        {
        }
    }
}
namespace ElementorPro\Modules\MegaMenu\Widgets {
    class Mega_Menu extends \Elementor\Modules\NestedElements\Base\Widget_Nested_Base
    {
        use \ElementorPro\Base\Base_Widget_Trait;
        use \ElementorPro\Modules\MegaMenu\Traits\Url_Helper_Trait;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function get_default_children_elements()
        {
        }
        protected function get_default_repeater_title_setting_key()
        {
        }
        protected function get_default_children_title()
        {
        }
        protected function get_default_children_placeholder_selector()
        {
        }
        protected function get_default_children_container_placeholder_selector()
        {
        }
        protected function get_html_wrapper_class()
        {
        }
        /**
         * Define a selector class for a widget control.
         *
         * @param string $item The name of the element which we need to select.
         * @param string $state The state of the selector, e.g. `:hover` or `:focus`.
         *
         * @return string The css selector for our element.
         * @since 3.12.0
         */
        protected function get_control_selector_class($control_item, $state = '')
        {
        }
        /**
         * Get Typography Selector
         *
         * Returns a selector class for the typography widget control.
         *
         * @param string $heading_selector The css selector for the menu.
         *
         * @return string The css selector for the typography control.
         */
        protected function get_typography_selector($heading_selector) : string
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        protected function render_menu_wrapper_attributes()
        {
        }
        protected function render_menu_toggle($settings)
        {
        }
        protected function render_menu_toggle_template()
        {
        }
        protected function merge_menu_title_classes($index, $item, $classes)
        {
        }
        protected function render_menu_titles_html($index, $item)
        {
        }
        public function add_attributes_to_item($key, $classes, $menu_item_id, $display_index)
        {
        }
        public function add_attributes_to_item_dropdown($key, $classes, $item_dropdown_id, $display_index, $has_dropdown_content = false, $title = '')
        {
        }
        protected function get_current_menu_item_class($menu_link_url)
        {
        }
        /**
         * Print the content area.
         *
         * @param int $index
         * @param boolean $has_dropdown_content
         * @param string $menu_item_id
         */
        public function print_child($index, $has_dropdown_content = false, $menu_item_id = '')
        {
        }
        protected function set_container_attributes($container, $menu_index, $menu_item_id)
        {
        }
        protected function item_has_dropdown_with_content($index, $children, $has_dropdown_content = false)
        {
        }
        // Any update in this function should also be updated in the content_template_single_repeater_item function too
        protected function content_template()
        {
        }
        protected function get_initial_config() : array
        {
        }
        // Any update in this function should be updated also in the content_template function too
        protected function content_template_single_repeater_item()
        {
        }
    }
}
namespace ElementorPro\Modules\ThemeBuilder\Documents {
    abstract class Theme_Document extends \Elementor\Modules\Library\Documents\Library_Document
    {
        const LOCATION_META_KEY = '_elementor_location';
        public static function get_properties()
        {
        }
        protected static function get_site_editor_route()
        {
        }
        protected static function get_site_editor_icon()
        {
        }
        protected static function get_site_editor_layout()
        {
        }
        protected static function get_site_editor_thumbnail_url()
        {
        }
        public static function get_site_editor_config()
        {
        }
        public static function get_editor_panel_config()
        {
        }
        protected function get_have_a_look_url()
        {
        }
        public static function get_create_url()
        {
        }
        protected static function get_site_editor_tooltip_data()
        {
        }
        public function get_name()
        {
        }
        public static function get_lock_behavior_v2()
        {
        }
        public function get_location_label()
        {
        }
        public function before_get_content()
        {
        }
        public function after_get_content()
        {
        }
        public function get_content($with_css = false)
        {
        }
        public function print_content()
        {
        }
        public static function get_preview_as_default()
        {
        }
        public static function get_preview_as_options()
        {
        }
        public function get_container_attributes()
        {
        }
        /**
         * @static
         * @since  2.0.0
         * @access public
         *
         * @return string
         */
        public function get_edit_url()
        {
        }
        public function get_export_summary()
        {
        }
        public function import(array $data)
        {
        }
        protected function register_controls()
        {
        }
        /**
         * @param null $elements_data
         * @since 2.9.0
         * @access public
         *
         * Overwrite method from document.php to check for user-selected tags to use as the document wrapper element
         */
        public function print_elements_with_wrapper($elements_data = null)
        {
        }
        public function get_wrapper_tags()
        {
        }
        public function get_elements_raw_data($data = null, $with_html_content = false)
        {
        }
        public function render_element($data)
        {
        }
        public function get_wp_preview_url()
        {
        }
        public function get_preview_as_query_args()
        {
        }
        public function after_preview_switch_to_query()
        {
        }
        public function get_location()
        {
        }
        public function get_initial_config()
        {
        }
    }
    abstract class Theme_Section_Document extends \ElementorPro\Modules\ThemeBuilder\Documents\Theme_Document
    {
        public static function get_properties()
        {
        }
        protected static function get_site_editor_layout()
        {
        }
        public static function get_preview_as_default()
        {
        }
        public static function get_preview_as_options()
        {
        }
        protected function get_remote_library_config()
        {
        }
    }
}
namespace ElementorPro\Modules\Popup {
    class Document extends \ElementorPro\Modules\ThemeBuilder\Documents\Theme_Section_Document
    {
        const DISPLAY_SETTINGS_META_KEY = '_elementor_popup_display_settings';
        public static function get_type()
        {
        }
        public static function get_properties()
        {
        }
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        public function get_display_settings()
        {
        }
        public function get_initial_config()
        {
        }
        public function get_name()
        {
        }
        public function get_css_wrapper_selector()
        {
        }
        public function get_display_settings_data()
        {
        }
        public function save_display_settings_data($display_settings_data)
        {
        }
        public function get_frontend_settings()
        {
        }
        public function get_export_data()
        {
        }
        public function import(array $data)
        {
        }
        protected function register_controls()
        {
        }
        protected function get_remote_library_config()
        {
        }
    }
}
namespace ElementorPro\Modules\DynamicTags\Tags\Base {
    trait Tag_Trait
    {
        public function is_editable()
        {
        }
        protected function render_taxonomy_content_by_key(string $key = 'name') : void
        {
        }
        protected function get_data_id_from_taxonomy_loop_query()
        {
        }
    }
}
namespace ElementorPro\Base {
    trait On_Import_Trait
    {
        /**
         * On import update dynamic content (e.g. post and term IDs).
         *
         * @since 3.8.0
         *
         * @param array              $element_config The config of the passed element.
         * @param array              $data           The data that requires updating/replacement when imported.
         * @param array|Element_Base $controls       The available controls.
         *
         * @return array
         */
        public static function on_import_update_dynamic_content(array $element_config, array $data, $controls = null) : array
        {
        }
        /**
         * Check if a control requires updating, and do so if needed.
         *
         * @param array $element_config
         * @param array $data
         * @param array $control
         * @param array $available_control_types
         *
         * @return array
         */
        private static function on_import_update_control(array $element_config, array $data, array $control, array $available_control_types) : array
        {
        }
        /**
         * Returns the data type that is required for updating.
         *
         * @param array $data
         * @param string $control_name
         *
         * @return array
         */
        private static function on_import_get_required_data(array $data, string $control_name) : array
        {
        }
        /**
         * Are the control values post IDs?
         *
         * @param string $control_name
         *
         * @return bool
         */
        private static function on_import_check_post_type(string $control_name) : bool
        {
        }
        /**
         * Update the value for the dynamic control.
         *
         * @param array $element_config
         * @param array $data
         * @param string $control_name
         * @param $current_value
         *
         * @return array
         */
        private static function on_import_update_control_value(array $element_config, array $data, string $control_name, $current_value) : array
        {
        }
    }
}
namespace ElementorPro\Modules\DynamicTags\Tags\Base {
    abstract class Tag extends \Elementor\Core\DynamicTags\Tag
    {
        use \ElementorPro\Modules\DynamicTags\Tags\Base\Tag_Trait;
        use \ElementorPro\Base\On_Import_Trait;
    }
}
namespace ElementorPro\Modules\Popup {
    class Tag extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        /**
         * @since 3.6.0
         *
         * @deprecated 3.8.0
         * On_Import_Trait::on_import_update_dynamic_content() should be used instead.
         * Remove in the future.
         */
        public static function on_import_replace_dynamic_content($config, $map_old_new_post_ids)
        {
        }
        public function register_controls()
        {
        }
        public function render()
        {
        }
        // Keep Empty to avoid default advanced section
        protected function register_advanced_section()
        {
        }
    }
}
namespace ElementorPro\Modules\Popup\DisplaySettings {
    abstract class Base extends \Elementor\Controls_Stack
    {
        protected function start_settings_group($group_name, $group_title)
        {
        }
        protected function end_settings_group()
        {
        }
        protected function add_settings_group_control($id, array $args)
        {
        }
        protected function get_prefixed_control_id($id)
        {
        }
    }
    class Timing extends \ElementorPro\Modules\Popup\DisplaySettings\Base
    {
        /**
         * Get element name.
         *
         * Retrieve the element name.
         *
         * @since  2.4.0
         * @access public
         *
         * @return string The name.
         */
        public function get_name()
        {
        }
        protected function register_controls()
        {
        }
    }
    class Triggers extends \ElementorPro\Modules\Popup\DisplaySettings\Base
    {
        /**
         * Get element name.
         *
         * Retrieve the element name.
         *
         * @since  2.4.0
         * @access public
         *
         * @return string The name.
         */
        public function get_name()
        {
        }
        protected function register_controls()
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Classes {
    abstract class Action_Base
    {
        public abstract function get_name();
        public abstract function get_label();
        /**
         * Get the action ID.
         *
         * TODO: Make it an abstract function that will replace `get_name()`.
         *
         * @since 3.5.0
         *
         * @return string
         */
        public function get_id()
        {
        }
        /**
         * @param Form_Record  $record
         * @param Ajax_Handler $ajax_handler
         */
        public abstract function run($record, $ajax_handler);
        /**
         * @param Form $form
         */
        public abstract function register_settings_section($form);
        /**
         * @param array $element
         */
        public abstract function on_export($element);
    }
}
namespace ElementorPro\Modules\Popup {
    class Form_Action extends \ElementorPro\Modules\Forms\Classes\Action_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function register_settings_section($widget)
        {
        }
        public function on_export($element)
        {
        }
        public function run($record, $ajax_handler)
        {
        }
        public function maybe_print_popup($settings, $widget)
        {
        }
        public function __construct()
        {
        }
    }
    class Module extends \ElementorPro\Base\Module_Base
    {
        const DOCUMENT_TYPE = 'popup';
        const PROMOTION_MENU_SLUG = 'e-popups';
        public function __construct()
        {
        }
        public function register_frontend_styles()
        {
        }
        public function enqueue_preview_styles()
        {
        }
        public function disable_editing()
        {
        }
        public function maybe_redirect_to_promotion_page()
        {
        }
        public function get_name()
        {
        }
        public function add_form_action()
        {
        }
        public static function add_popup_to_location($popup_id)
        {
        }
        public function register_documents(\Elementor\Core\Documents_Manager $documents_manager)
        {
        }
        public function register_location(\ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $location_manager)
        {
        }
        public function print_popups()
        {
        }
        public function register_tag(\Elementor\Core\DynamicTags\Manager $dynamic_tags)
        {
        }
        public function register_ajax_actions(\Elementor\Core\Common\Modules\Ajax\Module $ajax)
        {
        }
        /**
         * @throws \Exception
         */
        public function save_display_settings($data)
        {
        }
        public function add_finder_items(array $categories)
        {
        }
        public function localize_settings(array $settings) : array
        {
        }
        protected function get_assets_base_url()
        {
        }
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\Popup\AdminMenuItems {
    class Popups_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item
    {
        public function get_capability()
        {
        }
        public function get_label()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_position()
        {
        }
        public function is_visible()
        {
        }
    }
}
namespace ElementorPro\Modules\Tiers\AdminMenuItems {
    abstract class Base_Promotion_Item implements \Elementor\Modules\Promotions\AdminMenuItems\Interfaces\Promotion_Menu_Item
    {
        public function get_name()
        {
        }
        public function is_visible()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_capability()
        {
        }
        public function get_cta_text()
        {
        }
        public function get_image_url()
        {
        }
        public function get_promotion_description()
        {
        }
        public function render()
        {
        }
    }
}
namespace ElementorPro\Modules\Popup\AdminMenuItems {
    class Popups_Promotion_Menu_Item extends \ElementorPro\Modules\Tiers\AdminMenuItems\Base_Promotion_Item
    {
        public function get_name()
        {
        }
        public function get_position()
        {
        }
        public function get_cta_text()
        {
        }
        public function get_cta_url()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_label()
        {
        }
        public function get_page_title()
        {
        }
        public function get_promotion_title()
        {
        }
        public function get_promotion_description()
        {
        }
        /**
         * @deprecated use get_promotion_description instead
         * @return void
         */
        public function render_promotion_description()
        {
        }
    }
}
namespace ElementorPro\Modules\ScrollSnap {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        public function register_controls(\Elementor\Controls_Stack $controls_stack, $section_id)
        {
        }
    }
}
namespace ElementorPro\Modules\Payments\Classes {
    class Stripe_Handler
    {
        const STRIPE_ENDPOINT_URL = 'https://api.stripe.com/v1/';
        /**
         * Abstract function to create GET calls from the stripe API
         * @param string $secret_key
         * @param string $endpoint
         * @param array $body
         * @return array|\WP_Error
         */
        public function get($secret_key, $endpoint = '', $body = [])
        {
        }
        /**
         * @param $headers
         * @param $body
         * @param $endpoint
         * @return array|\WP_Error
         */
        public function post($headers, $body, $endpoint)
        {
        }
    }
    abstract class Payment_Button extends \Elementor\Widget_Button
    {
        use \ElementorPro\Base\Base_Widget_Trait;
        // Payment types.
        const PAYMENT_TYPE_CHECKOUT = 'checkout';
        const PAYMENT_TYPE_SUBSCRIPTION = 'subscription';
        const PAYMENT_TYPE_DONATION = 'donation';
        // Billing cycles.
        const BILLING_CYCLE_DAYS = 'days';
        const BILLING_CYCLE_WEEKS = 'weeks';
        const BILLING_CYCLE_MONTHS = 'months';
        const BILLING_CYCLE_YEARS = 'years';
        // Donation types.
        const DONATION_TYPE_ANY = 'any';
        const DONATION_TYPE_FIXED = 'fixed';
        // Error messages.
        const ERROR_MESSAGE_GLOBAL = 'global';
        const ERROR_MESSAGE_PAYMENT_METHOD = 'payment';
        // Retrieve the merchant display name.
        protected abstract function get_merchant_name();
        // Account details section.
        protected abstract function register_account_section();
        // Custom sandbox controls.
        protected abstract function register_sandbox_controls();
        public function get_group_name()
        {
        }
        // Render custom controls after product type.
        protected function after_product_type()
        {
        }
        // Render custom controls test toggle control.
        protected function after_custom_messages_toggle()
        {
        }
        // Edit error massage placeholders for stripe widget
        protected function update_error_massages()
        {
        }
        // Return an array of supported currencies.
        protected function get_currencies()
        {
        }
        // Return an array of default error messages.
        protected function get_default_error_messages()
        {
        }
        // Get message text by id (`error_message_$id`).
        protected function get_custom_message($id)
        {
        }
        // Product details section.
        protected function register_product_controls()
        {
        }
        // Submission settings section.
        protected function register_settings_section()
        {
        }
        // Customize the default button controls.
        protected function register_button_controls()
        {
        }
        // Add typography settings for custom messages.
        protected function register_messages_style_section()
        {
        }
        // Register widget controls.
        protected function register_controls()
        {
        }
        // Render the checkout button.
        protected function render_button(\Elementor\Widget_Base $instance = null, $tag = 'a')
        {
        }
        // Render the widget.
        protected function render()
        {
        }
        protected function content_template()
        {
        }
        // Check if it's sandbox mode.
        protected function is_sandbox()
        {
        }
    }
}
namespace ElementorPro\Modules\Payments {
    class Module extends \ElementorPro\Base\Module_Base
    {
        const STRIPE_CHECKOUT_URL_EXT = 'checkout/sessions';
        const STRIPE_TEST_SECRET_KEY = 'pro_stripe_test_secret_key';
        const STRIPE_LIVE_SECRET_KEY = 'pro_stripe_live_secret_key';
        const STRIPE_TAX_ENDPOINT_URL = 'tax_rates';
        const WP_DASH_STRIPE_API_KEYS_LINK = 'https://go.elementor.com/wp-dash-stripe-api-keys/';
        const STRIPE_TRANSACTIONS_LINK = 'https://go.elementor.com/stripe-transaction/';
        const STRIPE_LICENCE_FEATURE_NAME = 'stripe-button';
        const WIDGET_NAME_CLASS_NAME_MAP = ['paypal-button' => 'Paypal_Button', self::STRIPE_LICENCE_FEATURE_NAME => 'Stripe_Button'];
        public $secret_key = '';
        public function get_widgets()
        {
        }
        /**
         * Error handler
         *
         * @since 3.7.0
         *
         * @param integer $status_code
         * @param string $error_massage
         */
        protected function error_handler($status_code, $error_massage)
        {
        }
        public function get_name()
        {
        }
        /**
         * Reads secret test key from wp_options table
         *
         * @since 3.7.0
         *
         * @return string
         */
        public static function get_global_stripe_test_secret_key()
        {
        }
        /**
         * Reads secret live key from wp_options table
         *
         * @since 3.7.0
         *
         * @return string
         */
        public static function get_global_stripe_live_secret_key()
        {
        }
        /**
         * Integrations page secret key validations' callback function
         *
         * @since 3.7.0
         *
         * @return void
         */
        public function ajax_validate_secret_key()
        {
        }
        /**
         * Ajax callback
         *
         * Returns a list of tax rates
         *
         * @since 3.7.0
         *
         * @return array
         */
        public function register_ajax_actions($ajax)
        {
        }
        /**
         * returns a list of tax rates
         *
         * if tax rates are set in stripe admin dashboard
         * from here the tax rates array is implemented in
         * tax rates select control
         *
         * @param array $data
         *
         * @return array - returns to js ajax function.
         *
         * @throws \Exception
         * @since 3.7.0
         *
         */
        public function get_stripe_tax_rates(array $data)
        {
        }
        /**
         * Get ajax tax rates from API
         *
         * Read all ajax tax rates from stripes API and
         *
         * @since 3.7.0
         *
         * @param string $secret_key
         *
         * @return array - returns to js ajax function.
         *
         */
        protected function tax_rates_result_funnel($secret_key)
        {
        }
        /**
         * Gets and Organizes all tax rates in a
         * list suitable for the select control
         *
         * @since 3.7.0
         *
         * @param string $secret_key
         *
         * @return array - returns to js ajax function.
         *
         */
        protected function get_tax_rates($secret_key)
        {
        }
        /**
         * Create options array for tax_rates controls
         *
         * Zero decimal currencies by stripe https://stripe.com/docs/currencies#zero-decimal
         * this option is zero decimal what means that only complete numbers bill pass to stripe.
         * for example 555.55 will return product_price of 555.
         *
         * @since 3.7.0
         *
         * @param $currency string
         * @param $product_price
         *
         * @return false|float $tax_rates_options placed as the control options
         */
        public function currency_adaptation($currency, $product_price)
        {
        }
        /**
         * Secret key conditional function
         *
         * @since 3.7.0
         *
         * @param string $test_mode
         *
         * @return void
         */
        public function set_secret_key_by_environment_state($test_mode = 'no')
        {
        }
        /**
         * Ajax callback function - API stripe call .
         *
         * get stripe user data on widget load
         * sends the product data and returns the product page checkout url.
         *
         * @since 3.7.0
         */
        public function submit_stripe_form()
        {
        }
        /**
         * Builds the body for the API POST request.
         *
         * @since 3.7.0
         *
         * @param $args
         *
         * @return array
         */
        public function build_body_for_post_request($args)
        {
        }
        /**
         * API call handler
         *
         * @since 3.7.0
         *
         * @param $headers
         * @param $body
         *
         * @return void
         */
        public function execute_post_request_to_stripe_api($headers, $body)
        {
        }
        /**
         * Add secret_keys to Elementor integrations section
         *
         * @since 3.7.0
         *
         * @param Settings $settings
         */
        public function register_admin_fields(\Elementor\Settings $settings)
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\Payments\Widgets {
    class Paypal_Button extends \ElementorPro\Modules\Payments\Classes\Payment_Button
    {
        // API integration types.
        const API_TYPE_SIMPLE = 'simple';
        const API_TYPE_ADVANCED = 'advanced';
        // PayPal constants.
        const PROD_URL = 'https://www.paypal.com/cgi-bin/webscr';
        const SANDBOX_URL = 'https://sandbox.paypal.com/cgi-bin/webscr';
        const CMD_CHECKOUT = '_xclick';
        const CMD_DONATION = '_donations';
        const CMD_SUBSCRIPTION = '_xclick-subscriptions';
        const BILLING_CYCLE_TYPES = [self::BILLING_CYCLE_DAYS => 'D', self::BILLING_CYCLE_WEEKS => 'W', self::BILLING_CYCLE_MONTHS => 'M', self::BILLING_CYCLE_YEARS => 'Y'];
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function get_merchant_name()
        {
        }
        // Retrieve a numerical field from settings, and default to $min if it's too small.
        protected function get_numeric_setting($key, $min = 0)
        {
        }
        // Print a numerical field from settings, using `get_numeric_setting`.
        protected function print_numeric_setting($key, $min = 0)
        {
        }
        // Get the currently selected API communication method ( legacy / SDK ).
        protected function get_api_method()
        {
        }
        // Get validation errors.
        protected function get_errors($squash_errors = true)
        {
        }
        // Render PayPal's legacy checkout form.
        protected function render_legacy_form()
        {
        }
        // Render the payment button.
        protected function render_button(\Elementor\Widget_Base $instance = null, $tag = 'a')
        {
        }
        // Account details section.
        protected function register_account_section()
        {
        }
        /**
         * Updates Button tab controls in 'Style' tab
         *
         * @since 3.7.0
         */
        public function register_paypal_button_controls()
        {
        }
        /**
         * Edit button control initial UI
         *
         * @since 3.7.0
         *
         */
        protected function register_controls()
        {
        }
        // Custom sandbox controls.
        protected function register_sandbox_controls()
        {
        }
    }
    /**
     * Stripe_Button.
     *
     * @since 3.7.0
     */
    class Stripe_Button extends \ElementorPro\Modules\Payments\Classes\Payment_Button
    {
        /**
         * Stripe constants.
         */
        const STRIPE_PAYMENT_TYPE_CHECKOUT = 'payment';
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        protected function get_merchant_name()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Stripe currency supported list
         *
         * @since 3.7.0
         *
         * @return array
         */
        protected function get_stripe_currencies()
        {
        }
        /**
         * Global error message.
         *
         * @since 3.7.0
         *
         * @return string
         */
        protected function stripe_global_error_massage()
        {
        }
        /**
         * Gateway error message.
         *
         * @since 3.7.0
         *
         * @return string
         */
        protected function stripe_gateway_error_massage()
        {
        }
        /**
         * Get validation errors.
         *
         * @since 3.7.0
         *
         * @return array
         */
        protected function get_errors()
        {
        }
        /**
         * Render the payment button.
         *
         * @param string $tag - this is an inheritance from the payment_button class
         *
         * @since 3.7.0
         *
         * @return array
         */
        protected function render_button(\Elementor\Widget_Base $instance = null, $tag = 'a')
        {
        }
        /**
         * Registers account section
         *
         * @since 3.7.0
         */
        protected function register_account_section()
        {
        }
        /**
         * Updates Button tab controls in 'Style' tab
         *
         * @since 3.7.0
         */
        public function register_stripe_button_controls()
        {
        }
        /**
         * Edit button control initial UI
         *
         * @since 3.7.0
         *
         */
        protected function register_controls()
        {
        }
        /**
         * Update error messages controls text and placeholders.
         *
         * @since 3.7.0
         *
         */
        protected function update_error_massages()
        {
        }
        /**
         * Custom sandbox controls.
         *
         * @since 3.7.0
         *
         */
        protected function after_custom_messages_toggle()
        {
        }
        protected function register_sandbox_controls()
        {
        }
    }
}
namespace ElementorPro\Modules\ThemeBuilder\Classes {
    class Conditions_Repeater extends \Elementor\Control_Repeater
    {
        const CONTROL_TYPE = 'conditions_repeater';
        public function get_type()
        {
        }
        protected function get_default_settings()
        {
        }
    }
    class Template_Conditions extends \Elementor\Controls_Stack
    {
        public function get_name()
        {
        }
        protected function register_controls()
        {
        }
    }
    class Conditions_Cache
    {
        const OPTION_NAME = 'elementor_pro_theme_builder_conditions';
        protected $conditions = [];
        public function __construct()
        {
        }
        /**
         * @param Theme_Document $document
         * @param array          $conditions
         *
         * @return $this
         */
        public function add(\ElementorPro\Modules\ThemeBuilder\Documents\Theme_Document $document, array $conditions)
        {
        }
        /**
         * @param int $post_id
         *
         * @return $this
         */
        public function remove($post_id)
        {
        }
        /**
         * @param Theme_Document $document
         * @param array          $conditions
         *
         * @return $this
         */
        public function update($document, $conditions)
        {
        }
        public function save()
        {
        }
        public function refresh()
        {
        }
        public function clear()
        {
        }
        public function get_by_location($location)
        {
        }
        public function regenerate()
        {
        }
    }
}
namespace ElementorPro\Modules\Posts\Traits {
    trait Pagination_Trait
    {
        /**
         * Checks a set of elements if there is a posts/archive widget that may be paginated to a specific page number.
         *
         * @param array $elements
         * @param       $current_page
         *
         * @return bool
         */
        public function is_valid_pagination(array $elements, $current_page)
        {
        }
        /**
         * Get all widgets that may add pagination.
         *
         * @return array
         */
        public function get_widgets_that_support_pagination()
        {
        }
        /**
         * @return void
         */
        public function check_pagination_handler(array $posts_widgets, $current_page, &$is_valid)
        {
        }
        /**
         * @return bool
         */
        private function is_valid_post_widget($element, $posts_widgets)
        {
        }
        /**
         * @return bool
         */
        private function widget_has_pagination($element)
        {
        }
        /**
         * @return bool
         */
        private function should_allow_pagination($element, $current_page)
        {
        }
        public function get_base_url()
        {
        }
        /**
         * Determines whether the query is for an existing blog posts index page
         *
         * @param bool $custom_page_option
         * @return bool
         */
        private function is_posts_page($custom_page_option = true)
        {
        }
    }
}
namespace ElementorPro\Modules\ThemeBuilder\Classes {
    class Locations_Manager
    {
        use \ElementorPro\Modules\Posts\Traits\Pagination_Trait;
        protected $core_locations = [];
        protected $locations = [];
        protected $did_locations = [];
        protected $current_location;
        protected $current_page_template = '';
        protected $locations_queue = [];
        protected $locations_printed = [];
        protected $locations_skipped = [];
        public function __construct()
        {
        }
        /**
         * Fix WP 5.5 pagination issue.
         *
         * Return true to mark that it's handled and avoid WP to set it as 404.
         *
         * @see https://github.com/elementor/elementor/issues/12126
         * @see https://core.trac.wordpress.org/ticket/50976
         *
         * Based on the logic at \WP::handle_404.
         *
         * @param $handled - Default false.
         * @param $wp_query
         *
         * @return bool
         */
        public function should_allow_pagination_on_single_templates($handled, $wp_query)
        {
        }
        /**
         * Fix WP 5.5 pagination issue.
         *
         * Return true to mark that it's handled and avoid WP to set it as 404.
         *
         * @see https://github.com/elementor/elementor/issues/12126
         * @see https://core.trac.wordpress.org/ticket/50976
         *
         * Based on the logic at \WP::handle_404.
         *
         * @param $handled - Default false.
         * @param $wp_query
         *
         * @return bool
         */
        public function should_allow_pagination_on_archive_templates($handled, $wp_query)
        {
        }
        public function register_locations()
        {
        }
        public function enqueue_styles()
        {
        }
        public function template_include($template)
        {
        }
        /**
         * @param string $location
         * @param integer $document_id
         */
        public function add_doc_to_location($location, $document_id)
        {
        }
        public function remove_doc_from_location($location, $document_id)
        {
        }
        public function skip_doc_in_location($location, $document_id)
        {
        }
        public function is_printed($location, $document_id)
        {
        }
        public function set_is_printed($location, $document_id)
        {
        }
        public function do_location($location)
        {
        }
        public function get_documents_for_location(string $location) : array
        {
        }
        public function did_location($location)
        {
        }
        public function get_current_location()
        {
        }
        public function builder_wrapper($content)
        {
        }
        public function get_locations($filter_args = [])
        {
        }
        public function get_location($location)
        {
        }
        public function get_doc_location($post_id)
        {
        }
        public function get_core_locations()
        {
        }
        public function register_all_core_location()
        {
        }
        public function register_location($location, $args = [])
        {
        }
        public function register_core_location($location, $args = [])
        {
        }
        public function location_exits($location = '', $check_match = false)
        {
        }
        public function filter_add_location_meta_on_create_new_post($meta)
        {
        }
        public function inspector_log($args)
        {
        }
    }
    class Theme_Support
    {
        public function __construct()
        {
        }
        public function init()
        {
        }
        /**
         * @param Locations_Manager $location_manager
         */
        public function after_register_locations($location_manager)
        {
        }
        public function get_header($name)
        {
        }
        /**
         * Don't show admin bar on `wp_body_open` because the theme header HTML is ignored via `$this->get_header()`.
         *
         * @param bool $show_admin_bar
         *
         * @return bool
         */
        public function filter_admin_bar_from_body_open($show_admin_bar)
        {
        }
        public function get_footer($name)
        {
        }
    }
    class Conditions_Manager
    {
        public function __construct()
        {
        }
        public function on_untrash_post($post_id)
        {
        }
        public function admin_columns_headers($posts_columns)
        {
        }
        public function admin_columns_content($column_name, $post_id)
        {
        }
        /**
         * @access public
         *
         * @param Ajax $ajax_manager
         */
        public function register_ajax_actions($ajax_manager)
        {
        }
        /**
         * @throws \Exception
         */
        public function ajax_check_conditions_conflicts($request)
        {
        }
        public function get_conditions_conflicts_by_location($condition, $location, $ignore_post_id = null)
        {
        }
        public function get_conditions_conflicts($post_id, $condition)
        {
        }
        /**
         * @throws \Exception
         */
        public function ajax_save_theme_template_conditions($request)
        {
        }
        /**
         * @param Condition_Base $instance
         */
        public function register_condition_instance($instance)
        {
        }
        /**
         * @param $id
         *
         * @return Condition_Base|bool
         */
        public function get_condition($id)
        {
        }
        public function get_conditions_config()
        {
        }
        public function get_document_instances($post_id)
        {
        }
        public function register_conditions()
        {
        }
        public function save_conditions($post_id, $conditions)
        {
        }
        public function get_location_templates($location)
        {
        }
        public function get_theme_templates_ids($location)
        {
        }
        /**
         * @param Theme_Document $document
         *
         * @return array
         */
        public function get_document_conditions($document)
        {
        }
        protected function parse_condition($condition)
        {
        }
        /**
         * @param $location
         *
         * @return Theme_Document[]
         */
        public function get_documents_for_location($location)
        {
        }
        public function purge_post_from_cache($post_id)
        {
        }
        public function get_cache()
        {
        }
        public function clear_cache()
        {
        }
        public function clear_location_cache()
        {
        }
    }
    class Control_Media_Preview extends \Elementor\Control_Media
    {
        const CONTROL_TYPE = 'media-preview';
        public function get_type()
        {
        }
        public function content_template()
        {
        }
    }
    class Templates_Types_Manager
    {
        public function __construct()
        {
        }
        public function get_types_config($args = [])
        {
        }
        public function register_documents()
        {
        }
    }
    class Preview_Manager
    {
        public function __construct()
        {
        }
        public function filter_post_terms_taxonomy_arg($taxonomy_args)
        {
        }
        /**
         * @access public
         *
         * @param $query_vars array
         *
         * @return array
         */
        public function filter_query_control_args($query_vars)
        {
        }
        /**
         * @access public
         */
        public function switch_to_preview_query()
        {
        }
        /**
         * @access public
         */
        public function restore_current_query()
        {
        }
    }
}
namespace ElementorPro\Modules\Posts\Traits {
    trait Button_Widget_Trait
    {
        /**
         * Get button sizes.
         *
         * Retrieve an array of button sizes for the button widget.
         *
         * @since 3.4.0
         * @access public
         * @static
         *
         * @return array An array containing button sizes.
         */
        public static function get_button_sizes()
        {
        }
        protected function register_button_content_controls($args = [])
        {
        }
        protected function register_button_style_controls($args = [])
        {
        }
        /**
         * Render button widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @param \Elementor\Widget_Base|null $instance
         *
         * @since  3.4.0
         * @access protected
         */
        protected function render_button(\Elementor\Widget_Base $instance = null)
        {
        }
        /**
         * Render button text.
         *
         * Render button widget text.
         *
         * @param \Elementor\Widget_Base $instance
         *
         * @since  3.4.0
         * @access protected
         */
        protected function render_text(\Elementor\Widget_Base $instance)
        {
        }
        public function on_import($element)
        {
        }
    }
}
namespace ElementorPro\Modules\Posts\Skins {
    abstract class Skin_Base extends \Elementor\Skin_Base
    {
        use \ElementorPro\Modules\Posts\Traits\Button_Widget_Trait;
        /**
         * @var string Save current permalink to avoid conflict with plugins the filters the permalink during the post render.
         */
        protected $current_permalink;
        protected function _register_controls_actions()
        {
        }
        public function register_style_sections(\Elementor\Widget_Base $widget)
        {
        }
        public function register_controls(\Elementor\Widget_Base $widget)
        {
        }
        public function register_design_controls()
        {
        }
        protected function register_thumbnail_controls()
        {
        }
        protected function register_columns_controls()
        {
        }
        protected function register_post_count_control()
        {
        }
        protected function register_title_controls()
        {
        }
        protected function register_excerpt_controls()
        {
        }
        protected function register_read_more_controls()
        {
        }
        protected function register_link_controls()
        {
        }
        protected function get_optional_link_attributes_html()
        {
        }
        protected function register_meta_data_controls()
        {
        }
        /**
         * Style Tab
         */
        protected function register_design_layout_controls()
        {
        }
        protected function register_design_image_controls()
        {
        }
        protected function register_design_content_controls()
        {
        }
        public function render()
        {
        }
        protected function add_render_hooks()
        {
        }
        protected function remove_render_hooks()
        {
        }
        public function filter_excerpt_length()
        {
        }
        public function filter_excerpt_more($more)
        {
        }
        public function get_container_class()
        {
        }
        protected function render_thumbnail()
        {
        }
        protected function render_title()
        {
        }
        protected function render_excerpt()
        {
        }
        protected function render_read_more()
        {
        }
        protected function render_post_header()
        {
        }
        protected function render_post_footer()
        {
        }
        protected function render_text_header()
        {
        }
        protected function render_text_footer()
        {
        }
        protected function get_loop_header_widget_classes()
        {
        }
        protected function handle_no_posts_found()
        {
        }
        protected function render_loop_header()
        {
        }
        protected function render_message()
        {
        }
        protected function render_loop_footer()
        {
        }
        protected function get_pagination_format($paginate_args)
        {
        }
        protected function get_paginate_args_for_singular_post($paginate_args)
        {
        }
        protected function get_paginate_args_for_archive_with_filters($paginate_args)
        {
        }
        protected function get_paginate_args_for_rest_request($paginate_args)
        {
        }
        protected function render_meta_data()
        {
        }
        protected function render_author()
        {
        }
        protected function render_date_by_type($type = 'publish')
        {
        }
        protected function render_time()
        {
        }
        /**
         * Check if the Read More links needs to be displayed at the bottom of the Post item.
         *
         * Conditions:
         * 1) Read More aligned to the bottom
         * 2) Masonry layout not used.
         * 3) Display Read More link.
         *
         * @since 3.7.0
         *
         * @return boolean
         */
        protected function display_read_more_bottom()
        {
        }
        protected function render_comments()
        {
        }
        protected function render_post()
        {
        }
    }
    class Skin_Classic extends \ElementorPro\Modules\Posts\Skins\Skin_Base
    {
        protected function _register_controls_actions()
        {
        }
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function register_additional_design_controls()
        {
        }
    }
    trait Skin_Content_Base
    {
        protected function _register_controls_actions()
        {
        }
        public function get_title()
        {
        }
        public function register_skin_controls(\Elementor\Widget_Base $widget)
        {
        }
        public function register_thumbnail_controls()
        {
        }
        public function register_design_controls()
        {
        }
        public function register_row_gap_control()
        {
        }
        // Update selectors for full content
        public function update_image_spacing_control()
        {
        }
        protected function render_thumbnail()
        {
        }
        /**
         * Render post content.
         *
         * @param boolean     $with_wrapper - Whether to wrap the content with a div.
         * @param boolean     $with_css - Decides whether to print inline CSS before the post content.
         *
         * @return void
         */
        public function render_post_content($with_wrapper = false, $with_css = true)
        {
        }
        protected function render_post()
        {
        }
    }
    class Skin_Full_Content extends \ElementorPro\Modules\Posts\Skins\Skin_Classic
    {
        use \ElementorPro\Modules\Posts\Skins\Skin_Content_Base;
        public function get_id()
        {
        }
    }
}
namespace ElementorPro\Modules\ThemeBuilder\Skins {
    trait Posts_Archive_Skin_Base
    {
        public function render()
        {
        }
    }
    class Posts_Archive_Skin_Full_Content extends \ElementorPro\Modules\Posts\Skins\Skin_Full_Content
    {
        use \ElementorPro\Modules\Posts\Skins\Skin_Content_Base;
        use \ElementorPro\Modules\ThemeBuilder\Skins\Posts_Archive_Skin_Base;
        public function get_id()
        {
        }
        /* Remove `posts_per_page` control */
        protected function register_post_count_control()
        {
        }
    }
    class Post_Comments_Skin_Classic extends \Elementor\Skin_Base
    {
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        protected function _register_controls_actions()
        {
        }
        public function register_controls()
        {
        }
        public function render()
        {
        }
        public function comment_callback($comment, $args, $depth)
        {
        }
    }
}
namespace ElementorPro\Modules\Posts\Skins {
    class Skin_Cards extends \ElementorPro\Modules\Posts\Skins\Skin_Base
    {
        protected function _register_controls_actions()
        {
        }
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function start_controls_tab($id, $args)
        {
        }
        public function end_controls_tab()
        {
        }
        public function start_controls_tabs($id)
        {
        }
        public function end_controls_tabs()
        {
        }
        public function register_controls(\Elementor\Widget_Base $widget)
        {
        }
        public function register_design_controls()
        {
        }
        protected function register_thumbnail_controls()
        {
        }
        protected function register_meta_data_controls()
        {
        }
        public function register_additional_design_image_controls()
        {
        }
        public function register_badge_controls()
        {
        }
        public function register_avatar_controls()
        {
        }
        public function register_design_card_controls()
        {
        }
        protected function register_design_content_controls()
        {
        }
        protected function get_taxonomies()
        {
        }
        protected function render_post_header()
        {
        }
        protected function render_post_footer()
        {
        }
        protected function render_avatar()
        {
        }
        protected function render_badge()
        {
        }
        protected function render_thumbnail()
        {
        }
        protected function render_post()
        {
        }
    }
}
namespace ElementorPro\Modules\ThemeBuilder\Skins {
    class Posts_Archive_Skin_Cards extends \ElementorPro\Modules\Posts\Skins\Skin_Cards
    {
        use \ElementorPro\Modules\ThemeBuilder\Skins\Posts_Archive_Skin_Base;
        protected function _register_controls_actions()
        {
        }
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function get_container_class()
        {
        }
        /* Remove `posts_per_page` control */
        protected function register_post_count_control()
        {
        }
    }
    class Posts_Archive_Skin_Classic extends \ElementorPro\Modules\Posts\Skins\Skin_Classic
    {
        use \ElementorPro\Modules\ThemeBuilder\Skins\Posts_Archive_Skin_Base;
        protected function _register_controls_actions()
        {
        }
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function get_container_class()
        {
        }
        /* Remove `posts_per_page` control */
        protected function register_post_count_control()
        {
        }
    }
}
namespace ElementorPro\Modules\ThemeBuilder\ThemeSupport {
    class GeneratePress_Theme_Support
    {
        /**
         * @param Locations_Manager $manager
         */
        public function register_locations($manager)
        {
        }
        public function metabox_capability($capability)
        {
        }
        public function do_header()
        {
        }
        public function do_footer()
        {
        }
        public function body_classes($classes)
        {
        }
        public function __construct()
        {
        }
    }
    class Safe_Mode_Theme_Support
    {
        /**
         * @param Locations_Manager $manager
         */
        public function register_locations($manager)
        {
        }
        public function do_header()
        {
        }
        public function do_footer()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\ThemeBuilder {
    class Module extends \ElementorPro\Base\Module_Base
    {
        const ADMIN_LIBRARY_TAB_GROUP = 'theme';
        const ADMIN_MENU_PRIORITY = 15;
        public static function is_preview()
        {
        }
        public static function get_public_post_types($args = [])
        {
        }
        public function get_name()
        {
        }
        public function get_widgets()
        {
        }
        /**
         * @return Classes\Conditions_Manager
         */
        public function get_conditions_manager()
        {
        }
        /**
         * @return Classes\Locations_Manager
         */
        public function get_locations_manager()
        {
        }
        /**
         * @return Classes\Preview_Manager
         */
        public function get_preview_manager()
        {
        }
        /**
         * @return Classes\Templates_Types_Manager
         */
        public function get_types_manager()
        {
        }
        /**
         * @param $post_id
         *
         * @return Theme_Document
         */
        public function get_document($post_id)
        {
        }
        public function document_config($config, $post_id)
        {
        }
        public function register_controls(\Elementor\Controls_Manager $controls_manager)
        {
        }
        public function create_new_dialog_types($types)
        {
        }
        public function print_location_field()
        {
        }
        public function print_post_type_field()
        {
        }
        public function admin_head()
        {
        }
        /**
         * An hack to hide the app menu on before render without remove the app page from system.
         *
         * @param $menu
         *
         * @return mixed
         */
        public function hide_admin_app_submenu($menu)
        {
        }
        public function admin_columns_content($column_name, $post_id)
        {
        }
        public function get_template_type($post_id)
        {
        }
        public function is_theme_template($post_id)
        {
        }
        public function on_elementor_editor_init()
        {
        }
        public function add_finder_items(array $categories)
        {
        }
        public function print_new_theme_builder_promotion($views)
        {
        }
        /**
         * Get the conflicts between the active templates' conditions and new templates.
         *
         * @since 3.8.0
         *
         * @param array $templates
         * @return array
         */
        public function get_conditions_conflicts(array $templates) : array
        {
        }
        /**
         * Add attributes to the document wrapper element.
         *
         * @param array $attributes - The document's wrapper element attributes.
         * @param Document $document
         *
         * @return array
         */
        public function add_document_attributes(array $attributes, \Elementor\Core\Base\Document $document) : array
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\ThemeBuilder\Conditions {
    abstract class Condition_Base extends \Elementor\Controls_Stack
    {
        protected $sub_conditions = [];
        public static function get_priority()
        {
        }
        public abstract function get_label();
        public function get_unique_name()
        {
        }
        public static function get_type()
        {
        }
        public function check($args)
        {
        }
        public function get_sub_conditions()
        {
        }
        public function get_all_label()
        {
        }
        protected function get_initial_config()
        {
        }
        public function register_sub_conditions()
        {
        }
        /**
         * @param self $condition
         */
        public function register_sub_condition($condition)
        {
        }
        public function __construct(array $data = [])
        {
        }
    }
    class Child_Of extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public static function get_type()
        {
        }
        public static function get_priority()
        {
        }
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function check($args)
        {
        }
        protected function register_controls()
        {
        }
    }
    class Any_Child_Of extends \ElementorPro\Modules\ThemeBuilder\Conditions\Child_Of
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function check($args)
        {
        }
    }
    class Taxonomy extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public static function get_type()
        {
        }
        public static function get_priority()
        {
        }
        public function __construct($data)
        {
        }
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function check($args)
        {
        }
        protected function register_controls()
        {
        }
    }
    class Child_Of_Term extends \ElementorPro\Modules\ThemeBuilder\Conditions\Taxonomy
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function __construct($data)
        {
        }
        public function is_term()
        {
        }
        public function check($args)
        {
        }
    }
    class Any_Child_Of_Term extends \ElementorPro\Modules\ThemeBuilder\Conditions\Child_Of_Term
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function __construct($data)
        {
        }
        public function check($args)
        {
        }
    }
    class Post_Type_By_Author extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public static function get_type()
        {
        }
        public static function get_priority()
        {
        }
        public function __construct($post_type)
        {
        }
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function check($args = null)
        {
        }
        protected function register_controls()
        {
        }
    }
    class Singular extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        protected $sub_conditions = ['front_page'];
        public static function get_type()
        {
        }
        public function get_name()
        {
        }
        public static function get_priority()
        {
        }
        public function get_label()
        {
        }
        public function get_all_label()
        {
        }
        public function register_sub_conditions()
        {
        }
        public function check($args)
        {
        }
    }
    class Archive extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        protected $sub_conditions = ['author', 'date', 'search'];
        public static function get_type()
        {
        }
        public static function get_priority()
        {
        }
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function get_all_label()
        {
        }
        public function register_sub_conditions()
        {
        }
        public function check($args)
        {
        }
    }
    class Post extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public static function get_type()
        {
        }
        public static function get_priority()
        {
        }
        public function __construct($data)
        {
        }
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function get_all_label()
        {
        }
        public function check($args)
        {
        }
        public function register_sub_conditions()
        {
        }
        protected function register_controls()
        {
        }
    }
    class Post_Type_Archive extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public static function get_type()
        {
        }
        public static function get_priority()
        {
        }
        public function __construct($data)
        {
        }
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function get_all_label()
        {
        }
        public function register_sub_conditions()
        {
        }
        public function check($args)
        {
        }
    }
    class In_Taxonomy extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public static function get_type()
        {
        }
        public static function get_priority()
        {
        }
        public function __construct($data)
        {
        }
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function check($args)
        {
        }
        protected function register_controls()
        {
        }
    }
    class Search extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public static function get_type()
        {
        }
        public static function get_priority()
        {
        }
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function check($args)
        {
        }
    }
    class By_Author extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public static function get_type()
        {
        }
        public static function get_priority()
        {
        }
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function check($args = null)
        {
        }
        protected function register_controls()
        {
        }
    }
    class Not_Found404 extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public static function get_type()
        {
        }
        public static function get_priority()
        {
        }
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function check($args)
        {
        }
    }
    class Date extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public static function get_type()
        {
        }
        public static function get_priority()
        {
        }
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function check($args)
        {
        }
    }
    class In_Sub_Term extends \ElementorPro\Modules\ThemeBuilder\Conditions\In_Taxonomy
    {
        public function __construct($data)
        {
        }
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function check($args)
        {
        }
    }
    class General extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        protected $sub_conditions = ['archive', 'singular'];
        public static function get_type()
        {
        }
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function get_all_label()
        {
        }
        public function check($args)
        {
        }
    }
    class Author extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public static function get_type()
        {
        }
        public static function get_priority()
        {
        }
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function check($args = null)
        {
        }
        protected function register_controls()
        {
        }
    }
    class Front_Page extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public static function get_type()
        {
        }
        public static function get_priority()
        {
        }
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function check($args)
        {
        }
    }
}
namespace ElementorPro\Modules\ThemeBuilder\Documents {
    abstract class Theme_Page_Document extends \ElementorPro\Modules\ThemeBuilder\Documents\Theme_Document
    {
        /**
         * Document sub type meta key.
         */
        const REMOTE_CATEGORY_META_KEY = '_elementor_template_sub_type';
        public function get_css_wrapper_selector()
        {
        }
        public static function get_properties()
        {
        }
        protected function register_controls()
        {
        }
        /**
         * Add body classes.
         *
         * Add the body classes for the `style` controls selector.
         *
         * @param $body_classes
         *
         * @return array
         */
        public function filter_body_classes($body_classes)
        {
        }
        public function __construct(array $data = [])
        {
        }
    }
    abstract class Archive_Single_Base extends \ElementorPro\Modules\ThemeBuilder\Documents\Theme_Page_Document
    {
        /**
         * Document sub type meta key.
         */
        const REMOTE_CATEGORY_META_KEY = '_elementor_template_sub_type';
        public static function get_sub_type()
        {
        }
        public static function get_create_url()
        {
        }
        /**
         * @access public
         */
        public function save_template_type()
        {
        }
    }
    abstract class Single_Base extends \ElementorPro\Modules\ThemeBuilder\Documents\Archive_Single_Base
    {
        public static function get_properties()
        {
        }
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        public static function get_editor_panel_config()
        {
        }
        protected static function get_editor_panel_categories()
        {
        }
        public function before_get_content()
        {
        }
        public function after_get_content()
        {
        }
        public function get_container_attributes()
        {
        }
        public function print_content()
        {
        }
        protected function register_controls()
        {
        }
        public static function get_preview_as_options()
        {
        }
        public function get_depended_widget()
        {
        }
        public function get_elements_data($status = \Elementor\DB::STATUS_PUBLISH)
        {
        }
        public function preview_error_handler()
        {
        }
    }
    class Single_Page extends \ElementorPro\Modules\ThemeBuilder\Documents\Single_Base
    {
        public static function get_type()
        {
        }
        public static function get_sub_type()
        {
        }
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        protected static function get_site_editor_icon()
        {
        }
        protected static function get_site_editor_tooltip_data()
        {
        }
        protected function get_remote_library_config()
        {
        }
    }
    class Archive extends \ElementorPro\Modules\ThemeBuilder\Documents\Archive_Single_Base
    {
        public static function get_properties()
        {
        }
        public static function get_type()
        {
        }
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        protected static function get_site_editor_icon()
        {
        }
        protected static function get_site_editor_tooltip_data()
        {
        }
        protected static function get_editor_panel_categories()
        {
        }
        public static function get_preview_as_default()
        {
        }
        public static function get_preview_as_options()
        {
        }
    }
    class Section extends \ElementorPro\Modules\ThemeBuilder\Documents\Theme_Section_Document
    {
        public function get_name()
        {
        }
        public static function get_type()
        {
        }
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        public static function get_properties()
        {
        }
        protected function register_controls()
        {
        }
        public function get_export_data()
        {
        }
        public function save_settings($settings)
        {
        }
    }
    abstract class Header_Footer_Base extends \ElementorPro\Modules\ThemeBuilder\Documents\Theme_Section_Document
    {
        public function get_css_wrapper_selector()
        {
        }
        protected static function get_editor_panel_categories()
        {
        }
        protected function register_controls()
        {
        }
        protected function get_remote_library_config()
        {
        }
    }
    class Header extends \ElementorPro\Modules\ThemeBuilder\Documents\Header_Footer_Base
    {
        public static function get_properties()
        {
        }
        public static function get_type()
        {
        }
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        protected static function get_site_editor_icon()
        {
        }
        protected static function get_site_editor_tooltip_data()
        {
        }
    }
    class Footer extends \ElementorPro\Modules\ThemeBuilder\Documents\Header_Footer_Base
    {
        public static function get_properties()
        {
        }
        public static function get_type()
        {
        }
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        protected static function get_site_editor_icon()
        {
        }
        protected static function get_site_editor_tooltip_data()
        {
        }
    }
    class Single extends \ElementorPro\Modules\ThemeBuilder\Documents\Single_Base
    {
        public static function get_type()
        {
        }
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        protected function get_remote_library_config()
        {
        }
        protected static function get_site_editor_thumbnail_url()
        {
        }
    }
    class Search_Results extends \ElementorPro\Modules\ThemeBuilder\Documents\Archive
    {
        public function get_name()
        {
        }
        public static function get_type()
        {
        }
        public static function get_sub_type()
        {
        }
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        protected static function get_site_editor_icon()
        {
        }
        protected static function get_site_editor_tooltip_data()
        {
        }
        public static function get_preview_as_default()
        {
        }
        public static function get_preview_as_options()
        {
        }
        protected function get_remote_library_config()
        {
        }
    }
    class Single_Post extends \ElementorPro\Modules\ThemeBuilder\Documents\Single_Base
    {
        public static function get_type()
        {
        }
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        protected static function get_site_editor_icon()
        {
        }
        protected static function get_site_editor_tooltip_data()
        {
        }
        protected function get_remote_library_config()
        {
        }
    }
    class Error_404 extends \ElementorPro\Modules\ThemeBuilder\Documents\Single_Base
    {
        public static function get_type()
        {
        }
        public static function get_sub_type()
        {
        }
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        protected static function get_site_editor_icon()
        {
        }
        protected static function get_site_editor_tooltip_data()
        {
        }
        public static function get_preview_as_options()
        {
        }
        protected function get_remote_library_config()
        {
        }
    }
}
namespace ElementorPro\Modules\ThemeBuilder\AdminMenuItems {
    class Theme_Builder_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item
    {
        public function get_capability()
        {
        }
        public function get_label()
        {
        }
        public function get_parent_slug()
        {
        }
        public function is_visible()
        {
        }
        public function get_position()
        {
        }
    }
}
namespace ElementorPro\Modules\ThemeBuilder\Widgets {
    abstract class Title_Widget_Base extends \Elementor\Widget_Heading
    {
        use \ElementorPro\Base\Base_Widget_Trait;
        protected abstract function get_dynamic_tag_name();
        protected function should_show_page_title()
        {
        }
        protected function register_controls()
        {
        }
        protected function get_html_wrapper_class()
        {
        }
        public function render()
        {
        }
    }
    class Page_Title extends \ElementorPro\Modules\ThemeBuilder\Widgets\Title_Widget_Base
    {
        protected function get_dynamic_tag_name()
        {
        }
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
    }
}
namespace ElementorPro\Base {
    abstract class Base_Widget extends \Elementor\Widget_Base
    {
        use \ElementorPro\Base\Base_Widget_Trait;
        use \ElementorPro\Base\On_Import_Trait;
    }
}
namespace ElementorPro\Modules\Posts\Widgets {
    /**
     * Class Posts
     */
    abstract class Posts_Base extends \ElementorPro\Base\Base_Widget
    {
        use \ElementorPro\Modules\Posts\Traits\Button_Widget_Trait;
        use \ElementorPro\Modules\Posts\Traits\Pagination_Trait;
        const LOAD_MORE_ON_CLICK = 'load_more_on_click';
        const LOAD_MORE_INFINITE_SCROLL = 'load_more_infinite_scroll';
        /**
         * @var \WP_Query
         */
        protected $query = null;
        protected $_has_template_content = false;
        public function get_icon()
        {
        }
        public function get_script_depends()
        {
        }
        public function get_query()
        {
        }
        public function render()
        {
        }
        public function register_load_more_button_style_controls()
        {
        }
        public function register_load_more_message_style_controls()
        {
        }
        public function register_pagination_section_controls()
        {
        }
        public abstract function query_posts();
        public function get_current_page()
        {
        }
        public function is_rest_request()
        {
        }
        public function get_wp_link_page($i)
        {
        }
        public function is_allow_to_use_custom_page_option()
        {
        }
        protected function get_base_url_for_rest_request($post_id, $url)
        {
        }
        protected function get_wp_link_page_url_for_preview($post, $query_args, $url)
        {
        }
        protected function get_wp_link_page_url_for_rest_request($url, $link_unescaped)
        {
        }
        protected function get_wp_link_page_url_for_normal_page_load($url)
        {
        }
        public function current_url_contains_taxonomy_filter()
        {
        }
        public function referer_contains_taxonomy_filter()
        {
        }
        protected function format_query_string_concatenation($input)
        {
        }
        public function get_posts_nav_link($page_limit = null)
        {
        }
        protected function register_controls()
        {
        }
        protected function get_pagination_type_options()
        {
        }
        public function render_plain_content()
        {
        }
    }
}
namespace ElementorPro\Modules\ThemeBuilder\Widgets {
    /**
     * Class Posts
     */
    class Archive_Posts extends \ElementorPro\Modules\Posts\Widgets\Posts_Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_skins()
        {
        }
        protected function register_controls()
        {
        }
        public function register_advanced_section_controls()
        {
        }
        public function query_posts()
        {
        }
    }
    class Post_Title extends \ElementorPro\Modules\ThemeBuilder\Widgets\Title_Widget_Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function get_dynamic_tag_name()
        {
        }
        public function get_common_args()
        {
        }
    }
    class Post_Featured_Image extends \Elementor\Widget_Image
    {
        use \ElementorPro\Base\Base_Widget_Trait;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function register_controls()
        {
        }
        protected function get_html_wrapper_class()
        {
        }
    }
    class Site_Logo extends \Elementor\Widget_Image
    {
        use \ElementorPro\Base\Base_Widget_Trait;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function register_controls()
        {
        }
        /**
         * TODO: Remove this method when Elementor Core 3.11.0 is required.
         * Duplicate of render() method from Elementor\Widget_Image class, so it will use the get_link_url() method.
         *
         * @return void
         */
        protected function render()
        {
        }
        protected function get_html_wrapper_class()
        {
        }
        protected function get_link_url($settings)
        {
        }
    }
    class Site_Title extends \Elementor\Widget_Heading
    {
        use \ElementorPro\Base\Base_Widget_Trait;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        protected function register_controls()
        {
        }
        protected function get_html_wrapper_class()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
    }
    class Archive_Title extends \ElementorPro\Modules\ThemeBuilder\Widgets\Title_Widget_Base
    {
        protected function get_dynamic_tag_name()
        {
        }
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
    }
    class Post_Content extends \ElementorPro\Base\Base_Widget
    {
        use \ElementorPro\Modules\Posts\Skins\Skin_Content_Base;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        public function show_in_panel()
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
    }
    class Post_Excerpt extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
    }
}
namespace ElementorPro\Modules\Woocommerce\Settings {
    class Settings_Woocommerce extends \Elementor\Core\Kits\Documents\Tabs\Tab_Base
    {
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_group()
        {
        }
        public function get_help_url()
        {
        }
        protected function register_tab_controls()
        {
        }
        public function on_save($data)
        {
        }
        /**
         * @return array
         */
        public function get_notices_promotion_data()
        {
        }
    }
}
namespace ElementorPro\Modules\Woocommerce\Traits {
    trait Product_Id_Trait
    {
        public function get_product($product_id = false)
        {
        }
        private function product_already_queried($product) : bool
        {
        }
        public function get_product_variation()
        {
        }
    }
    trait Send_App_Plg_Trait
    {
        public function maybe_add_send_app_promotion_control($widget) : void
        {
        }
    }
    trait Products_Trait
    {
        private $product_query_types = ['cross_sells', 'related_products', 'upsells'];
        private $product_query_controls_to_hide = ['avoid_duplicates', 'date_after', 'date_before', 'exclude', 'exclude_authors', 'exclude_ids', 'exclude_term_ids', 'include', 'include_authors', 'include_term_ids', 'offset', 'query_exclude', 'query_include', 'select_date'];
        private $product_query_group_control_name;
        private $product_query_control_args;
        private $product_query_post_type_control_id;
        /**
         * Get Product Query Fields Options
         *
         * Returns an array of options for controls in the Query group control specific for products-related queries.
         *
         * @since 3.8.0
         *
         * @return array
         */
        private function get_query_fields_options()
        {
        }
        private function init_query_settings($name)
        {
        }
        /**
         * @return array
         */
        private function get_query_control_args()
        {
        }
        private function get_query_exclude_conditions()
        {
        }
        private function add_query_not_supported_types($control_name, $fields)
        {
        }
        /**
         * @return string
         */
        private function get_query_post_type_control_id()
        {
        }
        private function add_query_controls($name)
        {
        }
    }
}

namespace ElementorPro\Modules\LoopBuilder\Traits {
    trait Alternate_Templates_Trait
    {
        private $current_post_index = 0;
        private $alternate_templates = [];
        private $rendered_alternate_templates = [];
        private $has_alternate_templates = false;
        private $has_static_alternate_templates = false;
        private $query_contains_static_alternate_templates = false;
        private $static_alternate_template_query_data = [];
        /**
         * Query Posts For Alternate Templates
         *
         * Construct `static_alternate_template_query_data` and modify the widget query if the user has
         * added any valid 'static' alternate templates. If we do need to display any static templates - based on the existing
         * query - we do a new custom query that uses the `static_alternate_template_query_data['page_settings']` to
         * handle pagination by specifying an `offset` in the query, and we increase the resulting `$query->found_posts`
         * and `$query->max_num_pages` so the pagination, and any standard WP Query elements, display as expected.
         *
         * @return false|\WP_Query
         */
        public function query_posts_for_alternate_templates()
        {
        }
        /**
         * Init Alternate Templates Settings
         *
         * Improve performance by storing the `alternate_templates` repeater settings, so we don't use
         * `get_settings_for_display()` each time we check if a post should use an alternate template.
         *
         * At the same time we store `$has_alternate_templates` and `$has_static_alternate_templates` used by their
         * accompanying helper functions, for efficiency, and so we don't need to loop through the `alternate_templates`
         * each time we check.
         *
         * We also re-arrange the `alternate_templates` array for two reasons:
         * (1) The last template added by the user should take preference, so we reverse the array so that when we loop
         * through the repeater settings array to check if a post should use an alternate template, we find the last
         * added template first.
         * (2) 'Static' alternate templates should take preference over 'non-static' templates, so we group all static
         * templates before first, so when we loop through the array to check if a post should use an alternate template
         * we find the 'static' template first.
         *
         * @return void
         */
        private function init_alternate_template_settings() : void
        {
        }
        private function alternate_template_before_skin_render() : void
        {
        }
        private function alternate_template_after_skin_render() : void
        {
        }
        /**
         * @return void
         */
        private function reset_alternate_template_data()
        {
        }
        /**
         * @return void
         */
        private function maybe_add_alternate_template_wrapper_classes() : void
        {
        }
        /**
         * @return void
         */
        private function maybe_remove_alternate_template_wrapper_classes() : void
        {
        }
        /**
         * @param $attributes
         * @return array
         */
        public function add_alternate_template_wrapper_classes($attributes) : array
        {
        }
        /**
         * @param $attributes
         * @return array
         */
        public function add_alternate_template_editor_wrapper_classes($attributes) : array
        {
        }
        /**
         * @return void
         */
        private function render_post_if_widget_has_alternate_templates() : void
        {
        }
        /**
         * @param $template_id
         * @return void
         */
        private function store_rendered_alternate_templates($template_id) : void
        {
        }
        /**
         * Has Alternate Templates
         *
         * Has the user added any alternate templates to the widget.
         *
         * Improve performance by storing `has_alternate_templates` once when the widget is rendered
         * to avoid using `get_settings_for_display()` each time.
         *
         * @return bool
         */
        private function has_alternate_templates() : bool
        {
        }
        /**
         * Has Static Alternate Templates
         *
         * Has the user added any 'static' alternate templates to the widget.
         *
         * Improve performance by storing `has_static_alternate_templates` once when the widget is rendered
         * to avoid iterating through the repeater settings each time.
         *
         * @return bool
         */
        private function has_static_alternate_templates() : bool
        {
        }
        /**
         * Query Contains Static Alternate Templates
         *
         * After constructing the `init_static_alternate_template_query_data` array, we want to make sure - based on
         * the alternate templates settings - that we definitely have valid 'static' alternate templates to display.
         *
         * This flag is used to avoid modifying the query or re-rendering posts if we don't have to.
         *
         * @return bool
         */
        private function query_contains_static_alternate_templates() : bool
        {
        }
        /**
         * Init Static Alternate Template Query Data
         *
         * Construct `static_alternate_template_query_data` if the user has added any valid 'static' alternate templates.
         * Used to modify the widget query and when rendering a post.
         *
         * @param $query
         * @return void
         */
        private function init_static_alternate_template_query_data($required_posts_count) : void
        {
        }
        /**
         * Set Static Alternate Template Query Data Item
         *
         * Store which template to use when each post is rendered.
         *
         * `init_static_alternate_template_query_data()` stores each template's data in the
         * `static_alternate_template_query_data['templates']` and, at the same time, stores
         * `static_alternate_template_query_data['page_settings']` used to adjust the query.
         *
         * @param $template
         * @param $current_post_index
         * @param $static_alternate_template_count
         * @param $posts_per_page
         * @return void
         */
        private function set_static_alternate_template_query_data_item($template, $current_post_index, $static_alternate_template_count, $posts_per_page) : void
        {
        }
        /**
         * Get Static Alternate Template Current Page Settings
         *
         * Used to modify the widget query and when rendering a post.
         *
         * @return array|bool
         */
        private function get_static_alternate_template_current_page_settings()
        {
        }
        /**
         * Get Static Alternate Template Query Offset
         *
         * Used to modify the widget query.
         *
         * @return array|bool
         */
        private function get_static_alternate_template_query_offset()
        {
        }
        /**
         * Get Static Alternate Template Start Index
         *
         * Used when calling `render_post_if_widget_has_alternate_templates()`.
         *
         * @return array|bool
         */
        private function get_static_alternate_template_start_index()
        {
        }
        /**
         * Get Static Alternate Template Adjusted Found Posts
         *
         * Used to modify the widget query.
         *
         * @return int
         */
        private function get_static_alternate_template_adjusted_found_posts() : int
        {
        }
        /**
         * Get Static Alternate Template Adjusted Max Num Pages
         *
         * Used to modify the widget query.
         *
         * @return float
         */
        private function get_static_alternate_template_adjusted_max_num_pages() : float
        {
        }
        /**
         * Get Data For Static Alternate Template
         *
         * Used when rendering the current post.
         *
         * @param $index
         * @return array
         */
        private function get_data_for_static_alternate_template($index) : array
        {
        }
        /**
         * @return array
         */
        private function get_template_data_for_current_post()
        {
        }
        /**
         * @return int
         */
        private function get_current_post_index()
        {
        }
        /**
         * @param $index
         * @return array
         */
        private function get_template_data_by_index($index) : array
        {
        }
        /**
         * @return array
         */
        private function get_default_template() : array
        {
        }
        /**
         * @param $alternate_template
         * @return bool
         */
        private function is_alternate_template($alternate_template) : bool
        {
        }
        /**
         * @param $alternate_template
         * @return bool
         */
        private function is_alternate_template_static_position($alternate_template) : bool
        {
        }
        /**
         * @param $alternate_template
         * @return bool
         */
        private function is_alternate_template_show_once($alternate_template) : bool
        {
        }
        /**
         * @param $number_to_check
         * @param $multiple_to_check
         * @return bool
         */
        private function is_repeating_alternate_template_multiple_of($number_to_check, $multiple_to_check) : bool
        {
        }
        /**
         * @param $template
         * @return bool
         */
        private function is_alternate_template_first_occurrence($template) : bool
        {
        }
        /**
         * @param $alternate_template
         * @param $current_item_index
         * @return bool
         */
        private function should_show_alternate_template_once($alternate_template, $current_item_index) : bool
        {
        }
        /**
         * @param $alternate_template
         * @param $current_item_index
         * @return bool
         */
        private function should_show_repeating_alternate_template($alternate_template, $current_item_index) : bool
        {
        }
    }
}
namespace ElementorPro\Modules\LoopBuilder\Files\Css {
    trait Loop_Css_Trait
    {
        /**
         * Printed With CSS.
         *
         * Holds the list of printed files when `$with_css` is true.
         *
         * @access protected
         *
         * @var array
         */
        private static $printed_with_css = [];
        /**
         * Use external file.
         *
         * Whether to use external CSS file of not. Overwrites a parent method. In the Editor, internal embedding needs
         * to be disabled, because it causes the Loop Document (Template) CSS to be printed inline before each loop item.
         *
         * @access protected
         *
         * @return bool True if using an external file is needed, false if not.
         */
        protected function use_external_file()
        {
        }
        /**
         * @param array $fonts
         * @return void
         */
        private function enqueue_fonts(array $fonts)
        {
        }
        /**
         * @param $icon_fonts
         * @return void
         */
        private function enqueue_icon_fonts($icon_fonts)
        {
        }
        private function enqueue_font_links()
        {
        }
        /**
         * @param array $early_access_google_fonts
         * @return void
         */
        private function print_early_access_google_font_link_tags(array $early_access_google_fonts)
        {
        }
        private function print_fonts_links()
        {
        }
        public function enqueue_and_print_font_links()
        {
        }
        public function print_all_css(int $post_id)
        {
        }
        private function get_custom_css($post_id)
        {
        }
        public function print_css()
        {
        }
        public function print_dynamic_css($post_id, $post_id_for_data)
        {
        }
    }
}
namespace ElementorPro\Modules\Posts\Traits {
    trait Query_Note_Trait
    {
        public function is_editing_archive_template()
        {
        }
        public function inject_archive_query_note($placement_id, $condition_id, $widget)
        {
        }
    }
}
namespace ElementorPro\Modules\LoopBuilder\Skins {
    /**
     * Loop Base
     *
     * Base Skin for Loop widgets.
     *
     * @since 3.8.0
     */
    class Skin_Loop_Base extends \ElementorPro\Modules\Posts\Skins\Skin_Base
    {
        use \ElementorPro\Modules\LoopBuilder\Traits\Alternate_Templates_Trait;
        use \ElementorPro\Modules\LoopBuilder\Files\Css\Loop_Css_Trait;
        use \ElementorPro\Modules\Posts\Traits\Query_Note_Trait;
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        /**
         * Register Query Controls
         *
         * Registers the controls for the query used by the Loop.
         *
         * @since 3.8.0
         */
        public function register_query_controls(\ElementorPro\Modules\LoopBuilder\Widgets\Base $widget)
        {
        }
        protected function maybe_add_load_more_wrapper_class()
        {
        }
        public function query_posts()
        {
        }
        /**
         * Enqueue Loop Document CSS Meta
         *
         * Process the template before beginning to loop through the items. This ensures that
         * elements with dynamic CSS are identified before each individual item is rendered.
         *
         * @param int $post_id
         *
         * @return void
         */
        protected function enqueue_loop_document_css_meta($post_id)
        {
        }
        public function render()
        {
        }
        protected function add_render_hooks()
        {
        }
        protected function remove_render_hooks()
        {
        }
        public function filter_off_canvas_id($off_canvas_id)
        {
        }
        protected function handle_no_posts_found()
        {
        }
        protected function get_loop_header_widget_classes()
        {
        }
        protected function _register_controls_actions()
        {
        }
        /**
         * Render Post
         *
         * Uses the chosen custom template to render Loop posts.
         *
         * @since 3.8.0
         */
        protected function render_post()
        {
        }
        protected function render_loop_header()
        {
        }
        protected function render_loop_footer()
        {
        }
        /**
         * Render Empty View
         *
         * Renders the Loop widget's view if there is no default template (empty view).
         *
         * @since 3.8.0
         */
        protected function render_empty_view()
        {
        }
    }
}
namespace ElementorPro\Modules\WooCommerce\Skins {
    /**
     * Loop Products
     *
     * Skin for Product queries in Loop widgets.
     *
     * @since 3.8.0
     */
    class Skin_Loop_Product extends \ElementorPro\Modules\LoopBuilder\Skins\Skin_Loop_Base
    {
        use \ElementorPro\Modules\Woocommerce\Traits\Products_Trait;
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function render()
        {
        }
        /**
         * Register Query Controls
         *
         * Registers the controls for the query used by the Loop.
         *
         * @since 3.8.0
         */
        public function register_query_controls(\ElementorPro\Modules\LoopBuilder\Widgets\Base $widget)
        {
        }
        protected function render_post()
        {
        }
    }
}
namespace ElementorPro\Modules\Woocommerce\Skins {
    /**
     * Class Skin_Classic
     * @property Products $parent
     */
    class Skin_Classic extends \Elementor\Skin_Base
    {
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        protected function _register_controls_actions()
        {
        }
        public function register_controls(\Elementor\Widget_Base $widget)
        {
        }
        public function render()
        {
        }
    }
}
namespace ElementorPro\Modules\LoopFilter\Traits {
    trait Hierarchical_Taxonomy_Trait
    {
        /**
         * @param \WP_Term[] $terms
         * @param int $target_depth
         * @return \WP_Term[]
         */
        public function filter_child_terms_by_depth($terms, $target_depth)
        {
        }
        /**
         * @param \WP_Term[] $terms
         * @param \WP_Term $current_term
         * @param int $target_depth
         * @return void
         */
        private function filter_single_term(&$result, $terms, $current_term, $target_depth)
        {
        }
        /**
         * @param \WP_Term[] $terms
         * @param \WP_Term $child_term
         * @param int $depth
         * @return int|void
         */
        private function calculate_depth_for_child_term($terms, $child_term, $depth)
        {
        }
        /**
         * Transform terms hierarchy structure to plain [ parent_term_id => [ term, term ... ], ...] to [ term, term, ... ]
         *
         * @param array $taxonomy_plain_view
         * @param array $hierarchy_terms
         * @param int $parent_term_id
         * @return void
         */
        public function transform_taxonomy_hierarchy_to_plain(&$taxonomy_plain_view, $hierarchy_terms, $parent_term_id = 0)
        {
        }
    }
    trait Taxonomy_Filter_Trait
    {
        use \ElementorPro\Modules\LoopFilter\Traits\Hierarchical_Taxonomy_Trait;
        protected function get_taxonomy_options(array $post_types, $key_prefix = '')
        {
        }
        private function get_loop_widget_data($document, $selected_element = null)
        {
        }
        private function get_loop_widget($selected_element)
        {
        }
        /**
         * Adjusts Elementor query arguments to prevent taxonomy filter conflicts.
         *
         * Resolves an issue where the taxonomy filter unintentionally affects itself
         * when a Loop Grid widget is filtered by taxonomy. Without this adjustment,
         * taxonomy terms added by the filter itself may be included in the query results,
         * leading to unexpected behavior.
         *
         * This function ensures that only taxonomy terms specified via the query control
         * (with the `term_taxonomy_id` field) are considered in the `tax_query`,
         * excluding any others introduced by the filter.
         *
         * @param array $query_args The query arguments for the Elementor widget.
         * @param object $widget The Elementor widget instance.
         * @return array The modified query arguments.
         */
        public function modify_elementor_query_args($query_args, $widget)
        {
        }
        private function get_elementor_post_query($loop_widget) : \WP_Query
        {
        }
        /**
         * @param array $settings
         * @return bool
         */
        public function should_exclude_child_taxonomies(array $settings) : bool
        {
        }
        public function should_hide_empty_items(array $settings) : bool
        {
        }
        private function maybe_add_filtered_post_ids_to_args(array $args, $loop_widget, array $settings) : array
        {
        }
        /**
         * @param array $settings
         * @param array $display_settings
         * @return void|\WP_Term[]
         */
        public function get_filtered_taxonomies($settings, $display_settings)
        {
        }
        private function get_additional_allowed_args($args, $display_settings)
        {
        }
        private function get_taxonomies_to_exclude($post_type)
        {
        }
        private function should_exclude_taxonomy($taxonomy_name, $taxonomies_to_exclude)
        {
        }
    }
}
namespace ElementorPro\Modules\LoopBuilder\Skins {
    abstract class Skin_Loop_Taxonomy_Base extends \ElementorPro\Modules\LoopBuilder\Skins\Skin_Loop_Base
    {
        use \ElementorPro\Modules\LoopFilter\Traits\Taxonomy_Filter_Trait;
        use \ElementorPro\Modules\DynamicTags\Tags\Base\Tag_Trait;
        use \ElementorPro\Modules\LoopBuilder\Files\Css\Loop_Css_Trait;
        protected $provider;
        protected function init_parent($widget)
        {
        }
        protected function init_provider()
        {
        }
        public function render()
        {
        }
        protected function prepare_template_loop($terms, $template_id)
        {
        }
        protected function render_before_loop($template_id)
        {
        }
        protected function render_loop_content($terms, $template_id)
        {
        }
        /**
         * Render Post
         *
         * Uses the chosen custom template to render Loop posts.
         *
         * @since 3.8.0
         */
        protected function render_post()
        {
        }
        protected function render_loop_end()
        {
        }
        /**
         * Register Query Controls
         *
         * Registers the controls for the query used by the Loop.
         *
         * @since 3.8.0
         */
        public function register_query_controls(\ElementorPro\Modules\LoopBuilder\Widgets\Base $widget)
        {
        }
        /**
         * Returns desired taxonomy items.
         *
         * Uses control values from get_settings_for_display.
         */
        protected function get_terms()
        {
        }
        public function filter_loop_taxonomy_args($args, $settings, $display_settings)
        {
        }
    }
}
namespace ElementorPro\Modules\WooCommerce\Skins {
    class Skin_Loop_Product_Taxonomy extends \ElementorPro\Modules\LoopBuilder\Skins\Skin_Loop_Taxonomy_Base
    {
        protected $post_type = 'product';
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function render()
        {
        }
        protected function get_default_source_option()
        {
        }
    }
}
namespace ElementorPro\Modules\DynamicTags\Tags\Base {
    abstract class Data_Tag extends \Elementor\Core\DynamicTags\Data_Tag
    {
        use \ElementorPro\Modules\DynamicTags\Tags\Base\Tag_Trait;
    }
}
namespace ElementorPro\Modules\Woocommerce\Tags\Traits {
    trait Tag_Product_Id
    {
        public function add_product_id_control()
        {
        }
    }
}
namespace ElementorPro\Modules\Woocommerce\Tags {
    abstract class Base_Data_Tag extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        use \ElementorPro\Modules\Woocommerce\Tags\Traits\Tag_Product_Id;
        use \ElementorPro\Modules\Woocommerce\Traits\Product_Id_Trait;
        public function get_group()
        {
        }
        public function get_editor_config()
        {
        }
    }
    class Product_Gallery extends \ElementorPro\Modules\Woocommerce\Tags\Base_Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_value(array $options = [])
        {
        }
    }
    abstract class Base_Tag extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        use \ElementorPro\Modules\Woocommerce\Tags\Traits\Tag_Product_Id;
        use \ElementorPro\Modules\Woocommerce\Traits\Product_Id_Trait;
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_editor_config()
        {
        }
    }
    class Product_Price extends \ElementorPro\Modules\Woocommerce\Tags\Base_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
    }
    class Product_Rating extends \ElementorPro\Modules\Woocommerce\Tags\Base_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
    }
    class Category_Image extends \ElementorPro\Modules\Woocommerce\Tags\Base_Data_Tag
    {
        use \ElementorPro\Modules\DynamicTags\Tags\Base\Tag_Trait;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_value(array $options = [])
        {
        }
    }
    class Product_Content extends \ElementorPro\Modules\Woocommerce\Tags\Base_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
    }
    class Woocommerce_Add_To_Cart extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        protected function register_controls()
        {
        }
        public function get_value(array $options = [])
        {
        }
    }
    class Product_Sale extends \ElementorPro\Modules\Woocommerce\Tags\Base_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
    }
    class Product_Image extends \ElementorPro\Modules\Woocommerce\Tags\Base_Data_Tag
    {
        use \ElementorPro\Modules\Woocommerce\Tags\Traits\Tag_Product_Id;
        use \ElementorPro\Modules\Woocommerce\Traits\Product_Id_Trait;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        protected function register_controls()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_value(array $options = [])
        {
        }
    }
    class Product_SKU extends \ElementorPro\Modules\Woocommerce\Tags\Base_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
    }
    class Product_Title extends \ElementorPro\Modules\Woocommerce\Tags\Base_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
    }
    class Product_Terms extends \ElementorPro\Modules\Woocommerce\Tags\Base_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        protected function register_advanced_section()
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
    }
    class Product_Stock extends \ElementorPro\Modules\Woocommerce\Tags\Base_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function render()
        {
        }
        protected function register_controls()
        {
        }
    }
    class Product_Short_Description extends \ElementorPro\Modules\Woocommerce\Tags\Base_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
    }
}
namespace ElementorPro\Modules\Woocommerce {
    class Module extends \ElementorPro\Base\Module_Base
    {
        const WOOCOMMERCE_GROUP = 'woocommerce';
        const TEMPLATE_MINI_CART = 'cart/mini-cart.php';
        const OPTION_NAME_USE_MINI_CART = 'use_mini_cart_template';
        const MENU_CART_FRAGMENTS_ACTION = 'elementor-menu-cart-fragments';
        const MENU_CART_LICENSE_FEATURE_NAME = 'woocommerce-menu-cart';
        const SINGLE_PRODUCT_TEMPLATE_LICENSE_FEATURE_NAME = 'product-single';
        const ARCHIVE_PRODUCT_TEMPLATE_LICENSE_FEATURE_NAME = 'product-archive';
        const SITE_SETTINGS_PAGES_LICENSE_FEATURE_NAME = 'settings-woocommerce-pages';
        const SITE_SETTINGS_NOTICES_LICENSE_FEATURE_NAME = 'settings-woocommerce-notices';
        const DYNAMIC_TAGS_LICENSE_FEATURE_NAME = 'dynamic-tags-wc';
        const LOOP_PRODUCT_SKIN_ID = 'product';
        const LOOP_PRODUCT_TAXONOMY_SKIN_ID = 'product_taxonomy';
        const WC_PERSISTENT_SITE_SETTINGS = ['woocommerce_cart_page_id', 'woocommerce_checkout_page_id', 'woocommerce_myaccount_page_id', 'woocommerce_terms_page_id', 'woocommerce_purchase_summary_page_id', 'woocommerce_shop_page_id'];
        const WIDGET_NAME_CLASS_NAME_MAP = ['woocommerce-products' => 'Products', 'wc-products' => 'Products_Deprecated', 'woocommerce-product-add-to-cart' => 'Product_Add_To_Cart', 'wc-elements' => 'Elements', 'wc-categories' => 'Categories', 'woocommerce-product-price' => 'Product_Price', 'woocommerce-product-title' => 'Product_Title', 'woocommerce-product-images' => 'Product_Images', 'woocommerce-product-upsell' => 'Product_Upsell', 'woocommerce-product-short-description' => 'Product_Short_Description', 'woocommerce-product-meta' => 'Product_Meta', 'woocommerce-product-stock' => 'Product_Stock', 'woocommerce-product-rating' => 'Product_Rating', 'woocommerce-product-data-tabs' => 'Product_Data_Tabs', 'woocommerce-product-related' => 'Product_Related', 'woocommerce-breadcrumb' => 'Breadcrumb', 'wc-add-to-cart' => 'Add_To_Cart', 'wc-archive-products' => 'Archive_Products', 'woocommerce-archive-products' => 'Archive_Products_Deprecated', 'woocommerce-product-additional-information' => 'Product_Additional_Information', 'woocommerce-menu-cart' => 'Menu_Cart', 'woocommerce-product-content' => 'Product_Content', 'woocommerce-archive-description' => 'Archive_Description', 'woocommerce-checkout-page' => 'Checkout', 'woocommerce-cart' => 'Cart', 'woocommerce-my-account' => 'My_Account', 'woocommerce-purchase-summary' => 'Purchase_Summary', 'woocommerce-notices' => 'Notices', 'wc-single-elements' => 'Single_Elements'];
        const WIDGET_HAS_CUSTOM_BREAKPOINTS = true;
        protected $docs_types = [];
        protected $use_mini_cart_template;
        protected $woocommerce_notices_elements = [];
        public static function is_active()
        {
        }
        public static function is_product_search()
        {
        }
        /**
         * @param $settings
         * @param string $icon
         * @return void
         */
        public static function render_menu_icon($settings, string $icon)
        {
        }
        public function get_name()
        {
        }
        public function get_widgets()
        {
        }
        const RECOMMENDED_POSTS_WIDGET_NAMES = ['theme-post-featured-image', 'woocommerce-product-title', 'woocommerce-product-add-to-cart', 'woocommerce-product-price', 'woocommerce-product-rating', 'woocommerce-product-stock', 'woocommerce-product-meta', 'woocommerce-product-short-description', 'woocommerce-product-content', 'woocommerce-product-data-tabs', 'woocommerce-product-additional-information'];
        // 'WC page name' => 'Elementor widget name'
        const WC_STATUS_PAGES_MAPPED_TO_WIDGETS = ['Cart' => 'woocommerce-cart', 'Checkout' => 'woocommerce-checkout-page', 'My account' => 'woocommerce-my-account'];
        public function add_product_post_class($classes)
        {
        }
        public function add_products_post_class_filter()
        {
        }
        public function remove_products_post_class_filter()
        {
        }
        public function register_tags()
        {
        }
        public function register_wc_hooks()
        {
        }
        /**
         * @param Conditions_Manager $conditions_manager
         */
        public function register_conditions($conditions_manager)
        {
        }
        /**
         * @param Documents_Manager $documents_manager
         */
        public function register_documents($documents_manager)
        {
        }
        public static function render_menu_cart_toggle_button($settings)
        {
        }
        /**
         * Render Menu Cart
         *
         * The `widget_shopping_cart_content` div will be populated by woocommerce js.
         *
         * When in the editor we populate this on page load as we can't rely on the woocoommerce js to re-add the fragments
         * each time a widget us re-rendered.
         */
        public static function render_menu_cart($settings)
        {
        }
        public static function render_menu_cart_close_button($settings)
        {
        }
        /**
         * Menu cart fragments.
         *
         * Ajax action to create fragments for the menu carts in a page.
         *
         * @return void
         */
        public function menu_cart_fragments()
        {
        }
        /**
         * Get All Fragments.
         *
         * @since 3.7.0
         *
         * @param $id
         * @param $all_fragments
         * @return void
         */
        public function get_all_fragments($id, &$all_fragments)
        {
        }
        /**
         * Get Fragments In Document.
         *
         * A general function that will return any needed fragments for a Post.
         *
         * @since 3.7.0
         * @access public
         *
         * @param int $id
         *
         * @return mixed $fragments
         */
        public function get_fragments_in_document($id)
        {
        }
        /**
         * Get Fragments Handler.
         *
         * @since 3.7.0
         *
         * @param array $fragments
         * @return void
         */
        public function get_fragments_handler(array &$fragments)
        {
        }
        /**
         * Empty Cart Fragments
         *
         * When the Cart is emptied, the selected 'Empty Cart Template' needs to be added as an item
         * in the WooCommerce `$fragments` array, so that WC will push the custom Template content into the DOM.
         * This is done to prevent the need for a page refresh after the cart is cleared.
         *
         * @since 3.7.0
         *
         * @param array $fragments
         * @return array
         */
        public function empty_cart_fragments($fragments)
        {
        }
        public function maybe_init_cart()
        {
        }
        public function localized_settings_frontend($settings)
        {
        }
        public function theme_template_include($need_override_location, $location)
        {
        }
        public function add_loop_recommended_widgets($config, $post_id)
        {
        }
        /**
         * Add plugin path to wc template search path.
         * Based on: https://www.skyverge.com/blog/override-woocommerce-template-file-within-a-plugin/
         * @param $template
         * @param $template_name
         * @param $template_path
         *
         * @return string
         */
        public function woocommerce_locate_template($template, $template_name, $template_path)
        {
        }
        /**
         * WooCommerce/WordPress widget(s), some of the widgets have css classes that used by final selectors.
         * before this filter, all those widgets were warped by `.elementor-widget-container` without chain original widget
         * classes, now they will be warped by div with the original css classes.
         *
         * @param array $default_widget_args
         * @param \Elementor\Widget_WordPress $widget
         *
         * @return array $default_widget_args
         */
        public function woocommerce_wordpress_widget_css_class($default_widget_args, $widget)
        {
        }
        public function register_admin_fields(\Elementor\Settings $settings)
        {
        }
        /**
         * Load Widget Before WooCommerce Ajax.
         *
         * When outputting the complex WooCommerce shortcodes (which we use in our widgets) e.g. Checkout, Cart, etc. WC
         * immediately does more ajax calls and retrieves updated html fragments based on the data in the forms that may
         * be autofilled by the current user's browser e.g. the Payment section holding the "Place order" button.
         *
         * This function runs before these ajax calls. Using the `elementorPageId` and `elementorWidgetId` querystring
         * appended to the forms `_wp_http_referer` url field, or the referer page ID, it loads the relevant Elementor widget.
         * The rendered Elementor widget replaces the default WooCommerce template used to refresh WooCommerce elements in the page.
         *
         * This is necessary for example in the Checkout Payment section where we modify the Terms & Conditions text
         * using settings from the widget or when updating shipping methods on the Cart.
         *
         * @since 3.5.0
         */
        public function load_widget_before_wc_ajax()
        {
        }
        /**
         * Elementor Woocommerce Checkout Login User
         *
         * Handle the Ajax call for the custom login form on the Checkout Widget
         *
         * @since 3.5.0
         */
        public function elementor_woocommerce_checkout_login_user()
        {
        }
        /**
         * Register Ajax Actions.
         *
         * Registers ajax action used by the Editor js.
         *
         * @since 3.5.0
         *
         * @param Ajax $ajax
         */
        public function register_ajax_actions(\Elementor\Core\Common\Modules\Ajax\Module $ajax)
        {
        }
        /**
         * @throws \Exception
         */
        public function woocommerce_mock_notices($data)
        {
        }
        /**
         * Update Page Option.
         *
         * Ajax action can be used to update any WooCommerce option.
         *
         * @since 3.5.0
         *
         * @param array $data
         */
        public function update_page_option($data)
        {
        }
        public function init_site_settings(\Elementor\Core\Kits\Documents\Kit $kit)
        {
        }
        public function add_products_type_to_template_popup($form)
        {
        }
        public function add_products_type_to_loop_settings_query($form)
        {
        }
        public function add_products_taxonomy_type_to_template_popup($form)
        {
        }
        public function add_products_taxonomy_type_to_loop_settings_query($form)
        {
        }
        public function e_cart_count_fragments($fragments)
        {
        }
        /**
         * @param $form
         * @param $control_name
         * @return void
         */
        protected function add_products_to_options($form, $control_name)
        {
        }
        protected function add_taxonomies_to_options($form, $control_name)
        {
        }
        /**
         * Add Update Kit Settings Hooks
         *
         * Add hooks that update the corresponding kit setting when the WooCommerce option is updated.
         */
        public function add_update_kit_settings_hooks()
        {
        }
        /**
         * Elementor WC My Account Logout
         *
         * Programatically log out if $_REQUEST['elementor_wc_logout'] is set.
         * The $_REQUEST variables we have generated a custom logout URL for in the My Account menu.
         *
         * @since 3.5.0
         */
        public function elementor_wc_my_account_logout()
        {
        }
        /**
         * Add Localize Data
         *
         * Makes `woocommercePages` available with the page name and the associated post ID for use with the various
         * widgets site settings modal.
         *
         * @param $settings
         * @return array
         */
        public function add_localize_data($settings)
        {
        }
        /**
         * Localize Added To Cart On Product Single
         *
         * WooCommerce doesn't trigger `added_to_cart` event on its products single page which is required for us to
         * automatically open our Menu Cart if the settings is chosen. We make the `productAddedToCart` setting
         * available that we can use in the Menu Cart js to check if a product has just been added.
         *
         * @since 3.5.0
         */
        public function localize_added_to_cart_on_product_single()
        {
        }
        public function e_notices_body_classes($classes)
        {
        }
        public function get_styled_notice_elements()
        {
        }
        public function e_notices_css()
        {
        }
        public function get_order_received_endpoint_url($url, $endpoint, $value)
        {
        }
        public function maybe_define_woocommerce_checkout()
        {
        }
        /**
         * Products Query Sources Fragments.
         *
         * Since we introduced additional query sources to the Products Widget,
         * some of these query sources can now be used outside of the Single Product template.
         *
         * For example the Related Products and Cross-Sells.
         *
         * But now we'll need to make those sections also update when the Cart is updated. So
         * we'll do this by creating fragments for each of these.
         *
         * @since 3.7.0
         *
         * @param array $fragments
         *
         * @return array
         */
        public function products_query_sources_fragments($fragments)
        {
        }
        /**
         * Get Products Related Content.
         *
         * A function to return content for the 'related' products query type in the Products widget.
         * This function is declared in the Module file so it can be accessed during a WC fragment refresh
         * and also be used in the Product widget's render method.
         *
         * @since 3.7.0
         * @access public
         *
         * @param array $settings
         *
         * @return mixed The content or false
         */
        public static function get_products_related_content($settings)
        {
        }
        /**
         * Get Upsells Content.
         *
         * A function to return content for the 'upsell' query type in the Products widget.
         * This function is declared in the Module file so it can be accessed during a WC fragment refresh
         * and also be used in the Product widget's render method.
         *
         * @since 3.7.0
         * @access public
         *
         * @param array $settings
         *
         * @return mixed The content or false
         */
        public static function get_upsells_content($settings)
        {
        }
        /**
         * Get Cross Sells Content.
         *
         * A function to return content for the 'cross_sells' query type in the Products widget.
         * This function is declared in the Module file so it can be accessed during a WC fragment refresh
         * and also be used in the Product widget's render method.
         *
         * @since 3.7.0
         * @access public
         *
         * @param array $settings
         *
         * @return mixed The content or false
         */
        public static function get_cross_sells_content($settings)
        {
        }
        /**
         * Is Preview
         *
         * Helper to check if we are doing either:
         * - Viewing the WP Preview page - also used as the Elementor preview page when clicking "Preview Changes" in the editor
         * - Viewing the page in the editor, but not the active page being edited e.g. if you click Edit Header while editing a page
         *
         * @since 3.7.0
         *
         * @return bool
         */
        public static function is_preview()
        {
        }
        public function __construct()
        {
        }
        public function add_system_status_data($response, $system_status, $request)
        {
        }
        public function loop_query($query_args, $widget)
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/woocommerce/assets/scss/widgets/*.scss`
         * to `/assets/css/widget-*.min.css`.
         *
         * @return void
         */
        public function register_styles() : void
        {
        }
    }
}
namespace ElementorPro\Modules\Woocommerce\Conditions {
    class Product_Archive extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public function __construct(array $data = [])
        {
        }
        public static function get_type()
        {
        }
        public function get_name()
        {
        }
        public static function get_priority()
        {
        }
        public function get_label()
        {
        }
        public function get_all_label()
        {
        }
        public function register_sub_conditions()
        {
        }
        public function check($args)
        {
        }
    }
    class Woocommerce extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public static function get_type()
        {
        }
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function get_all_label()
        {
        }
        public function register_sub_conditions()
        {
        }
        public function check($args)
        {
        }
    }
    class Shop_Page extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public static function get_type()
        {
        }
        public function get_name()
        {
        }
        public static function get_priority()
        {
        }
        public function get_label()
        {
        }
        public function check($args)
        {
        }
    }
    class Product_Search extends \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base
    {
        public static function get_type()
        {
        }
        public function get_name()
        {
        }
        public static function get_priority()
        {
        }
        public function get_label()
        {
        }
        public function check($args)
        {
        }
    }
}
namespace ElementorPro\Modules\Woocommerce\Documents {
    class Product_Archive extends \ElementorPro\Modules\ThemeBuilder\Documents\Archive
    {
        public static function get_properties()
        {
        }
        public static function get_type()
        {
        }
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        protected static function get_site_editor_icon()
        {
        }
        /**
         * Fix for thumbnail name that is different from editor type.
         *
         * @return string
         */
        protected static function get_site_editor_thumbnail_url()
        {
        }
        protected static function get_site_editor_tooltip_data()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function get_container_attributes()
        {
        }
        public function filter_body_classes($body_classes)
        {
        }
        public static function get_preview_as_default()
        {
        }
        public static function get_preview_as_options()
        {
        }
        public function __construct(array $data = [])
        {
        }
        protected static function get_editor_panel_categories()
        {
        }
        public static function get_editor_panel_config()
        {
        }
        protected function register_controls()
        {
        }
        protected function get_remote_library_config()
        {
        }
    }
    class Product extends \ElementorPro\Modules\ThemeBuilder\Documents\Single_Base
    {
        public static function get_properties()
        {
        }
        public static function get_type()
        {
        }
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        protected static function get_site_editor_icon()
        {
        }
        protected static function get_site_editor_tooltip_data()
        {
        }
        public static function get_editor_panel_config()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function get_depended_widget()
        {
        }
        public function get_container_attributes()
        {
        }
        public function filter_body_classes($body_classes)
        {
        }
        public function before_get_content()
        {
        }
        public function after_get_content()
        {
        }
        public function print_content()
        {
        }
        public function __construct(array $data = [])
        {
        }
        protected static function get_editor_panel_categories()
        {
        }
        protected function register_controls()
        {
        }
        protected function get_remote_library_config()
        {
        }
    }
    class Product_Post extends \Elementor\Core\DocumentTypes\Post
    {
        public static function get_properties()
        {
        }
        /**
         * @since  2.0.0
         * @access public
         */
        public function get_name()
        {
        }
        /**
         * @since  2.0.0
         * @access public
         * @static
         */
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        public static function get_lock_behavior_v2()
        {
        }
        protected static function get_editor_panel_categories()
        {
        }
        protected function get_remote_library_config()
        {
        }
    }
}
namespace ElementorPro\Modules\Woocommerce\Widgets {
    abstract class Base_Widget extends \ElementorPro\Base\Base_Widget
    {
        use \ElementorPro\Modules\Woocommerce\Traits\Product_Id_Trait;
        protected $gettext_modifications;
        public function get_categories()
        {
        }
        protected function get_devices_default_args()
        {
        }
        protected function add_columns_responsive_control()
        {
        }
        /**
         * Is WooCommerce Feature Active.
         *
         * Checks whether a specific WooCommerce feature is active. These checks can sometimes look at multiple WooCommerce
         * settings at once so this simplifies and centralizes the checking.
         *
         * @since 3.5.0
         *
         * @param string $feature
         * @return bool
         */
        protected function is_wc_feature_active($feature)
        {
        }
        /**
         * Get Custom Border Type Options
         *
         * Return a set of border options to be used in different WooCommerce widgets.
         *
         * This will be used in cases where the Group Border Control could not be used.
         *
         * @since 3.5.0
         *
         * @return array
         */
        public static function get_custom_border_type_options()
        {
        }
        /**
         * Init Gettext Modifications
         *
         * Should be overridden by a method in the Widget class.
         *
         * @since 3.5.0
         */
        protected function init_gettext_modifications()
        {
        }
        /**
         * Filter Gettext.
         *
         * Filter runs when text is output to the page using the translation functions (`_e()`, `__()`, etc.)
         * used to apply text changes from the widget settings.
         *
         * This allows us to make text changes without having to ovveride WooCommerce templates, which would
         * lead to dev tax to keep all the templates up to date with each future WC release.
         *
         * @since 3.5.0
         *
         * @param string $translation
         * @param string $text
         * @param string $domain
         * @return string
         */
        public function filter_gettext($translation, $text, $domain)
        {
        }
    }
    class Product_Additional_Information extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Product_Add_To_Cart extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        use \ElementorPro\Modules\Woocommerce\Traits\Send_App_Plg_Trait;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function render()
        {
        }
        public function should_add_container()
        {
        }
        /**
         * Before Add to Cart Quantity
         *
         * Added wrapper tag around the quantity input and "Add to Cart" button
         * used to more solidly accommodate the layout when additional elements
         * are added by 3rd party plugins.
         *
         * @since 3.6.0
         */
        public function before_add_to_cart_quantity()
        {
        }
        /**
         * After Add to Cart Button
         *
         * @since 3.6.0
         */
        public function after_add_to_cart_button()
        {
        }
        protected function register_controls()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Product_Price extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Archive_Description extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function get_categories()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Product_Rating extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Product_Meta extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Cart extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        use \ElementorPro\Modules\Woocommerce\Traits\Send_App_Plg_Trait;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function get_categories()
        {
        }
        public function get_script_depends()
        {
        }
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        /**
         * Init Gettext Modifications
         *
         * Sets the `$gettext_modifications` property used with the `filter_gettext()` in the extended Base_Widget.
         *
         * @since 3.5.0
         */
        protected function init_gettext_modifications()
        {
        }
        /**
         * Check if an Elementor template has been selected to display the empty cart notification
         *
         * @since 3.7.0
         * @return boolean
         */
        protected function has_empty_cart_template()
        {
        }
        public function hide_coupon_field_on_cart($enabled)
        {
        }
        /**
         * Woocommerce Before Cart
         *
         * Output containing elements. Callback function for the woocommerce_before_cart hook
         *
         * This eliminates the need for template overrides.
         *
         * @since 3.5.0
         */
        public function woocommerce_before_cart()
        {
        }
        /**
         * Woocommerce Before Cart Table
         *
         * Output containing elements. Callback function for the woocommerce_before_cart_table hook
         *
         * This eliminates the need for template overrides.
         *
         * @since 3.5.0
         */
        public function woocommerce_before_cart_table()
        {
        }
        /**
         * Woocommerce After Cart Table
         *
         * Output containing elements. Callback function for the woocommerce_after_cart_table hook
         *
         * This eliminates the need for template overrides.
         *
         * @since 3.5.0
         */
        public function woocommerce_after_cart_table()
        {
        }
        /**
         * Woocommerce Before Cart Collaterals
         *
         * Output containing elements. * Callback function for the woocommerce_before_cart_collaterals hook
         *
         * This eliminates the need for template overrides.
         *
         * @since 3.5.0
         */
        public function woocommerce_before_cart_collaterals()
        {
        }
        /**
         * Woocommerce After Cart
         *
         * Output containing elements. Callback function for the woocommerce_after_cart hook.
         *
         * This eliminates the need for template overrides.
         *
         * @since 3.5.0
         */
        public function woocommerce_after_cart()
        {
        }
        /**
         * WooCommerce Get Remove URL.
         *
         * When in the Editor or (wp preview) and the uer clicks to remove an item from the cart, WooCommerce uses
         * the`_wp_http_referer` url during the ajax call to generate the new cart html. So when we're in the Editor
         * or (wp preview) we modify the `_wp_http_referer` to use the `get_wp_preview_url()` which will have
         * the new cart content.
         *
         * @since 3.5.0
         * @deprecated 3.7.0
         *
         * @param $url
         * @return string
         */
        public function woocommerce_get_remove_url($url)
        {
        }
        /**
         * WooCommerce Get Cart Url
         *
         * Used with the `woocommerce_get_cart_url`. This sets the url to the current page, so links like the `remove_url`
         * are set to the current page, and not the existing WooCommerce cart endpoint.
         *
         * @since 3.7.0
         *
         * @param $url
         * @return string
         */
        public function woocommerce_get_cart_url($url)
        {
        }
        /**
         * The following disabling of cart coupon needs to be done this way so that
         * we only disable the display of coupon interface in our cart widget and
         * `wc_coupons_enabled()` can still be reliably used elsewhere.
         */
        public function disable_cart_coupon()
        {
        }
        public function enable_cart_coupon()
        {
        }
        public function cart_coupon_return_false()
        {
        }
        /**
         * Add Render Hooks
         *
         * Add actions & filters before displaying our widget.
         *
         * @since 3.7.0
         */
        public function add_render_hooks()
        {
        }
        /**
         * Remove Render Hooks
         *
         * Remove actions & filters after displaying our widget.
         *
         * @since 3.7.0
         */
        public function remove_render_hooks()
        {
        }
        public function render()
        {
        }
        public function get_group_name()
        {
        }
    }
}
namespace ElementorPro\Modules\ThemeBuilder\Widgets {
    class Category_Image extends \Elementor\Widget_Image
    {
        use \ElementorPro\Base\Base_Widget_Trait;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function register_controls()
        {
        }
        protected function get_html_wrapper_class()
        {
        }
        public function get_group_name()
        {
        }
    }
}
namespace ElementorPro\Modules\Woocommerce\Widgets {
    class Product_Images extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
        public function get_group_name()
        {
        }
    }
    abstract class Products_Base extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        protected function register_controls()
        {
        }
        /**
         * Add To Cart Wrapper
         *
         * Add a div wrapper around the Add to Cart & View Cart buttons on the product cards inside the product grid.
         * The wrapper is used to vertically align the Add to Cart Button and the View Cart link to the bottom of the card.
         * This wrapper is added when the 'Automatically align buttons' toggle is selected.
         * Using the 'woocommerce_loop_add_to_cart_link' hook.
         *
         * @since 3.7.0
         *
         * @param string $string
         * @return string $string
         */
        public function add_to_cart_wrapper($string)
        {
        }
    }
    /**
     * Class Products_Deprecated
     *
     * @deprecated 2.4.1 Use `Products` class instead.
     */
    class Products_Deprecated extends \ElementorPro\Modules\Woocommerce\Widgets\Products_Base
    {
        protected $_has_template_content = false;
        public function get_name()
        {
        }
        public function get_categories()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        /* Deprecated Widget */
        public function show_in_panel()
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function on_export($element)
        {
        }
        public function get_query()
        {
        }
        protected function register_skins()
        {
        }
        protected function register_controls()
        {
        }
        public function query_posts()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Product_Content extends \ElementorPro\Modules\ThemeBuilder\Widgets\Post_Content
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        public function get_group_name()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
    }
    class Single_Elements extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        public function __construct($data = [], $args = null)
        {
        }
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        /* Deprecated Widget */
        public function show_in_panel()
        {
        }
        protected function register_controls()
        {
        }
        public function remove_description_tab($tabs)
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Notices extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function get_categories()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        public function get_help_url()
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        protected function render_demo_notice()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Product_Data_Tabs extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Checkout extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        use \ElementorPro\Modules\Woocommerce\Traits\Send_App_Plg_Trait;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function get_categories()
        {
        }
        public function get_help_url()
        {
        }
        public function get_script_depends()
        {
        }
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        /**
         * Init Gettext Modifications
         *
         * Sets the `$gettext_modifications` property used with the `filter_gettext()` in the extended Base_Widget.
         *
         * @since 3.5.0
         */
        protected function init_gettext_modifications()
        {
        }
        /**
         * WooCommerce Terms and Conditions Checkbox Text.
         *
         * WooCommerce filter is used to apply widget settings to Checkout Terms & Conditions text and link text.
         *
         * @since 3.5.0
         *
         * @param string $text
         * @return string
         */
        public function woocommerce_terms_and_conditions_checkbox_text($text)
        {
        }
        /**
         * Modify Form Field.
         *
         * WooCommerce filter is used to apply widget settings to the Checkout forms address fields
         * from the Billing and Shipping Details widget sections, e.g. label, placeholder, default.
         *
         * @since 3.5.0
         *
         * @param array $args
         * @param string $key
         * @param string $value
         * @return array
         */
        public function modify_form_field($args, $key, $value)
        {
        }
        /**
         * WooCommerce Checkout Before Customer Details
         *
         * Callback function for the woocommerce_checkout_before_customer_details hook that outputs elements
         *
         * This eliminates the need for template overrides.
         *
         * @since 3.5.0
         */
        public function woocommerce_checkout_before_customer_details()
        {
        }
        /**
         * Woocommerce Checkout After Customer Details
         *
         * Output containing elements. Callback function for the woocommerce_checkout_after_customer_details hook.
         *
         * This eliminates the need for template overrides.
         *
         * @since 3.5.0
         */
        public function woocommerce_checkout_after_customer_details()
        {
        }
        /**
         * Woocommerce Checkout Before Order Review Heading 1
         *
         * Output containing elements. Callback function for the woocommerce_checkout_before_order_review_heading hook.
         *
         * This eliminates the need for template overrides.
         *
         * @since 3.5.0
         */
        public function woocommerce_checkout_before_order_review_heading_1()
        {
        }
        /**
         * Woocommerce Checkout Before Order Review Heading 2
         *
         * Output containing elements. Callback function for the woocommerce_checkout_before_order_review_heading hook.
         *
         * This eliminates the need for template overrides.
         *
         * @since 3.5.0
         */
        public function woocommerce_checkout_before_order_review_heading_2()
        {
        }
        /**
         * Woocommerce Checkout Order Review
         *
         * Output containing elements. Callback function for the woocommerce_checkout_order_review hook.
         *
         * This eliminates the need for template overrides.
         *
         * @since 3.5.0
         */
        public function woocommerce_checkout_order_review()
        {
        }
        /**
         * Woocommerce Checkout After Order Review
         *
         * Output containing elements. Callback function for the woocommerce_checkout_after_order_review hook.
         *
         * This eliminates the need for template overrides.
         *
         * @since 3.5.0
         */
        public function woocommerce_checkout_after_order_review()
        {
        }
        /**
         * Add Render Hooks
         *
         * Add actions & filters before displaying our widget.
         *
         * @since 3.5.0
         */
        public function add_render_hooks()
        {
        }
        /**
         * Remove Render Hooks
         *
         * Remove actions & filters after displaying our widget.
         *
         * @since 3.5.0
         */
        public function remove_render_hooks()
        {
        }
        protected function render()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Purchase_Summary extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function get_categories()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        /**
         * Init Gettext Modifications
         *
         * Sets the `$gettext_modifications` property used with the `filter_gettext()` in the extended Base_Widget.
         *
         * @since 3.5.0
         */
        protected function init_gettext_modifications()
        {
        }
        /**
         * Modify Order Received Text.
         *
         * @since 3.5.0
         *
         * @param $text
         * @return string
         */
        public function modify_order_received_text($text)
        {
        }
        public function get_modified_order_id()
        {
        }
        public function get_modified_order_key()
        {
        }
        protected function render()
        {
        }
        public function no_order_notice()
        {
        }
        public function set_preview_order()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Product_Related extends \ElementorPro\Modules\Woocommerce\Widgets\Products_Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Menu_Cart extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Products extends \ElementorPro\Modules\Woocommerce\Widgets\Products_Base
    {
        use \ElementorPro\Modules\Woocommerce\Traits\Products_Trait;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function get_categories()
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        /**
         * @throws \Exception
         */
        protected function register_query_section()
        {
        }
        protected function register_controls()
        {
        }
        public static function get_shortcode_object($settings)
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Archive_Products extends \ElementorPro\Modules\Woocommerce\Widgets\Products
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_keywords()
        {
        }
        public function get_categories()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        public function get_group_name()
        {
        }
    }
    /**
     * Class Archive_Products_Deprecated
     *
     * @deprecated 2.4.1 Use `Archive_Products` instead.
     */
    class Archive_Products_Deprecated extends \ElementorPro\Modules\Woocommerce\Widgets\Products
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_categories()
        {
        }
        /* Deprecated Widget */
        public function show_in_panel()
        {
        }
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        public function render_no_results()
        {
        }
        protected function render()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Product_Title extends \Elementor\Widget_Heading
    {
        use \ElementorPro\Base\Base_Widget_Trait;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function register_controls()
        {
        }
        protected function get_html_wrapper_class()
        {
        }
        protected function render()
        {
        }
        /**
         * Render Woocommerce Product Title output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Product_Upsell extends \ElementorPro\Modules\Woocommerce\Widgets\Products_Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class My_Account extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        public function modify_menu_items($items, $endpoints)
        {
        }
        /**
         * WooCommerce Get My Account Page Permalink
         *
         * Modify the permalinks of the My Account menu items. By default the permalinks will go to the
         * set WooCommerce My Account Page, even if the widget is on a different page. This function will override
         * the permalinks to use the widget page URL as the base URL instead.
         *
         * This is a callback function for the woocommerce_get_myaccount_page_permalink filter.
         *
         * @since 3.5.0
         *
         * @return string
         */
        public function woocommerce_get_myaccount_page_permalink($bool)
        {
        }
        /**
         * WooCommerce Logout Default Redirect URL
         *
         * Modify the permalink of the My Account Logout menu item. We add this so that we can add custom
         * parameters to the URL, which we can later access to log the user out and redirect back to the widget
         * page. Without this WooCommerce would have always just redirect back to the set My Account Page
         * after log out.
         *
         * This is a callback function for the woocommerce_logout_default_redirect_url filter.
         *
         * @since 3.5.0
         *
         * @return string
         */
        public function woocommerce_logout_default_redirect_url($redirect)
        {
        }
        protected function render()
        {
        }
        /**
         * Woocommerce Account Navigation
         *
         * Output a horizontal menu if the setting was selected. The default vertical menu will be hidden with CSS
         * and this menu will show. We wrap this menu with a class '.e-wc-account-tabs-nav' so that we
         * can manipulate the display for this menu with CSS (make it horizontal).
         *
         * Callback function for the woocommerce_account_navigation hook.
         *
         * This eliminates the need for template overrides.
         *
         * @since 3.5.0
         */
        public function woocommerce_account_navigation()
        {
        }
        /**
         * Check if the My Account dashboard intro content is replaced with a custom Elementor template
         *
         * Conditions:
         * 1. Customize Dashboard = Show
         * 2. A Template ID has been set
         *
         * @since 3.7.0
         *
         * @return boolean
         */
        public function has_custom_template()
        {
        }
        /**
         * Before Account Content
         *
         * Output containing elements. Callback function for the woocommerce_account_content hook.
         *
         * This eliminates the need for template overrides.
         *
         * @since 3.5.0
         */
        public function before_account_content()
        {
        }
        /**
         * Get Dashboard Template ID
         *
         * Get the template_id for the dashboard intro section if a custom template should be displayed
         *
         * @since 3.7.0
         *
         * @return int
         */
        public function get_dashboard_template_id()
        {
        }
        /**
         * Display a custom template inside the My Account dashboard section
         *
         * @since 3.7.0
         */
        public function display_custom_template()
        {
        }
        /**
         * After Account Content
         *
         * Output containing elements. Callback function for the woocommerce_account_content hook.
         *
         * This eliminates the need for template overrides.
         *
         * @since 3.5.0
         */
        public function after_account_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Breadcrumb extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function get_categories()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Add_To_Cart extends \Elementor\Widget_Button
    {
        use \ElementorPro\Base\Base_Widget_Trait;
        use \ElementorPro\Modules\Woocommerce\Traits\Product_Id_Trait;
        use \ElementorPro\Modules\Woocommerce\Traits\Send_App_Plg_Trait;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function on_export($element)
        {
        }
        public function unescape_html($safe_text, $text)
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        /**
         * Before Add to Cart Quantity
         *
         * Added wrapper tag around the quantity input and "Add to Cart" button
         * used to more solidly accommodate the layout when additional elements
         * are added by 3rd party plugins.
         *
         * @since 3.6.0
         */
        public function before_add_to_cart_quantity()
        {
        }
        /**
         * After Add to Cart Quantity
         *
         * @since 3.6.0
         */
        public function after_add_to_cart_button()
        {
        }
        /**
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        // Force remote render
        protected function content_template()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Product_Stock extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Categories extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        protected $_has_template_content = false;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        public function get_categories()
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Product_Short_Description extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Elements extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function on_export($element)
        {
        }
        public function get_keywords()
        {
        }
        public function get_categories()
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
}
namespace ElementorPro\Modules\CodeHighlight {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        public function get_widgets()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/code-highlight/assets/scss/frontend.scss`
         * to `/assets/css/widget-code-highlight.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
        public function register_frontend_scripts()
        {
        }
    }
}
namespace ElementorPro\Modules\CodeHighlight\Widgets {
    class Code_Highlight extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function get_style_depends()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        public function get_script_depends()
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        protected function content_template()
        {
        }
    }
}
namespace ElementorPro\Modules\FlipBox {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        public function get_widgets()
        {
        }
        public function get_name()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/flip-box/assets/scss/frontend.scss`
         * to `/assets/css/widget-flip-box.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\FlipBox\Widgets {
    class Flip_Box extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        /**
         * Render Flip Box widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
}
namespace ElementorPro\Modules\Screenshots {
    class Render_Mode_Screenshot extends \Elementor\Core\Frontend\RenderModes\Render_Mode_Base
    {
        const ENQUEUE_SCRIPTS_PRIORITY = 1000;
        public static function get_name()
        {
        }
        public function prepare_render()
        {
        }
        public function filter_template()
        {
        }
        public function is_static()
        {
        }
        public function enqueue_scripts()
        {
        }
    }
    class Screenshot
    {
        const POST_META_KEY = '_elementor_screenshot';
        const IS_SCREENSHOT_POST_META_KEY = '_elementor_is_screenshot';
        const FAILED_POST_META_KEY = '_elementor_screenshot_failed';
        const SCREENSHOT_DIR = 'elementor/screenshots';
        /**
         * @var int
         */
        protected $post_id;
        /**
         * @var string
         */
        protected $base64_image;
        /**
         * @var string
         */
        protected $file_name;
        /**
         * @var array
         */
        protected $upload_bits;
        /**
         * Screenshot constructor.
         *
         * @param int $post_id
         * @param string $base64_image
         */
        public function __construct($post_id, $base64_image = null)
        {
        }
        /**
         * Creates the directory if needed + add index.html file for security reasons.
         *
         * @return $this
         */
        public function create_dir()
        {
        }
        /**
         * Uploads the base64 image it self.
         *
         * TODO: Use Upload Manager when ready.
         *
         * @return $this
         * @throws \Exception
         */
        public function upload()
        {
        }
        /**
         * Removes the old attachment if there is an old screenshot image.
         *
         * @return $this
         */
        public function remove_old_attachment()
        {
        }
        /**
         * Removes the old post meta of the current post.
         *
         * @return $this
         */
        public function remove_old_post_meta()
        {
        }
        /**
         * Creates an attachment to the new screenshot and attach it to the original post
         * via post_meta.
         *
         * @return $this
         * @throws \Exception
         */
        public function create_new_attachment()
        {
        }
        /**
         * Mark the post that the screenshot capture was failed.
         *
         * @return $this
         */
        public function mark_as_failed()
        {
        }
        /**
         * Remove the failed_screenshot post meta.
         *
         * @return $this
         */
        public function unmark_as_failed()
        {
        }
        /**
         * Get the file name,
         * if not exists will generate it.
         *
         * @return string
         */
        public function get_file_name()
        {
        }
        /**
         * Extend and change the upload_dirs original method
         * to update the current screenshot to custom directory.
         *
         * @param $upload_dirs
         *
         * @return array
         */
        public function extend_upload_dirs_array($upload_dirs)
        {
        }
        /**
         * Get wp_upload_bits result.
         *
         * This method will be throw an exception if was called before actually upload a screenshot.
         *
         * @return array
         * @throws \Exception
         */
        protected function get_upload_bits()
        {
        }
        /**
         * Get the url of the screenshot.
         *
         * @return string
         * @throws \Exception
         */
        public function get_screenshot_url()
        {
        }
    }
    class Module extends \ElementorPro\Base\Module_Base
    {
        const SCREENSHOT_PROXY_NONCE_ACTION = 'screenshot_proxy';
        /**
         * Module name.
         *
         * @return string
         */
        public function get_name()
        {
        }
        /**
         * Creates proxy for css and images,
         * dom to image libraries cannot load content from another origin.
         *
         * @param $url
         *
         * @return string
         */
        public function get_proxy_data($url)
        {
        }
        /**
         * Save screenshot and attached it to the post.
         *
         * @param $data
         *
         * @return bool|string
         * @throws \Exception
         */
        public function ajax_save($data)
        {
        }
        /**
         * Remove the screenshot image and the attachment data.
         *
         * @param $data
         *
         * @return bool
         */
        public function ajax_delete($data)
        {
        }
        /**
         * Mark screenshot as failed.
         *
         * @param $data
         *
         * @return bool
         */
        public function ajax_screenshot_failed($data)
        {
        }
        /**
         * Extends the json of the templates.
         * sets a screenshot as a thumbnail if exists, and if not will add a url to generate screenshot for
         * the specific template.
         *
         * @param array $template
         *
         * @return array
         */
        public function extend_templates_json_structure($template)
        {
        }
        /**
         * @param \WP_Query $query
         */
        public function filter_screenshots_from_attachments_query(\WP_Query $query)
        {
        }
        public function filter_screenshots_from_ajax_attachments_query($query)
        {
        }
        /**
         * Register screenshots action.
         *
         * @param \Elementor\Core\Common\Modules\Ajax\Module $ajax_manager
         */
        public function register_ajax_actions($ajax_manager)
        {
        }
        /**
         * @param Render_Mode_Manager $manager
         *
         * @throws \Exception
         */
        public function register_render_mode(\Elementor\Core\Frontend\Render_Mode_Manager $manager)
        {
        }
        /**
         * Check and validate proxy mode.
         *
         * @param array $query_params
         *
         * @return bool
         * @throws \Requests_Exception_HTTP_400
         * @throws \Requests_Exception_HTTP_403
         * @throws Status400
         * @throws Status403
         */
        protected function is_screenshot_proxy_mode(array $query_params)
        {
        }
        /**
         * Module constructor.
         */
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\Posts {
    class Module extends \ElementorPro\Base\Module_Base
    {
        use \ElementorPro\Modules\Posts\Traits\Pagination_Trait;
        public function get_name()
        {
        }
        public function get_widgets()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/posts/assets/scss/frontend.scss`
         * to `/assets/css/widget-posts.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
        /**
         * Fix WP 5.5 pagination issue.
         *
         * Return true to mark that it's handled and avoid WP to set it as 404.
         *
         * @see https://github.com/elementor/elementor/issues/12126
         * @see https://core.trac.wordpress.org/ticket/50976
         *
         * Based on the logic at \WP::handle_404.
         *
         * @param $handled - Default false.
         * @param $wp_query
         *
         * @return bool
         */
        public function allow_posts_widget_pagination($handled, $wp_query)
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Data\Base {
    abstract class Controller extends \Elementor\Data\Base\Controller
    {
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\Posts\Data {
    class Controller extends \ElementorPro\Data\Base\Controller
    {
        public function get_name()
        {
        }
        public function register_endpoints()
        {
        }
        public function get_items($request)
        {
        }
        public function get_permission_callback($request)
        {
        }
    }
}
namespace ElementorPro\Modules\Posts\Widgets {
    /**
     * Class Portfolio
     */
    class Portfolio extends \ElementorPro\Base\Base_Widget
    {
        protected $_has_template_content = false;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function get_script_depends()
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function on_import($element)
        {
        }
        public function get_query()
        {
        }
        protected function register_controls()
        {
        }
        protected function get_taxonomies()
        {
        }
        protected function get_posts_tags()
        {
        }
        public function query_posts()
        {
        }
        public function render()
        {
        }
        protected function render_thumbnail()
        {
        }
        protected function render_filter_menu()
        {
        }
        protected function render_title()
        {
        }
        protected function render_categories_names()
        {
        }
        protected function render_post_header()
        {
        }
        protected function render_post_footer()
        {
        }
        protected function render_overlay_header()
        {
        }
        protected function render_overlay_footer()
        {
        }
        protected function render_loop_header()
        {
        }
        protected function render_loop_footer()
        {
        }
        protected function render_post()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    /**
     * Class Posts
     */
    class Posts extends \ElementorPro\Modules\Posts\Widgets\Posts_Base
    {
        use \ElementorPro\Modules\Posts\Traits\Query_Note_Trait;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_keywords()
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function on_import($element)
        {
        }
        protected function register_skins()
        {
        }
        protected function register_controls()
        {
        }
        /**
         * Get Query Name
         *
         * Returns the query control name used in the widget's main query.
         *
         * @since 3.8.0
         *
         * @return string
         */
        public function get_query_name()
        {
        }
        public function query_posts()
        {
        }
        /**
         * Get Posts Per Page Value
         *
         * Returns the value of the Posts Per Page control of the widget. This method was created because in some cases,
         * the control is registered in the widget, and in some cases, it is registered in a widget skin.
         *
         * @since 3.8.0
         * @access protected
         *
         * @return mixed
         */
        protected function get_posts_per_page_value()
        {
        }
        protected function register_query_section_controls()
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Registrars {
    /**
     * Basic form fields registration manager.
     */
    class Form_Fields_Registrar extends \ElementorPro\Core\Utils\Registrar
    {
        /**
         * Form_Fields_Registrar constructor.
         *
         * @return void
         */
        public function __construct()
        {
        }
        /**
         * Initialize the default fields.
         *
         * @return void
         */
        public function init()
        {
        }
    }
    /**
     * Basic form actions registration manager.
     */
    class Form_Actions_Registrar extends \ElementorPro\Core\Utils\Registrar
    {
        const FEATURE_NAME_CLASS_NAME_MAP = ['email' => 'Email', 'email2' => 'Email2', 'redirect' => 'Redirect', 'webhook' => 'Webhook', 'mailchimp' => 'Mailchimp', 'drip' => 'Drip', 'activecampaign' => 'Activecampaign', 'getresponse' => 'Getresponse', 'convertkit' => 'Convertkit', 'mailerlite' => 'Mailerlite', 'slack' => 'Slack', 'discord' => 'Discord'];
        /**
         * Form_Actions_Registrar constructor.
         *
         * @return void
         */
        public function __construct()
        {
        }
        /**
         * Initialize the default fields.
         *
         * @return void
         */
        public function init()
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Classes {
    class Rest_Client
    {
        public $request_cache = [];
        public function __construct($rest_base_url)
        {
        }
        /**
         * Set REST API base url.
         *
         * @param string $url
         */
        public function set_base_url($url)
        {
        }
        /**
         * Get REST API base url.
         *
         * @return string
         */
        public function get_base_url()
        {
        }
        /**
         * Add headers to REST API.
         *
         * @param $key   Header key.
         * @param $value Optional. Header value. Default is null.
         *
         * @return $this An instance of REST API client.
         */
        public function add_headers($key, $value = null)
        {
        }
        /**
         * Set REST API request arguments.
         *
         * @param string $name  Optional. Request argument name. Default is ''.
         * @param null   $value Optional. Request argument value. Default is null.
         *
         * @return $this An instance of REST API client.
         */
        public function set_request_arg($name = '', $value = null)
        {
        }
        /**
         * @uses request
         *
         * @param string $endpoint Optional. Default is ''.
         * @param null   $data     Optional. Default is null.
         *
         * @return array|mixed
         * @throws \Exception
         */
        public function post($endpoint = '', $data = null)
        {
        }
        /**
         * @uses request
         *
         * @param string $endpoint Optional. Default is ''.
         * @param null   $data     Optional. Default is null.
         *
         * @return array|mixed
         * @throws \Exception
         */
        public function get($endpoint = '', $data = null)
        {
        }
        /**
         * @param string $method              Optional. Default is 'GET'.
         * @param string $endpoint            Optional. Default is ''.
         * @param null   $request_body        Optional. Default is null.
         * @param int    $valid_response_code Optional. Default is '200'.
         *
         * @return array
         * @throws \Exception
         */
        public function request($method = 'GET', $endpoint = '', $request_body = null, $valid_response_code = 200)
        {
        }
    }
    /**
     * Integration with Google reCAPTCHA
     */
    class Recaptcha_Handler
    {
        const OPTION_NAME_SITE_KEY = 'elementor_pro_recaptcha_site_key';
        const OPTION_NAME_SECRET_KEY = 'elementor_pro_recaptcha_secret_key';
        const OPTION_NAME_RECAPTCHA_THRESHOLD = 'elementor_pro_recaptcha_threshold';
        const V2_CHECKBOX = 'v2_checkbox';
        protected static function get_recaptcha_name()
        {
        }
        public static function get_site_key()
        {
        }
        public static function get_secret_key()
        {
        }
        public static function get_recaptcha_type()
        {
        }
        public static function is_enabled()
        {
        }
        public static function get_setup_message()
        {
        }
        public function register_admin_fields(\Elementor\Settings $settings)
        {
        }
        public function localize_settings($settings)
        {
        }
        protected static function get_script_render_param()
        {
        }
        protected static function get_script_name()
        {
        }
        public function register_scripts()
        {
        }
        public function enqueue_scripts()
        {
        }
        /**
         * @param Form_Record  $record
         * @param Ajax_Handler $ajax_handler
         */
        public function validation($record, $ajax_handler)
        {
        }
        /**
         * @param Ajax_Handler $ajax_handler
         * @param $field
         * @param $message
         */
        protected function add_error($ajax_handler, $field, $message)
        {
        }
        protected function validate_result($result, $field)
        {
        }
        /**
         * @param $item
         * @param $item_index
         * @param $widget Widget_Base
         */
        public function render_field($item, $item_index, $widget)
        {
        }
        /**
         * @param $item
         * @param $item_index
         * @param $widget Widget_Base
         */
        protected function add_render_attributes($item, $item_index, $widget)
        {
        }
        /**
         * @param $item
         * @param $item_index
         * @param $widget Widget_Base
         */
        protected function add_version_specific_render_attributes($item, $item_index, $widget)
        {
        }
        public function add_field_type($field_types)
        {
        }
        public function filter_field_item($item)
        {
        }
        public function __construct()
        {
        }
    }
    abstract class Form_Base extends \ElementorPro\Base\Base_Widget
    {
        public function on_export($element)
        {
        }
        public static function get_button_sizes()
        {
        }
        protected function make_textarea_field($item, $item_index)
        {
        }
        protected function make_select_field($item, $i)
        {
        }
        protected function make_radio_checkbox_field($item, $item_index, $type)
        {
        }
        protected function form_fields_render_attributes($i, $instance, $item)
        {
        }
        public function render_plain_content()
        {
        }
        public function get_attribute_name($item)
        {
        }
        public function get_attribute_id($item)
        {
        }
    }
    class Convertkit_Handler
    {
        /**
         * Convertkit_Handler constructor.
         *
         * @param $api_key
         *
         * @throws \Exception
         */
        public function __construct($api_key)
        {
        }
        public function get_forms_and_tags()
        {
        }
        /**
         * get GetResponse lists associated with API key
         * @return array
         * @throws \Exception
         */
        public function get_forms()
        {
        }
        public function get_tags()
        {
        }
        /**
         * create contact at ConvertKit via api
         *
         * @param array $subscriber_data
         *
         * @return array|mixed
         * @throws \Exception
         */
        public function create_subscriber($form_id, $subscriber_data = [])
        {
        }
    }
    class Mailchimp_Handler
    {
        /**
         * Mailchimp_Handler constructor.
         *
         * @param $api_key
         *
         * @throws \Exception
         */
        public function __construct($api_key)
        {
        }
        public function query($end_point)
        {
        }
        public function post($end_point, $data, $request_args = [])
        {
        }
        public function get_lists()
        {
        }
        public function get_groups($list_id)
        {
        }
        public function get_fields($list_id)
        {
        }
        public function get_list_details($list_id)
        {
        }
    }
    abstract class Integration_Base extends \ElementorPro\Modules\Forms\Classes\Action_Base
    {
        public function handle_panel_request(array $data)
        {
        }
        public static function global_api_control($widget, $api_key = '', $label = '', $condition = [], $id = '')
        {
        }
        protected function get_fields_map_control_options()
        {
        }
        protected final function register_fields_map_control(\ElementorPro\Modules\Forms\Widgets\Form $form)
        {
        }
    }
    class Ajax_Handler
    {
        public $is_success = true;
        public $messages = ['success' => [], 'error' => [], 'admin_error' => []];
        public $data = [];
        public $errors = [];
        const SUCCESS = 'success';
        const ERROR = 'error';
        const FIELD_REQUIRED = 'required_field';
        const INVALID_FORM = 'invalid_form';
        const SERVER_ERROR = 'server_error';
        const SUBSCRIBER_ALREADY_EXISTS = 'subscriber_already_exists';
        public static function is_form_submitted()
        {
        }
        public static function get_default_messages()
        {
        }
        public static function get_default_message($id, $settings)
        {
        }
        public function ajax_send_form()
        {
        }
        public function add_success_message($message)
        {
        }
        public function add_response_data($key, $data)
        {
        }
        public function add_error_message($message)
        {
        }
        public function add_error($field, $message = '')
        {
        }
        public function add_admin_error_message($message)
        {
        }
        public function set_success($bool)
        {
        }
        public function send()
        {
        }
        public function get_current_form()
        {
        }
        public function __construct()
        {
        }
    }
    class Drip_Handler
    {
        /**
         * Drip_Handler constructor.
         *
         * @param $api_token
         *
         * @throws \Exception
         */
        public function __construct($api_token)
        {
        }
        /**
         * get drip accounts associated with API token
         * @return array
         * @throws \Exception
         */
        public function get_accounts()
        {
        }
        /**
         * create subscriber at drip via api
         *
         * @param string $account_id
         * @param array  $subscriber_data
         *
         * @return array|mixed
         * @throws \Exception
         */
        public function create_subscriber($account_id = '', $subscriber_data = [])
        {
        }
    }
    class Getresponse_Handler
    {
        public $rest_client = null;
        public function __construct($api_key)
        {
        }
        /**
         * get GetResponse lists associated with API key
         * @return array
         * @throws \Exception
         */
        public function get_lists()
        {
        }
        public function get_fields()
        {
        }
        /**
         * create contact at GetResponse via api
         *
         * @param array $subscriber_data
         *
         * @return array|mixed
         * @throws \Exception
         */
        public function create_subscriber($subscriber_data = [])
        {
        }
    }
    class Mailerlite_Handler
    {
        /**
         * Mailerlite_Handler constructor.
         *
         * @param $api_key
         *
         * @throws \Exception
         */
        public function __construct($api_key)
        {
        }
        /**
         * get MailerLite groups associated with API key
         * @return array
         * @throws \Exception
         */
        public function get_groups()
        {
        }
        /**
         * get MailerLite fields associated with API key
         * @return array
         * @throws \Exception
         */
        public function get_fields()
        {
        }
        /**
         * create subscriber at drip via api
         *
         * @param string $group
         * @param array  $subscriber_data
         *
         * @return array|mixed
         * @throws \Exception
         */
        public function create_subscriber($group = '', $subscriber_data = [])
        {
        }
    }
    /**
     * Honeypot field
     */
    class Honeypot_Handler
    {
        public function add_field_type($field_types)
        {
        }
        public function hide_label($item, $item_index, $widget)
        {
        }
        /**
         * @param string      $item
         * @param integer     $item_index
         * @param Widget_Base $widget
         */
        public function render_field($item, $item_index, $widget)
        {
        }
        /**
         * @param Form_Record  $record
         * @param Ajax_Handler $ajax_handler
         */
        public function validation($record, $ajax_handler)
        {
        }
        public function update_controls(\Elementor\Widget_Base $widget)
        {
        }
        public function __construct()
        {
        }
    }
    class Form_Record
    {
        protected $sent_data;
        protected $fields;
        protected $form_type;
        protected $form_settings;
        protected $files = [];
        protected $meta = [];
        public function get_formatted_data($with_meta = false)
        {
        }
        /**
         * @param Ajax_Handler $ajax_handler An instance of the ajax handler.
         *
         * @return bool
         */
        public function validate($ajax_handler)
        {
        }
        /**
         * @param Ajax_Handler $ajax_handler An instance of the ajax handler.
         *
         */
        public function process_fields($ajax_handler)
        {
        }
        public function get($property)
        {
        }
        public function set($property, $value)
        {
        }
        public function get_form_settings($setting)
        {
        }
        public function get_field($args)
        {
        }
        public function remove_field($id)
        {
        }
        public function update_field($field_id, $property, $value)
        {
        }
        public function get_form_meta($meta_keys = [])
        {
        }
        public function replace_setting_shortcodes($setting, $urlencode = false)
        {
        }
        public function add_file($id, $index, $filename)
        {
        }
        public function has_field_type($type)
        {
        }
        public function __construct($sent_data, $form)
        {
        }
    }
    class Akismet
    {
        public function __construct()
        {
        }
        /**
         * @param Form $form
         */
        public function register_settings_section($form)
        {
        }
        /**
         * @param Form_Record  $record
         * @param Ajax_Handler $ajax_handler
         */
        public function validation($record, $ajax_handler)
        {
        }
    }
    /**
     * Integration with Google reCAPTCHA
     */
    class Recaptcha_V3_Handler extends \ElementorPro\Modules\Forms\Classes\Recaptcha_Handler
    {
        const OPTION_NAME_V3_SITE_KEY = 'elementor_pro_recaptcha_v3_site_key';
        const OPTION_NAME_V3_SECRET_KEY = 'elementor_pro_recaptcha_v3_secret_key';
        const OPTION_NAME_RECAPTCHA_THRESHOLD = 'elementor_pro_recaptcha_v3_threshold';
        const V3 = 'v3';
        const V3_DEFAULT_THRESHOLD = 0.5;
        const V3_DEFAULT_ACTION = 'Form';
        protected static function get_recaptcha_name()
        {
        }
        public static function get_site_key()
        {
        }
        public static function get_secret_key()
        {
        }
        public static function get_recaptcha_type()
        {
        }
        public static function get_recaptcha_threshold()
        {
        }
        public static function is_enabled()
        {
        }
        public static function get_setup_message()
        {
        }
        public function register_admin_fields(\Elementor\Settings $settings)
        {
        }
        /**
         * @param $item
         * @param $item_index
         * @param $widget Widget_Base
         */
        protected function add_version_specific_render_attributes($item, $item_index, $widget)
        {
        }
        /**
         * @param Ajax_Handler $ajax_handler
         * @param $field
         * @param $message
         */
        protected function add_error($ajax_handler, $field, $message)
        {
        }
        protected function validate_result($result, $field)
        {
        }
        public function add_field_type($field_types)
        {
        }
        /**
         * @param $item
         * @param $item_index
         * @param Widget_Base $widget
         *
         * @return $item
         */
        public function filter_recaptcha_item($item, $item_index, $widget)
        {
        }
        public function __construct()
        {
        }
    }
    class Activecampaign_Handler
    {
        public function __construct($api_key, $base_url)
        {
        }
        /**
         * get ActiveCampaign lists associated with API key
         * @return array
         * @throws \Exception
         */
        public function get_lists()
        {
        }
        /**
         * create contact at Activecampaign via api
         *
         * @param array $subscriber_data
         *
         * @return array|mixed
         * @throws \Exception
         */
        public function create_subscriber($subscriber_data = [])
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Controls {
    class Fields_Repeater extends \Elementor\Control_Repeater
    {
        const CONTROL_TYPE = 'form-fields-repeater';
        public function get_type()
        {
        }
    }
    /**
     * Class Fields_Map
     * @package ElementorPro\Modules\Forms\Controls
     *
     * each item needs the following properties:
     *   remote_id,
     *   remote_label
     *   remote_type
     *   remote_required
     *   local_id
     */
    class Fields_Map extends \Elementor\Control_Repeater
    {
        const CONTROL_TYPE = 'fields_map';
        public function get_type()
        {
        }
        protected function get_default_settings()
        {
        }
    }
}
namespace ElementorPro\Modules\Forms {
    class Module extends \ElementorPro\Base\Module_Base
    {
        /**
         * @var Form_Actions_Registrar
         */
        public $actions_registrar;
        /**
         * @var Form_Fields_Registrar
         */
        public $fields_registrar;
        const ACTIVITY_LOG_LICENSE_FEATURE_NAME = 'activity-log';
        const CF7DB_LICENSE_FEATURE_NAME = 'cf7db';
        const AKISMET_LICENSE_FEATURE_NAME = 'akismet';
        const WIDGET_NAME_CLASS_NAME_MAP = ['form' => 'Form', 'login' => 'Login'];
        public function get_name()
        {
        }
        public function get_widgets()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/forms/assets/scss/frontend.scss`
         * to `/assets/css/widget-forms.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
        public static function find_element_recursive($elements, $form_id)
        {
        }
        public function register_controls(\Elementor\Controls_Manager $controls_manager)
        {
        }
        /**
         * @param array $data
         *
         * @return array
         * @throws \Exception
         */
        public function forms_panel_action_data(array $data)
        {
        }
        /**
         * @deprecated 3.5.0 Use `fields_registrar->register()` instead.
         */
        public function add_form_field_type($type, $instance)
        {
        }
        /**
         * @deprecated 3.5.0 Use `actions_registrar->register()` instead.
         */
        public function add_form_action($id, $instance)
        {
        }
        /**
         * @deprecated 3.5.0 Use `actions_registrar->get()` instead.
         */
        public function get_form_actions($id = null)
        {
        }
        public function register_ajax_actions(\Elementor\Core\Common\Modules\Ajax\Module $ajax)
        {
        }
        public function register_submissions_admin_fields(\Elementor\Settings $settings)
        {
        }
        /**
         * Module constructor.
         */
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Actions {
    class Email extends \ElementorPro\Modules\Forms\Classes\Action_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function register_settings_section($widget)
        {
        }
        public function maybe_add_site_mailer_notice($widget)
        {
        }
        public function on_export($element)
        {
        }
        /**
         * @param \ElementorPro\Modules\Forms\Classes\Form_Record  $record
         * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
         */
        public function run($record, $ajax_handler)
        {
        }
        // Allow overwrite the control_id with a prefix, @see Email2
        protected function get_control_id($control_id)
        {
        }
        protected function get_reply_to($record, $fields)
        {
        }
    }
    class Mailchimp extends \ElementorPro\Modules\Forms\Classes\Integration_Base
    {
        const OPTION_NAME_API_KEY = 'pro_mailchimp_api_key';
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function register_settings_section($widget)
        {
        }
        public function on_export($element)
        {
        }
        public function run($record, $ajax_handler)
        {
        }
        public function ajax_validate_api_token()
        {
        }
        /**
         * @param array $data
         *
         * @return array
         * @throws \Exception
         */
        public function handle_panel_request(array $data)
        {
        }
        public function register_admin_fields(\Elementor\Settings $settings)
        {
        }
        public function __construct()
        {
        }
        protected function get_fields_map_control_options()
        {
        }
    }
    class CF7DB extends \ElementorPro\Modules\Forms\Classes\Action_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function register_settings_section($widget)
        {
        }
        public function on_export($element)
        {
        }
        public function run($record, $ajax_handler)
        {
        }
    }
    class Convertkit extends \ElementorPro\Modules\Forms\Classes\Integration_Base
    {
        const OPTION_NAME_API_KEY = 'pro_convertkit_api_key';
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function register_settings_section($widget)
        {
        }
        public function on_export($element)
        {
        }
        public function run($record, $ajax_handler)
        {
        }
        /**
         * @param array $data
         *
         * @return array
         * @throws \Exception
         */
        public function handle_panel_request(array $data)
        {
        }
        public function ajax_validate_api_token()
        {
        }
        public function register_admin_fields(\Elementor\Settings $settings)
        {
        }
        public function __construct()
        {
        }
        protected function get_fields_map_control_options()
        {
        }
    }
    class Mailerlite extends \ElementorPro\Modules\Forms\Classes\Integration_Base
    {
        const OPTION_NAME_API_KEY = 'pro_mailerlite_api_key';
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function register_settings_section($widget)
        {
        }
        public function on_export($element)
        {
        }
        public function run($record, $ajax_handler)
        {
        }
        public function handle_panel_request(array $data)
        {
        }
        public function register_admin_fields(\Elementor\Settings $settings)
        {
        }
        public function ajax_validate_api_key()
        {
        }
        public function __construct()
        {
        }
        protected function get_fields_map_control_options()
        {
        }
    }
    class Webhook extends \ElementorPro\Modules\Forms\Classes\Action_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function register_settings_section($widget)
        {
        }
        public function on_export($element)
        {
        }
        public function run($record, $ajax_handler)
        {
        }
    }
    /**
     * Integration with Activity Log
     */
    class Activity_Log extends \ElementorPro\Modules\Forms\Classes\Action_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function register_settings_section($widget)
        {
        }
        public function on_export($element)
        {
        }
        public function aal_init_roles($roles)
        {
        }
        public function run($record, $ajax_handler)
        {
        }
        public function __construct()
        {
        }
    }
    class Activecampaign extends \ElementorPro\Modules\Forms\Classes\Integration_Base
    {
        const OPTION_NAME_API_KEY = 'pro_activecampaign_api_key';
        const OPTION_NAME_API_URL = 'pro_activecampaign_api_url';
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function register_settings_section($widget)
        {
        }
        public function on_export($element)
        {
        }
        public function run($record, $ajax_handler)
        {
        }
        /**
         * @param array $data
         *
         * @return array
         * @throws \Exception
         */
        public function handle_panel_request(array $data)
        {
        }
        public function ajax_validate_api_token()
        {
        }
        public function register_admin_fields(\Elementor\Settings $settings)
        {
        }
        public function __construct()
        {
        }
        protected function get_fields_map_control_options()
        {
        }
    }
    class Slack extends \ElementorPro\Modules\Forms\Classes\Action_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function register_settings_section($widget)
        {
        }
        public function on_export($element)
        {
        }
        public function run($record, $ajax_handler)
        {
        }
    }
    class Discord extends \ElementorPro\Modules\Forms\Classes\Action_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function register_settings_section($widget)
        {
        }
        public function on_export($element)
        {
        }
        public function run($record, $ajax_handler)
        {
        }
    }
    class Mailpoet3 extends \ElementorPro\Modules\Forms\Classes\Integration_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function register_settings_section($widget)
        {
        }
        public function on_export($element)
        {
        }
        public function run($record, $ajax_handler)
        {
        }
        protected function get_fields_map_control_options()
        {
        }
    }
    class Drip extends \ElementorPro\Modules\Forms\Classes\Integration_Base
    {
        const OPTION_NAME_API_KEY = 'pro_drip_api_token';
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function register_settings_section($widget)
        {
        }
        public function on_export($element)
        {
        }
        public function run($record, $ajax_handler)
        {
        }
        /**
         * @param array $data
         *
         * @return array
         * @throws \Exception
         */
        public function handle_panel_request(array $data)
        {
        }
        public function register_admin_fields(\Elementor\Settings $settings)
        {
        }
        /**
         *
         */
        public function ajax_validate_api_token()
        {
        }
        public function __construct()
        {
        }
        protected function get_fields_map_control_options()
        {
        }
    }
    class Redirect extends \ElementorPro\Modules\Forms\Classes\Action_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function register_settings_section($widget)
        {
        }
        public function on_export($element)
        {
        }
        public function run($record, $ajax_handler)
        {
        }
    }
    class Email2 extends \ElementorPro\Modules\Forms\Actions\Email
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        protected function get_control_id($control_id)
        {
        }
        protected function get_reply_to($record, $fields)
        {
        }
        public function register_settings_section($widget)
        {
        }
    }
    class Getresponse extends \ElementorPro\Modules\Forms\Classes\Integration_Base
    {
        const OPTION_NAME_API_KEY = 'pro_getresponse_api_key';
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function register_settings_section($widget)
        {
        }
        public function on_export($element)
        {
        }
        public function run($record, $ajax_handler)
        {
        }
        /**
         * @param array $data
         *
         * @return array
         * @throws \Exception
         */
        public function handle_panel_request(array $data)
        {
        }
        public function ajax_validate_api_token()
        {
        }
        public function register_admin_fields(\Elementor\Settings $settings)
        {
        }
        public function __construct()
        {
        }
        protected function get_fields_map_control_options()
        {
        }
    }
    class Mailpoet extends \ElementorPro\Modules\Forms\Classes\Integration_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function register_settings_section($widget)
        {
        }
        public function on_export($element)
        {
        }
        public function run($record, $ajax_handler)
        {
        }
        protected function get_fields_map_control_options()
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Submissions\Database\Migrations {
    abstract class Base_Migration extends \Elementor\Core\Base\Base_Object
    {
        /*
         * Ref: wp-admin/includes/schema.php::wp_get_db_schema
         *
         * Indexes have a maximum size of 767 bytes. Historically, we haven't need to be concerned about that.
         * As of 4.2, however, we moved to utf8mb4, which uses 4 bytes per character. This means that an index which
         * used to have room for floor(767/3) = 255 characters, now only has room for floor(767/4) = 191 characters.
         */
        const MAX_INDEX_LENGTH = 191;
        /**
         * @var \wpdb
         */
        protected $wpdb;
        /**
         * @var Query
         */
        protected $query;
        /**
         * Base_Migration constructor.
         *
         * @param \wpdb $wpdb
         */
        public function __construct(\wpdb $wpdb)
        {
        }
        /**
         * Run migration.
         *
         * @return void
         */
        public abstract function run();
    }
    class Fix_Indexes extends \ElementorPro\Modules\Forms\Submissions\Database\Migrations\Base_Migration
    {
        /**
         * In the previous migrations some databases had problems with the indexes.
         * this migration checks if user's tables are filled with required indexes, if not it creates them.
         */
        public function run()
        {
        }
    }
    class Initial extends \ElementorPro\Modules\Forms\Submissions\Database\Migrations\Base_Migration
    {
        public function run()
        {
        }
    }
    class Referer_Extra extends \ElementorPro\Modules\Forms\Submissions\Database\Migrations\Base_Migration
    {
        public function run()
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Submissions\Database\Repositories {
    class Form_Snapshot_Repository extends \Elementor\Core\Base\Base_Object
    {
        // There are two underscore prefix to avoid duplicate the meta when the post will be published.
        const POST_META_KEY = '__elementor_forms_snapshot';
        /**
         * @return static
         */
        public static function instance()
        {
        }
        /**
         * Get specific form.
         *
         * @param      $post_id
         * @param      $form_id
         * @param bool $from_cache
         *
         * @return Form_Snapshot|null
         */
        public function find($post_id, $form_id, $from_cache = true)
        {
        }
        /**
         * Get all the forms.
         *
         * @return Collection
         */
        public function all()
        {
        }
        /**
         * @param $post_id
         * @param $form_id
         * @param $data
         *
         * @return Form_Snapshot
         */
        public function create_or_update($post_id, $form_id, $data)
        {
        }
        public function clear_cache()
        {
        }
        /**
         * Forms_Repository constructor.
         */
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Submissions\Database {
    class Query extends \Elementor\Core\Base\Base_Object
    {
        const STATUS_NEW = 'new';
        const STATUS_TRASH = 'trash';
        const ACTIONS_LOG_STATUS_SUCCESS = 'success';
        const ACTIONS_LOG_STATUS_FAILED = 'failed';
        const TYPE_SUBMISSION = 'submission';
        const E_SUBMISSIONS_ACTIONS_LOG = 'e_submissions_actions_log';
        const E_SUBMISSIONS_VALUES = 'e_submissions_values';
        const E_SUBMISSIONS = 'e_submissions';
        public static function get_instance()
        {
        }
        public function get_submissions($args = [])
        {
        }
        /**
         * Get count by status.
         *
         * @param $filter
         *
         * @return Collection
         */
        public function count_submissions_by_status($filter = [])
        {
        }
        public function get_submissions_by_email($email, $include_submission_values = false)
        {
        }
        /**
         * @param int $delete_timestamp
         *
         * @return array
         */
        public function get_trashed_submission_ids_to_delete($delete_timestamp)
        {
        }
        public function get_submission($id)
        {
        }
        public function get_referrers($search = '', $value = '')
        {
        }
        /**
         * @param       $submissions
         * @param false $only_main
         *
         * @return Collection
         */
        public function get_submissions_meta($submissions, $only_main = false)
        {
        }
        /**
         * @param $post_id
         * @param $element_id
         *
         * @return Collection
         */
        public function get_submissions_value_keys($post_id, $element_id)
        {
        }
        /**
         * @param $submission_id
         *
         * @return array|null
         */
        public function get_submissions_actions_log($submission_id)
        {
        }
        /**
         * Add submission.
         *
         * @param array $submission_data
         * @param array $fields_data
         *
         * @return int id or 0
         */
        public function add_submission(array $submission_data, array $fields_data)
        {
        }
        /**
         * @param       $submission_id
         * @param array $data
         * @param array $values
         *
         * @return bool|int affected rows
         */
        public function update_submission($submission_id, array $data, $values = [])
        {
        }
        /**
         * Move single submission to trash
         *
         * @param $id
         *
         * @return bool|int number of affected rows or false if failed
         */
        public function move_to_trash_submission($id)
        {
        }
        /**
         * Delete a single submission.
         *
         * @param $id
         *
         * @return bool|int number of affected rows or false if failed
         */
        public function delete_submission($id)
        {
        }
        /**
         * Restore a single submission.
         *
         * @param $id
         *
         * @return bool|int number of affected rows or false if failed
         */
        public function restore($id)
        {
        }
        /**
         * @param $submission_id
         * @param Action_Base $action Should be class based on ActionBase (do not type hint to support third party plugins)
         * @param $status
         * @param null $log_message
         *
         * @return bool|int
         */
        public function add_action_log($submission_id, $action, $status, $log_message = null)
        {
        }
        public function get_last_error()
        {
        }
        public function get_table_submissions()
        {
        }
        public function get_table_submissions_values()
        {
        }
        public function get_table_form_actions_log()
        {
        }
        public function __construct()
        {
        }
    }
    class Migration extends \Elementor\Core\Base\Base_Object
    {
        const OPTION_DB_VERSION = 'elementor_submissions_db_version';
        // This version must be updated when new migration created.
        const CURRENT_DB_VERSION = 5;
        /**
         * Checks if there is a need to run migrations.
         */
        public static function install()
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Submissions\Database\Entities {
    /**
     * The Form_Snapshot is a snapshot of the form as it saved in the document data, on each submission creation it updates the snapshot to the current state of the form,
     * As a consequence the queries are quicker (filters, export, etc.) and in case the form itself removed from the document, the Form_Snapshot
     * remains and allows the user export and filter submissions as before.
     */
    class Form_Snapshot extends \Elementor\Core\Base\Base_Object implements \JsonSerializable
    {
        /**
         * @var string
         */
        public $id;
        /**
         * @var int
         */
        public $post_id;
        /**
         * @var string
         */
        public $name;
        /**
         * @var array {
         *      @type string $id
         *      @type string $type
         *      @type string $label
         * }
         */
        public $fields = [];
        /**
         * @param $post_id
         * @param $form_id
         *
         * @return string
         */
        public static function generate_key($post_id, $form_id)
        {
        }
        /**
         * @return string
         */
        public function get_key()
        {
        }
        /**
         * @return string
         */
        public function get_label()
        {
        }
        /**
         * Implement for the JsonSerializable method, will trigger when trying to json_encode this object.
         *
         * @return array
         */
        #[\ReturnTypeWillChange]
        public function jsonSerialize()
        {
        }
        /**
         * Form constructor.
         *
         * @param $post_id
         * @param $data
         */
        public function __construct($post_id, $data)
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Submissions {
    class Personal_Data extends \Elementor\Core\Base\Base_Object
    {
        const WP_KEY = 'elementor-form-submissions';
        /**
         * Personal_Data constructor.
         */
        public function __construct()
        {
        }
    }
    class Component extends \ElementorPro\Base\Module_Base
    {
        const NAME = 'form-submissions';
        const PAGE_ID = 'e-form-submissions';
        /**
         * @return string
         */
        public function get_name()
        {
        }
        /**
         * @return string
         */
        public function get_assets_base_url()
        {
        }
        /**
         * Component constructor.
         */
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Submissions\Actions {
    class Save_To_Database extends \ElementorPro\Modules\Forms\Classes\Action_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function register_settings_section($widget)
        {
        }
        public function on_export($element)
        {
        }
        /**
         * @param \ElementorPro\Modules\Forms\Classes\Form_Record  $record
         * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
         */
        public function run($record, $ajax_handler)
        {
        }
        /**
         * Save_To_Database constructor.
         */
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Submissions\Export {
    class CSV_Export extends \Elementor\Core\Base\Base_Object
    {
        /**
         * Csv_Export constructor.
         *
         * Csv_Export constructor.
         *
         * @param Collection $submissions
         */
        public function __construct(\Elementor\Core\Utils\Collection $submissions)
        {
        }
        /**
         * @return array
         */
        public function prepare_for_json_response()
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Submissions\AdminMenuItems {
    class Submissions_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page
    {
        public function get_capability()
        {
        }
        public function get_label()
        {
        }
        public function get_page_title()
        {
        }
        public function get_parent_slug()
        {
        }
        public function is_visible()
        {
        }
        public function get_position()
        {
        }
        public function render()
        {
        }
        public function has_submissions($min_count = 1) : bool
        {
        }
        public function should_show_hint() : bool
        {
        }
    }
}
namespace ElementorPro\Modules\Tiers\AdminMenuItems {
    abstract class Base_Promotion_Template implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page
    {
        protected abstract function get_promotion_title() : string;
        protected abstract function get_cta_url() : string;
        protected abstract function get_content_lines() : array;
        protected abstract function get_video_url() : string;
        public function is_visible()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_capability()
        {
        }
        protected function get_cta_text()
        {
        }
        /**
         * Should the promotion have a side note.
         * @return string
         */
        protected function get_side_note() : string
        {
        }
        public function render()
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Submissions\AdminMenuItems {
    class Submissions_Promotion_Menu_Item extends \ElementorPro\Modules\Tiers\AdminMenuItems\Base_Promotion_Template
    {
        public function get_name() : string
        {
        }
        public function get_label() : string
        {
        }
        public function get_page_title() : string
        {
        }
        protected function get_promotion_title() : string
        {
        }
        protected function get_content_lines() : array
        {
        }
        protected function get_video_url() : string
        {
        }
        public function get_cta_text()
        {
        }
        protected function get_cta_url() : string
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Submissions\Data {
    class Controller extends \Elementor\Data\Base\Controller
    {
        public function get_name()
        {
        }
        public function get_collection_params()
        {
        }
        public function get_items($request)
        {
        }
        public function get_item($request)
        {
        }
        /**
         * @param \WP_REST_Request $request
         *
         * @return \WP_Error|\WP_REST_Response
         */
        public function delete_items($request)
        {
        }
        /**
         * Delete single submission
         *
         * @param \WP_REST_Request $request
         *
         * @return \WP_Error|\WP_REST_Response
         */
        public function delete_item($request)
        {
        }
        /**
         * Update a single submission.
         *
         * @param \WP_REST_Request $request
         *
         * @return \WP_Error|\WP_REST_Response
         */
        public function update_item($request)
        {
        }
        /**
         * Update multiple submissions.
         *
         * @param $request
         *
         * @return \WP_Error|\WP_REST_Response
         */
        public function update_items($request)
        {
        }
        public function get_permission_callback($request)
        {
        }
        public function register_endpoints()
        {
        }
        protected function register_internal_endpoints()
        {
        }
        protected function register()
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Submissions\Data\Endpoints {
    class Index extends \Elementor\Data\Base\Endpoint
    {
        public function get_name()
        {
        }
        protected function register()
        {
        }
    }
    /**
     * This logic should be under index.php::get_items method, but for now
     * the Data JS API does not support sending Headers like `Accept: text/csv`.
     */
    class Export extends \Elementor\Data\Base\Endpoint
    {
        const EXPORT_BY_IDS = 'ids';
        const EXPORT_BY_FILTER = 'filter';
        public function get_name()
        {
        }
        protected function register()
        {
        }
        /**
         * @param \WP_REST_Request $request
         *
         * @return \WP_Error|\WP_REST_Response
         */
        public function get_items($request)
        {
        }
    }
    class Referer extends \Elementor\Data\Base\Endpoint
    {
        public function get_name()
        {
        }
        protected function register()
        {
        }
        public function get_items($request)
        {
        }
    }
    class Restore extends \Elementor\Data\Base\Endpoint
    {
        public function get_name()
        {
        }
        /**
         * Restore a single trashed submission.
         *
         * @param string           $id
         * @param \WP_REST_Request $request
         *
         * @return \WP_Error|\WP_REST_Response
         */
        public function update_item($id, $request)
        {
        }
        /**
         * Restore multiple trashed submissions.
         *
         * @param \WP_REST_Request $request
         *
         * @return \WP_Error|\WP_REST_Response
         */
        public function update_items($request)
        {
        }
        protected function register()
        {
        }
        public function __construct($controller)
        {
        }
    }
    class Forms_Index extends \Elementor\Data\Base\Endpoint
    {
        public function get_name()
        {
        }
        protected function register()
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Submissions\Data\Responses {
    class Query_Failed_Response extends \WP_Error
    {
        public function __construct($query_error_message, $message = null)
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Submissions\Data {
    class Forms_Controller extends \Elementor\Data\Base\Controller
    {
        public function get_name()
        {
        }
        public function get_items($request)
        {
        }
        public function register_endpoints()
        {
        }
        protected function register_internal_endpoints()
        {
        }
        public function get_permission_callback($request)
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Fields {
    abstract class Field_Base
    {
        /**
         * @deprecated 3.28.0 Use `get_script_depends()` instead.
         */
        public $depended_scripts = [];
        /**
         * @deprecated 3.28.0 Use `get_style_depends()` instead.
         */
        public $depended_styles = [];
        public abstract function get_type();
        public abstract function get_name();
        /**
         * Get the field ID.
         *
         * TODO: Make it an abstract function that will replace `get_type()`.
         *
         * @since 3.5.0
         *
         * @return string
         */
        public function get_id()
        {
        }
        public abstract function render($item, $item_index, $form);
        public function validation($field, \ElementorPro\Modules\Forms\Classes\Form_Record $record, \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler)
        {
        }
        public function process_field($field, \ElementorPro\Modules\Forms\Classes\Form_Record $record, \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler)
        {
        }
        public function add_assets_depends($form)
        {
        }
        public function get_script_depends() : array
        {
        }
        public function get_style_depends() : array
        {
        }
        public function add_preview_depends()
        {
        }
        public function add_field_type($field_types)
        {
        }
        public function field_render($item, $item_index, $form)
        {
        }
        public function sanitize_field($value, $field)
        {
        }
        public function inject_field_controls($array, $controls_to_inject)
        {
        }
        public function __construct()
        {
        }
    }
    class Tel extends \ElementorPro\Modules\Forms\Fields\Field_Base
    {
        public function get_type()
        {
        }
        public function get_name()
        {
        }
        public function render($item, $item_index, $form)
        {
        }
        public function validation($field, \ElementorPro\Modules\Forms\Classes\Form_Record $record, \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler)
        {
        }
    }
    class Time extends \ElementorPro\Modules\Forms\Fields\Field_Base
    {
        public function get_type()
        {
        }
        public function get_name()
        {
        }
        public function get_script_depends() : array
        {
        }
        public function get_style_depends() : array
        {
        }
        public function update_controls($widget)
        {
        }
        public function render($item, $item_index, $form)
        {
        }
        public function validation($field, \ElementorPro\Modules\Forms\Classes\Form_Record $record, \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler)
        {
        }
    }
    class Number extends \ElementorPro\Modules\Forms\Fields\Field_Base
    {
        public function get_type()
        {
        }
        public function get_name()
        {
        }
        public function render($item, $item_index, $form)
        {
        }
        /**
         * @param Widget_Base $widget
         */
        public function update_controls($widget)
        {
        }
        public function validation($field, \ElementorPro\Modules\Forms\Classes\Form_Record $record, \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler)
        {
        }
        public function sanitize_field($value, $field)
        {
        }
    }
    class Step extends \ElementorPro\Modules\Forms\Fields\Field_Base
    {
        public function get_type()
        {
        }
        public function get_name()
        {
        }
        public function render($item, $item_index, $form)
        {
        }
        /**
         * @param Widget_Base $widget
         */
        public function update_controls($widget)
        {
        }
    }
    class Acceptance extends \ElementorPro\Modules\Forms\Fields\Field_Base
    {
        public function get_type()
        {
        }
        public function get_name()
        {
        }
        public function update_controls($widget)
        {
        }
        public function render($item, $item_index, $form)
        {
        }
    }
    class Upload extends \ElementorPro\Modules\Forms\Fields\Field_Base
    {
        public $fixed_files_indices = false;
        const MODE_LINK = 'link';
        const MODE_ATTACH = 'attach';
        const MODE_BOTH = 'both';
        public function get_type()
        {
        }
        public function get_name()
        {
        }
        /**
         * @param Widget_Base $widget
         */
        public function update_controls($widget)
        {
        }
        /**
         * @param      $item
         * @param      $item_index
         * @param Form $form
         */
        public function render($item, $item_index, $form)
        {
        }
        /**
         * validate uploaded file field
         *
         * @param array                $field
         * @param Classes\Form_Record  $record
         * @param Classes\Ajax_Handler $ajax_handler
         */
        public function validation($field, \ElementorPro\Modules\Forms\Classes\Form_Record $record, \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler)
        {
        }
        /**
         * process file and move it to uploads directory
         *
         * @param array                $field
         * @param Classes\Form_Record  $record
         * @param Classes\Ajax_Handler $ajax_handler
         */
        public function process_field($field, \ElementorPro\Modules\Forms\Classes\Form_Record $record, \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler)
        {
        }
        /**
         * Used to set the upload filed values with
         * value => file url
         * raw_value => file path
         *
         * @param Classes\Form_Record  $record
         * @param Classes\Ajax_Handler $ajax_handler
         */
        public function set_file_fields_values(\ElementorPro\Modules\Forms\Classes\Form_Record $record, \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler)
        {
        }
        public function __construct()
        {
        }
    }
    class Date extends \ElementorPro\Modules\Forms\Fields\Field_Base
    {
        public function get_type()
        {
        }
        public function get_name()
        {
        }
        public function get_script_depends() : array
        {
        }
        public function get_style_depends() : array
        {
        }
        public function render($item, $item_index, $form)
        {
        }
        public function update_controls($widget)
        {
        }
    }
}
namespace ElementorPro\Modules\Forms\Widgets {
    class Login extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        /**
         * Render Login Form output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Form extends \ElementorPro\Modules\Forms\Classes\Form_Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        /**
         * Render Form widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
        public function get_group_name()
        {
        }
    }
}
namespace ElementorPro\Modules\NestedCarousel {
    class Module extends \ElementorPro\Base\Module_Base
    {
        const NESTED_CAROUSEL = 'nested-carousel';
        public function __construct()
        {
        }
        public function get_widgets()
        {
        }
        public function get_name()
        {
        }
        public static function is_active()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/nested-carousel/assets/scss/frontend.scss`
         * to `/assets/css/widget-nested-carousel.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Base {
    trait Base_Carousel_Trait
    {
        public $num_of_carousel_items;
        public function add_carousel_layout_controls($params)
        {
        }
        public function add_carousel_settings_controls($params = [])
        {
        }
        public function add_carousel_navigation_controls($params = [])
        {
        }
        public function add_carousel_navigation_styling_controls($params = [])
        {
        }
        public function add_carousel_pagination_controls($params = [])
        {
        }
        public function add_carousel_pagination_style_controls($params)
        {
        }
        public function render_carousel_footer($settings)
        {
        }
        private function render_swiper_button($type)
        {
        }
        /**
         * @param string $state
         * @param $css_prefix
         * @return void
         */
        private function add_navigation_state_based_style_controls(string $state, $css_prefix)
        {
        }
        /**
         * @param string $css_prefix
         * @param string $pagination_type
         *
         * @return void
         */
        private function add_custom_pagination(string $css_prefix, string $pagination_type)
        {
        }
        /**
         * @return array
         */
        private function get_position_slider_initial_configuration() : array
        {
        }
        /**
         * @return array[]
         */
        private function get_arrows_horizontal_navigation_controls() : array
        {
        }
        /**
         * @param array $settings
         * @return boolean
         */
        private function should_render_pagination_and_arrows(array $settings)
        {
        }
    }
}
namespace ElementorPro\Modules\NestedCarousel\Widgets {
    class Nested_Carousel extends \Elementor\Modules\NestedElements\Base\Widget_Nested_Base
    {
        use \ElementorPro\Base\Base_Widget_Trait;
        use \ElementorPro\Base\Base_Carousel_Trait;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        // TODO: Replace this check with `is_active_feature` on 3.28.0 to support is_active_feature second parameter.
        public function show_in_panel()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        /**
         * Get script dependencies.
         *
         * Retrieve the list of script dependencies the widget requires.
         *
         * @since 3.27.0
         * @access public
         *
         * @return array Widget script dependencies.
         */
        public function get_script_depends() : array
        {
        }
        protected function get_default_children_elements()
        {
        }
        protected function get_default_repeater_title_setting_key()
        {
        }
        protected function get_default_children_title()
        {
        }
        protected function get_default_children_placeholder_selector()
        {
        }
        protected function get_html_wrapper_class()
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        protected function get_initial_config() : array
        {
        }
        protected function get_default_children_container_placeholder_selector()
        {
        }
        protected function content_template_single_repeater_item()
        {
        }
        protected function content_template()
        {
        }
        protected function content_template_navigation_arrows()
        {
        }
    }
}
namespace ElementorPro\Modules\VideoPlaylist {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        public function get_widgets()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/video-playlist/assets/scss/frontend.scss`
         * to `/assets/css/widget-video-playlist.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\VideoPlaylist\Widgets {
    class Video_Playlist extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        protected function content_template()
        {
        }
    }
}
namespace ElementorPro\Modules\NavMenu {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        public function get_widgets()
        {
        }
        public function get_name()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/nav-menu/assets/scss/frontend.scss`
         * to `/assets/css/widget-nav-menu.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\NavMenu\Widgets {
    class Nav_Menu extends \ElementorPro\Base\Base_Widget
    {
        protected $nav_menu_index = 1;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        public function get_script_depends()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function get_nav_menu_index()
        {
        }
        protected function register_controls()
        {
        }
        public function get_frontend_settings()
        {
        }
        protected function render()
        {
        }
        public function handle_link_classes($atts, $item, $args, $depth)
        {
        }
        public function handle_link_tabindex($atts, $item, $args)
        {
        }
        public function handle_sub_menu_classes($classes)
        {
        }
        public function render_plain_content()
        {
        }
        public function on_export($element)
        {
        }
        /**
         * When importing a menu, if the menu has a slug that already exists, we add "-duplicate" to the slug of the imported menu.
         * Upon importing a menu widget, we replace the slug to the correct one by fetching it from the correct ID in the $data array.
         *
         * Please take note that this function overrides On_Import_Trait::on_import_update_dynamic_content().
         *
         * @param array $element_config
         * @param array $data
         * @param $controls
         *
         * @return array
         */
        public static function on_import_update_dynamic_content(array $element_config, array $data, $controls = null) : array
        {
        }
    }
}
namespace ElementorPro\Modules\Countdown {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        public function get_widgets()
        {
        }
        public function get_name()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/countdown/assets/scss/frontend.scss`
         * to `/assets/css/widget-countdown.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\Countdown\Widgets {
    class Countdown extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        public function get_default_countdown_labels()
        {
        }
        protected function render()
        {
        }
    }
}
namespace ElementorPro\Modules\Sticky {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        public function register_controls(\Elementor\Element_Base $element)
        {
        }
        public function register_frontend_styles()
        {
        }
        public function enqueue_preview_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\Gallery {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        /**
         * Get module name.
         *
         * Retrieve the module name.
         *
         * @since  2.7.0
         * @access public
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
        public function get_widgets()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/gallery/assets/scss/frontend.scss`
         * to `/assets/css/widget-gallery.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\Gallery\Widgets {
    class Gallery extends \ElementorPro\Base\Base_Widget
    {
        /**
         * Get element name.
         *
         * Retrieve the element name.
         *
         * @return string The name.
         * @since 2.7.0
         * @access public
         *
         */
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_script_depends()
        {
        }
        public function get_style_depends() : array
        {
        }
        public function get_icon()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function register_controls()
        {
        }
        protected function render_static()
        {
        }
        /**
         *
         */
        protected function render()
        {
        }
        protected function get_image_data($attachment, $image_id, $image_src, $settings) : array
        {
        }
    }
}
namespace ElementorPro\Modules\FloatingButtons\Classes\Render {
    class Contact_Buttons_Var_8_Render extends \Elementor\Modules\FloatingButtons\Classes\Render\Contact_Buttons_Core_Render
    {
        protected function render_chat_button_icon() : void
        {
        }
        protected function render_close_button() : void
        {
        }
        protected function render_chat_button() : void
        {
        }
        protected function build_layout_render_attribute() : void
        {
        }
        protected function render_top_bar() : void
        {
        }
        protected function render_contact_section() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Contact_Buttons_Var_5_Render extends \Elementor\Modules\FloatingButtons\Classes\Render\Contact_Buttons_Core_Render
    {
        protected function build_layout_render_attribute() : void
        {
        }
        protected function render_chat_button() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Floating_Bars_Var_3_Render extends \Elementor\Modules\FloatingButtons\Classes\Render\Floating_Bars_Core_Render
    {
        public function render_shape() : void
        {
        }
        public function render_coupon_button() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Contact_Buttons_Var_7_Render extends \Elementor\Modules\FloatingButtons\Classes\Render\Contact_Buttons_Core_Render
    {
        protected function build_layout_render_attribute() : void
        {
        }
        protected function get_platform_text() : string
        {
        }
        protected function get_chat_button_text() : string
        {
        }
        protected function get_aria_label() : string
        {
        }
        protected function render_chat_button() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Contact_Buttons_Var_1_Render extends \Elementor\Modules\FloatingButtons\Classes\Render\Contact_Buttons_Core_Render
    {
        protected function render_message_bubble() : void
        {
        }
        protected function render_chat_button_icon() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Contact_Buttons_Var_10_Render extends \Elementor\Modules\FloatingButtons\Classes\Render\Contact_Buttons_Core_Render
    {
        protected function build_layout_render_attribute() : void
        {
        }
        protected function render_contact_section() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Contact_Buttons_Var_9_Render extends \Elementor\Modules\FloatingButtons\Classes\Render\Contact_Buttons_Core_Render
    {
        protected function render_chat_button() : void
        {
        }
        protected function build_layout_render_attribute() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Contact_Buttons_Var_4_Render extends \Elementor\Modules\FloatingButtons\Classes\Render\Contact_Buttons_Core_Render
    {
        protected function render_chat_button_icon() : void
        {
        }
        protected function build_layout_render_attribute() : void
        {
        }
        protected function render_close_button() : void
        {
        }
        protected function render_chat_button() : void
        {
        }
        protected function render_contact_section() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Contact_Buttons_Var_3_Render extends \Elementor\Modules\FloatingButtons\Classes\Render\Contact_Buttons_Core_Render
    {
        protected function render_chat_button_icon() : void
        {
        }
        protected function render_chat_button() : void
        {
        }
        protected function render_top_bar() : void
        {
        }
        protected function render_contact_section() : void
        {
        }
        protected function render_send_button_section() : void
        {
        }
        protected function build_layout_render_attribute() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Floating_Bars_Var_2_Render extends \Elementor\Modules\FloatingButtons\Classes\Render\Floating_Bars_Core_Render
    {
        protected function render_headlines() : void
        {
        }
        protected function render_pause_play_buttons() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Contact_Buttons_Var_6_Render extends \Elementor\Modules\FloatingButtons\Classes\Render\Contact_Buttons_Core_Render
    {
        protected function build_layout_render_attribute() : void
        {
        }
        protected function render_contact_links() : void
        {
        }
        public function render() : void
        {
        }
    }
}
namespace ElementorPro\Modules\FloatingButtons {
    class Module extends \Elementor\Core\Base\Module
    {
        const EXPERIMENT_NAME = 'floating-buttons';
        const FLOATING_BUTTONS_DOCUMENT_TYPE = 'floating-buttons';
        const CPT_FLOATING_BUTTONS = 'e-floating-buttons';
        public static function is_active() : bool
        {
        }
        public function get_name() : string
        {
        }
        public function get_widgets() : array
        {
        }
        public static function get_floating_elements_types()
        {
        }
        public function is_preview_for_document($post_id)
        {
        }
        public function __construct()
        {
        }
        public function print_floating_buttons()
        {
        }
        public function register_location(\ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $location_manager)
        {
        }
    }
}
namespace ElementorPro\Modules\FloatingButtons\Documents {
    class Floating_Buttons extends \ElementorPro\Modules\ThemeBuilder\Documents\Theme_Document
    {
        const FLOATING_ELEMENTS_TYPE_META_KEY = '_elementor_floating_elements_type';
        use \Elementor\Modules\Library\Traits\Library;
        public static function get_floating_element_type($post_id)
        {
        }
        public static function get_properties()
        {
        }
        public function get_edit_url()
        {
        }
        public function print_content()
        {
        }
        public function get_location()
        {
        }
        public static function get_type()
        {
        }
        public static function register_post_fields_control($document)
        {
        }
        public static function register_hide_title_control($document)
        {
        }
        public static function get_preview_as_default()
        {
        }
        public static function get_preview_as_options()
        {
        }
        public function get_name()
        {
        }
        public function filter_admin_row_actions($actions)
        {
        }
        public function add_built_with_elementor($actions)
        {
        }
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        public static function get_create_url()
        {
        }
        public function save($data)
        {
        }
        protected function register_controls()
        {
        }
        public function admin_columns_content($column_name)
        {
        }
        protected function get_remote_library_config()
        {
        }
    }
}
namespace ElementorPro\Modules\FloatingButtons\Base {
    abstract class Widget_Contact_Button_Base_Pro extends \Elementor\Modules\FloatingButtons\Base\Widget_Contact_Button_Base
    {
        public function has_widget_inner_wrapper() : bool
        {
        }
    }
    abstract class Widget_Floating_Bars_Base_Pro extends \Elementor\Modules\FloatingButtons\Base\Widget_Floating_Bars_Base
    {
        public function has_widget_inner_wrapper() : bool
        {
        }
    }
}
namespace ElementorPro\Modules\FloatingButtons\Widgets {
    class Contact_Buttons_Var_10 extends \ElementorPro\Modules\FloatingButtons\Base\Widget_Contact_Button_Base_Pro
    {
        public static function get_configuration()
        {
        }
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
        protected function add_content_tab() : void
        {
        }
        protected function add_style_tab() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Contact_Buttons_Var_5 extends \ElementorPro\Modules\FloatingButtons\Base\Widget_Contact_Button_Base_Pro
    {
        public static function get_configuration()
        {
        }
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
        protected function add_content_tab() : void
        {
        }
        protected function add_style_tab() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Contact_Buttons_Var_4 extends \ElementorPro\Modules\FloatingButtons\Base\Widget_Contact_Button_Base_Pro
    {
        public static function get_configuration()
        {
        }
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
        protected function add_content_tab() : void
        {
        }
        protected function add_style_tab() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Contact_Buttons_Var_6 extends \ElementorPro\Modules\FloatingButtons\Base\Widget_Contact_Button_Base_Pro
    {
        public static function get_configuration()
        {
        }
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
        protected function add_content_tab() : void
        {
        }
        protected function add_style_tab() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Floating_Bars_Var_3 extends \ElementorPro\Modules\FloatingButtons\Base\Widget_Floating_Bars_Base_Pro
    {
        public static function get_configuration()
        {
        }
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
        public function add_coupon_content_section() : void
        {
        }
        public function add_coupon_style_section() : void
        {
        }
        protected function add_content_tab() : void
        {
        }
        protected function add_style_tab() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Floating_Bars_Var_2 extends \ElementorPro\Modules\FloatingButtons\Base\Widget_Floating_Bars_Base_Pro
    {
        public static function get_configuration()
        {
        }
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
        protected function add_floating_bar_style_section() : void
        {
        }
        protected function add_accessible_name_control() : void
        {
        }
        protected function add_ticker_content_section() : void
        {
        }
        protected function add_content_tab() : void
        {
        }
        protected function add_style_tab() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Contact_Buttons_Var_7 extends \ElementorPro\Modules\FloatingButtons\Base\Widget_Contact_Button_Base_Pro
    {
        public static function get_configuration()
        {
        }
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
        protected function add_content_tab() : void
        {
        }
        protected function add_style_tab() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Contact_Buttons_Var_3 extends \ElementorPro\Modules\FloatingButtons\Base\Widget_Contact_Button_Base_Pro
    {
        public static function get_configuration()
        {
        }
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
        protected function add_content_tab() : void
        {
        }
        protected function add_style_tab() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Contact_Buttons_Var_1 extends \ElementorPro\Modules\FloatingButtons\Base\Widget_Contact_Button_Base_Pro
    {
        public static function get_configuration()
        {
        }
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
        protected function add_content_tab() : void
        {
        }
        protected function add_style_tab() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Contact_Buttons_Var_9 extends \ElementorPro\Modules\FloatingButtons\Base\Widget_Contact_Button_Base_Pro
    {
        public static function get_configuration()
        {
        }
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
        protected function add_content_tab() : void
        {
        }
        protected function add_style_tab() : void
        {
        }
        public function render() : void
        {
        }
    }
    class Contact_Buttons_Var_8 extends \ElementorPro\Modules\FloatingButtons\Base\Widget_Contact_Button_Base_Pro
    {
        public static function get_configuration()
        {
        }
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
        protected function add_content_tab() : void
        {
        }
        protected function add_style_tab() : void
        {
        }
        public function render() : void
        {
        }
    }
}
namespace ElementorPro\Modules\AssetsManager\Classes {
    abstract class Assets_Base
    {
        public abstract function get_name();
        public abstract function get_type();
        protected function actions()
        {
        }
        public function print_metabox($fields)
        {
        }
        public function get_metabox_field_html($field, $saved)
        {
        }
        public function get_field_label($field)
        {
        }
        public function get_input_field($attributes, $saved = '')
        {
        }
        public function get_attribute_string($attributes, $field = [])
        {
        }
        public function get_select_field($field, $selected = '')
        {
        }
        public function get_textarea_field($field, $html)
        {
        }
        public function get_file_field($field, $saved)
        {
        }
        public function get_html_field($field)
        {
        }
        public function get_dropzone_field($field)
        {
        }
        public function get_repeater_field($field, $saved)
        {
        }
        public function get_checkbox_field($field, $saved)
        {
        }
        public function get_field_row($field, $field_html)
        {
        }
        public function sanitize_text_field_recursive($data)
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\CustomCode {
    class Custom_Code_Metabox extends \ElementorPro\Modules\AssetsManager\Classes\Assets_Base
    {
        const FIELD_LOCATION = 'location';
        const FIELD_PRIORITY = 'priority';
        const FILED_EXTRA_OPTIONS = 'extra_options';
        const FIELD_CODE = 'code';
        const OPTION_LOCATION_HEAD = 'elementor_head';
        const OPTION_LOCATION_BODY_START = 'elementor_body_start';
        const OPTION_LOCATION_BODY_END = 'elementor_body_end';
        const OPTION_PRIORITY_LENGTH = 10;
        const INPUT_OPTION_ENSURE_JQUERY = 'ensure_jquery';
        const INPUT_FIELDS = [self::FIELD_LOCATION, self::FIELD_PRIORITY, self::FIELD_CODE, self::FILED_EXTRA_OPTIONS];
        const INPUT_OPTIONS = [self::INPUT_OPTION_ENSURE_JQUERY];
        public function get_name()
        {
        }
        public function get_type()
        {
        }
        public function get_field_label($field)
        {
        }
        public function get_location_labels()
        {
        }
        public function get_location_options()
        {
        }
        public function get_priority_options()
        {
        }
        /**
         * Add script integrity.
         *
         * This is method is public, since its has to remove its own filter.
         *
         * @param string $html
         * @param mixed $handle
         *
         * @return string
         */
        public function add_script_integrity($html, $handle)
        {
        }
        protected function actions()
        {
        }
    }
    class Document extends \ElementorPro\Modules\ThemeBuilder\Documents\Theme_Document
    {
        public static function get_properties()
        {
        }
        public static function get_title()
        {
        }
        public static function get_type()
        {
        }
        public function get_name()
        {
        }
        public function print_content()
        {
        }
        public static function get_create_url()
        {
        }
    }
    class Module extends \ElementorPro\Base\Module_Base
    {
        const CAPABILITY = 'manage_options';
        const CPT = 'elementor_snippet';
        const MODULE_NAME = 'custom_code';
        const DOCUMENT_TYPE = 'code_snippet';
        const ADDITIONAL_COLUMN_INSTANCES = 'instances';
        const MENU_SLUG = 'edit.php?post_type=' . self::CPT;
        const PROMOTION_MENU_SLUG = 'e-custom-code';
        /**
         * @var \ElementorPro\Modules\CustomCode\Custom_Code_Metabox
         */
        public $meta_box;
        public function __construct()
        {
        }
        public function get_name()
        {
        }
    }
}
namespace ElementorPro\Modules\CustomCode\AdminMenuItems {
    class Custom_Code_Promotion_Menu_Item extends \ElementorPro\Modules\Tiers\AdminMenuItems\Base_Promotion_Template
    {
        public function get_name() : string
        {
        }
        public function get_cta_url() : string
        {
        }
        public function get_cta_text()
        {
        }
        public function get_label()
        {
        }
        public function get_page_title()
        {
        }
        public function get_promotion_title() : string
        {
        }
        public function get_video_url() : string
        {
        }
        public function get_promotion_description()
        {
        }
        public function get_side_note() : string
        {
        }
        /**
         * @deprecated use get_promotion_description instead
         * @return void
         */
        public function render_promotion_description()
        {
        }
        protected function get_content_lines() : array
        {
        }
    }
    class Custom_Code_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item
    {
        const LICENSE_FEATURE_NAME = 'custom_code';
        public function get_capability()
        {
        }
        public function get_label()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_position()
        {
        }
        public function is_visible()
        {
        }
    }
}
namespace ElementorPro\Modules\LoopFilter {
    class Module extends \ElementorPro\Base\Module_Base
    {
        use \ElementorPro\Modules\LoopFilter\Traits\Hierarchical_Taxonomy_Trait;
        protected function get_widgets()
        {
        }
        public function get_name()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/loop-filter/assets/scss/frontend.scss`
         * to `/assets/css/widget-loop-filter.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
        public function get_post_type_taxonomies($data)
        {
        }
        public function register_widget_filter($widget_id, $filter_data)
        {
        }
        public function filter_loop_query($query_args, $widget)
        {
        }
        /**
         * @description Check if the filter is empty.
         * Taxonomy Filter URL parameter is empty but not removed i.e. `&e-filter-389c132-product_cat=`.
         * This edge case happens if a user clears terms and not the Taxonomy filter parameter
         * @param $filter
         * @return bool
         */
        public function is_filter_empty($filter)
        {
        }
        public function add_localize_data($config)
        {
        }
        /**
         * @return array
         */
        public function get_query_string_filters()
        {
        }
        public function remove_rest_route_parameter($link)
        {
        }
        /**
         * @return boolean
         */
        public function is_term_not_selected_for_inclusion($loop_widget_settings, $term, $skin)
        {
        }
        public function is_loop_grid_include_exclude_tax_belong_to_filter_tax(array $loop_widget_settings, \WP_Term $term, string $skin) : bool
        {
        }
        /**
         * @return boolean
         */
        public function is_term_selected_for_exclusion($loop_widget_settings, $term, $skin)
        {
        }
        /**
         * @return boolean
         */
        public function should_exclude_term_by_manual_selection($loop_widget_settings, $term, $user_selected_taxonomy, $skin)
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\LoopFilter\Query {
    class Taxonomy_Manager
    {
        /**
         * @param string $taxonomy default 'category'. Use taxonomy string i.e. 'product_cat'. This generates the terms_by_slug and terms_by_id arrays.
         * @return void
         */
        public function get_taxonomy_terms($taxonomy = 'category')
        {
        }
        /**
         * Check if a term is a parent term.
         * @param string $slug
         * @param string $taxonomy
         * @return bool;
         */
        public function is_parent_term_without_children($slug, $taxonomy)
        {
        }
        public function is_parent_term_with_children($slug, $taxonomy)
        {
        }
        public function is_top_level_parent_term($slug, $taxonomy)
        {
        }
        /**
         * @param array $filter_terms
         * @param string $taxonomy
         * @return array
         */
        public function get_hierarchy_of_selected_terms($filter_terms, $taxonomy)
        {
        }
        /**
         * @param string $slug
         * @param string $term
         * @return void
         */
        public function try_set_terms_by_slug($slug, $term)
        {
        }
        /**
         * @param string $slug
         * @param string $term
         * @return void
         */
        public function try_set_terms_by_id($slug, $term)
        {
        }
    }
}
namespace ElementorPro\Modules\LoopFilter\Query\Interfaces {
    interface Query_Interface
    {
        public function __construct($filter_terms, \ElementorPro\Modules\LoopFilter\Query\Taxonomy_Manager $taxonomy_manager);
        public function get_query();
    }
}
namespace ElementorPro\Modules\LoopFilter\Query\QueryTypes {
    class Hierarchy_Or_Query implements \ElementorPro\Modules\LoopFilter\Query\Interfaces\Query_Interface
    {
        public function __construct($filter_terms, $taxonomy_manager)
        {
        }
        /**
         * @return array
         */
        public function get_query()
        {
        }
    }
    class Single_Terms_Query implements \ElementorPro\Modules\LoopFilter\Query\Interfaces\Query_Interface
    {
        public function __construct($filter_terms, $taxonomy_manager)
        {
        }
        /**
         * Create the Inner query for AND OR queries with one or more filter terms targeted at the same Widget using terms with no parent and no children
         * @return array
         */
        public function get_query()
        {
        }
    }
    class Hierarchy_And_Query implements \ElementorPro\Modules\LoopFilter\Query\Interfaces\Query_Interface
    {
        public function __construct($filter_terms, $taxonomy_manager)
        {
        }
        /**
         * @return array
         */
        public function get_query()
        {
        }
    }
}
namespace ElementorPro\Modules\LoopFilter\Query {
    class Taxonomy_Query_Builder
    {
        public function __construct()
        {
        }
        public function get_merged_queries(&$tax_query, $taxonomy, $filter)
        {
        }
    }
}
namespace ElementorPro\Modules\LoopFilter\Query\Data {
    class Query_Constants
    {
        public const DATA = ['AND' => ['separator' => ['decoded' => '+', 'from-browser' => ' ', 'encoded' => '%2B'], 'operator' => 'AND', 'relation' => 'AND'], 'OR' => ['separator' => ['decoded' => '~', 'from-browser' => '~', 'encoded' => '%7C'], 'operator' => 'IN', 'relation' => 'OR'], 'NOT' => ['separator' => ['decoded' => '!', 'from-browser' => '!', 'encoded' => '%21'], 'operator' => 'NOT IN', 'relation' => 'AND'], 'DISABLED' => ['separator' => ['decoded' => '', 'from-browser' => '', 'encoded' => ''], 'operator' => 'AND', 'relation' => 'AND']];
    }
}
namespace ElementorPro\Modules\LoopFilter\Data {
    class Controller extends \ElementorPro\Core\Data\Controller
    {
        public function get_name()
        {
        }
        protected function register_endpoints()
        {
        }
    }
}
namespace ElementorPro\Modules\LoopFilter\Data\Endpoints {
    class Refresh_Loop extends \ElementorPro\Core\Data\Endpoints\Refresh_Base
    {
        public function get_name() : string
        {
        }
        public function get_route() : string
        {
        }
        public function get_updated_loop_widget_markup(\WP_REST_Request $request) : array
        {
        }
        protected function register()
        {
        }
    }
    // Create a class that extends the Base Endpoint class.
    // This class should handle fetching taxonomies from the database, it registers an endpoint that can be accessed via the REST API.
    // The endpoint accepts a string argument of 'post_type' and returns an array of taxonomies for that post type.
    class Get_Post_Type_Taxonomies extends \ElementorPro\Core\Data\Endpoints\Refresh_Base
    {
        use \ElementorPro\Modules\LoopFilter\Traits\Taxonomy_Filter_Trait;
        public function get_name() : string
        {
        }
        public function get_route() : string
        {
        }
        protected function permission_callback($request, $widget_name = '') : bool
        {
        }
        public function get_items(\WP_REST_Request $request) : array
        {
        }
        protected function register()
        {
        }
    }
}
namespace ElementorPro\Modules\LoopFilter\Widgets {
    class Taxonomy_Filter extends \ElementorPro\Base\Base_Widget
    {
        use \ElementorPro\Modules\LoopFilter\Traits\Hierarchical_Taxonomy_Trait;
        use \ElementorPro\Modules\LoopFilter\Traits\Taxonomy_Filter_Trait;
        use \ElementorPro\Modules\Posts\Traits\Pagination_Trait;
        public function get_name()
        {
        }
        public function get_group_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function register_controls()
        {
        }
        protected function register_design_layout_controls()
        {
        }
        protected function get_empty_widget_message_by_key($message_key)
        {
        }
        protected function print_empty_results_if_editor($message_key)
        {
        }
        public function render()
        {
        }
    }
}
namespace ElementorPro\Modules\CustomAttributes {
    class Module extends \ElementorPro\Base\Module_Base
    {
        const LICENSE_FEATURE_NAME = 'custom-attributes';
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        /**
         * @param Element_Base $element
         */
        public function replace_go_pro_custom_attributes_controls(\Elementor\Element_Base $element)
        {
        }
        public function register_custom_attributes_controls(\Elementor\Element_Base $element, $tab)
        {
        }
        /**
         * @param $element    Controls_Stack
         * @param $section_id string
         */
        public function register_controls(\Elementor\Controls_Stack $element, $section_id)
        {
        }
        /**
         * @param $element Element_Base
         */
        public function render_attributes(\Elementor\Element_Base $element)
        {
        }
        protected function add_actions()
        {
        }
    }
}
namespace ElementorPro\Modules\LoopBuilder\Providers {
    class Taxonomy_Loop_Provider
    {
        // QUERY TABS
        const TABS_WRAPPER = 'query_args';
        const INCLUDE_TAB = 'query_include';
        const EXCLUDE_TAB = 'query_exclude';
        // QUERY CONTROL KEYS
        const QUERY_CONTROL_GROUP_NAME = \ElementorPro\Modules\LoopBuilder\Module::QUERY_ID;
        const POST_TYPE = 'post_type';
        const FILTER_BY = 'filter_by';
        const PARENT = 'child_of';
        const INCLUDE = 'posts_ids';
        const EXCLUDE = 'exclude_ids';
        const AVOID_DUPLICATES = 'avoid_duplicates';
        const OFFSET = 'offset';
        const ORDER_BY = 'orderby';
        const ORDER = 'order';
        const HIDE_EMPTY = 'hide_empty';
        const HIERARCHICAL = 'hierarchical';
        const QUERY_DEPTH = 'child_taxonomy_depth';
        const QUERY_ID = 'term_taxonomy_id';
        // DEFAULT TAXONOMIES
        const POST_CATEGORY_TAXONOMY = 'category';
        const POST_TAG_TAXONOMY = 'post_tag';
        const PRODUCT_CATEGORY_TAXONOMY = 'product_cat';
        const PRODUCT_TAG_TAXONOMY = 'product_tag';
        // FILTER_BY OPTION KEYs
        const MANUAL_SELECTION = 'manual_selection';
        const SHOW_ALL = 'show_all';
        const BY_PARENT = 'by_parent';
        const CURRENT_QUERY = 'current_query';
        const CURRENT_SUBCATEGORIES = 'current_subcategories';
        // ORDER_BY OPTION KEYS
        const ORDER_BY_NAME = 'name';
        const ORDER_BY_ID = 'term_id';
        // ORDER OPTION KEYS
        const ASC_ORDER = 'ASC';
        const DESC_ORDER = 'DESC';
        public function __construct($skin_id = \ElementorPro\Modules\LoopBuilder\Module::LOOP_POST_TAXONOMY_SKIN_ID, $default_source_type = self::POST_CATEGORY_TAXONOMY)
        {
        }
        public function get_query_settings(array $display_settings) : array
        {
        }
        public function get_control_args(string $key, bool $is_prefixed) : array
        {
        }
        /**
         * Get settings key names.
         *
         * Adds prefix to the desired key.
         */
        public function get_property_name(string $key) : string
        {
        }
        /**
         * Get query settings key names.
         *
         * Adds prefix and '_query_' to the desired key.
         */
        public function get_query_property_name(string $key) : string
        {
        }
        public static function is_source_type_taxonomy($source_type)
        {
        }
        public static function get_loop_taxonomy_types()
        {
        }
        public static function get_default_source_type($taxonomy_loop_type, $prefix = '') : string
        {
        }
        public static function get_supported_cpts($taxonomy_loop_type)
        {
        }
        public static function is_loop_taxonomy() : bool
        {
        }
        public static function is_loop_taxonomy_strict() : bool
        {
        }
    }
}
namespace ElementorPro\Modules\LoopBuilder\Skins {
    class Skin_Loop_Post_Taxonomy extends \ElementorPro\Modules\LoopBuilder\Skins\Skin_Loop_Taxonomy_Base
    {
        protected $post_type = 'post';
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        protected function render_before_loop($template_id)
        {
        }
        protected function get_default_source_option()
        {
        }
    }
    class Skin_Loop_Post extends \ElementorPro\Modules\LoopBuilder\Skins\Skin_Loop_Base
    {
        public function get_id()
        {
        }
        public function get_title()
        {
        }
    }
}
namespace ElementorPro\Modules\LoopBuilder {
    class Module extends \ElementorPro\Base\Module_Base
    {
        /**
         * Elementor template-library taxonomy slug.
         */
        const TEMPLATE_LIBRARY_TYPE_SLUG = 'loop-item';
        const LOOP_BASE_SKIN_ID = 'base';
        const LOOP_POST_SKIN_ID = 'post';
        const LOOP_POST_TAXONOMY_SKIN_ID = 'post_taxonomy';
        const QUERY_ID = 'query';
        const LOOP_WIDGETS = ['loop-grid', 'loop-carousel'];
        const TAXONOMY_LOOP_EXPERIMENT_NAME = 'taxonomy_loop_addition';
        public static $taxonomies_displayed_ids = [];
        public static function add_to_taxonomies_avoid_list($ids)
        {
        }
        public static function get_taxonomies_avoid_list_ids()
        {
        }
        public function get_name()
        {
        }
        protected function get_widgets()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/loop-builder/assets/scss/widgets/*.scss`
         * to `/assets/css/widget-*.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
        public function __construct()
        {
        }
        public function filter_template_to_canvas_view()
        {
        }
        public function filter_body_class($classes)
        {
        }
        /**
         * Filter content data.
         *
         * Determine whether we are in the Editor and are trying to Edit an empty loop template.
         *
         * If this is the case, we add some elements to the $data array in order for frontend.php
         * to not 'return' an empty string and reach the print_elements_with_wrapper() function.
         *
         * We then override print_elements_with_wrapper() in the loop document using the variables
         * we added here.
         *
         * @since 3.8.0
         *
         * @param array $data
         * @param int $post_id
         *
         * @return mixed
         */
        public function filter_content_data($data, $post_id)
        {
        }
        public function add_finder_items(array $categories)
        {
        }
        public function add_posts_type_to_template_popup($form)
        {
        }
        public function get_source_type_from_post_meta($post_id)
        {
        }
        public function add_taxonomies_type_to_template_popup($form)
        {
        }
        public function add_taxonomies_type_to_loop_settings_query($form)
        {
        }
        protected function add_taxonomies_to_options($form, $control_name)
        {
        }
    }
}
namespace ElementorPro\Modules\LoopBuilder\Files\Css {
    class Loop_Dynamic_CSS extends \Elementor\Core\DynamicTags\Dynamic_CSS
    {
        public function __construct($post_id, $post_id_for_data)
        {
        }
        public function get_post_id_for_data()
        {
        }
    }
    class Loop_Preview extends \Elementor\Core\Files\CSS\Post_Preview
    {
        use \ElementorPro\Modules\LoopBuilder\Files\Css\Loop_Css_Trait;
        /**
         * Get CSS file name.
         *
         * Retrieve the CSS file name.
         *
         * @access public
         *
         * @return string CSS file name.
         */
        public function get_name()
        {
        }
    }
    class Loop extends \Elementor\Core\Files\CSS\Post
    {
        use \ElementorPro\Modules\LoopBuilder\Files\Css\Loop_Css_Trait;
        /**
         * Elementor Loop CSS file prefix.
         */
        const FILE_PREFIX = 'loop-';
        /**
         * Get CSS file name.
         *
         * Retrieve the CSS file name.
         *
         * @access public
         *
         * @return string CSS file name.
         */
        public function get_name()
        {
        }
        /**
         * Get file handle ID.
         *
         * Retrieve the handle ID for the post CSS file.
         *
         * @since 1.2.0
         * @access protected
         *
         * @return string CSS file handle ID.
         */
        protected function get_file_handle_id()
        {
        }
        /**
         * Loop CSS file constructor.
         *
         * Initializing the CSS file of the loop widget. Set the post ID and initiate the stylesheet.
         *
         * @since 1.2.0
         * @access public
         *
         * @param int $post_id Post ID.
         */
        public function __construct($loop_template_id)
        {
        }
    }
}
namespace ElementorPro\Modules\LoopBuilder\Documents {
    class Loop extends \ElementorPro\Modules\ThemeBuilder\Documents\Theme_Document
    {
        use \ElementorPro\Modules\LoopFilter\Traits\Taxonomy_Filter_Trait;
        use \ElementorPro\Modules\DynamicTags\Tags\Base\Tag_Trait;
        const DOCUMENT_TYPE = 'loop-item';
        const SINGLE_PREFIX = 'single/';
        const RECOMMENDED_POSTS_WIDGET_NAMES = ['theme-post-title', 'theme-post-excerpt', 'theme-post-featured-image', 'theme-post-content', 'post-info'];
        const WIDGETS_TO_HIDE = ['loop-grid', 'woocommerce-product-data-tabs', 'loop-carousel'];
        const PREVIEW_TYPE = 'preview_type';
        const PREVIEW_ID = 'preview_id';
        public static function get_type()
        {
        }
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        protected static function get_site_editor_icon()
        {
        }
        public static function get_site_editor_tooltip_data()
        {
        }
        protected static function get_site_editor_thumbnail_url()
        {
        }
        public static function get_properties()
        {
        }
        public function save($data)
        {
        }
        public function get_container_attributes()
        {
        }
        public function get_initial_config()
        {
        }
        public static function get_site_editor_config()
        {
        }
        public function get_location_label()
        {
        }
        public function get_css_wrapper_selector()
        {
        }
        public static function get_preview_as_options()
        {
        }
        protected function get_remote_library_config()
        {
        }
        /**
         * Get Edit Url
         *
         * Disable the Library modal for non-container (section) users.
         *
         * @return string
         */
        public function get_edit_url()
        {
        }
        protected static function get_editor_panel_categories()
        {
        }
        protected function register_controls()
        {
        }
        /**
         * Get Wrapper Tags
         *
         * We remove the `content_wrapper_html_tag` control in this document and default to using a `div`.
         * The setting no longer exists when printing the document element, so we need to override this method so that
         * the extended document class defaults to using a `div` when printing the element.
         *
         * @since 3.8.0
         *
         * @return false
         */
        public function get_wrapper_tags()
        {
        }
        /**
         * Print elements with wrapper.
         *
         * Overwrite method from theme-document.php to render some custom markup if a variable
         * $elements_data['empty_loop_template'] is set. This variable is set via a filter hook
         * 'elementor/frontend/builder_content_data' in the loop builder module.
         *
         * @since 3.8.0
         *
         * @param $elements_data
         *
         * @return void
         */
        public function print_elements_with_wrapper($elements_data = null)
        {
        }
        /**
         * Get content.
         *
         * Override the parent method to retrieve the content with CSS in the Editor.
         *
         * @since 3.8.0
         */
        public function get_content($with_css = false)
        {
        }
        /**
         * Runs on the 'elementor/frontend/builder_content/before_print_css' hook.
         *
         * @return false
         */
        public function prevent_inline_css_printing()
        {
        }
        /**
         * Print empty loop template markup.
         *
         * This function is used to render markup in the editor when a loop template is empty/blank.
         * Currently, nothing will be rendered in the editor if the template is empty.
         * This markup is needed in the DOM for us to be able to switch to this document in place.
         *
         * @since 3.8.0
         *
         * @param int $post_id The post ID of the document.
         *
         * @return void
         */
        protected function print_empty_loop_template_markup($post_id)
        {
        }
        /**
         * @return void
         */
        protected function add_query_section()
        {
        }
        /**
         * @return void
         */
        protected function inject_width_control()
        {
        }
        /**
         * @return void
         */
        protected function update_preview_control()
        {
        }
    }
}
namespace ElementorPro\Modules\LoopBuilder\Widgets {
    class Base extends \ElementorPro\Modules\Posts\Widgets\Posts
    {
        public function get_group_name()
        {
        }
        /**
         * Get Query Name
         *
         * Returns the query control name used in the widget's main query.
         *
         * @since 3.8.0
         *
         * @return string
         */
        public function get_query_name()
        {
        }
        protected function get_initial_config()
        {
        }
        public function query_posts()
        {
        }
        /**
         * Get Posts Per Page Value
         *
         * Returns the value of the Posts Per Page control of the widget.
         *
         * @since 3.8.0
         * @access protected
         *
         * @return mixed
         */
        public function get_posts_per_page_value()
        {
        }
        protected function register_skins()
        {
        }
        protected function register_controls()
        {
        }
        /**
         * Register Layout Section
         *
         * This registers the Layout section in order to allow Skins to register their layout controls.
         *
         * @since 3.8.0
         */
        protected function register_layout_section()
        {
        }
        /**
         * Register Query Section
         *
         * This registers the Query section in order to allow Skins to register their query controls.
         *
         * @since 3.8.0
         */
        protected function register_query_section()
        {
        }
        public function register_pagination_section_controls()
        {
        }
        protected function register_additional_options_section_controls()
        {
        }
        protected function register_design_layout_controls()
        {
        }
        protected function register_design_nothing_found_message_controls()
        {
        }
        protected function register_design_navigation_controls()
        {
        }
        protected function register_design_pagination_controls()
        {
        }
        public function register_settings_section_controls()
        {
        }
        public function register_navigation_section_controls()
        {
        }
        public function get_loop_header_widget_classes() : array
        {
        }
        public function render_loop_header()
        {
        }
        public function render_loop_footer()
        {
        }
        public function before_skin_render()
        {
        }
        public function after_skin_render()
        {
        }
    }
    class Loop_Grid extends \ElementorPro\Modules\LoopBuilder\Widgets\Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_keywords()
        {
        }
        public function get_icon()
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function register_layout_section()
        {
        }
        protected function register_additional_options_section_controls()
        {
        }
        protected function register_design_layout_controls()
        {
        }
        protected function register_design_nothing_found_message_controls()
        {
        }
        public static function on_import_update_dynamic_content(array $element_config, array $data, $controls = null) : array
        {
        }
    }
    class Loop_Carousel extends \ElementorPro\Modules\LoopBuilder\Widgets\Base
    {
        use \ElementorPro\Base\Base_Carousel_Trait;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_keywords()
        {
        }
        public function get_icon()
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function get_initial_config()
        {
        }
        public function register_settings_section_controls()
        {
        }
        public function register_navigation_section_controls()
        {
        }
        public function register_pagination_section_controls()
        {
        }
        public function register_design_layout_controls()
        {
        }
        protected function register_design_navigation_controls()
        {
        }
        public function register_design_pagination_controls()
        {
        }
        public function render_loop_header()
        {
        }
        public function render_loop_footer()
        {
        }
        public function add_swiper_slide_attributes_to_loop_item($attributes, $document)
        {
        }
        public function add_loop_header_attributes($render_attributes)
        {
        }
        public function get_loop_header_widget_classes() : array
        {
        }
        public function before_skin_render()
        {
        }
        public function after_skin_render()
        {
        }
        protected function register_layout_section()
        {
        }
    }
}
namespace ElementorPro\Modules\Hotspot {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        public function get_widgets()
        {
        }
        public function get_name()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/hotspot/assets/scss/frontend.scss`
         * to `/assets/css/widget-hotspot.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\Hotspot\Widgets {
    class Hotspot extends \Elementor\Widget_Image
    {
        use \ElementorPro\Base\Base_Widget_Trait;
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        /**
         * Render Hotspot widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since  2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
}
namespace ElementorPro\Modules\ProgressTracker {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        public function get_widgets()
        {
        }
        public function get_name()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/progress-tracker/assets/scss/frontend.scss`
         * to `/assets/css/widget-progress-tracker.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\ProgressTracker\Widgets {
    class ProgressTracker extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        public function render_plain_content()
        {
        }
        protected function render()
        {
        }
    }
}
namespace ElementorPro\Modules\DynamicTags\Toolset\Tags {
    abstract class Toolset_Base extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        protected function register_controls()
        {
        }
        protected function get_supported_fields()
        {
        }
    }
    class Toolset_Text extends \ElementorPro\Modules\DynamicTags\Toolset\Tags\Toolset_Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function render()
        {
        }
        protected function get_supported_fields()
        {
        }
    }
    class Toolset_Image extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        public function get_value(array $options = [])
        {
        }
        protected function register_controls()
        {
        }
        protected function get_supported_fields()
        {
        }
    }
    class Toolset_Gallery extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_categories()
        {
        }
        public function get_group()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        public function get_value(array $options = [])
        {
        }
        protected function register_controls()
        {
        }
        protected function get_supported_fields()
        {
        }
        /**
         * Toolset_Gallery constructor.
         *
         * @param array $data
         */
        public function __construct(array $data = [])
        {
        }
    }
    class Toolset_Date extends \ElementorPro\Modules\DynamicTags\Toolset\Tags\Toolset_Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function render()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        protected function register_controls()
        {
        }
        protected function get_supported_fields()
        {
        }
    }
    class Toolset_URL extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        public function get_value(array $options = [])
        {
        }
        protected function register_controls()
        {
        }
        protected function get_supported_fields()
        {
        }
    }
}
namespace ElementorPro\Modules\DynamicTags\Toolset {
    class Module extends \Elementor\Modules\DynamicTags\Module
    {
        const TOOLSET_GROUP = 'Toolset';
        /**
         * @param array $types
         *
         * @return array
         */
        public static function get_control_options($types)
        {
        }
        public static function toolset_image_mapping($field, $single = true)
        {
        }
        public static function valid_field_type($types, $field)
        {
        }
        public function get_tag_classes_names()
        {
        }
        public function get_groups()
        {
        }
    }
}
namespace ElementorPro\Modules\DynamicTags\ACF {
    class Dynamic_Value_Provider
    {
        public function get_value($key)
        {
        }
        /**
         * Retrieve the custom field value from `ACF` plugin.
         * Used for testing.
         *
         * @param $selector
         * @param $post_id
         *
         * @return array|false
         */
        protected function get_field_object($selector, $post_id)
        {
        }
    }
}
namespace ElementorPro\Modules\DynamicTags\ACF\Tags {
    class ACF_Gallery extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_categories()
        {
        }
        public function get_group()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        public function get_value(array $options = [])
        {
        }
        protected function register_controls()
        {
        }
        public function get_supported_fields()
        {
        }
    }
    class ACF_Image extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        public function get_value(array $options = [])
        {
        }
        protected function register_controls()
        {
        }
        public function get_supported_fields()
        {
        }
    }
    class ACF_File extends \ElementorPro\Modules\DynamicTags\ACF\Tags\ACF_Image
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_categories()
        {
        }
        public function get_supported_fields()
        {
        }
    }
    class ACF_Number extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function render()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        protected function register_controls()
        {
        }
        public function get_supported_fields()
        {
        }
    }
    class ACF_Date_Time extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        /**
         * @param array $options
         *
         * @return string - date time in format Y-m-d H:i:s
         */
        public function get_value(array $options = [])
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        protected function register_controls()
        {
        }
        public function get_supported_fields()
        {
        }
        public function __construct(array $data = [], $dynamic_value_provider = null)
        {
        }
    }
    class ACF_COLOR extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        public function get_value(array $options = [])
        {
        }
        protected function register_controls()
        {
        }
        public function get_supported_fields()
        {
        }
    }
    class ACF_URL extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        public function get_value(array $options = [])
        {
        }
        protected function register_controls()
        {
        }
        public function get_supported_fields()
        {
        }
    }
    class ACF_Text extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function render()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        protected function register_controls()
        {
        }
        public function get_supported_fields()
        {
        }
    }
}
namespace ElementorPro\Modules\DynamicTags\ACF {
    class Module extends \Elementor\Modules\DynamicTags\Module
    {
        const ACF_GROUP = 'acf';
        public function __construct()
        {
        }
        /**
         * ACF meta values are not copying to post revisions. This fix is for replacing revision post_id to actual one
         *
         * @param $null
         * @param $post_id
         * @return mixed
         */
        public function filter_post_in_preview($null, $post_id)
        {
        }
        /**
         * @param array $types
         *
         * @return array
         */
        public static function get_control_options($types)
        {
        }
        public static function add_key_control(\Elementor\Core\DynamicTags\Base_Tag $tag)
        {
        }
        public function get_tag_classes_names()
        {
        }
        // For use by ACF tags
        public static function get_tag_value_field(\Elementor\Core\DynamicTags\Base_Tag $tag)
        {
        }
        public function get_groups()
        {
        }
    }
}
namespace ElementorPro\Modules\DynamicTags\Tags {
    class Page_Title extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function render()
        {
        }
        protected function register_controls()
        {
        }
    }
}
namespace ElementorPro\Modules\DynamicTags\Tags\Base {
    abstract class Author_Tag extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_categories()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        public function get_group()
        {
        }
        public function render()
        {
        }
    }
}
namespace ElementorPro\Modules\DynamicTags\Tags {
    class Author_Info extends \ElementorPro\Modules\DynamicTags\Tags\Base\Author_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_editor_config()
        {
        }
        protected function register_controls()
        {
        }
    }
    class Archive_Description extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function render()
        {
        }
    }
    class Site_Tagline extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function render()
        {
        }
    }
    class Request_Parameter extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function render()
        {
        }
        protected function register_controls()
        {
        }
    }
    class Comments_Number extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
    }
    class Post_Title extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function render()
        {
        }
    }
    class Shortcode extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
    }
    class User_Info extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function render()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        protected function register_controls()
        {
        }
    }
    class Post_Terms extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
    }
    class Post_URL extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_value(array $options = [])
        {
        }
    }
    class Reload_Page extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        protected function register_advanced_section()
        {
        }
        public function register_controls()
        {
        }
        public function render()
        {
        }
    }
    class Post_Featured_Image extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_title()
        {
        }
        public function get_value(array $options = [])
        {
        }
        protected function register_controls()
        {
        }
    }
    class Site_Logo extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_value(array $options = [])
        {
        }
    }
    class Site_URL extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_value(array $options = [])
        {
        }
    }
    class Post_Date extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
    }
    class Author_Meta extends \ElementorPro\Modules\DynamicTags\Tags\Base\Author_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        protected function register_controls()
        {
        }
    }
    class Contact_URL extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        protected function register_controls()
        {
        }
        protected function register_advanced_section()
        {
        }
        public function build_viber_link($settings)
        {
        }
        public function render()
        {
        }
    }
    class Internal_URL extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_title()
        {
        }
        public function get_panel_template()
        {
        }
        /**
         * @since 3.6.0
         * @deprecated 3.8.0 Use `On_Import_Trait::on_import_update_dynamic_content()` instead.
         *
         * Remove in the future.
         */
        public static function on_import_replace_dynamic_content($config, $map_old_new_post_ids)
        {
        }
        public function get_value(array $options = [])
        {
        }
        protected function register_controls()
        {
        }
    }
    class Site_Title extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function render()
        {
        }
    }
    class Current_Date_Time extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
    }
    class Archive_Meta extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_panel_template()
        {
        }
        public function render()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        protected function register_controls()
        {
        }
    }
    class Post_Custom_Field extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        public function is_settings_required()
        {
        }
        protected function register_controls()
        {
        }
        public function get_custom_field_value(string $key) : string
        {
        }
        public function render()
        {
        }
    }
    class Archive_URL extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_title()
        {
        }
        public function get_panel_template()
        {
        }
        public function get_value(array $options = [])
        {
        }
    }
    class Author_URL extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_title()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        public function get_value(array $options = [])
        {
        }
        protected function register_controls()
        {
        }
    }
    class Comments_URL extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_value(array $options = [])
        {
        }
    }
    class Author_Profile_Picture extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_value(array $options = [])
        {
        }
    }
    class Featured_Image_Data extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_title()
        {
        }
        public function get_editor_config()
        {
        }
        public function render()
        {
        }
        protected function register_controls()
        {
        }
    }
    class Post_Gallery extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_categories()
        {
        }
        public function get_group()
        {
        }
        public function get_value(array $options = [])
        {
        }
    }
    class User_Profile_Picture extends \ElementorPro\Modules\DynamicTags\Tags\Author_Profile_Picture
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_value(array $options = [])
        {
        }
    }
    class Post_ID extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function render()
        {
        }
    }
    class Author_Name extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function render()
        {
        }
    }
    class Post_Time extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
    }
    class Lightbox extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        // Keep Empty to avoid default advanced section
        protected function register_advanced_section()
        {
        }
        public function register_controls()
        {
        }
        public function render()
        {
        }
    }
    class Archive_Title extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_editor_config()
        {
        }
        public function render()
        {
        }
        protected function register_controls()
        {
        }
    }
    class Post_Excerpt extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        protected function register_controls()
        {
        }
        public function get_categories()
        {
        }
        public function should_get_excerpt_from_post_content($settings)
        {
        }
        public function is_post_excerpt_valid($settings, $post)
        {
        }
        public function get_post_excerpt($settings, $post)
        {
        }
        public function render()
        {
        }
    }
}
namespace ElementorPro\Modules\DynamicTags\Components {
    class Author_Meta_Filter
    {
        const DYNAMIC_TAG_SHORTCODE_PATTERN = '/\\[elementor-tag.*?name=.*?"(author-meta|author-info).*?".*?settings=.*?".*?(user_email|email).*?".*?\\]/';
        public function filter($data, $document) : array
        {
        }
    }
}
namespace ElementorPro\Modules\DynamicTags {
    class Module extends \Elementor\Modules\DynamicTags\Module
    {
        const AUTHOR_GROUP = 'author';
        const POST_GROUP = 'post';
        const COMMENTS_GROUP = 'comments';
        const SITE_GROUP = 'site';
        const ARCHIVE_GROUP = 'archive';
        const MEDIA_GROUP = 'media';
        const ACTION_GROUP = 'action';
        const WOOCOMMERCE_GROUP = 'woocommerce';
        const LICENSE_FEATURE_ACF_NAME = 'dynamic-tags-acf';
        const LICENSE_FEATURE_PODS_NAME = 'dynamic-tags-pods';
        const LICENSE_FEATURE_TOOLSET_NAME = 'dynamic-tags-toolset';
        public function __construct()
        {
        }
        public function filter_woocommerce_add_to_cart_redirect($wc_get_cart_url)
        {
        }
        public function get_name()
        {
        }
        public function get_tag_classes_names()
        {
        }
        public function get_groups()
        {
        }
    }
}
namespace ElementorPro\Modules\DynamicTags\Pods {
    class Dynamic_Value_Provider
    {
        // Copied from the Module with some modifications & improvements.
        // TODO: Refactor the Tags to use this class instead of the Module.
        public function get_value($key)
        {
        }
        /**
         * Retrieve the Pod value from `Pods` plugin.
         * Used for testing.
         *
         * @param $type
         * @param $id
         *
         * @return bool|\Pods
         */
        protected function get_pods_value($type, $id)
        {
        }
    }
}
namespace ElementorPro\Modules\DynamicTags\Pods\Tags {
    class Pods_Image extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        public function get_value(array $options = [])
        {
        }
        protected function register_controls()
        {
        }
        protected function get_supported_fields()
        {
        }
    }
    class Pods_URL extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        public function get_value(array $options = [])
        {
        }
        protected function register_controls()
        {
        }
        protected function get_supported_fields()
        {
        }
    }
    abstract class Pods_Base extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_group()
        {
        }
        public function get_field()
        {
        }
        public function get_categories()
        {
        }
        protected function register_controls()
        {
        }
        protected function get_supported_fields()
        {
        }
    }
    class Pods_Numeric extends \ElementorPro\Modules\DynamicTags\Pods\Tags\Pods_Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_categories()
        {
        }
        public function render()
        {
        }
        protected function get_supported_fields()
        {
        }
    }
    class Pods_Date extends \ElementorPro\Modules\DynamicTags\Pods\Tags\Pods_Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function render()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        protected function register_controls()
        {
        }
        protected function get_supported_fields()
        {
        }
    }
    class Pods_Gallery extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_categories()
        {
        }
        public function get_group()
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        public function get_value(array $options = [])
        {
        }
        protected function register_controls()
        {
        }
        protected function get_supported_fields()
        {
        }
    }
    class Pods_Date_Time extends \ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        /**
         * @param array $options
         *
         * @return string - date time in format Y-m-d H:i:s
         */
        public function get_value(array $options = [])
        {
        }
        public function get_panel_template_setting_key()
        {
        }
        protected function register_controls()
        {
        }
        protected function get_supported_fields()
        {
        }
        public function __construct(array $data = [], $dynamic_value_provider = null)
        {
        }
    }
    class Pods_Text extends \ElementorPro\Modules\DynamicTags\Pods\Tags\Pods_Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function render()
        {
        }
        protected function get_supported_fields()
        {
        }
    }
}
namespace ElementorPro\Modules\DynamicTags\Pods {
    class Module extends \Elementor\Modules\DynamicTags\Module
    {
        const PODS_GROUP = 'Pods';
        /**
         * @param array $types
         *
         * @return array
         */
        public static function get_control_options($types)
        {
        }
        public static function valid_field_type($types, $field)
        {
        }
        public static function pods_file_mapping($field, $single = true)
        {
        }
        public static function pods_image_mapping($field, $single = true)
        {
        }
        public function get_tag_classes_names()
        {
        }
        public function get_groups()
        {
        }
    }
}
namespace ElementorPro\Modules\AnimatedHeadline {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        public function get_widgets()
        {
        }
        public function get_name()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/animated-headline/assets/scss/frontend.scss`
         * to `/assets/css/widget-animated-headline.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\AnimatedHeadline\Widgets {
    class Animated_Headline extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        /**
         * Render Animated Headline widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
}
namespace ElementorPro\Modules\Announcements\Triggers {
    class IsLicenseExpired extends \Elementor\Modules\Announcements\Classes\Trigger_Base
    {
        const META_KEY = '_elementor_pro_announcements_license_expired';
        const MUTED_PERIOD = 1;
        public function after_triggered()
        {
        }
        /**
         * @return bool
         */
        public function is_active() : bool
        {
        }
    }
}
namespace ElementorPro\Modules\Announcements {
    class Module extends \Elementor\Core\Base\App
    {
        /**
         * @return bool
         */
        public static function is_active() : bool
        {
        }
        /**
         * @return string
         */
        public function get_name() : string
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\Library\Classes {
    class Shortcode
    {
        const SHORTCODE = 'elementor-template';
        public function __construct()
        {
        }
        public function admin_columns_headers($defaults)
        {
        }
        public function admin_columns_content($column_name, $post_id)
        {
        }
        public function shortcode($attributes = [])
        {
        }
    }
}
namespace ElementorPro\Modules\Library\WP_Widgets {
    class Elementor_Library extends \WP_Widget
    {
        public function __construct()
        {
        }
        /**
         * @param array $args
         * @param array $instance
         */
        public function widget($args, $instance)
        {
        }
        /**
         * Avoid nesting a sidebar within a template that will appear in the sidebar itself
         *
         * @param array $data
         *
         * @return mixed
         */
        public function filter_content_data($data)
        {
        }
        /**
         * @param array $instance
         *
         * @return void
         */
        public function form($instance)
        {
        }
        /**
         *
         * @param array $new_instance
         * @param array $old_instance
         *
         * @return array
         */
        public function update($new_instance, $old_instance)
        {
        }
    }
}
namespace ElementorPro\Modules\Library {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function get_widgets()
        {
        }
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        public function register_wp_widgets()
        {
        }
        public function add_to_results_for_library_widget_templates($val, $post, $request)
        {
        }
        public function format_post_title_for_library_widget_templates($post_title, $post_id, $request)
        {
        }
        public function add_actions()
        {
        }
        /**
         * @deprecated 2.6.0 No longer used by internal code. See Autocomplete documentation in Query-Control Module.
         * @param array $results
         * @param array $data
         *
         * @return array
         */
        public function get_autocomplete_for_library_widget_templates(array $results, array $data)
        {
        }
        /**
         * @deprecated 2.6.0 No longer used by internal code. See Autocomplete documentation in Query-Control Module.
         * @param $results
         * @param $request
         *
         * @return mixed
         */
        public function get_value_title_for_library_widget_templates($results, $request)
        {
        }
        public function add_filters()
        {
        }
        public static function get_templates()
        {
        }
        public static function empty_templates_message()
        {
        }
    }
}
namespace ElementorPro\Modules\Library\Widgets {
    class Template extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function is_reload_preview_required()
        {
        }
        /**
         * @since 3.6.0
         *
         * @deprecated 3.8.0
         * On_Import_Trait::on_import_update_dynamic_content() should be used instead.
         * Remove in the future.
         */
        public static function on_import_replace_dynamic_content($config, $map_old_new_post_ids)
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
    }
}
namespace ElementorPro\Modules\Usage {
    class Integrations_Reporter extends \Elementor\Modules\System_Info\Reporters\Base
    {
        public function get_title()
        {
        }
        public function get_fields()
        {
        }
        public function get_integrations() : array
        {
        }
        public function get_raw_integrations() : array
        {
        }
    }
    class Features_Reporter extends \Elementor\Modules\System_Info\Reporters\Base
    {
        public function get_title()
        {
        }
        public function get_fields()
        {
        }
        public function get_custom_fonts() : array
        {
        }
        public function get_custom_icons() : array
        {
        }
    }
    /**
     * Elementor usage module.
     * @method static Module instance()
     */
    class Module extends \Elementor\Core\Base\Module
    {
        /**
         * Get module name.
         *
         * Retrieve the usage module name.
         *
         * @access public
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
        /**
         * Get integrations usage.
         *
         * Check all integrations in settings tab, find out who are in use.
         *
         * @return array
         */
        public function get_integrations_usage()
        {
        }
        /**
         * Get fonts usage.
         *
         * Retrieve the number of Elementor fonts variants saved.
         *
         * @access public
         * @static
         *
         * @return array The number of Elementor fonts variants.
         */
        public static function get_fonts_usage()
        {
        }
        /**
         * Get icons usage.
         *
         * Retrieve the number of Elementor icons saved.
         *
         * @access public
         * @static
         *
         * @return array The number of Elementor icons.
         */
        public static function get_icons_usage()
        {
        }
        /**
         * Add's tracking data.
         *
         * Called on elementor/tracker/send_tracking_data_params.
         *
         * @param array $params
         *
         * @return array
         */
        public function add_tracking_data($params)
        {
        }
        public function register_system_info_reporters()
        {
        }
        /**
         * Usage module constructor.
         *
         * Initializing Elementor usage module.
         *
         * @access public
         */
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\AssetsManager\Classes {
    class Font_Base extends \ElementorPro\Modules\AssetsManager\Classes\Assets_Base
    {
        const FONTS_OPTION_NAME = 'elementor_fonts_manager_fonts';
        protected $font_preview_phrase = '';
        protected function actions()
        {
        }
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        public function get_type()
        {
        }
        public function handle_panel_request(array $data)
        {
        }
        public function get_fonts($force = false)
        {
        }
        public function enqueue_font($font_family, $font_data, $post_css)
        {
        }
        public function get_font_family_type($post_id, $post_title)
        {
        }
        public function get_font_data($post_id, $post_title)
        {
        }
        public function render_preview_column($post_id)
        {
        }
        public function render_type_column($post_id)
        {
        }
        public function get_font_variations_count($post_id)
        {
        }
        public function save_meta($post_id, $data)
        {
        }
    }
}
namespace ElementorPro\Modules\AssetsManager {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function get_name()
        {
        }
        public function add_asset_manager($name, $instance)
        {
        }
        public function get_assets_manager($id = null)
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\AssetsManager\AssetTypes\Icons {
    class Custom_Icons extends \ElementorPro\Modules\AssetsManager\Classes\Assets_Base
    {
        const META_KEY = 'elementor_custom_icon_set_config';
        const OPTION_NAME = 'elementor_custom_icon_sets_config';
        public $current_post_id = 0;
        public function get_name()
        {
        }
        public function get_type()
        {
        }
        public function add_meta_box()
        {
        }
        public static function get_icon_set_config($id)
        {
        }
        public function render_metabox($post)
        {
        }
        public function save_post_meta($post_id, $post, $update)
        {
        }
        public static function get_supported_icon_sets()
        {
        }
        /**
         * get_wp_filesystem
         * @return \WP_Filesystem_Base
         */
        public static function get_wp_filesystem()
        {
        }
        public function custom_icons_upload_handler($data)
        {
        }
        public function handle_delete_icon_set($post_id)
        {
        }
        public static function clear_icon_list_option()
        {
        }
        public function display_post_states($post_states, $post)
        {
        }
        /**
         * Render preview column in font manager admin listing
         *
         * @param $column
         * @param $post_id
         */
        public function render_columns($column, $post_id)
        {
        }
        /**
         * Define which columns to display in font manager admin listing
         *
         * @param $columns
         *
         * @return array
         */
        public function manage_columns($columns)
        {
        }
        public function update_enter_title_here($title, $post)
        {
        }
        public function register_ajax_actions(\Elementor\Core\Common\Modules\Ajax\Module $ajax)
        {
        }
        public function register_icon_libraries_control($additional_sets)
        {
        }
        public function add_custom_icon_templates($current_screen)
        {
        }
        public function add_custom_icons_url($config)
        {
        }
        public static function get_custom_icons_config()
        {
        }
        public static function icon_set_prefix_exists($prefix)
        {
        }
        public function transition_post_status($new_status, $old_status, $post)
        {
        }
        protected function actions()
        {
        }
    }
}
namespace ElementorPro\Modules\AssetsManager\AssetTypes\Icons\IconSets {
    abstract class Icon_Set_Base
    {
        protected $dir_name = '';
        protected $directory = '';
        protected $data_file = '';
        protected $stylesheet_file = '';
        protected $allowed_zipped_files = [];
        protected $files_to_save = [];
        /**
         * Webfont extensions.
         *
         * @var array
         */
        protected $allowed_webfont_extensions = ['woff', 'woff2', 'ttf', 'svg', 'otf', 'eot'];
        protected abstract function extract_icon_list();
        protected abstract function prepare();
        protected abstract function get_type();
        public abstract function get_name();
        /**
         * is icon set
         *
         * validate that the current uploaded zip is in this icon set format
         * @return bool
         */
        public function is_icon_set()
        {
        }
        public function is_valid()
        {
        }
        protected function get_display_prefix()
        {
        }
        protected function get_prefix()
        {
        }
        public function handle_new_icon_set()
        {
        }
        /**
         * cleanup_temp_files
         * @param \WP_Filesystem_Base $wp_filesystem
         */
        protected function cleanup_temp_files($wp_filesystem)
        {
        }
        /**
         * Gets the URL to uploaded file.
         *
         * @param $file_name
         *
         * @return string
         */
        protected function get_file_url($file_name)
        {
        }
        protected function get_icon_sets_dir()
        {
        }
        protected function get_ensure_upload_dir($dir = '')
        {
        }
        public function move_files($post_id)
        {
        }
        public function get_unique_name()
        {
        }
        protected function get_url($filename = '')
        {
        }
        protected function get_stylesheet()
        {
        }
        protected function get_version()
        {
        }
        protected function get_enqueue()
        {
        }
        public function build_config()
        {
        }
        /**
         * Icon Set Base constructor.
         *
         * @param $directory
         */
        public function __construct($directory)
        {
        }
    }
    class Fontastic extends \ElementorPro\Modules\AssetsManager\AssetTypes\Icons\IconSets\Icon_Set_Base
    {
        protected $data = '';
        protected $data_file = 'icons-reference.html';
        protected $stylesheet_file = 'styles.css';
        protected $allowed_zipped_files = ['icons-reference.html', 'styles.css', 'fonts/'];
        protected $allowed_webfont_extensions = ['woff', 'ttf', 'svg', 'eot'];
        protected function prepare()
        {
        }
        public function get_type()
        {
        }
        public function is_valid()
        {
        }
        protected function extract_icon_list()
        {
        }
        protected function get_prefix()
        {
        }
        public function get_name()
        {
        }
        protected function get_stylesheet($unique_name = '')
        {
        }
    }
    class Fontello extends \ElementorPro\Modules\AssetsManager\AssetTypes\Icons\IconSets\Icon_Set_Base
    {
        protected $data_file = 'config.json';
        protected $stylesheet_file = '';
        protected $allowed_zipped_files = ['config.json', 'demo.html', 'README.txt', 'LICENSE.txt', 'css/', 'font/'];
        protected $allowed_webfont_extensions = ['woff', 'woff2', 'ttf', 'svg', 'otf'];
        protected function prepare()
        {
        }
        public function get_type()
        {
        }
        public function is_valid()
        {
        }
        protected function extract_icon_list()
        {
        }
        protected function get_prefix()
        {
        }
        public function get_name()
        {
        }
        protected function get_stylesheet()
        {
        }
    }
    class Icomoon extends \ElementorPro\Modules\AssetsManager\AssetTypes\Icons\IconSets\Icon_Set_Base
    {
        protected $data_file = 'selection.json';
        protected $stylesheet_file = 'style.css';
        protected $allowed_zipped_files = ['selection.json', 'demo.html', 'Read Mw.txt', 'demo-files/', 'fonts/'];
        protected $allowed_webfont_extensions = ['woff', 'ttf', 'svg', 'eot'];
        protected function prepare()
        {
        }
        public function get_type()
        {
        }
        public function is_valid()
        {
        }
        protected function extract_icon_list()
        {
        }
        protected function get_prefix()
        {
        }
        protected function get_display_prefix()
        {
        }
        public function get_name()
        {
        }
        protected function get_stylesheet()
        {
        }
    }
}
namespace ElementorPro\Modules\AssetsManager\AssetTypes\Icons {
    class Font_Awesome_Pro extends \ElementorPro\Modules\AssetsManager\Classes\Assets_Base
    {
        const FA_KIT_ID_OPTION_NAME = 'font_awesome_pro_kit_id';
        const FA_KIT_SCRIPT_LINK = 'https://kit.fontawesome.com/%s.js';
        public function get_name()
        {
        }
        public function get_type()
        {
        }
        public function replace_font_awesome_pro($settings)
        {
        }
        public function register_admin_fields(\Elementor\Settings $settings)
        {
        }
        public function enqueue_kit_js()
        {
        }
        public function sanitize_kit_id_settings($input)
        {
        }
        protected function actions()
        {
        }
    }
}
namespace ElementorPro\Modules\AssetsManager\AssetTypes {
    class Fonts_Manager
    {
        const CAPABILITY = 'manage_options';
        const CPT = 'elementor_font';
        const TAXONOMY = 'elementor_font_type';
        const FONTS_OPTION_NAME = 'elementor_fonts_manager_fonts';
        const FONTS_NAME_TYPE_OPTION_NAME = 'elementor_fonts_manager_font_types';
        const MENU_SLUG = 'edit.php?post_type=' . self::CPT;
        const PROMOTION_MENU_SLUG = 'e-custom-fonts';
        protected $font_types = [];
        /**
         * get a font type object for a given type
         *
         * @param null $type
         *
         * @return array|bool|\ElementorPro\Modules\AssetsManager\Classes\Font_Base
         */
        public function get_font_type_object($type = null)
        {
        }
        /**
         * Add a font type to the font manager
         *
         * @param string            $font_type
         * @param Classes\Font_Base $instance
         */
        public function add_font_type($font_type, $instance)
        {
        }
        /**
         * Register elementor font custom post type and elementor font type custom taxonomy
         */
        public function register_post_type_and_tax()
        {
        }
        public function post_updated_messages($messages)
        {
        }
        /**
         * Print Font Type metabox
         *
         * @param $post
         * @param $box
         */
        public function print_taxonomy_metabox($post, $box)
        {
        }
        public function redirect_admin_old_page_to_new()
        {
        }
        /**
         * Render preview column in font manager admin listing
         *
         * @param $column
         * @param $post_id
         */
        public function render_columns($column, $post_id)
        {
        }
        /**
         * Handle editor request to embed/link font CSS per font type
         *
         * @param array $data
         *
         * @return array
         * @throws \Exception
         */
        public function assets_manager_panel_action_data(array $data)
        {
        }
        /**
         * Clean up admin Font manager admin listing
         */
        public function clean_admin_listing_page()
        {
        }
        public function update_enter_title_here($title, $post)
        {
        }
        public function get_font_variables($font_variables)
        {
        }
        public function get_font_variable_ranges($font_variable_ranges)
        {
        }
        public function post_row_actions($actions, $post)
        {
        }
        public function display_post_states($post_states, $post)
        {
        }
        /**
         * Define which columns to display in font manager admin listing
         *
         * @param $columns
         *
         * @return array
         */
        public function manage_columns($columns)
        {
        }
        public function register_fonts_in_control($fonts)
        {
        }
        public function register_fonts_groups($font_groups)
        {
        }
        /**
         * runs on Elementor font post save and calls the font type handler save meta method
         *
         * @param int      $post_id
         * @param \WP_Post $post
         * @param bool     $update
         *
         * @return mixed
         */
        public function save_post_meta($post_id, $post, $update)
        {
        }
        /**
         * Helper to clean font list on save/update
         */
        public function clear_fonts_list()
        {
        }
        /**
         * Get fonts array form the database or generate a new list if $force is set to true
         *
         * @param bool $force
         *
         * @return array|bool|mixed
         */
        public function get_fonts()
        {
        }
        /**
         * Enqueue fonts css
         *
         * @param $post_css
         */
        public function enqueue_fonts($post_css)
        {
        }
        public function register_ajax_actions(\Elementor\Core\Common\Modules\Ajax\Module $ajax)
        {
        }
        public function add_finder_item(array $categories)
        {
        }
        public function admin_menu_make_open_on_subpage($parent_file)
        {
        }
        /**
         * Register Font Manager action and filter hooks
         */
        protected function actions()
        {
        }
        /**
         * Fonts_Manager constructor.
         */
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\AssetsManager\AssetTypes\AdminMenuItems {
    class Custom_Fonts_Promotion_Menu_Item extends \ElementorPro\Modules\Tiers\AdminMenuItems\Base_Promotion_Item
    {
        public function get_name()
        {
        }
        public function get_position()
        {
        }
        public function get_cta_url()
        {
        }
        public function get_cta_text()
        {
        }
        public function get_label()
        {
        }
        public function get_page_title()
        {
        }
        public function get_promotion_title()
        {
        }
        public function get_promotion_description()
        {
        }
        /**
         * @deprecated use get_promotion_description instead
         * @return void
         */
        public function render_promotion_description()
        {
        }
    }
    class Custom_Icons_Promotion_Menu_Item extends \ElementorPro\Modules\Tiers\AdminMenuItems\Base_Promotion_Item
    {
        public function get_name()
        {
        }
        public function get_cta_url()
        {
        }
        public function get_position()
        {
        }
        public function get_cta_text()
        {
        }
        public function get_label()
        {
        }
        public function get_page_title()
        {
        }
        public function get_promotion_title()
        {
        }
        public function get_promotion_description()
        {
        }
        /**
         * @deprecated use get_promotion_description instead
         * @return void
         */
        public function render_promotion_description()
        {
        }
    }
    class Custom_Icons_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item
    {
        public function get_capability()
        {
        }
        public function get_label()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_position()
        {
        }
        public function is_visible()
        {
        }
    }
    class Custom_Fonts_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item
    {
        public function get_capability()
        {
        }
        public function get_label()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_position()
        {
        }
        public function is_visible()
        {
        }
    }
}
namespace ElementorPro\Modules\AssetsManager\AssetTypes\Fonts {
    class Custom_Fonts extends \ElementorPro\Modules\AssetsManager\Classes\Font_Base
    {
        const FONT_META_KEY = 'elementor_font_files';
        const FONT_FACE_META_KEY = 'elementor_font_face';
        public function get_name()
        {
        }
        public function get_type()
        {
        }
        public function add_meta_box()
        {
        }
        public function render_metabox($post)
        {
        }
        public function save_meta($post_id, $data)
        {
        }
        public function upload_mimes($mine_types)
        {
        }
        public function wp_handle_upload_prefilter($file)
        {
        }
        /**
         * A workaround for upload validation which relies on a PHP extension (fileinfo) with inconsistent reporting behaviour.
         * ref: https://core.trac.wordpress.org/ticket/39550
         * ref: https://core.trac.wordpress.org/ticket/40175
         */
        public function filter_fix_wp_check_filetype_and_ext($data, $file, $filename, $mimes)
        {
        }
        public function generate_font_face($post_id)
        {
        }
        public function get_font_face_from_data($font_family, $data)
        {
        }
        public function get_fonts($force = false)
        {
        }
        public function render_preview_column($post_id)
        {
        }
        public function render_type_column($post_id)
        {
        }
        public function get_font_family_type($post_id, $post_title)
        {
        }
        public function get_font_data($post_id, $post_title)
        {
        }
        public function get_font_variations_count($post_id)
        {
        }
        /**
         * @param string $font_family
         * @param array  $font_data
         * @param Base   $post_css
         */
        public function enqueue_font($font_family, $font_data, $post_css)
        {
        }
        /**
         * @param array $data
         *
         * @return array
         * @throws \Exception
         */
        public function handle_panel_request(array $data)
        {
        }
        protected function actions()
        {
        }
    }
    class Typekit_Fonts extends \ElementorPro\Modules\AssetsManager\Classes\Font_Base
    {
        const TYPEKIT_KIT_ID_OPTION_NAME = 'typekit-kit-id';
        const TYPEKIT_FONTS_OPTION_NAME = 'elementor_typekit-data';
        const TYPEKIT_FONTS_LINK = 'https://use.typekit.net/%s.css';
        protected $kit_enqueued = false;
        protected $error = '';
        public function get_name()
        {
        }
        public function get_type()
        {
        }
        /**
         * @param array $data
         *
         * @return array
         * @throws \Exception
         */
        public function handle_panel_request(array $data)
        {
        }
        public function sanitize_kit_id_settings($input)
        {
        }
        public function register_admin_fields(\Elementor\Settings $settings)
        {
        }
        public function register_fonts_in_control($fonts)
        {
        }
        public function print_font_link($font)
        {
        }
        public function integrations_admin_ajax_handler()
        {
        }
        protected function actions()
        {
        }
    }
}
namespace ElementorPro\Modules\AssetsManager\AssetTypes {
    class Icons_Manager
    {
        const CAPABILITY = 'manage_options';
        const CPT = 'elementor_icons';
        const MENU_SLUG = 'edit.php?post_type=' . self::CPT;
        const PROMOTION_MENU_SLUG = 'e-custom-icons';
        protected $icon_types = [];
        /**
         * get a font type object for a given type
         *
         * @param null $type
         *
         * @return array|bool|\ElementorPro\Modules\AssetsManager\Classes\Font_Base
         */
        public function get_icon_type_object($type = null)
        {
        }
        /**
         * Add a font type to the font manager
         *
         * @param string            $icon_type
         * @param Classes\Assets_Base $instance
         */
        public function add_icon_type($icon_type, $instance)
        {
        }
        /**
         * Register elementor icon set custom post type
         */
        public function register_post_type()
        {
        }
        public function post_updated_messages($messages)
        {
        }
        public function redirect_admin_old_page_to_new()
        {
        }
        /**
         * Clean up admin Font manager admin listing
         */
        public function clean_admin_listing_page()
        {
        }
        public function post_row_actions($actions, $post)
        {
        }
        public function add_finder_item(array $categories)
        {
        }
        /**
         * Register Font Manager action and filter hooks
         */
        protected function actions()
        {
        }
        /**
         * Fonts_Manager constructor.
         */
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\Tiers {
    class Module extends \Elementor\Core\Base\Module
    {
        public function get_name()
        {
        }
        /**
         * @param array $texts
         * @return string
         */
        public static function get_promotion_template($texts, $is_marionette_template = false)
        {
        }
    }
}
namespace ElementorPro\Modules\Carousel {
    class Module extends \ElementorPro\Base\Module_Base
    {
        const WIDGET_HAS_CUSTOM_BREAKPOINTS = true;
        public function __construct()
        {
        }
        public function get_widgets()
        {
        }
        public function get_name()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/carousel/assets/scss/widgets/*.scss`
         * to `/assets/css/widget-*.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\Carousel\Widgets {
    abstract class Base extends \ElementorPro\Base\Base_Widget
    {
        public function get_script_depends()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        protected abstract function add_repeater_controls(\Elementor\Repeater $repeater);
        protected abstract function get_repeater_defaults();
        protected abstract function print_slide(array $slide, array $settings, $element_key);
        protected function register_controls()
        {
        }
        protected function print_slider(array $settings = null)
        {
        }
        protected function get_slide_image_url($slide, array $settings)
        {
        }
        protected function get_slide_image_alt_attribute($slide)
        {
        }
    }
    class Media_Carousel extends \ElementorPro\Modules\Carousel\Widgets\Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        /**
         * Get script dependencies.
         *
         * Retrieve the list of script dependencies the widget requires.
         *
         * @since 3.27.0
         * @access public
         *
         * @return array Widget script dependencies.
         */
        public function get_script_depends() : array
        {
        }
        protected function render()
        {
        }
        protected function register_controls()
        {
        }
        protected function add_repeater_controls(\Elementor\Repeater $repeater)
        {
        }
        protected function get_default_slides_count()
        {
        }
        protected function get_repeater_defaults()
        {
        }
        protected function get_image_caption($slide)
        {
        }
        protected function get_image_link_to($slide)
        {
        }
        protected function print_slider(array $settings = null)
        {
        }
        protected function print_slide(array $slide, array $settings, $element_key)
        {
        }
        protected function print_slide_image(array $slide, $element_key, array $settings)
        {
        }
        public function get_group_name()
        {
        }
    }
    class Testimonial_Carousel extends \ElementorPro\Modules\Carousel\Widgets\Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        /**
         * Get script dependencies.
         *
         * Retrieve the list of script dependencies the widget requires.
         *
         * @since 3.27.0
         * @access public
         *
         * @return array Widget script dependencies.
         */
        public function get_script_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function add_repeater_controls(\Elementor\Repeater $repeater)
        {
        }
        protected function get_repeater_defaults()
        {
        }
        protected function print_slide(array $slide, array $settings, $element_key)
        {
        }
        protected function render()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Reviews extends \ElementorPro\Modules\Carousel\Widgets\Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        /**
         * Get script dependencies.
         *
         * Retrieve the list of script dependencies the widget requires.
         *
         * @since 3.27.0
         * @access public
         *
         * @return array Widget script dependencies.
         */
        public function get_script_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function add_repeater_controls(\Elementor\Repeater $repeater)
        {
        }
        protected function get_repeater_defaults()
        {
        }
        protected function render_stars($slide, $settings)
        {
        }
        protected function print_slide(array $slide, array $settings, $element_key)
        {
        }
        protected function render()
        {
        }
        public function get_group_name()
        {
        }
    }
}
namespace ElementorPro\Modules\Slides\Controls {
    class Control_Slides_Animation extends \Elementor\Control_Hover_Animation
    {
        const TYPE = 'animation_slides_content';
        public function get_type() : string
        {
        }
        public static function get_animations() : array
        {
        }
    }
}
namespace ElementorPro\Modules\Slides {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        public function get_widgets()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/slides/assets/scss/frontend.scss`
         * to `/assets/css/widget-slides.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\Slides\Widgets {
    class Slides extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function get_script_depends()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public static function get_button_sizes()
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        /**
         * Render Slides widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
}
namespace ElementorPro\Modules\MotionFX {
    class Controls_Group extends \Elementor\Group_Control_Base
    {
        protected static $fields;
        /**
         * Get group control type.
         *
         * Retrieve the group control type.
         *
         * @since  2.5.0
         * @access public
         * @static
         */
        public static function get_type()
        {
        }
        /**
         * Init fields.
         *
         * Initialize group control fields.
         *
         * @since  2.5.0
         * @access protected
         */
        protected function init_fields()
        {
        }
        protected function get_default_options()
        {
        }
    }
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        /**
         * Get module name.
         *
         * Retrieve the module name.
         *
         * @since  2.5.0
         * @access public
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
        public function register_controls_group(\Elementor\Controls_Manager $controls_manager)
        {
        }
        public function add_controls_group_to_element(\Elementor\Element_Base $element)
        {
        }
        public function add_controls_group_to_element_background(\Elementor\Element_Base $element)
        {
        }
        public function register_frontend_styles()
        {
        }
        public function enqueue_preview_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\TableOfContents {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        public function get_widgets()
        {
        }
        public function get_name()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/table-of-contents/assets/scss/frontend.scss`
         * to `/assets/css/widget-table-of-contents.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\TableOfContents\Widgets {
    class Table_Of_Contents extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        /**
         * Get Frontend Settings
         *
         * In the TOC widget, this implementation is used to pass a pre-rendered version of the icon to the front end,
         * which is required in case the FontAwesome SVG experiment is active.
         *
         * @since 3.4.0
         *
         * @return array
         */
        public function get_frontend_settings()
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
    }
}
namespace ElementorPro\Modules\Search {
    class Module extends \ElementorPro\Base\Module_Base
    {
        const FEATURE_ID = 'search';
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        protected function get_widgets()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/search/assets/scss/frontend.scss`
         * to `/assets/css/widget-search.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
        public function add_localize_data($config)
        {
        }
        public function set_query($query)
        {
        }
    }
}
namespace ElementorPro\Modules\Search\Data {
    class Controller extends \ElementorPro\Core\Data\Controller
    {
        public function get_name()
        {
        }
        protected function register_endpoints()
        {
        }
    }
}
namespace ElementorPro\Modules\Search\Data\Endpoints {
    class Refresh_Search extends \ElementorPro\Core\Data\Endpoints\Refresh_Base
    {
        public function get_name() : string
        {
        }
        public function get_route() : string
        {
        }
        public function get_updated_search_widget_markup(\WP_REST_Request $request) : array
        {
        }
        protected function register()
        {
        }
    }
}
namespace ElementorPro\Modules\Search\Widgets {
    class Search extends \ElementorPro\Base\Base_Widget
    {
        use \ElementorPro\Modules\LoopBuilder\Files\Css\Loop_Css_Trait;
        protected $query = null;
        protected $search_term = '';
        protected $page_number = 1;
        public function set_search_term(string $search_term)
        {
        }
        public function set_page_number(int $page_number)
        {
        }
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function get_categories()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function get_query()
        {
        }
        public function get_query_args()
        {
        }
        protected function register_controls()
        {
        }
        protected function register_content_tab()
        {
        }
        protected function register_content_section_search_field()
        {
        }
        protected function register_content_section_results()
        {
        }
        protected function register_content_section_query()
        {
        }
        protected function register_content_section_additional_settings()
        {
        }
        protected function get_nothing_found_conditions()
        {
        }
        protected function register_style_tab()
        {
        }
        protected function register_style_section_search_field()
        {
        }
        protected function register_search_field_style_tabs($tab_id)
        {
        }
        protected function register_style_section_clear()
        {
        }
        protected function register_style_section_additional_settings()
        {
        }
        protected function register_style_section_submit()
        {
        }
        /**
         * Registers the controls of the submit section style for normal/hover state tabs.
         *
         * @param string $tab_id Accepts 'normal' or 'hover' as value.
         */
        protected function register_submit_style_tabs($tab_id)
        {
        }
        protected function register_style_section_results()
        {
        }
        protected function register_style_section_nothing_found_message()
        {
        }
        public function render_results()
        {
        }
        public function render_pagination()
        {
        }
        protected function add_nofollow_to_links($content)
        {
        }
        protected function get_paginate_args_for_rest_request($paginate_args)
        {
        }
        protected function handle_no_posts_found()
        {
        }
        protected function render_post()
        {
        }
        protected function render_loader($settings)
        {
        }
        protected function render()
        {
        }
        public function query_posts()
        {
        }
        public function get_query_name()
        {
        }
    }
}
namespace ElementorPro\Modules\AdminTopBar {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function get_name()
        {
        }
        /**
         * Module constructor.
         */
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\ShareButtons {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public static function get_networks($network_name = null)
        {
        }
        public function get_widgets()
        {
        }
        public function get_name()
        {
        }
        public function add_localize_data($settings)
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/share-buttons/assets/scss/frontend.scss`
         * to `/assets/css/widget-share-buttons.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\ShareButtons\Widgets {
    class Share_Buttons extends \ElementorPro\Base\Base_Widget
    {
        public function get_style_depends() : array
        {
        }
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        /**
         * Render Share Buttons widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
}
namespace ElementorPro\Modules\RoleManager {
    class Module extends \ElementorPro\Base\Module_Base
    {
        const ROLE_MANAGER_OPTION_NAME = 'role-manager';
        public function get_role_manager_options()
        {
        }
        public function get_name()
        {
        }
        public function save_advanced_options($input)
        {
        }
        public function get_user_restrictions()
        {
        }
        public function display_role_controls($role_slug, $role_data)
        {
        }
        public function register_admin_fields(\Elementor\Core\RoleManager\Role_Manager $role_manager)
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\LinkInBio\Classes\Render {
    class Single_Button_Cta_Render extends \Elementor\Modules\LinkInBio\Classes\Render\Core_Render
    {
        public function render_ctas() : void
        {
        }
    }
    class Icons_Below_Cta_Render extends \Elementor\Modules\LinkInBio\Classes\Render\Render_Base
    {
        public function render() : void
        {
        }
    }
}
namespace ElementorPro\Modules\LinkInBio {
    class Module extends \Elementor\Core\Base\Module
    {
        const EXPERIMENT_NAME = 'link-in-bio';
        public function get_name() : string
        {
        }
        public function get_widgets() : array
        {
        }
    }
}
namespace ElementorPro\Modules\LinkInBio\Base {
    abstract class Widget_Link_In_Bio_Base_Pro extends \Elementor\Modules\LinkInBio\Base\Widget_Link_In_Bio_Base
    {
    }
}
namespace ElementorPro\Modules\LinkInBio\Widgets {
    class Link_In_Bio_Var_2 extends \ElementorPro\Modules\LinkInBio\Base\Widget_Link_In_Bio_Base_Pro
    {
        public static function get_configuration()
        {
        }
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
        public function render() : void
        {
        }
        protected function add_content_tab() : void
        {
        }
        protected function add_style_tab() : void
        {
        }
        protected function add_style_identity_controls() : void
        {
        }
    }
    class Link_In_Bio_Var_3 extends \ElementorPro\Modules\LinkInBio\Base\Widget_Link_In_Bio_Base_Pro
    {
        public static function get_configuration()
        {
        }
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
    }
    class Link_In_Bio_Var_7 extends \ElementorPro\Modules\LinkInBio\Base\Widget_Link_In_Bio_Base_Pro
    {
        public static function get_configuration()
        {
        }
        public function get_description_position()
        {
        }
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
        public function render() : void
        {
        }
        protected function register_controls() : void
        {
        }
        protected function add_cta_controls()
        {
        }
        protected function add_style_identity_controls() : void
        {
        }
        protected function add_style_bio_controls() : void
        {
        }
        public function add_style_icons_controls() : void
        {
        }
    }
    class Link_In_Bio_Var_6 extends \ElementorPro\Modules\LinkInBio\Base\Widget_Link_In_Bio_Base_Pro
    {
        public static function get_configuration()
        {
        }
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
    }
    class Link_In_Bio_Var_4 extends \ElementorPro\Modules\LinkInBio\Base\Widget_Link_In_Bio_Base_Pro
    {
        public static function get_configuration()
        {
        }
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
        public function render() : void
        {
        }
        protected function add_content_tab() : void
        {
        }
        protected function add_style_tab() : void
        {
        }
    }
    class Link_In_Bio_Var_5 extends \ElementorPro\Modules\LinkInBio\Base\Widget_Link_In_Bio_Base_Pro
    {
        public static function get_configuration()
        {
        }
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
    }
}
namespace ElementorPro\Modules\PageTransitions {
    class Module extends \ElementorPro\Base\Module_Base
    {
        // Module name.
        const NAME = 'page-transitions';
        // Loader types.
        const TYPE_ANIMATION = 'animation';
        const TYPE_ICON = 'icon';
        const TYPE_IMAGE = 'image';
        // Pre-loader types.
        const LOADER_CIRCLE = 'circle';
        const LOADER_CIRCLE_DASHED = 'circle-dashed';
        const LOADER_BOUNCING_DOTS = 'bouncing-dots';
        const LOADER_PULSING_DOTS = 'pulsing-dots';
        const LOADER_PULSE = 'pulse';
        const LOADER_OVERLAP = 'overlap';
        const LOADER_SPINNERS = 'spinners';
        const LOADER_NESTED_SPINNERS = 'nested-spinners';
        const LOADER_OPPOSING_NESTED_SPINNERS = 'opposing-nested-spinners';
        const LOADER_OPPOSING_NESTED_RINGS = 'opposing-nested-rings';
        const LOADER_PROGRESS_BAR = 'progress-bar';
        const LOADER_TWO_WAY_PROGRESS_BAR = 'two-way-progress-bar';
        const LOADER_REPEATING_BAR = 'repeating-bar';
        /**
         * Module constructor.
         *
         * @return void
         */
        public function __construct()
        {
        }
        /**
         * Get the module name.
         *
         * @return string
         */
        public function get_name()
        {
        }
        /**
         * Register the Page Transitions controls.
         *
         * @param $element    Controls_Stack
         * @param $section_id string
         *
         * @return void
         */
        public function register_controls(\Elementor\Controls_Stack $element, $section_id)
        {
        }
        /**
         * Replace the Page Transition teaser with actual controls.
         *
         * @param Controls_Stack $controls_stack
         *
         * @return void
         */
        public function register_page_transitions_controls($controls_stack)
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url()
        {
        }
    }
}
namespace ElementorPro\Modules\GlobalWidget {
    class Module extends \ElementorPro\Base\Module_Base
    {
        const TEMPLATE_TYPE = 'widget';
        const WIDGET_TYPE_META_KEY = '_elementor_template_widget_type';
        const INCLUDED_POSTS_LIST_META_KEY = '_elementor_global_widget_included_posts';
        const WIDGET_NAME_CLASS_NAME_MAP = ['global-widget' => 'Global_Widget'];
        const LICENSE_FEATURE_NAME = 'global-widget';
        public function __construct()
        {
        }
        public function get_widgets()
        {
        }
        public function get_name()
        {
        }
        public function add_templates_localize_data($settings)
        {
        }
        public function set_template_widget_type_meta($post_id, $template_data)
        {
        }
        public function on_template_update($template_id, $template_data)
        {
        }
        public function filter_template_data($data)
        {
        }
        public function get_element_child_type(\Elementor\Element_Base $default_child_type, array $element_data)
        {
        }
        public function is_post_type_support_elementor($is_supported, $post_id, $post_type)
        {
        }
        public function is_template_supports_export($default_value, $template_id)
        {
        }
        /**
         * Remove user edit capabilities.
         *
         * Filters the user capabilities to disable editing in admin.
         *
         * @param array $allcaps An array of all the user's capabilities.
         * @param array $caps    Actual capabilities for meta capability.
         * @param array $args    Optional parameters passed to has_cap(), typically object ID.
         *
         * @return array
         * @deprecated 3.1.0 Use `Plugin::elementor()->documents->remove_user_edit_cap()` instead.
         */
        public function remove_user_edit_cap($allcaps, $caps, $args)
        {
        }
        public function is_widget_template($template_id)
        {
        }
        public function set_global_widget_included_posts_list($post_id, $editor_data)
        {
        }
        /**
         * @param Documents_Manager $documents_manager
         */
        public function register_documents($documents_manager)
        {
        }
        public function on_elementor_editor_init()
        {
        }
    }
}
namespace ElementorPro\Modules\GlobalWidget\Documents {
    class Widget extends \Elementor\Modules\Library\Documents\Library_Document
    {
        public static function get_properties()
        {
        }
        public function get_name()
        {
        }
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        public static function get_lock_behavior_v2()
        {
        }
        public function is_editable_by_current_user()
        {
        }
        public function import(array $data)
        {
        }
        public function save($data)
        {
        }
    }
}
namespace ElementorPro\Modules\GlobalWidget\Data {
    class Controller extends \Elementor\Data\Base\Controller
    {
        public function get_name()
        {
        }
        public function register_endpoints()
        {
        }
        // No endpoints.
        // TODO: After merging with 'REST API V2' add `get_collection_params`.
        public function get_items($request)
        {
        }
        public function get_permission_callback($request)
        {
        }
    }
}
namespace ElementorPro\Modules\GlobalWidget\Widgets {
    class Global_Widget extends \ElementorPro\Base\Base_Widget
    {
        public function __construct($data = [], $args = null)
        {
        }
        public function show_in_panel()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        public function get_raw_data($with_html_content = false)
        {
        }
        public function render_content()
        {
        }
        public function get_unique_selector()
        {
        }
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_script_depends()
        {
        }
        public function get_style_depends()
        {
        }
        public function get_controls($control_id = null)
        {
        }
        public function get_original_element_instance()
        {
        }
        public function on_export()
        {
        }
        public function render_plain_content()
        {
        }
        protected function add_render_attributes()
        {
        }
    }
}
namespace ElementorPro\Modules\Social\Classes {
    /**
     * Integration with Facebook SDK
     */
    class Facebook_SDK_Manager
    {
        const OPTION_NAME_APP_ID = 'elementor_pro_facebook_app_id';
        const FACEBOOK_PLUGINS_FAQ_URL = 'https://developers.facebook.com/docs/plugins/faqs?__cft__[0]=AZWTalTI1B5jfnDA1jij6GA2PisutktOCj7s5QwreTg5em5ewsd2SG3kRoKU88Q8v_2xyZHRsZs9mYrtQT1qBH05IIvy1T5a4SwAkTrZ7ZjuKqqahQEdc3dP-VZPvPApR-KDDeJmV2Auvjw_MKpySqgq&__tn__=R]-R#faq_1585575021764180';
        public static function get_app_id()
        {
        }
        public static function get_lang()
        {
        }
        public static function enqueue_meta_app_id()
        {
        }
        /**
         * @param Widget_Base $widget
         */
        public static function add_app_id_control($widget)
        {
        }
        public function localize_settings($settings)
        {
        }
        public function __construct()
        {
        }
        public static function get_permalink($settings = [])
        {
        }
        public function register_admin_fields(\Elementor\Settings $settings)
        {
        }
    }
}
namespace ElementorPro\Modules\Social {
    class Module extends \ElementorPro\Base\Module_Base
    {
        const URL_TYPE_CURRENT_PAGE = 'current_page';
        const URL_TYPE_CUSTOM = 'custom';
        const URL_FORMAT_PLAIN = 'plain';
        const URL_FORMAT_PRETTY = 'pretty';
        public function get_widgets()
        {
        }
        public function get_name()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/social/assets/scss/frontend.scss`
         * to `/assets/css/widget-social.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\Social\Widgets {
    class Facebook_Comments extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Facebook_Button extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Facebook_Page extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Facebook_Embed extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
}
namespace ElementorPro\Modules\CloudLibrary {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function get_name() : string
        {
        }
        public static function is_active() : bool
        {
        }
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\CustomCss {
    class Module extends \ElementorPro\Base\Module_Base
    {
        const LICENSE_FEATURE_NAME = 'custom-css';
        const LICENSE_FEATURE_NAME_GLOBAL = 'global-css';
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        /**
         * @param $element    Controls_Stack
         * @param $section_id string
         */
        public function register_controls(\Elementor\Controls_Stack $element, $section_id)
        {
        }
        /**
         * @param $post_css Post
         * @param $element  Element_Base
         */
        public function add_post_css($post_css, $element)
        {
        }
        /**
         * @param $post_css Post
         */
        public function add_page_settings_css($post_css)
        {
        }
        /**
         * @param Controls_Stack $controls_stack
         */
        public function replace_go_pro_custom_css_controls($controls_stack)
        {
        }
        protected function add_actions()
        {
        }
        public function replace_controls_with_upgrade_promotion(\Elementor\Controls_Stack $controls_stack, $tab, $template)
        {
        }
    }
}
namespace ElementorPro\Modules\CustomCss\AdminMenuItems {
    class Settings_Custom_CSS_Pro extends \Elementor\Core\Kits\Documents\Tabs\Settings_Custom_CSS
    {
        protected function register_tab_controls()
        {
        }
    }
}
namespace ElementorPro\Modules\Notes\Database\Migrations {
    class Add_Route_Post_Id extends \ElementorPro\Core\Database\Base_Migration
    {
        /**
         * @inheritDoc
         */
        public function up()
        {
        }
        /**
         * @inheritDoc
         */
        public function down()
        {
        }
    }
    class Add_Note_Position extends \ElementorPro\Core\Database\Base_Migration
    {
        /**
         * @inheritDoc
         */
        public function up()
        {
        }
        /**
         * @inheritDoc
         */
        public function down()
        {
        }
    }
    class Initial extends \ElementorPro\Core\Database\Base_Migration
    {
        /**
         * @inheritDoc
         */
        public function up()
        {
        }
        /**
         * @inheritDoc
         */
        public function down()
        {
        }
    }
    class Add_Author_Display_Name extends \ElementorPro\Core\Database\Base_Migration
    {
        /**
         * @inheritDoc
         */
        public function up()
        {
        }
        /**
         * @inheritDoc
         */
        public function down()
        {
        }
    }
    class Add_Capabilities extends \ElementorPro\Core\Database\Base_Migration
    {
        /**
         * @inheritDoc
         */
        public function up()
        {
        }
        /**
         * @inheritDoc
         */
        public function down()
        {
        }
    }
}
namespace ElementorPro\Modules\Notes\Database\Models {
    class Document extends \ElementorPro\Core\Database\Model_Base
    {
        /**
         * The document id (post_id)
         *
         * @var integer
         */
        public $ID;
        /**
         * The type of the document (post meta key = '_elementor_template_type')
         *
         * @var string
         */
        public $type;
        /**
         * Casts array.
         *
         * @var array
         */
        protected static $casts = ['ID' => self::TYPE_INTEGER];
        /**
         * Override the default Query Builder.
         *
         * @param \wpdb|null $connection
         *
         * @return Query_Builder
         */
        public static function query(\wpdb $connection = null)
        {
        }
        /**
         * Get the posts table name.
         *
         * @return string
         */
        public static function get_table()
        {
        }
        /**
         * Get the label of a document.
         *
         * @return string|null
         */
        public function get_type_title()
        {
        }
        /**
         * Return a JSON serialized representation of the User.
         *
         * @return array
         */
        #[\ReturnTypeWillChange]
        public function jsonSerialize()
        {
        }
    }
    class Note extends \ElementorPro\Core\Database\Model_Base
    {
        // Note statuses.
        const STATUS_PUBLISH = 'publish';
        const STATUS_DRAFT = 'draft';
        const STATUS_TRASH = 'trash';
        // Note user relations.
        const USER_RELATION_READ = 'read';
        const USER_RELATION_MENTION = 'mention';
        /**
         * Note ID.
         *
         * @var int
         */
        public $id;
        /**
         * Note's post ID.
         *
         * @var null|int
         */
        public $post_id = null;
        /**
         * Note's element ID.
         *
         * @var null|int
         */
        public $element_id = null;
        /**
         * Note's parent ID.
         *
         * @var int
         */
        public $parent_id = 0;
        /**
         * Note's author ID.
         *
         * @var null|int
         */
        public $author_id = null;
        /**
         * @var null|string
         */
        public $author_display_name = null;
        /**
         * Note's route post ID.
         *
         * @var null|integer
         */
        public $route_post_id = null;
        /**
         * @var string
         */
        public $route_url = null;
        /**
         * @var string
         */
        public $route_title = null;
        /**
         * Note's status.
         *
         * @var string
         */
        public $status = self::STATUS_PUBLISH;
        /**
         * Note's position in the element.
         *
         * @var array{x: int, y: int}
         */
        public $position = ['x' => 0, 'y' => 0];
        /**
         * Note's content.
         *
         * @var null|string
         */
        public $content = null;
        /**
         * Note's resolve status.
         *
         * @var bool
         */
        public $is_resolved = false;
        /**
         * Note's public status.
         *
         * @var bool
         */
        public $is_public = true;
        /**
         * Is the note read by the user.
         *
         * @var boolean
         */
        public $is_read = false;
        /**
         * Note's replies.
         *
         * @var Collection <Note>
         */
        public $replies;
        /**
         * Note's mentions.
         *
         * @var Collection<User>
         */
        public $mentions;
        /**
         * Note's author.
         *
         * @var User
         */
        public $author;
        /**
         * Note's document
         *
         * @var Document
         */
        public $document;
        /**
         * Note's replies count.
         *
         * @var int
         */
        public $replies_count = 0;
        /**
         * Note's unread replies count.
         *
         * @var int
         */
        public $unread_replies_count = 0;
        /**
         * Note's readers.
         *
         * @var Collection <User>
         */
        public $readers;
        /**
         * Note's creation time.
         *
         * @var \DateTime
         */
        public $created_at;
        /**
         * Note's last update time.
         *
         * @var \DateTime
         */
        public $updated_at;
        /**
         * Note's last activity time.
         *
         * @var \DateTime
         */
        public $last_activity_at;
        /**
         * User's capabilities for the current note.
         * [
         *  'edit' => boolean,
         *  'delete' => boolean,
         * ]
         *
         * @var array
         */
        public $user_can = [];
        /**
         * Casts array.
         *
         * @var array
         */
        protected static $casts = ['id' => self::TYPE_INTEGER, 'post_id' => self::TYPE_INTEGER, 'route_post_id' => self::TYPE_INTEGER, 'parent_id' => self::TYPE_INTEGER, 'author_id' => self::TYPE_INTEGER, 'position' => self::TYPE_JSON, 'is_resolved' => self::TYPE_BOOLEAN, 'is_public' => self::TYPE_BOOLEAN, 'is_read' => self::TYPE_BOOLEAN, 'replies' => self::TYPE_COLLECTION, 'mentions' => self::TYPE_COLLECTION, 'readers' => self::TYPE_COLLECTION, 'replies_count' => self::TYPE_INTEGER, 'unread_replies_count' => self::TYPE_INTEGER, 'created_at' => self::TYPE_DATETIME_GMT, 'updated_at' => self::TYPE_DATETIME_GMT, 'last_activity_at' => self::TYPE_DATETIME_GMT];
        public function __construct(array $fields)
        {
        }
        /**
         * Override the default Query Builder.
         *
         * @param \wpdb|null $connection
         *
         * @return Note_Query_Builder
         */
        public static function query(\wpdb $connection = null)
        {
        }
        /**
         * Get the notes table name.
         *
         * @return string
         */
        public static function get_table()
        {
        }
        /**
         * Is the current note is top level note.
         *
         * @return bool
         */
        public function is_thread()
        {
        }
        /**
         * Determine if the current note is a reply.
         *
         * @return bool
         */
        public function is_reply()
        {
        }
        /**
         * Get the thread ID of the current note.
         *
         * @return int
         */
        public function get_thread_id()
        {
        }
        /**
         * Get the note deep link.
         *
         * @param bool $force_auth - Whether to force authentication. Defaults to `true`.
         *
         * @return string
         */
        public function get_url($force_auth = true)
        {
        }
        /**
         * Generate a note deep link URL.
         *
         * @param string|int $id - Note ID.
         * @param string $route_url - Note route URL. Required if `$force_auth = false`.
         * @param bool $force_auth - Whether to force authentication. Defaults to `true`. Used in cases where the user
         *                           should be passed through the proxy in order to force their authentication (since the
         *                           "Notes" feature and the Web-CLI are available only for logged-in users).
         *
         * @return string
         */
        public static function generate_url($id = null, $route_url = '', $force_auth = true)
        {
        }
        /**
         * @shortcut `$this->add_user_relation()`
         */
        public function add_readers($user_ids = [])
        {
        }
        /**
         * @shortcut `$this->remove_user_relation()`
         */
        public function remove_readers($user_ids = [])
        {
        }
        /**
         * @shortcut `$this->sync_user_relation()`
         */
        public function sync_mentions($user_keys = [], $key = 'ID')
        {
        }
        /**
         * @shortcut `$this->add_user_relation()`
         */
        public function add_mentions($user_ids = [])
        {
        }
        /**
         * Remove old relations and add new ones.
         *
         * @param        $type
         * @param array  $user_keys
         * @param string $key
         *
         * @return Collection Only users with a newly created relation (excluding the existing ones).
         */
        public function sync_user_relation($type, array $user_keys, $key = 'ID')
        {
        }
        /**
         * Remove user relation.
         *
         * @param       $type
         * @param array $user_ids
         */
        public function remove_user_relation($type, array $user_ids)
        {
        }
        /**
         * Add user relation.
         *
         * @param       $type
         * @param array $user_ids
         *
         * @throws \Exception
         */
        public function add_user_relation($type, array $user_ids)
        {
        }
        /**
         * Add user capabilities to the Note and its replies.
         *
         * @param integer $user_id - User ID to use.
         * @param bool $recursive - Whether to add the capabilities also to the replies.
         *
         * @return Note
         */
        public function attach_user_capabilities($user_id, $recursive = true)
        {
        }
    }
    class Note_Summary extends \ElementorPro\Core\Database\Model_Base
    {
        /**
         * @var string
         */
        public $url = null;
        /**
         * @var string
         */
        public $full_url = null;
        /**
         * @var string
         */
        public $title = null;
        /**
         * @var int
         */
        public $notes_count = 0;
        /**
         * Casts array.
         *
         * @var array
         */
        protected static $casts = ['notes_count' => self::TYPE_INTEGER];
        /**
         * @inheritDoc
         */
        public function __construct(array $fields)
        {
        }
        /**
         * Override the default Query Builder.
         *
         * @param \wpdb|null $connection
         *
         * @return Note_Query_Builder
         */
        public static function query(\wpdb $connection = null)
        {
        }
        /**
         * Get the notes table name.
         *
         * @return string
         */
        public static function get_table()
        {
        }
    }
    // TODO: Should be in Core.
    class User extends \ElementorPro\Core\Database\Model_Base
    {
        use \ElementorPro\Core\Notifications\Traits\Notifiable;
        /**
         * User's ID.
         * Note: Must be uppercase to correspond with the DB naming.
         *
         * @var int
         */
        public $ID;
        /**
         * User's actual user name.
         *
         * @var string
         */
        public $user_login;
        /**
         * User's nice name.
         *
         * @var string
         */
        public $user_nicename;
        /**
         * User's email.
         *
         * @var string
         */
        public $user_email;
        /**
         * User's URL.
         *
         * @var string
         */
        public $user_url;
        /**
         * User's status.
         *
         * @var int
         */
        public $user_status;
        /**
         * User's display name.
         *
         * @var string
         */
        public $display_name;
        /**
         * Casts array.
         *
         * @var array
         */
        protected static $casts = ['ID' => self::TYPE_INTEGER];
        /**
         * Initialize a new `User` object from a `WP_User` object.
         *
         * @param \WP_User $user - WP_User object.
         *
         * @return static
         */
        public static function from_wp_user(\WP_User $user)
        {
        }
        /**
         * Override the default Query Builder.
         *
         * @param \wpdb|null $connection
         *
         * @return \ElementorPro\Modules\Notes\Database\Query\User_Query_Builder()
         */
        public static function query(\wpdb $connection = null)
        {
        }
        /**
         * Get the model's table name.
         *
         * @return string
         */
        public static function get_table()
        {
        }
        /**
         * Generate avatars urls based on user id.
         *
         * @param $id
         *
         * @return Collection
         */
        public static function generate_avatars_urls($id)
        {
        }
        /**
         * Get the user's avatars.
         *
         * @return Collection
         */
        public function get_avatars()
        {
        }
    }
}
namespace ElementorPro\Modules\Notes\Database\Transformers {
    class User_Transformer
    {
        /**
         * Apply transformations to the $user received.
         *
         * @param User $user
         * @param array $dependencies{post_id: int}
         *
         * @return array
         */
        public function transform(\ElementorPro\Modules\Notes\Database\Models\User $user, $dependencies = [])
        {
        }
        /**
         * Maps the user properties to new keys.
         *
         * @param User $user
         *
         * @return array
         */
        protected function map_properties(\ElementorPro\Modules\Notes\Database\Models\User $user)
        {
        }
        /**
         * Add user capabilities to the user object.
         *
         * @param array $user
         * @param array $dependencies
         *
         * @return array
         */
        protected function add_capabilities(array $user, $dependencies)
        {
        }
    }
}
namespace ElementorPro\Modules\Notes\Database {
    class Notes_Database_Updater extends \ElementorPro\Core\Database\Base_Database_Updater
    {
        const DB_VERSION = 5;
        const OPTION_NAME = 'elementor_notes_db_version';
        /**
         * @inheritDoc
         */
        protected function get_migrations()
        {
        }
        /**
         * @inheritDoc
         */
        protected function get_db_version()
        {
        }
        /**
         * @inheritDoc
         */
        protected function get_db_version_option_name()
        {
        }
    }
}
namespace ElementorPro\Modules\Notes\Database\Query {
    /**
     * @method User|null find( $id, $field = 'id' )
     */
    class User_Query_Builder extends \ElementorPro\Core\Database\Model_Query_Builder
    {
        /**
         * Note_Query_Builder constructor.
         *
         * @param \wpdb|null $connection
         */
        public function __construct(\wpdb $connection = null)
        {
        }
        /**
         * Filter only users who are relevant to note (created / replied to / mention in thread).
         *
         * @param Note $note
         *
         * @return $this
         */
        public function only_relevant_to_note(\ElementorPro\Modules\Notes\Database\Models\Note $note)
        {
        }
    }
    /**
     * @method Note|null first()
     * @method Note|null find( $id, $field = 'id' )
     */
    class Note_Query_Builder extends \ElementorPro\Core\Database\Model_Query_Builder
    {
        /**
         * Note_Query_Builder constructor.
         *
         * @param \wpdb|null $connection
         */
        public function __construct(\wpdb $connection = null)
        {
        }
        /**
         * Override the default `compile_wheres()` to handle `with_trashed()`.
         *
         * @inheritDoc
         */
        public function compile_wheres()
        {
        }
        /**
         * Set the `with_trashed` flag to `true`.
         *
         * @return $this
         */
        public function with_trashed()
        {
        }
        /**
         * Eager load the Note's replies.
         *
         * @param callable|null $callback - Callback that gets a `Note_Query_Builder` to customize the replies query.
         *
         * @return $this
         */
        public function with_replies(callable $callback = null)
        {
        }
        /**
         * Eager load the Note's replies count.
         *
         * @return $this
         */
        public function with_replies_count()
        {
        }
        /**
         * Eager load the Note's readers.
         *
         * @return $this
         */
        public function with_readers()
        {
        }
        /**
         * Eager load the Note's author.
         *
         * @return $this
         */
        public function with_author()
        {
        }
        /**
         * Eager load the Note's document.
         *
         * @return $this
         */
        public function with_document()
        {
        }
        /**
         * Eager load the Note's read state by a user ID.
         *
         * @param int $user_id - User ID to check.
         *
         * @return $this
         */
        public function with_is_read($user_id)
        {
        }
        /**
         * Make sure that users without permissions to read private notes won't get them.
         *
         * @param integer $user_id - User ID to check.
         *
         * @return Note_Query_Builder
         */
        public function only_visible($user_id)
        {
        }
        /**
         * Filter only notes that their post is visible to the user.
         *
         * @param $user_id
         *
         * @return $this
         */
        public function only_visible_posts($user_id)
        {
        }
        /**
         * Filter only the notes that are relevant to the user.
         *
         * @param int $user_id - User ID to check.
         *
         * @return Note_Query_Builder
         */
        public function only_relevant($user_id)
        {
        }
        /**
         * Filter only unread notes.
         *
         * @param integer $user_id - User id that the notes are unread by.
         *
         * @return Note_Query_Builder
         */
        public function only_unread($user_id)
        {
        }
        /**
         * Filter only threads.
         *
         * @return Note_Query_Builder
         */
        public function only_threads()
        {
        }
        /**
         * Filter only replies.
         *
         * @return Note_Query_Builder
         */
        public function only_replies()
        {
        }
        /**
         * Filter only trashed notes.
         *
         * @return Note_Query_Builder
         */
        public function only_trashed()
        {
        }
        /**
         * Eager load the Note's unread replies count by a user ID.
         *
         * @param int $user_id - User ID to check.
         *
         * @return Note_Query_Builder
         */
        public function with_unread_replies_count($user_id)
        {
        }
        /**
         * Extends base delete method to allow deleting all the related entities
         * of the notes, including 'user relations' and 'replies'.
         *
         * @param false $include_related_entities
         *
         * @return bool|int
         */
        public function delete($include_related_entities = false)
        {
        }
        /**
         * Move notes to trash.
         *
         * @return bool|int
         */
        public function trash()
        {
        }
        /**
         * Restore notes from trash.
         *
         * @return bool|int
         */
        public function restore()
        {
        }
    }
}
namespace ElementorPro\Modules\Notes {
    /**
     * A simple admin page to behave as a proxy for Note opening (using a deep-link).
     * This class registers an admin page the redirects to a Note page, in order to make sure that the user is logged in
     * before accessing a Note (since Notes aren't available for unauthorized users).
     */
    class Admin_Page
    {
        const PAGE_ID = 'elementor-pro-notes-proxy';
        /**
         * Register actions and hooks.
         *
         * @return void
         */
        public function register()
        {
        }
        /**
         * Register the admin page (will be removed later from the menu).
         *
         * @return void
         */
        protected function register_admin_menu()
        {
        }
        /**
         * Hide the menu item, since it shouldn't be visible to users in the UI.
         *
         * @return void
         */
        protected function hide_menu_item()
        {
        }
        /**
         * Run the actual proxy page.
         *
         * @return void
         */
        public function on_page_load()
        {
        }
        /**
         * Redirect to a note - Used for testing.
         *
         * @param Note $note
         *
         * @return void
         */
        protected function redirect_to_note(\ElementorPro\Modules\Notes\Database\Models\Note $note)
        {
        }
        /**
         * Safe redirect to a page - Used for testing.
         *
         * @param string $url
         *
         * @return void
         */
        protected function safe_redirect($url)
        {
        }
        /**
         * Show a message to the user and die - Used for testing.
         *
         * @param string $message
         *
         * @return void
         */
        protected function message_and_die($message)
        {
        }
    }
    class Document_Events
    {
        /**
         * Register all the actions
         */
        public function register()
        {
        }
    }
}
namespace ElementorPro\Modules\Notes\User {
    class Personal_Data
    {
        const WP_KEY = 'elementor-notes';
        /**
         * Register actions and hooks.
         *
         * @return void
         */
        public function register()
        {
        }
        /**
         * Get the data key for the exporter.
         *
         * @return string
         */
        public function get_key()
        {
        }
        /**
         * Get the exporter friendly name.
         *
         * @return string
         */
        public function get_title()
        {
        }
    }
    class Capabilities
    {
        const ENABLE_PERMISSIONS_OPTION = 'elementor_pro_notes_enable_permissions';
        const CREATE_NOTES = 'create_notes_elementor-pro';
        const EDIT_NOTES = 'edit_notes_elementor-pro';
        const EDIT_OTHERS_NOTES = 'edit_others_notes_elementor-pro';
        const DELETE_NOTES = 'delete_notes_elementor-pro';
        const DELETE_OTHERS_NOTES = 'delete_others_notes_elementor-pro';
        const READ_NOTES = 'read_notes_elementor-pro';
        const READ_OTHERS_PRIVATE_NOTES = 'read_others_private_notes_elementor-pro';
        const EDIT_POST = 'edit_post';
        /**
         * All the capabilities includes the admin permissions
         *
         * @return string[]
         */
        public static function all()
        {
        }
        /**
         * All the basic capabilities for regular users
         *
         * @return string[]
         */
        public static function basic()
        {
        }
        /**
         * Check if a user has all the basic Notes capabilities.
         *
         * @param \WP_User $user
         *
         * @return bool
         */
        public static function has_all_basic_capabilities(\WP_User $user)
        {
        }
        /**
         * Register actions and hooks
         */
        public function register()
        {
        }
        /**
         * Add or remove notes capabilities based on the permission checkbox.
         *
         * @param $user_id
         */
        public function update_user_capabilities($user_id)
        {
        }
        /**
         * Render the permission checkbox in the user edit page.
         *
         * @param \WP_User $user
         */
        public function render_edit_user_profile_options(\WP_User $user)
        {
        }
        /**
         * Check whether a user has access to Notes.
         *
         * @param int $user_id
         *
         * @return bool
         */
        public static function can_read_notes($user_id)
        {
        }
        /**
         * Check whether a user has edit access to specific post.
         *
         * @param int $user_id
         * @param int $post_id
         *
         * @return bool
         */
        public static function can_edit_post($user_id, $post_id)
        {
        }
    }
    class Delete_User
    {
        /**
         * Register actions and hooks.
         *
         * @return void
         */
        public function register()
        {
        }
    }
    class Preferences
    {
        const ENABLE_NOTIFICATIONS = 'elementor_pro_enable_notes_notifications';
        /**
         * Register actions and hooks.
         *
         * @return void
         */
        public function register()
        {
        }
        /**
         * Determine if notifications are enabled for a user.
         *
         * @param int $user_id - User ID.
         *
         * @return bool
         */
        public static function are_notifications_enabled($user_id)
        {
        }
        /**
         * Add settings to the "Personal Options".
         *
         * @param \WP_User $user - User object.
         *
         * @return void
         */
        protected function add_personal_options_settings(\WP_User $user)
        {
        }
        /**
         * Save the settings in the "Personal Options".
         *
         * @param int $user_id - User ID.
         *
         * @return void
         */
        protected function update_personal_options_settings($user_id)
        {
        }
        /**
         * Determine if the current user has permission to view/change notes preferences of a user.
         *
         * @param int $user_id
         *
         * @return bool
         */
        protected function has_permissions_to_edit_user($user_id)
        {
        }
    }
}
namespace ElementorPro\Modules\Notes {
    class Module extends \Elementor\Core\Base\App
    {
        const NAME = 'notes';
        const LICENSE_FEATURE_NAME = 'editor_comments';
        // Module-related tables.
        const TABLE_NOTES = 'e_notes';
        const TABLE_NOTES_USERS_RELATIONS = 'e_notes_users_relations';
        /**
         * @return string
         */
        public function get_name()
        {
        }
        /**
         * @return string
         */
        public function get_assets_base_url()
        {
        }
        /**
         * Enqueue Notes styles.
         */
        public function enqueue_styles()
        {
        }
        /**
         * Expose settings to the frontend under 'window.elementorNotesConfig'.
         *
         * @return void
         */
        protected function add_config()
        {
        }
        public function __construct()
        {
        }
    }
    class Admin_Bar
    {
        /**
         * Register actions and hooks.
         *
         * @return void
         */
        public function register()
        {
        }
    }
    class Usage
    {
        const THREADS = 'threads';
        const REPLIES = 'replies';
        /**
         * Register hooks.
         *
         * @return void
         */
        public function register()
        {
        }
    }
    /**
     * This is specific for notes and should not be used outside the module.
     */
    class Utils
    {
        /**
         * Clean the url.
         *
         * @param $url
         *
         * @return string
         */
        public static function clean_url($url)
        {
        }
        /**
         * @param $value
         *
         * @return bool
         */
        public static function validate_url_or_relative_url($value)
        {
        }
        /**
         * Clean the WP document title and return it.
         *
         * @return string
         */
        public static function get_clean_document_title()
        {
        }
    }
}
namespace ElementorPro\Modules\Notes\Data {
    class Controller extends \Elementor\Data\V2\Base\Controller
    {
        public function get_name()
        {
        }
        public function __construct()
        {
        }
        public function register_endpoints()
        {
        }
        /**
         * Notes index route params.
         *
         * @return array[]
         */
        public function get_collection_params()
        {
        }
        /**
         * Get all Notes by filters.
         *
         * GET `/notes`
         *
         * @param \WP_REST_Request $request
         *
         * @return array
         */
        public function get_items($request)
        {
        }
        /**
         * Get a single note.
         *
         * GET `/notes/{id}`
         *
         * @param \WP_REST_Request $request
         *
         * @return array
         */
        public function get_item($request)
        {
        }
        /**
         * Run all user models in the note through user transformer.
         *
         * @param Note $note
         *
         * @return Note
         */
        protected function transform_users(\ElementorPro\Modules\Notes\Database\Models\Note $note)
        {
        }
        /**
         * Create a note.
         *
         * POST `/notes`
         *
         * @param \WP_REST_Request $request
         *
         * @return array
         * @throws \Exception
         */
        public function create_items($request)
        {
        }
        /**
         * Update a note.
         *
         * PATCH `/notes/{id}`
         *
         * @param \WP_REST_Request $request
         *
         * @return array
         */
        public function update_item($request)
        {
        }
        /**
         * Delete a note.
         *
         * DELETE `/notes/{id}`
         *
         * @param \WP_REST_Request $request
         *
         * @return \WP_REST_Response
         * @throws \Elementor\Data\V2\Base\Exceptions\Error_404
         */
        public function delete_item($request)
        {
        }
        /**
         * @inheritDoc
         */
        public function get_permission_callback($request)
        {
        }
        /**
         * Get the Notes filters.
         *
         * @return array
         */
        public function get_filters()
        {
        }
        /**
         * Handle note creation side-effects.
         *
         * @param array $event
         *
         * @return void
         */
        protected function on_note_created(array $event)
        {
        }
        /**
         * Handle note update side-effects.
         *
         * @param array $event
         *
         * @return void
         */
        protected function on_note_updated(array $event)
        {
        }
    }
}
namespace ElementorPro\Modules\Notes\Data\Endpoints {
    class Summary_Endpoint extends \Elementor\Data\V2\Base\Endpoint
    {
        public function get_name()
        {
        }
        public function get_format()
        {
        }
        /**
         * Register the endpoint routes.
         *
         * @return void
         */
        protected function register()
        {
        }
        /**
         * Index route.
         *
         * GET `/notes/summary`
         *
         * @param \WP_REST_Request $request
         *
         * @return array
         */
        protected function get_items($request)
        {
        }
        /**
         * @inheritDoc
         */
        public function get_permission_callback($request)
        {
        }
    }
    class Users_Endpoint extends \Elementor\Data\V2\Base\Endpoint
    {
        public function get_name()
        {
        }
        public function get_format()
        {
        }
        /**
         * Register the endpoint routes.
         *
         * @return void
         */
        protected function register()
        {
        }
        /**
         * Index route.
         *
         * GET `/notes/users`
         *
         * @param \WP_REST_Request $request
         *
         * @return array
         */
        protected function get_items($request)
        {
        }
        /**
         * @inheritDoc
         */
        public function get_permission_callback($request)
        {
        }
        /**
         * Get the Users filters.
         *
         * @return array
         */
        protected function get_filters()
        {
        }
    }
    class Read_Status_Endpoint extends \Elementor\Data\V2\Base\Endpoint
    {
        /**
         * @inheritDoc
         */
        public function get_name()
        {
        }
        /**
         * @inheritDoc
         */
        public function get_format()
        {
        }
        /**
         * @inheritDoc
         */
        protected function register()
        {
        }
        /**
         * Mark notes as read by the current user.
         *
         * @param \WP_REST_Request $request
         *
         * @return \WP_REST_Response
         */
        protected function create_items($request)
        {
        }
        /**
         * Mark notes as unread by the current user.
         *
         * @param \WP_REST_Request $request
         *
         * @return \WP_REST_Response
         */
        protected function delete_items($request)
        {
        }
        /**
         * @inheritDoc
         */
        public function get_permission_callback($request)
        {
        }
    }
}
namespace ElementorPro\Modules\Notes\Notifications {
    abstract class Base_Notes_Notification extends \ElementorPro\Core\Notifications\Notification
    {
        /**
         * @var Note
         */
        public $note;
        /**
         * @var array
         */
        public $exclude;
        /**
         * @var User
         */
        public $actor;
        /**
         * Note_Notification constructor.
         *
         * @param Note $note
         * @param User $actor
         * @param array $exclude
         *
         * @return void
         */
        public function __construct(\ElementorPro\Modules\Notes\Database\Models\Note $note, \ElementorPro\Modules\Notes\Database\Models\User $actor, array $exclude = [])
        {
        }
        /**
         * Get the notification payloads.
         *
         * @param User $notifiable
         *
         * @return array
         */
        public function get_payloads($notifiable)
        {
        }
        /**
         * Get the sender email & name.
         *
         * @return string[]
         */
        protected function get_sender()
        {
        }
        /**
         * Initialize an `Email_Message` for the current notification.
         *
         * @param $notifiable
         *
         * @return \ElementorPro\Core\Integrations\Actions\Email\Email_Message
         */
        protected abstract function create_email_message($notifiable);
    }
    class User_Replied_Notification extends \ElementorPro\Modules\Notes\Notifications\Base_Notes_Notification
    {
        protected function create_email_message($notifiable)
        {
        }
    }
    class User_Mentioned_Notification extends \ElementorPro\Modules\Notes\Notifications\Base_Notes_Notification
    {
        protected function create_email_message($notifiable)
        {
        }
    }
    class User_Resolved_Notification extends \ElementorPro\Modules\Notes\Notifications\Base_Notes_Notification
    {
        protected function create_email_message($notifiable)
        {
        }
    }
}
namespace ElementorPro\Modules\CompatibilityTag {
    class Compatibility_Tag_Component extends \Elementor\Modules\CompatibilityTag\Base_Module
    {
        /**
         * This is the header used by extensions to show testing.
         *
         * @var string
         */
        const PLUGIN_VERSION_TESTED_HEADER = 'Elementor Pro tested up to';
        /**
         * @return string
         */
        protected function get_plugin_header()
        {
        }
        /**
         * @return string
         */
        protected function get_plugin_label()
        {
        }
        /**
         * @return string
         */
        protected function get_plugin_name()
        {
        }
        /**
         * @return string
         */
        protected function get_plugin_version()
        {
        }
    }
    class Module extends \ElementorPro\Base\Module_Base
    {
        const MODULE_NAME = 'compatibility-tag-pro';
        /**
         * Checks if elementor core compatibility module is exists before
         * activate this module
         *
         * @return bool
         */
        public static function is_active()
        {
        }
        /**
         * @return string
         */
        public function get_name()
        {
        }
        /**
         * Module constructor.
         */
        public function __construct()
        {
        }
    }
}
namespace ElementorPro\Modules\Blockquote {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        public function get_widgets()
        {
        }
        public function get_name()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/blockquote/assets/scss/frontend.scss`
         * to `/assets/css/widget-blockquote.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\Blockquote\Widgets {
    class Blockquote extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        /**
         * Render Blockquote widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
}
namespace ElementorPro\Modules\ThemeElements {
    class Module extends \ElementorPro\Base\Module_Base
    {
        const SOURCE_TYPE_CURRENT_POST = 'current_post';
        const SOURCE_TYPE_CUSTOM = 'custom';
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        public function get_widgets()
        {
        }
        public function is_yoast_seo_active()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/theme-elements/assets/scss/frontend.scss`
         * to `/assets/css/widget-theme-elements.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\ThemeElements\Widgets {
    abstract class Base extends \ElementorPro\Base\Base_Widget
    {
        public function get_categories()
        {
        }
        public function render_plain_content()
        {
        }
    }
    class Search_Form extends \ElementorPro\Modules\ThemeElements\Widgets\Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        public function show_in_panel() : bool
        {
        }
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        /**
         * Render Search Form widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Post_Navigation extends \ElementorPro\Modules\ThemeElements\Widgets\Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        public function get_script_depends()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Breadcrumbs extends \ElementorPro\Modules\ThemeElements\Widgets\Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_script_depends()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function get_keywords()
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function get_group_name()
        {
        }
    }
    /**
     * Elementor sitemap widget.
     *
     * Elementor widget that displays an HTML sitemap.
     *
     */
    class Sitemap extends \ElementorPro\Modules\ThemeElements\Widgets\Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Post_Comments extends \ElementorPro\Modules\ThemeElements\Widgets\Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function register_controls()
        {
        }
        public function render()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Author_Box extends \ElementorPro\Modules\ThemeElements\Widgets\Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Post_Info extends \ElementorPro\Modules\ThemeElements\Widgets\Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_categories()
        {
        }
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function get_taxonomies()
        {
        }
        protected function get_meta_data($repeater_item)
        {
        }
        protected function render_item($repeater_item)
        {
        }
        protected function render_item_icon_or_image($item_data, $repeater_item, $repeater_index)
        {
        }
        protected function render_item_text($item_data, $repeater_index)
        {
        }
        protected function render()
        {
        }
        public function get_group_name()
        {
        }
    }
}
namespace ElementorPro\Modules\Lottie\Classes {
    class Caption_Helper
    {
        public function __construct(\ElementorPro\Core\Isolation\Wordpress_Adapter_Interface $wp_adapter, array $settings)
        {
        }
        public function get_caption()
        {
        }
    }
}
namespace ElementorPro\Modules\Lottie {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        /**
         * Get module name.
         *
         * Retrieve the module name.
         *
         * @since  2.7.0
         * @access public
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
        public function get_widgets()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/lottie/assets/scss/frontend.scss`
         * to `/assets/css/widget-lottie.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
        // Fixing wordpress problem when `finfo_file()` returns wrong file type
        public function handle_file_type($file_data, $file, $filename)
        {
        }
        public function register_frontend_scripts()
        {
        }
        public function localize_settings(array $settings)
        {
        }
    }
}
namespace ElementorPro\Modules\Lottie\Widgets {
    class Lottie extends \ElementorPro\Base\Base_Widget
    {
        /**
         * Get element name.
         *
         * Retrieve the element name.
         *
         * @return string The name.
         * @since 2.7.0
         * @access public
         *
         */
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_script_depends()
        {
        }
        public function get_icon()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        public function get_style_depends() : array
        {
        }
        protected function current_user_can_use_external_source()
        {
        }
        protected function get_source_options()
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        protected function content_template()
        {
        }
    }
}
namespace ElementorPro\Modules\OffCanvas {
    class Tag extends \ElementorPro\Modules\DynamicTags\Tags\Base\Tag
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_categories()
        {
        }
        public static function on_import_replace_dynamic_content($config, $map_old_new_post_ids)
        {
        }
        public function register_controls()
        {
        }
        public function render()
        {
        }
        // Keep Empty to avoid default advanced section
        protected function register_advanced_section()
        {
        }
    }
    class Module extends \ElementorPro\Base\Module_Base
    {
        const FEATURE_ID = 'off-canvas';
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        protected function get_widgets()
        {
        }
        public static function is_active()
        {
        }
        public function register_tag(\Elementor\Core\DynamicTags\Manager $dynamic_tags)
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/off-canvas/assets/scss/frontend.scss`
         * to `/assets/css/widget-off-canvas.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\OffCanvas\Widgets {
    class Off_Canvas extends \Elementor\Modules\NestedElements\Base\Widget_Nested_Base
    {
        use \ElementorPro\Base\Base_Widget_Trait;
        const WIDGET_ID = 'Off_Canvas';
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function get_categories()
        {
        }
        // TODO: Replace this check with `is_active_feature` on 3.28.0 to support is_active_feature second parameter.
        public function show_in_panel()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function get_default_children_elements()
        {
        }
        protected function get_default_repeater_title_setting_key()
        {
        }
        protected function register_controls()
        {
        }
        protected function register_content_tab()
        {
        }
        protected function register_style_tab()
        {
        }
        protected function get_default_children_placeholder_selector()
        {
        }
        protected function register_layout_section()
        {
        }
        protected function register_settings_section()
        {
        }
        protected function register_background_controls()
        {
        }
        protected function register_overlay_controls()
        {
        }
        protected function render()
        {
        }
        protected function content_template()
        {
        }
        protected function add_wrapper_attributes()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
    }
}
namespace ElementorPro\Modules\QueryControl\Classes {
    /**
     * Class Elementor_Post_Query
     * Wrapper for WP_Query.
     * Used by the various widgets for generating the query, according to the controls added using Group_Control_Query.
     * Each class instance is associated with the specific widget that is passed in the class constructor.
     */
    class Elementor_Post_Query
    {
        /** @var Widget_Base */
        protected $widget;
        protected $query_args;
        protected $prefix;
        protected $widget_settings;
        /**
         * Elementor_Post_Query constructor.
         *
         * @param Widget_Base $widget
         * @param string $group_query_name
         * @param array $query_args
         */
        public function __construct($widget, $group_query_name, $query_args = [])
        {
        }
        /**
         * 1) build query args
         * 2) invoke callback to fine-tune query-args
         * 3) generate WP_Query object
         * 4) if no results & fallback is set, generate a new WP_Query with fallback args
         * 5) return WP_Query
         *
         * @return \WP_Query
         */
        public function get_query()
        {
        }
        protected function get_query_defaults()
        {
        }
        public function get_query_args()
        {
        }
        protected function set_pagination_args()
        {
        }
        protected function set_common_args()
        {
        }
        protected function set_post_include_args()
        {
        }
        protected function set_post_exclude_args()
        {
        }
        protected function set_avoid_duplicates()
        {
        }
        protected function set_terms_args()
        {
        }
        protected function build_terms_query_include($control_id)
        {
        }
        protected function build_terms_query_exclude($control_id)
        {
        }
        protected function build_terms_query($tab_id, $control_id, $exclude = false)
        {
        }
        protected function insert_tax_query($terms, $exclude)
        {
        }
        protected function set_author_args()
        {
        }
        protected function set_order_args()
        {
        }
        protected function set_date_args()
        {
        }
        /**
         * @param string $control_name
         *
         * @return mixed|null
         */
        protected function get_widget_settings($control_name)
        {
        }
        /**
         * @param       $key
         * @param       $value
         * @param false $override
         */
        protected function set_query_arg($key, $value, $override = false)
        {
        }
        /**
         * @param string    $value
         * @param mixed     $maybe_array
         *
         * @return bool
         */
        protected function maybe_in_array($value, $maybe_array)
        {
        }
        /**
         * @param \WP_Query $wp_query
         */
        public function pre_get_posts_query_filter($wp_query)
        {
        }
        /**
         * @param \WP_Query $query
         */
        public function fix_query_offset(&$query)
        {
        }
        /**
         * @param int       $found_posts
         * @param \WP_Query $query
         *
         * @return int
         */
        public function fix_query_found_posts($found_posts, $query)
        {
        }
    }
    class Elementor_Related_Query extends \ElementorPro\Modules\QueryControl\Classes\Elementor_Post_Query
    {
        /**
         * Elementor_Post_Query constructor.
         *
         * @param Widget_Base $widget
         * @param string $group_query_name
         * @param array $query_args
         * @param array $fallback_args
         */
        public function __construct($widget, $group_query_name, $query_args = [], $fallback_args = [])
        {
        }
        /**
         * 1) build query args
         * 2) invoke callback to fine-tune query-args
         * 3) generate WP_Query object
         * 4) if no results & fallback is set, generate a new WP_Query with fallback args
         * 5) return WP_Query
         *
         * @return \WP_Query
         */
        public function get_query()
        {
        }
        protected function get_fallback_query($original_query)
        {
        }
        protected function set_common_args()
        {
        }
        protected function set_post_exclude_args()
        {
        }
        protected function build_terms_query_include($control_id)
        {
        }
        protected function set_author_args()
        {
        }
        /**
         * @return string|array
         */
        public function get_post_types()
        {
        }
        protected function set_fallback_query_args()
        {
        }
    }
}
namespace ElementorPro\Modules\QueryControl\Controls {
    class Template_Query extends \Elementor\Control_Select2
    {
        const CONTROL_ID = 'template_query';
        /**
         * @return string
         */
        public function get_type()
        {
        }
        public function content_template()
        {
        }
        /**
         * @return array
         */
        protected function get_default_settings()
        {
        }
    }
    class Group_Control_Query extends \Elementor\Group_Control_Base
    {
        protected static $presets;
        protected static $fields;
        public static function get_type()
        {
        }
        protected function init_args($args)
        {
        }
        protected function init_fields()
        {
        }
        protected function get_fields_array($name)
        {
        }
        /**
         * Build the group-controls array
         * Note: this method completely overrides any settings done in Group_Control_Posts
         * @param string $name
         *
         * @return array
         */
        protected function init_fields_by_name($name)
        {
        }
        /**
         * Presets: filter controls subsets to be be used by the specific Group_Control_Query instance.
         *
         * Possible values:
         * 'full' : (default) all presets
         * 'include' : the 'include' tab - by id, by taxonomy, by author
         * 'exclude': the 'exclude' tab - by id, by taxonomy, by author
         * 'advanced_exclude': extend the 'exclude' preset with 'avoid-duplicates' & 'offset'
         * 'date': date query controls
         * 'pagination': posts per-page
         * 'order': sort & ordering controls
         * 'query_id': allow saving a specific query for future usage.
         *
         * Usage:
         * full: build a Group_Controls_Query with all possible controls,
         * when 'full' is passed, the Group_Controls_Query will ignore all other preset values.
         * $this->add_group_control(
         * Group_Control_Query::get_type(),
         * [
         * ...
         * 'presets' => [ 'full' ],
         *  ...
         *  ] );
         *
         * Subset: build a Group_Controls_Query with subset of the controls,
         * in the following example, the Query controls will set only the 'include' & 'date' query args.
         * $this->add_group_control(
         * Group_Control_Query::get_type(),
         * [
         * ...
         * 'presets' => [ 'include', 'date' ],
         *  ...
         *  ] );
         */
        protected static function init_presets()
        {
        }
        protected function prepare_fields($fields)
        {
        }
        protected function get_child_default_args()
        {
        }
        protected function get_default_options()
        {
        }
        protected function get_tabs_keys($name)
        {
        }
    }
    class Group_Control_Related extends \ElementorPro\Modules\QueryControl\Controls\Group_Control_Query
    {
        public static function get_type()
        {
        }
        /**
         * Build the group-controls array
         * Note: this method completely overrides any settings done in Group_Control_Posts
         * @param string $name
         *
         * @return array
         */
        protected function init_fields_by_name($name)
        {
        }
        protected function get_supported_taxonomies()
        {
        }
        protected static function init_presets()
        {
        }
    }
    class Group_Control_Taxonomy extends \ElementorPro\Modules\QueryControl\Controls\Group_Control_Query
    {
        public static function get_type()
        {
        }
        protected function get_fields_array($name)
        {
        }
    }
    /**
     * Class Group_Control_Posts
     *
     * @deprecated 2.5.0 Use `Group_Control_Query` and `Elementor_Post_Query` classes instead.
     */
    class Group_Control_Posts extends \Elementor\Group_Control_Base
    {
        const INLINE_MAX_RESULTS = 15;
        protected static $fields;
        public static function get_type()
        {
        }
        /**
         * @deprecated 2.4.0 Use Control's settings `export=false` instead.
         *
         * @param $element
         * @param $control_id
         *
         * @return mixed
         */
        public static function on_export_remove_setting_from_element($element, $control_id)
        {
        }
        protected function init_fields()
        {
        }
        protected function prepare_fields($fields)
        {
        }
        protected function get_default_options()
        {
        }
        protected function fix_offset($query_args, $settings, $prefix = '')
        {
        }
        protected function build_query_args($settings, $control_id_prefix)
        {
        }
        public function get_query_args($control_id_prefix, $settings)
        {
        }
    }
    class Query extends \Elementor\Control_Select2
    {
        public function get_type()
        {
        }
        /**
         * 'query' can be used for passing query args in the structure and format used by WP_Query.
         * @return array
         */
        protected function get_default_settings()
        {
        }
        /**
         * Update control settings using mapping config
         *
         * @param $value
         * @param array $control_args
         * @param array $config
         *
         * @return mixed
         */
        public function on_import_update_settings($value, array $control_args, array $config)
        {
        }
    }
}
namespace ElementorPro\Modules\QueryControl {
    class Module extends \ElementorPro\Base\Module_Base
    {
        const QUERY_CONTROL_ID = 'query';
        const AUTOCOMPLETE_ERROR_CODE = 'QueryControlAutocomplete';
        const GET_TITLES_ERROR_CODE = 'QueryControlGetTitles';
        // Supported objects for query:
        const QUERY_OBJECT_POST = 'post';
        const QUERY_OBJECT_TAX = 'tax';
        const QUERY_OBJECT_AUTHOR = 'author';
        const QUERY_OBJECT_USER = 'user';
        const QUERY_OBJECT_LIBRARY_TEMPLATE = 'library_template';
        const QUERY_OBJECT_ATTACHMENT = 'attachment';
        // Objects that are manipulated by js (not sent in AJAX):
        const QUERY_OBJECT_CPT_TAX = 'cpt_tax';
        const QUERY_OBJECT_JS = 'js';
        public static $displayed_ids = [];
        public function __construct()
        {
        }
        public static function add_to_avoid_list($ids)
        {
        }
        public static function get_avoid_list_ids()
        {
        }
        public function get_query_ignoring_avoid_list($loop_widget, $query_name, $query_args)
        {
        }
        /**
         * @deprecated 2.5.0 Use `Group_Control_Query` class capabilities instead.
         *
         * @param Widget_Base $widget
         */
        public static function add_exclude_controls($widget)
        {
        }
        public function get_name()
        {
        }
        /**
         * @deprecated 2.6.0 use new `autocomplete` format
         *
         * @param $data
         *
         * @return array
         * @throws \Exception
         */
        public function ajax_posts_filter_autocomplete_deprecated($data)
        {
        }
        /**
         * @throws \Exception
         */
        public static function verify_user_access_for_editing(array $data) : void
        {
        }
        /**
         * @param array $data
         *
         * @return array
         * @throws \Exception
         */
        public function ajax_posts_filter_autocomplete(array $data)
        {
        }
        /**
         * @param $request
         *
         * @return array
         * @throws \Exception
         * @deprecated 2.6.0 use new `autocomplete` format
         *
         */
        public function ajax_posts_control_value_titles_deprecated($request)
        {
        }
        /**
         * @throws \Exception
         */
        public function ajax_posts_control_value_titles($request)
        {
        }
        public function register_controls(\Elementor\Controls_Manager $controls_manager)
        {
        }
        /**
         * @deprecated 2.5.0 Use `Elementor_Post_Query` class capabilities instead.
         *
         * @param string $control_id
         * @param array $settings
         *
         * @return array
         */
        public function get_query_args($control_id, $settings)
        {
        }
        /**
         * @param \Elementor\Widget_Base $widget
         * @param string $name
         * @param array $query_args
         * @param array $fallback_args
         *
         * @return \WP_Query
         */
        public function get_query($widget, $name, $query_args = [], $fallback_args = [])
        {
        }
        /**
         * @param Ajax $ajax_manager
         */
        public function register_ajax_actions($ajax_manager)
        {
        }
        protected function add_actions()
        {
        }
    }
}
namespace ElementorPro\Modules\CallToAction {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        public function get_widgets()
        {
        }
        public function get_name()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/call-to-action/assets/scss/frontend.scss`
         * to `/assets/css/widget-call-to-action.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\CallToAction\Widgets {
    class Call_To_Action extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        /**
         * Render Call to Action widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
}
namespace ElementorPro\Modules\Pricing {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        public function get_widgets()
        {
        }
        /**
         * Get the base URL for assets.
         *
         * @return string
         */
        public function get_assets_base_url() : string
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/pricing/assets/scss/frontend.scss`
         * to `/assets/css/widget-pricing.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace ElementorPro\Modules\Pricing\Widgets {
    class Price_Table extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        /**
         * Render Price Table widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
        public function get_group_name()
        {
        }
    }
    class Price_List extends \ElementorPro\Base\Base_Widget
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        /**
         * Render Price List widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
        public function get_group_name()
        {
        }
    }
}
namespace ElementorPro\Modules\DisplayConditions\Classes {
    class Cache_Notice
    {
        const OPTION_NAME_DC_CACHE_NOTICE_DISMISSED = 'elementor_pro_dc_cache_notice_dismissed';
        const NOTICE_STATUS_YES = 'yes';
        public function should_show_notice() : bool
        {
        }
        public function set_notice_status() : bool
        {
        }
    }
}
namespace ElementorPro\Modules\DisplayConditions\Classes\DynamicTags {
    interface Data_Provider
    {
        /**
         * @param array $args
         * @return string | bool
         */
        public function get_value(array $args);
        public function get_control_options() : array;
    }
    class Dynamic_Tags_Data_Provider implements \ElementorPro\Modules\DisplayConditions\Classes\DynamicTags\Data_Provider
    {
        /**
         * Build the dynamic tags options for the control. Add the groups and the items.
         *
         * @return array
         */
        public function get_control_options() : array
        {
        }
        public function get_default_control_option() : string
        {
        }
        /**
         * @param string $key
         * @return array
         */
        public function get_dynamic_tag_options(string $key) : array
        {
        }
        /**
         * @return array
         */
        public function get_dynamic_tags_options() : array
        {
        }
        /**
         * @param array $args
         * @return string | bool
         */
        public function get_value(array $args)
        {
        }
    }
    class Custom_Fields_Data_Provider implements \ElementorPro\Modules\DisplayConditions\Classes\DynamicTags\Data_Provider
    {
        const CUSTOM_FIELDS_META_LIMIT = 500;
        /**
         * Build the custom fields options for the control. Add the groups and the items.
         *
         * @return array
         */
        public function get_control_options() : array
        {
        }
        /**
         * @param array $args
         *
         * @return string | bool
         */
        public function get_value(array $args)
        {
        }
    }
}
namespace ElementorPro\Modules\DisplayConditions\Classes {
    class Or_Condition
    {
        public function __construct($conditions_manager, $sets)
        {
        }
        public function check()
        {
        }
    }
    class And_Condition
    {
        public function __construct($conditions_manager, $conditions)
        {
        }
        public function check()
        {
        }
    }
    class Conditions_Manager
    {
        public function __construct($display_conditions_module)
        {
        }
        /**
         * @param Condition_Base $instance
         * @return false|void
         */
        public function register_condition_instance(\ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base $instance)
        {
        }
        /**
         * Add condition group.
         *
         * Register new group for the condition.
         *
         * @access public
         *
         * @param string $group_name       Group name.
         * @param array  $group_properties Group properties.
         */
        public function add_group($group_name, $group_properties)
        {
        }
        /**
         * @param $id
         *
         * @return Condition_Base|bool
         */
        public function get_condition($id)
        {
        }
        public function get_conditions_config()
        {
        }
        public function register_conditions()
        {
        }
    }
    class Comparators_Checker
    {
        /**
         * @param string $comparator
         * @param string|DateTime $value_to_check
         * @param string|DateTime $set_value
         *
         * @return bool
         */
        public static function check_date_time(string $comparator, $value_to_check, $set_value) : bool
        {
        }
        public static function check_array_contains(string $comparator, array $expected_values, array $array_of_values) : bool
        {
        }
        public static function check_string_contains(string $comparator, string $expected_value, string $actual_value) : bool
        {
        }
        public static function check_string_contains_and_empty(string $comparator, string $expected_value, string $actual_value) : bool
        {
        }
        public static function check_equality(string $comparator, string $value, string $compare_to) : bool
        {
        }
        /**
         * @param string $comparator
         * @param int $value
         * @param int $compare_to
         *
         * @return bool
         */
        public static function check_numeric_constraints(string $comparator, int $value, int $compare_to) : bool
        {
        }
    }
    class Comparator_Provider
    {
        public const COMPARATOR_IS = 'is';
        public const COMPARATOR_IS_NOT = 'is_not';
        public const COMPARATOR_IS_ONE_OF = 'is_one_of';
        public const COMPARATOR_IS_NONE_OF = 'is_none_of';
        public const COMPARATOR_CONTAINS = 'contains';
        public const COMPARATOR_NOT_CONTAIN = 'not_contain';
        public const COMPARATOR_IS_BEFORE = 'is_before';
        public const COMPARATOR_IS_AFTER = 'is_after';
        public const COMPARATOR_IS_LESS_THAN_INCLUSIVE = 'is_less_than_inclusive';
        public const COMPARATOR_IS_GREATER_THAN_INCLUSIVE = 'is_greater_than_inclusive';
        public const COMPARATOR_IS_BEFORE_INCLUSIVE = 'is_before_inclusive';
        public const COMPARATOR_IS_AFTER_INCLUSIVE = 'is_after_inclusive';
        public const COMPARATOR_IS_EMPTY = 'is_empty';
        public const COMPARATOR_IS_NOT_EMPTY = 'is_not_empty';
        public static function get_comparators(array $comparators) : array
        {
        }
    }
}
namespace ElementorPro\Modules\DisplayConditions {
    class Module extends \ElementorPro\Base\Module_Base
    {
        const LICENSE_FEATURE_NAME = 'display-conditions';
        public function __construct()
        {
        }
        public static function should_show_promo() : bool
        {
        }
        protected function add_render_actions()
        {
        }
        protected function get_saved_conditions($settings)
        {
        }
        public function before_element_render($element)
        {
        }
        public function after_element_render($element)
        {
        }
        /**
         * @return string
         */
        public function get_name()
        {
        }
        /**
         * @return Classes\Conditions_Manager
         */
        public function get_conditions_manager()
        {
        }
        /**
         * @param Ajax $ajax_manager
         */
        public function register_ajax_actions($ajax_manager)
        {
        }
        public function filter_element_caching_is_dynamic_content($is_dynamic_content, $element_rqw_data, $element_instance)
        {
        }
        /**
         * @return bool
         */
        public static function can_use_display_conditions() : bool
        {
        }
    }
}
namespace ElementorPro\Modules\DisplayConditions\Conditions\Base {
    abstract class Condition_Base extends \Elementor\Controls_Stack
    {
        /**
         * @var Wordpress_Adapter_Interface
         */
        protected $wordpress_adapter;
        public function __construct(array $data = [])
        {
        }
        public abstract function get_label();
        public abstract function get_options();
        public function get_group()
        {
        }
        public function check($args) : bool
        {
        }
        protected function register_controls()
        {
        }
        protected function get_initial_config()
        {
        }
    }
}
namespace ElementorPro\Modules\DisplayConditions\Conditions {
    class In_Tags_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function get_group()
        {
        }
        public function check($args) : bool
        {
        }
        public function get_options()
        {
        }
    }
    class Page_Author_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function get_group()
        {
        }
        public function check($args) : bool
        {
        }
        public function get_options()
        {
        }
    }
    class Post_Author_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Page_Author_Condition
    {
        public function get_name()
        {
        }
        public function get_group()
        {
        }
    }
}
namespace ElementorPro\Modules\DisplayConditions\Conditions\Base {
    abstract class Archive_Condition_Base extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base
    {
        public function __construct($condition_key)
        {
        }
        public abstract function get_name();
        public abstract function get_label();
        protected abstract function get_taxonomy();
        public function get_group() : string
        {
        }
        protected abstract function is_of_taxonomy($args) : bool;
        protected function check_is_of_taxonomy($args)
        {
        }
        public function get_options()
        {
        }
    }
}
namespace ElementorPro\Modules\DisplayConditions\Conditions {
    class Archive_Of_Tag_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Archive_Condition_Base
    {
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        public function get_label() : string
        {
        }
        public function check($args) : bool
        {
        }
        protected function is_of_taxonomy($args) : bool
        {
        }
        protected function get_taxonomy()
        {
        }
    }
}
namespace ElementorPro\Modules\DisplayConditions\Conditions\Base {
    abstract class Date_Condition_Base extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base
    {
        const COMPARATOR_KEY = 'comparator';
        const OPTION_KEY = 'date_type';
        const DATE_FORMAT = 'm-d-Y';
        const OPTION_SERVER = 'server';
        const OPTION_CLIENT = 'client';
        public function __construct($condition_key, $group_key)
        {
        }
        public function get_group()
        {
        }
        /**
         * @return array
         */
        public static function get_time_options() : array
        {
        }
        protected function check_date($args, $date_to_check) : bool
        {
        }
        public function get_options()
        {
        }
    }
}
namespace ElementorPro\Modules\DisplayConditions\Conditions {
    class Date_Of_Modification_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Date_Condition_Base
    {
        const CONDITION_KEY = 'date';
        const GROUP_KEY = 'date';
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function get_group()
        {
        }
        public function __construct()
        {
        }
        public function check($args) : bool
        {
        }
    }
    class User_Role_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base
    {
        const CONDITION_KEY = 'roles';
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function get_group()
        {
        }
        public function check($args) : bool
        {
        }
        public function get_options()
        {
        }
    }
    class Archive_Of_Category_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Archive_Condition_Base
    {
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        public function get_label() : string
        {
        }
        public function check($args) : bool
        {
        }
        protected function is_of_taxonomy($args) : bool
        {
        }
        protected function get_taxonomy()
        {
        }
    }
    class Archive_Of_Author_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base
    {
        public function get_name()
        {
        }
        public function get_label() : string
        {
        }
        public function get_group()
        {
        }
        public function check($args) : bool
        {
        }
        public function get_options()
        {
        }
    }
    class Login_Status_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function get_group()
        {
        }
        public function check($args) : bool
        {
        }
        public function get_options()
        {
        }
    }
}
namespace ElementorPro\Modules\DisplayConditions\Conditions\Base {
    abstract class Title_Condition_Base extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base
    {
        protected abstract function get_query();
        public function check($args) : bool
        {
        }
        public function get_options()
        {
        }
    }
}
namespace ElementorPro\Modules\DisplayConditions\Conditions {
    class Page_Title_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Title_Condition_Base
    {
        public function get_name()
        {
        }
        public function get_group()
        {
        }
        public function get_label()
        {
        }
        protected function get_query()
        {
        }
    }
    class User_Registration_Date_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Date_Condition_Base
    {
        const CONDITION_KEY = 'date';
        const GROUP_KEY = 'user';
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function __construct()
        {
        }
        public function check($args) : bool
        {
        }
    }
    class Date_Of_Publish_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Date_Condition_Base
    {
        const CONDITION_KEY = 'date';
        const GROUP_KEY = 'date';
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        public function get_group()
        {
        }
        public function get_label()
        {
        }
        public function check($args) : bool
        {
        }
    }
    class Featured_Image_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function get_group()
        {
        }
        public function check($args) : bool
        {
        }
        public function get_options()
        {
        }
    }
    class Page_Parent_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function get_group()
        {
        }
        public function check($args) : bool
        {
        }
        public function get_options()
        {
        }
    }
    class From_URL_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base
    {
        public function get_label()
        {
        }
        public function get_options()
        {
        }
        public function get_name()
        {
        }
        public function wp_get_referer()
        {
        }
        public function check($args) : bool
        {
        }
    }
    class Current_Date_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Date_Condition_Base
    {
        const CONDITION_KEY = 'date';
        const GROUP_KEY = 'date';
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function __construct()
        {
        }
        public function check($args) : bool
        {
        }
    }
    class Dynamic_Tags_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base
    {
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function get_group()
        {
        }
        public function check($args) : bool
        {
        }
        public function get_options()
        {
        }
    }
    class Day_Of_The_Week_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base
    {
        const CONDITION_KEY = 'days';
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function get_group()
        {
        }
        public function check($args) : bool
        {
        }
        public function get_options()
        {
        }
    }
    class Time_Of_The_Day_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function get_group()
        {
        }
        public function get_gm_date()
        {
        }
        public function check($args) : bool
        {
        }
        /**
         * @param $date_time_string
         * @return string
         * @throws \Exception
         */
        public function convert_date_time_to_24_hour_format($date_time_string) : string
        {
        }
        public function get_options()
        {
        }
    }
    class Post_Title_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Title_Condition_Base
    {
        public function get_name()
        {
        }
        public function get_group()
        {
        }
        public function get_label()
        {
        }
        protected function get_query()
        {
        }
    }
    class Post_Number_Of_Comments_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function get_group()
        {
        }
        public function check($args) : bool
        {
        }
        public function get_options()
        {
        }
    }
    class In_Categories_Condition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Condition_Base
    {
        public function get_name()
        {
        }
        public function get_label()
        {
        }
        public function get_group()
        {
        }
        public function check($args) : bool
        {
        }
        public function get_options()
        {
        }
    }
}
namespace ElementorPro\Modules\Checklist {
    class Module extends \ElementorPro\Base\Module_Base
    {
        public function __construct(?\ElementorPro\Modules\Checklist\Wordpress_Adapter_Interface $wordpress_adapter = null, ?\Elementor\Core\Isolation\Kit_Adapter_Interface $kit_adapter = null)
        {
        }
        public function get_name() : string
        {
        }
        /**
         * @param array $steps
         * @return \Elementor\Modules\Checklist\Steps\Step_Base[]
         */
        public function replace_steps(array $steps) : array
        {
        }
    }
}
namespace ElementorPro\Modules\Checklist\Steps {
    class Setup_Header extends \Elementor\Modules\Checklist\Steps\Setup_Header
    {
        const STEP_ID = 'setup_header_pro';
        public function __construct($module, $wordpress_adapter = null, $kit_adapter = null)
        {
        }
        public function get_id() : string
        {
        }
        public function is_visible() : bool
        {
        }
        public function get_cta_url() : string
        {
        }
    }
}
namespace ElementorPro\Data {
    // TODO: Move to core.
    class Http_Status
    {
        // Successful responses
        const OK = 200;
        const CREATED = 201;
        const NO_CONTENT = 204;
        // Client error responses
        const BAD_REQUEST = 400;
        const UNAUTHORIZED = 401;
        const FORBIDDEN = 403;
        const NOT_FOUND = 404;
        // Server error responses
        const INTERNAL_SERVER_ERROR = 500;
    }
}
namespace {
    \define('ELEMENTOR_PRO_VERSION', '3.29.0');
    \define('ELEMENTOR_PRO_REQUIRED_CORE_VERSION', '3.27');
    \define('ELEMENTOR_PRO_RECOMMENDED_CORE_VERSION', '3.29');
    \define('ELEMENTOR_PRO__FILE__', __FILE__);
    \define('ELEMENTOR_PRO_PLUGIN_BASE', \plugin_basename(\ELEMENTOR_PRO__FILE__));
    \define('ELEMENTOR_PRO_PATH', \plugin_dir_path(\ELEMENTOR_PRO__FILE__));
    \define('ELEMENTOR_PRO_ASSETS_PATH', \ELEMENTOR_PRO_PATH . 'assets/');
    \define('ELEMENTOR_PRO_MODULES_PATH', \ELEMENTOR_PRO_PATH . 'modules/');
    \define('ELEMENTOR_PRO_URL', \plugins_url('/', \ELEMENTOR_PRO__FILE__));
    \define('ELEMENTOR_PRO_ASSETS_URL', \ELEMENTOR_PRO_URL . 'assets/');
    \define('ELEMENTOR_PRO_MODULES_URL', \ELEMENTOR_PRO_URL . 'modules/');
}
