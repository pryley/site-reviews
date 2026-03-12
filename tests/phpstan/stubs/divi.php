<?php

namespace ET\Builder\FrontEnd\BlockParser {
    /**
     * Class BlockParserFrame
     *
     * Holds partial blocks in memory while parsing
     *
     * @internal
     * @since ??
     */
    class BlockParserFrame extends \WP_Block_Parser_Frame
    {
        /**
         * Full or partial block
         *
         * @since ??
         * @var BlockParserBlock
         */
        public $block;
        /**
         * Byte offset into document for start of parse token
         *
         * @since ??
         * @var int
         */
        public $token_start;
        /**
         * Byte length of entire parse token string
         *
         * @since ??
         * @var int
         */
        public $token_length;
        /**
         * Byte offset into document for after parse token ends
         * (used during reconstruction of stack into parse production)
         *
         * @since ??
         * @var int
         */
        public $prev_offset;
        /**
         * Byte offset into document where leading HTML before token starts
         *
         * @since ??
         * @var int
         */
        public $leading_html_start;
        /**
         * Create an instance of `BlockParserFrame`.
         *
         * Create an instance of `BlockParserFrame` and populate the object properties from the provided arguments.
         *
         * @since ??
         *
         * @param BlockParserBlock $block              Full or partial block.
         * @param int              $token_start        Byte offset into document for start of parse token.
         * @param int              $token_length       Byte length of entire parse token string.
         * @param int              $prev_offset        Optional. Byte offset into document for after parse token ends. Default `null`.
         * @param int              $leading_html_start Optional. Byte offset into document where leading HTML before token starts. Default `null`.
         */
        public function __construct($block, $token_start, $token_length, $prev_offset = null, $leading_html_start = null)
        {
        }
    }
    /**
     * Simple Gutenberg Block Parser
     *
     * A lightweight parser for extracting Gutenberg block information from content.
     * This parser provides basic block extraction functionality without the complexity
     * of maintaining hierarchical relationships or advanced parsing features.
     *
     * Key Features:
     * - Extracts block name and attributes from Gutenberg block comments
     * - Built-in caching for performance optimization
     * - Simple, flat array output structure
     * - Error handling for malformed JSON attributes
     *
     * Limitations:
     * - Does NOT preserve parent-child block relationships
     * - Does NOT maintain block order indices
     * - Does NOT support nested block structures
     * - Does NOT parse block content (only comments)
     * - Does NOT provide block positioning information
     *
     * Use Cases:
     * - Quick block identification and attribute extraction
     * - Performance-critical scenarios where full parsing is unnecessary
     * - Simple block analysis and filtering operations
     * - Basic block metadata extraction for processing
     *
     * @since ??
     */
    class SimpleBlockParser
    {
        /**
         * Parse Gutenberg blocks from content as a flattened array.
         *
         * This method extracts Gutenberg block information from content and returns
         * a simplified, flattened array structure. It does NOT preserve parent-child
         * relationships, block order index, or other advanced hierarchical information
         * that would be available in a full block parser. Each block is treated as
         * an independent entity with only basic name and attributes data.
         *
         * The parsed results are cached using MD5 hash of the content for performance.
         * Note: Caching is disabled when a filter function is provided.
         *
         * @param string $content The content containing Gutenberg block comments to parse.
         * @param array  $options {
         *    Optional. Configuration options for parsing behavior.
         *
         *    @type string $blockName Optional. The specific block name to filter for (e.g., 'divi/button').
         *                           When provided, only blocks matching this exact name will be returned.
         *                           Split with comma to match multiple block names.
         *                           When empty, all blocks in the content will be parsed.
         *    @type callable $filter Optional. A callable function to filter blocks after parsing.
         *                          The function receives a SimpleBlock instance and should return
         *                          true to include the block, false to exclude it.
         *                          Example: function( SimpleBlock $block ) { return 'divi/button' === $block->name(); }.
         *                          Note: Providing a filter disables caching for this parse operation.
         *    @type bool $excludeError Optional. Whether to exclude blocks with parsing errors. Default true.
         *                             When true, blocks with errors (malformed JSON, missing names) are excluded.
         *                             When false, all blocks are included regardless of parsing errors.
         *    @type int $limit Optional. Maximum number of blocks to return. Default 0 (no limit).
         *                     When greater than 0, only the first N matching blocks will be returned.
         * }
         *
         * @return SimpleBlockParserStore
         */
        public static function parse(string $content, array $options = []): \ET\Builder\FrontEnd\BlockParser\SimpleBlockParserStore
        {
        }
    }
    // phpcs:disable ET.Sniffs.ValidVariableName.PropertyNotSnakeCase -- WP use snakeCase in \WP_Block_Parser_Block
    // phpcs:disable ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- WP use snakeCase in \WP_Block_Parser_Block
    /**
     * Class BlockParserBlock
     *
     * Holds the block structure in memory
     *
     * @since ??
     */
    class BlockParserBlock extends \WP_Block_Parser_Block
    {
        /**
         * The order index of the block.
         *
         * @since ??
         *
         * @var int
         */
        public $orderIndex;
        /**
         * The index of the block that will be used to sort blocks list.
         *
         * @since ??
         *
         * @var int
         */
        public $index;
        /**
         * The unique ID of the block.
         *
         * @since ??
         *
         * @var string
         */
        public $id;
        /**
         * The parent ID of the block.
         *
         * @since ??
         *
         * @var string
         */
        public $parentId;
        /**
         * The BlockParserStore class instance where this block stored
         *
         * @since ??
         *
         * @var int
         */
        public $storeInstance;
        /**
         * List of inner blocks (of this same class)
         *
         * @since ??
         *
         * @var BlockParserBlock[]
         */
        public $innerBlocks;
        /**
         * Layout type where this block is being rendered.
         *
         * @since ??
         *
         * @var string
         */
        public $layout_type;
        /**
         * Create an instance of `BlockParserBlock`.
         *
         * Will populate object properties from the provided arguments.
         *
         * @since ??
         *
         * @param string $name           Name of block.
         * @param array  $attrs          Optional set of attributes from block comment delimiters.
         * @param array  $inner_blocks   List of inner blocks (of this same class: `BlockParserBlock`).
         * @param string $inner_html     Resultant HTML from inside block comment delimiters after removing inner blocks.
         * @param array  $inner_content  List of string fragments and null markers where inner blocks were found.
         * @param int    $store_instance The store instance where this block will be stored.
         * @param string $parent_id      Optional. The parent ID of the block. Default `divi/root`.
         * @param string $layout_type    Optional. The layout type of the block. Default `default`.
         */
        public function __construct($name, $attrs, $inner_blocks, $inner_html, $inner_content, $store_instance, $parent_id = 'divi/root', $layout_type = 'default')
        {
        }
        /**
         * Reset order indexes data
         *
         * Resets all module order indexes using the central module order manager.
         *
         * @since ??
         *
         * @return void
         */
        public static function reset_order_index()
        {
        }
        /**
         * Merges module attributes with preset and group preset attributes.
         *
         * This method retrieves and merges attributes from a specified module,
         * its selected preset, and any applicable group presets.
         *
         * @since ??
         *
         * @return array The merged attributes array.
         */
        public function get_merged_attrs(): array
        {
        }
    }
    // phpcs:disable ET.Sniffs.ValidVariableName.PropertyNotSnakeCase -- WP use snakeCase in \WP_Block_Parser_Block
    // phpcs:disable ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase -- WP use snakeCase in \WP_Block_Parser_Block
    /**
     * Class BlockParserBlockRoot
     *
     * Holds the block structure in memory
     *
     * @since ??
     */
    class BlockParserBlockRoot extends \ET\Builder\FrontEnd\BlockParser\BlockParserBlock
    {
        /**
         * Create an instance of `BlockParserBlockRoot`.
         *
         * Root is a read-only and unique block. It can only to be added using this method.
         * The `innerBlocks` data will be populated when calling `BlockParserStore::get('divi/root')`.
         *
         * @since ??
         *
         * @param int $store_instance The store instance where this block will be stored.
         */
        public function __construct($store_instance)
        {
        }
    }
    /**
     * Simple Block class for parsing and managing individual block data.
     *
     * This class represents a single parsed block within the Divi 5 block parser system.
     * It encapsulates block data including raw content, name, attributes, JSON representation,
     * and error status, providing a consistent interface for accessing block information.
     *
     * @since 5.0.0
     */
    class SimpleBlock
    {
        /**
         * Constructor for SimpleBlock.
         *
         * Initializes a new SimpleBlock instance with the provided block data.
         * All properties are set from the data array passed to the constructor.
         *
         * @since 5.0.0
         *
         * @param array $data {
         *     Block data array containing parsed block information.
         *
         *     @type string $raw   Original raw block string.
         *     @type string $name  Block type name/identifier.
         *     @type array  $attrs Block attributes and configuration.
         *     @type string $json  JSON representation of block data.
         *     @type bool   $error Whether parsing encountered an error.
         * }
         */
        public function __construct(array $data)
        {
        }
        /**
         * Get the raw block string.
         *
         * Returns the original, unparsed block content as it appeared in the post content.
         *
         * @since 5.0.0
         *
         * @return string The raw block string, or empty string if not set.
         */
        public function raw(): string
        {
        }
        /**
         * Get the block name.
         *
         * Returns the block type identifier that determines which Divi module
         * or component this block represents.
         *
         * @since 5.0.0
         *
         * @return string The block name/type, or empty string if not set.
         */
        public function name(): string
        {
        }
        /**
         * Get the block attributes.
         *
         * Returns the associative array containing all configuration data
         * and settings for this block instance.
         *
         * @since 5.0.0
         *
         * @return array The block attributes array, or empty array if not set.
         */
        public function attrs(): array
        {
        }
        /**
         * Get the block JSON representation.
         *
         * Returns the JSON-encoded string representation of the block data,
         * typically used for serialization and data transfer operations.
         *
         * @since 5.0.0
         *
         * @return string The block JSON string, or empty string if not set.
         */
        public function json(): string
        {
        }
        /**
         * Get the block error status.
         *
         * Returns whether an error occurred during the parsing process of this block.
         *
         * @since 5.0.0
         *
         * @return bool True if there was a parsing error, false if successful.
         */
        public function error(): bool
        {
        }
    }
    /**
     * Simple Block Parser Store class.
     *
     * A storage container for managing collections of parsed SimpleBlock objects.
     * This class provides a simple interface for adding blocks to a collection
     * and retrieving the complete collection when needed.
     *
     * The store is designed to be lightweight and efficient for use during the
     * block parsing process, allowing for incremental addition of blocks as they
     * are processed.
     *
     * Implements Iterator to allow direct iteration over the stored blocks using
     * foreach loops and other iteration constructs. Implements Countable to enable
     * using the count() function directly on store instances.
     *
     * Usage Examples:
     *
     *     // Initialize with blocks.
     *     $store = new SimpleBlockParserStore( $initial_blocks );
     *
     *     // Add blocks incrementally.
     *     $store->add( $new_block );
     *
     *     // Find a specific block.
     *     $block = $store->find( function( $block ) {
     *         return 'divi/cta' === $block->blockName;
     *     } );
     *
     *     // Filter blocks by criteria.
     *     $filtered_store = $store->filter( function( $block ) {
     *         return 'divi/section' === $block->blockName;
     *     } );
     *
     *     // Iterate over blocks.
     *     foreach ( $store as $index => $block ) {
     *         // Process each block.
     *     }
     *
     *     // Get block count.
     *     $total = count( $store );
     *
     *     // Get all blocks as array.
     *     $all_blocks = $store->results();
     *
     * @since ??
     *
     * @see SimpleBlock For the block objects stored in this collection.
     * @see Iterator For iteration capabilities.
     * @see Countable For counting capabilities.
     */
    class SimpleBlockParserStore implements \Iterator, \Countable
    {
        /**
         * Constructor.
         *
         * Initializes the store with an optional collection of SimpleBlock objects.
         * If no blocks are provided, the store will be initialized as empty and
         * blocks can be added later using the add() method.
         *
         * @since ??
         *
         * @param SimpleBlock[] $results Initial collection of parsed blocks. Can be
         *                               an empty array if starting with no blocks.
         *
         * @see add() For adding blocks after initialization.
         */
        public function __construct(array $results)
        {
        }
        /**
         * Add a new block to the collection.
         *
         * Appends a SimpleBlock object to the end of the current collection.
         * The block will be added in the order it was received, maintaining
         * insertion order within the store. This will increment the collection
         * count and make the block available during iteration.
         *
         * @since ??
         *
         * @param SimpleBlock $block The block object to add to the collection.
         *                           Must be a valid SimpleBlock instance.
         *
         * @return void
         *
         * @see results() For retrieving the complete collection including added blocks.
         * @see count() For getting the total number of blocks after addition.
         */
        public function add(\ET\Builder\FrontEnd\BlockParser\SimpleBlock $block)
        {
        }
        /**
         * Get the complete collection of stored blocks.
         *
         * Returns all SimpleBlock objects currently stored in this collection,
         * including both blocks provided during initialization and any blocks
         * added subsequently via the add() method.
         *
         * The returned array maintains the original insertion order and contains
         * references to the actual SimpleBlock objects (not copies).
         *
         * This method is useful when you need the blocks as an array for operations
         * like array_map() or array_filter(). For sequential access, you can also
         * iterate directly over the store object using foreach.
         *
         * @since ??
         *
         * @return SimpleBlock[] The complete collection of parsed blocks.
         *
         * @see add() For adding blocks to the collection.
         * @see count() For getting the number of blocks without retrieving them.
         * @see current() For accessing blocks during iteration.
         */
        public function results(): array
        {
        }
        /**
         * Find the first block that matches the test function.
         *
         * Searches through all SimpleBlock objects in the collection and returns
         * the first block that satisfies the provided test function. If no blocks
         * match, returns null.
         *
         * The test function receives three parameters:
         * - SimpleBlock $block: The current block being tested
         * - int $index: The current index in the collection
         * - SimpleBlock[] $blocks: The complete array of blocks
         *
         * The test function should return true when a match is found.
         *
         * Example usage:
         *
         *     // Find a block by name.
         *     $cta_block = $store->find( function( $block ) {
         *         return 'divi/cta' === $block->blockName;
         *     } );
         *
         *     // Find a block with specific attribute.
         *     $block = $store->find( function( $block ) {
         *         return isset( $block->attrs['custom_id'] ) && 'my-id' === $block->attrs['custom_id'];
         *     } );
         *
         * @since ??
         *
         * @param callable $test_function Function that will be invoked to test each block.
         *                                Must return true for a match to be found.
         *
         * @return SimpleBlock|null The first matching block or null if no match is found.
         *
         * @see results() For retrieving all blocks as an array.
         * @see ArrayUtility::find() For the underlying implementation.
         */
        public function find(callable $test_function): ?\ET\Builder\FrontEnd\BlockParser\SimpleBlock
        {
        }
        /**
         * Filter blocks based on a test function.
         *
         * Creates and returns a new SimpleBlockParserStore containing only the blocks
         * that satisfy the provided test function. This method does not modify the
         * original store. If no blocks match the test function, an empty store is returned.
         *
         * The test function receives three parameters:
         * - SimpleBlock $block: The current block being tested
         * - int $index: The current index in the collection
         * - SimpleBlock[] $blocks: The complete array of blocks
         *
         * The test function should return true to include the block in the result.
         *
         * This function is equivalent to PHP's array_filter() and JavaScript's
         * Array.prototype.filter().
         *
         * Example usage:
         *
         *     // Filter blocks by name.
         *     $cta_blocks = $store->filter( function( $block ) {
         *         return 'divi/cta' === $block->blockName;
         *     } );
         *
         *     // Filter blocks with specific attribute.
         *     $blocks_with_id = $store->filter( function( $block ) {
         *         return isset( $block->attrs['custom_id'] );
         *     } );
         *
         *     // Filter and chain operations.
         *     $visible_sections = $store->filter( function( $block ) {
         *         return 'divi/section' === $block->blockName;
         *     } );
         *     foreach ( $visible_sections as $section ) {
         *         // Process visible sections.
         *     }
         *
         * @since ??
         *
         * @param callable $test_function Function that will be invoked to test each block.
         *                                Must return true to include the block in the result.
         *
         * @return SimpleBlockParserStore A new store containing only matching blocks.
         *
         * @see find() For finding a single matching block.
         * @see results() For retrieving all blocks as an array.
         */
        public function filter(callable $test_function): \ET\Builder\FrontEnd\BlockParser\SimpleBlockParserStore
        {
        }
        /**
         * Get the number of blocks in the collection.
         *
         * Returns the total number of SimpleBlock objects currently stored in this
         * collection, including both blocks provided during initialization and any
         * blocks added subsequently via the add() method.
         *
         * This method implements the Countable interface, allowing the store to be
         * used directly with PHP's count() function:
         *
         *     $total = count( $store );
         *
         * @since ??
         *
         * @return int The number of blocks in the collection.
         *
         * @see add() For adding blocks to the collection.
         * @see results() For retrieving all blocks as an array.
         */
        public function count(): int
        {
        }
        /**
         * Rewind the Iterator to the first element.
         *
         * Resets the internal position pointer to the beginning of the collection.
         * This method is part of the Iterator interface and is automatically called
         * at the start of a foreach loop. It should not typically be called directly.
         *
         * @since ??
         *
         * @return void
         *
         * @see Iterator::rewind() For interface documentation.
         * @see current() For retrieving the current element.
         * @see valid() For checking if the current position is valid.
         */
        public function rewind(): void
        {
        }
        /**
         * Return the current element.
         *
         * Returns the SimpleBlock object at the current iteration position. This
         * method is part of the Iterator interface and is automatically called during
         * foreach iteration. It should not typically be called directly.
         *
         * @since ??
         *
         * @return SimpleBlock|null The current block or null if position is invalid.
         *
         * @see Iterator::current() For interface documentation.
         * @see key() For retrieving the current position index.
         * @see valid() For checking if the current position is valid.
         */
        public function current(): ?\ET\Builder\FrontEnd\BlockParser\SimpleBlock
        {
        }
        /**
         * Return the key of the current element.
         *
         * Returns the current numeric index in the blocks collection. This method
         * is part of the Iterator interface and is automatically called during
         * foreach iteration to provide the iteration key. It should not typically
         * be called directly.
         *
         * @since ??
         *
         * @return int The current position index.
         *
         * @see Iterator::key() For interface documentation.
         * @see current() For retrieving the current element.
         */
        public function key(): int
        {
        }
        /**
         * Move forward to next element.
         *
         * Advances the internal position pointer to the next element in the
         * collection. This method is part of the Iterator interface and is
         * automatically called at the end of each iteration in a foreach loop.
         * It should not typically be called directly.
         *
         * @since ??
         *
         * @return void
         *
         * @see Iterator::next() For interface documentation.
         * @see valid() For checking if the next position is valid.
         * @see current() For retrieving the element at the new position.
         */
        public function next(): void
        {
        }
        /**
         * Check if current position is valid.
         *
         * Determines whether the current position points to a valid element in the
         * collection. Returns false when iteration has completed. This method is
         * part of the Iterator interface and is automatically called during foreach
         * iteration to determine if the loop should continue. It should not typically
         * be called directly.
         *
         * @since ??
         *
         * @return bool True if the current position is valid, false otherwise.
         *
         * @see Iterator::valid() For interface documentation.
         * @see current() For retrieving the current element.
         * @see next() For advancing to the next element.
         */
        public function valid(): bool
        {
        }
    }
    /**
     * Class BlockParser
     *
     * Parses a document and constructs a list of parsed block objects
     *
     * @since ??
     */
    class BlockParser extends \WP_Block_Parser
    {
        /**
         * It's a property that is used to store the instance of the BlockParserStore class.
         *
         * @since ??
         *
         * @var number
         */
        protected $_store_instance = null;
        /**
         * An array to hold empty attributes for a block.
         *
         * @since ??
         *
         * @var array
         */
        public $empty_attrs = [];
        /**
         * An array to hold the modules that have been loaded.
         *
         * @since ??
         *
         * @var array
         */
        protected static $_modules_loaded = null;
        /**
         * Get the instance of the BlockParserStore class
         *
         * @since ??
         *
         * @return number
         */
        public function get_store_instance()
        {
        }
        /**
         * Gets the block class map list.
         *
         * @since ??
         *
         * @return array
         */
        public static function get_block_class_map_list()
        {
        }
        /**
         * Load the module corresponding to the given block name
         *
         * @param string $block_name The name of the block to load the module for.
         */
        protected static function _load_module_from_block_name($block_name)
        {
        }
        /**
         * Processes the next token from the input document
         * and returns whether to proceed processing more tokens
         *
         * This is the "next step" function that essentially
         * takes a token as its input and decides what to do
         * with that token before descending deeper into a
         * nested block tree or continuing along the document
         * or breaking out of a level of nesting.
         *
         * @internal
         * @since ??
         *
         * @return bool
         */
        public function proceed()
        {
        }
        /**
         * Returns a new block object for freeform HTML
         *
         * @internal
         * @since ??
         *
         * @param string $inner_html HTML content of block.
         *
         * @return WP_Block_Parser_Block|BlockParserBlock A freeform block object.
         */
        public function freeform($inner_html)
        {
        }
        /**
         * Combine post attributes with local attributes.
         *
         * Combine by swapping out the values of the post attributes with the local attributes.
         *
         * @since ??
         *
         * @param array $attrs Original attributes (passed by reference).
         * @param array $local_attrs Local attributes.
         *
         * @return array Combined attributes.
         */
        public function combine_local_attrs(array &$attrs, array $local_attrs): array
        {
        }
        /**
         * Get `post_content` of a global layout if the post exists and matches the given arguments.
         *
         * This helper is intended to simplify the way to get `post_content` object of a global layout since we already know the ID.
         * Instead of using the complex and heavy `WP_Query` class, we use the light and cached `get_post` build-in function.
         *
         * @since ??
         *
         * @param string  $content The content of the global layout.
         * @param string  $post_id The ID of the post.
         * @param array   $fields Optional. An array of `key => value` arguments to match against the post object. Default `[]`.
         * @param array   $capabilities Optional. An array of user capability to match against the current user. Defaults `[]`.
         * @param boolean $mask_post_password Optional. Whether to mask `post_password` field. Default `true`.
         * @param string  $inner_html Optional. The innerHTML content from global-layout block for local children. Default `''`.
         *
         * @return string|null The post content or null on failure.
         */
        public function get_global_layout_content(string $content, string $post_id, array $fields = array(), array $capabilities = array(), bool $mask_post_password = true, string $inner_html = '')
        {
        }
        /**
         * Checks if the content includes any Divi modules.
         *
         * @param string $content The block serialized content to be checked.
         *
         * @return bool True if a Divi module is found, false otherwise.
         */
        public static function has_any_divi_block($content)
        {
        }
        /**
         * Parses a document and returns a list of block structures
         *
         * When encountering an invalid parse will return a best-effort
         * parse. In contrast to the specification parser this does not
         * return an error on invalid inputs.
         *
         * @since ??
         *
         * @param string $document Input document to be parsed.
         *
         * @return BlockParserBlock[]
         */
        public function parse($document)
        {
        }
    }
    /**
     * Class BlockParserStore
     *
     * Holds the block structure in memory as flatten associative array. This class is counterparts of EditPostStore in VB, with a slight
     * difference that this class can have multiple instances. A new store instance will be created when `do_blocks` function is invoked. This is intended to prevent
     * the data for previous call of `do_blocks` get overridden by a later call of `do_blocks`.
     * Each item stored in the store will have a `storeInstance` property that hold the data to which store instance is the item belongs to.
     *
     * @since ??
     */
    class BlockParserStore
    {
        /**
         * Check if currently rendering inner content.
         *
         * Returns the current state of the inner content rendering flag. This is used
         * to determine whether the block parser is in the middle of processing inner
         * content, which helps prevent infinite loops and ensures proper parsing behavior.
         *
         * @since ??
         * @return bool True if currently rendering inner content, false otherwise.
         */
        public static function is_rendering_inner_content(): bool
        {
        }
        /**
         * Set the inner content rendering state.
         *
         * Controls whether the block parser is currently rendering inner content. This flag
         * is used to track parsing state and help prevent infinite loops or incorrect parsing
         * behavior when processing nested blocks.
         *
         * @since ??
         * @param bool $state True if currently rendering inner content, false otherwise.
         * @return void
         */
        public static function set_rendering_inner_content(bool $state): void
        {
        }
        /**
         * Renders inner content by applying WordPress the_content filter while setting appropriate internal state.
         *
         * This method temporarily enables inner content rendering state, applies the 'the_content' filter
         * to the provided content, then disables the inner content rendering state. This is used for
         * rendering content within blocks while ensuring that the rendering state is properly tracked
         * by the BlockParserStore instance.
         *
         * The inner content rendering state is used by other methods in this class to determine
         * whether they are currently processing content that's inside a block, which may affect
         * how content is parsed or rendered.
         *
         * @since ??
         *
         * @param string $content The content to render. This content will be processed through
         *                       WordPress's 'the_content' filter chain.
         *
         * @return string The rendered content after applying WordPress content filters.
         *
         * @example
         * ```php
         * $raw_content   = 'Some content with shortcodes and HTML';
         * $rendered_html = BlockParserStore::render_inner_content( $raw_content );
         * // $rendered_html now contains processed HTML with shortcodes expanded
         * ```
         */
        public static function render_inner_content(string $content): string
        {
        }
        /**
         * Add root block.
         *
         * Root is a read-only and unique block. It can only to be added using this method.
         * The `innerBlocks` data will be populated when calling `BlockParserStore::get('divi/root')`.
         *
         * @since ??
         *
         * @param int $instance Optional. The instance of the store you want to use. Default `null`.
         *
         * @return void
         */
        protected static function _add_root($instance = null)
        {
        }
        /**
         * Add item to store.
         *
         * @since ??
         *
         * @param BlockParserBlock $block The block object.
         *
         * @return BlockParserBlock
         */
        public static function add(\ET\Builder\FrontEnd\BlockParser\BlockParserBlock $block)
        {
        }
        /**
         * Find the ancestor of existing item in the store.
         *
         * @since ??
         *
         * @param string   $child_id The unique ID of the child block.
         * @param callable $matcher  Callable function that will be invoked to determine if the ancestor is match.
         * @param int      $instance Optional. The instance of the store you want to use. Default `null`.
         *
         * @return BlockParserBlock|null
         */
        public static function find_ancestor($child_id, $matcher, $instance = null)
        {
        }
        /**
         * Get all of existing items in the store.
         *
         * @since ??
         *
         * @param int $instance The instance of the store you want to use.
         *
         * @return BlockParserBlock[]
         */
        public static function get_all($instance = null)
        {
        }
        /**
         * Get the ancestors of existing item in the store.
         *
         * @since ??
         *
         * @param string $child_id The unique ID of the child block.
         * @param int    $instance Optional. The instance of the store you want to use. Default `null`.
         *
         * @return BlockParserBlock[] An array of ancestors sorted from bottom to the very top level of the structure tree.
         */
        public static function get_ancestors($child_id, $instance = null)
        {
        }
        /**
         * Get the ancestor ids of existing item in the store.
         *
         * @since ??
         *
         * @param string   $child_id The unique ID of the child block.
         * @param int|null $instance Optional. The instance of the store you want to use. Default `null`.
         *
         * @return BlockParserBlock[] An array of ancestors sorted from bottom to the very top level of the structure tree.
         */
        public static function get_ancestor_ids(string $child_id, $instance = null): array
        {
        }
        /**
         * Get the ancestor of existing item in the store.
         *
         * @since ??
         *
         * @param string   $child_id The unique ID of the child block.
         * @param callable $matcher  Optional.
         *                           Callable function that will be invoked to determine to return early if it returns a `true`.
         *                           If not provided, it will match up to the very top level of the structure tree.
         *                           Default `null`.
         * @param int      $instance Optional. The instance of the store you want to use. Default `null`.
         *
         * @return BlockParserBlock|null
         */
        public static function get_ancestor($child_id, $matcher = null, $instance = null)
        {
        }
        /**
         * Get an array of all the children of a given block.
         *
         * @since ??
         *
         * @param string $id       The id of the block you want to get.
         * @param int    $instance Optional. The instance of the store you want to use. Default `null`.
         *
         * @return BlockParserBlock[] An array of the children of the block.
         */
        public static function get_children($id, $instance = null)
        {
        }
        /**
         * Apply selective filtering based on localAttrsMap.
         *
         * This implements the new snapshot-based architecture where:
         * 1. localAttrs contains ALL attributes (complete snapshot)
         * 2. Template's localAttrsMap determines which attributes to use from snapshot
         * 3. Provides temporal stability - pages preserve state even if localAttrsMap changes
         *
         * @since ??
         *
         * @param array $template_attrs Template attributes (base), including localAttrsMap.
         * @param array $local_attrs    Complete attribute snapshot from serialized block.
         *
         * @return array Merged attributes with selective filtering applied.
         */
        public static function apply_local_attrs_filtering(array $template_attrs, array $local_attrs): array
        {
        }
        /**
         * Filter out whitespace-only blocks that WordPress parse_blocks() creates.
         *
         * WordPress parse_blocks() creates blocks with null blockName for whitespace/newlines between blocks.
         * When these get normalized and serialized, they become empty <!-- wp: --> blocks in rendered output.
         * This method filters them out to prevent rendering artifacts.
         *
         * @since ??
         *
         * @param array $blocks Array of parsed blocks from parse_blocks().
         * @return array Filtered blocks with whitespace-only blocks removed.
         */
        public static function _filter_whitespace_blocks(array $blocks): array
        {
        }
        /**
         * Expand placeholder-wrapped global module content for WordPress block parsing compatibility.
         *
         * Global modules are intentionally stored with placeholder wrappers around content blocks.
         * However, WordPress parse_blocks() treats this as a single block with innerHTML instead
         * of separate blocks, preventing proper selective sync attribute extraction.
         *
         * Transforms: <!-- wp:divi/placeholder --><!-- wp:divi/text {...} /--><!-- /wp:divi/placeholder -->
         * Into:       <!-- wp:divi/placeholder /-->
         *
         *             <!-- wp:divi/text {...} -->
         *             <p>Content here</p>
         *             <!-- /wp:divi/text -->
         *
         * @since ??
         *
         * @param string $content The placeholder-wrapped block content.
         * @return string The expanded block content with separate blocks.
         */
        public static function _expand_placeholder_wrapped_blocks(string $content): string
        {
        }
        /**
         * Get `post_content` of a global layout if the post exists and matches the given arguments.
         *
         * This helper is intended to simplify the way to get `post_content` object of a global layout since we already know the ID.
         * Instead of using the complex and heavy `WP_Query` class, we use the light and cached `get_post` build-in function.
         *
         * @since ??
         *
         * @param string  $content The content of the global layout.
         * @param string  $post_id The ID of the post.
         * @param array   $fields Optional. An array of `key => value` arguments to match against the post object. Default `[]`.
         * @param array   $capabilities Optional. An array of user capability to match against the current user. Default `[]`.
         * @param boolean $mask_post_password Optional. Whether to mask `post_password` field. Default `true`.
         * @param string  $inner_html Optional. The innerHTML content from global-layout block for local children. Default `''`.
         *
         * @return string|null The post content or null on failure.
         */
        public static function get_global_layout_content(string $content, string $post_id, array $fields = array(), array $capabilities = array(), bool $mask_post_password = true, string $inner_html = '')
        {
        }
        /**
         * Get the ID of the currently active store instance.
         *
         * @since ??
         *
         * @return int|null The active store instance ID. Will return `null` when no instance has been created.
         */
        public static function get_instance()
        {
        }
        /**
         * Get the parent of existing item in the store.
         *
         * @since ??
         *
         * @param string $child_id The unique ID of the child block.
         * @param int    $instance Optional. The instance of the store you want to use. Default `null`.
         *
         * @return BlockParserBlock|null
         */
        public static function get_parent($child_id, $instance = null)
        {
        }
        /**
         * Get the siblings of existing item in the store.
         *
         * @since ??
         *
         * @param string $id       The ID of the block you want to get the sibling of.
         * @param string $location Sibling location. Can be either `before` or `after`.
         * @param int    $instance Optional. The instance of the store you want to use. Default `null`.
         *
         * @return BlockParserBlock[] Array of siblings sorted from the closest sibling. Will return empty array on failure.
         */
        public static function get_siblings($id, $location, $instance = null)
        {
        }
        /**
         * Get the direct sibling of existing item in the store.
         *
         * @since ??
         *
         * @param string $id       The ID of the block you want to get the sibling of.
         * @param string $location Sibling location. Can be either `before` or `after`.
         * @param int    $instance Optional. The instance of the store you want to use. Default null.
         *
         * @return BlockParserBlock|null
         */
        public static function get_sibling($id, $location, $instance = null): ?\ET\Builder\FrontEnd\BlockParser\BlockParserBlock
        {
        }
        /**
         * Get existing item in the store.
         *
         * @since ??
         *
         * @param string $id       The unique ID of the block.
         * @param int    $instance The instance of the store you want to use.
         *
         * @return BlockParserBlock|null
         */
        public static function get($id, $instance = null)
        {
        }
        /**
         * Block Parser Store: Instance check.
         *
         * Check if a store ID exists in the current instance's `$_data`.
         *
         * @since ??
         *
         * @param int $instance The instance ID of the store.
         *
         * @return bool
         */
        public static function has_instance($instance)
        {
        }
        /**
         * Block Parser Store: Block check.
         *
         * Check if a particular block exists in the instance store.
         *
         * @since ??
         *
         * @param string $id       The unique ID of the block.
         * @param int    $instance Optional. The instance of the store you want to use. Default `null`.
         *
         * @return bool
         */
        public static function has($id, $instance = null)
        {
        }
        /**
         * Block Parser Store: Is First check.
         *
         * Check if the given block is the first block in the parent block.
         *
         * @since ??
         *
         * @param string $id       The ID of the block you want to check.
         * @param int    $instance The instance of the store you want to use.
         *
         * @return bool
         */
        public static function is_first($id, $instance = null)
        {
        }
        /**
         * Block Parser Store: Is Last check.
         *
         * Check if the given block is the last block in the parent block.
         *
         * @since ??
         *
         * @param string $id       The ID of the block you want to check.
         * @param int    $instance The instance of the store you want to use.
         *
         * @return bool
         */
        public static function is_last($id, $instance = null)
        {
        }
        /**
         * Block Parser Store: Is Nested Module.
         *
         * Check if the given block is a nested module (eg. row inside row module).
         *
         * @since ??
         *
         * @param string $id       The ID of the block you want to check.
         * @param int    $instance The instance of the store you want to use.
         *
         * @return bool
         */
        public static function is_nested_module($id, $instance = null)
        {
        }
        /**
         * Check if given block is root block.
         *
         * Checks if the given ID is equal to `divi/root`.
         *
         * @since ??
         *
         * @param string $id The ID of the block you want to check.
         */
        protected static function _is_root($id)
        {
        }
        /**
         * Set layout area before parsing module / block.
         * This allows module to know which area it is being rendered in.
         *
         * @since ??
         *
         * @param array $layout The layout area. The format is matched to layout array passed by `et_theme_builder_begin_layout` filter.
         */
        public static function set_layout($layout)
        {
        }
        /**
         * Reset layout area.
         * After any (theme builder) layout is done rendered, its layout should be reset.
         *
         * @since ??
         */
        public static function reset_layout()
        {
        }
        /**
         * Get layout type.
         *
         * @since ??
         *
         * @return string
         */
        public static function get_layout_type()
        {
        }
        /**
         * Get layout types.
         *
         * @since ??
         *
         * @return array
         */
        public static function get_layout_types()
        {
        }
        /**
         * Create or return existing instance.
         *
         * Create new store instance when no instance has created yet.
         * Otherwise returns existing latest instance.
         *
         * @since ??
         *
         * @internal Do not use this method outside the `BlockParser::parse()`.
         *
         * @return int The store instance ID.
         */
        public static function maybe_new_instance()
        {
        }
        /**
         * Create new store instance and switch to the new instance instantly.
         *
         * @since ??
         *
         * @internal Do not use this method outside the `BlockParser::parse()`.
         *
         * @return int The new store instance ID.
         */
        public static function new_instance()
        {
        }
        /**
         * Reset specific store instance.
         *
         * Will reset the store to an empty array `[]`.
         *
         * @since ??
         *
         * @param int $instance The instance of the store you want to reset.
         *
         * @return int|null The given store instance ID or `null` if the given ID is not found.
         */
        public static function reset_instance($instance)
        {
        }
        /**
         * Store active instance
         *
         * @since ??
         *
         * @var int
         */
        protected static $_instance = null;
        /**
         * Store data
         *
         * @since ??
         *
         * @var BlockParserBlock[]
         */
        protected static $_data = [];
        /**
         * Current layout area.
         *
         * @since ??
         *
         * @var array
         */
        protected static $_layout = ['id' => '', 'type' => 'default'];
        /**
         * Array of currently used layouts.
         *
         * Collect all currently used layout so when there are nested layout like body > post content, correct previous
         * layout gets restored correctly when the layout is being reset.
         *
         * @since ??
         *
         * @var array
         */
        protected static $_layouts = [];
        /**
         * Reset whole store data.
         *
         * @since ??
         */
        public static function reset()
        {
        }
        /**
         * Set property of existing block item in the store.
         *
         * @since ??
         *
         * @param string $id       The ID of the block you want to set the property for.
         * @param string $property The property/key you want to set.
         * @param mixed  $value    The value to set.
         * @param int    $instance Optional. The instance of the store you want to use. Default `null`.
         */
        public static function set_property($id, $property, $value, $instance = null)
        {
        }
        /**
         * Switch to specific store instance.
         *
         * @since ??
         *
         * @param int $instance The instance you want to switch to.
         *
         * @return int|null The previous instance before the switch. Will return null on failure or when no instance created yet.
         */
        public static function switch_instance(int $instance)
        {
        }
    }
}
namespace ET\Builder\FrontEnd\Module {
    /**
     * Frontend Script Data Class.
     *
     * The ScriptData class is used to manage and manipulate script data. It provides methods to add,
     * enqueue, retrieve, and reset script data items, as well as get information about the script.
     *
     * @since ??
     */
    class ScriptData
    {
        /**
         * Retrieves the mapping of object name to script handle.
         *
         * This function returns an associative array that maps script data keys to their corresponding script handles.
         * The mapping allows easy access to the script handle for a given object name.
         *
         * @since ??
         *
         * @return array An associative array mapping script data keys to their corresponding script handles.
         *
         * @example
         * ```php
         * $scriptMapping = get_script_mapping();
         *
         * echo $scriptMapping['object_name']; // Retrieves the script handle for the 'object_name' object
         * ```
         */
        public static function get_object_name_to_script_handle_mapping(): array
        {
        }
        /**
         * Add data item to the script data.
         *
         * This function adds a data item to the script data array. The script data array stores information
         * related to various modules and actions performed on them.
         *
         * The data item includes the action, module ID, module name, selector, hover selector, and data
         * for the different viewports. The data item ID is set to null, so the item will be appended as
         * a zero-indexed array.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments for adding the data item.
         *
         *     @type string $data_name    The name of the data. Data can have multiple data items associated with it.
         *     @type string $data_item_id The identifier for the data item.
         *     @type array  $data_item    The data item to be added.
         * }
         *
         * @return boolean Returns true if the data item was successfully added to the script data array, false otherwise.
         *
         * @example: Adding a data item to `multi_view` in the script data array.
         * ```php
         * self::add_data_item(array(
         *     'data_name'    => 'multi_view',
         *     'data_item_id' => null,
         *     'data_item'    => array(
         *         'action'        => 'setAttrs',
         *         'moduleId'      => 'divi/cta-0',
         *         'moduleName'    => 'CTA',
         *         'selector'      => '.et_pb_cta_0',
         *         'hoverSelector' => '.et_pb_cta_0_hover',
         *         'data'          => array(
         *             'desktop'        => array(
         *                 'src' => 'http://example.com/desktop.jpg',
         *                 'alt' => 'Desktop Image',
         *             ),
         *             'tablet'         => array(
         *                 'src' => 'http://example.com/tablet.jpg',
         *                 'alt' => 'Tablet Image',
         *             ),
         *             'phone'          => array(
         *                 'src' => 'http://example.com/phone.jpg',
         *                 'alt' => 'Phone Image',
         *             ),
         *         ),
         *     ),
         * ));
         * ```
         */
        public static function add_data_item(array $args): bool
        {
        }
        /**
         * Enqueue data as an object into the assigned script.
         *
         * This function is used to enqueue a data object into the assigned script. The data object is used
         * to provide dynamic data to the script during execution.
         *
         * @since ??
         *
         * @param string $data_name The name of the data object to enqueue.
         *
         * @throws \Exception When the data object is not found.
         *
         * @return void
         *
         * @example: Enqueue script data
         * ```php
         * MyScript::enqueue_data( 'my_data' );
         * ```
         *
         * @example: Enqueue script data at the footer using a class method
         * ```php
         * MyClass::enqueue_script_at_footer();
         * ```
         *
         * @example: Enqueue fonts in the footer
         * ```php
         * enqueue_fonts_in_footer();
         * ```
         */
        public static function enqueue_data(string $data_name): void
        {
        }
        /**
         * Retrieves a specific data item based on the given data name and data item identifier.
         *
         * This function is used to retrieve a specific data item from the $_script_data array, which
         * stores data items for different data names.
         *
         * If the specified data item is found, it will be stored in the $data_item variable. Otherwise,
         * an empty array will be returned.
         *
         * @since ??
         *
         * @param string $data_name    The name of the data.
         * @param string $data_item_id The identifier of the data item.
         *
         * @return array The retrieved data item. If the data item is not found, an empty array is returned.
         *
         * @example: Retrieve data item
         * ```php
         * $data_name = 'link';
         * $data_item_id = 'divi/cta-0';
         * $data_item = self::get_data_item($data_name, $data_item_id);
         * ```
         */
        public static function get_data_item(string $data_name, string $data_item_id): array
        {
        }
        /**
         * Get data collection based on the given data name.
         *
         * This function retrieves the entire data collection (an array of data items) associated with the
         * given data name. The data collection is used to provide dynamic data to the script during execution.
         *
         * @since ??
         *
         * @param string $data_name The name of the data collection to retrieve.
         *
         * @throws \Exception When the data collection is not found.
         *
         * @return array The retrieved data collection. Returns an empty array if the data collection is not found.
         *
         * @example: Get data collection
         * ```php
         * $data = self::get_data( 'my_data' );
         * ```
         */
        public static function get_data(string $data_name): array
        {
        }
        /**
         * Get script info related to the given data name.
         *
         * This function retrieves the relevant information about a script assigned to a data object.
         * The data object provides dynamic data to the script during execution. The script handle may
         * not have the same name as the data name, hence this function returns the script and object
         * names associated with the data name.
         *
         * @since ??
         *
         * @param string $data_name The name of the data object.
         *
         * @throws \Exception When the data object is not found.
         *
         * @return array Associative array containing 'object_name' and 'script_name'.
         *
         * @example
         * ```php
         * MyNamespace\MyClass::get_script_info( 'my_data' );
         * // Returns: ['object_name' => 'object_name_value', 'script_name' => 'script_name_value']
         * ```
         */
        public static function get_script_info(string $data_name): array
        {
        }
        /**
         * Reset the state of the script data property.
         *
         * This function resets the state of the script data property to its initial state. The script
         * data property is used to store relevant data for script execution.
         *
         * @since ??
         *
         * @return void
         *
         * @example
         * ```php
         * resetScriptData();
         * ```
         */
        public static function reset(): void
        {
        }
    }
    /**
     * Font Loader class.
     *
     * Responsible for loading fonts in the frontend.
     *
     * @since ??
     */
    class Fonts
    {
        /**
         * Keep track of Fonts added.
         *
         * @since ??
         *
         * @var array
         */
        public static $_fonts_added = [];
        /**
         * Add a font family to the store.
         *
         * Enqueue a given font family for use in the Builder.
         *
         * @since ??
         *
         * @param string $font_family The name of the font family.
         *
         * @return void
         *
         * @example: Enqueue the 'Open Sans' font family.
         * ```php
         * add_font_family( 'Open Sans' );
         * ```
         */
        public static function add(string $font_family): void
        {
        }
        /**
         * Enqueue user custom fonts
         *
         * This function is used to enqueue custom fonts specified by the user. It takes in an array of
         * font URLs and registers them using the WordPress `wp_enqueue_style` function. This allows the
         * fonts to be loaded on the front-end of the website.
         *
         * @since ??
         *
         * @see wp_enqueue_style() To register and enqueue the custom font stylesheets.
         *
         * @return void
         */
        public static function enqueue(): void
        {
        }
    }
    /**
     * Registration class for handling frontend scripts.
     *
     * This class is responsible for registering and enqueuing the necessary
     * frontend scripts required by the module. It provides a set of functions that
     * can be used to register and enqueue scripts.
     *
     * @since ??
     */
    class Script
    {
        /**
         * Register a new script.
         *
         * This function allows you to register a new script that can be enqueued
         * later on. The script can be either a local file or a remote file.
         * Optionally, you can specify dependencies, module names, version numbers,
         * and whether the script should be enqueued in the footer.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments for registering the script.
         *
         *     @type string           $handle    Required. The name of the script. Should be unique.
         *     @type string|false     $src       Optional. The full URL of the script, or the path of
         *                                       the script relative to the WordPress root directory. If
         *                                       set to `false`, the script is an alias of other scripts
         *                                       it depends on. Default empty string.
         *     @type string[]         $deps      Optional. An array of registered script handles that
         *                                       this script depends on. Default `[]`.
         *     @type string[]         $module    Optional. An array of module names. If set, the script
         *                                       should only be rendered if module exists on this page.
         *                                       Default `[]`.
         *     @type string|bool|null $ver       Optional. The version number of the script. If `null`,
         *                                       no version number is added. If `false`, a version
         *                                       number equal to the current installed WordPress version
         *                                       is automatically added. Default `null`.
         *     @type bool             $in_footer Optional. Whether to enqueue the script before the
         *                                       closing `body` tag. Default `false` will enqueue the
         *                                       script before the closing `head` tag. Default `false`.
         * }
         *
         * @return void
         *
         * @example
         * ```php
         * $args = array(
         *     'handle'    => 'my-script',
         *     'src'       => 'https://example.com/js/my-script.js',
         *     'deps'      => array( 'jquery' ),
         *     'module'    => array( 'my-module' ),
         *     'ver'       => '1.0.0',
         *     'in_footer' => true,
         * );
         * wp_register_script( $args );
         * ```
         */
        public static function register(array $args = ['handle' => '', 'src' => '', 'deps' => [], 'module' => [], 'ver' => false, 'in_footer' => false, 'is_enqueue' => false]): void
        {
        }
        /**
         * Get all registered scripts.
         *
         * Retrieves an array of all registered scripts in the application.
         *
         * @since ??
         *
         * @return array An array of registered scripts.
         */
        public static function get_all(): array
        {
        }
    }
    /**
     * Frontend Style class.
     *
     * This class is used to store and enqueue module styles.
     */
    class Style
    {
        /**
         * Check if a preset selector has been processed.
         *
         * @since ??
         *
         * @param string $preset_selector_classname The classname of the preset selector to check.
         *
         * @return bool True if the preset selector has already been processed, false otherwise.
         */
        public static function is_preset_selector_processed(string $preset_selector_classname): bool
        {
        }
        /**
         * Retrieve Post ID from 1 of 3 sources depending on which exists:
         * - get_the_ID()
         * - $_GET['post']
         * - $_POST['et_post_id']
         *
         * @since ??
         *
         * @return int|bool
         */
        public static function get_current_post_id_reverse()
        {
        }
        /**
         * Get the current TB layout ID if we are rendering one or the current post ID instead.
         *
         * @since ??
         *
         * @return integer
         */
        public static function get_layout_id()
        {
        }
        /**
         * Get style key.
         *
         * @return int|string
         */
        public static function get_style_key()
        {
        }
        /**
         * Return style array from {@see self::$internal_modules_styles} or {@see self::$styles}.
         *
         * @param string     $group Style Group.
         * @param int|string $key   Style Key.
         *
         * @return array
         */
        public static function get_style_array(string $group = 'module', $key = 0): array
        {
        }
        /**
         * Return media query from the media query name.
         * E.g For max_width_767 media query name, this function return "@media only screen and ( max-width: 767px )".
         *
         * @since ??
         *
         * @param string $name Media query name e.g max_width_767, max_width_980.
         *
         * @return bool|mixed
         */
        public static function get_media_query(string $name)
        {
        }
        /**
         * Return media query key value pairs.
         *
         * @since ??
         *
         * @param bool $for_js Whether media queries is for js ETBuilderBackend.et_builder_css_media_queries variable.
         *
         * @return array|mixed|void
         */
        public static function get_media_quries(bool $for_js = false)
        {
        }
        /**
         * Set media queries key value pairs.
         *
         * @since ??
         */
        public static function set_media_queries()
        {
        }
        /**
         * Add a new style.
         *
         * Adds a new style to the CSS styles data. The style will be enqueued by `self::enqueue()`.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments for adding a style.
         *
         *     @type string    $id              The ID of the style.
         *     @type int       $orderIndex      The order index of the style.
         *     @type array     $styles          Optional. An array of CSS styles for the style. Default `[]`.
         *     @type object    $storeInstance   Optional. The instance of the store. Default `null`.
         *     @type int       $priority        Optional. The priority of the style. Default `10`.
         *     @type string    $group           Optional. The group of the style. Default `module`.
         * }
         *
         * @return void
         *
         * @example
         * ```php
         * self::add( [
         *     'id'          => 'style-1',
         *     'styles'      => ['color' => '#000', 'font-size' => '16px'],
         *     'storeInstance' => $store,
         *     'orderIndex'  => 1,
         *     'priority'    => 20,
         * ] );
         * ```
         */
        public static function add(array $args): void
        {
        }
        /**
         * Sort an array of items by their priority.
         *
         * This function takes an array of items. The function then sorts the array of priorities in ascending
         * order. If two items have the same priority, they will be sorted by their original index
         * within the input array.
         *
         * @since ??
         *
         * @param array $collection The array to be sorted. Each child item in the array should have a 'priority' key.
         *
         * @return array An array of items sorted by priority. The array will maintain the same keys as the input array.
         *
         * @example
         * ```php
         * $collection = [
         *     'selector1' => ['priority' => 5, 'item' => 'A'],
         *     'selector2' => ['priority' => 10, 'item' => 'B'],
         *     'selector3' => ['priority' => 5, 'item' => 'C'],
         * ];
         *
         * $sortedCollection = sort_by_priority($collection);
         *
         * // $sortedCollection will be:
         * // [
         * //     'selector1' => ['priority' => 5, 'item' => 'A'],
         * //     'selector3' => ['priority' => 5, 'item' => 'C'],
         * //     'selector2' => ['priority' => 10, 'item' => 'B'],
         * // ]
         * ```
         */
        public static function sort_by_priority(array &$collection): array
        {
        }
        /**
         * Enqueue styles from the Style class.
         *
         * This function retrieves the styles data from the Style class and enqueues the styles on the
         * page. It concatenates the styles into a single string and echoes them within `style` tags.
         * The styles are sanitized and escaped before being output to the page.
         *
         * @since ??
         *
         * @param string $style_type The type of styles to enqueue.
         * @param string $group The group of styles to enqueue. Default is 'module'.
         * @param string $key   Optional. The element id.
         *
         * @return void
         *
         * @example: Enqueue styles
         * ```php
         * MyStyles::enqueue();
         * ```
         */
        public static function enqueue(string $style_type = 'default', string $group = 'module', $key = 0): void
        {
        }
        /**
         * Render sorted styles as string.
         *
         * @since ??
         *
         * @param string $style_type The type of styles to enqueue.
         * @param string $group The group of styles to enqueue. Default is 'module'.
         * @param string $key        Optional. The element id.
         *
         * @example: Render styles
         * ```php
         * MyStyles::render();
         * ```
         */
        public static function render(string $style_type = 'default', string $group = 'module', $key = 0): string
        {
        }
        /**
         * Reset styles data.
         *
         * Resets the styles data to an empty array `[]`.
         *
         * @since ??
         *
         * @return void
         */
        public static function reset()
        {
        }
        /**
         * Provides styles for global colors.
         *
         * This function retrieves and prepares style data from global colors data. The values are then
         * sanitized and escaped for secure use.
         *
         * It can be used in two ways:
         * 1. Without any parameters - In this case, it returns styles for all available global colors.
         * 2. With an array of $global_color_ids - It only returns styles for the colors associated with the provided ids.
         *
         * @since ??
         *
         * @param array $global_color_ids An optional parameter. When provided, the function will only include
         *                                the styles for the global colors associated with these ids.
         *                                If not provided or an empty array is passed, styles for all global colors
         *                                will be included.
         *
         * @return string Returns a string containing the styles for the global colors.
         */
        public static function get_global_colors_style(array $global_color_ids = []): string
        {
        }
        /**
         * Set the group of the style where it will be added.
         *
         * @since ??
         *
         * @param string $group The group of the style.
         *
         * @return void
         */
        public static function set_group_style(string $group): void
        {
        }
        /**
         * Get the group of the style where it will be added.
         *
         * @since ??
         *
         * @return string
         */
        public static function get_group_style(): string
        {
        }
        /**
         * Get global numeric and fonts variables as CSS styles.
         *
         * This function retrieves numeric and fonts global variables from the global data and formats them
         * into CSS variable declarations for use in stylesheets.
         *
         * @since ??
         *
         * @return string The generated CSS style block containing global numeric and fonts variables.
         */
        public static function get_global_numeric_and_fonts_vars_style(): string
        {
        }
    }
}
namespace ET\Builder\Framework\DependencyManagement\Interfaces {
    interface DependencyInterface
    {
        /**
         * This function registers and initiates all the logic the class implements.
         *
         * @return void
         */
        public function load();
    }
}
namespace ET\Builder\Framework\UserRole {
    /**
     * UserRole class.
     *
     * This class contains functionality to determine a user's permissions, role and capabilities.
     *
     * @since ??
     */
    class UserRole
    {
        /**
         * Determine whether the current user can use the Visual Builder.
         *
         * This function checks if the current user has permission to use the visual builder based on role settings.
         *
         *
         * Note: By default, all roles have access to the Visual Builder, when you start using RoleEditor then only the
         * selected users will have access. This is admittedly not an ideal situation that might be changed in the future
         * and is kept in D5 for backwards compatibility.
         *
         * @since ??
         *
         * @return bool Returns `true` if the current user can use the visual builder, `false` otherwise.
         */
        public static function can_current_user_use_visual_builder(): bool
        {
        }
    }
}
namespace ET\Builder\Framework\Route {
    /**
     * REST API Route class.
     *
     * @since ??
     */
    class RESTRoute
    {
        /**
         * REST route prefix.
         *
         * This string is going to be prefixed to the route you want to register.
         *
         * @var string
         */
        public $prefix = '';
        /**
         * Create an instance of `RestRoute`.
         *
         * @param string $namespace WordPress REST API namespace.
         */
        public function __construct(string $namespace)
        {
        }
        /**
         * Register a REST API route.
         *
         * @param string       $method              The method to register e.g. `POST`, `GET`, `PUT`.
         * @param string       $route               The route name to add e.g. `/route-name`.
         * @param array        $route_args          Route arguments as used in `register_rest_route()`.
         * @param string|array $route_callback      Route callback as used in `register_rest_route()`.
         * @param string|array $permission_callback Route permission callback as used in `register_rest_route()`.
         *
         * @return void
         */
        public function register_rest_route(string $method, string $route, array $route_args, $route_callback, $permission_callback): void
        {
        }
        /**
         * Register a REST resource route with WordPress.
         *
         * This function registers a REST resource route with WordPress.
         * The route should be a string representing the URL endpoint for the resource.
         * The controller should be an instance of a class that contains the action methods for the resource.
         * The options parameter allows customization of the resource registration.
         *
         * A resource route is useful if you are to perform the same sets of actions against each resource. So by using
         * `resource()` you can assign the typical create, read, update, and delete ("CRUD") routes to a controller with a
         * single method call, following RESTful convention.
         *
         * @see https://developer.wordpress.org/rest-api/extending-the-rest-api/routes-and-endpoints/#resource-paths
         *
         * @since ??
         *
         * @param string $route      The route string for the resource.
         * @param mixed  $controller A controller containing the action methods for the resource.
         * @param array  $options {
         *     An array of options to customize the resource registration.
         *
         *     @type array  $actions        An array of action names allowed for the resource.
         *     @type string $route_variable A regex string representing the route variable for the resource.
         * }
         *
         * @return void
         *
         * @example:
         * ```php
         * $route = '/my-resource';
         * $controller = new MyController();
         * $options = [
         *     'actions' => ['index', 'store', 'show', 'update', 'destroy'],
         *     'route_variable' => '(?P<id>[\d]+)',
         * ];
         * resource($route, $controller, $options);
         * ```
         */
        public function resource(string $route, $controller, array $options): void
        {
        }
        /**
         * Register a `GET` REST route with the specified route and controller.
         *
         * If the controller is a string and a class, retrieve the index arguments, callback, and permission callback from the controller.
         * If the controller is an array, retrieve the args, callback, and permission callback from the array.
         *
         * @since ??
         *
         * @param string|array $route      The route to register.
         * @param mixed        $controller The controller containing the endpoint logic, either a class-string or array.
         *                                 This should define `args`, `callback` and `permission_callback` as used in `RESTRoute::register_rest_route()`.
         *
         * @example:
         * ```php
         *      $restRouter = new RestRoute();
         *      $controller = new MyController();
         *      $restRouter->get('/my-route', $controller);
         * ```
         *
         * @example:
         * ```php
         *      // Register a REST route with an array controller.
         *      $restRouter = new RestRoute();
         *      $restRouter->get('/my-route', [
         *          'args'                => 'my_callback_args',
         *          'callback'            => 'my_callback',
         *          'permission_callback' => 'my_permission_callback',
         *      ]);
         * ```
         */
        public function get($route, $controller): void
        {
        }
        /**
         * Register a `POST` REST route with the specified route and controller.
         *
         * If the controller is a string and a class, retrieve the index arguments, callback, and permission callback from the controller.
         * If the controller is an array, retrieve the args, callback, and permission callback from the array.
         *
         * @since ??
         *
         * @param string $route      The route to register.
         * @param mixed  $controller The controller containing the endpoint logic, either a class-string or array.
         *                           This should define `args`, `callback` and `permission_callback` as used in `RESTRoute::register_rest_route()`.
         *
         * @example:
         * ```php
         *      $restRouter = new RestRoute();
         *      $controller = new MyController();
         *      $restRouter->post('/my-route', $controller);
         * ```
         *
         * @example:
         * ```php
         *      // Register a REST route with an array controller.
         *      $restRouter = new RestRoute();
         *      $restRouter->post('/my-route', [
         *          'args'                => 'my_callback_args',
         *          'callback'            => 'my_callback',
         *          'permission_callback' => 'my_permission_callback',
         *      ]);
         * ```
         */
        public function post(string $route, $controller): void
        {
        }
        /**
         * Register a `PUT` REST route with the specified route and controller.
         *
         * If the controller is a string and a class, retrieve the index arguments, callback, and permission callback from the controller.
         * If the controller is an array, retrieve the args, callback, and permission callback from the array.
         *
         * @since ??
         *
         * @param string $route      The route to register.
         * @param mixed  $controller The controller containing the endpoint logic, either a class-string or array.
         *                           This should define `args`, `callback` and `permission_callback` as used in `RESTRoute::register_rest_route()`.
         *
         * @example:
         * ```php
         *      $restRouter = new RestRoute();
         *      $controller = new MyController();
         *      $restRouter->put('/my-route', $controller);
         * ```
         *
         * @example:
         * ```php
         *      // Register a REST route with an array controller.
         *      $restRouter = new RestRoute();
         *      $restRouter->put('/my-route', [
         *          'args'                => 'my_callback_args',
         *          'callback'            => 'my_callback',
         *          'permission_callback' => 'my_permission_callback',
         *      ]);
         * ```
         */
        public function put(string $route, $controller): void
        {
        }
        /**
         * Register a `DELETE` REST route with the specified route and controller.
         *
         * If the controller is a string and a class, retrieve the index arguments, callback, and permission callback from the controller.
         * If the controller is an array, retrieve the args, callback, and permission callback from the array.
         *
         * @since ??
         *
         * @param string $route      The route to register.
         * @param mixed  $controller The controller containing the endpoint logic, either a class-string or array.
         *                           This should define `args`, `callback` and `permission_callback` as used in `RESTRoute::register_rest_route()`.
         *
         * @example:
         * ```php
         *      $restRouter = new RestRoute();
         *      $controller = new MyController();
         *      $restRouter->delete('/my-route', $controller);
         * ```
         *
         * @example:
         * ```php
         *      // Register a REST route with an array controller.
         *      $restRouter = new RestRoute();
         *      $restRouter->delete('/my-route', [
         *          'args'                => 'my_callback_args',
         *          'callback'            => 'my_callback',
         *          'permission_callback' => 'my_permission_callback',
         *      ]);
         * ```
         */
        public function delete(string $route, $controller): void
        {
        }
        /**
         * Set a new prefix for the current RESTRoute instance.
         *
         * Create a new instance of `RESTRoute` and set the given prefix which
         * will be applied to all the routes instance.
         *
         * @since ??
         *
         * @param string $prefix REST route prefix.
         *
         * @return RESTRoute
         */
        public function prefix(string $prefix): \ET\Builder\Framework\Route\RESTRoute
        {
        }
        /**
         * Group a set of REST routes using a callback function.
         *
         * This method allows for grouping a set of routes by executing a given callback function,
         * which can modify and add routes to the current route collection represented by this class instance.
         *
         * This function passes the current instance of `RESTRoute` to the executed callback.
         *
         * @since ??
         *
         * @param callable $callback The callback function to be executed for grouping routes.
         *                           It should accept a single parameter representing the current instance
         *                           of the RESTRoute class.
         *
         * @return RESTRoute The current instance of the RESTRoute class.
         *
         * @example:
         * ```php
         * // Grouping routes using a callback function
         * $route = new RESTRoute();
         * $route->group( function( $router ) {
         *     $router->get( '/posts', 'MyApp\Controllers\PostController@index' );
         *     $router->post( '/posts', 'MyApp\Controllers\PostController@store' );
         * });
         * ```
         */
        public function group(callable $callback): \ET\Builder\Framework\Route\RESTRoute
        {
        }
    }
}
namespace ET\Builder\Framework\DependencyManagement {
    /**
     * `DependencyTree` class is used as a utility to manage loading classes in a meaningful manner.
     *
     * Any class passed to `DependencyTree` should implement `DependencyInterface`.
     *
     * @since ??
     */
    class DependencyTree
    {
        /**
         * Add a new dependency to the dependency tree.
         *
         * @param DependencyInterface $dependency Dependency class.
         *
         * @since ??
         *
         * @return void
         *
         * @example:
         * ```php
         * $dependency_tree = new DependencyTree();
         *
         * $dependency_tree->add_dependency( new DynamicContentOptionProductTitle() );
         * $dependency_tree->add_dependency( new DynamicContentOptionPostTitle() );
         * $dependency_tree->add_dependency( new DynamicContentOptionPostExcerpt() );
         *
         * // ... Add more dependencies ...
         * ```
         */
        public function add_dependency(\ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface $dependency): void
        {
        }
        /**
         * Loads all the dependencies registered in the dependency tree.
         *
         * This function iterates through the dependency tree and loads each dependency
         * by calling the `load()` method on each dependency object.
         *
         * @since ??
         *
         * @return void
         *
         * @example:
         * ```php
         * $dependency_tree = new DependencyTree();
         *
         * $dependency_tree->add_dependency( new DynamicContentOptionProductTitle() );
         * $dependency_tree->add_dependency( new DynamicContentOptionPostTitle() );
         * $dependency_tree->add_dependency( new DynamicContentOptionPostExcerpt() );
         *
         * // ... Add more dependencies ...
         *
         * $dependency_tree->load_dependencies();
         * ```
         */
        public function load_dependencies(): void
        {
        }
    }
}
namespace ET\Builder\Framework\Controllers {
    /**
     * REST Controller class.
     *
     * @since ??
     */
    abstract class RESTController
    {
        /**
         * Generate and return a successful `WP_REST_Response` object.
         *
         * This function prepares a `WP_REST_Response` object with the provided data, headers, and status code.
         *
         * @since ??
         *
         * @param mixed|null $data    Optional. The response data. Default `null`.
         * @param array      $headers Optional. HTTP headers map. Default `[]`
         *                            A default set of headers `{'content-type' => 'application/json'}` is
         *                            always set unless the key is overwritten.
         * @param int        $status  Optional. HTTP status code. Default `200`.
         *
         * @return WP_REST_Response The prepared `WP_REST_Response` object.
         *
         * @example:
         * ```php
         * $response = RESTController::response_success( [ 'foo' => 'bar', 'message => 'Success!' ] );
         * ```
         */
        public static function response_success($data = null, array $headers = [], int $status = 200): \WP_REST_Response
        {
        }
        /**
         * Generate and return a `WP_Error` response error object.
         *
         * This function prepares a `WP_Error` object with the provided data,
         * message, error code and and status code.
         *
         * @since ??
         *
         * @param string $code    Optional. Error code. Default empty string.
         * @param string $message Optional. Error message. Default empty string.
         * @param array  $data    Optional. Error data. Default `[]`.
         * @param int    $status  Optional. HTTP status code. Default `400`.
         *
         * @return WP_Error The prepared `WP_Error` object.
         *
         * @example:
         * ```php
         * $code = 'example_code';
         * $message = 'Example message.';
         * $data = [ 'foo' => 'bar' ];
         * $status = 500;
         * $response = RESTController::response_error( $code, $message, $data, $status );
         * ```
         */
        public static function response_error(string $code = '', string $message = '', array $data = [], int $status = 400): \WP_Error
        {
        }
        /**
         * Generate a server response error for insufficient permission.
         *
         * This function returns a `WP_Error` object with an error code and message for insufficient permission.
         * It can be used to handle cases where the user does not have sufficient permission to perform certain actions.
         *
         * @since ??
         *
         * @param array $data Optional. Error data. Default `[]`.
         *
         * @return WP_Error The WP_Error object representing the error response.
         *
         * @example:
         * ```php
         * $data = [ 'foo' => 'bar' ];
         *
         * $response = RESTController::response_error_permission( $data );
         * ```
         */
        public static function response_error_permission(array $data = []): \WP_Error
        {
        }
        /**
         * Generate server response error for invalid nonce.
         *
         * This function generates a `WP_Error` object with an error code and message for an invalid nonce.
         * It can be used to handle cases where the provided nonce is invalid.
         *
         * @since ??
         *
         * @param array $data Optional. Error data. Default `[]`.
         *
         * @return WP_Error The WP_Error object representing the error response.
         *
         * @example:
         * ```php
         * $data = [ 'foo' => 'bar' ];
         *
         * $response = RESTController::response_error_nonce( $data );
         * ```
         */
        public static function response_error_nonce(array $data = []): \WP_Error
        {
        }
        /**
         * Get the nonce name based on the provided namespace, route, and method.
         *
         * This function concatenates the full route, namespace, and method to create a unique nonce name.
         *
         * @since ??
         *
         * @param string $namespace The namespace of the route.
         * @param string $route     The route to get the nonce name for.
         * @param string $method    The HTTP method used for the request.
         *
         * @return string The nonce name for the specified route and method.
         *
         * @example:
         * ```php
         * $namespace = 'my_namespace';
         * $route = 'my_route';
         * $method = 'GET';
         * $nonceName = RESTController::get_nonce_name( $namespace, $route, $method );
         *
         * // Result: 'my_namespace/my_route--GET'
         * ```
         */
        public static function get_nonce_name(string $namespace, string $route, string $method): string
        {
        }
        /**
         * Create nonce based on give namespace, route and request method.
         *
         * Creates a cryptographic token tied to a specific action
         * (uses `REST::get_nonce_name($namespace, $route, $method)`), user, user session, and window of time.
         *
         * @since ??
         *
         * @param string $namespace The REST API namespace.
         * @param string $route     The REST API route.
         * @param string $method    The HTTP method to use for the request.
         *
         * @return string The nonce token.
         */
        public static function create_nonce(string $namespace, string $route, string $method): string
        {
        }
        /**
         * Get the full route by concatenating the namespace and route.
         *
         * This function takes a namespace and route and concatenates them with a forward slash to form the full route.
         *
         * @since ??
         *
         * @param string $namespace The namespace of the route.
         * @param string $route     The route to be concatenated.
         *
         * @return string The full route formed by concatenating the namespace and route.
         *
         * @example:
         * ```php
         * $namespace = 'my_namespace';
         * $route = 'my_route';
         * $fullRoute = RESTController::get_full_route( $namespace, $route );
         *
         * // Result: '/my_namespace/my_route'
         * ```
         */
        public static function get_full_route(string $namespace, string $route): string
        {
        }
    }
}
namespace ET\Builder\Packages\StyleLibrary\Utils {
    /**
     * StyleDeclarations class is a helper class with methods to work with the style library.
     *
     * This class is equivalent of JS class:
     * {@link /docs/builder-api/js/style-library/style-declarations} in
     * `@divi/style-library` package.
     *
     * @since ??
     */
    class StyleDeclarations
    {
        /**
         * Create an instance of StyleDeclarations class.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type bool|array $important  Optional. Whether to add `!important` tag. Default `false`.
         *     @type string     $returnType Optional.
         *                                  This is the type of value that the function will return.
         *                                  Can be either `string` or `key_value_pair`. Default `string`.
         * }
         */
        public function __construct(array $args)
        {
        }
        /**
         * Add declaration's property and value.
         *
         * @since ??
         *
         * @param string $property The CSS property to add.
         * @param string $value    The value of the property.
         *
         * @return void
         */
        public function add(string $property, string $value): void
        {
        }
        /**
         * Get style declaration.
         *
         * Returns either array of declarations or string of declarations based on the specified return type.
         *
         * @since ??
         *
         * @return array|string|null Returns either array of declarations or string of declarations based on the specified return type.
         */
        public function value()
        {
        }
    }
    /**
     * Utils class is a helper class with helper methods to work with the style library.
     *
     * @since ??
     */
    class Utils
    {
        /**
         * Join array of declarations into `;` separated string, suffixed by `;`.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js/style-library/join-declarations joinDeclarations} in:
         * `@divi/style-library` package.
         *
         * @since ??
         *
         * @param array $declarations Array of declarations.
         *
         * @return string
         */
        public static function join_declarations(array $declarations): string
        {
        }
        /**
         * Recursively resolve any `$variable(...)$` strings within an array or string.
         *
         * @since ??
         *
         * @param mixed $value The raw input, string or array.
         *
         * @return mixed The resolved value with all dynamic variables normalized.
         */
        public static function resolve_dynamic_variables_recursive($value)
        {
        }
        /**
         * Resolves a `$variable(...)$` encoded dynamic content string into a CSS variable.
         *
         * Example:
         * Input:  $variable({"type":"content","value":{"name":"gvid-abc123"}})$
         * Output: var(--gvid-abc123)
         *
         * @since ??
         *
         * @param string $value The raw string to be resolved.
         *
         * @return string The resolved CSS variable or original value if not matched.
         */
        public static function resolve_dynamic_variable($value)
        {
        }
        /**
         * Helper function to resolve nested global colors and global variables to actual color values.
         * This ensures SVG elements get concrete color values instead of CSS variables or variable syntax.
         *
         * Handles all global color formats including:
         * - CSS variables: var(--gcid-xxx)
         * - Variable syntax: $variable({"type":"color","value":{"name":"gcid-xxx","settings":{...}}})$
         * - HSL with variables: hsl(from var(--gcid-xxx) calc(h + 0) calc(s + 0) calc(l + 0) / 0.2)
         * - Nested global colors: Global colors that reference other global colors
         * - Multiple levels of nesting with recursive resolution
         *
         * @param string $color The input color value (could be global color ID, $variable syntax, CSS variable, or nested reference).
         * @param int    $depth Current recursion depth to prevent infinite loops.
         * @return string The resolved concrete color value or original color if not a global color.
         */
        public static function resolve_global_color_to_value($color, $depth = 0)
        {
        }
    }
}
namespace ET\Builder\Packages\Module\Options\Border {
    /**
     * BorderStyle class
     *
     * @since ??
     */
    class BorderStyle
    {
        /**
         * Get border style component.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/BorderStyle BorderStyle} in
         * `@divi/module` package.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type string        $selector                 The CSS selector.
         *     @type array         $selectors                Optional. An array of selectors for each breakpoint and state. Default `[]`.
         *     @type callable      $selectorFunction         Optional. The function to be called to generate CSS selector. Default `null`.
         *     @type array         $propertySelectors        Optional. The property selectors that you want to unpack. Default `[]`.
         *     @type array         $attr                     An array of module attribute data.
         *     @type array         $defaultPrintedStyleAttr  Optional. An array of default printed style attribute data. Default `[]`.
         *     @type array|bool    $important                Optional. Whether to apply "!important" flag to the style declarations.
         *                                                   Default `false`.
         *     @type bool          $asStyle                  Optional. Whether to wrap the style declaration with style tag or not.
         *                                                   Default `true`.
         *     @type string|null   $orderClass               Optional. The selector class name.
         *     @type bool          $isInsideStickyModule     Optional. Whether the blockquote element is inside a sticky module.
         *     @type string        $attrs_json               Optional. The JSON string of module attribute data, use to improve performance.
         *     @type string        $returnType               Optional. This is the type of value that the function will return.
         *                                                   Can be either `string` or `array`. Default `array`.
         * }
         *
         * @return string|array The border style component.
         *
         * @example:
         * ```php
         * // Apply style using default arguments.
         * $args = [];
         * $style = BorderStyle::style( $args );
         *
         * // Apply style with specific selectors and properties.
         * $args = [
         *     'selectors' => [
         *         '.element1',
         *         '.element2',
         *     ],
         *     'propertySelectors' => [
         *         '.element1 .property1',
         *         '.element2 .property2',
         *     ]
         * ];
         * $style = BorderStyle::style( $args );
         * ```
         */
        public static function style($args)
        {
        }
        /**
         * Normalize the border attributes.
         *
         * Some attributes are not available in all breakpoints and states. This function
         * will normalize the attributes by filling the missing attributes with the
         * inherited values.
         *
         * @since ??
         *
         * @param array $attr The array of attributes to be normalized.
         * @return array The normalized array of attributes.
         */
        public static function normalize_attr(array $attr): array
        {
        }
    }
}
namespace ET\Builder\Packages\Module\Options\BoxShadow {
    /**
     * BoxShadowStyle class.
     *
     * This class provides methods for manipulating the box shadow style.
     *
     * @since ??
     */
    class BoxShadowStyle
    {
        /**
         * Get box shadow style component.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/BoxShadowStyle BoxShadowStyle} in
         * `@divi/module` package.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type string        $selector                 The CSS selector.
         *     @type array         $selectors                Optional. An array of selectors for each breakpoint and state. Default `[]`.
         *     @type callable      $selectorFunction         Optional. The function to be called to generate CSS selector. Default `null`.
         *     @type array         $propertySelectors        Optional. The property selectors that you want to unpack. Default `[]`.
         *     @type array         $attr                     An array of module attribute data.
         *     @type array         $defaultPrintedStyleAttr  Optional. An array of default printed style attribute data. Default `[]`.
         *     @type array|bool    $important                Optional. Whether to apply "!important" flag to the style declarations.
         *                                                   Default `false`.
         *     @type bool          $asStyle                  Optional. Whether to wrap the style declaration with style tag or not.
         *                                                   Default `true`.
         *     @type bool          $useOverlay               Optional. Whether to generate the `selectors` and `selector` that  are suffixed
         *                                                   with box shadow overlay element (` > .box-shadow-overlay`).
         *                                                   Note: this is only applicable when the `selectors` params is empty.
         *     @type string|null   $orderClass Optional.     The selector class name.
         *     @type bool          $isInsideStickyModule     Optional. Whether the module is inside a sticky module or not. Default `false`.
         *     @type string        $returnType               Optional. This is the type of value that the function will return.
         *                                                   Can be either `string` or `array`. Default `array`.
         * }
         *
         * @return string|array The transform style component.
         *
         * @example:
         * ```php
         * // Apply style using default arguments.
         * $args = [];
         * $style = BoxShadowStyle::style( $args );
         *
         * // Apply style with specific selectors and properties.
         * $args = [
         *     'selectors' => [
         *         '.element1',
         *         '.element2',
         *     ],
         *     'propertySelectors' => [
         *         '.element1 .property1',
         *         '.element2 .property2',
         *     ]
         * ];
         * $style = BoxShadowStyle::style( $args );
         * ```
         */
        public static function style($args)
        {
        }
        /**
         * Normalize the box shadow attributes.
         *
         * Some attributes are not available in all breakpoints and states. This function
         * will normalize the attributes by filling the missing attributes with the
         * inherited values and presets.
         *
         * @since ??
         *
         * @param array $attr The array of attributes to be normalized.
         * @return array The normalized array of attributes.
         */
        public static function normalize_attr(array $attr): array
        {
        }
    }
}
namespace ET\Builder\Packages\Module\Options\Css {
    /**
     * CssStyleUtils class.
     *
     * @since ??
     */
    class CssStyleUtils
    {
        /**
         * Get custom CSS statements based on given params.
         *
         * This function retrieves the CSS statements based on the provided arguments.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/GetStatements getStatements} in
         * `@divi/module` package.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type array         $selectors        Optional. An array of selectors for each breakpoint and state. Default `[]`.
         *     @type callable      $selectorFunction Optional. The function to be called to generate CSS selector. Default `null`.
         *     @type array         $attr             Optional. An array of module attribute data. Default `[]`.
         *     @type array         $cssFields        Optional. An array of CSS fields. Default `[]`.
         *     @type string|null   $orderClass       Optional. The selector class name.
         *     @type string        $returnType       Optional. This is the type of value that the function will return.
         *                                           Can be either `string` or `array`. Default `array`.
         * }
         *
         * @return string|array The CSS statements formatted as a string.
         *
         * @example:
         * ```php
         * // Usage Example 1: Simple usage with default arguments.
         * $args = [
         *     'selectors'         => ['.element'],
         *     'selectorFunction'  => null,
         *     'attr'              => [
         *         'desktop' => [
         *             'state1' => [
         *                 'custom_css1' => 'color: red;',
         *                 'custom_css2' => 'font-weight: bold;',
         *             ],
         *             'state2' => [
         *                 'custom_css1' => 'color: blue;',
         *             ],
         *         ],
         *         'tablet'  => [
         *             'state1' => [
         *                 'custom_css1' => 'color: green;',
         *                 'custom_css2' => 'font-size: 16px;',
         *             ],
         *         ],
         *     ],
         *     'cssFields'         => [
         *         'custom_css1' => [
         *             'selectorSuffix' => '::before',
         *         ],
         *         'custom_css2' => [
         *             'selectorSuffix' => '::after',
         *         ],
         *     ],
         * ];
         *
         * $cssStatements = MyClass::get_statements( $args );
         * ```
         * @example:
         * ```php
         * // Usage Example 2: Custom selector function to modify the selector and additional at-rules.
         * $args = [
         *     'selectors'         => ['.element'],
         *     'selectorFunction'  => function( $args ) {
         *         $defaultSelector = $args['selector'];
         *         $breakpoint = $args['breakpoint'];
         *         $state = $args['state'];
         *         $attr = $args['attr'];
         *
         *         // Append breakpoint and state to the default selector.
         *         $modifiedSelector = $defaultSelector . '-' . $breakpoint . '-' . $state;
         *
         *         return $modifiedSelector;
         *     },
         *     'attr'              => [
         *         'desktop' => [
         *             'state1' => [
         *                 'custom_css1' => 'color: red;',
         *                 'custom_css2' => 'font-weight: bold;',
         *             ],
         *         ],
         *     ],
         *     'cssFields'         => [
         *         'custom_css1' => [
         *             'selectorSuffix' => '::before',
         *         ],
         *         'custom_css2' => [
         *             'selectorSuffix' => '::after',
         *         ],
         *     ],
         * ];
         *
         * $cssStatements = MyClass::get_statements( $args );
         * ```
         */
        public static function get_statements(array $args)
        {
        }
    }
    /**
     * CssStyle class.
     *
     * @since ??
     */
    class CssStyle
    {
        /**
         * Get custom CSS style component.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/CssStyle CssStyle} in
         * `@divi/module` package.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type string        $selector                 The CSS selector.
         *     @type array         $selectors                Optional. An array of selectors for each breakpoint and state. Default `[]`.
         *     @type callable      $selectorFunction         Optional. The function to be called to generate CSS selector. Default `null`.
         *     @type array         $attr                     An array of module attribute data.
         *     @type bool          $asStyle                  Optional. Whether to wrap the style declaration with style tag or not.
         *                                                   Default `true`.
         *     @type array         $cssFields                Optional. CSS fields. Default `[]`.
         *     @type string|null   $orderClass               Optional. The selector class name.
         *     @type string        $returnType               Optional. This is the type of value that the function will return.
         *                                                   Can be either `string` or `array`. Default `array`.
         * }
         *
         * @return string|array The custom CSS style component.
         *
         * @example:
         * ```php
         * $args = [
         *     'selectors'        => [ 'h1', '.container' ],
         *     'selectorFunction' => null,
         *     'asStyle'          => true,
         *     'cssFields'        => [
         *         'color'    => '#000',
         *         'font-size' => '16px',
         *     ],
         * ];
         *
         * $style = CssStyle::style( $args );
         * ```
         *
         * @example:
         * ```php
         * $args = [
         *     'selectors'        => [ '#header', '#footer' ],
         *     'selectorFunction' => 'get_custom_selector',
         *     'asStyle'          => true,
         *     'cssFields'        => [
         *         'background-color' => '#fff',
         *         'font-family'      => 'Arial, sans-serif',
         *     ],
         * ];
         *
         * $style = CssStyle::style( $args );
         * ```
         */
        public static function style(array $args)
        {
        }
    }
}
namespace ET\Builder\Packages\Module\Options\Element {
    /**
     * Element Classnames class.
     *
     * This class provides methods to manipulate class names of elements.
     *
     * @since ??
     */
    class ElementClassnames
    {
        /**
         * Get element classnames based on the provided arguments.
         *
         * This function is used to generate a string of class names based on the provided arguments.
         * It can be used to add class names to HTML elements dynamically.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/ElementClassnames ElementClassnames} in
         * `@divi/module` package.
         *
         * @since 1.0.0
         *
         * @param array $args {
         *     Optional. An array of arguments to customize the class names.
         *
         *     @type array    $attrs              Optional. Additional attributes for the class names. Default `[]`.
         *     @type bool     $animation          Optional. Whether to include animation class names. Default `true`.
         *     @type bool     $background         Optional. Whether to include background class names. Default `true`.
         *     @type bool     $border             Optional. Whether to include border class names. Default `true`.
         *     @type bool     $link               Optional. Whether to include link class names. Default `true`.
         *     @type bool     $dividers           Optional. Whether to include divider class names. Default `false`.
         *     @type bool     $boxShadow          Optional. Whether to include box shadow class names. Default `false`.
         *     @type bool     $interactions       Optional. Whether to include interaction target class names. Default `true`.
         * }
         *
         * @return string The generated class names.
         *
         * @example:
         * ```php
         * // Example 1: Provide only the 'division' argument.
         * $args = [
         *     'dividers' => true,
         * ];
         *
         * $result = ElementClassnames::classnames( $args ); // Returns the class names related to dividers.
         * ```
         *
         * @example:
         * ```php
         * // Example 2: Provide multiple arguments.
         * $args = [
         *     'animation'  => true,
         *     'background' => true,
         *     'border'     => true,
         * ];
         * $result = ElementClassnames::classnames( $args ); // Returns the class names related to animation, background, and border.
         * ```
         *
         * @example:
         * ```php
         * // Example 3: Provide empty arguments.
         * $args = [];
         * $result = ElementClassnames::classnames( $args ); // Returns an empty string as no class names are included.
         * ```
         */
        public static function classnames(array $args): string
        {
        }
    }
    /**
     * ElementStyleAdvancedStyles class.
     *
     * This class provides the functionality for handling advanced styles.
     *
     * @since ??
     */
    class ElementStyleAdvancedStyles
    {
        /**
         * Get style component based on style component name.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/ElementStyle/advanced-styles/utils/get-style-components getStyleComponents} in
         * `@divi/module` package.
         *
         * @since ??
         *
         * @param string $component_name Style component name.
         *
         * @return string|object Style component. The style component must be a class object
         *                       with static `style()` method. This follow the same pattern
         *                       as module options component style.
         *
         * @example:
         * ```php
         * // Get style component using default arguments.
         * $component_name = '';
         * $style_component = ElementStyleAdvancedStyles::get_style_component( $component_name );
         *
         * // Get style component with specific component name.
         * $component_name = 'divi/text';
         * $style_component = ElementStyleAdvancedStyles::style( $component_name );
         * ```
         */
        public static function get_style_component($component_name)
        {
        }
        /**
         * Get style component map.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/ElementStyle/advanced-styles/utils/style-component-map styleComponentMap} in
         * `@divi/module` package.
         *
         * There are missing style components in this trait:
         * - `divi/animation`
         * VB has `divi/animation` style component, but not in FE.
         *
         * - `divi/button`
         * ButtonStyle is special kind of "options" where the options are actually the entire
         * element. We need to rethink this element-level module options.
         *
         * - `divi/form-field`
         * FormFieldStyle is special kind of "options" where the options are actually the entire
         * element. We need to rethink this element-level module options.
         *
         * @since ??
         *
         * @return array Array of style component map.
         *
         * @example:
         * ```php
         * // Get style component map.
         * $style_component_map = ElementStyleAdvancedStyles::style_component_map();
         * ```
         */
        public static function style_component_map()
        {
        }
        /**
         * Get advanced styles style declaration.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/ElementStyle/advanced-styles AdvancedStyles} in
         * `@divi/module` package.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type string      $selector             Optional. The CSS selector. Default `''`.
         *     @type array       $advanced_styles      Optional. An array of module advanced styles. Default `[]`.
         *     @type string|null $orderClass           Optional. The selector class name.
         *     @type bool        $isInsideStickyModule Optional. Whether the module is inside a sticky module or not. Default `false`.
         *     @type string      $attrs_json           Optional. The JSON string of module attribute data, use to improve performance.
         *     @type string      $returnType           Optional. This is the type of value that the function will return.
         *                                             Can be either `string` or `array`. Default `array`.
         *     @type string      $atRules              Optional. CSS at-rules to wrap the style declarations in. Default `''`.
         * }
         *
         * @return string|array The advanced styles style declaration.
         *
         * @example:
         * ```php
         * // Apply style using default arguments.
         * $args = [];
         * $style = ElementStyleAdvancedStyles::style( $args );
         *
         * // Apply style with specific selector and advanced styles.
         * $args = [
         *     'selector' => '.element1',
         *     'advanced_styles' => [
         *         [
         *             'componentName' => 'divi/text',
         *             'props' => [
         *                 'attr' => [
         *                     'text' => [
         *                         'desktop' => [
         *                             'value' => [
         *                                 'orientation' => 'left',
         *                             ],
         *                         ],
         *                     ],
         *                 ],
         *             ],
         *         ],
         *     ],
         * ];
         * $style = ElementStyleAdvancedStyles::style( $args );
         * // Result: ".element1 {text-align: left;}"
         * ```
         */
        public static function style($args)
        {
        }
    }
    /**
     * ElementComponents class.
     *
     * This class is responsible for handling the components of an element.
     *
     * @since ??
     */
    class ElementComponents
    {
        /**
         * Component function for rendering a background element.
         *
         * This function takes an array of arguments and returns a string containing the rendered background element.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/ElementClassnames ElementComponents} in
         * `@divi/module` package.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type array      $attrs         Optional. The attributes of the background element. Default `[]`.
         *     @type string     $id            Optional. The ID of the background element. Default empty string.
         *     @type bool|array $background    Optional. The background settings of the element. Default `null`.
         *     @type bool|array $boxShadow     Optional. Whether to include a box shadow for the element. Default `false`.
         *     @type int|null   $orderIndex    Optional. The order index of the element. Default `null`.
         *     @type int        $storeInstance Optional. The ID of instance where this block stored in BlockParserStore.
         *                                     Default `null`.
         * }
         * @return string The rendered background element.
         *
         * @example:
         * ```php
         * $args = [
         *     'attrs'         => [
         *         'attribute_1' => 'value_1',
         *         'attribute_2' => 'value_2',
         *     ],
         *     'id'            => 'element_id',
         *     'background'    => [
         *         'settings' => [
         *             'color' => 'red',
         *         ],
         *     ],
         *     'boxShadow'     => true,
         *     'orderIndex'    => 1,
         *     'storeInstance' => 'store_instance',
         * ];
         * $result = ElementComponents::component( $args );
         *
         * // This example demonstrates how to use the `component()` function to render a background element with custom attributes, ID, background settings, box shadow, order index, and store instance.
         * ```
         *
         * @example:
         * ```php
         * // Example of rendering a background element with default values.
         * $result = ElementComponents::component( [] );
         * ```
         */
        public static function component(array $args): string
        {
        }
    }
    /**
     * ElementFilterFunctions class.
     *
     * This class provides common filter functions for elements, such as button type attributes
     * and a filter function map.
     *
     * @since ??
     */
    class ElementFilterFunctions
    {
        /**
         * Filters the ElementStyle attributes based on the button's 'enable' style value.
         *
         * This function is used in button elements to determine if custom styles should be rendered or not.
         * If the 'Use custom styles for button' setting is turned off, no styles are applied to the button.
         *
         * @param array $attrs The attributes of the element.
         *
         * @return array The modified attributes of the element, with or without the 'button' key depending on the 'enable' style value.
         *
         * @example
         * ```php
         *     $element_attrs = [
         *         'button' => [
         *             'desktop' => [
         *                 'value' => [
         *                     'enable' => 'on' // Enable custom styles for button
         *                 ]
         *             ]
         *         ]
         *     ];
         *
         *     $modified_attrs = $this->button_type_attrs( $element_attrs );
         *
         *     // Use the modified attributes
         * ```
         */
        public static function button_type_attrs(array $attrs): array
        {
        }
        /**
         * Map of filter functions for element attribute.
         *
         * @var array $filter_function_map {
         *     @type string $button Button type attributes filter function.
         * }
         *
         * @since ??
         */
        public static $filter_function_map = ['button' => '\ET\Builder\Packages\Module\Options\Element\ElementFilterFunctions::button_type_attrs'];
    }
    /**
     * Interaction Classnames class.
     *
     * This class provides methods to generate CSS classes for interaction targeting.
     *
     * @since ??
     */
    class InteractionClassnames
    {
        /**
         * Generate interaction target classnames.
         *
         * Generates CSS classes for modules that are targets of interactions. Uses the persistent
         * interactionTarget attribute stored on the module rather than ephemeral module IDs.
         *
         * @since ??
         *
         * @param string $interaction_target_id The interaction target ID.
         *
         * @return string The target CSS class if this module is an interaction target.
         */
        public static function target_classnames(string $interaction_target_id): string
        {
        }
    }
    /**
     * ElementStyle class.
     *
     * This class provides the functionality for handling element styles.
     *
     * @since ??
     */
    class ElementStyle
    {
        /**
         * Get element style component.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/ElementStyle ElementStyle} in
         * `@divi/module` package.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type string        $selector                  Optional. The CSS selector. Default `null`.
         *     @type array         $attrs                     Optional. An array of module attribute data. Default `[]`.
         *     @type array         $defaultPrintedStyleAttrs  Optional. An array of default printed style attribute data. Default `[]`.
         *     @type callable      $attrsFilter               Optional. A callback function to filter the attributes. Default `null`.
         *     @type string|null   $orderClass                Optional. The selector class name.
         *     @type string        $type                      Optional. Element type. This might use built in callback for attributes.
         *                                                    Default `module`.
         *     @type bool          $isInsideStickyModule      Optional. Whether the module is inside a sticky module or not. Default `false`.
         *     @type bool          $hasBackgroundPresets          Optional. Whether background presets are actively applied. Default `false`.
         *     @type array         $background                Optional. An array of background style data. Default `[]`.
         *     @type array         $font                      Optional. An array of font style data. Default `[]`.
         *     @type array         $icon                      Optional. An array of icon style data. Default `[]`.
         *     @type array         $bodyFont                  Optional. An array of bodyFont style data. Default `[]`.
         *     @type array         $spacing                   Optional. An array of spacing style data. Default `[]`.
         *     @type array         $sizing                    Optional. An array of sizing style data. Default `[]`.
         *     @type array         $border                    Optional. An array of border style data. Default `[]`.
         *     @type array         $boxShadow                 Optional. An array of boxShadow style data. Default `[]`.
         *     @type array         $filters                   Optional. An array of filter style data. Default `[]`.
         *     @type array         $transform                 Optional. An array of transform style data. Default `[]`.
         *     @type array         $transition                Optional. An array of transition style data. Default `[]`.
         *     @type array         $disabledOn                Optional. An array of disabledOn style data. Default `[]`.
         *     @type array         $overflow                  Optional. An array of overflow style data. Default `[]`.
         *     @type array         $position                  Optional. An array of position style data. Default `[]`.
         *     @type array         $zIndex                    Optional. An array of zIndex style data. Default `[]`.
         *     @type array         $advanced_styles           Optional. An array of module advanced styles. Default `[]`.
         *     @type array         $button                    Optional. An array of button style data. Default `[]`.
         *     @type array         $order                     Optional. An array of order style data. default '[]'.
         *     @type bool          $asStyle                   Optional. Whether to wrap the style declaration with style tag or not.
         *                                                    Default `true`
         *     @type string        $returnType                Optional. This is the type of value that the function will return.
         *                                                    Can be either `string` or `array`. Default `array`.
         *     @type bool          $isParentFlexLayout        Optional. Whether the module is inside a parent layout flex or not. Default `false`.
         *     @type array         $layout                    Optional. An array of layout style data. Default `[]`.
         *     @type string        $atRules                   Optional. CSS at-rules to wrap the style declarations in.
         * }
         * }
         *
         * @return string|array The element style component.
         *
         * @example:
         * ```php
         * // Apply style using default arguments.
         * $args = [];
         * $style = ElementStyle::style( $args );
         *
         * // Apply style with specific selectors and properties.
         * $args = [
         *     'selectors' => [
         *         '.element1',
         *         '.element2',
         *     ],
         *     'propertySelectors' => [
         *         '.element1 .property1',
         *         '.element2 .property2',
         *     ]
         * ];
         * $style = ElementStyle::style( $args );
         * ```
         */
        public static function style($args)
        {
        }
    }
    /**
     * `ElementScriptData`
     *
     * @since ??
     */
    /**
     * ElementScriptData class.
     *
     * This class provides functionality to set data in script data element.
     *
     * @since ??
     */
    class ElementScriptData
    {
        /**
         * Set the attributes and options and generate script data for a given element.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/UseElementScriptData useElementScriptData} in
         * `@divi/module` package.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type string        $id             Optional. The ID of the element. Default empty string.
         *     @type string|null   $selector       Optional. The CSS selector of the element. Default `null`.
         *     @type array         $attrs          Optional. The attributes for the element. Default `[]`.
         *     @type array         $animation      Optional. The animation settings for the element. Default `[]`.
         *     @type array         $interactions   Optional. The interactions settings for the element. Default `[]`.
         *     @type array         $background     Optional. The background settings for the element. Default `[]`.
         *     @type array         $link           Optional. The link settings for the element. Default `[]`.
         *     @type array         $scroll         Optional. The scroll settings for the element. Default `[]`.
         *     @type array         $sticky         Optional. The sticky settings for the element. Default `[]`.
         *     @type null|string   $storeInstance  Optional. The ID of instance where this block stored in BlockParserStore. Default `null`.
         * }
         *
         * @return void
         */
        public static function set(array $args): void
        {
        }
    }
}
namespace ET\Builder\Packages\Module\Options\Text {
    /**
     * TextStyle class.
     *
     * @since ??
     */
    class TextStyle
    {
        /**
         * Get text style component.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/TextStyle TextStyle} in
         * `@divi/module` package.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type string        $selector                 The CSS selector.
         *     @type array         $selectors                Optional. An array of selectors for each breakpoint and state. Default `[]`.
         *     @type callable      $selectorFunction         Optional. The function to be called to generate CSS selector. Default `null`.
         *     @type array         $propertySelectors        Optional. The property selectors that you want to unpack. Default `[]`.
         *     @type array         $attr                     An array of module attribute data.
         *     @type array         $defaultPrintedStyleAttr  Optional. An array of default printed style attribute data. Default `[]`.
         *     @type array|bool    $important                Optional. Whether to apply "!important" flag to the style declarations.
         *                                                   Default `false`.
         *     @type bool          $asStyle                  Optional. Whether to wrap the style declaration with style tag or not.
         *                                                   Default `true`
         *     @type bool          $orientation              Optional. Whether to apply orientation style or not. Default `true`.
         *     @type string|null   $orderClass               Optional. The selector class name.
         *     @type bool          $isInsideStickyModule     Optional. Whether the module is inside a sticky module or not. Default `false`.
         *     @type string        $returnType Optional. This is the type of value that the function will return.
         *                                                   Can be either `string` or `array`. Default `array`.
         * }
         *
         * @return string|array The text style component
         *
         * @example:
         * ```php
         *     // Generate a basic stylesheet with a single selector and property.
         *     $args = array(
         *         'selectors'  => array(
         *             array(
         *                 'value' => '#my-element',
         *             ),
         *         ),
         *         'propertySelectors' => array(
         *             'text' => array(
         *                 'color' => array(
         *                     'value' => '#000000',
         *                 ),
         *             ),
         *         ),
         *     );
         *     $stylesheet = My_Namespace\My_Class::style( $args );
         * ```
         *
         * @example:
         * ```php
         *     // Generate a stylesheet with multiple selectors and multiple properties.
         *     $args = array(
         *         'selectors'  => array(
         *             array(
         *                 'value' => '.my-class',
         *             ),
         *             array(
         *                 'value' => '#my-element',
         *             ),
         *         ),
         *         'propertySelectors' => array(
         *             'text' => array(
         *                 'color' => array(
         *                     'value' => '#000000',
         *                 ),
         *                 'font-size' => array(
         *                     'value' => '16px',
         *                 ),
         *             ),
         *             'background' => array(
         *                 'background-color' => array(
         *                     'value' => '#FFFFFF',
         *                 ),
         *             ),
         *         ),
         *         'orientation' => false,
         *     );
         *     $stylesheet = My_Namespace\My_Class::style( $args );
         * ```
         */
        public static function style(array $args)
        {
        }
    }
    /**
     * TextClassnames class.
     *
     * @since ??
     */
    class TextClassnames
    {
        /**
         * Get the text alignment classnames.
         *
         * This function generates classnames for aligning text based on the provided attributes.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/GetAlignmentClassnames getAlignmentClassnames} in
         * `@divi/module` package.
         *
         * @since ??
         *
         * @param array $attr The text group attributes.
         *
         * @return string The generated classnames for text alignment.
         *
         * @example:
         * ```php
         * $attr = [
         *     'text' => [
         *         'breakpoint1' => [
         *             'value' => [
         *                 'orientation' => 'center'
         *             ]
         *         ],
         *         'breakpoint2' => [
         *             'value' => [
         *                 'orientation' => 'right'
         *             ]
         *         ]
         *     ]
         * ];
         *
         * $classnames = self::get_alignment_classnames( $attr );
         * // Returns: "et_pb_text_align_center et_pb_text_align_right-breakpoint2"
         * ```
         */
        public static function get_alignment_classnames(array $attr): string
        {
        }
        /**
         * Get background layout classnames.
         *
         * This function retrieves the classnames for the background layout of a text group.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/GetBackgroundLayoutClassnames getBackgroundLayoutClassnames} in
         * `@divi/module` package.
         *
         * @since ??
         *
         * @param array $attr          The text group attributes.
         * @param bool  $skip_desktop  Optional. Whether to skip adding the desktop breakpoint classname. Default `false`.
         * @param bool  $is_text_color Optional. Whether to render the text color classname. Default `false`.
         *
         * @return string The generated classnames separated by a space.
         *
         * @example:
         * ```php
         * self::get_background_layout_classnames( $attr );
         * ```
         *
         * @example:
         * ```php
         * $attr = [
         *     'text' => [
         *         'desktop' => [
         *             'value' => [
         *                 'color' => 'dark',
         *             ],
         *         ],
         *         'tablet' => [
         *             'hover' => [
         *                 'color' => 'light',
         *             ],
         *         ],
         *     ],
         * ];
         *
         * self::get_background_layout_classnames( $attr, true, true );
         * ```
         */
        public static function get_background_layout_classnames(array $attr, bool $skip_desktop = false, bool $is_text_color = false): string
        {
        }
        /**
         * Get the color classnames for a text option group.
         * Generate set of text color classnames for each attribute breakpoint and state.
         *
         * This function retrieves the color classnames for a given text option group.
         * It iterates through the available breakpoints and uses the value mapping array to determine the corresponding color classnames.
         * It then sanitizes the classnames and returns them as an array if they exist.
         *
         * @since ??
         *
         * @param array $text_option_group_attrs The attributes of the text option group.
         *
         * @return array The color classnames for the text option group.
         *
         * @example:
         * ```php
         *     $text_option_group_attrs = [
         *         'desktop' => [
         *             'value' => [
         *                 'color' => 'light',
         *             ],
         *         ],
         *         'tablet' => [
         *             'value' => [
         *                 'color' => 'dark',
         *             ],
         *         ],
         *     ];
         *     $color_classnames = self::get_color_classnames( $text_option_group_attrs );
         *     // Returns ['et_pb_text_color_dark', 'et_pb_text_color_light_tablet']
         *
         * @example:
         * ```php
         *     $text_option_group_attrs = [
         *         'desktop' => [
         *             'value' => [
         *                 'color' => 'invalid',
         *             ],
         *         ],
         *         'tablet' => [
         *             'value' => [
         *                 'color' => 'dark',
         *             ],
         *         ],
         *     ];
         *     $color_classnames = self::get_color_classnames( $text_option_group_attrs );
         *     // Returns ['et_pb_text_color_dark']
         * ```
         */
        public static function get_color_classnames(array $text_option_group_attrs): array
        {
        }
        /**
         * Get the classnames for text options.
         *
         * This function is used to retrieve the classnames for text options based on the given attributes and settings.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/TextOptionsClassnames textOptionsClassnames} in
         * `@divi/module` package.
         *
         * @since ??
         *
         * @param array $attr      The attributes for the text options.
         * @param array $settings  Optional. The settings for the text options. Default `[]`.
         *
         * @return string          The classnames for the text options.
         *
         * @example:
         * ```php
         * $attr = array(
         *   'color' => true,
         *   'orientation' => true,
         * );
         *
         * $settings = array(
         *   'color' => false,
         *   'orientation' => true,
         * );
         *
         * $class_names = self::text_options_classnames( $attr, $settings );
         * ```
         */
        public static function text_options_classnames(array $attr, array $settings = []): string
        {
        }
    }
    /**
     * TextPresetAttrsMap class.
     *
     * This class provides static map for the text preset attributes.
     *
     * @since ??
     */
    class TextPresetAttrsMap
    {
        /**
         * Get the map for the text preset attributes.
         *
         * @since ??
         *
         * @param string $attr_name The attribute name.
         *
         * @return array The map for the text preset attributes.
         */
        public static function get_map(string $attr_name)
        {
        }
    }
}
namespace ET\Builder\Packages\Module\Options\Font {
    /**
     * FontStyle class.
     *
     * This class has font style functionality.
     *
     * @since ??
     */
    class FontStyle
    {
        /**
         * Get font style component.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/FontStyle FontStyle} in
         * `@divi/module` package.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type string        $selector                 The CSS selector.
         *     @type array         $selectors                Optional. An array of selectors for each breakpoint and state. Default `[]`.
         *     @type callable      $selectorFunction         Optional. The function to be called to generate CSS selector. Default `null`.
         *     @type array         $propertySelectors        Optional. The property selectors that you want to unpack. Default `[]`.
         *     @type array         $attr                     An array of module attribute data.
         *     @type array         $defaultPrintedStyleAttr  Optional. An array of default printed style attribute data. Default `[]`.
         *     @type array|bool    $important                Optional. Whether to apply "!important" flag to the style declarations.
         *                                                   Default `false`.
         *     @type bool          $asStyle                  Optional. Whether to wrap the style declaration with style tag or not.
         *                                                   Default `true`
         *     @type string|bool   $headingLevel             Optional. HTML heading tag. Default `false`.
         *     @type string|null   $orderClass               Optional. The selector class name.
         *     @type bool          $isInsideStickyModule     Optional. Whether the module is inside a sticky module or not. Default `false`.
         *     @type string        $attrs_json               Optional. The JSON string of module attribute data, use to improve performance.
         *     @type string        $returnType               Optional. This is the type of value that the function will return.
         *                                                   Can be either `string` or `array`. Default `array`.
         *     @type string        $atRules                  Optional. CSS at-rules to wrap the style declarations in. Default `''`.
         * }
         *
         * @return string|array The font style component.
         *
         * @example:
         * ```php
         * // Apply style using default arguments.
         * $args = [];
         * $style = FontStyle::style( $args );
         *
         * // Apply style with specific selectors and properties.
         * $args = [
         *     'selectors' => [
         *         '.element1',
         *         '.element2',
         *     ],
         *     'propertySelectors' => [
         *         '.element1 .property1',
         *         '.element2 .property2',
         *     ]
         * ];
         * $style = FontStyle::style( $args );
         * ```
         */
        public static function style(array $args)
        {
        }
    }
    /**
     * FontPresetAttrsMap class.
     *
     * This class provides static map for the text shadow preset attributes.
     *
     * @since ??
     */
    class FontPresetAttrsMap
    {
        /**
         * Get the map for the text shadow preset attributes.
         *
         * @since ??
         *
         * @param string $attr_name The attribute name.
         * @param array  $args      The arguments.
         *
         * @return array The map for the text shadow preset attributes.
         */
        public static function get_map(string $attr_name, array $args = [])
        {
        }
    }
}
namespace ET\Builder\Packages\Module\Layout\Components\ModuleElements {
    /**
     * Module related helper class.
     *
     * @since ??
     */
    class ModuleElements
    {
        /**
         * Module ID
         *
         * @since ??
         *
         * @var string
         */
        public $id;
        /**
         * Module name
         *
         * @since ??
         *
         * @var string
         */
        public $name;
        /**
         * A key-value pair of module attributes data where the key is the module attribute name and the value is the formatted attribute array.
         *
         * @since ??
         *
         * @var array
         */
        public $module_attrs = [];
        /**
         * A key-value pair of selectors where the key is the module attribute name and the value is the selector.
         *
         * @since ??
         *
         * @var array
         */
        public $selectors = [];
        /**
         * Key-value pair of module metadata (module.json config file).
         *
         * @since ??
         *
         * @var WP_Block_Type
         */
        public $module_metadata;
        /**
         * Base order classname.
         *
         * @since ??
         *
         * @var string
         */
        public $base_order_class = '';
        /**
         * The selector class name.
         *
         * @since ??
         *
         * @var string
         */
        public $order_class = '';
        /**
         * Base wrapper order classname.
         *
         * @since ??
         *
         * @var string
         */
        public $base_wrapper_order_class = '';
        /**
         * The selector class name.
         *
         * @since ??
         *
         * @var string
         */
        public $wrapper_order_class = '';
        /**
         * Module name class name.
         *
         * @since ??
         *
         * @var string
         */
        public $module_name_class = '';
        /**
         * Module order ID.
         *
         * @since ??
         *
         * @var string
         */
        public $order_id = '';
        /**
         *
         * Module order index.
         *
         * @since ??
         *
         * @var mixed|null
         */
        public $order_index;
        /**
         *
         * Module store instance.
         *
         * @since ??
         *
         * @var int|null
         */
        public $store_instance;
        /**
         * Default printed styles.
         *
         * @var array
         */
        public $default_printed_style_attrs = [];
        /**
         * Preset printed styles.
         *
         * @var array
         */
        public $preset_printed_style_attrs = [];
        /**
         * Create an instance of ModuleElements class.
         *
         * @since ??
         *
         * @param array $args {
         *     Optional. An array of arguments. Default `[]`.
         *
         *     @type string $id                The Module unique ID.
         *     @type string $name              The Module name.
         *     @type array  $moduleAttrs       A key-value pair of module attributes data where the key is
         *                                     the module attribute name and the value is the formatted attribute array.
         *     @type array  $selectors         Optional. A key-value pair of selectors where the key is the module attribute
         *                                     name and the value is the selector. If not provided, the selectors will be
         *                                     retrieved from the module.json config file.
         *                                     Default `ModuleRegistration::get_selectors( $this->name )`.
         *     @type int    $storeInstance     Optional. The ID of instance where the module object is stored in BlockParserStore.
         *                                     Default `null`.
         *     @type int    $orderIndex        Optional. The order index of the module. Default `null`.
         *     @type WP_Block_Type|array $moduleMetadata Optional. The module metadata. Could be an instance of WP_Block_Type or an array to be converted into WP_Block_Type instance.
         *     @type boolean $is_custom_post_type Optional. Whether current post type is custom post type or not. Default `false`.
         *     @type boolean $is_parent_flex_layout Optional. Whether parent module is flex or not. Default `false`.
         *     @type array  $targetedAttributes Optional. Custom attributes separated by target element. Default `[]`.
         * }
         */
        public function __construct(array $args = [])
        {
        }
        /**
         * Create a new instance of the ModuleElements class with the given arguments.
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type string $id          The Module unique ID.
         *     @type string $name        The Module name.
         *     @type array  $moduleAttrs A key-value pair of module attributes data where the key is the module attribute name
         *                               and the value is the formatted attribute array.
         *     @type array  $selectors   Optional. A key-value pair of selectors where the key is the module attribute name and
         *                               the value is the selector.
         *                               If not provided, the selectors will be retrieved from the module.json config file.
         *                               Default `ModuleRegistration::get_selectors( $this->name )`.
         * }
         *
         * @return ModuleElements A new instance of the ModuleElements class.
         */
        public static function create(array $args): \ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements
        {
        }
        /**
         * Get inside sticky module status.
         *
         * @since ??
         *
         * @return boolean Whether current module is inside another sticky module or not.
         */
        public function get_is_inside_sticky_module()
        {
        }
        /**
         * Get parent layout flex status.
         *
         * @since ??
         *
         * @return boolean Whether current module is parent layout flex or not.
         */
        public function get_is_parent_flex_layout()
        {
        }
        /**
         * Get parent layout grid status.
         *
         * @since ??
         *
         * @return boolean Whether current module is parent layout grid or not.
         */
        public function get_is_parent_grid_layout()
        {
        }
        /**
         * Render HTML code with specified attributes and children.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type string                           $tagName              Optional. HTML Element tag. Default `div`.
         *     @type string                           $parentTag            Optional. The parent HTML Element tag where this element will be rendered. Default empty string.
         *                                                                  This is used to compute the required attributes for certain self-closing tags like `source` which
         *                                                                  needs to know the parent tag to compute the required attributes list.
         *     @type array                            $attributes           Optional. A key-value pair array of attributes data. Default `[]`.
         *                                                                    - The array item key must be a string.
         *                                                                    - For boolean attributes, the array item value must be a `true`.
         *                                                                    - For key-value pair attributes, the array item value must be a MultiViewElementValue object,
         *                                                                      array of ModuleElementsAttr constructor arguments, int, float, string, boolean, array or null.
         *                                                                    - `ModuleElementsAttr` or array of ModuleElementsAttr constructor arguments value will be
         *                                                                       computed with multi view data.
         *                                                                    - `boolean` value will be stringified to avoid `true` get printed as `1` and `false` get
         *                                                                       printed as `0`.
         *                                                                    - `array` value only applicable for `style` attribute.
         *                                                                    - `null` value will skip the attribute to be rendered.
         *     @type string|array|ModuleElementsAttr $children              Optional. The children element. Default `null`.
         *                                                                    - Pass instance of ModuleElementsAttr or array of ModuleElementsAttr constructor arguments to
         *                                                                      compute multi view data.
         *                                                                    - Pass string for single children element.
         *                                                                    - Pass array for multiple children elements and nested children elements.
         *                                                                    - Only applicable for non self-closing tags.
         *     @type callable                         $childrenSanitizer    Optional. The function that will be invoked to sanitize/escape the children element. Default `esc_html`.
         *     @type array                            $attributesSanitizers Optional. A key-value pair array of custom sanitizers that will be used to override the default sanitizer.
         *                                                                  Default `[]`.
         *     @type string                           $attrName             Optional. The Module attribute name. Default empty string.
         *     @type array                            $attr                 Optional. The Module formatted attribute array. Default `[]`.
         *     @type string                           $attrSubName          Optional. The attribute sub name that will be queried. Default `null`.
         *     @type callable                         $valueResolver        Optional. A function that will be invoked to resolve the value. Default `null`.
         *     @type string                           $selector             Optional. The selector of element to be updated. Default `null`.
         *     @type string                           $hoverSelector        Optional. The selector to trigger hover event. Default `null`.
         *     @type bool                             $forceRender          Optional. Flag to keep render the HTML code even if the children element is empty
         *                                                                  or the required attributes in certain self-closing tags are not provided, or the module attribute that
         *                                                                  passed into the `hiddenIfFalsy` param has no value across all breakpoints and states is empty.
         *                                                                  Default `false`.
         *     @type array|ModuleElementsAttr         $hiddenIfFalsy        Optional. Parameter that will be computed to determine if the element should be hidden if
         *                                                                  certain module attribute value is falsy. Default ``.
         *                                                                     - Array of ModuleElementsAttr constructor arguments.
         *                                                                     - Instance of ModuleElementsAttr.
         *     @type string                             $elementType        Optional. The element type. Default `element`.
         *     @type array                              $elementProps       Optional. The element props. Default `[]`.
         *     @type bool                               $skipAttrChildren   Optional. When true, prevents automatic content generation
         *                                                                  from module attributes and uses explicitly provided children
         *                                                                  instead. Useful for self-closing tags (input, img) or elements
         *                                                                  with custom pre-processed content. Default false.
         *
         * }
         *
         * @return string The rendered HTML code.
         */
        public function render(array $args): string
        {
        }
        /**
         * Set base order class.
         *
         * @since ??
         *
         * @param string $base_order_class The base order class.
         */
        public function set_base_order_class(string $base_order_class): void
        {
        }
        /**
         * Set the order class.
         *
         * @since ??
         *
         * @param string $order_class The order class.
         *
         * @return void
         */
        public function set_order_class(string $order_class): void
        {
        }
        /**
         * Set base wrapper order class.
         *
         * @since ??
         *
         * @param string $base_wrapper_order_class The base wrapper order class.
         */
        public function set_base_wrapper_order_class(string $base_wrapper_order_class): void
        {
        }
        /**
         * Set the wrapper order class.
         *
         * @since ??
         *
         * @param string $wrapper_order_class The order class.
         *
         * @return void
         */
        public function set_wrapper_order_class(string $wrapper_order_class): void
        {
        }
        /**
         * Set module name class.
         *
         * @since ??
         *
         * @param string $module_name_class The module name class.
         *
         * @return void
         */
        public function set_module_name_class(string $module_name_class): void
        {
        }
        /**
         * Set the module order ID.
         *
         * @since ??
         *
         * @param string $order_id The order ID.
         *
         * @return void
         */
        public function set_order_id(string $order_id): void
        {
        }
        /**
         * Set module script data.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type string   $attrName        Optional. The attribute name declared in module.json config file. Default empty string.
         *     @type array    $scriptDataProps Optional. A key-value pair array of script data props. Default `[]`.
         *     @type callable $attrsResolver   Optional. A function that will be called to filter/resolve the attributes data. Default `null`.
         * }
         *
         * @return void
         */
        public function script_data(array $args): void
        {
        }
        /**
         * Set the style group which will be used to calculate the attributes data that will be used to render the style.
         *
         * @since ??
         *
         * @param string $group The style group.
         *
         * @return void
         */
        public function set_style_group(string $group)
        {
        }
        /**
         * Render style declaration.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type string $attrName   Optional. The attribute name declared in module.json config file. Default empty string.
         *     @type array  $styleProps Optional. A key-value pair array of style props. Default `[]`.
         *     @type bool   $isMergeRecursiveProps Optional. Whether to merge style properties recursively. Default `false`.
         *     @type string $group      Optional. The style group. This group will be used to calculate the attributes data that will be used to render the style. Default `module`.
         * }
         *
         * @return string|array|null
         */
        public function style(array $args)
        {
        }
        /**
         * Set custom module attributes.
         *
         * This method is used to set custom module attributes that will be used in the current module instance.
         *
         * @param array $attrs An array of custom module attributes.
         * @return void
         */
        public function use_custom_module_attrs(array $attrs)
        {
        }
        /**
         * Clear custom module attributes.
         *
         * This method is used to clear custom module attributes that have been set using `use_custom_module_attrs` method.
         *
         * @return void
         */
        public function clear_custom_attributes()
        {
        }
        /**
         * Render element style components.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type string $attrName             Optional. The attribute name declared in module.json config file. Default empty string.
         *     @type array  $styleComponentsProps Optional. A key-value pair array of component props. Default `[]`.
         * }
         *
         * @return string|null
         */
        public function style_components(array $args)
        {
        }
        /**
         * Merges module attributes with preset and group preset attributes.
         *
         * This method retrieves and merges attributes from a specified module,
         * its selected preset, and any applicable group presets.
         *
         * @since ??
         *
         * @return array The merged attributes array.
         */
        public function get_merged_attrs(): array
        {
        }
    }
    /**
     * Module related helper class.
     *
     * @since ??
     */
    class ModuleElementsAttr
    {
        /**
         * Create an instance of ModuleElementsAttr class.
         *
         * @since ??
         *
         * @param array $args {
         *    An array of arguments.
         *
         *     @type string       $attrName      The module attribute name. Optional when `attr` is defined.
         *     @type array        $attr          The module formatted attribute array. Optional when `attrName` is defined.
         *     @type string       $subName       Optional. The attribute sub name that will be queried. Default `null`.
         *     @type callable     $valueResolver Optional. A function that will be invoked to resolve the value. Default `null`.
         *     @type string       $selector      Optional. The selector of element to be updated. Default `null`.
         *     @type string       $hoverSelector Optional. The selector to trigger hover event. Default `null`.
         * }
         */
        public function __construct(array $args)
        {
        }
        /**
         * Creates a new instance of the ModuleElementsAttr class with the given arguments.
         *
         * @since ??
         *
         * @param array $args {
         *    An array of arguments.
         *
         *     @type string       $attrName      The module attribute name. Optional when `attr` is defined.
         *     @type array        $attr          The module formatted attribute array. Optional when `attrName` is defined.
         *     @type string       $subName       Optional. The attribute sub name that will be queried. Default `null`.
         *     @type callable     $valueResolver Optional. A function that will be invoked to resolve the value. Default `null`.
         *     @type string       $selector      Optional. The selector of element to be updated. Default `null`.
         *     @type string       $hoverSelector Optional. The selector to trigger hover event. Default `null`.
         * }
         *
         * @return ModuleElementsAttr A new instance of the ModuleElementsAttr class.
         */
        public static function create(array $args): \ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElementsAttr
        {
        }
        /**
         * Get the module attribute name.
         *
         * @since ??
         *
         * @return string|null The module attribute name.
         */
        public function get_attr_name(): ?string
        {
        }
        /**
         * Get the module formatted attribute.
         *
         * @since ??
         *
         * @return array The module formatted attribute.
         */
        public function get_attr(): array
        {
        }
        /**
         * Get the module attribute sub name.
         *
         * @since ??
         *
         * @return string|null The module attribute sub name.
         */
        public function get_sub_name(): ?string
        {
        }
        /**
         * Get the function that will be invoked to resolve the value.
         *
         * @since ??
         *
         * @return callable|null The function that will be invoked to resolve the value.
         */
        public function get_value_resolver(): ?callable
        {
        }
        /**
         * Get the selector of element to be updated.
         *
         * @since ??
         *
         * @return string|null The selector of element to be updated.
         */
        public function get_selector(): ?string
        {
        }
        /**
         * Get the selector to trigger hover event.
         *
         * @since ??
         *
         * @return string|null The selector to trigger hover event.
         */
        public function get_hover_selector(): ?string
        {
        }
        /**
         * Set the module attribute name.
         *
         * @since ??
         *
         * @param string $attr_name The module attribute name.
         *
         * @return void
         */
        public function set_attr_name(string $attr_name): void
        {
        }
        /**
         * Set the module formatted attribute array.
         *
         * @since ??
         *
         * @param array $attr The module formatted attribute array.
         *
         * @return void
         */
        public function set_attr(array $attr): void
        {
        }
        /**
         * Set the module attribute sub name.
         *
         * @since ??
         *
         * @param string $sub_name The module attribute sub name.
         *
         * @return void
         */
        public function set_sub_name(string $sub_name): void
        {
        }
        /**
         * Set the function that will be invoked to resolve the value.
         *
         * @since ??
         *
         * @param callable $value_resolver The function that will be invoked to resolve the value.
         *
         * @return void
         */
        public function set_value_resolver(callable $value_resolver): void
        {
        }
        /**
         * Set the selector of element to be updated.
         *
         * @since ??
         *
         * @param string $selector The selector of element to be updated.
         *
         * @return void
         */
        public function set_selector(string $selector): void
        {
        }
        /**
         * Set the selector to trigger hover event.
         *
         * @since ??
         *
         * @param string $hover_selector The selector to trigger hover event.
         *
         * @return void
         */
        public function set_hover_selector(string $hover_selector): void
        {
        }
        /**
         * Set the value of the instance properties.
         *
         * @since ??
         *
         * @param array $args                The arguments to set the value.
         * @param bool  $create_new_instance Optional. Whether to create a new instance or not. Default `true`.
         *
         * @return ModuleElementsAttr A new instance, or the same instance if `$create_new_instance` is `false`.
         */
        public function set(array $args, $create_new_instance = true): \ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElementsAttr
        {
        }
    }
    /**
     * ModuleElementsUtils class.
     *
     * @since ??
     */
    class ModuleElementsUtils
    {
        /**
         * Interpolate a selector template with a value.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/InterpolateSelector interpolateSelector} in
         * `@divi/module` packages.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type string $value                  The value to interpolate.
         *     @type string|array $selectorTemplate The selector template to interpolate.
         *     @type string $placeholder            Optional. The placeholder to replace. Default `{{selector}}`.
         * }
         *
         * @return string|array The interpolated selector.
         *                      If the selector template is a string, a string is returned.
         *                      Otherwise an array is returned.
         */
        public static function interpolate_selector(array $args)
        {
        }
        /**
         * Extracts the attachment URL from the image source.
         *
         * @since ??
         *
         * @param string $image_src The URL of the image attachment.
         * @return array {
         *    An array containing the image path without the scaling suffix and the query string,
         *    and the scaling suffix if found.
         *
         *    @type string $path   The image path without the scaling suffix and query string.
         *    @type string $suffix The scaling suffix if found. Otherwise an empty string.
         * }
         */
        public static function extract_attachment_url(string $image_src): array
        {
        }
        /**
         * Converts an attachment URL to its corresponding ID.
         *
         * @since ??
         *
         * @param string $image_src The URL of the attachment image.
         * @return int The ID of the attachment.
         */
        public static function attachment_url_to_id(string $image_src): int
        {
        }
        /**
         * Populates the image element attributes with additional information.
         *
         * This function takes an array of attributes and populates it with additional information
         * related to the image element, such as the attachment ID, width, height, srcset, and sizes.
         *
         * @since ??
         *
         * @param array $attrs The array of attributes to be populated.
         * @return array The updated array of attributes.
         */
        public static function populate_image_element_attrs(array $attrs): array
        {
        }
    }
}
namespace ET\Builder\Packages\Module {
    /**
     * Module class.
     *
     * @since ??
     */
    class Module
    {
        /**
         * Module renderer.
         *
         * This function is used to render a module in FE.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-module/functions/Module Module}
         * in `@divi/module` package.
         *
         * @since ??
         *
         * @param array $args {
         *     An array of arguments.
         *
         *     @type array    $attrs                     Optional. Module attributes data. Default `[]`.
         *     @type array    $htmlAttrs                 Optional. Custom HTML attributes. Default `null`.
         *     @type string   $id                        Optional. Module ID. Default empty string.
         *                                               In Visual Builder, the ID of module is a UUIDV4 string.
         *                                               In FrontEnd, it is module name + order index.
         *     @type string   $children                  Optional. The children element(s). Default empty string.
         *     @type string   $childrenIds               Optional. Module inner blocks. Default `[]`.
         *     @type bool     $hasModule                 Optional. Whether the module has module or not. Default `true`.
         *     @type string   $moduleCategory            Optional. Module category. Default empty string.
         *     @type string   $classname                 Optional. Custom CSS class attribute. Default empty string.
         *     @type bool     $isFirst                   Optional. Is first child flag. Default `false`.
         *     @type bool     $isLast                    Optional. Is last child flag. Default `false`.
         *     @type bool     $hasModuleClassName        Optional. Has module class name. Default `true`.
         *     @type callable $classnamesFunction        Optional. Function that will be invoked to generate module CSS class. Default `null`.
         *     @type array    $styles                    Optional. Custom inline style attribute. Default `[]`.
         *     @type string   $tag                       Optional. HTML tag. Default `div`.
         *     @type bool     $hasModuleWrapper          Optional. Has module wrapper flag. Default `false`.
         *     @type string   $wrapperTag                Optional. Wrapper HTML tag. Default `div`.
         *     @type array    $wrapperHtmlAttrs          Optional. Wrapper custom html attributes. Default `[]`.
         *     @type string   $wrapperClassname          Optional. Wrapper custom CSS class. Default empty string.
         *     @type callable $wrapperClassnamesFunction Optional. Function that will be invoked to generate module wrapper CSS class. Default `null`.
         *     @type callable $stylesComponent           Optional. Function that will be invoked to generate module styles. Default `null`.
         *     @type array    $parentAttrs               Optional. Parent module attributes data. Default `[]`.
         *     @type string   $parentId                  Optional. Parent Module ID. Default empty string.
         *                                               In Visual Builder, the ID of module is a UUIDV4 string.
         *                                               In FrontEnd, it is parent module name + parent order index.
         *     @type string   $parentName                Optional. Parent module name. Default empty string.
         *     @type array    $siblingAttrs              Optional. Module sibling attributes data. Default `[]`.
         *     @type array    $settings                  Optional. Custom settings. Default `[]`.
         *     @type int      $orderIndex                Optional. Module order index. Default `0`.
         *     @type int      $storeInstance             Optional. The ID of instance where this block stored in BlockParserStore class. Default `null`.
         * }
         *
         * @return string The module HTML.
         *
         * @example:
         * ```php
         *  ET_Builder_Module::render( array(
         *    'arg1' => 'value1',
         *    'arg2' => 'value2',
         *  ) );
         * ```
         *
         * @example:
         * ```php
         *  $module = new ET_Builder_Module();
         *  $module->render( array(
         *    'arg1' => 'value1',
         *    'arg2' => 'value2',
         *   ) );
         * ```
         */
        public static function render(array $args): string
        {
        }
        /**
         * Renders the styles preset for a module.
         *
         * @since ??
         *
         * @param array $args {
         *     Array of arguments.
         *
         *     @type string         $name                            The name of the module.
         *     @type array          $attrs                           The attributes of the module.
         *     @type array          $defaultPrintedStyleAttrs        The default printed style attributes.
         *     @type string         $parentId                        The ID of the parent module.
         *     @type string         $parentName                      The name of the parent module.
         *     @type string         $id                              The ID of the module.
         *     @type int            $storeInstance                   The store instance.
         *     @type ModuleElements $elements                        The elements of the module.
         *     @type Classnames     $classnamesInstance              The classnames instance.
         *     @type Classnames     $wrapperClassnamesInstance       The wrapper classnames instance.
         *     @type string         $selectorPrefix                  The selector prefix.
         *     @type bool           $hasModuleWrapper                Whether the module has a wrapper.
         *     @type bool           $isStyleEnqueuedAsStaticCss      Whether the style is enqueued as static CSS.
         *     @type callable       $stylesComponent                 The styles component.
         *     @type array          $settings                        The settings of the module.
         *     @type int            $orderIndex                      The order index of the module.
         * }
         *
         * @return void
         */
        public static function render_styles_preset_module(array $args): void
        {
        }
        /**
         * Renders styles for a preset group.
         *
         * @since ??
         *
         * @param array $args {
         *     Array of arguments.
         *
         *     @type array          $attrs                           Attributes of the module.
         *     @type string         $parentId                        ID of the parent module.
         *     @type array          $defaultPrintedStyleAttrs        Default printed style attributes.
         *     @type string         $name                            Name of the module.
         *     @type ModuleElements $elements                        Elements of the module.
         *     @type Classnames     $classnamesInstance              Instance of classnames.
         *     @type Classnames     $wrapperClassnamesInstance       Instance of wrapper classnames.
         *     @type string         $id                              ID of the module.
         *     @type int            $storeInstance                   Instance of the store.
         *     @type string         $selectorPrefix                  Prefix for the selector.
         *     @type bool           $hasModuleWrapper                Whether the module has a wrapper.
         *     @type bool           $isStyleEnqueuedAsStaticCss      Whether the style is enqueued as static CSS.
         *     @type callable       $stylesComponent                 Component for styles.
         *     @type array          $settings                        Settings for the module.
         *     @type int            $orderIndex                      Order index of the module.
         * }
         *
         * @return void
         */
        public static function render_styles_preset_group(array $args): void
        {
        }
        /**
         * Renders preset styles.
         *
         * @since ??
         *
         * @param array $args {
         *     Array of arguments.
         *
         *     @type string           $styleGroup                  The style group. Either 'preset' or 'presetGroup'.
         *     @type string           $name                        The name of the module.
         *     @type array            $defaultPrintedStyleAttrs    Default printed style attributes.
         *     @type ModuleElements   $elements                    Instance of ModuleElements.
         *     @type Classnames       $classnamesInstance          Instance of Classnames for the module.
         *     @type Classnames       $wrapperClassnamesInstance   Instance of Classnames for the module wrapper.
         *     @type string           $id                          The ID of the module.
         *     @type int              $storeInstance               Instance of the store.
         *     @type string           $selectorPrefix              The selector prefix.
         *     @type bool             $hasModuleWrapper            Whether the module has a wrapper.
         *     @type bool             $isStyleEnqueuedAsStaticCss  Whether the style is enqueued as static CSS.
         *     @type bool             $isSelectorProcessed         Whether the selector has been processed.
         *     @type callable         $stylesComponent             The styles component callback.
         *     @type array            $settings                    The settings array.
         *     @type int              $orderIndex                  The order index.
         *     @type GlobalPresetItem $presetItem                  Instance of GlobalPresetItem for the current preset.
         *     @type GlobalPresetItem $parentPresetItem            Instance of GlobalPresetItem for the parent preset.
         *     @type GlobalPresetItem $siblingPreviousPresetItem   Instance of GlobalPresetItem for the previous sibling preset.
         *     @type GlobalPresetItem $siblingNextPresetItem       Instance of GlobalPresetItem for the next sibling preset.
         * }
         *
         * @return void
         */
        public static function render_styles_preset(array $args): void
        {
        }
    }
}
namespace ET\Builder\Packages\ModuleLibrary {
    // phpcs:disable Squiz.Commenting.InlineComment -- Temporarily disabled to get the PR CI pass for now. TODO: Fix this later.
    // phpcs:disable Squiz.PHP.CommentedOutCode.Found -- Temporarily disabled to get the PR CI pass for now. TODO: Fix this later.
    // phpcs:disable ET.Sniffs.Todo.TodoFound -- Temporarily disabled to get the PR CI pass for now. TODO: Fix this later.
    // phpcs:disable WordPress.NamingConventions.ValidHookName -- Temporarily disabled to get the PR CI pass for now. TODO: Fix this later.
    /**
     * ModuleRegistration class.
     *
     * This is a helper class that provides an easier interface to register modules on the backend.
     *
     * @since ??
     */
    class ModuleRegistration
    {
        /**
         * Retrieves the core module name derived from the metadata folder path.
         *
         * This function processes the given metadata folder path to extract and
         * return the core module name in the appropriate format.
         *
         * Examples:
         * - Divi/includes/builder-5/visual-builder/packages/module-library/src/components/[module-name]/module.json
         * - Divi/includes/builder-5/visual-builder/packages/module-library/src/components/woocommerce/[module-name]/module.json
         *
         * @since ??
         *
         * @param string $metadata_folder The path to the metadata folder.
         *
         * @return string The core module name derived from the metadata folder.
         */
        public static function get_core_module_name_from_metadata_folder(string $metadata_folder): string
        {
        }
        /**
         * Process conversion outline.
         *
         * @since ??
         *
         * @param array  $metadata                The metadata of the module.
         * @param string $conversion_outline_file The path to the conversion outline file.
         *
         * @return bool True if the conversion outline is processed successfully, false otherwise.
         */
        public static function process_conversion_outline(array $metadata, ?string $conversion_outline_file = null): bool
        {
        }
        /**
         * Registers a module with the given metadata folder and arguments.
         *
         * This method reads the metadata `module.json` file from the specified folder, decodes it,
         * and merges the metadata with the default arguments. It then registers the block type
         * using the merged arguments and returns the registered block type.
         *
         * @since          ??
         *
         * @param string $metadata_folder The path to the metadata folder.
         * @param array  $args             Optional. An array of arguments to merge with the default arguments.
         *                                 Default `[]`.
         *                                 Accepts any public property of `WP_Block_Type`. See
         *                                 `WP_Block_Type::__construct()` for more information on accepted arguments.
         *
         * @return WP_Block_Type|null The registered block type or `null` if the metadata file does not exist or cannot be
         *                            decoded.
         *
         * @throws \Exception If the metadata file cannot be read or decoded.
         * @example        :
         *                 ```php
         *                 ModuleRegistration::register_module(
         *                 '/path/to/metadata/folder',
         *                 [
         *                 'title' => 'Custom Title',
         *                 'attributes' => [
         *                 'attr1' => 'value1',
         *                 'attr2' => 'value2',
         *                 ],
         *                 ]
         *                 );
         *                 ```
         * @example        :
         *                 ```php
         *                 ModuleRegistration::register_module( '/path/to/metadata/folder' );
         *                 ```
         */
        public static function register_module(string $metadata_folder, array $args = []): ?\WP_Block_Type
        {
        }
        /**
         * Registers a block type from the metadata stored in the `block.json` file.
         *
         * @param string $block_type    Block type name including namespace prefix.
         * @param string $metadata_file Path to the block metadata file.
         * @param array  $metadata      Block type metadata.
         * @return WP_Block_Type|false The registered block type on success, or false on failure.
         */
        public static function register_block_type_from_metadata($block_type, $metadata_file, $metadata = array())
        {
        }
        /**
         * Retrieve the default attributes of a registered block module.
         *
         * This function retrieves the default attributes of a registered block module based on the provided module name.
         * It checks if the default attributes are already cached to optimize performance and returns the cached attributes if available.
         * It check if default attributes definition file exists in the module folder. If it exists, it retrieves the default attributes from the file.
         * If the default attributes are not cached, it retrieves the registered module using the `WP_Block_Type_Registry` class.
         * If the registered module is found, it retrieves the attributes of the module and extracts the default values into an array.
         *
         * @since ??
         *
         * @param string     $module_name The name of the module.
         * @param string     $default_property_name Optional. The name of the default property to use. It can be either `'default'` or `'defaultPrintedStyle'`. Default `'default'`.
         * @param array|null $metadata Optional. The metadata of the module. Default `null`.
         *
         * @return array An array of default attributes for the module.
         */
        public static function get_default_attrs(string $module_name, string $default_property_name = 'default', $metadata = null): array
        {
        }
        /**
         * Get the default attributes for a module.
         *
         * This function returns the default attributes for the module with the provided module name and default property name.
         * The attributes are  defined and retrieved from the module's `module.json` file.
         *
         * @since ??
         *
         * @param string     $module_name           The name of the module to retrieve the default attributes for.
         * @param string     $default_property_name Optional. The name of the default property to use. It can be either `'default'` or `'defaultPrintedStyle'`. Default `'default'`.
         * @param array|null $metadata              Optional. The metadata of the module. Default `null`.
         *
         * @return array The default attributes for the module.
         *
         * @example:
         * ```php
         * // Retrieve the default attributes for a module called 'my_module'.
         * $default_attrs = ModuleRegistration::generate_default_attrs( 'my_module' );
         *
         * // Retrieve the default attributes for a module called 'another_module' using a custom default property called 'custom'.
         * $default_attrs = ModuleRegistration::generate_default_attrs( 'another_module', 'custom' );
         * ```
         */
        public static function generate_default_attrs(string $module_name, string $default_property_name = 'default', $metadata = null): array
        {
        }
        /**
         * Retrieve module selectors.
         *
         * Get the selectors associated with the attributes of a registered block that is defined in the module.json file.
         *
         * @since ??
         *
         * @param string $module_name The name of the module for which to retrieve the selectors.
         *
         * @return array An array of selectors where the key is the module attribute name and the value is the selector.
         *
         * @example:
         * ```php
         *     $selectors = ModuleRegistration::get_selectors( 'module_name' );
         *     // Returns an array of selectors for the specified module.
         *     // Example: ['attribute_name' => '.selector']
         * ```
         */
        public static function get_selectors(string $module_name): array
        {
        }
        /**
         * Check if a module is a child module.
         *
         * @since ??
         *
         * @param string $module_name The name of the module to check.
         *
         * @return bool True if the module is a child module, false otherwise.
         */
        public static function is_child_module($module_name)
        {
        }
        /**
         * Check if a module is nestable.
         *
         * @since ??
         *
         * @param string $module_name The name of the module to check.
         *
         * @return bool True if the module is nestable, false otherwise.
         */
        public static function is_nestable($module_name)
        {
        }
        /**
         * Retrieves the settings for a specified module.
         *
         * This function attempts to get the module settings from the registered block types.
         * If the module is not registered, it falls back to retrieving metadata from a PHP file
         * to improve performance for core modules.
         *
         * @since ??
         *
         * @param string $module_name The name of the module to retrieve settings for.
         * @return WP_Block_Type|null The module settings if found, or null if the module is not registered.
         */
        public static function get_module_settings($module_name): ?\WP_Block_Type
        {
        }
        /**
         * Retrieves all module metadata from the generated metadata file.
         *
         * This method implements lazy loading for the module metadata array. On first call,
         * it loads the metadata from the automatically generated `_all_modules_metadata.php`
         * file and caches it in the static property. Subsequent calls return the cached data.
         *
         * The metadata file contains comprehensive information about all available Divi 5
         * modules including their names, titles, categories, icons, child modules, and
         * other configuration data required for module registration and rendering.
         *
         * @since ??
         *
         * @return array Associative array containing metadata for all available modules.
         *
         * @example
         * ```php
         * $metadata = ModuleRegistration::get_all_core_modules_metadata();
         * $accordion_data = $metadata['accordion'];
         * echo $accordion_data['title']; // Outputs: "Accordion"
         * ```
         */
        public static function get_all_core_modules_metadata(): array
        {
        }
        /**
         * Retrieves the core module metadata folder path for a specific module.
         *
         * This method builds a cache of metadata folder paths by iterating through all
         * module metadata and checking if the corresponding folder exists in the file system.
         * The cache is stored in the static property $_all_core_metadata_folders for performance.
         *
         * The metadata folder path is constructed as:
         * ET_BUILDER_5_DIR/visual-builder/packages/module-library/src/components/{metadata_key}
         *
         * @since ??
         *
         * @param string $module_name The name of the module to get the metadata folder for.
         *
         * @return string|null The metadata folder path if found, null otherwise.
         */
        public static function get_core_metadata_folder(string $module_name): ?string
        {
        }
        /**
         * Retrieves the metadata for a specific core module.
         *
         * This method implements lazy loading for individual module metadata. On first call,
         * it loads the complete metadata from the automatically generated `_all_modules_metadata.php`
         * file and caches it in the static property. Subsequent calls return the cached data.
         * The method maps the module name to its relative directory path and returns the
         * corresponding metadata array for that module.
         *
         * @since ??
         *
         * @param string $module_name The name of the module (e.g., 'divi/accordion', 'divi/button').
         *
         * @return array The metadata array for the specified module, or an empty array if the module
         *               is not found or the module name cannot be mapped to a relative directory.
         *
         * @example
         * ```php
         * // Get metadata for accordion module.
         * $metadata = ModuleRegistration::get_core_module_metadata( 'divi/accordion' );
         * if ( ! empty( $metadata ) ) {
         *     echo $metadata['title']; // Outputs: "Accordion"
         *     echo $metadata['description']; // Outputs module description
         * }
         *
         * // Get metadata for button module.
         * $button_metadata = ModuleRegistration::get_core_module_metadata( 'divi/button' );
         * ```
         */
        public static function get_core_module_metadata(string $module_name): array
        {
        }
        /**
         * Retrieves the module conversion outline for a specific Divi module.
         *
         * This method implements lazy loading and caching for module conversion outlines. On first call,
         * it loads the conversion outline data from either the core modules conversion outline file
         * or from an individual module conversion outline JSON file. Subsequent calls return the cached data.
         *
         * @since ??
         *
         * @param string      $module_name                  The name of the module to get conversion outline for.
         * @param string|null $conversion_outline_json_file Optional path to individual module conversion outline JSON file.
         *                                                  Used when the module is not found in core modules.
         *
         * @return array The module conversion outline array containing field mappings and transformation rules.
         *               Returns empty array if no conversion outline is found.
         */
        public static function get_module_conversion_outline(string $module_name, ?string $conversion_outline_json_file = null): ?array
        {
        }
        /**
         * Retrieves the default printed style attributes for a core module.
         *
         * This method implements lazy loading for the module default printed style attributes data. On first call,
         * it loads the attributes from the automatically generated `_all_modules_default_printed_style_attributes.php`
         * file and caches it in the static property. Subsequent calls return the cached data.
         *
         * Default printed style attributes define the CSS styles that are automatically applied to modules
         * when they are rendered on the frontend. These attributes ensure consistent styling across all
         * instances of a module type and provide the foundation for user customization.
         *
         * @since ??
         *
         * @param string $module_name The name of the module to retrieve the default printed style attributes for.
         *
         * @return array|null The default printed style attributes array for the specified module, or null if not found.
         *
         * @example
         * ```php
         * $style_attrs = ModuleRegistration::get_core_module_default_printed_style_attributes( 'divi/button' );
         * // Returns default CSS styles for button module
         * ```
         */
        public static function get_core_module_default_printed_style_attributes(string $module_name): ?array
        {
        }
        /**
         * Retrieves the default render attributes for a core module.
         *
         * This method implements lazy loading for the module default render attributes data. On first call,
         * it loads the attributes from the automatically generated `_all_modules_default_render_attributes.php`
         * file and caches it in the static property. Subsequent calls return the cached data.
         *
         * Default render attributes define the initial values and configuration for module attributes
         * when they are first rendered. These attributes serve as the baseline for module behavior
         * and can be overridden by user-defined values or preset configurations.
         *
         * @since ??
         *
         * @param string $module_name The name of the module to retrieve the default render attributes for.
         *
         * @return array|null The default render attributes array for the specified module, or null if not found.
         *
         * @example
         * ```php
         * $render_attrs = ModuleRegistration::get_core_module_default_render_attributes( 'divi/text' );
         * // Returns default attribute values for text module rendering
         * ```
         */
        public static function get_core_module_default_render_attributes(string $module_name): ?array
        {
        }
    }
}
namespace ET\Builder\Packages\IconLibrary\IconFont {
    /**
     * Utils class.
     *
     * This class contains methods to work with icon font(s).
     *
     * This class is equivalent of JS package:
     * {@link /docs/category/icon-library @divi/icon-library}
     *
     * @since ??
     */
    class Utils {
        /**
         * Get required icon font from the list of icons.
         *
         * Check the provided icons list and return the icon that match the provided icon attribute value.
         * If the provided icon attribute value does not match any icon in the list, return `null`.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-icon-library/functions/findIconInList findIconInList}
         * in `@divi/icon-library` package.
         *
         * @since ??
         *
         * @param array $icon_list {
         *   A list of icons.
         *
         *   @type array $key {
         *     @type string $unicode         The unicode representation of the icon symbol.
         *     @type string $fontWeight      The font weight of the font icon.
         *     @type array  $styles          The font styles of the font icon.
         *     @type string $decodedUnicode  The decoded unicode representation of the icon symbol.
         *   }
         * }
         * @param array $icon {
         *     Icon attribute value.
         *
         *     @type string $unicode The unicode representation of the icon symbol.
         *     @type string $type    The font type.
         *     @type string $weight  The font weight of the font icon.
         * }
         *
         * @return array The icon that match the provided icon attribute value.
         *               If the provided icon attribute value does not match any icon in the list, return `null`.
         */
        public static function find_icon_in_list( array $icon_list, array $icon ): ?array
        {
        }

        /**
         * Check if the given icon font is `FontAwesome` icon font.
         *
         * The font icon is considered to be `FontAwesome` if the icon's type attribute value is `fa`.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-icon-library/functions/isFaIcon isFaIcon}
         * in `@divi/icon-library` package.
         *
         * @since ??
         *
         * @param array|null $icon {
         *     Icon attribute value.
         *
         *     @type string $type The font type.
         * }
         *
         * @return bool
         */
        public static function is_fa_icon( ?array $icon ): bool
        {
        }

        /**
         * Process font icon.
         *
         * Process the font icon and return the decoded unicode.
         *
         * This function is equivalent of JS function:
         * {@link /docs/builder-api/js-beta/divi-icon-library/functions/processFontIcon processFontIcon}
         * in `@divi/icon-library` package.
         *
         * @since ??
         *
         * @param array|string $icon {
         *     Icon attribute value.
         *
         *     @type string $unicode The unicode representation of the icon symbol.
         *     @type string $type The font type.
         *     @type string $weight  The font weight of the font icon.
         * }
         * @param bool         $is_font_icons_down Optional. Whether the icon is a font icon from the `downIcons` list.
         *                                                   Default `false`.
         * @param bool         $is_unicode         Optional. Whether the icon is a unicode representation of the icon symbol.
         *                                                   Default `false`.
         *
         * @throws \Exception Throw error when the icon JSON file is not exist.
         *
         * @return string The decoded unicode representation of the icon symbol.
         *                If the icon JSON file is not exist, `null` is returned.
         */
        public static function process_font_icon( $icon, bool $is_font_icons_down = false, bool $is_unicode = false ): ?string
        {
        }

        /**
         * Escape decoded font icon.
         *
         * This function is equivalent of JS function processFontIcon located in:
         * visual-builder/packages/icon-library/src/components/icon-font/utils/escape-font-icon/index.ts.
         *
         * @since ??
         *
         * @param string $icon Decoded unicode Icon value.
         *
         * @return string|null
         * @throws \Exception Throw error when the icon json file is not exist.
         */
        public static function escape_font_icon( $icon = '' )
        {
        }
    }
}
namespace ET\Builder\VisualBuilder\Assets {
    /**
     * AssetsUtility class.
     *
     * This class provides utility methods for handling assets such as scripts, styles, and preferences data for packages,
     * with functionality related to asset enqueueing, data retrieval, and injection.
     *
     * @since ??
     */
    class AssetsUtility
    {
        /**
         * Validates the dependencies of enqueued scripts.
         *
         * This function iterates through all scripts enqueued via wp_enqueue_script and checks if their dependencies
         * are registered.
         *
         * By default, WordPress lacks a validation mechanism for script dependencies. This leads to a silent failure
         * and the script is not enqueued if any of its dependencies are missing. Given our extensive use of scripts
         * a missing dependency can lead us down to the rabbit hole.
         *
         * @since ??
         *
         * @return void
         */
        public static function validate_enqueue_script_dependencies(): void
        {
        }
        /**
         * Enqueue visual builder's core dependencies, which are built by WebPack as externals e.g. react, wp-data, wp-blocks.
         * Package version enqueued here has to match with the version on visual builder's package.json.
         * See: `/visual-builder/yarn.config.cjs`
         *
         * @since ??
         *
         * @return void
         *
         * @example:
         * ```php
         *   enqueue_visual_builder_dependencies();
         * ```
         */
        public static function enqueue_visual_builder_dependencies(): void
        {
        }
        /**
         * Conditionally enqueues a JavaScript file if it exists in the filesystem.
         *
         * Note: We don't ship dev dependencies, this method is used so when `ET_DEBUG` is set to `true` on customer's website, enqueuing won't fail.
         *
         * @param string   $handle        Unique identifier for the script. This handle is used to register the script.
         * @param string   $relative_path Relative path to the JavaScript file from the '/includes/builder-5' directory in the theme. It should not contain .js suffix.
         * @param string[] $dependencies  Optional. An array of registered script handles that this script depends on. Default is an empty array.
         * @param string   $version       Optional. The script version number for cache busting. Default is '1.0.0'.
         * @param bool     $in_footer     Optional. Whether to enqueue the script in the footer. Default is true.
         *
         * @return void
         */
        public static function enqueue_dev_or_prod_script($handle, $relative_path, $dependencies = [], $version = '1.0.0', $in_footer = true)
        {
        }
        /**
         * Retrieves the settings data for the Visual Builder.
         *
         * The settings data includes various information required for the visual builder to function properly,
         * such as post ID, post content, post type, post status, layout type, current URL, fonts, Google API settings,
         * Divi Taxonomies, GMT offset, sidebar values, raw post content, TinyMCE plugins, and more.
         *
         * This function runs the value through `divi_visual_builder_settings_data` filter.
         *
         * NOTE: The returned value is equivalent to data attached over window.ETBuilderBackend in D4 which
         * is equivalent of the returned array values of these three functions merged:
         * - et_fb_get_static_backend_helpers( $post_type )
         * - et_fb_get_dynamic_backend_helpers()
         * - et_fb_get_builder_shortcode_object( $post_type, $post_id, $layout_type )
         *
         * In D5, the returned value is organized to be more consistent.
         *
         * @since ??
         *
         * @return array The settings data for the Visual Builder.
         */
        public static function get_settings_data(): array
        {
        }
        /**
         * Inject the preboot script for the Divi theme.
         *
         * This function injects a preboot script adapted from preboot.js to make the window variable available for the Divi theme.
         * The preboot.js file cannot be enqueued directly because it contains an override mechanism that is used for "moving assets
         * from top to app window" approach.
         *
         * Ideally, this is used in `wp_head`.
         *
         * @since ??
         *
         * @return void
         *
         * @example:
         * ```php
         *   AssetsUtility::inject_preboot_script();
         * ```
         */
        public static function inject_preboot_script(): void
        {
        }
        /**
         * Inject preboot style: Style that needs to be printed so early enqueueing as external .css
         * would be too late for it. This is presumable used at `wp_head`.
         *
         * @since ??
         */
        /**
         * Injects preboot style to hide Divi's heading and footer on visual builder load.
         *
         * These styles are used to hide Divi's heading and footer on visual builder load so preloader
         * elements will appear without distraction. This is needed at both top window only since header
         * and footer are expected to appear on app window. The paradox is to make this work this needs
         * at both top and app window on very limited time because as long as the app hasn't been rendered,
         * the header and footer are better hidden. `.et-vb-app-ancestor` is added by Visual Builder app
         * so the following in app window translates into "hide header and footer until app is rendered
         * which is indicated by existence of `.et-vb-app-ancestor` classname".
         *
         * Ideally, this is used in `wp_head`.
         *
         * @since ??
         *
         * @return void
         *
         * @example
         * ```php
         *   // Injects the preboot style
         *   AssetsUtility::inject_preboot_style();
         * ```
         */
        public static function inject_preboot_style(): void
        {
        }
        /**
         * Dequeue queued scripts and styles that is not needed on top window which are registered early on `wp_enqueue_scripts`.
         *
         * @since ??
         */
        public static function dequeue_top_window_early_scripts(): void
        {
        }
        /**
         * Dequeue queued scripts and styles that is not needed on top window which are registered late on `wp_footer`.
         *
         * @since ??
         */
        public static function dequeue_top_window_late_scripts(): void
        {
        }
    }
    /**
     * Class for handling visual builder's package build.
     *
     * Divi 5's visual builder is organized as monorepo. This means instead of one big bundle file, visual builder
     * is organized as multiple packages and being orchestrated together. These package are bundled into mainly a
     * javascript style, PHP array file containing build version and dependency, and sometimes, static css style.
     * This package's build output is what is being referred as `PackageBuild`.
     *
     * This class is responsible for handling PackageBuild as a single PackageBuild entity.
     *
     * @since ??
     */
    class PackageBuild
    {
        /**
         * Package build's name.
         *
         * @var string
         */
        public $name;
        /**
         * Package build's version.
         *
         * @var string
         */
        public $version;
        /**
         * Package build's script settings.
         *
         * @var array
         */
        public $script;
        /**
         * Package build's style settings.
         *
         * @var array
         */
        public $style;
        /**
         * Package build's constructor.
         *
         * @since ??
         *
         * @param array $params Package build's constructor params.
         */
        public function __construct($params)
        {
        }
        /**
         * Set package build's properties.
         *
         * @since ??
         *
         * @param array $params Package build's params.
         */
        public function set_properties($params)
        {
        }
        /**
         * Get default properties.
         *
         * @since ??
         *
         * @return array
         */
        public function get_default_properties()
        {
        }
        /**
         * Get package build's properties.
         *
         * @since ??
         *
         * @return array
         */
        public function get_properties()
        {
        }
    }
    /**
     * Extended class of PackageBuild specifically for handling Divi 5's package build.
     *
     * PackageBuild is intentionally made to be generic so it can be used by third party a well.
     * Significant aspect of Divi 5's package build is consistent so they can be inferred based
     * on convetion used for organizing Divi's code so this class is specifically made for Divi 5's
     * package build.
     *
     * @since ??
     */
    class DiviPackageBuild extends \ET\Builder\VisualBuilder\Assets\PackageBuild
    {
        /**
         * Package build's name.
         *
         * @var string
         */
        public $name;
        /**
         * Build dependencies that are inferred from package build's *.assets.php that is generated on build process.
         *
         * @var []
         */
        public $build_dependencies;
        /**
         * Build version that is inferred from package build's *.assets.php that is generated  on build process.
         *
         * @var string
         */
        public $build_version;
        /**
         * Whether this divi package build has asset file or not.
         *
         * The asset file is a dependency file generated by webpack during VB's build process, using the `@wordpress/dependency-extraction-webpack-plugin`.
         *
         * @var boolean
         */
        public $has_asset = false;
        /**
         * Package build's constructor.
         *
         * @since ??
         *
         * @param array $params Package build's constructor params.
         */
        public function __construct($params)
        {
        }
        /**
         * Parse and set build properties based on package build's asset file that is generated from build process.
         *
         * @since ??
         *
         * @return boolean
         */
        public function set_build_properties()
        {
        }
        /**
         * Generate package build's properties.
         *
         * @since ??
         *
         * @param array $script Scripts that are used by package build.
         * @param array $style Styles that are used by package build.
         */
        public function generate_properties($script = [], $style = [])
        {
        }
    }
    /**
     * Class for handling package builds: registering and populating it to be
     * enqueued in top or app window of the visual builder.
     *
     * @since ??
     */
    class PackageBuildManager implements \ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface
    {
        /**
         * Method that is automatically loaded by class which implements `DependencyInterface`
         *
         * @since ??
         */
        public function load()
        {
        }
        /**
         * Register divi package builds.
         *
         * @since ??
         */
        public static function register_divi_package_builds()
        {
        }
        /**
         * Register divi package build.
         *
         * @since ??
         *
         * @param array $params Package build's params.
         */
        public static function register_package_build($params)
        {
        }
        /**
         * Register divi package build.
         *
         * @since ??
         *
         * @param array $params Package build's params.
         */
        public static function register_divi_package_build($params)
        {
        }
        /**
         * Register package build item.
         *
         * @since ??
         *
         * @param array $properties package build's properties.
         */
        public static function register($properties)
        {
        }
        /**
         * Enqueue styles.
         *
         * @since ??
         */
        public static function enqueue_styles()
        {
        }
        /**
         * Enqueue scripts.
         *
         * @since ??
         */
        public static function enqueue_scripts()
        {
        }
        /**
         * Load specific stylesheets asynchronously by swapping the media attribute on load. This for stylesheets that not required to be loaded immediately.
         *
         * @since ??
         *
         * @param string $html HTML to replace.
         * @param string $handle Stylesheet handle.
         * @return string $html replacement html.
         */
        public static function defer_styles($html, $handle)
        {
        }
    }
}
namespace {
    function et_builder_d5_enabled()
    {
    }
    function et_core_is_fb_enabled()
    {
    }
}
