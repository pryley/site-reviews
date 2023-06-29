<?php

namespace {
    abstract class myCRED_Hook
    {
        /**
         * Unique Hook ID
         */
        public $id = \false;
        /**
         * The Hooks settings
         */
        public $prefs = \false;
        /**
         * The current point type key
         */
        public $mycred_type = \MYCRED_DEFAULT_TYPE_KEY;
        /**
         * The myCRED object for the current type
         */
        public $core = \false;
        /**
         * Array of all existing point types
         */
        public $point_types = array();
        /**
         * Indicates if the current instance is for the main point type or not
         */
        public $is_main_type = \true;
        /**
         * Construct
         */
        public function __construct($args = array(), $hook_prefs = \NULL, $type = \MYCRED_DEFAULT_TYPE_KEY)
        {
        }
        /**
         * Run
         * Must be over-ridden by sub-class!
         * @since 0.1
         * @version 1.0
         */
        public function run()
        {
        }
        /**
         * Preferences
         * @since 0.1
         * @version 1.0
         */
        public function preferences()
        {
        }
        /**
         * Sanitise Preference
         * @since 0.1
         * @version 1.0
         */
        public function sanitise_preferences($data)
        {
        }
        /**
         * Get Field Name
         * Returns the field name for the current hook
         * @since 0.1
         * @version 1.1
         */
        public function field_name($field = '')
        {
        }
        /**
         * Get Field ID
         * Returns the field id for the current hook
         * @since 0.1
         * @version 1.2
         */
        public function field_id($field = '')
        {
        }
        /**
         * Check Limit
         * @since 1.6
         * @version 1.3
         */
        public function over_hook_limit($instance = '', $reference = '', $user_id = \NULL, $ref_id = \NULL)
        {
        }
        /**
         * Get Limit Types
         * @since 1.6
         * @version 1.0
         */
        public function get_limit_types()
        {
        }
        /**
         * Select Limit
         * @since 1.6
         * @version 1.0
         */
        public function hook_limit_setting($name = '', $id = '', $selected = '')
        {
        }
        /**
         * Impose Limits Dropdown
         * @since 0.1
         * @version 1.3
         */
        public function impose_limits_dropdown($pref_id = '', $use_select = \true)
        {
        }
        /**
         * Has Entry
         * Moved to myCRED_Settings
         * @since 0.1
         * @version 1.3
         */
        public function has_entry($action = '', $ref_id = '', $user_id = '', $data = '', $point_type = '')
        {
        }
        /**
         * Available Template Tags
         * @since 1.4
         * @version 1.0
         */
        public function available_template_tags($available = array(), $custom = '')
        {
        }
        /**
         * Over Daily Limit
         * @since 1.0
         * @version 1.1.1
         */
        public function is_over_daily_limit($ref = '', $user_id = 0, $max = 0, $ref_id = \NULL)
        {
        }
        /**
         * Include Post Type
         * Checks if a given post type should be excluded
         * @since 0.1
         * @version 1.1
         */
        public function include_post_type($post_type)
        {
        }
        /**
         * Limit Query
         * Queries the myCRED log for the number of occurances of the specified
         * refernece and optional reference id for a specific user between two dates.
         * @param $ref (string) reference to search for, required
         * @param $user_id (int) user id to search for, required
         * @param $start (int) unix timestamp for start date, required
         * @param $end (int) unix timestamp for the end date, required
         * @param $ref_id (int) optional reference id to include in search
         * @returns number of entries found (int) or NULL if required params are missing
         * @since 1.4
         * @version 1.2
         */
        public function limit_query($ref = '', $user_id = 0, $start = 0, $end = 0, $ref_id = \NULL)
        {
        }
    }
    final class myCRED_Core
    {
        // Plugin Version
        public $version = '2.5.2';
        // Instnace
        protected static $_instance = \NULL;
        // Current session
        public $session = \NULL;
        // Modules
        public $modules = \NULL;
        // Point Types
        public $point_types = \NULL;
        // Account Object
        public $account = \NULL;
        /**
         * Setup Instance
         * @since 1.7
         * @version 1.0
         */
        public static function instance()
        {
        }
        /**
         * Not allowed
         * @since 1.7
         * @version 1.0
         */
        public function __clone()
        {
        }
        /**
         * Not allowed
         * @since 1.7
         * @version 1.0
         */
        public function __wakeup()
        {
        }
        /**
         * Get
         * @since 1.7
         * @version 1.0
         */
        public function __get($key)
        {
        }
        /**
         * Define
         * @since 1.7
         * @version 1.0
         */
        private function define($name, $value, $definable = \true)
        {
        }
        /**
         * Require File
         * @since 1.7
         * @version 1.0
         */
        public function file($required_file)
        {
        }
        /**
         * Construct
         * @since 1.7
         * @version 1.0
         */
        public function __construct()
        {
        }
        /**
         * Define Constants
         * First, we start with defining all requires constants if they are not defined already.
         * @since 1.7
         * @version 1.0.2
         */
        private function define_constants()
        {
        }
        public function myc_fs()
        {
        }
        /**
         * Include Plugin Files
         * @since 1.7
         * @since 2.4 Tools Import/ Export Added
         * @version 1.3
         */
        public function includes()
        {
        }
        /**
         * Internal Setup
         * @since 1.8
         * @version 1.0
         */
        private function include_hooks()
        {
        }
        /**
         * Internal Setup
         * @since 1.7
         * @version 1.0
         */
        private function internal()
        {
        }
        /**
         * Pre Init Globals
         * Globals that does not reply on external sources and can be loaded before init.
         * @since 1.7
         * @version 1.1
         */
        private function pre_init_globals()
        {
        }
        /**
         * WordPress
         * Next we hook into WordPress
         * @since 1.7
         * @version 1.0.1
         */
        public function wordpress()
        {
        }
        /**
         * After Plugins Loaded
         * Used to setup modules that are not replacable.
         * @since 1.7
         * @version 1.0
         */
        public function after_plugin()
        {
        }
        /**
         * After Themes Loaded
         * Used to load internal features via modules.
         * @since 1.7
         * @version 1.1
         */
        public function after_theme()
        {
        }
        /**
         * Load Shortcodes
         * @since 1.7
         * @version 1.1
         */
        public function load_shortcodes()
        {
        }
        /**
         * Init
         * General plugin setup during the init hook.
         * @since 1.7
         * @version 1.0
         */
        public function init()
        {
        }
        /**
         * Post Init Globals
         * Globals that needs to be defined after init. Mainly used for user related globals.
         * @since 1.7
         * @version 1.1
         */
        private function post_init_globals()
        {
        }
        /**
         * Load Plugin Textdomain
         * @since 1.7
         * @version 1.0
         */
        public function load_plugin_textdomain()
        {
        }
        /**
         * Register Assets
         * @since 1.7
         * @version 1.1
         */
        public function register_assets()
        {
        }
        /**
         * Setup Cron Jobs
         * @since 1.7
         * @version 1.0
         */
        private function setup_cron_jobs()
        {
        }
        /**
         * Register Importers
         * @since 1.7
         * @version 1.0.1
         */
        private function register_importers()
        {
        }
        /**
         * Front Enqueue Before
         * Enqueues scripts and styles that must run before content is loaded.
         * @since 1.7
         * @version 1.1
         */
        public function enqueue_front_before()
        {
        }
        /**
         * Front Enqueue After
         * Enqueuest that must run after content has loaded.
         * @since 1.7
         * @version 1.0
         */
        public function enqueue_front_after()
        {
        }
        /**
         * Admin Enqueue
         * @since 1.7
         * @version 1.2
         */
        public function enqueue_admin_before()
        {
        }
        /**
         * Widgets Init
         * @since 1.7
         * @version 1.0
         */
        public function widgets_init()
        {
        }
        /**
         * Admin Init
         * @since 1.7
         * @version 1.0
         */
        public function admin_init()
        {
        }
        /**
         * Load Importer: Log Entries
         * @since 1.4
         * @version 1.1
         */
        public function import_log_entries()
        {
        }
        /**
         * Load Importer: Point Balances
         * @since 1.4
         * @version 1.1
         */
        public function import_balances()
        {
        }
        /**
         * Load Importer: CubePoints
         * @since 1.4
         * @version 1.1.1
         */
        public function import_cubepoints()
        {
        }
        /**
         * Admin Menu
         * @since 1.7
         * @version 1.0
         */
        public function adjust_admin_menu()
        {
        }
        /**
         * Toolbar
         * @since 1.7
         * @version 1.0.1
         */
        public function adjust_toolbar($wp_admin_bar)
        {
        }
        /**
         * Cron: Reset Encryption Key
         * @since 1.2
         * @version 1.0
         */
        public function cron_reset_key()
        {
        }
        /**
         * Cron: Delete Leaderboard Cache
         * @since 1.7.9.1
         * @version 1.1
         */
        public function cron_delete_leaderboard_cache()
        {
        }
        /**
         * FIX: Add admin page style
         * @since 1.7
         * @version 1.0
         */
        public function fix_admin_page_styles()
        {
        }
        /**
         * Plugin Links
         * @since 1.7
         * @version 1.0
         */
        public function plugin_links($actions, $plugin_file, $plugin_data, $context)
        {
        }
        /**
         * Plugin Description Links
         * @since 1.7
         * @version 1.0.2
         */
        public function plugin_description_links($links, $file)
        {
        }
        /**
         * After Plugin Loaded
         * @since 2.5
         * @version 1.0.0
         */
        public function after_mycred_loaded()
        {
        }
    }
    class myCRED_Hook_WooCommerce_Reviews extends \myCRED_Hook
    {
    }
}
namespace {
    function mycred_get_user_meta($user_id, $key = '', $end = '', $unique = \false)
    {
    }
    function mycred_update_user_meta($user_id, $key = '', $end = '', $value = '', $previous = '')
    {
    }
    function mycred_get_post($post_id = \NULL)
    {
    }
}
