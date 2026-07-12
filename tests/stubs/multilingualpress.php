<?php

namespace Inpsyde\MultilingualPress\Framework\Database {
    /**
     * Table installer implementation using the WordPress database object.
     */
    class TableInstaller
    {
        /**
         * @param wpdb $wpdb
         * @param Table|null $table
         */
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Framework\Database\Table $table = null)
        {
        }
        /**
         * Installs the given table.
         *
         * @param Table|null $table
         * @return bool
         * @throws InvalidTable If a table was neither passed, nor injected via the constructor.
         */
        public function install(\Inpsyde\MultilingualPress\Framework\Database\Table $table = null): bool
        {
        }
        /**
         * Uninstalls the given table.
         *
         * @param Table|null $table
         * @return bool
         * @throws InvalidTable If a table was neither passed, nor injected via the constructor.
         */
        public function uninstall(\Inpsyde\MultilingualPress\Framework\Database\Table $table = null): bool
        {
        }
        /**
         * Alters the given table with the given query.
         *
         * @param Table $table The table.
         * @param string $alterQuery The "Alter" query.
         * @return void
         * @throws InvalidTable If a table was neither passed, nor injected via the constructor.
         */
        public function alterTable(\Inpsyde\MultilingualPress\Framework\Database\Table $table, string $alterQuery): void
        {
        }
    }
    /**
     * Table string replacer implementation using the WordPress database object.
     */
    class TableStringReplacer
    {
        /**
         * @param wpdb $wpdb
         */
        public function __construct(\wpdb $wpdb)
        {
        }
        /**
         * Replaces one string with another all given columns of the given table at once.
         *
         * @param string $table
         * @param string[] $columns
         * @param string $search
         * @param string $replacement
         * @return int
         */
        public function replace(string $table, array $columns, string $search, string $replacement): int
        {
        }
    }
    /**
     * Table duplicator implementation using the WordPress database object.
     */
    class TableDuplicator
    {
        /**
         * @param \wpdb $db
         */
        public function __construct(\wpdb $db)
        {
        }
        /**
         * Creates a new table that is an exact duplicate of an existing table.
         *
         * @param string $existingTableName
         * @param string $newTableName
         * @return bool
         */
        public function duplicate(string $existingTableName, string $newTableName): bool
        {
        }
    }
    /**
     * Table list implementation using the WordPress database object.
     */
    class TableList
    {
        public const ALL_TABLES_CACHE_KEY = 'allTables';
        /**
         * @param wpdb $db
         * @param Facade $cache
         * @param CacheSettingsRepository $cacheSettingsRepository
         */
        public function __construct(\wpdb $db, \Inpsyde\MultilingualPress\Framework\Cache\Server\Facade $cache, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository $cacheSettingsRepository)
        {
        }
        /**
         * Returns an array with the names of all tables for the site with the given ID.
         * By default will return main site tables.
         *
         * @param int|null $siteId
         * @return string[] of all table names for given site
         * @throws Throwable
         */
        public function allTablesForSite(int $siteId = null): array
        {
        }
        /**
         * Returns an array with the names of all tables.
         *
         * @return string[] of all table names for given site
         * @throws Throwable
         */
        public function allTables(): array
        {
        }
        /**
         * Returns an array with the names of all network tables.
         *
         * @return string[] The array of network table names
         * @throws Throwable
         */
        public function networkTables(): array
        {
        }
        /**
         * Returns an array with the names of all tables for the site with the given ID.
         *
         * @param int $siteId
         * @return string[] The array of site table names
         * @throws Throwable
         */
        public function siteTables(int $siteId): array
        {
        }
        /**
         * Returns a list with the names of all MLP tables.
         *
         * @return string[] The list of all MLP table names.
         */
        public function mlpTableNames(): array
        {
        }
    }
    /**
     * Interface for all tables.
     */
    interface Table
    {
        /**
         * Returns an array with all columns that do not have any default content.
         *
         * @return string[]
         */
        public function columnsWithoutDefaultContent(): array;
        /**
         * Returns the SQL string for the default content.
         *
         * @return string
         */
        public function defaultContentSql(): string;
        /**
         * Returns the SQL string for all (unique) keys.
         *
         * @return string
         */
        public function keysSql(): string;
        /**
         * Returns the table name.
         *
         * @return string
         */
        public function name(): string;
        /**
         * Check if table exists or not
         *
         * @return bool
         */
        public function exists(): bool;
        /**
         * Returns the primary key.
         *
         * @return string
         */
        public function primaryKey(): string;
        /**
         * Returns the table schema as an array with column names as keys and SQL definitions as values.
         *
         * @return string[]
         */
        public function schema(): array;
    }
}
namespace Inpsyde\MultilingualPress\Framework\Database\Exception {
    /**
     * Exception to be thrown when an action is to be performed on an invalid table.
     */
    class InvalidTable extends \Exception
    {
        /**
         * Returns a new exception object.
         *
         * @param string $action
         * @return InvalidTable
         */
        public static function forAction(string $action = 'install'): \Inpsyde\MultilingualPress\Framework\Database\Exception\InvalidTable
        {
        }
    }
    /**
     * Exception to be thrown when an action is to be performed on an table that doesn't exists.
     */
    class NonexistentTable extends \Exception
    {
        /**
         * NonexistentTable constructor.
         * @param string $action
         * @param string $table
         */
        public function __construct(string $action, string $table)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Database {
    /**
     * Table replacer implementations using the WordPress database object.
     */
    class TableReplacer
    {
        /**
         * @param wpdb $wpdb
         */
        public function __construct(\wpdb $wpdb)
        {
        }
        /**
         * Replaces the content of one table with another table's content.
         *
         * @param string $destination
         * @param string $source
         * @param string $whereClause The WHERE condition for filtering rows.
         * @return bool
         */
        public function replace(string $destination, string $source, string $whereClause = ''): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Repository {
    interface EntityRepository
    {
        /**
         * Fetches entities based on the provided query arguments.
         */
        public function fetch(\Inpsyde\MultilingualPress\Framework\Repository\QueryArgs $queryArgs): array;
    }
    interface QueryArgs
    {
        /**
         * Get all query arguments.
         *
         * @return array
         */
        public function args(): array;
    }
    /**
     * Manages arguments for WordPress queries.
     */
    class WpQueryArgs implements \Inpsyde\MultilingualPress\Framework\Repository\QueryArgs
    {
        public function __construct(array $args)
        {
        }
        /**
         * @inerhitDoc
         */
        public function args(): array
        {
        }
        /**
         * Get a specific query argument by key.
         *
         * @param string $key
         * @return mixed
         */
        //phpcs:ignore Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
        public function arg(string $key)
        {
        }
        /**
         * Create a new instance with an added or modified argument.
         *
         * @param string $key
         * @param mixed $value
         * @return self
         */
        public function withArg(string $key, $value): self
        {
        }
        /**
         * Create a new instance with a modified 'orderby' argument.
         *
         * @param array $orderBy
         * @return self
         */
        public function withOrderBy(array $orderBy): self
        {
        }
        /**
         * Create a new instance with an added tax query item.
         *
         * @param array $item
         * @return self
         */
        public function withTaxQueryItem(array $item): self
        {
        }
        /**
         * Create a new instance with an added meta query item.
         *
         * @param array $item
         * @return self
         */
        public function withMetaQueryItem(array $item): self
        {
        }
    }
    /**
     * Fetches posts based on the provided WP_Query arguments.
     */
    class PostRepository
    {
        public function __construct(\Inpsyde\MultilingualPress\Framework\Repository\FiltersCollection $filtersCollection)
        {
        }
        /**
         * Sets up necessary filters, fetches the posts,
         * and then cleans up by removing the filters.
         *
         * @param QueryArgs $queryArgs
         * @return array
         */
        public function fetch(\Inpsyde\MultilingualPress\Framework\Repository\QueryArgs $queryArgs): array
        {
        }
    }
    /**
     * Builds WpQueryArgs objects with default and custom arguments.
     */
    class WpQueryArgsBuilder
    {
        /**
         * Builds a WpQueryArgs object with default and custom arguments.
         *
         * @param array $args
         * @return WpQueryArgs
         */
        public function build(array $args): \Inpsyde\MultilingualPress\Framework\Repository\WpQueryArgs
        {
        }
    }
    /**
     * Represents a collection of instances implementing the Inpsyde\MultilingualPress\Framework\Filter\Filter interface.
     */
    class FiltersCollection extends \ArrayObject
    {
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    /**
     * @method string basename()
     * @method string dirPath()
     * @method string dirUrl()
     * @method string filePath()
     * @method string name()
     * @method string website()
     * @method string version()
     * @method string textDomain()
     * @method string textDomainPath()
     *
     * @template-implements \ArrayAccess<string, mixed>
     */
    class PluginProperties implements \ArrayAccess
    {
        protected const BASENAME = 'basename';
        protected const DIR_PATH = 'dirPath';
        protected const DIR_URL = 'dirUrl';
        protected const FILE_PATH = 'filePath';
        protected const NAME = 'name';
        protected const WEBSITE = 'website';
        protected const VERSION = 'version';
        protected const TEXT_DOMAIN = 'textDomain';
        protected const TEXT_DOMAIN_PATH = 'textDomainPath';
        /**
         * @param string $pluginFilePath
         */
        public function __construct(string $pluginFilePath)
        {
        }
        /**
         * @param string $name
         * @param array $args
         * @return string
         */
        public function __call(string $name, array $args = []): string
        {
        }
        /**
         * Checks if a property with the given name exists.
         *
         * @param string $name
         * @return bool
         */
        public function offsetExists($name): bool
        {
        }
        /**
         * Returns the value of the property with the given name.
         *
         * @param string $offset
         * @return mixed
         * @throws \OutOfRangeException If there is no property with the given name.
         */
        #[\ReturnTypeWillChange]
        public function offsetGet($offset)
        {
        }
        /**
         * Disabled.
         *
         * @inheritdoc
         *
         * @throws \BadMethodCallException
         */
        public function offsetSet($offset, $value): void
        {
        }
        /**
         * Disabled.
         *
         * @inheritdoc
         *
         * @throws \BadMethodCallException
         */
        public function offsetUnset($offset): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Nonce {
    /**
     * Interface for all nonce context implementations.
     *
     * @template-extends \ArrayAccess<string, mixed>
     */
    interface Context extends \ArrayAccess
    {
    }
    trait ReadOnlyContextTrait
    {
        /**
         * @param $name
         * @param $value
         * @psalm-suppress MissingParamType
         * @throws ContextValueManipulationNotAllowed
         */
        public function offsetSet($name, $value): void
        {
        }
        /**
         * @param $name
         * @psalm-suppress MissingParamType
         * @throws ContextValueManipulationNotAllowed
         */
        public function offsetUnset($name): void
        {
        }
    }
    /**
     * Array-based nonce context implementation.
     */
    final class ArrayContext implements \Inpsyde\MultilingualPress\Framework\Nonce\Context
    {
        use \Inpsyde\MultilingualPress\Framework\Nonce\ReadOnlyContextTrait;
        /**
         * @param array $data
         */
        public function __construct(array $data)
        {
        }
        /**
         * @inheritdoc
         */
        public function offsetExists($name): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function offsetGet($offset)
        {
        }
    }
    /**
     * Interface for all nonce implementations.
     */
    interface Nonce
    {
        /**
         * Returns the nonce value.
         *
         * @return string
         */
        public function __toString(): string;
        /**
         * Returns the nonce action.
         *
         * @return string
         */
        public function action(): string;
        /**
         * Checks if the nonce is valid with respect to the given context.
         * Implementation can decide what to do in case of no context given.
         *
         * @param Context|null $context
         * @return bool
         */
        public function isValid(\Inpsyde\MultilingualPress\Framework\Nonce\Context $context = null): bool;
    }
    interface SiteAwareNonce extends \Inpsyde\MultilingualPress\Framework\Nonce\Nonce
    {
        /**
         * Make nonce instance specific for a given site.
         *
         * @param int $siteId
         * @return SiteAwareNonce
         */
        public function withSite(int $siteId): \Inpsyde\MultilingualPress\Framework\Nonce\SiteAwareNonce;
    }
    /**
     * WordPress-specific nonce implementation.
     */
    final class WpNonce implements \Inpsyde\MultilingualPress\Framework\Nonce\SiteAwareNonce
    {
        /**
         * @param string $action
         */
        public function __construct(string $action)
        {
        }
        /**
         * @inheritdoc
         */
        public function withSite(int $siteId): \Inpsyde\MultilingualPress\Framework\Nonce\SiteAwareNonce
        {
        }
        /**
         * @inheritdoc
         */
        public function __toString(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function action(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function isValid(\Inpsyde\MultilingualPress\Framework\Nonce\Context $context = null): bool
        {
        }
    }
    /**
     * Nonce context implementation wrapping around the server request.
     */
    final class ServerRequestContext implements \Inpsyde\MultilingualPress\Framework\Nonce\Context
    {
        use \Inpsyde\MultilingualPress\Framework\Nonce\ReadOnlyContextTrait;
        /**
         * @param ServerRequest|null $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request = null)
        {
        }
        /**
         * @inheritdoc
         */
        public function offsetExists($name): bool
        {
        }
        /**
         * @inheritdoc
         */
        #[\ReturnTypeWillChange]
        public function offsetGet($offset)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Nonce\Exception {
    /**
     * Exception to be thrown when a nonce context value is to be manipulated.
     */
    class ContextValueManipulationNotAllowed extends \Exception
    {
        /**
         * Returns a new exception object.
         *
         * @param string $name
         * @param string $action
         * @return ContextValueManipulationNotAllowed
         */
        public static function forName(string $name, string $action = 'set'): \Inpsyde\MultilingualPress\Framework\Nonce\Exception\ContextValueManipulationNotAllowed
        {
        }
    }
    /**
     * Exception to be thrown when a nonce context value that has not yet been set is to be read from
     * the container.
     */
    class ContextValueNotSet extends \Exception
    {
        /**
         * Returns a new exception object.
         *
         * @param string $name
         * @param string $action
         * @return ContextValueNotSet
         */
        public static function forName(string $name, string $action = 'read'): \Inpsyde\MultilingualPress\Framework\Nonce\Exception\ContextValueNotSet
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Widget\Sidebar {
    /**
     * Trait to be used by all self-registering widget implementations.
     *
     * @see Widget
     */
    trait SelfRegisteringWidgetTrait
    {
        /**
         * Registers the widget.
         *
         * @return bool
         */
        public function register(): bool
        {
        }
    }
    /**
     * Interface for all sidebar widget view implementations.
     */
    interface View
    {
        /**
         * Renders the widget's front end view.
         *
         * @param array $args
         * @param array $instance
         * @param string $idBase
         */
        public function render(array $args, array $instance, string $idBase): void;
    }
    /**
     * Interface for all registrable widget implementations.
     */
    interface Widget
    {
        /**
         * Registers the widget.
         *
         * @return bool
         */
        public function register(): bool;
    }
}
namespace Inpsyde\MultilingualPress\Framework\Widget\Dashboard {
    /**
     * Interface for all dashboard widget view implementations.
     */
    interface View
    {
        /**
         * Renders the widget's view.
         *
         * @param array $widgetInstanceSettings
         */
        public function render(array $widgetInstanceSettings): void;
    }
    class Options
    {
        /**
         * @param string $widgetId
         */
        public function __construct(string $widgetId)
        {
        }
        /**
         * Returns the options for the widget with the given ID.
         *
         * @return array
         */
        public function options(): array
        {
        }
        /**
         * Returns a specific widget option, if available.
         *
         * @param string $name
         * @param mixed $default
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function option(string $name, $default = null)
        {
        }
        /**
         * Saves an array of options for the widget with the given ID.
         *
         * @param array $options
         * @return bool
         */
        public function updateAll(array $options = []): bool
        {
        }
        /**
         * Saves a specific widget option.
         *
         * @param string $name
         * @param mixed $value
         * @return bool
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function update(string $name, $value): bool
        {
        }
    }
    class Widget
    {
        /**
         * @param string $widgetId
         * @param string $widgetName
         * @param View $view
         * @param string $capability
         * @param array $callbackArgs
         * @param callable|null $controlCallback
         */
        public function __construct(string $widgetId, string $widgetName, \Inpsyde\MultilingualPress\Framework\Widget\Dashboard\View $view, string $capability = '', array $callbackArgs = [], callable $controlCallback = null)
        {
        }
        /**
         * Registers the widget.
         *
         * @return bool
         */
        public function register(): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Cache\Driver {
    interface CacheDriver
    {
        public const FOR_NETWORK = 32;
        /**
         * @return bool
         */
        public function isNetwork(): bool;
        /**
         * Reads a value from the cache.
         *
         * @param string $namespace
         * @param string $name
         * @return Value
         */
        public function read(string $namespace, string $name): \Inpsyde\MultilingualPress\Framework\Cache\Item\Value;
        /**
         * Write a value to the cache.
         *
         * @param string $namespace
         * @param string $name
         * @param mixed $value
         * @return bool
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function write(string $namespace, string $name, $value): bool;
        /**
         * Delete a value from the cache.
         *
         * @param string $namespace
         * @param string $name
         * @return bool
         *
         * phpcs:enable
         */
        public function delete(string $namespace, string $name): bool;
    }
    /**
     * Cache driver implementation that vanish with request.
     * Useful in tests or to share things that should never survive a single request
     * without polluting classes with many static variables.
     */
    final class EphemeralCacheDriver implements \Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver
    {
        public const NOOP = 8192;
        /**
         * @param int $flags
         */
        public function __construct(int $flags = 0)
        {
        }
        /**
         * @inheritdoc
         */
        public function isNetwork(): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function read(string $namespace, string $name): \Inpsyde\MultilingualPress\Framework\Cache\Item\Value
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function write(string $namespace, string $name, $value): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function delete(string $namespace, string $name): bool
        {
        }
    }
    final class WpObjectCacheDriver implements \Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver
    {
        /**
         * @param int $flags
         */
        public function __construct(int $flags = 0)
        {
        }
        /**
         * @inheritdoc
         */
        public function isNetwork(): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function read(string $namespace, string $name): \Inpsyde\MultilingualPress\Framework\Cache\Item\Value
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function write(string $namespace, string $name, $value): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function delete(string $namespace, string $name): bool
        {
        }
    }
    /**
     * A driver implementation that uses WordPress transient functions.
     *
     * Two gotchas:
     * 1) when using external object cache, prefer object cache driver, WP will use object cache anyway;
     * 2) avoid to store `false` using this driver because it can't be disguised from a cache miss.
     */
    final class WpTransientDriver implements \Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver
    {
        /**
         * @param int $flags
         */
        public function __construct(int $flags = 0)
        {
        }
        /**
         * @inheritdoc
         */
        public function isNetwork(): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function read(string $namespace, string $name): \Inpsyde\MultilingualPress\Framework\Cache\Item\Value
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function write(string $namespace, string $name, $value): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function delete(string $namespace, string $name): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Cache\Server {
    class ItemLogic
    {
        /**
         * @param string $namespace
         * @param string $key
         */
        public function __construct(string $namespace, string $key)
        {
        }
        /**
         * @return string
         */
        public function namespace(): string
        {
        }
        /**
         * @return string
         */
        public function key(): string
        {
        }
        /**
         * @return callable
         */
        public function updater(): callable
        {
        }
        /**
         * @param array|null $args
         * @return string
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         */
        public function generateItemKey(...$args): string
        {
        }
        /**
         * @return int
         */
        public function timeToLive(): int
        {
        }
        /**
         * @return int
         */
        public function extensionOnFailure(): int
        {
        }
        /**
         * Set the callback that will be used to both generate and update the cache item value.
         *
         * The callback should throw an exception when the generation of the value fails.
         *
         * @param callable $callback
         * @return ItemLogic
         */
        public function updateWith(callable $callback): \Inpsyde\MultilingualPress\Framework\Cache\Server\ItemLogic
        {
        }
        /**
         * Set the callback that will be used to generate an unique key for updater arguments.
         *
         * The callback will receive the "base" key as 1st argument, plus variadically all the updater
         * arguments, and has to return a string that's unique per key (likely should start with it)
         * and per arguments.
         *
         * Please note: the second argument passed to callback could be null.
         *
         * @see ItemLogic::generateItemKey()
         *
         * @param callable $callback
         * @return ItemLogic
         */
        public function generateKeyWith(callable $callback): \Inpsyde\MultilingualPress\Framework\Cache\Server\ItemLogic
        {
        }
        /**
         * Set the time to live for the cached value.
         *
         * @param int $timeToLive
         * @return ItemLogic
         */
        public function liveFor(int $timeToLive): \Inpsyde\MultilingualPress\Framework\Cache\Server\ItemLogic
        {
        }
        /**
         * @param int $extension
         * @return ItemLogic
         */
        public function onFailureExtendFor(int $extension): \Inpsyde\MultilingualPress\Framework\Cache\Server\ItemLogic
        {
        }
    }
    class Server
    {
        public const UPDATING_KEYS_TRANSIENT = 'mlp_cache_server_updating_keys';
        public const SPAWNING_KEYS_TRANSIENT = 'mlp_cache_server_spawning_keys_';
        public const HEADER_KEY = 'Mlp-Cache-Update-Key';
        public const HEADER_TTL = 'Mlp-Cache-Update-TTL';
        protected const VALID_ARG_TYPES = ['boolean', 'integer', 'double', 'string'];
        /**
         * @param CacheFactory $factory
         * @param CacheDriver $driver
         * @param CacheDriver $networkDriver
         * @param ServerRequest $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Cache\CacheFactory $factory, \Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver $driver, \Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver $networkDriver, \Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request)
        {
        }
        /**
         * On regular requests it is possible to register a callback to generates same
         * value to cache and associate it with an unique key in the also given pool.
         * This should be called early, because the values can only be then "claimed"
         * (which is retrieved for actual use) after registration.
         *
         * The value generated will be valid for the given TTL or for the default (1 hour).
         * When value is expired it will be returned anyway, but an update will be scheduled.
         * The scheduled updates happens in separate HEAD requests.
         *
         * It means that once cached for first time a value will be served always from
         * cache (unless manually flushed) and updated automatically on expiration
         * without affecting user request time.
         *
         * @param ItemLogic $itemLogic
         * @return Server
         * @throws Exception\BadCacheItemRegistration
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Cache\Server\ItemLogic $itemLogic): self
        {
        }
        /**
         * @param ItemLogic $itemLogic
         * @return Server
         * @throws Exception\BadCacheItemRegistration
         */
        public function registerForNetwork(\Inpsyde\MultilingualPress\Framework\Cache\Server\ItemLogic $itemLogic): self
        {
        }
        /**
         * Check whether the given pair of namespace and key is registered.
         *
         * @param string $namespace
         * @param string $logicKey
         * @return bool
         */
        public function isRegistered(string $namespace, string $logicKey): bool
        {
        }
        /**
         * @param string $namespace
         * @param string $logicKey
         * @return CachePool
         * @throws Exception\NotRegisteredCacheItem
         * @throws Exception\InvalidCacheDriver
         */
        public function poolForLogic(string $namespace, string $logicKey): \Inpsyde\MultilingualPress\Framework\Cache\Pool\CachePool
        {
        }
        /**
         * On regular requests returns the cached (or just newly generated) value for
         * a registered couple of namespace and key.
         * In case the value is expired, it will be returned anyway, but an updating
         * will be scheduled and will happen in a separate HEAD request and the
         * expired cached value will continue to be served until the value is
         * successfully updated.
         *
         * @param string $namespace
         * @param string $key
         * @param array|null $args
         * @return mixed
         * @throws Exception\NotRegisteredCacheItem
         * @throws Exception\InvalidCacheArgument
         * @throws Exception\InvalidCacheDriver
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function claim(string $namespace, string $key, array $args = null)
        {
        }
        /**
         * Once cached for first time values continue to be served from cache
         * (automatically updated on expiration) unless this method is called to
         * force flush of a specific namespace / key pair or of a whole namespace.
         *
         * @param string $namespace
         * @param string $key
         * @param array|null $args
         * @return bool
         * @throws Exception\NotRegisteredCacheItem
         * @throws Exception\InvalidCacheDriver
         */
        public function flush(string $namespace, string $key, array $args = null): bool
        {
        }
        /**
         * When an expired value is requested, it is returned to claiming code, and
         * an HTTP HEAD request is sent to home page containing headers with
         * information about key and the TTL.
         * This methods check them and if the request fits criteria update the value
         * using the registered callable.
         */
        public function listenSpawn(): void
        {
        }
        /**
         * @param string $namespace
         * @param string $key
         * @param array|null $args
         * @return bool
         */
        public function isQueuedForUpdate(string $namespace, string $key, array $args = null): bool
        {
        }
    }
    class Facade
    {
        /**
         * @param Server $server
         * @param string $namespace
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Cache\Server\Server $server, string $namespace)
        {
        }
        /**
         * Wrapper for server get.
         *
         * @param string $key
         * @param mixed $args
         * @return mixed
         * @throws Exception\NotRegisteredCacheItem
         * @throws Exception\InvalidCacheArgument
         * @throws Exception\InvalidCacheDriver
         *
         * @see Server::claim()
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         */
        public function claim(string $key, ...$args)
        {
        }
        /**
         * Wrapper for server flush.
         *
         * @param string $key
         * @param array|null $args
         * @return bool
         * @throws Exception\NotRegisteredCacheItem
         * @throws Exception\InvalidCacheDriver
         *
         * @see Server::flush()
         */
        public function flush(string $key, array $args = null): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Cache\Item {
    interface CacheItem
    {
        public const LIFETIME_IN_SECONDS = \HOUR_IN_SECONDS;
        /**
         * Cache item key.
         *
         * @return string
         */
        public function key(): string;
        /**
         * Cache item value.
         *
         * @return mixed
         */
        public function value();
        /**
         * Check if the cache item was a hit. Necessary to disguise null values stored in cache.
         *
         * @return bool
         */
        public function isHit(): bool;
        /**
         * Check if the cache item is expired.
         *
         * @return bool
         */
        public function isExpired(): bool;
        /**
         * Sets the value for the cache item.
         *
         * @param mixed $value
         * @return bool
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function fillWith($value): bool;
        /**
         * Delete the cache item from its storage and ensure that next value() call return null.
         *
         * @return bool
         *
         * phpcs:enable
         */
        public function delete(): bool;
        /**
         * Sets a specific time to live for the item.
         *
         * @param int $ttl
         * @return CacheItem
         */
        public function liveFor(int $ttl): \Inpsyde\MultilingualPress\Framework\Cache\Item\CacheItem;
        /**
         * Push values to storage driver.
         *
         * @return bool
         */
        public function syncToStorage(): bool;
        /**
         * Load values from storage driver.
         *
         * @return bool
         */
        public function syncFromStorage(): bool;
    }
    /**
     * A complete multi-driver cache item.
     */
    final class WpCacheItem implements \Inpsyde\MultilingualPress\Framework\Cache\Item\CacheItem
    {
        /**
         * @param CacheDriver $driver
         * @param string $key
         * @param string $group
         * @param int|null $timeToLive
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver $driver, string $key, string $group = '', int $timeToLive = null)
        {
        }
        /**
         * Before the object vanishes its storage its updated if needs to.
         */
        public function __destruct()
        {
        }
        /**
         * @inheritdoc
         */
        public function key(): string
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function fillWith($value): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function isHit(): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function isExpired(): bool
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function value()
        {
        }
        /**
         * @inheritdoc
         */
        public function delete(): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function liveFor(int $ttl): \Inpsyde\MultilingualPress\Framework\Cache\Item\CacheItem
        {
        }
        /**
         * @inheritdoc
         */
        public function syncToStorage(): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function syncFromStorage(): bool
        {
        }
    }
    final class Value
    {
        /**
         * @param mixed $value
         * @param bool $hit
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function __construct($value = null, bool $hit = \false)
        {
        }
        /**
         * @return bool
         */
        public function isHit(): bool
        {
        }
        /**
         * @return mixed|null
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function value()
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Cache {
    /**
     * A factory for Cache pool objects.
     */
    class CacheFactory
    {
        /**
         * @param string $prefix
         * @param string $poolDefaultClass
         */
        public function __construct(string $prefix = '', string $poolDefaultClass = \Inpsyde\MultilingualPress\Framework\Cache\Pool\WpCachePool::class)
        {
        }
        /**
         * @return string
         */
        public function prefix(): string
        {
        }
        /**
         * @param string $namespace
         * @param CacheDriver|null $driver
         * @return CachePool
         */
        public function create(string $namespace, \Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver $driver = null): \Inpsyde\MultilingualPress\Framework\Cache\Pool\CachePool
        {
        }
        /**
         * @param string $namespace
         * @param CacheDriver|null $driver
         * @return CachePool
         * @throws InvalidCacheDriver If a site-specific is used instead of a network one.
         */
        public function createForNetwork(string $namespace, \Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver $driver = null): \Inpsyde\MultilingualPress\Framework\Cache\Pool\CachePool
        {
        }
        /**
         * @param string $namespace
         * @return CachePool
         */
        public function createEthereal(string $namespace): \Inpsyde\MultilingualPress\Framework\Cache\Pool\CachePool
        {
        }
        /**
         * @param string $namespace
         * @return CachePool
         */
        public function createEtherealForNetwork(string $namespace): \Inpsyde\MultilingualPress\Framework\Cache\Pool\CachePool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Cache\Pool {
    /**
     * Interface for all cache pool implementations.
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    interface CachePool
    {
        /**
         * Return pool namespace.
         *
         * @return string
         */
        public function namespace(): string;
        /**
         * Check if the cache pool is for network.
         *
         * @return bool
         */
        public function isNetwork(): bool;
        /**
         * Fetches a value from the cache.
         *
         * @param string $key
         * @return CacheItem
         */
        public function item(string $key): \Inpsyde\MultilingualPress\Framework\Cache\Item\CacheItem;
        /**
         * Fetches a value from the cache.
         *
         * The difference between `$pool->get($key)` and `$pool->item($key)->value()` is that the latter
         * could return a cached value even if expired, and it is responsibility of the caller check for
         * that if necessary. Moreover, `get()` also has a default param that is returned in case cache
         * is a miss or is expired.
         *
         * @param string $key
         * @param mixed|null $default
         * @return mixed
         */
        public function valueOfKey(string $key, $default = null);
        /**
         * Fetches a value from the cache.
         *
         * The "bulk" version of `get()`.
         *
         * @param string[] $keys
         * @param mixed|null $default
         * @return array
         */
        public function valuesOfKeys(array $keys, $default = null): array;
        /**
         * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
         *
         * @param string $key
         * @param mixed $value
         * @param null|int $ttl
         * @return CacheItem
         */
        public function cache(string $key, $value = null, int $ttl = null): \Inpsyde\MultilingualPress\Framework\Cache\Item\CacheItem;
        /**
         * Delete an item from the cache by its unique key.
         *
         * @param string $key
         * @return bool
         */
        public function delete(string $key): bool;
        /**
         * Determines whether an item is present in the cache.
         *
         * A true outcome does not provide warranty the value is not expired.
         *
         * @param string $key
         * @return bool
         */
        public function has(string $key): bool;
    }
    final class WpCachePool implements \Inpsyde\MultilingualPress\Framework\Cache\Pool\CachePool
    {
        /**
         * @param string $namespace
         * @param CacheDriver $driver
         */
        public function __construct(string $namespace, \Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver $driver)
        {
        }
        /**
         * @inheritdoc
         */
        public function namespace(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function isNetwork(): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function item(string $key): \Inpsyde\MultilingualPress\Framework\Cache\Item\CacheItem
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function valueOfKey(string $key, $default = null)
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function valuesOfKeys(array $keys, $default = null): array
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function cache(string $key, $value = null, int $ttl = null): \Inpsyde\MultilingualPress\Framework\Cache\Item\CacheItem
        {
        }
        /**
         * @inheritdoc
         */
        public function delete(string $key): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function has(string $key): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Cache\Exception {
    class Exception extends \Exception
    {
    }
    class NotRegisteredCacheItem extends \Inpsyde\MultilingualPress\Framework\Cache\Exception\Exception
    {
        /**
         * @param string $namespace
         * @param string $key
         * @return NotRegisteredCacheItem
         */
        public static function forNamespaceAndKey(string $namespace, string $key): \Inpsyde\MultilingualPress\Framework\Cache\Exception\NotRegisteredCacheItem
        {
        }
    }
    class InvalidCacheArgument extends \Inpsyde\MultilingualPress\Framework\Cache\Exception\Exception
    {
        /**
         * @param string $namespace
         * @param string $key
         * @return InvalidCacheArgument
         */
        public static function forNamespaceAndKey(string $namespace, string $key): \Inpsyde\MultilingualPress\Framework\Cache\Exception\InvalidCacheArgument
        {
        }
    }
    class InvalidCacheDriver extends \Inpsyde\MultilingualPress\Framework\Cache\Exception\Exception
    {
        public const SITE_DRIVER_AS_NETWORK = 1;
        /**
         * @param CacheDriver $driver
         * @return InvalidCacheDriver
         */
        public static function forSiteDriverAsNetwork(\Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver $driver): \Inpsyde\MultilingualPress\Framework\Cache\Exception\InvalidCacheDriver
        {
        }
    }
    class BadCacheItemRegistration extends \Inpsyde\MultilingualPress\Framework\Cache\Exception\Exception
    {
        /**
         * @return BadCacheItemRegistration
         */
        public static function forWrongTiming(): \Inpsyde\MultilingualPress\Framework\Cache\Exception\BadCacheItemRegistration
        {
        }
        /**
         * @param string $key
         * @return BadCacheItemRegistration
         */
        public static function forKeyUsedForNetwork(string $key): \Inpsyde\MultilingualPress\Framework\Cache\Exception\BadCacheItemRegistration
        {
        }
        /**
         * @param string $key
         * @return BadCacheItemRegistration
         */
        public static function forKeyUsedForSite(string $key): \Inpsyde\MultilingualPress\Framework\Cache\Exception\BadCacheItemRegistration
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Auth {
    /**
     * @package MultilingualPress
     * @license http://opensource.org/licenses/MIT MIT
     */
    interface Auth
    {
        /**
         * Check if the current http request is authorized
         *
         * @return bool
         */
        public function isAuthorized(): bool;
    }
    /**
     * Class TermAuth
     * @package Inpsyde\MultilingualPress\Framework\Auth
     */
    final class TermAuth implements \Inpsyde\MultilingualPress\Framework\Auth\Auth
    {
        /**
         * @param WP_Term $term
         * @param Nonce $nonce
         */
        public function __construct(\WP_Term $term, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function isAuthorized(): bool
        {
        }
    }
    /**
     * Class AuthFactory
     * @package Inpsyde\MultilingualPress\Framework\Auth
     */
    class AuthFactory
    {
        /**
         * Create and Auth instance
         *
         * @param Nonce $nonce
         * @param Capability $capability
         * @return Auth
         */
        public function create(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Framework\Auth\Capability $capability): \Inpsyde\MultilingualPress\Framework\Auth\Auth
        {
        }
    }
    /**
     * @package MultilingualPress
     * @license http://opensource.org/licenses/MIT MIT
     */
    final class PostAuth implements \Inpsyde\MultilingualPress\Framework\Auth\Auth
    {
        /**
         * @param WP_Post $post
         * @param Nonce $nonce
         */
        public function __construct(\WP_Post $post, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function isAuthorized(): bool
        {
        }
    }
    /**
     * Interface Capability
     * @package Inpsyde\MultilingualPress\Framework\Http\Auth
     */
    interface Capability
    {
        /**
         * Check if a capability is valid
         *
         * @return bool True if valid, false otherwise
         */
        public function isValid(): bool;
    }
    /**
     * Class WpUserCapability
     * @package Inpsyde\MultilingualPress\Framework\Http\Auth
     */
    class WpUserCapability implements \Inpsyde\MultilingualPress\Framework\Auth\Capability
    {
        /**
         * WpCurrentUserCapability constructor
         * @param WP_User $user
         * @param string $capability
         * @param int $id
         */
        public function __construct(\WP_User $user, string $capability, int $id)
        {
        }
        /**
         * @inheritDoc
         */
        public function isValid(): bool
        {
        }
    }
    /**
     * Class EntityAuthFactory
     * @package Inpsyde\MultilingualPress\Framework\Http\Auth
     */
    class EntityAuthFactory
    {
        /**
         * @param Entity $entity
         * @param Nonce $nonce
         * @return Auth|null
         * @throws AuthFactoryException
         */
        public function create(\Inpsyde\MultilingualPress\Framework\Entity $entity, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce): ?\Inpsyde\MultilingualPress\Framework\Auth\Auth
        {
        }
    }
    /**
     * @package MultilingualPress
     * @license http://opensource.org/licenses/MIT MIT
     */
    final class CommentAuth implements \Inpsyde\MultilingualPress\Framework\Auth\Auth
    {
        /**
         * @param WP_Comment $comment
         * @param Nonce $nonce
         */
        public function __construct(\WP_Comment $comment, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function isAuthorized(): bool
        {
        }
    }
    /**
     * Class AuthFactoryException
     * @package Inpsyde\MultilingualPress\Framework\Auth
     */
    class AuthFactoryException extends \RuntimeException
    {
        /**
         * Create a new Exception because Entity is not valid
         *
         * @return AuthFactoryException
         */
        public static function becauseEntityIsInvalid(): self
        {
        }
    }
    /**
     * Class RequestAuth
     * @package Inpsyde\MultilingualPress\Framework\Http\Auth
     */
    final class RequestAuth implements \Inpsyde\MultilingualPress\Framework\Auth\Auth
    {
        /**
         * Validator constructor
         * @param Nonce $nonce
         * @param $capability
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Framework\Auth\Capability $capability)
        {
        }
        /**
         * @inheritDoc
         */
        public function isAuthorized(): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    /**
     * Class Filesystem
     * @package Inpsyde\MultilingualPress\Framework
     */
    class Filesystem
    {
        public const ACTION_INIT_CREDENTIALS = 'multilingualpress.init_filesystem_credentials';
        public const FILTER_CREDENTIALS_CONTEXT = 'multilingualpress.filesystem_context';
        /**
         * @return string
         */
        public static function forceDirect(): string
        {
        }
        /**
         * @return bool
         */
        public static function forceCredentials(): bool
        {
        }
        /**
         * @return void
         */
        public static function removeForceFilters(): void
        {
        }
        /**
         * @param string $source
         * @param string $destination
         * @param int $mode
         *
         * @return bool
         */
        public function copy(string $source, string $destination, int $mode = null): bool
        {
        }
        /**
         * @param string $source
         * @param string $destination
         * @param int $mode
         *
         * @return bool
         */
        public function copyIfNotExist(string $source, string $destination, int $mode = null): bool
        {
        }
        /**
         * @param string $source
         * @param string $destination
         *
         * @return bool
         */
        public function move(string $source, string $destination): bool
        {
        }
        /**
         * @param string $source
         * @param string $destination
         *
         * @return bool
         */
        public function moveIfNotExist(string $source, string $destination): bool
        {
        }
        /**
         * @param string $filepath
         *
         * @return bool
         */
        public function deleteFile(string $filepath): bool
        {
        }
        /**
         * @param string $path
         *
         * @return bool
         */
        public function deleteFolder(string $path): bool
        {
        }
        /**
         * @param string $filepath
         *
         * @return bool
         */
        public function pathExists(string $filepath): bool
        {
        }
        /**
         * @param string $filepath
         *
         * @return bool
         */
        public function isFile(string $filepath): bool
        {
        }
        /**
         * @param string $path
         *
         * @return bool
         */
        public function isDir(string $path): bool
        {
        }
        /**
         * @param string $filepath
         *
         * @return bool
         */
        public function isReadable(string $filepath): bool
        {
        }
        /**
         * @param string $path
         * @param int $mode
         *
         * @return bool
         */
        //phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
        public function mkDirP(string $path, int $mode = null): bool
        {
        }
        /**
         * @param string $path
         * @param int $mode
         *
         * @return bool
         */
        public function mkDir(string $path, int $mode = null): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Integration {
    /**
     * Interface for all integration controllers.
     */
    interface Integration
    {
        /**
         * Integrates some (possibly external) service with MultilingualPress.
         */
        public function integrate(): void;
    }
}
namespace Inpsyde\MultilingualPress\Framework\Module {
    class ModuleManager
    {
        public const MODULE_STATE_ACTIVE = 1;
        public const MODULE_STATE_ALL = 0;
        public const MODULE_STATE_INACTIVE = 2;
        public const OPTION = 'multilingualpress_modules';
        /**
         * @param string $option
         */
        public function __construct(string $option)
        {
        }
        /**
         * Activates the module with the given ID.
         *
         * @param string $id
         * @return Module
         * @throws InvalidModule If there is no module with the given ID.
         */
        public function activateById(string $id): \Inpsyde\MultilingualPress\Framework\Module\Module
        {
        }
        /**
         * Deactivates the module with the given ID.
         *
         * @param string $id
         * @return Module
         * @throws InvalidModule If there is no module with the given ID.
         */
        public function deactivateById(string $id): \Inpsyde\MultilingualPress\Framework\Module\Module
        {
        }
        /**
         * Checks if any modules have been registered.
         *
         * @return bool
         */
        public function isManagingAnything(): bool
        {
        }
        /**
         * Checks if the module with the given ID has been registered.
         *
         * @param string $id
         * @return bool
         */
        public function isManagingModule(string $id): bool
        {
        }
        /**
         * Checks if the module with the given ID is active.
         *
         * @param string $id
         * @return bool
         */
        public function isModuleActive(string $id): bool
        {
        }
        /**
         * Returns the module with the given ID.
         *
         * @param string $id
         * @return Module
         * @throws InvalidModule If there is no module with the given ID.
         */
        public function moduleOfId(string $id): \Inpsyde\MultilingualPress\Framework\Module\Module
        {
        }
        /**
         * Returns all modules with the given state.
         *
         * @param int $state
         * @return Module[]
         */
        public function modulesByState(int $state = self::MODULE_STATE_ALL): array
        {
        }
        /**
         * Registers the given module.
         *
         * @param Module $module
         * @return bool
         * @throws ModuleAlreadyRegistered
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Module\Module $module): bool
        {
        }
        /**
         * Saves the modules persistently.
         *
         * @return bool
         */
        public function persistModules(): bool
        {
        }
        /**
         * Unregisters the module with the given.
         *
         * @param string $moduleId
         * @return Module[]
         */
        public function unregisterById(string $moduleId): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Service {
    /**
     * Interface for all service provider implementations to be used for dependency management.
     */
    interface ServiceProvider
    {
        /**
         * Registers the provided services on the given container.
         *
         * @param Container $container
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void;
    }
}
namespace Inpsyde\MultilingualPress\Framework\Module {
    /**
     * Interface for all module service provider implementations to be used for dependency management.
     */
    interface ModuleServiceProvider extends \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        /**
         * Registers the module at the module manager.
         *
         * @param ModuleManager $moduleManager
         * @return bool
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool;
        /**
         * Performs various tasks on module activation.
         *
         * @param Container $container
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void;
    }
    /**
     * When pointed to a directory of modules, locates module files in that directory.
     *
     * @template-implements IteratorAggregate<string, SplFileInfo>
     */
    class FileLocator implements \IteratorAggregate
    {
        /**
         * The base directory to look for files in.
         */
        protected string $baseDir;
        /**
         * The name of the module file.
         */
        protected string $moduleFileName;
        /**
         * The maximal directory depth to scan into.
         */
        protected int $maxDepth;
        public function __construct(string $baseDir, string $moduleFileName, int $maxDepth)
        {
        }
        /**
         * {@inheritdoc}
         *
         * @return Traversable<string, SplFileInfo>
         * @throws Exception If problem retrieving internal iterator.
         * {@see https://youtrack.jetbrains.com/issue/WI-44884}.
         */
        public function getIterator(): \Traversable
        {
        }
        /**
         * Retrieves paths of module files.
         *
         * @throws Exception If problem retrieving paths.
         *
         * @return Traversable<string, SplFileInfo> The list of file name paths.
         */
        protected function moduleFilesPaths(): \Traversable
        {
        }
        /**
         * Determines whether or not a module file is valid.
         *
         * @param SplFileInfo $fileInfo The file to filter.
         *
         * @return bool True if the file is valid for inclusion; false otherwise.
         */
        protected function filterFile(\SplFileInfo $fileInfo): bool
        {
        }
        /**
         * Creates a recursive directory iterator.
         *
         * @param string $dir Path to the directory to iterate over.
         *
         * @throws UnexpectedValueException If the directory cannot be accessed.
         *
         * @return RecursiveIteratorIterator<RecursiveDirectoryIterator> The iterator that will recursively
         * iterate over items in the specified directory.
         */
        protected function createRecursiveDirectoryIterator(string $dir): \RecursiveIteratorIterator
        {
        }
        /**
         * Filters a list of items by applying a callback.
         *
         * @param Traversable<string, SplFileInfo> $list The list to filter.
         * @param callable(SplFileInfo, string, Iterator): bool $callback The callback criteria to use for filtering.
         *
         * @return Traversable<string, SplFileInfo> The list of items from the iterator that match the criteria.
         */
        protected function filterList(\Traversable $list, callable $callback): \Traversable
        {
        }
        /**
         * Finds the deepest iterator that matches.
         *
         * Because the given traversable can be an {@see IteratorAggregate},
         * it will try to get its inner iterator.
         *
         * phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
         * @link https://github.com/Dhii/iterator-helper-base/blob/v0.1-alpha2/src/ResolveIteratorCapableTrait.php
         * phpcs:enable
         * Ported from here.
         *
         * @param Traversable<string, SplFileInfo> $iterator The iterator to resolve.
         * @param int         $limit    The depth limit for resolution.
         *
         * @throws OutOfRangeException      If infinite recursion is detected.
         * @throws UnexpectedValueException If the iterator could not be resolved within
         *                                  the depth limit.
         *
         * @return Iterator<string, SplFileInfo> The inner-most iterator, or whatever the test function allows.
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function resolveIterator(\Traversable $iterator, int $limit = 100): \Iterator
        {
        }
    }
    /**
     * Something able to performs various tasks on module activation.
     */
    interface Activator
    {
        /**
         * Performs various tasks on module activation.
         *
         * @param Container $container
         */
        public function activate(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void;
    }
    /**
     * Locates modules in module files.
     *
     * @template-implements IteratorAggregate<ServiceProvider>
     */
    class ModuleLocator implements \IteratorAggregate
    {
        /**
         * The list of module file paths to load.
         *
         */
        protected \Traversable $moduleFiles;
        /**
         * @param Traversable $moduleFiles The list of module definition file paths.
         */
        public function __construct(\Traversable $moduleFiles)
        {
        }
        /**
         * {@inheritdoc}
         *
         * @return ArrayIterator
         *
         * @throws Throwable If problem locating modules.
         */
        #[\ReturnTypeWillChange]
        public function getIterator()
        {
        }
        /**
         * Retrieves a list of modules.
         *
         * @throws Throwable If problem locating modules.
         *
         * @return ServiceProvider[] A list of modules.
         */
        protected function locate(): array
        {
        }
        /**
         * Retrieves the list of module files.
         *
         * @return Traversable The list of absolute paths to module definition files.
         */
        protected function moduleFiles(): \Traversable
        {
        }
        /**
         * Creates a module defined by the specified file.
         *
         * @param string $filePath The path to the file which defines the module.
         *
         * @throws UnexpectedValueException If the file defined by the specified path does not exist
         * or is not readable.
         * @throws RangeException If the file does not contain a valid module definition.
         * @throws RuntimeException If problem creating module.
         *
         * @return ServiceProvider The module defined in the file.
         */
        protected function createModule(string $filePath): \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
        {
        }
    }
    final class Module
    {
        /**
         * @param string $id
         * @param array $data
         */
        public function __construct(string $id, array $data = [])
        {
        }
        /**
         * Activates the module.
         *
         * @return Module
         */
        public function activate(): \Inpsyde\MultilingualPress\Framework\Module\Module
        {
        }
        /**
         * Deactivates the module.
         *
         * @return Module
         */
        public function deactivate(): \Inpsyde\MultilingualPress\Framework\Module\Module
        {
        }
        /**
         * Returns the description of the module.
         *
         * @return string
         */
        public function description(): string
        {
        }
        /**
         * Returns the ID of the module.
         *
         * @return string
         */
        public function id(): string
        {
        }
        /**
         * Checks if the module is active.
         *
         * @return bool
         */
        public function isActive(): bool
        {
        }
        /**
         * Module is not able to be activated
         *
         * @return bool
         */
        public function isDisabled(): bool
        {
        }
        /**
         * Returns the name of the module.
         *
         * @return string
         */
        public function name(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Module\Exception {
    /**
     * Exception to be thrown when a module that does not exist is to be manipulated.
     */
    class InvalidModule extends \Exception
    {
        /**
         * Returns a new exception object.
         *
         * @param string $moduleId
         * @param string $action
         * @return InvalidModule
         */
        public static function forId(string $moduleId, string $action = 'read'): self
        {
        }
    }
    /**
     * Exception to be thrown when a module that has already been registered is to be manipulated.
     */
    class ModuleAlreadyRegistered extends \Exception
    {
        /**
         * Returns a new exception object.
         *
         * @param string $moduleId
         * @param string $action
         * @return ModuleAlreadyRegistered
         */
        public static function forId(string $moduleId, string $action = 'register'): self
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Admin {
    class SettingsPage
    {
        public const ADMIN_NETWORK = 1;
        public const ADMIN_SITE = 0;
        public const ADMIN_USER = 2;
        public const PARENT_APPEARANCE = 'themes.php';
        public const PARENT_COMMENTS = 'edit-comments.php';
        public const PARENT_DASHBOARD = 'index.php';
        public const PARENT_LINKS = 'link-manager.php';
        public const PARENT_MEDIA = 'upload.php';
        public const PARENT_NETWORK_SETTINGS = 'settings.php';
        public const PARENT_PAGES = 'edit.php?post_type=page';
        public const PARENT_PLUGINS = 'plugins.php';
        public const PARENT_POSTS = 'edit.php';
        public const PARENT_SETTINGS = 'options-general.php';
        public const PARENT_SITES = 'sites.php';
        public const PARENT_THEMES = 'themes.php';
        public const PARENT_TOOLS = 'tools.php';
        public const PARENT_USER_PROFILE = 'profile.php';
        public const PARENT_USERS = 'users.php';
        public const PARENT_MULTILINGUALPRESS = 'multilingualpress';
        /**
         * @param int $admin
         * @param string $pageTitle
         * @param string $menuTitle
         * @param string $capability
         * @param string $menuSlug
         * @param SettingsPageView $view
         * @param string $iconUrl
         * @param int|null $position
         */
        final public function __construct(int $admin, string $pageTitle, string $menuTitle, string $capability, string $menuSlug, \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView $view, string $iconUrl = '', int $position = null)
        {
        }
        /**
         * @param int $admin
         * @param string $parent
         * @param string $pageTitle
         * @param string $menuTitle
         * @param string $capability
         * @param string $menuSlug
         * @param SettingsPageView $view
         * @return SettingsPage
         */
        public static function withParent(int $admin, string $parent, string $pageTitle, string $menuTitle, string $capability, string $menuSlug, \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView $view): \Inpsyde\MultilingualPress\Framework\Admin\SettingsPage
        {
        }
        /**
         * @return string
         */
        public function capability(): string
        {
        }
        /**
         * @return string
         */
        public function hookName(): string
        {
        }
        /**
         * @return bool
         */
        public function register(): bool
        {
        }
        /**
         * @return string
         */
        public function menuSlug(): string
        {
        }
        /**
         * @return string
         */
        public function pageTitle(): string
        {
        }
        /**
         * Returns the full URL.
         *
         * @return string
         */
        public function url(): string
        {
        }
    }
    /**
     * Interface for all settings page tab data implementations.
     */
    interface SettingsPageTabDataAccess
    {
        /**
         * Returns the capability.
         *
         * @return string
         */
        public function capability(): string;
        /**
         * Returns the ID.
         *
         * @return string
         */
        public function id(): string;
        /**
         * Returns the slug.
         *
         * @return string
         */
        public function slug(): string;
        /**
         * Returns the title.
         *
         * @return string
         */
        public function title(): string;
    }
    /**
     * Settings page tab.
     */
    class SettingsPageTab implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabDataAccess
    {
        /**
         * @param SettingsPageTabDataAccess $data
         * @param SettingsPageView $view
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabDataAccess $data, \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView $view)
        {
        }
        /**
         * @inheritdoc
         */
        public function capability(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function data(): \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabDataAccess
        {
        }
        /**
         * @inheritdoc
         */
        public function id(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function slug(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
        /**
         * Returns the view object.
         *
         * @return SettingsPageView
         */
        public function view(): \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
        {
        }
    }
    /**
     * Interface for all settings page view implementations.
     */
    interface SettingsPageView
    {
        /**
         * Renders the markup.
         */
        public function render(): void;
    }
    /**
     * Settings page tab data structure.
     */
    final class SettingsPageTabData implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabDataAccess
    {
        /**
         * @param string $id
         * @param string $title
         * @param string $slug
         * @param string $capability
         */
        public function __construct(string $id, string $title, string $slug, string $capability = '')
        {
        }
        /**
         * @inheritdoc
         */
        public function capability(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function id(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function slug(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
    /**
     * Represents the custom column for MultilingualPress in admin entity list view.
     */
    interface TranslationColumnInterface
    {
        /**
         * The name of the column.
         *
         * @return string
         */
        public function name(): string;
        /**
         * The title of the column.
         *
         * @return string
         */
        public function title(): string;
        /**
         * The value of the column for given entity ID.
         *
         * @param int $id The entity ID.
         * @return string
         */
        public function value(int $id): string;
    }
    /**
     * Tab for all Edit Site pages.
     */
    class EditSiteTab
    {
        /**
         * @param SettingsPageTab $tab
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTab $tab)
        {
        }
        /**
         * Registers both the tab and the settings page for the tab.
         *
         * @return bool
         * @throws Throwable
         */
        public function register(): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Admin\Metabox {
    /**
     * @template-implements ArrayAccess<mixed, mixed>
     */
    final class Info implements \ArrayAccess
    {
        public const CONTEXT_SIDE = 'side';
        /**
         * @param string $title
         * @param string $id
         * @param string $context
         * @param string $priority
         */
        public function __construct(string $title, string $id = '', string $context = '', string $priority = '')
        {
        }
        /**
         * @return string
         */
        public function id(): string
        {
        }
        /**
         * @return string
         */
        public function title(): string
        {
        }
        /**
         * @return string
         */
        public function context(): string
        {
        }
        /**
         * @return string
         */
        public function priority(): string
        {
        }
        /**
         * @param int|string $offset
         */
        public function offsetExists($offset): bool
        {
        }
        /**
         * @param int|string $offset
         * @return mixed
         */
        #[\ReturnTypeWillChange]
        public function offsetGet($offset)
        {
        }
        /**
         * @param int|string|null $offset
         * @param mixed $value
         */
        #[\ReturnTypeWillChange]
        public function offsetSet($offset, $value): void
        {
        }
        /**
         * @param int|string $offset
         */
        #[\ReturnTypeWillChange]
        public function offsetUnset($offset): void
        {
        }
    }
    /**
     * Can render post metaboxes.
     */
    interface PostMetaboxRendererInterface
    {
        /**
         * Renders the markup for given post ID.
         *
         * @param int $postId The post ID.
         */
        public function render(int $postId): void;
    }
    /**
     * @package MultilingualPress
     * @license http://opensource.org/licenses/MIT MIT
     */
    interface Metabox
    {
        public const SAVE = 'save';
        public const SHOW = 'show';
        /**
         * @param string $showOrSave
         * @param Entity $entity
         * @return Info
         */
        public function createInfo(string $showOrSave, \Inpsyde\MultilingualPress\Framework\Entity $entity): \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info;
        /**
         * Returns the site ID for the meta box.
         * @return int
         */
        public function siteId(): int;
        /**
         * Create an instance of Action for the given entity.
         *
         * @param Entity $entity
         * @return Action
         */
        public function action(\Inpsyde\MultilingualPress\Framework\Entity $entity): \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action;
        /**
         * Check if the given entity is a valid one to be in the metabox.
         *
         * @param Entity $entity
         * @return bool true if is valid, otherwise false.
         */
        public function isValid(\Inpsyde\MultilingualPress\Framework\Entity $entity): bool;
        /**
         * Create the metabox view for a given entity.
         *
         * @param Entity $entity
         * @return View
         */
        public function view(\Inpsyde\MultilingualPress\Framework\Entity $entity): \Inpsyde\MultilingualPress\Framework\Admin\Metabox\View;
    }
    interface Action
    {
        /**
         * @inheritdoc
         */
        public function save(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices): bool;
    }
    final class NoopAction implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
    {
        /**
         * @inheritdoc
         */
        public function save(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    /**
     * Trait SwitchSiteHelper
     * @package Inpsyde\MultilingualPress\Framework
     */
    trait SwitchSiteTrait
    {
        /**
         * @param int $targetSiteId
         * @return int
         */
        protected function maybeSwitchSite(int $targetSiteId): int
        {
        }
        /**
         * @param int $originalSiteId
         * @return bool
         */
        protected function maybeRestoreSite(int $originalSiteId): bool
        {
        }
        /**
         * Switching between blogs doesn't update the locale.
         * This ensures that the loaded locale matches the
         * locale of the current blog.
         *
         * @see https://core.trac.wordpress.org/ticket/49263
         * @param int $siteId
         * @return void
         */
        private function updateLocaleToMatchSite(int $siteId): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Admin\Metabox {
    /**
     * Class MetaboxUpdater
     * @package Inpsyde\MultilingualPress\Framework\Admin\Metabox
     */
    class MetaboxUpdater
    {
        use \Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
        public const ACTION_UNAUTHORIZED_METABOX_SAVE = 'multilingualpress.unauthorized_box_save';
        public const ACTION_SAVE_METABOX = 'multilingualpress.save_metabox';
        public const ACTION_SAVED_METABOX = 'multilingualpress.saved_metabox';
        /**
         * MetaboxUpdater constructor.
         * @param Request $request
         * @param PersistentAdminNotices $notices
         * @param MetaboxAuthFactory $metaboxAuthFactory
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices, \Inpsyde\MultilingualPress\Framework\Admin\Metabox\MetaboxAuthFactory $metaboxAuthFactory)
        {
        }
        /**
         * Save Metabox Data for the given Entity
         *
         * @param Metabox $metabox
         * @param string $metaboxId
         * @param Entity $entity
         * @throws AuthFactoryException
         * @throws DomainException
         */
        public function saveMetabox(\Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metabox $metabox, string $metaboxId, \Inpsyde\MultilingualPress\Framework\Entity $entity): void
        {
        }
        /**
         * Create instance of Action by the given metabox for the given Entity
         *
         * @param Entity $entity
         * @param Metabox $metabox
         * @return Action
         * @throws DomainException
         */
        protected function actionFactory(\Inpsyde\MultilingualPress\Framework\Entity $entity, \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metabox $metabox): \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
        {
        }
    }
    class Metaboxes
    {
        public const REGISTER_METABOXES = 'multilingualpress.register_metaboxes';
        public const ACTION_INSIDE_METABOX_AFTER = 'multilingualpress.inside_box_after';
        public const ACTION_INSIDE_METABOX_BEFORE = 'multilingualpress.inside_box_before';
        public const ACTION_SHOW_METABOXES = 'multilingualpress.show_metaboxes';
        public const ACTION_SHOWED_METABOXES = 'multilingualpress.showed_metaboxes';
        public const ACTION_SAVE_METABOXES = 'multilingualpress.save_metaboxes';
        public const ACTION_SAVED_METABOXES = 'multilingualpress.saved_metaboxes';
        public const FILTER_SAVE_METABOX_ON_EMPTY_POST = 'multilingualpress.metabox_save_on_empty_post';
        public const FILTER_METABOX_ENABLED = 'multilingualpress.metabox_enabled';
        protected const FILTER_TAXONOMY_METABOXES_ORDER = 'multilingualpress.taxonomy_metaboxes_order';
        protected \Inpsyde\MultilingualPress\Core\PostTypeRepository $postTypeRepository;
        protected \Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\RequestGlobalsManipulator $globalsManipulator, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices, \Inpsyde\MultilingualPress\Framework\Admin\Metabox\MetaboxUpdater $metaboxUpdater, \Inpsyde\MultilingualPress\Core\PostTypeRepository $postTypeRepository, \Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager)
        {
        }
        /**
         * @return void
         */
        public function init(): void
        {
        }
        /**
         * @param Metabox[] $boxes
         *
         * @return Metaboxes
         */
        public function addBox(\Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metabox ...$boxes): \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metaboxes
        {
        }
        /**
         * WordPress does not print metaboxes for terms, let's fix this.
         *
         * @param WP_Term $term
         */
        public function printTermBoxes(\WP_Term $term): void
        {
        }
        /**
         * @param WP_Comment $comment
         */
        protected function onCommentSave(\WP_Comment $comment): void
        {
        }
        /**
         * Add the metaboxes for given entity.
         *
         * @param Entity $entity
         */
        protected function addMetaBoxes(\Inpsyde\MultilingualPress\Framework\Entity $entity): void
        {
        }
        /**
         * Perform metabox saving actions for given entity.
         *
         * @param Entity $entity
         */
        protected function saveMetaboxesActions(\Inpsyde\MultilingualPress\Framework\Entity $entity): void
        {
        }
    }
    interface View
    {
        /**
         * @param Info $info
         * @return void
         */
        public function render(\Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info $info): void;
    }
    /**
     * Class MetaboxAuthFactory
     * @package Inpsyde\MultilingualPress\Framework\Admin\Metabox
     */
    class MetaboxAuthFactory
    {
        /**
         * MetaboxAuthFactory constructor.
         * @param EntityAuthFactory $entityAuthFactory
         * @param NonceFactory $nonceFactory
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Auth\EntityAuthFactory $entityAuthFactory, \Inpsyde\MultilingualPress\Framework\Factory\NonceFactory $nonceFactory)
        {
        }
        /**
         * Create an instance of Auth by the given Entity
         *
         * @param Entity $entity
         * @param string $metaboxId
         * @param int $metaboxSiteId
         * @return Auth|null
         * @throws AuthFactoryException
         */
        public function create(\Inpsyde\MultilingualPress\Framework\Entity $entity, string $metaboxId, int $metaboxSiteId): ?\Inpsyde\MultilingualPress\Framework\Auth\Auth
        {
        }
    }
    final class NoopView implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\View
    {
        /**
         * @inheritdoc
         */
        public function render(\Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info $info): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Admin {
    /**
     * Model for a custom column in the Sites list table in the Network Admin.
     */
    class SitesListTableColumn
    {
        /**
         * @param string $columnName
         * @param string $columnLabel
         * @param callable $renderCallback
         */
        public function __construct(string $columnName, string $columnLabel, callable $renderCallback)
        {
        }
        /**
         * Registers the column methods by using the appropriate WordPress hooks.
         */
        public function register(): void
        {
        }
        /**
         * Renders the column content.
         *
         * @param string $column
         * @param int $siteId
         * @return void
         */
        public function renderContent(string $column, int $siteId): void
        {
        }
    }
    /**
     * Model for an admin notice.
     */
    class AdminNotice
    {
        public const DISMISSIBLE = 8192;
        public const IN_ALL_SCREENS = 128;
        public const IN_NETWORK_SCREENS = 256;
        public const IN_USER_SCREENS = 512;
        public const IN_DEFAULT_SCREENS = 1024;
        public const TYPE_SUCCESS = 1;
        public const TYPE_ERROR = 2;
        public const TYPE_INFO = 4;
        public const TYPE_WARNING = 8;
        public const TYPE_MULTILINGUALPRESS = 16;
        public const HOOKS = [self::IN_ALL_SCREENS => 'all_admin_notices', self::IN_DEFAULT_SCREENS => 'admin_notices', self::IN_NETWORK_SCREENS => 'network_admin_notices', self::IN_USER_SCREENS => 'user_admin_notices'];
        protected const CLASSES = [self::TYPE_ERROR => 'notice-error', self::TYPE_WARNING => 'notice-warning', self::TYPE_SUCCESS => 'notice-success', self::TYPE_INFO => 'notice-info', self::TYPE_MULTILINGUALPRESS => 'notice-multilingualpress'];
        protected const KSES_ALLOWED = ['a' => ['href' => [], 'title' => [], 'class' => [], 'style' => [], 'target' => []], 'br' => [], 'em' => [], 'strong' => [], 'div' => ['style' => [], 'class' => []]];
        /**
         * @param string[] $content
         * @return AdminNotice
         */
        public static function error(string ...$content): \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @param string[] $content
         * @return AdminNotice
         */
        public static function info(string ...$content): \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @param string[] $content
         * @return AdminNotice
         */
        public static function success(string ...$content): \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @param string[] $content
         * @return AdminNotice
         */
        public static function warning(string ...$content): \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @param string[] $content
         * @return AdminNotice
         */
        public static function multilingualpress(string ...$content): \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @param int|null $flags
         * @param string $title
         * @param string[] $content
         */
        final public function __construct(int $flags = null, string $title = '', string ...$content)
        {
        }
        /**
         * @return AdminNotice
         */
        public function makeDismissible(): \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @return AdminNotice
         */
        public function inAllScreens(): \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @return AdminNotice
         */
        public function inDefaultScreens(): \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @return AdminNotice
         */
        public function inNetworkScreens(): \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @return AdminNotice
         */
        public function inUserScreens(): \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @param string $title
         * @return AdminNotice
         */
        public function withTitle(string $title): \Inpsyde\MultilingualPress\Framework\Admin\AdminNotice
        {
        }
        /**
         * @return void
         */
        public function render(): void
        {
        }
        /**
         * @return void
         */
        public function renderNow(): void
        {
        }
        /**
         * @return string
         */
        public function action(): string
        {
        }
    }
    class PersistentAdminNotices
    {
        public const OPTION_NAME = 'multilingualpress_notices_';
        protected const DEFAULT_TTL = 300;
        public const FILTER_ADMIN_NOTICE_TTL = 'multilingualpress.admin_notice_ttl';
        /**
         * @return void
         */
        public function init(): void
        {
        }
        /**
         * @param AdminNotice $notice
         * @param string|null $onlyOnScreen
         * @return PersistentAdminNotices
         */
        public function add(\Inpsyde\MultilingualPress\Framework\Admin\AdminNotice $notice, string $onlyOnScreen = null): \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices
        {
        }
        /**
         * @wp-hook admin_notices
         */
        public function doDefaultNotices(): void
        {
        }
        /**
         * @wp-hook network_admin_notices
         */
        public function doNetworkNotices(): void
        {
        }
        /**
         * @wp-hook user_admin_notices
         */
        public function doUserNotices(): void
        {
        }
        /**
         * @wp-hook all_admin_notices
         */
        public function doAllNotices(): void
        {
        }
        /**
         * Store (or delete) messages on shutdown.
         *
         * @return bool
         */
        public function record(): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Content {
    /**
     * Replaces blocks in post content with updated versions.
     */
    class BlockReplacer
    {
        /**
         * Compares the original blocks with their updated versions, and replaces them in the post content if they differ.
         *
         * @psalm-param array<array> $originBlocks
         * @psalm-param array<array> $updatedBlocks
         */
        public function replaceBlocks(string $postContent, array $originBlocks, array $updatedBlocks): string
        {
        }
    }
    /**
     * Class BlockCopier
     *
     * Copy СА block's content to remote site.
     */
    class BlockCopier
    {
        /**
         * @param BlockDataCopier $blockDataCopier
         * @param BlockParser $blockParser
         * @param BlockReplacer $blockReplacer
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Content\BlockDataCopier $blockDataCopier, \Inpsyde\MultilingualPress\Framework\Content\BlockParser $blockParser, \Inpsyde\MultilingualPress\Framework\Content\BlockReplacer $blockReplacer)
        {
        }
        /**
         * Handle the copy of blocks
         *
         * This method is a callback for the MetaboxAction::FILTER_CONTENT_BEFORE_UPDATE_REMOTE_POST filter.
         * It receives the post content, finds blocks with fields containing attachments, copies the attachments,
         * and updates the post content with the remote attachment IDs.
         *
         * @wp-hook multilingualpress.content_before_update_remote_post
         *
         * @param string $postContent The content to parse for blocks.
         * @param RelationshipContext $context Relationship context data object.
         * @return string Updated content.
         */
        public function handleCopyBlocks(string $postContent, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): string
        {
        }
    }
    interface BlockDataCopier
    {
        public function handleBlockData(array $block, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): array;
    }
    /**
     * Class AttachmentCopier
     *
     * Copy attachment(s) from source site to the remote site.
     */
    class AttachmentCopier
    {
        protected \Inpsyde\MultilingualPress\Attachment\Copier $copier;
        public function __construct(\Inpsyde\MultilingualPress\Attachment\Copier $copier)
        {
        }
        /**
         * The method copies the attachment from the source site to the remote site.
         *
         * @param RelationshipContext $context Relationship context data object.
         * @param int $attachmentId The attachment ID that should be copied to the remote entity.
         * @return int The attachment ID in remote site which is copied from source site.
         */
        public function copySingleAttachment(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, int $attachmentId): int
        {
        }
        /**
         * The method copies the attachments from the source site to the remote site.
         *
         * @param RelationshipContext $context Relationship context data object.
         * @param array $attachmentIds The list of attachment IDs that should be copied to the remote entity.
         * @return array The list of the attachment IDs in remote site which are copied from source site.
         */
        public function copyAttachments(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, array $attachmentIds): array
        {
        }
    }
    class Attachment
    {
        public function __construct(int $attachmentId)
        {
        }
        public function attachmentId(): int
        {
        }
        public function attachmentUrl(): ?string
        {
        }
        public function attachmentPageUrl(): ?string
        {
        }
        public function attachmentDirectoryUrl(): ?string
        {
        }
        public function isValid(): bool
        {
        }
    }
    class BlockParser
    {
        public function findBlocks(string $postContent): array
        {
        }
        /**
         * Find blocks in the post content that match a given condition.
         *
         * @psalm-param callable(array):bool $isBlockMatched
         */
        protected function findMatchedBlocks(string $postContent, callable $isBlockMatched): array
        {
        }
        /**
         * Verify if the block has valid data.
         */
        protected function isBlockValid(array $block): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Message {
    /**
     * Represents a factory of messages.
     */
    interface MessageFactoryInterface
    {
        /**
         * Create a new instance of a Message.
         *
         * @param string $type The message type. This is determined by module semantics.
         * @param string $content The message content.
         * @param array $data Structured data of the message.
         * All values in this array must be recursively serializable
         * @return MessageInterface The new message.
         *
         * @throws \InvalidArgumentException If message data is invalid.
         * @throws \Exception If message could not be created.
         */
        public function create(string $type, string $content, array $data): \Inpsyde\MultilingualPress\Framework\Message\MessageInterface;
    }
    /**
     * A factory of Messages.
     *
     * Will create instances of a class that corresponds to the message type,
     * or fall back to a default class if specified, finally throwing.
     */
    class MessageFactory implements \Inpsyde\MultilingualPress\Framework\Message\MessageFactoryInterface
    {
        /**
         * @param array<string, callable> $typeFactories
         * A map of message type codes to their respective factories.
         * Each factory has the following signature:
         * `function (string $code, string $content, array $data)`
         * @param callable|null $fallbackFactory The factory of the class to use if type code doesn't match.
         * Use `null` to indicated that no fallback should be used, and the factory should throw instead.
         */
        public function __construct(array $typeFactories, callable $fallbackFactory = null)
        {
        }
        /**
         * @inheritDoc
         */
        public function create(string $type, string $content, array $data): \Inpsyde\MultilingualPress\Framework\Message\MessageInterface
        {
        }
        /**
         * Retrieves a factory for a message type.
         *
         * @param string $type The code of a message type.
         *
         * @return callable The factory of a message. See {@see __construct()} parameter `$typeFactories`.
         * @throws OutOfRangeException If specified type code is invalid.
         *
         */
        protected function messageFactory(string $type): callable
        {
        }
    }
    /**
     * Interface Message
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    interface MessageInterface
    {
        /**
         * Message Content
         *
         * @return string
         */
        public function content(): string;
        /**
         * Response Data
         *
         * @return array
         */
        public function data(): array;
        /**
         * @return string
         */
        public function type(): string;
        /**
         * @param string $type
         * @return bool
         */
        public function isOfType(string $type): bool;
    }
    /**
     * Class Message
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    class Message implements \Inpsyde\MultilingualPress\Framework\Message\MessageInterface
    {
        /**
         * SuccessMessage constructor.
         * @param string $type
         * @param string $content
         * @param array $data
         * @throws \InvalidArgumentException
         */
        public function __construct(string $type, string $content, array $data)
        {
        }
        /**
         * @inheritDoc
         */
        public function type(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function content(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function data(): array
        {
        }
        /**
         * @inheritDoc
         */
        public function isOfType(string $type): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Language {
    /**
     * Interface for all language data type implementations.
     */
    interface Language
    {
        public const ISO_SHORTEST = 'iso_shortest';
        /**
         * Returns the ID of the language.
         *
         * @return int
         */
        public function id(): int;
        /**
         * Checks if the language is written right-to-left (RTL).
         *
         * @return bool
         */
        public function isRtl(): bool;
        /**
         * Returns the language name.
         *
         * @return string
         */
        public function name(): string;
        /**
         * Returns the language name.
         *
         * @return string
         */
        public function englishName(): string;
        /**
         * Returns the language name.
         *
         * @return string
         */
        public function nativeName(): string;
        /**
         * Returns the language ISO 639 code.
         *
         * @param string $which
         * @return string
         */
        public function isoCode(string $which = self::ISO_SHORTEST): string;
        /**
         * Returns the language name to be used for frontend purposes.
         *
         * @return string
         */
        public function isoName(): string;
        /**
         * Returns the language BCP-47 tag.
         *
         * @return string
         */
        public function bcp47tag(): string;
        /**
         * Returns the language locale.
         *
         * @return string
         */
        public function locale(): string;
        /**
         * Returns the language type.
         *
         * @return string
         */
        public function type(): string;
    }
    /**
     * Null language implementation.
     */
    final class NullLanguage implements \Inpsyde\MultilingualPress\Framework\Language\Language
    {
        /**
         * @inheritdoc
         */
        public function id(): int
        {
        }
        /**
         * @inheritdoc
         */
        public function isRtl(): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function name(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function englishName(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function isoName(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function nativeName(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function isoCode(string $which = self::ISO_SHORTEST): string
        {
        }
        /**
         * @inheritdoc
         */
        public function bcp47tag(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function locale(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function type(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    /**
     * Interface Stringable
     * @package Inpsyde\MultilingualPress\Framework
     */
    interface Stringable
    {
        /**
         * Retrieve String
         *
         * @return string
         */
        public function __toString(): string;
    }
}
namespace Inpsyde\MultilingualPress\Framework\Language {
    /**
     * Trait Bcp47tagValidator
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
     */
    trait Bcp47tagValidator
    {
        /**
         * Pattern to test against
         *
         * @var string
         * phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
         */
        private string $pattern = '/^(?<grandfathered>(?:en-GB-oed|i-(?:ami|bnn|default|enochian|hak|klingon|lux|mingo|navajo|pwn|t(?:a[oy]|su))|sgn-(?:BE-(?:FR|NL)|CH-DE))|(?:art-lojban|cel-gaulish|no-(?:bok|nyn)|zh-(?:guoyu|hakka|min(?:-nan)?|xiang)))|(?:(?<language>(?:[A-Za-z]{2,3}(?:-(?<extlang>[A-Za-z]{3}(?:-[A-Za-z]{3}){0,2}))?)|[A-Za-z]{4}|[A-Za-z]{5,8})(?:-(?<script>[A-Za-z]{4}))?(?:-(?<region>[A-Za-z]{2}|[0-9]{3}))?(?:-(?<variant>[A-Za-z0-9]{5,8}|[0-9][A-Za-z0-9]{3}))*(?:-(?<extension>[0-9A-WY-Za-wy-z](?:-[A-Za-z0-9]{2,8})+))*)(?:-(?<privateUse>x(?:-[A-Za-z0-9]{1,8})+))?$/Di';
        // phpcs:enable
        /**
         * Validate bcp47Tag
         *
         * @param string $bcp47Tag
         * @return bool
         */
        protected function validate(string $bcp47Tag): bool
        {
        }
    }
    /**
     * Class Bcp47Tag
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
     */
    class Bcp47Tag implements \Inpsyde\MultilingualPress\Framework\Stringable
    {
        use \Inpsyde\MultilingualPress\Framework\Language\Bcp47tagValidator;
        /**
         * Bcp47Tag constructor.
         * @param string $bcp47Tag
         * @throws InvalidArgumentException
         */
        public function __construct(string $bcp47Tag)
        {
        }
        /**
         * @return string
         */
        public function __toString(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Translator {
    /**
     * Interface for all translator implementations.
     */
    interface Translator
    {
        /**
         * Returns the translation data for the given site, according to the given arguments.
         *
         * @param int $siteId
         * @param TranslationSearchArgs $args
         * @return Translation
         */
        public function translationFor(int $siteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args): \Inpsyde\MultilingualPress\Framework\Api\Translation;
    }
    /**
     * Null translator implementation.
     */
    final class NullTranslator implements \Inpsyde\MultilingualPress\Framework\Translator\Translator
    {
        /**
         * @inheritdoc
         */
        public function translationFor(int $siteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args): \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Url {
    /**
     * Interface for all URL data type implementations.
     */
    interface Url extends \Inpsyde\MultilingualPress\Framework\Stringable
    {
    }
    /**
     * Class Url
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
     */
    class SimpleUrl implements \Inpsyde\MultilingualPress\Framework\Url\Url
    {
        /**
         * SimpleUrl constructor.
         * @param string $url
         * @throws InvalidArgumentException
         */
        public function __construct(string $url)
        {
        }
        /**
         * @inheritDoc
         */
        public function __toString(): string
        {
        }
    }
    /**
     * Escaped URL data type.
     */
    final class EscapedUrl implements \Inpsyde\MultilingualPress\Framework\Url\Url
    {
        /**
         * @param string $url
         */
        public function __construct(string $url)
        {
        }
        /**
         * Returns the URL string.
         *
         * @return string
         */
        public function __toString(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    class WordpressContext
    {
        public const TYPE_ADMIN = 'admin';
        public const TYPE_HOME = 'home';
        public const TYPE_POST_TYPE_ARCHIVE = 'post-type-archive';
        public const TYPE_SEARCH = 'search';
        public const TYPE_SINGULAR = 'post';
        public const TYPE_TERM_ARCHIVE = 'term';
        public const TYPE_DATE_ARCHIVE = 'date-archive';
        public const TYPE_CUSTOMIZER = 'customizer';
        /**
         * @param \WP_Query|null $wpQuery
         */
        public function __construct(\WP_Query $wpQuery = null)
        {
        }
        /**
         * Returns the (first) post type of the current request.
         *
         * @return string
         */
        public function postType(): string
        {
        }
        /**
         * Returns the ID of the queried object.
         *
         * For term archives, this is the term taxonomy ID (not the term ID).
         *
         * @return int
         */
        public function queriedObjectId(): int
        {
        }
        /**
         * Returns all types of the current request or empty string on failure.
         *
         * @return string[]
         */
        public function types(): array
        {
        }
        /**
         * Returns the type of the current request or empty string on failure.
         *
         * @return string
         */
        public function type(): string
        {
        }
        /**
         * Returns if the current request match given type.
         *
         * @param string $type
         * @return bool
         */
        public function isType(string $type): bool
        {
        }
    }
    /**
     * Storage for the (switched) state of the network.
     */
    class NetworkState
    {
        /**
         * Returns a new instance for the global site ID and switched stack.
         *
         * @return static
         */
        public static function create(): \Inpsyde\MultilingualPress\Framework\NetworkState
        {
        }
        /**
         * Restores the stored site state.
         *
         * @return int
         */
        public function restore(): int
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Http {
    /**
     * Interface for all HTTP request abstraction implementations.
     */
    interface Request
    {
        public const CONNECT = 'CONNECT';
        public const DELETE = 'DELETE';
        public const GET = 'GET';
        public const HEAD = 'HEAD';
        public const OPTIONS = 'OPTIONS';
        public const PATCH = 'PATCH';
        public const POST = 'POST';
        public const PUT = 'PUT';
        public const TRACE = 'TRACE';
        public const INPUT_GET = \INPUT_GET;
        public const INPUT_POST = \INPUT_POST;
        public const INPUT_REQUEST = 99;
        public const INPUT_COOKIE = \INPUT_COOKIE;
        public const INPUT_SERVER = \INPUT_SERVER;
        public const INPUT_ENV = \INPUT_ENV;
        public const METHODS = [self::CONNECT, self::DELETE, self::GET, self::HEAD, self::OPTIONS, self::PATCH, self::POST, self::PUT, self::TRACE];
        /**
         * Returns the URL for current request.
         *
         * @return Url
         */
        public function url(): \Inpsyde\MultilingualPress\Framework\Url\Url;
        /**
         * Returns the body of the request as string.
         *
         * @return string
         */
        public function body(): string;
        /**
         * Return a value from request body, optionally filtered.
         *
         * @param string $name
         * @param int $source The input source of the value. One of the `INPUT_*` constants.
         * @param int $filter
         * @param int $options
         * @return mixed
         */
        public function bodyValue(string $name, int $source = self::INPUT_REQUEST, int $filter = \FILTER_UNSAFE_RAW, int $options = \FILTER_FLAG_NONE);
        /**
         * Returns header value as set in the request.
         *
         * @param string $name
         * @return string
         */
        public function header(string $name): string;
        /**
         * Returns method (GET, POST..) value as set in the request.
         *
         * @return string
         */
        public function method(): string;
    }
    /**
     * Interface for all HTTP server request abstraction implementations.
     */
    interface ServerRequest extends \Inpsyde\MultilingualPress\Framework\Http\Request
    {
        /**
         * Returns a server value.
         *
         * @param string $name
         * @return string
         */
        public function serverValue(string $name): string;
    }
    final class PhpServerRequest implements \Inpsyde\MultilingualPress\Framework\Http\ServerRequest
    {
        public const INPUT_SOURCES = [\INPUT_POST => \Inpsyde\MultilingualPress\Framework\Http\Request::INPUT_POST, \INPUT_GET => \Inpsyde\MultilingualPress\Framework\Http\Request::INPUT_GET, \INPUT_COOKIE => \Inpsyde\MultilingualPress\Framework\Http\Request::INPUT_COOKIE, \INPUT_SERVER => \Inpsyde\MultilingualPress\Framework\Http\Request::INPUT_SERVER, \INPUT_ENV => \Inpsyde\MultilingualPress\Framework\Http\Request::INPUT_ENV];
        /**
         * Returns the URL for current request.
         *
         * @return Url
         */
        public function url(): \Inpsyde\MultilingualPress\Framework\Url\Url
        {
        }
        /**
         * @inheritdoc
         */
        public function body(): string
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function bodyValue(string $name, int $source = \Inpsyde\MultilingualPress\Framework\Http\Request::INPUT_REQUEST, int $filter = \FILTER_UNSAFE_RAW, int $options = \FILTER_FLAG_NONE)
        {
        }
        /**
         * @inheritdoc
         */
        public function header(string $name): string
        {
        }
        /**
         * @inheritdoc
         */
        public function serverValue(string $name): string
        {
        }
        /**
         * @inheritdoc
         */
        public function method(): string
        {
        }
    }
    /**
     * URL implementation that is build starting from server data as array.
     */
    final class ServerUrl implements \Inpsyde\MultilingualPress\Framework\Url\Url
    {
        /**
         * @param array $serverData
         * @param string $host
         */
        public function __construct(array $serverData, string $host = '')
        {
        }
        /**
         * Returns the URL string.
         *
         * @return string
         */
        public function __toString(): string
        {
        }
    }
    /**
     * phpcs:disable WordPress.VIP.SuperGlobalInputUsage
     * phpcs:disable WordPress.CSRF
     */
    class RequestGlobalsManipulator
    {
        public const METHOD_GET = 'GET';
        public const METHOD_POST = 'POST';
        /**
         * @param string $requestMethod
         */
        public function __construct(string $requestMethod = self::METHOD_POST)
        {
        }
        /**
         * Removes all data from the request globals.
         *
         * @return int
         */
        public function clear(): int
        {
        }
        /**
         * Restores all data from the storage.
         *
         * @return int
         */
        public function restore(): int
        {
        }
    }
    /**
     * Something able to handle the server request.
     */
    interface RequestHandler
    {
        /**
         * Handles the given server request.
         *
         * @param ServerRequest $request
         */
        public function handle(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request): void;
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    /**
     * Version number implementation according to the SemVer specification.
     *
     * @see http://semver.org/#semantic-versioning-specification-semver
     */
    class SemanticVersionNumber
    {
        public const FALLBACK_VERSION = '0.0.0';
        /**
         * @param string $version
         */
        public function __construct(string $version)
        {
        }
        /**
         * Returns the version string.
         *
         * @return string
         */
        public function __toString(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Api {
    class Translation
    {
        public const FILTER_URL = 'multilingualpress.translation_url';
        protected const REMOTE_TITLE = 'remote_title';
        protected const REMOTE_URL = 'remote_url';
        protected const REMOTE_CONTENT_ID = 'target_content_id';
        protected const REMOTE_SITE_ID = 'target_site_id';
        protected const SOURCE_SITE_ID = 'source_site_id';
        protected const TYPE = 'type';
        protected const KEYS = [self::REMOTE_TITLE => 'is_string', self::REMOTE_URL => 'is_string', self::REMOTE_CONTENT_ID => 'is_int', self::REMOTE_SITE_ID => 'is_int', self::SOURCE_SITE_ID => 'is_int', self::TYPE => 'is_string'];
        /**
         * @param Language|null $language
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Language\Language $language = null)
        {
        }
        /**
         * @param Translation $translation
         * @return Translation
         */
        public function merge(\Inpsyde\MultilingualPress\Framework\Api\Translation $translation): \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @return Language
         */
        public function language(): \Inpsyde\MultilingualPress\Framework\Language\Language
        {
        }
        /**
         * @return string
         */
        public function remoteTitle(): string
        {
        }
        /**
         * @param string $title
         * @return Translation
         */
        public function withRemoteTitle(string $title): \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @return string
         */
        public function remoteUrl(): string
        {
        }
        /**
         * @param Url $url
         * @return Translation
         */
        public function withRemoteUrl(\Inpsyde\MultilingualPress\Framework\Url\Url $url): \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @return int
         */
        public function remoteContentId(): int
        {
        }
        /**
         * @param int $contentId
         * @return Translation
         */
        public function withRemoteContentId(int $contentId): \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @return int
         */
        public function remoteSiteId(): int
        {
        }
        /**
         * @param int $siteId
         * @return Translation
         */
        public function withRemoteSiteId(int $siteId): \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @return int
         */
        public function sourceSiteId(): int
        {
        }
        /**
         * @param int $siteId
         * @return Translation
         */
        public function withSourceSiteId(int $siteId): \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @return string
         */
        public function type(): string
        {
        }
        /**
         * @param string $type
         * @return Translation
         */
        public function withType(string $type): \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
    }
    /**
     * Interface for all site relations API implementations.
     */
    interface SiteRelations
    {
        public const RELATED_SITE_IDS_CACHE_KEY = 'relatedSiteIds';
        public const ALL_RELATIONS_CACHE_KEY = 'allRelations';
        /**
         * Deletes the relationship between the given sites. If only one site is given, all its relations
         * will be deleted.
         *
         * @param int $sourceSite
         * @param int $targetSite
         * @return int
         * @throws NonexistentTable
         */
        public function deleteRelation(int $sourceSite, int $targetSite = 0): int;
        /**
         * Returns an array with site IDs as keys and arrays with the IDs of all related sites as values.
         *
         * @return array<int, int[]>
         * @throws NonexistentTable
         */
        public function allRelations(): array;
        /**
         * Returns an array holding the IDs of all sites related to the site with the given ID.
         *
         * @param int $siteId
         * @param bool $includeSite
         * @return int[]
         * @throws NonexistentTable
         */
        public function relatedSiteIds(int $siteId, bool $includeSite = \false): array;
        /**
         * Creates relations between one site and one or more other sites.
         *
         * @param int $baseSiteId
         * @param int[] $siteIds
         * @return int
         * @throws NonexistentTable
         */
        public function insertRelations(int $baseSiteId, array $siteIds): int;
        /**
         * Sets the relations for the site with the given ID.
         *
         * @param int $baseSiteId
         * @param int[] $siteIds
         * @return int
         * @throws NonexistentTable
         */
        public function relateSites(int $baseSiteId, array $siteIds): int;
    }
    /**
     * Interface for all content relations API implementations.
     */
    interface ContentRelations
    {
        public const CONTENT_IDS_CACHE_KEY = 'contentIds';
        public const RELATIONS_CACHE_KEY = 'relations';
        public const HAS_SITE_RELATIONS_CACHE_KEY = 'hasSiteRelations';
        public const CONTENT_TYPE_POST = 'post';
        public const CONTENT_TYPE_TERM = 'term';
        public const CONTENT_TYPE_COMMENT = 'comment';
        public const FILTER_POST_TYPE = 'multilingualpress.content_relations_post_type';
        public const FILTER_POST_STATUS = 'multilingualpress.content_relations_post_status';
        public const FILTER_TAXONOMY = 'multilingualpress.content_relations_taxonomy';
        /**
         * Creates a relationship for the given content ids provided as an array with site IDs as keys
         * and content IDs as values.
         *
         * @param int[] $contentIds
         * @param string $type
         * @return int
         * @throws NonexistentTable
         */
        public function createRelationship(array $contentIds, string $type): int;
        /**
         * Deletes all relations for content elements that don't exist (anymore).
         *
         * @param string $type
         * @return bool
         * @throws NonexistentTable
         */
        public function deleteAllRelationsForInvalidContent(string $type): bool;
        /**
         * Deletes all relations for sites that don't exist (anymore).
         *
         * @return bool
         * @throws NonexistentTable
         */
        public function deleteAllRelationsForInvalidSites(): bool;
        /**
         * Deletes all relations for the site with the given ID.
         *
         * @param int $siteId
         * @return bool
         * @throws NonexistentTable
         */
        public function deleteAllRelationsForSite(int $siteId): bool;
        /**
         * Deletes a relation according to the given arguments.
         *
         * @param int[] $contentIds
         * @param string $type
         * @return bool
         * @throws NonexistentTable
         */
        public function deleteRelation(array $contentIds, string $type): bool;
        /**
         * Copies all relations of the given (or any) content type from the given source site to the
         * given destination site.
         *
         * This method is suited to be used after site duplication, because both sites are assumed to
         * have the exact same content IDs.
         *
         * @param int $sourceSiteId
         * @param int $targetSiteId
         * @return int
         * @throws NonexistentTable
         */
        public function duplicateRelations(int $sourceSiteId, int $targetSiteId): int;
        /**
         * Returns the content ID for the given arguments.
         *
         * @param int $relationshipId
         * @param int $siteId
         * @return int
         * @throws NonexistentTable
         */
        public function contentId(int $relationshipId, int $siteId): int;
        /**
         * Returns the content ID in the given target site for the given content element.
         *
         * @param int $siteId
         * @param int $contentId
         * @param string $type
         * @param int $targetSiteId
         * @return int
         * @throws NonexistentTable
         */
        public function contentIdForSite(int $siteId, int $contentId, string $type, int $targetSiteId): int;
        /**
         * Returns the content IDs for the given relationship ID.
         *
         * @param int $relationshipId
         * @return int[]
         * @throws NonexistentTable
         */
        public function contentIds(int $relationshipId): array;
        /**
         * Returns all relations for the given content element.
         *
         * @param int $siteId
         * @param int $contentId
         * @param string $type
         * @return int[]
         * @throws NonexistentTable
         */
        public function relations(int $siteId, int $contentId, string $type): array;
        /**
         * Returns the relationship ID for the given arguments.
         *
         * @param int[] $contentIds
         * @param string $type
         * @param bool $create
         * @return int
         * @throws NonexistentTable
         */
        public function relationshipId(array $contentIds, string $type, bool $create = \false): int;
        /**
         * Checks if the site with the given ID has any relations of the given (or any) content type.
         *
         * @param int $siteId
         * @param string $type
         * @return bool
         * @throws NonexistentTable
         */
        public function hasSiteRelations(int $siteId, string $type = ''): bool;
        /**
         * Relates all posts between the given source site and the given destination site.
         *
         * This method is suited to be used after site duplication, because both sites are assumed to
         * have the exact same post IDs.
         * Furthermore, the current site is assumed to be either the source site or the destination site.
         *
         * @param int $sourceSite
         * @param int $targetSite
         * @return bool
         * @throws NonexistentTable
         */
        public function relateAllPosts(int $sourceSite, int $targetSite): bool;
        /**
         * Relates all terms between the given source site and the given destination site.
         *
         * This method is suited to be used after site duplication, because both sites are assumed to
         * have the exact same term taxonomy IDs.
         * Furthermore, the current site is assumed to be either the source site or the destination site.
         *
         * @param int $sourceSite
         * @param int $targetSite
         * @return bool
         * @throws NonexistentTable
         */
        public function relateAllTerms(int $sourceSite, int $targetSite): bool;
        /**
         * Sets a relation according to the given arguments.
         *
         * @param int $relationshipId
         * @param int $siteId
         * @param int $contentId
         * @return bool
         * @throws NonexistentTable
         */
        public function saveRelation(int $relationshipId, int $siteId, int $contentId): bool;
        /**
         * Relates all comments between the given source site and the given destination site.
         *
         * @param int $sourceSite The source site ID.
         * @param int $targetSite The Target site ID.
         * @return bool true if the comments are related, false if not.
         * @throws RuntimeException if problem relating.
         */
        public function relateAllComments(int $sourceSite, int $targetSite): bool;
    }
    class TranslationSearchArgs
    {
        protected const CONTENT_ID = 'content_id';
        protected const INCLUDE_BASE = 'include_base';
        protected const POST_STATUS = 'post_status';
        protected const POST_TYPE = 'post_type';
        protected const SEARCH_TERM = 'search_term';
        protected const SITE_ID = 'site_id';
        protected const STRICT = 'strict';
        protected const TYPE = 'type';
        protected const KEYS = [self::CONTENT_ID => 'is_int', self::INCLUDE_BASE => null, self::POST_STATUS => 'is_array', self::POST_TYPE => 'is_string', self::SEARCH_TERM => 'is_string', self::SITE_ID => 'is_int', self::STRICT => null, self::TYPE => 'is_string'];
        /**
         * @param WordpressContext $context
         * @param array<string, mixed> $data
         * @return static
         */
        public static function forContext(\Inpsyde\MultilingualPress\Framework\WordpressContext $context, array $data = []): \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @param array<string, mixed> $data
         */
        final public function __construct(array $data = [])
        {
        }
        /**
         * @return int|null
         */
        public function contentId(): ?int
        {
        }
        /**
         * @param int $contentId
         * @return static
         */
        public function forContentId(int $contentId): \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return bool
         */
        public function shouldIncludeBase(): bool
        {
        }
        /**
         * @return static
         */
        public function includeBase(): \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return static
         */
        public function dontIncludeBase(): \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return array
         */
        public function postStatus(): array
        {
        }
        /**
         * @param string[] $postStatus
         * @return static
         */
        public function forPostStatus(string ...$postStatus): \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return string
         */
        public function postType(): ?string
        {
        }
        /**
         * @param string $postType
         * @return static
         */
        public function forPostType(string $postType): \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return string
         */
        public function searchTerm(): ?string
        {
        }
        /**
         * @param string $searchTerm
         * @return static
         */
        public function searchFor(string $searchTerm): \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return int|null
         */
        public function siteId(): ?int
        {
        }
        /**
         * @param int $siteId
         * @return static
         */
        public function forSiteId(int $siteId): \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return bool
         */
        public function isStrict(): bool
        {
        }
        /**
         * @return static
         */
        public function makeStrictSearch(): \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return static
         */
        public function makeNotStrictSearch(): \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return string
         */
        public function type(): string
        {
        }
        /**
         * @param string $type
         * @return static
         */
        public function forType(string $type): \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
        /**
         * @return array
         */
        public function toArray(): array
        {
        }
    }
    /**
     * Interface for all languages API implementations.
     */
    interface Languages
    {
        /**
         * Deletes the language with the given ID.
         *
         * @param int $id
         * @return bool
         * @throws NonexistentTable
         */
        public function deleteLanguage(int $id): bool;
        /**
         * Returns an array with objects of all available languages.
         *
         * @return Language[]
         * @throws NonexistentTable
         */
        public function allLanguages(): array;
        /**
         * Returns the complete language data of all sites.
         *
         * @return Language[]
         * @throws NonexistentTable
         */
        public function allAssignedLanguages(): array;
        /**
         * Returns the language for the given arguments.
         *
         * @param string $column
         * @param string|int $value
         * @return Language
         * @throws NonexistentTable
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function languageBy(string $column, $value): \Inpsyde\MultilingualPress\Framework\Language\Language;
        /**
         * Creates a new language entry according to the given data.
         *
         * @param array $languageData
         * @return int
         * @throws NonexistentTable
         */
        public function insertLanguage(array $languageData): int;
        /**
         * Updates the language with the given ID according to the given data.
         *
         * @param int $id
         * @param array $data
         * @return bool
         * @throws NonexistentTable
         */
        public function updateLanguage(int $id, array $data): bool;
    }
    class ContentRelationSearch
    {
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\Translations $translations)
        {
        }
        /**
         * Retrieves the content relations for the current page.
         *
         * @return Translation[]
         */
        public function fetchCurrentContentRelations(): array
        {
        }
    }
    /**
     * Interface for all content relationship meta API implementations.
     */
    interface ContentRelationshipMetaInterface
    {
        /**
         * Updates or creates(if doesn't exist) the relationship meta with given arguments.
         *
         * @param int $relationshipId The Relationship ID.
         * @param string $metaKey The meta key.
         * @param string $metaValue The meta value.
         * @throws RuntimeException if problem creating.
         */
        public function updateRelationshipMeta(int $relationshipId, string $metaKey, string $metaValue): void;
        /**
         * Gets the relationship meta value with given ID and meta key.
         *
         * @param int $relationshipId The Relationship ID.
         * @param string $metaKey The meta key.
         * @return string The meta value.
         */
        public function relationshipMetaValue(int $relationshipId, string $metaKey): string;
        /**
         * Gets the relationship meta value for given post ID and meta key.
         *
         * @param int $postId The post ID.
         * @param string $metaKey The meta key.
         * @return string The meta value.
         * @throws RuntimeException if problem getting.
         */
        public function relationshipMetaValueByPostId(int $postId, string $metaKey): string;
        /**
         * Deletes the relationship meta by given relationship ID.
         *
         * @param int $relationshipId The Relationship ID.
         * @return bool true if the relationship meta is deleted, otherwise false.
         * @throws RuntimeException if problem deleting.
         */
        public function deleteRelationshipMeta(int $relationshipId): bool;
    }
    /**
     * Interface for all translations API implementations.
     */
    interface Translations
    {
        /**
         * Returns all translations according to the given arguments.
         *
         * @param TranslationSearchArgs $args
         * @return Translation[]
         */
        public function searchTranslations(\Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args): array;
        /**
         * Registers the given translator for the given type.
         *
         * @param Translator $translator
         * @param string $type
         * @return bool
         */
        public function registerTranslator(\Inpsyde\MultilingualPress\Framework\Translator\Translator $translator, string $type): bool;
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    /**
     * Provides access to the correct basedir and baseurl paths of the current site's
     * uploads folder.
     */
    class BasePathAdapter
    {
        use \Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
        /**
         * Returns the correct basedir path of the current site's uploads folder.
         *
         * @return string
         */
        public function basedir(): string
        {
        }
        /**
         * Returns the correct base url path for the give site
         *
         * @param int $siteId
         * @return string
         */
        public function basedirForSite(int $siteId): string
        {
        }
        /**
         * Returns the correct baseurl path of the current site's uploads folder.
         *
         * @return string
         */
        public function baseurl(): string
        {
        }
        /**
         * Returns the correct baseurl path for the given site
         *
         * @param int $siteId
         * @return string
         */
        public function baseurlForSite(int $siteId): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Filter {
    /**
     * Interface for all filter implementations.
     */
    interface Filter
    {
        /**
         * Removes the filter.
         *
         * @return bool
         */
        public function disable(): bool;
        /**
         * Adds the filter.
         *
         * @return bool
         */
        public function enable(): bool;
    }
    /**
     * The CompositeFilter maintains a collection of Filter objects and provides methods
     * to enable or disable all contained filters at once.
     *
     * This is useful when you need to manage multiple interdependent filters as a single unit.
     */
    class CompositeFilter implements \Inpsyde\MultilingualPress\Framework\Filter\Filter
    {
        public function __construct(\Inpsyde\MultilingualPress\Framework\Filter\Filter ...$filters)
        {
        }
        /**
         * Register all filters in the collection.
         */
        public function enable(): bool
        {
        }
        /**
         * Disables all filters in the collection.
         */
        public function disable(): bool
        {
        }
    }
    /**
     * Trait for basic filter implementations.
     *
     * @see Filter
     */
    trait FilterTrait
    {
        private int $acceptedArgs = 1;
        private string $hook;
        private int $priority = 10;
        /**
         * @var callable
         */
        private $callback;
        /**
         * @return bool
         *
         * @see Filter::enable()
         */
        public function enable(): bool
        {
        }
        /**
         * @return bool
         *
         * @see Filter::disable()
         */
        public function disable(): bool
        {
        }
        /**
         * Returns the callback priority.
         *
         * @return int
         */
        public function priority(): int
        {
        }
        /**
         * Returns the number of accepted arguments.
         *
         * @return int
         */
        public function acceptedArgs(): int
        {
        }
        /**
         * Returns the callback.
         *
         * @return callable
         */
        public function callback(): callable
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Service {
    /**
     * Append-only container implementation to be used for dependency management.
     *
     * @template-implements \ArrayAccess<string, mixed>
     */
    class Container implements \ArrayAccess
    {
        /**
         * @param array $values
         */
        public function __construct(array $values = [])
        {
        }
        /**
         * Bootstraps (and locks) the container.
         *
         * Only shared values and factory callbacks are accessible from now on.
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function bootstrap(): void
        {
        }
        /**
         * Locks the container.
         *
         * A locked container cannot be manipulated anymore.
         * All stored values and factory callbacks are still accessible.
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function lock(): void
        {
        }
        /**
         * Returns true when either a service factory callback or a value with the given name are stored
         * stored in the container.
         *
         * PSR-11 compatible.
         *
         * @param string $name
         * @return bool
         */
        public function has(string $name): bool
        {
        }
        /**
         * Retrieve a service or a value from the container.
         *
         * Values are just returned.
         * Services are built first time they are requested.
         * Stored factories are always executed and the returned value is returned.
         *
         * PSR-11 compatible.
         *
         * @param string $name
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function get(string $name)
        {
        }
        /**
         * Stores the given service factory callback with the given name.
         *
         * @param string $name
         * @param callable $factory
         * @return Container
         * @throws Exception\NameOverwriteNotAllowed
         * @throws Exception\WriteAccessOnLockedContainer
         */
        public function addService(string $name, callable $factory): \Inpsyde\MultilingualPress\Framework\Service\Container
        {
        }
        /**
         * Stores the given value with the given name.
         *
         * Scalar values are automatically shared.
         *
         * @param string $name
         * @param mixed $value
         * @return Container
         * @throws Exception\NameOverwriteNotAllowed
         * @throws Exception\WriteAccessOnLockedContainer
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function addValue(string $name, $value): \Inpsyde\MultilingualPress\Framework\Service\Container
        {
        }
        /**
         * @param string $name
         * @param callable $factory
         * @return Container
         */
        public function addFactory(string $name, callable $factory): \Inpsyde\MultilingualPress\Framework\Service\Container
        {
        }
        /**
         * Stores the given value or factory callback with the given name, and defines it to be
         * accessible even after the container has been bootstrapped.
         *
         * @param string $name
         * @param callable $factory
         * @return Container
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function share(string $name, callable $factory): \Inpsyde\MultilingualPress\Framework\Service\Container
        {
        }
        /**
         * Stores the given value with the given name, and defines it to be
         * accessible even after the container has been bootstrapped.
         *
         * Scalar values are automatically shared.
         *
         * @param string $name
         * @param mixed $value
         * @return Container
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function shareValue(string $name, $value): \Inpsyde\MultilingualPress\Framework\Service\Container
        {
        }
        /**
         * @param string $name
         * @param callable $value
         * @return Container
         */
        public function shareFactory(string $name, callable $value): \Inpsyde\MultilingualPress\Framework\Service\Container
        {
        }
        /**
         * Replaces the factory callback with the given name with the given factory callback.
         *
         * The new factory callback will receive as first argument the object created by the current
         * factory, and as second argument the container.
         *
         * @param string $name
         * @param callable $factory
         * @return Container
         * @throws Exception\WriteAccessOnLockedContainer
         * @throws Exception\NameNotFound
         * @throws Exception\NameOverwriteNotAllowed
         */
        public function extend(string $name, callable $factory): \Inpsyde\MultilingualPress\Framework\Service\Container
        {
        }
        /**
         * @inheritdoc
         *
         * @see Container::has()
         */
        public function offsetExists($offset): bool
        {
        }
        /**
         * @inheritdoc
         *
         * @see Container::get()
         */
        #[\ReturnTypeWillChange]
        public function offsetGet($offset)
        {
        }
        /**
         * @inheritdoc
         *
         * @see Container::addService()
         * @see Container::addValue()
         */
        public function offsetSet($offset, $value): void
        {
        }
        /**
         * Removing values or factory callbacks is not allowed.
         *
         * @param string $offset
         * @throws Exception\UnsetNotAllowed
         */
        public function offsetUnset($offset): void
        {
        }
    }
    /**
     * Interface for all bootstrappable service provider implementations.
     */
    interface BootstrappableServiceProvider extends \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        /**
         * Bootstraps the registered services.
         *
         * @param Container $container
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void;
    }
    /**
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    class ServiceProvidersCollection implements \Countable
    {
        final public function __construct()
        {
        }
        /**
         * Adds the given service provider to the collection.
         *
         * @param ServiceProvider $provider
         * @return ServiceProvidersCollection
         */
        public function add(\Inpsyde\MultilingualPress\Framework\Service\ServiceProvider $provider): \Inpsyde\MultilingualPress\Framework\Service\ServiceProvidersCollection
        {
        }
        /**
         * Removes the given service provider from the collection.
         *
         * @param ServiceProvider $provider
         * @return ServiceProvidersCollection
         */
        public function remove(\Inpsyde\MultilingualPress\Framework\Service\ServiceProvider $provider): \Inpsyde\MultilingualPress\Framework\Service\ServiceProvidersCollection
        {
        }
        /**
         * Calls the method with the given name on all registered providers,
         * and passes on potential further arguments.
         *
         * @param string $methodName
         * @param mixed ...$args
         */
        public function applyMethod(string $methodName, ...$args): void
        {
        }
        /**
         * Executes the given callback for all registered providers,
         * and passes along potential further arguments.
         *
         * @param callable $callback
         * @param mixed ...$args
         */
        public function applyCallback(callable $callback, ...$args): void
        {
        }
        /**
         * Executes the given callback for all registered providers, and returns the instance that
         * contains the providers that passed the filtering.
         *
         * @param callable $callback
         * @param array ...$args
         * @return ServiceProvidersCollection
         */
        public function filter(callable $callback, ...$args): \Inpsyde\MultilingualPress\Framework\Service\ServiceProvidersCollection
        {
        }
        /**
         * Executes the given callback for all registered providers, and returns the instance that
         * contains the providers obtained.
         *
         * @param callable $callback
         * @param array ...$args
         * @return ServiceProvidersCollection
         * @throws \UnexpectedValueException If a given callback did not return a service provider instance.
         */
        public function map(callable $callback, ...$args): \Inpsyde\MultilingualPress\Framework\Service\ServiceProvidersCollection
        {
        }
        /**
         * Executes the given callback for all registered providers, and passes along the result of
         * previous callback.
         *
         * @param callable $callback
         * @param mixed $initial
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function reduce(callable $callback, $initial = null)
        {
        }
        /**
         * Returns the number of providers in the collection.
         *
         * @return int
         */
        public function count(): int
        {
        }
    }
    /**
     * Interface for all integration service provider implementations.
     */
    interface IntegrationServiceProvider extends \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        /**
         * Integrates the registered services with MultilingualPress.
         *
         * @param Container $container
         */
        public function integrate(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void;
    }
}
namespace Inpsyde\MultilingualPress\Framework\Service\Exception {
    /**
     * Exception base class for all exceptions thrown by the container.
     *
     * This is necessary to be able to catch all exceptions thrown in the module in one go.
     * Moreover, compliance with PSR-11 would be easier, with pretty much no code necessary.
     */
    class ContainerException extends \Exception
    {
    }
    /**
     * Exception to be thrown when a value that has already been set is to be manipulated.
     */
    class InvalidValueAccess extends \Inpsyde\MultilingualPress\Framework\Service\Exception\ContainerException
    {
    }
    /**
     * Exception to be thrown when a value that has already been set is to be manipulated.
     */
    class InvalidValueReadAccess extends \Inpsyde\MultilingualPress\Framework\Service\Exception\InvalidValueAccess
    {
    }
    /**
     * Exception to be thrown when a not shared value or factory callback is to be accessed on a
     * bootstrapped container.
     */
    class LateAccessToNotSharedService extends \Inpsyde\MultilingualPress\Framework\Service\Exception\InvalidValueReadAccess
    {
        /**
         * @param string $name
         * @param string $action
         * @return self
         */
        public static function forService(string $name, string $action): self
        {
        }
    }
    /**
     * Exception to be thrown when a value that has already been set is to be manipulated.
     */
    class ExtendingResolvedNotAllowed extends \Inpsyde\MultilingualPress\Framework\Service\Exception\InvalidValueAccess
    {
        /**
         * @param string $name
         * @return ExtendingResolvedNotAllowed
         */
        public static function forName(string $name): self
        {
        }
    }
    /**
     * Exception to be thrown when a value that has already been set is to be manipulated.
     */
    class NameOverwriteNotAllowed extends \Inpsyde\MultilingualPress\Framework\Service\Exception\InvalidValueAccess
    {
        /**
         * @param string $name
         * @return NameOverwriteNotAllowed
         */
        public static function forServiceName(string $name): self
        {
        }
        /**
         * @param string $name
         * @return NameOverwriteNotAllowed
         */
        public static function forValueName(string $name): self
        {
        }
    }
    /**
     * Exception to be thrown when a value that has already been set is to be manipulated.
     */
    class UnsetNotAllowed extends \Inpsyde\MultilingualPress\Framework\Service\Exception\InvalidValueAccess
    {
        /**
         * @param string $name
         * @return UnsetNotAllowed
         */
        public static function forName(string $name): self
        {
        }
    }
    /**
     * Exception to be thrown when a value or factory callback could not be found in the container.
     */
    class NameNotFound extends \Inpsyde\MultilingualPress\Framework\Service\Exception\InvalidValueReadAccess
    {
        /**
         * Returns a new exception object.
         *
         * @param string $name
         * @return self
         */
        public static function forName(string $name): self
        {
        }
    }
    /**
     * Exception to be thrown when a locked container is to be manipulated.
     */
    class WriteAccessOnLockedContainer extends \Inpsyde\MultilingualPress\Framework\Service\Exception\NameOverwriteNotAllowed
    {
        /**
         * @param string $name
         * @return WriteAccessOnLockedContainer
         */
        public static function forName(string $name): self
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Setting {
    /**
     * Represents the setting.
     */
    interface SettingInterface
    {
        /**
         * The setting id
         *
         * @return string
         */
        public function id(): string;
        /**
         * The setting label.
         *
         * @return string
         */
        public function label(): string;
        /**
         * The setting description.
         *
         * @return string
         */
        public function description(): string;
        /**
         * The list of options.
         *
         * @return SettingOptionInterface[]
         */
        public function options(): array;
    }
    /**
     * Represents the setting with an image.
     */
    interface SettingWithImageInterface extends \Inpsyde\MultilingualPress\Framework\Setting\SettingInterface
    {
        /**
         * The setting image url.
         *
         * @return string
         */
        public function imageUrl(): string;
    }
    /**
     * The interface for options of settings.
     */
    interface SettingOptionInterface
    {
        /**
         * The setting id
         *
         * @return string
         */
        public function id(): string;
        /**
         * The setting value
         *
         * @return mixed
         */
        public function value();
        /**
         * The setting label
         *
         * @return string
         */
        public function label(): string;
        /**
         * The setting option description
         *
         * @return string
         */
        public function description(): string;
        /**
         * The setting option attributes
         *
         * @return array<string, scalar|array> The map of additional setting option attribute names to their values.
         */
        public function attributes(): array;
    }
    /**
     * Interface for all settings page repository implementations.
     *
     * @psalm-type settingName = string
     * @psalm-type settingValue = array | scalar
     */
    interface SettingsRepositoryInterface
    {
        /**
         * Returns all setting values for settings page.
         *
         * @return array | scalar
         */
        public function allSettingValues();
        /**
         * Returns the setting value with given name.
         *
         * @param string $settingName The setting name.
         * @return array | scalar
         */
        public function settingValue(string $settingName);
        /**
         * Updates the given setting values.
         *
         * @param array<string, mixed> $settingsMap A map of module setting names to values.
         * @psalm-param array<settingName, settingValue> $settingsMap
         * @return void
         */
        public function updateSettings(array $settingsMap): void;
    }
    interface SiteSettingsUpdatable
    {
        /**
         * Defines the initial settings of a new site.
         *
         * @param int $siteId
         */
        public function defineInitialSettings(int $siteId): void;
        /**
         * Updates the settings of an existing site.
         *
         * @param int $siteId
         */
        public function updateSettings(int $siteId): void;
    }
    class SettingWithImage implements \Inpsyde\MultilingualPress\Framework\Setting\SettingWithImageInterface
    {
        protected string $id;
        protected string $label;
        protected string $description;
        /**
         * @var SettingOptionInterface[]
         */
        protected array $options;
        protected string $imageUrl;
        public function __construct(string $id, string $label, string $description, array $options, string $imageUrl)
        {
        }
        public function id(): string
        {
        }
        public function label(): string
        {
        }
        public function description(): string
        {
        }
        public function imageUrl(): string
        {
        }
        public function options(): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Setting\User {
    /**
     * User setting view.
     */
    class UserSettingView
    {
        /**
         * @param UserSettingViewModel $model
         * @param bool $checkUser
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\User\UserSettingViewModel $model, bool $checkUser = \true)
        {
        }
        /**
         * Renders the user setting markup.
         *
         * @param \WP_User $user
         * @return bool
         */
        public function render(\WP_User $user): bool
        {
        }
    }
    /**
     * User setting.
     */
    class UserSetting
    {
        /**
         * @param UserSettingViewModel $model
         * @param UserSettingUpdater $updater
         * @param bool $checkUser
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\User\UserSettingViewModel $model, \Inpsyde\MultilingualPress\Framework\Setting\User\UserSettingUpdater $updater, bool $checkUser = \true)
        {
        }
        /**
         * Registers the according callbacks.
         */
        public function register(): void
        {
        }
    }
    /**
     * User setting updater implementation validating a nonce specific to the update action included in
     * the request data.
     */
    class UserSettingUpdater
    {
        /**
         * @param string $metaKey
         * @param Request $request
         * @param Nonce|null $nonce
         */
        public function __construct(string $metaKey, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce = null)
        {
        }
        /**
         * Updates the setting with the data in the request for the user with the given ID.
         *
         * @param int $userId
         * @return bool
         */
        public function update(int $userId): bool
        {
        }
    }
    /**
     * Interface for all user setting view model implementations.
     */
    interface UserSettingViewModel
    {
        /**
         * Renders the user setting.
         *
         * @param \WP_User $user
         */
        public function render(\WP_User $user): void;
        /**
         * Returns the title of the user setting.
         *
         * @return string
         */
        public function title(): string;
    }
}
namespace Inpsyde\MultilingualPress\Framework\Setting\Site {
    /**
     * Interface for all site setting view implementations.
     */
    interface SiteSettingView
    {
        /**
         * Renders the site setting markup.
         *
         * @param int $siteId
         * @return bool
         */
        public function render(int $siteId): bool;
    }
    /**
     * Site setting view implementation for a whole settings section.
     */
    final class SiteSettingsSectionView implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView
    {
        public const ACTION_AFTER = 'multilingualpress.after_site_settings';
        public const ACTION_BEFORE = 'multilingualpress.before_site_settings';
        /**
         * @param SiteSettingsSectionViewModel $model
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionViewModel $model)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId = 0): bool
        {
        }
    }
    /**
     * Site setting.
     */
    class SiteSetting
    {
        /**
         * @param SiteSettingViewModel $model
         * @param SiteSettingUpdater $updater
         * @param bool $checkUser
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel $model, \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingUpdater $updater, bool $checkUser = \true)
        {
        }
        /**
         * Registers the according callbacks.
         *
         * @param string $renderHook
         * @param string $updateHook
         */
        public function register(string $renderHook, string $updateHook = ''): void
        {
        }
    }
    /**
     * Interface for all site settings section view model implementations.
     */
    interface SiteSettingsSectionViewModel
    {
        /**
         * Returns the ID of the site settings section.
         *
         * @return string
         */
        public function id(): string;
        /**
         * Returns the markup for the site settings section.
         *
         * @param int $siteId
         * @return bool
         */
        public function renderView(int $siteId): bool;
        /**
         * Returns the title of the site settings section.
         *
         * @return string
         */
        public function title(): string;
    }
    /**
     * Site setting view implementation for multiple single settings.
     */
    final class SiteSettingMultiView implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView
    {
        /**
         * Returns a new instance.
         *
         * @param SiteSettingViewModel[] $settings
         * @param bool $checkUser
         * @return SiteSettingMultiView
         */
        public static function fromViewModels(array $settings, bool $checkUser = \true): \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingMultiView
        {
        }
        /**
         * @param SiteSettingView[] $views
         * @param bool $checkUser
         */
        public function __construct(array $views, bool $checkUser = \true)
        {
        }
        /**
         * @inheritdoc
         *
         * @wp-hook SiteSettingsSectionView::ACTION_AFTER . '_' . NewSiteSettings::SECTION_ID
         */
        public function render(int $siteId): bool
        {
        }
    }
    class SiteSettingUpdater
    {
        /**
         * @param string $option
         * @param Request $request
         * @param Nonce|null $nonce
         */
        public function __construct(string $option, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce = null)
        {
        }
        /**
         * Updates the setting with the given data for the site with the given ID.
         *
         * @param int $siteId
         * @return bool
         */
        public function update(int $siteId): bool
        {
        }
    }
    /**
     * Interface for all site setting view model implementations.
     */
    interface SiteSettingViewModel
    {
        /**
         * Renders the markup for the site setting.
         *
         * @param int $siteId
         */
        public function render(int $siteId): void;
        /**
         * Returns the title of the site setting.
         *
         * @return string
         */
        public function title(): string;
    }
    /**
     * Site setting view implementation for a single setting.
     */
    final class SiteSettingSingleView implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView
    {
        /**
         * @param SiteSettingViewModel $model
         * @param bool $checkUser
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel $model, bool $checkUser = \true)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Setting {
    class SettingOption implements \Inpsyde\MultilingualPress\Framework\Setting\SettingOptionInterface
    {
        protected string $id;
        protected string $value;
        protected string $label;
        protected string $description;
        /**
         * @var array<string, scalar|array>
         */
        protected array $attributes;
        public function __construct(string $id, string $value, string $label, string $description, array $attributes = [])
        {
        }
        /**
         * @return string The setting id
         */
        public function id(): string
        {
        }
        /**
         * @return mixed The setting value
         */
        public function value()
        {
        }
        /**
         * @inheritdoc
         */
        public function label(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function description(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function attributes(): array
        {
        }
    }
    class Setting implements \Inpsyde\MultilingualPress\Framework\Setting\SettingInterface
    {
        protected string $id;
        protected string $label;
        protected string $description;
        /**
         * @var SettingOptionInterface[]
         */
        protected array $options;
        public function __construct(string $id, string $label, string $description, array $options)
        {
        }
        public function id(): string
        {
        }
        public function label(): string
        {
        }
        public function description(): string
        {
        }
        public function options(): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    /**
     * Class Entity
     * @package Inpsyde\MultilingualPress\Framework
     */
    class Entity
    {
        /**
         * @param WP_Post|WP_Term|WP_Comment|Entity $object
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function __construct($object)
        {
        }
        /**
         * @param string $var
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function __get(string $var)
        {
        }
        /**
         * @return int
         */
        public function id(): int
        {
        }
        /**
         * @return bool
         */
        public function isValid(): bool
        {
        }
        /**
         * @param string $type
         *
         * @return bool
         */
        public function is(string $type): bool
        {
        }
        /**
         * Retrieve the class name of the entity
         *
         * @return string
         */
        public function type(): string
        {
        }
        /**
         * @param string $prop
         * @param null $default
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function prop(string $prop, $default = null)
        {
        }
        /**
         * @return WP_Post|WP_Term|WP_Comment|Entity|null
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function expose()
        {
        }
    }
    /**
     * Trait SiteIdValidatorTrait
     * @package Inpsyde\MultilingualPress\Framework
     */
    trait SiteIdValidatorTrait
    {
        /**
         * @param int $siteId
         * @throws UnexpectedValueException
         */
        protected function siteIdMustBeGreaterThanZero(int $siteId): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Factory {
    class LanguageFactory
    {
        /**
         * @param ClassResolver $classResolver
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Factory\ClassResolver $classResolver)
        {
        }
        /**
         * Returns a new language object of the given (or default) class,
         * instantiated with the given arguments.
         *
         * @param array $args
         * @param string|null $class
         * @return Language
         */
        public function create(array $args = [], string $class = null): \Inpsyde\MultilingualPress\Framework\Language\Language
        {
        }
    }
    /**
     * Factory for nonce objects.
     */
    class NonceFactory
    {
        /**
         * @param ClassResolver $classResolver
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Factory\ClassResolver $classResolver)
        {
        }
        /**
         * Returns a new nonce object, instantiated with the given arguments.
         *
         * @param array $args
         * @param string $class
         * @return Nonce
         */
        public function create(array $args = [], string $class = ''): \Inpsyde\MultilingualPress\Framework\Nonce\Nonce
        {
        }
    }
    class UrlFactory
    {
        /**
         * @param ClassResolver $classResolver
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Factory\ClassResolver $classResolver)
        {
        }
        /**
         * Returns a new url object of the given (or default) class,
         * instantiated with the given arguments.
         *
         * @param array $args
         * @param string|null $class
         * @return Url
         */
        public function create(array $args = [], string $class = null): \Inpsyde\MultilingualPress\Framework\Url\Url
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework\Factory\Exception {
    /**
     * Exception to be thrown when trying to create an instance of an invalid class.
     */
    class InvalidClass extends \Exception
    {
    }
}
namespace Inpsyde\MultilingualPress\Framework\Factory {
    /**
     * Factory for WordPress error objects.
     */
    class ErrorFactory
    {
        /**
         * @param ClassResolver $classResolver
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Factory\ClassResolver $classResolver)
        {
        }
        /**
         * Returns a new WordPress error object, instantiated with the given arguments.
         *
         * @param array $args
         * @param string $class
         * @return \WP_Error
         */
        public function create(array $args = [], string $class = ''): \WP_Error
        {
        }
    }
    /**
     * Class to be used for class resolution in factories.
     */
    class ClassResolver
    {
        /**
         * @param string $base
         * @param string|null $defaultClass
         * @throws \InvalidArgumentException If the given base is not a valid fully qualified class
         */
        public function __construct(string $base, string $defaultClass = null)
        {
        }
        /**
         * Resolves the class to be used for instantiation, which might be either the given class or
         * the default class.
         *
         * @param string|null $class
         * @return string
         * @throws \InvalidArgumentException If no class is given and no default class is available.
         */
        public function resolve(string $class = null): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Framework {
    /**
     * Trait ThrowableHandleCapableTrait
     * @package Inpsyde\MultilingualPress\Framework
     */
    trait ThrowableHandleCapableTrait
    {
        /**
         * @param Throwable $throwable
         * @throws Throwable
         */
        protected function handleThrowable(\Throwable $throwable): void
        {
        }
        /**
         * @param Throwable $throwable
         */
        protected function logThrowable(\Throwable $throwable): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Blocks\BlockTypeRegistrar {
    /**
     * Can register a BlockType.
     */
    interface BlockTypeRegistrarInterface
    {
        /**
         * Register the given block type.
         *
         * @param BlockTypeInterface $blockType The block type to register.
         * @throws RuntimeException If failing to register.
         */
        public function register(\Inpsyde\MultilingualPress\Module\Blocks\BlockType\BlockTypeInterface $blockType): void;
    }
    class BlockTypeRegistrar implements \Inpsyde\MultilingualPress\Module\Blocks\BlockTypeRegistrar\BlockTypeRegistrarInterface
    {
        protected string $scriptName;
        public function __construct(string $scriptName)
        {
        }
        /**
         * @inheritDoc
         */
        public function register(\Inpsyde\MultilingualPress\Module\Blocks\BlockType\BlockTypeInterface $blockType): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Blocks\Context {
    /**
     * Can create a context from the given attributes.
     *
     * @psalm-type name = string
     * @psalm-type value = scalar|array
     */
    interface ContextFactoryInterface
    {
        /**
         * Creates the context from the given attributes.
         *
         * @param array<string, mixed> $attributes A map of attribute name to value.
         * @psalm-param array<name, value> $attributes
         * @return array<string, mixed> The context.
         * @psalm-return array<name, value>
         * @throws RuntimeException If problem creating.
         */
        public function createContext(array $attributes): array;
    }
}
namespace Inpsyde\MultilingualPress\Module\Blocks\BlockType {
    /**
     * Can create a BlockType.
     *
     * @psalm-type optionName = string
     * @psalm-type optionValue = array{type: string}
     * @psalm-type extraValue = scalar|array
     * @psalm-type blockConfig = array{
     *      name: string,
     *      category: string,
     *      attributes: array<optionName, optionValue>,
     *      templatePath: string,
     *      contextFactory: ContextFactoryInterface,
     *      icon?: string,
     *      title?: string,
     *      description?: string,
     *      extra?: array<optionName, extraValue>,
     *      supports?: array<string, array{type: string}>
     * }
     */
    interface BlockTypeFactoryInterface
    {
        /**
         * Creates a new block type instance with a given config.
         *
         * @param array $config The config.
         * @psalm-param blockConfig $config
         * @return BlockTypeInterface The new instance.
         * @throws RuntimeException If problem creating.
         */
        public function createBlockType(array $config): \Inpsyde\MultilingualPress\Module\Blocks\BlockType\BlockTypeInterface;
    }
    /**
     * Represents the BlockType.
     *
     * @psalm-type name = string
     * @psalm-type type = array{type: string}
     * @psalm-type value = scalar|array
     */
    interface BlockTypeInterface
    {
        /**
         * The name of the block type.
         *
         * @return string
         */
        public function name(): string;
        /**
         * The block type category name, used in search interfaces to arrange block types by category.
         *
         * @return string
         */
        public function category(): string;
        /**
         * The block type icon.
         *
         * @return string
         */
        public function icon(): string;
        /**
         * The block type title.
         *
         * @return string
         */
        public function title(): string;
        /**
         * The block type description.
         *
         * @return string
         */
        public function description(): string;
        /**
         * Returns block type attributes config.
         *
         * @return array<string, mixed> A map of attribute name to type.
         * @psalm-return array<name, type>
         */
        public function attributes(): array;
        /**
         * Returns block type supports config.
         *
         * @return array<string, mixed> An array of `supports` entries.
         * @psalm-return array<name, type>
         */
        public function supports(): array;
        /**
         * Returns block extra config.
         *
         * These are additional custom configs which can contain block type specific information.
         *
         * @return array<string, mixed> A map of extra config name to value.
         * @psalm-return array<name, value>
         */
        public function extra(): array;
        /**
         * Renders the block type with given attributes.
         *
         * @param array<string, mixed> $attributes A map of attribute name to value.
         * @psalm-param array<name, value> $attributes
         * @return string
         * @throws RuntimeException If problem rendering.
         */
        public function render(array $attributes): string;
        /**
         * The context factory.
         *
         * @return ContextFactoryInterface
         */
        public function contextFactory(): \Inpsyde\MultilingualPress\Module\Blocks\Context\ContextFactoryInterface;
        /**
         * Returns the template path of a block type.
         *
         * @return string The template path.
         */
        public function templatePath(): string;
    }
    class BlockTypeFactory implements \Inpsyde\MultilingualPress\Module\Blocks\BlockType\BlockTypeFactoryInterface
    {
        protected \Inpsyde\MultilingualPress\Module\Blocks\TemplateRenderer\TemplateRendererInterface $templateRenderer;
        public function __construct(\Inpsyde\MultilingualPress\Module\Blocks\TemplateRenderer\TemplateRendererInterface $templateRenderer)
        {
        }
        /**
         * @inheritDoc
         */
        public function createBlockType(array $config): \Inpsyde\MultilingualPress\Module\Blocks\BlockType\BlockTypeInterface
        {
        }
    }
    /**
     * Represents the BlockType.
     *
     * @psalm-type name = string
     * @psalm-type type = array{type: string}
     * @psalm-type value = scalar|array
     */
    // phpcs:ignore Inpsyde.CodeQuality.PropertyPerClassLimit.TooManyProperties
    class BlockType implements \Inpsyde\MultilingualPress\Module\Blocks\BlockType\BlockTypeInterface
    {
        protected string $name;
        protected string $category;
        protected array $attributes;
        /**
         * @var array<string, mixed>
         * @psalm-var array<name, type>
         */
        protected array $supports;
        protected string $icon;
        protected string $title;
        protected string $description;
        /**
         * @var array<string, mixed>
         * @psalm-var array<name, value>
         */
        protected array $extra;
        protected string $templatePath;
        protected \Inpsyde\MultilingualPress\Module\Blocks\Context\ContextFactoryInterface $contextFactory;
        protected \Inpsyde\MultilingualPress\Module\Blocks\TemplateRenderer\TemplateRendererInterface $templateRenderer;
        /**
         * @param array<string, mixed> $supports
         * @psalm-param array<name, type> $supports
         * @param array<string, mixed> $extra
         * @psalm-param array<name, value> $extra
         */
        public function __construct(string $name, string $category, string $icon, string $title, string $description, array $attributes, array $supports, array $extra, string $templatePath, \Inpsyde\MultilingualPress\Module\Blocks\Context\ContextFactoryInterface $contextFactory, \Inpsyde\MultilingualPress\Module\Blocks\TemplateRenderer\TemplateRendererInterface $templateRenderer)
        {
        }
        /**
         * @inheritDoc
         */
        public function name(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function category(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function icon(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function title(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function description(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function attributes(): array
        {
        }
        /**
         * @inheritDoc
         */
        public function supports(): array
        {
        }
        /**
         * @inheritDoc
         */
        public function extra(): array
        {
        }
        /**
         * @inheritDoc
         */
        public function contextFactory(): \Inpsyde\MultilingualPress\Module\Blocks\Context\ContextFactoryInterface
        {
        }
        /**
         * @inheritDoc
         */
        public function templatePath(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function render(array $attributes): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Blocks {
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'blocks';
        public const SCRIPT_NAME_TO_REGISTER_BLOCK_SCRIPTS = 'multilingualpress-blocks';
        public const CONFIGURATION_NAME_FOR_URL_TO_MODULE_ASSETS = 'multilingualpress.blocks.urlToModuleAssets';
        /**
         * @inheritdoc
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Registers the given block types.
         *
         * @param BlockTypeRegistrarInterface $blockTypeRegistrar
         * @param BlockTypeInterface[] $blockTypes A list of block types.
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function registerBlockTypes(\Inpsyde\MultilingualPress\Module\Blocks\BlockTypeRegistrar\BlockTypeRegistrarInterface $blockTypeRegistrar, array $blockTypes): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Blocks\TemplateRenderer {
    /**
     * Can render the given template with the given context.
     */
    interface TemplateRendererInterface
    {
        /**
         * Renders the given template with the given context.
         *
         * @param string $templatePath The template path.
         * @param array<string, mixed> $context The context.
         * @return string The rendered HTML.
         * @throws RuntimeException If failing to render.
         */
        public function render(string $templatePath, array $context): string;
    }
    class BlockTypeTemplateRenderer implements \Inpsyde\MultilingualPress\Module\Blocks\TemplateRenderer\TemplateRendererInterface
    {
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.StaticClosure
         */
        public function render(string $templatePath, array $context): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\LanguageSwitcher {
    class ItemFactory
    {
        public function create(string $languageName, string $locale, string $isoCode, \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Flag $flag, string $url, int $siteId, string $hreflangDisplayCode, string $type = ''): \Inpsyde\MultilingualPress\Module\LanguageSwitcher\Item
        {
        }
    }
    class Model
    {
        public const FILTER_SHOULD_PRESERVE_LANGUAGE_SWITCHER_ITEM_URL_PARAMS = 'multilingualpress.languageSwitcher.should_preserve_url_params';
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelationSearch $contentRelationSearch, \Inpsyde\MultilingualPress\Module\LanguageSwitcher\ItemFactory $itemFactory, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository, \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Factory $flagFactory, bool $isExternalSitesModuleActive, bool $isSiteFlagsModuleActive)
        {
        }
        /**
         * @param array $args
         * @param array $instance
         * @return array
         */
        //phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
        public function data(array $args, array $instance): array
        {
        }
        /**
         * Returns a flag. Checks for legacy multilingualpress-site-flags plugin support,
         * then falls back to the current Site Flags module implementation.
         *
         * @param array $model
         * @param Translation $translation
         * @return Flag
         * @throws NonexistentTable
         */
        protected function languageFlag(array $model, \Inpsyde\MultilingualPress\Framework\Api\Translation $translation): \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Flag
        {
        }
        public function hreflangDisplayCode(int $siteId): string
        {
        }
    }
    class View
    {
        public const FILTER_ITEM_LANGUAGE_NAME = 'multilingualpress.language_switcher_item_language_name';
        public const FILTER_LANGUAGE_SWITCHER_ITEMS = 'multilingualpress.languageSwitcher.Items';
        /**
         * Displays widget view in frontend
         *
         * @param array $model
         * @return void
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         */
        public function render(array $model): void
        {
        }
        /**
         * Creates the widget title markup
         *
         * @param string $beforeTitle
         * @param string $title
         * @param string $afterTitle
         * @return string Tittle markup
         */
        protected function title(string $beforeTitle, string $title, string $afterTitle): string
        {
        }
        /**
         * retrieve an array of item classes
         *
         * @param int $siteId
         * @return array of classes
         */
        protected function itemClass(int $siteId): array
        {
        }
    }
    class Item
    {
        protected string $type;
        public function __construct(string $languageName, string $locale, string $isoCode, \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Flag $flag, string $url, int $siteId, string $hreflangDisplayCode, string $type = '')
        {
        }
        /**
         * @return string
         */
        public function languageName(): string
        {
        }
        /**
         * @return string
         */
        public function isoCode(): string
        {
        }
        /**
         * @return Flag
         */
        public function flag(): \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Flag
        {
        }
        /**
         * @return string
         */
        public function url(): string
        {
        }
        /**
         * @return int
         */
        public function siteId(): int
        {
        }
        /**
         * @return string
         */
        public function locale(): string
        {
        }
        /**
         * @return string
         */
        public function hreflangDisplayCode(): string
        {
        }
        /**
         * The item type.
         *
         * Can be used to specify the special item types like for external sites.
         *
         * @return string
         */
        public function type(): string
        {
        }
    }
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'language-switcher';
        /**
         * @inheritdoc
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        // phpcs:ignore Inpsyde.CodeQuality.FunctionLength.TooLong
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    class Widget extends \WP_Widget
    {
        /**
         * Whether the ExternalSites module is active.
         */
        protected bool $isExternalSitesModuleActive;
        public function __construct(\Inpsyde\MultilingualPress\Module\LanguageSwitcher\Model $model, \Inpsyde\MultilingualPress\Module\LanguageSwitcher\View $view, \Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager, bool $isExternalSitesModuleActive)
        {
        }
        /**
         * Outputs the content of the widget
         *
         * @param array $args
         * @param array $instance
         * @return void
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         */
        public function widget($args, $instance): void
        {
        }
        /**
         * Outputs the options form on admin
         *
         * @param array $instance
         * @return void
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function form($instance): void
        {
        }
        /**
         * Processing widget options on save
         *
         * @param array $newInstance
         * @param array $oldInstance
         * @return array|void
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         * @psalm-suppress ParamNameMismatch
         */
        public function update($newInstance, $oldInstance)
        {
        }
        /**
         * Whether to show the site flags option
         *
         * The "Show Flags" option should be shown if the old version of Site Flags addon is active or
         * if the new Site Flags module is enabled
         *
         * @return bool
         */
        protected function isShowFlagOption(): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\TranslationUi {
    class CommentMetabox implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metabox
    {
        public const RELATIONSHIP_TYPE = 'comment';
        public const ID_PREFIX = 'multilingualpress_comment_translation_metabox_';
        protected string $title;
        protected \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContext $relationshipContext;
        /**
         * @var CommentMetaboxTabInterface[]
         */
        protected array $metaboxTabs;
        /**
         * @var CommentMetaboxField[]
         */
        protected array $metaboxFields;
        protected \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $metaboxFieldsHelper;
        protected \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentRelationSaveHelperInterface $commentRelationSaveHelper;
        public function __construct(string $title, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContext $relationshipContext, array $metaboxTabs, array $metaboxFields, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $metaboxFieldsHelper, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentRelationSaveHelperInterface $commentRelationSaveHelper)
        {
        }
        /**
         * @inheritDoc
         */
        public function siteId(): int
        {
        }
        /**
         * @inheritDoc
         */
        public function isValid(\Inpsyde\MultilingualPress\Framework\Entity $entity): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function createInfo(string $showOrSave, \Inpsyde\MultilingualPress\Framework\Entity $entity): \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info
        {
        }
        /**
         * @inheritdoc
         */
        public function view(\Inpsyde\MultilingualPress\Framework\Entity $entity): \Inpsyde\MultilingualPress\Framework\Admin\Metabox\View
        {
        }
        /**
         * @inheritdoc
         */
        public function action(\Inpsyde\MultilingualPress\Framework\Entity $entity): \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
        {
        }
    }
    /**
     * Represents the comment metabox tab.
     */
    interface CommentMetaboxTabInterface
    {
        /**
         * The id of the metabox tab.
         *
         * @return string
         */
        public function id(): string;
        /**
         * The label to show to the tab header.
         *
         * @return string
         */
        public function label(): string;
        /**
         * The fields collection for the current tab.
         *
         * @return CommentMetaboxField[]
         */
        public function fields(): array;
        /**
         * If the metabox tab is enabled or not.
         *
         * @param CommentsRelationshipContextInterface $relationshipContext
         * @return bool
         */
        public function enabled(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext): bool;
        /**
         * Render the metabox markup.
         *
         * @param MetaboxFieldsHelper $helper
         * @param CommentsRelationshipContextInterface $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext): void;
    }
    class CommentMetaboxTab implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\CommentMetaboxTabInterface
    {
        public const ACTION_AFTER_TRANSLATION_UI_TAB = 'multilingualpress.TranslationUi.Comment.AfterTranslationUiTab';
        public const ACTION_BEFORE_TRANSLATION_UI_TAB = 'multilingualpress.TranslationUi.Comment.BeforeTranslationUiTab';
        public const FILTER_TRANSLATION_UI_SHOW_TAB = 'multilingualpress.TranslationUi.Comment.TranslationUiShowTab';
        public const FILTER_COMMENT_METABOX_TAB = 'multilingualpress.TranslationUi.Comment.TranslationUiTab';
        protected string $id;
        protected string $label;
        /**
         * @var CommentMetaboxField[]
         */
        protected array $fields;
        public function __construct(string $id, string $label, array $fields)
        {
        }
        /**
         * @inheritDoc
         */
        public function id(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function label(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function fields(): array
        {
        }
        /**
         * @inheritDoc
         */
        public function enabled(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext): bool
        {
        }
        /**
         * @inheritDoc
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext): void
        {
        }
    }
    /**
     * @psalm-type FieldName = string
     * @psalm-type FieldValue = scalar
     */
    class MetaboxAction implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
    {
        public const FILTER_TAXONOMIES_SLUGS_BEFORE_REMOVE = 'multilingualpress.taxonomies_slugs_before_remove';
        public const FILTER_NEW_RELATE_REMOTE_COMMENT_BEFORE_INSERT = 'multilingualpress.new_relate_remote_comment_before_insert';
        public const ACTION_METABOX_AFTER_RELATE_COMMENTS = 'multilingualpress.metabox_after_relate_comments';
        public const ACTION_METABOX_BEFORE_UPDATE_REMOTE_COMMENT = 'multilingualpress.metabox_before_update_remote_comment';
        public const ACTION_METABOX_AFTER_UPDATE_REMOTE_COMMENT = 'multilingualpress.metabox_after_update_remote_comment';
        /**
         * @var CommentMetaboxField[]
         */
        protected array $metaboxFields;
        protected \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentRelationSaveHelperInterface $commentRelationSaveHelper;
        /**
         * @psalm-suppress PropertyTypeCoercion
         */
        public function __construct(array $metaboxFields, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $fieldsHelper, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContext $relationshipContext, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentRelationSaveHelperInterface $commentRelationSaveHelper)
        {
        }
        /**
         * @inheritdoc
         */
        public function save(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices): bool
        {
        }
        /**
         * Checks if the relationship should be updated based on given params.
         *
         * @param string $relationType The relation type (existing, new, remove, leave).
         * @param bool $hasRemoteComment True if connection exists, otherwise false.
         * @return bool true if relationship should be updated, otherwise false.
         */
        protected function shouldSaveComment(string $relationType, bool $hasRemoteComment): bool
        {
        }
        /**
         * Returns the map of field keys to values from given request.
         *
         * @param Request $request
         * @return array<string, scalar> The map of field keys to values.
         * @psalm-return array<FieldName, FieldValue>
         */
        protected function allFieldsValues(\Inpsyde\MultilingualPress\Framework\Http\Request $request): array
        {
        }
        /**
         * Creates the remote comment data for given request.
         *
         * @param array<string, scalar> $values A map of field keys to values.
         * @psalm-param array<FieldName, FieldValue> $values
         * @param Request $request
         * @return array A map of WP_comment properties to values.
         */
        protected function createCommentData(array $values, \Inpsyde\MultilingualPress\Framework\Http\Request $request): array
        {
        }
        /**
         * Saves the given comment (inserts or updates) for given relation type.
         *
         * @param array $comment A map of WP_comment properties to values.
         * @param string $relationType The relation type (existing, new, remove, leave).
         * @return int The inserted or updated comment ID.
         */
        protected function saveComment(array $comment, string $relationType): int
        {
        }
        /**
         * Checks if the field value with given name is changed for given request.
         *
         * @param string $fieldName The field name.
         * @param Request $request
         * @return bool true if the field value with given name is changed, otherwise false.
         */
        protected function isFieldValueChanged(string $fieldName, \Inpsyde\MultilingualPress\Framework\Http\Request $request): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field {
    /**
     * Represents the comment metabox field.
     */
    interface CommentMetaboxField
    {
        /**
         * The key of the field.
         *
         * @return string
         */
        public function key(): string;
        /**
         * The field label.
         *
         * @return string
         */
        public function label(): string;
        /**
         * Renders the field by given context.
         *
         * @param CommentsRelationshipContextInterface $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext): void;
    }
    class CommentMetaboxFieldAuthorName implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field\CommentMetaboxField
    {
        protected string $label;
        protected \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory;
        public function __construct(string $key, string $label, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory)
        {
        }
        /**
         * @inheritDoc
         */
        public function key(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function label(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext): void
        {
        }
        /**
         * Creates a new metabox fields helper.
         *
         * @param int $siteId The ID of the site for which to create a helper.
         * @return MetaboxFieldsHelperInterface The new helper.
         * @throws RuntimeException If problem creating.
         */
        protected function createHelper(int $siteId): \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface
        {
        }
    }
    class CommentMetaboxRelation implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field\CommentMetaboxField
    {
        protected \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory;
        protected string $key;
        protected string $label = '';
        public function __construct(string $key, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory)
        {
        }
        /**
         * @inheritDoc
         */
        public function key(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function label(): string
        {
        }
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext): void
        {
        }
        /**
         * Creates a value for 'id' HTML attribute based on relation type.
         *
         * @param string $type The relation type (existing, new, remove, leave).
         * @return string The value for 'id' HTML attribute.
         */
        protected function relationFieldId(string $type, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $helper): string
        {
        }
        /**
         * The relation field markup based on the relation type.
         *
         * @param string $fieldId The value for 'id' HTML attribute.
         * @param string $fieldName The value for 'name' HTML attribute.
         * @param string $type The relation type (existing, new, remove, leave).
         * @param string $description The field description.
         * @return void
         */
        protected function relationFieldMarkup(string $fieldId, string $fieldName, string $type, string $description): void
        {
        }
        /**
         * Returns the relation field description based on relation type.
         *
         * @param string $type The relation type (existing, new, remove, leave).
         * @param string $languageName The language name.
         * @param bool $hasRemoteComment True if remote connection exists, otherwise false.
         * @param string $commentType The comment type. can be 'comment' or 'review' or custom type.
         * @return string The relation field description
         */
        protected function relationFieldDescription(string $type, string $languageName, bool $hasRemoteComment, string $commentType): string
        {
        }
        /**
         * The "Search for remote site comments to connect" input markup.
         *
         * @param MetaboxFieldsHelperInterface $helper
         * @return void
         */
        protected function searchRow(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $helper): void
        {
        }
        /**
         * The update relation button markup.
         *
         * @return void
         */
        protected function buttonRow(): void
        {
        }
        /**
         * Creates a new metabox fields helper.
         *
         * @param int $siteId The ID of the site for which to create a helper.
         * @return MetaboxFieldsHelperInterface The new helper.
         * @throws RuntimeException If problem creating.
         */
        protected function createHelper(int $siteId): \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface
        {
        }
    }
    class CommentMetaboxCopyContent implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field\CommentMetaboxField
    {
        public const FILTER_COPY_CONTENT_IS_CHECKED = 'multilingualpress.Comments.copy_content_is_checked';
        protected string $key;
        protected string $label;
        protected \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory;
        public function __construct(string $key, string $label, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory)
        {
        }
        /**
         * @inheritDoc
         */
        public function key(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function label(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext): void
        {
        }
        /**
         * Creates a new metabox fields helper.
         *
         * @param int $siteId The ID of the site for which to create a helper.
         * @return MetaboxFieldsHelperInterface The new helper.
         * @throws RuntimeException If problem creating.
         */
        protected function createHelper(int $siteId): \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface
        {
        }
    }
    class CommentMetaboxStatus implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field\CommentMetaboxField
    {
        public const FILTER_TRANSLATION_UI_POST_STATUSES = 'multilingualpress.translation_ui_comment_statuses';
        protected string $key;
        protected string $label;
        protected \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory;
        public function __construct(string $key, string $label, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory)
        {
        }
        /**
         * @inheritDoc
         */
        public function key(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function label(): string
        {
        }
        /**
         * Available comment statuses.
         *
         * @return array<string> The list of available comment statuses
         */
        protected function availableStatuses(): array
        {
        }
        /**
         * @inheritDoc
         */
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext): void
        {
        }
        /**
         * Creates a new metabox fields helper.
         *
         * @param int $siteId The ID of the site for which to create a helper.
         * @return MetaboxFieldsHelperInterface The new helper.
         * @throws RuntimeException If problem creating.
         */
        protected function createHelper(int $siteId): \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface
        {
        }
    }
    class CommentMetaboxFieldAuthorUrl implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field\CommentMetaboxField
    {
        protected string $label;
        protected \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory;
        public function __construct(string $key, string $label, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory)
        {
        }
        /**
         * @inheritDoc
         */
        public function key(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function label(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext): void
        {
        }
        /**
         * Creates a new metabox fields helper.
         *
         * @param int $siteId The ID of the site for which to create a helper.
         * @return MetaboxFieldsHelperInterface The new helper.
         * @throws RuntimeException If problem creating.
         */
        protected function createHelper(int $siteId): \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface
        {
        }
    }
    class CommentMetaboxFieldAuthorEmail implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field\CommentMetaboxField
    {
        protected \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory;
        protected string $label;
        public function __construct(string $key, string $label, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory)
        {
        }
        /**
         * @inheritDoc
         */
        public function key(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function label(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext): void
        {
        }
        /**
         * Creates a new metabox fields helper.
         *
         * @param int $siteId The ID of the site for which to create a helper.
         * @return MetaboxFieldsHelperInterface The new helper.
         * @throws RuntimeException If problem creating.
         */
        protected function createHelper(int $siteId): \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\TranslationUi {
    class CommentMetaboxView implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\View
    {
        /**
         * @var CommentMetaboxTabInterface[]
         */
        protected array $metaboxTabs;
        protected \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $helper;
        protected \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext;
        public function __construct(array $metaboxTabs, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $helper, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext)
        {
        }
        /**
         * @inheritdoc
         *
         * @psalm-suppress ArgumentTypeCoercion
         */
        public function render(\Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info $info): void
        {
        }
        /**
         * Renders the metabox wrapper div HTML attributes.
         *
         * @return void
         */
        protected function boxDataAttributes(): void
        {
        }
        /**
         * Renders the metabox tab anchors.
         *
         * @param CommentMetaboxTabInterface $tab
         */
        protected function renderTabAnchor(\Inpsyde\MultilingualPress\Module\Comments\TranslationUi\CommentMetaboxTabInterface $tab): void
        {
        }
    }
    class CommentsListViewTranslationColumn implements \Inpsyde\MultilingualPress\Framework\Admin\TranslationColumnInterface
    {
        public const FILTER_SITE_LANGUAGE_TAG = 'multilingualpress.site_language_tag';
        protected string $name;
        protected string $title;
        protected \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations;
        public function __construct(string $name, string $title, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Api\ContentRelationsValidator $contentRelationsValidator)
        {
        }
        /**
         * @inheritDoc
         */
        public function name(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function title(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function value(int $id): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Ajax {
    class AjaxSearchCommentRequestHandler implements \Inpsyde\MultilingualPress\Framework\Http\RequestHandler
    {
        public const ACTION = 'multilingualpress_remote_comment_search';
        public const FILTER_REMOTE_ARGUMENTS = 'multilingualpress.remote_post_search_arguments';
        protected \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextFactoryInterface $relationshipContextFactory;
        protected string $alreadyConnectedNotice;
        public function __construct(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextFactoryInterface $relationshipContextFactory, string $alreadyConnectedNotice)
        {
        }
        /**
         * @inheritDoc
         */
        public function handle(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request): void
        {
        }
        /**
         * Finds the comment for given context
         *
         * @param CommentsRelationshipContextInterface $context
         * @param string $searchQuery
         * @return array
         */
        protected function findComments(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $context, string $searchQuery): array
        {
        }
        /**
         * Creates the relationship context from given request.
         *
         * @param ServerRequest $request
         * @return CommentsRelationshipContextInterface
         * @throws RuntimeException if problem creating.
         */
        protected function createContextFromRequest(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request): \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface
        {
        }
        /**
         * Checks if the comment with given comment ID is connected to any comment from given site ID.
         *
         * @param int $commentId The comment ID.
         * @param int $siteId The site ID.
         * @return bool true if is connected, otherwise false.
         * @throws NonexistentTable
         */
        protected function isConnectedWithCommentOfSite(int $commentId, int $siteId): bool
        {
        }
    }
    class AjaxUpdateCommentsRelationshipRequestHandler implements \Inpsyde\MultilingualPress\Framework\Http\RequestHandler
    {
        public const ACTION = 'multilingualpress_update_comment_relationship';
        protected const AVAILABLE_TASKS = ['new', 'existing', 'remove'];
        protected \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextFactoryInterface $relationshipContextFactory;
        protected \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations;
        /**
         * @var CommentMetaboxTabInterface[]
         */
        protected array $metaboxTabs;
        /**
         * @var CommentMetaboxField[]
         */
        protected array $metaboxFields;
        protected \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $metaboxFieldsHelperFactory;
        protected \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentRelationSaveHelperInterface $commentRelationSaveHelper;
        public function __construct(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextFactoryInterface $relationshipContextFactory, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, array $metaboxTabs, array $metaboxFields, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $metaboxFieldsHelperFactory, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentRelationSaveHelperInterface $commentRelationSaveHelper)
        {
        }
        /**
         * @inheritDoc
         */
        public function handle(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request): void
        {
        }
        /**
         * Creates the relationship context from given request.
         *
         * @param ServerRequest $request
         * @return CommentsRelationshipContext
         * @throws RuntimeException if problem creating.
         */
        protected function createContextFromRequest(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request): \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContext
        {
        }
        /**
         * Configures the given context.
         *
         * @param CommentsRelationshipContext $context
         * @return CommentsRelationshipContext
         * @throws NonexistentTable
         *
         * @psalm-suppress ArgumentTypeCoercion
         */
        protected function configureContext(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContext $context): \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContext
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\SiteSettings {
    class CommentSettingViewModel implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        public const ACTION_AFTER_RELATED_SITE_OPTION = 'multilingualpress.Comments.after_related_site_option';
        protected \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations;
        /**
         * @var array<SettingOptionInterface>
         */
        protected array $options;
        protected string $postType;
        protected \Inpsyde\MultilingualPress\Module\Comments\SiteSettings\CommentsSettingsRepositoryInterface $siteTabSettingsRepository;
        public function __construct(array $options, \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \Inpsyde\MultilingualPress\Module\Comments\SiteSettings\CommentsSettingsRepositoryInterface $siteTabSettingsRepository, string $postType)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId): void
        {
        }
        /**
         * Renders the options for given source site.
         *
         * @param int $sourceSiteId The source site ID.
         * @param int $remoteSiteId The Remote site ID.
         * @param SettingOptionInterface $option The option.
         * @return void
         * @throws NonexistentTable
         */
        protected function renderOption(int $sourceSiteId, int $remoteSiteId, \Inpsyde\MultilingualPress\Framework\Setting\SettingOptionInterface $option): void
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
        /**
         * Returns the comment setting option name for given post type.
         *
         * @param string $postType The post type name.
         * @param string $optionId The option ID name.
         * @return string The option name.
         */
        protected function fieldName(string $postType, string $optionId): string
        {
        }
    }
    /**
     * The repository for comment settings.
     *
     * @psalm-type PostTypeName = string
     * @psalm-type OptionName = string
     * @psalm-type siteIds = list<int>
     * @psalm-type CommentSettings = array<PostTypeName, array<OptionName, siteIds>>
     */
    interface CommentsSettingsRepositoryInterface
    {
        /**
         * Gets the given setting option value for the post type of the site.
         *
         * @param string $optionName The option name.
         * @param string $postTypeName The post type name.
         * @param int $siteId The site ID.
         * @return int[] The list of site IDs.
         */
        public function settingOptionValue(string $optionName, string $postTypeName, int $siteId): array;
        /**
         * Gets all comment IDs of a given site for a given post types.
         *
         * @param string[] $postTypes The post type names.
         * @param int $siteId The site ID.
         * @return int[] A list of comment IDs.
         */
        public function postTypeComments(array $postTypes, int $siteId): array;
        /**
         * Gets the comments settings of a given site.
         *
         * @param int $siteId The site ID.
         * @return array<string, array<string, int[]>> The map of post type names to comment setting option values.
         * @psalm-return CommentSettings
         */
        public function allSettings(int $siteId): array;
    }
    /**
     * @psalm-type PostTypeName = string
     * @psalm-type OptionName = string
     * @psalm-type siteIds = list<int>
     * @psalm-type CommentSettings = array<PostTypeName, array<OptionName, siteIds>>
     */
    class CommentSettingsUpdater implements \Inpsyde\MultilingualPress\Framework\Setting\SiteSettingsUpdatable
    {
        public const ACTION_AFTER_COMMENT_SITE_SETTINGS_ARE_UPDATED = 'multilingualpress,after_comment_settings_are_updated';
        public const FILTER_COMMENT_SITE_SETTINGS_BEFORE_SAVE = 'multilingualpress.comment_settings';
        protected \Inpsyde\MultilingualPress\Framework\Http\Request $request;
        protected \Inpsyde\MultilingualPress\Module\Comments\CommentsCopy\CommentsCopierInterface $commentsCopier;
        protected \Inpsyde\MultilingualPress\Module\Comments\SiteSettings\CommentsSettingsRepositoryInterface $commentsSettingsRepository;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Module\Comments\CommentsCopy\CommentsCopierInterface $commentsCopier, \Inpsyde\MultilingualPress\Module\Comments\SiteSettings\CommentsSettingsRepositoryInterface $commentsSettingsRepository)
        {
        }
        /**
         * @inheritdoc
         */
        public function defineInitialSettings(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function updateSettings(int $siteId): void
        {
        }
        /**
         * Updates the comment settings for the given site.
         *
         * @param array<string, array<string, int[]>> $commentsSettings The map of post type names to comment option values.
         * @psalm-param CommentSettings $commentsSettings
         * @param int $siteId The site ID.
         */
        protected function updateCommentSettings(array $commentsSettings, int $siteId): void
        {
        }
        /**
         * Returns the comment setting option values by given name.
         *
         * @param array<string, array<string, int[]>> $commentsSettings The map of post type names to comment option values.
         * @psalm-param CommentSettings $commentsSettings
         * @param string $optionName The comment setting option name.
         * @return array The map of post type names to comment setting option values.
         * @psalm-return CommentSettings
         */
        protected function commentSettingOptionValuesToSave(array $commentsSettings, string $optionName): array
        {
        }
    }
    class CommentsSettingsRepository implements \Inpsyde\MultilingualPress\Module\Comments\SiteSettings\CommentsSettingsRepositoryInterface
    {
        public const COMMENTS_TAB_UPDATE_ACTION_NAME = 'update_multilingualpress_comments_site_settings';
        public const COMMENTS_TAB_NONCE_NAME = 'save_site_comment_settings';
        public const COMMENTS_TAB_SETTING = 'mlp_site_comments';
        public const COMMENTS_TAB_OPTION_COPY_COMMENTS = 'comments_copy';
        public const COMMENTS_TAB_OPTION_COPY_NEW_COMMENT = 'copy_new_comment';
        public const FILTER_COMMENTS_ENABLED_FOR_POST_TYPE = 'multilingualpress.are_comments_enabled_for_post_type';
        public function settingOptionValue(string $optionName, string $postTypeName, int $siteId): array
        {
        }
        public function postTypeComments(array $postTypes, int $siteId): array
        {
        }
        public function allSettings(int $siteId): array
        {
        }
    }
    class CommentSettingsView implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView
    {
        public const ACTION_AFTER = 'multilingualpress.after_site_tab_settings';
        public const ACTION_BEFORE = 'multilingualpress.before_site_tab_settings';
        protected \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionViewModel $model;
        /**
         * @param SiteSettingsSectionViewModel $model
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionViewModel $model)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId): bool
        {
        }
    }
    class CommentSettingsPageView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        protected string $action;
        protected \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabData $data;
        protected \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce;
        protected int $siteId;
        protected \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView $view;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabData $data, \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView $view, int $siteId, string $action, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli {
    /**
     * @psalm-type Arg = array{type: string, name: string, description?: string, optional?: bool, options?: list<string>}
     * @psalm-type Doc = array{shortdesc?: string, synopsis?: list<Arg>, when?: string, longdesc?: string}
     */
    interface WpCliCommand
    {
        /**
         * The handler of
         * {@link https://make.wordpress.org/cli/handbook/references/internal-api/wp-cli-add-command/ WP_CLI::add_command}
         * implementation.
         *
         * @param array<string> $args The list of positional arguments
         * @param array<string, string> $associativeArgs A map of associative argument names to values
         * @return void
         * @throws WP_CLI\ExitException
         */
        public function handler(array $args, array $associativeArgs): void;
        /**
         * The command documentation.
         *
         * @psalm-return Doc A map of
         * {@link https://make.wordpress.org/cli/handbook/references/documentation-standards/ command doc} names to values
         * @return array<string, string|array> A map of
         * {@link https://make.wordpress.org/cli/handbook/references/documentation-standards/ command doc} names to values
         */
        public function docs(): array;
        /**
         * The Name of the command
         *
         * @return string The Name of the command
         */
        public function name(): string;
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\WpCli {
    class ListCommentRelations implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationsListBuilder $relationsListBuilder)
        {
        }
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
        public function buildRecord(int $siteId, int $relatedCommentId): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli\Commands\Relations\Validators {
    interface EntitiesTypeMatchValidator
    {
        public function validate(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data): void;
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\WpCli {
    class CommentTypeMatchValidator implements \Inpsyde\MultilingualPress\WpCli\Commands\Relations\Validators\EntitiesTypeMatchValidator
    {
        public function validate(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli\Commands\Relations\Validators {
    interface EntityIdValidator
    {
        public function validate(int $entityId, int $siteId, string $entityOriginType): void;
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\WpCli {
    class CommentIdValidator implements \Inpsyde\MultilingualPress\WpCli\Commands\Relations\Validators\EntityIdValidator
    {
        public function validate(int $entityId, int $siteId, string $entityOriginType): void
        {
        }
    }
    class DeleteCommentRelation implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationHandler $relationHandler)
        {
        }
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
    }
    class CreateCommentRelation implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationHandler $relationHandler)
        {
        }
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli\Commands\Relations {
    interface RelationDataToConfirmationMessage
    {
        public function prepare(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data): array;
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\WpCli {
    class CommentConfirmation implements \Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataToConfirmationMessage
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\ContentRelationsFetcher $contentRelationsFetcher, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\ContentRelationsChecker $contentRelationsChecker)
        {
        }
        // phpcs:ignore Inpsyde.CodeQuality.FunctionLength.TooLong
        public function prepare(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments {
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'mlp-comments';
        public const MODULE_SCRIPTS_HANDLER_NAME = 'multilingualpress-comments-site-settings';
        public const CONFIGURATION_NAME_FOR_URL_TO_MODULE_ASSETS = 'multilingualpress.comments.urlToModuleAssets';
        /**
         * @inheritDoc
         * @param ModuleManager $moduleManager
         * @return bool
         * @throws ModuleAlreadyRegistered
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         * @throws LateAccessToNotSharedService
         * @throws NameNotFound|Throwable
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Bootstraps frontend functionality.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService
         * @throws NameNotFound|Throwable
         */
        protected function bootstrapFrontEnd(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Bootstraps admin functionality.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService|NameNotFound|NonexistentTable
         */
        protected function bootstrapAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Bootstraps Network admin functionality.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService|NameNotFound
         */
        protected function bootstrapNetworkAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Bootstraps the translation metaboxes for comments.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService|NameNotFound|NonexistentTable
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function bootstrapMetaboxes(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Will add the custom translation column in comments list view admin screen.
         *
         * @param TranslationColumnInterface $translationColumn
         */
        protected function bootstrapTranslationColumnForListView(\Inpsyde\MultilingualPress\Framework\Admin\TranslationColumnInterface $translationColumn): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\CommentsCopy {
    /**
     * Can copy the comments to the given sites.
     */
    interface CommentsCopierInterface
    {
        /**
         * Copies the comments by given comment IDs from give source site ID to the sites by given site IDs.
         *
         * @param int $sourceSiteId The source site ID.
         * @param int[] $sourceCommentIds The list of comment IDs.
         * @param int[] $remoteSiteIds The list of site IDs.
         * @throws RuntimeException If problem copying.
         */
        public function copyCommentsToSites(int $sourceSiteId, array $sourceCommentIds, array $remoteSiteIds): void;
    }
    class CommentsCopier implements \Inpsyde\MultilingualPress\Module\Comments\CommentsCopy\CommentsCopierInterface
    {
        public const ACTION_AFTER_REMOTE_COMMENT_IS_INSERTED = 'multilingualpress.after_remote_comment_is_inserted';
        public const FILTER_COMMENT_CONTENT_BEFORE_INSERT = 'multilingualpress.comment_content_before_insert';
        protected \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextFactoryInterface $relationshipContextFactory;
        protected \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentRelationSaveHelperInterface $commentRelationSaveHelper;
        public function __construct(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextFactoryInterface $relationshipContextFactory, \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentRelationSaveHelperInterface $commentRelationSaveHelper)
        {
        }
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function copyCommentsToSites(int $sourceSiteId, array $sourceCommentIds, array $remoteSiteIds): void
        {
        }
        /**
         * Inserts the given comment to the given site.
         *
         * @param array $comment A map of WP_Comment fields to values.
         * @param int $siteId The site ID.
         * @return int The inserted comment id.
         * @throws RuntimeException|NonexistentTable If problem inserting.
         */
        protected function insertComment(array $comment, int $siteId): int
        {
        }
        /**
         * Checks if the comment connection already exists in given remote site.
         *
         * @param int $commentId The comment ID.
         * @param int $sourceSiteId The source site ID.
         * @param int $remoteSiteId The remote site ID.
         * @return bool true if the comment connection exists, otherwise false.
         */
        protected function commentConnectionExistsInSite(int $commentId, int $sourceSiteId, int $remoteSiteId): bool
        {
        }
        /**
         * Returns the remote comment parent ID.
         *
         * @param int $commentParentId The source comment parent ID.
         * @param int $sourceSiteId The source site ID.
         * @param int $remoteSiteId The remote site ID.
         * @return int The remote comment parent ID.
         */
        protected function remoteCommentParent(int $commentParentId, int $sourceSiteId, int $remoteSiteId): int
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Comments\RelationshipContext {
    /**
     * Can save the relationship for comments.
     */
    interface CommentRelationSaveHelperInterface
    {
        /**
         * Relates the comments of given relationship context.
         *
         * @param CommentsRelationshipContextInterface $context
         * @throws RuntimeException If problem relating.
         */
        public function relateComments(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $context): void;
        /**
         * Disconnects the comments of given relationship context.
         *
         * @param CommentsRelationshipContextInterface $context
         * @throws RuntimeException If problem disconnecting.
         */
        public function disconnectComments(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $context): void;
    }
    class CommentRelationSaveHelper implements \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentRelationSaveHelperInterface
    {
        public const ACTION_BEFORE_SAVE_COMMENT_RELATIONS = 'multilingualpress.before_save_comment_relations';
        public const ACTION_AFTER_SAVED_COMMENTS_RELATIONS = 'multilingualpress.after_saved_comment_relations';
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @inheritDoc
         * @throws NonexistentTable
         */
        public function relateComments(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $context): void
        {
        }
        /**
         * @inheritDoc
         */
        public function disconnectComments(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $context): void
        {
        }
    }
    interface CommentsRelationshipContextInterface
    {
        /**
         * The remote comment ID.
         *
         * @return int
         */
        public function remoteCommentId(): int;
        /**
         * The remote comment object.
         *
         * @return WP_Comment|null
         */
        public function remoteComment(): ?\WP_Comment;
        /**
         * The remote post ID.
         *
         * @return int
         */
        public function remotePostId(): ?int;
        /**
         * The remote site ID.
         *
         * @return int
         */
        public function remoteSiteId(): int;
        /**
         * The remote comment parent comment ID.
         *
         * @return int
         */
        public function remoteCommentParentId(): ?int;
        /**
         * Returns whether the comment has connection.
         *
         * @return bool
         */
        public function hasRemoteComment(): bool;
        /**
         * The source comment ID.
         *
         * @return int
         */
        public function sourceCommentId(): int;
        /**
         * The source site ID.
         *
         * @return int
         */
        public function sourceSiteId(): int;
        /**
         * The source comment object.
         *
         * @return WP_Comment|null
         */
        public function sourceComment(): ?\WP_Comment;
        /**
         * Print HTML fields for the relationship context.
         *
         * @param MetaboxFieldsHelperInterface $helper
         */
        public function renderFields(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $helper): void;
    }
    class CommentsRelationshipContext implements \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface
    {
        public const REMOTE_COMMENT_ID = 'remote_comment_id';
        public const REMOTE_POST_ID = 'remote_post_id';
        public const REMOTE_SITE_ID = 'remote_site_id';
        public const SOURCE_COMMENT_ID = 'source_comment_id';
        public const SOURCE_SITE_ID = 'source_site_id';
        protected const DEFAULTS = [self::REMOTE_COMMENT_ID => 0, self::REMOTE_POST_ID => 0, self::REMOTE_SITE_ID => 0, self::SOURCE_COMMENT_ID => 0, self::SOURCE_SITE_ID => 0];
        /**
         * @var array<WP_Comment|null>
         */
        protected array $comments = [];
        protected array $data = [];
        /**
         * Returns a new context object, instantiated according to the data in the given context object
         * and the array.
         *
         * @param CommentsRelationshipContext $context
         * @param array $data
         * @return CommentsRelationshipContext
         */
        public static function fromExistingAndData(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContext $context, array $data): \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContext
        {
        }
        final public function __construct(array $data = [])
        {
        }
        /**
         * @inheritDoc
         */
        public function remoteCommentId(): int
        {
        }
        /**
         * @inheritDoc
         */
        public function remoteComment(): ?\WP_Comment
        {
        }
        /**
         * @inheritDoc
         */
        public function remotePostId(): ?int
        {
        }
        /**
         * @inheritDoc
         */
        public function remoteSiteId(): int
        {
        }
        /**
         * @inheritDoc
         */
        public function remoteCommentParentId(): ?int
        {
        }
        /**
         * @inheritDoc
         */
        public function hasRemoteComment(): bool
        {
        }
        /**
         * @inheritDoc
         */
        public function sourceCommentId(): int
        {
        }
        /**
         * @inheritDoc
         */
        public function sourceSiteId(): int
        {
        }
        /**
         * @inheritDoc
         *
         * @psalm-suppress InvalidArgument
         */
        public function sourceComment(): \WP_Comment
        {
        }
        /**
         * Returns the comment object from given site by given type.
         *
         * @param int $siteId The site ID.
         * @param string $type The type: source or remote.
         * @return WP_Comment|null
         */
        protected function commentByType(int $siteId, string $type): ?\WP_Comment
        {
        }
        /**
         * @inheritDoc
         */
        public function renderFields(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $helper): void
        {
        }
    }
    /**
     * Can create relationship context for comments.
     */
    interface CommentsRelationshipContextFactoryInterface
    {
        /**
         * Creates new relationship context for comments.
         *
         * @param int $sourceSiteId The source site ID.
         * @param int $remoteSiteId The remote site ID.
         * @param int $sourceCommentId The source comment ID.
         * @param int $remoteCommentId The remote comment ID.
         * @return CommentsRelationshipContext The new instance.
         * @throws RuntimeException If problem creating.
         */
        public function createCommentsRelationshipContext(int $sourceSiteId, int $remoteSiteId, int $sourceCommentId, int $remoteCommentId): \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContext;
    }
    class CommentsRelationshipContextFactory implements \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextFactoryInterface
    {
        /**
         * @inheritDoc
         */
        public function createCommentsRelationshipContext(int $sourceSiteId, int $remoteSiteId, int $sourceCommentId, int $remoteCommentId): \Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContext
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce {
    /**
     * Class AvailableTaxonomiesAttributes
     */
    class AvailableTaxonomiesAttributes
    {
        /**
         * Remove Attributes from the list of the translatable taxonomies in the settings ui
         *
         * @param array $taxonomies
         * @return array
         */
        public function removeAttributes(array $taxonomies): array
        {
        }
    }
    /**
     * Class AttributeTermTranslateUrl
     *
     * @psalm-suppress PropertyNotSetInConstructor
     */
    class AttributeTermTranslateUrl implements \Inpsyde\MultilingualPress\Framework\Filter\Filter
    {
        use \Inpsyde\MultilingualPress\Framework\Filter\FilterTrait;
        /**
         * AttributeTermTranslateUrlFilter constructor.
         *
         * @param wpdb $wpdb
         * @param UrlFactory $urlFactory
         */
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Framework\Factory\UrlFactory $urlFactory)
        {
        }
        /**
         * Retrieve the term link page by his term taxonomy Id.
         *
         * @param bool $checker
         * @param Translation $translation
         * @param int $siteId
         * @param TranslationSearchArgs $args
         * @return bool
         */
        public function termLinkByTaxonomyId(bool $checker, \Inpsyde\MultilingualPress\Framework\Api\Translation $translation, int $siteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args): bool
        {
        }
        /**
         * Lazy inject for \WP_Rewrite
         *
         * @param WP_Rewrite $wp_rewrite
         * @return bool
         */
        public function ensureWpRewrite(\WP_Rewrite $wp_rewrite = null): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product {
    /**
     * MultilingualPress WooCommerce Metabox Fields
     */
    class MetaboxFields
    {
        public const TAB = 'tab-product';
        public const FIELD_PRODUCT_URL = 'product_url';
        public const FIELD_PRODUCT_URL_BUTTON_TEXT = 'button_text';
        public const FIELD_OVERRIDE_PRODUCT_TYPE = 'override_product_type';
        public const FIELD_OVERRIDE_PRODUCT_GALLERY = 'override_product_gallery';
        public const FIELD_OVERRIDE_VARIATIONS = 'override_attribute_variations';
        public const FIELD_OVERRIDE_ATTRIBUTES = 'override_attributes';
        public const FIELD_OVERRIDE_DOWNLOADABLE_FILES = 'override_downloadable_files';
        public const FIELD_OVERRIDE_DOWNLOADABLE_SETTINGS = 'override_downloadable_settings';
        public const FIELD_OVERRIDE_INVENTORY_SETTINGS = 'override_inventory_settings';
        public const FIELD_REGULAR_PRICE = 'regular_price';
        public const FIELD_SALE_PRICE = 'sale_price';
        public const FIELD_PRODUCT_SHORT_DESCRIPTION = 'product_short_description';
        public const FIELD_PURCHASE_NOTE = 'purchase_note';
        public const FIELD_SKU = 'sku';
        public const FIELD_GLOBAL_UNIQUE_ID = 'global_unique_id';
        public const FIELD_MANAGE_STOCK = 'manage_stock';
        public const FIELD_SOLD_INDIVIDUALLY = 'sold_individually';
        public const FIELD_STOCK = 'stock';
        public const FIELD_BACKORDERS = 'backorders';
        public const FIELD_STOCK_STATUS = 'stock_status';
        public const FIELD_LOW_STOCK_AMOUNT = 'low_stock_amount';
        public const FIELD_GROUPED_PRODUCTS = 'grouped_products';
        public const FIELD_CROSSELLS_PRODUCTS = 'crossells_products';
        public const FIELD_UPSELLS_PRODUCTS = 'upsells_products';
        /**
         * MetaboxFields constructor.
         * @param WooCommerceMetaboxFields $wooCommerceFields
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\WooCommerceMetaboxFields $wooCommerceFields)
        {
        }
        /**
         * Retrieve all fields for the WooCommerce metabox tab.
         *
         * @return MetaboxTab[]
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function allFieldsTabs(): array
        {
        }
        public static function isFieldSupported(string $field): bool
        {
        }
    }
    /**
     * Class FieldsAwareOfProductType
     */
    class FieldsAwareOfProductType
    {
        protected const OPTIONS = [\Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxFields::FIELD_OVERRIDE_VARIATIONS, \Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxFields::FIELD_GROUPED_PRODUCTS, \Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxFields::FIELD_PRODUCT_URL, \Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxFields::FIELD_PRODUCT_URL_BUTTON_TEXT];
        /**
         * Check if the same product type is needed based on the give values and the options
         *
         * @param array $values
         * @return bool
         */
        public static function needSameProductType(array $values): bool
        {
        }
    }
    /**
     * Class MetaboxAction
     * @package Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product
     */
    final class MetaboxAction implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
    {
        public const RELATIONSHIP_TYPE = 'post';
        public const DEFAULT_PRODUCT_TYPE = 'simple';
        public const PRODUCT_TYPE_TAXONOMY_NAME = 'product_type';
        public const PRODUCT_TYPE_FIELD_NAME = 'product-type';
        public const PRODUCT_GALLERY_META_KEY = 'product_image_gallery';
        // phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
        public const ACTION_METABOX_BEFORE_UPDATE_REMOTE_PRODUCT = 'multilingualpress.metabox_before_update_remote_product';
        public const ACTION_METABOX_AFTER_UPDATE_REMOTE_PRODUCT = 'multilingualpress.metabox_after_update_remote_product';
        public const ACTION_METABOX_AFTER_SAVE_REMOTE_PRODUCT_VARIATIONS = 'multilingualpress.metabox_after_save_remote_product_variations';
        /**
         * MetaboxAction constructor
         *
         * @param Post\RelationshipContext $postRelationshipContext
         * @param ActivePostTypes $postTypes
         * @param ContentRelations $contentRelations
         * @param Attachment\Copier $attachmentCopier
         * @param MetaboxFields $metaboxFields
         * @param PersistentAdminNotices $notice
         */
        public function __construct(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $postRelationshipContext, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $postTypes, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Attachment\Copier $attachmentCopier, \Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxFields $metaboxFields, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notice)
        {
        }
        /**
         * @inheritdoc
         */
        public function save(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\SearchRepository {
    /**
     * Modifies the GROUP BY clause.
     */
    class WcSearchGroupByClauseFilter implements \Inpsyde\MultilingualPress\Framework\Filter\Filter
    {
        use \Inpsyde\MultilingualPress\Framework\Filter\FilterTrait;
        public function __construct(\wpdb $wpdb)
        {
        }
        /**
         * Modifies the GROUP BY clause for WooCommerce product searches to group results by post ID.
         *
         * @wp-hook posts_groupby
         *
         * @param string $groupBy
         * @param \WP_Query $wpQuery
         *
         * @return string
         */
        public function modifySearchGroupByClause(string $groupBy, \WP_Query $wpQuery): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Post\SearchRepository {
    /**
     * Modifies the WHERE clause.
     */
    class SearchWhereClauseFilter implements \Inpsyde\MultilingualPress\Framework\Filter\Filter
    {
        use \Inpsyde\MultilingualPress\Framework\Filter\FilterTrait;
        protected \wpdb $wpdb;
        public function __construct(\wpdb $wpdb)
        {
        }
        /**
         * Modifies the WHERE clause to include searches by slug for all post types.
         *
         * @wp-hook posts_search
         *
         * @param string $where
         * @param \WP_Query $wpQuery
         *
         * @return string
         */
        public function modifySearchWhereClause(string $where, \WP_Query $wpQuery): string
        {
        }
        /**
         * Adds an OR clause to the existing WHERE clause.
         *
         * @param string $where
         * @param string $orClause
         *
         * @return string
         */
        protected function addOrClauseToWhere(string $where, string $orClause): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\SearchRepository {
    /**
     * Modifies the WHERE clause.
     */
    class WcSearchWhereClauseFilter extends \Inpsyde\MultilingualPress\TranslationUi\Post\SearchRepository\SearchWhereClauseFilter
    {
        /**
         * Modifies the WHERE clause to include searches by SKU for product post type.
         *
         * @param string $where
         * @param \WP_Query $wpQuery
         *
         * @return string
         */
        public function modifySearchWhereClause(string $where, \WP_Query $wpQuery): string
        {
        }
    }
    /**
     * Modifies the JOIN clause.
     */
    class WcSearchJoinClauseFilter implements \Inpsyde\MultilingualPress\Framework\Filter\Filter
    {
        use \Inpsyde\MultilingualPress\Framework\Filter\FilterTrait;
        public function __construct(\wpdb $wpdb)
        {
        }
        /**
         * Modifies the JOIN clause for WooCommerce product searches by adding
         * a LEFT JOIN with the postmeta table.
         *
         * @wp-hook posts_join
         *
         * @param string $join
         * @param \WP_Query $wpQuery
         *
         * @return string
         */
        public function modifySearchJoinClause(string $join, \WP_Query $wpQuery): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Post {
    interface PostMetaboxField
    {
        /**
         * @return string
         */
        public function key();
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): void;
        /**
         * @param Request $request
         * @param MetaboxFieldsHelper $helper
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function requestValue(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper);
        /**
         * @param RelationshipContext $relationshipContext
         * @return bool
         */
        public function enabled(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): bool;
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product {
    /**
     * MultilingualPress WooCommerce Metabox Field Interface
     */
    interface WooCommerceMetaboxField extends \Inpsyde\MultilingualPress\TranslationUi\Post\PostMetaboxField
    {
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Post {
    /**
     * Interface for any kind of custom Metabox tabs.
     */
    interface MetaboxFillable
    {
        /**
         * The id of the metabox tab.
         *
         * @return string
         */
        public function id(): string;
        /**
         * The label to show to the tab header.
         *
         * @return string
         */
        public function label(): string;
        /**
         * The fields collection for the current tab.
         *
         * @return PostMetaboxField[]
         */
        public function fields(): array;
        /**
         * If the metabox tab is enabled or not.
         *
         * @param RelationshipContext $relationshipContext
         * @return bool
         */
        public function enabled(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): bool;
        /**
         * Render the metabox markup.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): void;
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product {
    /**
     * MultilingualPress MetaboxTab for Product
     */
    final class MetaboxTab implements \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFillable
    {
        public const ACTION_BEFORE_METABOX_UI_PANEL = 'multilingualpress.before_metabox_panel';
        public const ACTION_AFTER_METABOX_UI_PANEL = 'multilingualpress.after_metabox_panel';
        public const ACTION_AFTER_TRANSLATION_UI_TAB = 'multilingualpress.after_translation_ui_tab';
        public const ACTION_BEFORE_TRANSLATION_UI_TAB = 'multilingualpress.before_translation_ui_tab';
        public const FILTER_TRANSLATION_UI_SHOW_CONTENT = 'multilingualpress.translation_ui_show_content';
        public function __construct(string $id, string $label, \Inpsyde\MultilingualPress\TranslationUi\Post\PostMetaboxField ...$fields)
        {
        }
        /**
         * @inheritdoc
         */
        public function id(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function label(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function fields(): array
        {
        }
        /**
         * @inheritdoc
         */
        public function enabled(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): void
        {
        }
    }
    /**
     *  WooCommerce Settings Fields
     */
    final class SettingView
    {
        /**
         * Setting constructor.
         * @param string $name
         * @param MetaboxField ...$fields
         */
        public function __construct(string $name, \Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxField ...$fields)
        {
        }
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
    }
    /**
     * Class SettingsView
     */
    class PanelView
    {
        /**
         * SettingsView constructor.
         * @param callable ...$settings
         */
        public function __construct(callable ...$settings)
        {
        }
        /**
         * Render the settings fields.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): void
        {
        }
    }
    /**
     * MultilingualPress Metabox Fields for WooCommerce Panel
     */
    class WooCommerceMetaboxFields
    {
        /**
         * Build the WooCommerce General metabox fields
         *
         * @return array
         */
        public function generalSettingFields(): array
        {
        }
        /**
         * Build the WooCommerce Invetory metabox fields
         *
         * @return array
         */
        //phpcs:ignore Inpsyde.CodeQuality.FunctionLength.TooLong
        public function inventorySettingFields(): array
        {
        }
        /**
         * Build the WooCommerce Advanced metabox fields
         *
         * @return array
         */
        public function advancedSettingFields(): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field {
    /**
     * Class OverrideCrossellsProducts
     */
    final class OverrideCrossellsProducts
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    /**
     * MultilingualPress override the WooCommerce downloadable product data
     */
    class OverrideDownloadableFiles
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    /**
     * MultilingualPress Product Url Field
     */
    class ProductUrl
    {
        /**
         * Render the Product Url Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return mixed|void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
    }
    final class OverrideGroupedProducts
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    /**
     * MultilingualPress Product Regular Price Field
     */
    class RegularPrice
    {
        /**
         * Render the Product Regular Price Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return mixed|void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
    }
    /**
     * Class OverrideVariations
     */
    class OverrideVariations
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class OverrideAttributes
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    /**
     * MultilingualPress Purchase Note Field
     */
    class PurchaseNote
    {
        /**
         * Render the Purchase Note Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return mixed|void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
    }
    /**
     * MultilingualPress Product Sale Price Field
     */
    class SalePrice
    {
        /**
         * Render the Product Sale Price Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return mixed|void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
    }
    /**
     * MultilingualPress Product Url Button Text Field
     */
    class ProductUrlButtonText
    {
        /**
         * Render the Product Url Button Text Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return mixed|void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
    }
    /**
     * Class OverrideUpsellsProducts
     */
    class OverrideUpsellsProducts
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Post {
    interface RenderCallback
    {
        /**
         * The callback to render the field settings.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext);
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field\Inventory {
    /**
     * MultilingualPress Product Inventory Field
     */
    class Backorders implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        /**
         * Backorders constructor.
         *
         * @param array $backorderOptions A map of Woo backorder field options
         */
        public function __construct(array $backorderOptions)
        {
        }
        /**
         * Render the Manage Backorders Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Build Description ToolTip
         *
         * @return string
         */
        protected function descriptionTooltip(): string
        {
        }
        /**
         * Retrieve the value for the input field.
         *
         * @param RelationshipContext $relationshipContext
         * @return string
         */
        protected function value(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): string
        {
        }
    }
    /**
     * MultilingualPress Product Inventory Field
     */
    class StockStatus implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        /**
         * Render the Stock Status Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Build Description ToolTip
         *
         * @return string
         */
        protected function descriptionTooltip(): string
        {
        }
        /**
         * Retrieve the value for the input field.
         *
         * @param RelationshipContext $relationshipContext
         * @return string
         */
        protected function value(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): string
        {
        }
    }
    /**
     * MultilingualPress Product Inventory Field
     */
    class Stock implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        /**
         * Render the Stock Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Build Description ToolTip
         *
         * @return string
         */
        protected function descriptionTooltip(): string
        {
        }
        /**
         * Retrieve the value for the input field.
         *
         * @param RelationshipContext $relationshipContext
         * @return int
         */
        protected function value(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): int
        {
        }
    }
    /**
     * MultilingualPress Product General Field
     */
    class Sku implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        /**
         * Render the Sku Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
    }
    class GlobalUniqueId implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        /**
         * Render the global unique id Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
    }
    /**
     * MultilingualPress Product Inventory Field
     */
    class ManageStock implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        /**
         * Render the Manage Stock Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
    }
    /**
     * MultilingualPress Product Inventory Field
     */
    class SoldIndividually implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        /**
         * Render the Sold Individually Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
    }
    /**
     * MultilingualPress override the WooCommerce Inventory product data
     */
    class OverrideInventorySettings implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        /**
         * Render the Override Inventory setting field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
    }
    /**
     * MultilingualPress Product Inventory Field
     */
    class LowStockAmount implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        /**
         * LowStockAmount constructor.
         *
         * @param int $lowStockAmount Woo Store-wide threshold amount value
         */
        public function __construct(int $lowStockAmount)
        {
        }
        /**
         * Render the Manage Low Stock Amount Field.
         *
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext)
        {
        }
        /**
         * Build Description ToolTip
         *
         * @return string
         */
        protected function descriptionTooltip(): string
        {
        }
        /**
         * Create the placeholder text for Low Stock Amount field
         *
         * @return string
         */
        protected function placeholder(): string
        {
        }
        /**
         * Retrieve the value for the input field.
         *
         * @param RelationshipContext $relationshipContext
         * @return int
         */
        protected function value(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): int
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field {
    /**
     * MultilingualPress override WooCommerce product short description
     */
    class ShortDescription
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    /**
     * MultilingualPress override the WooCommerce product type
     */
    class OverrideProductType
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    /**
     * MultilingualPress override the WooCommerce downloadable product data
     */
    class OverrideDownloadableSettings
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class OverrideProductGallery
    {
        /**
         * @inheritdoc
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product {
    /**
     * Class ProductRelationSaveHelper
     */
    class ProductRelationSaveHelper
    {
        use \Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
        /**
         * ProductRelationSaveHelper constructor
         *
         * @param Request $request
         * @param ContentRelations $contentRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @param array $attributes
         * @return array
         */
        public function filterProductCustomAttributes(array $attributes): array
        {
        }
        /**
         * @param array $attributes
         * @return array
         */
        public function filterProductAttributesTerms(array $attributes): array
        {
        }
        /**
         * Retrieve related remote terms and create them in the remote site if necessary.
         *
         * @param array $sourceTermsIds
         * @param int $sourceSiteId
         * @param int $remoteSiteId
         * @param string $taxonomyName
         * @return array
         *
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         */
        public function mayRelateTerms(array $sourceTermsIds, int $sourceSiteId, int $remoteSiteId, string $taxonomyName): array
        {
        }
        /**
         * Get the remote product based on the product type value given in the current request
         *
         * @param int $sourceSiteId
         * @param int $sourceProductId
         * @param int $remoteSiteId
         * @param bool $overrideProductType
         * @return \WC_Product
         * @throws \DomainException
         */
        public function remoteProduct(int $sourceSiteId, int $sourceProductId, int $remoteSiteId, bool $overrideProductType): \WC_Product
        {
        }
        /**
         * Retrieve the related products by a specific remote site
         *
         * @param int $sourceSiteId
         * @param int $remoteSiteId
         * @param array $productsIds
         * @return array
         */
        public function relatedProductsForSiteId(int $sourceSiteId, int $remoteSiteId, array $productsIds): array
        {
        }
        /**
         * @param Post\RelationshipContext $context
         * @param \WC_Product_Variation $sourceVariation
         * @return array
         */
        public function relatedAttributeTerms(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, \WC_Product_Variation $sourceVariation): array
        {
        }
        /**
         * Relate terms between source and remote site
         *
         * @param int $sourceSiteId
         * @param int $sourceTermId
         * @param int $remoteSiteId
         * @param int $remoteTermId
         */
        protected function relateTerms(int $sourceSiteId, int $sourceTermId, int $remoteSiteId, int $remoteTermId): void
        {
        }
        /**
         * Create a term if not exists in the current site.
         *
         * @param int $remoteTermId
         * @param string $sourceTermName
         * @param string $taxonomyName
         * @return mixed
         *
         * @phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        protected function maybeTerm(int $remoteTermId, string $sourceTermName, string $taxonomyName)
        {
        }
    }
    /**
     * MultilingualPress WooCommerce Metabox Field
     *
     * This class is a proxy to Post\MetaboxField
     */
    final class MetaboxField implements \Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\WooCommerceMetaboxField
    {
        /**
         * MetaboxField constructor.
         * @param PostMetaboxField $metaboxField
         */
        public function __construct(\Inpsyde\MultilingualPress\TranslationUi\Post\PostMetaboxField $metaboxField)
        {
        }
        /**
         * @inheritdoc
         */
        public function key()
        {
        }
        /**
         * @inheritdoc
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): void
        {
        }
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function requestValue(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper)
        {
        }
        /**
         * @inheritdoc
         */
        public function enabled(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Review\Field {
    class CommentMetaboxReviewRating implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field\CommentMetaboxField
    {
        protected string $label;
        protected \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory;
        public function __construct(string $key, string $label, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory)
        {
        }
        /**
         * @inheritDoc
         */
        public function key(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function label(bool $hasRemoteComment = \false): string
        {
        }
        /**
         * @inheritDoc
         */
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext): void
        {
        }
        /**
         * Creates a new metabox fields helper.
         *
         * @param int $siteId The ID of the site for which to create a helper.
         * @return MetaboxFieldsHelperInterface The new helper.
         * @throws RuntimeException If problem creating.
         */
        protected function createHelper(int $siteId): \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface
        {
        }
        /**
         * Get the rating meta value of a given review from a given site.
         *
         * @param int $siteId The site ID.
         * @param int $commentId The review ID.
         * @return int The review rating meta value.
         */
        protected function reviewRatingMetaValue(int $siteId, int $commentId): int
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\WooCommerce {
    /**
     * Class AttributesRelationship
     */
    class AttributesRelationship
    {
        protected const WC_ATTRIBUTE_TAXONOMY_PREFIX = 'pa_';
        /**
         * AttributesRelationship constructor
         *
         * @param TaxonomyRepository $taxonomyRepository
         * @param SiteRelations $siteRelations
         * @param wpdb $wpdb
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\TaxonomyRepository $taxonomyRepository, \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \wpdb $wpdb)
        {
        }
        /**
         * Create attribute taxonomy into current site by getting data by the source site
         *
         * @param \WP_Term $term
         * @param string $taxonomy
         * @return void
         */
        public function createAttributeRelation(\WP_Term $term, string $taxonomy): void
        {
        }
        /**
         * Add translation support for attribute taxonomy
         *
         * @param int $id
         * @param array $data
         */
        public function addSupportForAttribute(int $id, array $data): void
        {
        }
    }
    /**
     * Class ArchiveProductsUrlFilter
     */
    class ArchiveProducts
    {
        /**
         * Retrieve the translated shop page archive url
         *
         * @param string $url
         * @return string
         */
        public function shopArchiveUrl(string $url): string
        {
        }
    }
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'woocommerce';
        public const MODULE_SCRIPTS_HANDLER_NAME = 'multilingualpress-woocommerce';
        public const PARAMETER_CONFIG_SHOULD_ENQUEUE_MODULE_ASSETS = 'multilingualpress.woocommerce.shouldEnqueueModuleAssets';
        public const CONFIGURATION_NAME_FOR_URL_TO_MODULE_ASSETS = 'multilingualpress.woocommerce.urlToModuleAssets';
        /**
         * @inheritdoc
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         * @throws NameOverwriteNotAllowed | WriteAccessOnLockedContainer
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         * @throws LateAccessToNotSharedService | NameNotFound
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Handle the WooCommerce reviews support.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService|NameNotFound
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
         */
        protected function handleSupportForReviews(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Disable MLP settings for certain WooCommerce entities.
         *
         * Regardless of whether the WooCommerce module is active, some WooCommerce entities settings should be removed
         * from admin area, cause some entities like "Attributes" are supported under the hood when the module is active and
         * some are not translatable at all, such as "Orders".
         * This method is for removing the settings of such entities from admin area.
         *
         * @param Container $container
         */
        protected function disableSettingsForWooCommerceEntities(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
    class ProductMetaboxesBehaviorActivator
    {
        protected const ALLOWED_POST_TYPES = ['product'];
        /**
         * ProductMetaboxesActivator constructor
         *
         * @param MetaboxFields $metaboxFields
         * @param PanelView $panelView
         * @param ActivePostTypes $activePostTypes
         * @param ContentRelations $contentRelations
         * @param Attachment\Copier $attachmentCopier
         * @param PersistentAdminNotices $notice
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxFields $metaboxFields, \Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\PanelView $panelView, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $activePostTypes, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Attachment\Copier $attachmentCopier, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notice)
        {
        }
        /**
         * @param array $tabs
         * @param Post\RelationshipContext $context
         * @return array
         */
        public function setupMetaboxFields(array $tabs, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): array
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param Post\RelationshipContext $relationshipContext
         */
        public function renderPanels(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): void
        {
        }
        /**
         * @param Post\RelationshipContext $context
         * @param Request $request
         * @param PersistentAdminNotices $notice
         */
        public function saveMetaboxes(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notice): void
        {
        }
    }
    /**
     * Class PermalinkStructure
     */
    class PermalinkStructure
    {
        /**
         * Get the base permalink structure for product by WooCommerce Settings
         *
         * @return string
         */
        public function baseforProduct(): string
        {
        }
        /**
         * Get the base permalink structure for product category by WooCommerce Settings
         *
         * @return string
         */
        public function forProductCategory(): string
        {
        }
        /**
         * Get the base permalink structure for product tag by WooCommerce Settings
         *
         * @return string
         */
        public function forProductTag(): string
        {
        }
        /**
         * Get the base permalink structure for product attribute by WooCommerce Settings
         *
         * @param string $taxonomySlug
         * @return string
         */
        public function forProductAttribute(string $taxonomySlug): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\QuickLinks\Model {
    /**
     * Interface ViewModel
     * @package Inpsyde\MultilingualPress\Core\Setting
     */
    interface ViewModel
    {
        /**
         * The ID of the Model
         *
         * @return string
         */
        public function id(): string;
        /**
         * Print the Title for the Setting
         *
         * @return void
         */
        public function title(): void;
        /**
         * Print the Settings
         *
         * @return void
         */
        public function render(): void;
    }
}
namespace Inpsyde\MultilingualPress\Module\OriginalTranslationLanguage\Integrations\QuickLinks {
    class QuickLinksOriginalLanguageViewModel implements \Inpsyde\MultilingualPress\Module\QuickLinks\Model\ViewModel
    {
        protected string $modelName;
        protected string $quickLinksModuleSettingsName;
        protected string $description;
        protected bool $value;
        public function __construct(string $modelName, string $quickLinksModuleSettingsName, string $description, bool $value)
        {
        }
        /**
         * @inheritDoc
         */
        public function id(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function title(): void
        {
        }
        /**
         * @inheritDoc
         */
        public function render(): void
        {
        }
        /**
         * Returns the original language setting name.
         *
         * @return string
         */
        protected function originalLanguageSettingId(): string
        {
        }
    }
    class QuickLinksIntegration implements \Inpsyde\MultilingualPress\Framework\Integration\Integration
    {
        protected \Inpsyde\MultilingualPress\Module\QuickLinks\Model\ViewModel $originalLanguageViewModel;
        protected \Inpsyde\MultilingualPress\Framework\Api\ContentRelationshipMetaInterface $contentRelationshipMeta;
        protected string $originalKeyword;
        protected bool $quickLinkSettingOptionValue;
        public function __construct(\Inpsyde\MultilingualPress\Module\QuickLinks\Model\ViewModel $originalLanguageViewModel, \Inpsyde\MultilingualPress\Framework\Api\ContentRelationshipMetaInterface $contentRelationshipMeta, string $originalKeyword, bool $quickLinkSettingOptionValue)
        {
        }
        /**
         * @inheritDoc
         */
        public function integrate(): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\OriginalTranslationLanguage {
    /**
     * @psalm-type relatedSites = array{id: int, name: string}
     */
    class MetaboxRenderer implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\PostMetaboxRendererInterface
    {
        /**
         * @var array<int, string> The list of related sites.
         * @psalm-var array<relatedSites>
         */
        protected array $relatedSites;
        protected string $label;
        protected string $relationshipMetaName;
        protected \Inpsyde\MultilingualPress\Framework\Api\ContentRelationshipMetaInterface $contentRelationshipMeta;
        public function __construct(array $relatedSites, string $label, string $relationshipMetaName, \Inpsyde\MultilingualPress\Framework\Api\ContentRelationshipMetaInterface $contentRelationshipMeta)
        {
        }
        /**
         * @inheritDoc
         */
        public function render(int $postId): void
        {
        }
    }
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'original_translation_language';
        /**
         * @inheritdoc
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\LanguageManager {
    /**
     * Language Manager Table Form View
     */
    class TableFormView
    {
        public function __construct(\Inpsyde\MultilingualPress\Module\LanguageManager\Db $db, \Inpsyde\MultilingualPress\Module\LanguageManager\LanguageInstaller $languageInstaller)
        {
        }
        /**
         * @return void
         */
        public function render(): void
        {
        }
    }
    /**
     * MultilingualPress Language Manager Database
     */
    class Db
    {
        /**
         * @var int
         */
        protected const PAGE_SIZE = 100;
        /**
         * Db constructor.
         * @param wpdb $wpdb
         * @param Languages $languages
         * @param Table $table
         */
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Framework\Api\Languages $languages, \Inpsyde\MultilingualPress\Framework\Database\Table $table)
        {
        }
        /**
         * We have to run the "analyze table" query before getting the auto-increment value
         * to reset MySQL 8.0 database cache.
         *
         * @return int
         */
        public function nextLanguageID(): int
        {
        }
        /**
         * @return Language[]
         */
        public function read(): array
        {
        }
        /**
         * @param array $items
         * @throws NonexistentTable
         * @return array
         */
        public function update(array $items): array
        {
        }
        /**
         * @param array $items
         * @return array
         */
        public function create(array $items): array
        {
        }
        /**
         * @param array $items
         * @return array
         */
        public function delete(array $items): array
        {
        }
    }
    /**
     * Language Manager Page View
     */
    final class PageView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        /**
         * PageView constructor.
         * @param Nonce $nonce
         * @param Request $request
         * @param TableFormView $table
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Module\LanguageManager\TableFormView $table)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(): void
        {
        }
    }
    /**
     * MultilingualPress Language Manager Updater
     */
    class Updater
    {
        /**
         * Updater constructor.
         * @param Db $storage
         * @param Table $table
         * @param LanguageFactory $languageFactory
         * @param LanguageInstaller $languageInstaller
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\LanguageManager\Db $storage, \Inpsyde\MultilingualPress\Framework\Database\Table $table, \Inpsyde\MultilingualPress\Framework\Factory\LanguageFactory $languageFactory, \Inpsyde\MultilingualPress\Module\LanguageManager\LanguageInstaller $languageInstaller)
        {
        }
        /**
         * @param array $languages
         * @return bool
         * @throws NonexistentTable
         */
        public function updateLanguages(array $languages): bool
        {
        }
        /**
         * @param array $languages
         * @return array
         */
        public function splitLanguages(array $languages): array
        {
        }
    }
    /**
     * Class LanguageInstaller
     */
    class LanguageInstaller
    {
        /**
         * @param Language $language
         * @return bool
         */
        public function install(\Inpsyde\MultilingualPress\Framework\Language\Language $language): bool
        {
        }
        /**
         * @param Language $language
         * @return bool
         */
        public function exists(\Inpsyde\MultilingualPress\Framework\Language\Language $language): bool
        {
        }
    }
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'language-manager';
        public const MODULE_SCRIPTS_HANDLER_NAME = 'multilingualpress-language-manager';
        public const CONFIGURATION_NAME_FOR_URL_TO_MODULE_ASSETS = 'multilingualpress.languageManager.urlToModuleAssets';
        /**
         * @inheritdoc
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
    class RequestHandler
    {
        public const ACTION = 'update_multilingualpress_languages';
        /**
         * @param Updater $updater
         * @param Request $request
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\LanguageManager\Updater $updater, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Handles POST requests.
         */
        public function handlePostRequest(): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AltLanguageTitleInAdminBar {
    class SettingsRepository
    {
        public const OPTION_SITE = 'multilingualpress_alt_language_title';
        /**
         * Returns the alternative language title of the site with the given ID.
         *
         * @param int $siteId
         * @return string
         */
        public function alternativeLanguageTitle(int $siteId): string
        {
        }
    }
    /**
     * Replaces the site names in the admin bar with the respective alternative language titles.
     */
    class AdminBarCustomizer
    {
        /**
         * @param SettingsRepository $siteSettingsRepository
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\AltLanguageTitleInAdminBar\SettingsRepository $siteSettingsRepository)
        {
        }
        /**
         * Replaces the current site's name with the site's alternative language title, if not empty.
         *
         * @param \WP_Admin_Bar $adminBar
         * @return \WP_Admin_Bar
         */
        public function replaceSiteName(\WP_Admin_Bar $adminBar): \WP_Admin_Bar
        {
        }
        /**
         * Replaces all site names with the individual site's alternative language title, if not empty.
         *
         * @param \WP_Admin_Bar $adminBar
         * @return \WP_Admin_Bar
         */
        public function replaceSiteNodes(\WP_Admin_Bar $adminBar): \WP_Admin_Bar
        {
        }
    }
    /**
     * Module service provider.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'alternative_language_title';
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ACF {
    /**
     * phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
     * @psalm-type FieldType = 'repeater'|'group'|'flexible_content'|'image'|'gallery'|'taxonomy'|'clone'|'simple'|'post_object'|'relationship'
     * @psalm-type Field = array{name: string, value: mixed, type?: FieldType, return_format?: string}
     * phpcs:enable
     */
    class FieldCopier
    {
        public function __construct(\Inpsyde\MultilingualPress\Module\ACF\Fields $fields, \Inpsyde\MultilingualPress\Framework\Content\AttachmentCopier $attachmentCopier)
        {
        }
        /**
         * Handle the copy of ACF Fields
         *
         * The Method is a callback for PostRelationSaveHelper::FILTER_SYNC_KEYS filter
         * It will receive the keys of the meta fields which should be synced and
         * will add the ACF field keys
         *
         * @param array $keysToSync The list of meta keys
         * where should be added the ACF field keys to be synced
         * @param RelationshipContext $context
         * @param Request $request
         * @return array The list of meta keys to be synced
         * @throws NonexistentTable
         */
        public function handleCopyACFFields(array $keysToSync, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, \Inpsyde\MultilingualPress\Framework\Http\Request $request): array
        {
        }
        /**
         * Extract all meta keys from the list of ACF fields.
         *
         * We need a list of ACF post meta keys, including field key reference that each field has,
         * cause this way the data for the target page will be complete immediately and the editor
         * wont have to save the page in order for ACF content to show up in the frontend.
         *
         * @param array $acfFieldObjects The list of advanced custom fields
         * @psalm-param array<Field> $acfFieldObjects The list of advanced custom fields
         * @return array<string> The list of ACF post meta keys
         */
        protected function extractACFFieldMetaKeys(array $acfFieldObjects): array
        {
        }
        /**
         * The method will handle the Taxonomy type fields copy process
         *
         * @param string $fieldType The ACF field type, should be image, gallery or file
         * @param array|WP_Term $fieldValue The value of taxonomy field
         * @param RelationshipContext $context
         * @param string $fieldKey The ACF field key
         * @throws NonexistentTable
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        protected function handleTaxTypeFieldsCopy(string $fieldType, $fieldValue, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, string $fieldKey): void
        {
        }
        /**
         * The method will handle the file type fields(image, gallery, file) copy process
         *
         * @param string $fieldType The ACF field type, should be image, gallery or file
         * @param array $fieldValue The ACF field value
         * @param RelationshipContext $context
         * @param string $fieldKey The ACF field key
         */
        protected function handleFileTypeFieldsCopy(string $fieldType, array $fieldValue, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, string $fieldKey): void
        {
        }
        /**
         * Filter the values of the ACF fields
         *
         * The Method will filter the values of the ACF fields in remote site and will replace them with
         * the correct ids which are copied from source site
         *
         * @param array $values The values which should be replaced in remote site fields
         * @param string $filedKey The ACF field Key of the remote site
         * for which the value should be filtered
         */
        protected function filterRemoteFieldValues(array $values, string $filedKey): void
        {
        }
        /**
         * Gets the connected post IDs of a given ones.
         *
         * @param int[] $postIds The list of post IDs.
         * @param RelationshipContext $context
         * @return int[] The list of post IDs.
         * @throws NonexistentTable
         */
        protected function connectedPostIds(array $postIds, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): array
        {
        }
        /**
         * Gets the list of post IDs regarding how the field is configured.
         *
         * @param string $returnType The return type configuration.
         * @psalm-param 'id'|'object' $returnType
         * @param int[]|int|WP_Post[]|WP_Post $value The field value.
         * @return int[] The list of post IDs.
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        protected function selectedPostIdsByFieldConfig(string $returnType, $value): array
        {
        }
        /**
         * Handles the clone type fields.
         *
         * @param string $fieldValue The field value.
         * @param string $fieldKey The field key.
         * @return array The clone field.
         */
        protected function handleCloneFields(string $fieldValue, string $fieldKey): array
        {
        }
    }
    /**
     * Class Fields
     */
    class Fields
    {
        /**
         * Returns the field types that need to be copied to the remote site.
         *
         * @return array
         */
        public function acfFileFieldTypes(): array
        {
        }
        /**
         * Returns the gallery field type.
         *
         * @return string
         */
        public function galleryFieldType(): string
        {
        }
        /**
         * Returns the relationship field type.
         *
         * @return string
         */
        public function relationshipFieldType(): string
        {
        }
        /**
         * Returns the post field type.
         *
         * @return string
         */
        public function postFieldType(): string
        {
        }
        /**
         * Returns the taxonomy field type.
         *
         * @return string
         */
        public function taxonomyFieldType(): string
        {
        }
        /**
         * Returns the field types that need to be copied to the remote site(include gallery).
         *
         * @return array
         */
        public function acfAttachmentFieldTypes(): array
        {
        }
    }
    /**
     * Class FieldsRepository
     *
     * Handles operations related to ACF fields, including type detection and value manipulation.
     */
    class FieldsRepository
    {
        /**
         * Determine the ACF field type based on the field name and value.
         *
         * @param mixed $value The value of the ACF field.
         * @param string $fieldName The name of the ACF field.
         * @return string|null The ACF field type, or null if not found.
         */
        public function acfFieldType($value, string $fieldName): ?string
        {
        }
        /**
         * Find the origin value by using the field name without the "_".
         *
         * @param array $fields An array of ACF fields.
         * @param string $fieldName The name of the field to find the origin value for.
         * @return mixed|null The origin value of the field, or null if not found.
         */
        //phpcs:ignore Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
        public function findFieldOriginValue(array $fields, string $fieldName)
        {
        }
        /**
         * Update the origin value by using the field name without the "_".
         *
         * @param array $fields An array of ACF fields.
         * @param string $fieldName The name of the field to update.
         * @param mixed $value The new value for the field.
         * @return array The updated array of fields.
         */
        public function updateFieldOriginValue(array $fields, string $fieldName, $value): array
        {
        }
    }
    /**
     * Copy attachment(s), posts relation from source site to the remote site.
     */
    class AcfBlockDataCopier implements \Inpsyde\MultilingualPress\Framework\Content\BlockDataCopier
    {
        public function __construct(\Inpsyde\MultilingualPress\Framework\Content\AttachmentCopier $attachmentCopier, \Inpsyde\MultilingualPress\Module\ACF\Fields $fields, \Inpsyde\MultilingualPress\Module\ACF\FieldsRepository $fieldsRepository)
        {
        }
        /**
         * Handle the copy of ACF Fields
         *
         * ACF block fields contain pairs of fields. For example, a field "image" will be represented by two fields:
         * - "image" with the image ID,
         * - "_image" with the post ID of this field.
         */
        //phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
        public function handleBlockData(array $block, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ACF\TranslationUi\Post {
    /**
     * MultilingualPress ACF Metabox Fields
     */
    class MetaboxFields
    {
        public const TAB = 'tab-custom-fields';
        public const FIELD_COPY_ACF_FIELDS = 'remote-acf-fields-copy';
        /**
         * Retrieve all fields for the ACF metabox tab.
         *
         * @return MetaboxTab[]
         */
        public function allFieldsTabs(): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ACF\TranslationUi\Post\Field {
    class CopyACFFields
    {
        public const FILTER_COPY_ACF_FIELDS_IS_CHECKED = 'multilingualpress.copy_custom_fields_is_checked';
        /**
         * @param mixed $value
         * @return string
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value): string
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ACF {
    /**
     * Class ServiceProvider
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'acf';
        /**
         * @inheritDoc
         *
         * @throws ModuleAlreadyRegistered
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        /**
         * @inheritdoc
         *
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         *
         * @phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Disable MLP settings for ACF custom post type.
         *
         * Regardless of whether the ACF module is active, ACF custom post type settings should be removed
         * from admin area, because they are not translatable. The custom post type is called "Field Groups"
         */
        protected function disableSettingsForAcfEntities(): void
        {
        }
    }
    /**
     * Parses and validates ACF blocks from post content.
     */
    class AcfBlockParser extends \Inpsyde\MultilingualPress\Framework\Content\BlockParser
    {
        /**
         * Verify if the ACF block has valid data.
         */
        protected function isBlockValid(array $block): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ToolbarLanguageSwitcher {
    class MenuItems
    {
        public const FILTER_REMOTE_ADMIN_URL = 'multilingualpress.toolbar_language_switcher.remote_admin_url';
        public const FILTER_MENU_ITEMS = 'multilingualpress.toolbar_language_switcher.menu_items';
        public const FILTER_USER_HAS_WP_ADMIN_ACCESS = 'multilingualpress.toolbar_language_switcher.user_has_wp_admin_access';
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\Translations $translations, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request)
        {
        }
        /**
         * Build menu items based on current page's translations.
         *
         * @return list<array{id: string, title: string, href: string}>
         */
        public function items(): array
        {
        }
    }
    /**
     * Module service provider.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'toolbar_language_switcher';
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
    class ToolbarMenuFilter
    {
        public function __construct(\Inpsyde\MultilingualPress\Module\ToolbarLanguageSwitcher\MenuItems $menuItems)
        {
        }
        /**
         * Modify toolbar menus and add a language switcher menu.
         *
         * @wp-hook admin_bar_menu
         */
        public function __invoke(\WP_Admin_Bar $wpAdminBar): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\User\TranslationUi {
    class MetaboxFields
    {
        public const FIELD_BIOGRAPHY = 'description';
        public const TRANSLATABLE_USER_META_FIELDS = 'multilingualpress.translatable_user_meta_fields';
        /**
         * Will return array of all user translatable fields
         *
         * @return array of All user translatable fields
         */
        public function allFields(): array
        {
        }
    }
    class MetaboxAction
    {
        public const NAME_PREFIX = 'multilingualpress';
        public const TRANSLATION_META = 'multilingualpress_translation_meta';
        /**
         * Will handle the user profile field translations update
         *
         * @param int $userId The user id which is currently in edit
         * @param ServerRequest $request
         */
        public function updateTranslationData(int $userId, \Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request): void
        {
        }
    }
    class MetaboxView
    {
        /**
         * @param  MetaboxFields  $fields
         * @param  string[]  $assignedLanguageNames
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\User\TranslationUi\MetaboxFields $fields)
        {
        }
        /**
         * Will render user profile translation settings, which includes
         * The section title for MultilingualPress settings and translatable fields
         * for each site in tabs
         *
         * @param WP_User $user The user which is currently in edit
         * @throws NonexistentTable
         */
        public function render(\WP_User $user): void
        {
        }
        /**
         * Will render translation metabox tab title. Should be the site name
         *
         * @param int $siteId The site id which name should be rendered as tab title
         * @throws NonexistentTable
         */
        protected function renderTabAnchor(int $siteId): void
        {
        }
        /**
         * Will render translation metabox tab content (translatable options)
         *
         * @param int $siteId The site id
         * @param WP_User $user The user which is currently in edit
         * @param MetaboxFieldsHelper $helper
         */
        protected function renderTabContent(int $siteId, \WP_User $user, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\User\TranslationUi\Field {
    class Biography
    {
        /**
         * Biography field constructor.
         * @param string $key The meta key of the biography field
         */
        public function __construct(string $key)
        {
        }
        /**
         * Will render User Biography translation field
         *
         * @param int $userId The user id which is currently in edit
         * @param int $siteId The site id
         * @param MetaboxFieldsHelper $helper
         */
        public function render(int $userId, int $siteId, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\User {
    class MetaValueFilter
    {
        /**
         * Filter the frontend values for user meta fields and replace with correct translations
         *
         * @param string $authorMeta The value of the metadata.
         * @param mixed $userId The user ID.
         * @return string user meta value replaced with correct translation
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         */
        public function filterMetaValues(string $authorMeta, $userId): string
        {
        }
    }
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'user';
        /**
         * @inheritdoc
         * @throws ModuleAlreadyRegistered
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         * @throws Throwable
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Render MultilingualPress custom metaboxes on user profile pages.
         *
         * @param MetaboxView $metaboxView
         */
        protected function metaboxViewActions(\Inpsyde\MultilingualPress\Module\User\TranslationUi\MetaboxView $metaboxView): void
        {
        }
        /**
         * Handles the actions when the user profile page is updated.
         *
         * When the user profile page is updated we need to save our custom translation meta.
         *
         * @param ServerRequest $request
         * @param MetaboxAction $metaboxAction
         */
        protected function metaboxUpdateActions(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request, \Inpsyde\MultilingualPress\Module\User\TranslationUi\MetaboxAction $metaboxAction): void
        {
        }
        /**
         * Filters the frontend values for user meta fields and replaces with correct translations.
         *
         * @param MetaValueFilter $metaValueFilter
         * @param MetaboxFields $metaboxFields
         */
        protected function filterUserMetaValues(\Inpsyde\MultilingualPress\Module\User\MetaValueFilter $metaValueFilter, \Inpsyde\MultilingualPress\Module\User\TranslationUi\MetaboxFields $metaboxFields): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Elementor {
    /**
     * Class ServiceProvider
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'elementor';
        public const ELEMENTOR_ENTITIES_TO_REMOVE_SUPPORT = 'elementor.entities.slugs';
        /**
         * @inheritdoc
         *
         * @param ModuleManager $moduleManager
         * @return bool
         * @throws ModuleAlreadyRegistered
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        /**
         * @inheritdoc
         *
         * @param Container $container
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService
         * @throws NameNotFound
         * @throws Throwable
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        public function disableQueryStringParametersPreservationForSearch(bool $shouldPreserve, \Inpsyde\MultilingualPress\Framework\Api\Translation $translation): bool
        {
        }
        /**
         * When "Copy source content" option is checked the method will copy the additional postmeta data
         * Which is needed for elementor to work correctly.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService
         * @throws NameNotFound
         * @throws Throwable
         */
        protected function handleCopyContentEditions(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Copy Meta data from source post to remote post
         *
         * @param array $data Metadata to be copied
         * @param RelationshipContext $context
         */
        protected function copyMetaData(array $data, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): void
        {
        }
        /**
         * @return bool
         */
        protected function isElementorActive(): bool
        {
        }
        /**
         * Elementor Post types and taxonomies doesn't need to be supported
         *
         * @param array $entities for which the support needs to be deleted
         * @param string $filter The filter name which is used to remove support
         *
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function filterSupportForEntities(array $entities, string $filter): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\QuickLinks\Settings {
    /**
     * Class TabView
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Settings
     */
    class TabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        public const FILTER_VIEW_MODELS = 'multilingualpress.quicklinks_module_setting_models';
        /**
         * ModuleSettingsTabView constructor
         *
         * @param Nonce $nonce
         * @param ViewModel[] $viewModels
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\QuickLinks\Model\ViewModel ...$viewModels)
        {
        }
        /**
         * Render the Settings Tab Content
         *
         * @inheritDoc
         */
        public function render(): void
        {
        }
        /**
         * Retrieve the Models
         *
         * @return ViewModel[]
         */
        protected function viewModels(): array
        {
        }
        /**
         * Validate View Model by Type Hint all of the models of the given collection
         *
         * @param array $models
         * @return array
         */
        protected function validateViewModels(array $models): array
        {
        }
    }
    /**
     * Class Updater
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Settings
     */
    class Updater
    {
        /**
         * Updater constructor
         *
         * @param Nonce $nonce
         * @param Repository $repository
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\QuickLinks\Settings\Repository $repository)
        {
        }
        /**
         * Update Module Redirect Settings
         *
         * @param Request $request
         */
        public function updateSettings(\Inpsyde\MultilingualPress\Framework\Http\Request $request): void
        {
        }
    }
    /**
     * Class QuickLinksPositionViewModel
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Settings
     */
    class QuickLinksPositionViewModel implements \Inpsyde\MultilingualPress\Module\QuickLinks\Model\ViewModel
    {
        protected const ID = 'position';
        /**
         * QuickLinksPositionViewModel constructor.
         * @param Repository $repository
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\QuickLinks\Settings\Repository $repository)
        {
        }
        /**
         * @inheritDoc
         */
        public function id(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function title(): void
        {
        }
        /**
         * @inheritDoc
         */
        public function render(): void
        {
        }
    }
    /**
     * Class Repository
     * @package Inpsyde\MultilingualPress\Module\QuickLinks
     */
    class Repository
    {
        public const MODULE_SETTINGS = 'multilingualpress_module_quicklinks_settings';
        public const MODULE_SETTING_QUICKLINKS_POSITION = 'position';
        /**
         * Retrieve the value for the given Quick Links setting name.
         *
         * @param string $settingName The setting name.
         * @return string The setting value.
         */
        public function settingValue(string $settingName): string
        {
        }
        /**
         * Retrieve the Module Settings
         *
         * @return array
         */
        protected function moduleSettings(): array
        {
        }
        /**
         * Update the Given Module Settings
         *
         * @param array $options
         * @return void
         */
        public function updateModuleSettings(array $options): void
        {
        }
        /**
         * Gets the default setting value by given setting name.
         *
         * @param string $settingName The setting name.
         * @return string The default setting value.
         */
        protected function defaultValueForSetting(string $settingName): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\QuickLinks {
    /**
     * Class Redirector
     * @package Inpsyde\MultilingualPress\Module\QuickLinks
     */
    class Redirector
    {
        public const REDIRECT_VALUE_KEY = 'mlp_quicklinks_redirect_selection';
        public const ACTION_BEFORE_VALIDATE_REDIRECT = 'multilingualpress.before_validate_redirect';
        public const ACTION_AFTER_VALIDATE_REDIRECT = 'multilingualpress.after_validate_redirect';
        /**
         * Redirector constructor.
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Take Redirect Action
         *
         * @return void
         */
        public function redirect(): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\QuickLinks\Model {
    /**
     * Interface ModelInterface
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
     */
    interface ModelInterface
    {
        /**
         * Return the Url
         *
         * @return Url
         */
        public function url(): \Inpsyde\MultilingualPress\Framework\Url\Url;
        /**
         * Return the Language HTTP Code
         *
         * @return Bcp47Tag
         */
        public function language(): \Inpsyde\MultilingualPress\Framework\Language\Bcp47Tag;
        /**
         * Return a Text Label
         *
         * @return string
         */
        public function label(): string;
        public function hreflangDisplayCode(): string;
        /**
         * Return site id
         *
         * @return ?int
         */
        public function siteId(): ?int;
    }
    /**
     * Class Model
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
     */
    class Model implements \Inpsyde\MultilingualPress\Module\QuickLinks\Model\ModelInterface
    {
        /**
         * @throws InvalidArgumentException
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Url\Url $url, \Inpsyde\MultilingualPress\Framework\Language\Bcp47Tag $language, string $label, string $hreflangDisplayCode, ?int $siteId = null)
        {
        }
        /**
         * @inheritDoc
         */
        public function url(): \Inpsyde\MultilingualPress\Framework\Url\Url
        {
        }
        /**
         * @inheritDoc
         */
        public function language(): \Inpsyde\MultilingualPress\Framework\Language\Bcp47Tag
        {
        }
        /**
         * @inheritDoc
         */
        public function label(): string
        {
        }
        public function hreflangDisplayCode(): string
        {
        }
        public function siteId(): ?int
        {
        }
    }
    /**
     * Class CollectionFactory
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
     */
    class CollectionFactory
    {
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository, \Inpsyde\MultilingualPress\Framework\Api\Translations $translations, \Inpsyde\MultilingualPress\Framework\Api\ContentRelationSearch $contentRelationSearch)
        {
        }
        /**
         * Create the Model Collection
         *
         * All of the models within the collection are related with the given site and content id.
         *
         * @param int $sourceSiteId
         * @param int $sourceContentId
         * @return Collection
         * @throws InvalidArgumentException
         */
        public function create(int $sourceSiteId, int $sourceContentId): \Inpsyde\MultilingualPress\Module\QuickLinks\Model\Collection
        {
        }
        /**
         * Build the Collection of Models by the Given Content Relations
         *
         * Content Relations is an array where the keys are the site id and the value the content id.
         *
         * @param array $contentRelations
         * @return Collection
         * @throws InvalidArgumentException
         */
        protected function buildModelCollectionByContentRelations(array $contentRelations): \Inpsyde\MultilingualPress\Module\QuickLinks\Model\Collection
        {
        }
        /**
         * Create the Single Model
         *
         * The returned value is an array like this one
         *
         * ```
         * [
         *     'url' => URL OF THE TARGET POST,
         *     'language' => HTTP LANGUAGE CODE OF THE TARGET SITE
         *     'label' => THE TEXT TO USE AS LABEL FOR THE ITEM
         * ]
         * ```
         *
         * @param int $remoteSiteId
         * @param int $remoteContentId
         * @return ModelInterface
         * @throws InvalidArgumentException
         * @throws NonexistentTable
         */
        protected function singleModel(int $remoteSiteId, int $remoteContentId): \Inpsyde\MultilingualPress\Module\QuickLinks\Model\ModelInterface
        {
        }
        /**
         * Gets the hreflang display code of the given site.
         *
         * @param int $siteId The site ID.
         * @return string The hreflang display code
         */
        protected function hreflangDisplayCode(int $siteId): string
        {
        }
        /**
         * Create a NetworkState Instance
         *
         * Basically a wrapper for a static constructor that's difficult to mock in unit tests.
         *
         * @return NetworkState
         */
        protected function networkState(): \Inpsyde\MultilingualPress\Framework\NetworkState
        {
        }
        /**
         * Get the translations for remote content
         *
         * @param int $remoteContentId
         * @return Translation[]
         */
        protected function translations(int $remoteContentId): array
        {
        }
        /**
         * Creates Bcp47Tag for given site ID.
         *
         * @param int $siteId The site ID.
         * @return Bcp47Tag The Bcp47Tag tag
         */
        protected function createBcp47Tag(int $siteId): \Inpsyde\MultilingualPress\Framework\Language\Bcp47Tag
        {
        }
    }
    /**
     * Class ModelCollectionValidator
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
     */
    trait ModelCollectionValidator
    {
        /**
         * Check that all of the items withing the give argument are instances of
         * ModelInterface
         *
         * @param array $models
         * @return bool
         */
        protected function validate(array $models): bool
        {
        }
    }
    /**
     * Class Collection
     *
     * @template-implements IteratorAggregate<Traversable>
     *
     * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
     */
    class Collection implements \IteratorAggregate, \Countable
    {
        use \Inpsyde\MultilingualPress\Module\QuickLinks\Model\ModelCollectionValidator;
        /**
         * Collection constructor.
         * @param array $models
         * @throws InvalidArgumentException
         */
        public function __construct(array $models)
        {
        }
        /**
         * @inheritDoc
         */
        public function getIterator(): \ArrayIterator
        {
        }
        /**
         * @inheritDoc
         */
        public function count(): int
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\QuickLinks {
    /**
     * @psalm-type siteId = int
     * @psalm-type siteName = string
     * @psalm-type relatedSites = array<siteId, siteName>
     */
    class QuickLink
    {
        public const FILTER_NOFOLLOW_ATTRIBUTE = 'multilingualpress.quicklinks_nofollow';
        public const FILTER_RENDER_AS_SELECT = 'multilingualpress.QuickLinks.RenderAsSelect';
        public const FILTER_QUICKLINK_LABEL = 'multilingualpress.QuickLinks.Label';
        public const FILTER_MODEL_COLLECTION = 'multilingualpress.QuickLinks.ModelCollection';
        /**
         * @var array<int, string>
         * @psalm-var relatedSites
         */
        protected array $relatedSites;
        /**
         * QuickLink constructor.
         * @param CollectionFactory $collectionFactory
         * @param Nonce $nonce
         * @param Repository $settingRepository
         * @param array<int, string> $relatedSites A map of the related site site IDs to site names.
         * @psalm-param relatedSites $relatedSites
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\QuickLinks\Model\CollectionFactory $collectionFactory, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\QuickLinks\Settings\Repository $settingRepository, array $relatedSites)
        {
        }
        /**
         * Filter the Post Content
         *
         * Include the Quick Links in the content output
         *
         * @param string $theContent
         * @return string
         */
        public function filter(string $theContent): string
        {
        }
        /**
         * Render
         *
         * @param string $position
         * @param Collection $modelCollection
         */
        protected function render(string $position, \Inpsyde\MultilingualPress\Module\QuickLinks\Model\Collection $modelCollection): void
        {
        }
        /**
         * Render the collection.
         *
         * @param Collection $collection
         */
        protected function renderCollection(\Inpsyde\MultilingualPress\Module\QuickLinks\Model\Collection $collection): void
        {
        }
        /**
         * Render the Quick Links as a List of Links
         *
         * @param Collection $modelCollection
         */
        protected function renderAsLinkList(\Inpsyde\MultilingualPress\Module\QuickLinks\Model\Collection $modelCollection): void
        {
        }
        /**
         * Render the Quick Links as a Select/Dropdown Element
         *
         * @param Collection $modelCollection
         */
        protected function renderAsSelect(\Inpsyde\MultilingualPress\Module\QuickLinks\Model\Collection $modelCollection): void
        {
        }
    }
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'quick_links';
        public const MODULE_SETTINGS_TAB_NAME = 'quick-links';
        public const MODULE_SCRIPTS_HANDLER_NAME = 'multilingualpress-quicklinks';
        public const CONFIGURATION_NAME_FOR_URL_TO_MODULE_ASSETS = 'multilingualpress.quickLinks.urlToModuleAssets';
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritDoc
         * @throws ModuleAlreadyRegistered
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        /**
         * @inheritDoc
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
    /**
     * Class ValidateRedirectFilter
     * @package Inpsyde\MultilingualPress\Module\QuickLinks
     */
    class ValidateRedirectFilter implements \Inpsyde\MultilingualPress\Framework\Filter\Filter
    {
        use \Inpsyde\MultilingualPress\Framework\Filter\FilterTrait;
        /**
         * ValidateRedirectFilter constructor.
         * @param wpdb $wpdb
         */
        public function __construct(\wpdb $wpdb)
        {
        }
        /**
         * Enable the filter
         *
         * @return void
         */
        public function enableExtendsAllowedHosts(): void
        {
        }
        /**
         * Disable the filter
         *
         * @return bool
         */
        public function disable(): bool
        {
        }
        /**
         * Filter
         *
         * @param array $homeHosts
         * @param $remoteHosts
         * @return array
         */
        public function extendsAllowedHosts(array $homeHosts, string $remoteHosts): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\TheEventsCalendar {
    class EventPostCreator
    {
        /**
         * Creates a callback for event creation.
         *
         * @wp-hook multilingualpress.function_create_remote_post
         */
        public function createEventCallback(callable $callback, array $post, string $operation): callable
        {
        }
    }
    trait EventsSlugTrait
    {
        /**
         * Gets the events slug for the current blog.
         */
        private function getCurrentBlogEventsSlug(): string
        {
        }
        /**
         * Gets the single event slug for the current blog.
         */
        private function getCurrentBlogSingleEventSlug(): string
        {
        }
    }
    class OriginalEventCategorySlugRepository
    {
        use \Inpsyde\MultilingualPress\Module\TheEventsCalendar\EventsSlugTrait;
        /**
         * Retrieves the appropriate slug for The Events Calendar taxonomy from specified site.
         *
         * @wp-hook multilingualpress.original_taxonomy_slug
         */
        public function slug(string $originalSlug, string $taxonomyName, int $siteId): string
        {
        }
    }
    class TheEventsCalendar
    {
        public const POST_TYPE = 'tribe_events';
        public const TAXONOMY = 'tribe_events_cat';
        public const DEFAULT_POST_ARCHIVE_SLUG = 'events';
        public const DEFAULT_SINGLE_SLUG = 'event';
        public const OPTION_NAME = 'tribe_events_calendar_options';
    }
    class EventDataModifier
    {
        /**
         * Modifies the event data for remote posts.
         *
         * @wp-hook multilingualpress.new_relate_remote_post_before_insert
         */
        public function modify(array $data, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): array
        {
        }
    }
    /**
     * Provides services for The Events Calendar integration with MultilingualPress.
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'the_events_calendar';
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Checks if The Events Calendar plugin is active.
         */
        protected function isTheEventsCalendarActive(): bool
        {
        }
    }
    /**
     * Modifies translation URLs for The Events Calendar plugin.
     */
    class TranslationUrlModifier
    {
        use \Inpsyde\MultilingualPress\Module\TheEventsCalendar\EventsSlugTrait;
        public function __construct(\Inpsyde\MultilingualPress\Core\OriginalTaxonomySlugsRepository $originalTaxonomySlugsRepository)
        {
        }
        /**
         * Modifies the translations array by updating the URLs for
         * The Events Calendar custom post type or taxonomies.
         *
         * @wp-hook multilingualpress.search_translations
         *
         * @param Translation[] $translations
         * @return Translation[]
         */
        public function modify(array $translations): array
        {
        }
    }
    /**
     * Modifies translation search arguments for The Events Calendar integration.
     */
    class TranslationSearchArgsModifier
    {
        /**
         * Modifies the translation search arguments for event-related queries.
         *
         * This method adjusts the search arguments specifically for The Events Calendar
         * post type when dealing with taxonomy or tag archives.
         *
         * @wp-hook multilingualpress.translation_search_args
         */
        public function modify(\Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args): \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Cornerstone {
    class PostMetaCopier
    {
        public function __construct(\Inpsyde\MultilingualPress\Framework\Content\AttachmentCopier $attachmentCopier)
        {
        }
        /**
         * Checks the request for content copy option and initiates meta data copy.
         *
         * @wp-hook multilingualpress.metabox_after_relate_posts
         */
        public function copyPostMeta(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, \Inpsyde\MultilingualPress\Framework\Http\Request $request): void
        {
        }
        /**
         * Copies all Cornerstone-specific meta data from source post to remote post.
         */
        protected function copyMetaData(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): void
        {
        }
        /**
         * Check if a meta key starts with an allowed prefix.
         */
        protected function hasAllowedPrefix(string $metaKey): bool
        {
        }
        /**
         * Handles attachments found in the metadata.
         */
        protected function handleAttachments(string $metaValue, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): string
        {
        }
        /**
         * Recursively finds and modifies image sources within the metadata.
         */
        protected function handleImageReferences(array $data, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): array
        {
        }
        /**
         * Copies an image attachment and updates its metadata.
         */
        protected function copyImage(string $imageData, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): string
        {
        }
    }
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'cornerstone';
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Updates the Cornerstone status for all sites in the network.
         */
        protected function updateCornerstoneStatusOnSites(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Sets up handlers for copying content editions between posts.
         */
        protected function handleCopyContentEditions(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Checks if Cornerstone is active on any site in the network.
         */
        protected function isCornerstoneActive(): bool
        {
        }
    }
    class StatusUpdater
    {
        public function __construct(\Inpsyde\MultilingualPress\Module\Cornerstone\StatusRepository $statusRepository)
        {
        }
        /**
         * Updates the Cornerstone module status.
         *
         * Only updates if there's a mismatch between the stored status
         * and the actual plugin activation state. Updates are skipped if:
         * - Status does not exist, and module is not activated
         * - Current module activation state matches stored status
         *
         * @wp-hook after_setup_theme
         */
        public function update(): void
        {
        }
    }
    class PostContentUpdater
    {
        /**
         * Checks if content contains references to source post ID and updates them
         * to reference the remote post ID instead. Only processes content that
         * contains specific post ID references.
         *
         * @wp-hook multilingualpress.metabox_after_update_remote_post
         */
        public function updateContent(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext, array $post): void
        {
        }
    }
    class StatusRepository
    {
        public const OPTION = 'multilingualpress_module_cornerstone_settings';
        public function __construct()
        {
        }
        /**
         * Checks if any site in the network has an active status.
         *
         * @return bool True if at least one site has an active status, false otherwise.
         */
        public function hasActiveStatus(): bool
        {
        }
        /**
         * Gets the status for a specific site.
         *
         * @param int $siteId The ID of the site to check. Defaults to 0 (current site).
         * @return bool|null The status of the site if set, null otherwise.
         */
        public function status(int $siteId = 0): ?bool
        {
        }
        /**
         * Updates the status for a specific site.
         *
         * @param bool $status The new status to set.
         * @param int $siteId The ID of the site to update. Defaults to 0 (current site).
         * @return bool True if the update was successful, false otherwise.
         */
        public function updateStatus(bool $status, int $siteId = 0): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Redirect {
    /**
     * Request validator to be used for (potential) redirect requests.
     */
    class RedirectRequestChecker
    {
        public const FILTER_REDIRECT = 'multilingualpress.do_redirect';
        public function __construct(\Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository\Repository $settingsRepository, \Inpsyde\MultilingualPress\Module\Redirect\NoRedirectStorage\NoRedirectStorage $redirectStorage)
        {
        }
        /**
         * Checks if the current request should be redirected.
         *
         * @return bool true if the current request should be redirected, otherwise false.
         */
        public function isRedirectRequest(): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Redirect\Settings\Renderers {
    /**
     * Interface ViewRenderer
     */
    interface ViewRenderer
    {
        /**
         * Print the Title for the Setting
         *
         * @return void
         */
        public function title();
        /**
         * Print the Settings
         *
         * @return void
         */
        public function content();
    }
    /**
     * @psalm-type ExecutionTypeTitle = string
     * @psalm-type RedirectExecutionTypes = array{PHP: ExecutionTypeTitle, JAVASCRIPT: ExecutionTypeTitle}
     */
    class RedirectExecutionTypeViewRenderer implements \Inpsyde\MultilingualPress\Module\Redirect\Settings\Renderers\ViewRenderer
    {
        protected string $title;
        protected string $settingName;
        protected string $description;
        protected \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository\Repository $repository;
        /**
         * @var array<string, string>
         * @psalm-var RedirectExecutionTypes
         */
        protected array $types;
        public function __construct(string $title, string $settingName, string $description, \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository\Repository $repository, array $types)
        {
        }
        /**
         * @inheritDoc
         */
        public function title(): void
        {
        }
        /**
         * @inheritDoc
         */
        public function content(): void
        {
        }
    }
    class RedirectFallbackViewRenderer implements \Inpsyde\MultilingualPress\Module\Redirect\Settings\Renderers\ViewRenderer
    {
        public function __construct(\Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository\Repository $repository)
        {
        }
        /**
         * @inheritDoc
         */
        public function title(): void
        {
        }
        /**
         * @inheritDoc
         */
        public function content(): void
        {
        }
        /**
         * Render the Options List of Sites that can be selected
         *
         * @param WP_Site[] $sites
         * @param int $selected
         */
        protected function renderOptionsForSites(array $sites, int $selected): void
        {
        }
        /**
         * Render a Single Option
         *
         * @param WP_Site $site
         * @param int $selected
         */
        protected function renderOption(\WP_Site $site, int $selected): void
        {
        }
        /**
         * Retrieve the Existing Sites
         *
         * @return array
         */
        protected function sites(): array
        {
        }
    }
    class RedirectTypeViewRenderer implements \Inpsyde\MultilingualPress\Module\Redirect\Settings\Renderers\ViewRenderer
    {
        protected string $title;
        protected string $settingName;
        protected string $description;
        /**
         * @var array<string, string>
         */
        protected array $redirectTypes;
        protected \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository\Repository $repository;
        public function __construct(string $title, string $settingName, string $description, array $redirectTypes, \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository\Repository $repository)
        {
        }
        /**
         * @inheritDoc
         */
        public function title(): void
        {
        }
        /**
         * @inheritDoc
         */
        public function content(): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository {
    /**
     * Interface for the redirect settings repository.
     */
    interface RedirectSettingsRepositoryInterface extends \Inpsyde\MultilingualPress\Framework\Setting\SettingsRepositoryInterface
    {
        /**
         * Retrieves the given redirect setting for the given site ID.
         *
         * @param int $siteId The site ID.
         * @param string $settingName The setting name.
         * @return array | scalar
         */
        public function redirectSiteSetting(int $siteId, string $settingName);
        /**
         * Retrieves the given redirect setting for the given user ID.
         *
         * @param int $userId The user ID.
         * @param string $settingName The setting name.
         * @return array | scalar
         */
        public function redirectUserSetting(int $userId, string $settingName);
    }
    class Repository implements \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository\RedirectSettingsRepositoryInterface
    {
        public const META_KEY_USER = 'multilingualpress_redirect';
        public const OPTION_SITE = 'multilingualpress_module_redirect';
        public const OPTION_SITE_ENABLE_REDIRECT = 'option_site_enable_redirect';
        public const OPTION_SITE_ENABLE_REDIRECT_FALLBACK = 'option_site_enable_redirect_fallback';
        public const MODULE_SETTINGS = 'multilingualpress_module_redirect_settings';
        public const MODULE_SETTING_FALLBACK_REDIRECT_SITE_ID = 'fallback_site_id';
        public const MODULE_SETTING_FALLBACK_REDIRECT_EXTERNAL_SITE_ID = 'fallback_external_site_id';
        public const MODULE_SETTING_EXECUTION_TYPE = 'execution_type';
        public const MODULE_SETTING_REDIRECT_TYPE = 'redirect_type';
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function redirectSiteSetting(int $siteId, string $settingName)
        {
        }
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function redirectUserSetting(int $userId, string $settingName)
        {
        }
        /**
         * @inheritdoc
         */
        public function allSettingValues(): array
        {
        }
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function settingValue(string $settingName)
        {
        }
        /**
         * @inheritdoc
         */
        public function updateSettings(array $settingsMap): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Redirect\Settings {
    class TabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        public const FILTER_VIEW_MODELS = 'multilingualpress.redirect_module_setting_models';
        /**
         * ModuleSettingsTabView constructor
         *
         * @param Nonce $nonce
         * @param ViewRenderer[] $viewRenderer
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\Redirect\Settings\Renderers\ViewRenderer ...$viewRenderer)
        {
        }
        /**
         * @inheritDoc
         */
        public function render(): void
        {
        }
        /**
         * Retrieve the Models
         *
         * @return ViewRenderer[]
         */
        protected function viewRenderers(): array
        {
        }
        /**
         * Validate View Model by Type Hint all of the models of the given collection
         *
         * @param array $models
         * @return array
         */
        protected function validateViewRenderers(array $models): array
        {
        }
    }
    class RedirectSiteSettings implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @param array<SettingOptionInterface> $options
         * @param Nonce $nonce
         * @param Repository $repository
         */
        public function __construct(array $options, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository\Repository $repository)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
    /**
     * Class Updater
     * @package Inpsyde\MultilingualPress\Module\Redirect
     */
    class Updater
    {
        /**
         * Updater constructor
         *
         * @param Nonce $nonce
         * @param Repository $repository
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository\Repository $repository)
        {
        }
        /**
         * Update Module Redirect Settings
         *
         * @param Request $request
         */
        public function updateSettings(\Inpsyde\MultilingualPress\Framework\Http\Request $request): void
        {
        }
    }
    final class RedirectUserSetting implements \Inpsyde\MultilingualPress\Framework\Setting\User\UserSettingViewModel
    {
        public function __construct(string $userMetaKey, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository\Repository $repository)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(\WP_User $user): void
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Redirect\RedirectTarget {
    /**
     * Represents the redirectTarget.
     * @psalm-type redirectType = 'browser_language'|'geolocation'|'geolocation_with_fallback_to_browser_language'
     */
    interface RedirectTargetInterface
    {
        /**
         * The target site ID.
         *
         * @return int
         */
        public function siteId(): int;
        /**
         * The target content ID.
         *
         * @return int
         */
        public function contentId(): int;
        /**
         * The target content url.
         *
         * @return string
         */
        public function url(): string;
        /**
         * The target language.
         *
         * @return string
         */
        public function language(): string;
        /**
         * The redirect target priority index.
         *
         * @return int
         */
        public function priorityIndex(): int;
        /**
         * The target redirection priority based on given redirect type.
         *
         * @param string $redirectType The redirect type ('browser_language' || 'geolocation').
         * @psalm-param redirectType $redirectType
         * @return float
         */
        public function redirectPriority(string $redirectType): float;
        /**
         * The target redirect fallback priority.
         *
         * @return float
         */
        public function redirectFallbackPriority(): float;
    }
    /**
     * @psalm-type RedirectTargetConfig = array{
     *     siteId?: int,
     *     contentId?: int,
     *     language?: string,
     *     priorityIndex?: int,
     *     url?: string,
     *     redirectFallbackPriority?: float
     * }
     */
    class RedirectTarget implements \Inpsyde\MultilingualPress\Module\Redirect\RedirectTarget\RedirectTargetInterface
    {
        public const KEY_SITE_ID = 'siteId';
        public const KEY_CONTENT_ID = 'contentId';
        public const KEY_LANGUAGE = 'language';
        public const KEY_PRIORITY_INDEX = 'priorityIndex';
        public const KEY_URL = 'url';
        public const KEY_REDIRECT_FALLBACK_PRIORITY = 'redirectFallbackPriority';
        protected const DEFAULTS = [self::KEY_CONTENT_ID => 0, self::KEY_LANGUAGE => '', self::KEY_PRIORITY_INDEX => 0, self::KEY_SITE_ID => 0, self::KEY_URL => '', self::KEY_REDIRECT_FALLBACK_PRIORITY => 0.0];
        public const FILTER_PRIORITY_FACTOR = 'multilingualpress.language_only_priority_factor';
        /**
         * A map of language codes and their priorities.
         *
         * @var array<string, float>
         */
        protected array $userLanguages;
        protected \Inpsyde\MultilingualPress\Module\Redirect\GeoLocation\GeolocationFinderInterface $geolocationFinder;
        /**
         * @var array<string, int|float|string>
         * @psalm-var RedirectTargetConfig
         */
        protected array $data;
        public function __construct(array $userLanguages, \Inpsyde\MultilingualPress\Module\Redirect\GeoLocation\GeolocationFinderInterface $geolocationFinder, array $data = [])
        {
        }
        /**
         * @inheritDoc
         */
        public function siteId(): int
        {
        }
        /**
         * @inheritDoc
         */
        public function contentId(): int
        {
        }
        /**
         * @inheritDoc
         */
        public function url(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function language(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function priorityIndex(): int
        {
        }
        public function redirectPriority(string $redirectType): float
        {
        }
        /**
         * @inheritDoc
         */
        public function redirectFallbackPriority(): float
        {
        }
        /**
         * Retrieves the redirect target priority based on browser language.
         *
         * @return float The redirect target priority.
         */
        protected function redirectPriorityBasedOnBrowserLanguages(): float
        {
        }
        /**
         * Retrieves the redirect target priority based on geolocation.
         *
         * @return float The redirect target priority.
         */
        protected function redirectPriorityBasedOnGeolocation(): float
        {
        }
    }
    /**
     * @psalm-type languageCode = string
     */
    class LanguageNegotiator
    {
        public const FILTER_REDIRECT_URL = 'multilingualpress.redirect_url';
        public const FILTER_POST_STATUS = 'multilingualpress.redirect_post_status';
        public const FILTER_REDIRECT_TARGETS = 'multilingualpress.redirect_targets';
        /**
         * A map of language codes to priorities.
         *
         * @var array<string, float>
         * @psalm-var array<languageCode, float>
         */
        protected array $userLanguages;
        protected \Inpsyde\MultilingualPress\Module\Redirect\GeoLocation\GeolocationFinderInterface $geolocationFinder;
        /**
         * @psalm-var 'browser_language'|'geolocation'
         */
        protected string $redirectType;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\Translations $translations, \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository\Repository $repository, array $userLanguages, \Inpsyde\MultilingualPress\Module\Redirect\GeoLocation\GeolocationFinderInterface $geolocationFinder, string $redirectType)
        {
        }
        /**
         * Returns the redirect target data object for the best-matching language version.
         *
         * @param TranslationSearchArgs|null $args
         * @return RedirectTarget
         */
        public function redirectTarget(\Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args = null): \Inpsyde\MultilingualPress\Module\Redirect\RedirectTarget\RedirectTarget
        {
        }
        /**
         * Returns the redirect target data objects for all available language versions.
         *
         * @param TranslationSearchArgs|null $args
         * @return RedirectTarget[]
         */
        public function redirectTargets(\Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args = null): array
        {
        }
        /**
         * Calculate the redirect language fallback priority
         *
         * @param int $siteId
         * @return float The redirect language fallback priority
         */
        protected function languageFallbackPriority(int $siteId): float
        {
        }
        /**
         * Configures the language tag for a given language.
         *
         * Will fix the language tags for language variants
         * and will remove the third part from language ta so de-DE-formal will become de-DE
         *
         * @param Language $language The language Object.
         * @return string The language bcp47 tag.
         */
        protected function configureLanguageTag(\Inpsyde\MultilingualPress\Framework\Language\Language $language): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Redirect {
    /**
     * Permalink filter adding the noredirect query argument.
     */
    final class NoredirectPermalinkFilter implements \Inpsyde\MultilingualPress\Framework\Filter\Filter
    {
        use \Inpsyde\MultilingualPress\Framework\Filter\FilterTrait;
        public const QUERY_ARGUMENT = 'noredirect';
        public function __construct()
        {
        }
        /**
         * Adds the no-redirect query argument to the permalink, if applicable.
         *
         * @param string $url
         * @param int $siteId
         * @return string
         * @throws NonexistentTable
         */
        public function addNoRedirectQueryArgument(string $url, int $siteId): string
        {
        }
        /**
         * Removes the noredirect query argument from the given URL.
         *
         * @param string $url
         * @return string
         */
        public function removeNoRedirectQueryArgument(string $url): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Redirect\GeoLocation {
    /**
     * Something able to find the geolocation.
     */
    interface GeolocationFinderInterface
    {
        /**
         * Retrieves the user geolocation country code.
         *
         * @return string The country code of geolocation.
         * @throws RuntimeException If problem finding.
         */
        public function find(): string;
    }
    class GeolocationFinder implements \Inpsyde\MultilingualPress\Module\Redirect\GeoLocation\GeolocationFinderInterface
    {
        protected \Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request;
        protected string $geolocationServiceUrl;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request, string $geolocationServiceUrl)
        {
        }
        /**
         * @inheritDoc
         */
        public function find(): string
        {
        }
        /**
         * Finds the IP address from the request.
         *
         * @return string The IP address.
         */
        protected function findIpAddress(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Redirect {
    /**
     * @psalm-type languageCode = string
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'redirect';
        public const PARAMETER_CONFIG_MODULE_DIR_PATH = 'multilingualpress.redirect.moduleDirPath';
        public const MODULE_SCRIPTS_HANDLER_NAME = 'multilingualpress-redirect';
        public const CONFIGURATION_NAME_FOR_URL_TO_MODULE_ASSETS = 'multilingualpress.redirect.urlToModuleAssets';
        public const SETTING_NONCE_ACTION = 'multilingualpress_save_redirect_setting_nonce_';
        public const CONFIGURATION_NAME_FOR_REDIRECT_USER_LANGUAGES = 'multilingualpress.redirect.userLanguages';
        /**
         * @inheritdoc
         * @throws ModuleAlreadyRegistered
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        /**
         * @inheritdoc
         *
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Redirect\Redirector {
    /**
     * Interface for all redirector implementations.
     */
    interface Redirector
    {
        public const FILTER_REDIRECT_TYPE = 'multilingualpress.redirector_type';
        public const FILTER_REDIRECT_URL = 'multilingualpress.redirect_target.redirect_url';
        public const ACTION_TARGET_NOT_FOUND = 'multilingualpress.redirect_target_not_found';
        public const TYPE_JAVASCRIPT = 'JAVASCRIPT';
        public const TYPE_PHP = 'PHP';
        public const REDIRECT_TYPE_BROWSER_LANGUAGE = 'browser_language';
        public const REDIRECT_TYPE_GEOLOCATION = 'geolocation';
        public const REDIRECT_TYPE_GEOLOCATION_WITH_FALLBACK_TO_BROWSER_LANGUAGE = 'geolocation_with_fallback_to_browser_language';
        /**
         * Redirects the user to the best-matching language version, if any.
         *
         * @return void
         */
        public function redirect();
    }
    class PhpRedirector implements \Inpsyde\MultilingualPress\Module\Redirect\Redirector\Redirector
    {
        public function __construct(\Inpsyde\MultilingualPress\Module\Redirect\RedirectTarget\LanguageNegotiator $languageNegotiator, \Inpsyde\MultilingualPress\Module\Redirect\NoRedirectStorage\NoRedirectStorage $noRedirectStorage, \Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request, \Inpsyde\MultilingualPress\Module\Redirect\Redirector\CurrentSiteLocaleChecker $currentSiteLocaleChecker)
        {
        }
        /**
         * @inheritdoc
         */
        public function redirect(): void
        {
        }
        /**
         * Prepares the data passed to the frontend script.
         */
        public function redirectScriptData(): array
        {
        }
    }
    /**
     * Class NotFoundSiteRedirect
     * @package Inpsyde\MultilingualPress\Module\Redirect
     */
    class NotFoundSiteRedirect implements \Inpsyde\MultilingualPress\Module\Redirect\Redirector\Redirector
    {
        public function __construct(\Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository\Repository $redirectSettingsRepository, \Inpsyde\MultilingualPress\Module\Redirect\NoRedirectStorage\NoRedirectStorage $noRedirectStorage)
        {
        }
        /**
         * @inheritDoc
         */
        public function redirect(): bool
        {
        }
        /**
         * Retrieve the Site Url Where Redirect the User
         *
         * @param int $siteId
         * @return string
         * @throws NonexistentTable
         */
        protected function redirectUrlForSite(int $siteId): string
        {
        }
        /**
         * Do the Redirect and Stop the Execution
         *
         * @param string $url
         */
        protected function redirectToUrl(string $url): void
        {
        }
    }
    /**
     * @psalm-type language = string
     * @psalm-type url = string
     */
    final class JsRedirector implements \Inpsyde\MultilingualPress\Module\Redirect\Redirector\Redirector
    {
        public const FILTER_UPDATE_INTERVAL = 'multilingualpress.noredirect_update_interval';
        public const FILTER_REDIRECT_TARGET = 'multilingualpress-redirect-js-redirect_target';
        public function __construct(\Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository\Repository $redirectSettingsRepository, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $translationSearchArgs, \Inpsyde\MultilingualPress\Module\Redirect\RedirectTarget\LanguageNegotiator $languageNegotiator, \Inpsyde\MultilingualPress\Module\Redirect\Redirector\CurrentSiteLocaleChecker $currentSiteLocaleChecker)
        {
        }
        /**
         * @inheritdoc
         */
        public function redirect()
        {
        }
        /**
         * Prepares the data passed to the frontend script that handles the redirection.
         *
         * @return array
         * @throws NonexistentTable
         */
        public function redirectScriptData(): array
        {
        }
    }
    class RedirectUrlModifier
    {
        public function __invoke(string $url, int $siteId): string
        {
        }
    }
    class CurrentSiteLocaleChecker
    {
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request, \Inpsyde\MultilingualPress\Module\Redirect\AcceptLanguageParser $acceptLanguageParser)
        {
        }
        /**
         * Checks if the Request language coming from 'Accept-Language' header
         * is the same as the current site language and user does not prefer
         * target language over current site language
         */
        public function currentSiteLanguagePreferred(string $targetLanguage): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Redirect\NoRedirectStorage {
    /**
     * Interface for all noredirect storage implementations.
     */
    interface NoRedirectStorage
    {
        public const FILTER_LIFETIME = 'multilingualpress.noredirect_storage_lifetime';
        public const LIFETIME_IN_SECONDS = 5 * \MINUTE_IN_SECONDS;
        public const KEY = 'noredirect';
        /**
         * Adds the given language to the storage.
         *
         * Returns false if language is not actually added, e.g it was already added.
         *
         * @param string $language
         * @return bool
         */
        public function addLanguage(string $language): bool;
        /**
         * Checks if the given language has been stored before.
         *
         * @param string $language
         * @return bool
         */
        public function hasLanguage(string $language): bool;
    }
    /**
     * Session-based noredirect storage implementation, used when no user is logged or no persistent
     * object cache is in use.
     *
     * phpcs:disable WordPress.VIP.SessionVariableUsage
     * phpcs:disable WordPress.VIP.SessionFunctionsUsage
     */
    final class NoRedirectSessionStorage implements \Inpsyde\MultilingualPress\Module\Redirect\NoRedirectStorage\NoRedirectStorage
    {
        /**
         * @inheritdoc
         */
        public function addLanguage(string $language): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function hasLanguage(string $language): bool
        {
        }
    }
    /**
     * Object-cache-based noredirect storage implementation.
     *
     * Only used for logged-in users, so they do not mutually affect each other.
     */
    final class NoRedirectObjectCacheStorage implements \Inpsyde\MultilingualPress\Module\Redirect\NoRedirectStorage\NoRedirectStorage
    {
        /**
         * @inheritdoc
         */
        public function addLanguage(string $language): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function hasLanguage(string $language): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Redirect\Assets {
    class AssetsRegistrar
    {
        public function __construct(string $urlToModuleAssetsDirectory, \Inpsyde\MultilingualPress\Module\Redirect\Redirector\Redirector $redirector)
        {
        }
        public function register(\MultilingualPress\Vendor\Inpsyde\Assets\AssetManager $assetManager): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Redirect {
    /**
     * Parser for Accept-Language headers, sorting by priority.
     */
    class AcceptLanguageParser
    {
        /**
         * Parses the given Accept header and returns the according data in array form and returns
         * an array with language codes as keys, and priorities as values.
         *
         * @param string $header
         * @return float[]
         */
        public function parseHeader(string $header): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\Settings {
    /**
     * @psalm-type Attributes = array{
     *      class?: string,
     *      size?: int,
     *      data-connected?: string,
     *      data-none?: string
     * }
     * @psalm-type Column = array{
     *      header: string,
     *      type: string,
     *      attributes: Attributes,
     *      options: array<string, string>
     * }
     * @psalm-type ColumnName = string
     */
    class TableFormView
    {
        /**
         * @var string
         */
        public const INPUT_NAME_PREFIX = 'externalSites';
        /**
         * @var string
         */
        public const TABLE_ID = 'mlp-external-sites-table';
        protected \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesRepository\ExternalSitesRepositoryInterface $externalSitesRepository;
        protected array $columns;
        /**
         * @param ExternalSitesRepositoryInterface $externalSitesRepository
         * @param array $columns
         * @psalm-param array<ColumnName, Column> $columns
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesRepository\ExternalSitesRepositoryInterface $externalSitesRepository, array $columns)
        {
        }
        /**
         * Renders the table.
         *
         * @return void
         */
        public function render(): void
        {
        }
        /**
         * The table body markup.
         *
         * @return void
         */
        protected function tBody(): void
        {
        }
        /**
         * Creates an empty row.
         *
         * @return void
         */
        protected function emptyRow(): void
        {
        }
        /**
         * The row HTML markup.
         *
         * @param int $id The row ID.
         * @param ExternalSiteInterface $externalSite
         * @return void
         */
        protected function row(int $id, \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface $externalSite): void
        {
        }
        /**
         * The column HTML markup.
         *
         * @param string $col The column Name.
         * @param int $id The row ID.
         * @param array $data The column configuration data.
         * @psalm-param Column $data
         * @param scalar $value The input value.
         * @return void
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         */
        protected function column(string $col, int $id, array $data, $value): void
        {
        }
        /**
         * The input markup.
         *
         * @param int $id The row ID.
         * @param string $col The column name.
         * @param string $value The input value.
         * @param array $attributes The column attributes configuration.
         * @psalm-param Attributes $attributes
         * @return void
         */
        protected function text(int $id, string $col, string $value, array $attributes = []): void
        {
        }
        /**
         * The checkbox markup.
         *
         * @param int $id The row ID.
         * @param string $col The column name.
         * @param bool $value The input value.
         * @param array $attributes The column attributes configuration.
         * @psalm-param Attributes $attributes
         */
        protected function checkbox(int $id, string $col, bool $value, array $attributes = []): void
        {
        }
        /**
         * The select markup.
         *
         * @param int $id The row ID.
         * @param string $col The column name.
         * @param string $value The input value.
         * @param array<string, string> $options The select options.
         * @param array $attributes The column attributes configuration.
         * @psalm-param Attributes $attributes
         */
        protected function select(int $id, string $col, string $value, array $options, array $attributes = []): void
        {
        }
        /**
         * Creates the input name from given row ID and column name.
         *
         * @param int $id The row ID
         * @param string $col The column name.
         * @return string The input name.
         */
        protected function inputName(int $id, string $col): string
        {
        }
        /**
         * Creates the input ID from given row ID and column name.
         *
         * @param int $id The row ID
         * @param string $col The column name.
         * @return string The input ID.
         */
        protected function inputId(int $id, string $col): string
        {
        }
        /**
         * The table head HTML markup.
         *
         * @return void
         */
        protected function header(): void
        {
        }
    }
    class PageView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        protected \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce;
        protected \Inpsyde\MultilingualPress\Framework\Http\Request $request;
        protected \Inpsyde\MultilingualPress\Module\ExternalSites\Settings\TableFormView $table;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Module\ExternalSites\Settings\TableFormView $table)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(): void
        {
        }
        /**
         * Renders the form.
         *
         * @return void
         */
        protected function renderForm(): void
        {
        }
    }
    /**
     * @psalm-type Action = 'insert'|'update'|'delete'
     *
     * @psalm-type Item = array{
     *       ID: int,
     *       site_url: string,
     *       site_language_name: string,
     *       site_language_locale: string,
     *       enable_hreflang: int,
     *       site_redirect: int,
     *       display_style: string
     * }
     */
    class RequestHandler
    {
        public const ACTION = 'update_multilingualpress_external_sites';
        public const ACTION_AFTER_EXTERNAL_SITE_IS_DELETED = 'multilingualpress.after_external_site_is_deleted';
        protected \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce;
        protected \Inpsyde\MultilingualPress\Framework\Http\Request $request;
        protected \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesRepository\ExternalSitesRepositoryInterface $externalSitesRepository;
        protected \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesRepository\ExternalSitesRepositoryInterface $externalSitesRepository, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices)
        {
        }
        /**
         * Handles the POST requests.
         */
        public function handlePostRequest(): void
        {
        }
        /**
         * Process the given action(insert, update, delete) with the given data items.
         *
         * @param string $action The action name, can be "insert", "update" or "delete".
         * @psalm-param Action $action
         * @param array $items The list of items.
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function processAction(string $action, array $items): void
        {
        }
        /**
         * Splits the request data into the map of appropriate action names to a list of external site items.
         *
         * @param array $externalSites The list of external site items.
         * @psalm-param Item[] $externalSites
         * @return array A map of appropriate action name to a list of external site items.
         */
        protected function splitExternalSites(array $externalSites): array
        {
        }
        /**
         * Configures the external site's request data.
         *
         * @param array $externalSites The list of external site items.
         * @psalm-param Item[] $externalSites
         * @psalm-param-out array $externalSites
         */
        protected function configureExternalSitesRequestData(array &$externalSites): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesMetaBox {
    /**
     * Can render an External sites MetaBox.
     */
    interface ExternalSitesMetaBoxViewInterface
    {
        /**
         * Renders the given external sites MetaBox HTML markup for given post.
         *
         * @param ExternalSiteInterface[] $externalSites The list of external sites.
         * @param int $postId The post ID.
         */
        public function render(array $externalSites, int $postId): void;
    }
    class ExternalSitesMetaBoxView implements \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesMetaBox\ExternalSitesMetaBoxViewInterface
    {
        public const META_NAME = 'mlp-external-sites';
        /**
         * @inheritDoc
         */
        public function render(array $externalSites, int $postId): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Flags {
    /**
     * Can create flag for external sites.
     */
    interface ExternalSiteFlagFactoryInterface
    {
        /**
         * Creates flag image tag for given external site.
         *
         * @param ExternalSiteInterface $externalSite
         * @return Flag The flag(<img>) tag.
         */
        public function create(\Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface $externalSite): \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Flag;
    }
    class ExternalSiteFlagFactory implements \Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Flags\ExternalSiteFlagFactoryInterface
    {
        public function __construct(\Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Factory $flagFactory, bool $isSiteFlagsModuleActive)
        {
        }
        /**
         * @inheritDoc
         */
        public function create(\Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface $externalSite): \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Flag
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\Integrations {
    class QuickLinksIntegration implements \Inpsyde\MultilingualPress\Framework\Integration\Integration
    {
        /**
         * @var array<ExternalSiteInterface>
         */
        protected array $externalSites;
        public function __construct(array $externalSites)
        {
        }
        /**
         * @inheritDoc
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        public function integrate(): void
        {
        }
        /**
         * Gets the external site url from entity meta by given external site ID.
         *
         * @param int $externalSiteId The external site ID.
         * @return string The eternal site url.
         */
        protected function externalSiteUrlById(int $externalSiteId): string
        {
        }
    }
    class LanguageSwitcherWidgetIntegration implements \Inpsyde\MultilingualPress\Framework\Integration\Integration
    {
        /**
         * @var array<ExternalSiteInterface>
         */
        protected array $externalSites;
        protected \Inpsyde\MultilingualPress\Module\LanguageSwitcher\ItemFactory $itemFactory;
        protected \Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Flags\ExternalSiteFlagFactoryInterface $externalSiteFlagFactory;
        protected string $externalSiteKeyWord;
        public function __construct(array $externalSites, \Inpsyde\MultilingualPress\Module\LanguageSwitcher\ItemFactory $itemFactory, \Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Flags\ExternalSiteFlagFactoryInterface $externalSiteFlagFactory, string $externalSiteKeyWord)
        {
        }
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        public function integrate(): void
        {
        }
        /**
         * Gets the external site url from entity meta by given external site ID.
         *
         * @param int $externalSiteId The external site ID.
         * @return string The eternal site url.
         */
        protected function externalSiteUrlById(int $externalSiteId): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Redirect {
    /**
     * @psalm-type languageCode = string
     * @psalm-type url = string
     */
    class RedirectIntegration implements \Inpsyde\MultilingualPress\Framework\Integration\Integration
    {
        /**
         * @var array<ExternalSiteInterface>
         */
        protected array $externalSites;
        protected \Inpsyde\MultilingualPress\Module\Redirect\RedirectTarget\LanguageNegotiator $languageNegotiator;
        protected \Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Redirect\ExternalSiteRedirectTargetFactoryInterface $externalSiteRedirectTargetFactory;
        protected \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository\Repository $redirectSettingsRepository;
        protected \Inpsyde\MultilingualPress\Module\Redirect\Settings\Renderers\ViewRenderer $externalRedirectFallbackViewRenderer;
        protected \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesRepository\ExternalSitesRepositoryInterface $externalSitesRepository;
        /**
         * A map of language codes to priorities.
         *
         * @var array<string, float>
         * @psalm-var array<languageCode, float>
         */
        protected array $userLanguages;
        protected \Inpsyde\MultilingualPress\Module\Redirect\GeoLocation\GeolocationFinderInterface $geolocationFinder;
        public function __construct(array $externalSites, \Inpsyde\MultilingualPress\Module\Redirect\RedirectTarget\LanguageNegotiator $languageNegotiator, \Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Redirect\ExternalSiteRedirectTargetFactoryInterface $externalSiteRedirectTargetFactory, \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository\Repository $redirectSettingsRepository, \Inpsyde\MultilingualPress\Module\Redirect\Settings\Renderers\ViewRenderer $externalRedirectFallbackViewRenderer, \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesRepository\ExternalSitesRepositoryInterface $externalSitesRepository, array $userLanguages, \Inpsyde\MultilingualPress\Module\Redirect\GeoLocation\GeolocationFinderInterface $geolocationFinder)
        {
        }
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        public function integrate(): void
        {
        }
        /**
         * Integrates the redirect fallback functionality for external sites.
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         *
         * @return void
         */
        protected function integrateRedirectFallback(): void
        {
        }
        /**
         * Gets the external site url from entity meta by given external site ID.
         *
         * @param int $externalSiteId The external site ID.
         * @return string The eternal site url.
         */
        protected function externalSiteUrlById(int $externalSiteId): string
        {
        }
        /**
         * Checks if redirect is enabled for any external site.
         *
         * @return bool true if redirect is enabled for any external site, otherwise false.
         */
        protected function isRedirectEnabledForAnyExternalSite(): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Redirect\Fallback {
    class ExternalRedirectFallbackViewRenderer implements \Inpsyde\MultilingualPress\Module\Redirect\Settings\Renderers\ViewRenderer
    {
        /**
         * @var array<ExternalSiteInterface>
         */
        protected array $externalSites;
        protected \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository\Repository $repository;
        public function __construct(array $externalSites, \Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository\Repository $repository)
        {
        }
        /**
         * @inheritDoc
         */
        public function title(): void
        {
        }
        /**
         * @inheritDoc
         */
        public function content(): void
        {
        }
        /**
         * Renders the options List of external sites that can be selected.
         *
         * @param ExternalSiteInterface[] $externalSites
         * @param int $selected The selected site ID.
         */
        protected function renderOptionsForSites(array $externalSites, int $selected): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Redirect {
    /**
     * Can create RedirectTarget for external site.
     *
     * @psalm-type redirectTargetConfig = array{
     *      locale: string,
     *      priority: int,
     *      siteId: int,
     *      url: string,
     * }
     * @psalm-type languageCode = string
     */
    interface ExternalSiteRedirectTargetFactoryInterface
    {
        /**
         * Creates a new RedirectTarget instance with a given config.
         *
         * @param array<string, float> $userLanguages A map of language codes to priorities.
         * @psalm-param array<languageCode, float> $userLanguages
         * @param GeolocationFinderInterface $geolocationFinder
         * @param array $config The config.
         * @psalm-param redirectTargetConfig $config
         * @return RedirectTarget The new instance.
         * @throws RuntimeException If problem creating.
         */
        public function createExternalSiteRedirectTarget(array $userLanguages, \Inpsyde\MultilingualPress\Module\Redirect\GeoLocation\GeolocationFinderInterface $geolocationFinder, array $config): \Inpsyde\MultilingualPress\Module\Redirect\RedirectTarget\RedirectTarget;
    }
    class ExternalSiteRedirectTargetFactory implements \Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Redirect\ExternalSiteRedirectTargetFactoryInterface
    {
        /**
         * @inheritDoc
         */
        public function createExternalSiteRedirectTarget(array $userLanguages, \Inpsyde\MultilingualPress\Module\Redirect\GeoLocation\GeolocationFinderInterface $geolocationFinder, array $config): \Inpsyde\MultilingualPress\Module\Redirect\RedirectTarget\RedirectTarget
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\Integrations {
    class HreflangIntegration implements \Inpsyde\MultilingualPress\Framework\Integration\Integration
    {
        /**
         * @var array<ExternalSiteInterface>
         */
        protected array $externalSites;
        protected array $ksesTags;
        public function __construct(array $externalSites, array $ksesTags)
        {
        }
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        public function integrate(): void
        {
        }
        /**
         * Gets the external site url from entity meta by given external site ID.
         *
         * @param int $externalSiteId The external site ID.
         * @return string The eternal site url.
         */
        protected function externalSiteUrlById(int $externalSiteId): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites {
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'external-sites';
        public const NONCE_ACTION_FOR_EXTERNAL_SITES_NAV_MENU = 'add_external_sites_to_nav_menu';
        public const CONFIGURATION_NAME_FOR_EXTERNAL_SITE_KEYWORD = 'multilingualpress.externalSites.ExternalSiteKeyWord';
        public const CONFIGURATION_NAME_FOR_EXTERNAL_SITE_DISPLAY_STYLES = 'multilingualpress.externalSites.DisplayStyle';
        public const CONFIGURATION_NAME_FOR_FLAGS_FOLDER_PATH = 'multilingualpress.FlagsFolderPath';
        public const CONFIGURATION_NAME_FOR_UNSUPPORTED_POST_TYPES = 'multilingualpress.externalSites.Integrations.UnsupportedPostTypes';
        public const MODULE_SCRIPTS_HANDLER_NAME = 'multilingualpress-external-sites';
        public const CONFIGURATION_NAME_FOR_URL_TO_MODULE_ASSETS = 'multilingualpress.externalSites.urlToModuleAssets';
        /**
         * @inheritdoc
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Performs various tasks when is in admin screen on module activation.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService
         * @throws NameNotFound
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        protected function activateModuleForAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Renders the MetaBoxes for given external sites.
         *
         * @param ExternalSiteInterface[] $externalSites The list of external sites.
         * @param ExternalSitesMetaBoxViewInterface $externalSitesMetaBoxView
         * @param string[] $unsupportedPostTypes The list of unsupported post types.
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function renderMetaBoxes(array $externalSites, \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesMetaBox\ExternalSitesMetaBoxViewInterface $externalSitesMetaBoxView, array $unsupportedPostTypes): void
        {
        }
        /**
         * Saves the requested external sites metabox values.
         *
         * @param ServerRequest $request
         */
        protected function saveMetaBoxes(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request): void
        {
        }
        /**
         * Filters the external site menu item on frontend.
         *
         * @param ExternalSitesRepository $externalSitesRepository
         * @param bool $isSiteFlagsModuleActive true if the site flags module is active, otherwise false.
         * @param ExternalSiteFlagFactoryInterface $externalSiteFlagFactory
         * @throws Throwable
         */
        // phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
        // phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
        // phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
        protected function filterExternalSiteMenuItem(\Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesRepository\ExternalSitesRepository $externalSitesRepository, bool $isSiteFlagsModuleActive, \Inpsyde\MultilingualPress\Module\ExternalSites\Integrations\Flags\ExternalSiteFlagFactoryInterface $externalSiteFlagFactory): void
        {
        }
        /**
         * Filters the menu items for external sites.
         *
         * @param wpdb $wpdb
         */
        protected function filterMenuItems(\wpdb $wpdb): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\NavMenu {
    class CopyNavMenu
    {
        /**
         * @wp-hook multilingualpress.copy_nav_menu.item_copied
         */
        public function copyExternalSiteIdMeta(int $menuItemId, int $remoteMenuItemId, int $remoteSiteId): void
        {
        }
    }
    class MetaBoxView
    {
        public const ID = 'mlp-navMenu-external-sites';
        /**
         * @var ExternalSiteInterface[]
         */
        protected array $externalSites;
        protected string $selectAllUrl;
        protected array $submitButtonAttributes;
        public function __construct(array $externalSites, string $selectAllUrl, array $submitButtonAttributes)
        {
        }
        /**
         * @inheritDoc
         */
        public function render(): void
        {
        }
        /**
         * Renders checkboxes to select external sites.
         */
        protected function renderCheckboxes(): void
        {
        }
        /**
         * Renders a single item for given external site with given name.
         *
         * @param string $name The item name.
         * @param int $siteId The external site ID.
         */
        protected function renderCheckbox(string $name, int $siteId): void
        {
        }
        /**
         * Renders the button controls HTML.
         */
        protected function renderButtonControls(): void
        {
        }
    }
    class AjaxHandler
    {
        public const ACTION = 'multilingualpress_add_external_sites_to_nav_menu';
        protected \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce;
        protected \Inpsyde\MultilingualPress\Framework\Http\Request $request;
        protected \Inpsyde\MultilingualPress\Module\ExternalSites\NavMenu\ExternalSiteMenuItemFactoryInterface $externalSiteMenuItemFactory;
        /**
         * @var ExternalSiteInterface[]
         */
        protected array $allExternalSites;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Module\ExternalSites\NavMenu\ExternalSiteMenuItemFactoryInterface $externalSiteMenuItemFactory, array $allExternalSites)
        {
        }
        /**
         * Handles the AJAX request and sends an appropriate response.
         */
        public function handle(): void
        {
        }
        /**
         * Gets the list of external site IDs from request.
         *
         * @return int[] The list of external site IDs.
         */
        protected function externalSiteIdsFromRequest(): array
        {
        }
    }
    /**
     * Can create a menu item for external site.
     *
     */
    interface ExternalSiteMenuItemFactoryInterface
    {
        /**
         * Creates a new menu item in given menu for given external site
         *
         * @param int $menuId The WordPress navigation menu ID.
         * @param ExternalSiteInterface $externalSite The external site.
         * @return WP_Post The WordPress menu item object.
         * @throws RuntimeException If problem creating.
         */
        public function createExternalSiteMenuItem(int $menuId, \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface $externalSite): \WP_Post;
    }
    class ExternalSiteMenuItemFactory implements \Inpsyde\MultilingualPress\Module\ExternalSites\NavMenu\ExternalSiteMenuItemFactoryInterface
    {
        public const META_KEY_EXTERNAL_SITE_ID = '_external_site_id';
        public const META_KEY_ITEM_TYPE = '_menu_item_type';
        protected const ITEM_TYPE = 'mlp_external_site';
        protected const FILTER_MENU_EXTERNAL_SITE_NAME = 'multilingualpress.nav_menu_external_site_name';
        /**
         * @inheritDoc
         */
        public function createExternalSiteMenuItem(int $menuId, \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface $externalSite): \WP_Post
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite {
    /**
     * Can create an ExternalSite.
     *
     * @psalm-type externalSiteConfig = array{
     *      ID: int|string,
     *      site_url: string,
     *      site_language_name: string,
     *      site_language_locale: string,
     *      site_redirect: int,
     *      enable_hreflang: int,
     *      display_style: string,
     * }
     */
    interface ExternalSiteFactoryInterface
    {
        /**
         * Creates a new external site instance with a given config.
         *
         * @param array $config The config.
         * @psalm-param externalSiteConfig $config
         * @return ExternalSiteInterface The new instance.
         * @throws RuntimeException If problem creating.
         */
        public function createExternalSite(array $config): \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface;
    }
    /**
     * Represents the ExternalSite.
     */
    interface ExternalSiteInterface
    {
        /**
         * The ID of the external site.
         *
         * @return int
         */
        public function id(): int;
        /**
         * The external site language name.
         *
         * @return string
         */
        public function languageName(): string;
        /**
         * The external site URL.
         *
         * @return string
         */
        public function siteUrl(): string;
        /**
         * The external site language locale.
         *
         * @return string
         */
        public function locale(): string;
        /**
         * Whether redirect is enabled for external site.
         *
         * @return bool
         */
        public function isRedirectEnabled(): bool;
        /**
         * Whether display of hreflang is enabled for external site.
         *
         * @return bool
         */
        public function isHreflangEnabled(): bool;
        /**
         * The external site display style.
         *
         * @return string
         */
        public function displayStyle(): string;
    }
    class ExternalSiteFactory implements \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteFactoryInterface
    {
        /**
         * @inheritDoc
         */
        public function createExternalSite(array $config): \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface
        {
        }
    }
    class ExternalSite implements \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface
    {
        protected int $id;
        protected string $languageName;
        protected string $siteUrl;
        protected string $locale;
        protected bool $isRedirectEnabled;
        protected bool $isHreflangEnabled;
        protected string $displayStyle;
        public function __construct(int $id, string $siteUrl, string $languageName, string $locale, bool $isRedirectEnabled, bool $isHreflangEnabled, string $displayStyle)
        {
        }
        /**
         * @inheritDoc
         */
        public function id(): int
        {
        }
        /**
         * @inheritDoc
         */
        public function languageName(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function siteUrl(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function locale(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function isRedirectEnabled(): bool
        {
        }
        /**
         * @inheritDoc
         */
        public function isHreflangEnabled(): bool
        {
        }
        /**
         * @inheritDoc
         */
        public function displayStyle(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesRepository {
    /**
     * Represents the repository for ExternalSites.
     *
     * @psalm-type externalSiteData = array{
     *      ID: int,
     *      site_url: string,
     *      site_language_name: string,
     *      site_language_locale: string,
     *      enable_hreflang: int,
     *      site_redirect: int,
     *      display_style: string
     * }
     */
    interface ExternalSitesRepositoryInterface
    {
        /**
         * Deletes the external site with the given ID.
         *
         * @param int $id The external site ID.
         * @throws RuntimeException If problem deleting.
         */
        public function deleteExternalSite(int $id): void;
        /**
         * Returns the list of all existing external sites.
         *
         * @return ExternalSiteInterface[] The list of all existing external sites.
         * @throws RuntimeException If problem returning.
         */
        public function allExternalSites(): array;
        /**
         * Inserts the external site entry according to the given data.
         *
         * @param array $externalSiteData The requested external site data.
         * @psalm-param externalSiteData $externalSiteData
         * @throws RuntimeException If problem inserting.
         */
        public function insertExternalSite(array $externalSiteData): void;
        /**
         * Updates the external site entry according to the given data.
         *
         * @param int $siteId The external site ID to update.
         * @param array $externalSiteData The requested external site data.
         * @psalm-param externalSiteData $externalSiteData
         * @throws RuntimeException If problem updating.
         */
        public function updateExternalSite(int $siteId, array $externalSiteData): void;
        /**
         * Returns the external site for the given column.
         *
         * @param string $column The column name.
         * @param string|int $value The column value.
         * @return ExternalSiteInterface|null The external site or null if it doesn't exist with the given params.
         * @throws RuntimeException If problem returning.
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function externalSiteBy(string $column, $value): ?\Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface;
        /**
         * Returns the auto increment value from external sites table.
         *
         * @return int
         */
        public function autoIncrementValue(): int;
    }
    /**
     * @psalm-type columnName = string
     * @psalm-type specification = string
     * @psalm-type externalSiteConfig = array{
     *      ID: int,
     *      site_url: string,
     *      site_language_name: string,
     *      site_language_locale: string,
     *      enable_hreflang: int,
     *      site_redirect: int,
     *      display_style: string
     * }
     */
    class ExternalSitesRepository implements \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSitesRepository\ExternalSitesRepositoryInterface
    {
        protected \wpdb $wpdb;
        protected \Inpsyde\MultilingualPress\Framework\Database\Table $table;
        protected \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteFactoryInterface $externalSiteFactory;
        /**
         * @var string[]
         */
        protected array $requiredColumnNames;
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Framework\Database\Table $table, \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteFactoryInterface $externalSiteFactory, array $requiredColumnNames)
        {
        }
        /**
         * @inheritDoc
         * @throws NonexistentTable
         */
        public function deleteExternalSite(int $id): void
        {
        }
        /**
         * @inheritDoc
         * @throws NonexistentTable
         */
        public function allExternalSites(): array
        {
        }
        /**
         * @inheritDoc
         * @throws NonexistentTable
         */
        public function insertExternalSite(array $externalSiteData): void
        {
        }
        /**
         * @inheritDoc
         * @throws NonexistentTable
         */
        public function updateExternalSite(int $siteId, array $externalSiteData): void
        {
        }
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function externalSiteBy(string $column, $value): ?\Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface
        {
        }
        /**
         * @inheritDoc
         */
        public function autoIncrementValue(): int
        {
        }
        /**
         * Creates a new external site instance with a given config.
         *
         * @param array $config A map of external site field name to value.
         * @psalm-param externalSiteConfig $config
         * @return ExternalSiteInterface The new instance.
         */
        protected function createExternalSite(array $config): \Inpsyde\MultilingualPress\Module\ExternalSites\ExternalSite\ExternalSiteInterface
        {
        }
        /**
         * Extracts the column specifications from a given table.
         *
         * @param Table $table The table.
         * @return array<string, string> A map of column name to specification.
         * @psalm-return array<columnName, specification>
         */
        protected function extractColumnSpecifications(\Inpsyde\MultilingualPress\Framework\Database\Table $table): array
        {
        }
        /**
         * finds the data specifications from the given column specifications.
         *
         * @param array<string, string> $columnSpecifications A map of column name to specification.
         * @psalm-param array<columnName, specification> $columnSpecifications
         * @param array $data The request data.
         * @psalm-param externalSiteConfig $data
         * @return string[] The list of specifications.
         */
        protected function findSpecifications(array $columnSpecifications, array $data): array
        {
        }
        /**
         * Validates the required data.
         *
         * @param array $data The request data.
         * @psalm-param externalSiteConfig $data
         * @return void
         * @throws RuntimeException if validation fails.
         * @psalm-suppress PossiblyFalseArgument
         */
        protected function validateData(array $data): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\SiteDuplication {
    class SiteDuplicationServices implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
    // phpcs:ignore Inpsyde.CodeQuality.PropertyPerClassLimit.TooManyProperties
    class SiteDuplicationRequestHandler
    {
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Factory\EntityCollectionFactoryInterface $entityCollectionFactory;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslatorManager\TranslatorManagerInterface $translatorManager;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Scheduler\TranslationSchedulerInterface $translationScheduler;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Scheduler\TranslationSchedulerInterface $commentTranslationScheduler;
        protected \Inpsyde\MultilingualPress\Framework\Http\Request $request;
        protected \Inpsyde\MultilingualPress\Core\PostTypeRepositoryInterface $postTypeRepository;
        protected \Inpsyde\MultilingualPress\Core\TaxonomyRepository $taxonomyRepository;
        protected \Inpsyde\MultilingualPress\Module\Comments\SiteSettings\CommentsSettingsRepository $commentsSettingsRepository;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $postCollectionRepository;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $termCollectionRepository;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $commentCollectionRepository;
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Factory\EntityCollectionFactoryInterface $entityCollectionFactory, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslatorManager\TranslatorManagerInterface $translatorManager, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Scheduler\TranslationSchedulerInterface $translationScheduler, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Scheduler\TranslationSchedulerInterface $commentTranslationScheduler, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Core\PostTypeRepositoryInterface $postTypeRepository, \Inpsyde\MultilingualPress\Core\TaxonomyRepository $taxonomyRepository, \Inpsyde\MultilingualPress\Module\Comments\SiteSettings\CommentsSettingsRepository $commentsSettingsRepository, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $postCollectionRepository, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $termCollectionRepository, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $commentCollectionRepository)
        {
        }
        public function handle(int $sourceSiteId, int $newSiteId): void
        {
        }
        /**
         * Schedules translation for posts and terms if content exists.
         *
         * @param int $sourceSiteId
         * @param int $newSiteId
         * @param string $translatorId
         */
        protected function scheduleTranslateContent(int $sourceSiteId, int $newSiteId, string $translatorId): void
        {
        }
        /**
         * Schedules translation for comments if available.
         *
         * @param int $sourceSiteId
         * @param int $newSiteId
         * @param string $translatorId
         */
        protected function scheduleTranslateComments(int $sourceSiteId, int $newSiteId, string $translatorId): void
        {
        }
    }
    class SiteDuplicationActivator implements \Inpsyde\MultilingualPress\Framework\Module\Activator
    {
        public function activate(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Settings\SiteDuplication {
    class TranslateContentSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        protected string $inputId;
        protected string $inputName;
        protected string $title;
        protected string $description;
        /**
         * @var string[]
         */
        protected array $options;
        public function __construct(string $inputId, string $inputName, string $title, string $description, array $options)
        {
        }
        public function render(int $siteId): void
        {
        }
        public function title(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Settings\SiteSettings {
    class TranslateComment implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        public const OPTION_TRANSLATE_COMMENTS = 'translate_comments';
        public const OPTION_TRANSLATE_NEW_COMMENT = 'translate_new_comment';
        protected \Inpsyde\MultilingualPress\Module\Comments\SiteSettings\CommentsSettingsRepositoryInterface $commentsSettingsRepository;
        protected string $optionId;
        protected int $remoteSiteId;
        protected string $postType;
        protected string $title;
        public function __construct(\Inpsyde\MultilingualPress\Module\Comments\SiteSettings\CommentsSettingsRepositoryInterface $commentsSettingsRepository, string $optionId, int $remoteSiteId, string $postType, string $title)
        {
        }
        public function render(int $sourceSiteId): void
        {
        }
        public function title(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Settings {
    /**
     * @psalm-import-type CommentSettings
     */
    class SettingsActivator implements \Inpsyde\MultilingualPress\Framework\Module\Activator
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
         */
        public function activate(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
    /**
     * Contains information about setting names.
     */
    interface SettingNamesInterface
    {
        /**
         * AWS Settings
         */
        public const SETTINGS_GROUP_AWS = 'aws';
        public const SETTINGS_GROUP_AWS_SETTING_OPTION_ENABLED = 'aws_enabled';
        public const SETTINGS_GROUP_AWS_SETTING_OPTION_KEY = 'aws_key';
        public const SETTINGS_GROUP_AWS_SETTING_OPTION_SECRET = 'aws_secret';
        public const SETTINGS_GROUP_AWS_SETTING_OPTION_REGION = 'aws_region';
        /**
         * DeepL Settings
         */
        public const SETTINGS_GROUP_DEEPL = 'deepl';
        public const SETTINGS_GROUP_DEEPL_SETTING_OPTION_ENABLED = 'deepl_enabled';
        public const SETTINGS_GROUP_DEEPL_SETTING_OPTION_KEY = 'deepl_key';
        /**
         * OpenAI Settings
         */
        public const SETTINGS_GROUP_OPENAI = 'openai';
        public const SETTINGS_GROUP_OPENAI_SETTING_OPTION_ENABLED = 'openai_enabled';
        public const SETTINGS_GROUP_OPENAI_SETTING_OPTION_KEY = 'openai_key';
        public const SETTINGS_GROUP_OPENAI_SETTING_MODEL = 'openai_model';
        /**
         * Translator Preference Settings
         */
        public const SETTINGS_GROUP_TRANSLATOR_PREFERENCE = 'translator-preference';
        public const SETTING_GROUP_TRANSLATOR_PREFERENCE_SETTING_OPTION_DEFAULT_TRANSLATOR = 'select_default_translator';
        public const SETTING_GROUP_TRANSLATOR_PREFERENCE_SETTING_OPTION_DEFAULT_FORMALITY = 'default_translation_formality';
        /**
         * Formality setting options
         */
        public const FORMALITY_DETECT_TARGET_SITE_FORMALITY = 'target_site_language_formality';
        public const FORMALITY_SAME_AS_SOURCE_TEXT = 'preserve_source_text_formality';
        public const FORMALITY_FORCE_FORMAL = 'formal';
        public const FORMALITY_FORCE_INFORMAL = 'informal';
        public const FORMALITY_DEFAULT = self::FORMALITY_DETECT_TARGET_SITE_FORMALITY;
    }
    /**
     * @psalm-type SettingIdentifier = string
     */
    class SettingsServices implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        public const PARAMETER_CONFIG_LIST_OF_NETWORK_SETTINGS = 'multilingualpress.automaticTranslation.settings';
        public const PARAMETER_CONFIG_MODULE_SETTINGS_NAME = 'multilingualpress.automaticTranslation.settings.moduleSettingsName';
        public const PARAMETER_CONFIG_MODULE_SETTINGS_NONCE = 'multilingualpress.automaticTranslation.settings.moduleSettingsNonce';
        public const PARAMETER_CONFIG_SETTINGS_DEEPL_VALUES = 'multilingualpress.automaticTranslation.settings.deepLValues';
        public const PARAMETER_CONFIG_SETTINGS_AWS_VALUES = 'multilingualpress.automaticTranslation.settings.awsValues';
        public const PARAMETER_CONFIG_SETTINGS_OPENAI_VALUES = 'multilingualpress.automaticTranslation.settings.openAiValues';
        public const PARAMETER_CONFIG_SETTINGS_SITE_DUPLICATION_SETTINGS = 'multilingualpress.automaticTranslation.settings.siteDuplication.settings';
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Registers the services for network settings.
         *
         * @param Container $container
         * @return void
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        protected function registerServicesForNetworkSettings(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @return array<string, array{label: string, description: string}>
         */
        // phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
        public static function formalitySettingOptions(): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Settings\NetworkSettings {
    class AutomaticTranslationSettingsRepository implements \Inpsyde\MultilingualPress\Framework\Setting\SettingsRepositoryInterface
    {
        protected string $moduleSettingsName;
        /**
         * @var array<string, scalar|array>
         */
        protected static array $cachedSettings = [];
        public function __construct(string $moduleSettingsName)
        {
        }
        public function allSettingValues(): array
        {
        }
        public function settingValue(string $settingName): array
        {
        }
        public function updateSettings(array $settingsMap): void
        {
        }
    }
    class AutomaticTranslationSettingsTabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        public const FILTER_SETTINGS = 'multilingualpress.AutomaticTranslation.Settings';
        protected \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce;
        /**
         * @var array<SettingInterface | SettingWithImageInterface>
         */
        protected array $settings;
        protected \Inpsyde\MultilingualPress\Framework\Setting\SettingsRepositoryInterface $automaticTranslationSettingsRepository;
        protected string $automaticTranslationModuleSettingsName;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, array $settings, \Inpsyde\MultilingualPress\Framework\Setting\SettingsRepositoryInterface $automaticTranslationSettingsRepository, string $automaticTranslationModuleSettingsName)
        {
        }
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function render(): void
        {
        }
        /**
         * Renders the setting option.
         *
         * @param SettingOptionInterface $option The setting option.
         * @param string $settingsId The setting ID.
         * @return void
         * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function renderSettingOption(\Inpsyde\MultilingualPress\Framework\Setting\SettingOptionInterface $option, string $settingsId): void
        {
        }
        /**
         * Renders the text input.
         *
         * @param string $id The input ID.
         * @param string $name The input name.
         * @param string $value The input value.
         * @return void
         */
        protected function renderTextInput(string $id, string $name, string $value): void
        {
        }
        /**
         * Renders the checkbox input.
         *
         * @param string $id The input ID.
         * @param string $label The input label.
         * @param string $name The input name.
         * @param bool $value The input value.
         * @return void
         */
        protected function renderCheckboxInput(string $id, string $label, string $name, bool $value): void
        {
        }
        /**
         * Renders the select input.
         *
         * @param string $id The input ID.
         * @param string $name The input name.
         * @param string $value The input value.
         * @param array<string, string> $options The map of select option keys to values.
         * @return void
         */
        protected function renderSelectInput(string $id, string $name, string $value, array $options): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository {
    /**
     * Represents the repository for entity collections.
     */
    interface EntityCollectionRepositoryInterface
    {
        /**
         * Saves the collection in DB for the given site.
         *
         * @param int $siteId The site ID.
         * @param EntityCollectionInterface $entityCollection
         * @return void
         * @throws RuntimeException If problem saving.
         */
        public function saveCollection(int $siteId, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\EntityCollectionInterface $entityCollection): void;
        /**
         * Deletes the collection from DB for the given site.
         *
         * @param int $siteId The site ID.
         * @return void
         * @throws RuntimeException If problem deleting.
         */
        public function deleteCollection(int $siteId): void;
        /**
         * Retrieves the collection for the given site.
         *
         * @param int $siteId The site ID.
         * @param int $offset The offset, number of entities to skip before retrieving entities.
         * @param int|null $limit The limit, maximum number of entities to retrieve. Use null for no limit.
         * @return EntityCollectionInterface
         */
        public function findCollection(int $siteId, int $offset = 0, ?int $limit = null): \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\EntityCollectionInterface;
    }
    class EntityCollectionRepository implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface
    {
        protected string $optionName;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Factory\EntityCollectionFactoryInterface $entityCollectionFactory;
        public function __construct(string $optionName, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Factory\EntityCollectionFactoryInterface $entityCollectionFactory)
        {
        }
        public function saveCollection(int $siteId, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\EntityCollectionInterface $entityCollection): void
        {
        }
        public function deleteCollection(int $siteId): void
        {
        }
        public function findCollection(int $siteId, int $offset = 0, ?int $limit = null): \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\EntityCollectionInterface
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection {
    /**
     * Represents the collection for entities (post | term | comment).
     */
    interface EntityCollectionInterface
    {
        /**
         * Adds the given entity ID to the collection.
         *
         * @param int $entityId The entity ID.
         * @throws RuntimeException if problem adding.
         */
        public function addEntityId(int $entityId): \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\EntityCollectionInterface;
        /**
         * Removes the given entity ID from the collection.
         *
         * @param int $entityId The entity ID.
         * @throws RuntimeException if problem removing.
         */
        public function removeEntityId(int $entityId): \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\EntityCollectionInterface;
        /**
         * Retrieves the list of all entity IDs from the collection.
         *
         * @return int[] The list of collection entity IDs.
         * @throws RuntimeException if problem retrieving.
         */
        public function allEntityIds(): array;
    }
    /**
     * @template-implements IteratorAggregate<int>
     */
    class EntityCollection implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\EntityCollectionInterface, \Countable, \IteratorAggregate
    {
        /**
         * @var int[]
         */
        protected array $entityIds;
        /**
         * @param int[] $entityIds
         */
        public function __construct(array $entityIds)
        {
        }
        public function addEntityId(int $entityId): self
        {
        }
        public function removeEntityId(int $entityId): self
        {
        }
        public function allEntityIds(): array
        {
        }
        public function count(): int
        {
        }
        public function getIterator(): \ArrayIterator
        {
        }
    }
    class CollectionServices implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        public const PARAMETER_CONFIG_POST_REPOSITORY_COLLECTION = 'multilingualpress.automaticTranslation.collection.postRepository';
        public const PARAMETER_CONFIG_TERM_REPOSITORY_COLLECTION = 'multilingualpress.automaticTranslation.collection.termRepository';
        public const PARAMETER_CONFIG_COMMENT_REPOSITORY_COLLECTION = 'multilingualpress.automaticTranslation.collection.commentRepository';
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Factory {
    /**
     * A factory to create entity collection.
     */
    interface EntityCollectionFactoryInterface
    {
        /**
         * Creates entity collection.
         *
         * @param int[] $entityIds The list of entity IDs.
         * @return EntityCollectionInterface
         * @throws RuntimeException If problem creating.
         */
        public function create(array $entityIds): \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\EntityCollectionInterface;
    }
    class EntityCollectionFactory implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Factory\EntityCollectionFactoryInterface
    {
        public function create(array $entityIds): \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\EntityCollectionInterface
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\TranslationUi {
    class TranslationUiActivator implements \Inpsyde\MultilingualPress\Framework\Module\Activator
    {
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
         */
        public function activate(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\TranslationUi\Post\Field {
    class TranslateTitle implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        public const FILTER_TRANSLATE_TITLE_FIELD_IS_CHECKED = 'multilingualpress.AutomaticTranslation.translate_title_is_checked';
        protected string $id;
        public function __construct(string $id)
        {
        }
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class TranslateTerms implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        public const FILTER_TRANSLATE_TERMS_FIELD_IS_CHECKED = 'multilingualpress.AutomaticTranslation.translate_terms_is_checked';
        protected string $id;
        public function __construct(string $id)
        {
        }
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class TranslateExcerpt implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        public const FILTER_TRANSLATE_TITLE_FIELD_IS_CHECKED = 'multilingualpress.AutomaticTranslation.translate_excerpt_is_checked';
        protected string $id;
        public function __construct(string $id)
        {
        }
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class TranslateContent implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        public const FILTER_TRANSLATE_CONTENT_FIELD_IS_CHECKED = 'multilingualpress.AutomaticTranslation.translate_content_is_checked';
        protected string $id;
        public function __construct(string $id)
        {
        }
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\TranslationUi {
    class MetaboxTabFieldsExtension
    {
        public static function forPostMetabox(string $tabId): self
        {
        }
        public static function forTermMetabox(string $tabId): self
        {
        }
        public static function forCommentMetabox(string $tabId): self
        {
        }
        /**
         * @param PostMetaboxField | TermMetaboxField | CommentMetaboxField $fields
         */
        public function addField(...$fields): self
        {
        }
        public function filterName(): string
        {
        }
        /**
         * @return array<PostMetaboxField | TermMetaboxField | CommentMetaboxField>
         */
        public function fields(): array
        {
        }
        public function tabId(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\TranslationUi\Comment\Field {
    class TranslateComment implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field\CommentMetaboxField
    {
        public const FILTER_TRANSLATE_COMMENT = 'multilingualpress.AutomaticTranslation.translate_comment_is_checked';
        protected string $key;
        protected string $label;
        protected \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory;
        public function __construct(string $key, string $label, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory)
        {
        }
        public function key(): string
        {
        }
        public function label(): string
        {
        }
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext): void
        {
        }
        /**
         * Creates a metabox fields helper instance for the given site.
         *
         * @param int $siteId
         * @return MetaboxFieldsHelperInterface
         */
        protected function createHelper(int $siteId): \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface
        {
        }
    }
    /**
     * Adapts a callable field intended for post/term translation UI
     * to the CommentMetaboxField interface for use in comment translation UI.
     */
    class CommentSelectFieldAdapter implements \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field\CommentMetaboxField
    {
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\TranslationUi\Common\Field\AdaptableMetaboxField $field, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface $helperFactory)
        {
        }
        public function key(): string
        {
        }
        public function label(): string
        {
        }
        public function render(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContextInterface $relationshipContext): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\TranslationUi\ScheduleInfoRenderer {
    /**
     * Can render the info for the schedule status.
     */
    interface ScheduleStatusRendererInterface
    {
        /**
         * Renders the schedule status info for given site ID, post ID.
         *
         * @param int $siteId The site ID.
         * @param int $postId The post ID.
         * @param int $sourceSiteId The source post ID.
         * @return void
         */
        public function render(int $siteId, int $postId, int $sourceSiteId): void;
    }
    class ScheduleStatusRenderer implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\TranslationUi\ScheduleInfoRenderer\ScheduleStatusRendererInterface
    {
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $postCollectionRepository;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Repository\SchedulerInfoRepositoryInterface $schedulerInfoRepository;
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $postCollectionRepository, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Repository\SchedulerInfoRepositoryInterface $schedulerInfoRepository)
        {
        }
        public function render(int $siteId, int $postId, int $sourceSiteId): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\TranslationUi\Term\Field {
    class TranslateTermData
    {
        public const FILTER_TRANSLATE_TERM_DATA_FIELD_IS_CHECKED = 'multilingualpress.AutomaticTranslation.translate_term_data_is_checked';
        public function __construct(string $id)
        {
        }
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $relationshipContext): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\TranslationUi {
    class MetaboxTabExtension
    {
        public static function forPostMetabox(): self
        {
        }
        public static function forTermMetabox(): self
        {
        }
        public static function forCommentMetabox(): self
        {
        }
        /**
         * @param Post\MetaboxTab | Term\MetaboxTab | CommentMetaboxTab $tabs
         */
        public function addTab(...$tabs): self
        {
        }
        public function filterName(): string
        {
        }
        /**
         * @return array<Post\MetaboxTab | Term\MetaboxTab | CommentMetaboxTab>
         */
        public function tabs(): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\TranslationUi\Common\Field {
    /**
     * Represents a translation UI field that can be used across different metabox contexts
     * (e.g., post, term, comment).
     *
     * Implementations must provide a callable method to render the field using a helper and context.
     */
    interface AdaptableMetaboxField
    {
        /**
         * The ID used as the key when registering the field in a metabox tab.
         *
         * @return string
         */
        public function id(): string;
        /**
         * The field title.
         *
         * @return string
         */
        public function title(): string;
        /**
         * Renders the field using the provided metabox helper and relationship context.
         *
         * This method is invoked when rendering the translation metabox tab.
         * The context can be for post, term, or comment, depending on where the field is used.
         *
         * @param MetaboxFieldsHelperInterface $helper Helper to render metabox fields.
         * @param TermRelationshipContext | PostRelationshipContext | CommentsRelationshipContextInterface $context
         *          Relationship context (post, term, or comment).
         * @return void
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $helper, $context): void;
    }
    /**
     * Factory for adapting generic adaptable metabox fields to specific metabox field types.
     *
     * Currently, supports adapting to comment metabox fields.
     */
    class AdaptableMetaboxFieldFactory
    {
        public function __construct(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactory $helperFactory)
        {
        }
        public function adaptCommentField(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\TranslationUi\Common\Field\AdaptableMetaboxField $field): \Inpsyde\MultilingualPress\Module\Comments\TranslationUi\Field\CommentMetaboxField
        {
        }
    }
    class SelectTranslator implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\TranslationUi\Common\Field\AdaptableMetaboxField
    {
        public const FILTER_SELECT_TRANSLATOR_DEFAULT_VALUE = 'multilingualpress.AutomaticTranslation.select_translator_default_value';
        protected string $id;
        protected string $title;
        /**
         * @var TranslatorInterface[]
         */
        protected array $activeTranslators;
        protected string $defaultValue;
        /**
         * @param TranslatorInterface[] $activeTranslators
         */
        public function __construct(string $id, string $title, array $activeTranslators, string $defaultValue)
        {
        }
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $helper, $context): void
        {
        }
        public function title(): string
        {
        }
        public function id(): string
        {
        }
        /**
         * The message when the translator is disabled.
         *
         * @param string $sourceLang The source language code.
         * @param string $remoteLang The target language code.
         * @return string The message.
         */
        protected function disabledMessage(string $sourceLang, string $remoteLang): string
        {
        }
    }
    class SelectFormality implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\TranslationUi\Common\Field\AdaptableMetaboxField
    {
        public const FILTER_SELECT_TRANSLATOR_DEFAULT_VALUE = 'multilingualpress.AutomaticTranslation.select_formality_default_value';
        protected string $title;
        /**
         * @param array<string, array{label: string, description: string}> $options
         * @param array<TranslatorInterface> $activeTranslators
         */
        public function __construct(string $id, string $title, array $options, string $defaultValue, array $activeTranslators)
        {
        }
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface $helper, $context): void
        {
        }
        public function title(): string
        {
        }
        public function id(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\TranslationUi {
    class TranslationUiServices implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        public const PARAMETER_CONFIG_MODULE_TRANSLATION_METABOX_TABS = 'multilingualpress.automaticTranslation.metaboxTabs';
        public const PARAMETER_CONFIG_MODULE_TRANSLATION_METABOX_TAB_FIELDS = 'multilingualpress.automaticTranslation.metaboxTabFields';
        public const PARAMETER_CONFIG_MODULE_FIELD_TRANSLATOR_SELECT = 'multilingualpress.automaticTranslation.fieldTranslatorSelect';
        public const PARAMETER_CONFIG_MODULE_FIELD_FORMALITY_SELECT = 'multilingualpress.automaticTranslation.fieldFormalitySelect';
        // phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Repository {
    /**
     * The repository for managing the scheduler info.
     */
    interface SchedulerInfoRepositoryInterface
    {
        /**
         * Creates the scheduler info for the given site.
         *
         * @param int $siteId The site ID of the scheduler.
         * @param string $scheduleId The schedule ID.
         * @param int $sourceSiteId The source site ID.
         * @param string $hookName The scheduler hook name.
         * @return void
         * @throws RuntimeException If problem creating.
         */
        public function create(int $siteId, string $scheduleId, int $sourceSiteId, string $hookName): void;
        /**
         * Retrieves the scheduler info for the given site.
         *
         * @param int $siteId The site ID of the scheduler.
         * @return array{scheduleId: string, sourceSiteId: int, hookName: string } The scheduler info map.
         * @throws RuntimeException If problem retrieving.
         */
        public function read(int $siteId): array;
        /**
         * Deletes the scheduler info for the given site.
         *
         * @param int $siteId The site ID of the scheduler.
         * @return void
         * @throws RuntimeException If problem deleting.
         */
        public function delete(int $siteId): void;
        /**
         * Retrieves all the existing schedulers' info.
         *
         * @return array<int, array{scheduleId: string, sourceSiteId: int, hookName: string}> The list of scheduler infos.
         * @throws RuntimeException If problem retrieving.
         */
        public function readAllInfo(): array;
    }
    class SchedulerInfoRepository implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Repository\SchedulerInfoRepositoryInterface
    {
        protected string $optionName;
        public function __construct(string $optionName)
        {
        }
        public function create(int $siteId, string $scheduleId, int $sourceSiteId, string $hookName): void
        {
        }
        public function read(int $siteId): array
        {
        }
        public function delete(int $siteId): void
        {
        }
        public function readAllInfo(): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler {
    class SchedulerActivator implements \Inpsyde\MultilingualPress\Framework\Module\Activator
    {
        public function activate(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\ScheduleArgs {
    /**
     * A factory to create auto-translation schedule args.
     */
    interface AutoTranslationScheduleArgsFactoryInterface
    {
        /**
         * Creates auto-translation schedule args.
         *
         * @param int $sourceSiteId
         * @param int $targetSiteId
         * @param string $translatorId
         * @return AutoTranslationScheduleArgs
         * @throws RuntimeException If problem creating.
         */
        public function create(int $sourceSiteId, int $targetSiteId, string $translatorId): \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\ScheduleArgs\AutoTranslationScheduleArgs;
    }
    class AutoTranslationScheduleArgsFactory implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\ScheduleArgs\AutoTranslationScheduleArgsFactoryInterface
    {
        public function create(int $sourceSiteId, int $targetSiteId, string $translatorId): \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\ScheduleArgs\AutoTranslationScheduleArgs
        {
        }
    }
    class AutoTranslationScheduleArgs
    {
        protected string $translatorId;
        public function __construct(int $sourceSiteId, int $targetSiteId, string $translatorId)
        {
        }
        public function sourceSiteId(): int
        {
        }
        public function targetSiteId(): int
        {
        }
        public function translatorId(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Scheduler\Handler {
    class CommentTranslationScheduleHandler
    {
        public const ACTION_BEFORE_SCHEDULED_COMMENT_TRANSLATION = 'multilingualpress.before_scheduled_comment_translation';
        protected \Inpsyde\MultilingualPress\Schedule\Scheduler $scheduler;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Scheduler\TranslationSchedulerInterface $translationScheduler;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $commentCollectionRepository;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Repository\SchedulerInfoRepositoryInterface $schedulerInfoRepository;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\Handlers\AutoTranslateCommentsCommandHandler $autoTranslateCommentsCommandHandler;
        public function __construct(\Inpsyde\MultilingualPress\Schedule\Scheduler $scheduler, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Scheduler\TranslationSchedulerInterface $translationScheduler, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $commentCollectionRepository, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Repository\SchedulerInfoRepositoryInterface $schedulerInfoRepository, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\Handlers\AutoTranslateCommentsCommandHandler $autoTranslateCommentsCommandHandler)
        {
        }
        /**
         * Handles the auto-translation cron job request.
         *
         * @wp-hook multilingualpress.AutomaticTranslation.CommentTranslationScheduler
         *
         * @param stdClass $scheduleArgs
         * @return bool
         */
        public function handle(\stdClass $scheduleArgs): bool
        {
        }
    }
    class TranslationScheduleHandler
    {
        public const ACTION_BEFORE_SCHEDULED_POST_TRANSLATION = 'multilingualpress.before_scheduled_post_translation';
        protected \Inpsyde\MultilingualPress\Schedule\Scheduler $scheduler;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Scheduler\TranslationSchedulerInterface $translationScheduler;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $postCollectionRepository;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $termCollectionRepository;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Repository\SchedulerInfoRepositoryInterface $schedulerInfoRepository;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\Handlers\AutoTranslatePostsCommandHandler $autoTranslatePostsCommandHandler;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\Handlers\AutoTranslateTermsCommandHandler $autoTranslateTermsCommandHandler;
        public function __construct(\Inpsyde\MultilingualPress\Schedule\Scheduler $scheduler, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Scheduler\TranslationSchedulerInterface $translationScheduler, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $postCollectionRepository, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $termCollectionRepository, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Repository\SchedulerInfoRepositoryInterface $schedulerInfoRepository, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\Handlers\AutoTranslatePostsCommandHandler $autoTranslatePostsCommandHandler, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\Handlers\AutoTranslateTermsCommandHandler $autoTranslateTermsCommandHandler)
        {
        }
        /**
         * Handles the auto-translation cron job request.
         *
         * @wp-hook multilingualpress.AutomaticTranslation.TranslationScheduler
         *
         * @param stdClass $scheduleArgs
         * @return bool
         */
        public function handle(\stdClass $scheduleArgs): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Scheduler {
    /**
     * Represents the translation scheduler.
     */
    interface TranslationSchedulerInterface
    {
        /**
         * The limit of posts to be retrieved for the schedule.
         *
         * @return int
         */
        public function limit(): int;
        /**
         * The hook name of the schedule.
         *
         * @return string
         */
        public function hookName(): string;
        /**
         * Schedules a new set of cron jobs to translate the source site posts into the given target site language.
         *
         * @param int $sourceSiteId The source site ID.
         * @param int $targetSiteId The target site ID.
         * @param string $translatorId The translator ID.
         * @throws RuntimeException If problem scheduling.
         */
        public function schedule(int $sourceSiteId, int $targetSiteId, string $translatorId): void;
        /**
         * Clears the given schedule for the given site ID.
         *
         * @param Schedule $schedule The schedule.
         * @param int $targetSiteId The target site ID.
         * @throws RuntimeException If problem clearing.
         */
        public function clearSchedule(\Inpsyde\MultilingualPress\Schedule\Schedule $schedule, int $targetSiteId): void;
    }
    class TranslationScheduler implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Scheduler\TranslationSchedulerInterface
    {
        public const FILTER_DEFAULT_COLLECTION_LIMIT = 'multilingualpress.AutomaticTranslation.TranslationScheduler.CollectionLimit';
        protected \Inpsyde\MultilingualPress\Schedule\Scheduler $scheduler;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $postCollectionRepository;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $termCollectionRepository;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Repository\SchedulerInfoRepositoryInterface $schedulerInfoRepository;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\ScheduleArgs\AutoTranslationScheduleArgsFactoryInterface $autoTranslationScheduleArgsFactory;
        protected int $limit;
        protected string $hookName;
        public function __construct(\Inpsyde\MultilingualPress\Schedule\Scheduler $scheduler, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $postCollectionRepository, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $termCollectionRepository, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Repository\SchedulerInfoRepositoryInterface $schedulerInfoRepository, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\ScheduleArgs\AutoTranslationScheduleArgsFactoryInterface $autoTranslationScheduleArgsFactory, int $limit, string $hookName)
        {
        }
        public function limit(): int
        {
        }
        public function hookName(): string
        {
        }
        public function schedule(int $sourceSiteId, int $targetSiteId, string $translatorId): void
        {
        }
        public function clearSchedule(\Inpsyde\MultilingualPress\Schedule\Schedule $schedule, int $targetSiteId): void
        {
        }
    }
    class CommentTranslationScheduler implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Scheduler\TranslationSchedulerInterface
    {
        public const FILTER_DEFAULT_COLLECTION_LIMIT = 'multilingualpress.AutomaticTranslation.CommentsTranslationScheduler.CollectionLimit';
        protected \Inpsyde\MultilingualPress\Schedule\Scheduler $scheduler;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $commentCollectionRepository;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Repository\SchedulerInfoRepositoryInterface $schedulerInfoRepository;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\ScheduleArgs\AutoTranslationScheduleArgsFactoryInterface $autoTranslationScheduleArgsFactory;
        protected int $limit;
        protected string $hookName;
        public function __construct(\Inpsyde\MultilingualPress\Schedule\Scheduler $scheduler, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $commentCollectionRepository, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Repository\SchedulerInfoRepositoryInterface $schedulerInfoRepository, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\ScheduleArgs\AutoTranslationScheduleArgsFactoryInterface $autoTranslationScheduleArgsFactory, int $limit, string $hookName)
        {
        }
        public function limit(): int
        {
        }
        public function hookName(): string
        {
        }
        public function schedule(int $sourceSiteId, int $targetSiteId, string $translatorId): void
        {
        }
        public function clearSchedule(\Inpsyde\MultilingualPress\Schedule\Schedule $schedule, int $targetSiteId): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler {
    class SchedulerServices implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        public const PARAMETER_CONFIG_COMMENT_SCHEDULER_INFO_REPOSITORY = 'multilingualpress.automaticTranslation.scheduler.commentInfoRepository';
        // phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Scheduler\Ajax {
    class AjaxRemovePostFromScheduleRequestHandler implements \Inpsyde\MultilingualPress\Framework\Http\RequestHandler
    {
        public const ACTION = 'multilingualpress_remove_post_from_auto_translate_schedule';
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $postCollectionRepository;
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $postCollectionRepository)
        {
        }
        /**
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function handle(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator {
    class TranslatorServices implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        public const PARAMETER_CONFIG_TRANSLATORS_ALL_TRANSLATORS = 'multilingualpress.automaticTranslation.translator.allTranslators';
        public const PARAMETER_CONFIG_TRANSLATOR_DEEPL_REQUEST_PARAMS = 'multilingualpress.automaticTranslation.translator.deepL.requestParams';
        public const FILTER_NAME_TRANSLATOR_DEEPL_REQUEST_PARAMS = 'multilingualpress.automaticTranslation.translator.deepL.filterRequestParams';
        public const PARAMETER_CONFIG_TRANSLATOR_AWS_REGIONS = 'multilingualpress.automaticTranslation.translator.aws.regions';
        public const FILTER_NAME_AWS_TRANSLATOR_REGIONS = 'multilingualpress.automaticTranslation.translator.aws.filterRegions';
        public const PARAMETER_CONFIG_TRANSLATOR_AWS_SUPPORTED_LANGUAGES = 'multilingualpress.automaticTranslation.translator.aws.supportedLanguages';
        public const FILTER_NAME_AWS_TRANSLATOR_SUPPORTED_LANGUAGES = 'multilingualpress.automaticTranslation.translator.aws.filterSupportedLanguages';
        public const PARAMETER_CONFIG_TRANSLATOR_DEEPL_SUPPORTED_TARGET_LANGUAGES = 'multilingualpress.automaticTranslation.translator.deepL.SupportedTargetLanguages';
        public const FILTER_NAME_DEEPL_TRANSLATOR_SUPPORTED_TARGET_LANGUAGES = 'multilingualpress.automaticTranslation.translator.deepL.filterSupportedTargetLanguages';
        public const PARAMETER_CONFIG_TRANSLATOR_DEEPL_SUPPORTED_SOURCE_LANGUAGES = 'multilingualpress.automaticTranslation.translator.deepL.supportedSourceLanguages';
        public const FILTER_NAME_DEEPL_TRANSLATOR_SUPPORTED_SOURCE_LANGUAGES = 'multilingualpress.automaticTranslation.translator.deepL.filterSupportedSourceLanguages';
        public const FILTER_NAME_OPENAI_MODELS = 'multilingualpress.automaticTranslation.translator.openAi.filterModels';
        public const FILTER_NAME_OPENAI_SELECTED_MODEL = 'multilingualpress.automaticTranslation.translator.openAi.filterSelectedModel';
        public const PARAMETER_CONFIG_TRANSLATOR_OPENAI_MODELS = 'multilingualpress.automaticTranslation.translator.openAi.models';
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators {
    trait FormalityDetectorTrait
    {
        /**
         * @return 'formal'|'informal' $targetLanguageCode
         */
        private function detectLanguageFormality(string $targetLanguageCode): string
        {
        }
        /**
         * This method covers exceptional cases where default formality is not the same as
         * our fallback formality (informal).
         *
         * An example is 'de-CH', which by default is considered formal.
         * However its parent language (de) is by default considered informal.
         *
         * @return 'formal'|'informal'|null
         */
        private function languageDefaultFormality(string $targetLanguageCode): ?string
        {
        }
    }
    /**
     * Represents the translator.
     */
    interface TranslatorInterface
    {
        public const CONFIG_FORMALITY = 'formality';
        /**
         * The ID of the translator.
         *
         * @return string The ID.
         */
        public function id(): string;
        /**
         * The name of the translator.
         *
         * @return string The name.
         */
        public function name(): string;
        /**
         * Translates the given text using the given source and target language codes.
         *
         * @param string $sourceLanguageCode The source language code.
         * @param string $sourceText The text to translate.
         * @param string $targetLanguageCode The target language code.
         * @param array<string, mixed> $options translator-specific options.
         * @return string The translated text.
         * @throws RuntimeException If problem translating.
         */
        public function translate(string $sourceLanguageCode, string $sourceText, string $targetLanguageCode, array $options = []): string;
        /**
         * Checks if the source and target languages are supported.
         *
         * @param string $sourceLanguageCode The source language code.
         * @param string $targetLanguageCode The target language code.
         * @return bool true if the languages are supported, otherwise false.
         */
        public function isLanguageCombinationSupported(string $sourceLanguageCode, string $targetLanguageCode): bool;
        /**
         * Checks whether the translator supports formality for the given target language.
         *
         * @param string $targetLanguageCode
         * @return bool
         */
        public function isFormalitySupported(string $targetLanguageCode): bool;
        /**
         * Checks if the translator is active.
         *
         * @return bool true if is active, otherwise false.
         */
        public function isActive(): bool;
    }
    /**
     * @psalm-import-type Messages from ChatCompletions
     */
    class OpenAiTranslator implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\TranslatorInterface
    {
        use \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\FormalityDetectorTrait;
        public const FILTER_TRANSLATION_PROMPTS = 'multilingualpress.automatic_translation.openai.translation_prompts';
        public const FILTER_ACCEPT_INCOMPLETE_RESPONSE = 'multilingualpress.automatic_translation.openai.accept_incomplete_response';
        public const FILTER_LANGUAGE_COMBINATION_SUPPORTED = 'multilingualpress.automatic_translation.openai.language_combination_supported';
        public function __construct(string $id, string $name, bool $enabled, string $defaultFormality, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\OpenAi\Client $client)
        {
        }
        public function id(): string
        {
        }
        public function name(): string
        {
        }
        public function translate(string $sourceLanguageCode, string $sourceText, string $targetLanguageCode, array $options = []): string
        {
        }
        public function isLanguageCombinationSupported(string $sourceLanguageCode, string $targetLanguageCode): bool
        {
        }
        public function isFormalitySupported(string $targetLanguageCode): bool
        {
        }
        public function isActive(): bool
        {
        }
    }
    class DeepLTranslator implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\TranslatorInterface
    {
        protected string $id;
        protected string $name;
        protected bool $deeplEnabled;
        protected \MultilingualPress\Vendor\DeepL\Translator $deeplTranslatorService;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Languages\LanguageMapperInterface $languageMapper;
        /**
         * @var string[]
         */
        protected array $sourceSupportedLanguages;
        /**
         * @var string[]
         */
        protected array $targetSupportedLanguages;
        /**
         * @var array<string, string|bool|array<string>>
         */
        protected array $requestArgs;
        public function __construct(string $id, string $name, bool $deeplEnabled, \MultilingualPress\Vendor\DeepL\Translator $deeplTranslatorService, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Languages\LanguageMapperInterface $languageMapper, array $sourceSupportedLanguages, array $targetSupportedLanguages, array $requestArgs)
        {
        }
        public function id(): string
        {
        }
        public function name(): string
        {
        }
        public function isActive(): bool
        {
        }
        public function translate(string $sourceLanguageCode, string $sourceText, string $targetLanguageCode, array $options = []): string
        {
        }
        public function isLanguageCombinationSupported(string $sourceLanguageCode, string $targetLanguageCode): bool
        {
        }
        public function isFormalitySupported(string $targetLanguageCode): bool
        {
        }
    }
    class AWSTranslator implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\TranslatorInterface
    {
        protected string $id;
        protected string $name;
        protected bool $awsEnabled;
        protected \MultilingualPress\Vendor\Aws\AwsClientInterface $awsTranslatorService;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Languages\LanguageMapperInterface $languageMapper;
        /**
         * @var string[]
         */
        protected array $supportedLanguages;
        public function __construct(string $id, string $name, bool $awsEnabled, \MultilingualPress\Vendor\Aws\AwsClientInterface $awsTranslatorService, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Languages\LanguageMapperInterface $languageMapper, array $supportedLanguages)
        {
        }
        public function id(): string
        {
        }
        public function name(): string
        {
        }
        public function isActive(): bool
        {
        }
        public function translate(string $sourceLanguageCode, string $sourceText, string $targetLanguageCode, array $options = []): string
        {
        }
        public function isLanguageCombinationSupported(string $sourceLanguageCode, string $targetLanguageCode): bool
        {
        }
        public function isFormalitySupported(string $targetLanguageCode): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\OpenAi {
    /**
     * @psalm-type Response = array{
     *      object: string,
     *      data: array<
     *          int,
     *          array{
     *              id: string,
     *              object: string,
     *              created: int,
     *              owned_by: string
     *          }
     *      >
     * }
     */
    class Models
    {
        public const TRANSIENT_KEY = 'multilingualpress.auto_translation.openai_models';
        public const DEFAULT_MODELS = ['gpt-4o', 'gpt-4o-mini', 'gpt-o1', 'gpt-o1-mini', 'gpt-4'];
        public const DEFAULT_MODEL = self::DEFAULT_MODELS[0];
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\OpenAi\Api $api, \MultilingualPress\Vendor\Psr\Log\LoggerInterface $logger)
        {
        }
        public function list(): array
        {
        }
    }
    class Config
    {
        public function __construct(string $apiKey, string $baseUrl)
        {
        }
        public function apiKey(): string
        {
        }
        public function baseUrl(): string
        {
        }
    }
    class Api
    {
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\OpenAi\Config $config)
        {
        }
        public function get(string $path, array $body = [], array $headers = []): array
        {
        }
        public function post(string $path, array $body = [], array $headers = []): array
        {
        }
        /**
         * @param WP_Error|array $response
         * @throws RuntimeException
         */
        public function prepareResponse($response): array
        {
        }
    }
    /**
     * Chat completion OpenAI API client.
     * @psalm-type ChatCompletionResponse = array{
     *      id: string,
     *      choices: array<
     *          int,
     *          array{
     *              index: int,
     *              message: array{role: string, content: string},
     *              finish_reason: string
     *          }
     *      >
     * }
     * @psalm-type Messages = array<
     *      int,
     *      array{role: string, content: string}
     * >
     */
    class ChatCompletions
    {
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\OpenAi\Api $api, string $model, \MultilingualPress\Vendor\Psr\Log\LoggerInterface $logger)
        {
        }
        /**
         * @param Messages $messages
         * @return ?ChatCompletionResponse
         */
        public function create(array $messages, array $extra = []): ?array
        {
        }
    }
    class CredentialsVerifier
    {
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\OpenAi\Api $api, \MultilingualPress\Vendor\Psr\Log\LoggerInterface $logger)
        {
        }
        public function verify(): bool
        {
        }
    }
    class Client
    {
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\OpenAi\Api $api, string $model, ?\MultilingualPress\Vendor\Psr\Log\LoggerInterface $logger = null)
        {
        }
        public function completions(): \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\OpenAi\ChatCompletions
        {
        }
        public function models(): \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\OpenAi\Models
        {
        }
        public function credentialsVerifier(): \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\OpenAi\CredentialsVerifier
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator {
    class TranslatorActivator implements \Inpsyde\MultilingualPress\Framework\Module\Activator
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        // phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
        public function activate(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Languages {
    /**
     * Something able to map the languages.
     */
    interface LanguageMapperInterface
    {
        /**
         * Maps the MLP language code to given external supported languages.
         *
         * @param string $languageCode The language code to map.
         * @param string[] $supportedLanguages The list of supported languages.
         * @return string The mapped language code.
         * @throws RuntimeException If an error occurs during mapping.
         */
        public function map(string $languageCode, array $supportedLanguages): string;
    }
    class LanguageMapper implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Languages\LanguageMapperInterface
    {
        public function map(string $languageCode, array $supportedLanguages): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\EntityTranslator {
    class CommentTranslator
    {
        public const FILTER_TRANSLATED_DATA = 'multilingualpress.automatic_translation.comment_translated_data';
        /**
         * @param array<string, mixed> $translatorOptions
         * @throws RuntimeException
         */
        public function translate(int $sourceSiteId, int $targetSiteId, \WP_Comment $sourceComment, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\TranslatorInterface $translator, array $translatorOptions = []): void
        {
        }
    }
    class PostTranslator
    {
        public const FILTER_TRANSLATED_DATA = 'multilingualpress.automatic_translation.post_translated_data';
        /**
         * @param array<string, mixed> $translatorOptions
         * @param string[] $fields
         * @throws RuntimeException
         */
        public function translate(int $sourceSiteId, int $targetSiteId, int $sourcePostId, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\TranslatorInterface $translator, array $translatorOptions = [], array $fields = ['post_title', 'post_content', 'post_excerpt']): void
        {
        }
    }
    class TermTranslator
    {
        public const FILTER_TRANSLATED_DATA = 'multilingualpress.automatic_translation.term_translated_data';
        /**
         * @param string[] $fields
         * @param array<string, mixed> $translatorOptions
         * @throws RuntimeException
         */
        public function translate(int $sourceSiteId, int $targetSiteId, \WP_Term $sourceTerm, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\TranslatorInterface $translator, array $translatorOptions = [], array $fields = ['name', 'slug', 'description']): void
        {
        }
    }
    class EntityTranslatorServices implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslationRequestHandlers {
    trait TranslationDataTrait
    {
        /**
         * Get the metabox translation data from request for given site.
         *
         * @param Request $request
         * @param int $siteId The site ID.
         * @return array<string, mixed> The map of metabox translation data from request
         */
        private function translationFromRequest(\Inpsyde\MultilingualPress\Framework\Http\Request $request, int $siteId): array
        {
        }
        /**
         * Get the selected translator.
         *
         * @param TranslatorManagerInterface $translatorManager
         * @param string $selectedTranslator The selected translator name form request.
         * @return TranslatorInterface|null The translator.
         */
        protected function selectedTranslator(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslatorManager\TranslatorManagerInterface $translatorManager, string $selectedTranslator): ?\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\TranslatorInterface
        {
        }
    }
    trait TranslatorOptionsTrait
    {
        /**
         * @return array<string, mixed>
         */
        private function translatorOptions(array $translationData): array
        {
        }
    }
    class PostTranslationRequestHandler
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        use \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslationRequestHandlers\TranslationDataTrait;
        use \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslationRequestHandlers\TranslatorOptionsTrait;
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslatorManager\TranslatorManagerInterface $translatorManager, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\EntityTranslator\PostTranslator $postTranslator, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\EntityTranslator\TermTranslator $termTranslator)
        {
        }
        public function handle(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext, \Inpsyde\MultilingualPress\Framework\Http\Request $request): void
        {
        }
    }
    class CommentTranslationRequestHandler
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        use \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslationRequestHandlers\TranslationDataTrait;
        use \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslationRequestHandlers\TranslatorOptionsTrait;
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslatorManager\TranslatorManagerInterface $translatorManager, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\EntityTranslator\CommentTranslator $commentTranslator)
        {
        }
        public function handle(\Inpsyde\MultilingualPress\Module\Comments\RelationshipContext\CommentsRelationshipContext $relationshipContext, \Inpsyde\MultilingualPress\Framework\Http\Request $request): void
        {
        }
    }
    class TermTranslationRequestHandler
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        use \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslationRequestHandlers\TranslationDataTrait;
        use \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslationRequestHandlers\TranslatorOptionsTrait;
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslatorManager\TranslatorManagerInterface $translatorManager, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\EntityTranslator\TermTranslator $termTranslator)
        {
        }
        public function handle(\Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $relationshipContext, \Inpsyde\MultilingualPress\Framework\Http\Request $request): void
        {
        }
    }
    class SettingsCommentTranslationRequestHandler
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        use \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslationRequestHandlers\TranslationDataTrait;
        protected \Inpsyde\MultilingualPress\Framework\Setting\SettingsRepositoryInterface $automaticTranslationSettingsRepository;
        protected \Inpsyde\MultilingualPress\Module\Comments\SiteSettings\CommentsSettingsRepositoryInterface $commentsSettingsRepository;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslatorManager\TranslatorManagerInterface $translatorManager;
        protected \Inpsyde\MultilingualPress\Framework\Http\Request $request;
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslatorManager\TranslatorManagerInterface $translatorManager, \Inpsyde\MultilingualPress\Framework\Setting\SettingsRepositoryInterface $automaticTranslationSettingsRepository, \Inpsyde\MultilingualPress\Module\Comments\SiteSettings\CommentsSettingsRepositoryInterface $commentsSettingsRepository, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        public function handle(string $commentContent, int $sourceCommentPostId, int $sourceSiteId, int $remoteSiteId, string $action): string
        {
        }
        /**
         * Determines whether a new comment on a post should be automatically translated to the given remote site.
         *
         * It checks the site settings to see if automatic translation of new comments is enabled
         * for the given post type.
         *
         * @param int $sourceCommentPostId
         * @param int $sourceSiteId
         * @param int $remoteSiteId
         *
         * @return bool True if the comment should be automatically translated, false otherwise.
         */
        protected function shouldAutoTranslateComment(int $sourceCommentPostId, int $sourceSiteId, int $remoteSiteId, string $action): bool
        {
        }
        /**
         * Retrieves comment settings based on the given action, post type, and source site ID.
         *
         * If the action is `OPTION_TRANSLATE_NEW_COMMENT`, it fetches the setting from the repository.
         * Otherwise, it retrieves the settings from the request body and returns them as an array of integers.
         *
         * @param string $action The translation action (e.g. OPTION_TRANSLATE_NEW_COMMENT or OPTION_TRANSLATE_COMMENTS).
         * @param string $postType
         * @param int $sourceSiteId
         *
         * @return array<int> A list of site IDs to which the action applies.
         */
        protected function commentSettingsByAction(string $action, string $postType, int $sourceSiteId): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslatorManager {
    /**
     * Something able to manage translators.
     */
    interface TranslatorManagerInterface
    {
        /**
         * Retrieves the list of active translators.
         *
         * @return TranslatorInterface[] The list of active translators.
         */
        public function activeTranslators(): array;
        /**
         * Retrieves the translator by given ID.
         *
         * @param string $id The id of translator.
         * @return null | TranslatorInterface
         */
        public function translatorById(string $id): ?\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\TranslatorInterface;
        /**
         * Checks if several translators are activated.
         *
         * @return bool true if several translators are activated, otherwise false.
         */
        public function areSeveralTranslatorsActive(): bool;
        /**
         * Checks if the given languages are supported by any of the active translators.
         *
         * @param string $sourceLanguageCode The source language code.
         * @param string $targetLanguageCode The target language code.
         * @return bool true if supported, otherwise false.
         */
        public function areLanguagesSupportedByAnyActiveTranslator(string $sourceLanguageCode, string $targetLanguageCode): bool;
    }
    class TranslatorManager implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslatorManager\TranslatorManagerInterface
    {
        /**
         * @var TranslatorInterface[]
         */
        protected array $allTranslators;
        public function __construct(array $allTranslators)
        {
        }
        public function activeTranslators(): array
        {
        }
        public function translatorById(string $id): ?\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Translators\TranslatorInterface
        {
        }
        public function areSeveralTranslatorsActive(): bool
        {
        }
        public function areLanguagesSupportedByAnyActiveTranslator(string $sourceLanguageCode, string $targetLanguageCode): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Factories {
    /**
     * Represents a factory to create translator service instances.
     */
    interface TranslatorServiceFactoryInterface
    {
        /**
         * Creates AWS translator service instance.
         *
         * @param CredentialsInterface $credentials The AWS credentials.
         * @param string $region The AWS service region.
         * @return AWSTranslatorService The AWS translator Service instance.
         * @throws RuntimeException If problem creating.
         */
        public function createAWSTranslatorService(\MultilingualPress\Vendor\Aws\Credentials\CredentialsInterface $credentials, string $region): \MultilingualPress\Vendor\Aws\Translate\TranslateClient;
        /**
         * Creates DeepL translator service instance.
         *
         * @param string $deeplKey The DeepL API key.
         * @return DeepLTranslatorService The DeepL translator Service instance.
         * @throws RuntimeException If problem creating.
         */
        public function createDeepLTranslatorService(string $deeplKey): \MultilingualPress\Vendor\DeepL\Translator;
    }
    class TranslatorServiceFactory implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Factories\TranslatorServiceFactoryInterface
    {
        public function createAWSTranslatorService(\MultilingualPress\Vendor\Aws\Credentials\CredentialsInterface $credentials, string $region): \MultilingualPress\Vendor\Aws\Translate\TranslateClient
        {
        }
        public function createDeepLTranslatorService(string $deeplKey): \MultilingualPress\Vendor\DeepL\Translator
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Validators {
    /**
     * Can validate the credentials
     *
     * @psalm-type Credentials = array{key: string, secret?: string}
     */
    interface CredentialsValidatorInterface
    {
        /**
         * Validates the given credentials.
         *
         * @param array<string, string> $credentials The map of credentials(key/secret) to their values.
         * @psalm-param Credentials $credentials
         * @return bool true if given credentials are valid, otherwise false.
         * @throws RuntimeException If problem validating.
         */
        public function validate(array $credentials): bool;
    }
    class AWSCredentialsValidator implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Validators\CredentialsValidatorInterface
    {
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Factories\TranslatorServiceFactoryInterface $translatorServiceFactory;
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Factories\TranslatorServiceFactoryInterface $translatorServiceFactory)
        {
        }
        public function validate(array $credentials): bool
        {
        }
    }
    class DeepLCredentialsValidator implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Validators\CredentialsValidatorInterface
    {
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Factories\TranslatorServiceFactoryInterface $translatorServiceFactory;
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Factories\TranslatorServiceFactoryInterface $translatorServiceFactory)
        {
        }
        public function validate(array $credentials): bool
        {
        }
    }
    class OpenAiCredentialsValidator implements \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\Validators\CredentialsValidatorInterface
    {
        public function __construct(string $apiBaseUrl, \MultilingualPress\Vendor\Psr\Log\LoggerInterface $logger)
        {
        }
        public function validate(array $credentials): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands {
    class AutoTranslateCommentsCommand
    {
        protected int $sourceSiteId;
        protected int $targetSiteId;
        /**
         * @var int[]
         */
        protected array $commentIds;
        protected string $translatorId;
        /**
         * @param int[] $commentIds
         */
        public function __construct(int $sourceSiteId, int $targetSiteId, array $commentIds, string $translatorId)
        {
        }
        /**
         * The source site ID.
         *
         * @return int
         */
        public function sourceSiteId(): int
        {
        }
        /**
         * The target site ID.
         *
         * @return int
         */
        public function targetSiteId(): int
        {
        }
        /**
         * The list of comment IDs.
         *
         * @return int[] The list of comment IDs.
         */
        public function commentIds(): array
        {
        }
        /**
         * The translator ID.
         *
         * @return string
         */
        public function translatorId(): string
        {
        }
    }
    class CommandsServices implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        // phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
    class AutoTranslateTermCommand
    {
        public function __construct(int $sourceSiteId, int $targetSiteId, int $sourceTermId, string $translatorId)
        {
        }
        public function sourceSiteId(): int
        {
        }
        public function targetSiteId(): int
        {
        }
        public function sourceTermId(): int
        {
        }
        public function translatorId(): string
        {
        }
    }
    class AutoTranslateCommentCommand
    {
        public function __construct(int $sourceSiteId, int $targetSiteId, int $sourceCommentId, string $translatorId)
        {
        }
        public function sourceSiteId(): int
        {
        }
        public function targetSiteId(): int
        {
        }
        public function sourceCommentId(): int
        {
        }
        public function translatorId(): string
        {
        }
    }
    class AutoTranslatePostCommand
    {
        protected int $sourceSiteId;
        protected int $targetSiteId;
        protected int $sourcePostId;
        protected string $translatorId;
        public function __construct(int $sourceSiteId, int $targetSiteId, int $sourcePostId, string $translatorId)
        {
        }
        /**
         * The source site ID.
         *
         * @return int
         */
        public function sourceSiteId(): int
        {
        }
        /**
         * The target site ID.
         *
         * @return int
         */
        public function targetSiteId(): int
        {
        }
        /**
         * The source post ID.
         *
         * @return int
         */
        public function sourcePostId(): int
        {
        }
        /**
         * The translator ID.
         *
         * @return string
         */
        public function translatorId(): string
        {
        }
    }
    class AutoTranslatePostsCommand
    {
        protected int $sourceSiteId;
        protected int $targetSiteId;
        /**
         * @var int[]
         */
        protected array $postIds;
        protected string $translatorId;
        /**
         * @param int[] $postIds
         */
        public function __construct(int $sourceSiteId, int $targetSiteId, array $postIds, string $translatorId)
        {
        }
        /**
         * The source site ID.
         *
         * @return int
         */
        public function sourceSiteId(): int
        {
        }
        /**
         * The target site ID.
         *
         * @return int
         */
        public function targetSiteId(): int
        {
        }
        /**
         * The list of post IDs.
         *
         * @return int[] The list of post IDs
         */
        public function postIds(): array
        {
        }
        /**
         * The translator ID.
         *
         * @return string
         */
        public function translatorId(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\Handlers {
    class AutoTranslatePostCommandHandler
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslatorManager\TranslatorManagerInterface $translatorManager;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\EntityTranslator\PostTranslator $postTranslator;
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslatorManager\TranslatorManagerInterface $translatorManager, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\EntityTranslator\PostTranslator $postTranslator)
        {
        }
        /**
         * Handles the command.
         *
         * @param AutoTranslatePostCommand $command The auto translation command.
         * @return void
         * @throws NonexistentTable
         */
        public function handle(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\AutoTranslatePostCommand $command): void
        {
        }
    }
    class AutoTranslateCommentCommandHandler
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslatorManager\TranslatorManagerInterface $translatorManager, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\EntityTranslator\CommentTranslator $commentTranslator)
        {
        }
        /**
         * Handles the command.
         *
         * @param AutoTranslateCommentCommand $command The auto translation command.
         * @return void
         * @throws RuntimeException When failing to handle the comment translation request.
         */
        public function handle(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\AutoTranslateCommentCommand $command): void
        {
        }
    }
    class AutoTranslateTermCommandHandler
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\TranslatorManager\TranslatorManagerInterface $translatorManager, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Translator\EntityTranslator\TermTranslator $termTranslator)
        {
        }
        /**
         * Handles the command.
         *
         * @param AutoTranslateTermCommand $command The auto translation command.
         * @return void
         * @throws NonexistentTable
         */
        public function handle(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\AutoTranslateTermCommand $command): void
        {
        }
    }
    class AutoTranslatePostsCommandHandler
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $postCollectionRepository;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\Handlers\AutoTranslatePostCommandHandler $autoTranslatePostCommandHandler;
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $postCollectionRepository, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\Handlers\AutoTranslatePostCommandHandler $autoTranslatePostCommandHandler)
        {
        }
        /**
         * Handles the command.
         *
         * @param AutoTranslatePostsCommand $command The auto translation command.
         * @return void
         */
        public function handle(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\AutoTranslatePostsCommand $command): void
        {
        }
    }
    class AutoTranslateTermsCommandHandler
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $termCollectionRepository, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\Handlers\AutoTranslateTermCommandHandler $autoTranslateTermCommandHandler)
        {
        }
        /**
         * Handles the command.
         *
         * @param AutoTranslateTermsCommand $command The auto translation command.
         * @return void
         */
        public function handle(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\AutoTranslateTermsCommand $command): void
        {
        }
    }
    class AutoTranslateCommentsCommandHandler
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $commentCollectionRepository;
        protected \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\Handlers\AutoTranslateCommentCommandHandler $autoTranslateCommentCommandHandler;
        public function __construct(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Collection\Repository\EntityCollectionRepositoryInterface $commentCollectionRepository, \Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\Handlers\AutoTranslateCommentCommandHandler $autoTranslateCommentCommandHandler)
        {
        }
        /**
         * Handles the command.
         *
         * @param AutoTranslateCommentsCommand $command The auto translation command.
         * @return void
         */
        public function handle(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands\AutoTranslateCommentsCommand $command): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Commands {
    class AutoTranslateTermsCommand
    {
        /**
         * @param int[] $termIds
         */
        public function __construct(int $sourceSiteId, int $targetSiteId, array $termIds, string $translatorId)
        {
        }
        public function sourceSiteId(): int
        {
        }
        public function targetSiteId(): int
        {
        }
        /**
         * @return int[] The list of term IDs
         */
        public function termIds(): array
        {
        }
        public function translatorId(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation {
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        public const MODULE_ID = 'automatic-translation';
        public const PARAMETER_CONFIG_MODULE_DIR_PATH = 'multilingualpress.AutomaticTranslation.ModuleDirPath';
        public const PARAMETER_CONFIG_MODULE_SERVICES = 'multilingualpress.automaticTranslation.moduleServices';
        public const PARAMETER_CONFIG_MODULE_ACTIVATORS = 'multilingualpress.automaticTranslation.moduleActivators';
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        /**
         * @inheritdoc
         * @throws LateAccessToNotSharedService | NameNotFound
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritDoc
         * @throws LateAccessToNotSharedService | NameNotFound
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\AutomaticTranslation\Assets {
    class AssetsServices implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        public const MODULE_SCRIPTS_HANDLER_NAME = 'multilingualpress-' . \Inpsyde\MultilingualPress\Module\AutomaticTranslation\ServiceProvider::MODULE_ID;
        public const CONFIGURATION_NAME_FOR_URL_TO_MODULE_ASSETS = 'multilingualpress.automaticTranslation.urlToModuleAssets';
        public const PARAMETER_CONFIG_SHOULD_ENQUEUE_MODULE_ASSETS = 'multilingualpress.automaticTranslation.shouldEnqueueModuleAssets';
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
    class AssetsActivator implements \Inpsyde\MultilingualPress\Framework\Module\Activator
    {
        public function activate(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Creates the options for the JS schedule status renderer.
         *
         * @param ScheduleStatusRendererInterface $scheduleStatusRenderer
         * @return array{shouldRender: bool, markup: string}
         */
        protected function removePostFromScheduleRendererOptions(\Inpsyde\MultilingualPress\Module\AutomaticTranslation\TranslationUi\ScheduleInfoRenderer\ScheduleStatusRendererInterface $scheduleStatusRenderer): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\Trasher {
    /**
     * Trasher setting updater.
     */
    class TrasherSettingUpdater
    {
        /**
         * @param TrasherSettingRepository $settingRepository
         * @param ContentRelations $contentRelations
         * @param Request $request
         * @param Nonce $nonce
         * @param ActivePostTypes $activePostTypes
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\Trasher\TrasherSettingRepository $settingRepository, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $activePostTypes)
        {
        }
        /**
         * Updates the trasher setting of the post with the given ID as well as all related posts.
         *
         * @param int $postId
         * @param \WP_Post $post
         * @return int
         */
        public function update(int $postId, \WP_Post $post): int
        {
        }
        /**
         * @param \WP_Post $post
         * @param \WP_REST_Request $request
         * @return int
         */
        public function updateFromRestApi(\WP_Post $post, \WP_REST_Request $request): int
        {
        }
    }
    final class TrasherSettingRepository
    {
        public const META_KEY = '_trash_the_other_posts';
        /**
         * Returns the trasher setting value for the post with the given ID, or the current post.
         *
         * @param int $postId
         * @return bool
         */
        public function settingForPost(int $postId = 0): bool
        {
        }
        /**
         * Updates the trasher setting value for the post with the given ID.
         *
         * @param int $postId
         * @param bool $value
         * @return bool
         */
        public function updateSetting(int $postId, bool $value): bool
        {
        }
    }
    /**
     * Trasher setting view.
     */
    class TrasherSettingView
    {
        /**
         * @param TrasherSettingRepository $settingRepository
         * @param Nonce $nonce
         * @param ActivePostTypes $activePostTypes
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\Trasher\TrasherSettingRepository $settingRepository, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $activePostTypes)
        {
        }
        /**
         * Renders the setting markup.
         *
         * @param \WP_Post $post
         */
        public function render(\WP_Post $post): void
        {
        }
    }
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'trasher';
        public const MODULE_ASSETS_FACTORY_SERVICE_NAME = 'trasher_asset_factory';
        public const CONFIGURATION_NAME_FOR_URL_TO_MODULE_ASSETS = 'multilingualpress.trasher.urlToModuleAssets';
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
    class Trasher
    {
        /**
         * @param TrasherSettingRepository $settingRepository
         * @param ContentRelations $contentRelations
         * @param ActivePostTypes $activePostTypes
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\Trasher\TrasherSettingRepository $settingRepository, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $activePostTypes)
        {
        }
        /**
         * Trashes all related posts.
         *
         * @param int $postId
         * @return int
         */
        public function trashRelatedPosts(int $postId): int
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\SiteFlags\Flag {
    class Factory
    {
        public function __construct(\Inpsyde\MultilingualPress\Module\SiteFlags\Core\Admin\SiteSettingsRepository $settingsRepository, \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\FlagRepository $flagRepository, string $flagsDirectoryUrl)
        {
        }
        /**
         * Return Raster or Svg flag based on site settings.
         *
         * @param int $siteId
         * @return Flag
         * @throws NonexistentTable
         */
        public function create(int $siteId): \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Flag
        {
        }
        public function createEmpty(): \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Flag
        {
        }
        public function createFromUrl(string $url, \Inpsyde\MultilingualPress\Framework\Language\Language $language): \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Flag
        {
        }
        /**
         * Tries to generate a flag based on the provided locale's country code. If a flag is not available,
         * falls back to the locale's territory code. Finally, falls back to an empty flag.
         *
         * @param string $locale
         * @return Flag
         */
        public function createFromLocale(string $locale): \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Flag
        {
        }
    }
    /**
     * Interface Flag
     */
    interface Flag
    {
        /**
         * @return string
         */
        public function url(): string;
        /**
         * @return string
         */
        public function markup(): string;
    }
    final class CustomFlagIcon implements \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Flag
    {
        public function __construct(string $flagIconsDirectoryUrl, \Inpsyde\MultilingualPress\Framework\Language\Language $language, string $url)
        {
        }
        /**
         * @inheritdoc
         */
        public function url(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function markup(): string
        {
        }
    }
    final class FlagRepository
    {
        public function __construct(string $flagsDirectoryPath)
        {
        }
        /**
         * Given a flag type and a locale, returns the appropriate country or territory code.
         *
         * @param string $flagTypeSetting
         * @param string $locale
         * @return string
         */
        public function countryOrTerritoryCode(string $flagTypeSetting, string $locale): string
        {
        }
        public function flagIconExists(string $countryOrTerritoryCode): bool
        {
        }
    }
    final class EmptyFlag implements \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Flag
    {
        public function url(): string
        {
        }
        public function markup(): string
        {
        }
    }
    /**
     * Class Svg
     *
     * @todo Convert to SVG markup
     */
    final class Svg implements \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Flag
    {
        /**
         * Svg constructor
         * @param Language $language
         * @param string $iconsDirectoryUrl
         */
        public function __construct(string $countryOrTerritoryCode, string $iconsDirectoryUrl)
        {
        }
        /**
         * @inheritdoc
         */
        public function url(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function markup(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\SiteFlags\Core\Admin {
    /**
     * MultilingualPress "Site Custom Flag Url" site setting
     */
    final class SiteFlagUrlSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @param SiteSettingsRepository $repository
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\SiteFlags\Core\Admin\SiteSettingsRepository $repository)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
    /**
     * Class SiteSettingsUpdater
     */
    final class SiteSettingsUpdater implements \Inpsyde\MultilingualPress\Framework\Setting\SiteSettingsUpdatable
    {
        /**
         * @param SiteSettingsRepository $repository
         * @param Request $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\SiteFlags\Core\Admin\SiteSettingsRepository $repository, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * @inheritdoc
         */
        public function defineInitialSettings(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function updateSettings(int $siteId): void
        {
        }
    }
    final class SiteFlagSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @var string
         */
        public const OPTION_FLAG_MLP_LANG_TERRITORY = 'flag-mlp-lang-territory-code';
        /**
         * @var string
         */
        public const OPTION_FLAG_MLP_LANG = 'flag-mlp-lang-code';
        /**
         * @var string
         */
        public const OPTION_FLAG_CUSTOM = 'flag-custom-image';
        /**
         * @var string
         */
        public const OPTION_FLAG_NONE = 'flag-none';
        public function __construct(\Inpsyde\MultilingualPress\Module\SiteFlags\Core\Admin\SiteSettingsRepository $settingsRepository, \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\FlagRepository $flagsRepository)
        {
        }
        public function render(int $siteId): void
        {
        }
        public function title(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core\Admin {
    /**
     * Trait SiteSettingsRepositoryTrait
     */
    trait SiteSettingsRepositoryTrait
    {
        /**
         * Return an array where keys are site IDs and values are the setting value for the given key.
         *
         * @param string $settingKey
         * @return array
         */
        public function allSitesSetting(string $settingKey): array
        {
        }
        /**
         * Returns the complete settings data.
         *
         * @return array
         */
        public function allSettings(): array
        {
        }
        /**
         * Sets the given settings data.
         *
         * @param array $settings
         * @return bool
         */
        public function updateSettings(array $settings): bool
        {
        }
        /**
         * Updates the given setting for the site with the given ID, or the current site.
         *
         * @param string $key
         * @param mixed $value
         * @param int|null $siteId
         * @return bool
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        private function updateSetting(string $key, $value, int $siteId = null): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\SiteFlags\Core\Admin {
    /**
     * Class SiteSettingsRepository
     */
    class SiteSettingsRepository
    {
        use \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepositoryTrait;
        /**
         * @var string
         */
        public const KEY_SITE_FLAG_URL = 'flag_url';
        /**
         * @var string
         */
        public const KEY_SITE_FLAG_TYPE = 'flag_type';
        /**
         * @var string
         */
        public const KEY_SITE_MENU_LANGUAGE_STYLE = 'menu_flag_style';
        /**
         * @param int|null $siteId
         * @return string
         */
        public function siteFlagType(int $siteId = null): string
        {
        }
        /**
         * @param string $type
         * @param int|null $siteId
         * @return bool
         */
        public function updateSiteFlagType(string $type, int $siteId = null): bool
        {
        }
        /**
         * @param int|null $siteId
         * @return string
         */
        public function siteFlagUrl(int $siteId = null): string
        {
        }
        /**
         * @param string $url
         * @param int|null $siteId
         * @return bool
         */
        public function updateSiteFlagUrl(string $url, int $siteId = null): bool
        {
        }
        /**
         * @param int|null $siteId
         * @return string
         * @deprecated
         */
        public function siteMenuLanguageStyle(int $siteId = null): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\SiteFlags\Core\NavMenu {
    final class SiteMenuLanguageStyleSetting
    {
        public const FLAG_AND_LANGUAGES = 'flag_and_text';
        public const ONLY_FLAGS = 'only_flag';
        public const ONLY_LANGUAGES = 'only_language';
        public function __construct(\Inpsyde\MultilingualPress\Module\SiteFlags\Core\NavMenu\NavMenuSettingsRepository $repository)
        {
        }
        /**
         * @wp-hook wp_nav_menu_item_custom_fields
         */
        public function register(string $itemId, \WP_Post $menuItem): void
        {
        }
        /**
         * @wp-hook wp_update_nav_menu_item
         */
        public function store(int $menuId, int $menuItemId): void
        {
        }
    }
    class NavMenuMetaCopier
    {
        public function __construct(\Inpsyde\MultilingualPress\Module\SiteFlags\Core\NavMenu\NavMenuSettingsRepository $repository)
        {
        }
        /**
         * @wp-hook multilingualpress.copy_nav_menu.item_copied
         */
        public function copyNavMenuStyleMeta(int $menuItemId, int $remoteMenuItemId, int $remoteSiteId): void
        {
        }
    }
    final class NavMenuSettingsRepository
    {
        public function __construct(\Inpsyde\MultilingualPress\Module\SiteFlags\Core\Admin\SiteSettingsRepository $siteSettingsRepository)
        {
        }
        /**
         * @param int $menuItemId
         * @return string
         */
        public function menuLanguageStyle(int $menuItemId): string
        {
        }
        /**
         * @param string $style
         * @param int $menuItemId
         * @return void
         */
        public function updateMenuLanguageStyle(string $style, int $menuItemId): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\SiteFlags {
    /**
     * Class FlagFilter
     */
    class FlagFilter
    {
        /**
         * NavMenuLanguageStyleFilter constructor
         * @param NavMenuSettingsRepository $settingsRepository
         * @param Factory $flagFactory
         */
        public function __construct(\Inpsyde\MultilingualPress\Module\SiteFlags\Core\NavMenu\NavMenuSettingsRepository $settingsRepository, \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Factory $flagFactory)
        {
        }
        /**
         * Show the flags on nav menu items based on site settings
         *
         * @param string $title
         * @param \WP_Post $item
         * @return string
         */
        public function navMenuItems(string $title, \WP_Post $item): string
        {
        }
        /**
         * Filter the Language Switcher item flag url
         *
         * @param string $flagUrl The Language Switcher item flag Url
         * @param int $siteId The Language Switcher item site id
         * @return string The filtered Language Switcher item flag Url
         */
        public function languageSwitcherItemFlagUrl(string $flagUrl, int $siteId): string
        {
        }
        /**
         * Show flags in the table list columns for translated content
         *
         * @param string $languageTag
         * @param int $siteId
         * @return string
         */
        public function tableListPostsRelations(string $languageTag, int $siteId): string
        {
        }
    }
    /**
     * @psalm-type SiteFlagProperties = array{
     *     pluginPath: string,
     *     pluginUrl: string,
     *     assetsPath: string,
     *     assetsUrl: string
     * }
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'multilingualpress-site-flags';
        protected const OLD_FLAGS_ADDON_PATH = 'multilingualpress-site-flags/multilingualpress-site-flags.php';
        public const CONFIGURATION_NAME_FOR_URL_TO_MODULE_ASSETS = 'multilingualpress.siteFlags.urlToModuleAssets';
        /**
         * Registers the module at the module manager.
         *
         * @param ModuleManager $moduleManager
         * @return bool
         * @throws ModuleAlreadyRegistered
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        /**
         * Registers the provided services on the given container.
         *
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        public function bootstrapAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        public function bootstrapFrontend(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        public function bootstrapNetworkAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        protected function description(): string
        {
        }
        protected function isSiteFlagsAddonActive(): bool
        {
        }
    }
    final class Assets
    {
        public function __construct(string $assetsDirectoryUrl)
        {
        }
        public function load(\MultilingualPress\Vendor\Inpsyde\Assets\AssetManager $assetManager): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Module\BeaverBuilder {
    /**
     * Class ServiceProvider
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider
    {
        public const MODULE_ID = 'beaverbuilder';
        public const CONFIGURATION_NAME_FOR_UNSUPPORTED_POST_TYPES = 'beaverbuilder.UnsupportedPostTypes';
        public const CONFIGURATION_NAME_FOR_FILTERS_NEEDED_TO_REMOVE_ENTITIES_SUPPORT = 'beaverbuilder.FiltersNeededToRemoveEntitiesSupport';
        /**
         * @inheritdoc
         *
         * @param ModuleManager $moduleManager
         * @return bool
         * @throws ModuleAlreadyRegistered
         */
        public function registerModule(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager): bool
        {
        }
        /**
         * @inheritdoc
         *
         * @param Container $container
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService
         * @throws NameNotFound
         * @throws Throwable
         */
        public function activateModule(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * When "Copy source content" option is checked the method will copy the additional postmeta data
         * Which is needed for Beaver Builder to work correctly.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService
         * @throws NameNotFound
         * @throws Throwable
         */
        protected function handleCopyContentEditions(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Copy Meta data from source post to remote post
         *
         * @param array $data Metadata to be copied
         * @param RelationshipContext $context
         */
        protected function copyMetaData(array $data, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): void
        {
        }
        /**
         * @return bool
         */
        protected function isBeaverBuilderActive(): bool
        {
        }
        /**
         * Removes the support of beaver entities for translation metaboxes.
         *
         * @param Container $container
         * @throws LateAccessToNotSharedService | NameNotFound
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function filterSupportForEntities(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\SiteDuplication\Settings {
    /**
     * Site duplication "Based on site" setting.
     */
    final class BasedOnSiteSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @param wpdb $db
         * @param Nonce $nonce
         */
        public function __construct(\wpdb $db, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
    /**
     * Class CopyAttachmentsSetting
     * @package Inpsyde\MultilingualPress\SiteDuplication
     */
    final class CopyAttachmentsSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @inheritDoc
         */
        public function render(int $siteId): void
        {
        }
        /**
         * @inheritDoc
         */
        public function title(): string
        {
        }
    }
    /**
     * Site duplication "Plugins" setting.
     */
    final class CopyUsersSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @inheritdoc
         */
        public function render(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
    /**
     * Site duplication "Connect Content" setting.
     */
    final class ConnectContentSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @inheritdoc
         */
        public function render(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
    /**
     * Site duplication "Search Engine Visibility" setting.
     */
    final class SearchEngineVisibilitySetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        public const FILTER_SEARCH_ENGINE_VISIBILITY = 'multilingualpress.search_engine_visibility';
        /**
         * @inheritdoc
         */
        public function render(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
    class ConnectCommentsSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        protected string $inputId;
        public function __construct(string $inputId)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
    /**
     * Site duplication "Plugins" setting.
     */
    final class ActivatePluginsSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @inheritdoc
         */
        public function render(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\SiteDuplication\Schedule {
    /**
     * Class AttachmentDuplicatorScheduler
     * @package Inpsyde\MultilingualPress\SiteDuplication
     */
    class AttachmentDuplicatorScheduler
    {
        public const FILTER_DEFAULT_COLLECTION_LIMIT = 'multilingualpress.attachment_duplicator_default_limit';
        public const DEFAULT_COLLECTION_LIMIT = 100;
        public const SCHEDULE_HOOK = 'multilingualpress.site_attachments_duplicator';
        /**
         * AttachmentDuplicatorScheduler constructor.
         * @param SiteScheduleOption $option
         * @param Attachment\Collection $attachmentsCollection
         * @param Scheduler $scheduler
         */
        public function __construct(\Inpsyde\MultilingualPress\SiteDuplication\Schedule\SiteScheduleOption $option, \Inpsyde\MultilingualPress\Attachment\Collection $attachmentsCollection, \Inpsyde\MultilingualPress\Schedule\Scheduler $scheduler)
        {
        }
        /**
         * Schedule a new set of cron jobs to copy source site attachments into the new given site.
         *
         * @param int $sourceSiteId
         * @param int $newSiteId
         * @throws UnexpectedValueException
         */
        public function schedule(int $sourceSiteId, int $newSiteId): void
        {
        }
    }
    /**
     * Class SiteScheduleOption
     *
     * @package Inpsyde\MultilingualPress\SiteDuplication
     */
    class SiteScheduleOption
    {
        use \Inpsyde\MultilingualPress\Framework\SiteIdValidatorTrait;
        public const OPTION_SCHEDULE_IDS = 'multilingualpress.schedule_option_ids';
        /**
         * Create new schedule id for the given site
         *
         * @param int $siteId
         * @param string $scheduleId
         * @return bool
         * @throws UnexpectedValueException
         */
        public function createForSite(int $siteId, string $scheduleId): bool
        {
        }
        /**
         * Retrieve the schedule id for the given site
         *
         * @param int $siteId
         * @return string
         * @throws UnexpectedValueException
         */
        public function readForSite(int $siteId): string
        {
        }
        /**
         * Delete the schedule id for the given site
         *
         * @param int $siteId
         * @return bool
         * @throws UnexpectedValueException
         */
        public function deleteForSite(int $siteId): bool
        {
        }
        /**
         * Retrieve all schedule
         *
         * @return array
         */
        public function allSchedule(): array
        {
        }
    }
    /**
     * Class MaybeScheduleAttachmentDuplication
     * @package Inpsyde\MultilingualPress\SiteDuplication\Schedule
     */
    class MaybeScheduleAttachmentDuplication
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        use \Inpsyde\MultilingualPress\Framework\SiteIdValidatorTrait;
        public function __construct(\Inpsyde\MultilingualPress\SiteDuplication\Schedule\AttachmentDuplicatorScheduler $attachmentDuplicatorScheduler)
        {
        }
        /**
         * Schedule the attachment duplication if requested
         *
         * @param int $sourceSiteId
         * @param int $newSiteId
         * @param SiteDuplicationContext $context
         * @throws Throwable
         */
        public function maybeScheduleAttachmentsDuplication(int $sourceSiteId, int $newSiteId, \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): void
        {
        }
        /**
         * @param int $sourceSiteId
         * @param int $newSiteId
         * @throws Throwable
         */
        protected function scheduleAttachmentDuplication(int $sourceSiteId, int $newSiteId): void
        {
        }
    }
    class ScheduleAssetManager
    {
        public const NAME_ATTACHMENT_SCHEDULE_ID = 'scheduleId';
        public const NAME_SITE_ID = 'siteId';
        public const SCRIPTS_HANDLER_NAME = 'multilingualpress-site-duplication-admin';
        protected \Inpsyde\MultilingualPress\SiteDuplication\Schedule\SiteScheduleOption $siteScheduleOption;
        protected \Inpsyde\MultilingualPress\Schedule\AjaxScheduleHandler $ajaxScheduleHandler;
        protected \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $scheduleActionsNonce;
        protected string $urlToAssetsFolder;
        public function __construct(\Inpsyde\MultilingualPress\SiteDuplication\Schedule\SiteScheduleOption $siteScheduleOption, \Inpsyde\MultilingualPress\Schedule\AjaxScheduleHandler $ajaxScheduleHandler, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $scheduleActionsNonce, string $urlToAssetsFolder)
        {
        }
        /**
         * Enqueues the scripts for scheduling the assets duplication.
         *
         * @return void
         */
        public function enqueueScript(): void
        {
        }
        /**
         * Retrieve the ajax schedule information url to call to obtain information about the current
         * status of the cron jobs
         *
         * @return string
         */
        protected function scheduleUrl(): string
        {
        }
        /**
         * @return array
         */
        protected function attachmentDuplicatorTranslations(): array
        {
        }
        /**
         * @return array
         */
        protected function attachmentDuplicatorActions(): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Schedule\Action {
    /**
     * Interface ActionTask
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    interface ActionTask
    {
        /**
         * @return void
         * @throws RuntimeException if execution fails
         */
        public function execute(): void;
    }
}
namespace Inpsyde\MultilingualPress\SiteDuplication\Schedule {
    /**
     * Class RemoveAttachmentIdsTask
     * @package Inpsyde\MultilingualPress\SiteDuplication\Schedule\Action
     */
    class RemoveAttachmentIdsTask implements \Inpsyde\MultilingualPress\Schedule\Action\ActionTask
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        use \Inpsyde\MultilingualPress\Framework\SiteIdValidatorTrait;
        /**
         * AttachmentsScheduleIdsRemoveAction constructor.
         * @param Request $request
         * @param SiteScheduleOption $siteScheduleOption
         * @param string $siteIdNameInRequest
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\SiteDuplication\Schedule\SiteScheduleOption $siteScheduleOption, string $siteIdNameInRequest)
        {
        }
        /**
         * @inheritDoc
         * @throws Throwable
         */
        public function execute(): void
        {
        }
        /**
         * @return int
         * @throws UnexpectedValueException
         */
        protected function siteIdByRequest(): int
        {
        }
    }
    class AttachmentDuplicatorHandler
    {
        use \Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        public const ACTION_BEFORE_SCHEDULED_ATTACHMENT_DUPLICATION = 'multilingualpress.before_scheduled_attachment_duplication';
        /**
         * AttachmentDuplicatorHandler constructor.
         * @param SiteScheduleOption $option
         * @param Attachment\Duplicator $attachmentDuplicator
         * @param Attachment\Collection $attachmentCollection
         * @param Scheduler $scheduler
         * @param Attachment\DatabaseDataReplacer $dataBaseDataReplacer
         */
        public function __construct(\Inpsyde\MultilingualPress\SiteDuplication\Schedule\SiteScheduleOption $option, \Inpsyde\MultilingualPress\Attachment\Duplicator $attachmentDuplicator, \Inpsyde\MultilingualPress\Attachment\Collection $attachmentCollection, \Inpsyde\MultilingualPress\Schedule\Scheduler $scheduler, \Inpsyde\MultilingualPress\Attachment\DatabaseDataReplacer $dataBaseDataReplacer)
        {
        }
        /**
         * Handle the cron job request by copy an entire directory of attachments.
         * The $step identify the current directory to copy within the uploads directory.
         *
         * @wp-hook AttachmentDuplicatorScheduler::SCHEDULE_HOOK
         *
         * @param \stdClass $scheduleArgs
         *
         * @return bool
         * @throws Throwable
         */
        public function handle(\stdClass $scheduleArgs): bool
        {
        }
    }
    /**
     * Class ScheduleActionsNames
     * @package Inpsyde\MultilingualPress\SiteDuplication\Schedule\Action
     */
    class ScheduleActionsNames
    {
        public const STOP_ATTACHMENTS_COPY = 'stop_attachments_copy';
    }
    /**
     * Class NewSiteScheduleTemplate
     * @package Inpsyde\MultilingualPress\SiteDuplication
     */
    class NewSiteScheduleTemplate
    {
        /**
         * Render the template for the attachment schedule cron jobs
         * Used in the context of a new site to show information about the current status of the
         * attachment copy to the target site.
         *
         * @wp-hook admin_footer
         *
         * @return void
         */
        public function render(): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps {
    /**
     * @template TKey as array-key
     * @template TValue as SiteDuplicationContext
     * @template-extends ArrayObject<TKey, TValue>
     */
    class DuplicationStepsCollection extends \ArrayObject
    {
    }
    interface DuplicationStepHandler
    {
        /**
         * Handles a site duplication step.
         *
         * @param SiteDuplicationContext $context
         * @return SiteDuplicationContext
         * @throws Throwable
         */
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext;
        public function isApplicable(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): bool;
    }
    /**
     * Certain steps (e.g. copying users) are applicable only
     * when actually duplicating a site (using "based on").
     */
    trait StepApplicableWhenDuplicatingSite
    {
        /**
         * @param SiteDuplicationContext $context
         * @return bool
         */
        public function isApplicable(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): bool
        {
        }
    }
    class FetchAdminEmail implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        use \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\StepApplicableWhenDuplicatingSite;
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
    }
    class DuplicateTables implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        use \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\StepApplicableWhenDuplicatingSite;
        protected \Inpsyde\MultilingualPress\SiteDuplication\Exclusions\ExclusionFactory $exclusionFactory;
        public function __construct(\Inpsyde\MultilingualPress\SiteDuplication\Exclusions\ExclusionFactory $exclusionFactory, \Inpsyde\MultilingualPress\Framework\Database\TableDuplicator $tableDuplicator, \Inpsyde\MultilingualPress\Framework\Database\TableReplacer $tableReplacer, \wpdb $wpdb)
        {
        }
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
    }
    class CopyUsers implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        use \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\StepApplicableWhenDuplicatingSite;
        /**
         * If the appropriate option is selected, the users will be copied to the new site.
         *
         * @param SiteDuplicationContext $context
         * @return SiteDuplicationContext
         */
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
    }
    class ValidateNonce implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
        public function isApplicable(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): bool
        {
        }
    }
    class SetupSiteRelations implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations)
        {
        }
        /**
         * Sets up content relations between the source site and the new site.
         *
         * @param SiteDuplicationContext $context
         * @return SiteDuplicationContext
         * @throws NonexistentTable
         */
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
        public function isApplicable(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): bool
        {
        }
    }
    class RenameUserRoleOptions implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        use \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\StepApplicableWhenDuplicatingSite;
        public function __construct(\wpdb $wpdb)
        {
        }
        /**
         * Renames the user roles option according to the given table prefix.
         *
         * @param SiteDuplicationContext $context
         * @return SiteDuplicationContext
         */
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
    }
    class UpdateAdminEmail implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        use \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\StepApplicableWhenDuplicatingSite;
        public function __construct(\wpdb $wpdb)
        {
        }
        /**
         * Sets the admin email option to its initial value, which has been overwritten
         * by the table duplication at this point.
         *
         * @param SiteDuplicationContext $context
         * @return SiteDuplicationContext
         */
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
    }
    class UpdateHomeAndSiteUrls implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        use \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\StepApplicableWhenDuplicatingSite;
        /**
         * Sets the home and site URL options to their initial values, which have been overwritten
         * by the table duplication at this point.
         *
         * @param SiteDuplicationContext $context
         * @return SiteDuplicationContext
         */
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
    }
    class RemoveOptions implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        use \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\StepApplicableWhenDuplicatingSite;
        /**
         * Remove options not meant to be present in the duplicated site.
         *
         * @param SiteDuplicationContext $context
         * @return SiteDuplicationContext
         */
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
    }
    class ActivatePlugins implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        use \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\StepApplicableWhenDuplicatingSite;
        public function __construct(\Inpsyde\MultilingualPress\SiteDuplication\ActivePlugins $activePlugins)
        {
        }
        /**
         * Adapts all active plugins according to the setting included in the request.
         *
         * @param SiteDuplicationContext $context
         * @return SiteDuplicationContext
         */
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
    }
    class SwitchTheme implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        use \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\StepApplicableWhenDuplicatingSite;
        /**
         * Triggers potential setup routines of the used theme.
         *
         * @param SiteDuplicationContext $context
         * @return SiteDuplicationContext
         */
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
    }
    class MapDomain implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        use \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\StepApplicableWhenDuplicatingSite;
        public function __construct(\wpdb $wpdb)
        {
        }
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
    }
    class SwitchToNewSite implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
        public function isApplicable(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): bool
        {
        }
    }
    class CollectTables implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        use \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\StepApplicableWhenDuplicatingSite;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Database\TableList $tableList)
        {
        }
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
    }
    class SetupContentRelations implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        use \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\StepApplicableWhenDuplicatingSite;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager)
        {
        }
        /**
         * Sets up content relations between the source site and the new site.
         *
         * @param SiteDuplicationContext $context
         * @return SiteDuplicationContext
         * @throws NonexistentTable
         */
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
    }
    class FetchSiteUrl implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        use \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\StepApplicableWhenDuplicatingSite;
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
    }
    class SwitchToSourceSite implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        use \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\StepApplicableWhenDuplicatingSite;
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
    }
    class ValidateSiteIds implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
        public function isApplicable(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): bool
        {
        }
    }
    class CreateNetworkState implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
        public function isApplicable(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): bool
        {
        }
    }
    class FireCompletionAction implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        use \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\StepApplicableWhenDuplicatingSite;
        /**
         * Fires an action after successful site duplication.
         *
         * @param SiteDuplicationContext $context
         * @return SiteDuplicationContext
         */
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
    }
    class UpdateBlogName implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        /**
         * Update the site title
         *
         * @param SiteDuplicationContext $context
         * @return SiteDuplicationContext
         */
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
        public function isApplicable(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): bool
        {
        }
    }
    class RestoreNetworkState implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
        public function isApplicable(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): bool
        {
        }
    }
    class UpdateSiteLanguage implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository, \wpdb $wpdb)
        {
        }
        /**
         * Updates the site language.
         *
         * @param SiteDuplicationContext $context
         * @return SiteDuplicationContext
         */
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
        public function isApplicable(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): bool
        {
        }
    }
    class FetchTablePrefix implements \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepHandler
    {
        use \Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\StepApplicableWhenDuplicatingSite;
        public function __construct(\wpdb $wpdb)
        {
        }
        public function handle(\Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext $context): \Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicationContext
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\SiteDuplication {
    /**
     * Handles duplication of a site.
     */
    class SiteDuplicator
    {
        public const NAME_ACTIVATE_PLUGINS = 'mlp_activate_plugins';
        public const NAME_BASED_ON_SITE = 'mlp_based_on_site';
        public const NAME_SITE_LANGUAGE = 'mlp_site_language';
        public const NAME_SITE_RELATIONS = 'mlp_site_relations';
        public const NAME_CONNECT_CONTENT = 'mlp_connect_content';
        public const NAME_COPY_ATTACHMENTS = 'mlp_copy_attachments';
        public const NAME_COPY_USERS = 'mlp_copy_users';
        public const NAME_CONNECT_COMMENTS = 'mlp_connect_comments';
        public const DUPLICATE_ACTION_KEY = 'multilingualpress.duplicated_site';
        public const FILTER_SITE_TABLES = 'multilingualpress.duplicate_site_tables';
        public const FILTER_EXCLUDED_TABLES = 'multilingualpress.filter_excluded_tables';
        public function __construct(\Inpsyde\MultilingualPress\SiteDuplication\DuplicationSteps\DuplicationStepsCollection $duplicationStepsCollection, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\WpCli\CliInput $cliInput, array $optionsToRemove)
        {
        }
        /**
         * Duplicates a complete site to the new site just created.
         *
         * @param int $newSiteId
         * @return bool
         */
        //phpcs:ignore Inpsyde.CodeQuality.NestingLevel.High
        public function duplicateSite(int $newSiteId): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\SiteDuplication\Exclusions {
    /**
     * Interface for defining exclusion conditions during the site duplication.
     */
    interface ExclusionInterface
    {
        /**
         * Returns the exclusion condition for the specific table and exclusion criteria.
         *
         * This method should generate and return a valid SQL `WHERE` clause that
         * can be used to exclude records based on the exclusion criteria defined
         * in the implementing class.
         *
         * @return string The SQL `WHERE` clause that defines the exclusion condition.
         */
        public function exclude(): string;
    }
    class PostTypeExclusion implements \Inpsyde\MultilingualPress\SiteDuplication\Exclusions\ExclusionInterface
    {
        public function __construct(\wpdb $wpdb, string $sourceTableName, string $newTableName, array $postTypes)
        {
        }
        /**
         * Returns the exclusion condition for posts and postmeta.
         */
        public function exclude(): string
        {
        }
    }
    class ExclusionFactory
    {
        public function __construct(\wpdb $wpdb)
        {
        }
        public function createPostTypeExclusion(string $sourceTableName, string $newTableName, array $postTypes): \Inpsyde\MultilingualPress\SiteDuplication\Exclusions\ExclusionInterface
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\SiteDuplication {
    /**
     * Handles (de)activation of all active plugins.
     */
    class ActivePlugins
    {
        /**
         * Fires the plugin activation hooks for all active plugins.
         *
         * @return int
         */
        public function activate(): int
        {
        }
        /**
         * Deactivates all plugins.
         *
         * @return bool
         */
        public function deactivate(): bool
        {
        }
    }
    class SiteDuplicationNotice
    {
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * Extend the "Site added" notice.
         *
         * @wp-hook load-site-new.php
         * @return void
         */
        public function filter(): void
        {
        }
    }
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        public const SITE_DUPLICATION_SUCCESS_ACTIONS_MESSAGES = 'siteDuplication.successActionsMessages';
        public const SCHEDULE_ACTION_ATTACHMENTS_REMOVER_SERVICE = 'siteDuplication.scheduleActionAttachmentsRemover';
        public const SCHEDULE_ACTION_ATTACHMENT_HANDLER_SERVICE = 'siteDuplication.scheduleActionAttachmentHandler';
        public const SITE_DUPLICATION_ACTIONS = 'siteDuplication.actionsService';
        public const FILTER_SUCCESS_ACTIONS_MESSAGES = 'multilingualpress.filter_success_actions_messages';
        public const FILTER_SITE_DUPLICATION_ACTIONS = 'multilingualpress.site_duplication_actions';
        public const SCHEDULE_ACTION_ATTACHMENTS_AJAX_HOOK_NAME = 'multilingualpress_site_duplicator_attachments_schedule_action';
        protected const SCHEDULE_ACTION_ATTACHMENTS_USER_REQUIRED_CAPABILITY = 'create_sites';
        protected const SCHEDULE_ACTION_ATTACHMENTS_NONCE_KEY = 'multilingualpress_attachment_duplicator_action';
        public const SITE_DUPLICATION_FILTER_MLP_TABLES = 'siteDuplication.filterMlpTables';
        public const CONFIGURATION_SITE_DUPLICATION_EXCLUDED_OPTIONS = 'siteDuplication.excludedOptions';
        public const SITE_DUPLICATION_FILTER_EXCLUDED_OPTIONS = 'multilingualpress.site_duplicator.filter_excluded_options';
        public const FILTER_SETTING_VIEW_MODELS = 'siteDuplication.filterSettingViewModels';
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @param Container $container
         * @throws Throwable
         */
        protected function duplicateSiteBackCompactBootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @param Container $container
         * @throws LateAccessToNotSharedService
         * @throws NameNotFound
         * @throws Throwable
         */
        protected function defineInitialSettingsBackCompactBootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
    //phpcs:ignore Inpsyde.CodeQuality.PropertyPerClassLimit.TooManyProperties
    class SiteDuplicationContext
    {
        public static function empty(): self
        {
        }
        // phpcs:ignore Inpsyde.CodeQuality.FunctionLength.TooLong
        public static function fromHttpRequest(\Inpsyde\MultilingualPress\Framework\Http\Request $request): self
        {
        }
        // phpcs:ignore Inpsyde.CodeQuality.FunctionLength.TooLong
        public static function fromCliRequest(\Inpsyde\MultilingualPress\WpCli\CliInput $cliInput): self
        {
        }
        public function newSiteId(): int
        {
        }
        public function withNewSiteId(int $newSiteId): self
        {
        }
        public function sourceSiteId(): int
        {
        }
        public function withSourceSiteId(int $sourceSiteId): self
        {
        }
        public function tablePrefix(): string
        {
        }
        public function withTablePrefix(string $tablePrefix): self
        {
        }
        public function mappedDomain(): string
        {
        }
        public function withMappedDomain(string $mappedDomain): self
        {
        }
        public function siteUrl(): string
        {
        }
        public function withSiteUrl(string $siteUrl): self
        {
        }
        public function adminEmail(): string
        {
        }
        public function withAdminEmail(string $adminEmail): self
        {
        }
        public function tables(): array
        {
        }
        public function withTables(array $tables): self
        {
        }
        public function optionsToRemove(): array
        {
        }
        public function withOptionsToRemove(array $optionsToRemove): self
        {
        }
        public function networkState(): ?\Inpsyde\MultilingualPress\Framework\NetworkState
        {
        }
        public function withNetworkState(\Inpsyde\MultilingualPress\Framework\NetworkState $networkState): self
        {
        }
        public function siteTitle(): string
        {
        }
        public function withSiteTitle(string $title): self
        {
        }
        public function siteLanguage(): string
        {
        }
        public function withSiteLanguage(string $language): self
        {
        }
        public function siteRelations(): array
        {
        }
        public function withSiteRelations(array $siteRelations): self
        {
        }
        public function isCopyAttachmentsEnabled(): bool
        {
        }
        public function withCopyAttachmentsEnabled(bool $isCopyAttachmentsEnabled): self
        {
        }
        public function isConnectContentEnabled(): bool
        {
        }
        public function withConnectContentEnabled(bool $isConnectContentEnabled): self
        {
        }
        public function isConnectCommentsEnabled(): bool
        {
        }
        public function withConnectCommentsEnabled(bool $isConnectCommentsEnabled): self
        {
        }
        public function isActivatePluginsEnabled(): bool
        {
        }
        public function withActivatePluginsEnabled(bool $isActivatePluginsEnabled): self
        {
        }
        public function isCopyUsersEnabled(): bool
        {
        }
        public function withCopyUsersEnabled(bool $isCopyUsersEnabled): self
        {
        }
        public function isCli(): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Database\Table {
    /**
     * Trait TableTrait
     *
     * @see Table
     */
    trait TableTrait
    {
        /**
         * @inheritdoc
         */
        public function exists(): bool
        {
        }
    }
    /**
     * Languages table.
     */
    final class ExternalSitesTable implements \Inpsyde\MultilingualPress\Framework\Database\Table
    {
        use \Inpsyde\MultilingualPress\Database\Table\TableTrait;
        public const COLUMN_SITE_URL = 'site_url';
        public const COLUMN_SITE_LANGUAGE_NAME = 'site_language_name';
        public const COLUMN_SITE_LANGUAGE_LOCALE = 'site_language_locale';
        public const COLUMN_REDIRECT = 'site_redirect';
        public const COLUMN_ENABLE_HREFLANG = 'enable_hreflang';
        public const COLUMN_DISPLAY_STYLE = 'display_style';
        public const COLUMN_ID = 'ID';
        /**
         * @param string $prefix
         */
        public function __construct(string $prefix = '')
        {
        }
        /**
         * @inheritdoc
         */
        public function columnsWithoutDefaultContent(): array
        {
        }
        /**
         * @inheritdoc
         */
        public function defaultContentSql(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function keysSql(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function name(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function primaryKey(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function schema(): array
        {
        }
    }
    /**
     * Content relations table.
     */
    final class ContentRelationsTable implements \Inpsyde\MultilingualPress\Framework\Database\Table
    {
        use \Inpsyde\MultilingualPress\Database\Table\TableTrait;
        public const COLUMN_CONTENT_ID = 'content_id';
        public const COLUMN_RELATIONSHIP_ID = 'relationship_id';
        public const COLUMN_SITE_ID = 'site_id';
        /**
         * @param string $tablePrefix
         */
        public function __construct(string $tablePrefix = '')
        {
        }
        /**
         * @inheritdoc
         */
        public function columnsWithoutDefaultContent(): array
        {
        }
        /**
         * @inheritdoc
         */
        public function defaultContentSql(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function keysSql(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function name(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function primaryKey(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function schema(): array
        {
        }
    }
    /**
     * Site relations table.
     */
    final class SiteRelationsTable implements \Inpsyde\MultilingualPress\Framework\Database\Table
    {
        use \Inpsyde\MultilingualPress\Database\Table\TableTrait;
        public const COLUMN_ID = 'ID';
        public const COLUMN_SITE_1 = 'site_1';
        public const COLUMN_SITE_2 = 'site_2';
        /**
         * @param string $prefix
         */
        public function __construct(string $prefix = '')
        {
        }
        /**
         * @inheritdoc
         */
        public function columnsWithoutDefaultContent(): array
        {
        }
        /**
         * @inheritdoc
         */
        public function defaultContentSql(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function keysSql(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function name(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function primaryKey(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function schema(): array
        {
        }
    }
    /**
     * Relationships table.
     */
    final class RelationshipsTable implements \Inpsyde\MultilingualPress\Framework\Database\Table
    {
        use \Inpsyde\MultilingualPress\Database\Table\TableTrait;
        public const COLUMN_ID = 'id';
        public const COLUMN_TYPE = 'type';
        /**
         * @param string $tablePrefix
         */
        public function __construct(string $tablePrefix = '')
        {
        }
        /**
         * @inheritdoc
         */
        public function columnsWithoutDefaultContent(): array
        {
        }
        /**
         * @inheritdoc
         */
        public function defaultContentSql(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function keysSql(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function name(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function primaryKey(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function schema(): array
        {
        }
    }
    class RelationshipMetaTable implements \Inpsyde\MultilingualPress\Framework\Database\Table
    {
        use \Inpsyde\MultilingualPress\Database\Table\TableTrait;
        public const COLUMN_RELATIONSHIP_ID = 'relationship_id';
        public const COLUMN_META_KEY = 'meta_key';
        public const COLUMN_META_VALUE = 'meta_value';
        public function __construct(string $tablePrefix = '')
        {
        }
        /**
         * @inheritdoc
         */
        public function columnsWithoutDefaultContent(): array
        {
        }
        /**
         * @inheritdoc
         */
        public function defaultContentSql(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function keysSql(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function name(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function primaryKey(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function schema(): array
        {
        }
    }
    /**
     * Languages table.
     */
    final class LanguagesTable implements \Inpsyde\MultilingualPress\Framework\Database\Table
    {
        use \Inpsyde\MultilingualPress\Database\Table\TableTrait;
        public const COLUMN_CUSTOM_NAME = 'custom_name';
        public const COLUMN_ENGLISH_NAME = 'english_name';
        public const COLUMN_BCP_47_TAG = 'http_code';
        public const COLUMN_ID = 'ID';
        public const COLUMN_ISO_639_1_CODE = 'iso_639_1';
        public const COLUMN_ISO_639_2_CODE = 'iso_639_2';
        public const COLUMN_ISO_639_3_CODE = 'iso_639_3';
        public const COLUMN_LOCALE = 'locale';
        public const COLUMN_NATIVE_NAME = 'native_name';
        public const COLUMN_RTL = 'is_rtl';
        /**
         * @param string $prefix
         */
        public function __construct(string $prefix = '')
        {
        }
        /**
         * @inheritdoc
         */
        public function columnsWithoutDefaultContent(): array
        {
        }
        /**
         * @inheritdoc
         */
        public function defaultContentSql(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function keysSql(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function name(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function primaryKey(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function schema(): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Database {
    /**
     * Service provider for all database objects.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider, \Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider
    {
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function integrate(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Schedule {
    /**
     * Used to simplify AJAX interaction with scheduler.
     *
     * It provides two methods to get URLs of:
     * - an AJAX endpoint to generate a schedule for given hook, steps and args
     * - an AJAX endpoint to get information about a schedule of given ID.
     * Both URLS are nonced.
     *
     * The class also ships the handler for the two AJAX actions, registered by the service provider to
     * respond to them.
     */
    class AjaxScheduleHandler
    {
        public const ACTION_SCHEDULE = 'multilingualpress_ajax_cron_schedule';
        public const ACTION_INFO = 'multilingualpress_ajax_cron_schedule_info';
        public const FILTER_AJAX_SCHEDULE_DELAY = 'multilingualpress.ajax_schedule_delay';
        protected const MODE_PUBLIC = 'public';
        protected const MODE_RESTRICTED = 'restricted';
        protected const SCHEDULE_ID = 'schedule-id';
        protected const SCHEDULE_STEPS = 'schedule-steps';
        protected const SCHEDULE_HOOK = 'schedule-hook';
        protected const SCHEDULE_ARGS = 'schedule-args';
        /**
         * @param Scheduler $scheduler
         * @param NonceFactory $factory
         */
        public function __construct(\Inpsyde\MultilingualPress\Schedule\Scheduler $scheduler, \Inpsyde\MultilingualPress\Framework\Factory\NonceFactory $factory)
        {
        }
        /**
         * Generate an AJAX URL whose handler will generate a new schedule.
         *
         * Steps, hook, and args, necessary to build the schedule can be passed here to be already
         * included in the URL, or can be ignored here and passed later with the request.
         *
         * @param int $steps
         * @param string $hook
         * @param array|null $args
         * @param string $mode
         * @return string
         */
        public function scheduleAjaxUrl(int $steps = null, string $hook = null, array $args = null, string $mode = self::MODE_RESTRICTED): string
        {
        }
        /**
         * Generate an AJAX URL whose handler will provide information for a schedule.
         *
         * Schedule ID, necessary to retrieve the schedule can be passed here to be already
         * included in the URL, or can be ignored here and passed later with the request.
         *
         * @param string $scheduleId
         * @param string $mode
         * @return string
         */
        public function scheduleInfoAjaxUrl(string $scheduleId = null, string $mode = self::MODE_RESTRICTED): string
        {
        }
        /**
         * Handler for both AJAX actions managed by this class.
         *
         * Checks action and nonce, then dispatch the request to the specific handling method.
         *
         * @param ServerRequest $request
         * @param Context|null $context
         * @return void
         */
        public function handle(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request, \Inpsyde\MultilingualPress\Framework\Nonce\Context $context = null): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Schedule\Action {
    /**
     * Class ActionException
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    class ActionException extends \Exception
    {
        /**
         * @param Schedule $schedule
         * @return ActionException
         */
        public static function becauseScheduleCannotBeDeleted(\Inpsyde\MultilingualPress\Schedule\Schedule $schedule): \Inpsyde\MultilingualPress\Schedule\Action\ActionException
        {
        }
        /**
         * @param string $scheduleId
         * @return ActionException
         */
        public static function forInvalidScheduleId(string $scheduleId): \Inpsyde\MultilingualPress\Schedule\Action\ActionException
        {
        }
        /**
         * @param string $hook
         * @param Schedule $schedule
         * @return ActionException
         */
        public static function becauseUnscheduleHookFailForSchedule(string $hook, \Inpsyde\MultilingualPress\Schedule\Schedule $schedule): \Inpsyde\MultilingualPress\Schedule\Action\ActionException
        {
        }
    }
    /**
     * Class RemoveActionTask
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    class RemoveActionTask implements \Inpsyde\MultilingualPress\Schedule\Action\ActionTask
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        /**
         * ScheduleRemoveAction constructor.
         * @param Request $request
         * @param Scheduler $scheduler
         * @param string $scheduleIdName
         * @param string $scheduleHook
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Schedule\Scheduler $scheduler, string $scheduleIdName, string $scheduleHook)
        {
        }
        /**
         * @inheritDoc
         * @throws Throwable
         */
        public function execute(): void
        {
        }
        /**
         * Execute Tasks
         * @throws ActionException
         */
        protected function executeTasks(): void
        {
        }
        /**
         * Delete Schedule
         *
         * Remove the schedule Id from the list of the schedule id in the database
         *
         * @throws ActionException
         */
        protected function deleteSchedule(): void
        {
        }
        /**
         * Retrieve the Schedule Id from the request
         *
         * @return Schedule
         * @throws ActionException
         */
        protected function schedule(): \Inpsyde\MultilingualPress\Schedule\Schedule
        {
        }
        /**
         * Retrieve the schedule Id from the current Request
         *
         * @return string
         */
        protected function scheduleIdFromRequest(): string
        {
        }
        /**
         * Clean Scheduled Events
         *
         * Un-schedule all of the events for the copy attachment
         *
         * @return void
         * @throws ActionException
         */
        protected function cleanScheduledEvents(): void
        {
        }
    }
    /**
     * Trait AuthorizationTrait
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    trait AuthorizationTrait
    {
        private \Inpsyde\MultilingualPress\Framework\Nonce\Context $context;
        private \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce;
        private string $userCapability;
        /**
         * @return bool
         */
        protected function isUserAuthorized(): bool
        {
        }
    }
    /**
     * Trait ResponseTrait
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    trait ResponseTrait
    {
        private array $successMessages;
        /**
         * @param string $actionName
         * @param array $errors
         */
        protected function sendResponseFor(string $actionName, array $errors): void
        {
        }
        /**
         * @param string $actionName
         * @return string
         */
        protected function successMessage(string $actionName): string
        {
        }
        /**
         * @param array $errors
         * @return string
         */
        protected function errorMessage(array $errors): string
        {
        }
        /**
         * @param array $messages
         * @return string
         */
        protected function reduceMessages(array $messages): string
        {
        }
        /**
         * @return void
         * @psalm-return never
         * @uses die
         */
        protected function die(): void
        {
        }
    }
    /**
     * Trait ActionsProcessorTrait
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    trait ActionsProcessorTrait
    {
        use \Inpsyde\MultilingualPress\Schedule\Action\ResponseTrait;
        private array $tasks = [];
        private \Inpsyde\MultilingualPress\Framework\Message\MessageFactoryInterface $messageFactory;
        private string $actionNameKey;
        /**
         * @param ServerRequest $request
         * @throws \Exception
         */
        protected function process(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request): void
        {
        }
        /**
         * @param ServerRequest $request
         * @return string
         */
        protected function actionNameFrom(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request): string
        {
        }
    }
    /**
     * Class ScheduleActionRequestHandler
     * @package Inpsyde\MultilingualPress\Schedule\Action
     */
    class ScheduleActionRequestHandler implements \Inpsyde\MultilingualPress\Framework\Http\RequestHandler
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        use \Inpsyde\MultilingualPress\Schedule\Action\AuthorizationTrait;
        use \Inpsyde\MultilingualPress\Schedule\Action\ActionsProcessorTrait;
        public const ACTION_AFTER_ACTION_DISPATCHED = 'multilingualpress.after_schedule_action_dispatched';
        public const ACTION_NO_SCHEDULE_ACTION_DISPATCHED = 'multilingualpress.no_schedule_action_dispatched';
        /**
         * ActionsRequestHandler constructor.
         * @param Nonce $nonce
         * @param array $tasks
         * @param MessageFactoryInterface $messageFactory
         * @param Context $context
         * @param array $successMessages
         * @param string $actionName
         * @param string $userCapability
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, array $tasks, \Inpsyde\MultilingualPress\Framework\Message\MessageFactoryInterface $messageFactory, \Inpsyde\MultilingualPress\Framework\Nonce\Context $context, array $successMessages, string $actionName, string $userCapability)
        {
        }
        /**
         * @inheritDoc
         * @param ServerRequest $request
         */
        public function handle(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Schedule {
    /**
     * Make easier the schedule (via cron) of asynchronous tasks.
     *
     * For example:
     *
     * ```
     * $scheduler = new Scheduler();
     *
     * $ids = someFunctionThatCalculatesPostIdsToProcess();
     *
     * if ($ids && !get_option('my-schedule-is-running')) {
     *
     *     $scheduleId = $scheduler->newSchedule(count($ids), 'my-schedule-hook', $ids);
     *
     *     update_option('my-schedule-is-running', $scheduleId);
     * }
     *
     * add_action('my-schedule-hook', function ($scheduleArgs) use ($scheduler) {
     *
     *     list(
     *         $schedule,  // A Schedule object
     *         $step,      // The (zero-based) index for the step running
     *         $args       // The array of args for the schedule
     *     ) = $scheduler->parseScheduleHookParam($scheduleArgs);
     *
     *     if (!$schedule
     *         || get_option('my-schedule-is-running') !== $schedule->id()
     *         || $schedule->isDone()
     *         || empty($args[$step])
     *     ) {
     *         return;
     *     }
     *
     *     // Something like "X minutes", "or X hours", or "1 minute", properly translated.
     *     // For the first step it will be "Unknown" (translated).
     *     echo 'Estimated remaining time: ' . $schedule->estimatedRemainingTime();
     *
     *     $postToProcess = get_post($args[$step]);
     *
     *     // ... Process post here...
     *
     *     $scheduler->stepDone($schedule);  // This is **REQUIRED**
     *
     *     // In case what we just completed was the last step...
     *     if ($schedule->isDone()) {
     *         delete_option('my-schedule-is-running');
     *
     *         // We're doing this manually, but even if we don't, it'll be done via cron in 24 hours
     *         $scheduler->cleanup($schedule);
     *     }
     * }
     * ```
     *
     * So the hook is fired once per each step, passing the current step index as hook argument.
     *
     * The schedule object (among others) has a method to inform about its status, and a method to
     * inform about the estimated remaining time.
     *
     * Every time `$scheduler->stepDone` is called, the schedule is updated increasing the count of done
     * steps and a "last update" property is updated so that estimated remaining time can be calculated.
     * Plus the schedule can be marked as done when all steps are completed.
     *
     * Note that every time `$scheduler->newSchedule()` is called a *new* schedule is created, this is
     * why in the example the schedule id is stored in an option: to avoid to schedule more processes.
     *
     * @package Inpsyde\MultilingualPress\Schedule
     */
    class Scheduler
    {
        public const OPTION = 'multilingualpress_cron_schedules';
        public const ACTION_CLEANUP = 'multilingualpress.done-schedule-cleanup';
        public const ACTION_SCHEDULED = 'multilingualpress.cron-scheduled';
        /**
         * Creates a new multi-step schedule.
         *
         * @param int $stepsCount
         * @param string $hook
         * @param array $args
         * @param Delay\Delay|null $delay
         * @return string
         */
        public function newSchedule(int $stepsCount, string $hook, array $args = [], \Inpsyde\MultilingualPress\Schedule\Delay\Delay $delay = null): string
        {
        }
        /**
         * Tells scheduler that a step for given schedule just completed.
         *
         * @param Schedule $schedule
         * @return Schedule
         */
        public function stepDone(\Inpsyde\MultilingualPress\Schedule\Schedule $schedule): \Inpsyde\MultilingualPress\Schedule\Schedule
        {
        }
        /**
         * Cleanups schedule of given ID.
         *
         * Use responsibly, will break running schedules.
         * `Scheduler::cleanupIfDone` can be used to only cleanup a schedule if completed.
         *
         * @param Schedule $schedule
         * @return bool
         */
        public function cleanup(\Inpsyde\MultilingualPress\Schedule\Schedule $schedule): bool
        {
        }
        /**
         * Cleanups schedule of given ID only if done.
         *
         * This is run via cron 24h after the schedule is marked as complete.
         *
         * @param string $scheduleId
         * @return bool
         */
        public function cleanupIfDone(string $scheduleId): bool
        {
        }
        /**
         * @param \stdClass $param
         * @return array
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function parseScheduleHookParam($param): array
        {
        }
        /**
         * Loads a Schedule object form its ID.
         *
         * @param string $scheduleId
         * @return Schedule|null
         */
        public function scheduleById(string $scheduleId)
        {
        }
    }
    /**
     * Class Schedule
     * @package Inpsyde\MultilingualPress\Schedule
     */
    class Schedule
    {
        protected const STARTED = 'started';
        protected const RUNNING = 'running';
        protected const DONE = 'done';
        public const TIMEZONE = 'UTC';
        /**
         * Create a new multi-step schedule.
         *
         * @param int $steps
         * @param Delay\Delay|null $delay
         * @param array $args
         * @return Schedule
         * @throws Exception
         */
        public static function newMultiStepInstance(int $steps, \Inpsyde\MultilingualPress\Schedule\Delay\Delay $delay = null, array $args = []): \Inpsyde\MultilingualPress\Schedule\Schedule
        {
        }
        /**
         * Create a new single-step schedule.
         *
         * @return Schedule
         * @throws Exception
         */
        public static function newMonoStepInstance(): \Inpsyde\MultilingualPress\Schedule\Schedule
        {
        }
        /**
         * @param array $data
         * @return Schedule
         * @throws Exception
         */
        //phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
        public static function fromArray(array $data): \Inpsyde\MultilingualPress\Schedule\Schedule
        {
        }
        /**
         * @return string
         */
        public function id(): string
        {
        }
        /**
         * @return DateTimeInterface
         */
        public function startedOn(): \DateTimeInterface
        {
        }
        /**
         * @return DateTimeInterface
         */
        public function lastUpdate(): \DateTimeInterface
        {
        }
        /**
         * @return DateTimeInterface|null
         */
        public function estimatedFinishTime(): ?\DateTimeInterface
        {
        }
        /**
         * @return string
         */
        public function estimatedRemainingTime(): string
        {
        }
        /**
         * @return bool
         */
        public function isMultiStep(): bool
        {
        }
        /**
         * @return int
         */
        public function stepToFinish(): int
        {
        }
        /**
         * @return bool
         */
        public function isDone(): bool
        {
        }
        /**
         * Force schedule to done status
         *
         * @return Schedule
         * @throws Exception
         */
        public function done(): \Inpsyde\MultilingualPress\Schedule\Schedule
        {
        }
        /**
         * @return Schedule
         * @throws Exception
         */
        public function nextStep(): \Inpsyde\MultilingualPress\Schedule\Schedule
        {
        }
        /**
         * @return array
         */
        public function toArray(): array
        {
        }
        /**
         * @return Delay\Delay
         */
        public function delay(): \Inpsyde\MultilingualPress\Schedule\Delay\Delay
        {
        }
    }
    /**
     * Service provider for all schedule objects.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        /**
         * @param Container $container
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Bootstraps the registered services.
         *
         * @param Container $container
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Schedule\Delay {
    interface Delay
    {
        /**
         * Calculates the delay in seconds that should be used to setup the cron event for each index,
         * providing the total steps and schedule arguments as context.
         *
         * @param int $index
         * @param int $total
         * @param array|null $args
         * @return int
         */
        public function calculate(int $index, int $total, array $args = null): int;
    }
    /**
     * Class OneSecondEveryGivenSteps
     * @package Inpsyde\MultilingualPress\Schedule\Delay
     */
    final class OneSecondEveryGivenSteps implements \Inpsyde\MultilingualPress\Schedule\Delay\Delay
    {
        /**
         * @return OneSecondEveryGivenSteps
         */
        public static function default(): \Inpsyde\MultilingualPress\Schedule\Delay\OneSecondEveryGivenSteps
        {
        }
        /**
         * @param int $everySteps
         */
        public function __construct(int $everySteps)
        {
        }
        /**
         * @param int $index
         * @param int $total
         * @param array $args
         * @return int
         */
        public function calculate(int $index, int $total, array $args = null): int
        {
        }
    }
    /**
     * Class AverageMicrosecondsDuration
     * @package Inpsyde\MultilingualPress\Schedule\Delay
     */
    final class AverageMicrosecondsDuration implements \Inpsyde\MultilingualPress\Schedule\Delay\Delay
    {
        /**
         * Creates an instance with default average of 500 microseconds, which means delay added will be
         * 1 seconds every 2000 steps.
         *
         * @return AverageMicrosecondsDuration
         */
        public static function default(): \Inpsyde\MultilingualPress\Schedule\Delay\AverageMicrosecondsDuration
        {
        }
        /**
         * @param int $microseconds
         */
        public function __construct(int $microseconds)
        {
        }
        /**
         * @param int $index
         * @param int $total
         * @param array $args
         *
         * @return int
         */
        public function calculate(int $index, int $total, array $args = null): int
        {
        }
    }
    final class Zero implements \Inpsyde\MultilingualPress\Schedule\Delay\Delay
    {
        /**
         * @param int $index
         * @param int $total
         * @param array $args
         * @return int
         */
        public function calculate(int $index, int $total, array $args = null): int
        {
        }
    }
    final class MaxDelay implements \Inpsyde\MultilingualPress\Schedule\Delay\Delay
    {
        /**
         * @param int $maxDelay
         */
        public function __construct(int $maxDelay)
        {
        }
        /**
         * @param int $index
         * @param int $total
         * @param array $args
         * @return int
         */
        public function calculate(int $index, int $total, array $args = null): int
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core {
    /**
     * Deletes all plugin-specific data when a site is deleted.
     */
    class SiteDataDeletor
    {
        /**
         * @param ContentRelations $contentRelations
         * @param SiteRelations $siteRelations
         * @param SiteSettingsRepository $siteSettingsRepository
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository)
        {
        }
        /**
         * Deletes all plugin-specific data of the site with the given ID.
         *
         * @param \WP_Site $oldSite
         * @throws NonexistentTable
         */
        public function deleteSiteData(\WP_Site $oldSite): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core\Frontend {
    /**
     * Interface for all alternate language renderer implementations.
     */
    interface AltLanguageRenderer
    {
        public const TYPE_HTTP_HEADER = 1;
        public const TYPE_HTML_LINK_TAG = 2;
        /**
         * Returns the output type.
         *
         * @return int
         */
        public function type(): int;
        /**
         * Renders all available alternate languages.
         *
         * @param array ...$args
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function render(...$args): void;
    }
    /**
     * Alternate language HTML link tag renderer implementation.
     */
    final class AltLanguageHtmlLinkTagRenderer implements \Inpsyde\MultilingualPress\Core\Frontend\AltLanguageRenderer
    {
        public const FILTER_HREFLANG = 'multilingualpress.hreflang_html_link_tag';
        public const FILTER_RENDER_HREFLANG = 'multilingualpress.render_hreflang';
        /**
         * @param AlternateLanguages $alternateLanguages
         * @param SiteSettingsRepository $siteSettingsRepository
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Frontend\AlternateLanguages $alternateLanguages, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository)
        {
        }
        /**
         * Renders all alternate languages as HTML link tags into the HTML head.
         *
         * @param array ...$args
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         * phpcs:disable WordPressVIPMinimum.Security.ProperEscapingFunction.notAttrEscAttr
         */
        public function render(...$args): void
        {
        }
        /**
         * Returns the output type.
         *
         * @return int
         */
        public function type(): int
        {
        }
    }
    /**
     * Post type link URL filter.
     * @psalm-suppress PropertyNotSetInConstructor
     */
    final class PostTypeLinkUrlFilter implements \Inpsyde\MultilingualPress\Framework\Filter\Filter
    {
        use \Inpsyde\MultilingualPress\Framework\Filter\FilterTrait;
        /**
         * @param PostTypeRepository $postTypeRepository
         * @throws \Throwable
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\PostTypeRepository $postTypeRepository)
        {
        }
        /**
         * Filters the post type link URL and returns a query-based representation, if set for the
         * according post type.
         *
         * @param string $postLink
         * @param \WP_Post $post
         * @return string
         */
        public function unprettifyPermalink(string $postLink, \WP_Post $post): string
        {
        }
    }
    /**
     * Alternate languages data object.
     *
     * @template-implements \IteratorAggregate<string>
     */
    class AlternateLanguages implements \IteratorAggregate
    {
        public const FILTER_HREFLANG_POST_STATUS = 'multilingualpress.hreflang_post_status';
        public const FILTER_HREFLANG_TRANSLATIONS = 'multilingualpress.hreflang_translations';
        public const FILTER_HREFLANG_URL = 'multilingualpress.hreflang_url';
        protected \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\Translations $api, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository)
        {
        }
        /**
         * @inheritdoc
         */
        public function getIterator(): \Traversable
        {
        }
        /**
         * @param Translation $translation
         * @return string
         */
        public function hreflangCode(\Inpsyde\MultilingualPress\Framework\Api\Translation $translation): string
        {
        }
    }
    /**
     * Alternate language controller.
     */
    class AltLanguageController
    {
        public const FILTER_HREFLANG_TYPE = 'multilingualpress.hreflang_type';
        public function __construct()
        {
        }
        /**
         * Registers the given renderer according to the given arguments.
         *
         * @param AltLanguageRenderer $renderer
         * @param string $action
         * @param int $priority
         * @param int $acceptedArgs
         * @return bool
         */
        public function registerRenderer(\Inpsyde\MultilingualPress\Core\Frontend\AltLanguageRenderer $renderer, string $action, int $priority = 10, int $acceptedArgs = 1): bool
        {
        }
    }
    /**
     * Alternate language HTTP header renderer implementation.
     */
    final class AltLanguageHttpHeaderRenderer implements \Inpsyde\MultilingualPress\Core\Frontend\AltLanguageRenderer
    {
        public const FILTER_HREFLANG = 'multilingualpress.hreflang_http_header';
        public const FILTER_RENDER_HREFLANG = 'multilingualpress.render_hreflang';
        /**
         * @param AlternateLanguages $alternateLanguages
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Frontend\AlternateLanguages $alternateLanguages)
        {
        }
        /**
         * Renders all available alternate languages as Link HTTP headers.
         *
         * @param array ...$args
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         */
        public function render(...$args): void
        {
        }
        /**
         * Returns the output type.
         *
         * @return int
         */
        public function type(): int
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core {
    class OriginalTaxonomySlugsRepository
    {
        /**
         * Keeps track of the original taxonomy slug (the taxonomy `rewrite` slug or the taxonomy name),
         * i.e., the slug the taxonomy has before any translations from the MultilingualPress settings are applied.
         *
         * @param string $taxonomy
         * @param string $slug
         * @return void
         */
        public function storeSlugForTaxonomy(string $taxonomy, string $slug): void
        {
        }
        /**
         * Retrieves the original slug for the given taxonomy.
         *
         * @param string $taxonomy
         * @return string
         */
        public function slugForTaxonomy(string $taxonomy): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core\Entity {
    /**
     * Simple read-only storage for taxonomies active for MultilingualPress.
     */
    final class ActiveTaxonomies
    {
        public const FILTER_ACTIVE_TAXONOMIES = 'multilingualpress.active_taxonomies';
        /**
         * Returns the allowed taxonomy names.
         *
         * @return string[]
         */
        public function names(): array
        {
        }
        /**
         * Returns the allowed taxonomy objects.
         *
         * @return array<\WP_Taxonomy|bool>
         */
        public function objects(): array
        {
        }
        /**
         * Returns true if given taxonomy names are allowed.
         *
         * @param string ...$taxonomySlugs
         * @return bool
         */
        public function areTaxonomiesActive(string ...$taxonomySlugs): bool
        {
        }
    }
    /**
     * Simple read-only storage for post types active for MultilingualPress.
     */
    final class ActivePostTypes
    {
        public const FILTER_ACTIVE_POST_TYPES = 'multilingualpress.active_post_types';
        /**
         * Returns the active post type slugs.
         *
         * @return string[]
         */
        public function names(): array
        {
        }
        /**
         * Returns the active post type objects.
         *
         * @return array<\WP_Post_Type|null>
         */
        public function objects(): array
        {
        }
        /**
         * Checks if all given post type slugs are active.
         *
         * @param string ...$postTypeSlugs
         * @return bool
         */
        public function arePostTypesActive(string ...$postTypeSlugs): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core {
    /**
     * MultilingualPress-specific locations implementation.
     */
    class Locations
    {
        /**
         * Adds a new location according to the given arguments.
         *
         * @param string $name
         * @param string $path
         * @param string $url
         * @return Locations
         */
        public function add(string $name, string $path, string $url): \Inpsyde\MultilingualPress\Core\Locations
        {
        }
        /**
         * Returns the location data according to the given arguments.
         *
         * @param string $name
         * @param string $type
         * @return string
         */
        public function valueFor(string $name, string $type): string
        {
        }
        /**
         * Checks if a location with the given name exists.
         *
         * @param string $name
         * @return bool
         */
        public function contain(string $name): bool
        {
        }
    }
    /**
     * Interface for settings repository
     * @psalm-type Setting = array{active?: int, ui?: string}
     */
    interface SettingsRepository
    {
        /**
         * Get all the settings of current repository stored in site options.
         *
         * Returns a two-items array, where the first is a boolean indicating if settings are found in database,
         * the second is actual settings array. Help distinguish on-purpose empty array in db from a no-result.
         *
         * @return array<int, bool|array>
         * @psalm-return  array{0: bool, 1: array<string, Setting>}
         */
        public function allSettings(): array;
    }
}
namespace Inpsyde\MultilingualPress\Core\Admin {
    /**
     * Module settings updater.
     */
    class ModuleSettingsUpdater
    {
        public const ACTION_SAVE_MODULES = 'multilingualpress.save_modules';
        public const NAME_MODULE_SETTINGS = 'multilingualpress_modules';
        /**
         * @param ModuleManager $moduleManager
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Updates the plugin settings according to the data in the request.
         *
         * @param Request $request
         * @return bool
         */
        public function updateSettings(\Inpsyde\MultilingualPress\Framework\Http\Request $request): bool
        {
        }
    }
    final class TaxonomySlugsSettingsSectionView implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView
    {
        /**
         * @param SiteSettingsSectionViewModel $model
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionViewModel $model, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, string $description = '')
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core\Admin\Settings {
    /**
     * Add MultilingualPress Settings into WordPress site settings screen
     */
    class WordPressSettingsScreen
    {
        /**
         * @param array<SiteSettingViewModel> $settings The list of settings
         * @param SiteSettingsRepository $repository
         */
        public function __construct(array $settings, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $repository)
        {
        }
        /**
         * Create the MultilingualPress settings in WordPress site settings screen
         *
         * @see https://developer.wordpress.org/reference/functions/add_settings_field/ add_settings_field
         */
        public function addSettings(): void
        {
        }
        /**
         * Update the MultilingualPress language
         *
         * When the WordPress settings are updated, we also need to update MultilingualPress language.
         * The MultilingualPress Language setting is added in WordPress General Settings screen.
         *
         * @see https://developer.wordpress.org/reference/hooks/update_option/ update_option
         *
         * @param string $option The option name
         * @param mixed $oldValue The old option value.
         * @param mixed $value The new option value.
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function saveSettings(string $option, $oldValue, $value): void
        {
        }
    }
    /**
     * Interface SettingsUpdater
     * @package Inpsyde\MultilingualPress\Core\Admin\Settings
     */
    interface SettingsUpdater
    {
        /**
         * Update Settings
         *
         * @param Request $request
         * @return bool
         */
        public function updateSettings(\Inpsyde\MultilingualPress\Framework\Http\Request $request): bool;
    }
}
namespace Inpsyde\MultilingualPress\Core\Admin\Settings\Cache {
    /**
     * Class CacheSettingsUpdater
     * @package Inpsyde\MultilingualPress\Core\Admin
     */
    class CacheSettingsUpdater implements \Inpsyde\MultilingualPress\Core\Admin\Settings\SettingsUpdater
    {
        /**
         * CacheSettingsUpdater constructor.
         * @param CacheSettingsRepository $cacheSettingsRepository
         * @param Auth $auth
         * @param CacheSettingsOptions $cacheSettingsOptions
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository $cacheSettingsRepository, \Inpsyde\MultilingualPress\Framework\Auth\Auth $auth, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions $cacheSettingsOptions)
        {
        }
        /**
         * @inheritDoc
         */
        public function updateSettings(\Inpsyde\MultilingualPress\Framework\Http\Request $request): bool
        {
        }
        /**
         * Retrieve Values From Request
         *
         * @param Request $request
         * @return array
         */
        protected function retrieveValueFromRequest(\Inpsyde\MultilingualPress\Framework\Http\Request $request): array
        {
        }
        /**
         * Normalize Values By Converting Strings into Boolean
         *
         * The submitted value for checkboxes it's usually a 'on' or nothing,
         * we don't want to have an 'on' value into the database because there's no
         * off for them, simply non submitted values are no in $_POST request so we
         * do not store them into database.
         *
         * @param array $settings
         * @return array
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function normalizeValues(array $settings): array
        {
        }
    }
    /**
     * Class CacheSettingsRepository
     * @package Inpsyde\MultilingualPress\Core\Admin
     */
    class CacheSettingsRepository
    {
        public const OPTION_NAME = 'multilingualpress_internal_cache_setting';
        /**
         * CacheSettingsRepository constructor.
         * @param CacheSettingsOptions $cacheSettingsOptions
         * @param CacheSettingNamesValidator $cacheSettingNamesValidator
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions $cacheSettingsOptions, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingNamesValidator $cacheSettingNamesValidator)
        {
        }
        /**
         * Retrieve All Options
         *
         * @return array
         */
        public function all(): array
        {
        }
        /**
         * Get Single Cache Setting
         *
         * @param string $group
         * @param string $key
         * @return bool
         */
        public function get(string $group, string $key): bool
        {
        }
        /**
         * Update Settings
         *
         * @param array $settings
         * @return bool
         * @throws DomainException
         */
        public function update(array $settings): bool
        {
        }
        /**
         * Fill Options With Values From Database
         *
         * @return array
         */
        protected function optionsFromDatabase(): array
        {
        }
    }
    /**
     * Class CacheSettingsOptions
     * @package Inpsyde\MultilingualPress\Core\Admin
     */
    class CacheSettingsOptions
    {
        public const OPTION_GROUP_API_NAME = 'api';
        public const OPTION_GROUP_DATABASE_NAME = 'database';
        public const OPTION_GROUP_NAV_MENU_NAME = 'nav_menu';
        public const OPTION_SEARCH_TRANSLATIONS_API_NAME = 'api.translation';
        public const OPTION_CONTENT_IDS_API_NAME = 'api.content_ids';
        public const OPTION_RELATIONS_API_NAME = 'api.content_relations';
        public const OPTION_HAS_SITE_RELATIONS_API_NAME = 'api.has_site_relations';
        public const OPTION_ALL_RELATIONS_API_NAME = 'api.all_relations';
        public const OPTION_RELATED_SITE_IDS_API_NAME = 'api.related_site_ids';
        public const OPTION_ALL_TABLES_DATABASE_NAME = 'database.table_list';
        public const OPTION_ITEM_FILTER_NAV_MENU_NAME = 'nav_menu.item_filter';
        /**
         * Retrieve Default Options
         *
         * Default options are also the list of the options it self not just default values.
         *
         * @return array
         */
        public function defaults(): array
        {
        }
        /**
         * Retrieve Information about the options such as
         * - Group Name
         * - Label for specific option
         * - Description for specific option
         *
         * @return array
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function info(): array
        {
        }
    }
    /**
     * Class CacheSettingsTabView
     * @package Inpsyde\MultilingualPress\Core\Admin
     */
    class CacheSettingsTabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        /**
         * CacheSettingsTabView constructor.
         * @param CacheSettingsOptionsView $options
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptionsView $options, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritDoc
         */
        public function render(): void
        {
        }
    }
    /**
     * Class CacheOptionNamesValidator
     * @package Inpsyde\MultilingualPress\Core\Admin\Settings\Cache
     */
    class CacheSettingNamesValidator
    {
        /**
         * @param array $settings
         * @return bool
         */
        public function allowed(array $settings): bool
        {
        }
    }
    /**
     * Class CacheSettingsOptions
     * @package Inpsyde\MultilingualPress\Core\Admin
     */
    class CacheSettingsOptionsView
    {
        /**
         * CacheSettingsOptionsView constructor.
         * @param CacheSettingsRepository $repository
         * @param CacheSettingsOptions $cacheSettingsOptions
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository $repository, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions $cacheSettingsOptions)
        {
        }
        /**
         * Render the Options Markup
         *
         * @return void
         */
        public function render(): void
        {
        }
        /**
         * Render a Group of Options
         *
         * @param string $name
         * @param array $group
         */
        protected function renderGroup(string $name, array $group): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core\Admin {
    /**
     * MultilingualPress "Language" site setting.
     */
    final class LanguageSiteSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @inheritdoc
         */
        public function render(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
    final class SiteSettings implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionViewModel
    {
        public const ID = 'mlp-site-settings';
        /**
         * SiteSettings constructor.
         * @param SiteSettingView $view
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView $view)
        {
        }
        /**
         * @inheritdoc
         */
        public function id(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function renderView(int $siteId): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
    /**
     * Hreflang site setting.
     */
    final class HreflangSiteSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * Hreflang site setting constructor.
         *
         * @param array<SettingOptionInterface> $options
         * @param SiteRelations $siteRelations
         * @param SiteSettingsRepository $siteSettingsRepository
         */
        public function __construct(array $options, \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository)
        {
        }
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        public function render(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
    class LicenseSettingsRepository
    {
        public function license(): \Inpsyde\MultilingualPress\License\License
        {
        }
        public function update(\Inpsyde\MultilingualPress\License\License $license): bool
        {
        }
    }
    final class TaxonomySlugsSettingsTabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        public function __construct(\Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabData $data, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(): void
        {
        }
    }
    /**
     * Request handler for site settings update requests.
     */
    class SiteSettingsUpdateRequestHandler
    {
        public const ACTION = 'update_multilingualpress_site_settings';
        /**
         * @param SiteSettingsUpdatable $updater
         * @param Request $request
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\SiteSettingsUpdatable $updater, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Handles POST requests.
         */
        public function handlePostRequest(): void
        {
        }
    }
    /**
     * MultilingualPress "Alternative language title" site setting.
     */
    final class AltLanguageTitleSiteSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @param string $option
         * @param Nonce $nonce
         * @param SettingsRepository $repository
         */
        public function __construct(string $option, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Module\AltLanguageTitleInAdminBar\SettingsRepository $repository)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
    /**
     * WordPress "Language" site setting.
     */
    class WordPressLanguageSiteSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @param string $wordPressLanguageSettingMarkup
         */
        public function __construct(string $wordPressLanguageSettingMarkup)
        {
        }
        /**
         * @inheritdoc
         * phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
         */
        public function render(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
        protected function description(): string
        {
        }
    }
    class TaxonomySlugsSettingsRepository
    {
        public const TAXONOMY_SLUGS = 'mlp_site_taxonomy_slugs';
        /**
         * Retrieve the taxonomy slugs for the site with the given ID.
         *
         * @param int|null $siteId
         * @return array
         */
        public function taxonomySlugs(int $siteId = null): array
        {
        }
        /**
         * Update the taxonomy slugs for the site with the given ID.
         *
         * @param array $slugs
         * @param int|null $siteId
         * @return bool
         */
        public function updateTaxonomySlugs(array $slugs, int $siteId = null): bool
        {
        }
    }
    class PostTypeSlugSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @param PostTypeSlugsSettingsRepository $repository
         * @param PostTypeRepository $postTypeRepository
         * @param WP_Post_Type $postType
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\PostTypeSlugsSettingsRepository $repository, \Inpsyde\MultilingualPress\Core\PostTypeRepository $postTypeRepository, \WP_Post_Type $postType)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
    /**
     * MultilingualPress "Relationships" site setting.
     */
    final class RelationshipsSiteSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        /**
         * @param SiteSettingsRepository $settings
         * @param SiteRelations $siteRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $settings, \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
    /**
     * New site settings section view model implementation.
     */
    final class NewSiteSettings implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionViewModel
    {
        public const SECTION_ID = 'mlp-new-site-settings';
        /**
         * @param SiteSettingView $view
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView $view)
        {
        }
        /**
         * @inheritdoc
         */
        public function id(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function renderView(int $siteId): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
    class TaxonomySettingsUpdater
    {
        public const SETTINGS_NAME = 'taxonomy_settings';
        public const SETTINGS_FIELD_ACTIVE = 'active';
        /**
         * @param TaxonomyRepository $repository
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\TaxonomyRepository $repository, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Updates the taxonomy settings.
         *
         * @param Request $request
         * @return bool
         */
        public function updateSettings(\Inpsyde\MultilingualPress\Framework\Http\Request $request): bool
        {
        }
    }
    /**
     * Site settings updater.
     */
    class SiteSettingsUpdater implements \Inpsyde\MultilingualPress\Framework\Setting\SiteSettingsUpdatable
    {
        public const ACTION_DEFINE_INITIAL_SETTINGS = 'multilingualpress.define_initial_site_settings';
        public const ACTION_UPDATE_SETTINGS = 'multilingualpress.update_site_settings';
        public const NAME_SEARCH_ENGINE_VISIBILITY = 'mlp_search_engine_visibility';
        /**
         * @param SiteSettingsRepository $repository
         * @param Request $request
         * @param LanguageInstaller $languageInstaller
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $repository, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Core\Admin\LanguageInstaller $languageInstaller)
        {
        }
        /**
         * @inheritdoc
         */
        public function defineInitialSettings(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function updateSettings(int $siteId): void
        {
        }
    }
    final class PostTypeSettingsTabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        /**
         * @param PostTypeRepository $repository
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\PostTypeRepository $repository, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(): void
        {
        }
    }
    class TaxonomySlugsSettingsUpdateRequestHandler
    {
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\TaxonomySlugsSettingsUpdater $updater, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Handles POST requests.
         */
        public function handlePostRequest(): void
        {
        }
    }
    class LicenseSettingsUpdater
    {
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\LicenseSettingsRepository $licenseSettingsRepository, \Inpsyde\MultilingualPress\License\Api\Activator $activator, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @param Request $request
         * @return bool
         */
        public function updateSettings(\Inpsyde\MultilingualPress\Framework\Http\Request $request): bool
        {
        }
    }
    class LicenseNotice
    {
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\LicenseSettingsRepository $licenseSettingsRepository)
        {
        }
        /**
         * @wp-hook current_screen
         */
        public function showMlpAdminNotice(): void
        {
        }
        /**
         * @psalm-param array<'Name'> $pluginData
         * @wp-hook after_plugin_row_multilingualpress
         */
        public function showPluginRowNotice(string $file, array $pluginData): void
        {
        }
    }
    class PostTypeSlugsSettingsUpdater
    {
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\PostTypeSlugsSettingsRepository $repository, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        public function updateSettings(int $siteId): void
        {
        }
    }
    class LanguagesAjaxSearch
    {
        public const ACTION = 'multilingualpress_search_languages';
        /**
         * @param Request $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * @return void
         */
        public function handle(): void
        {
        }
    }
    class TaxonomySlugSetting implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel
    {
        public const FILTER_ORIGINAL_TAXONOMY_SLUG = 'multilingualpress.original_taxonomy_slug';
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\TaxonomySlugsSettingsRepository $taxonomySlugsSettingsRepository, \Inpsyde\MultilingualPress\Core\OriginalTaxonomySlugsRepository $originalTaxonomySlugsRepository, \Inpsyde\MultilingualPress\Core\TaxonomyRepository $taxonomyRepository, \WP_Taxonomy $taxonomy)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId): void
        {
        }
        /**
         * @inheritdoc
         */
        public function title(): string
        {
        }
    }
    class Screen
    {
        /**
         * @return bool
         */
        public static function isNetworkSite(): bool
        {
        }
        /**
         * @return bool
         */
        public static function isMultilingualPressSettings(): bool
        {
        }
        /**
         * @return bool
         */
        public static function isLanguageManagerSettings(): bool
        {
        }
        /**
         * @return bool
         */
        public static function isExternalSitesSettings(): bool
        {
        }
        public static function isCommentsSettingsPage(): bool
        {
        }
        /**
         * @return bool
         */
        public static function isQuickLinksSettings(): bool
        {
        }
        /**
         * @return bool
         */
        public static function isRedirectSettings(): bool
        {
        }
        /**
         * @return bool
         */
        public static function isEditPostsTable(): bool
        {
        }
        public static function isEditOrCreatePostType(string $postType = ''): bool
        {
        }
        public static function isPage(string ...$pages): bool
        {
        }
    }
    class PostTypeSlugsSettingsUpdateRequestHandler
    {
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\PostTypeSlugsSettingsUpdater $updater, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Handles POST requests.
         */
        public function handlePostRequest(): void
        {
        }
    }
    final class PostTypeSlugsSettingsSectionView implements \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView
    {
        public const ACTION_AFTER = 'multilingualpress.after_permalink_site_settings';
        public const ACTION_BEFORE = 'multilingualpress.before_permalink_site_settings';
        /**
         * @param SiteSettingsSectionViewModel $model
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionViewModel $model, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, string $description = '')
        {
        }
        /**
         * @inheritdoc
         */
        public function render(int $siteId): bool
        {
        }
    }
    /**
     * Module settings tab view.
     */
    final class ModuleSettingsTabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        public const ACTION_IN_MODULE_LIST = 'multilingualpress.in_module_list';
        public const FILTER_SHOW_MODULE = 'multilingualpress.show_module';
        /**
         * @param ModuleManager $moduleManager
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(): void
        {
        }
    }
    /**
     * Plugin settings page view.
     */
    final class PluginSettingsPageView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        /**
         * @param Nonce $nonce
         * @param ServerRequest $request
         * @param array $settingTabs
         * @throws \UnexpectedValueException
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request, array $settingTabs)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core\Admin\Pointers {
    class Pointers
    {
        public const USER_META_KEY = '_dismissed_mlp_pointers';
        public const ACTION_AFTER_POINTERS_CREATED = 'multilingualpress.after_pointers_created';
        public const FILTER_DISABLE_ONBOARDING_POINTERS = 'multilingualpress.disable_onboarding_pointers';
        public const SCRIPTS_HANDLER_NAME = 'pointers';
        protected \Inpsyde\MultilingualPress\Framework\Http\Request $request;
        protected \Inpsyde\MultilingualPress\Core\Admin\Pointers\Repository $repository;
        protected string $urlToAssetsFolder;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Core\Admin\Pointers\Repository $repository, string $urlToAssetsFolder)
        {
        }
        /**
         * @return void
         */
        public function createPointers(): void
        {
        }
        /**
         * @param array $pointers
         * @param string $ajaxAction
         * @return void
         */
        public function enqueuePointers(array $pointers, string $ajaxAction): void
        {
        }
        /**
         * @return void
         */
        public function dismiss(): void
        {
        }
    }
    /**
     * Pointers Repository.
     */
    class Repository
    {
        /**
         * @param string $screen
         * @param string $key
         * @param string $target
         * @param string $next
         * @param array $nextTrigger
         * @param array $options
         * @return $this
         */
        public function registerForScreen(string $screen, string $key, string $target, string $next, array $nextTrigger, array $options): \Inpsyde\MultilingualPress\Core\Admin\Pointers\Repository
        {
        }
        /**
         * @param string $screen
         * @param string $action
         * @return $this
         */
        public function registerActionForScreen(string $screen, string $action): \Inpsyde\MultilingualPress\Core\Admin\Pointers\Repository
        {
        }
        /**
         * @param string $screen
         * @return array
         */
        public function forScreen(string $screen): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core\Admin {
    class SiteSettingsRepository
    {
        use \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepositoryTrait;
        public const KEY_LANGUAGE = 'lang';
        public const NAME_LANGUAGE = 'mlp_site_language';
        public const NAME_RELATIONSHIPS = 'mlp_site_relations';
        public const NAME_HREFLANG = 'multilingualpress_hreflang';
        public const NAME_HREFLANG_XDEFAULT = 'xdefault';
        public const NAME_HREFLANG_DISPLAY_TYPE = 'display_type';
        public const OPTION = 'multilingualpress_site_settings';
        /**
         * @param SiteRelations $siteRelations
         * @param Facade $cache
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \Inpsyde\MultilingualPress\Framework\Cache\Server\Facade $cache)
        {
        }
        /**
         * Returns an array with the IDs of all sites with an assigned language,
         * minus the given IDs, if any.
         *
         * @param int[] $exclude
         * @return int[]
         */
        public function allSiteIds(array $exclude = []): array
        {
        }
        /**
         * Returns the site language of the site with the given ID, or the current site.
         *
         * @param int|null $siteId
         * @return string
         */
        public function siteLanguageTag(int $siteId = null): string
        {
        }
        /**
         * Sets the language for the site with the given ID, or the current site.
         *
         * @param string $language
         * @param int|null $siteId
         * @return bool
         */
        public function updateLanguage(string $language, int $siteId = null): bool
        {
        }
        /**
         * Sets the relationships for the site with the given ID, or the current site.
         *
         * @param int[] $siteIds
         * @param int|null $baseSiteId
         * @return bool
         * @throws NonexistentTable
         */
        public function relate(array $siteIds, int $baseSiteId = null): bool
        {
        }
        /**
         * Updates Hreflang settings values.
         * @param array $hreflangSettings
         * @param int|null $siteId
         * @return bool
         */
        public function updateHreflangSettings(array $hreflangSettings, int $siteId = null): bool
        {
        }
        /**
         * Get the value of Hreflang setting option
         *
         * @param int $siteId
         * @param string $optionName The Hreflang setting option name
         * @return string The value of Hreflang setting option
         */
        public function hreflangSettingForSite(int $siteId, string $optionName): string
        {
        }
    }
    class TaxonomySlugsSettingsUpdater
    {
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\TaxonomySlugsSettingsRepository $repository, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * Update the translation of taxonomy slugs for the site with the given ID.
         *
         * @param int $siteId
         */
        public function updateSettings(int $siteId): void
        {
        }
    }
    final class TaxonomySettingsTabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        /**
         * @param TaxonomyRepository $repository
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\TaxonomyRepository $repository, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(): void
        {
        }
    }
    class PostTypeSettingsUpdater
    {
        public const SETTINGS_NAME = 'post_type_settings';
        public const SETTINGS_FIELD_ACTIVE = 'active';
        public const SETTINGS_FIELD_PERMALINKS = 'permalinks';
        /**
         * @param PostTypeRepository $repository
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\PostTypeRepository $repository, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Updates the post type settings.
         *
         * @param Request $request
         * @return bool
         */
        public function updateSettings(\Inpsyde\MultilingualPress\Framework\Http\Request $request): bool
        {
        }
    }
    class LicenseSettingsTabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        /**
         * @param LicenseSettingsRepository $licenseSettingsRepository
         * @param Activator $activator
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\LicenseSettingsRepository $licenseSettingsRepository, \Inpsyde\MultilingualPress\License\Api\Activator $activator, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * We refresh the license status on page rendering to provide actual data to the user.
         * The license is marked as inactive if there is an error while refreshing the status.
         */
        public function render(): void
        {
        }
        /**
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        protected function activateView(\Inpsyde\MultilingualPress\License\License $license): void
        {
        }
        protected function deactivateView(\Inpsyde\MultilingualPress\License\License $license): void
        {
        }
        /**
         * @param string $licenseApiKey
         * @return string
         */
        protected function displayLastDigits(string $licenseApiKey): string
        {
        }
    }
    /**
     * Settings page view for the MultilingualPress site settings tab.
     */
    final class SiteSettingsTabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        /**
         * @param SettingsPageTabData $data
         * @param SiteSettingView $view
         * @param Request $request
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabData $data, \Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView $view, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(): void
        {
        }
    }
    /**
     * Class LanguageInstaller
     */
    class LanguageInstaller
    {
        /**
         * @param string $languageCode The WordPress language code
         */
        public function install(string $languageCode): void
        {
        }
    }
    final class PostTypeSlugsSettingsTabView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        public function __construct(\Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabData $data, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(): void
        {
        }
    }
    /**
     * Plugin settings updater.
     */
    class PluginSettingsUpdater
    {
        public const ACTION = 'update_multilingualpress_settings';
        public const ACTION_UPDATE_PLUGIN_SETTINGS = 'multilingualpress.update_plugin_settings';
        /**
         * @param Nonce $nonce
         * @param Request $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * Updates the plugin settings according to the data in the request.
         */
        public function updateSettings(): void
        {
        }
    }
    class PostTypeSlugsSettingsRepository
    {
        public const POST_TYPE_SLUGS = 'mlp_site_post_type_slugs';
        public const OPTION = 'multilingualpress_post_type_slugs_translation';
        /**
         * Retrieve the post type slugs for the site with the given ID.
         *
         * @param int|null $siteId
         * @return array
         */
        public function postTypeSlugs(int $siteId = null): array
        {
        }
        /**
         * Update the post type slugs for the site with the given ID.
         *
         * @param array $slugs
         * @param int|null $siteId
         * @return bool
         */
        public function updatePostTypeSlugs(array $slugs, int $siteId = null): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Core {
    class OriginalPostTypeSlugsRepository
    {
        /**
         * Keeps track of the original post type slug (the post type `rewrite` slug or the post type name),
         * i.e., the slug the post type has before any translations from the MultilingualPress settings are applied.
         *
         * @param string $postType
         * @param string $slug
         * @return void
         */
        public function storeSlugForPostType(string $postType, string $slug): void
        {
        }
        /**
         * Retrieves the original slug for the given post type.
         *
         * @param string $postType
         * @return string
         */
        public function slugForPostType(string $postType): string
        {
        }
    }
    interface PostTypeRepositoryInterface
    {
        /**
         * Returns all post types that MultilingualPress is able to support.
         *
         * @return \WP_Post_Type[]
         */
        public function allAvailablePostTypes(): array;
        /**
         * Returns all post types supported by MultilingualPress.
         *
         * @return string[]
         */
        public function supportedPostTypes(): array;
        /**
         * Checks if the post type with the given slug is active.
         *
         * @param string $slug
         * @return bool
         */
        public function isPostTypeActive(string $slug): bool;
        /**
         * Checks if the post type with the given slug is set to be query-based.
         *
         * @param string $slug
         * @return bool
         */
        public function isPostTypeQueryBased(string $slug): bool;
        /**
         * Sets post type support according to the given settings.
         *
         * @param array $postTypes
         * @return bool
         */
        public function supportPostTypes(array $postTypes): bool;
        /**
         * Removes the support for all post types.
         *
         * @return bool
         */
        public function removeSupportForAllPostTypes(): bool;
        /**
         * Retrieves a list of all the supported post type post IDs for given site ID.
         *
         * @param int $siteId The site ID.
         * @param string $status The post status, defaults to 'any'.
         * @return int[] A list of post IDs.
         */
        public function supportedPostTypePostIds(int $siteId, string $status = 'any'): array;
    }
    /**
     * Type-safe post type repository implementation.
     */
    final class PostTypeRepository implements \Inpsyde\MultilingualPress\Core\PostTypeRepositoryInterface
    {
        public const DEFAULT_SUPPORTED_POST_TYPES = ['page', 'post'];
        public const FIELD_ACTIVE = 'active';
        public const FIELD_PERMALINK = 'permalink';
        public const OPTION = 'multilingualpress_post_types';
        public const FILTER_PUBLIC_POST_TYPES = 'multilingualpress.public_post_types';
        public const FILTER_ALL_AVAILABLE_POST_TYPES = 'multilingualpress.all_post_types';
        public const FILTER_SUPPORTED_POST_TYPES = 'multilingualpress.supported_post_types';
        public function allAvailablePostTypes(): array
        {
        }
        public function supportedPostTypes(): array
        {
        }
        public function isPostTypeActive(string $slug): bool
        {
        }
        public function isPostTypeQueryBased(string $slug): bool
        {
        }
        public function supportPostTypes(array $postTypes): bool
        {
        }
        public function removeSupportForAllPostTypes(): bool
        {
        }
        public function supportedPostTypePostIds(int $siteId, string $status = 'any'): array
        {
        }
    }
    // phpcs:disable WordPress.PHP.StrictInArray.MissingArguments
    /**
     * Class TaxonomyRepository
     * @package Inpsyde\MultilingualPress\Core
     */
    class TaxonomyRepository implements \Inpsyde\MultilingualPress\Core\SettingsRepository
    {
        public const CATEGORY = 'category';
        public const TAG = 'post_tag';
        public const DEFAULT_SUPPORTED_TAXONOMIES = [self::CATEGORY, self::TAG];
        public const FIELD_ACTIVE = 'active';
        public const FIELD_SKIN = 'ui';
        public const OPTION = 'multilingualpress_taxonomies';
        public const FILTER_ALL_AVAILABLE_TAXONOMIES = 'multilingualpress.all_taxonomies';
        public const FILTER_SUPPORTED_TAXONOMIES = 'multilingualpress.supported_taxonomies';
        /**
         * Returns all taxonomies that MultilingualPress is able to support.
         *
         * @return WP_Taxonomy[]
         */
        public function allAvailableTaxonomies(): array
        {
        }
        /**
         * Returns all taxonomies supported by MultilingualPress.
         *
         * @return string[]
         */
        public function supportedTaxonomies(): array
        {
        }
        /**
         * Checks if the taxonomy with the given slug is active.
         *
         * @param string $slug
         * @return bool
         */
        public function isTaxonomyActive(string $slug): bool
        {
        }
        /**
         * Sets taxonomy support according to the given settings.
         *
         * @param array $taxonomies
         * @return bool
         */
        public function supportTaxonomies(array $taxonomies): bool
        {
        }
        /**
         * Removes the support for all taxonomies.
         *
         * @return bool
         */
        public function removeSupportForAllTaxonomies(): bool
        {
        }
        /**
         * Retrieve all Registered Taxonomies
         *
         * @return WP_Taxonomy[]
         */
        protected function allAllowedTaxonomies(): array
        {
        }
        /**
         * @inheritDoc
         */
        public function allSettings(): array
        {
        }
        /**
         * @return array<int>
         */
        public function supportedTaxonomiesTermIds(int $siteId): array
        {
        }
    }
    /**
     * MultilingualPress Modules Deactivator
     */
    class ModuleDeactivator
    {
        /**
         * ModuleDeactivator constructor
         *
         * @param ModuleManager $moduleManager
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager)
        {
        }
        /**
         * Deactivate WooCommerce Module
         */
        public function deactivateWooCommerce(): void
        {
        }
    }
    /**
     * Service provider for all Core objects.
     *
     * @psalm-type siteId = int
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        public const FILTER_PLUGIN_LOCALE = 'plugin_locale';
        public const FILTER_AVAILABLE_POST_TYPE_FOR_SETTINGS = 'multilingualpress.post_type_slugs_settings';
        public const FILTER_AVAILABLE_TAXONOMY_FOR_SETTINGS = 'multilingualpress.taxonomy_slugs_settings';
        public const FILTER_HTTP_CLIENT_CONFIG = 'multilingualpress.http_client_config';
        public const FILTER_ADMIN_ALLOWED_SCRIPT_PAGES = 'multilingualpress.allowed_admin_script_pages';
        public const FILTER_ADMIN_ALLOWED_CORE_SCRIPT_PAGES = 'multilingualpress.allowed_admin_core_script_pages';
        public const FILTER_CUSTOM_SLUGS_ALLOWED_USER_ROLE = 'multilingualpress.allowed_user_roles_for_custom_slugs';
        public const ACTION_BUILD_TABS = 'multilingualpress.build_tabs';
        public const WORDPRESS_LANGUAGE_SETTING_MARKUP = 'wordpress.language_setting_markup';
        public const MESSAGE_TYPE_FACTORIES = 'message_type_factories';
        public const PARAMETER_CONFIG_RELATED_SITES = 'multilingualpress.relatedSites';
        public const MODULE_SCRIPTS_HANDLER_NAME = 'multilingualpress';
        public const CONFIGURATION_NAME_FOR_URL_TO_CORE_MODULE_ASSETS = 'multilingualpress.core.urlToModuleAssets';
        /**
         * @inheritdoc
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         * @throws Throwable
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Cache {
    /**
     * Class NavMenuItemSerializer
     * @package Inpsyde\MultilingualPress\Cache
     */
    /**
     * Class NavMenuItemSerializer
     * @package Inpsyde\MultilingualPress\Cache
     */
    class NavMenuItemsSerializer
    {
        public const ALLOWED_MENU_ITEM_FILTER = 'mlp.cache.allowed-nav-menu-items';
        /**
         * @var string[]
         */
        public const POST_ALLOWED_PROPERTIES = ['ID', 'filter'];
        /**
         * @var list<list<int|string|bool>>
         */
        public const MENU_ITEM_ALLOWED_PROPERTIES = [['menu_item_parent', 'int', 0], ['db_id', 'int', 0], ['object_id', 'int', 0], ['object', 'string', ''], ['type', 'string', ''], ['type_label', 'string', ''], ['title', 'string', ''], ['url', 'string', ''], ['target', 'string', ''], ['attr_title', 'string', ''], ['description', 'string', ''], ['classes', 'array', ''], ['xfn', 'string', ''], ['current', 'bool', \false], ['current_item_ancestor', 'bool', \false], ['current_item_parent', 'bool', \false]];
        /**
         * @param \WP_Post[] $items
         * @return NavMenuItemsSerializer
         */
        public static function fromWpPostItems(\WP_Post ...$items): \Inpsyde\MultilingualPress\Cache\NavMenuItemsSerializer
        {
        }
        /**
         * @param array[] $items
         * @return NavMenuItemsSerializer
         */
        public static function fromSerialized(array ...$items): \Inpsyde\MultilingualPress\Cache\NavMenuItemsSerializer
        {
        }
        /**
         * @return \WP_Post[]
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        public function unserialize(): array
        {
        }
        /**
         * @return array[]
         */
        public function serialize(): array
        {
        }
    }
    /**
     * Service provider for all cache objects.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        /**
         * @inheritdoc
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @param Container $container
         */
        public function bootstrapNetworkAdmin(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\License {
    class License
    {
        public const STATUS_ACTIVE = 'active';
        public const STATUS_INACTIVE = 'inactive';
        public function __construct(string $productId, string $apiKey, string $instance, string $status)
        {
        }
        public function productId(): string
        {
        }
        public function apiKey(): string
        {
        }
        /**
         * Unique website key, instance should be never deleted
         */
        public function instance(): string
        {
        }
        public function status(): string
        {
        }
        public function isActive(): bool
        {
        }
        public function withStatus(string $status): self
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\License\Api {
    class Activator
    {
        public const WC_API = 'wc-am-api';
        /**
         * @param array $apiConfiguration
         */
        public function __construct(array $apiConfiguration)
        {
        }
        /**
         * @param License $license
         * @return License|WP_Error
         */
        public function activate(\Inpsyde\MultilingualPress\License\License $license): object
        {
        }
        /**
         * @param License $license
         * @return License|WP_Error
         */
        public function deactivate(\Inpsyde\MultilingualPress\License\License $license): object
        {
        }
        /**
         * @param License $license
         * @return License|WP_Error
         */
        public function refreshStatus(\Inpsyde\MultilingualPress\License\License $license): object
        {
        }
    }
    class Updater
    {
        protected const WC_API = 'wc-am-api';
        /**
         * @param array $pluginProperties
         * @param array $apiConfiguration
         * @param License $license
         */
        public function __construct(array $pluginProperties, array $apiConfiguration, \Inpsyde\MultilingualPress\License\License $license)
        {
        }
        /**
         * @param stdClass $transient
         * @return stdClass
         */
        public function updateCheck(\stdClass $transient): \stdClass
        {
        }
        /**
         * @param bool|mixed $result
         * @param string $action
         * @param stdClass $args
         * @return bool|mixed
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
         */
        public function pluginInformation($result, string $action, \stdClass $args)
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\License {
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Auth {
    /**
     * Class ServiceProvider
     * @package Inpsyde\MultilingualPress\Auth
     */
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        /**
         * @inheritDoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress {
    /**
     * MultilingualPress front controller.
     */
    final class MultilingualPress
    {
        public const ACTION_BOOTSTRAPPED = 'multilingualpress.bootstrapped';
        public const ACTION_REGISTER_MODULES = 'multilingualpress.register_modules';
        public const OPTION_VERSION = 'multilingualpress_version';
        /**
         * @param Container $container
         * @param ServiceProvidersCollection $serviceProviders
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Service\Container $container, \Inpsyde\MultilingualPress\Framework\Service\ServiceProvidersCollection $serviceProviders)
        {
        }
        /**
         * Bootstraps MultilingualPress.
         *
         * @return bool
         * @throws \RuntimeException
         */
        public function bootstrap(): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Attachment {
    /**
     * MultilingualPress Attachment Copier
     */
    class Copier
    {
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Framework\Filesystem $filesystem, \Inpsyde\MultilingualPress\Editor\Notices\ExistingAttachmentsNotice $existingAttachmentsNotice)
        {
        }
        /**
         * Copy attachments from source site to the give remote site using a list of attachment ids
         *
         * @param int $sourceSiteId
         * @param int $remoteSiteId
         * @param array $sourceAttachmentIds
         * @return array
         */
        public function copyById(int $sourceSiteId, int $remoteSiteId, array $sourceAttachmentIds): array
        {
        }
        /**
         * Copy attachments from source site to remote site using source attachments data
         *
         * @param int $sourceSiteId
         * @param int $remoteSiteId
         * @param array $sourceAttachmentsData
         * @return array
         */
        public function copyByAttachmentsData(int $sourceSiteId, int $remoteSiteId, array $sourceAttachmentsData): array
        {
        }
    }
    /**
     * Class AttachmentData
     */
    class AttachmentData
    {
        /**
         * AttachmentData constructor.
         * @param WP_Post $post
         * @param array $meta
         * @param string $filePath
         */
        public function __construct(\WP_Post $post, array $meta, string $filePath)
        {
        }
        /**
         * @return WP_Post
         */
        public function post(): \WP_Post
        {
        }
        /**
         * @return array
         */
        public function meta(): array
        {
        }
        /**
         * @return string
         */
        public function filePath(): string
        {
        }
    }
    /**
     * Class Duplicator
     * @package Inpsyde\MultilingualPress\Attachment
     */
    class Duplicator
    {
        use \Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
        public const FILTER_ATTACHMENTS_PATHS = 'multilingualpress.attachments_to_target_paths';
        /**
         * @param BasePathAdapter $basePathAdapter
         * @param Filesystem $filesystem
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\BasePathAdapter $basePathAdapter, \Inpsyde\MultilingualPress\Framework\Filesystem $filesystem)
        {
        }
        /**
         * Copies all attachment files of the site with given ID to the current site.
         *
         * @param int $sourceSiteId
         * @param int $targetSiteId
         * @param array $attachmentsPaths
         * @return bool
         */
        public function duplicateAttachmentsFromSite(int $sourceSiteId, int $targetSiteId, array $attachmentsPaths): bool
        {
        }
    }
    /**
     * @psalm-type filePaths = array{dir: string, files: string[]}
     */
    class Collection
    {
        public const DEFAULT_LIMIT = 0;
        public const DEFAULT_OFFSET = 0;
        public const META_KEY_ATTACHMENTS = '_wp_attachment_metadata';
        public const META_KEY_ATTACHED_FILE = '_wp_attached_file';
        protected \wpdb $wpdb;
        public function __construct(\wpdb $wpdb)
        {
        }
        /**
         * Retrieves the list of all registered attachments names and paths from the database.
         *
         * Only files referenced in the database are trustworthy, and will therefore get copied.
         *
         * @param int $offset The offset.
         * @param int $limit The limit.
         * @return array The list of file directories and file names.
         * @psalm-return list<filePaths>
         */
        public function list(int $offset = self::DEFAULT_OFFSET, int $limit = self::DEFAULT_LIMIT): array
        {
        }
        /**
         * @return int
         */
        public function count(): int
        {
        }
        /**
         * Retrieves the file paths from given attachment metadata object.
         *
         * @param stdClass $metadata The attachment metadata object.
         * @return array<string, array> The file directory and the list of file names.
         * @psalm-return filePaths
         */
        protected function filePaths(\stdClass $metadata): array
        {
        }
        /**
         * Get the Attachment backup file.
         *
         * When the image is edited in WordPress media editor, the original image will be backed up and
         * stored in _wp_attachment_backup_sizes meta, so the users can restore the original image later.
         * When copying the site attachments to a new site with MLP and "Based On Site" option we also need to check the
         * backup files and copy them as well, so in a new site the users will be also able to restore the original images.
         *
         * @param int $postId The attachment post ID
         * @return string The backed up file.
         */
        protected function backupFile(int $postId): string
        {
        }
    }
    /**
     * Class DataBaseDataReplacer
     * @package Inpsyde\MultilingualPress\Attachment
     */
    class DatabaseDataReplacer
    {
        use \Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
        public const FILTER_TABLES = 'multilingualpress.database_data_replacer_tables';
        /**
         * UrlDataBaseReplacer constructor.
         * @param wpdb $wpdb
         * @param TableStringReplacer $tableStringReplacer
         * @param BasePathAdapter $basePathAdapter
         */
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Framework\Database\TableStringReplacer $tableStringReplacer, \Inpsyde\MultilingualPress\Framework\BasePathAdapter $basePathAdapter)
        {
        }
        /**
         * Updates attachment URLs according to the given arguments.
         *
         * @param int $sourceSiteId
         * @param int $targetSiteId
         */
        public function replaceUrlsForSites(int $sourceSiteId, int $targetSiteId): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Customizer {
    interface SaveCustomizerDataInterface
    {
        /**
         * @param array $changeSetData
         */
        public function updateCustomizerMenuData(array $changeSetData): void;
    }
    class SaveCustomizerData implements \Inpsyde\MultilingualPress\Customizer\SaveCustomizerDataInterface
    {
        /**
         * if there are language items in the changed data of customizer then update menu item meta values
         * @param array $changeSetData
         */
        public function updateCustomizerMenuData(array $changeSetData): void
        {
        }
        /**
         * Check if there are language items in the changed data of customizer
         *
         * @param array $data customizer's changed data item
         * @return bool
         */
        protected function isLanguageItemExists(array $data): bool
        {
        }
        /**
         * update menu item meta values Which are necessary for passing the proper url when
         * wp_nav_menu_objects will be called in frontend
         *
         * @param array $data customizer's changed language data item
         */
        protected function updateMenuItemMeta(array $data): void
        {
        }
    }
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         * @param Container $container
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi {
    /**
     * Can create helper for translation metabox fields.
     */
    interface MetaboxFieldsHelperFactoryInterface
    {
        /**
         * Creates a new metabox fields helper instance for given site.
         *
         * @param int $siteId The site ID.
         * @return MetaboxFieldsHelperInterface The new helper instance.
         * @throws RuntimeException If problem creating.
         */
        public function createMetaboxFieldsHelper(int $siteId): \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface;
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Post {
    /**
     * Permission checker to be used to either permit or prevent access to posts.
     */
    class RelationshipPermission
    {
        public const FILTER_IS_RELATED_POST_EDITABLE = 'multilingualpress.is_related_post_editable';
        /**
         * @param ContentRelations $contentRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * Checks if the current user can edit (or create) a post in the site with the given ID that is
         * related to given post in the current site.
         *
         * @param \WP_Post $post
         * @param int $relatedSiteId
         * @return bool
         */
        public function isRelatedPostEditable(\WP_Post $post, int $relatedSiteId): bool
        {
        }
    }
    /**
     * Relationship context data object.
     */
    class RelationshipContext
    {
        public const REMOTE_POST_ID = 'remote_post_id';
        public const REMOTE_SITE_ID = 'remote_site_id';
        public const SOURCE_POST_ID = 'source_post_id';
        public const SOURCE_SITE_ID = 'source_site_id';
        protected const DEFAULTS = [self::REMOTE_POST_ID => 0, self::REMOTE_SITE_ID => 0, self::SOURCE_POST_ID => 0, self::SOURCE_SITE_ID => 0];
        /**
         * Returns a new context object, instantiated according to the data in the given context object
         * and the array.
         *
         * @param RelationshipContext $context
         * @param array $data
         * @return RelationshipContext
         */
        public static function fromExistingAndData(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, array $data): \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext
        {
        }
        /**
         * @param array $data
         */
        final public function __construct(array $data = [])
        {
        }
        /**
         * Returns the remote post ID.
         *
         * @return int
         */
        public function remotePostId(): int
        {
        }
        /**
         * Returns the remote site ID.
         *
         * @return int
         */
        public function remoteSiteId(): int
        {
        }
        /**
         * Returns the source post ID.
         *
         * @return int
         */
        public function sourcePostId(): int
        {
        }
        /**
         * Returns the source site ID.
         *
         * @return int
         */
        public function sourceSiteId(): int
        {
        }
        /**
         * Returns the source post object.
         *
         * @return bool
         */
        public function hasRemotePost(): bool
        {
        }
        /**
         * Returns the source post object.
         *
         * @return \WP_Post|null
         */
        public function remotePost(): ?\WP_Post
        {
        }
        /**
         * Returns the source post object.
         *
         * @return \WP_Post
         */
        public function sourcePost(): \WP_Post
        {
        }
        /**
         * Print HTML fields for the relationship context.
         * @param MetaboxFieldsHelper $helper
         */
        public function renderFields(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper): void
        {
        }
    }
    class MetaboxFields
    {
        public const TAB_BASE = 'tab-base';
        public const TAB_EXCERPT = 'tab-excerpt';
        public const TAB_MORE = 'tab-more';
        public const TAB_RELATION = 'tab-relation';
        public const TAB_TAXONOMIES = 'tab-taxonomies';
        public const FIELD_RELATION = 'relationship';
        public const FIELD_RELATION_NEW = 'new';
        public const FIELD_RELATION_EXISTING = 'existing';
        public const FIELD_RELATION_REMOVE = 'remove';
        public const FIELD_RELATION_LEAVE = 'leave';
        public const FIELD_RELATION_NOTHING = 'nothing';
        public const FIELD_RELATION_SEARCH = 'search_post_id';
        public const FIELD_EXCERPT = 'remote-excerpt';
        public const FIELD_TITLE = 'remote-title';
        public const FIELD_SLUG = 'remote-slug';
        public const FIELD_STATUS = 'remote-status';
        public const FIELD_COPY_FEATURED = 'remote-thumbnail-copy';
        public const FIELD_COPY_CONTENT = 'remote-content-copy';
        public const FIELD_COPY_TAXONOMIES = 'remote-taxonomies-copy';
        public const FIELD_TAXONOMIES = 'remote-taxonomies';
        public const FIELD_TAXONOMY_SLUGS = 'remote-taxonomy-slugs';
        public const FIELD_EDIT_LINK = 'edit-link';
        public const FIELD_CHANGED_FIELDS = 'changed-fields';
        public const FILTER_TAXONOMIES_AND_TERMS_OF = 'multilingualpress.taxonomies_and_terms_of';
        public const FILTER_MAX_NUMBER_OF_TERMS = 'multilingualpress.max_number_of_terms';
        public const FILTER_NAME_PREFIX_FOR_POST_METABOX_TAB_FIELDS = 'multilingualpress.translation_ui_metabox_tab';
        /**
         * Get all existing taxonomies for the given post, including all existing terms.
         *
         * @param \WP_Post $post
         * @return object[]
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        public static function taxonomiesAndTermsOf(\WP_Post $post): array
        {
        }
        /**
         * @param RelationshipContext $context
         * @return array
         */
        public function allFieldsTabs(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): array
        {
        }
        /**
         * Will create a new hidden metabox field for detecting changed fields with JS
         *
         * @return MetaboxField
         */
        public function changedFieldsField(): \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxField
        {
        }
        /**
         * Filters the given tab fields.
         *
         * @param string $tabId The tab ID.
         * @param PostMetaboxField[] $fields The fields.
         * @return PostMetaboxField[]
         */
        protected function filterTabFields(string $tabId, array $fields): array
        {
        }
    }
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    class PostModifiedDateFilter implements \Inpsyde\MultilingualPress\Framework\Filter\Filter
    {
        use \Inpsyde\MultilingualPress\Framework\Filter\FilterTrait;
        public function __construct()
        {
        }
        /**
         * @param array $data
         * @param array $postarr
         * @return array
         */
        public function doNotUpdateModifiedDate(array $data, array $postarr): array
        {
        }
    }
    final class Metabox implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metabox
    {
        public const RELATIONSHIP_TYPE = 'post';
        public const ID_PREFIX = 'multilingualpress_post_translation_metabox_';
        public const HOOK_PREFIX = 'multilingualpress.post_translation_metabox_';
        /**
         * @param int $sourceSiteSite
         * @param int $remoteSiteId
         * @param ActivePostTypes $postTypes
         * @param ContentRelations $contentRelations
         * @param RelationshipPermission $relationshipPermission
         */
        public function __construct(int $sourceSiteSite, int $remoteSiteId, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $postTypes, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipPermission $relationshipPermission)
        {
        }
        /**
         * Returns the site ID for the meta box
         *
         * @return int
         */
        public function siteId(): int
        {
        }
        /**
         * @inheritDoc
         */
        public function isValid(\Inpsyde\MultilingualPress\Framework\Entity $entity): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function createInfo(string $showOrSave, \Inpsyde\MultilingualPress\Framework\Entity $entity): \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info
        {
        }
        /**
         * @inheritdoc
         */
        public function view(\Inpsyde\MultilingualPress\Framework\Entity $entity): \Inpsyde\MultilingualPress\Framework\Admin\Metabox\View
        {
        }
        /**
         * @inheritdoc
         */
        public function action(\Inpsyde\MultilingualPress\Framework\Entity $entity): \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
        {
        }
    }
    /**
     * Class MetaboxAction
     */
    final class MetaboxAction implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
    {
        // phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
        public const FILTER_FUNCTION_CREATE_OR_UPDATE_REMOTE_POST = 'multilingualpress.function_create_remote_post';
        public const FILTER_CONTENT_BEFORE_UPDATE_REMOTE_POST = 'multilingualpress.content_before_update_remote_post';
        public const FILTER_TAXONOMIES_SLUGS_BEFORE_REMOVE = 'multilingualpress.taxonomies_slugs_before_remove';
        public const FILTER_NEW_RELATE_REMOTE_POST_BEFORE_INSERT = 'multilingualpress.new_relate_remote_post_before_insert';
        public const ACTION_METABOX_AFTER_RELATE_POSTS = 'multilingualpress.metabox_after_relate_posts';
        public const ACTION_METABOX_BEFORE_UPDATE_REMOTE_POST = 'multilingualpress.metabox_before_update_remote_post';
        public const ACTION_METABOX_AFTER_UPDATE_REMOTE_POST = 'multilingualpress.metabox_after_update_remote_post';
        /**
         * @param MetaboxFields $fields
         * @param MetaboxFieldsHelper $fieldsHelper
         * @param RelationshipContext $relationshipContext
         * @param ActivePostTypes $postTypes
         * @param ContentRelations $contentRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields $fields, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $fieldsHelper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $postTypes, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @inheritdoc
         */
        public function save(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices): bool
        {
        }
    }
    class SourcePostSaveContext
    {
        public const POST_TYPE = 'real_post_type';
        public const POST_ID = 'real_post_id';
        public const POST = 'post';
        public const POST_STATUS = 'original_post_status';
        public const FEATURED_IMG_PATH = 'featured_image_path';
        public const CONNECTABLE_STATUSES = ['auto-draft', 'draft', 'future', 'private', 'publish'];
        /**
         * @param WP_Post $sourcePost
         * @param ActivePostTypes $postTypes
         * @param Request $request
         */
        public function __construct(\WP_Post $sourcePost, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $postTypes, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * @return string
         */
        public function postType(): string
        {
        }
        /**
         * @return string
         */
        public function postStatus(): string
        {
        }
    }
    class MetaboxTab implements \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFillable
    {
        public const ACTION_AFTER_TRANSLATION_UI_TAB = 'multilingualpress.after_translation_ui_tab';
        public const ACTION_BEFORE_TRANSLATION_UI_TAB = 'multilingualpress.before_translation_ui_tab';
        public const FILTER_TRANSLATION_UI_SHOW_TAB = 'multilingualpress.translation_ui_show_tab';
        public const FILTER_PREFIX_TAB_FIELDS = 'multilingualpress.translation_ui.post.tab_fields';
        /**
         * @param string $id
         * @param string $label
         * @param MetaboxField ...$fields
         */
        public function __construct(string $id, string $label, \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxField ...$fields)
        {
        }
        /**
         * @return string
         */
        public function id(): string
        {
        }
        /**
         * @return string
         */
        public function label(): string
        {
        }
        /**
         * @return MetaboxField[]
         */
        public function fields(): array
        {
        }
        /**
         * @param RelationshipContext $relationshipContext
         * @return bool
         */
        public function enabled(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): bool
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): void
        {
        }
    }
    final class MetaboxView implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\View
    {
        /**
         * @param MetaboxFields $fields
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         * @param ChangedFields $fieldsAreChangedInput
         */
        public function __construct(\Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields $fields, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext, \Inpsyde\MultilingualPress\TranslationUi\Post\Field\ChangedFields $fieldsAreChangedInput)
        {
        }
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function render(\Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info $info): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Post\Field {
    class Relation
    {
        protected const VALUES = [\Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields::FIELD_RELATION_NEW, \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields::FIELD_RELATION_EXISTING, \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields::FIELD_RELATION_REMOVE, \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields::FIELD_RELATION_LEAVE];
        public const PREFIX_FOR_RELATION_MESSAGE_FILTER = 'multilingualpress.translation_ui.relation_message_';
        public const ACTION_BEFORE_CONNECTION_INFO = 'multilingualpress.translation_ui.relation_before_connection_info';
        public const FILTER_FORCE_CREATE_RELATIONS = 'multilingualpress.force_create_post_relations';
        /**
         * @param mixed $value
         * @return string
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value): string
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param string $language
         * @param RelationshipContext $context
         */
        protected function newPostField(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, string $language, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): void
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param string $language
         * @param RelationshipContext $context
         */
        protected function existingPostField(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, string $language, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): void
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param string $language
         */
        protected function removeConnectionField(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, string $language): void
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        protected function leaveConnectionField(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): void
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @return void
         */
        protected function searchRow(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper): void
        {
        }
        /**
         * @return void
         */
        protected function buttonRow(): void
        {
        }
        /**
         * Determines if a relation should be created.
         * Returns true only if relations are enabled via filter and no remote post exists.
         *
         * @param RelationshipContext $context
         * @return bool
         */
        protected function shouldCreateRelationAutomatically(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): bool
        {
        }
    }
    class EditLink
    {
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): void
        {
        }
    }
    class ChangedFields implements \Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback
    {
        protected const FILTER_FIELD_FIELDS_ARE_CHANGED = 'multilingualpress.field_changed_fields';
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class CopyFeaturedImage
    {
        public const FILTER_COPY_FEATURED_IMAGE_IS_CHECKED = 'multilingualpress.copy_featured_image_is_checked';
        /**
         * @param mixed $value
         * @return string
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value): string
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class TaxonomySlugs
    {
        public const FILTER_FIELD_TAXONOMY_SLUGS = 'multilingualpress.field_taxonomy_slugs';
        /**
         * @param mixed $value
         * @return array
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value): array
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class CopyTaxonomies
    {
        public const FILTER_COPY_TAXONOMIES_IS_CHECKED = 'multilingualpress.copy_taxonomies_is_checked';
        /**
         * @param mixed $value
         * @return string
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value): string
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class Excerpt
    {
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class CopyContent
    {
        public const FILTER_COPY_CONTENT_IS_CHECKED = 'multilingualpress.copy_content_is_checked';
        /**
         * @param mixed $value
         * @return string
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value): string
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class Status
    {
        public const FILTER_TRANSLATION_UI_POST_STATUSES = 'multilingualpress.translation_ui_post_statuses';
        protected static array $statues = [];
        /**
         * @param mixed $value
         * @return string
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value): string
        {
        }
        /**
         * @return array
         */
        protected static function statuses(): array
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    class Base
    {
        /**
         * Relation constructor.
         * @param string $key
         */
        public function __construct(string $key)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context)
        {
        }
    }
    /**
     * A Walker_Category_Checklist to use radio instead of checkboxes when necessary, and to replace
     * the input name attribute and the category id attribute.
     *
     * @package Inpsyde\MultilingualPress\TranslationUi\Post\Field
     * @psalm-suppress PropertyNotSetInConstructor
     */
    class TaxonomyWalker extends \Walker_Category_Checklist
    {
        /**
         * @param string $name
         * @param string $type
         * @param int $siteId
         */
        public function __construct(string $name, string $type, int $siteId)
        {
        }
        /**
         * @param string $output
         * @param \WP_Term $category
         * @param int $depth
         * @param array $args
         * @param int $id
         *
         * phpcs:disable
         * @psalm-suppress ParamNameMismatch
         */
        public function start_el(&$output, $category, $depth = 0, $args = [], $id = 0): void
        {
        }
    }
    class Taxonomies
    {
        public const FILTER_SINGLE_TERM_TAXONOMIES = 'multilingualpress.single_term_taxonomies';
        public const FILTER_TRANSLATION_UI_SELECT_THRESHOLD = 'multilingualpress.translation_ui_select_threshold';
        public const FILTER_TRANSLATION_UI_USE_SELECT = 'multilingualpress.translation_ui_taxonomies_use_select';
        /**
         * @param mixed $value
         * @return array<string, int[]>
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value): array
        {
        }
        /**
         * @param WP_Taxonomy $taxonomy
         * @param \WP_Term ...$terms
         */
        public function __construct(\WP_Taxonomy $taxonomy, \WP_Term ...$terms)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Post {
    final class MetaboxField implements \Inpsyde\MultilingualPress\TranslationUi\Post\PostMetaboxField
    {
        public const ACTION_AFTER_TRANSLATION_UI_FIELD = 'multilingualpress.after_translation_ui_field';
        public const ACTION_BEFORE_TRANSLATION_UI_FIELD = 'multilingualpress.before_translation_ui_field';
        public const FILTER_TRANSLATION_UI_SHOW_FIELD = 'multilingualpress.translation_ui_show_field';
        /**
         * @param string $key
         * @psalm-param callable(MetaboxFieldsHelper, RelationshipContext): void $renderCallback
         * @param callable $renderCallback
         * @param callable|null $sanitizer
         */
        public function __construct(string $key, callable $renderCallback, callable $sanitizer = null)
        {
        }
        /**
         * @return string
         */
        public function key(): string
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): void
        {
        }
        /**
         * @param Request $request
         * @param MetaboxFieldsHelper $helper
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function requestValue(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper)
        {
        }
        /**
         * @param RelationshipContext $relationshipContext
         * @return bool
         */
        public function enabled(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $relationshipContext): bool
        {
        }
    }
    /**
     * Class TableList
     * @package Inpsyde\MultilingualPress\TranslationUi\Post
     */
    class TableList
    {
        protected const RELATION_TYPE = 'post';
        protected const EDIT_TRANSLATIONS_COLUMN_NAME = 'translations';
        public const FILTER_SITE_LANGUAGE_TAG = 'multilingualpress.site_language_tag';
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Api\ContentRelationsValidator $contentRelationsValidator)
        {
        }
        public function editTranslationColumns(array $postsColumns): array
        {
        }
        public function editTranslationLinks(string $columnName, int $postId): void
        {
        }
    }
    class PostRelationSaveHelper
    {
        public const FILTER_METADATA = 'multilingualpress.post_meta_data';
        public const FILTER_SYNC_KEYS = 'multilingualpress.sync_post_meta_keys';
        public const ACTION_BEFORE_SAVE_RELATIONS = 'multilingualpress.before_save_posts_relations';
        public const ACTION_AFTER_SAVED_RELATIONS = 'multilingualpress.after_saved_posts_relations';
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @param RelationshipContext $context
         * @return int
         */
        public function relatedPostParent(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): int
        {
        }
        /**
         * Set the source id of the element.
         *
         * @param RelationshipContext $context
         * @return bool
         */
        public function relatePosts(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): bool
        {
        }
        /**
         * @param RelationshipContext $context
         * @param Request $request
         */
        public function syncMetadata(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context, \Inpsyde\MultilingualPress\Framework\Http\Request $request): void
        {
        }
        /**
         * @param RelationshipContext $context
         * @return bool
         */
        public function syncThumb(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): bool
        {
        }
        /**
         * Sync terms from source post to remote post.
         *
         * @param RelationshipContext $context
         * @return bool
         */
        public function syncTaxonomyTerms(\Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Post\Ajax {
    /**
     * Multilingualpress Relationship Ajax Updater for Posts
     */
    class RelationshipUpdater
    {
        public const ACTION = 'multilingualpress_update_post_relationship';
        protected const TASK_PARAM = 'task';
        protected const TASK_METHOD_MAP = [\Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields::FIELD_RELATION_EXISTING => 'connectExistingPost', \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields::FIELD_RELATION_REMOVE => 'disconnectPost', \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields::FIELD_RELATION_NEW => 'newRelationPost'];
        /**
         * @param Request $request
         * @param ContextBuilder $contextBuilder
         * @param ContentRelations $contentRelations
         * @param ActivePostTypes $postTypes
         * @param RelationshipPermission $relationshipPermission
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\Post\Ajax\ContextBuilder $contextBuilder, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $postTypes, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipPermission $relationshipPermission)
        {
        }
        /**
         * Handle AJAX request.
         *
         * @see RelationshipUpdater::connectExistingPost()
         * @see RelationshipUpdater::disconnectPost()
         * @see RelationshipUpdater::newRelationPost()
         */
        public function handle(): void
        {
        }
    }
    class Term
    {
        public const ACTION = 'multilingualpress_remote_terms';
        protected const TAXONOMIES = 'taxonomies';
        /**
         * @param Request $request
         * @param ContextBuilder $contextBuilder
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\Post\Ajax\ContextBuilder $contextBuilder)
        {
        }
        /**
         * Handle AJAX request.
         */
        public function handle(): void
        {
        }
        /**
         * The Method is used to return current editing post taxonomy name from request
         *
         * @return array Taxonomy name of current editing post
         */
        protected function taxNameFromRequest(): array
        {
        }
    }
    /**
     * @psalm-type postId = int
     * @psalm-type title = string
     */
    class Search
    {
        use \Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
        public const ACTION = 'multilingualpress_remote_post_search';
        public const FILTER_REMOTE_ARGUMENTS = 'multilingualpress.remote_post_search_arguments';
        protected \Inpsyde\MultilingualPress\Framework\Repository\PostRepository $postRepository;
        protected \Inpsyde\MultilingualPress\Framework\Repository\WpQueryArgsBuilder $wpQueryArgsBuilder;
        protected string $alreadyConnectedNotice;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Repository\PostRepository $postRepository, \Inpsyde\MultilingualPress\Framework\Repository\WpQueryArgsBuilder $wpQueryArgsBuilder, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\Post\Ajax\ContextBuilder $contextBuilder, string $alreadyConnectedNotice)
        {
        }
        /**
         * Handle AJAX request.
         */
        public function handle(): void
        {
        }
        /**
         * Finds posts by given search query.
         *
         * @param string $searchQuery The search query.
         * @param RelationshipContext $context
         * @return array<int, string> A map of post ID to post title.
         * @psalm-return array<postId, title>
         * @throws NonexistentTable
         */
        protected function findPosts(string $searchQuery, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): array
        {
        }
        /**
         * Prepares the arguments for a query.
         *
         * @param string $searchQuery
         * @param RelationshipContext $context
         * @return array
         */
        protected function prepareArgs(string $searchQuery, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): array
        {
        }
        /**
         * Creates a map of post IDs to their titles
         *
         * @param \WP_Post[] $posts
         * @return array<int, string> A map of post ID to post title.
         * @psalm-return array<postId, title>
         * @throws NonexistentTable
         */
        protected function postIdsToTitlesMap(array $posts, int $sourceSiteId): array
        {
        }
    }
    class ContextBuilder
    {
        protected const SOURCE_SITE_PARAM = 'source_site_id';
        protected const SOURCE_POST_PARAM = 'source_post_id';
        protected const REMOTE_SITE_PARAM = 'remote_site_id';
        protected const REMOTE_POST_PARAM = 'remote_post_id';
        /**
         * @param Request $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * @return RelationshipContext
         */
        public function build(): \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Term {
    /**
     * Permission checker to be used to either permit or prevent access to terms.
     */
    class RelationshipPermission
    {
        public const FILTER_IS_RELATED_TERM_EDITABLE = 'multilingualpress.is_related_term_editable';
        /**
         * @param ContentRelations $contentRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * Checks if the current user can edit (or create) a term in the site with the given ID that is
         * related to given term in the current site.
         *
         * @param \WP_Term $sourceTerm
         * @param int $remoteSiteId
         * @return bool
         */
        public function isRelatedTermEditable(\WP_Term $sourceTerm, int $remoteSiteId): bool
        {
        }
    }
    class TermRelationSaveHelper
    {
        public const FILTER_METADATA = 'multilingualpress.term_meta_data';
        public const FILTER_SYNC_META_KEYS = 'multilingualpress.sync_term_meta_keys';
        public const ACTION_BEFORE_SAVE_RELATIONS = 'multilingualpress.before_save_terms_relations';
        public const ACTION_AFTER_SAVED_RELATIONS = 'multilingualpress.after_saved_terms_relations';
        /**
         * @param ContentRelations $contentRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * Finds the related term parent ID.
         *
         * @param RelationshipContext $context
         * @param int $sourceParentId The source parent ID.
         * @return int The related parent ID.
         * @throws NonexistentTable
         */
        public function relatedTermParent(\Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context, int $sourceParentId): int
        {
        }
        /**
         * @param RelationshipContext $context
         * @return bool
         */
        public function relateTerms(\Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context): bool
        {
        }
        /**
         * @param RelationshipContext $context
         * @param Request $request
         */
        public function syncMetadata(\Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context, \Inpsyde\MultilingualPress\Framework\Http\Request $request): void
        {
        }
    }
    /**
     * Relationship context data object.
     */
    class RelationshipContext
    {
        public const REMOTE_TERM_ID = 'remote_term_id';
        public const REMOTE_SITE_ID = 'remote_site_id';
        public const SOURCE_TERM_ID = 'source_term_id';
        public const SOURCE_SITE_ID = 'source_site_id';
        protected const DEFAULTS = [self::REMOTE_TERM_ID => 0, self::REMOTE_SITE_ID => 0, self::SOURCE_TERM_ID => 0, self::SOURCE_SITE_ID => 0];
        /**
         * Returns a new context object, instantiated according to the data in the given context object
         * and the array.
         *
         * @param RelationshipContext $context
         * @param array $data
         * @return RelationshipContext
         */
        public static function fromExistingAndData(\Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context, array $data): \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext
        {
        }
        /**
         * @param array $data
         */
        final public function __construct(array $data = [])
        {
        }
        /**
         * @return int
         */
        public function remoteTermId(): int
        {
        }
        /**
         * @return int
         */
        public function remoteSiteId(): int
        {
        }
        /**
         * @return int
         */
        public function sourceTermId(): int
        {
        }
        /**
         * @return int
         */
        public function sourceSiteId(): int
        {
        }
        /**
         * @return bool
         */
        public function hasRemoteTerm(): bool
        {
        }
        /**
         * @return WP_Term|null
         */
        public function remoteTerm(): ?\WP_Term
        {
        }
        /**
         * @return WP_Term
         */
        public function sourceTerm(): \WP_Term
        {
        }
        /**
         * Print HTML fields for the relationship context.
         * @param MetaboxFieldsHelper $helper
         */
        public function renderFields(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper): void
        {
        }
    }
    class MetaboxFields
    {
        public const TAB_RELATION = 'tab-relation';
        public const TAB_DATA = 'tab-data';
        public const FIELD_RELATION = 'relationship';
        public const FIELD_RELATION_NEW = 'new';
        public const FIELD_RELATION_EXISTING = 'existing';
        public const FIELD_RELATION_REMOVE = 'remove';
        public const FIELD_RELATION_LEAVE = 'leave';
        public const FIELD_RELATION_NOTHING = 'nothing';
        public const FIELD_RELATION_SEARCH = 'search_term_id';
        public const FIELD_NAME = 'remote-name';
        public const FIELD_SLUG = 'remote-slug';
        public const FIELD_DESCRIPTION = 'remote-description';
        public const FIELD_PARENT = 'remote-parent';
        /**
         * @return array
         */
        public function allFieldsTabs(): array
        {
        }
    }
    final class Metabox implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Metabox
    {
        public const RELATIONSHIP_TYPE = 'term';
        public const ID_PREFIX = 'multilingualpress_term_translation_metabox_';
        public const HOOK_PREFIX = 'multilingualpress_.term_translation_metabox_';
        /**
         * @param int $sourceSiteSite
         * @param int $remoteSiteId
         * @param ActiveTaxonomies $taxonomies
         * @param ContentRelations $contentRelations
         * @param RelationshipPermission $relationshipPermission
         */
        public function __construct(int $sourceSiteSite, int $remoteSiteId, \Inpsyde\MultilingualPress\Core\Entity\ActiveTaxonomies $taxonomies, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipPermission $relationshipPermission)
        {
        }
        /**
         * Returns the site ID for the meta box.
         * @return int
         */
        public function siteId(): int
        {
        }
        /**
         * @inheritDoc
         */
        public function isValid(\Inpsyde\MultilingualPress\Framework\Entity $entity): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function createInfo(string $showOrSave, \Inpsyde\MultilingualPress\Framework\Entity $entity): \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info
        {
        }
        /**
         * @inheritdoc
         */
        public function view(\Inpsyde\MultilingualPress\Framework\Entity $entity): \Inpsyde\MultilingualPress\Framework\Admin\Metabox\View
        {
        }
        /**
         * @inheritdoc
         */
        public function action(\Inpsyde\MultilingualPress\Framework\Entity $entity): \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
        {
        }
    }
    final class MetaboxAction implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\Action
    {
        public const ACTION_METABOX_AFTER_RELATE_TERMS = 'multilingualpress.metabox_after_relate_terms';
        public const ACTION_BEFORE_UPDATE_REMOTE_TERM = 'multilingualpress.metabox_before_update_remote_term';
        public const ACTION_AFTER_UPDATE_REMOTE_TERM = 'multilingualpress.metabox_after_update_remote_term';
        /**
         * @param MetaboxFields $fields
         * @param MetaboxFieldsHelper $fieldsHelper
         * @param RelationshipContext $relationshipContext
         * @param ActiveTaxonomies $taxonomies
         * @param ContentRelations $contentRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields $fields, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $fieldsHelper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $relationshipContext, \Inpsyde\MultilingualPress\Core\Entity\ActiveTaxonomies $taxonomies, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @inheritdoc
         */
        public function save(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices $notices): bool
        {
        }
    }
    class MetaboxTab
    {
        public const ACTION_AFTER_TRANSLATION_UI_TAB = 'multilingualpress.after_translation_ui_tab';
        public const ACTION_BEFORE_TRANSLATION_UI_TAB = 'multilingualpress.before_translation_ui_tab';
        public const FILTER_TRANSLATION_UI_SHOW_TAB = 'multilingualpress.translation_ui_show_tab';
        public const FILTER_PREFIX_TAB_FIELDS = 'multilingualpress.translation_ui.term.tab_fields';
        /**
         * @param string $id
         * @param string $label
         * @param MetaboxField ...$fields
         */
        public function __construct(string $id, string $label, \Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxField ...$fields)
        {
        }
        /**
         * @return string
         */
        public function id(): string
        {
        }
        /**
         * @return string
         */
        public function label(): string
        {
        }
        /**
         * @return MetaboxField[]
         */
        public function fields(): array
        {
        }
        /**
         * @param RelationshipContext $relationshipContext
         * @return bool
         */
        public function enabled(\Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $relationshipContext): bool
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $relationshipContext): void
        {
        }
    }
    final class MetaboxView implements \Inpsyde\MultilingualPress\Framework\Admin\Metabox\View
    {
        /**
         * @param MetaboxFields $fields
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         */
        public function __construct(\Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields $fields, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $relationshipContext)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(\Inpsyde\MultilingualPress\Framework\Admin\Metabox\Info $info): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Term\Field {
    class Relation
    {
        protected const VALUES = [\Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields::FIELD_RELATION_NEW, \Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields::FIELD_RELATION_EXISTING, \Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields::FIELD_RELATION_REMOVE, \Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields::FIELD_RELATION_LEAVE];
        protected const PREFIX_FOR_TERM_RELATION_MESSAGE_FILTER = 'multilingualpress.term.translation_ui.relation_message_';
        public const FILTER_FORCE_CREATE_RELATIONS = 'multilingualpress.force_create_term_relations';
        /**
         * @param mixed $value
         * @return string
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value): string
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         * @throws NonexistentTable
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param string $language
         * @param RelationshipContext $context
         */
        protected function newTermField(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, string $language, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context): void
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param string $language
         */
        protected function existingTermField(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, string $language): void
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param string $language
         */
        protected function removeConnectionField(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, string $language): void
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        protected function leaveConnectionField(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context): void
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @return void
         */
        protected function searchRow(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper): void
        {
        }
        /**
         * @return void
         */
        protected function buttonRow(): void
        {
        }
        /**
         * Determines if a relation should be created.
         * Returns true only if relations are enabled via filter and no remote term exists.
         *
         * @param RelationshipContext $context
         * @return bool
         */
        protected function shouldCreateRelationAutomatically(\Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context): bool
        {
        }
    }
    class Base
    {
        /**
         * Relation constructor.
         * @param string $key
         */
        public function __construct(string $key)
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context)
        {
        }
    }
    class Description
    {
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context)
        {
        }
    }
    class ParentTerm
    {
        /**
         * @param mixed $value
         * @return int
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public static function sanitize($value): int
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $context
         */
        public function __invoke(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Term {
    class MetaboxField
    {
        public const ACTION_AFTER_TRANSLATION_UI_FIELD = 'multilingualpress.after_translation_ui_field';
        public const ACTION_BEFORE_TRANSLATION_UI_FIELD = 'multilingualpress.before_translation_ui_field';
        public const FILTER_TRANSLATION_UI_SHOW_FIELD = 'multilingualpress.translation_ui_show_field';
        /**
         * @param string $key
         * @param callable $renderCallback
         * @param callable|null $sanitizer
         */
        public function __construct(string $key, callable $renderCallback, callable $sanitizer = null)
        {
        }
        /**
         * @return string
         */
        public function key(): string
        {
        }
        /**
         * @param MetaboxFieldsHelper $helper
         * @param RelationshipContext $relationshipContext
         */
        public function render(\Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $relationshipContext): void
        {
        }
        /**
         * @param Request $request
         * @param MetaboxFieldsHelper $helper
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function requestValue(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper $helper)
        {
        }
        /**
         * @param RelationshipContext $relationshipContext
         * @return bool
         */
        public function enabled(\Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $relationshipContext): bool
        {
        }
    }
    class TableList
    {
        protected const RELATION_TYPE = 'term';
        protected const EDIT_TRANSLATIONS_COLUMN_NAME = 'translations';
        protected const FILTER_SITE_LANGUAGE_TAG = 'multilingualpress.site_language_tag';
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Api\ContentRelationsValidator $contentRelationsValidator)
        {
        }
        public function editTranslationColumns(array $postsColumns): array
        {
        }
        // phpcs:ignore Inpsyde.CodeQuality.FunctionLength.TooLong, Generic.Metrics.CyclomaticComplexity.TooHigh
        public function editTranslationLinks(string $content, string $columnName, int $termId): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi\Term\Ajax {
    class RelationshipUpdater
    {
        public const ACTION = 'multilingualpress_update_term_relationship';
        protected const TASK_PARAM = 'task';
        protected const TASK_METHOD_MAP = [\Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields::FIELD_RELATION_EXISTING => 'connectExistingTerm', \Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields::FIELD_RELATION_REMOVE => 'disconnectTerm'];
        /**
         * @param Request $request
         * @param ContextBuilder $contextBuilder
         * @param ContentRelations $contentRelations
         * @param ActiveTaxonomies $taxonomies
         * @param RelationshipPermission $relationshipPermission
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\Term\Ajax\ContextBuilder $contextBuilder, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Core\Entity\ActiveTaxonomies $taxonomies, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipPermission $relationshipPermission)
        {
        }
        /**
         * Handle AJAX request.
         *
         * @see RelationshipUpdater::connectExistingTerm()
         * @see RelationshipUpdater::disconnectTerm()
         */
        public function handle(): void
        {
        }
    }
    /**
     * @psalm-type termTaxonomyId = int
     * @psalm-type title = string
     */
    class Search
    {
        public const ACTION = 'multilingualpress_remote_term_search_arguments';
        public const FILTER_REMOTE_ARGUMENTS = 'multilingualpress.remote_term_search_arguments';
        protected string $alreadyConnectedNotice;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\TranslationUi\Term\Ajax\ContextBuilder $contextBuilder, string $alreadyConnectedNotice)
        {
        }
        /**
         * Handle AJAX request.
         */
        public function handle(): void
        {
        }
        /**
         * Finds the term by given search query.
         *
         * @param string $searchQuery The search query.
         * @param RelationshipContext $context
         * @return array<string, int|string>[] A map of term ID to term title.
         * @psalm-return array<string, termTaxonomyId|title>[]
         * @throws NonexistentTable
         */
        public function findTerm(string $searchQuery, \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext $context): array
        {
        }
        /**
         * Checks if the term with given term taxonomy ID is connected to any term from given site ID.
         *
         * @param int $termTaxonomyId The term taxonomy ID.
         * @param int $siteId The site ID.
         * @return bool true if is connected, otherwise false.
         * @throws NonexistentTable
         */
        protected function isConnectedWithTermOfSite(int $termTaxonomyId, int $siteId): bool
        {
        }
    }
    class ContextBuilder
    {
        protected const SOURCE_SITE_PARAM = 'source_site_id';
        protected const SOURCE_TERM_PARAM = 'source_term_id';
        protected const REMOTE_SITE_PARAM = 'remote_site_id';
        protected const REMOTE_TERM_PARAM = 'remote_term_id';
        /**
         * @param Request $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * @return RelationshipContext
         */
        public function build(): \Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\TranslationUi {
    interface MetaboxFieldsHelperInterface
    {
        /**
         * Create the field ID from field key.
         *
         * @param string $fieldKey The field key.
         * @return string The field id.
         */
        public function fieldId(string $fieldKey): string;
        /**
         * Create the field name from field key.
         *
         * @param string $fieldKey The field key.
         * @return string The field name.
         */
        public function fieldName(string $fieldKey): string;
        /**
         * Get the value of a given field key from a given request.
         *
         * @param Request $request
         * @param string $fieldKey The field key.
         * @param null $default The default value.
         * @return mixed The request value.
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function fieldRequestValue(\Inpsyde\MultilingualPress\Framework\Http\Request $request, string $fieldKey, $default = null);
    }
    class MetaboxFieldsHelper implements \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface
    {
        public const NAME_PREFIX = 'multilingualpress';
        public const ID_PREFIX = 'multilingualpress-';
        /**
         * @param int $siteId
         */
        public function __construct(int $siteId)
        {
        }
        /**
         * @param string $fieldKey
         * @return string
         */
        public function fieldId(string $fieldKey): string
        {
        }
        /**
         * @param string $fieldKey
         * @return string
         */
        public function fieldName(string $fieldKey): string
        {
        }
        /**
         * @param Request $request
         * @param string $fieldKey
         * @param null $default
         * @return mixed
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        public function fieldRequestValue(\Inpsyde\MultilingualPress\Framework\Http\Request $request, string $fieldKey, $default = null)
        {
        }
    }
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        public const CONFIGURATION_NAME_FOR_ALREADY_CONNECTED_ENTITY_NOTICE = 'multilingualpress.TranslationUi.AlreadyConnectedEntityNotice';
        public const FILTER_NAME_FOR_ALREADY_CONNECTED_ENTITY_NOTICE = 'multilingualpress.TranslationUi.already_connected_entity_notice';
        public const FILTER_AJAX_SEARCH_POSTS_HOOKS = 'multilingualpress.included_search_hooks';
        /**
         * @inheritdoc
         * @param Container $container
         * @throws NameOverwriteNotAllowed
         * @throws WriteAccessOnLockedContainer
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         * @param Container $container
         * @throws Throwable
         * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
    class MetaboxFieldsHelperFactory implements \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperFactoryInterface
    {
        /**
         * @inheritDoc
         */
        public function createMetaboxFieldsHelper(int $siteId): \Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelperInterface
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Activation {
    /**
     * Activator implementation using a network option.
     */
    class Activator
    {
        public const OPTION = 'multilingualpress_activation';
        /**
         * Takes care of pending plugin activation tasks.
         *
         * @return bool
         */
        public function handlePendingActivation(): bool
        {
        }
        /**
         * Performs anything to handle the plugin activation.
         *
         * @return bool
         */
        public function handleActivation(): bool
        {
        }
        /**
         * Registers the given callback.
         *
         * @param callable $callback
         * @param bool $prepend
         * @return Activator
         */
        public function registerCallback(callable $callback, bool $prepend = \false): \Inpsyde\MultilingualPress\Activation\Activator
        {
        }
    }
    /**
     * Service provider for all activation objects.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider
    {
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function integrate(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Content\CoreBlockHandlers {
    interface BlockHandler extends \Inpsyde\MultilingualPress\Framework\Stringable
    {
        /**
         * Handles the block processing based on the provided context.
         */
        public function handle(array $block, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): array;
    }
    /**
     * Handles the processing of core/image block by copying attachments and replacing
     * URLs in the block data.
     */
    class CoreImageBlock implements \Inpsyde\MultilingualPress\Content\CoreBlockHandlers\BlockHandler
    {
        public function __construct(\Inpsyde\MultilingualPress\Framework\Content\AttachmentCopier $attachmentCopier)
        {
        }
        public function __toString(): string
        {
        }
        /**
         * Handles the core/image block by processing attachment replacement.
         * Returns the updated block data with new attachment IDs and URLs.
         */
        public function handle(array $block, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): array
        {
        }
        /**
         * Processes the image import and replacement between source and remote sites.
         */
        protected function processImageImportAndReplace(array $block, int $sourceImageId, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): ?array
        {
        }
        /**
         * Replaces image URLs and IDs in the block content with the corresponding remote values.
         */
        protected function replaceImageUrlsAndIds(array $block, \Inpsyde\MultilingualPress\Framework\Content\Attachment $sourceAttachment, \Inpsyde\MultilingualPress\Framework\Content\Attachment $remoteAttachment): array
        {
        }
        /**
         * Builds the replacements for URLs and IDs between source and remote attachments.
         */
        protected function buildReplacements(\Inpsyde\MultilingualPress\Framework\Content\Attachment $sourceAttachment, \Inpsyde\MultilingualPress\Framework\Content\Attachment $remoteAttachment, array $block): array
        {
        }
        /**
         * Builds link replacements for href attributes based on link destination.
         */
        protected function buildLinkReplacements(\Inpsyde\MultilingualPress\Framework\Content\Attachment $sourceAttachment, \Inpsyde\MultilingualPress\Framework\Content\Attachment $remoteAttachment, string $destination): array
        {
        }
        /**
         * Performs the replacements for URLs and IDs in the block data.
         */
        protected function performReplacements(array $block, array $replacements): array
        {
        }
    }
    /**
     * Handles the processing of the "core/gallery" block.
     * Extends the functionality of the CoreImageBlock to manage a gallery of images.
     */
    class CoreGalleryBlock extends \Inpsyde\MultilingualPress\Content\CoreBlockHandlers\CoreImageBlock
    {
        protected const BLOCK_NAME = 'core/gallery';
        /**
         * Processes the gallery block and its inner image blocks.
         */
        public function handle(array $block, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): array
        {
        }
    }
    /**
     * Handles the processing of "core/file" blocks by copying attachments and replacing
     * URLs in the block data.
     */
    class CoreFileBlock implements \Inpsyde\MultilingualPress\Content\CoreBlockHandlers\BlockHandler
    {
        /** @var string */
        protected const BLOCK_NAME = 'core/file';
        public function __construct(\Inpsyde\MultilingualPress\Framework\Content\AttachmentCopier $attachmentCopier)
        {
        }
        public function __toString(): string
        {
        }
        /**
         * Copies the file attachment to the remote site and updates the block's attributes accordingly.
         */
        public function handle(array $block, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): array
        {
        }
        /**
         * Replaces the file URLs and IDs in the block content, inner content, and attributes.
         *
         * phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
         */
        protected function replaceFileUrlsAndIds(array $block, string $sourceFileUrl, string $remoteFileUrl, int $remoteFileId): array
        {
        }
    }
    /**
     * Handles the processing of the "core/audio" block by extending the logic of CoreFileBlock.
     */
    class CoreAudioBlock extends \Inpsyde\MultilingualPress\Content\CoreBlockHandlers\CoreFileBlock
    {
        protected const BLOCK_NAME = 'core/audio';
    }
    /**
     * Handles the processing of the "core/media-text" block.
     * Extends the functionality of the CoreImageBlock to manage media attachments.
     */
    class CoreMediaTextBlock extends \Inpsyde\MultilingualPress\Content\CoreBlockHandlers\CoreImageBlock
    {
        protected const BLOCK_NAME = 'core/media-text';
        /**
         * Handles the processing of a media text block, coping and replacing the media attachment.
         */
        public function handle(array $block, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): array
        {
        }
        /**
         * Replaces image URLs and IDs in block content, attributes, and inner content.
         * Adds specific handling for the 'mediaLink' attribute.
         */
        protected function replaceImageUrlsAndIds(array $block, \Inpsyde\MultilingualPress\Framework\Content\Attachment $sourceAttachment, \Inpsyde\MultilingualPress\Framework\Content\Attachment $remoteAttachment): array
        {
        }
    }
    /**
     * Handles the processing of the 'core/cover' block by extending
     * the functionality of the CoreImageBlock class.
     */
    class CoreCoverBlock extends \Inpsyde\MultilingualPress\Content\CoreBlockHandlers\CoreImageBlock
    {
        protected const BLOCK_NAME = 'core/cover';
        /**
         * Replaces image URLs and IDs in block content, attributes, and inner content.
         * Adds specific handling for the 'url' attribute in cover blocks.
         */
        protected function replaceImageUrlsAndIds(array $block, \Inpsyde\MultilingualPress\Framework\Content\Attachment $sourceAttachment, \Inpsyde\MultilingualPress\Framework\Content\Attachment $remoteAttachment): array
        {
        }
    }
    /**
     * Handles the processing of the "core/video" block.
     * Extends the functionality of the CoreFileBlock to copy a video attachment.
     */
    class CoreVideoBlock extends \Inpsyde\MultilingualPress\Content\CoreBlockHandlers\CoreFileBlock
    {
        protected const BLOCK_NAME = 'core/video';
    }
}
namespace Inpsyde\MultilingualPress\Content {
    /**
     * @template-extends ArrayObject<array-key, BlockHandler>
     */
    class BlockHandlersCollection extends \ArrayObject
    {
    }
    class SpecificBlockParser extends \Inpsyde\MultilingualPress\Framework\Content\BlockParser
    {
        public function __construct(\Inpsyde\MultilingualPress\Content\CoreBlockHandlers\BlockHandler ...$blocks)
        {
        }
        /**
         * Check if a block is a valid specific block.
         *
         * phpcs:disable WordPress.PHP.StrictInArray.MissingTrueStrict
         */
        protected function isBlockValid(array $block): bool
        {
        }
    }
    class SpecificBlockDataCopier implements \Inpsyde\MultilingualPress\Framework\Content\BlockDataCopier
    {
        public function __construct(\Inpsyde\MultilingualPress\Content\BlockHandlersCollection $blockHandlersCollection)
        {
        }
        /**
         * Handles the block data by delegating to the appropriate block handler.
         * Returns the processed block data, or the original block if no handler was found.
         */
        public function handleBlockData(array $block, \Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext $context): array
        {
        }
    }
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        public const FILTER_BLOCK_HANDLERS = 'multilingualpress.block_handlers';
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Language {
    /**
     * Language wrapping data shipped with MLP.
     */
    final class EmbeddedLanguage implements \Inpsyde\MultilingualPress\Framework\Language\Language
    {
        public const KEY_TYPE = 'type';
        public const TYPE_LANGUAGE = 'language';
        /**
         * @param array $jsonData
         * @return EmbeddedLanguage
         */
        //phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
        public static function fromJsonData(array $jsonData): \Inpsyde\MultilingualPress\Framework\Language\Language
        {
        }
        /**
         * @param FrameworkLanguage $language
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Language\Language $language)
        {
        }
        /**
         * @inheritdoc
         */
        public function id(): int
        {
        }
        /**
         * @inheritdoc
         */
        public function isRtl(): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function name(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function englishName(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function nativeName(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function isoName(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function isoCode(string $which = self::ISO_SHORTEST): string
        {
        }
        /**
         * @inheritdoc
         */
        public function bcp47tag(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function locale(): string
        {
        }
        public function type(): string
        {
        }
        public function parentLanguageTag(): string
        {
        }
        /**
         * The method will change the language variant locale from lang_LANG_Variant to lang_LANG
         *
         * @param string $locale of the language variant
         * @return string changed locale for language variant
         */
        public static function changeLanguageVariantLocale(string $locale): string
        {
        }
        /**
         * The method will change the language variant from lang-LANG-Variant to lang-LANG
         *
         * @param string $language of the language variant
         * @return string changed language
         */
        public static function changeLanguageVariant(string $language): string
        {
        }
    }
    /**
     * Basic language data type implementation.
     */
    final class Language implements \Inpsyde\MultilingualPress\Framework\Language\Language
    {
        /**
         * @param array $data
         */
        public function __construct(array $data)
        {
        }
        /**
         * @inheritdoc
         */
        public function id(): int
        {
        }
        /**
         * @inheritdoc
         */
        public function isRtl(): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function name(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function englishName(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function nativeName(): string
        {
        }
        /**
         * Returns the language name.
         *
         * @return string
         */
        public function isoName(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function isoCode(string $which = self::ISO_SHORTEST): string
        {
        }
        /**
         * @inheritdoc
         */
        public function bcp47tag(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function locale(): string
        {
        }
        /**
         * @inheritdoc
         */
        public function type(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Installation {
    /**
     * MultilingualPress installation checker.
     */
    class InstallationChecker
    {
        /**
         * @param SystemChecker $checker
         * @param PluginProperties $properties
         */
        public function __construct(\Inpsyde\MultilingualPress\Installation\SystemChecker $checker, \Inpsyde\MultilingualPress\Framework\PluginProperties $properties)
        {
        }
        /**
         * Checks the installation for compliance with the system requirements and return one of the
         * SystemChecker status flags.
         *
         * @return int
         */
        public function check(): int
        {
        }
    }
    /**
     * Deactivates plugins network-wide by matching (partial) base names against all active plugins.
     */
    class NetworkPluginDeactivator
    {
        /**
         * Deactivates the given plugins network-wide.
         *
         * @param string[] $plugins
         * @return string[]
         */
        public function deactivatePlugins(string ...$plugins): array
        {
        }
    }
    /**
     * MultilingualPress uninstaller.
     */
    class Uninstaller
    {
        public const FILTER_DELETE_PLUGIN_SETTINGS = 'multilingualpress.delete_plugin_settings';
        protected \wpdb $db;
        public function __construct(\wpdb $db)
        {
        }
        /**
         * Uninstalls the given tables.
         *
         * @param string[] $tableNames The list of table names.
         * @return void
         */
        public function uninstallTables(array $tableNames): void
        {
        }
        /**
         * Erases the DB tables entries.
         *
         * @param array $tableNames
         * @return void
         */
        public function truncateTables(array $tableNames): void
        {
        }
        /**
         * Deletes all MultilingualPress network options.
         *
         * @param string[] $options
         * @return int
         */
        public function deleteNetworkOptions(array $options): int
        {
        }
        /**
         * Deletes all MultilingualPress post meta.
         *
         * @param string[] $keys
         * @param int[] $siteIds
         * @return bool
         */
        public function deletePostMeta(array $keys, array $siteIds = []): bool
        {
        }
        /**
         * Deletes all MultilingualPress options for the given (or all) sites.
         *
         * @param string[] $options
         * @param int[] $siteIds
         * @return int
         */
        public function deleteSiteOptions(array $options, array $siteIds = []): int
        {
        }
        /**
         * Deletes all MultilingualPress user meta.
         *
         * @param string[] $keys
         */
        public function deleteUserMeta(array $keys): void
        {
        }
        /**
         * @param array $siteOptions
         * @param array $userMeta
         */
        public function deleteOnboardingData(array $siteOptions, array $userMeta): void
        {
        }
        /**
         * Unschedule all MLP events
         *
         * When the plugin is uninstalled, we need to remove all the scheduled events
         *
         * @param array<string> $events The array of the hook names for which the events should be unscheduled
         */
        public function deleteScheduledEvents(array $events): void
        {
        }
    }
    /**
     * Performs various system-specific checks.
     */
    class SystemChecker
    {
        public const FILTER_FORCE_CHECK = 'multilingualpress.force_system_check';
        public const ACTION_CHECKED_VERSION = 'multilingualpress.checked_version';
        public const WRONG_PAGE_FOR_CHECK = 1;
        public const INSTALLATION_OK = 2;
        public const PLUGIN_DEACTIVATED = 3;
        public const VERSION_OK = 4;
        public const NEEDS_INSTALLATION = 5;
        public const NEEDS_UPGRADE = 6;
        /**
         * @param PluginProperties $pluginProperties
         * @param SiteRelationsChecker $siteRelationsChecker
         * @param SiteSettingsRepository $siteSettingsRepository
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\PluginProperties $pluginProperties, \Inpsyde\MultilingualPress\Installation\SiteRelationsChecker $siteRelationsChecker, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository)
        {
        }
        /**
         * Checks the installation for compliance with the system requirements.
         *
         * @return int
         */
        public function checkInstallation(): int
        {
        }
        /**
         * Checks the installed plugin version.
         *
         * @param SemanticVersionNumber $installedMlpVersion
         * @param SemanticVersionNumber $currentMlpVersion
         * @return int
         */
        public function checkVersion(\Inpsyde\MultilingualPress\Framework\SemanticVersionNumber $installedMlpVersion, \Inpsyde\MultilingualPress\Framework\SemanticVersionNumber $currentMlpVersion): int
        {
        }
        /**
         * Checks if an old version of MLP is installed in the system.
         * @return void
         */
        public function checkLegacyVersion(): void
        {
        }
    }
    /**
     * Deactivates specific plugin.
     */
    class PluginDeactivator
    {
        /**
         * @param string $pluginBaseName
         * @param string $pluginName
         * @param string[] $errors
         */
        public function __construct(string $pluginBaseName, string $pluginName, array $errors = [])
        {
        }
        /**
         * Deactivates the plugin, and renders an according admin notice.
         */
        public function deactivatePlugin(): void
        {
        }
    }
    /**
     * Updates any installed plugin data to the current version.
     */
    class Updater
    {
        /**
         * @param PluginProperties $pluginProperties
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\PluginProperties $pluginProperties)
        {
        }
        /**
         * Updates any installed plugin data to the current version.
         *
         * @param SemanticVersionNumber $installedVersion
         */
        public function update(\Inpsyde\MultilingualPress\Framework\SemanticVersionNumber $installedVersion): void
        {
        }
        /**
         * Will perform the necessary rewrites when the plugin is upgraded.
         *
         * When the plugin is upgraded, we need to fix the permalink rewrites.
         *
         * @see https://developer.wordpress.org/reference/hooks/upgrader_process_complete/ upgrader_process_complete
         *
         * @param \WP_Upgrader $upgraderObject
         * @param array $options
         */
        public function rewriteRulesAfterPluginUpgrade(\WP_Upgrader $upgraderObject, array $options): void
        {
        }
    }
    /**
     * MultilingualPress installer.
     */
    class Installer
    {
        /**
         * @param TableInstaller $tableInstaller
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Database\TableInstaller $tableInstaller)
        {
        }
        /**
         * Installs the given tables.
         *
         * @param Table ...$tables
         * @throws InvalidTable
         */
        public function installTables(\Inpsyde\MultilingualPress\Framework\Database\Table ...$tables): void
        {
        }
    }
    /**
     * Service provider for all Installation objects.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider
    {
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function integrate(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
    class SiteRelationsChecker
    {
        /**
         * @param SiteRelations $siteRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations)
        {
        }
        /**
         * Checks if there are at least two sites related to each other, and renders an admin notice if not.
         *
         * @return bool
         */
        public function checkRelations(): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Translator {
    /**
     * Translator implementation for post types.
     */
    final class PostTypeTranslator implements \Inpsyde\MultilingualPress\Framework\Translator\Translator
    {
        public const FILTER_POST_TYPE_PERMALINK = 'multilingualpress.post_type_permalink';
        public const FILTER_TRANSLATION = 'multilingualpress.filter_post_type_translation';
        public const FILTER_AUTO_TRANSLATE_CPT_SLUG = 'multilingualpress.autoTranslateCptSlug';
        /**
         * @param PostTypeSlugsSettingsRepository $slugsRepository
         * @param UrlFactory $urlFactory
         * @param ActivePostTypes $activePostTypes
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\PostTypeSlugsSettingsRepository $slugsRepository, \Inpsyde\MultilingualPress\Framework\Factory\UrlFactory $urlFactory, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $activePostTypes, \Inpsyde\MultilingualPress\Core\OriginalPostTypeSlugsRepository $originalPostTypeSlugsRepository)
        {
        }
        /**
         * @inheritdoc
         */
        public function translationFor(int $siteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args): \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * Modifies CPT args to include a translated rewrite slug based on the MLP site settings.
         *
         * @wp-hook register_post_type_args
         *
         * @param array $args An array of arguments that will be passed to register_post_type().
         * @param string $postType The name/slug of the post type.
         *
         * @return array Updated arguments.
         */
        public function translateRewrite(array $args, string $postType): array
        {
        }
    }
    trait UrlBlogFragmentTrailingTrait
    {
        /**
         * @param string $string
         * @return string
         */
        private function untrailingBlogIt(string $string): string
        {
        }
        /**
         * @param string $string
         * @return string
         */
        private function trailingBlogIt(string $string): string
        {
        }
    }
    /**
     * Translator implementation for posts.
     */
    final class PostTranslator implements \Inpsyde\MultilingualPress\Framework\Translator\Translator
    {
        public const ACTION_GENERATE_PERMALINK = 'multilingualpress.generate_permalink';
        public const ACTION_GENERATED_PERMALINK = 'multilingualpress.generated_permalink';
        public const FILTER_TRANSLATION = 'multilingualpress.filter_post_translation';
        /**
         * @param PostTypeRepository $postTypeRepository
         * @param PostTypeSlugsSettingsRepository $slugsRepository
         * @param OriginalPostTypeSlugsRepository $originalPostTypeSlugsRepository
         * @param UrlFactory $urlFactory
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\PostTypeRepository $postTypeRepository, \Inpsyde\MultilingualPress\Core\Admin\PostTypeSlugsSettingsRepository $slugsRepository, \Inpsyde\MultilingualPress\Core\OriginalPostTypeSlugsRepository $originalPostTypeSlugsRepository, \Inpsyde\MultilingualPress\Framework\Factory\UrlFactory $urlFactory)
        {
        }
        /**
         * @inheritdoc
         */
        public function translationFor(int $siteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args): \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @wp-hook setup_theme
         *
         * @param WP_Rewrite|null $wp_rewrite
         * @return bool
         */
        public function ensureWpRewrite(\WP_Rewrite $wp_rewrite = null): bool
        {
        }
        /**
         * @param string $key
         * @param callable $function
         */
        public function registerBaseStructureCallback(string $key, callable $function): void
        {
        }
    }
    /**
     * Class DateTranslator
     * @package Inpsyde\MultilingualPress\Translator
     */
    final class DateTranslator implements \Inpsyde\MultilingualPress\Framework\Translator\Translator
    {
        use \Inpsyde\MultilingualPress\Translator\UrlBlogFragmentTrailingTrait;
        /**
         * DateTranslator constructor.
         * @param UrlFactory $urlFactory
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Factory\UrlFactory $urlFactory)
        {
        }
        /**
         * @param int $siteId
         * @param TranslationSearchArgs $args
         * @return Translation
         */
        public function translationFor(int $siteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args): \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @wp-hook setup_theme
         *
         * @param WP|null $wp
         * @return bool
         */
        public function ensureWp(\WP $wp = null): bool
        {
        }
        /**
         * @wp-hook setup_theme
         *
         * @param WP_Rewrite|null $wp_rewrite
         * @return bool
         */
        public function ensureWpRewrite(\WP_Rewrite $wp_rewrite = null): bool
        {
        }
    }
    /**
     * Translator implementation for front-page requests.
     */
    final class HomeTranslator implements \Inpsyde\MultilingualPress\Framework\Translator\Translator
    {
        public const FILTER_TRANSLATION = 'multilingualpress.filter_home_translation';
        /**
         * HomeTranslator constructor.
         * @param UrlFactory $urlFactory
         * @param ContentRelations $contentRelations
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Factory\UrlFactory $urlFactory, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @inheritdoc
         */
        public function translationFor(int $siteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args): \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
    }
    /**
     * Translator implementation for terms.
     */
    class TermTranslator implements \Inpsyde\MultilingualPress\Framework\Translator\Translator
    {
        use \Inpsyde\MultilingualPress\Translator\UrlBlogFragmentTrailingTrait;
        use \Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
        public const FILTER_TAXONOMY_LIST = 'multilingualpress.term_translator_taxonomy_list';
        public const FILTER_TRANSLATION = 'multilingualpress.filter_term_translation';
        public const FILTER_TERM_PUBLIC_URL = 'multilingualpress.filter_term_public_url';
        public const FILTER_AUTO_TRANSLATE_TAXONOMY_SLUG = 'multilingualpress.autoTranslateTaxonomySlug';
        /**
         * @param TaxonomyRepository $taxonomyRepository
         * @param TaxonomySlugsSettingsRepository $slugsRepository
         * @param UrlFactory $urlFactory
         * @param OriginalTaxonomySlugsRepository $originalTaxonomySlugsRepository
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\TaxonomyRepository $taxonomyRepository, \Inpsyde\MultilingualPress\Core\Admin\TaxonomySlugsSettingsRepository $slugsRepository, \Inpsyde\MultilingualPress\Framework\Factory\UrlFactory $urlFactory, \Inpsyde\MultilingualPress\Core\OriginalTaxonomySlugsRepository $originalTaxonomySlugsRepository)
        {
        }
        /**
         * @inheritdoc
         */
        public function translationFor(int $remoteSiteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args): \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * @wp-hook setup_theme
         *
         * @param WP_Rewrite|null $wp_rewrite
         * @return bool
         */
        public function ensureWpRewrite(\WP_Rewrite $wp_rewrite = null): bool
        {
        }
        /**
         * @param string $key
         * @param callable $function
         */
        public function registerBaseStructureCallback(string $key, callable $function): void
        {
        }
        /**
         * @param WP|null $wp
         * @return bool
         */
        public function ensureWp(\WP $wp = null): bool
        {
        }
        /**
         * Returns the translation data for the given term taxonomy ID.
         *
         * @param int $termTaxonomyId
         * @param int $sourceSiteId
         * @param int $remoteSiteId
         * @return array
         */
        protected function translationData(int $termTaxonomyId, int $sourceSiteId, int $remoteSiteId): array
        {
        }
        /**
         * Returns term data according to the given term taxonomy ID.
         *
         * @param int $termTaxonomyId
         * @return array
         */
        protected function termByTermTaxonomyId(int $termTaxonomyId): array
        {
        }
        /**
         * Returns permalink for the given taxonomy term.
         *
         * @param int $termId
         * @param string $taxonomySlug
         * @param int $sourceSiteId
         * @param int $remoteSiteId
         * @return string
         */
        protected function publicUrl(int $termId, string $taxonomySlug, int $sourceSiteId, int $remoteSiteId): string
        {
        }
        /**
         * Updates the global WordPress rewrite instance if it is wrong.
         *
         * @param string $taxonomySlug
         * @return void
         */
        protected function fixTermBase(string $taxonomySlug): void
        {
        }
        /**
         * Finds a custom taxonomy base.
         *
         * @param string $taxonomySlug
         * @return string
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High,Generic.Metrics.CyclomaticComplexity.TooHigh
         */
        protected function expectedBase(string $taxonomySlug): string
        {
        }
        /**
         * @param string $fragment
         * @param bool $hasBlogPrefix
         * @return string
         */
        protected function ensureRequestFragment(string $fragment, bool $hasBlogPrefix): string
        {
        }
        /**
         * @param string $translated
         * @param string $taxonomySlug
         * @return string
         */
        protected function composeBase(string $translated, string $taxonomySlug): string
        {
        }
        /**
         * Updates the global WordPress rewrite instance for the given custom taxonomy.
         *
         * @param string $taxonomy
         * @param string $struct
         */
        protected function updateRewritePermastruct(string $taxonomy, string $struct): void
        {
        }
        /**
         * @param string $struct
         */
        protected function ensurePermastruct(string $struct): void
        {
        }
        /**
         * Retrieves the taxonomy base option name.
         *
         * @param string $taxonomySlug
         * @return string
         */
        protected function taxonomyBaseOption(string $taxonomySlug): string
        {
        }
        /**
         * Modifies custom taxonomies args to include a translated rewrite slug based on the MLP site settings.
         *
         * @wp-hook register_taxonomy_args
         *
         * @param array $args An array of arguments that will be passed to register_taxonomy().
         * @param string $taxonomy The name/slug of the taxonomy.
         *
         * @return array Updated arguments.
         */
        public function translateRewrite(array $args, string $taxonomy): array
        {
        }
    }
    /**
     * Translator implementation for search requests.
     */
    final class SearchTranslator implements \Inpsyde\MultilingualPress\Framework\Translator\Translator
    {
        public const FILTER_TRANSLATION = 'multilingualpress.filter_search_translation';
        /**
         * @param UrlFactory $urlFactory
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Factory\UrlFactory $urlFactory)
        {
        }
        /**
         * @inheritdoc
         */
        public function translationFor(int $siteId, \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args): \Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
    }
    /**
     * Service provider for all translation objects.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli {
    final class CliInput
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper)
        {
        }
        public function withArguments(array $arguments): void
        {
        }
        /**
         * @param string $argument
         * @param int $filter
         * @return mixed
         */
        //phpcs:ignore Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
        public function value(string $argument, int $filter = \FILTER_UNSAFE_RAW)
        {
        }
    }
    class WpCliCommandRegistrar
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper)
        {
        }
        /**
         * Register Cli commands for MLP
         *
         * @param WpCliCommand[] $commands
         * @throws Exception
         */
        public function register(array $commands): void
        {
        }
    }
    /**
     * WP-CLI commands helper.
     */
    class WpCliCommandsHelper
    {
        /**
         * Registers a CLI command handler.
         *
         * @param string $command The command.
         * Sub-commands should be separated by a space, i.e. `command sub-command`.
         * @param callable $handler The command handler.
         * @throws Exception
         */
        public function addCliCommand(string $command, callable $handler, array $documentation = []): void
        {
        }
        /**
         * Display an error message
         *
         * @param string $message An error message.
         * @throws WP_CLI\ExitException
         */
        public function showCliError(string $message): void
        {
        }
        /**
         * Display a success message
         *
         * @param string $message An error message.
         */
        public function showCliSuccess(string $message): void
        {
        }
        /**
         * @param string $command
         * @param array $options
         * @return mixed
         */
        //phpcs:ignore Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
        public function runCommand(string $command, array $options)
        {
        }
        /**
         * @param array $args
         * @param string $value
         * @param mixed $default
         * @return mixed
         */
        //phpcs:ignore Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
        public function flagValue(array $args, string $value, $default = null)
        {
        }
        public function formatItems(string $format, array $items, array $fields): void
        {
        }
        public function confirmation(string $message, array $args): void
        {
        }
        public function multiLineConfirmation(array $messageParts, string $separator, array $args): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli\Commands\License {
    class ActivateLicense implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\Core\Admin\LicenseSettingsRepository $licenseSettingsRepository, \Inpsyde\MultilingualPress\License\Api\Activator $activator)
        {
        }
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
    }
    class DeactivateLicense implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\Core\Admin\LicenseSettingsRepository $licenseSettingsRepository, \Inpsyde\MultilingualPress\License\Api\Activator $activator)
        {
        }
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli\Commands {
    class ResetToDefaults implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\Installation\Uninstaller $uninstaller, \Inpsyde\MultilingualPress\Framework\Database\TableList $tableList)
        {
        }
        // phpcs:ignore Inpsyde.CodeQuality.FunctionLength.TooLong
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli\Commands\Relations {
    class RelationDataInput
    {
        public static function fromArray(array $data): \Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput
        {
        }
        public function sourceSiteId(): int
        {
        }
        public function targetSiteId(): int
        {
        }
        public function sourceEntityId(): int
        {
        }
        public function targetEntityId(): int
        {
        }
    }
    class SuccessMessage
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper)
        {
        }
        public function connected(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data): void
        {
        }
        public function disconnected(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli\Commands\Relations\Post {
    class PostConfirmation implements \Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataToConfirmationMessage
    {
        public function prepare(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data): array
        {
        }
    }
    class ListPostRelations implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationsListBuilder $relationsListBuilder)
        {
        }
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
        public function buildRecord(int $siteId, int $relatedPostId): array
        {
        }
    }
    class DeletePostRelation implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationHandler $relationHandler)
        {
        }
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
    }
    class CreatePostRelation implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationHandler $relationHandler)
        {
        }
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli\Commands\Relations {
    class RelationsListBuilder
    {
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * Creates an array of records related to the given content type and ID.
         *
         * @param int $contentId
         * @param string $contentType
         * @param callable $recordBuilderCallback
         * @return array
         * @throws NonexistentTable
         */
        public function build(int $contentId, string $contentType, callable $recordBuilderCallback): array
        {
        }
    }
    class ConfirmationMessage
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\ContentRelationsChecker $contentRelationsChecker, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataToConfirmationMessage $relationDataToConfirmationMessage)
        {
        }
        public function confirmConnection(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data, array $relations, array $associativeArgs): void
        {
        }
        public function confirmDisconnection(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data, array $associativeArgs): void
        {
        }
        public function confirmSiteDisconnection(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data, array $associativeArgs): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli\Commands\Relations\Term {
    class CreateTermRelation implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationHandler $relationHandler)
        {
        }
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
    }
    class DeleteTermRelation implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationHandler $relationHandler)
        {
        }
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
    }
    class ListTermRelations implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationsListBuilder $relationsListBuilder)
        {
        }
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
        public function buildRecord(int $siteId, int $relatedTermId): array
        {
        }
    }
    class TermConfirmation implements \Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataToConfirmationMessage
    {
        public function prepare(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli\Commands\Relations {
    class ContentRelationsChecker
    {
        public function isAlreadyConnected(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data, array $relations): bool
        {
        }
        public function isOverwrite(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data, array $relations): bool
        {
        }
    }
    class ContentRelationsFetcher
    {
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        public function fetch(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data, string $type): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli\Commands\Relations\Site {
    class SiteConfirmation implements \Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataToConfirmationMessage
    {
        public function prepare(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data): array
        {
        }
    }
    class DeleteSiteRelation implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\Validators\SiteIdsValidator $siteIdsValidator, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\ConfirmationMessage $confirmationMessage)
        {
        }
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
    }
    class CreateSiteRelation implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\Validators\SiteIdsValidator $siteIdsValidator)
        {
        }
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
    }
    class ListSiteRelations implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations)
        {
        }
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
        public function buildRecord(int $relatedSiteId): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli\Commands\Relations {
    class RelationHandler
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\Validators\RelationEntitiesInputDataValidator $relationshipEntitiesInputDataValidator, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\ConfirmationMessage $confirmationMessage, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\SuccessMessage $successMessage, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\ContentRelationsFetcher $contentRelationsFetcher, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\ContentRelationsChecker $contentRelationsChecker, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @param array $associativeArgs
         * @param string $contentRelationType
         * @return void
         * @throws NonexistentTable
         */
        public function connect(array $associativeArgs, string $contentRelationType): void
        {
        }
        /**
         * @param array $associativeArgs
         * @param string $contentRelationType
         * @return void
         * @throws NonexistentTable
         */
        public function disconnect(array $associativeArgs, string $contentRelationType): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli\Commands\Relations\Validators {
    class SiteIdsValidator
    {
        public function validate(int $sourceSiteId, int $targetSiteId): void
        {
        }
    }
    class EntityOriginType
    {
        public const ENTITY_ORIGIN_TYPE_SOURCE = 'source';
        public const ENTITY_ORIGIN_TYPE_TARGET = 'target';
        public static function labelByOrigin(string $origin): string
        {
        }
    }
    class RelationEntitiesInputDataValidator
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\Validators\SiteIdsValidator $siteIdsValidator, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\Validators\EntityIdValidator $entityIdValidator, ?\Inpsyde\MultilingualPress\WpCli\Commands\Relations\Validators\EntitiesTypeMatchValidator $entitiesTypeMatchValidator = null)
        {
        }
        public function validate(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data): void
        {
        }
    }
    class TermTaxonomyMatchValidator implements \Inpsyde\MultilingualPress\WpCli\Commands\Relations\Validators\EntitiesTypeMatchValidator
    {
        public function validate(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data): void
        {
        }
    }
    class TermIdValidator implements \Inpsyde\MultilingualPress\WpCli\Commands\Relations\Validators\EntityIdValidator
    {
        public function validate(int $entityId, int $siteId, string $entityOriginType): void
        {
        }
    }
    class PostTypeMatchValidator implements \Inpsyde\MultilingualPress\WpCli\Commands\Relations\Validators\EntitiesTypeMatchValidator
    {
        public function validate(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $data): void
        {
        }
    }
    class ModuleValidator
    {
        public function __construct(\Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager)
        {
        }
        public function validate(string $moduleId): void
        {
        }
    }
    class PostIdValidator implements \Inpsyde\MultilingualPress\WpCli\Commands\Relations\Validators\EntityIdValidator
    {
        public function __construct(\Inpsyde\MultilingualPress\Core\PostTypeRepository $postTypeRepository)
        {
        }
        public function validate(int $entityId, int $siteId, string $entityOriginType): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli\Commands\Exceptions {
    class EntitiesAlreadyConnected extends \RuntimeException
    {
        public static function forRelationData(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $relationData): self
        {
        }
    }
    class EntitiesNotConnected extends \RuntimeException
    {
        public static function forRelationData(\Inpsyde\MultilingualPress\WpCli\Commands\Relations\RelationDataInput $relationData): self
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli\Commands\Sites {
    class CreateSite implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public const ARG_LANGUAGE = 'mlp-language';
        public const ARG_RELATIONS = 'relations';
        public const ARG_BASED_ON = 'based-on';
        public const ARG_COPY_ATTACHMENTS = 'copy-attachments';
        public const ARG_COPY_USERS = 'copy-users';
        public const ARG_CONNECT_CONTENT = 'connect-content';
        public const ARG_CONNECT_COMMENTS = 'connect-comments';
        public const ARG_ACTIVATE_PLUGINS = 'activate-plugins';
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\WpCli\CliInput $cliInput)
        {
        }
        // phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
    }
    class SitesList implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations)
        {
        }
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli\Commands\Modules {
    class ActivateModule implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\Validators\ModuleValidator $moduleValidation)
        {
        }
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
    }
    class DeactivateModule implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        public function __construct(\Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper, \Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager, \Inpsyde\MultilingualPress\WpCli\Commands\Relations\Validators\ModuleValidator $moduleValidation)
        {
        }
        public function handler(array $args, array $associativeArgs): void
        {
        }
        public function docs(): array
        {
        }
        public function name(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\WpCli {
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritDoc
         * @throws NameNotFound
         * @throws LateAccessToNotSharedService
         * @throws Exception
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\SiteHealth {
    /**
     * Adds MultilingualPress information to the WordPress Site Health debug data.
     */
    class SiteHealth
    {
        public function __construct(\Inpsyde\MultilingualPress\Framework\PluginProperties $pluginProperties, \Inpsyde\MultilingualPress\Core\Admin\LicenseSettingsRepository $licenseSettingsRepository, \Inpsyde\MultilingualPress\Framework\Module\ModuleManager $moduleManager, \Inpsyde\MultilingualPress\Framework\Api\Languages $languages, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository $cacheSettingsRepository, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions $cacheSettingsOptions)
        {
        }
        public function debugInfo(): array
        {
        }
    }
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Api {
    /**
     * Content relations API implementation using the WordPress database object.
     */
    final class WpdbContentRelations implements \Inpsyde\MultilingualPress\Framework\Api\ContentRelations
    {
        use \Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Database\Table\ContentRelationsTable $contentRelationshipTable, \Inpsyde\MultilingualPress\Database\Table\RelationshipsTable $relationshipsTable, \Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes $activePostTypes, \Inpsyde\MultilingualPress\Core\Entity\ActiveTaxonomies $activeTaxonomies, \Inpsyde\MultilingualPress\Framework\Cache\Server\Facade $cache, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository $cacheSettingsRepository, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository, \Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \Inpsyde\MultilingualPress\Database\Table\RelationshipMetaTable $relationshipMetaTable)
        {
        }
        /**
         * @inheritdoc
         */
        public function createRelationship(array $contentIds, string $type): int
        {
        }
        /**
         * @inheritdoc
         */
        public function deleteAllRelationsForInvalidContent(string $type): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function deleteAllRelationsForInvalidSites(): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function deleteAllRelationsForSite(int $siteId): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function deleteRelation(array $contentIds, string $type): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function duplicateRelations(int $sourceSiteId, int $targetSiteId): int
        {
        }
        /**
         * @inheritdoc
         */
        public function contentId(int $relationshipId, int $siteId): int
        {
        }
        /**
         * @inheritdoc
         */
        public function contentIdForSite(int $siteId, int $contentId, string $type, int $targetSiteId): int
        {
        }
        /**
         * @inheritdoc
         */
        public function contentIds(int $relationshipId): array
        {
        }
        /**
         * @inheritdoc
         */
        public function relations(int $siteId, int $contentId, string $type): array
        {
        }
        /**
         * @inheritdoc
         */
        public function relationshipId(array $contentIds, string $type, bool $create = \false): int
        {
        }
        /**
         * @inheritdoc
         */
        public function hasSiteRelations(int $siteId, string $type = ''): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function relateAllPosts(int $sourceSite, int $targetSite): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function relateAllTerms(int $sourceSite, int $targetSite): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function relateAllComments(int $sourceSite, int $targetSite): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function saveRelation(int $relationshipId, int $siteId, int $contentId): bool
        {
        }
    }
    /**
     * Site relations API implementation using the WordPress database object.
     */
    final class WpdbSiteRelations implements \Inpsyde\MultilingualPress\Framework\Api\SiteRelations
    {
        /**
         * @param \wpdb $wpdb
         * @param Table $table
         * @param Facade $cache
         * @param CacheSettingsRepository $cacheSettingsRepository
         */
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Framework\Database\Table $table, \Inpsyde\MultilingualPress\Framework\Cache\Server\Facade $cache, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository $cacheSettingsRepository)
        {
        }
        /**
         * @inheritdoc
         */
        public function deleteRelation(int $sourceSite, int $targetSite = 0): int
        {
        }
        /**
         * @inheritdoc
         */
        public function allRelations(): array
        {
        }
        /**
         * @inheritdoc
         */
        public function relatedSiteIds(int $siteId, bool $includeSite = \false): array
        {
        }
        /**
         * @inheritdoc
         */
        public function insertRelations(int $baseSiteId, array $siteIds): int
        {
        }
        /**
         * @inheritdoc
         */
        public function relateSites(int $baseSiteId, array $siteIds): int
        {
        }
    }
    class ContentRelationsValidator
    {
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations)
        {
        }
        /**
         * @psalm-param int[] $relations
         * @psalm-return int[]
         */
        public function validate(array $relations, int $siteId): array
        {
        }
    }
    /**
     * Translations API implementation.
     */
    final class Translations implements \Inpsyde\MultilingualPress\Framework\Api\Translations
    {
        public const FILTER_SEARCH_TRANSLATIONS = 'multilingualpress.search_translations';
        public const FILTER_TRANSLATION_SEARCH_ARGS = 'multilingualpress.translation_search_args';
        /**
         * @var string
         */
        public const SEARCH_CACHE_KEY = 'translations';
        /**
         * @param SiteRelations $siteRelations
         * @param ContentRelations $contentRelations
         * @param Languages $languages
         * @param WordpressContext $wordpressContext
         * @param Facade $cache
         * @param CacheSettingsRepository $cacheSettingsRepository
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations, \Inpsyde\MultilingualPress\Framework\Api\Languages $languages, \Inpsyde\MultilingualPress\Framework\WordpressContext $wordpressContext, \Inpsyde\MultilingualPress\Framework\Cache\Server\Facade $cache, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository $cacheSettingsRepository)
        {
        }
        /**
         * @inheritdoc
         */
        public function searchTranslations(\Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs $args): array
        {
        }
        /**
         * @inheritdoc
         */
        public function registerTranslator(\Inpsyde\MultilingualPress\Framework\Translator\Translator $translator, string $type): bool
        {
        }
    }
    class ContentRelationshipMeta implements \Inpsyde\MultilingualPress\Framework\Api\ContentRelationshipMetaInterface
    {
        protected \wpdb $wpdb;
        protected \Inpsyde\MultilingualPress\Database\Table\RelationshipMetaTable $relationshipMetaTable;
        protected \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations;
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Database\Table\RelationshipMetaTable $relationshipMetaTable, \Inpsyde\MultilingualPress\Framework\Api\ContentRelations $contentRelations)
        {
        }
        /**
         * @inheritDoc
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
         */
        public function updateRelationshipMeta(int $relationshipId, string $metaKey, $metaValue): void
        {
        }
        /**
         * @inheritDoc
         */
        public function deleteRelationshipMeta(int $relationshipId): bool
        {
        }
        /**
         * @inheritDoc
         */
        public function relationshipMetaValue(int $relationshipId, string $metaKey): string
        {
        }
        /**
         * @inheritDoc
         */
        public function relationshipMetaValueByPostId(int $postId, string $metaKey): string
        {
        }
    }
    /**
     * Service provider for all API objects.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider, \Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider
    {
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function integrate(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
    /**
     * Languages API implementation using the WordPress database object.
     */
    final class WpdbLanguages implements \Inpsyde\MultilingualPress\Framework\Api\Languages
    {
        /**
         * @param wpdb $wpdb
         * @param Table $table
         * @param SiteSettingsRepository $siteSettingsRepository
         * @param LanguageFactory $languageFactory
         */
        public function __construct(\wpdb $wpdb, \Inpsyde\MultilingualPress\Framework\Database\Table $table, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository, \Inpsyde\MultilingualPress\Framework\Factory\LanguageFactory $languageFactory)
        {
        }
        /**
         * @inheritdoc
         */
        public function deleteLanguage(int $id): bool
        {
        }
        /**
         * @inheritdoc
         */
        public function allLanguages(): array
        {
        }
        /**
         * @inheritdoc
         */
        public function allAssignedLanguages(): array
        {
        }
        /**
         * @inheritdoc
         *
         * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
         */
        public function languageBy(string $column, $value): \Inpsyde\MultilingualPress\Framework\Language\Language
        {
        }
        /**
         * @inheritdoc
         */
        public function insertLanguage(array $languageData): int
        {
        }
        /**
         * @inheritdoc
         */
        public function updateLanguage(int $id, array $data): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Api\WpCliCommands {
    /**
     * WP-CLI Set Language.
     */
    class SetLanguage implements \Inpsyde\MultilingualPress\WpCli\WpCliCommand
    {
        protected \Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper;
        /**
         * SetLanguage constructor.
         *
         * @param SiteSettingsRepository $repository
         * @param list<string> $availableMlpLanguages A list of available MLP language BCP-47 codes
         * @param WpCliCommandsHelper $wpCliCommandsHelper
         */
        public function __construct(\Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $repository, array $availableMlpLanguages, \Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper $wpCliCommandsHelper)
        {
        }
        /**
         * @inheritDoc
         */
        public function name(): string
        {
        }
        /**
         * The handler of
         * {@link https://make.wordpress.org/cli/handbook/references/internal-api/wp-cli-add-command/ WP_CLI::add_command}
         * implementation
         *
         * @param array<string> $args The list of positional arguments
         * @param array<string, string> $associativeArgs A map of associative argument names to values
         * A map of associative argument names to values
         * @return void
         * @throws WP_CLI\ExitException
         */
        public function handler(array $args, array $associativeArgs): void
        {
        }
        /**
         * @inheritDoc
         */
        public function docs(): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\MediaLibrary\Settings {
    /**
     * Represents the MediaLibrary setting.
     */
    interface MediaLibrarySetting
    {
        /**
         * The key of the setting.
         *
         * @return string
         */
        public function key(): string;
        /**
         * The setting label.
         *
         * @return string
         */
        public function label(): string;
        /**
         * Renders the setting with given arguments.
         *
         * @param array<string, scalar> $args A map of argument keys to values to use for the render.
         */
        public function render(array $args = []): void;
    }
    class RelatedSitesSetting implements \Inpsyde\MultilingualPress\MediaLibrary\Settings\MediaLibrarySetting
    {
        public const FILTER_NAME_RENDER_TYPE = 'multilingualpress.MediaLibrary.related_site_setting_render_type';
        public const RENDER_TYPE_SELECT = 'select';
        public const RENDER_TYPE_CHECKBOX = 'checkbox';
        public const AVAILABLE_RENDER_TYPES = [self::RENDER_TYPE_SELECT, self::RENDER_TYPE_CHECKBOX];
        protected string $key;
        protected string $label;
        /**
         * @var array<int, Language>
         */
        protected array $relatedSites;
        public function __construct(string $key, string $label, array $relatedSites)
        {
        }
        /**
         * @inheritDoc
         */
        public function key(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function label(): string
        {
        }
        /**
         * @inheritDoc
         */
        public function render(array $args = []): void
        {
        }
        /**
         * Configures the render type.
         *
         * @param string $renderType The render type.
         * @return string The configured render type('select' | 'checkbox').
         */
        protected function configureRenderType(string $renderType): string
        {
        }
        /**
         * Renders the checkboxes by given name.
         *
         * @param string $name The name.
         * @return void
         */
        protected function renderCheckboxes(string $name): void
        {
        }
        /**
         * Renders the select by given name.
         *
         * @param string $name The name.
         * @param bool $includeEmpty true if an empty option should be included, otherwise false.
         * @param bool $includeAll true if the "Select All" option should be included, otherwise false.
         * @param string $description The description.
         * @return void
         */
        protected function renderSelect(string $name, bool $includeEmpty, bool $includeAll, string $description): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\MediaLibrary {
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        public const MODULE_ID = 'media-library';
        public const PARAMETER_CONFIG_MODULE_DIR_PATH = 'multilingualpress.MediaLibrary.ModuleDirPath';
        public const PARAMETER_CONFIG_MODULE_ASSETS_FACTORY = 'multilingualpress.MediaLibrary.assetsFactory';
        public const PARAMETER_CONFIG_BULK_ACTIONS_KEY_PREFIX = 'multilingualpress.MediaLibrary.bulkActionsKeyPrefix';
        public const PARAMETER_CONFIG_BULK_ACTIONS = 'multilingualpress.MediaLibrary.bulkActions';
        public const PARAMETER_CONFIG_SETTINGS_ALL_SETTINGS = 'multilingualpress.MediaLibrary.allSettings';
        public const PARAMETER_CONFIG_SETTINGS_TITLE = 'multilingualpress.MediaLibrary.settingsTitle';
        public const MODULE_SCRIPTS_HANDLER_NAME = 'multilingualpress-media-library';
        public const CONFIGURATION_NAME_FOR_URL_TO_MODULE_ASSETS = 'multilingualpress.MediaLibrary.urlToModuleAssets';
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * Sets up assets for module.
         *
         * @param string $urlToModuleAssetsFolder The url to assets folder.
         */
        protected function registerAssets(string $urlToModuleAssetsFolder): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\MediaLibrary\BulkCopyAttachments {
    class BulkCopyAttachmentsRequestHandler implements \Inpsyde\MultilingualPress\Framework\Http\RequestHandler
    {
        public const ACTION = 'multilingualpress.MediaLibrary.BulkCopyAttachments';
        protected const PARAM_NAME_FOR_SITE_IDS = 'siteIds';
        protected const PARAM_NAME_FOR_ATTACHMENT_IDS = 'attachmentIds';
        protected \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce;
        protected \Inpsyde\MultilingualPress\Attachment\Copier $copier;
        protected \Inpsyde\MultilingualPress\Editor\Notices\ExistingAttachmentsNotice $existingAttachmentsNotice;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Attachment\Copier $copier, \Inpsyde\MultilingualPress\Editor\Notices\ExistingAttachmentsNotice $existingAttachmentsNotice)
        {
        }
        /**
         * @inheritdoc
         */
        public function handle(\Inpsyde\MultilingualPress\Framework\Http\ServerRequest $request): void
        {
        }
    }
    class BulkCopyAttachmentsSettingsView implements \Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView
    {
        public const ACTION = 'multilingualpress.MediaLibrary.BulkCopyAttachmentsSettingsView';
        protected \Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce;
        protected \Inpsyde\MultilingualPress\MediaLibrary\Settings\MediaLibrarySetting $relatedSitesSetting;
        protected string $settingsTitle;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\MediaLibrary\Settings\MediaLibrarySetting $relatedSitesSetting, string $settingsTitle)
        {
        }
        /**
         * @inheritdoc
         */
        public function render(): void
        {
        }
        /**
         * Renders the label.
         *
         * @param string $label The label.
         * @return void
         */
        protected function renderLabel(string $label): void
        {
        }
        /**
         * Renders the button.
         *
         * @return void
         */
        protected function renderButton(): void
        {
        }
        /**
         * Renders the nonce input.
         */
        protected function renderNonce(): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Log {
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\NavMenu\CopyNavMenu {
    /**
     * Handler for nav menu AJAX requests.
     * @psalm-suppress UndefinedMagicPropertyFetch
     */
    class CopyNavMenu
    {
        public const ACTION_MENU_ITEM_COPIED = 'multilingualpress.copy_nav_menu.item_copied';
        /**
         * The Request param names
         */
        protected const REQUEST_VALUE_NAME_FOR_MENU_TO_COPY = 'mlp_menu_to_copy';
        protected const REQUEST_VALUE_NAME_FOR_REMOTE_SITE_ID = 'remote_site_id';
        protected const REQUEST_VALUE_NAME_FOR_CURRENT_MENU_NAME = 'current_menu_name';
        /**
         * MLP language menu item configs
         */
        protected const LANGUAGE_MENU_ITEM_META_KEY_SITE_ID = '_blog_id';
        protected const LANGUAGE_MENU_ITEM_META_KEY_ITEM_TYPE = '_menu_item_type';
        protected const LANGUAGE_MENU_ITEM_TYPE = 'mlp_language';
        /**
         * Configs to determinate and update parent menu item of copied menu
         */
        protected const REMOTE_MENU_ITEM_ID = 'remote_menu_item_id';
        protected const MENU_ITEM_META_KEY_PARENT_MENU_ITEM = '_menu_item_menu_item_parent';
        /**
         * @param Nonce $nonce
         * @param Request $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * Handles the copy of navigation menu from remote site
         * Will get the values from Request
         * Will delete the current menu items
         * Will copy the menu items from remote site
         */
        public function handleCopyNavMenu(): void
        {
        }
        /**
         * Will return the value from request with the giben param name
         *
         * @param string $requestParamName The name of the Request param,
         * can be either self::REQUEST_VALUE_NAME_FOR_MENU_TO_COPY or
         * self::REQUEST_VALUE_NAME_FOR_REMOTE_SITE_ID or
         * self::REQUEST_VALUE_NAME_FOR_CURRENT_MENU_ID
         * @return string the value of request param
         */
        protected function valueFromRequest(string $requestParamName): string
        {
        }
        /**
         * Retrieves all menu items of a navigation menu.
         *
         * @param int $menuId The id of the menu to get
         * @return false|array $items Array of menu items, otherwise false.
         *
         * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
         */
        protected function fetchMenuItems(int $menuId)
        {
        }
        /**
         * Will delete the menu items of given menu
         *
         * @param int $menuId The menu id from which the items should be deleted
         */
        protected function deleteMenuItems(int $menuId): void
        {
        }
        /**
         * Will Copy the Menu Items from remote site for selected menu
         *
         * @param array $remoteMenu The Remote menu which is selected to be copied
         * @param int $remoteSiteId The Remote site id to which the selected menu to be copied belongs
         * @param int $sourceMenuId The Source menu id to whcih the items should be copied
         * @throws NonexistentTable
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function copyMenuItems(array $remoteMenu, int $remoteSiteId, int $sourceMenuId): void
        {
        }
        /**
         * Will update the necessary metadata for mlp_language type menu items
         *
         * @param WP_Post $remoteMenuItem The menu item object from remote site
         * @param int $remoteSiteId The remote site id from where the menu item is copied
         * @param int $sourceMenuItemDbId The copied source menu item db id
         */
        protected function updateSourceLanguageMenuItemMeta(\WP_Post $remoteMenuItem, int $remoteSiteId, int $sourceMenuItemDbId): void
        {
        }
        /**
         * Will generate the menu item data which should be created because of the menu copy
         * If there is a connected post in source site then it's data will be taken, otherwise
         * will be created a custom menu item with the url to remote post.
         *
         * @param WP_Post $remoteMenuItem The remote menu item which should be copied
         * @param int $sourceContentId The source post id, if exist it can be used to grab
         * additional info from it instead of taking from remote menu item
         * @return array of generated menu item data
         */
        protected function generateNewMenuItemData(\WP_Post $remoteMenuItem, int $sourceContentId): array
        {
        }
        /**
         * Check if menu item has parent
         *
         * @param int $parentMenuItemId the menu item id to check
         * @return bool true/false if menu item has parent or no
         */
        protected function hasParentMenuItem(int $parentMenuItemId): bool
        {
        }
        /**
         * The method will update the parent menu item ids for the given menu
         *
         * @param int $menuId The menu Id for which to check and update parent menu item ids
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         */
        protected function updateParentMenuItems(int $menuId): void
        {
        }
        /**
         * Will create a new navigation menu
         *
         * @param string $namePrefix The name prefix of new menu
         * @return int created menu ID
         */
        protected function createNewNavMenu(string $namePrefix): int
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\NavMenu\CopyNavMenu\Ajax {
    class CopyNavMenuSettingsView
    {
        public const ACTION = 'multilingualpress_copy_nav_menu_settings_view';
        /**
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Handle AJAX request.
         * @throws NonexistentTable
         */
        public function handle(): void
        {
        }
        /**
         * Render a select of menu names
         * @throws NonexistentTable
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        protected function generateCopyNavMenuSettingsMarkup(): string
        {
        }
        /**
         * Will return assigned location names of given menu
         *
         * @param WP_Term $menu WP_Term object for Menu
         * @return array of menu location names
         */
        protected function assignedMenuLocationNames(\WP_Term $menu): array
        {
        }
        /**
         * Generate the copy menu select-box label markup
         *
         * @return string the markup of the copy menu select-box label
         */
        protected function selectLabelMarkup(): string
        {
        }
        /**
         * Generate the hidden input with the site id value of remote site
         *
         * @param int $siteId The remote site id
         *
         * @return string the markup of the hidden input with remote site id value
         */
        protected function hiddenSiteIdFieldMarkup(int $siteId): string
        {
        }
        /**
         * Generate the hidden input with the current menu id
         *
         * @return string the markup of the hidden input with the current menu id
         */
        protected function hiddenCurrentMenuNameFieldMarkup(): string
        {
        }
        /**
         * Generate the markup of the select options with remote site menu's id as the option value
         * and the option name with combination of remote site menu's name and location if is assigned
         *
         * @param int $menuTermId The menu id of the remote site
         * @param string $menuName The manu name of the remote site
         * @param array $assignedMenuLocationNames the assigned menu location of the menu
         *
         * @return string the markup of the select-box options
         */
        protected function selectOptionMarkup(int $menuTermId, string $menuName, array $assignedMenuLocationNames): string
        {
        }
        /**
         * Generate the markup of the select options group with the data-site_id to which the menus belong,
         * with the site name as the group label and the options with the menus which belong to that site
         *
         * @param int $siteId The remote site id
         * @param string $selectGroupOptionsMarkup The markup of the options of the group
         *
         * @return string the markup of the Group of options
         * @throws NonexistentTable
         */
        protected function selectOptionGroupMarkup(int $siteId, string $selectGroupOptionsMarkup): string
        {
        }
        /**
         * Generate the Nonce field markup
         *
         * @return string the markup of the Nonce field
         */
        protected function nonceFieldMarkup(): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\NavMenu {
    /**
     * Deletes nav menu items.
     */
    class ItemDeletor
    {
        /**
         * @param wpdb $wpdb
         */
        public function __construct(\wpdb $wpdb)
        {
        }
        /**
         * Deletes all remote MultilingualPress nav menu items linking to the (to-be-deleted) site with
         * the given ID.
         *
         * @param \WP_Site $oldSite
         * @return int
         */
        public function deleteItemsForDeletedSite(\WP_Site $oldSite): int
        {
        }
    }
    /**
     * Languages meta box view.
     */
    class LanguagesMetaboxView
    {
        /**
         * @param Nonce $nonce
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce)
        {
        }
        /**
         * Renders the HTML.
         */
        public function render(): void
        {
        }
    }
    class ItemRepository
    {
        public const META_KEY_SITE_ID = '_blog_id';
        public const ITEM_TYPE = 'mlp_language';
        public const FILTER_MENU_LANGUAGE_NAME = 'multilingualpress.nav_menu_language_name';
        /**
         * Returns the according items for the sites with the given IDs.
         *
         * @param int $menuId
         * @param int[] $siteIds
         * @return WP_Post[]
         * @throws NonexistentTable
         */
        public function itemsForSites(int $menuId, int ...$siteIds): array
        {
        }
        /**
         * Returns the site ID for the nav menu item with the given ID.
         *
         * @param int $itemId
         * @return int
         */
        public function siteIdOfMenuItem(int $itemId): int
        {
        }
    }
    /**
     * Handler for nav menu AJAX requests.
     */
    class AjaxHandler
    {
        public const ACTION = 'multilingualpress_add_languages_to_nav_menu';
        /**
         * @param Nonce $nonce
         * @param ItemRepository $repository
         * @param Request $request
         */
        public function __construct(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, \Inpsyde\MultilingualPress\NavMenu\ItemRepository $repository, \Inpsyde\MultilingualPress\Framework\Http\Request $request)
        {
        }
        /**
         * Handles the AJAX request and sends an appropriate response.
         */
        public function handle(): void
        {
        }
    }
    /**
     * Filters nav menu items and passes the proper URL.
     */
    class ItemFilter
    {
        public const ITEMS_FILTER_CACHE_KEY = 'filter_items';
        public const ACTION_PREPARE_ITEM = 'multilingualpress.prepare_nav_menu_item';
        public const FILTER_SHOULD_PRESERVE_URL_PARAMS = 'multilingualpress.should_preserve_url_params';
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelationSearch $contentRelationSearch, \Inpsyde\MultilingualPress\NavMenu\ItemRepository $repository, \Inpsyde\MultilingualPress\Framework\Cache\Server\Facade $cache, \Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository $cacheSettingsRepository, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $siteSettingsRepository)
        {
        }
        /**
         * Filters the nav menu items.
         *
         * @param WP_Post[] $items
         * @return WP_Post[]
         * @throws Exception\NotRegisteredCacheItem
         * @throws Exception\InvalidCacheArgument
         * @throws Exception\InvalidCacheDriver
         */
        public function filterItems(array $items): array
        {
        }
        /**
         * @param WP_Post[] $items
         * @return bool
         */
        protected function itemsExists(array $items): bool
        {
        }
        /**
         * @param array $translations
         * @param ItemRepository $repository
         * @param SiteSettingsRepository $settingsRepository
         * @return void
         * @throws Throwable
         */
        public function hookToMenuLink(array $translations, \Inpsyde\MultilingualPress\NavMenu\ItemRepository $repository, \Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository $settingsRepository): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\NavMenu\BlockTypes {
    /**
     * Represent the language menu display styles.
     */
    interface LanguageMenuDisplayStyleInterface
    {
        public const HORIZONTAL = 'horizontal';
        public const VERTICAL = 'vertical';
        public const DROPDOWN = 'dropdown';
    }
    /**
     * @psalm-type flagDisplayTypeValues = 'only_language'|'flag_and_text'|'only_flag'
     * @psalm-type displayStyleValues = 'horizontal'|'vertical'|'dropdown'
     * @psalm-type siteId = int
     * @psalm-type languageInfo = array{name: string, url: string, flagUrl?: string, flagMarkup: string}
     * @psalm-type siteLanguages = array<siteId, languageInfo>
     * @psalm-type wrapperAttributesMap = array{wrapperAttributes: string, dropdownAttributes: string}
     * @psalm-type languageMenuContext = array{
     *     languages: siteLanguages,
     *     displayStyle: displayStyleValues,
     *     flagDisplayType: flagDisplayTypeValues,
     *     placeholder: string,
     *     wrapperAttributes: wrapperAttributesMap
     * }
     */
    class LanguageMenuContextFactory implements \Inpsyde\MultilingualPress\Module\Blocks\Context\ContextFactoryInterface
    {
        public const FILTER_SHOULD_PRESERVE_URL_PARAMS = 'multilingualpress.navMenu.block.should_preserve_url_params';
        protected \Inpsyde\MultilingualPress\Framework\Api\ContentRelationSearch $contentRelationSearch;
        protected \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Factory $flagFactory;
        /**
         * @var array{wrapperAttributes: string, dropdownAttributes: string}
         */
        protected array $wrapperAttributes;
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\ContentRelationSearch $contentRelationSearch, \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Factory $flagFactory, array $wrapperAttributes)
        {
        }
        /**
         * @inheritDoc
         * @psalm-return languageMenuContext The context.
         * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
         * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
         */
        public function createContext(array $attributes): array
        {
        }
        /**
         * Returns the flag object of given site.
         *
         * @param int $siteId The site ID.
         * @return Flag The flag object.
         */
        protected function siteFlag(int $siteId): \Inpsyde\MultilingualPress\Module\SiteFlags\Flag\Flag
        {
        }
        /**
         * Returns the translation of a given site.
         *
         * @param int $siteId The site ID.
         * @return ?Translation
         */
        protected function siteTranslation(int $siteId): ?\Inpsyde\MultilingualPress\Framework\Api\Translation
        {
        }
        /**
         * Configures the block wrapper attributes.
         *
         * Will extract the styling related classes such as `has-[color]-background-color` | `has-[color]`
         * allowing to use them on any element we want.
         *
         * @param string $displayStyle The display style ('horizontal' | 'vertical' | 'dropdown').
         * @psalm-param displayStyleValues $displayStyle
         * @param string $flagDisplayType The flags display style ('only_language'|'flag_and_text'|'only_flag').
         * @psalm-param flagDisplayTypeValues $flagDisplayType
         * @return array{wrapperAttributes: string, dropdownAttributes: string} The configured wrapper attributes.
         */
        protected function configureWrapperAttributes(string $displayStyle, string $flagDisplayType): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\NavMenu {
    /**
     * @psalm-type relatedSites = array{id: int, name: string}
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider, \Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider
    {
        public const NONCE_COPY_NAV_MENU_ACTION = 'copy_nav_menu';
        /**
         * @inheritdoc
         * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function integrate(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Factory {
    /**
     * Service provider for all factories.
     */
    final class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\ServiceProvider
    {
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Editor {
    class EditorNoticesPostMetaRegistrar
    {
        public function __construct(\Inpsyde\MultilingualPress\Core\PostTypeRepositoryInterface $repository)
        {
        }
        public function __invoke(): void
        {
        }
    }
    class EditorNoticesHandler
    {
        public function __construct(\Inpsyde\MultilingualPress\Editor\EditorNoticesRepository $editorNoticesRepository, \Inpsyde\MultilingualPress\Editor\Notices\EditorNotice ...$notices)
        {
        }
        public function handle(int $postId): void
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Editor\Notices {
    interface EditorNotice
    {
        public function message(): string;
    }
    class ExistingAttachmentsNotice implements \Inpsyde\MultilingualPress\Editor\Notices\EditorNotice
    {
        public function addAttachment(int $attachmentId, int $siteId): void
        {
        }
        public function message(bool $includeHtml = \false): string
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Editor {
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
    class EditorNoticesRepository
    {
        public const META_KEY = 'editor_notices';
        public function addNotice(int $postId, \Inpsyde\MultilingualPress\Editor\Notices\EditorNotice $notice): bool
        {
        }
    }
}
namespace Inpsyde\MultilingualPress\Onboarding {
    /**
     * Onboarding state manager.
     */
    class State
    {
        public const OPTION_NAME = 'onboarding_state';
        public const STATE_SITES = 'sites';
        public const STATE_SETTINGS = 'settings';
        public const STATE_POST = 'post';
        public const STATE_END = 'end';
        /**
         * Update onboarding state based on site relations and screen.
         * @param string $onboardingState
         * @param array $siteRelations
         * @return string
         */
        public function update(string $onboardingState, array $siteRelations): string
        {
        }
        /**
         * @return string
         */
        public function read(): string
        {
        }
    }
    class Onboarding
    {
        public const OPTION_ONBOARDING_DISMISSED = 'onboarding_dismissed';
        public const OPTION_PHP_UPDATE_NOTICE_DISMISSED = 'multilingualpress.php_80_update_notice_dismissed';
        public function __construct(\Inpsyde\MultilingualPress\Framework\Api\SiteRelations $siteRelations, \Inpsyde\MultilingualPress\Framework\Http\Request $request, \Inpsyde\MultilingualPress\Onboarding\State $onboardingState, \Inpsyde\MultilingualPress\Onboarding\Notice $onboardingMessages)
        {
        }
        /**
         * @return void
         * @throws NonexistentTable
         */
        public function messages(): void
        {
        }
        /**
         * @return void
         */
        public function handleDismissOnboardingMessage(): void
        {
        }
        /**
         * @return void
         */
        public function handleAjaxDismissOnboardingMessage(): void
        {
        }
        /**
         * Displays the PHP version related messages.
         *
         * @return void
         */
        public function displayPhpVersionMessage(): void
        {
        }
    }
    class ServiceProvider implements \Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider, \Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider
    {
        public const MODULE_SCRIPTS_HANDLER_NAME = 'onboarding';
        /**
         * @inheritdoc
         */
        public function register(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function integrate(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
        /**
         * @inheritdoc
         */
        public function bootstrap(\Inpsyde\MultilingualPress\Framework\Service\Container $container): void
        {
        }
    }
    /**
     * Onboarding messages
     */
    class Notice
    {
        /**
         * @param State $onboardingState
         */
        public function __construct(\Inpsyde\MultilingualPress\Onboarding\State $onboardingState)
        {
        }
        /**
         * Creates onboarding message content.
         *
         * @param string $onboardingState
         * @return object
         */
        public function onboardingMessageContent(string $onboardingState): object
        {
        }
        /**
         * @return array
         */
        public function forMoreThanOneSite(): array
        {
        }
    }
}
namespace Inpsyde\MultilingualPress {
    /**
     * @param callable $function
     * @param bool $deactivate
     *
     * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
     */
    function deactivateNotice(callable $function, bool $deactivate = \true): void
    {
    }
    /**
     * Updates the meta links shown for the plugin on the Plugins screen.
     *
     * Adds custom links such as "Visit plugin site", "Documentation", and "Support".
     *
     * @param array $links An array of plugin meta links.
     * @param string $file Path to the plugin file relative to the plugins directory.
     * @return array Modified array of plugin meta links.
     */
    function updatePluginRowMeta(array $links, string $file): array
    {
    }
    /**
     * Loads definitions and/or autoloader.
     *
     * @param string $rootDir
     * @throws Exception
     */
    function autoload(string $rootDir): void
    {
    }
    /**
     * Bootstraps MultilingualPress.
     *
     * @wp-hook plugins_loaded
     * @throws Throwable
     * @return bool
     */
    function bootstrap()
    {
    }
    /**
     * Triggers a plugin-specific activation action third parties can listen to.
     *
     * @wp-hook activate_{$plugin}
     */
    function activate(): void
    {
    }
    /**
     * Load missed WordPress functions.
     */
    function loadWordPressFunctions(): void
    {
    }
}
namespace MultilingualPress\Vendor\Inpsyde\Assets {
    /**
     * Returns ".min" if SCRIPT_DEBUG is false.
     *
     * @return string
     */
    function assetSuffix(): string
    {
    }
    /**
     * Adding the assetSuffix() before file extension to the given file.
     *
     * @param string $file
     *
     * @return string
     * @example before: my-script.js | after: my-script.min.js
     *
     */
    function withAssetSuffix(string $file): string
    {
    }
    /**
     * Symlinks a folder inside the web-root for Assets, which are outside of the web-root
     * and returns a link to that folder.
     *
     * @param string $originDir
     * @param string $name
     *
     * @return string|null
     */
    function symlinkedAssetFolder(string $originDir, string $name): ?string
    {
    }
}
namespace MultilingualPress\Vendor\Inpsyde\Assets {
    const BOOTSTRAPPED = \true;
    function bootstrap(): bool
    {
    }
}
namespace MultilingualPress\Vendor\GuzzleHttp {
    /**
     * Debug function used to describe the provided value type and class.
     *
     * @param mixed $input Any type of variable to describe the type of. This
     *                     parameter misses a typehint because of that.
     *
     * @return string Returns a string containing the type of the variable and
     *                if a class is provided, the class name.
     *
     * @deprecated describe_type will be removed in guzzlehttp/guzzle:8.0. Use Utils::describeType instead.
     */
    function describe_type($input): string
    {
    }
    /**
     * Parses an array of header lines into an associative array of headers.
     *
     * @param iterable $lines Header lines array of strings in the following
     *                        format: "Name: Value"
     *
     * @deprecated headers_from_lines will be removed in guzzlehttp/guzzle:8.0. Use Utils::headersFromLines instead.
     */
    function headers_from_lines(iterable $lines): array
    {
    }
    /**
     * Returns a debug stream based on the provided variable.
     *
     * @param mixed $value Optional value
     *
     * @return resource
     *
     * @deprecated debug_resource will be removed in guzzlehttp/guzzle:8.0. Use Utils::debugResource instead.
     */
    function debug_resource($value = null)
    {
    }
    /**
     * Chooses and creates a default handler to use based on the environment.
     *
     * The returned handler is not wrapped by any default middlewares.
     *
     * @return callable(\Psr\Http\Message\RequestInterface, array): Promise\PromiseInterface Returns the best handler for the given system.
     *
     * @throws \RuntimeException if no viable Handler is available.
     *
     * @deprecated choose_handler will be removed in guzzlehttp/guzzle:8.0. Use Utils::chooseHandler instead.
     */
    function choose_handler(): callable
    {
    }
    /**
     * Get the default User-Agent string to use with Guzzle.
     *
     * @deprecated default_user_agent will be removed in guzzlehttp/guzzle:8.0. Use Utils::defaultUserAgent instead.
     */
    function default_user_agent(): string
    {
    }
    /**
     * Returns the default cacert bundle for the current system.
     *
     * First, the openssl.cafile and curl.cainfo php.ini settings are checked.
     * If those settings are not configured, then the common locations for
     * bundles found on Red Hat, CentOS, Fedora, Ubuntu, Debian, FreeBSD, OS X
     * and Windows are checked. If any of these file locations are found on
     * disk, they will be utilized.
     *
     * Note: the result of this function is cached for subsequent calls.
     *
     * @throws \RuntimeException if no bundle can be found.
     *
     * @deprecated default_ca_bundle will be removed in guzzlehttp/guzzle:8.0. This function is not needed in PHP 5.6+.
     */
    function default_ca_bundle(): string
    {
    }
    /**
     * Creates an associative array of lowercase header names to the actual
     * header casing.
     *
     * @deprecated normalize_header_keys will be removed in guzzlehttp/guzzle:8.0. Use Utils::normalizeHeaderKeys instead.
     */
    function normalize_header_keys(array $headers): array
    {
    }
    /**
     * Returns true if the provided host matches any of the no proxy areas.
     *
     * This method will strip a port from the host if it is present. Each pattern
     * can be matched with an exact match (e.g., "foo.com" == "foo.com") or a
     * partial match: (e.g., "foo.com" == "baz.foo.com" and ".foo.com" ==
     * "baz.foo.com", but ".foo.com" != "foo.com").
     *
     * Areas are matched in the following cases:
     * 1. "*" (without quotes) always matches any hosts.
     * 2. An exact match.
     * 3. The area starts with "." and the area is the last part of the host. e.g.
     *    '.mit.edu' will match any host that ends with '.mit.edu'.
     *
     * @param string   $host         Host to check against the patterns.
     * @param string[] $noProxyArray An array of host patterns.
     *
     * @throws Exception\InvalidArgumentException
     *
     * @deprecated is_host_in_noproxy will be removed in guzzlehttp/guzzle:8.0. Use Utils::isHostInNoProxy instead.
     */
    function is_host_in_noproxy(string $host, array $noProxyArray): bool
    {
    }
    /**
     * Wrapper for json_decode that throws when an error occurs.
     *
     * @param string $json    JSON data to parse
     * @param bool   $assoc   When true, returned objects will be converted
     *                        into associative arrays.
     * @param int    $depth   User specified recursion depth.
     * @param int    $options Bitmask of JSON decode options.
     *
     * @return object|array|string|int|float|bool|null
     *
     * @throws Exception\InvalidArgumentException if the JSON cannot be decoded.
     *
     * @see https://www.php.net/manual/en/function.json-decode.php
     * @deprecated json_decode will be removed in guzzlehttp/guzzle:8.0. Use Utils::jsonDecode instead.
     */
    function json_decode(string $json, bool $assoc = \false, int $depth = 512, int $options = 0)
    {
    }
    /**
     * Wrapper for JSON encoding that throws when an error occurs.
     *
     * @param mixed $value   The value being encoded
     * @param int   $options JSON encode option bitmask
     * @param int   $depth   Set the maximum depth. Must be greater than zero.
     *
     * @throws Exception\InvalidArgumentException if the JSON cannot be encoded.
     *
     * @see https://www.php.net/manual/en/function.json-encode.php
     * @deprecated json_encode will be removed in guzzlehttp/guzzle:8.0. Use Utils::jsonEncode instead.
     */
    function json_encode($value, int $options = 0, int $depth = 512): string
    {
    }
}
namespace MultilingualPress\Vendor {
    function includeIfExists(string $file): ?\Composer\Autoload\ClassLoader
    {
    }
}
namespace MultilingualPress\Vendor\Aws {
    //-----------------------------------------------------------------------------
    // Functional functions
    //-----------------------------------------------------------------------------
    /**
     * Returns a function that always returns the same value;
     *
     * @param mixed $value Value to return.
     *
     * @return callable
     */
    function constantly($value)
    {
    }
    /**
     * Filters values that do not satisfy the predicate function $pred.
     *
     * @param mixed    $iterable Iterable sequence of data.
     * @param callable $pred Function that accepts a value and returns true/false
     *
     * @return \Generator
     */
    function filter($iterable, callable $pred)
    {
    }
    /**
     * Applies a map function $f to each value in a collection.
     *
     * @param mixed    $iterable Iterable sequence of data.
     * @param callable $f        Map function to apply.
     *
     * @return \Generator
     */
    function map($iterable, callable $f)
    {
    }
    /**
     * Creates a generator that iterates over a sequence, then iterates over each
     * value in the sequence and yields the application of the map function to each
     * value.
     *
     * @param mixed    $iterable Iterable sequence of data.
     * @param callable $f        Map function to apply.
     *
     * @return \Generator
     */
    function flatmap($iterable, callable $f)
    {
    }
    /**
     * Partitions the input sequence into partitions of the specified size.
     *
     * @param mixed    $iterable Iterable sequence of data.
     * @param int $size Size to make each partition (except possibly the last chunk)
     *
     * @return \Generator
     */
    function partition($iterable, $size)
    {
    }
    /**
     * Returns a function that invokes the provided variadic functions one
     * after the other until one of the functions returns a non-null value.
     * The return function will call each passed function with any arguments it
     * is provided.
     *
     *     $a = function ($x, $y) { return null; };
     *     $b = function ($x, $y) { return $x + $y; };
     *     $fn = \Aws\or_chain($a, $b);
     *     echo $fn(1, 2); // 3
     *
     * @return callable
     */
    function or_chain()
    {
    }
    //-----------------------------------------------------------------------------
    // JSON compiler and loading functions
    //-----------------------------------------------------------------------------
    /**
     * Loads a compiled JSON file from a PHP file.
     *
     * If the JSON file has not been cached to disk as a PHP file, it will be loaded
     * from the JSON source file and returned.
     *
     * @param string $path Path to the JSON file on disk
     *
     * @return mixed Returns the JSON decoded data. Note that JSON objects are
     *     decoded as associative arrays.
     */
    function load_compiled_json($path)
    {
    }
    /**
     * No-op
     */
    function clear_compiled_json()
    {
    }
    //-----------------------------------------------------------------------------
    // Directory iterator functions.
    //-----------------------------------------------------------------------------
    /**
     * Iterates over the files in a directory and works with custom wrappers.
     *
     * @param string   $path Path to open (e.g., "s3://foo/bar").
     * @param resource $context Stream wrapper context.
     *
     * @return \Generator Yields relative filename strings.
     */
    function dir_iterator($path, $context = null)
    {
    }
    /**
     * Returns a recursive directory iterator that yields absolute filenames.
     *
     * This iterator is not broken like PHP's built-in DirectoryIterator (which
     * will read the first file from a stream wrapper, then rewind, then read
     * it again).
     *
     * @param string   $path    Path to traverse (e.g., s3://bucket/key, /tmp)
     * @param resource $context Stream context options.
     *
     * @return \Generator Yields absolute filenames.
     */
    function recursive_dir_iterator($path, $context = null)
    {
    }
    //-----------------------------------------------------------------------------
    // Misc. functions.
    //-----------------------------------------------------------------------------
    /**
     * Debug function used to describe the provided value type and class.
     *
     * @param mixed $input
     *
     * @return string Returns a string containing the type of the variable and
     *                if a class is provided, the class name.
     */
    function describe_type($input)
    {
    }
    /**
     * Creates a default HTTP handler based on the available clients.
     *
     * @return callable
     */
    function default_http_handler()
    {
    }
    /**
     * Gets the default user agent string depending on the Guzzle version
     *
     * @return string
     */
    function default_user_agent()
    {
    }
    /**
     * Get the major version of guzzle that is installed.
     *
     * @internal This function is internal and should not be used outside aws/aws-sdk-php.
     * @return int
     * @throws \RuntimeException
     */
    function guzzle_major_version()
    {
    }
    /**
     * Serialize a request for a command but do not send it.
     *
     * Returns a promise that is fulfilled with the serialized request.
     *
     * @param CommandInterface $command Command to serialize.
     *
     * @return RequestInterface
     * @throws \RuntimeException
     */
    function serialize(\MultilingualPress\Vendor\Aws\CommandInterface $command)
    {
    }
    /**
     * Retrieves data for a service from the SDK's service manifest file.
     *
     * Manifest data is stored statically, so it does not need to be loaded more
     * than once per process. The JSON data is also cached in opcache.
     *
     * @param string $service Case-insensitive namespace or endpoint prefix of the
     *                        service for which you are retrieving manifest data.
     *
     * @return array
     * @throws \InvalidArgumentException if the service is not supported.
     */
    function manifest($service = null)
    {
    }
    /**
     * Checks if supplied parameter is a valid hostname
     *
     * @param string $hostname
     * @return bool
     */
    function is_valid_hostname($hostname)
    {
    }
    /**
     * Checks if supplied parameter is a valid host label
     *
     * @param $label
     * @return bool
     */
    function is_valid_hostlabel($label)
    {
    }
    /**
     * Ignores '#' full line comments, which parse_ini_file no longer does
     * in PHP 7+.
     *
     * @param $filename
     * @param bool $process_sections
     * @param int $scanner_mode
     * @return array|bool
     */
    function parse_ini_file($filename, $process_sections = \false, $scanner_mode = \INI_SCANNER_NORMAL)
    {
    }
    /**
     * Outputs boolean value of input for a select range of possible values,
     * null otherwise
     *
     * @param $input
     * @return bool|null
     */
    function boolean_value($input)
    {
    }
    /**
     * Parses ini sections with subsections (i.e. the service section)
     *
     * @param $filename
     * @param $filename
     * @return array
     */
    function parse_ini_section_with_subsections($filename, $section_name)
    {
    }
    /**
     * Checks if an input is a valid epoch time
     *
     * @param $input
     * @return bool
     */
    function is_valid_epoch($input)
    {
    }
    /**
     * Checks if an input is a fips pseudo region
     *
     * @param $region
     * @return bool
     */
    function is_fips_pseudo_region($region)
    {
    }
    /**
     * Returns a region without a fips label
     *
     * @param $region
     * @return string
     */
    function strip_fips_pseudo_regions($region)
    {
    }
    /**
     * Checks if an array is associative
     *
     * @param array $array
     *
     * @return bool
     */
    function is_associative(array $array): bool
    {
    }
}
namespace Inpsyde\MultilingualPress {
    //phpcs:enable
    const FUNCTIONS_LOADED = 1;
}
namespace Inpsyde\MultilingualPress {
    const ACTION_ACTIVATION = 'multilingualpress.activation';
    const ACTION_ADD_SERVICE_PROVIDERS = 'multilingualpress.add_service_providers';
    const ACTION_LOG = 'multilingualpress.log';
    const MULTILINGUALPRESS_LICENSE_API_URL = "https://multilingualpress.org";
}
namespace Inpsyde\MultilingualPress {
    /**
     * Resolves the value with the given name from the container.
     *
     * @param string|null $name
     * @return mixed
     *
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    function resolve(string $name = null)
    {
    }
    /**
     * Checks if MultilingualPress debug mode is on.
     *
     * @return bool
     */
    function isDebugMode(): bool
    {
    }
    /**
     * Checks if either MultilingualPress or WordPress script debug mode is on.
     *
     * @return bool
     */
    function isScriptDebugMode(): bool
    {
    }
    /**
     * Checks if either MultilingualPress or WordPress debug mode is on.
     *
     * @return bool
     */
    function isWpDebugMode(): bool
    {
    }
    /**
     * Check if the plugin need license or not
     *
     * @return bool
     */
    function isLicensed(): bool
    {
    }
    /**
     * Returns the given content ID, if valid, and the ID of the queried object otherwise.
     *
     * @param int $contentId
     * @return int
     */
    function defaultContentId(int $contentId): int
    {
    }
    /**
     * Print the setting page header
     *
     * @param \WP_Site $site
     * @param string $id
     */
    function settingsPageHead(\WP_Site $site, string $id): void
    {
    }
    /**
     * Add error messages to the settings_errors transient.
     *
     * @param array $errors
     * @param string $setting
     * @param string $type
     */
    function settingsErrors(array $errors, string $setting, string $type): void
    {
    }
    /**
     * Redirects to the given URL (or the referer) after a settings update request.
     *
     * @param string $url
     * @param string $setting
     * @param string $code
     */
    function redirectAfterSettingsUpdate(string $url = '', string $setting = 'mlp-setting', string $code = 'mlp-setting'): void
    {
    }
    /**
     * Checks if the site with the given ID exists (within the current or given network)
     * and is not marked as deleted.
     *
     * @param int $siteId
     * @param int $networkId
     * @return bool
     */
    function siteExists(int $siteId, int $networkId = 0): bool
    {
    }
    /**
     * Checks if a given table exists within the database.
     *
     * @param string $tableName
     * @return bool
     */
    function tableExists(string $tableName): bool
    {
    }
    /**
     * Wrapper for the exit language construct.
     *
     * Introduced to allow for easy unit testing.
     *
     * @param int|string $message
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    function callExit($message = ''): void
    {
    }
    /**
     * Renders the HTML string for the hidden nonce field according to the given nonce object.
     *
     * @param Nonce $nonce
     * @param bool $withReferer
     */
    function printNonceField(\Inpsyde\MultilingualPress\Framework\Nonce\Nonce $nonce, bool $withReferer = \true): void
    {
    }
    /**
     * Combine Attributes
     *
     * @param array $pairs
     * @param array $atts
     * @return array
     */
    function combineAtts(array $pairs, array $atts): array
    {
    }
    /**
     * Array to attributes
     *
     * @param array $attributes
     * @param bool $xml
     * @return string
     */
    // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
    function arrayToAttrs(array $attributes, $xml = \false): string
    {
    }
    /**
     * Proxy for WordPress defined callbacks
     *
     * This function is used when we have to call one of our methods but the callback is hooked into
     * a WordPress filter or action.
     *
     * Since isn't possible to ensure third party plugins will pass the correct data declared
     * by WordPress we need a way to prevent fatal errors without introduce complexity.
     *
     * In this case, this function will allow us to maintain our type hints and in case something wrong
     * happen we rise a E_USER_NOTICE error so the issue get logged and also firing an action we allow
     * use or third party developer to be able to perform a more accurate debug.
     *
     * @param callable $callback
     * @return callable
     * @throws Throwable In case WP_DEBUG is active
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
     */
    function wpHookProxy(callable $callback): callable
    {
    }
    /**
     * Convert String To Boolean
     *
     * This function is the same of wc_string_to_bool.
     *
     * @param string $value The string to convert to boolean. 'yes', 'true', '1' are converted to true.
     *
     * @return bool True or false depending on the passed value.
     */
    function stringToBool(string $value): bool
    {
    }
    /**
     * Convert Boolean to String
     *
     * This function is the same of wc_bool_to_string
     *
     * @param bool $bool The bool value to convert.
     *
     * @return string The converted value. 'yes' or 'no'.
     */
    function boolToString(bool $bool): string
    {
    }
    /**
     * @return string
     */
    function wpVersion(): string
    {
    }
    /**
     * Sanitize Html Class
     *
     * @param string|array|\Traversable|mixed $class
     * @return string
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    function sanitizeHtmlClass($class): string
    {
    }
    /**
     * Will add the request params to given url.
     *
     * @param string $url The URL.
     * @return string The URL.
     */
    function preserveUrlRequestParams(string $url): string
    {
    }
}
namespace Inpsyde\MultilingualPress {
    /**
     * Returns the content IDs of all translations for the given content element data.
     *
     * @param int $contentId
     * @param string $type
     * @param int $siteId
     * @return int[]
     * @throws NonexistentTable
     */
    function translationIds(int $contentId = 0, string $type = 'post', int $siteId = 0): array
    {
    }
    /**
     * Returns the MultilingualPress language for the site with the given ID.
     *
     * @param int $siteId
     * @return string
     * @throws NonexistentTable
     */
    function siteLocale(int $siteId = 0): string
    {
    }
    /**
     * Returns the MultilingualPress language for the site with the given ID.
     *
     * @param int $siteId
     * @return string
     */
    function siteLanguageTag(int $siteId = 0): string
    {
    }
    /**
     * Returns the MultilingualPress locale name for the site with the given ID.
     *
     * @param int $siteId
     * @return string
     * @throws NonexistentTable
     */
    function siteLocaleName(int $siteId = 0): string
    {
    }
    /**
     * Returns the MultilingualPress language name for the site with the given ID.
     *
     * @param int $siteId
     * @return string
     * @throws NonexistentTable
     */
    function siteLanguageName(int $siteId = 0): string
    {
    }
    /**
     * Returns the MultilingualPress site name (which includes site name and site language) for the site
     * with the given ID.
     *
     * @param int $siteId
     * @return string
     * @throws NonexistentTable
     */
    function siteNameWithLanguage(int $siteId = 0): string
    {
    }
    /**
     * Return all available languages, including default and DB.
     * Array keys are BCP-47 tags.
     *
     * @return Language[]
     * @throws NonexistentTable
     */
    function allLanguages(): array
    {
    }
    /**
     * Returns the names of all available languages according to the given arguments.
     *
     * @param bool $onlyRelatedToCurrentSite
     * @param bool $includeCurrentSite
     * @return string[]
     * @throws NonexistentTable
     */
    function assignedLanguageNames(bool $onlyRelatedToCurrentSite = \true, bool $includeCurrentSite = \true): array
    {
    }
    /**
     * Retrieves a map of site IDs to languages, which the given user is assigned.
     *
     * @param int $userId The user ID.
     *
     * @return array<int, string> A map of site IDs to languages.
     * @throws NonexistentTable
     */
    function assignedLanguagesForUser(int $userId): array
    {
    }
    /**
     * Returns the individual MultilingualPress language code of all (related) sites.
     *
     * @param bool $relatedSitesOnly
     * @param bool $includeCurrentSite
     * @return string[]
     * @throws NonexistentTable
     */
    function assignedLanguageTags(bool $relatedSitesOnly = \true, bool $includeCurrentSite = \true): array
    {
    }
    /**
     * Returns the individual MultilingualPress language object of all (related) sites.
     *
     * @param bool $relatedSitesOnly
     * @return Language[]
     * @throws NonexistentTable
     */
    function assignedLanguages(bool $relatedSitesOnly = \true): array
    {
    }
    /**
     * Returns the MultilingualPress language for the current site.
     *
     * @return string
     * @throws NonexistentTable
     */
    function currentSiteLocale(): string
    {
    }
    /**
     * Returns the language with the given BCP-47 tag.
     *
     * @param string $bcp47tag
     * @return Language
     * @throws NonexistentTable
     */
    function languageByTag(string $bcp47tag): \Inpsyde\MultilingualPress\Framework\Language\Language
    {
    }
    /**
     * @return Language[]
     */
    function allDefaultLanguages(): array
    {
    }
    /**
     * @param string $bcp47tag
     * @return Language
     */
    function defaultLanguageByTag(string $bcp47tag): \Inpsyde\MultilingualPress\Framework\Language\Language
    {
    }
}
namespace MultilingualPress\Vendor {
    /** @var array $context */
    // phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
    $languages = $context['languages'] ?? [];
}
