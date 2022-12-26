<?php

namespace LPFW\Abstracts {
    /**
     * Abstract class that the main plugin class needs to extend.
     *
     * @since 1.0.0
     */
    abstract class Abstract_Main_Plugin_Class
    {
        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */
        /**
         * Property that houses an array of all the "regular models" of the plugin.
         * All runnable models "SHOULD" be added inside this array for them to be ran.
         * All models inside this array is not automatically accessible to the external world.
         *
         * @since 1.0.0
         * @access protected
         * @var array
         */
        protected $_all_models = array();
        /**
         * Property that houses an array of all "public regular models" of the plugin.
         * Public models can be accessed and utilized by external entities via the main plugin class.
         *
         * When adding a public model, add them to the "_all_models" array first via the "add_to_all_plugin_models" function
         * for them to be ran.
         *
         * Then add them to "_models" array via the "add_to_public_models" function for them to be
         * accessible to the outside world.
         *
         * Ex. LPFW->Public_Model->some_function();
         *
         * @since 1.0.0
         * @access protected
         * @var array
         */
        protected $_models = array();
        /**
         * Property that houses an array of all "public helper classes" of the plugin.
         * Can be accessed and utilized by external entities via the main plugin class.
         *
         * @since 1.0.0
         * @access protected
         * @var array
         */
        protected $_helpers = array();
        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
        */
        /**
         * Add a "regular model" to the main plugin class "all models" array.
         *
         * @since 1.0.0
         * @access public
         *
         * @param Model_Interface $model Regular model.
         */
        public function add_to_all_plugin_models(\LPFW\Interfaces\Model_Interface $model)
        {
        }
        /**
         * Add a "regular model" to the main plugin class "public models" array.
         *
         * @since 1.0.0
         * @access public
         *
         * @param Model_Interface $model Regular model.
         */
        public function add_to_public_models(\LPFW\Interfaces\Model_Interface $model)
        {
        }
        /**
         * Add a "helper class instance" to the main plugin class "public helpers" array.
         *
         * @since 1.0.0
         * @access public
         *
         * @param object $helper Helper class instance.
         */
        public function add_to_public_helpers($helper)
        {
        }
        /**
         * Access public models and helper models.
         * We use this magic method to automatically access data from the _models and _helpers property so
         * we do not need to create individual methods to expose each of the properties.
         *
         * @since 1.0.0
         * @access public
         *
         * @throws \Exception Error message.
         * @param string $prop Model to access.
         */
        public function __get($prop)
        {
        }
    }
}
namespace LPFW\Helpers {
    class Helper_Functions
    {
        /**
         * This function is an alias for WP get_option(), but will return the default value if option value is empty or invalid.
         *
         * @since 1.0
         * @access public
         *
         * @param string $option_name   Name of the option of value to fetch.
         * @param mixed  $default_value Defaut option value.
         * @return mixed Option value.
         */
        public function get_option($option_name, $default_value = '')
        {
        }
    }
    class Plugin_Constants
    {
        public $COMMENT_ENTRY_ID_META = '_acfw_comment_loyalprog_entry_id';
        public $EARN_ACTION_PRODUCT_REVIEW = 'acfw_loyalprog_earn_action_product_review';
        public $EARN_POINTS_PRODUCT_REVIEW = 'acfw_loyalprog_earn_points_product_review';
    }
}
namespace LPFW\Interfaces {
    /**
     * Abstraction that provides contract relating to initialization.
     * Any model that needs some sort of initialization must implement this interface.
     *
     * @since 1.0.0
     */
    interface Initiable_Interface
    {
        /**
         * Contruct for initialization.
         *
         * @since 1.0.0
         * @access public
         */
        public function initialize();
    }
    /**
     * Abstraction that provides contract relating to plugin models.
     * All "regular models" should implement this interface.
     *
     * @since 1.0.0
     */
    interface Model_Interface
    {
        /**
         * Contract for running the model.
         *
         * @since 1.0.0
         * @access public
         */
        public function run();
    }
}
namespace LPFW\Models {
    /**
     * Model that houses the logic of extending the coupon system of woocommerce.
     * It houses the logic of handling coupon url.
     * Public Model.
     *
     * @since 1.0
     */
    class Earn_Points implements \LPFW\Interfaces\Model_Interface
    {
        /*
            |--------------------------------------------------------------------------
            | Class Properties
            |--------------------------------------------------------------------------
        */
        /**
         * Property that holds the single main instance of URL_Coupon.
         *
         * @since 1.0
         * @access private
         * @var Earn_Points
         */
        private static $_instance;
        /**
         * Model that houses all the plugin constants.
         *
         * @since 1.0
         * @access private
         * @var Plugin_Constants
         */
        private $_constants;
        /**
         * Property that houses all the helper functions of the plugin.
         *
         * @since 1.0
         * @access private
         * @var Helper_Functions
         */
        private $_helper_functions;
        /*
            |--------------------------------------------------------------------------
            | Class Methods
            |--------------------------------------------------------------------------
        */
        /**
         * Class constructor.
         *
         * @since 1.0
         * @access public
         *
         * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
         * @param Plugin_Constants           $constants        Plugin constants object.
         * @param Helper_Functions           $helper_functions Helper functions object.
         */
        public function __construct(\LPFW\Abstracts\Abstract_Main_Plugin_Class $main_plugin, \LPFW\Helpers\Plugin_Constants $constants, \LPFW\Helpers\Helper_Functions $helper_functions)
        {
        }
        /**
         * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
         *
         * @since 1.0
         * @access public
         *
         * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
         * @param Plugin_Constants           $constants        Plugin constants object.
         * @param Helper_Functions           $helper_functions Helper functions object.
         * @return Earn_Points
         */
        public static function get_instance(\LPFW\Abstracts\Abstract_Main_Plugin_Class $main_plugin, \LPFW\Helpers\Plugin_Constants $constants, \LPFW\Helpers\Helper_Functions $helper_functions)
        {
        }
        /*
            |--------------------------------------------------------------------------
            | Earn points methods
            |--------------------------------------------------------------------------
        */
        /**
         * Earn points action when products bought (run on order payment completion).
         *
         * @since 1.0
         * @access public
         *
         * @param int $order_id Order ID.
         */
        public function earn_points_buy_product_action($order_id)
        {
        }
        /**
         * Earn points action on blog comment posting/approval.
         *
         * @since 1.0
         * @access public
         *
         * @param int $comment_id Comment ID.
         * @param int $user_id    User ID.
         */
        public function earn_points_blog_comment_action($comment_id, $user_id)
        {
        }
        /**
         * Earn points action on product review posting/approval.
         *
         * @since 1.0
         * @access public
         *
         * @param int $comment_id Comment ID.
         * @param int $user_id    User ID.
         */
        public function earn_points_product_review_action($comment_id, $user_id)
        {
        }
        /**
         * Earn points action on customer first order.
         *
         * @since 1.0
         * @access public
         *
         * @param int $order_id Order ID.
         */
        public function earn_points_first_order_action($order_id)
        {
        }
        /**
         * Earn points action when user is created.
         *
         * @since 1.0
         * @access public
         *
         * @param int $user_id User ID.
         */
        public function earn_points_user_register_action($user_id)
        {
        }
        /**
         * Earn points action when customer spends equal or more than set breakpoints.
         *
         * @since 1.0
         * @access public
         *
         * @param int $order_id Order ID.
         */
        public function earn_points_high_spend_breakpoint($order_id)
        {
        }
        /**
         * Earn points action when order is done within set period.
         *
         * @since 1.0
         * @access public
         *
         * @param int $order_id Order ID.
         */
        public function earn_points_order_within_period_action($order_id)
        {
        }
        /*
            |--------------------------------------------------------------------------
            | Triggers to earn points.
            |--------------------------------------------------------------------------
        */
        /**
         * Trigger earn_points_buy_product_action method when status is either changed to 'processing' or 'completed'.
         *
         * @since 1.0
         * @access public
         *
         * @param int    $order_id   Order ID.
         * @param string $old_status Order old status.
         * @param string $new_status Order new status.
         */
        public function trigger_earn_points_buy_product_order_status_change($order_id, $old_status, $new_status)
        {
        }
        /**
         * Trigger comment related earn actions on comment post.
         *
         * @since 1.0
         * @access public
         *
         * @param int        $comment_id  Comment ID.
         * @param int|string $is_approved Check if comment is approved, not approved or spam.
         * @param array      $commentdata Comment data.
         */
        public function trigger_comment_earn_actions_on_insert($comment_id, $is_approved, $commentdata)
        {
        }
        /**
         * Trigger comment related earn actions on comment status change.
         *
         * @since 1.0
         * @access public
         *
         * @param string     $new_status New comment status.
         * @param string     $old_status Old comment status.
         * @param WP_Comment $comment    Comment object.
         */
        public function trigger_comment_earn_actions_on_status_change($new_status, $old_status, $comment)
        {
        }
        /**
         * Check if the customer's comment is the first one for the post/product.
         *
         * @since 1.2
         * @access private
         *
         * @param int    $comment_id Comment ID.
         * @param object $comment    WP_Comment or standard object with comment data.
         * @return bool True if first comment, false otherwise.
         */
        private function _is_user_comment_first_for_post($comment_id, $comment)
        {
        }
        /*
            |--------------------------------------------------------------------------
            | Waiting period for order related points
            |--------------------------------------------------------------------------
        */
        /**
         * Check if the order points to be earned has waiting period.
         *
         * @since 1.2
         * @access private
         *
         * @return bool True if waiting period, false otherwise.
         */
        private function _is_order_points_waiting_period()
        {
        }
        /**
         * Schedule approval of pending points for an order.
         *
         * @since 1.2
         * @access public
         *
         * @param int $order_id Order ID.
         */
        public function schedule_approve_order_pending_points($order_id)
        {
        }
        /**
         * Run the approval of order pending points.
         * This is triggered via WC action scheduler.
         *
         * @since 1.2
         * @access public
         *
         * @param int $order_id Order ID.
         */
        public function run_approve_order_pending_points($order_id)
        {
        }
        /**
         * Check if customer should earn loyalty points.
         *
         * @since 1.5
         * @access public
         *
         * @param int      $user_id User ID.
         * @param WC_Order $order   Order object.
         * @return bool True if allowed, false otherwise.
         */
        public function should_customer_earn_points($user_id, $order = null)
        {
        }
        /**
         * Check if a loyalty coupon has been applied on an order.
         *
         * @since 1.5
         * @access private
         *
         * @param \WC_Order $order Order object.
         * @return bool True if coupon has been applied, false otherwise.
         */
        private function _is_loyalty_coupon_applied_in_order($order)
        {
        }
        /**
         * Check if a store credits payment is applied on a given order.
         *
         * @since 1.8
         * @access private
         *
         * @param \WC_Order $order Order object.
         * @return bool True if store credits was applied, false otherwise.
         */
        private function _is_store_credits_applied_on_order($order)
        {
        }
        /*
            |--------------------------------------------------------------------------
            | Fulfill implemented interface contracts
            |--------------------------------------------------------------------------
        */
        /**
         * Execute Earn_Points class.
         *
         * @since 1.0
         * @access public
         * @inherit LPFW\Interfaces\Model_Interface
         */
        public function run()
        {
        }
    }
    /**
     * Model that houses the logic of extending the coupon system of woocommerce.
     * It houses the logic of handling coupon url.
     * Public Model.
     *
     * @since 1.0
     */
    class Entries implements \LPFW\Interfaces\Model_Interface, \LPFW\Interfaces\Initiable_Interface
    {
        /*
            |--------------------------------------------------------------------------
            | Class Properties
            |--------------------------------------------------------------------------
        */
        /**
         * Property that holds the single main instance of URL_Coupon.
         *
         * @since 1.0
         * @access private
         * @var Entries
         */
        private static $_instance;
        /**
         * Model that houses all the plugin constants.
         *
         * @since 1.0
         * @access private
         * @var Plugin_Constants
         */
        private $_constants;
        /**
         * Property that houses all the helper functions of the plugin.
         *
         * @since 1.0
         * @access private
         * @var Helper_Functions
         */
        private $_helper_functions;
        /*
            |--------------------------------------------------------------------------
            | Class Methods
            |--------------------------------------------------------------------------
        */
        /**
         * Class constructor.
         *
         * @since 1.0
         * @access public
         *
         * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
         * @param Plugin_Constants           $constants        Plugin constants object.
         * @param Helper_Functions           $helper_functions Helper functions object.
         */
        public function __construct(\LPFW\Abstracts\Abstract_Main_Plugin_Class $main_plugin, \LPFW\Helpers\Plugin_Constants $constants, \LPFW\Helpers\Helper_Functions $helper_functions)
        {
        }
        /**
         * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
         *
         * @since 1.0
         * @access public
         *
         * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
         * @param Plugin_Constants           $constants        Plugin constants object.
         * @param Helper_Functions           $helper_functions Helper functions object.
         * @return Entries
         */
        public static function get_instance(\LPFW\Abstracts\Abstract_Main_Plugin_Class $main_plugin, \LPFW\Helpers\Plugin_Constants $constants, \LPFW\Helpers\Helper_Functions $helper_functions)
        {
        }
        /*
            |--------------------------------------------------------------------------
            | CRUD Methods
            |--------------------------------------------------------------------------
        */
        /**
         * Insert points entry.
         *
         * @since 1.4
         * @access public
         *
         * @global wpdb $wpdb Object that contains a set of functions used to interact with a database.
         *
         * @param int    $user_id   User id.
         * @param string $type      Entry type (redeem or earn).
         * @param string $action    Entry action.
         * @param int    $amount    Entry amount.
         * @param int    $object_id Related object ID (posts, order, comments, etc.).
         * @return int|WP_Error Entry ID if successfull, error object on failure.
         */
        public function insert_entry($user_id, $type, $action, $amount, $object_id = 0)
        {
        }
        /**
         * Update points entry.
         *
         * @since 1.9
         * @access public
         *
         * @global wpdb $wpdb Object that contains a set of functions used to interact with a database.
         *
         * @param Point_Entry|int $entry_id Entry ID.
         * @param array           $changes  List of entry changes.
         * @return bool|WP_Error True if successfull, error object on failure.
         */
        public function update_entry($entry_id, $changes = array())
        {
        }
        /**
         * Delete points entry.
         *
         * @since 1.9
         * @access public
         *
         * @global wpdb $wpdb Object that contains a set of functions used to interact with a database.
         *
         * @param int $entry_id Entry ID.
         * @return bool|WP_Error true if successfull, error object on fail.
         */
        public function delete_entry($entry_id)
        {
        }
        /*
            |--------------------------------------------------------------------------
            | Alias method
            |--------------------------------------------------------------------------
        */
        /**
         * Insert entry alias for increasing points.
         *
         * @since 1.4
         * @access public
         *
         * @param int    $user_id           User ID.
         * @param int    $points            Points to increase.
         * @param string $source            Source key.
         * @param int    $related_object_id Related object ID.
         * @param bool   $is_pending        Flag if points entry is pending or not.
         * @return int|WP_Error Entry ID if successfull, error object on failure.
         */
        public function increase_points($user_id, $points, $source, $related_object_id = 0, $is_pending = false)
        {
        }
        /**
         * Insert entry alias for decreasing points.
         *
         * @since 1.4
         * @access public
         *
         * @param int    $user_id           User ID.
         * @param int    $points            Points to increase.
         * @param string $action            Action key.
         * @param int    $related_object_id Related object ID.
         * @return int|WP_Error Entry ID if successfull, error object on failure.
         */
        public function decrease_points($user_id, $points, $action, $related_object_id = 0)
        {
        }
        /*
            |--------------------------------------------------------------------------
            | Point_Entry methods
            |--------------------------------------------------------------------------
        */
        /**
         * Get customer's total points earned from an order.
         *
         * @since 1.2
         * @access public
         *
         * @param \WC_Order $order      Order object.
         * @param string    $deprecated Deprecated param.
         * @return Point_Entry[] Array of point entry objects.
         */
        public function get_user_points_data_from_order(\WC_Order $order, $deprecated = '')
        {
        }
        /**
         * Calculate the total points amount from a list of Point_Entry objects.
         *
         * @since 1.2
         * @access public
         *
         * @param array $entries List of Point_Entry objects.
         * @return int Total points.
         */
        public function calculate_entries_total_points($entries)
        {
        }
        /*
            |--------------------------------------------------------------------------
            | Points revoke related methods
            |--------------------------------------------------------------------------
        */
        /**
         * Get order status for revoke.
         *
         * @since 1.2
         * @access public
         *
         * @param string $new_status Order new status key.
         * @return array List of status for revoke.
         */
        public function is_order_new_status_for_revoke($new_status)
        {
        }
        /**
         * Revoke points from an order.
         *
         * @since 1.2
         * @access public
         *
         * @param \WC_Order $order Order object.
         */
        public function revoke_points_from_order(\WC_Order $order)
        {
        }
        /**
         * Undo revoking of points from an order.
         *
         * @since 1.2
         * @access public
         *
         * @param \WC_Order $order Order object.
         */
        public function undo_revoke_points_from_order(\WC_Order $order)
        {
        }
        /**
         * Approve pending points for order.
         *
         * @since 1.5.1
         * @access public
         *
         * @param \WC_Order $order Order object.
         */
        public function approve_pending_points_for_order(\WC_Order $order)
        {
        }
        /**
         * Cancel pending points for order by deleting the point entries and related metadata.
         *
         * @since 1.5.1
         * @access public
         *
         * @param \WC_Order $order Order object.
         */
        public function cancel_pending_points_for_order(\WC_Order $order)
        {
        }
        /*
            |--------------------------------------------------------------------------
            | Fulfill implemented interface contracts
            |--------------------------------------------------------------------------
        */
        /**
         * Execute codes that needs to run plugin activation.
         *
         * @since 1.0
         * @access public
         * @implements LPFW\Interfaces\Initializable_Interface
         */
        public function initialize()
        {
        }
        /**
         * Execute Entries class.
         *
         * @since 1.0
         * @access public
         * @inherit LPFW\Interfaces\Model_Interface
         */
        public function run()
        {
        }
    }
}
namespace {
    /**
     * The main plugin class.
     * 
     * @property \LPFW\Models\Earn_Points $Earn_Points
     * @property \LPFW\Models\Entries $Entries
     * @property \LPFW\Helpers\Helper_Functions $Helper_Functions
     * @property \LPFW\Helpers\Plugin_Constants $Plugin_Constants
     */
    class LPFW extends \LPFW\Abstracts\Abstract_Main_Plugin_Class
    {
        // phpcs:ignore
        /*
            |--------------------------------------------------------------------------
            | Class Properties
            |--------------------------------------------------------------------------
        */
        /**
         * Single main instance of Plugin LPFW plugin.
         *
         * @since  1.0.0
         * @access private
         * @var    LPFW
         */
        private static $_instance;
        /**
         * Array of missing external plugins/or plugins with invalid version that this plugin is depends on.
         *
         * @since  1.0.0
         * @access private
         * @var    array
         */
        private $_failed_dependencies;
        /*
            |--------------------------------------------------------------------------
            | Class Methods
            |--------------------------------------------------------------------------
        */
        /**
         * LPFW constructor.
         *
         * @since  1.0.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Ensure that only one instance of Plugin Boilerplate is loaded or can be loaded (Singleton Pattern).
         *
         * @since  1.0.0
         * @access public
         *
         * @return LPFW
         */
        public static function get_instance()
        {
        }
        /**
         * Add notice to notify users that some plugin dependencies of this plugin are missing.
         *
         * @since  1.0.0
         * @access public
         */
        public function missing_plugin_dependencies_notice()
        {
        }
        /**
         * Add notice to notify user that some plugin dependencies did not meet the required version for the current version of this plugin.
         *
         * @since  1.0.0
         * @access public
         */
        public function invalid_plugin_dependency_version_notice()
        {
        }
        /**
         * Display a notice to inform admin users that the required ACFWF modules are not yet active.
         *
         * @since 1.8
         * @access public
         */
        public function missing_acfwf_module_dependency()
        {
        }
        /**
         * The purpose of this function is to have a "general/global" deactivation function callback that is
         * guaranteed to execute when a plugin is deactivated.
         *
         * We have experienced in the past that WordPress does not require "activation" and "deactivation" callbacks,
         * regardless if its present or not, it just activates/deactivates the plugin.
         *
         * In our past experience, a plugin can be activated/deactivated without triggering its "activation" and/or
         * "deactivation" callback on cases where plugin dependency requirements failed or plugin dependency version
         * requirement failed.
         *
         * By registering this "deactivation" callback on constructor, we ensure this "deactivation" callback
         * is always triggered on plugin deactivation.
         *
         * We put inside the function body just the "general" deactivation codebase.
         * Model specific activation/deactivation code base should still reside inside its individual models.
         *
         * We do not need to register a general/global "activation" callback coz we do need all plugin requirements
         * passed before activating the plugin.
         *
         * @since  1.0.0
         * @access public
         *
         * @global object $wpdb Object that contains a set of functions used to interact with a database.
         *
         * @param boolean $network_wide Flag that determines whether the plugin has been activated network wid ( on multi site environment ) or not.
         */
        public function general_deactivation_code($network_wide)
        {
        }
        /**
         * Check for external plugin dependencies.
         *
         * @since  1.0.0
         * @access private
         *
         * @return mixed Array if there are missing plugin dependencies, True if all plugin dependencies are present.
         */
        private function _check_plugin_dependencies()
        {
        }
        /**
         * Check plugin dependency version requirements.
         *
         * @since  1.0.0
         * @access private
         *
         * @return mixed Array if there are invalid versioned plugin dependencies, True if all plugin dependencies have valid version.
         */
        private function _check_plugin_dependency_version_requirements()
        {
        }
        /**
         * Initialize plugin components.
         *
         * @since  1.0.0
         * @access private
         */
        private function _initialize_plugin_components()
        {
        }
        /**
         * Run the plugin. ( Runs the various plugin components ).
         *
         * @since  1.0.0
         * @access private
         */
        private function _run_plugin()
        {
        }
    }
}
namespace {
    /**
     * Returns the main instance of LPFW to prevent the need to use globals.
     *
     * @since  1.0.0
     * @return LPFW Main instance of the plugin.
     */
    function LPFW()
    {
    }
}
