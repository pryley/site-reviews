<?php
/**
 * Generated stub declarations for WooCommerce.
 * @see https://woocommerce.com
 * @see https://github.com/php-stubs/woocommerce-stubs
 */

namespace {
    /**
     * Abstract WC Data Class
     *
     * Implemented by classes using the same CRUD(s) pattern.
     *
     * @version  2.6.0
     * @package  WooCommerce\Abstracts
     */
    abstract class WC_Data
    {
        /**
         * ID for this object.
         *
         * @since 3.0.0
         * @var int
         */
        protected $id = 0;
        /**
         * Core data for this object. Name value pairs (name + default value).
         *
         * @since 3.0.0
         * @var array
         */
        protected $data = array();
        /**
         * Core data changes for this object.
         *
         * @since 3.0.0
         * @var array
         */
        protected $changes = array();
        /**
         * This is false until the object is read from the DB.
         *
         * @since 3.0.0
         * @var bool
         */
        protected $object_read = \false;
        /**
         * This is the name of this object type.
         *
         * @since 3.0.0
         * @var string
         */
        protected $object_type = 'data';
        /**
         * Extra data for this object. Name value pairs (name + default value).
         * Used as a standard way for sub classes (like product types) to add
         * additional information to an inherited class.
         *
         * @since 3.0.0
         * @var array
         */
        protected $extra_data = array();
        /**
         * Set to _data on construct so we can track and reset data if needed.
         *
         * @since 3.0.0
         * @var array
         */
        protected $default_data = array();
        /**
         * Contains a reference to the data store for this class.
         *
         * @since 3.0.0
         * @var object
         */
        protected $data_store;
        /**
         * Stores meta in cache for future reads.
         * A group must be set to to enable caching.
         *
         * @since 3.0.0
         * @var string
         */
        protected $cache_group = '';
        /**
         * Stores additional meta data.
         *
         * @since 3.0.0
         * @var array
         */
        protected $meta_data = \null;
        /**
         * List of properties that were earlier managed by data store. However, since DataStore is a not a stored entity in itself, they used to store data in metadata of the data object.
         * With custom tables, some of these are moved from metadata to their own columns, but existing code will still try to add them to metadata. This array is used to keep track of such properties.
         *
         * Only reason to add a property here is that you are moving properties from DataStore instance to data object. If you are adding a new property, consider adding it to to $data array instead.
         *
         * @var array
         */
        protected $legacy_datastore_props = array();
        /**
         * Default constructor.
         *
         * @param int|object|array $read ID to load from the DB (optional) or already queried data.
         */
        public function __construct($read = 0)
        {
        }
        /**
         * Only store the object ID to avoid serializing the data object instance.
         *
         * @return array
         */
        public function __sleep()
        {
        }
        /**
         * Re-run the constructor with the object ID.
         *
         * If the object no longer exists, remove the ID.
         */
        public function __wakeup()
        {
        }
        /**
         * When the object is cloned, make sure meta is duplicated correctly.
         *
         * @since 3.0.2
         */
        public function __clone()
        {
        }
        /**
         * Get the data store.
         *
         * @since  3.0.0
         * @return object
         */
        public function get_data_store()
        {
        }
        /**
         * Returns the unique ID for this object.
         *
         * @since  2.6.0
         * @return int
         */
        public function get_id()
        {
        }
        /**
         * Delete an object, set the ID to 0, and return result.
         *
         * @since  2.6.0
         * @param  bool $force_delete Should the date be deleted permanently.
         * @return bool result
         */
        public function delete($force_delete = \false)
        {
        }
        /**
         * Save should create or update based on object existence.
         *
         * @since  2.6.0
         * @return int
         */
        public function save()
        {
        }
        /**
         * Change data to JSON format.
         *
         * @since  2.6.0
         * @return string Data in JSON format.
         */
        public function __toString()
        {
        }
        /**
         * Returns all data for this object.
         *
         * @since  2.6.0
         * @return array
         */
        public function get_data()
        {
        }
        /**
         * Returns array of expected data keys for this object.
         *
         * @since   3.0.0
         * @return array
         */
        public function get_data_keys()
        {
        }
        /**
         * Returns all "extra" data keys for an object (for sub objects like product types).
         *
         * @since  3.0.0
         * @return array
         */
        public function get_extra_data_keys()
        {
        }
        /**
         * Filter null meta values from array.
         *
         * @since  3.0.0
         * @param mixed $meta Meta value to check.
         * @return bool
         */
        protected function filter_null_meta($meta)
        {
        }
        /**
         * Get All Meta Data.
         *
         * @since 2.6.0
         * @return array of objects.
         */
        public function get_meta_data()
        {
        }
        /**
         * Check if the key is an internal one.
         *
         * @since  3.2.0
         * @param  string $key Key to check.
         * @return bool   true if it's an internal key, false otherwise
         */
        protected function is_internal_meta_key($key)
        {
        }
        /**
         * Get Meta Data by Key.
         *
         * @since  2.6.0
         * @param  string $key Meta Key.
         * @param  bool   $single return first found meta with key, or all with $key.
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return mixed
         */
        public function get_meta($key = '', $single = \true, $context = 'view')
        {
        }
        /**
         * See if meta data exists, since get_meta always returns a '' or array().
         *
         * @since  3.0.0
         * @param  string $key Meta Key.
         * @return boolean
         */
        public function meta_exists($key = '')
        {
        }
        /**
         * Set all meta data from array.
         *
         * @since 2.6.0
         * @param array $data Key/Value pairs.
         */
        public function set_meta_data($data)
        {
        }
        /**
         * Add meta data.
         *
         * @since 2.6.0
         *
         * @param string       $key Meta key.
         * @param string|array $value Meta value.
         * @param bool         $unique Should this be a unique key?.
         */
        public function add_meta_data($key, $value, $unique = \false)
        {
        }
        /**
         * Update meta data by key or ID, if provided.
         *
         * @since  2.6.0
         *
         * @param  string       $key Meta key.
         * @param  string|array $value Meta value.
         * @param  int          $meta_id Meta ID.
         */
        public function update_meta_data($key, $value, $meta_id = 0)
        {
        }
        /**
         * Delete meta data.
         *
         * @since 2.6.0
         * @param string $key Meta key.
         */
        public function delete_meta_data($key)
        {
        }
        /**
         * Delete meta data.
         *
         * @since 2.6.0
         * @param int $mid Meta ID.
         */
        public function delete_meta_data_by_mid($mid)
        {
        }
        /**
         * Read meta data if null.
         *
         * @since 3.0.0
         */
        protected function maybe_read_meta_data()
        {
        }
        /**
         * Helper method to compute meta cache key. Different from WP Meta cache key in that meta data cached using this key also contains meta_id column.
         *
         * @since 4.7.0
         *
         * @return string
         */
        public function get_meta_cache_key()
        {
        }
        /**
         * Generate cache key from id and group.
         *
         * @since 4.7.0
         *
         * @param int|string $id          Object ID.
         * @param string     $cache_group Group name use to store cache. Whole group cache can be invalidated in one go.
         *
         * @return string Meta cache key.
         */
        public static function generate_meta_cache_key($id, $cache_group)
        {
        }
        /**
         * Prime caches for raw meta data. This includes meta_id column as well, which is not included by default in WP meta data.
         *
         * @since 4.7.0
         *
         * @param array  $raw_meta_data_collection Array of objects of { object_id => array( meta_row_1, meta_row_2, ... }.
         * @param string $cache_group              Name of cache group.
         */
        public static function prime_raw_meta_data_cache($raw_meta_data_collection, $cache_group)
        {
        }
        /**
         * Read Meta Data from the database. Ignore any internal properties.
         * Uses it's own caches because get_metadata does not provide meta_ids.
         *
         * @since 2.6.0
         * @param bool $force_read True to force a new DB read (and update cache).
         */
        public function read_meta_data($force_read = \false)
        {
        }
        /**
         * Helper function to initialize metadata entries from filtered raw meta data.
         *
         * @param array $filtered_meta_data Filtered metadata fetched from DB.
         */
        public function init_meta_data(array $filtered_meta_data = array())
        {
        }
        /**
         * Update Meta Data in the database.
         *
         * @since 2.6.0
         */
        public function save_meta_data()
        {
        }
        /**
         * Set ID.
         *
         * @since 3.0.0
         * @param int $id ID.
         */
        public function set_id($id)
        {
        }
        /**
         * Set all props to default values.
         *
         * @since 3.0.0
         */
        public function set_defaults()
        {
        }
        /**
         * Set object read property.
         *
         * @since 3.0.0
         * @param boolean $read Should read?.
         */
        public function set_object_read($read = \true)
        {
        }
        /**
         * Get object read property.
         *
         * @since  3.0.0
         * @return boolean
         */
        public function get_object_read()
        {
        }
        /**
         * Set a collection of props in one go, collect any errors, and return the result.
         * Only sets using public methods.
         *
         * @since  3.0.0
         *
         * @param array  $props Key value pairs to set. Key is the prop and should map to a setter function name.
         * @param string $context In what context to run this.
         *
         * @return bool|WP_Error
         */
        public function set_props($props, $context = 'set')
        {
        }
        /**
         * Sets a prop for a setter method.
         *
         * This stores changes in a special array so we can track what needs saving
         * the the DB later.
         *
         * @since 3.0.0
         * @param string $prop Name of prop to set.
         * @param mixed  $value Value of the prop.
         */
        protected function set_prop($prop, $value)
        {
        }
        /**
         * Return data changes only.
         *
         * @since 3.0.0
         * @return array
         */
        public function get_changes()
        {
        }
        /**
         * Merge changes with data and clear.
         *
         * @since 3.0.0
         */
        public function apply_changes()
        {
        }
        /**
         * Prefix for action and filter hooks on data.
         *
         * @since  3.0.0
         * @return string
         */
        protected function get_hook_prefix()
        {
        }
        /**
         * Gets a prop for a getter method.
         *
         * Gets the value from either current pending changes, or the data itself.
         * Context controls what happens to the value before it's returned.
         *
         * @since  3.0.0
         * @param  string $prop Name of prop to get.
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return mixed
         */
        protected function get_prop($prop, $context = 'view')
        {
        }
        /**
         * Sets a date prop whilst handling formatting and datetime objects.
         *
         * @since 3.0.0
         * @param string         $prop Name of prop to set.
         * @param string|integer $value Value of the prop.
         */
        protected function set_date_prop($prop, $value)
        {
        }
        /**
         * When invalid data is found, throw an exception unless reading from the DB.
         *
         * @throws WC_Data_Exception Data Exception.
         * @since 3.0.0
         * @param string $code             Error code.
         * @param string $message          Error message.
         * @param int    $http_status_code HTTP status code.
         * @param array  $data             Extra error data.
         */
        protected function error($code, $message, $http_status_code = 400, $data = array())
        {
        }
    }
    /**
     * WC_Settings_API class.
     */
    abstract class WC_Settings_API
    {
        /**
         * The plugin ID. Used for option names.
         *
         * @var string
         */
        public $plugin_id = 'woocommerce_';
        /**
         * ID of the class extending the settings API. Used in option names.
         *
         * @var string
         */
        public $id = '';
        /**
         * Validation errors.
         *
         * @var array of strings
         */
        public $errors = array();
        /**
         * Setting values.
         *
         * @var array
         */
        public $settings = array();
        /**
         * Form option fields.
         *
         * @var array
         */
        public $form_fields = array();
        /**
         * The posted settings data. When empty, $_POST data will be used.
         *
         * @var array
         */
        protected $data = array();
        /**
         * Get the form fields after they are initialized.
         *
         * @return array of options
         */
        public function get_form_fields()
        {
        }
        /**
         * Set default required properties for each field.
         *
         * @param array $field Setting field array.
         * @return array
         */
        protected function set_defaults($field)
        {
        }
        /**
         * Output the admin options table.
         */
        public function admin_options()
        {
        }
        /**
         * Initialise settings form fields.
         *
         * Add an array of fields to be displayed on the gateway's settings screen.
         *
         * @since  1.0.0
         */
        public function init_form_fields()
        {
        }
        /**
         * Return the name of the option in the WP DB.
         *
         * @since 2.6.0
         * @return string
         */
        public function get_option_key()
        {
        }
        /**
         * Get a fields type. Defaults to "text" if not set.
         *
         * @param  array $field Field key.
         * @return string
         */
        public function get_field_type($field)
        {
        }
        /**
         * Get a fields default value. Defaults to "" if not set.
         *
         * @param  array $field Field key.
         * @return string
         */
        public function get_field_default($field)
        {
        }
        /**
         * Get a field's posted and validated value.
         *
         * @param string $key Field key.
         * @param array  $field Field array.
         * @param array  $post_data Posted data.
         * @return string
         */
        public function get_field_value($key, $field, $post_data = array())
        {
        }
        /**
         * Sets the POSTed data. This method can be used to set specific data, instead of taking it from the $_POST array.
         *
         * @param array $data Posted data.
         */
        public function set_post_data($data = array())
        {
        }
        /**
         * Returns the POSTed data, to be used to save the settings.
         *
         * @return array
         */
        public function get_post_data()
        {
        }
        /**
         * Update a single option.
         *
         * @since 3.4.0
         * @param string $key Option key.
         * @param mixed  $value Value to set.
         * @return bool was anything saved?
         */
        public function update_option($key, $value = '')
        {
        }
        /**
         * Processes and saves options.
         * If there is an error thrown, will continue to save and validate fields, but will leave the erroring field out.
         *
         * @return bool was anything saved?
         */
        public function process_admin_options()
        {
        }
        /**
         * Add an error message for display in admin on save.
         *
         * @param string $error Error message.
         */
        public function add_error($error)
        {
        }
        /**
         * Get admin error messages.
         */
        public function get_errors()
        {
        }
        /**
         * Display admin error messages.
         */
        public function display_errors()
        {
        }
        /**
         * Initialise Settings.
         *
         * Store all settings in a single database entry
         * and make sure the $settings array is either the default
         * or the settings stored in the database.
         *
         * @since 1.0.0
         * @uses get_option(), add_option()
         */
        public function init_settings()
        {
        }
        /**
         * Get option from DB.
         *
         * Gets an option from the settings API, using defaults if necessary to prevent undefined notices.
         *
         * @param  string $key Option key.
         * @param  mixed  $empty_value Value when empty.
         * @return string The value specified for the option or a default value for the option.
         */
        public function get_option($key, $empty_value = \null)
        {
        }
        /**
         * Prefix key for settings.
         *
         * @param  string $key Field key.
         * @return string
         */
        public function get_field_key($key)
        {
        }
        /**
         * Generate Settings HTML.
         *
         * Generate the HTML for the fields on the "settings" screen.
         *
         * @param array $form_fields (default: array()) Array of form fields.
         * @param bool  $echo Echo or return.
         * @return string the html for the settings
         * @since  1.0.0
         * @uses   method_exists()
         */
        public function generate_settings_html($form_fields = array(), $echo = \true)
        {
        }
        /**
         * Get HTML for tooltips.
         *
         * @param  array $data Data for the tooltip.
         * @return string
         */
        public function get_tooltip_html($data)
        {
        }
        /**
         * Get HTML for descriptions.
         *
         * @param  array $data Data for the description.
         * @return string
         */
        public function get_description_html($data)
        {
        }
        /**
         * Get custom attributes.
         *
         * @param  array $data Field data.
         * @return string
         */
        public function get_custom_attribute_html($data)
        {
        }
        /**
         * Generate Text Input HTML.
         *
         * @param string $key Field key.
         * @param array  $data Field data.
         * @since  1.0.0
         * @return string
         */
        public function generate_text_html($key, $data)
        {
        }
        /**
         * Generate Price Input HTML.
         *
         * @param string $key Field key.
         * @param array  $data Field data.
         * @since  1.0.0
         * @return string
         */
        public function generate_price_html($key, $data)
        {
        }
        /**
         * Generate Decimal Input HTML.
         *
         * @param string $key Field key.
         * @param array  $data Field data.
         * @since  1.0.0
         * @return string
         */
        public function generate_decimal_html($key, $data)
        {
        }
        /**
         * Generate Password Input HTML.
         *
         * @param string $key Field key.
         * @param array  $data Field data.
         * @since  1.0.0
         * @return string
         */
        public function generate_password_html($key, $data)
        {
        }
        /**
         * Generate Color Picker Input HTML.
         *
         * @param string $key Field key.
         * @param array  $data Field data.
         * @since  1.0.0
         * @return string
         */
        public function generate_color_html($key, $data)
        {
        }
        /**
         * Generate Textarea HTML.
         *
         * @param string $key Field key.
         * @param array  $data Field data.
         * @since  1.0.0
         * @return string
         */
        public function generate_textarea_html($key, $data)
        {
        }
        /**
         * Generate Checkbox HTML.
         *
         * @param string $key Field key.
         * @param array  $data Field data.
         * @since  1.0.0
         * @return string
         */
        public function generate_checkbox_html($key, $data)
        {
        }
        /**
         * Generate Select HTML.
         *
         * @param string $key Field key.
         * @param array  $data Field data.
         * @since  1.0.0
         * @return string
         */
        public function generate_select_html($key, $data)
        {
        }
        /**
         * Generate Multiselect HTML.
         *
         * @param string $key Field key.
         * @param array  $data Field data.
         * @since  1.0.0
         * @return string
         */
        public function generate_multiselect_html($key, $data)
        {
        }
        /**
         * Generate Title HTML.
         *
         * @param string $key Field key.
         * @param array  $data Field data.
         * @since  1.0.0
         * @return string
         */
        public function generate_title_html($key, $data)
        {
        }
        /**
         * Validate Text Field.
         *
         * Make sure the data is escaped correctly, etc.
         *
         * @param  string $key Field key.
         * @param  string $value Posted Value.
         * @return string
         */
        public function validate_text_field($key, $value)
        {
        }
        /**
         * Sanitize 'Safe Text' fields.
         *
         * These fields are similar to regular text fields, but a much  smaller set of HTML tags are allowed. By default,
         * this means `<br>`, `<img>`, `<p>` and `<span>` tags.
         *
         * Note: this is a sanitization method, rather than a validation method (the name is due to some historic naming
         * choices).
         *
         * @param  string $key   Field key (currently unused).
         * @param  string $value Posted Value.
         *
         * @return string
         */
        public function validate_safe_text_field(string $key, ?string $value) : string
        {
        }
        /**
         * Validate Price Field.
         *
         * Make sure the data is escaped correctly, etc.
         *
         * @param  string $key Field key.
         * @param  string $value Posted Value.
         * @return string
         */
        public function validate_price_field($key, $value)
        {
        }
        /**
         * Validate Decimal Field.
         *
         * Make sure the data is escaped correctly, etc.
         *
         * @param  string $key Field key.
         * @param  string $value Posted Value.
         * @return string
         */
        public function validate_decimal_field($key, $value)
        {
        }
        /**
         * Validate Password Field. No input sanitization is used to avoid corrupting passwords.
         *
         * @param  string $key Field key.
         * @param  string $value Posted Value.
         * @return string
         */
        public function validate_password_field($key, $value)
        {
        }
        /**
         * Validate Textarea Field.
         *
         * @param  string $key Field key.
         * @param  string $value Posted Value.
         * @return string
         */
        public function validate_textarea_field($key, $value)
        {
        }
        /**
         * Validate Checkbox Field.
         *
         * If not set, return "no", otherwise return "yes".
         *
         * @param  string $key Field key.
         * @param  string $value Posted Value.
         * @return string
         */
        public function validate_checkbox_field($key, $value)
        {
        }
        /**
         * Validate Select Field.
         *
         * @param  string $key Field key.
         * @param  string $value Posted Value.
         * @return string
         */
        public function validate_select_field($key, $value)
        {
        }
        /**
         * Validate Multiselect Field.
         *
         * @param  string $key Field key.
         * @param  string $value Posted Value.
         * @return string|array
         */
        public function validate_multiselect_field($key, $value)
        {
        }
        /**
         * Validate the data on the "Settings" form.
         *
         * @deprecated 2.6.0 No longer used.
         * @param array $form_fields Array of fields.
         */
        public function validate_settings_fields($form_fields = array())
        {
        }
        /**
         * Format settings if needed.
         *
         * @deprecated 2.6.0 Unused.
         * @param  array $value Value to format.
         * @return array
         */
        public function format_settings($value)
        {
        }
    }
    /**
     * Legacy Abstract Order
     *
     * Legacy and deprecated functions are here to keep the WC_Abstract_Order clean.
     * This class will be removed in future versions.
     *
     * @version  3.0.0
     * @package  WooCommerce\Abstracts
     * @category    Abstract Class
     * @author    WooThemes
     */
    abstract class WC_Abstract_Legacy_Order extends \WC_Data
    {
        /**
         * Add coupon code to the order.
         * @param string|array $code
         * @param int $discount tax amount.
         * @param int $discount_tax amount.
         * @return int order item ID
         * @throws WC_Data_Exception
         */
        public function add_coupon($code = array(), $discount = 0, $discount_tax = 0)
        {
        }
        /**
         * Add a tax row to the order.
         * @param int $tax_rate_id
         * @param int $tax_amount amount of tax.
         * @param int $shipping_tax_amount shipping amount.
         * @return int order item ID
         * @throws WC_Data_Exception
         */
        public function add_tax($tax_rate_id, $tax_amount = 0, $shipping_tax_amount = 0)
        {
        }
        /**
         * Add a shipping row to the order.
         * @param WC_Shipping_Rate shipping_rate
         * @return int order item ID
         * @throws WC_Data_Exception
         */
        public function add_shipping($shipping_rate)
        {
        }
        /**
         * Add a fee to the order.
         * Order must be saved prior to adding items.
         *
         * Fee is an amount of money charged for a particular piece of work
         * or for a particular right or service, and not supposed to be negative.
         *
         * @throws WC_Data_Exception
         * @param  object $fee Fee data.
         * @return int         Updated order item ID.
         */
        public function add_fee($fee)
        {
        }
        /**
         * Update a line item for the order.
         *
         * Note this does not update order totals.
         *
         * @param object|int $item order item ID or item object.
         * @param WC_Product $product
         * @param array $args data to update.
         * @return int updated order item ID
         * @throws WC_Data_Exception
         */
        public function update_product($item, $product, $args)
        {
        }
        /**
         * Update coupon for order. Note this does not update order totals.
         * @param object|int $item
         * @param array $args
         * @return int updated order item ID
         * @throws WC_Data_Exception
         */
        public function update_coupon($item, $args)
        {
        }
        /**
         * Update shipping method for order.
         *
         * Note this does not update the order total.
         *
         * @param object|int $item
         * @param array $args
         * @return int updated order item ID
         * @throws WC_Data_Exception
         */
        public function update_shipping($item, $args)
        {
        }
        /**
         * Update fee for order.
         *
         * Note this does not update order totals.
         *
         * @param object|int $item
         * @param array $args
         * @return int updated order item ID
         * @throws WC_Data_Exception
         */
        public function update_fee($item, $args)
        {
        }
        /**
         * Update tax line on order.
         * Note this does not update order totals.
         *
         * @since 3.0
         * @param object|int $item
         * @param array $args
         * @return int updated order item ID
         * @throws WC_Data_Exception
         */
        public function update_tax($item, $args)
        {
        }
        /**
         * Get a product (either product or variation).
         * @deprecated 4.4.0
         * @param object $item
         * @return WC_Product|bool
         */
        public function get_product_from_item($item)
        {
        }
        /**
         * Set the customer address.
         * @param array $address Address data.
         * @param string $type billing or shipping.
         */
        public function set_address($address, $type = 'billing')
        {
        }
        /**
         * Set an order total.
         * @param float $amount
         * @param string $total_type
         * @return bool
         */
        public function legacy_set_total($amount, $total_type = 'total')
        {
        }
        /**
         * Magic __isset method for backwards compatibility. Handles legacy properties which could be accessed directly in the past.
         *
         * @param string $key
         * @return bool
         */
        public function __isset($key)
        {
        }
        /**
         * Magic __get method for backwards compatibility.
         *
         * @param string $key
         * @return mixed
         */
        public function __get($key)
        {
        }
        /**
         * has_meta function for order items. This is different to the WC_Data
         * version and should be removed in future versions.
         *
         * @deprecated 3.0
         *
         * @param int $order_item_id
         *
         * @return array of meta data.
         */
        public function has_meta($order_item_id)
        {
        }
        /**
         * Display meta data belonging to an item.
         * @param  array $item
         */
        public function display_item_meta($item)
        {
        }
        /**
         * Display download links for an order item.
         * @param  array $item
         */
        public function display_item_downloads($item)
        {
        }
        /**
         * Get the Download URL.
         *
         * @param  int $product_id
         * @param  int $download_id
         * @return string
         */
        public function get_download_url($product_id, $download_id)
        {
        }
        /**
         * Get the downloadable files for an item in this order.
         *
         * @param  array $item
         * @return array
         */
        public function get_item_downloads($item)
        {
        }
        /**
         * Gets shipping total. Alias of WC_Order::get_shipping_total().
         * @deprecated 3.0.0 since this is an alias only.
         * @return float
         */
        public function get_total_shipping()
        {
        }
        /**
         * Get order item meta.
         * @deprecated 3.0.0
         * @param mixed $order_item_id
         * @param string $key (default: '')
         * @param bool $single (default: false)
         * @return array|string
         */
        public function get_item_meta($order_item_id, $key = '', $single = \false)
        {
        }
        /**
         * Get all item meta data in array format in the order it was saved. Does not group meta by key like get_item_meta().
         *
         * @param mixed $order_item_id
         * @return array of objects
         */
        public function get_item_meta_array($order_item_id)
        {
        }
        /**
         * Get coupon codes only.
         *
         * @deprecated 3.7.0 - Replaced with better named method to reflect the actual data being returned.
         * @return array
         */
        public function get_used_coupons()
        {
        }
        /**
         * Expand item meta into the $item array.
         * @deprecated 3.0.0 Item meta no longer expanded due to new order item
         *      classes. This function now does nothing to avoid data breakage.
         * @param array $item before expansion.
         * @return array
         */
        public function expand_item_meta($item)
        {
        }
        /**
         * Load the order object. Called from the constructor.
         * @deprecated 3.0.0 Logic moved to constructor
         * @param int|object|WC_Order $order Order to init.
         */
        protected function init($order)
        {
        }
        /**
         * Gets an order from the database.
         * @deprecated 3.0
         * @param int $id (default: 0).
         * @return bool
         */
        public function get_order($id = 0)
        {
        }
        /**
         * Populates an order from the loaded post data.
         * @deprecated 3.0
         * @param mixed $result
         */
        public function populate($result)
        {
        }
        /**
         * Cancel the order and restore the cart (before payment).
         * @deprecated 3.0.0 Moved to event handler.
         * @param string $note (default: '') Optional note to add.
         */
        public function cancel_order($note = '')
        {
        }
        /**
         * Record sales.
         * @deprecated 3.0.0
         */
        public function record_product_sales()
        {
        }
        /**
         * Increase applied coupon counts.
         * @deprecated 3.0.0
         */
        public function increase_coupon_usage_counts()
        {
        }
        /**
         * Decrease applied coupon counts.
         * @deprecated 3.0.0
         */
        public function decrease_coupon_usage_counts()
        {
        }
        /**
         * Reduce stock levels for all line items in the order.
         * @deprecated 3.0.0
         */
        public function reduce_order_stock()
        {
        }
        /**
         * Send the stock notifications.
         * @deprecated 3.0.0 No longer needs to be called directly.
         *
         * @param $product
         * @param $new_stock
         * @param $qty_ordered
         */
        public function send_stock_notifications($product, $new_stock, $qty_ordered)
        {
        }
        /**
         * Output items for display in html emails.
         * @deprecated 3.0.0 Moved to template functions.
         * @param array $args Items args.
         * @return string
         */
        public function email_order_items_table($args = array())
        {
        }
        /**
         * Get currency.
         * @deprecated 3.0.0
         */
        public function get_order_currency()
        {
        }
    }
    /**
     * Trait WC_Item_Totals.
     *
     * Right now this do not have much, but plan is to eventually move all shared calculation logic between Orders and Cart in this file.
     *
     * @since 3.9.0
     */
    trait WC_Item_Totals
    {
        /**
         * Line items to calculate. Define in child class.
         *
         * @since 3.9.0
         * @param string $field Field name to calculate upon.
         *
         * @return array having `total`|`subtotal` property.
         */
        protected abstract function get_values_for_total($field);
        /**
         * Return rounded total based on settings. Will be used by Cart and Orders.
         *
         * @since 3.9.0
         *
         * @param array $values Values to round. Should be with precision.
         *
         * @return float|int Appropriately rounded value.
         */
        public static function get_rounded_items_total($values)
        {
        }
        /**
         * Apply rounding to item subtotal before summing.
         *
         * @since 3.9.0
         * @param float $value Item subtotal value.
         * @return float
         */
        public static function round_item_subtotal($value)
        {
        }
        /**
         * Should always round at subtotal?
         *
         * @since 3.9.0
         * @return bool
         */
        protected static function round_at_subtotal()
        {
        }
        /**
         * Apply rounding to an array of taxes before summing. Rounds to store DP setting, ignoring precision.
         *
         * @since  3.2.6
         * @param  float $value    Tax value.
         * @param  bool  $in_cents Whether precision of value is in cents.
         * @return float
         */
        protected static function round_line_tax($value, $in_cents = \true)
        {
        }
    }
    /**
     * WC_Abstract_Order class.
     */
    abstract class WC_Abstract_Order extends \WC_Abstract_Legacy_Order
    {
        use \WC_Item_Totals;
        /**
         * Order Data array. This is the core order data exposed in APIs since 3.0.0.
         *
         * Notes: cart_tax = cart_tax is the new name for the legacy 'order_tax'
         * which is the tax for items only, not shipping.
         *
         * @since 3.0.0
         * @var array
         */
        protected $data = array('parent_id' => 0, 'status' => '', 'currency' => '', 'version' => '', 'prices_include_tax' => \false, 'date_created' => \null, 'date_modified' => \null, 'discount_total' => 0, 'discount_tax' => 0, 'shipping_total' => 0, 'shipping_tax' => 0, 'cart_tax' => 0, 'total' => 0, 'total_tax' => 0);
        /**
         * List of properties that were earlier managed by data store. However, since DataStore is a not a stored entity in itself, they used to store data in metadata of the data object.
         * With custom tables, some of these are moved from metadata to their own columns, but existing code will still try to add them to metadata. This array is used to keep track of such properties.
         *
         * Only reason to add a property here is that you are moving properties from DataStore instance to data object. If you are adding a new property, consider adding it to to $data array instead.
         *
         * @var array
         */
        protected $legacy_datastore_props = array('_recorded_coupon_usage_counts');
        /**
         * Order items will be stored here, sometimes before they persist in the DB.
         *
         * @since 3.0.0
         * @var array
         */
        protected $items = array();
        /**
         * Order items that need deleting are stored here.
         *
         * @since 3.0.0
         * @var array
         */
        protected $items_to_delete = array();
        /**
         * Stores meta in cache for future reads.
         *
         * A group must be set to to enable caching.
         *
         * @var string
         */
        protected $cache_group = 'orders';
        /**
         * Which data store to load.
         *
         * @var string
         */
        protected $data_store_name = 'order';
        /**
         * This is the name of this object type.
         *
         * @var string
         */
        protected $object_type = 'order';
        /**
         * Get the order if ID is passed, otherwise the order is new and empty.
         * This class should NOT be instantiated, but the wc_get_order function or new WC_Order_Factory
         * should be used. It is possible, but the aforementioned are preferred and are the only
         * methods that will be maintained going forward.
         *
         * @param  int|object|WC_Order $order Order to read.
         */
        public function __construct($order = 0)
        {
        }
        /**
         * Get internal type.
         *
         * @return string
         */
        public function get_type()
        {
        }
        /**
         * Get all class data in array format.
         *
         * @since 3.0.0
         * @return array
         */
        public function get_data()
        {
        }
        /*
        |--------------------------------------------------------------------------
        | CRUD methods
        |--------------------------------------------------------------------------
        |
        | Methods which create, read, update and delete orders from the database.
        | Written in abstract fashion so that the way orders are stored can be
        | changed more easily in the future.
        |
        | A save method is included for convenience (chooses update or create based
        | on if the order exists yet).
        |
        */
        /**
         * Save data to the database.
         *
         * @since 3.0.0
         * @return int order ID
         */
        public function save()
        {
        }
        /**
         * Log an error about this order is exception is encountered.
         *
         * @param Exception $e Exception object.
         * @param string    $message Message regarding exception thrown.
         * @since 3.7.0
         */
        protected function handle_exception($e, $message = 'Error')
        {
        }
        /**
         * Save all order items which are part of this order.
         */
        protected function save_items()
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Getters
        |--------------------------------------------------------------------------
        */
        /**
         * Get parent order ID.
         *
         * @since 3.0.0
         * @param  string $context View or edit context.
         * @return integer
         */
        public function get_parent_id($context = 'view')
        {
        }
        /**
         * Gets order currency.
         *
         * @param  string $context View or edit context.
         * @return string
         */
        public function get_currency($context = 'view')
        {
        }
        /**
         * Get order_version.
         *
         * @param  string $context View or edit context.
         * @return string
         */
        public function get_version($context = 'view')
        {
        }
        /**
         * Get prices_include_tax.
         *
         * @param  string $context View or edit context.
         * @return bool
         */
        public function get_prices_include_tax($context = 'view')
        {
        }
        /**
         * Get date_created.
         *
         * @param  string $context View or edit context.
         * @return WC_DateTime|NULL object if the date is set or null if there is no date.
         */
        public function get_date_created($context = 'view')
        {
        }
        /**
         * Get date_modified.
         *
         * @param  string $context View or edit context.
         * @return WC_DateTime|NULL object if the date is set or null if there is no date.
         */
        public function get_date_modified($context = 'view')
        {
        }
        /**
         * Return the order statuses without wc- internal prefix.
         *
         * @param  string $context View or edit context.
         * @return string
         */
        public function get_status($context = 'view')
        {
        }
        /**
         * Get discount_total.
         *
         * @param  string $context View or edit context.
         * @return string
         */
        public function get_discount_total($context = 'view')
        {
        }
        /**
         * Get discount_tax.
         *
         * @param  string $context View or edit context.
         * @return string
         */
        public function get_discount_tax($context = 'view')
        {
        }
        /**
         * Get shipping_total.
         *
         * @param  string $context View or edit context.
         * @return string
         */
        public function get_shipping_total($context = 'view')
        {
        }
        /**
         * Get shipping_tax.
         *
         * @param  string $context View or edit context.
         * @return string
         */
        public function get_shipping_tax($context = 'view')
        {
        }
        /**
         * Gets cart tax amount.
         *
         * @param  string $context View or edit context.
         * @return float
         */
        public function get_cart_tax($context = 'view')
        {
        }
        /**
         * Gets order grand total. incl. taxes. Used in gateways.
         *
         * @param  string $context View or edit context.
         * @return float
         */
        public function get_total($context = 'view')
        {
        }
        /**
         * Get total tax amount. Alias for get_order_tax().
         *
         * @param  string $context View or edit context.
         * @return float
         */
        public function get_total_tax($context = 'view')
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Non-CRUD Getters
        |--------------------------------------------------------------------------
        */
        /**
         * Gets the total discount amount.
         *
         * @param  bool $ex_tax Show discount excl any tax.
         * @return float
         */
        public function get_total_discount($ex_tax = \true)
        {
        }
        /**
         * Gets order subtotal.
         *
         * @return float
         */
        public function get_subtotal()
        {
        }
        /**
         * Get taxes, merged by code, formatted ready for output.
         *
         * @return array
         */
        public function get_tax_totals()
        {
        }
        /**
         * Get all valid statuses for this order
         *
         * @since 3.0.0
         * @return array Internal status keys e.g. 'wc-processing'
         */
        protected function get_valid_statuses()
        {
        }
        /**
         * Get user ID. Used by orders, not other order types like refunds.
         *
         * @param  string $context View or edit context.
         * @return int
         */
        public function get_user_id($context = 'view')
        {
        }
        /**
         * Get user. Used by orders, not other order types like refunds.
         *
         * @return WP_User|false
         */
        public function get_user()
        {
        }
        /**
         * Gets information about whether coupon counts were updated.
         *
         * @param string $context What the value is for. Valid values are view and edit.
         *
         * @return bool True if coupon counts were updated, false otherwise.
         */
        public function get_recorded_coupon_usage_counts($context = 'view')
        {
        }
        /**
         * Get basic order data in array format.
         *
         * @return array
         */
        public function get_base_data()
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Setters
        |--------------------------------------------------------------------------
        |
        | Functions for setting order data. These should not update anything in the
        | database itself and should only change what is stored in the class
        | object. However, for backwards compatibility pre 3.0.0 some of these
        | setters may handle both.
        */
        /**
         * Set parent order ID.
         *
         * @since 3.0.0
         * @param int $value Value to set.
         * @throws WC_Data_Exception Exception thrown if parent ID does not exist or is invalid.
         */
        public function set_parent_id($value)
        {
        }
        /**
         * Set order status.
         *
         * @since 3.0.0
         * @param string $new_status Status to change the order to. No internal wc- prefix is required.
         * @return array details of change
         */
        public function set_status($new_status)
        {
        }
        /**
         * Set order_version.
         *
         * @param string $value Value to set.
         * @throws WC_Data_Exception Exception may be thrown if value is invalid.
         */
        public function set_version($value)
        {
        }
        /**
         * Set order_currency.
         *
         * @param string $value Value to set.
         * @throws WC_Data_Exception Exception may be thrown if value is invalid.
         */
        public function set_currency($value)
        {
        }
        /**
         * Set prices_include_tax.
         *
         * @param bool $value Value to set.
         * @throws WC_Data_Exception Exception may be thrown if value is invalid.
         */
        public function set_prices_include_tax($value)
        {
        }
        /**
         * Set date_created.
         *
         * @param  string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if there is no date.
         * @throws WC_Data_Exception Exception may be thrown if value is invalid.
         */
        public function set_date_created($date = \null)
        {
        }
        /**
         * Set date_modified.
         *
         * @param  string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if there is no date.
         * @throws WC_Data_Exception Exception may be thrown if value is invalid.
         */
        public function set_date_modified($date = \null)
        {
        }
        /**
         * Set discount_total.
         *
         * @param string $value Value to set.
         * @throws WC_Data_Exception Exception may be thrown if value is invalid.
         */
        public function set_discount_total($value)
        {
        }
        /**
         * Set discount_tax.
         *
         * @param string $value Value to set.
         * @throws WC_Data_Exception Exception may be thrown if value is invalid.
         */
        public function set_discount_tax($value)
        {
        }
        /**
         * Set shipping_total.
         *
         * @param string $value Value to set.
         * @throws WC_Data_Exception Exception may be thrown if value is invalid.
         */
        public function set_shipping_total($value)
        {
        }
        /**
         * Set shipping_tax.
         *
         * @param string $value Value to set.
         * @throws WC_Data_Exception Exception may be thrown if value is invalid.
         */
        public function set_shipping_tax($value)
        {
        }
        /**
         * Set cart tax.
         *
         * @param string $value Value to set.
         * @throws WC_Data_Exception Exception may be thrown if value is invalid.
         */
        public function set_cart_tax($value)
        {
        }
        /**
         * Sets order tax (sum of cart and shipping tax). Used internally only.
         *
         * @param string $value Value to set.
         * @throws WC_Data_Exception Exception may be thrown if value is invalid.
         */
        protected function set_total_tax($value)
        {
        }
        /**
         * Set total.
         *
         * @param string $value Value to set.
         * @param string $deprecated Function used to set different totals based on this.
         *
         * @return bool|void
         * @throws WC_Data_Exception Exception may be thrown if value is invalid.
         */
        public function set_total($value, $deprecated = '')
        {
        }
        /**
         * Stores information about whether the coupon usage were counted.
         *
         * @param bool|string $value True if counted, false if not.
         *
         * @return void
         */
        public function set_recorded_coupon_usage_counts($value)
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Order Item Handling
        |--------------------------------------------------------------------------
        |
        | Order items are used for products, taxes, shipping, and fees within
        | each order.
        */
        /**
         * Remove all line items (products, coupons, shipping, taxes) from the order.
         *
         * @param string $type Order item type. Default null.
         */
        public function remove_order_items($type = \null)
        {
        }
        /**
         * Convert a type to a types group.
         *
         * @param string $type type to lookup.
         * @return string
         */
        protected function type_to_group($type)
        {
        }
        /**
         * Return an array of items/products within this order.
         *
         * @param string|array $types Types of line items to get (array or string).
         * @return WC_Order_Item[]
         */
        public function get_items($types = 'line_item')
        {
        }
        /**
         * Return array of values for calculations.
         *
         * @param string $field Field name to return.
         *
         * @return array Array of values.
         */
        protected function get_values_for_total($field)
        {
        }
        /**
         * Return an array of coupons within this order.
         *
         * @since  3.7.0
         * @return WC_Order_Item_Coupon[]
         */
        public function get_coupons()
        {
        }
        /**
         * Return an array of fees within this order.
         *
         * @return WC_Order_item_Fee[]
         */
        public function get_fees()
        {
        }
        /**
         * Return an array of taxes within this order.
         *
         * @return WC_Order_Item_Tax[]
         */
        public function get_taxes()
        {
        }
        /**
         * Return an array of shipping costs within this order.
         *
         * @return WC_Order_Item_Shipping[]
         */
        public function get_shipping_methods()
        {
        }
        /**
         * Gets formatted shipping method title.
         *
         * @return string
         */
        public function get_shipping_method()
        {
        }
        /**
         * Get used coupon codes only.
         *
         * @since 3.7.0
         * @return array
         */
        public function get_coupon_codes()
        {
        }
        /**
         * Gets the count of order items of a certain type.
         *
         * @param string $item_type Item type to lookup.
         * @return int|string
         */
        public function get_item_count($item_type = '')
        {
        }
        /**
         * Get an order item object, based on its type.
         *
         * @since  3.0.0
         * @param  int  $item_id ID of item to get.
         * @param  bool $load_from_db Prior to 3.2 this item was loaded direct from WC_Order_Factory, not this object. This param is here for backwards compatibility with that. If false, uses the local items variable instead.
         * @return WC_Order_Item|false
         */
        public function get_item($item_id, $load_from_db = \true)
        {
        }
        /**
         * Get key for where a certain item type is stored in _items.
         *
         * @since  3.0.0
         * @param  string $item object Order item (product, shipping, fee, coupon, tax).
         * @return string
         */
        protected function get_items_key($item)
        {
        }
        /**
         * Remove item from the order.
         *
         * @param int $item_id Item ID to delete.
         * @return false|void
         */
        public function remove_item($item_id)
        {
        }
        /**
         * Adds an order item to this order. The order item will not persist until save.
         *
         * @since 3.0.0
         * @param WC_Order_Item $item Order item object (product, shipping, fee, coupon, tax).
         * @return false|void
         */
        public function add_item($item)
        {
        }
        /**
         * Check and records coupon usage tentatively so that counts validation is correct. Display an error if coupon usage limit has been reached.
         *
         * If you are using this method, make sure to `release_held_coupons` in case an Exception is thrown.
         *
         * @throws Exception When not able to apply coupon.
         *
         * @param string $billing_email Billing email of order.
         */
        public function hold_applied_coupons($billing_email)
        {
        }
        /**
         * Hold coupon if a global usage limit is defined.
         *
         * @param WC_Coupon $coupon Coupon object.
         *
         * @return string    Meta key which indicates held coupon.
         * @throws Exception When can't be held.
         */
        private function hold_coupon($coupon)
        {
        }
        /**
         * Hold coupon if usage limit per customer is defined.
         *
         * @param WC_Coupon $coupon              Coupon object.
         * @param array     $user_ids_and_emails Array of user Id and emails to check for usage limit.
         * @param string    $user_alias          User ID or email to use to record current usage.
         *
         * @return string    Meta key which indicates held coupon.
         * @throws Exception When coupon can't be held.
         */
        private function hold_coupon_for_users($coupon, $user_ids_and_emails, $user_alias)
        {
        }
        /**
         * Helper method to get all aliases for current user and provide billing email.
         *
         * @param string $billing_email Billing email provided in form.
         *
         * @return array     Array of all aliases.
         * @throws Exception When validation fails.
         */
        private function get_billing_and_current_user_aliases($billing_email)
        {
        }
        /**
         * Apply a coupon to the order and recalculate totals.
         *
         * @since 3.2.0
         * @param string|WC_Coupon $raw_coupon Coupon code or object.
         * @return true|WP_Error True if applied, error if not.
         */
        public function apply_coupon($raw_coupon)
        {
        }
        /**
         * Remove a coupon from the order and recalculate totals.
         *
         * Coupons affect line item totals, but there is no relationship between
         * coupon and line total, so to remove a coupon we need to work from the
         * line subtotal (price before discount) and re-apply all coupons in this
         * order.
         *
         * Manual discounts are not affected; those are separate and do not affect
         * stored line totals.
         *
         * @since  3.2.0
         * @param  string $code Coupon code.
         * @return void
         */
        public function remove_coupon($code)
        {
        }
        /**
         * Apply all coupons in this order again to all line items.
         * This method is public since WooCommerce 3.8.0.
         *
         * @since 3.2.0
         */
        public function recalculate_coupons()
        {
        }
        /**
         * After applying coupons via the WC_Discounts class, update line items.
         *
         * @since 3.2.0
         * @param WC_Discounts $discounts Discounts class.
         */
        protected function set_item_discount_amounts($discounts)
        {
        }
        /**
         * After applying coupons via the WC_Discounts class, update or create coupon items.
         *
         * @since 3.2.0
         * @param WC_Discounts $discounts Discounts class.
         */
        protected function set_coupon_discount_amounts($discounts)
        {
        }
        /**
         * Add a product line item to the order. This is the only line item type with
         * its own method because it saves looking up order amounts (costs are added up for you).
         *
         * @param  WC_Product $product Product object.
         * @param  int        $qty Quantity to add.
         * @param  array      $args Args for the added product.
         * @return int
         */
        public function add_product($product, $qty = 1, $args = array())
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Payment Token Handling
        |--------------------------------------------------------------------------
        |
        | Payment tokens are hashes used to take payments by certain gateways.
        |
        */
        /**
         * Add a payment token to an order
         *
         * @since 2.6
         * @param WC_Payment_Token $token Payment token object.
         * @return boolean|int The new token ID or false if it failed.
         */
        public function add_payment_token($token)
        {
        }
        /**
         * Returns a list of all payment tokens associated with the current order
         *
         * @since 2.6
         * @return array An array of payment token objects
         */
        public function get_payment_tokens()
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Calculations.
        |--------------------------------------------------------------------------
        |
        | These methods calculate order totals and taxes based on the current data.
        |
        */
        /**
         * Calculate shipping total.
         *
         * @since 2.2
         * @return float
         */
        public function calculate_shipping()
        {
        }
        /**
         * Get all tax classes for items in the order.
         *
         * @since 2.6.3
         * @return array
         */
        public function get_items_tax_classes()
        {
        }
        /**
         * Get tax location for this order.
         *
         * @since 3.2.0
         * @param array $args array Override the location.
         * @return array
         */
        protected function get_tax_location($args = array())
        {
        }
        /**
         * Get tax rates for an order. Use order's shipping or billing address, defaults to base location.
         *
         * @param string $tax_class     Tax class to get rates for.
         * @param array  $location_args Location to compute rates for. Should be in form: array( country, state, postcode, city).
         * @param object $customer      Only used to maintain backward compatibility for filter `woocommerce-matched_rates`.
         *
         * @return mixed|void Tax rates.
         */
        protected function get_tax_rates($tax_class, $location_args = array(), $customer = \null)
        {
        }
        /**
         * Calculate taxes for all line items and shipping, and store the totals and tax rows.
         *
         * If by default the taxes are based on the shipping address and the current order doesn't
         * have any, it would use the billing address rather than using the Shopping base location.
         *
         * Will use the base country unless customer addresses are set.
         *
         * @param array $args Added in 3.0.0 to pass things like location.
         */
        public function calculate_taxes($args = array())
        {
        }
        /**
         * Calculate fees for all line items.
         *
         * @return float Fee total.
         */
        public function get_total_fees()
        {
        }
        /**
         * Update tax lines for the order based on the line item taxes themselves.
         */
        public function update_taxes()
        {
        }
        /**
         * Helper function.
         * If you add all items in this order in cart again, this would be the cart subtotal (assuming all other settings are same).
         *
         * @return float Cart subtotal.
         */
        protected function get_cart_subtotal_for_order()
        {
        }
        /**
         * Helper function.
         * If you add all items in this order in cart again, this would be the cart total (assuming all other settings are same).
         *
         * @return float Cart total.
         */
        protected function get_cart_total_for_order()
        {
        }
        /**
         * Calculate totals by looking at the contents of the order. Stores the totals and returns the orders final total.
         *
         * @since 2.2
         * @param  bool $and_taxes Calc taxes if true.
         * @return float calculated grand total.
         */
        public function calculate_totals($and_taxes = \true)
        {
        }
        /**
         * Get item subtotal - this is the cost before discount.
         *
         * @param object $item Item to get total from.
         * @param bool   $inc_tax (default: false).
         * @param bool   $round (default: true).
         * @return float
         */
        public function get_item_subtotal($item, $inc_tax = \false, $round = \true)
        {
        }
        /**
         * Get line subtotal - this is the cost before discount.
         *
         * @param object $item Item to get total from.
         * @param bool   $inc_tax (default: false).
         * @param bool   $round (default: true).
         * @return float
         */
        public function get_line_subtotal($item, $inc_tax = \false, $round = \true)
        {
        }
        /**
         * Calculate item cost - useful for gateways.
         *
         * @param object $item Item to get total from.
         * @param bool   $inc_tax (default: false).
         * @param bool   $round (default: true).
         * @return float
         */
        public function get_item_total($item, $inc_tax = \false, $round = \true)
        {
        }
        /**
         * Calculate line total - useful for gateways.
         *
         * @param object $item Item to get total from.
         * @param bool   $inc_tax (default: false).
         * @param bool   $round (default: true).
         * @return float
         */
        public function get_line_total($item, $inc_tax = \false, $round = \true)
        {
        }
        /**
         * Get item tax - useful for gateways.
         *
         * @param mixed $item Item to get total from.
         * @param bool  $round (default: true).
         * @return float
         */
        public function get_item_tax($item, $round = \true)
        {
        }
        /**
         * Get line tax - useful for gateways.
         *
         * @param mixed $item Item to get total from.
         * @return float
         */
        public function get_line_tax($item)
        {
        }
        /**
         * Gets line subtotal - formatted for display.
         *
         * @param object $item Item to get total from.
         * @param string $tax_display Incl or excl tax display mode.
         * @return string
         */
        public function get_formatted_line_subtotal($item, $tax_display = '')
        {
        }
        /**
         * Gets order total - formatted for display.
         *
         * @return string
         */
        public function get_formatted_order_total()
        {
        }
        /**
         * Gets subtotal - subtotal is shown before discounts, but with localised taxes.
         *
         * @param bool   $compound (default: false).
         * @param string $tax_display (default: the tax_display_cart value).
         * @return string
         */
        public function get_subtotal_to_display($compound = \false, $tax_display = '')
        {
        }
        /**
         * Gets shipping (formatted).
         *
         * @param string $tax_display Excl or incl tax display mode.
         * @return string
         */
        public function get_shipping_to_display($tax_display = '')
        {
        }
        /**
         * Get the discount amount (formatted).
         *
         * @since  2.3.0
         * @param string $tax_display Excl or incl tax display mode.
         * @return string
         */
        public function get_discount_to_display($tax_display = '')
        {
        }
        /**
         * Add total row for subtotal.
         *
         * @param array  $total_rows Reference to total rows array.
         * @param string $tax_display Excl or incl tax display mode.
         */
        protected function add_order_item_totals_subtotal_row(&$total_rows, $tax_display)
        {
        }
        /**
         * Add total row for discounts.
         *
         * @param array  $total_rows Reference to total rows array.
         * @param string $tax_display Excl or incl tax display mode.
         */
        protected function add_order_item_totals_discount_row(&$total_rows, $tax_display)
        {
        }
        /**
         * Add total row for shipping.
         *
         * @param array  $total_rows Reference to total rows array.
         * @param string $tax_display Excl or incl tax display mode.
         */
        protected function add_order_item_totals_shipping_row(&$total_rows, $tax_display)
        {
        }
        /**
         * Add total row for fees.
         *
         * @param array  $total_rows Reference to total rows array.
         * @param string $tax_display Excl or incl tax display mode.
         */
        protected function add_order_item_totals_fee_rows(&$total_rows, $tax_display)
        {
        }
        /**
         * Add total row for taxes.
         *
         * @param array  $total_rows Reference to total rows array.
         * @param string $tax_display Excl or incl tax display mode.
         */
        protected function add_order_item_totals_tax_rows(&$total_rows, $tax_display)
        {
        }
        /**
         * Add total row for grand total.
         *
         * @param array  $total_rows Reference to total rows array.
         * @param string $tax_display Excl or incl tax display mode.
         */
        protected function add_order_item_totals_total_row(&$total_rows, $tax_display)
        {
        }
        /**
         * Get totals for display on pages and in emails.
         *
         * @param mixed $tax_display Excl or incl tax display mode.
         * @return array
         */
        public function get_order_item_totals($tax_display = '')
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Conditionals
        |--------------------------------------------------------------------------
        |
        | Checks if a condition is true or false.
        |
        */
        /**
         * Checks the order status against a passed in status.
         *
         * @param array|string $status Status to check.
         * @return bool
         */
        public function has_status($status)
        {
        }
        /**
         * Check whether this order has a specific shipping method or not.
         *
         * @param string $method_id Method ID to check.
         * @return bool
         */
        public function has_shipping_method($method_id)
        {
        }
        /**
         * Returns true if the order contains a free product.
         *
         * @since 2.5.0
         * @return bool
         */
        public function has_free_item()
        {
        }
        /**
         * Get order title.
         *
         * @return string Order title.
         */
        public function get_title() : string
        {
        }
    }
    /**
     * Legacy Abstract Product
     *
     * Legacy and deprecated functions are here to keep the WC_Abstract_Product
     * clean.
     * This class will be removed in future versions.
     *
     * @version  3.0.0
     * @package  WooCommerce\Abstracts
     * @category Abstract Class
     * @author   WooThemes
     */
    abstract class WC_Abstract_Legacy_Product extends \WC_Data
    {
        /**
         * Magic __isset method for backwards compatibility. Legacy properties which could be accessed directly in the past.
         *
         * @param  string $key Key name.
         * @return bool
         */
        public function __isset($key)
        {
        }
        /**
         * Magic __get method for backwards compatibility. Maps legacy vars to new getters.
         *
         * @param  string $key Key name.
         * @return mixed
         */
        public function __get($key)
        {
        }
        /**
         * If set, get the default attributes for a variable product.
         *
         * @deprecated 3.0.0
         * @return array
         */
        public function get_variation_default_attributes()
        {
        }
        /**
         * Returns the gallery attachment ids.
         *
         * @deprecated 3.0.0
         * @return array
         */
        public function get_gallery_attachment_ids()
        {
        }
        /**
         * Set stock level of the product.
         *
         * @deprecated 3.0.0
         *
         * @param int $amount
         * @param string $mode
         *
         * @return int
         */
        public function set_stock($amount = \null, $mode = 'set')
        {
        }
        /**
         * Reduce stock level of the product.
         *
         * @deprecated 3.0.0
         * @param int $amount Amount to reduce by. Default: 1
         * @return int new stock level
         */
        public function reduce_stock($amount = 1)
        {
        }
        /**
         * Increase stock level of the product.
         *
         * @deprecated 3.0.0
         * @param int $amount Amount to increase by. Default 1.
         * @return int new stock level
         */
        public function increase_stock($amount = 1)
        {
        }
        /**
         * Check if the stock status needs changing.
         *
         * @deprecated 3.0.0 Sync is done automatically on read/save, so calling this should not be needed any more.
         */
        public function check_stock_status()
        {
        }
        /**
         * Get and return related products.
         * @deprecated 3.0.0 Use wc_get_related_products instead.
         *
         * @param int $limit
         *
         * @return array
         */
        public function get_related($limit = 5)
        {
        }
        /**
         * Retrieves related product terms.
         * @deprecated 3.0.0 Use wc_get_product_term_ids instead.
         *
         * @param $term
         *
         * @return array
         */
        protected function get_related_terms($term)
        {
        }
        /**
         * Builds the related posts query.
         * @deprecated 3.0.0 Use Product Data Store get_related_products_query instead.
         *
         * @param $cats_array
         * @param $tags_array
         * @param $exclude_ids
         * @param $limit
         */
        protected function build_related_query($cats_array, $tags_array, $exclude_ids, $limit)
        {
        }
        /**
         * Returns the child product.
         * @deprecated 3.0.0 Use wc_get_product instead.
         * @param mixed $child_id
         * @return WC_Product|WC_Product|WC_Product_variation
         */
        public function get_child($child_id)
        {
        }
        /**
         * Functions for getting parts of a price, in html, used by get_price_html.
         *
         * @deprecated 3.0.0
         * @return string
         */
        public function get_price_html_from_text()
        {
        }
        /**
         * Functions for getting parts of a price, in html, used by get_price_html.
         *
         * @deprecated 3.0.0 Use wc_format_sale_price instead.
         * @param  string $from String or float to wrap with 'from' text
         * @param  mixed $to String or float to wrap with 'to' text
         * @return string
         */
        public function get_price_html_from_to($from, $to)
        {
        }
        /**
         * Lists a table of attributes for the product page.
         * @deprecated 3.0.0 Use wc_display_product_attributes instead.
         */
        public function list_attributes()
        {
        }
        /**
         * Returns the price (including tax). Uses customer tax rates. Can work for a specific $qty for more accurate taxes.
         *
         * @deprecated 3.0.0 Use wc_get_price_including_tax instead.
         * @param  int $qty
         * @param  string $price to calculate, left blank to just use get_price()
         * @return string
         */
        public function get_price_including_tax($qty = 1, $price = '')
        {
        }
        /**
         * Returns the price including or excluding tax, based on the 'woocommerce_tax_display_shop' setting.
         *
         * @deprecated 3.0.0 Use wc_get_price_to_display instead.
         * @param  string  $price to calculate, left blank to just use get_price()
         * @param  integer $qty   passed on to get_price_including_tax() or get_price_excluding_tax()
         * @return string
         */
        public function get_display_price($price = '', $qty = 1)
        {
        }
        /**
         * Returns the price (excluding tax) - ignores tax_class filters since the price may *include* tax and thus needs subtracting.
         * Uses store base tax rates. Can work for a specific $qty for more accurate taxes.
         *
         * @deprecated 3.0.0 Use wc_get_price_excluding_tax instead.
         * @param  int $qty
         * @param  string $price to calculate, left blank to just use get_price()
         * @return string
         */
        public function get_price_excluding_tax($qty = 1, $price = '')
        {
        }
        /**
         * Adjust a products price dynamically.
         *
         * @deprecated 3.0.0
         * @param mixed $price
         */
        public function adjust_price($price)
        {
        }
        /**
         * Returns the product categories.
         *
         * @deprecated 3.0.0
         * @param string $sep (default: ', ').
         * @param string $before (default: '').
         * @param string $after (default: '').
         * @return string
         */
        public function get_categories($sep = ', ', $before = '', $after = '')
        {
        }
        /**
         * Returns the product tags.
         *
         * @deprecated 3.0.0
         * @param string $sep (default: ', ').
         * @param string $before (default: '').
         * @param string $after (default: '').
         * @return array
         */
        public function get_tags($sep = ', ', $before = '', $after = '')
        {
        }
        /**
         * Get the product's post data.
         *
         * @deprecated 3.0.0
         * @return WP_Post
         */
        public function get_post_data()
        {
        }
        /**
         * Get the parent of the post.
         *
         * @deprecated 3.0.0
         * @return int
         */
        public function get_parent()
        {
        }
        /**
         * Returns the upsell product ids.
         *
         * @deprecated 3.0.0
         * @return array
         */
        public function get_upsells()
        {
        }
        /**
         * Returns the cross sell product ids.
         *
         * @deprecated 3.0.0
         * @return array
         */
        public function get_cross_sells()
        {
        }
        /**
         * Check if variable product has default attributes set.
         *
         * @deprecated 3.0.0
         * @return bool
         */
        public function has_default_attributes()
        {
        }
        /**
         * Get variation ID.
         *
         * @deprecated 3.0.0
         * @return int
         */
        public function get_variation_id()
        {
        }
        /**
         * Get product variation description.
         *
         * @deprecated 3.0.0
         * @return string
         */
        public function get_variation_description()
        {
        }
        /**
         * Check if all variation's attributes are set.
         *
         * @deprecated 3.0.0
         * @return boolean
         */
        public function has_all_attributes_set()
        {
        }
        /**
         * Returns whether or not the variations parent is visible.
         *
         * @deprecated 3.0.0
         * @return bool
         */
        public function parent_is_visible()
        {
        }
        /**
         * Get total stock - This is the stock of parent and children combined.
         *
         * @deprecated 3.0.0
         * @return int
         */
        public function get_total_stock()
        {
        }
        /**
         * Get formatted variation data with WC < 2.4 back compat and proper formatting of text-based attribute names.
         *
         * @deprecated 3.0.0
         *
         * @param bool $flat
         *
         * @return string
         */
        public function get_formatted_variation_attributes($flat = \false)
        {
        }
        /**
         * Sync variable product prices with the children lowest/highest prices.
         *
         * @deprecated 3.0.0 not used in core.
         *
         * @param int $product_id
         */
        public function variable_product_sync($product_id = 0)
        {
        }
        /**
         * Sync the variable product's attributes with the variations.
         *
         * @param $product
         * @param bool $children
         */
        public static function sync_attributes($product, $children = \false)
        {
        }
        /**
         * Match a variation to a given set of attributes using a WP_Query.
         * @deprecated 3.0.0 in favour of Product data store's find_matching_product_variation.
         *
         * @param array $match_attributes
         */
        public function get_matching_variation($match_attributes = array())
        {
        }
        /**
         * Returns whether or not we are showing dimensions on the product page.
         * @deprecated 3.0.0 Unused.
         * @return bool
         */
        public function enable_dimensions_display()
        {
        }
        /**
         * Returns the product rating in html format.
         *
         * @deprecated 3.0.0
         * @param string $rating (default: '')
         * @return string
         */
        public function get_rating_html($rating = \null)
        {
        }
        /**
         * Sync product rating. Can be called statically.
         *
         * @deprecated 3.0.0
         * @param  int $post_id
         */
        public static function sync_average_rating($post_id)
        {
        }
        /**
         * Sync product rating count. Can be called statically.
         *
         * @deprecated 3.0.0
         * @param  int $post_id
         */
        public static function sync_rating_count($post_id)
        {
        }
        /**
         * Same as get_downloads in CRUD.
         *
         * @deprecated 3.0.0
         * @return array
         */
        public function get_files()
        {
        }
        /**
         * @deprecated 3.0.0 Sync is taken care of during save - no need to call this directly.
         */
        public function grouped_product_sync()
        {
        }
    }
    /**
     * Abstract Product Class
     *
     * The WooCommerce product class handles individual product data.
     *
     * @version 3.0.0
     * @package WooCommerce\Abstracts
     */
    class WC_Product extends \WC_Abstract_Legacy_Product
    {
        /**
         * This is the name of this object type.
         *
         * @var string
         */
        protected $object_type = 'product';
        /**
         * Post type.
         *
         * @var string
         */
        protected $post_type = 'product';
        /**
         * Cache group.
         *
         * @var string
         */
        protected $cache_group = 'products';
        /**
         * Stores product data.
         *
         * @var array
         */
        protected $data = array('name' => '', 'slug' => '', 'date_created' => \null, 'date_modified' => \null, 'status' => \false, 'featured' => \false, 'catalog_visibility' => 'visible', 'description' => '', 'short_description' => '', 'sku' => '', 'price' => '', 'regular_price' => '', 'sale_price' => '', 'date_on_sale_from' => \null, 'date_on_sale_to' => \null, 'total_sales' => '0', 'tax_status' => 'taxable', 'tax_class' => '', 'manage_stock' => \false, 'stock_quantity' => \null, 'stock_status' => 'instock', 'backorders' => 'no', 'low_stock_amount' => '', 'sold_individually' => \false, 'weight' => '', 'length' => '', 'width' => '', 'height' => '', 'upsell_ids' => array(), 'cross_sell_ids' => array(), 'parent_id' => 0, 'reviews_allowed' => \true, 'purchase_note' => '', 'attributes' => array(), 'default_attributes' => array(), 'menu_order' => 0, 'post_password' => '', 'virtual' => \false, 'downloadable' => \false, 'category_ids' => array(), 'tag_ids' => array(), 'shipping_class_id' => 0, 'downloads' => array(), 'image_id' => '', 'gallery_image_ids' => array(), 'download_limit' => -1, 'download_expiry' => -1, 'rating_counts' => array(), 'average_rating' => 0, 'review_count' => 0);
        /**
         * Supported features such as 'ajax_add_to_cart'.
         *
         * @var array
         */
        protected $supports = array();
        /**
         * Get the product if ID is passed, otherwise the product is new and empty.
         * This class should NOT be instantiated, but the wc_get_product() function
         * should be used. It is possible, but the wc_get_product() is preferred.
         *
         * @param int|WC_Product|object $product Product to init.
         */
        public function __construct($product = 0)
        {
        }
        /**
         * Get internal type. Should return string and *should be overridden* by child classes.
         *
         * The product_type property is deprecated but is used here for BW compatibility with child classes which may be defining product_type and not have a get_type method.
         *
         * @since  3.0.0
         * @return string
         */
        public function get_type()
        {
        }
        /**
         * Get product name.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_name($context = 'view')
        {
        }
        /**
         * Get product slug.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_slug($context = 'view')
        {
        }
        /**
         * Get product created date.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return WC_DateTime|NULL object if the date is set or null if there is no date.
         */
        public function get_date_created($context = 'view')
        {
        }
        /**
         * Get product modified date.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return WC_DateTime|NULL object if the date is set or null if there is no date.
         */
        public function get_date_modified($context = 'view')
        {
        }
        /**
         * Get product status.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_status($context = 'view')
        {
        }
        /**
         * If the product is featured.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return boolean
         */
        public function get_featured($context = 'view')
        {
        }
        /**
         * Get catalog visibility.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_catalog_visibility($context = 'view')
        {
        }
        /**
         * Get product description.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_description($context = 'view')
        {
        }
        /**
         * Get product short description.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_short_description($context = 'view')
        {
        }
        /**
         * Get SKU (Stock-keeping unit) - product unique ID.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_sku($context = 'view')
        {
        }
        /**
         * Returns the product's active price.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string price
         */
        public function get_price($context = 'view')
        {
        }
        /**
         * Returns the product's regular price.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string price
         */
        public function get_regular_price($context = 'view')
        {
        }
        /**
         * Returns the product's sale price.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string price
         */
        public function get_sale_price($context = 'view')
        {
        }
        /**
         * Get date on sale from.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return WC_DateTime|NULL object if the date is set or null if there is no date.
         */
        public function get_date_on_sale_from($context = 'view')
        {
        }
        /**
         * Get date on sale to.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return WC_DateTime|NULL object if the date is set or null if there is no date.
         */
        public function get_date_on_sale_to($context = 'view')
        {
        }
        /**
         * Get number total of sales.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return int
         */
        public function get_total_sales($context = 'view')
        {
        }
        /**
         * Returns the tax status.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_tax_status($context = 'view')
        {
        }
        /**
         * Returns the tax class.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_tax_class($context = 'view')
        {
        }
        /**
         * Return if product manage stock.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return boolean
         */
        public function get_manage_stock($context = 'view')
        {
        }
        /**
         * Returns number of items available for sale.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return int|null
         */
        public function get_stock_quantity($context = 'view')
        {
        }
        /**
         * Return the stock status.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @since  3.0.0
         * @return string
         */
        public function get_stock_status($context = 'view')
        {
        }
        /**
         * Get backorders.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @since  3.0.0
         * @return string yes no or notify
         */
        public function get_backorders($context = 'view')
        {
        }
        /**
         * Get low stock amount.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @since  3.5.0
         * @return int|string Returns empty string if value not set
         */
        public function get_low_stock_amount($context = 'view')
        {
        }
        /**
         * Return if should be sold individually.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @since  3.0.0
         * @return boolean
         */
        public function get_sold_individually($context = 'view')
        {
        }
        /**
         * Returns the product's weight.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_weight($context = 'view')
        {
        }
        /**
         * Returns the product length.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_length($context = 'view')
        {
        }
        /**
         * Returns the product width.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_width($context = 'view')
        {
        }
        /**
         * Returns the product height.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_height($context = 'view')
        {
        }
        /**
         * Returns formatted dimensions.
         *
         * @param  bool $formatted True by default for legacy support - will be false/not set in future versions to return the array only. Use wc_format_dimensions for formatted versions instead.
         * @return string|array
         */
        public function get_dimensions($formatted = \true)
        {
        }
        /**
         * Get upsell IDs.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return array
         */
        public function get_upsell_ids($context = 'view')
        {
        }
        /**
         * Get cross sell IDs.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return array
         */
        public function get_cross_sell_ids($context = 'view')
        {
        }
        /**
         * Get parent ID.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return int
         */
        public function get_parent_id($context = 'view')
        {
        }
        /**
         * Return if reviews is allowed.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return bool
         */
        public function get_reviews_allowed($context = 'view')
        {
        }
        /**
         * Get purchase note.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_purchase_note($context = 'view')
        {
        }
        /**
         * Returns product attributes.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return array
         */
        public function get_attributes($context = 'view')
        {
        }
        /**
         * Get default attributes.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return array
         */
        public function get_default_attributes($context = 'view')
        {
        }
        /**
         * Get menu order.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return int
         */
        public function get_menu_order($context = 'view')
        {
        }
        /**
         * Get post password.
         *
         * @since  3.6.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return int
         */
        public function get_post_password($context = 'view')
        {
        }
        /**
         * Get category ids.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return array
         */
        public function get_category_ids($context = 'view')
        {
        }
        /**
         * Get tag ids.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return array
         */
        public function get_tag_ids($context = 'view')
        {
        }
        /**
         * Get virtual.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return bool
         */
        public function get_virtual($context = 'view')
        {
        }
        /**
         * Returns the gallery attachment ids.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return array
         */
        public function get_gallery_image_ids($context = 'view')
        {
        }
        /**
         * Get shipping class ID.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return int
         */
        public function get_shipping_class_id($context = 'view')
        {
        }
        /**
         * Get downloads.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return array
         */
        public function get_downloads($context = 'view')
        {
        }
        /**
         * Get download expiry.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return int
         */
        public function get_download_expiry($context = 'view')
        {
        }
        /**
         * Get downloadable.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return bool
         */
        public function get_downloadable($context = 'view')
        {
        }
        /**
         * Get download limit.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return int
         */
        public function get_download_limit($context = 'view')
        {
        }
        /**
         * Get main image ID.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_image_id($context = 'view')
        {
        }
        /**
         * Get rating count.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return array of counts
         */
        public function get_rating_counts($context = 'view')
        {
        }
        /**
         * Get average rating.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return float
         */
        public function get_average_rating($context = 'view')
        {
        }
        /**
         * Get review count.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return int
         */
        public function get_review_count($context = 'view')
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Setters
        |--------------------------------------------------------------------------
        |
        | Functions for setting product data. These should not update anything in the
        | database itself and should only change what is stored in the class
        | object.
        */
        /**
         * Set product name.
         *
         * @since 3.0.0
         * @param string $name Product name.
         */
        public function set_name($name)
        {
        }
        /**
         * Set product slug.
         *
         * @since 3.0.0
         * @param string $slug Product slug.
         */
        public function set_slug($slug)
        {
        }
        /**
         * Set product created date.
         *
         * @since 3.0.0
         * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
         */
        public function set_date_created($date = \null)
        {
        }
        /**
         * Set product modified date.
         *
         * @since 3.0.0
         * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
         */
        public function set_date_modified($date = \null)
        {
        }
        /**
         * Set product status.
         *
         * @since 3.0.0
         * @param string $status Product status.
         */
        public function set_status($status)
        {
        }
        /**
         * Set if the product is featured.
         *
         * @since 3.0.0
         * @param bool|string $featured Whether the product is featured or not.
         */
        public function set_featured($featured)
        {
        }
        /**
         * Set catalog visibility.
         *
         * @since  3.0.0
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         * @param  string $visibility Options: 'hidden', 'visible', 'search' and 'catalog'.
         */
        public function set_catalog_visibility($visibility)
        {
        }
        /**
         * Set product description.
         *
         * @since 3.0.0
         * @param string $description Product description.
         */
        public function set_description($description)
        {
        }
        /**
         * Set product short description.
         *
         * @since 3.0.0
         * @param string $short_description Product short description.
         */
        public function set_short_description($short_description)
        {
        }
        /**
         * Set SKU.
         *
         * @since  3.0.0
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         * @param  string $sku Product SKU.
         */
        public function set_sku($sku)
        {
        }
        /**
         * Set the product's active price.
         *
         * @param string $price Price.
         */
        public function set_price($price)
        {
        }
        /**
         * Set the product's regular price.
         *
         * @since 3.0.0
         * @param string $price Regular price.
         */
        public function set_regular_price($price)
        {
        }
        /**
         * Set the product's sale price.
         *
         * @since 3.0.0
         * @param string $price sale price.
         */
        public function set_sale_price($price)
        {
        }
        /**
         * Set date on sale from.
         *
         * @since 3.0.0
         * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
         */
        public function set_date_on_sale_from($date = \null)
        {
        }
        /**
         * Set date on sale to.
         *
         * @since 3.0.0
         * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
         */
        public function set_date_on_sale_to($date = \null)
        {
        }
        /**
         * Set number total of sales.
         *
         * @since 3.0.0
         * @param int $total Total of sales.
         */
        public function set_total_sales($total)
        {
        }
        /**
         * Set the tax status.
         *
         * @since  3.0.0
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         * @param  string $status Tax status.
         */
        public function set_tax_status($status)
        {
        }
        /**
         * Set the tax class.
         *
         * @since 3.0.0
         * @param string $class Tax class.
         */
        public function set_tax_class($class)
        {
        }
        /**
         * Return an array of valid tax classes
         *
         * @return array valid tax classes
         */
        protected function get_valid_tax_classes()
        {
        }
        /**
         * Set if product manage stock.
         *
         * @since 3.0.0
         * @param bool $manage_stock Whether or not manage stock is enabled.
         */
        public function set_manage_stock($manage_stock)
        {
        }
        /**
         * Set number of items available for sale.
         *
         * @since 3.0.0
         * @param float|null $quantity Stock quantity.
         */
        public function set_stock_quantity($quantity)
        {
        }
        /**
         * Set stock status.
         *
         * @param string $status New status.
         */
        public function set_stock_status($status = 'instock')
        {
        }
        /**
         * Set backorders.
         *
         * @since 3.0.0
         * @param string $backorders Options: 'yes', 'no' or 'notify'.
         */
        public function set_backorders($backorders)
        {
        }
        /**
         * Set low stock amount.
         *
         * @param int|string $amount Empty string if value not set.
         * @since 3.5.0
         */
        public function set_low_stock_amount($amount)
        {
        }
        /**
         * Set if should be sold individually.
         *
         * @since 3.0.0
         * @param bool $sold_individually Whether or not product is sold individually.
         */
        public function set_sold_individually($sold_individually)
        {
        }
        /**
         * Set the product's weight.
         *
         * @since 3.0.0
         * @param float|string $weight Total weight.
         */
        public function set_weight($weight)
        {
        }
        /**
         * Set the product length.
         *
         * @since 3.0.0
         * @param float|string $length Total length.
         */
        public function set_length($length)
        {
        }
        /**
         * Set the product width.
         *
         * @since 3.0.0
         * @param float|string $width Total width.
         */
        public function set_width($width)
        {
        }
        /**
         * Set the product height.
         *
         * @since 3.0.0
         * @param float|string $height Total height.
         */
        public function set_height($height)
        {
        }
        /**
         * Set upsell IDs.
         *
         * @since 3.0.0
         * @param array $upsell_ids IDs from the up-sell products.
         */
        public function set_upsell_ids($upsell_ids)
        {
        }
        /**
         * Set crosssell IDs.
         *
         * @since 3.0.0
         * @param array $cross_sell_ids IDs from the cross-sell products.
         */
        public function set_cross_sell_ids($cross_sell_ids)
        {
        }
        /**
         * Set parent ID.
         *
         * @since 3.0.0
         * @param int $parent_id Product parent ID.
         */
        public function set_parent_id($parent_id)
        {
        }
        /**
         * Set if reviews is allowed.
         *
         * @since 3.0.0
         * @param bool $reviews_allowed Reviews allowed or not.
         */
        public function set_reviews_allowed($reviews_allowed)
        {
        }
        /**
         * Set purchase note.
         *
         * @since 3.0.0
         * @param string $purchase_note Purchase note.
         */
        public function set_purchase_note($purchase_note)
        {
        }
        /**
         * Set product attributes.
         *
         * Attributes are made up of:
         *     id - 0 for product level attributes. ID for global attributes.
         *     name - Attribute name.
         *     options - attribute value or array of term ids/names.
         *     position - integer sort order.
         *     visible - If visible on frontend.
         *     variation - If used for variations.
         * Indexed by unqiue key to allow clearing old ones after a set.
         *
         * @since 3.0.0
         * @param array $raw_attributes Array of WC_Product_Attribute objects.
         */
        public function set_attributes($raw_attributes)
        {
        }
        /**
         * Set default attributes. These will be saved as strings and should map to attribute values.
         *
         * @since 3.0.0
         * @param array $default_attributes List of default attributes.
         */
        public function set_default_attributes($default_attributes)
        {
        }
        /**
         * Set menu order.
         *
         * @since 3.0.0
         * @param int $menu_order Menu order.
         */
        public function set_menu_order($menu_order)
        {
        }
        /**
         * Set post password.
         *
         * @since 3.6.0
         * @param int $post_password Post password.
         */
        public function set_post_password($post_password)
        {
        }
        /**
         * Set the product categories.
         *
         * @since 3.0.0
         * @param array $term_ids List of terms IDs.
         */
        public function set_category_ids($term_ids)
        {
        }
        /**
         * Set the product tags.
         *
         * @since 3.0.0
         * @param array $term_ids List of terms IDs.
         */
        public function set_tag_ids($term_ids)
        {
        }
        /**
         * Set if the product is virtual.
         *
         * @since 3.0.0
         * @param bool|string $virtual Whether product is virtual or not.
         */
        public function set_virtual($virtual)
        {
        }
        /**
         * Set shipping class ID.
         *
         * @since 3.0.0
         * @param int $id Product shipping class id.
         */
        public function set_shipping_class_id($id)
        {
        }
        /**
         * Set if the product is downloadable.
         *
         * @since 3.0.0
         * @param bool|string $downloadable Whether product is downloadable or not.
         */
        public function set_downloadable($downloadable)
        {
        }
        /**
         * Set downloads.
         *
         * @throws WC_Data_Exception If an error relating to one of the downloads is encountered.
         *
         * @param array $downloads_array Array of WC_Product_Download objects or arrays.
         *
         * @since 3.0.0
         */
        public function set_downloads($downloads_array)
        {
        }
        /**
         * Takes an array of downloadable file representations and converts it into an array of
         * WC_Product_Download objects, indexed by download ID.
         *
         * @param array[]|WC_Product_Download[] $downloads Download data to be re-mapped.
         *
         * @return WC_Product_Download[]
         */
        private function build_downloads_map(array $downloads) : array
        {
        }
        /**
         * Set download limit.
         *
         * @since 3.0.0
         * @param int|string $download_limit Product download limit.
         */
        public function set_download_limit($download_limit)
        {
        }
        /**
         * Set download expiry.
         *
         * @since 3.0.0
         * @param int|string $download_expiry Product download expiry.
         */
        public function set_download_expiry($download_expiry)
        {
        }
        /**
         * Set gallery attachment ids.
         *
         * @since 3.0.0
         * @param array $image_ids List of image ids.
         */
        public function set_gallery_image_ids($image_ids)
        {
        }
        /**
         * Set main image ID.
         *
         * @since 3.0.0
         * @param int|string $image_id Product image id.
         */
        public function set_image_id($image_id = '')
        {
        }
        /**
         * Set rating counts. Read only.
         *
         * @param array $counts Product rating counts.
         */
        public function set_rating_counts($counts)
        {
        }
        /**
         * Set average rating. Read only.
         *
         * @param float $average Product average rating.
         */
        public function set_average_rating($average)
        {
        }
        /**
         * Set review count. Read only.
         *
         * @param int $count Product review count.
         */
        public function set_review_count($count)
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Other Methods
        |--------------------------------------------------------------------------
        */
        /**
         * Ensure properties are set correctly before save.
         *
         * @since 3.0.0
         */
        public function validate_props()
        {
        }
        /**
         * Save data (either create or update depending on if we are working on an existing product).
         *
         * @since  3.0.0
         * @return int
         */
        public function save()
        {
        }
        /**
         * Do any extra processing needed before the actual product save
         * (but after triggering the 'woocommerce_before_..._object_save' action)
         *
         * @return mixed A state value that will be passed to after_data_store_save_or_update.
         */
        protected function before_data_store_save_or_update()
        {
        }
        /**
         * Do any extra processing needed after the actual product save
         * (but before triggering the 'woocommerce_after_..._object_save' action)
         *
         * @param mixed $state The state object that was returned by before_data_store_save_or_update.
         */
        protected function after_data_store_save_or_update($state)
        {
        }
        /**
         * Delete the product, set its ID to 0, and return result.
         *
         * @param  bool $force_delete Should the product be deleted permanently.
         * @return bool result
         */
        public function delete($force_delete = \false)
        {
        }
        /**
         * If this is a child product, queue its parent for syncing at the end of the request.
         */
        protected function maybe_defer_product_sync()
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Conditionals
        |--------------------------------------------------------------------------
        */
        /**
         * Check if a product supports a given feature.
         *
         * Product classes should override this to declare support (or lack of support) for a feature.
         *
         * @param  string $feature string The name of a feature to test support for.
         * @return bool True if the product supports the feature, false otherwise.
         * @since  2.5.0
         */
        public function supports($feature)
        {
        }
        /**
         * Returns whether or not the product post exists.
         *
         * @return bool
         */
        public function exists()
        {
        }
        /**
         * Checks the product type.
         *
         * Backwards compatibility with downloadable/virtual.
         *
         * @param  string|array $type Array or string of types.
         * @return bool
         */
        public function is_type($type)
        {
        }
        /**
         * Checks if a product is downloadable.
         *
         * @return bool
         */
        public function is_downloadable()
        {
        }
        /**
         * Checks if a product is virtual (has no shipping).
         *
         * @return bool
         */
        public function is_virtual()
        {
        }
        /**
         * Returns whether or not the product is featured.
         *
         * @return bool
         */
        public function is_featured()
        {
        }
        /**
         * Check if a product is sold individually (no quantities).
         *
         * @return bool
         */
        public function is_sold_individually()
        {
        }
        /**
         * Returns whether or not the product is visible in the catalog.
         *
         * @return bool
         */
        public function is_visible()
        {
        }
        /**
         * Returns whether or not the product is visible in the catalog (doesn't trigger filters).
         *
         * @return bool
         */
        protected function is_visible_core()
        {
        }
        /**
         * Returns false if the product cannot be bought.
         *
         * @return bool
         */
        public function is_purchasable()
        {
        }
        /**
         * Returns whether or not the product is on sale.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return bool
         */
        public function is_on_sale($context = 'view')
        {
        }
        /**
         * Returns whether or not the product has dimensions set.
         *
         * @return bool
         */
        public function has_dimensions()
        {
        }
        /**
         * Returns whether or not the product has weight set.
         *
         * @return bool
         */
        public function has_weight()
        {
        }
        /**
         * Returns whether or not the product can be purchased.
         * This returns true for 'instock' and 'onbackorder' stock statuses.
         *
         * @return bool
         */
        public function is_in_stock()
        {
        }
        /**
         * Checks if a product needs shipping.
         *
         * @return bool
         */
        public function needs_shipping()
        {
        }
        /**
         * Returns whether or not the product is taxable.
         *
         * @return bool
         */
        public function is_taxable()
        {
        }
        /**
         * Returns whether or not the product shipping is taxable.
         *
         * @return bool
         */
        public function is_shipping_taxable()
        {
        }
        /**
         * Returns whether or not the product is stock managed.
         *
         * @return bool
         */
        public function managing_stock()
        {
        }
        /**
         * Returns whether or not the product can be backordered.
         *
         * @return bool
         */
        public function backorders_allowed()
        {
        }
        /**
         * Returns whether or not the product needs to notify the customer on backorder.
         *
         * @return bool
         */
        public function backorders_require_notification()
        {
        }
        /**
         * Check if a product is on backorder.
         *
         * @param  int $qty_in_cart (default: 0).
         * @return bool
         */
        public function is_on_backorder($qty_in_cart = 0)
        {
        }
        /**
         * Returns whether or not the product has enough stock for the order.
         *
         * @param  mixed $quantity Quantity of a product added to an order.
         * @return bool
         */
        public function has_enough_stock($quantity)
        {
        }
        /**
         * Returns whether or not the product has any visible attributes.
         *
         * @return boolean
         */
        public function has_attributes()
        {
        }
        /**
         * Returns whether or not the product has any child product.
         *
         * @return bool
         */
        public function has_child()
        {
        }
        /**
         * Does a child have dimensions?
         *
         * @since  3.0.0
         * @return bool
         */
        public function child_has_dimensions()
        {
        }
        /**
         * Does a child have a weight?
         *
         * @since  3.0.0
         * @return boolean
         */
        public function child_has_weight()
        {
        }
        /**
         * Check if downloadable product has a file attached.
         *
         * @since 1.6.2
         *
         * @param  string $download_id file identifier.
         * @return bool Whether downloadable product has a file attached.
         */
        public function has_file($download_id = '')
        {
        }
        /**
         * Returns whether or not the product has additional options that need
         * selecting before adding to cart.
         *
         * @since  3.0.0
         * @return boolean
         */
        public function has_options()
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Non-CRUD Getters
        |--------------------------------------------------------------------------
        */
        /**
         * Get the product's title. For products this is the product name.
         *
         * @return string
         */
        public function get_title()
        {
        }
        /**
         * Product permalink.
         *
         * @return string
         */
        public function get_permalink()
        {
        }
        /**
         * Returns the children IDs if applicable. Overridden by child classes.
         *
         * @return array of IDs
         */
        public function get_children()
        {
        }
        /**
         * If the stock level comes from another product ID, this should be modified.
         *
         * @since  3.0.0
         * @return int
         */
        public function get_stock_managed_by_id()
        {
        }
        /**
         * Returns the price in html format.
         *
         * @param string $deprecated Deprecated param.
         *
         * @return string
         */
        public function get_price_html($deprecated = '')
        {
        }
        /**
         * Get product name with SKU or ID. Used within admin.
         *
         * @return string Formatted product name
         */
        public function get_formatted_name()
        {
        }
        /**
         * Get min quantity which can be purchased at once.
         *
         * @since  3.0.0
         * @return int
         */
        public function get_min_purchase_quantity()
        {
        }
        /**
         * Get max quantity which can be purchased at once.
         *
         * @since  3.0.0
         * @return int Quantity or -1 if unlimited.
         */
        public function get_max_purchase_quantity()
        {
        }
        /**
         * Get the add to url used mainly in loops.
         *
         * @return string
         */
        public function add_to_cart_url()
        {
        }
        /**
         * Get the add to cart button text for the single page.
         *
         * @return string
         */
        public function single_add_to_cart_text()
        {
        }
        /**
         * Get the add to cart button text.
         *
         * @return string
         */
        public function add_to_cart_text()
        {
        }
        /**
         * Get the add to cart button text description - used in aria tags.
         *
         * @since  3.3.0
         * @return string
         */
        public function add_to_cart_description()
        {
        }
        /**
         * Returns the main product image.
         *
         * @param  string $size (default: 'woocommerce_thumbnail').
         * @param  array  $attr Image attributes.
         * @param  bool   $placeholder True to return $placeholder if no image is found, or false to return an empty string.
         * @return string
         */
        public function get_image($size = 'woocommerce_thumbnail', $attr = array(), $placeholder = \true)
        {
        }
        /**
         * Returns the product shipping class SLUG.
         *
         * @return string
         */
        public function get_shipping_class()
        {
        }
        /**
         * Returns a single product attribute as a string.
         *
         * @param  string $attribute to get.
         * @return string
         */
        public function get_attribute($attribute)
        {
        }
        /**
         * Get the total amount (COUNT) of ratings, or just the count for one rating e.g. number of 5 star ratings.
         *
         * @param  int $value Optional. Rating value to get the count for. By default returns the count of all rating values.
         * @return int
         */
        public function get_rating_count($value = \null)
        {
        }
        /**
         * Get a file by $download_id.
         *
         * @param  string $download_id file identifier.
         * @return array|false if not found
         */
        public function get_file($download_id = '')
        {
        }
        /**
         * Get file download path identified by $download_id.
         *
         * @param  string $download_id file identifier.
         * @return string
         */
        public function get_file_download_path($download_id)
        {
        }
        /**
         * Get the suffix to display after prices > 0.
         *
         * @param  string  $price to calculate, left blank to just use get_price().
         * @param  integer $qty   passed on to get_price_including_tax() or get_price_excluding_tax().
         * @return string
         */
        public function get_price_suffix($price = '', $qty = 1)
        {
        }
        /**
         * Returns the availability of the product.
         *
         * @return string[]
         */
        public function get_availability()
        {
        }
        /**
         * Get availability text based on stock status.
         *
         * @return string
         */
        protected function get_availability_text()
        {
        }
        /**
         * Get availability classname based on stock status.
         *
         * @return string
         */
        protected function get_availability_class()
        {
        }
    }
    /**
     * WC_Widget
     *
     * @package  WooCommerce\Abstracts
     * @version  2.5.0
     * @extends  WP_Widget
     */
    abstract class WC_Widget extends \WP_Widget
    {
        /**
         * CSS class.
         *
         * @var string
         */
        public $widget_cssclass;
        /**
         * Widget description.
         *
         * @var string
         */
        public $widget_description;
        /**
         * Widget ID.
         *
         * @var string
         */
        public $widget_id;
        /**
         * Widget name.
         *
         * @var string
         */
        public $widget_name;
        /**
         * Settings.
         *
         * @var array
         */
        public $settings;
        /**
         * Constructor.
         */
        public function __construct()
        {
        }
        /**
         * Get cached widget.
         *
         * @param  array $args Arguments.
         * @return bool true if the widget is cached otherwise false
         */
        public function get_cached_widget($args)
        {
        }
        /**
         * Cache the widget.
         *
         * @param  array  $args Arguments.
         * @param  string $content Content.
         * @return string the content that was cached
         */
        public function cache_widget($args, $content)
        {
        }
        /**
         * Flush the cache.
         */
        public function flush_widget_cache()
        {
        }
        /**
         * Get this widgets title.
         *
         * @param array $instance Array of instance options.
         * @return string
         */
        protected function get_instance_title($instance)
        {
        }
        /**
         * Output the html at the start of a widget.
         *
         * @param array $args Arguments.
         * @param array $instance Instance.
         */
        public function widget_start($args, $instance)
        {
        }
        /**
         * Output the html at the end of a widget.
         *
         * @param  array $args Arguments.
         */
        public function widget_end($args)
        {
        }
        /**
         * Updates a particular instance of a widget.
         *
         * @see    WP_Widget->update
         * @param  array $new_instance New instance.
         * @param  array $old_instance Old instance.
         * @return array
         */
        public function update($new_instance, $old_instance)
        {
        }
        /**
         * Outputs the settings update form.
         *
         * @see   WP_Widget->form
         *
         * @param array $instance Instance.
         */
        public function form($instance)
        {
        }
        /**
         * Get current page URL with various filtering props supported by WC.
         *
         * @return string
         * @since  3.3.0
         */
        protected function get_current_page_url()
        {
        }
        /**
         * Get widget id plus scheme/protocol to prevent serving mixed content from (persistently) cached widgets.
         *
         * @since  3.4.0
         * @param  string $widget_id Id of the cached widget.
         * @param  string $scheme    Scheme for the widget id.
         * @return string            Widget id including scheme/protocol.
         */
        protected function get_widget_id_for_cache($widget_id, $scheme = '')
        {
        }
    }
    /**
     * Order Class.
     *
     * These are regular WooCommerce orders, which extend the abstract order class.
     */
    class WC_Order extends \WC_Abstract_Order
    {
        /**
         * Stores data about status changes so relevant hooks can be fired.
         *
         * @var bool|array
         */
        protected $status_transition = \false;
        /**
         * Order Data array. This is the core order data exposed in APIs since 3.0.0.
         *
         * @since 3.0.0
         * @var array
         */
        protected $data = array(
            // Abstract order props.
            'parent_id' => 0,
            'status' => '',
            'currency' => '',
            'version' => '',
            'prices_include_tax' => \false,
            'date_created' => \null,
            'date_modified' => \null,
            'discount_total' => 0,
            'discount_tax' => 0,
            'shipping_total' => 0,
            'shipping_tax' => 0,
            'cart_tax' => 0,
            'total' => 0,
            'total_tax' => 0,
            // Order props.
            'customer_id' => 0,
            'order_key' => '',
            'billing' => array('first_name' => '', 'last_name' => '', 'company' => '', 'address_1' => '', 'address_2' => '', 'city' => '', 'state' => '', 'postcode' => '', 'country' => '', 'email' => '', 'phone' => ''),
            'shipping' => array('first_name' => '', 'last_name' => '', 'company' => '', 'address_1' => '', 'address_2' => '', 'city' => '', 'state' => '', 'postcode' => '', 'country' => '', 'phone' => ''),
            'payment_method' => '',
            'payment_method_title' => '',
            'transaction_id' => '',
            'customer_ip_address' => '',
            'customer_user_agent' => '',
            'created_via' => '',
            'customer_note' => '',
            'date_completed' => \null,
            'date_paid' => \null,
            'cart_hash' => '',
            // Operational data.
            'order_stock_reduced' => \false,
            'download_permissions_granted' => \false,
            'new_order_email_sent' => \false,
            'recorded_sales' => \false,
            'recorded_coupon_usage_counts' => \false,
        );
        /**
         * List of properties that were earlier managed by data store. However, since DataStore is a not a stored entity in itself, they used to store data in metadata of the data object.
         * With custom tables, some of these are moved from metadata to their own columns, but existing code will still try to add them to metadata. This array is used to keep track of such properties.
         *
         * Only reason to add a property here is that you are moving properties from DataStore instance to data object. Otherwise, if you are adding a new property, consider adding it to $data array instead.
         *
         * @var array
         */
        protected $legacy_datastore_props = array('_recorded_sales', '_recorded_coupon_usage_counts', '_download_permissions_granted', '_order_stock_reduced', '_new_order_email_sent');
        /**
         * When a payment is complete this function is called.
         *
         * Most of the time this should mark an order as 'processing' so that admin can process/post the items.
         * If the cart contains only downloadable items then the order is 'completed' since the admin needs to take no action.
         * Stock levels are reduced at this point.
         * Sales are also recorded for products.
         * Finally, record the date of payment.
         *
         * @param string $transaction_id Optional transaction id to store in post meta.
         * @return bool success
         */
        public function payment_complete($transaction_id = '')
        {
        }
        /**
         * Gets order total - formatted for display.
         *
         * @param string $tax_display      Type of tax display.
         * @param bool   $display_refunded If should include refunded value.
         *
         * @return string
         */
        public function get_formatted_order_total($tax_display = '', $display_refunded = \true)
        {
        }
        /*
        |--------------------------------------------------------------------------
        | CRUD methods
        |--------------------------------------------------------------------------
        |
        | Methods which create, read, update and delete orders from the database.
        | Written in abstract fashion so that the way orders are stored can be
        | changed more easily in the future.
        |
        | A save method is included for convenience (chooses update or create based
        | on if the order exists yet).
        |
        */
        /**
         * Save data to the database.
         *
         * @since 3.0.0
         * @return int order ID
         */
        public function save()
        {
        }
        /**
         * Log an error about this order is exception is encountered.
         *
         * @param Exception $e Exception object.
         * @param string    $message Message regarding exception thrown.
         * @since 3.7.0
         */
        protected function handle_exception($e, $message = 'Error')
        {
        }
        /**
         * Set order status.
         *
         * @since 3.0.0
         * @param string $new_status    Status to change the order to. No internal wc- prefix is required.
         * @param string $note          Optional note to add.
         * @param bool   $manual_update Is this a manual order status change?.
         * @return array
         */
        public function set_status($new_status, $note = '', $manual_update = \false)
        {
        }
        /**
         * Maybe set date paid.
         *
         * Sets the date paid variable when transitioning to the payment complete
         * order status. This is either processing or completed. This is not filtered
         * to avoid infinite loops e.g. if loading an order via the filter.
         *
         * Date paid is set once in this manner - only when it is not already set.
         * This ensures the data exists even if a gateway does not use the
         * `payment_complete` method.
         *
         * @since 3.0.0
         */
        public function maybe_set_date_paid()
        {
        }
        /**
         * Maybe set date completed.
         *
         * Sets the date completed variable when transitioning to completed status.
         *
         * @since 3.0.0
         */
        protected function maybe_set_date_completed()
        {
        }
        /**
         * Updates status of order immediately.
         *
         * @uses WC_Order::set_status()
         * @param string $new_status    Status to change the order to. No internal wc- prefix is required.
         * @param string $note          Optional note to add.
         * @param bool   $manual        Is this a manual order status change?.
         * @return bool
         */
        public function update_status($new_status, $note = '', $manual = \false)
        {
        }
        /**
         * Handle the status transition.
         */
        protected function status_transition()
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Getters
        |--------------------------------------------------------------------------
        |
        | Methods for getting data from the order object.
        |
        */
        /**
         * Get basic order data in array format.
         *
         * @return array
         */
        public function get_base_data()
        {
        }
        /**
         * Get all class data in array format.
         *
         * @since 3.0.0
         * @return array
         */
        public function get_data()
        {
        }
        /**
         * Expands the shipping and billing information in the changes array.
         */
        public function get_changes()
        {
        }
        /**
         * Gets the order number for display (by default, order ID).
         *
         * @return string
         */
        public function get_order_number()
        {
        }
        /**
         * Get order key.
         *
         * @since  3.0.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_order_key($context = 'view')
        {
        }
        /**
         * Get customer_id.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return int
         */
        public function get_customer_id($context = 'view')
        {
        }
        /**
         * Alias for get_customer_id().
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return int
         */
        public function get_user_id($context = 'view')
        {
        }
        /**
         * Get the user associated with the order. False for guests.
         *
         * @return WP_User|false
         */
        public function get_user()
        {
        }
        /**
         * Gets a prop for a getter method.
         *
         * @since  3.0.0
         * @param  string $prop Name of prop to get.
         * @param  string $address billing or shipping.
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return mixed
         */
        protected function get_address_prop($prop, $address = 'billing', $context = 'view')
        {
        }
        /**
         * Get billing first name.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_billing_first_name($context = 'view')
        {
        }
        /**
         * Get billing last name.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_billing_last_name($context = 'view')
        {
        }
        /**
         * Get billing company.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_billing_company($context = 'view')
        {
        }
        /**
         * Get billing address line 1.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_billing_address_1($context = 'view')
        {
        }
        /**
         * Get billing address line 2.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_billing_address_2($context = 'view')
        {
        }
        /**
         * Get billing city.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_billing_city($context = 'view')
        {
        }
        /**
         * Get billing state.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_billing_state($context = 'view')
        {
        }
        /**
         * Get billing postcode.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_billing_postcode($context = 'view')
        {
        }
        /**
         * Get billing country.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_billing_country($context = 'view')
        {
        }
        /**
         * Get billing email.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_billing_email($context = 'view')
        {
        }
        /**
         * Get billing phone.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_billing_phone($context = 'view')
        {
        }
        /**
         * Get shipping first name.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_shipping_first_name($context = 'view')
        {
        }
        /**
         * Get shipping_last_name.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_shipping_last_name($context = 'view')
        {
        }
        /**
         * Get shipping company.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_shipping_company($context = 'view')
        {
        }
        /**
         * Get shipping address line 1.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_shipping_address_1($context = 'view')
        {
        }
        /**
         * Get shipping address line 2.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_shipping_address_2($context = 'view')
        {
        }
        /**
         * Get shipping city.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_shipping_city($context = 'view')
        {
        }
        /**
         * Get shipping state.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_shipping_state($context = 'view')
        {
        }
        /**
         * Get shipping postcode.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_shipping_postcode($context = 'view')
        {
        }
        /**
         * Get shipping country.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_shipping_country($context = 'view')
        {
        }
        /**
         * Get shipping phone.
         *
         * @since  5.6.0
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_shipping_phone($context = 'view')
        {
        }
        /**
         * Get the payment method.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_payment_method($context = 'view')
        {
        }
        /**
         * Get payment method title.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_payment_method_title($context = 'view')
        {
        }
        /**
         * Get transaction d.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_transaction_id($context = 'view')
        {
        }
        /**
         * Get customer ip address.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_customer_ip_address($context = 'view')
        {
        }
        /**
         * Get customer user agent.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_customer_user_agent($context = 'view')
        {
        }
        /**
         * Get created via.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_created_via($context = 'view')
        {
        }
        /**
         * Get customer note.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_customer_note($context = 'view')
        {
        }
        /**
         * Get date completed.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return WC_DateTime|NULL object if the date is set or null if there is no date.
         */
        public function get_date_completed($context = 'view')
        {
        }
        /**
         * Get date paid.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return WC_DateTime|NULL object if the date is set or null if there is no date.
         */
        public function get_date_paid($context = 'view')
        {
        }
        /**
         * Get cart hash.
         *
         * @param  string $context What the value is for. Valid values are view and edit.
         * @return string
         */
        public function get_cart_hash($context = 'view')
        {
        }
        /**
         * Returns the requested address in raw, non-formatted way.
         * Note: Merges raw data with get_prop data so changes are returned too.
         *
         * @since  2.4.0
         * @param  string $type Billing or shipping. Anything else besides 'billing' will return shipping address.
         * @return array The stored address after filter.
         */
        public function get_address($type = 'billing')
        {
        }
        /**
         * Get a formatted shipping address for the order.
         *
         * @return string
         */
        public function get_shipping_address_map_url()
        {
        }
        /**
         * Get a formatted billing full name.
         *
         * @return string
         */
        public function get_formatted_billing_full_name()
        {
        }
        /**
         * Get a formatted shipping full name.
         *
         * @return string
         */
        public function get_formatted_shipping_full_name()
        {
        }
        /**
         * Get a formatted billing address for the order.
         *
         * @param string $empty_content Content to show if no address is present. @since 3.3.0.
         * @return string
         */
        public function get_formatted_billing_address($empty_content = '')
        {
        }
        /**
         * Get a formatted shipping address for the order.
         *
         * @param string $empty_content Content to show if no address is present. @since 3.3.0.
         * @return string
         */
        public function get_formatted_shipping_address($empty_content = '')
        {
        }
        /**
         * Returns true if the order has a billing address.
         *
         * @since  3.0.4
         * @return boolean
         */
        public function has_billing_address()
        {
        }
        /**
         * Returns true if the order has a shipping address.
         *
         * @since  3.0.4
         * @return boolean
         */
        public function has_shipping_address()
        {
        }
        /**
         * Gets information about whether stock was reduced.
         *
         * @since 7.0.0
         * @param string $context What the value is for. Valid values are view and edit.
         * @return bool
         */
        public function get_order_stock_reduced(string $context = 'view')
        {
        }
        /**
         * Gets information about whether permissions were generated yet.
         *
         * @param string $context What the value is for. Valid values are view and edit.
         *
         * @return bool True if permissions were generated, false otherwise.
         */
        public function get_download_permissions_granted(string $context = 'view')
        {
        }
        /**
         * Whether email have been sent for this order.
         *
         * @param string $context What the value is for. Valid values are view and edit.
         *
         * @return bool
         */
        public function get_new_order_email_sent(string $context = 'view')
        {
        }
        /**
         * Gets information about whether sales were recorded.
         *
         * @param string $context What the value is for. Valid values are view and edit.
         *
         * @return bool True if sales were recorded, false otherwise.
         */
        public function get_recorded_sales(string $context = 'view')
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Setters
        |--------------------------------------------------------------------------
        |
        | Functions for setting order data. These should not update anything in the
        | database itself and should only change what is stored in the class
        | object. However, for backwards compatibility pre 3.0.0 some of these
        | setters may handle both.
        |
        */
        /**
         * Sets a prop for a setter method.
         *
         * @since 3.0.0
         * @param string $prop Name of prop to set.
         * @param string $address Name of address to set. billing or shipping.
         * @param mixed  $value Value of the prop.
         */
        protected function set_address_prop($prop, $address, $value)
        {
        }
        /**
         * Setter for billing address, expects the $address parameter to be key value pairs for individual address props.
         *
         * @param array $address Address to set.
         *
         * @return void
         */
        public function set_billing_address(array $address)
        {
        }
        /**
         * Shortcut for calling set_billing_address.
         *
         * This is useful in scenarios where set_$prop_name is invoked, and since we store the billing address as 'billing' prop in data, it can be called directly.
         *
         * @param array $address Address to set.
         *
         * @return void
         */
        public function set_billing(array $address)
        {
        }
        /**
         * Setter for shipping address, expects the $address parameter to be key value pairs for individual address props.
         *
         * @param array $address Address to set.
         *
         * @return void
         */
        public function set_shipping_address(array $address)
        {
        }
        /**
         * Shortcut for calling set_shipping_address. This is useful in scenarios where set_$prop_name is invoked, and since we store the shipping address as 'shipping' prop in data, it can be called directly.
         *
         * @param array $address Address to set.
         *
         * @return void
         */
        public function set_shipping(array $address)
        {
        }
        /**
         * Set order key.
         *
         * @param string $value Max length 22 chars.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_order_key($value)
        {
        }
        /**
         * Set customer id.
         *
         * @param int $value Customer ID.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_customer_id($value)
        {
        }
        /**
         * Set billing first name.
         *
         * @param string $value Billing first name.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_billing_first_name($value)
        {
        }
        /**
         * Set billing last name.
         *
         * @param string $value Billing last name.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_billing_last_name($value)
        {
        }
        /**
         * Set billing company.
         *
         * @param string $value Billing company.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_billing_company($value)
        {
        }
        /**
         * Set billing address line 1.
         *
         * @param string $value Billing address line 1.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_billing_address_1($value)
        {
        }
        /**
         * Set billing address line 2.
         *
         * @param string $value Billing address line 2.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_billing_address_2($value)
        {
        }
        /**
         * Set billing city.
         *
         * @param string $value Billing city.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_billing_city($value)
        {
        }
        /**
         * Set billing state.
         *
         * @param string $value Billing state.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_billing_state($value)
        {
        }
        /**
         * Set billing postcode.
         *
         * @param string $value Billing postcode.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_billing_postcode($value)
        {
        }
        /**
         * Set billing country.
         *
         * @param string $value Billing country.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_billing_country($value)
        {
        }
        /**
         * Maybe set empty billing email to that of the user who owns the order.
         */
        protected function maybe_set_user_billing_email()
        {
        }
        /**
         * Set billing email.
         *
         * @param string $value Billing email.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_billing_email($value)
        {
        }
        /**
         * Set billing phone.
         *
         * @param string $value Billing phone.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_billing_phone($value)
        {
        }
        /**
         * Set shipping first name.
         *
         * @param string $value Shipping first name.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_shipping_first_name($value)
        {
        }
        /**
         * Set shipping last name.
         *
         * @param string $value Shipping last name.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_shipping_last_name($value)
        {
        }
        /**
         * Set shipping company.
         *
         * @param string $value Shipping company.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_shipping_company($value)
        {
        }
        /**
         * Set shipping address line 1.
         *
         * @param string $value Shipping address line 1.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_shipping_address_1($value)
        {
        }
        /**
         * Set shipping address line 2.
         *
         * @param string $value Shipping address line 2.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_shipping_address_2($value)
        {
        }
        /**
         * Set shipping city.
         *
         * @param string $value Shipping city.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_shipping_city($value)
        {
        }
        /**
         * Set shipping state.
         *
         * @param string $value Shipping state.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_shipping_state($value)
        {
        }
        /**
         * Set shipping postcode.
         *
         * @param string $value Shipping postcode.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_shipping_postcode($value)
        {
        }
        /**
         * Set shipping country.
         *
         * @param string $value Shipping country.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_shipping_country($value)
        {
        }
        /**
         * Set shipping phone.
         *
         * @since 5.6.0
         * @param string $value Shipping phone.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_shipping_phone($value)
        {
        }
        /**
         * Set the payment method.
         *
         * @param string $payment_method Supports WC_Payment_Gateway for bw compatibility with < 3.0.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_payment_method($payment_method = '')
        {
        }
        /**
         * Set payment method title.
         *
         * @param string $value Payment method title.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_payment_method_title($value)
        {
        }
        /**
         * Set transaction id.
         *
         * @param string $value Transaction id.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_transaction_id($value)
        {
        }
        /**
         * Set customer ip address.
         *
         * @param string $value Customer ip address.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_customer_ip_address($value)
        {
        }
        /**
         * Set customer user agent.
         *
         * @param string $value Customer user agent.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_customer_user_agent($value)
        {
        }
        /**
         * Set created via.
         *
         * @param string $value Created via.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_created_via($value)
        {
        }
        /**
         * Set customer note.
         *
         * @param string $value Customer note.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_customer_note($value)
        {
        }
        /**
         * Set date completed.
         *
         * @param  string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_date_completed($date = \null)
        {
        }
        /**
         * Set date paid.
         *
         * @param  string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_date_paid($date = \null)
        {
        }
        /**
         * Set cart hash.
         *
         * @param string $value Cart hash.
         * @throws WC_Data_Exception Throws exception when invalid data is found.
         */
        public function set_cart_hash($value)
        {
        }
        /**
         * Stores information about whether stock was reduced.
         *
         * @param bool|string $value True if stock was reduced, false if not.
         *
         * @return void
         */
        public function set_order_stock_reduced($value)
        {
        }
        /**
         * Stores information about whether permissions were generated yet.
         *
         * @param bool|string $value True if permissions were generated, false if not.
         *
         * @return void
         */
        public function set_download_permissions_granted($value)
        {
        }
        /**
         * Stores information about whether email was sent.
         *
         * @param bool|string $value True if email was sent, false if not.
         *
         * @return void
         */
        public function set_new_order_email_sent($value)
        {
        }
        /**
         * Stores information about whether sales were recorded.
         *
         * @param bool|string $value True if sales were recorded, false if not.
         *
         * @return void
         */
        public function set_recorded_sales($value)
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Conditionals
        |--------------------------------------------------------------------------
        |
        | Checks if a condition is true or false.
        |
        */
        /**
         * Check if an order key is valid.
         *
         * @param string $key Order key.
         * @return bool
         */
        public function key_is_valid($key)
        {
        }
        /**
         * See if order matches cart_hash.
         *
         * @param string $cart_hash Cart hash.
         * @return bool
         */
        public function has_cart_hash($cart_hash = '')
        {
        }
        /**
         * Checks if an order can be edited, specifically for use on the Edit Order screen.
         *
         * @return bool
         */
        public function is_editable()
        {
        }
        /**
         * Returns if an order has been paid for based on the order status.
         *
         * @since 2.5.0
         * @return bool
         */
        public function is_paid()
        {
        }
        /**
         * Checks if product download is permitted.
         *
         * @return bool
         */
        public function is_download_permitted()
        {
        }
        /**
         * Checks if an order needs display the shipping address, based on shipping method.
         *
         * @return bool
         */
        public function needs_shipping_address()
        {
        }
        /**
         * Returns true if the order contains a downloadable product.
         *
         * @return bool
         */
        public function has_downloadable_item()
        {
        }
        /**
         * Get downloads from all line items for this order.
         *
         * @since  3.2.0
         * @return array
         */
        public function get_downloadable_items()
        {
        }
        /**
         * Checks if an order needs payment, based on status and order total.
         *
         * @return bool
         */
        public function needs_payment()
        {
        }
        /**
         * See if the order needs processing before it can be completed.
         *
         * Orders which only contain virtual, downloadable items do not need admin
         * intervention.
         *
         * Uses a transient so these calls are not repeated multiple times, and because
         * once the order is processed this code/transient does not need to persist.
         *
         * @since 3.0.0
         * @return bool
         */
        public function needs_processing()
        {
        }
        /*
        |--------------------------------------------------------------------------
        | URLs and Endpoints
        |--------------------------------------------------------------------------
        */
        /**
         * Generates a URL so that a customer can pay for their (unpaid - pending) order. Pass 'true' for the checkout version which doesn't offer gateway choices.
         *
         * @param  bool $on_checkout If on checkout.
         * @return string
         */
        public function get_checkout_payment_url($on_checkout = \false)
        {
        }
        /**
         * Generates a URL for the thanks page (order received).
         *
         * @return string
         */
        public function get_checkout_order_received_url()
        {
        }
        /**
         * Generates a URL so that a customer can cancel their (unpaid - pending) order.
         *
         * @param string $redirect Redirect URL.
         * @return string
         */
        public function get_cancel_order_url($redirect = '')
        {
        }
        /**
         * Generates a raw (unescaped) cancel-order URL for use by payment gateways.
         *
         * @param string $redirect Redirect URL.
         * @return string The unescaped cancel-order URL.
         */
        public function get_cancel_order_url_raw($redirect = '')
        {
        }
        /**
         * Helper method to return the cancel endpoint.
         *
         * @return string the cancel endpoint; either the cart page or the home page.
         */
        public function get_cancel_endpoint()
        {
        }
        /**
         * Generates a URL to view an order from the my account page.
         *
         * @return string
         */
        public function get_view_order_url()
        {
        }
        /**
         * Get's the URL to edit the order in the backend.
         *
         * @since 3.3.0
         * @return string
         */
        public function get_edit_order_url()
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Order notes.
        |--------------------------------------------------------------------------
        */
        /**
         * Adds a note (comment) to the order. Order must exist.
         *
         * @param  string $note              Note to add.
         * @param  int    $is_customer_note  Is this a note for the customer?.
         * @param  bool   $added_by_user     Was the note added by a user?.
         * @return int                       Comment ID.
         */
        public function add_order_note($note, $is_customer_note = 0, $added_by_user = \false)
        {
        }
        /**
         * Add an order note for status transition
         *
         * @since 3.9.0
         * @uses WC_Order::add_order_note()
         * @param string $note          Note to be added giving status transition from and to details.
         * @param bool   $transition    Details of the status transition.
         * @return int                  Comment ID.
         */
        private function add_status_transition_note($note, $transition)
        {
        }
        /**
         * List order notes (public) for the customer.
         *
         * @return array
         */
        public function get_customer_order_notes()
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Refunds
        |--------------------------------------------------------------------------
        */
        /**
         * Get order refunds.
         *
         * @since 2.2
         * @return array of WC_Order_Refund objects
         */
        public function get_refunds()
        {
        }
        /**
         * Get amount already refunded.
         *
         * @since 2.2
         * @return string
         */
        public function get_total_refunded()
        {
        }
        /**
         * Get the total tax refunded.
         *
         * @since  2.3
         * @return float
         */
        public function get_total_tax_refunded()
        {
        }
        /**
         * Get the total shipping refunded.
         *
         * @since  2.4
         * @return float
         */
        public function get_total_shipping_refunded()
        {
        }
        /**
         * Gets the count of order items of a certain type that have been refunded.
         *
         * @since  2.4.0
         * @param string $item_type Item type.
         * @return string
         */
        public function get_item_count_refunded($item_type = '')
        {
        }
        /**
         * Get the total number of items refunded.
         *
         * @since  2.4.0
         *
         * @param  string $item_type Type of the item we're checking, if not a line_item.
         * @return int
         */
        public function get_total_qty_refunded($item_type = 'line_item')
        {
        }
        /**
         * Get the refunded amount for a line item.
         *
         * @param  int    $item_id   ID of the item we're checking.
         * @param  string $item_type Type of the item we're checking, if not a line_item.
         * @return int
         */
        public function get_qty_refunded_for_item($item_id, $item_type = 'line_item')
        {
        }
        /**
         * Get the refunded amount for a line item.
         *
         * @param  int    $item_id   ID of the item we're checking.
         * @param  string $item_type Type of the item we're checking, if not a line_item.
         * @return int
         */
        public function get_total_refunded_for_item($item_id, $item_type = 'line_item')
        {
        }
        /**
         * Get the refunded tax amount for a line item.
         *
         * @param  int    $item_id   ID of the item we're checking.
         * @param  int    $tax_id    ID of the tax we're checking.
         * @param  string $item_type Type of the item we're checking, if not a line_item.
         * @return double
         */
        public function get_tax_refunded_for_item($item_id, $tax_id, $item_type = 'line_item')
        {
        }
        /**
         * Get total tax refunded by rate ID.
         *
         * @param  int $rate_id Rate ID.
         * @return float
         */
        public function get_total_tax_refunded_by_rate_id($rate_id)
        {
        }
        /**
         * How much money is left to refund?
         *
         * @return string
         */
        public function get_remaining_refund_amount()
        {
        }
        /**
         * How many items are left to refund?
         *
         * @return int
         */
        public function get_remaining_refund_items()
        {
        }
        /**
         * Add total row for the payment method.
         *
         * @param array  $total_rows  Total rows.
         * @param string $tax_display Tax to display.
         */
        protected function add_order_item_totals_payment_method_row(&$total_rows, $tax_display)
        {
        }
        /**
         * Add total row for refunds.
         *
         * @param array  $total_rows  Total rows.
         * @param string $tax_display Tax to display.
         */
        protected function add_order_item_totals_refund_rows(&$total_rows, $tax_display)
        {
        }
        /**
         * Get totals for display on pages and in emails.
         *
         * @param string $tax_display Tax to display.
         * @return array
         */
        public function get_order_item_totals($tax_display = '')
        {
        }
        /**
         * Check if order has been created via admin, checkout, or in another way.
         *
         * @since 4.0.0
         * @param string $modus Way of creating the order to test for.
         * @return bool
         */
        public function is_created_via($modus)
        {
        }
    }
}
namespace Automattic\WooCommerce\Internal\Traits {
    /**
     * This trait allows making private methods of a class accessible from outside.
     * This is useful to define hook handlers with the [$this, 'method'] or [__CLASS__, 'method'] syntax
     * without having to make the method public (and thus having to keep it forever for backwards compatibility).
     *
     * Example:
     *
     * class Foobar {
     *   use AccessiblePrivateMethods;
     *
     *   public function __construct() {
     *     self::add_action('some_action', [$this, 'handle_some_action']);
     *   }
     *
     *   public static function init() {
     *     self::add_filter('some_filter', [__CLASS__, 'handle_some_filter']);
     *   }
     *
     *   private function handle_some_action() {
     *   }
     *
     *   private static function handle_some_filter() {
     *   }
     * }
     *
     * For this to work the callback must be an array and the first element of the array must be either '$this', '__CLASS__',
     * or another instance of the same class; otherwise the method won't be marked as accessible
     * (but the corresponding WordPress 'add_action' and 'add_filter' functions will still be called).
     *
     * No special procedure is needed to remove hooks set up with these methods, the regular 'remove_action'
     * and 'remove_filter' functions provided by WordPress can be used as usual.
     */
    trait AccessiblePrivateMethods
    {
        //phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore
        /**
         * List of instance methods marked as externally accessible.
         *
         * @var array
         */
        private $_accessible_private_methods = array();
        /**
         * List of static methods marked as externally accessible.
         *
         * @var array
         */
        private static $_accessible_static_private_methods = array();
        //phpcs:enable PSR2.Classes.PropertyDeclaration.Underscore
        /**
         * Register a WordPress action.
         * If the callback refers to a private or protected instance method in this class, the method is marked as externally accessible.
         *
         * $callback can be a standard callable, or a string representing the name of a method in this class.
         *
         * @param string          $hook_name       The name of the action to add the callback to.
         * @param callable|string $callback        The callback to be run when the action is called.
         * @param int             $priority        Optional. Used to specify the order in which the functions
         *                                         associated with a particular action are executed.
         *                                         Lower numbers correspond with earlier execution,
         *                                         and functions with the same priority are executed
         *                                         in the order in which they were added to the action. Default 10.
         * @param int             $accepted_args   Optional. The number of arguments the function accepts. Default 1.
         */
        protected static function add_action(string $hook_name, $callback, int $priority = 10, int $accepted_args = 1) : void
        {
        }
        /**
         * Register a WordPress filter.
         * If the callback refers to a private or protected instance method in this class, the method is marked as externally accessible.
         *
         * $callback can be a standard callable, or a string representing the name of a method in this class.
         *
         * @param string          $hook_name       The name of the filter to add the callback to.
         * @param callable|string $callback        The callback to be run when the filter is called.
         * @param int             $priority        Optional. Used to specify the order in which the functions
         *                                         associated with a particular filter are executed.
         *                                         Lower numbers correspond with earlier execution,
         *                                         and functions with the same priority are executed
         *                                         in the order in which they were added to the filter. Default 10.
         * @param int             $accepted_args   Optional. The number of arguments the function accepts. Default 1.
         */
        protected static function add_filter(string $hook_name, $callback, int $priority = 10, int $accepted_args = 1) : void
        {
        }
        /**
         * Do the required processing to a callback before invoking the WordPress 'add_action' or 'add_filter' function.
         *
         * @param callable $callback The callback to process.
         * @return void
         */
        protected static function process_callback_before_hooking($callback) : void
        {
        }
        /**
         * Register a private or protected instance method of this class as externally accessible.
         *
         * @param string $method_name Method name.
         * @return bool True if the method has been marked as externally accessible, false if the method doesn't exist.
         */
        protected function mark_method_as_accessible(string $method_name) : bool
        {
        }
        /**
         * Register a private or protected static method of this class as externally accessible.
         *
         * @param string $method_name Method name.
         * @return bool True if the method has been marked as externally accessible, false if the method doesn't exist.
         */
        protected static function mark_static_method_as_accessible(string $method_name) : bool
        {
        }
        /**
         * Undefined/inaccessible instance method call handler.
         *
         * @param string $name Called method name.
         * @param array  $arguments Called method arguments.
         * @return mixed
         * @throws \Error The called instance method doesn't exist or is private/protected and not marked as externally accessible.
         */
        public function __call($name, $arguments)
        {
        }
        /**
         * Undefined/inaccessible static method call handler.
         *
         * @param string $name Called method name.
         * @param array  $arguments Called method arguments.
         * @return mixed
         * @throws \Error The called static method doesn't exist or is private/protected and not marked as externally accessible.
         */
        public static function __callStatic($name, $arguments)
        {
        }
    }
}
namespace {
    /**
     * Emails class.
     */
    class WC_Emails
    {
        /**
         * Array of email notification classes
         *
         * @var WC_Email[]
         */
        public $emails = array();
        /**
         * The single instance of the class
         *
         * @var WC_Emails
         */
        protected static $_instance = \null;
        /**
         * Background emailer class.
         *
         * @var WC_Background_Emailer
         */
        protected static $background_emailer = \null;
        /**
         * Main WC_Emails Instance.
         *
         * Ensures only one instance of WC_Emails is loaded or can be loaded.
         *
         * @since 2.1
         * @static
         * @return WC_Emails Main instance
         */
        public static function instance()
        {
        }
        /**
         * Cloning is forbidden.
         *
         * @since 2.1
         */
        public function __clone()
        {
        }
        /**
         * Unserializing instances of this class is forbidden.
         *
         * @since 2.1
         */
        public function __wakeup()
        {
        }
        /**
         * Hook in all transactional emails.
         */
        public static function init_transactional_emails()
        {
        }
        /**
         * Queues transactional email so it's not sent in current request if enabled,
         * otherwise falls back to send now.
         *
         * @param mixed ...$args Optional arguments.
         */
        public static function queue_transactional_email(...$args)
        {
        }
        /**
         * Init the mailer instance and call the notifications for the current filter.
         *
         * @internal
         *
         * @param string $filter Filter name.
         * @param array  $args Email args (default: []).
         */
        public static function send_queued_transactional_email($filter = '', $args = array())
        {
        }
        /**
         * Init the mailer instance and call the notifications for the current filter.
         *
         * @internal
         *
         * @param array $args Email args (default: []).
         */
        public static function send_transactional_email($args = array())
        {
        }
        /**
         * Constructor for the email class hooks in all emails that can be sent.
         */
        public function __construct()
        {
        }
        /**
         * Init email classes.
         */
        public function init()
        {
        }
        /**
         * Return the email classes - used in admin to load settings.
         *
         * @return WC_Email[]
         */
        public function get_emails()
        {
        }
        /**
         * Get from name for email.
         *
         * @return string
         */
        public function get_from_name()
        {
        }
        /**
         * Get from email address.
         *
         * @return string
         */
        public function get_from_address()
        {
        }
        /**
         * Get the email header.
         *
         * @param mixed $email_heading Heading for the email.
         */
        public function email_header($email_heading)
        {
        }
        /**
         * Get the email footer.
         */
        public function email_footer()
        {
        }
        /**
         * Replace placeholder text in strings.
         *
         * @since  3.7.0
         * @param  string $string Email footer text.
         * @return string         Email footer text with any replacements done.
         */
        public function replace_placeholders($string)
        {
        }
        /**
         * Filter callback to replace {site_title} in email footer
         *
         * @since  3.3.0
         * @deprecated 3.7.0
         * @param  string $string Email footer text.
         * @return string         Email footer text with any replacements done.
         */
        public function email_footer_replace_site_title($string)
        {
        }
        /**
         * Wraps a message in the woocommerce mail template.
         *
         * @param string $email_heading Heading text.
         * @param string $message       Email message.
         * @param bool   $plain_text    Set true to send as plain text. Default to false.
         *
         * @return string
         */
        public function wrap_message($email_heading, $message, $plain_text = \false)
        {
        }
        /**
         * Send the email.
         *
         * @param mixed  $to          Receiver.
         * @param mixed  $subject     Email subject.
         * @param mixed  $message     Message.
         * @param string $headers     Email headers (default: "Content-Type: text/html\r\n").
         * @param string $attachments Attachments (default: "").
         * @return bool
         */
        public function send($to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = '')
        {
        }
        /**
         * Prepare and send the customer invoice email on demand.
         *
         * @param int|WC_Order $order Order instance or ID.
         */
        public function customer_invoice($order)
        {
        }
        /**
         * Customer new account welcome email.
         *
         * @param int   $customer_id        Customer ID.
         * @param array $new_customer_data  New customer data.
         * @param bool  $password_generated If password is generated.
         */
        public function customer_new_account($customer_id, $new_customer_data = array(), $password_generated = \false)
        {
        }
        /**
         * Show the order details table
         *
         * @param WC_Order $order         Order instance.
         * @param bool     $sent_to_admin If should sent to admin.
         * @param bool     $plain_text    If is plain text email.
         * @param string   $email         Email address.
         */
        public function order_details($order, $sent_to_admin = \false, $plain_text = \false, $email = '')
        {
        }
        /**
         * Show order downloads in a table.
         *
         * @since 3.2.0
         * @param WC_Order $order         Order instance.
         * @param bool     $sent_to_admin If should sent to admin.
         * @param bool     $plain_text    If is plain text email.
         * @param string   $email         Email address.
         */
        public function order_downloads($order, $sent_to_admin = \false, $plain_text = \false, $email = '')
        {
        }
        /**
         * Add order meta to email templates.
         *
         * @param WC_Order $order         Order instance.
         * @param bool     $sent_to_admin If should sent to admin.
         * @param bool     $plain_text    If is plain text email.
         */
        public function order_meta($order, $sent_to_admin = \false, $plain_text = \false)
        {
        }
        /**
         * Is customer detail field valid?
         *
         * @param  array $field Field data to check if is valid.
         * @return boolean
         */
        public function customer_detail_field_is_valid($field)
        {
        }
        /**
         * Allows developers to add additional customer details to templates.
         *
         * In versions prior to 3.2 this was used for notes, phone and email but this data has moved.
         *
         * @param WC_Order $order         Order instance.
         * @param bool     $sent_to_admin If should sent to admin.
         * @param bool     $plain_text    If is plain text email.
         */
        public function customer_details($order, $sent_to_admin = \false, $plain_text = \false)
        {
        }
        /**
         * Get the email addresses.
         *
         * @param WC_Order $order         Order instance.
         * @param bool     $sent_to_admin If should sent to admin.
         * @param bool     $plain_text    If is plain text email.
         */
        public function email_addresses($order, $sent_to_admin = \false, $plain_text = \false)
        {
        }
        /**
         * Renders any additional fields captured during block based checkout.
         *
         * @param WC_Order $order         Order instance.
         * @param bool     $sent_to_admin If email is sent to admin.
         * @param bool     $plain_text    If this is a plain text email.
         */
        public function additional_checkout_fields($order, $sent_to_admin = \false, $plain_text = \false)
        {
        }
        /**
         * Renders any additional address fields captured during block based checkout.
         *
         * @param string   $address_type Address type.
         * @param WC_Order $order         Order instance.
         * @param bool     $sent_to_admin If email is sent to admin.
         * @param bool     $plain_text    If this is a plain text email.
         */
        public function additional_address_fields($address_type, $order, $sent_to_admin = \false, $plain_text = \false)
        {
        }
        /**
         * Get blog name formatted for emails.
         *
         * @return string
         */
        private function get_blogname()
        {
        }
        /**
         * Low stock notification email.
         *
         * @param WC_Product $product Product instance.
         */
        public function low_stock($product)
        {
        }
        /**
         * No stock notification email.
         *
         * @param WC_Product $product Product instance.
         */
        public function no_stock($product)
        {
        }
        /**
         * Backorder notification email.
         *
         * @param array $args Arguments.
         */
        public function backorder($args)
        {
        }
        /**
         * Adds Schema.org markup for order in JSON-LD format.
         *
         * @deprecated 3.0.0
         * @see WC_Structured_Data::generate_order_data()
         *
         * @since 2.6.0
         * @param WC_Order $order         Order instance.
         * @param bool     $sent_to_admin If should sent to admin.
         * @param bool     $plain_text    If is plain text email.
         */
        public function order_schema_markup($order, $sent_to_admin = \false, $plain_text = \false)
        {
        }
    }
    /**
     * Order item class.
     */
    class WC_Order_Item extends \WC_Data implements \ArrayAccess
    {
        /**
         * Legacy cart item values.
         *
         * @deprecated 4.4.0 For legacy actions.
         * @var array
         */
        public $legacy_values;
        /**
         * Legacy cart item keys.
         *
         * @deprecated 4.4.0 For legacy actions.
         * @var string
         */
        public $legacy_cart_item_key;
        /**
         * Order Data array. This is the core order data exposed in APIs since 3.0.0.
         *
         * @since 3.0.0
         * @var array
         */
        protected $data = array('order_id' => 0, 'name' => '');
        /**
         * Stores meta in cache for future reads.
         * A group must be set to to enable caching.
         *
         * @var string
         */
        protected $cache_group = 'order-items';
        /**
         * Meta type. This should match up with
         * the types available at https://developer.wordpress.org/reference/functions/add_metadata/.
         * WP defines 'post', 'user', 'comment', and 'term'.
         *
         * @var string
         */
        protected $meta_type = 'order_item';
        /**
         * This is the name of this object type.
         *
         * @var string
         */
        protected $object_type = 'order_item';
        /**
         * Legacy package key.
         *
         * @deprecated 4.4.0 For legacy actions.
         * @var string
         */
        public $legacy_package_key;
        /**
         * Constructor.
         *
         * @param int|object|array $item ID to load from the DB, or WC_Order_Item object.
         */
        public function __construct($item = 0)
        {
        }
        /**
         * Merge changes with data and clear.
         * Overrides WC_Data::apply_changes.
         * array_replace_recursive does not work well for order items because it merges taxes instead
         * of replacing them.
         *
         * @since 3.2.0
         */
        public function apply_changes()
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Getters
        |--------------------------------------------------------------------------
        */
        /**
         * Get order ID this meta belongs to.
         *
         * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
         * @return int
         */
        public function get_order_id($context = 'view')
        {
        }
        /**
         * Get order item name.
         *
         * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
         * @return string
         */
        public function get_name($context = 'view')
        {
        }
        /**
         * Get order item type. Overridden by child classes.
         *
         * @return string
         */
        public function get_type()
        {
        }
        /**
         * Get quantity.
         *
         * @return int
         */
        public function get_quantity()
        {
        }
        /**
         * Get tax status.
         *
         * @return string
         */
        public function get_tax_status()
        {
        }
        /**
         * Get tax class.
         *
         * @return string
         */
        public function get_tax_class()
        {
        }
        /**
         * Get parent order object.
         *
         * @return WC_Order
         */
        public function get_order()
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Setters
        |--------------------------------------------------------------------------
        */
        /**
         * Set order ID.
         *
         * @param int $value Order ID.
         */
        public function set_order_id($value)
        {
        }
        /**
         * Set order item name.
         *
         * @param string $value Item name.
         */
        public function set_name($value)
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Other Methods
        |--------------------------------------------------------------------------
        */
        /**
         * Type checking.
         *
         * @param  string|array $type Type.
         * @return boolean
         */
        public function is_type($type)
        {
        }
        /**
         * Calculate item taxes.
         *
         * @since  3.2.0
         * @param  array $calculate_tax_for Location data to get taxes for. Required.
         * @return bool  True if taxes were calculated.
         */
        public function calculate_taxes($calculate_tax_for = array())
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Meta Data Handling
        |--------------------------------------------------------------------------
        */
        /**
         * Wrapper for get_formatted_meta_data that includes all metadata by default. See https://github.com/woocommerce/woocommerce/pull/30948
         *
         * @param string $hideprefix  Meta data prefix, (default: _).
         * @param bool   $include_all Include all meta data, this stop skip items with values already in the product name.
         * @return array
         */
        public function get_all_formatted_meta_data($hideprefix = '_', $include_all = \true)
        {
        }
        /**
         * Expands things like term slugs before return.
         *
         * @param string $hideprefix  Meta data prefix, (default: _).
         * @param bool   $include_all Include all meta data, this stop skip items with values already in the product name.
         * @return array
         */
        public function get_formatted_meta_data($hideprefix = '_', $include_all = \false)
        {
        }
        /*
        |--------------------------------------------------------------------------
        | Array Access Methods
        |--------------------------------------------------------------------------
        |
        | For backwards compatibility with legacy arrays.
        |
        */
        /**
         * OffsetSet for ArrayAccess.
         *
         * @param string $offset Offset.
         * @param mixed  $value  Value.
         */
        #[\ReturnTypeWillChange]
        public function offsetSet($offset, $value)
        {
        }
        /**
         * OffsetUnset for ArrayAccess.
         *
         * @param string $offset Offset.
         */
        #[\ReturnTypeWillChange]
        public function offsetUnset($offset)
        {
        }
        /**
         * OffsetExists for ArrayAccess.
         *
         * @param string $offset Offset.
         * @return bool
         */
        #[\ReturnTypeWillChange]
        public function offsetExists($offset)
        {
        }
        /**
         * OffsetGet for ArrayAccess.
         *
         * @param string $offset Offset.
         * @return mixed
         */
        #[\ReturnTypeWillChange]
        public function offsetGet($offset)
        {
        }
    }
    /**
     * WC_Query Class.
     */
    class WC_Query
    {
        use \Automattic\WooCommerce\Internal\Traits\AccessiblePrivateMethods;
        /**
         * Query vars to add to wp.
         *
         * @var array
         */
        public $query_vars = array();
        /**
         * Reference to the main product query on the page.
         *
         * @var WP_Query
         */
        private static $product_query;
        /**
         * Stores chosen attributes.
         *
         * @var array
         */
        private static $chosen_attributes;
        /**
         * The instance of the class that helps filtering with the product attributes lookup table.
         *
         * @var Filterer
         */
        private $filterer;
        /**
         * Constructor for the query class. Hooks in methods.
         */
        public function __construct()
        {
        }
        /**
         * Reset the chosen attributes so that get_layered_nav_chosen_attributes will get them from the query again.
         */
        public static function reset_chosen_attributes()
        {
        }
        /**
         * Get any errors from querystring.
         */
        public function get_errors()
        {
        }
        /**
         * Init query vars by loading options.
         */
        public function init_query_vars()
        {
        }
        /**
         * Get page title for an endpoint.
         *
         * @param string $endpoint Endpoint key.
         * @param string $action Optional action or variation within the endpoint.
         *
         * @since 2.3.0
         * @since 4.6.0 Added $action parameter.
         * @return string The page title.
         */
        public function get_endpoint_title($endpoint, $action = '')
        {
        }
        /**
         * Endpoint mask describing the places the endpoint should be added.
         *
         * @since 2.6.2
         * @return int
         */
        public function get_endpoints_mask()
        {
        }
        /**
         * Add endpoints for query vars.
         */
        public function add_endpoints()
        {
        }
        /**
         * Add query vars.
         *
         * @param array $vars Query vars.
         * @return array
         */
        public function add_query_vars($vars)
        {
        }
        /**
         * Get query vars.
         *
         * @return array
         */
        public function get_query_vars()
        {
        }
        /**
         * Get query current active query var.
         *
         * @return string
         */
        public function get_current_endpoint()
        {
        }
        /**
         * Parse the request and look for query vars - endpoints may not be supported.
         */
        public function parse_request()
        {
        }
        /**
         * Are we currently on the front page?
         *
         * @param WP_Query $q Query instance.
         * @return bool
         */
        private function is_showing_page_on_front($q)
        {
        }
        /**
         * Is the front page a page we define?
         *
         * @param int $page_id Page ID.
         * @return bool
         */
        private function page_on_front_is($page_id)
        {
        }
        /**
         * Hook into pre_get_posts to do the main product query.
         *
         * @param WP_Query $q Query instance.
         */
        public function pre_get_posts($q)
        {
        }
        /**
         * Handler for the 'the_posts' WP filter.
         *
         * @param array    $posts Posts from WP Query.
         * @param WP_Query $query Current query.
         *
         * @return array
         */
        public function handle_get_posts($posts, $query)
        {
        }
        /**
         * Pre_get_posts above may adjust the main query to add WooCommerce logic. When this query is done, we need to ensure
         * all custom filters are removed.
         *
         * This is done here during the_posts filter. The input is not changed.
         *
         * @param array $posts Posts from WP Query.
         * @return array
         */
        public function remove_product_query_filters($posts)
        {
        }
        /**
         * This function used to be hooked to found_posts and adjust the posts count when the filtering by attribute
         * widget was used and variable products were present. Now it isn't hooked anymore and does nothing but return
         * the input unchanged, since the pull request in which it was introduced has been reverted.
         *
         * @since 4.4.0
         * @param int      $count Original posts count, as supplied by the found_posts filter.
         * @param WP_Query $query The current WP_Query object.
         *
         * @return int Adjusted posts count.
         */
        public function adjust_posts_count($count, $query)
        {
        }
        /**
         * Instance version of get_layered_nav_chosen_attributes, needed for unit tests.
         *
         * @return array
         */
        protected function get_layered_nav_chosen_attributes_inst()
        {
        }
        /**
         * Get the posts (or the ids of the posts) found in the current WP loop.
         *
         * @return array Array of posts or post ids.
         */
        protected function get_current_posts()
        {
        }
        /**
         * WP SEO meta description.
         *
         * Hooked into wpseo_ hook already, so no need for function_exist.
         *
         * @return string
         */
        public function wpseo_metadesc()
        {
        }
        /**
         * WP SEO meta key.
         *
         * Hooked into wpseo_ hook already, so no need for function_exist.
         *
         * @return string
         */
        public function wpseo_metakey()
        {
        }
        /**
         * Query the products, applying sorting/ordering etc.
         * This applies to the main WordPress loop.
         *
         * @param WP_Query $q Query instance.
         */
        public function product_query($q)
        {
        }
        /**
         * Add extra clauses to the product query.
         *
         * @param array    $args Product query clauses.
         * @param WP_Query $wp_query The current product query.
         * @return array The updated product query clauses array.
         */
        private function product_query_post_clauses($args, $wp_query)
        {
        }
        /**
         * Remove the query.
         */
        public function remove_product_query()
        {
        }
        /**
         * Remove ordering queries.
         */
        public function remove_ordering_args()
        {
        }
        /**
         * Returns an array of arguments for ordering products based on the selected values.
         *
         * @param string $orderby Order by param.
         * @param string $order Order param.
         * @return array
         */
        public function get_catalog_ordering_args($orderby = '', $order = '')
        {
        }
        /**
         * Custom query used to filter products by price.
         *
         * @since 3.6.0
         *
         * @param array    $args Query args.
         * @param WP_Query $wp_query WP_Query object.
         *
         * @return array
         */
        public function price_filter_post_clauses($args, $wp_query)
        {
        }
        /**
         * Handle numeric price sorting.
         *
         * @param array $args Query args.
         * @return array
         */
        public function order_by_price_asc_post_clauses($args)
        {
        }
        /**
         * Handle numeric price sorting.
         *
         * @param array $args Query args.
         * @return array
         */
        public function order_by_price_desc_post_clauses($args)
        {
        }
        /**
         * WP Core does not let us change the sort direction for individual orderby params - https://core.trac.wordpress.org/ticket/17065.
         *
         * This lets us sort by meta value desc, and have a second orderby param.
         *
         * @param array $args Query args.
         * @return array
         */
        public function order_by_popularity_post_clauses($args)
        {
        }
        /**
         * Order by rating post clauses.
         *
         * @param array $args Query args.
         * @return array
         */
        public function order_by_rating_post_clauses($args)
        {
        }
        /**
         * Join wc_product_meta_lookup to posts if not already joined.
         *
         * @param string $sql SQL join.
         * @return string
         */
        private function append_product_sorting_table_join($sql)
        {
        }
        /**
         * Appends meta queries to an array.
         *
         * @param  array $meta_query Meta query.
         * @param  bool  $main_query If is main query.
         * @return array
         */
        public function get_meta_query($meta_query = array(), $main_query = \false)
        {
        }
        /**
         * Appends tax queries to an array.
         *
         * @param  array $tax_query  Tax query.
         * @param  bool  $main_query If is main query.
         * @return array
         */
        public function get_tax_query($tax_query = array(), $main_query = \false)
        {
        }
        /**
         * Get the main query which product queries ran against.
         *
         * @return WP_Query
         */
        public static function get_main_query()
        {
        }
        /**
         * Get the tax query which was used by the main query.
         *
         * @return array
         */
        public static function get_main_tax_query()
        {
        }
        /**
         * Get the meta query which was used by the main query.
         *
         * @return array
         */
        public static function get_main_meta_query()
        {
        }
        /**
         * Based on WP_Query::parse_search
         */
        public static function get_main_search_query_sql()
        {
        }
        /**
         * Get an array of attributes and terms selected with the layered nav widget.
         *
         * @return array
         */
        public static function get_layered_nav_chosen_attributes()
        {
        }
        /**
         * Remove the add-to-cart param from pagination urls.
         *
         * @param string $url URL.
         * @return string
         */
        public function remove_add_to_cart_pagination($url)
        {
        }
        /**
         * Return a meta query for filtering by rating.
         *
         * @deprecated 3.0.0 Replaced with taxonomy.
         * @return array
         */
        public function rating_filter_meta_query()
        {
        }
        /**
         * Returns a meta query to handle product visibility.
         *
         * @deprecated 3.0.0 Replaced with taxonomy.
         * @param string $compare (default: 'IN').
         * @return array
         */
        public function visibility_meta_query($compare = 'IN')
        {
        }
        /**
         * Returns a meta query to handle product stock status.
         *
         * @deprecated 3.0.0 Replaced with taxonomy.
         * @param string $status (default: 'instock').
         * @return array
         */
        public function stock_status_meta_query($status = 'instock')
        {
        }
        /**
         * Layered nav init.
         *
         * @deprecated 2.6.0
         */
        public function layered_nav_init()
        {
        }
        /**
         * Get an unpaginated list all product IDs (both filtered and unfiltered). Makes use of transients.
         *
         * @deprecated 2.6.0 due to performance concerns
         */
        public function get_products_in_view()
        {
        }
        /**
         * Layered Nav post filter.
         *
         * @deprecated 2.6.0 due to performance concerns
         *
         * @param mixed $deprecated Deprecated.
         */
        public function layered_nav_query($deprecated)
        {
        }
        /**
         * Search post excerpt.
         *
         * @param string $where Where clause.
         *
         * @deprecated 3.2.0 - Not needed anymore since WordPress 4.5.
         */
        public function search_post_excerpt($where = '')
        {
        }
        /**
         * Remove the posts_where filter.
         *
         * @deprecated 3.2.0 - Nothing to remove anymore because search_post_excerpt() is deprecated.
         */
        public function remove_posts_where()
        {
        }
    }
    /**
     * Main WooCommerce Class.
     *
     * @class WooCommerce
     */
    final class WooCommerce
    {
        /**
         * WooCommerce version.
         *
         * @var string
         */
        public $version = '7.2.2';
        /**
         * WooCommerce Schema version.
         *
         * @since 4.3 started with version string 430.
         *
         * @var string
         */
        public $db_version = '430';
        /**
         * The single instance of the class.
         *
         * @var WooCommerce
         * @since 2.1
         */
        protected static $_instance = \null;
        /**
         * Session instance.
         *
         * @var WC_Session|WC_Session_Handler
         */
        public $session = \null;
        /**
         * Query instance.
         *
         * @var WC_Query
         */
        public $query = \null;
        /**
         * Product factory instance.
         *
         * @var WC_Product_Factory
         */
        public $product_factory = \null;
        /**
         * Countries instance.
         *
         * @var WC_Countries
         */
        public $countries = \null;
        /**
         * Integrations instance.
         *
         * @var WC_Integrations
         */
        public $integrations = \null;
        /**
         * Cart instance.
         *
         * @var WC_Cart
         */
        public $cart = \null;
        /**
         * Customer instance.
         *
         * @var WC_Customer
         */
        public $customer = \null;
        /**
         * Order factory instance.
         *
         * @var WC_Order_Factory
         */
        public $order_factory = \null;
        /**
         * Structured data instance.
         *
         * @var WC_Structured_Data
         */
        public $structured_data = \null;
        /**
         * Array of deprecated hook handlers.
         *
         * @var array of WC_Deprecated_Hooks
         */
        public $deprecated_hook_handlers = array();
        /**
         * Main WooCommerce Instance.
         *
         * Ensures only one instance of WooCommerce is loaded or can be loaded.
         *
         * @since 2.1
         * @static
         * @see WC()
         * @return WooCommerce - Main instance.
         */
        public static function instance()
        {
        }
        /**
         * Cloning is forbidden.
         *
         * @since 2.1
         */
        public function __clone()
        {
        }
        /**
         * Unserializing instances of this class is forbidden.
         *
         * @since 2.1
         */
        public function __wakeup()
        {
        }
        /**
         * Auto-load in-accessible properties on demand.
         *
         * @param mixed $key Key name.
         * @return mixed
         */
        public function __get($key)
        {
        }
        /**
         * WooCommerce Constructor.
         */
        public function __construct()
        {
        }
        /**
         * When WP has loaded all plugins, trigger the `woocommerce_loaded` hook.
         *
         * This ensures `woocommerce_loaded` is called only after all other plugins
         * are loaded, to avoid issues caused by plugin directory naming changing
         * the load order. See #21524 for details.
         *
         * @since 3.6.0
         */
        public function on_plugins_loaded()
        {
        }
        /**
         * Hook into actions and filters.
         *
         * @since 2.3
         */
        private function init_hooks()
        {
        }
        /**
         * Add woocommerce_inbox_variant for the Remote Inbox Notification.
         *
         * P2 post can be found at https://wp.me/paJDYF-1uJ.
         */
        public function add_woocommerce_inbox_variant()
        {
        }
        /**
         * Ensures fatal errors are logged so they can be picked up in the status report.
         *
         * @since 3.2.0
         */
        public function log_errors()
        {
        }
        /**
         * Define WC Constants.
         */
        private function define_constants()
        {
        }
        /**
         * Register custom tables within $wpdb object.
         */
        private function define_tables()
        {
        }
        /**
         * Define constant if not already set.
         *
         * @param string      $name  Constant name.
         * @param string|bool $value Constant value.
         */
        private function define($name, $value)
        {
        }
        /**
         * Returns true if the request is a non-legacy REST API request.
         *
         * Legacy REST requests should still run some extra code for backwards compatibility.
         *
         * @return bool
         */
        public function is_rest_api_request()
        {
        }
        /**
         * Load REST API.
         */
        public function load_rest_api()
        {
        }
        /**
         * What type of request is this?
         *
         * @param  string $type admin, ajax, cron or frontend.
         * @return bool
         */
        private function is_request($type)
        {
        }
        /**
         * Include required core files used in admin and on the frontend.
         */
        public function includes()
        {
        }
        /**
         * Include classes for theme support.
         *
         * @since 3.3.0
         */
        private function theme_support_includes()
        {
        }
        /**
         * Include required frontend files.
         */
        public function frontend_includes()
        {
        }
        /**
         * Function used to Init WooCommerce Template Functions - This makes them pluggable by plugins and themes.
         */
        public function include_template_functions()
        {
        }
        /**
         * Init WooCommerce when WordPress Initialises.
         */
        public function init()
        {
        }
        /**
         * Load Localisation files.
         *
         * Note: the first-loaded translation file overrides any following ones if the same translation is present.
         *
         * Locales found in:
         *      - WP_LANG_DIR/woocommerce/woocommerce-LOCALE.mo
         *      - WP_LANG_DIR/plugins/woocommerce-LOCALE.mo
         */
        public function load_plugin_textdomain()
        {
        }
        /**
         * Ensure theme and server variable compatibility and setup image sizes.
         */
        public function setup_environment()
        {
        }
        /**
         * Ensure post thumbnail support is turned on.
         */
        private function add_thumbnail_support()
        {
        }
        /**
         * Add WC Image sizes to WP.
         *
         * As of 3.3, image sizes can be registered via themes using add_theme_support for woocommerce
         * and defining an array of args. If these are not defined, we will use defaults. This is
         * handled in wc_get_image_size function.
         *
         * 3.3 sizes:
         *
         * woocommerce_thumbnail - Used in product listings. We assume these work for a 3 column grid layout.
         * woocommerce_single - Used on single product pages for the main image.
         *
         * @since 2.3
         */
        public function add_image_sizes()
        {
        }
        /**
         * Get the plugin url.
         *
         * @return string
         */
        public function plugin_url()
        {
        }
        /**
         * Get the plugin path.
         *
         * @return string
         */
        public function plugin_path()
        {
        }
        /**
         * Get the template path.
         *
         * @return string
         */
        public function template_path()
        {
        }
        /**
         * Get Ajax URL.
         *
         * @return string
         */
        public function ajax_url()
        {
        }
        /**
         * Return the WC API URL for a given request.
         *
         * @param string    $request Requested endpoint.
         * @param bool|null $ssl     If should use SSL, null if should auto detect. Default: null.
         * @return string
         */
        public function api_request_url($request, $ssl = \null)
        {
        }
        /**
         * Load & enqueue active webhooks.
         *
         * @since 2.2
         */
        private function load_webhooks()
        {
        }
        /**
         * Initialize the customer and cart objects and setup customer saving on shutdown.
         *
         * @since 3.6.4
         * @return void
         */
        public function initialize_cart()
        {
        }
        /**
         * Initialize the session class.
         *
         * @since 3.6.4
         * @return void
         */
        public function initialize_session()
        {
        }
        /**
         * Set tablenames inside WPDB object.
         */
        public function wpdb_table_fix()
        {
        }
        /**
         * Ran when any plugin is activated.
         *
         * @since 3.6.0
         * @param string $filename The filename of the activated plugin.
         */
        public function activated_plugin($filename)
        {
        }
        /**
         * Ran when any plugin is deactivated.
         *
         * @since 3.6.0
         * @param string $filename The filename of the deactivated plugin.
         */
        public function deactivated_plugin($filename)
        {
        }
        /**
         * Get queue instance.
         *
         * @return WC_Queue_Interface
         */
        public function queue()
        {
        }
        /**
         * Get Checkout Class.
         *
         * @return WC_Checkout
         */
        public function checkout()
        {
        }
        /**
         * Get gateways class.
         *
         * @return WC_Payment_Gateways
         */
        public function payment_gateways()
        {
        }
        /**
         * Get shipping class.
         *
         * @return WC_Shipping
         */
        public function shipping()
        {
        }
        /**
         * Email Class.
         *
         * @return WC_Emails
         */
        public function mailer()
        {
        }
        /**
         * Check if plugin assets are built and minified
         *
         * @return bool
         */
        public function build_dependencies_satisfied()
        {
        }
        /**
         * Output a admin notice when build dependencies not met.
         *
         * @return void
         */
        public function build_dependencies_notice()
        {
        }
        /**
         * Is the WooCommerce Admin actively included in the WooCommerce core?
         * Based on presence of a basic WC Admin function.
         *
         * @return boolean
         */
        public function is_wc_admin_active()
        {
        }
        /**
         * Call a user function. This should be used to execute any non-idempotent function, especially
         * those in the `includes` directory or provided by WordPress.
         *
         * This method can be useful for unit tests, since functions called using this method
         * can be easily mocked by using WC_Unit_Test_Case::register_legacy_proxy_function_mocks.
         *
         * @param string $function_name The function to execute.
         * @param mixed  ...$parameters The parameters to pass to the function.
         *
         * @return mixed The result from the function.
         *
         * @since 4.4
         */
        public function call_function($function_name, ...$parameters)
        {
        }
        /**
         * Call a static method in a class. This should be used to execute any non-idempotent method in classes
         * from the `includes` directory.
         *
         * This method can be useful for unit tests, since methods called using this method
         * can be easily mocked by using WC_Unit_Test_Case::register_legacy_proxy_static_mocks.
         *
         * @param string $class_name The name of the class containing the method.
         * @param string $method_name The name of the method.
         * @param mixed  ...$parameters The parameters to pass to the method.
         *
         * @return mixed The result from the method.
         *
         * @since 4.4
         */
        public function call_static($class_name, $method_name, ...$parameters)
        {
        }
        /**
         * Gets an instance of a given legacy class.
         * This must not be used to get instances of classes in the `src` directory.
         *
         * This method can be useful for unit tests, since objects obtained using this method
         * can be easily mocked by using WC_Unit_Test_Case::register_legacy_proxy_class_mocks.
         *
         * @param string $class_name The name of the class to get an instance for.
         * @param mixed  ...$args Parameters to be passed to the class constructor or to the appropriate internal 'get_instance_of_' method.
         *
         * @return object The instance of the class.
         * @throws \Exception The requested class belongs to the `src` directory, or there was an error creating an instance of the class.
         *
         * @since 4.4
         */
        public function get_instance_of(string $class_name, ...$args)
        {
        }
        /**
         * Gets the value of a global.
         *
         * @param string $global_name The name of the global to get the value for.
         * @return mixed The value of the global.
         */
        public function get_global(string $global_name)
        {
        }
    }
    /**
     * Email Class
     *
     * WooCommerce Email Class which is extended by specific email template classes to add emails to WooCommerce
     *
     * @class       WC_Email
     * @version     2.5.0
     * @package     WooCommerce\Classes\Emails
     * @extends     WC_Settings_API
     */
    class WC_Email extends \WC_Settings_API
    {
        /**
         * Email method ID.
         *
         * @var String
         */
        public $id;
        /**
         * Email method title.
         *
         * @var string
         */
        public $title;
        /**
         * 'yes' if the method is enabled.
         *
         * @var string yes, no
         */
        public $enabled;
        /**
         * Description for the email.
         *
         * @var string
         */
        public $description;
        /**
         * Default heading.
         *
         * Supported for backwards compatibility but we recommend overloading the
         * get_default_x methods instead so localization can be done when needed.
         *
         * @var string
         */
        public $heading = '';
        /**
         * Default subject.
         *
         * Supported for backwards compatibility but we recommend overloading the
         * get_default_x methods instead so localization can be done when needed.
         *
         * @var string
         */
        public $subject = '';
        /**
         * Plain text template path.
         *
         * @var string
         */
        public $template_plain;
        /**
         * HTML template path.
         *
         * @var string
         */
        public $template_html;
        /**
         * Template path.
         *
         * @var string
         */
        public $template_base;
        /**
         * Recipients for the email.
         *
         * @var string
         */
        public $recipient;
        /**
         * Object this email is for, for example a customer, product, or email.
         *
         * @var object|bool
         */
        public $object;
        /**
         * Mime boundary (for multipart emails).
         *
         * @var string
         */
        public $mime_boundary;
        /**
         * Mime boundary header (for multipart emails).
         *
         * @var string
         */
        public $mime_boundary_header;
        /**
         * True when email is being sent.
         *
         * @var bool
         */
        public $sending;
        /**
         * True when the email notification is sent manually only.
         *
         * @var bool
         */
        protected $manual = \false;
        /**
         * True when the email notification is sent to customers.
         *
         * @var bool
         */
        protected $customer_email = \false;
        /**
         *  List of preg* regular expression patterns to search for,
         *  used in conjunction with $plain_replace.
         *  https://raw.github.com/ushahidi/wp-silcc/master/class.html2text.inc
         *
         *  @var array $plain_search
         *  @see $plain_replace
         */
        public $plain_search = array(
            "/\r/",
            // Non-legal carriage return.
            '/&(nbsp|#0*160);/i',
            // Non-breaking space.
            '/&(quot|rdquo|ldquo|#0*8220|#0*8221|#0*147|#0*148);/i',
            // Double quotes.
            '/&(apos|rsquo|lsquo|#0*8216|#0*8217);/i',
            // Single quotes.
            '/&gt;/i',
            // Greater-than.
            '/&lt;/i',
            // Less-than.
            '/&#0*38;/i',
            // Ampersand.
            '/&amp;/i',
            // Ampersand.
            '/&(copy|#0*169);/i',
            // Copyright.
            '/&(trade|#0*8482|#0*153);/i',
            // Trademark.
            '/&(reg|#0*174);/i',
            // Registered.
            '/&(mdash|#0*151|#0*8212);/i',
            // mdash.
            '/&(ndash|minus|#0*8211|#0*8722);/i',
            // ndash.
            '/&(bull|#0*149|#0*8226);/i',
            // Bullet.
            '/&(pound|#0*163);/i',
            // Pound sign.
            '/&(euro|#0*8364);/i',
            // Euro sign.
            '/&(dollar|#0*36);/i',
            // Dollar sign.
            '/&[^&\\s;]+;/i',
            // Unknown/unhandled entities.
            '/[ ]{2,}/',
        );
        /**
         *  List of pattern replacements corresponding to patterns searched.
         *
         *  @var array $plain_replace
         *  @see $plain_search
         */
        public $plain_replace = array(
            '',
            // Non-legal carriage return.
            ' ',
            // Non-breaking space.
            '"',
            // Double quotes.
            "'",
            // Single quotes.
            '>',
            // Greater-than.
            '<',
            // Less-than.
            '&',
            // Ampersand.
            '&',
            // Ampersand.
            '(c)',
            // Copyright.
            '(tm)',
            // Trademark.
            '(R)',
            // Registered.
            '--',
            // mdash.
            '-',
            // ndash.
            '*',
            // Bullet.
            '£',
            // Pound sign.
            'EUR',
            // Euro sign. € ?.
            '$',
            // Dollar sign.
            '',
            // Unknown/unhandled entities.
            ' ',
        );
        /**
         * Strings to find/replace in subjects/headings.
         *
         * @var array
         */
        protected $placeholders = array();
        /**
         * Strings to find in subjects/headings.
         *
         * @deprecated 3.2.0 in favour of placeholders
         * @var array
         */
        public $find = array();
        /**
         * Strings to replace in subjects/headings.
         *
         * @deprecated 3.2.0 in favour of placeholders
         * @var array
         */
        public $replace = array();
        /**
         * Constructor.
         */
        public function __construct()
        {
        }
        /**
         * Handle multipart mail.
         *
         * @param  PHPMailer $mailer PHPMailer object.
         * @return PHPMailer
         */
        public function handle_multipart($mailer)
        {
        }
        /**
         * Format email string.
         *
         * @param mixed $string Text to replace placeholders in.
         * @return string
         */
        public function format_string($string)
        {
        }
        /**
         * Set the locale to the store locale for customer emails to make sure emails are in the store language.
         */
        public function setup_locale()
        {
        }
        /**
         * Restore the locale to the default locale. Use after finished with setup_locale.
         */
        public function restore_locale()
        {
        }
        /**
         * Get email subject.
         *
         * @since  3.1.0
         * @return string
         */
        public function get_default_subject()
        {
        }
        /**
         * Get email heading.
         *
         * @since  3.1.0
         * @return string
         */
        public function get_default_heading()
        {
        }
        /**
         * Default content to show below main email content.
         *
         * @since 3.7.0
         * @return string
         */
        public function get_default_additional_content()
        {
        }
        /**
         * Return content from the additional_content field.
         *
         * Displayed above the footer.
         *
         * @since 3.7.0
         * @return string
         */
        public function get_additional_content()
        {
        }
        /**
         * Get email subject.
         *
         * @return string
         */
        public function get_subject()
        {
        }
        /**
         * Get email heading.
         *
         * @return string
         */
        public function get_heading()
        {
        }
        /**
         * Get valid recipients.
         *
         * @return string
         */
        public function get_recipient()
        {
        }
        /**
         * Get email headers.
         *
         * @return string
         */
        public function get_headers()
        {
        }
        /**
         * Get email attachments.
         *
         * @return array
         */
        public function get_attachments()
        {
        }
        /**
         * Return email type.
         *
         * @return string
         */
        public function get_email_type()
        {
        }
        /**
         * Get email content type.
         *
         * @param string $default_content_type Default wp_mail() content type.
         * @return string
         */
        public function get_content_type($default_content_type = '')
        {
        }
        /**
         * Return the email's title
         *
         * @return string
         */
        public function get_title()
        {
        }
        /**
         * Return the email's description
         *
         * @return string
         */
        public function get_description()
        {
        }
        /**
         * Proxy to parent's get_option and attempt to localize the result using gettext.
         *
         * @param string $key Option key.
         * @param mixed  $empty_value Value to use when option is empty.
         * @return string
         */
        public function get_option($key, $empty_value = \null)
        {
        }
        /**
         * Checks if this email is enabled and will be sent.
         *
         * @return bool
         */
        public function is_enabled()
        {
        }
        /**
         * Checks if this email is manually sent
         *
         * @return bool
         */
        public function is_manual()
        {
        }
        /**
         * Checks if this email is customer focussed.
         *
         * @return bool
         */
        public function is_customer_email()
        {
        }
        /**
         * Get WordPress blog name.
         *
         * @return string
         */
        public function get_blogname()
        {
        }
        /**
         * Get email content.
         *
         * @return string
         */
        public function get_content()
        {
        }
        /**
         * Apply inline styles to dynamic content.
         *
         * We only inline CSS for html emails, and to do so we use Emogrifier library (if supported).
         *
         * @version 4.0.0
         * @param string|null $content Content that will receive inline styles.
         * @return string
         */
        public function style_inline($content)
        {
        }
        /**
         * Return if emogrifier library is supported.
         *
         * @version 4.0.0
         * @since 3.5.0
         * @return bool
         */
        protected function supports_emogrifier()
        {
        }
        /**
         * Get the email content in plain text format.
         *
         * @return string
         */
        public function get_content_plain()
        {
        }
        /**
         * Get the email content in HTML format.
         *
         * @return string
         */
        public function get_content_html()
        {
        }
        /**
         * Get the from name for outgoing emails.
         *
         * @param string $from_name Default wp_mail() name associated with the "from" email address.
         * @return string
         */
        public function get_from_name($from_name = '')
        {
        }
        /**
         * Get the from address for outgoing emails.
         *
         * @param string $from_email Default wp_mail() email address to send from.
         * @return string
         */
        public function get_from_address($from_email = '')
        {
        }
        /**
         * Send an email.
         *
         * @param string $to Email to.
         * @param string $subject Email subject.
         * @param string $message Email message.
         * @param string $headers Email headers.
         * @param array  $attachments Email attachments.
         * @return bool success
         */
        public function send($to, $subject, $message, $headers, $attachments)
        {
        }
        /**
         * Initialise Settings Form Fields - these are generic email options most will use.
         */
        public function init_form_fields()
        {
        }
        /**
         * Email type options.
         *
         * @return array
         */
        public function get_email_type_options()
        {
        }
        /**
         * Admin Panel Options Processing.
         */
        public function process_admin_options()
        {
        }
        /**
         * Get template.
         *
         * @param  string $type Template type. Can be either 'template_html' or 'template_plain'.
         * @return string
         */
        public function get_template($type)
        {
        }
        /**
         * Save the email templates.
         *
         * @since 2.4.0
         * @param string $template_code Template code.
         * @param string $template_path Template path.
         */
        protected function save_template($template_code, $template_path)
        {
        }
        /**
         * Get the template file in the current theme.
         *
         * @param  string $template Template name.
         *
         * @return string
         */
        public function get_theme_template_file($template)
        {
        }
        /**
         * Move template action.
         *
         * @param string $template_type Template type.
         */
        protected function move_template_action($template_type)
        {
        }
        /**
         * Delete template action.
         *
         * @param string $template_type Template type.
         */
        protected function delete_template_action($template_type)
        {
        }
        /**
         * Admin actions.
         */
        protected function admin_actions()
        {
        }
        /**
         * Admin Options.
         *
         * Setup the email settings screen.
         * Override this in your email.
         *
         * @since 1.0.0
         */
        public function admin_options()
        {
        }
        /**
         * Clears the PhpMailer AltBody field, to prevent that content from leaking across emails.
         */
        private function clear_alt_body_field() : void
        {
        }
    }
}
namespace {
    /**
     * Abstract Rest Controller Class
     *
     * @package WooCommerce\RestApi
     * @extends  WP_REST_Controller
     * @version  2.6.0
     */
    abstract class WC_REST_Controller extends \WP_REST_Controller
    {
        /**
         * Endpoint namespace.
         *
         * @var string
         */
        protected $namespace = 'wc/v1';
        /**
         * Route base.
         *
         * @var string
         */
        protected $rest_base = '';
        /**
         * Used to cache computed return fields.
         *
         * @var null|array
         */
        private $_fields = \null;
        /**
         * Used to verify if cached fields are for correct request object.
         *
         * @var null|WP_REST_Request
         */
        private $_request = \null;
        /**
         * Add the schema from additional fields to an schema array.
         *
         * The type of object is inferred from the passed schema.
         *
         * @param array $schema Schema array.
         *
         * @return array
         */
        protected function add_additional_fields_schema($schema)
        {
        }
        /**
         * Compatibility functions for WP 5.5, since custom types are not supported anymore.
         * See @link https://core.trac.wordpress.org/changeset/48306
         *
         * @param string $method Optional. HTTP method of the request.
         *
         * @return array Endpoint arguments.
         */
        public function get_endpoint_args_for_item_schema($method = \WP_REST_Server::CREATABLE)
        {
        }
        /**
         * Change datatypes `date-time` to string, and `mixed` to composite of all built in types. This is required for maintaining forward compatibility with WP 5.5 since custom post types are not supported anymore.
         *
         * See @link https://core.trac.wordpress.org/changeset/48306
         *
         * We still use the 'mixed' type, since if we convert to composite type everywhere, it won't work in 5.4 anymore because they require to define the full schema.
         *
         * @param array $endpoint_args Schema with datatypes to convert.
         * @return mixed Schema with converted datatype.
         */
        protected function adjust_wp_5_5_datatype_compatibility($endpoint_args)
        {
        }
        /**
         * Get normalized rest base.
         *
         * @return string
         */
        protected function get_normalized_rest_base()
        {
        }
        /**
         * Check batch limit.
         *
         * @param array $items Request items.
         * @return bool|WP_Error
         */
        protected function check_batch_limit($items)
        {
        }
        /**
         * Bulk create, update and delete items.
         *
         * @param WP_REST_Request $request Full details about the request.
         * @return array Of WP_Error or WP_REST_Response.
         */
        public function batch_items($request)
        {
        }
        /**
         * Validate a text value for a text based setting.
         *
         * @since 3.0.0
         * @param string $value Value.
         * @param array  $setting Setting.
         * @return string
         */
        public function validate_setting_text_field($value, $setting)
        {
        }
        /**
         * Validate select based settings.
         *
         * @since 3.0.0
         * @param string $value Value.
         * @param array  $setting Setting.
         * @return string|WP_Error
         */
        public function validate_setting_select_field($value, $setting)
        {
        }
        /**
         * Validate multiselect based settings.
         *
         * @since 3.0.0
         * @param array $values Values.
         * @param array $setting Setting.
         * @return array|WP_Error
         */
        public function validate_setting_multiselect_field($values, $setting)
        {
        }
        /**
         * Validate image_width based settings.
         *
         * @since 3.0.0
         * @param array $values Values.
         * @param array $setting Setting.
         * @return string|WP_Error
         */
        public function validate_setting_image_width_field($values, $setting)
        {
        }
        /**
         * Validate radio based settings.
         *
         * @since 3.0.0
         * @param string $value Value.
         * @param array  $setting Setting.
         * @return string|WP_Error
         */
        public function validate_setting_radio_field($value, $setting)
        {
        }
        /**
         * Validate checkbox based settings.
         *
         * @since 3.0.0
         * @param string $value Value.
         * @param array  $setting Setting.
         * @return string|WP_Error
         */
        public function validate_setting_checkbox_field($value, $setting)
        {
        }
        /**
         * Validate textarea based settings.
         *
         * @since 3.0.0
         * @param string $value Value.
         * @param array  $setting Setting.
         * @return string
         */
        public function validate_setting_textarea_field($value, $setting)
        {
        }
        /**
         * Add meta query.
         *
         * @since 3.0.0
         * @param array $args       Query args.
         * @param array $meta_query Meta query.
         * @return array
         */
        protected function add_meta_query($args, $meta_query)
        {
        }
        /**
         * Get the batch schema, conforming to JSON Schema.
         *
         * @return array
         */
        public function get_public_batch_schema()
        {
        }
        /**
         * Gets an array of fields to be included on the response.
         *
         * Included fields are based on item schema and `_fields=` request argument.
         * Updated from WordPress 5.3, included into this class to support old versions.
         *
         * @since 3.5.0
         * @param WP_REST_Request $request Full details about the request.
         * @return array Fields to be included in the response.
         */
        public function get_fields_for_response($request)
        {
        }
        /**
         * Limit the contents of the meta_data property based on certain request parameters.
         *
         * Note that if both `include_meta` and `exclude_meta` are present in the request,
         * `include_meta` will take precedence.
         *
         * @param \WP_REST_Request $request   The request.
         * @param array            $meta_data All of the meta data for an object.
         *
         * @return array
         */
        protected function get_meta_data_for_response($request, $meta_data)
        {
        }
    }
    /**
     * REST API Reports controller class.
     *
     * @package WooCommerce\RestApi
     * @extends WC_REST_Controller
     */
    class WC_REST_Reports_V1_Controller extends \WC_REST_Controller
    {
        /**
         * Endpoint namespace.
         *
         * @var string
         */
        protected $namespace = 'wc/v1';
        /**
         * Route base.
         *
         * @var string
         */
        protected $rest_base = 'reports';
        /**
         * Register the routes for reports.
         */
        public function register_routes()
        {
        }
        /**
         * Check whether a given request has permission to read reports.
         *
         * @param  WP_REST_Request $request Full details about the request.
         * @return WP_Error|boolean
         */
        public function get_items_permissions_check($request)
        {
        }
        /**
         * Get reports list.
         *
         * @since 3.5.0
         * @return array
         */
        protected function get_reports()
        {
        }
        /**
         * Get all reports.
         *
         * @param WP_REST_Request $request
         * @return array|WP_Error
         */
        public function get_items($request)
        {
        }
        /**
         * Prepare a report object for serialization.
         *
         * @param stdClass $report Report data.
         * @param WP_REST_Request $request Request object.
         * @return WP_REST_Response $response Response data.
         */
        public function prepare_item_for_response($report, $request)
        {
        }
        /**
         * Get the Report's schema, conforming to JSON Schema.
         *
         * @return array
         */
        public function get_item_schema()
        {
        }
        /**
         * Get the query params for collections.
         *
         * @return array
         */
        public function get_collection_params()
        {
        }
    }
    /**
     * REST API Reports controller class.
     *
     * @package WooCommerce\RestApi
     * @extends WC_REST_Reports_V1_Controller
     */
    class WC_REST_Reports_V2_Controller extends \WC_REST_Reports_V1_Controller
    {
        /**
         * Endpoint namespace.
         *
         * @var string
         */
        protected $namespace = 'wc/v2';
    }
    /**
     * REST API Product Reviews Controller Class.
     *
     * @package WooCommerce\RestApi
     * @extends WC_REST_Controller
     */
    class WC_REST_Product_Reviews_Controller extends \WC_REST_Controller
    {
        /**
         * Endpoint namespace.
         *
         * @var string
         */
        protected $namespace = 'wc/v3';
        /**
         * Route base.
         *
         * @var string
         */
        protected $rest_base = 'products/reviews';
        /**
         * Register the routes for product reviews.
         */
        public function register_routes()
        {
        }
        /**
         * Check whether a given request has permission to read webhook deliveries.
         *
         * @param  WP_REST_Request $request Full details about the request.
         * @return WP_Error|boolean
         */
        public function get_items_permissions_check($request)
        {
        }
        /**
         * Check if a given request has access to read a product review.
         *
         * @param  WP_REST_Request $request Full details about the request.
         * @return WP_Error|boolean
         */
        public function get_item_permissions_check($request)
        {
        }
        /**
         * Check if a given request has access to create a new product review.
         *
         * @param  WP_REST_Request $request Full details about the request.
         * @return WP_Error|boolean
         */
        public function create_item_permissions_check($request)
        {
        }
        /**
         * Check if a given request has access to update a product review.
         *
         * @param  WP_REST_Request $request Full details about the request.
         * @return WP_Error|boolean
         */
        public function update_item_permissions_check($request)
        {
        }
        /**
         * Check if a given request has access to delete a product review.
         *
         * @param  WP_REST_Request $request Full details about the request.
         * @return WP_Error|boolean
         */
        public function delete_item_permissions_check($request)
        {
        }
        /**
         * Check if a given request has access batch create, update and delete items.
         *
         * @param  WP_REST_Request $request Full details about the request.
         * @return boolean|WP_Error
         */
        public function batch_items_permissions_check($request)
        {
        }
        /**
         * Get all reviews.
         *
         * @param WP_REST_Request $request Full details about the request.
         * @return array|WP_Error
         */
        public function get_items($request)
        {
        }
        /**
         * Create a single review.
         *
         * @param WP_REST_Request $request Full details about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function create_item($request)
        {
        }
        /**
         * Get a single product review.
         *
         * @param WP_REST_Request $request Full details about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function get_item($request)
        {
        }
        /**
         * Updates a review.
         *
         * @param WP_REST_Request $request Full details about the request.
         * @return WP_Error|WP_REST_Response Response object on success, or error object on failure.
         */
        public function update_item($request)
        {
        }
        /**
         * Deletes a review.
         *
         * @param WP_REST_Request $request Full details about the request.
         * @return WP_Error|WP_REST_Response Response object on success, or error object on failure.
         */
        public function delete_item($request)
        {
        }
        /**
         * Prepare a single product review output for response.
         *
         * @param WP_Comment      $review Product review object.
         * @param WP_REST_Request $request Request object.
         * @return WP_REST_Response $response Response data.
         */
        public function prepare_item_for_response($review, $request)
        {
        }
        /**
         * Prepare a single product review to be inserted into the database.
         *
         * @param  WP_REST_Request $request Request object.
         * @return array|WP_Error  $prepared_review
         */
        protected function prepare_item_for_database($request)
        {
        }
        /**
         * Prepare links for the request.
         *
         * @param WP_Comment $review Product review object.
         * @return array Links for the given product review.
         */
        protected function prepare_links($review)
        {
        }
        /**
         * Get the Product Review's schema, conforming to JSON Schema.
         *
         * @return array
         */
        public function get_item_schema()
        {
        }
        /**
         * Get the query params for collections.
         *
         * @return array
         */
        public function get_collection_params()
        {
        }
        /**
         * Get the reivew, if the ID is valid.
         *
         * @since 3.5.0
         * @param int $id Supplied ID.
         * @return WP_Comment|WP_Error Comment object if ID is valid, WP_Error otherwise.
         */
        protected function get_review($id)
        {
        }
        /**
         * Prepends internal property prefix to query parameters to match our response fields.
         *
         * @since 3.5.0
         * @param string $query_param Query parameter.
         * @return string
         */
        protected function normalize_query_param($query_param)
        {
        }
        /**
         * Checks comment_approved to set comment status for single comment output.
         *
         * @since 3.5.0
         * @param string|int $comment_approved comment status.
         * @return string Comment status.
         */
        protected function prepare_status_response($comment_approved)
        {
        }
        /**
         * Sets the comment_status of a given review object when creating or updating a review.
         *
         * @since 3.5.0
         * @param string|int $new_status New review status.
         * @param int        $id         Review ID.
         * @return bool Whether the status was changed.
         */
        protected function handle_status_param($new_status, $id)
        {
        }
    }
    /**
     * REST API Reports controller class.
     *
     * @package WooCommerce\RestApi
     * @extends WC_REST_Reports_V2_Controller
     */
    class WC_REST_Reports_Controller extends \WC_REST_Reports_V2_Controller
    {
        /**
         * Endpoint namespace.
         *
         * @var string
         */
        protected $namespace = 'wc/v3';
        /**
         * Get reports list.
         *
         * @since 3.5.0
         * @return array
         */
        protected function get_reports()
        {
        }
    }
    /**
     * REST API Reports Reviews Totals controller class.
     *
     * @package WooCommerce\RestApi
     * @extends WC_REST_Reports_Controller
     */
    class WC_REST_Report_Reviews_Totals_Controller extends \WC_REST_Reports_Controller
    {
        /**
         * Endpoint namespace.
         *
         * @var string
         */
        protected $namespace = 'wc/v3';
        /**
         * Route base.
         *
         * @var string
         */
        protected $rest_base = 'reports/reviews/totals';
        /**
         * Get reports list.
         *
         * @since 3.5.0
         * @return array
         */
        protected function get_reports()
        {
        }
        /**
         * Prepare a report object for serialization.
         *
         * @param  stdClass        $report Report data.
         * @param  WP_REST_Request $request Request object.
         * @return WP_REST_Response $response Response data.
         */
        public function prepare_item_for_response($report, $request)
        {
        }
        /**
         * Get the Report's schema, conforming to JSON Schema.
         *
         * @return array
         */
        public function get_item_schema()
        {
        }
    }
}
namespace {
    /**
     * Widget rating filter class.
     */
    class WC_Widget_Rating_Filter extends \WC_Widget
    {
        /**
         * Constructor.
         */
        public function __construct()
        {
        }
        /**
         * Count products after other filters have occurred by adjusting the main query.
         *
         * @param  int $rating Rating.
         * @return int
         */
        protected function get_filtered_product_count($rating)
        {
        }
        /**
         * Widget function.
         *
         * @see WP_Widget
         * @param array $args     Arguments.
         * @param array $instance Widget instance.
         */
        public function widget($args, $instance)
        {
        }
    }
    /**
     * Widget recent reviews class.
     */
    class WC_Widget_Recent_Reviews extends \WC_Widget
    {
        /**
         * Constructor.
         */
        public function __construct()
        {
        }
        /**
         * Output widget.
         *
         * @see WP_Widget
         * @param array $args     Arguments.
         * @param array $instance Widget instance.
         */
        public function widget($args, $instance)
        {
        }
    }
}
namespace {
    /**
     * Is_shop - Returns true when viewing the product type archive (shop).
     *
     * @return bool
     */
    function is_shop()
    {
    }
    /**
     * Is_product_taxonomy - Returns true when viewing a product taxonomy archive.
     *
     * @return bool
     */
    function is_product_taxonomy()
    {
    }
    /**
     * Checks if a user (by email or ID or both) has bought an item.
     *
     * @param string $customer_email Customer email to check.
     * @param int    $user_id User ID to check.
     * @param int    $product_id Product ID to check.
     * @return bool
     */
    function wc_customer_bought_product($customer_email, $user_id, $product_id)
    {
    }
    /**
     * Format a date for output.
     *
     * @since  3.0.0
     * @param  WC_DateTime $date   Instance of WC_DateTime.
     * @param  string      $format Data format.
     *                             Defaults to the wc_date_format function if not set.
     * @return string
     */
    function wc_format_datetime($date, $format = '')
    {
    }
    /**
     * Main function for returning orders, uses the WC_Order_Factory class.
     *
     * @since  2.2
     *
     * @param mixed $the_order       Post object or post ID of the order.
     *
     * @return bool|WC_Order|WC_Order_Refund
     */
    function wc_get_order($the_order = \false)
    {
    }
    /**
     * Standard way of retrieving products based on certain parameters.
     *
     * This function should be used for product retrieval so that we have a data agnostic
     * way to get a list of products.
     *
     * Args and usage: https://github.com/woocommerce/woocommerce/wiki/wc_get_products-and-WC_Product_Query
     *
     * @since  3.0.0
     * @param  array $args Array of args (above).
     * @return array|stdClass Number of pages and an array of product objects if
     *                             paginate is true, or just an array of values.
     */
    function wc_get_products($args)
    {
    }
    /**
     * Main function for returning products, uses the WC_Product_Factory class.
     *
     * This function should only be called after 'init' action is finished, as there might be taxonomies that are getting
     * registered during the init action.
     *
     * @since 2.2.0
     *
     * @param mixed $the_product Post object or post ID of the product.
     * @param array $deprecated Previously used to pass arguments to the factory, e.g. to force a type.
     * @return WC_Product|null|false
     */
    function wc_get_product($the_product = \false, $deprecated = array())
    {
    }
    /**
     * Like wc_get_template, but returns the HTML instead of outputting.
     *
     * @see wc_get_template
     * @since 2.5.0
     * @param string $template_name Template name.
     * @param array  $args          Arguments. (default: array).
     * @param string $template_path Template path. (default: '').
     * @param string $default_path  Default path. (default: '').
     *
     * @return string
     */
    function wc_get_template_html($template_name, $args = array(), $template_path = '', $default_path = '')
    {
    }
    /**
     * Parses and formats a date for ISO8601/RFC3339.
     *
     * Required WP 4.4 or later.
     * See https://developer.wordpress.org/reference/functions/mysql_to_rfc3339/
     *
     * @since  2.6.0
     * @param  string|null|WC_DateTime $date Date.
     * @param  bool                    $utc  Send false to get local/offset time.
     * @return string|null ISO8601/RFC3339 formatted datetime.
     */
    function wc_rest_prepare_date_response($date, $utc = \true)
    {
    }
    /**
     * Get review verification status.
     *
     * @param  int $comment_id Comment ID.
     * @return bool
     */
    function wc_review_is_from_verified_owner($comment_id)
    {
    }
    /**
     * Check if reviews ratings are enabled.
     *
     * @since 3.6.0
     * @return bool
     */
    function wc_review_ratings_enabled()
    {
    }
    /**
     * Returns the main instance of WC.
     *
     * @since  2.1
     * @return WooCommerce
     */
    function WC()
    {
    }
}
namespace Automattic\WooCommerce\StoreApi\Routes {
    /**
     * RouteInterface.
     */
    interface RouteInterface
    {
        /**
         * Get the path of this REST route.
         *
         * @return string
         */
        public function get_path();
        /**
         * Get arguments for this REST route.
         *
         * @return array An array of endpoints.
         */
        public function get_args();
    }
}
namespace Automattic\WooCommerce\StoreApi\Routes\V1 {
    /**
     * AbstractRoute class.
     */
    abstract class AbstractRoute implements \Automattic\WooCommerce\StoreApi\Routes\RouteInterface
    {
        /**
         * Schema class instance.
         *
         * @var AbstractSchema
         */
        protected $schema;
        /**
         * Route namespace.
         *
         * @var string
         */
        protected $namespace = 'wc/store/v1';
        /**
         * Schema Controller instance.
         *
         * @var SchemaController
         */
        protected $schema_controller;
        /**
         * The routes schema.
         *
         * @var string
         */
        const SCHEMA_TYPE = '';
        /**
         * The routes schema version.
         *
         * @var integer
         */
        const SCHEMA_VERSION = 1;
        /**
         * Constructor.
         *
         * @param SchemaController $schema_controller Schema Controller instance.
         * @param AbstractSchema   $schema Schema class for this route.
         */
        public function __construct(\Automattic\WooCommerce\StoreApi\SchemaController $schema_controller, \Automattic\WooCommerce\StoreApi\Schemas\v1\AbstractSchema $schema)
        {
        }
        /**
         * Get the namespace for this route.
         *
         * @return string
         */
        public function get_namespace()
        {
        }
        /**
         * Set the namespace for this route.
         *
         * @param string $namespace Given namespace.
         */
        public function set_namespace($namespace)
        {
        }
        /**
         * Get item schema properties.
         *
         * @return array
         */
        public function get_item_schema()
        {
        }
        /**
         * Get the route response based on the type of request.
         *
         * @param \WP_REST_Request $request Request object.
         * @return \WP_REST_Response
         */
        public function get_response(\WP_REST_Request $request)
        {
        }
        /**
         * Converts an error to a response object. Based on \WP_REST_Server.
         *
         * @param \WP_Error $error WP_Error instance.
         * @return \WP_REST_Response List of associative arrays with code and message keys.
         */
        protected function error_to_response($error)
        {
        }
        /**
         * Get route response for GET requests.
         *
         * When implemented, should return a \WP_REST_Response.
         *
         * @throws RouteException On error.
         * @param \WP_REST_Request $request Request object.
         */
        protected function get_route_response(\WP_REST_Request $request)
        {
        }
        /**
         * Get route response for POST requests.
         *
         * When implemented, should return a \WP_REST_Response.
         *
         * @throws RouteException On error.
         * @param \WP_REST_Request $request Request object.
         */
        protected function get_route_post_response(\WP_REST_Request $request)
        {
        }
        /**
         * Get route response for PUT requests.
         *
         * When implemented, should return a \WP_REST_Response.
         *
         * @throws RouteException On error.
         * @param \WP_REST_Request $request Request object.
         */
        protected function get_route_update_response(\WP_REST_Request $request)
        {
        }
        /**
         * Get route response for DELETE requests.
         *
         * When implemented, should return a \WP_REST_Response.
         *
         * @throws RouteException On error.
         * @param \WP_REST_Request $request Request object.
         */
        protected function get_route_delete_response(\WP_REST_Request $request)
        {
        }
        /**
         * Get route response when something went wrong.
         *
         * @param string $error_code String based error code.
         * @param string $error_message User facing error message.
         * @param int    $http_status_code HTTP status. Defaults to 500.
         * @param array  $additional_data  Extra data (key value pairs) to expose in the error response.
         * @return \WP_Error WP Error object.
         */
        protected function get_route_error_response($error_code, $error_message, $http_status_code = 500, $additional_data = [])
        {
        }
        /**
         * Get route response when something went wrong and the supplied error is a WP_Error. This currently only happens
         * when an item in the cart is out of stock, partially out of stock, can only be bought individually, or when the
         * item is not purchasable.
         *
         * @param WP_Error $error_object The WP_Error object containing the error.
         * @param int      $http_status_code HTTP status. Defaults to 500.
         * @param array    $additional_data  Extra data (key value pairs) to expose in the error response.
         * @return WP_Error WP Error object.
         */
        protected function get_route_error_response_from_object($error_object, $http_status_code = 500, $additional_data = [])
        {
        }
        /**
         * Prepare a single item for response.
         *
         * @param mixed            $item Item to format to schema.
         * @param \WP_REST_Request $request Request object.
         * @return \WP_REST_Response $response Response data.
         */
        public function prepare_item_for_response($item, \WP_REST_Request $request)
        {
        }
        /**
         * Retrieves the context param.
         *
         * Ensures consistent descriptions between endpoints, and populates enum from schema.
         *
         * @param array $args Optional. Additional arguments for context parameter. Default empty array.
         * @return array Context parameter details.
         */
        protected function get_context_param($args = array())
        {
        }
        /**
         * Prepares a response for insertion into a collection.
         *
         * @param \WP_REST_Response $response Response object.
         * @return array|mixed Response data, ready for insertion into collection data.
         */
        protected function prepare_response_for_collection(\WP_REST_Response $response)
        {
        }
        /**
         * Prepare links for the request.
         *
         * @param mixed            $item Item to prepare.
         * @param \WP_REST_Request $request Request object.
         * @return array
         */
        protected function prepare_links($item, $request)
        {
        }
        /**
         * Retrieves the query params for the collections.
         *
         * @return array Query parameters for the collection.
         */
        public function get_collection_params()
        {
        }
    }
}
namespace Automattic\WooCommerce\StoreApi\Routes\V1 {
    /**
     * ProductReviews class.
     */
    class ProductReviews extends \Automattic\WooCommerce\StoreApi\Routes\V1\AbstractRoute
    {
        /**
         * The route identifier.
         *
         * @var string
         */
        const IDENTIFIER = 'product-reviews';
        /**
         * The routes schema.
         *
         * @var string
         */
        const SCHEMA_TYPE = 'product-review';
        /**
         * Get the path of this REST route.
         *
         * @return string
         */
        public function get_path()
        {
        }
        /**
         * Get method arguments for this REST route.
         *
         * @return array An array of endpoints.
         */
        public function get_args()
        {
        }
        /**
         * Get a collection of reviews.
         *
         * @param \WP_REST_Request $request Request object.
         * @return \WP_REST_Response
         */
        protected function get_route_response(\WP_REST_Request $request)
        {
        }
        /**
         * Prepends internal property prefix to query parameters to match our response fields.
         *
         * @param string $query_param Query parameter.
         * @return string
         */
        protected function normalize_query_param($query_param)
        {
        }
        /**
         * Get the query params for collections of products.
         *
         * @return array
         */
        public function get_collection_params()
        {
        }
    }
    /**
     * Products class.
     */
    class Products extends \Automattic\WooCommerce\StoreApi\Routes\V1\AbstractRoute
    {
        /**
         * The route identifier.
         *
         * @var string
         */
        const IDENTIFIER = 'products';
        /**
         * The routes schema.
         *
         * @var string
         */
        const SCHEMA_TYPE = 'product';
        /**
         * Get the path of this REST route.
         *
         * @return string
         */
        public function get_path()
        {
        }
        /**
         * Get method arguments for this REST route.
         *
         * @return array An array of endpoints.
         */
        public function get_args()
        {
        }
        /**
         * Get a collection of posts and add the post title filter option to \WP_Query.
         *
         * @param \WP_REST_Request $request Request object.
         * @return \WP_REST_Response
         */
        protected function get_route_response(\WP_REST_Request $request)
        {
        }
        /**
         * Prepare links for the request.
         *
         * @param \WC_Product      $item Product object.
         * @param \WP_REST_Request $request Request object.
         * @return array
         */
        protected function prepare_links($item, $request)
        {
        }
        /**
         * Get the query params for collections of products.
         *
         * @return array
         */
        public function get_collection_params()
        {
        }
    }
}
namespace Automattic\WooCommerce\StoreApi {
    /**
     * SchemaController class.
     */
    class SchemaController
    {
        /**
         * Stores schema class instances.
         *
         * @var Schemas\V1\AbstractSchema[]
         */
        protected $schemas = [];
        /**
         * Stores Rest Extending instance
         *
         * @var ExtendSchema
         */
        private $extend;
        /**
         * Constructor.
         *
         * @param ExtendSchema $extend Rest Extending instance.
         */
        public function __construct(\Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema $extend)
        {
        }
        /**
         * Get a schema class instance.
         *
         * @throws \Exception If the schema does not exist.
         *
         * @param string $name Name of schema.
         * @param int    $version API Version being requested.
         * @return Schemas\V1\AbstractSchema A new instance of the requested schema.
         */
        public function get($name, $version = 1)
        {
        }
    }
}
namespace Automattic\WooCommerce\StoreApi\Schemas {
    /**
     * Provides utility functions to extend Store API schemas.
     *
     * Note there are also helpers that map to these methods.
     *
     * @see woocommerce_store_api_register_endpoint_data()
     * @see woocommerce_store_api_register_update_callback()
     * @see woocommerce_store_api_register_payment_requirements()
     * @see woocommerce_store_api_get_formatter()
     */
    final class ExtendSchema
    {
        /**
         * List of Store API schema that is allowed to be extended by extensions.
         *
         * @var string[]
         */
        private $endpoints = [\Automattic\WooCommerce\StoreApi\Schemas\V1\CartItemSchema::IDENTIFIER, \Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema::IDENTIFIER, \Automattic\WooCommerce\StoreApi\Schemas\V1\CheckoutSchema::IDENTIFIER, \Automattic\WooCommerce\StoreApi\Schemas\V1\ProductSchema::IDENTIFIER];
        /**
         * Holds the formatters class instance.
         *
         * @var Formatters
         */
        private $formatters;
        /**
         * Data to be extended
         *
         * @var array
         */
        private $extend_data = [];
        /**
         * Data to be extended
         *
         * @var array
         */
        private $callback_methods = [];
        /**
         * Array of payment requirements
         *
         * @var array
         */
        private $payment_requirements = [];
        /**
         * Constructor
         *
         * @param Formatters $formatters An instance of the formatters class.
         */
        public function __construct(\Automattic\WooCommerce\StoreApi\Formatters $formatters)
        {
        }
        /**
         * Register endpoint data under a specified namespace
         *
         * @param array $args {
         *     An array of elements that make up a post to update or insert.
         *
         *     @type string   $endpoint Required. The endpoint to extend.
         *     @type string   $namespace Required. Plugin namespace.
         *     @type callable $schema_callback Callback executed to add schema data.
         *     @type callable $data_callback Callback executed to add endpoint data.
         *     @type string   $schema_type The type of data, object or array.
         * }
         *
         * @throws \Exception On failure to register.
         */
        public function register_endpoint_data($args)
        {
        }
        /**
         * Add callback functions that can be executed by the cart/extensions endpoint.
         *
         * @param array $args {
         *     An array of elements that make up the callback configuration.
         *
         *     @type string   $namespace Required. Plugin namespace.
         *     @type callable $callback Required. The function/callable to execute.
         * }
         *
         * @throws \Exception On failure to register.
         */
        public function register_update_callback($args)
        {
        }
        /**
         * Registers and validates payment requirements callbacks.
         *
         * @param array $args {
         *     Array of registration data.
         *
         *     @type callable $data_callback Required. Callback executed to add payment requirements data.
         * }
         *
         * @throws \Exception On failure to register.
         */
        public function register_payment_requirements($args)
        {
        }
        /**
         * Returns a formatter instance.
         *
         * @param string $name Formatter name.
         * @return FormatterInterface
         */
        public function get_formatter($name)
        {
        }
        /**
         * Get callback for a specific endpoint and namespace.
         *
         * @param string $namespace The namespace to get callbacks for.
         *
         * @return callable The callback registered by the extension.
         * @throws \Exception When callback is not callable or parameters are incorrect.
         */
        public function get_update_callback($namespace)
        {
        }
        /**
         * Returns the registered endpoint data
         *
         * @param string $endpoint    A valid identifier.
         * @param array  $passed_args Passed arguments from the Schema class.
         * @return object Returns an casted object with registered endpoint data.
         * @throws \Exception If a registered callback throws an error, or silently logs it.
         */
        public function get_endpoint_data($endpoint, array $passed_args = [])
        {
        }
        /**
         * Returns the registered endpoint schema
         *
         * @param string $endpoint    A valid identifier.
         * @param array  $passed_args Passed arguments from the Schema class.
         * @return object Returns an array with registered schema data.
         * @throws \Exception If a registered callback throws an error, or silently logs it.
         */
        public function get_endpoint_schema($endpoint, array $passed_args = [])
        {
        }
        /**
         * Returns the additional payment requirements for the cart which are required to make payments. Values listed here
         * are compared against each Payment Gateways "supports" flag.
         *
         * @param array $requirements list of requirements that should be added to the collected requirements.
         * @return array Returns a list of payment requirements.
         * @throws \Exception If a registered callback throws an error, or silently logs it.
         */
        public function get_payment_requirements(array $requirements = ['products'])
        {
        }
        /**
         * Throws error and/or silently logs it.
         *
         * @param string|\Throwable $exception_or_error Error message or \Exception.
         * @throws \Exception An error to throw if we have debug enabled and user is admin.
         */
        private function throw_exception($exception_or_error)
        {
        }
        /**
         * Format schema for an extension.
         *
         * @param string $namespace Error message or \Exception.
         * @param array  $schema An error to throw if we have debug enabled and user is admin.
         * @param string $schema_type How should data be shaped.
         * @return array Formatted schema.
         */
        private function format_extensions_properties($namespace, $schema, $schema_type)
        {
        }
    }
}
namespace Automattic\WooCommerce\StoreApi\Schemas\V1 {
    /**
     * AbstractSchema class.
     *
     * For REST Route Schemas
     */
    abstract class AbstractSchema
    {
        /**
         * The schema item name.
         *
         * @var string
         */
        protected $title = 'Schema';
        /**
         * Rest extend instance.
         *
         * @var ExtendSchema
         */
        protected $extend;
        /**
         * Schema Controller instance.
         *
         * @var SchemaController
         */
        protected $controller;
        /**
         * Extending key that gets added to endpoint.
         *
         * @var string
         */
        const EXTENDING_KEY = 'extensions';
        /**
         * Constructor.
         *
         * @param ExtendSchema     $extend Rest Extending instance.
         * @param SchemaController $controller Schema Controller instance.
         */
        public function __construct(\Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema $extend, \Automattic\WooCommerce\StoreApi\SchemaController $controller)
        {
        }
        /**
         * Returns the full item schema.
         *
         * @return array
         */
        public function get_item_schema()
        {
        }
        /**
         * Return schema properties.
         *
         * @return array
         */
        public abstract function get_properties();
        /**
         * Recursive removal of arg_options.
         *
         * @param array $properties Schema properties.
         */
        protected function remove_arg_options($properties)
        {
        }
        /**
         * Returns the public schema.
         *
         * @return array
         */
        public function get_public_item_schema()
        {
        }
        /**
         * Returns extended data for a specific endpoint.
         *
         * @param string $endpoint The endpoint identifier.
         * @param array  ...$passed_args An array of arguments to be passed to callbacks.
         * @return object the data that will get added.
         */
        protected function get_extended_data($endpoint, ...$passed_args)
        {
        }
        /**
         * Gets an array of schema defaults recursively.
         *
         * @param array $properties Schema property data.
         * @return array Array of defaults, pulled from arg_options
         */
        protected function get_recursive_schema_property_defaults($properties)
        {
        }
        /**
         * Gets a function that validates recursively.
         *
         * @param array $properties Schema property data.
         * @return function Anonymous validation callback.
         */
        protected function get_recursive_validate_callback($properties)
        {
        }
        /**
         * Gets a function that sanitizes recursively.
         *
         * @param array $properties Schema property data.
         * @return function Anonymous validation callback.
         */
        protected function get_recursive_sanitize_callback($properties)
        {
        }
        /**
         * Returns extended schema for a specific endpoint.
         *
         * @param string $endpoint The endpoint identifer.
         * @param array  ...$passed_args An array of arguments to be passed to callbacks.
         * @return array the data that will get added.
         */
        protected function get_extended_schema($endpoint, ...$passed_args)
        {
        }
        /**
         * Apply a schema get_item_response callback to an array of items and return the result.
         *
         * @param AbstractSchema $schema Schema class instance.
         * @param array          $items Array of items.
         * @return array Array of values from the callback function.
         */
        protected function get_item_responses_from_schema(\Automattic\WooCommerce\StoreApi\Schemas\V1\AbstractSchema $schema, $items)
        {
        }
        /**
         * Retrieves an array of endpoint arguments from the item schema for the controller.
         *
         * @uses rest_get_endpoint_args_for_schema()
         * @param string $method Optional. HTTP method of the request.
         * @return array Endpoint arguments.
         */
        public function get_endpoint_args_for_item_schema($method = \WP_REST_Server::CREATABLE)
        {
        }
        /**
         * Force all schema properties to be readonly.
         *
         * @param array $properties Schema.
         * @return array Updated schema.
         */
        protected function force_schema_readonly($properties)
        {
        }
        /**
         * Returns consistent currency schema used across endpoints for prices.
         *
         * @return array
         */
        protected function get_store_currency_properties()
        {
        }
        /**
         * Adds currency data to an array of monetary values.
         *
         * @param array $values Monetary amounts.
         * @return array Monetary amounts with currency data appended.
         */
        protected function prepare_currency_response($values)
        {
        }
        /**
         * Convert monetary values from WooCommerce to string based integers, using
         * the smallest unit of a currency.
         *
         * @param string|float $amount Monetary amount with decimals.
         * @param int          $decimals Number of decimals the amount is formatted with.
         * @param int          $rounding_mode Defaults to the PHP_ROUND_HALF_UP constant.
         * @return string      The new amount.
         */
        protected function prepare_money_response($amount, $decimals = 2, $rounding_mode = PHP_ROUND_HALF_UP)
        {
        }
        /**
         * Prepares HTML based content, such as post titles and content, for the API response.
         *
         * @param string|array $response Data to format.
         * @return string|array Formatted data.
         */
        protected function prepare_html_response($response)
        {
        }
    }
    /**
     * ProductSchema class.
     */
    class ProductSchema extends \Automattic\WooCommerce\StoreApi\Schemas\V1\AbstractSchema
    {
        /**
         * The schema item name.
         *
         * @var string
         */
        protected $title = 'product';
        /**
         * The schema item identifier.
         *
         * @var string
         */
        const IDENTIFIER = 'product';
        /**
         * Image attachment schema instance.
         *
         * @var ImageAttachmentSchema
         */
        protected $image_attachment_schema;
        /**
         * Constructor.
         *
         * @param ExtendSchema     $extend Rest Extending instance.
         * @param SchemaController $controller Schema Controller instance.
         */
        public function __construct(\Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema $extend, \Automattic\WooCommerce\StoreApi\SchemaController $controller)
        {
        }
        /**
         * Product schema properties.
         *
         * @return array
         */
        public function get_properties()
        {
        }
        /**
         * Convert a WooCommerce product into an object suitable for the response.
         *
         * @param \WC_Product $product Product instance.
         * @return array
         */
        public function get_item_response($product)
        {
        }
        /**
         * Get list of product images.
         *
         * @param \WC_Product $product Product instance.
         * @return array
         */
        protected function get_images(\WC_Product $product)
        {
        }
        /**
         * Gets remaining stock amount for a product.
         *
         * @param \WC_Product $product Product instance.
         * @return integer|null
         */
        protected function get_remaining_stock(\WC_Product $product)
        {
        }
        /**
         * If a product has low stock, return the remaining stock amount for display.
         *
         * @param \WC_Product $product Product instance.
         * @return integer|null
         */
        protected function get_low_stock_remaining(\WC_Product $product)
        {
        }
        /**
         * Returns true if the given attribute is valid.
         *
         * @param mixed $attribute Object or variable to check.
         * @return boolean
         */
        protected function filter_valid_attribute($attribute)
        {
        }
        /**
         * Returns true if the given attribute is valid and used for variations.
         *
         * @param mixed $attribute Object or variable to check.
         * @return boolean
         */
        protected function filter_variation_attribute($attribute)
        {
        }
        /**
         * Get variation IDs and attributes from the DB.
         *
         * @param \WC_Product $product Product instance.
         * @returns array
         */
        protected function get_variations(\WC_Product $product)
        {
        }
        /**
         * Get list of product attributes and attribute terms.
         *
         * @param \WC_Product $product Product instance.
         * @return array
         */
        protected function get_attributes(\WC_Product $product)
        {
        }
        /**
         * Prepare an attribute term for the response.
         *
         * @param \WP_Term $term Term object.
         * @return object
         */
        protected function prepare_product_attribute_taxonomy_value(\WP_Term $term)
        {
        }
        /**
         * Prepare an attribute term for the response.
         *
         * @param string $name Attribute term name.
         * @param int    $id Attribute term ID.
         * @param string $slug Attribute term slug.
         * @return object
         */
        protected function prepare_product_attribute_value($name, $id = 0, $slug = '')
        {
        }
        /**
         * Get an array of pricing data.
         *
         * @param \WC_Product $product Product instance.
         * @param string      $tax_display_mode If returned prices are incl or excl of tax.
         * @return array
         */
        protected function prepare_product_price_response(\WC_Product $product, $tax_display_mode = '')
        {
        }
        /**
         * WooCommerce can return prices including or excluding tax; choose the correct method based on tax display mode.
         *
         * @param string $tax_display_mode Provided tax display mode.
         * @return string Valid tax display mode.
         */
        protected function get_tax_display_mode($tax_display_mode = '')
        {
        }
        /**
         * WooCommerce can return prices including or excluding tax; choose the correct method based on tax display mode.
         *
         * @param string $tax_display_mode If returned prices are incl or excl of tax.
         * @return string Function name.
         */
        protected function get_price_function_from_tax_display_mode($tax_display_mode)
        {
        }
        /**
         * Get price range from certain product types.
         *
         * @param \WC_Product $product Product instance.
         * @param string      $tax_display_mode If returned prices are incl or excl of tax.
         * @return object|null
         */
        protected function get_price_range(\WC_Product $product, $tax_display_mode = '')
        {
        }
        /**
         * Returns a list of terms assigned to the product.
         *
         * @param \WC_Product $product Product object.
         * @param string      $taxonomy Taxonomy name.
         * @return array Array of terms (id, name, slug).
         */
        protected function get_term_list(\WC_Product $product, $taxonomy = '')
        {
        }
    }
    /**
     * ImageAttachmentSchema class.
     */
    class ImageAttachmentSchema extends \Automattic\WooCommerce\StoreApi\Schemas\V1\AbstractSchema
    {
        /**
         * The schema item name.
         *
         * @var string
         */
        protected $title = 'image';
        /**
         * The schema item identifier.
         *
         * @var string
         */
        const IDENTIFIER = 'image';
        /**
         * Product schema properties.
         *
         * @return array
         */
        public function get_properties()
        {
        }
        /**
         * Convert a WooCommerce product into an object suitable for the response.
         *
         * @param int $attachment_id Image attachment ID.
         * @return array|null
         */
        public function get_item_response($attachment_id)
        {
        }
    }
    /**
     * ProductReviewSchema class.
     */
    class ProductReviewSchema extends \Automattic\WooCommerce\StoreApi\Schemas\V1\AbstractSchema
    {
        /**
         * The schema item name.
         *
         * @var string
         */
        protected $title = 'product_review';
        /**
         * The schema item identifier.
         *
         * @var string
         */
        const IDENTIFIER = 'product-review';
        /**
         * Image attachment schema instance.
         *
         * @var ImageAttachmentSchema
         */
        protected $image_attachment_schema;
        /**
         * Constructor.
         *
         * @param ExtendSchema     $extend Rest Extending instance.
         * @param SchemaController $controller Schema Controller instance.
         */
        public function __construct(\Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema $extend, \Automattic\WooCommerce\StoreApi\SchemaController $controller)
        {
        }
        /**
         * Product review schema properties.
         *
         * @return array
         */
        public function get_properties()
        {
        }
        /**
         * Convert a WooCommerce product into an object suitable for the response.
         *
         * @param \WP_Comment $review Product review object.
         * @return array
         */
        public function get_item_response(\WP_Comment $review)
        {
        }
    }
}
namespace Automattic\WooCommerce\StoreApi {
    /**
     * StoreApi Main Class.
     */
    final class StoreApi
    {
        /**
         * Init and hook in Store API functionality.
         */
        public function init()
        {
        }
        /**
         * Loads the DI container for Store API.
         *
         * @internal This uses the Blocks DI container. If Store API were to move to core, this container could be replaced
         * with a different compatible container.
         *
         * @param boolean $reset Used to reset the container to a fresh instance. Note: this means all dependencies will be reconstructed.
         * @return mixed
         */
        public static function container($reset = false)
        {
        }
    }
}
namespace Automattic\WooCommerce\StoreApi\Utilities {
    /**
     * Pagination class.
     */
    class Pagination
    {
        /**
         * Add pagination headers to a response object.
         *
         * @param \WP_REST_Response $response Reference to the response object.
         * @param \WP_REST_Request  $request The request object.
         * @param int               $total_items Total items found.
         * @param int               $total_pages Total pages found.
         * @return \WP_REST_Response
         */
        public function add_headers($response, $request, $total_items, $total_pages)
        {
        }
        /**
         * Get current page.
         *
         * @param \WP_REST_Request $request The request object.
         * @return int Get the page from the request object.
         */
        protected function get_current_page($request)
        {
        }
        /**
         * Get base for links from the request object.
         *
         * @param \WP_REST_Request $request The request object.
         * @return string
         */
        protected function get_link_base($request)
        {
        }
        /**
         * Add a page link.
         *
         * @param \WP_REST_Response $response Reference to the response object.
         * @param string            $name Page link name. e.g. prev.
         * @param int               $page Page number.
         * @param string            $link_base Base URL.
         */
        protected function add_page_link(&$response, $name, $page, $link_base)
        {
        }
    }
    /**
     * Product Query class.
     *
     * Helper class to handle product queries for the API.
     */
    class ProductQuery
    {
        /**
         * Prepare query args to pass to WP_Query for a REST API request.
         *
         * @param \WP_REST_Request $request Request data.
         * @return array
         */
        public function prepare_objects_query($request)
        {
        }
        /**
         * Get results of query.
         *
         * @param \WP_REST_Request $request Request data.
         * @return array
         */
        public function get_results($request)
        {
        }
        /**
         * Get objects.
         *
         * @param \WP_REST_Request $request Request data.
         * @return array
         */
        public function get_objects($request)
        {
        }
        /**
         * Get last modified date for all products.
         *
         * @return int timestamp.
         */
        public function get_last_modified()
        {
        }
        /**
         * Add in conditional search filters for products.
         *
         * @param array     $args Query args.
         * @param \WC_Query $wp_query WC_Query object.
         * @return array
         */
        public function add_query_clauses($args, $wp_query)
        {
        }
        /**
         * Add in conditional price filters.
         *
         * @param array     $args Query args.
         * @param \WC_Query $wp_query WC_Query object.
         * @return array
         */
        protected function add_price_filter_clauses($args, $wp_query)
        {
        }
        /**
         * Get query for price filters when dealing with displayed taxes.
         *
         * @param float  $price_filter Price filter to apply.
         * @param string $column Price being filtered (min or max).
         * @param string $operator Comparison operator for column.
         * @return string Constructed query.
         */
        protected function get_price_filter_query_for_displayed_taxes($price_filter, $column = 'min_price', $operator = '>=')
        {
        }
        /**
         * If price filters need adjustment to work with displayed taxes, this returns true.
         *
         * This logic is used when prices are stored in the database differently to how they are being displayed, with regards
         * to taxes.
         *
         * @return boolean
         */
        protected function adjust_price_filters_for_displayed_taxes()
        {
        }
        /**
         * Converts price filter from subunits to decimal.
         *
         * @param string|int $price_filter Raw price filter in subunit format.
         * @return float Price filter in decimal format.
         */
        protected function prepare_price_filter($price_filter)
        {
        }
        /**
         * Adjusts a price filter based on a tax class and whether or not the amount includes or excludes taxes.
         *
         * This calculation logic is based on `wc_get_price_excluding_tax` and `wc_get_price_including_tax` in core.
         *
         * @param float  $price_filter Price filter amount as entered.
         * @param string $tax_class Tax class for adjustment.
         * @return float
         */
        protected function adjust_price_filter_for_tax_class($price_filter, $tax_class)
        {
        }
        /**
         * Join wc_product_meta_lookup to posts if not already joined.
         *
         * @param string $sql SQL join.
         * @return string
         */
        protected function append_product_sorting_table_join($sql)
        {
        }
    }
}
namespace Automattic\WooCommerce\Blocks\Utils {
    /**
     * BlocksWpQuery query.
     *
     * Wrapper for WP Query with additional helper methods.
     * Allows query args to be set and parsed without doing running it, so that a cache can be used.
     *
     * @deprecated 2.5.0
     */
    class BlocksWpQuery extends \WP_Query
    {
        /**
         * Constructor.
         *
         * Sets up the WordPress query, if parameter is not empty.
         *
         * Unlike the constructor in WP_Query, this does not RUN the query.
         *
         * @param string|array $query URL query string or array of vars.
         */
        public function __construct($query = '')
        {
        }
        /**
         * Get cached posts, if a cache exists.
         *
         * A hash is generated using the array of query_vars. If doing custom queries via filters such as posts_where
         * (where the SQL query is manipulated directly) you can still ensure there is a unique hash by injecting custom
         * query vars via the parse_query filter. For example:
         *
         *      add_filter( 'parse_query', function( $wp_query ) {
         *           $wp_query->query_vars['my_custom_query_var'] = true;
         *      } );
         *
         * Doing so won't have any negative effect on the query itself, and it will cause the hash to change.
         *
         * @param string $transient_version Transient version to allow for invalidation.
         * @return WP_Post[]|int[] Array of post objects or post IDs.
         */
        public function get_cached_posts($transient_version = '')
        {
        }
    }
}
