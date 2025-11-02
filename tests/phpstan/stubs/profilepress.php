<?php

namespace ProfilePress\Core {
    class DBUpdates
    {
        public static $instance;
        const DB_VER = 11;
        public function init_options()
        {
        }
        public function maybe_update()
        {
        }
        public function update()
        {
        }
        public function update_routine_1()
        {
        }
        public function update_routine_2()
        {
        }
        public function update_routine_3()
        {
        }
        public function update_routine_4()
        {
        }
        public function update_routine_5()
        {
        }
        public function update_routine_6()
        {
        }
        public function update_routine_7()
        {
        }
        public function update_routine_8()
        {
        }
        public function update_routine_9()
        {
        }
        public function update_routine_10()
        {
        }
        public function update_routine_11()
        {
        }
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\Classes {
    class FormPreviewHandler
    {
        protected $_form_id = '';
        protected $_form_type = '';
        public function __construct()
        {
        }
        public function pre_get_posts($query)
        {
        }
        /**
         * @return string
         */
        function the_title($title)
        {
        }
        /**
         * @return string
         */
        function the_content()
        {
        }
        /**
         * @return string
         */
        function template_include()
        {
        }
        function post_thumbnail_html()
        {
        }
        public static function get_instance()
        {
        }
    }
    class PasswordReset
    {
        protected static function is_ajax()
        {
        }
        /**
         * Change the password reset title.
         *
         * @param \WP_User $user_data
         * @param string $key
         *
         * @return string
         */
        public static function reset_email_title($user_data, $key)
        {
        }
        /**
         * Return the formatted password reset message.
         *
         * @param string $content
         * @param \WP_User $user_data username
         * @param string $key activation key
         *
         * @return string
         */
        protected static function parse_placeholders($content, $user_data, $key)
        {
        }
        /**
         * Callback function for filter
         *
         * @param mixed $user_data
         * @param $key
         *
         * @return string formatted message for use by the password reset form
         */
        protected static function reset_email_message($user_data, $key)
        {
        }
        /**
         * Does the heavy lifting of resetting password
         *
         * @param $user_login string username or email
         *
         * @return bool|WP_Error
         */
        public static function retrieve_password_func($user_login)
        {
        }
        /**
         * The error or success message received from the retrieve_password_func
         *
         * @param string $user_login username/email
         * @param int|null $form_id password_reset id
         * @param string $is_melange is this melange
         *
         * @return string|array
         */
        public static function password_reset_status($user_login, $form_id = null, $is_melange = '')
        {
        }
        /**
         * Resets the user's password if the password reset form was submitted.
         */
        public static function do_password_reset()
        {
        }
        /**
         * Status messages for do password reset.
         *
         * @param string $type
         *
         * @return string
         */
        public static function do_password_reset_status_messages($type)
        {
        }
        /**
         * Do password reset.
         *
         * @return string
         */
        public static function parse_password_reset_error_codes()
        {
        }
        public static function get_instance()
        {
        }
    }
    class WelcomeEmailAfterSignup
    {
        public function __construct($user_id, $password)
        {
        }
        /**
         * Format the email message and replace placeholders with real values
         */
        public function parse_placeholders($content)
        {
        }
        public function send_welcome_mail()
        {
        }
    }
    class SendEmail
    {
        protected $sender_name;
        protected $sender_email;
        protected $content_type;
        protected $to;
        protected $subject;
        protected $message;
        public function __construct($to, $subject, $message)
        {
        }
        public function email_content_type()
        {
        }
        public function email_sender_name()
        {
        }
        public function email_sender_email()
        {
        }
        public function get_headers()
        {
        }
        public function before_send()
        {
        }
        public function after_send()
        {
        }
        public function templatified_email()
        {
        }
        /**
         * @param \WP_Error $wp_error
         */
        public function log_wp_mail_failed($wp_error)
        {
        }
        public function send()
        {
        }
    }
    /**
     * Indicate which form or social login where user signup from in ser listing page.
     *
     * @package ProfilePress\Core\Classes
     */
    class UserSignupLocationListingPage
    {
        public function __construct()
        {
        }
        public function add_column($columns)
        {
        }
        public function populate_column($status, $column_name, $user_id)
        {
        }
        /**
         * @return UserSignupLocationListingPage
         */
        public static function get_instance()
        {
        }
    }
    class UsernameEmailRestrictLogin
    {
        public function __construct()
        {
        }
        /**
         * @param WP_User|WP_Error|null $user
         * @param string $username [description]
         * @param string $password [description]
         *
         * @return WP_Error|WP_User
         */
        public function do_action($user, $username, $password)
        {
        }
        public static function get_instance()
        {
        }
    }
    class BlockRegistrations
    {
        public static function init()
        {
        }
        public static function do_action($reg_errors, $form_id, $user_data)
        {
        }
    }
    class PROFILEPRESS_sql
    {
        /** @param $meta_key
         * @param $meta_value
         * @param string $flag
         *
         * @return bool|int
         */
        public static function add_meta_data($meta_key, $meta_value, $flag = '')
        {
        }
        /**
         * @param $meta_key
         * @param $meta_value
         * @param $flag
         *
         * @return bool|int
         */
        public static function update_meta_data($id, $meta_key, $meta_value, $flag = '')
        {
        }
        /**
         * @param $meta_id
         * @param $meta_key
         * @param $meta_value
         *
         * @return false|int
         */
        public static function update_meta_value($meta_id, $meta_key, $meta_value)
        {
        }
        /**
         * @param $meta_id
         * @param $meta_key
         *
         * @return bool|mixed
         */
        public static function get_meta_value($meta_id, $meta_key)
        {
        }
        public static function get_meta_data_by_key($meta_key)
        {
        }
        /**
         * @param $id
         *
         * @return array|false
         */
        public static function get_meta_data_by_id($id)
        {
        }
        public static function get_meta_data_by_flag($flag)
        {
        }
        public static function delete_meta_data($meta_id)
        {
        }
        public static function delete_meta_data_by_flag($flag)
        {
        }
        public static function delete_meta_data_by_meta_key($meta_key)
        {
        }
        /**
         * Query for profile placement if user can view the his profile
         *
         * @return mixed
         */
        public static function get_profile_custom_fields()
        {
        }
        /**
         * Retrieve the profile field row of an ID
         *
         * @param int $id
         *
         * @return array
         */
        public static function get_profile_custom_field_by_id($id)
        {
        }
        /**
         * Retrieve the profile custom field by field key
         *
         * @param $field_key
         *
         * @return array
         */
        public static function get_profile_custom_field_by_key($field_key)
        {
        }
        public static function get_profile_custom_fields_by_types($types)
        {
        }
        public static function delete_profile_custom_field($id)
        {
        }
        /**
         * Return a list of created custom profile IDs.
         *
         * @return array
         */
        public static function get_profile_field_ids()
        {
        }
        /**
         * Check if a profile field's key exist in the database.
         *
         * @param int $field_key
         *
         * @return bool
         */
        public static function is_profile_field_key_exist($field_key)
        {
        }
        /**
         * Add custom field to DB
         *
         * @param string $label_name
         * @param string $key
         * @param string $description
         * @param string $type
         * @param string $options
         *
         * @return bool|int
         */
        public static function add_profile_field($label_name, $key, $description, $type, $options)
        {
        }
        /**
         * Update custom field in DB
         *
         * @param $id
         * @param string $label_name
         * @param string $key
         * @param string $description
         * @param string $type
         * @param string $options
         *
         * @return bool|int
         */
        public static function update_profile_field($id, $label_name, $key, $description, $type, $options)
        {
        }
        /***
         * Mark a select field as multi selectable.
         *
         * @param string $key
         *
         * @param int $id must have a value.
         *
         * @return bool
         */
        public static function add_multi_selectable($key, $id = 0)
        {
        }
        /***
         * Remove a select field as multi selectable.
         *
         * @param string $key
         *
         * @return bool
         */
        public static function delete_multi_selectable($key)
        {
        }
        /**
         * get radio buttons options of an added custom field
         */
        public static function get_field_option_values($field_key)
        {
        }
        /**
         * Get radio buttons options of an added custom field
         */
        public static function get_field_label($field_key)
        {
        }
        public static function get_field_type($field_key)
        {
        }
        public static function get_contact_info_fields()
        {
        }
        public static function get_contact_info_field_label($field_key)
        {
        }
        /** One time Passwordless login */
        public static function passwordless_insert_record($user_id, $token, $expiration)
        {
        }
        /**
         * Delete OTP record for a user.
         *
         * @param int $user_id
         *
         * @return false|int
         */
        public static function passwordless_delete_record($user_id)
        {
        }
        /**
         * Get the passwordless token of a user by ID
         *
         * @param int $user_id ID of user
         *
         * @return null|string
         */
        public static function passwordless_get_user_token($user_id)
        {
        }
        /**
         * Get the expiration time
         *
         * @param int $user_id
         *
         * @return null|string
         */
        public static function passwordless_get_expiration($user_id)
        {
        }
    }
    class ImageUploader
    {
        const AVATAR = 'avatar';
        const COVER_IMAGE = 'cover_image';
        /**
         * @param $image
         * @param string $image_id used to identify in the code if image is an avatar or cover photo.
         * @param mixed|void $path
         *
         * @return string|string[]|WP_Error|null
         */
        public static function process($image, $image_id = 'avatar', $path = PPRESS_AVATAR_UPLOAD_DIR)
        {
        }
    }
    trait WPProfileFieldParserTrait
    {
        /**
         * Add multipart/form-data to wordpress profile admin page
         */
        public function add_form_enctype()
        {
        }
        protected function date_field_picker($field_key)
        {
        }
        private function description_markup($description)
        {
        }
        public function parse_custom_field($user, $label_name, $field_key, $field_type, $options = [], $description = '')
        {
        }
        /**
         * Array of core user profile.
         *
         * This is useful in cases where say first and last name field is added so it can be added to buddypress
         * extended profile synced to WordPress user profile.
         *
         * @return array
         */
        public function core_user_fields()
        {
        }
    }
    class ExtensionManager
    {
        const DB_OPTION_NAME = 'ppress_extension_manager';
        const EMAIL_CONFIRMATION = 'email_confirmation';
        const PAYPAL = 'paypal';
        const MOLLIE = 'mollie';
        const RAZORPAY = 'razorpay';
        const PAYSTACK = 'paystack';
        const RECEIPT = 'receipt';
        const JOIN_BUDDYPRESS_GROUPS = 'join_buddypress_groups';
        const BUDDYPRESS_SYNC = 'buddypress_sync';
        const MULTISITE = 'multisite';
        const WOOCOMMERCE = 'woocommerce';
        const AKISMET = 'akismet';
        const CAMPAIGN_MONITOR = 'campaign_monitor';
        const MAILCHIMP = 'mailchimp';
        const POLYLANG = 'polylang';
        const PASSWORDLESS_LOGIN = 'passwordless_login';
        const USER_MODERATION = 'user_moderation';
        const RECAPTCHA = 'recaptcha';
        const SOCIAL_LOGIN = 'social_login';
        const CUSTOM_FIELDS = 'custom_fields';
        const TWOFA = 'TWOFA';
        const METERED_PAYWALL = 'metered_paywall';
        const LEARNDASH = 'learndash';
        const TUTORLMS = 'tutorlms';
        const SENSEI_LMS = 'sensei_lms';
        const LIFTERLMS = 'lifterlms';
        const INVITATION_CODES = 'invitation_codes';
        public static function is_premium()
        {
        }
        public static function class_map()
        {
        }
        public static function available_extensions()
        {
        }
        public static function is_enabled($extension_id)
        {
        }
    }
    class ShortcodeThemeFactory
    {
        /**
         * @param string $form_type
         * @param string $form_class
         *
         * @return bool|ThemeInterface|PasswordResetThemeInterface|LoginThemeInterface|RegistrationThemeInterface|MelangeThemeInterface
         */
        public static function make($form_type, $form_class)
        {
        }
    }
    class FormRepository
    {
        const SHORTCODE_BUILDER_TYPE = 'shortcode';
        const DRAG_DROP_BUILDER_TYPE = 'dragdrop';
        const BUILD_FROM_SCRATCH_THEME = 'BuildScratch';
        const LOGIN_TYPE = 'login';
        const REGISTRATION_TYPE = 'registration';
        const PASSWORD_RESET_TYPE = 'password-reset';
        const EDIT_PROFILE_TYPE = 'edit-profile';
        const MELANGE_TYPE = 'melange';
        const USER_PROFILE_TYPE = 'user-profile';
        const MEMBERS_DIRECTORY_TYPE = 'member-directory';
        const FORM_CLASS = 'form_class';
        const FORM_STRUCTURE = 'form_structure';
        const FORM_CSS = 'form_css';
        const SUCCESS_MESSAGE = 'success_message';
        const PROCESSING_LABEL = 'processing_label';
        const MELANGE_REGISTRATION_SUCCESS_MESSAGE = 'melange_registration_success_msg';
        const MELANGE_PASSWORD_RESET_SUCCESS_MESSAGE = 'melange_password_reset_success_msg';
        const MELANGE_EDIT_PROFILE_SUCCESS_MESSAGE = 'melange_edit_profile_success_msg';
        const PASSWORDLESS_LOGIN = 'passwordless_login';
        const REGISTRATION_USER_ROLE = 'registration_user_role';
        const DISABLE_USERNAME_REQUIREMENT = 'disable_username_requirement';
        const PASSWORD_RESET_HANDLER = 'password_reset_handler';
        const FORM_BUILDER_FIELDS_SETTINGS = 'form_builder_fields_settings';
        const METABOX_FORM_BUILDER_SETTINGS = 'form_builder_settings';
        public static function wpdb()
        {
        }
        public static function get_forms($form_type = false)
        {
        }
        /**
         * Check if an form name already exist.
         *
         * @param string $name
         *
         * @return bool
         */
        public static function name_exist($name)
        {
        }
        public static function form_id_exist($id, $form_type)
        {
        }
        public static function add_form_meta($form_id, $form_type, $key, $value)
        {
        }
        public static function update_form_meta($form_id, $form_type, $key, $value)
        {
        }
        public static function delete_form_meta($form_id, $form_type, $key = '')
        {
        }
        public static function get_processing_label($form_id, $form_type)
        {
        }
        public static function get_form_meta($form_id, $form_type, $key, $single = true)
        {
        }
        public static function get_form_first_id($form_type)
        {
        }
        public static function get_form_last_id($form_type)
        {
        }
        /**
         * Add new form to database.
         *
         * @param string $name
         * @param string $form_type
         * @param string $form_theme_class
         * @param string $builder_type
         *
         * @return false|int
         */
        public static function add_form($name, $form_type, $form_theme_class, $builder_type = 'shortcode')
        {
        }
        public static function clone_form($form_id, $form_type)
        {
        }
        public static function delete_form($form_id, $form_type)
        {
        }
        public static function update_form($form_id, $form_type, $name = '', $data = [])
        {
        }
        /**
         * Form name.
         *
         * @param int $form_id
         * @param string $form_type
         *
         * @return string
         */
        public static function get_name($form_id, $form_type)
        {
        }
        public static function get_form_ids($form_type)
        {
        }
        /**
         * Get form class
         *
         * @param int $form_id
         * @param string $form_type
         *
         * @return string
         */
        public static function get_form_class($form_id, $form_type)
        {
        }
        public static function is_login_passwordless($form_id)
        {
        }
        public static function is_drag_drop($form_id, $form_type)
        {
        }
        /**
         * PHP namespaced class of the form theme.
         *
         * @param $form_theme_class
         * @param $form_type
         *
         * @return string
         */
        public static function make_class($form_theme_class, $form_type)
        {
        }
        /**
         * @param $form_id
         * @param $form_theme_class
         * @param $form_type
         *
         * @return AbstractTheme|bool
         */
        public static function forge_class($form_id, $form_theme_class, $form_type)
        {
        }
        public static function form_builder_fields_settings($form_id, $form_type)
        {
        }
        public static function dnd_form_fields_json($form_id, $form_type, $defaults)
        {
        }
        public static function get_dnd_metabox_setting($key, $form_id, $form_type, $default = '')
        {
        }
        public static function dnd_class_instance($id, $form_type)
        {
        }
    }
    class EditUserProfile
    {
        public static function is_ajax()
        {
        }
        public static function get_success_message($form_id = 0, $is_melange = false)
        {
        }
        /**
         * @param $form_id
         * @param $redirect
         * @param bool $is_melange
         *
         * @return mixed|void the edit profile response be it error or success message
         */
        public static function process_func($form_id, $redirect, $is_melange = false)
        {
        }
        public static function get_current_user_id()
        {
        }
        /**
         * Update user profile.
         *
         * @param int $form_id ID of edit profile form
         * @param string $redirect URL to redirect to after edit profile.
         *
         * @return mixed
         */
        public static function update_user_profile($form_id, $redirect = '')
        {
        }
        /**
         * Escaped the POST data
         *
         * @param $post_data array raw post data
         *
         * @return array
         */
        public static function escaped_post_data($post_data)
        {
        }
        /**
         * @param $post_data array escaped $_POST Data @see self::escaped_post_data
         *
         * @param $valid_userdata array userdata valid for wp_update_user
         *
         * @return array
         */
        public static function custom_usermeta_data($post_data, $valid_userdata)
        {
        }
        /**
         * Remove user avatar and redirect. Triggered when JS is disabled.
         */
        public static function remove_user_avatar()
        {
        }
        /**
         * Remove user cover photo and redirect. Triggered when JS is disabled.
         */
        public static function remove_user_cover_image()
        {
        }
        /**
         * Core function that removes/delete the user's avatar
         */
        public static function remove_avatar_core()
        {
        }
        /**
         * Core function that removes/delete the user's cover photo
         *
         * @param int $user_id
         */
        public static function remove_cover_image($user_id = 0)
        {
        }
    }
    /**
     * Rewrite the profile page URL
     *
     * Rewrite the page URL to contain the "/profile" slug
     */
    class ProfileUrlRewrite
    {
        public function __construct()
        {
        }
        public function rewrite_function()
        {
        }
        public static function get_instance()
        {
        }
    }
    /** @todo needed? */
    class Geolocation
    {
        /**
         * Get current user IP Address.
         *
         * @return string
         */
        public static function get_ip_address()
        {
        }
        /**
         * Get user IP Address using an external service.
         * This can be used as a fallback for users on localhost where
         * get_ip_address() will be a local IP and non-geolocatable.
         *
         * @return string
         */
        public static function get_external_ip_address()
        {
        }
        /**
         * Geolocate an IP address.
         *
         * @param string $ip_address IP Address.
         * @param bool $fallback If true, fallbacks to alternative IP detection (can be slower).
         * @param bool $api_fallback If true, uses geolocation APIs if the database file doesn't exist (can be slower).
         *
         * @return array
         */
        public static function geolocate_ip($ip_address = '', $fallback = false, $api_fallback = true)
        {
        }
    }
    /**
     * This is a wrapper class for WP_PPress_Session / PHP $_SESSION
     *
     * @copyright   Copyright (c) 2015, Pippin Williamson
     * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
     */
    class PPRESS_Session
    {
        /**
         * Get things started
         *
         * Defines our WP_PPress_Session constants, includes the necessary libraries and
         * retrieves the WP Session instance
         */
        public function __construct()
        {
        }
        public function get_session_cookie_name()
        {
        }
        /**
         * Setup the WP_PPress_Session instance
         *
         * @return mixed
         */
        public function init()
        {
        }
        public function get_all()
        {
        }
        /**
         * Retrieve a session variable
         *
         * @param string $key Session key
         *
         * @return mixed Session variable
         */
        public function get($key)
        {
        }
        /**
         * Set a session variable
         *
         * @param string $key Session key
         * @param int|string|array $value Session variable
         *
         * @return mixed Session variable
         */
        public function set($key, $value)
        {
        }
        /**
         * Determines if we should start sessions
         *
         * @return bool
         */
        public function should_start_session()
        {
        }
        /**
         * Retrieve the URI blacklist
         *
         * These are the URIs where we never start sessions
         *
         * @return array
         */
        public function get_blacklist()
        {
        }
        public static function get_instance()
        {
        }
    }
    class GlobalSiteAccess
    {
        public static function init()
        {
        }
        /**
         * Exclude the redirect URL.
         * strtok() remove all query strings and trailing slash. @see https://stackoverflow.com/a/6975045/2648410
         *
         * @param $val
         *
         * @return string
         */
        public static function remove_query_string_trailing_slash($val)
        {
        }
        public static function global_redirect()
        {
        }
    }
    class RegistrationAuth
    {
        protected static $registration_form_status;
        public static function is_ajax()
        {
        }
        /**
         * Wrapper function for call to the welcome email class
         *
         * @param int $user_id
         * @param string $password
         * @param string $form_id
         */
        public static function send_welcome_email($user_id, $password = '', $form_id = '')
        {
        }
        /**
         *
         * Wrapper function for call to the automatic login after reg function
         *
         * @param int $user_id
         * @param int $form_id
         * @param string $redirect redirect url after registration
         *
         * @return mixed
         */
        public static function auto_login_after_reg($user_id, $form_id, $redirect)
        {
        }
        /**
         * Perform redirect after registration without logging the user in.
         *
         * @param int $form_id
         * @param string $no_login_redirect URL to redirect to.
         *
         * @return array
         */
        public static function no_login_redirect_after_reg($form_id, $no_login_redirect)
        {
        }
        /**
         * Register new users
         *
         * @param array $post user form submitted data
         * @param int $form_id Registration builder ID
         * @param string $redirect URL to redirect to after registration.
         *
         * @param bool $is_melange
         * @param string $no_login_redirect
         *
         * @return string|mixed|void
         */
        public static function register_new_user($post, $form_id = 0, $redirect = '', $is_melange = false, $no_login_redirect = '')
        {
        }
        /**
         * Array list of acceptable defined roles.
         *
         * @param int $form_id ID of registration form
         *
         * @return array
         */
        public static function acceptable_defined_roles($form_id)
        {
        }
    }
    /**
     * @package ProfilePress custom file upload PHP class
     */
    class FileUploader
    {
        /** init poop */
        public static function init()
        {
        }
        /**
         * Upload the file
         *
         * @param $file
         *
         * @return bool|WP_Error
         */
        public static function process($file, $field_key)
        {
        }
        /**
         * Error message of file upload converted from errorCode to readable message.
         *
         * @param int $code
         *
         * @return string
         */
        public static function codeToMessage($code)
        {
        }
        /**
         * @param array $file_array global $_File['field_key'] of the file.
         * @param string $error_message
         */
        public static function error_file_logger($file_array, $error_message)
        {
        }
    }
    class AjaxHandler
    {
        public function __construct()
        {
        }
        public function menu_bar($builder_type)
        {
        }
        public function drag_drop_build_your_own_tmp($builder_type, $form_type)
        {
        }
        public function form_template_single($theme, $builder_type)
        {
        }
        public function form_name_field($label = '', $placeholder = '')
        {
        }
        public function get_forms_by_builder_type($form_type = \ProfilePress\Core\Classes\FormRepository::LOGIN_TYPE, $builder_type = false)
        {
        }
        public function theme_listing($builder_type, $form_type)
        {
        }
        /**
         * Filter forms by type.
         */
        public function form_type_selection()
        {
        }
        /**
         * Create new form.
         */
        public function create_form()
        {
        }
        function ajax_delete_avatar()
        {
        }
        public function ajax_delete_profile_cover_image()
        {
        }
        function profile_fields_sortable_func()
        {
        }
        function payment_methods_sortable()
        {
        }
        function pp_contact_info_sortable_func()
        {
        }
        public function ajax_login_func()
        {
        }
        function ajax_signup_func()
        {
        }
        function ajax_passwordreset_func()
        {
        }
        function ajax_editprofile_func()
        {
        }
        public static function get_instance()
        {
        }
    }
    class DisableConcurrentLogins
    {
        public function __construct()
        {
        }
        /**
         * @param string $logged_in_cookie The logged-in cookie value.
         * @param int $expire The time the login grace period expires as a UNIX timestamp.
         *                                 Default is 12 hours past the cookie's expiration time.
         * @param int $expiration The time when the logged-in authentication cookie expires as a UNIX timestamp.
         *                                 Default is 14 days from now.
         * @param int $user_id User ID.
         * @param string $scheme Authentication scheme. Default 'logged_in'.
         * @param string $token User's session token to use for this cookie.
         */
        public function disable_concurrent_logins($logged_in_cookie, $expire, $expiration, $user_id, $scheme, $token)
        {
        }
        public static function get_instance()
        {
        }
    }
    /**
     * Alter default login, registration, password_reset login and logout url
     */
    class ModifyRedirectDefaultLinks
    {
        public function __construct()
        {
        }
        public function is_third_party_2fa_active()
        {
        }
        /**
         * Modify the lost password url returned by wp_lostpassword_url() function.
         *
         * @param $val
         *
         * @return string
         */
        public function lost_password_url_func($val)
        {
        }
        /**
         * Force redirection of default password reset to the page with custom one.
         */
        public function redirect_password_reset_page()
        {
        }
        /**
         * Modify the login url returned by wp_login_url()
         *
         * @param $url
         * @param string $redirect
         * @param bool $force_reauth
         *
         * @return string page with login shortcode
         */
        public function set_login_url_func($url, $redirect, $force_reauth)
        {
        }
        /**
         * Force redirect default login to page with login shortcode
         */
        public function redirect_login_page()
        {
        }
        /**
         * Modify the url returned by wp_registration_url().
         *
         * @return string page url with registration shortcode.
         */
        public function register_url_func($val)
        {
        }
        /**
         * force redirection of default registration to custom one
         */
        public function redirect_reg_page()
        {
        }
        /**
         * Add query string (url) to logout url which is url to redirect to after logout
         *
         * @param $logout_url string filter default login url to be modified
         * @param $redirect string where to redirect to after logout
         *
         * @return string
         */
        public function logout_url_func($logout_url, $redirect)
        {
        }
        /**
         * Redirect user edit profile (/wp-admin/profile.php) to "custom edit profile" page.
         */
        public function redirect_default_edit_profile_to_custom()
        {
        }
        public function author_link_func($url, $author_id)
        {
        }
        /**
         * @param $url
         * @param int $id comment ID
         * @param \WP_Comment $comment
         *
         * @return string|void
         */
        public function comment_author_url_to_profile($url, $id, $comment)
        {
        }
        /**
         * Redirect the default logout page (/wp-login.php?loggedout=true) to blog homepage
         */
        public function redirect_logout_page()
        {
        }
        public function redirect_bp_registration_page()
        {
        }
        /**
         * Rewrite buddypress registration url to PP custom url or WP's if not set.
         *
         * @param string $page
         *
         * @return string
         */
        public function rewrite_bp_registration_url($page)
        {
        }
        /**
         * Redirect author page to user's profile
         */
        public function redirect_author_page()
        {
        }
        public static function get_instance()
        {
        }
    }
    class UserAvatar
    {
        public function __construct()
        {
        }
        /**
         * Get user profile picture. Falls back to default avatar or gravatar set in settings.
         *
         * @param string|int $id_or_email
         * @param int $size
         * @param bool $original
         *
         * @return false|string
         */
        public static function get_avatar_complete_url($id_or_email, $size = '', $original = false)
        {
        }
        public static function user_has_pp_avatar($id_or_email)
        {
        }
        /**
         * @param $id_or_email
         *
         * @param string $size
         *
         * @return string
         */
        public static function get_pp_avatar_url($id_or_email, $size = '', $original = false)
        {
        }
        /**
         * HTML image for the user profile
         *
         * @param $id_or_email
         * @param string $size
         * @param string $alt
         * @param string $class
         * @param string $css_id
         * @param bool $original
         *
         * @return mixed
         */
        public static function get_avatar_img($id_or_email, $size = '96', $alt = '', $class = '', $css_id = '', $original = true)
        {
        }
        /**
         * Culled from get_avatar_data()
         *
         * @param mixed $id_or_email
         *
         * @return bool|int
         */
        public static function get_avatar_user_id($id_or_email)
        {
        }
        public static function get_instance()
        {
        }
    }
    class GDPR
    {
        public function __construct()
        {
        }
        public function wp_erase_data($erasers)
        {
        }
        public function erase_data($email_address)
        {
        }
        public function wp_export_data($exporters)
        {
        }
        /**
         * @return GDPR
         */
        public static function get_instance()
        {
        }
    }
    /**
     * Authorise login and redirect to the appropriate page
     *
     * currently used only by the tabbed widget.
     */
    class LoginAuth
    {
        /**
         * Called to validate login credentials
         *
         * @return string
         */
        public static function is_ajax()
        {
        }
        /**
         * Authenticate login
         *
         * @param string $username
         * @param string $password
         * @param string $remember_login
         * @param null|int $login_form_id
         * @param string $redirect
         *
         * @return mixed|string|void|WP_Error|\WP_User
         */
        public static function login_auth($username, $password, $remember_login = 'true', $login_form_id = 0, $redirect = '')
        {
        }
        public static function after_do_login()
        {
        }
        public static function wp_redirect_intercept()
        {
        }
    }
    class Miscellaneous
    {
        public function __construct()
        {
        }
        /**
         * Add a page display state for special ProfilePress pages in the page list table.
         *
         * @param array $post_states An array of post display states.
         * @param \WP_Post $post The current post object.
         *
         * @return array
         */
        function add_display_page_states($post_states, $post)
        {
        }
        /**
         * Register the template for a privacy policy.
         *
         * Note, this is just a suggestion and should be customized to meet your businesses needs.
         *
         */
        function register_privacy_policy_template()
        {
        }
        public function maybe_add_store_mode_admin_bar_menu($wp_admin_bar)
        {
        }
        public function store_mode_admin_bar_print_link_styles()
        {
        }
        public function action_links($actions, $plugin_file, $plugin_data, $context)
        {
        }
        /**
         * Show row meta on the plugin screen.
         *
         * @param mixed $links Plugin Row Meta
         * @param mixed $file Plugin Base file
         *
         * @return    array
         */
        public static function plugin_row_meta($links, $file)
        {
        }
        public function skip_ppress_shortcodes($shortcodes)
        {
        }
        public static function get_instance()
        {
        }
    }
    class AdminNotices
    {
        public function __construct()
        {
        }
        public function add_admin_body_class($classes)
        {
        }
        public function admin_notices_bucket()
        {
        }
        public function act_on_request()
        {
        }
        public function test_mode_notice()
        {
        }
        /**
         * Display one-time admin notice to review plugin at least 7 days after installation
         */
        public function review_plugin_notice()
        {
        }
        public function seo_friendly_permalink_not_set()
        {
        }
        /**
         * Let user avatar plugin users know it is now ProfilePress
         */
        public function wp_user_avatar_now_ppress_notice()
        {
        }
        public function addons_promo_notices()
        {
        }
        public function create_plugin_pages()
        {
        }
        public function connect_enabled_stripe_method()
        {
        }
        /**
         * Notice when user registration is disabled.
         */
        function registration_disabled_notice()
        {
        }
        public function removable_query_args($args = [])
        {
        }
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\Classes {
    class Autologin
    {
        public static function is_ajax()
        {
        }
        /**
         * Initialize class
         *
         * @param int $user_id
         * @param string $login_id
         * @param string $redirect
         *
         * @return  void|mixed
         */
        public static function initialize($user_id, $login_id = '', $redirect = '')
        {
        }
    }
    class FormShortcodeDefaults
    {
        protected $form_type;
        public function __construct($form_type)
        {
        }
        public function get_structure()
        {
        }
        public function get_css()
        {
        }
        public function success_message()
        {
        }
        public function user_profile_structure()
        {
        }
        public function login_structure()
        {
        }
        public function login_css()
        {
        }
        public function registration_structure()
        {
        }
        public function registration_css()
        {
        }
        public function registration_success_message()
        {
        }
        public function edit_profile_structure()
        {
        }
        public function edit_profile_css()
        {
        }
        public function edit_profile_success_message()
        {
        }
        public function password_reset_structure()
        {
        }
        public function password_reset_css()
        {
        }
        public function password_reset_handler()
        {
        }
        public function password_reset_success_message()
        {
        }
    }
    class BuddyPressBbPress
    {
        public function __construct()
        {
        }
        public static function override_bp_profile_url($domain, $user_id, $user_nicename, $user_login)
        {
        }
        public static function override_bbp_profile_url($user_id)
        {
        }
        /**
         * Override HTML BP avatar output.
         *
         * @param string $image_in_html
         * @param array $params
         * @param int $item_id
         *
         * @return mixed
         */
        public static function override_html_avatar($image_in_html, $params, $item_id)
        {
        }
        /**
         * Override BP avatar url.
         *
         * @param string $image_url
         * @param array $params
         *
         * @return bool|mixed|string
         */
        public static function override_avatar_url($image_url, $params)
        {
        }
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages {
    abstract class AbstractSettingsPage
    {
        protected $option_name;
        public static $parent_menu_url_map = [];
        public function register_core_menu()
        {
        }
        /** --------------------------------------------------------------- */
        // commented out to prevent any fatal error
        //abstract function default_header_menu();
        public function header_menu_tabs()
        {
        }
        public function header_submenu_tabs()
        {
        }
        public function settings_page_header($active_menu, $active_submenu)
        {
        }
        public function settings_page_header_menus($active_menu)
        {
        }
        public function settings_page_header_sub_menus($active_menu, $active_submenu)
        {
        }
        public function active_menu_tab()
        {
        }
        public function active_submenu_tab()
        {
        }
        public function admin_page_callback()
        {
        }
        /** --------------------------------------------------------------- */
        /**
         * Register core settings.
         *
         * @param Custom_Settings_Page_Api $instance
         * @param bool $remove_sidebar
         */
        public static function register_core_settings(\ProfilePress\Custom_Settings_Page_Api $instance, $remove_sidebar = false)
        {
        }
        public static function sidebar_args()
        {
        }
        public static function sidebar_support_docs()
        {
        }
        public static function mailoptin_ad_block()
        {
        }
        public static function page_dropdown($id, $appends = [], $args = ['skip_append_default_select' => false])
        {
        }
        protected function custom_text_input($id, $placeholder = '')
        {
        }
    }
    class ExtensionsSettingsPage extends \ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage
    {
        protected $spInstance;
        public function __construct()
        {
        }
        public function default_header_menu()
        {
        }
        public function add_extension_tab($tabs)
        {
        }
        public function extension_view($tabs)
        {
        }
        public function admin_page_title()
        {
        }
        public function register_settings_page()
        {
        }
        public function settings_page_function()
        {
        }
        public function admin_settings_page_callback()
        {
        }
        public static function get_instance()
        {
        }
    }
    class MemberDirectories extends \ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage
    {
        /**
         * @var FormList
         */
        protected $wplist_instance;
        protected $DragDropClassInstance;
        public function __construct()
        {
        }
        public function admin_page_title() : string
        {
        }
        public function register_settings_page()
        {
        }
        public function default_header_menu()
        {
        }
        /**
         * Save screen option.
         *
         * @param string $status
         * @param string $option
         * @param string $value
         *
         * @return mixed
         */
        public function set_screen($status, $option, $value)
        {
        }
        /**
         * Screen options
         */
        public function screen_option()
        {
        }
        /**
         * @param $echo
         *
         * @return string|void
         */
        public function live_form_preview_btn($echo = true)
        {
        }
        /**
         * Build the settings page structure. I.e tab, sidebar.
         *
         * @return mixed|void
         */
        public function settings_admin_page_callback()
        {
        }
        public function add_new_form_button()
        {
        }
        /**
         * @param string $content
         * @param string $option_name settings Custom_Settings_Page_Api option name.
         *
         * @return string
         */
        public function wp_list_table($content, $option_name)
        {
        }
        /**
         * @return self
         */
        public static function get_instance()
        {
        }
    }
    class FormList extends \WP_List_Table
    {
        public function __construct($wpdb)
        {
        }
        public function get_forms($per_page, $current_page = 1, $form_type = '')
        {
        }
        /**
         * Returns the count of records in the database.
         *
         * @param string $form_type
         *
         * @return null|string
         */
        public function record_count($form_type = '')
        {
        }
        public static function customize_url($form_id, $form_type, $builder_type = \ProfilePress\Core\Classes\FormRepository::SHORTCODE_BUILDER_TYPE)
        {
        }
        public static function delete_url($form_id, $form_type)
        {
        }
        public static function clone_url($form_id, $form_type)
        {
        }
        public static function preview_url($form_id, $form_type)
        {
        }
        /**
         * Text displayed when no email optin form is available
         */
        public function no_items()
        {
        }
        /**
         *  Associative array of columns
         *
         * @return array
         */
        public function get_columns()
        {
        }
        /**
         * Render the bulk edit checkbox
         *
         * @param array $item
         *
         * @return string
         */
        function column_cb($item)
        {
        }
        /**
         * Method for Title column
         *
         * @param array $item an array of DB data
         *
         * @return string
         */
        public function column_title($item)
        {
        }
        /**
         * Method for Shortcode column
         *
         * @param array $item an array of DB data
         *
         * @return string
         */
        public function column_shortcode($item)
        {
        }
        public function column_builder($item)
        {
        }
        public function column_default($item, $column_name)
        {
        }
        /**
         * Columns to make sortable.
         *
         * @return array
         */
        public function get_sortable_columns()
        {
        }
        /**
         * Returns an associative array containing the bulk action
         *
         * @return array
         */
        public function get_bulk_actions()
        {
        }
        /**
         * Handles data query and filter, sorting, and pagination.
         *
         * @param string $form_type
         */
        public function prepare_items($form_type = '')
        {
        }
        public function process_actions()
        {
        }
        /**
         * @return FormList
         */
        public static function get_instance()
        {
        }
    }
    class MembersDirectoryList extends \ProfilePress\Core\Admin\SettingsPages\FormList
    {
        public function no_items()
        {
        }
    }
    class IDUserColumn
    {
        public function __construct()
        {
        }
        /**
         * @param $columns
         *
         * @return mixed
         */
        public function add_user_id_column($columns)
        {
        }
        /**
         * @param $value
         * @param $column_name
         * @param $user_id
         *
         * @return mixed
         */
        public function show_user_id($value, $column_name, $user_id)
        {
        }
        /**
         * @return self
         */
        public static function get_instance()
        {
        }
    }
    class LicenseUpgrader
    {
        public function __construct()
        {
        }
        public function add_menu($tabs)
        {
        }
        public function admin_page()
        {
        }
        public function admin_settings_page_callback()
        {
        }
        public function settings_enqueues()
        {
        }
        public function generate_url()
        {
        }
        public function process()
        {
        }
        public static function get_instance()
        {
        }
    }
    class GeneralSettings extends \ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage
    {
        public $settingsPageInstance;
        public function __construct()
        {
        }
        public function register_menu_page()
        {
        }
        public function default_header_menu()
        {
        }
        public function header_menu_tabs()
        {
        }
        public function integrations_submenu_tabs()
        {
        }
        public function header_submenu_tabs()
        {
        }
        public function screen_option()
        {
        }
        public function settings_admin_page_callback()
        {
        }
        public function install_missing_db_tables()
        {
        }
        public function custom_sanitize()
        {
        }
        public function js_script()
        {
        }
        public static function get_instance()
        {
        }
    }
    class AddNewForm
    {
        /**
         * Build the settings page structure. I.e tab, sidebar.
         */
        public function settings_admin_page()
        {
        }
        /**
         */
        public function sub_header()
        {
        }
        /**
         * Display list of optin
         */
        public function form_list()
        {
        }
        public function back_to_overview()
        {
        }
        /**
         * @return AddNewForm
         */
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder {
    class DragDropBuilder
    {
        public $saved_data = [];
        public $form_type;
        public $form_class;
        public $form_id;
        public $meta_box_settings;
        /** @var AbstractTheme */
        public $theme_class_instance;
        public function __construct()
        {
        }
        public function standard_fields()
        {
        }
        public function extra_fields()
        {
        }
        public function defined_fields($woocommerce_field = false)
        {
        }
        public function is_drag_drop_page()
        {
        }
        public function save_form()
        {
        }
        public function form_fields_json()
        {
        }
        public function icon_picker_template()
        {
        }
        public function print_template()
        {
        }
        public function sidebar_fields_block_tmpl()
        {
        }
        public function builder_header()
        {
        }
        public function sidebar_section()
        {
        }
        public function admin_page()
        {
        }
        public function registration_settings()
        {
        }
        public function edit_profile_settings()
        {
        }
        public function login_settings()
        {
        }
        public function password_reset_settings()
        {
        }
        public function meta_box()
        {
        }
        public function js_wp_editor_enqueue()
        {
        }
        public function js_wp_editor()
        {
        }
        public static function get_instance()
        {
        }
    }
    class Metabox
    {
        protected $args;
        protected $saved_values;
        protected $theme_class_instance;
        /** @var DragDropBuilder */
        protected $DragDropBuilderInstance;
        /**
         * @param array $args
         * @param AbstractTheme $theme_class_instance
         * @param DragDropBuilder $DragDropBuilderInstance
         */
        public function __construct($args, $theme_class_instance, $DragDropBuilderInstance)
        {
        }
        public function text($name, $options)
        {
        }
        public function number($name, $options)
        {
        }
        public function color($name, $options)
        {
        }
        public function upload($name, $options)
        {
        }
        public function textarea($name, $options)
        {
        }
        public function checkbox($name, $options)
        {
        }
        public function select($name, $options)
        {
        }
        public function custom($name, $options)
        {
        }
        public function select2($name, $options)
        {
        }
        protected function get_google_fonts($amount = 300)
        {
        }
        public function font_family($name, $setting)
        {
        }
        public function tab_radio($name, $options)
        {
        }
        public function build()
        {
        }
    }
    interface FieldInterface
    {
        public function field_type();
        public static function field_icon();
        public function field_title();
        public function field_bar_title();
        public function field_settings();
        public function field_settings_tabs();
        public function category();
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Controls {
    class WPEditor
    {
        public $args;
        public function __construct($args)
        {
        }
        public function render()
        {
        }
    }
    class IconPicker
    {
        public $args;
        public function __construct($args)
        {
        }
        public function render()
        {
        }
    }
    class Input
    {
        public $args;
        public function __construct($args)
        {
        }
        public function render()
        {
        }
    }
    class Textarea
    {
        public $args;
        public function __construct($args)
        {
        }
        public function render()
        {
        }
    }
    class Select
    {
        public $args;
        public function __construct($args)
        {
        }
        public function render()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder {
    abstract class FieldBase implements \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldInterface
    {
        const STANDARD_CATEGORY = 'standard';
        const EXTRA_CATEGORY = 'extra';
        const INPUT_FIELD = 'input';
        const WPEDITOR_FIELD = 'wpeditor';
        const SELECT_FIELD = 'select';
        const TEXTAREA_FIELD = 'textarea';
        const ICON_PICKER_FIELD = 'icon_picker';
        const GENERAL_TAB = 'pp_tab_general';
        const SETTINGS_TAB = 'pp_tab_settings';
        const STYLE_TAB = 'pp_style_settings';
        const COLUMN_SETTINGS = 'pp_column_settings';
        const COLUMN_2_SETTINGS = 'pp_column_2_settings';
        public $tag_name;
        public $form_id;
        public $form_type;
        public $form_class;
        public function __construct()
        {
        }
        public function field_bar_title()
        {
        }
        public function category()
        {
        }
        public function register_field()
        {
        }
        public function field_settings_tabs()
        {
        }
        public function print_template()
        {
        }
        public function field_settings_modal_tmpl()
        {
        }
        public function field_bar_tmpl()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields {
    class Email extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields\EditProfile {
    class ShowProfilePicture extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class ShowCoverImage extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields {
    class RadioButtons extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function category()
        {
        }
        public function field_settings()
        {
        }
    }
    class Init
    {
        public static function init()
        {
        }
    }
    class TextBox extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function category()
        {
        }
        public function field_settings()
        {
        }
    }
    class Number extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function category()
        {
        }
        public function field_settings()
        {
        }
    }
    class Website extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class CoverImage extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class ProfilePicture extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class CFPassword extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function category()
        {
        }
        public function field_settings()
        {
        }
    }
    class Country extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function category()
        {
        }
        public function field_settings()
        {
        }
    }
    class Username extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class PasswordStrengthMeter extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class SingleCheckbox extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function category()
        {
        }
        public function field_settings()
        {
        }
    }
    class SelectDropdown extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function category()
        {
        }
        public function field_settings()
        {
        }
    }
    class LastName extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class Nickname extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class CheckboxList extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function category()
        {
        }
        public function field_settings()
        {
        }
    }
    class DisplayName extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class ConfirmPassword extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class Password extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class Date extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function category()
        {
        }
        public function field_settings()
        {
        }
    }
    class HTML extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_bar_title()
        {
        }
        public function field_settings()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields\PasswordReset {
    class Userlogin extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_bar_title()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields {
    class ConfirmEmail extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class Bio extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields\UserProfile {
    class Email extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class Website extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class Username extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class LastName extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class Nickname extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class DisplayName extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class Bio extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class CustomField extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class FirstName extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields {
    class SelectRole extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class Textarea extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function category()
        {
        }
        public function field_settings()
        {
        }
    }
    class FirstName extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields\Login {
    class Password extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class Userlogin extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_bar_title()
        {
        }
        public function field_title()
        {
        }
        public function field_settings()
        {
        }
    }
    class RememberLogin extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        public static function field_icon()
        {
        }
        public function field_title()
        {
        }
        public function field_bar_title()
        {
        }
        public function field_settings()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields\DefinedFieldTypes {
    class Radio extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public static function field_icon()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public function field_title()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public function category()
        {
        }
        public function field_settings()
        {
        }
    }
    class Agreeable extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public static function field_icon()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public function field_title()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public function category()
        {
        }
        public function field_settings()
        {
        }
    }
    class Password extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public static function field_icon()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public function field_title()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public function category()
        {
        }
        public function field_settings()
        {
        }
    }
    class Date extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public static function field_icon()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public function field_title()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public function category()
        {
        }
        public function field_settings()
        {
        }
    }
    class Input extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public static function field_icon()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public function field_title()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public function category()
        {
        }
        public function field_settings()
        {
        }
    }
    class Select extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public static function field_icon()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public function field_title()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public function category()
        {
        }
        public function field_settings()
        {
        }
    }
    class Checkbox extends \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase
    {
        public function field_type()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public static function field_icon()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public function field_title()
        {
        }
        /**
         * Interface contract fulfillment
         *
         * Dynamically implemented by DragDropBuilder::defined_custom_fields()
         */
        public function category()
        {
        }
        public function field_settings()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages {
    class AdminFooter
    {
        public function __construct()
        {
        }
        /**
         * Add rating links to the admin dashboard
         *
         * @param       string $footer_text The existing footer text
         *
         * @return      string
         */
        public function admin_rate_us($footer_text)
        {
        }
        /**
         * @return AdminFooter
         */
        public static function get_instance()
        {
        }
    }
    class MailOptin
    {
        const SLUG = 'ppress-mailoptin';
        public function __construct()
        {
        }
        public function ppress_install_plugin()
        {
        }
        public function ppress_activate_plugin()
        {
        }
        public function register_settings_page()
        {
        }
        public function enqueue_assets()
        {
        }
        /**
         * JS Strings.
         */
        protected function get_js_strings()
        {
        }
        /**
         * Generate and output page HTML.
         */
        public function output()
        {
        }
        /**
         * Generate and output heading section HTML.
         */
        protected function output_section_heading()
        {
        }
        /**
         * Generate and output screenshot section HTML.
         */
        protected function output_section_screenshot()
        {
        }
        /**
         * Generate and output step 'Install' section HTML.
         */
        protected function output_section_step_install()
        {
        }
        /**
         * Generate and output step 'Setup' section HTML.
         */
        protected function output_section_step_setup()
        {
        }
        /**
         * Step 'Install' data.
         */
        protected function get_data_step_install()
        {
        }
        /**
         * Step 'Setup' data.
         */
        protected function get_data_step_setup()
        {
        }
        /**
         * Ajax endpoint. Check plugin setup status.
         * Used to properly init step 'Setup' section after completing step 'Install'.
         */
        public function ajax_check_plugin_status()
        {
        }
        /**
         * Whether MailOptin plugin configured or not.
         */
        protected function is_configured()
        {
        }
        /**
         * Whether MailOptin plugin active or not.
         */
        protected function is_activated()
        {
        }
        public function redirect_to_mailoptin_settings()
        {
        }
        /**
         * @return self
         */
        public static function get_instance()
        {
        }
    }
    class FuseWP
    {
        const SLUG = 'ppress-fusewp';
        public function __construct()
        {
        }
        public function ppress_install_plugin()
        {
        }
        public function ppress_activate_plugin()
        {
        }
        public function register_settings_page()
        {
        }
        public function enqueue_assets()
        {
        }
        /**
         * JS Strings.
         */
        protected function get_js_strings()
        {
        }
        /**
         * Generate and output page HTML.
         */
        public function output()
        {
        }
        /**
         * Generate and output heading section HTML.
         */
        protected function output_section_heading()
        {
        }
        /**
         * Generate and output screenshot section HTML.
         */
        protected function output_section_screenshot()
        {
        }
        /**
         * Generate and output step 'Install' section HTML.
         */
        protected function output_section_step_install()
        {
        }
        /**
         * Generate and output step 'Setup' section HTML.
         */
        protected function output_section_step_setup()
        {
        }
        /**
         * Step 'Install' data.
         */
        protected function get_data_step_install()
        {
        }
        /**
         * Step 'Setup' data.
         */
        protected function get_data_step_setup()
        {
        }
        /**
         * Ajax endpoint. Check plugin setup status.
         * Used to properly init step 'Setup' section after completing step 'Install'.
         */
        public function ajax_check_plugin_status()
        {
        }
        /**
         * Whether FuseWP plugin configured or not.
         */
        protected function is_configured()
        {
        }
        /**
         * Whether FuseWP plugin active or not.
         */
        protected function is_activated()
        {
        }
        public function redirect_to_fusewp_settings()
        {
        }
        /**
         * @return self
         */
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\Membership\Emails {
    trait EmailDataTrait
    {
        public function get_order_placeholders()
        {
        }
        public function get_subscription_placeholders()
        {
        }
        public function get_order_receipt_content($renewal = false)
        {
        }
        public function get_subscription_cancelled_content()
        {
        }
        public function get_subscription_completed_content()
        {
        }
        public function get_subscription_expired_content()
        {
        }
        public function get_subscription_renewal_reminder_content($expiration = false)
        {
        }
        public function get_new_order_admin_notification_content()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\EmailSettings {
    class EmailSettingsPage
    {
        use \ProfilePress\Core\Membership\Emails\EmailDataTrait;
        const ACCOUNT_EMAIL_TYPE = 'account';
        const ORDER_EMAIL_TYPE = 'order';
        const SUBSCRIPTION_EMAIL_TYPE = 'subscription';
        public $email_notification_list_table;
        public $settingsPageInstance;
        public function __construct()
        {
        }
        public function screen_option()
        {
        }
        public function menu_tab($tabs)
        {
        }
        public function get_admin_title()
        {
        }
        /**
         * @return mixed|void
         */
        public function email_notifications($type = '')
        {
        }
        public function email_edit_screen_setup()
        {
        }
        public function emails_admin_page()
        {
        }
        public function email_settings_admin_page()
        {
        }
        public function handle_email_preview()
        {
        }
        protected function placeholder_tags_table($placeholders)
        {
        }
        public function toggle_field_js_script()
        {
        }
        public static function get_instance()
        {
        }
    }
    trait CustomizerTrait
    {
        public function modify_customizer_publish_button()
        {
        }
        public function clean_up_customizer()
        {
        }
    }
    class DefaultTemplateCustomizer
    {
        use \ProfilePress\Core\Admin\SettingsPages\EmailSettings\CustomizerTrait;
        const DBPREFIX = 'ppress_email';
        const body_section = 'ppress_email_template_body';
        const header_section = 'ppress_email_template_header';
        const footer_section = 'ppress_email_template_footer';
        protected static $site_title;
        public function __construct()
        {
        }
        public static function get_customizer_value($setting)
        {
        }
        public function monkey_patch_customizer_payload()
        {
        }
        public function include_customizer_template()
        {
        }
        public function set_customizer_urls()
        {
        }
        public static function get_prefixed_id($setting)
        {
        }
        public function remove_sections($active, $section)
        {
        }
        public function rewrite_customizer_panel_description($translations, $text, $domain)
        {
        }
        public function customizer_register($wp_customize)
        {
        }
        /**
         * @param \WP_Customize_Manager $wp_customize
         */
        public function sections($wp_customize)
        {
        }
        public static function defaults($setting)
        {
        }
        /**
         * @param \WP_Customize_Manager $wp_customize
         */
        public function settings($wp_customize)
        {
        }
        /**
         * @param \WP_Customize_Manager $wp_customize
         */
        public function controls($wp_customize)
        {
        }
        public static function get_instance()
        {
        }
    }
    class WPListTable extends \WP_List_Table
    {
        public $items;
        public function __construct($data)
        {
        }
        public function no_items()
        {
        }
        public function get_columns()
        {
        }
        public function display_tablenav($which)
        {
        }
        public function column_default($item, $column_name)
        {
        }
        public function column_title($item)
        {
        }
        public function prepare_items()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages {
    class Forms extends \ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage
    {
        /**
         * @var FormList
         */
        protected $forms_instance;
        protected $DragDropClassInstance;
        public function __construct()
        {
        }
        public function admin_page_title()
        {
        }
        public function register_menu_page()
        {
        }
        public function default_header_menu()
        {
        }
        /**
         * Sub-menu header for form types.
         */
        public function form_sub_header()
        {
        }
        /**
         * Save screen option.
         *
         * @param string $status
         * @param string $option
         * @param string $value
         *
         * @return mixed
         */
        public function set_screen($status, $option, $value)
        {
        }
        /**
         * Screen options
         */
        public function screen_option()
        {
        }
        /**
         * @param $echo
         *
         * @return string|void
         */
        public function live_form_preview_btn($echo = true)
        {
        }
        public function no_form_exist_redirect($form_id, $form_type)
        {
        }
        /**
         * Build the settings page structure. I.e tab, sidebar.
         *
         * @return mixed|void
         */
        public function settings_admin_page_callback()
        {
        }
        public function add_new_form_button()
        {
        }
        /**
         * @param string $content
         * @param string $option_name settings Custom_Settings_Page_Api option name.
         *
         * @return string
         */
        public function wp_list_table($content, $option_name)
        {
        }
        /**
         * @return Forms
         */
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\Membership {
    class FileDownloads
    {
        public function __construct()
        {
        }
        public function header_sub_menu_tab($tabs)
        {
        }
        public function file_downloads_page()
        {
        }
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\Membership\ExportPage {
    class SettingsPage extends \ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage
    {
        function __construct()
        {
        }
        public function admin_page_title($title = '')
        {
        }
        public function settings_page_function()
        {
        }
        public function handle_export()
        {
        }
        public function admin_settings_page_callback()
        {
        }
        public static function get_instance()
        {
        }
    }
    abstract class AbstractExport
    {
        protected $form = [];
        public function __construct($form)
        {
        }
        protected abstract function headers();
        protected abstract function get_data($page = 1, $limit = 9999);
        public function execute()
        {
        }
    }
    class PlansExport extends \ProfilePress\Core\Admin\SettingsPages\Membership\ExportPage\AbstractExport
    {
        protected function headers()
        {
        }
        public function get_data($page = 1, $limit = 9999)
        {
        }
    }
    class CustomersExport extends \ProfilePress\Core\Admin\SettingsPages\Membership\ExportPage\AbstractExport
    {
        protected function headers()
        {
        }
        public function get_data($page = 1, $limit = 9999)
        {
        }
    }
    class OrdersExport extends \ProfilePress\Core\Admin\SettingsPages\Membership\ExportPage\AbstractExport
    {
        protected function headers()
        {
        }
        public function get_data($page = 1, $limit = 9999)
        {
        }
    }
    class ProductSalesExport extends \ProfilePress\Core\Admin\SettingsPages\Membership\ExportPage\AbstractExport
    {
        protected function headers()
        {
        }
        public function get_data($page = 1, $limit = 9999)
        {
        }
    }
    class SubscriptionsExport extends \ProfilePress\Core\Admin\SettingsPages\Membership\ExportPage\AbstractExport
    {
        protected function headers()
        {
        }
        public function get_data($page = 1, $limit = 9999)
        {
        }
    }
    class SalesEarningsExport extends \ProfilePress\Core\Admin\SettingsPages\Membership\ExportPage\AbstractExport
    {
        protected function headers()
        {
        }
        public function get_data($page = 1, $limit = 9999)
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\Membership {
    class PaymentSettings extends \ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage
    {
        public $settingsPageInstance;
        public function __construct()
        {
        }
        public function header_menu_tab($tabs)
        {
        }
        public function header_sub_menu_tab($tabs)
        {
        }
        public function settings_page()
        {
        }
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\Membership\CouponsPage {
    class SettingsPage extends \ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage
    {
        public $error_bucket;
        function __construct()
        {
        }
        /**
         * @return string|void
         */
        public function save_coupon()
        {
        }
        public function admin_page_title($title = '')
        {
        }
        public static function set_screen($status, $option, $value)
        {
        }
        public function add_options()
        {
        }
        public function admin_notices()
        {
        }
        public function settings_page_function()
        {
        }
        public function add_new_button()
        {
        }
        public function admin_settings_page_callback()
        {
        }
        public static function get_instance()
        {
        }
    }
    class CouponWPListTable extends \WP_List_Table
    {
        public function __construct()
        {
        }
        public function no_items()
        {
        }
        public function get_columns()
        {
        }
        /**
         * Render the bulk edit checkbox
         *
         * @param CouponEntity $item
         *
         * @return string
         */
        public function column_cb($item)
        {
        }
        public function column_coupon_code(\ProfilePress\Core\Membership\Models\Coupon\CouponEntity $item)
        {
        }
        public function column_discount(\ProfilePress\Core\Membership\Models\Coupon\CouponEntity $item)
        {
        }
        public function column_start_date(\ProfilePress\Core\Membership\Models\Coupon\CouponEntity $item)
        {
        }
        public function column_end_date(\ProfilePress\Core\Membership\Models\Coupon\CouponEntity $item)
        {
        }
        public function column_redemption(\ProfilePress\Core\Membership\Models\Coupon\CouponEntity $item)
        {
        }
        public function column_status(\ProfilePress\Core\Membership\Models\Coupon\CouponEntity $item)
        {
        }
        public static function delete_coupon_url($coupon_id)
        {
        }
        public function get_coupons($per_page, $current_page = 1)
        {
        }
        public function record_count()
        {
        }
        public function prepare_items()
        {
        }
        public function current_action()
        {
        }
        public function get_bulk_actions()
        {
        }
        public function process_bulk_action()
        {
        }
        /**
         * @return array List of CSS classes for the table tag.
         */
        public function get_table_classes()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\Membership\SubscriptionsPage {
    class SettingsPage extends \ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage
    {
        public $error_bucket;
        function __construct()
        {
        }
        public function default_header_menu()
        {
        }
        public function register_cpf_settings_page()
        {
        }
        /**
         * @return void|string
         * @throws \Exception
         */
        public function save_subscription()
        {
        }
        public function admin_page_title()
        {
        }
        public static function set_screen($status, $option, $value)
        {
        }
        public function add_options()
        {
        }
        public function admin_notices()
        {
        }
        public function settings_page_function()
        {
        }
        public function add_new_button()
        {
        }
        public function admin_settings_page_callback()
        {
        }
        public static function get_instance()
        {
        }
    }
    class SubscriptionWPListTable extends \WP_List_Table
    {
        public function __construct()
        {
        }
        public function no_items()
        {
        }
        public function get_columns()
        {
        }
        public function get_views()
        {
        }
        /**
         * Render the bulk edit checkbox
         *
         * @param SubscriptionEntity $subscription
         *
         * @return string
         */
        public function column_cb($subscription)
        {
        }
        public static function view_edit_subscription_url($subscription_id)
        {
        }
        public function column_subscription(\ProfilePress\Core\Membership\Models\Subscription\SubscriptionEntity $subscription)
        {
        }
        public function column_plan(\ProfilePress\Core\Membership\Models\Subscription\SubscriptionEntity $subscription)
        {
        }
        public function column_initial_payment(\ProfilePress\Core\Membership\Models\Subscription\SubscriptionEntity $subscription)
        {
        }
        public function column_renewal_date(\ProfilePress\Core\Membership\Models\Subscription\SubscriptionEntity $subscription)
        {
        }
        public function column_date_created(\ProfilePress\Core\Membership\Models\Subscription\SubscriptionEntity $subscription)
        {
        }
        public function column_status(\ProfilePress\Core\Membership\Models\Subscription\SubscriptionEntity $subscription)
        {
        }
        public static function delete_subscription_url($subscription_id)
        {
        }
        public function record_count()
        {
        }
        public function prepare_items()
        {
        }
        public function current_action()
        {
        }
        public function get_bulk_actions()
        {
        }
        public function process_bulk_action()
        {
        }
        public function filter_bar()
        {
        }
        /**
         * Returns markup for an subscription status badge.
         *
         * @param string $subscription_status
         *
         * @return string
         *
         */
        public static function get_subscription_status_badge($subscription_status)
        {
        }
        /**
         * @return array List of CSS classes for the table tag.
         */
        public function get_table_classes()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\Membership\DashboardPage {
    abstract class AbstractReport
    {
        protected $db_table;
        protected $start_date;
        protected $end_date;
        protected $plan_id;
        protected $start_date_carbon;
        protected $end_date_carbon;
        protected $interval;
        /**
         * @param ReportFilterData $filterData
         */
        public function __construct($filterData)
        {
        }
        public function wpdb()
        {
        }
        /**
         * @param bool $groupBy
         * @param Carbon $start_date
         * @param Carbon $end_date
         *
         * @return string|void
         */
        public function get_where_clause($groupBy = false, $start_date = '', $end_date = '')
        {
        }
        public function get_date_column()
        {
        }
        public function get_interval_data($bucket, $datetime)
        {
        }
        public function get_labels()
        {
        }
        public function get_trend()
        {
        }
        public function val()
        {
        }
    }
    class Taxes extends \ProfilePress\Core\Admin\SettingsPages\Membership\DashboardPage\AbstractReport
    {
        public function get_total($start_date = '', $end_date = '')
        {
        }
        public function get_data()
        {
        }
    }
    class SettingsPage extends \ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage
    {
        function __construct()
        {
        }
        public function admin_page_title()
        {
        }
        public function register_cpf_settings_page()
        {
        }
        public function header_menu_tabs()
        {
        }
        public function default_header_menu()
        {
        }
        public function reports_admin_page()
        {
        }
        public function get_filter_data()
        {
        }
        public function js_script()
        {
        }
        public function cache_transformer($key, $callback)
        {
        }
        public function get_revenue()
        {
        }
        public function get_taxes()
        {
        }
        public function get_orders()
        {
        }
        public function get_refunds()
        {
        }
        public function get_top_plans()
        {
        }
        public function get_payment_methods()
        {
        }
        public static function get_lifetime_revenue()
        {
        }
        public function admin_settings_page_callback()
        {
        }
        public function range_picker()
        {
        }
        public function single_report_card($id, $title, $total, $trend = '', $sign = '%')
        {
        }
        /**
         * @return void
         */
        public function top_cards()
        {
        }
        public function report_charts()
        {
        }
        public static function get_instance()
        {
        }
    }
    class ReportFilterData
    {
        public $start_date;
        public $end_date;
        public $plan_id;
    }
    class Revenue extends \ProfilePress\Core\Admin\SettingsPages\Membership\DashboardPage\AbstractReport
    {
        public function get_total($start_date = '', $end_date = '')
        {
        }
        public function get_data()
        {
        }
    }
    class Refunds extends \ProfilePress\Core\Admin\SettingsPages\Membership\DashboardPage\AbstractReport
    {
        public function get_total($start_date = '', $end_date = '')
        {
        }
        public function get_data()
        {
        }
    }
    class ReportInterval
    {
        const HOURLY = 'hour';
        const DAILY = 'day';
        const MONTHLY = 'month';
        const YEARLY = 'year';
    }
    class PaymentMethods extends \ProfilePress\Core\Admin\SettingsPages\Membership\DashboardPage\AbstractReport
    {
        public function get_where_clause($groupBy = false, $start_date = '', $end_date = '')
        {
        }
        protected function get_query_data()
        {
        }
        public function get_interval_data($bucket, $payment_method)
        {
        }
        public function get_total($start_date = '', $end_date = '')
        {
        }
        public function get_trend()
        {
        }
        public function get_labels()
        {
        }
        public function get_data()
        {
        }
    }
    class TopPlans extends \ProfilePress\Core\Admin\SettingsPages\Membership\DashboardPage\AbstractReport
    {
        public function get_where_clause($groupBy = false, $start_date = '', $end_date = '')
        {
        }
        protected function get_query_data()
        {
        }
        public function get_interval_data($bucket, $plan_id)
        {
        }
        public function get_total($start_date = '', $end_date = '')
        {
        }
        public function get_trend()
        {
        }
        public function get_labels()
        {
        }
        public function get_data()
        {
        }
    }
    class Orders extends \ProfilePress\Core\Admin\SettingsPages\Membership\DashboardPage\AbstractReport
    {
        public function get_total($start_date = '', $end_date = '')
        {
        }
        public function get_data()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\Membership {
    class ContextualStateChangeHelper
    {
        public static function init()
        {
        }
        public static function css_js_script()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\Membership\PlansPage {
    class SettingsPage extends \ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage
    {
        public $error_bucket;
        function __construct()
        {
        }
        public function default_header_menu()
        {
        }
        public function register_cpf_settings_page()
        {
        }
        public function header_menu_tabs()
        {
        }
        public function admin_page_title()
        {
        }
        public function save_changes()
        {
        }
        public static function set_screen($status, $option, $value)
        {
        }
        public function add_options()
        {
        }
        public function admin_notices()
        {
        }
        public function settings_page_function()
        {
        }
        public function add_new_button()
        {
        }
        public function admin_settings_page_callback()
        {
        }
        public function js_template()
        {
        }
        public static function get_instance()
        {
        }
    }
    class PlanWPListTable extends \WP_List_Table
    {
        public function __construct()
        {
        }
        public function no_items()
        {
        }
        public function get_columns()
        {
        }
        /**
         * Render the bulk edit checkbox
         *
         * @param PlanEntity $item
         *
         * @return string
         */
        public function column_cb($item)
        {
        }
        public function column_name(\ProfilePress\Core\Membership\Models\Plan\PlanEntity $item)
        {
        }
        public function column_billing_details(\ProfilePress\Core\Membership\Models\Plan\PlanEntity $item)
        {
        }
        public function column_checkout_url(\ProfilePress\Core\Membership\Models\Plan\PlanEntity $item)
        {
        }
        public function column_status(\ProfilePress\Core\Membership\Models\Plan\PlanEntity $item)
        {
        }
        public static function delete_plan_url($plan_id)
        {
        }
        public function get_plans($per_page, $current_page = 1)
        {
        }
        public function record_count()
        {
        }
        public function prepare_items()
        {
        }
        public function current_action()
        {
        }
        public function get_bulk_actions()
        {
        }
        public function process_bulk_action()
        {
        }
        /**
         * @return array List of CSS classes for the table tag.
         */
        public function get_table_classes()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\Membership\TaxSettings {
    class SettingsPage
    {
        public function __construct()
        {
        }
        public function header_sub_menu_tab($tabs)
        {
        }
        public function tax_rate_setup_ui()
        {
        }
        public function taxes_page()
        {
        }
        public static function tax_rate_row($index = '0', $country = '', $state = '', $global = '', $rate = '')
        {
        }
        public function enqueue_script()
        {
        }
        public function js_template()
        {
        }
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\Membership\DownloadLogsPage {
    class SettingsPage extends \ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage
    {
        public $error_bucket;
        function __construct()
        {
        }
        public function admin_page_title($title = '')
        {
        }
        public static function set_screen($status, $option, $value)
        {
        }
        public function add_options()
        {
        }
        public function admin_notices()
        {
        }
        public function settings_page_function()
        {
        }
        public function admin_settings_page_callback()
        {
        }
        public static function get_instance()
        {
        }
    }
    class WPListTable extends \WP_List_Table
    {
        public function __construct()
        {
        }
        public function no_items()
        {
        }
        public function get_columns()
        {
        }
        /**
         * Render the bulk edit checkbox
         *
         * @param $item
         *
         * @return string
         */
        public function column_cb($item)
        {
        }
        public function column_membership_plan($item)
        {
        }
        public function column_customer($item)
        {
        }
        public function column_order_number($item)
        {
        }
        public function column_default($item, $column_name)
        {
        }
        public function prepare_items()
        {
        }
        public function get_bulk_actions()
        {
        }
        public function process_bulk_action()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\Membership\GroupsPage {
    class SettingsPage extends \ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage
    {
        public $error_bucket;
        function __construct()
        {
        }
        /**
         * @return string|void
         */
        public function save_group()
        {
        }
        public function admin_page_title($title = '')
        {
        }
        public static function set_screen($status, $option, $value)
        {
        }
        public function add_options()
        {
        }
        public function admin_notices()
        {
        }
        public function settings_page_function()
        {
        }
        public function add_new_button()
        {
        }
        public function admin_settings_page_callback()
        {
        }
        public static function group_info_note()
        {
        }
        public static function get_instance()
        {
        }
    }
    class GroupWPListTable extends \WP_List_Table
    {
        public function __construct()
        {
        }
        public function no_items()
        {
        }
        public function get_columns()
        {
        }
        /**
         * Render the bulk edit checkbox
         *
         * @param GroupEntity $item
         *
         * @return string
         */
        public function column_cb($item)
        {
        }
        public function column_group_name(\ProfilePress\Core\Membership\Models\Group\GroupEntity $item)
        {
        }
        public function column_group_plans(\ProfilePress\Core\Membership\Models\Group\GroupEntity $item)
        {
        }
        public function column_checkout_url(\ProfilePress\Core\Membership\Models\Group\GroupEntity $item)
        {
        }
        public static function delete_group_url($group_id)
        {
        }
        public function get_groups($per_page, $current_page = 1)
        {
        }
        public function record_count()
        {
        }
        public function prepare_items()
        {
        }
        public function current_action()
        {
        }
        public function get_bulk_actions()
        {
        }
        public function process_bulk_action()
        {
        }
        /**
         * @return array List of CSS classes for the table tag.
         */
        public function get_table_classes()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\Membership {
    class PaymentMethods
    {
        public function __construct()
        {
        }
        public function header_sub_menu_tab($tabs)
        {
        }
        public function payment_method_list()
        {
        }
        protected function get_enabled_payment_methods()
        {
        }
        public function payment_methods_page()
        {
        }
        public function js_script()
        {
        }
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\Membership\CustomersPage {
    class SettingsPage extends \ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage
    {
        public $error_bucket;
        function __construct()
        {
        }
        public function default_header_menu()
        {
        }
        public function register_cpf_settings_page()
        {
        }
        /**
         * @return void
         * @throws \Exception
         */
        public function save_customer()
        {
        }
        /**
         * @return void|string
         * @throws \Exception
         */
        public function add_customer()
        {
        }
        public function search_wp_users()
        {
        }
        public function admin_page_title()
        {
        }
        public static function set_screen($status, $option, $value)
        {
        }
        public function add_options()
        {
        }
        public function admin_notices()
        {
        }
        public function settings_page_function()
        {
        }
        protected static function user_account_type_settings()
        {
        }
        protected static function password_field()
        {
        }
        public function add_new_button()
        {
        }
        public function admin_settings_page_callback($content)
        {
        }
        public function js_script()
        {
        }
        public static function get_instance()
        {
        }
    }
    class CustomerWPListTable extends \WP_List_Table
    {
        public function __construct()
        {
        }
        public function no_items()
        {
        }
        public function get_columns()
        {
        }
        public static function delete_customer_url($customer_id)
        {
        }
        public static function view_customer_url($customer_id)
        {
        }
        /**
         * @param CustomerEntity $customer
         *
         * @return string
         */
        public function column_customer_name($customer)
        {
        }
        /**
         * @param CustomerEntity $customer
         *
         * @return string
         */
        public function column_customer_email($customer)
        {
        }
        /**
         * @param CustomerEntity $customer
         *
         * @return string
         */
        public function column_customer_since($customer)
        {
        }
        /**
         * @param CustomerEntity $customer
         *
         * @return string
         */
        public function column_customer_last_login($customer)
        {
        }
        /**
         * @param CustomerEntity $customer
         *
         * @return string
         */
        public function column_customer_subscriptions($customer)
        {
        }
        public function get_views()
        {
        }
        /**
         * Render the bulk edit checkbox
         *
         * @param CustomerEntity $customer
         *
         * @return string
         */
        public function column_cb($customer)
        {
        }
        public function record_count()
        {
        }
        public function prepare_items()
        {
        }
        public function current_action()
        {
        }
        public function get_bulk_actions()
        {
        }
        public function process_bulk_action()
        {
        }
        public function filter_bar()
        {
        }
        /**
         * @return array List of CSS classes for the table tag.
         */
        public function get_table_classes()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\Membership\OrdersPage {
    class SettingsPage extends \ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage
    {
        public $error_bucket;
        function __construct()
        {
        }
        public function default_header_menu()
        {
        }
        public function register_cpf_settings_page()
        {
        }
        public function search_membership_plans()
        {
        }
        public function search_customers()
        {
        }
        public function search_plan_coupon()
        {
        }
        public function delete_order_note()
        {
        }
        public function replace_order_item_modal()
        {
        }
        public function refund_order()
        {
        }
        /**
         * @return void
         */
        public function save_order()
        {
        }
        /**
         * @return void|string
         */
        public function add_order()
        {
        }
        public function admin_page_title()
        {
        }
        public static function set_screen($status, $option, $value)
        {
        }
        public function add_options()
        {
        }
        public function admin_notices()
        {
        }
        public function settings_page_function()
        {
        }
        public function add_new_button()
        {
        }
        public function admin_settings_page_callback($content)
        {
        }
        public static function get_instance()
        {
        }
    }
    class OrderWPListTable extends \WP_List_Table
    {
        public function __construct()
        {
        }
        public function no_items()
        {
        }
        public function get_columns()
        {
        }
        public function get_views()
        {
        }
        /**
         * Render the bulk edit checkbox
         *
         * @param OrderEntity $order
         *
         * @return string
         */
        public function column_cb($order)
        {
        }
        public function column_order(\ProfilePress\Core\Membership\Models\Order\OrderEntity $order)
        {
        }
        public function column_plan(\ProfilePress\Core\Membership\Models\Order\OrderEntity $order)
        {
        }
        public function column_payment_method(\ProfilePress\Core\Membership\Models\Order\OrderEntity $order)
        {
        }
        public function column_order_total(\ProfilePress\Core\Membership\Models\Order\OrderEntity $order)
        {
        }
        public function column_date_created(\ProfilePress\Core\Membership\Models\Order\OrderEntity $order)
        {
        }
        public function column_status(\ProfilePress\Core\Membership\Models\Order\OrderEntity $order)
        {
        }
        public static function delete_order_url($order_id)
        {
        }
        public static function view_edit_order_url($order_id)
        {
        }
        public function record_count()
        {
        }
        public function prepare_items()
        {
        }
        public function current_action()
        {
        }
        public function get_bulk_actions()
        {
        }
        public function process_bulk_action()
        {
        }
        public function filter_bar()
        {
        }
        /**
         * Returns markup for an Order status badge.
         *
         * @param string $order_status Order status ID.
         *
         * @return string
         *
         */
        public static function get_order_status_badge($order_status)
        {
        }
        /**
         * @return array List of CSS classes for the table tag.
         */
        public function get_table_classes()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages\Membership {
    class PlanIntegrationsMetabox
    {
        protected $args;
        protected $saved_values;
        protected $plan_id;
        /**
         * @param array $args
         */
        public function __construct($args, $saved_values)
        {
        }
        public function text($name, $options)
        {
        }
        public function number($name, $options)
        {
        }
        public function upload($name, $options)
        {
        }
        public function digital_files($name, $options)
        {
        }
        public function textarea($name, $options)
        {
        }
        public function checkbox($name, $options)
        {
        }
        public function select($name, $options)
        {
        }
        public function custom($name, $options)
        {
        }
        public function select2($name, $options)
        {
        }
        public function build()
        {
        }
    }
    class CheckoutFieldsManager
    {
        public function __construct()
        {
        }
        public function header_sub_menu_tab($tabs)
        {
        }
        public function save_checkout_fields()
        {
        }
        public function checkout_fields_page()
        {
        }
        public static function checkout_field_addition_dropdown($fieldGroup = 'accountInfo')
        {
        }
        public function global_js_vars()
        {
        }
        public function js_template()
        {
        }
        public static function get_instance()
        {
        }
    }
    class CheckListHeader
    {
        public function __construct()
        {
        }
        public function checklist_header()
        {
        }
        public static function get_instance()
        {
        }
    }
    class SettingsFieldsParser
    {
        protected $config;
        protected $dbData;
        protected $field_class;
        public function __construct($config, $dbData = [], $field_class = 'ppress-plan-control')
        {
        }
        protected function field_output($config)
        {
        }
        public function build()
        {
        }
    }
}
namespace ProfilePress\Core\Admin\SettingsPages {
    class ToolsSettingsPage
    {
        public function __construct()
        {
        }
        public function clear_error_log()
        {
        }
        public function admin_page()
        {
        }
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\Admin {
    class ProfileCustomFields
    {
        use \ProfilePress\Core\Classes\WPProfileFieldParserTrait;
        public $upload_errors;
        /**
         * add the extra field and update to DB
         */
        public function __construct()
        {
        }
        public function date_field_picker($field_key)
        {
        }
        /**
         * Add multipart/form-data to wordpress profile admin page
         */
        public function edit_form_type()
        {
        }
        public function display_billing_details_fields($user)
        {
        }
        /**
         * Update user profile info.
         *
         * @param int $user_id
         */
        public function save_profile_update($user_id)
        {
        }
        /**
         * Output generated files upload errors.
         *
         * @param \WP_Error $errors
         * @param string $update
         * @param \WP_User $user
         */
        public function file_upload_errors($errors, $update, $user)
        {
        }
        public function js_scripts()
        {
        }
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\ContentProtection {
    class SettingsPage extends \ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage
    {
        public $content_protection_rule_errors;
        const META_DATA_KEY = 'content_restrict_data';
        function __construct()
        {
        }
        public function logic_upgrade_notice()
        {
        }
        public function admin_page_title()
        {
        }
        public function register_cpf_settings_page()
        {
        }
        public function header_menu_tabs()
        {
        }
        public function default_header_menu()
        {
        }
        public static function set_screen($status, $option, $value)
        {
        }
        public function add_options()
        {
        }
        public function sanitize_data($data)
        {
        }
        public function save_rule($type)
        {
        }
        public function admin_notices()
        {
        }
        public function settings_page_function()
        {
        }
        public function add_new_button()
        {
        }
        public function admin_settings_page_callback()
        {
        }
        public static function get_instance()
        {
        }
    }
    class Init
    {
        public function __construct()
        {
        }
        public function get_content_condition_field()
        {
        }
        public function get_exempt_content_condition_field()
        {
        }
        public function get_content_condition_search()
        {
        }
        /**
         * @param string|array $post_type
         * @param array $args
         *
         * @return int[]|\WP_Post[]
         */
        public static function post_type_query($post_type, $args = [])
        {
        }
        public function taxonomy_query($taxonomy, $args = [])
        {
        }
        public static function get_instance()
        {
        }
    }
    class ElementorRestriction
    {
        public function __construct()
        {
        }
        public function register_section($element)
        {
        }
        public function register_controls($element, $args)
        {
        }
        /**
         * @param bool $should_render
         * @param Element_Base $widget
         *
         * @return bool
         */
        public function should_render($should_render, $widget)
        {
        }
        /**
         * Renders the "Content restricted" message instead of the widget's content.
         *
         * Applies if widget should be hidden from members and "Content restricted" message option is enabled.
         *
         * @param string $widget_content Elementor Widget content
         * @param Widget_Base $widget Elementor Widget object
         *
         * @return string
         *
         */
        public function maybe_render_restricted_message($widget_content, $widget)
        {
        }
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\ContentProtection\Frontend {
    class SearchAndAPI
    {
        public function __construct()
        {
        }
        public function exclude_protected_posts($query)
        {
        }
        public static function get_instance()
        {
        }
    }
    class RestrictionShortcode
    {
        public function __construct()
        {
        }
        public function shortcode_handler($atts, $content = null)
        {
        }
        public function rule_matches($roles = '', $user_ids = '', $plans = '')
        {
        }
        public static function get_instance()
        {
        }
    }
    class PostContent
    {
        public $restrictedAccessConditions = [];
        public function __construct()
        {
        }
        public function uncode_theme_compatibility()
        {
        }
        public function woocommerce_compatibility()
        {
        }
        /**
         * Checks whether current page can use custom template.
         *
         * @return bool
         */
        public function can_use_restricted_template()
        {
        }
        public function restricted_page_template($template)
        {
        }
        public function protection_disabled()
        {
        }
        /**
         * @param bool $skip_cache
         *
         * @return bool
         */
        public function is_post_content_restricted($skip_cache = false)
        {
        }
        public function the_content($content)
        {
        }
        public function get_restricted_message($post_content, $noaccess_message_type = 'global', $custom_message = '', $message_style = 'none')
        {
        }
        public function parse_message($message)
        {
        }
        public function style_paywall_message($message, $style = 'none')
        {
        }
        public function get_post_excerpt($post_content)
        {
        }
        public static function trim_content($the_excerpt = '', $length = 100, $more = false)
        {
        }
        /**
         * See https://stackoverflow.com/a/3810341/2648410
         *
         * @param $content
         *
         * @return false|mixed|string
         */
        public static function close_tags($content)
        {
        }
        public static function get_instance()
        {
        }
    }
    class Checker
    {
        public static function is_blocked($who_can_access = 'everyone', $roles = [], $wp_users = [], $membership_plans = [])
        {
        }
        /**
         * @param $protection_rule
         * @param bool $is_redirect set to true if this is a redirect check and not post content check.
         * @param bool $is_new True if using the new reversed logic where OR comes AND and AND beocomes OR
         *
         * @return bool
         */
        public static function content_match($protection_rule, $is_redirect = false, $is_new = false)
        {
        }
        public static function check_condition($condition_id, $rule_saved_value, $is_redirect = false)
        {
        }
        public static function fr($response, ...$filter_args)
        {
        }
    }
    class Redirect
    {
        public function __construct()
        {
        }
        public function handler()
        {
        }
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\ContentProtection {
    class ConditionCallbacks
    {
        /**
         * Checks if this is one of the selected post_type items.
         *
         * @param string $condition_id
         * @param mixed $rule_saved_value
         * @param bool $is_redirect
         *
         * @return bool
         */
        public static function post_type($condition_id, $rule_saved_value, $is_redirect = false)
        {
        }
        /**
         * Checks if this is one of the selected taxonomy term.
         *
         * @param string $condition_id
         * @param mixed $rule_saved_value
         *
         * @return bool
         */
        public static function taxonomy($condition_id, $rule_saved_value)
        {
        }
        /**
         * Checks if the post_type has the selected categories.
         *
         * @param string $condition_id
         * @param mixed $rule_saved_value
         * @param bool $is_redirect
         *
         * @return bool
         */
        public static function post_type_tax($condition_id, $rule_saved_value, $is_redirect = false)
        {
        }
        /**
         * Checks if this is one of the selected categories.
         *
         * @param string $condition_id
         * @param mixed $rule_saved_value
         *
         * @return bool
         */
        public static function _category($condition_id, $rule_saved_value)
        {
        }
        /**
         * Checks if this is one of the selected tags.
         *
         * @param string $condition_id
         * @param mixed $rule_saved_value
         *
         * @return bool
         */
        public static function _post_tag($condition_id, $rule_saved_value)
        {
        }
        /**
         * Checks if the post_type has the selected categories.
         *
         * @param string $condition_id
         * @param mixed $rule_saved_value
         *
         * @return bool
         */
        public static function _post_type_category($condition_id, $rule_saved_value)
        {
        }
        /**
         * Checks is a post_type has the selected tags.
         *
         * @param string $condition_id
         * @param mixed $rule_saved_value
         *
         * @return bool
         */
        public static function _post_type_tag($condition_id, $rule_saved_value)
        {
        }
        /**
         * @param string $post_type
         *
         * @return bool
         */
        public static function _is_post_type($post_type)
        {
        }
    }
    class WPListTable extends \WP_List_Table
    {
        public function __construct()
        {
        }
        public function no_items()
        {
        }
        public function get_columns()
        {
        }
        public function column_default($item, $column_name)
        {
        }
        /**
         * Render the bulk edit checkbox
         *
         * @param array $item
         *
         * @return string
         */
        public function column_cb($item)
        {
        }
        public static function delete_rule_url($rule_id)
        {
        }
        public function column_title($item)
        {
        }
        public function get_condition_title($condition_id)
        {
        }
        public function rule_title_transform($condition_id, $title)
        {
        }
        public function rule_value_transform($condition_id, $value)
        {
        }
        public function column_content($item)
        {
        }
        public function column_access($item)
        {
        }
        public function get_rules($per_page, $current_page = 1)
        {
        }
        public function record_count()
        {
        }
        public function prepare_items()
        {
        }
        public function get_bulk_actions()
        {
        }
        public function single_row($item)
        {
        }
        public function process_bulk_action()
        {
        }
        /**
         * @return array List of CSS classes for the table tag.
         */
        public function get_table_classes()
        {
        }
    }
    class CapabilityCheck
    {
        public function __construct()
        {
        }
        public function user_has_cap($all_caps, $caps, $args)
        {
        }
        public static function get_instance()
        {
        }
    }
    class NavMenuProtection
    {
        public function __construct()
        {
        }
        public function init()
        {
        }
        /*
         * Add custom roles to Nav Menu Roles menu options
         *
         * @param array $roles An array of all available roles, by default is global $wp_roles
         * @return array
         */
        function new_roles($roles)
        {
        }
        /*
         * Change visibility of each menu item.
         *
         * NMR settings can be "in" (all logged in), "out" (all logged out) or an array of specific roles
         *
         * @param bool $visible
         * @param object $item The menu item object. Nav Menu Roles adds its info to $item->roles
         * @return boolean
         */
        function item_visibility($visible, $item)
        {
        }
        /*-----------------------------------------------------------------------------------*/
        /* Helper Functions */
        /*-----------------------------------------------------------------------------------*/
        /*
         * Get the plugin-specific "roles" returned in an array, with ID => Name key pairs
         *
         * @return array
         */
        function get_roles_wrapper()
        {
        }
        /*
         * Get the plugin-specific "roles" relevant to this menu item
         *
         * @return array
         */
        function get_relevant_roles_wrapper($roles = array())
        {
        }
        /*
         * Check the current user has plugin-specific level capability
         *
         * @param string $role_id | The ID of the "role" with a plugin-specific prefix
         *
         * @return bool
         */
        function current_user_can_wrapper($role_id = false)
        {
        }
        public static function get_instance()
        {
        }
    }
    class ConditionalBlocksIntegration
    {
        public function __construct()
        {
        }
        public function init()
        {
        }
        public function condition_category($categories)
        {
        }
        public function conditions($conditions)
        {
        }
        /**
         * @param bool $should_block_render if condition passed validation.
         * @param array $condition contains the configured conditions with keys/values.
         *
         * @return bool $should_block_render - defaults to false.
         */
        function visibility_check($should_block_render, $condition)
        {
        }
        public static function get_instance()
        {
        }
    }
    class ContentConditions
    {
        /**
         * @var array
         */
        public $conditions;
        /**
         * @param array $conditions
         */
        public function add_conditions($conditions = [])
        {
        }
        /**
         * @param array $condition
         */
        public function add_condition($condition = [])
        {
        }
        /**
         * @return array
         */
        public function get_conditions()
        {
        }
        /**
         * @return array
         */
        public function get_conditions_by_group()
        {
        }
        /**
         * @return array
         */
        public function conditions_dropdown_list()
        {
        }
        public function rule_row($facetListId, $facetId, $savedRule = [])
        {
        }
        public function exempt_rule_row($facetListId, $facetId, $savedRule = [])
        {
        }
        public function unlinked_and_rule_badge()
        {
        }
        public function linked_and_rule_badge()
        {
        }
        public function rules_group_row($facetListId = '', $facetId = '', $facets = [], $unlink_and = false)
        {
        }
        public function exempt_rules_group_row($facetListId = '', $facetId = '', $facets = [])
        {
        }
        /**
         * @param null $condition
         *
         * @return mixed|null
         *
         */
        public function get_condition($condition = null)
        {
        }
        /**
         * @return array
         */
        public function generate_post_type_conditions()
        {
        }
        /**
         * @param $name
         *
         * @return array
         */
        public function generate_post_type_tax_conditions($name)
        {
        }
        /**
         * @return array
         */
        public function generate_taxonomy_conditions()
        {
        }
        /**
         * @return mixed|void
         */
        public function register_conditions()
        {
        }
        public function select_field($name_attr, $args = [])
        {
        }
        public function postselect_field($name_attr, $savedValue = [])
        {
        }
        public function taxonomyselect_field($name_attr, $savedValue)
        {
        }
        public function rule_value_field($condition_id, $facetListId, $facetId, $savedValue = [])
        {
        }
        public function exempt_rule_value_field($condition_id, $facetListId, $facetId, $savedValue = [])
        {
        }
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\Integrations\TutorLMS {
    class Init
    {
        public static $instance_flag = false;
        public function __construct()
        {
        }
        public function tutor_monetization_options($arr)
        {
        }
        public function loop_course_checkout_link($output)
        {
        }
        public function course_checkout_link($output, $course_id)
        {
        }
        public function after_user_registration($form_id, $user_data, $user_id, $is_melange)
        {
        }
        /**
         * @param OrderEntity|SubscriptionEntity $order_or_sub
         *
         * @return void
         */
        public function on_order_sub_success($order_or_sub)
        {
        }
        /**
         * @param SubscriptionEntity $subscription
         *
         * @return void
         */
        public function on_subscription_cancelled($subscription)
        {
        }
        /**
         * @param array $args
         *
         * @return array
         */
        public function settings_page($args)
        {
        }
        public function plan_edit_screen($settings)
        {
        }
        public function save_shortcode_builder_settings($settings)
        {
        }
        public function dnd_builder_settings($meta_box_settings)
        {
        }
        public function shortcode_builder_settings($form_id)
        {
        }
        public function override_student_register_url($url)
        {
        }
        public function override_instructor_register_url($url)
        {
        }
        /**
         * @return self|void
         */
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core {
    class LoginRedirect
    {
        const DB_OPTION_NAME = 'ppress_login_redirect_rules';
        public function __construct()
        {
        }
        /**
         * @param \WP_User $user
         * @param string $default_url
         *
         * @return false|string|null
         */
        public function get_login_redirect_url($user, $default_url = '')
        {
        }
        public function enqueue_script()
        {
        }
        public function header_sub_menu_tab($tabs)
        {
        }
        public function save_settings()
        {
        }
        public function login_redirect_fields_page()
        {
        }
        public static function field_addition_dropdown($saved_rules, $fieldGroup = 'plans')
        {
        }
        public function redirect_rule_item($redirect_type, $redirect_target, $redirect_url_slug, $label)
        {
        }
        public function js_template()
        {
        }
        public static function get_instance()
        {
        }
    }
    class Cron
    {
        public function __construct()
        {
        }
        public function create_recurring_schedule()
        {
        }
        /**
         * Check for expired subscriptions once per day and mark them as expired
         */
        public function check_for_expired_subscriptions()
        {
        }
        /**
         * @return self
         */
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\RegisterActivation {
    class Base
    {
        public static function run_install($networkwide = false)
        {
        }
        /**
         * Run plugin install / activation action when new blog is created in multisite setup.
         *
         * @param int $blog_id
         */
        public static function multisite_new_blog_install($blog_id)
        {
        }
        /**
         * Perform plugin activation / installation.
         */
        public static function pp_install()
        {
        }
        public static function create_default_forms()
        {
        }
        public static function default_settings()
        {
        }
        public static function membership_default_settings()
        {
        }
        public static function create_pages()
        {
        }
        public static function create_membership_pages()
        {
        }
        public static function clear_wpengine_cache()
        {
        }
    }
    class CreateDBTables
    {
        public static function make()
        {
        }
        public static function membership_db_make()
        {
        }
    }
}
namespace ProfilePress\Core {
    class DBTables
    {
        public static function form_db_table()
        {
        }
        public static function form_meta_db_table()
        {
        }
        public static function passwordless_login_db_table()
        {
        }
        public static function meta_data_db_table()
        {
        }
        public static function profile_fields_db_table()
        {
        }
        public static function subscription_plans_db_table()
        {
        }
        public static function customers_db_table()
        {
        }
        public static function orders_db_table()
        {
        }
        public static function order_meta_db_table()
        {
        }
        public static function subscriptions_db_table()
        {
        }
        public static function coupons_db_table()
        {
        }
    }
    class Base extends \ProfilePress\Core\DBTables
    {
        // core contact info fields
        const cif_facebook = 'facebook';
        const cif_twitter = 'twitter';
        const cif_linkedin = 'linkedin';
        const cif_youtube = 'youtube';
        const cif_vk = 'vk';
        const cif_instagram = 'instagram';
        const cif_github = 'github';
        const cif_pinterest = 'pinterest';
        public function __construct()
        {
        }
        function register_metadata_table()
        {
        }
        public function admin_hooks()
        {
        }
        public function db_updates()
        {
        }
        public function older_to_v4_upgrader()
        {
        }
        public function wpmu_drop_tables($tables)
        {
        }
        /**
         * Singleton.
         *
         * @return Base
         */
        public static function get_instance()
        {
        }
    }
}
namespace {
    /**
     * Utility class for session utilities
     *
     * THIS CLASS SHOULD NEVER BE INSTANTIATED
     */
    class WP_PPress_Session_Utils
    {
        /**
         * Count the total sessions in the database.
         *
         * @return int
         * @global wpdb $wpdb
         *
         */
        public static function count_sessions()
        {
        }
        /**
         * Create a new, random session in the database.
         *
         * @param null|string $date
         */
        public static function create_dummy_session($date = \null)
        {
        }
        /**
         * Delete old sessions from the database.
         *
         * @param int $limit Maximum number of sessions to delete.
         *
         * @return int Sessions deleted.
         * @global wpdb $wpdb
         *
         */
        public static function delete_old_sessions($limit = 1000)
        {
        }
        /**
         * Delete old sessions from the options table.
         *
         * @param int $limit Maximum number of sessions to delete.
         *
         * @return int Sessions deleted.
         * @global wpdb $wpdb
         *
         */
        protected static function delete_old_sessions_from_options($limit = 1000)
        {
        }
        /**
         * Remove all sessions from the database, regardless of expiration.
         *
         * @return int Sessions deleted
         * @global wpdb $wpdb
         *
         */
        public static function delete_all_sessions()
        {
        }
        /**
         * Remove all sessions from the options table, regardless of expiration.
         *
         * @return int Sessions deleted
         * @global wpdb $wpdb
         *
         */
        public static function delete_all_sessions_from_options()
        {
        }
        /**
         * Generate a new, random session ID.
         *
         * @return string
         */
        public static function generate_id()
        {
        }
        /**
         * Get session from database.
         *
         * @param string $session_id The session ID to retrieve
         * @param array $default The default value to return if the option does not exist.
         *
         * @return array Session data
         */
        public static function get_session($session_id, $default = array())
        {
        }
        /**
         * Test whether or not a session exists
         *
         * @param string $session_id The session ID to retrieve
         *
         * @return bool
         */
        public static function session_exists($session_id)
        {
        }
        /**
         * Add session in database.
         *
         * @param array $data Data to update (in column => value pairs). Both $data columns and $data values should be "raw" (neither should be SQL escaped).
         *                    This means that if you are using GET or POST data you may need to use stripslashes() to avoid slashes ending up in the database.
         *
         * @return bool|int false if the row could not be inserted or the number of affected rows (which will always be 1).
         */
        public static function add_session($data = array())
        {
        }
        /**
         * Delete session in database.
         *
         * @param int $session_id The session ID to update
         *
         * @return bool
         */
        public static function delete_session($session_id = '')
        {
        }
        /**
         * Update session in database.
         *
         * @param int $session_id The session ID to update
         * @param array $data Data to update (in column => value pairs). Both $data columns and $data values should be "raw" (neither should be SQL escaped).
         *                    This means that if you are using GET or POST data you may need to use stripslashes() to avoid slashes ending up in the database.
         *
         * @return bool|int the number of rows updated, or false if there is an error.
         *                  Keep in mind that if the $data matches what is already in the database, no rows will be updated, so 0 will be returned.
         *                  Because of this, you should probably check the return with false === $result
         */
        public static function update_session($session_id = '', $data = array())
        {
        }
    }
    /**
     * Multidimensional ArrayAccess
     *
     * Allows ArrayAccess-like functionality with multidimensional arrays.  Fully supports
     * both sets and unsets.
     *
     * @package WordPress
     * @subpackage Session
     */
    /**
     * Recursive array class to allow multidimensional array access.
     *
     * @package WordPress
     */
    class PPRESS_Recursive_ArrayAccess implements \ArrayAccess, \Iterator, \Countable
    {
        /**
         * Internal data collection.
         *
         * @var array
         */
        protected $container = array();
        /**
         * Flag whether or not the internal collection has been changed.
         *
         * @var bool
         */
        protected $dirty = \false;
        /**
         * Default object constructor.
         *
         * @param array $data
         */
        protected function __construct($data = array())
        {
        }
        /**
         * Allow deep copies of objects
         */
        public function __clone()
        {
        }
        /**
         * Output the data container as a multidimensional array.
         *
         * @return array
         */
        public function toArray()
        {
        }
        /*****************************************************************/
        /*                   ArrayAccess Implementation                  */
        /*****************************************************************/
        /**
         * Whether a offset exists
         *
         * @link http://php.net/manual/en/arrayaccess.offsetexists.php
         *
         * @param mixed $offset An offset to check for.
         *
         * @return boolean true on success or false on failure.
         */
        #[\ReturnTypeWillChange]
        public function offsetExists($offset)
        {
        }
        /**
         * Offset to retrieve
         *
         * @link http://php.net/manual/en/arrayaccess.offsetget.php
         *
         * @param mixed $offset The offset to retrieve.
         *
         * @return mixed Can return all value types.
         */
        #[\ReturnTypeWillChange]
        public function offsetGet($offset)
        {
        }
        /**
         * Offset to set
         *
         * @link http://php.net/manual/en/arrayaccess.offsetset.php
         *
         * @param mixed $offset The offset to assign the value to.
         * @param mixed $value The value to set.
         *
         * @return void
         */
        #[\ReturnTypeWillChange]
        public function offsetSet($offset, $data)
        {
        }
        /**
         * Offset to unset
         *
         * @link http://php.net/manual/en/arrayaccess.offsetunset.php
         *
         * @param mixed $offset The offset to unset.
         *
         * @return void
         */
        #[\ReturnTypeWillChange]
        public function offsetUnset($offset)
        {
        }
        /*****************************************************************/
        /*                     Iterator Implementation                   */
        /*****************************************************************/
        /**
         * Current position of the array.
         *
         * @link http://php.net/manual/en/iterator.current.php
         *
         * @return mixed
         */
        #[\ReturnTypeWillChange]
        public function current()
        {
        }
        /**
         * Key of the current element.
         *
         * @link http://php.net/manual/en/iterator.key.php
         *
         * @return mixed
         */
        #[\ReturnTypeWillChange]
        public function key()
        {
        }
        /**
         * Move the internal point of the container array to the next item
         *
         * @link http://php.net/manual/en/iterator.next.php
         *
         * @return void
         */
        #[\ReturnTypeWillChange]
        public function next()
        {
        }
        /**
         * Rewind the internal point of the container array.
         *
         * @link http://php.net/manual/en/iterator.rewind.php
         *
         * @return void
         */
        #[\ReturnTypeWillChange]
        public function rewind()
        {
        }
        /**
         * Is the current key valid?
         *
         * @link http://php.net/manual/en/iterator.rewind.php
         *
         * @return bool
         */
        #[\ReturnTypeWillChange]
        public function valid()
        {
        }
        /*****************************************************************/
        /*                    Countable Implementation                   */
        /*****************************************************************/
        /**
         * Get the count of elements in the container array.
         *
         * @link http://php.net/manual/en/countable.count.php
         *
         * @return int
         */
        #[\ReturnTypeWillChange]
        public function count()
        {
        }
    }
    /**
     * WordPress session management.
     *
     * Standardizes WordPress session data using database-backed options for storage.
     * for storing user session information.
     *
     * @package WordPress
     * @subpackage Session
     */
    /**
     * WordPress Session class for managing user session data.
     *
     * @package WordPress
     */
    final class WP_PPress_Session extends \PPRESS_Recursive_ArrayAccess
    {
        /**
         * ID of the current session.
         *
         * @var string
         */
        public $session_id;
        /**
         * Retrieve the current session instance.
         *
         * @return bool|WP_PPress_Session
         */
        public static function get_instance()
        {
        }
        /**
         * Write the data from the current session to the data storage system.
         */
        public function write_data()
        {
        }
        /**
         * Output the current container contents as a JSON-encoded string.
         *
         * @return string
         */
        public function json_out()
        {
        }
        /**
         * Decodes a JSON string and, if the object is an array, overwrites the session container with its contents.
         *
         * @param string $data
         *
         * @return bool
         */
        public function json_in($data)
        {
        }
        /**
         * Regenerate the current session's ID.
         *
         * @param bool $delete_old Flag whether or not to delete the old session data from the server.
         */
        public function regenerate_id($delete_old = \false)
        {
        }
        /**
         * Check if a session has been initialized.
         *
         * @return bool
         */
        public function session_started()
        {
        }
        /**
         * Return the read-only cache expiration value.
         *
         * @return int
         */
        public function cache_expiration()
        {
        }
        /**
         * Flushes all session variables.
         */
        public function reset()
        {
        }
    }
}
namespace ProfilePress\Core\ShortcodeParser {
    class FormProcessor
    {
        /**
         * When a password reset form is submitted to generate a reset key to be emailed,
         * it holds both success and error message.
         *
         * @var array
         */
        public $password_reset_form_error = [];
        public $login_form_error = [];
        public $edit_profile_form_error = [];
        public $registration_form_error = [];
        public $is_2fa = [];
        public $myac_change_password_error = '';
        public static function set_global_state($key, $value, $form_id = false)
        {
        }
        public static function get_global_state_error($key)
        {
        }
        public function restore_form_error($key)
        {
        }
        /**
         * @return string|void
         */
        public function process_myaccount_change_password()
        {
        }
        /**
         * @return string|void
         */
        public function process_myaccount_delete_account()
        {
        }
        public function process_edit_profile_form()
        {
        }
        public function process_registration_form()
        {
        }
        public function process_login_form()
        {
        }
        public function process_password_reset_form()
        {
        }
        public function check_password_reset_key()
        {
        }
        public function process_password_reset_handler_form()
        {
        }
    }
    class MelangeTag extends \ProfilePress\Core\ShortcodeParser\FormProcessor
    {
        public function __construct()
        {
        }
        public function parser($atts)
        {
        }
        /**
         * Get the melange structure from the database
         *
         * @param int $id
         * @param string $redirect
         *
         * @return string
         */
        public function get_melange_structure($id, $redirect)
        {
        }
        /**
         * Get the CSS stylesheet for the ID melange
         *
         * @return string
         */
        public static function get_melange_css($melange_id)
        {
        }
        public static function get_instance()
        {
        }
    }
    /**
     * Parse the individual profile shortcode of "Edit profile" builder
     */
    class FrontendProfileTag
    {
        public function __construct()
        {
        }
        public function set_up_detected_profile()
        {
        }
        /**
         * Shortcode callback function to parse the shortcode.
         *
         * @param $atts
         *
         * @return string
         */
        public function user_profile_parser($atts)
        {
        }
        /**
         * Get the registration structure from the database
         *
         * @param int $id
         *
         * @return string
         */
        public static function get_user_profile_structure($id)
        {
        }
        /**
         * Get the CSS stylesheet for the ID registration
         *
         * @return mixed
         */
        public static function get_user_profile_css($id)
        {
        }
        /** Rewrite the title of the profile */
        public function rewrite_profile_title($title)
        {
        }
        public static function title_possessiveness($string)
        {
        }
        public static function get_instance()
        {
        }
    }
    class Init
    {
        public function __construct()
        {
        }
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\ShortcodeParser\MyAccount {
    class MyAccountTag extends \ProfilePress\Core\ShortcodeParser\FormProcessor
    {
        public function __construct()
        {
        }
        public function redirect_non_logged_in_users()
        {
        }
        public static function myaccount_tabs()
        {
        }
        public static function email_notification_endpoint_content()
        {
        }
        public static function account_settings_endpoint_content()
        {
        }
        public function email_notification_callback()
        {
        }
        public function account_settings_callback()
        {
        }
        public function change_password_callback()
        {
        }
        public function delete_account_callback()
        {
        }
        public function display_name_select_dropdown()
        {
        }
        public function edit_profile_callback()
        {
        }
        public function handle_subscription_actions()
        {
        }
        public function subscriptions_callback()
        {
        }
        public function orders_callback()
        {
        }
        public function downloads_callback()
        {
        }
        public function billing_details_callback()
        {
        }
        public static function alert_message($message, $type = 'success')
        {
        }
        public function page_endpoint_title($title)
        {
        }
        public function get_endpoint_title($endpoint)
        {
        }
        /**
         * @return string
         */
        public function get_current_endpoint()
        {
        }
        public function remove_post_query()
        {
        }
        /**
         * @param \WP_Query $q Query instance.
         */
        public function pre_get_posts($q)
        {
        }
        public function parse_request()
        {
        }
        public function get_endpoints_mask()
        {
        }
        function add_endpoints()
        {
        }
        /**
         * Add query vars.
         *
         * @param array $vars Query vars.
         *
         * @return array
         */
        public function add_query_vars($vars)
        {
        }
        public static function get_tab_endpoint($tab_key)
        {
        }
        public static function is_endpoint($tab_key = false)
        {
        }
        /**
         * @param $tab_key
         *
         * @return callable|mixed|bool
         */
        public static function get_tab_callback($tab_key)
        {
        }
        public static function get_endpoint_url($tab_key)
        {
        }
        /**
         * Shortcode callback function to parse the shortcode.
         *
         * @param $atts
         *
         * @return string
         */
        public function parse_shortcode()
        {
        }
        public function js_script()
        {
        }
        public static function get_instance() : self
        {
        }
    }
}
namespace ProfilePress\Core\ShortcodeParser {
    class LoginFormTag extends \ProfilePress\Core\ShortcodeParser\FormProcessor
    {
        public function __construct()
        {
        }
        public function login_parser($atts)
        {
        }
        /**
         * Build the login structure
         *
         * @param int $id login builder ID
         * @param string $redirect url to redirect to. only used by ajax login form.
         *
         * @return string string login structure
         */
        public function get_login_structure($id, $redirect = '')
        {
        }
        /**
         * Get the CSS stylesheet for the ID login
         *
         * @param $form_id
         *
         * @return mixed
         */
        public function get_login_css($form_id)
        {
        }
        static function get_instance()
        {
        }
    }
    class RegistrationFormTag extends \ProfilePress\Core\ShortcodeParser\FormProcessor
    {
        public function __construct()
        {
        }
        public function parse_shortcode($atts)
        {
        }
        /**
         * Get the registration structure from the database
         *
         * @param int $id
         *
         * @param $redirect
         * @param $no_login_redirect
         *
         * @return string
         */
        public static function get_registration_structure($id, $redirect, $no_login_redirect)
        {
        }
        /**
         * Get the CSS stylesheet for the ID registration
         *
         * @param $id
         *
         * @return mixed
         */
        public static function get_registration_css($id)
        {
        }
        public static function get_instance()
        {
        }
    }
    class PasswordResetTag extends \ProfilePress\Core\ShortcodeParser\FormProcessor
    {
        public function __construct()
        {
        }
        /**
         * Parse the password reset shortcode
         *
         * @param $atts
         *
         * @return string
         */
        public function parser($atts)
        {
        }
        /**
         * Get the password reset structure from the database
         *
         * @param int $id
         *
         * @return string
         */
        public function get_password_reset_structure($id)
        {
        }
        public static function get_default_handler_form()
        {
        }
        /**
         * Return password reset handler form or redirect to password reset page when key is invalid.
         *
         * @param int $id
         *
         * @return null|string
         */
        public static function get_password_reset_handler_structure($id = null)
        {
        }
        /**
         * Get the CSS stylesheet for the ID password reset
         *
         * @param $form_id
         *
         * @return mixed
         */
        public static function get_password_reset_css($form_id)
        {
        }
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\Themes\DragDrop {
    trait MemberDirectoryTrait
    {
        protected int $_form_id;
        protected array $args;
        public function initializeMemberDirectoryTrait($_form_id, $args)
        {
        }
        protected function search_query_key() : string
        {
        }
        protected function search_filter_query_params()
        {
        }
        protected function wp_user_query()
        {
        }
        public function member_directory_users(array $parsed_args = []) : \WP_User_Query
        {
        }
        protected function get_current_page() : int
        {
        }
        protected function display_pagination($total_users_found, $users_per_page, $prev_text = '<span class="ppress-material-icons">keyboard_arrow_left</span>', $next_text = '<span class="ppress-material-icons">keyboard_arrow_right</span>')
        {
        }
    }
}
namespace ProfilePress\Core\ShortcodeParser {
    class MemberDirectoryTag
    {
        use \ProfilePress\Core\Themes\DragDrop\MemberDirectoryTrait;
        protected int $directory_id;
        protected int $total_users_found;
        public function __construct()
        {
        }
        public function base64_search_query_params()
        {
        }
        /**
         * @param $atts
         *
         * @return string
         */
        public function parser($atts) : string
        {
        }
        /**
         * @param int $id
         *
         * @return string
         */
        public static function directory_structure(int $id) : string
        {
        }
        /**
         * @param int $id
         *
         * @return string
         */
        public static function directory_css(int $id) : string
        {
        }
        /** Member directory shortcode builder codes STARTS HERE  */
        public function define_shortcodes()
        {
        }
        public function init($sorting = 'newest', $search_fields = '')
        {
        }
        public function search_form($atts)
        {
        }
        public function pagination(array $atts) : string
        {
        }
        public function user_loop($atts, $content) : string
        {
        }
        public static function convert_shortcode_brackets($content)
        {
        }
        public static function get_instance()
        {
        }
    }
    class MembershipShortcodes
    {
        public function __construct()
        {
        }
        function filter_success_page_content($content)
        {
        }
        public function checkout_page_wrapper()
        {
        }
        public function checkout_page()
        {
        }
        public function success_page()
        {
        }
        public static function get_instance()
        {
        }
    }
    class EditProfileTag extends \ProfilePress\Core\ShortcodeParser\FormProcessor
    {
        public function __construct()
        {
        }
        /** Get the current user id */
        public static function get_current_user_id()
        {
        }
        /**
         * Shortcode callback function to parse the shortcode.
         *
         * @param $atts
         *
         * @return string
         */
        public function parse_shortcode($atts)
        {
        }
        /**
         * Get the registration structure from the database
         *
         * @param int $id
         * @param string $redirect URL to redirect to after edit profile.
         *
         * @return string
         */
        public static function get_edit_profile_structure($id, $redirect = '')
        {
        }
        /**
         * Get the CSS stylesheet for the ID registration
         *
         * @return mixed
         */
        public static function get_edit_profile_css($id)
        {
        }
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\ShortcodeParser\Builder {
    class GlobalShortcodes
    {
        public static function initialize()
        {
        }
        /** Get the currently logged user */
        public static function get_current_user()
        {
        }
        /**
         * Login form tag
         *
         * @param array $atts
         * @param string $content
         *
         * @return string
         */
        public static function login_form_tag($atts, $content)
        {
        }
        /**
         * Registration form tag
         *
         * @param array $atts
         * @param string $content
         *
         * @return string
         */
        public static function registration_form_tag($atts, $content)
        {
        }
        /**
         * Password reset form tag
         *
         * @param array $atts
         * @param string $content
         *
         * @return string
         */
        public static function password_reset_form_tag($atts, $content)
        {
        }
        /**
         * Edit profile form tag
         *
         * @param array $atts
         * @param string $content
         *
         * @return string
         */
        public static function edit_profile_form_tag($atts, $content)
        {
        }
        /**
         * @return string
         */
        public static function custom_html_block($atts)
        {
        }
        /**
         * Registration url
         */
        public static function link_registration($atts)
        {
        }
        /** Lost password url */
        public static function link_lost_password($atts)
        {
        }
        /** Login url */
        public static function link_login($atts)
        {
        }
        /** Logout URL */
        public static function link_logout($atts)
        {
        }
        /**
         * URL to user edit page
         */
        public static function link_edit_profile($atts)
        {
        }
        /**
         * Display avatar of currently logged in user
         *
         * @param $atts
         *
         * @return string
         */
        public static function user_avatar($atts)
        {
        }
        public static function user_cover_image($atts)
        {
        }
        /**
         * Redirect non logged users to login page.
         *
         * @param array $atts
         *
         * @return false|string|void
         */
        public static function redirect_non_logged_in_users($atts)
        {
        }
        /**
         * Redirect logged users to login page.
         *
         * @param array $atts
         *
         * @return false|string|void
         */
        public static function redirect_logged_in_users($atts)
        {
        }
        /**
         * Only logged user can view content.
         *
         * @param array $atts
         * @param mixed $content
         *
         * @return mixed
         */
        public static function pp_log_in_users($atts, $content)
        {
        }
        /**
         * Only non-logged user can view content.
         *
         * @param array $atts
         * @param mixed $content
         *
         * @return mixed
         */
        public static function pp_non_log_in_users($atts, $content)
        {
        }
        /**
         * URL to topics started by users.
         *
         * @return string
         */
        public static function bbp_topic_started_url()
        {
        }
        /**
         * URL to topics started by users.
         *
         * @return string
         */
        public static function bbp_replies_created_url()
        {
        }
        /**
         * URL to topics started by users.
         *
         * @return string
         */
        public static function bbp_favorites_url()
        {
        }
        /**
         * URL to topics started by users.
         *
         * @return string
         */
        public static function bbp_subscriptions_url()
        {
        }
    }
    class FrontendProfileBuilder
    {
        /**
         * Define all front-end profile sub-shortcode.
         *
         * @param $user
         */
        public function __construct($user)
        {
        }
        public function date_user_registered()
        {
        }
        public function view_user_profile_url() : string
        {
        }
        public function author_post_url()
        {
        }
        public static function author_posts_query($user_id, $attributes)
        {
        }
        public function author_post_list($attributes)
        {
        }
        public static function author_comment_query($user_id, $attributes)
        {
        }
        public function author_comment_list($attributes)
        {
        }
        /**
         * Profile username
         *
         * @return mixed
         */
        public function profile_username()
        {
        }
        /**
         * User email
         *
         * @return mixed
         */
        public function profile_email()
        {
        }
        /**
         * Return user avatar image url
         *
         * @return string image url
         */
        public function user_avatar_url()
        {
        }
        /**
         * Return user cover photo url
         *
         * @return string image url
         */
        public function cover_image_url()
        {
        }
        /**
         * User website URL
         *
         * @return mixed
         */
        public function profile_website()
        {
        }
        /**
         * Nickname of user
         *
         * @return mixed
         */
        public function profile_nickname()
        {
        }
        /**
         * Display name of profile
         *
         * @return mixed
         */
        public function profile_display_name($atts = false)
        {
        }
        /**
         * Profile first name
         *
         * @return mixed
         */
        public function profile_first_name()
        {
        }
        /**
         * Last name of user.
         *
         * @return mixed
         */
        public function profile_last_name()
        {
        }
        /**
         * Description/bio of user.
         *
         * @return mixed
         */
        public function profile_bio()
        {
        }
        /**
         * Custom profile data of user.
         *
         * @param $atts array shortcode attributes
         *
         * @return string
         */
        public function profile_custom_profile_field($atts)
        {
        }
        public function profile_user_uploaded_file($atts)
        {
        }
        /**
         * Return number of post written by a user
         *
         * @return int
         */
        public function post_count()
        {
        }
        public function hide_empty_data($atts, $content)
        {
        }
        /**
         * Return the total comment count made by a user
         */
        public function get_comment_count()
        {
        }
        protected function jcarousel_css()
        {
        }
        /**
         * jCarousel author latest post slider
         *
         * @param $atts
         *
         * @return string
         */
        public function pp_jcarousel_author_posts($atts)
        {
        }
        public static function get_instance($user = '')
        {
        }
    }
    class EditProfileBuilder
    {
        public function __construct()
        {
        }
        /**
         * Password strength meter field.
         */
        public static function password_meter($atts)
        {
        }
        public static function get_instance()
        {
        }
    }
    /**
     * Parser for the child-shortcode of login form
     */
    class LoginFormBuilder
    {
        /**
         * define all login builder sub shortcode.
         */
        function __construct()
        {
        }
        /**
         * parse the [login-username] shortcode
         *
         * @param array $atts
         *
         * @return string
         */
        function login_username($atts)
        {
        }
        /**
         * @param array $atts
         *
         * parse the [login-password] shortcode
         *
         * @return string
         */
        function login_password($atts)
        {
        }
        /** Remember me checkbox */
        function login_remember($atts)
        {
        }
        public function login_submit($atts)
        {
        }
        public static function get_instance()
        {
        }
    }
    class FieldsShortcodeCallback
    {
        protected $form_type;
        protected $form_name;
        protected $tag_name;
        public function __construct($form_type, $form_name = '', $tag_name = '')
        {
        }
        public function get_current_user()
        {
        }
        public function GET_POST()
        {
        }
        /**
         * Is field a required field?
         *
         * @param array $atts
         *
         * @return bool
         */
        public function is_field_required($atts)
        {
        }
        /**
         * Rewrite custom field key to something more human readable.
         *
         * @param string $key field key
         *
         * @return string
         */
        public function human_readable_field_key($key)
        {
        }
        public static function sanitize_field_attributes($atts)
        {
        }
        public function valid_field_atts($atts)
        {
        }
        public function field_attributes($field_name, $atts, $required = 'false')
        {
        }
        /**
         * @param array $atts
         *
         * @return string
         */
        public function username($atts)
        {
        }
        /**
         * @param array $atts
         *
         * @return string
         */
        public function password($atts)
        {
        }
        /**
         * @param array $atts
         *
         * @return string
         */
        public function confirm_password($atts)
        {
        }
        /**
         * Callback function for email
         *
         * @param $atts
         *
         * @return string
         */
        public function email($atts)
        {
        }
        /**
         * @param array $atts
         *
         * @return string
         */
        public function confirm_email($atts)
        {
        }
        /**
         * @param $atts
         *
         * @return string
         */
        public function website($atts)
        {
        }
        /**
         * Callback function for nickname
         *
         * @param $atts
         *
         * @return string
         */
        public function nickname($atts)
        {
        }
        /**
         * Callback function for nickname
         *
         * @param $atts
         *
         * @return string
         */
        public function display_name($atts)
        {
        }
        /**
         * Callback function for first name
         *
         * @param $atts
         *
         * @return string
         */
        public function first_name($atts)
        {
        }
        /**
         * Callback for last name
         *
         * @param $atts
         *
         * @return string
         */
        public function last_name($atts)
        {
        }
        /**
         * @param $atts
         *
         * @return string
         */
        public function bio($atts)
        {
        }
        /**
         * Upload avatar field
         */
        public function avatar($atts)
        {
        }
        /**
         * Upload cover photo field
         */
        public function cover_image($atts)
        {
        }
        /**
         * @param $atts
         *
         * @return string
         */
        public function textbox_field($atts)
        {
        }
        public function number_field($atts)
        {
        }
        /**
         * @param $atts
         *
         * @return string
         */
        public function cf_password_field($atts)
        {
        }
        /**
         * @param $atts
         *
         * @return string
         */
        public function country_field($atts)
        {
        }
        public static function hasTime($string)
        {
        }
        public static function hasDate($string)
        {
        }
        public static function date_picker_config($field_key, $dateFormat = '')
        {
        }
        /**
         * @param $atts
         *
         * @return string
         */
        public function date_field($atts)
        {
        }
        /**
         * @param $atts
         *
         * @return string
         */
        public function textarea_field($atts)
        {
        }
        public function select_dropdown_field($atts)
        {
        }
        public function radio_buttons_field($atts)
        {
        }
        public function checkbox_list_field($atts)
        {
        }
        public function single_checkbox_field($atts)
        {
        }
        /**
         * @param $atts
         *
         * @return string
         */
        public function custom_profile_field($atts)
        {
        }
        /**
         * Callback function for submit button
         *
         * @param $atts
         *
         * @return string
         */
        public function submit($atts)
        {
        }
        public function select2_js_script($key, $limit = 0)
        {
        }
        /**
         * Remove a user avatar
         *
         * @param $atts
         *
         * @return string
         */
        public function remove_user_avatar($atts)
        {
        }
        /**
         * Remove a user cover photo
         *
         * @param $atts
         *
         * @return string
         */
        public function remove_cover_image($atts)
        {
        }
    }
    class RegistrationFormBuilder
    {
        public static function initialize()
        {
        }
        public static function GET_POST()
        {
        }
        /**
         * Is field a required field?
         *
         * @param array $atts
         *
         * @return bool
         */
        public static function is_field_required($atts)
        {
        }
        /**
         * Rewrite custom field key to something more human readable.
         *
         * @param string $key field key
         *
         * @return string
         */
        public static function human_readable_field_key($key)
        {
        }
        public static function field_attributes($field_name, $atts, $required = 'false')
        {
        }
        /**
         * Password strength meter field.
         * @see http://code.tutsplus.com/articles/using-the-included-password-strength-meter-script-in-wordpress--wp-34736
         */
        public static function password_meter($atts)
        {
        }
        public static function select_role($atts)
        {
        }
    }
    class PasswordResetBuilder
    {
        public function __construct()
        {
        }
        /**
         * parse the [user-login] shortcode
         *
         * @param array $atts
         *
         * @return string
         */
        public function user_login($atts)
        {
        }
        protected function get_processing_label($atts)
        {
        }
        /**
         * Password reset submit button.
         *
         * @param $atts array shortcode param
         *
         * @return string HTML submit button
         */
        public function submit_button($atts)
        {
        }
        /**
         * parse the [enter-password] shortcode
         *
         * @param array $atts
         *
         * @return string
         */
        public function enter_password($atts)
        {
        }
        /**
         * parse the [re-enter-password] shortcode
         *
         * @param array $atts
         *
         * @return string
         */
        function re_enter_password($atts)
        {
        }
        /**
         * Password strength meter field for password reset handler form.
         * @see http://code.tutsplus.com/articles/using-the-included-password-strength-meter-script-in-wordpress--wp-34736
         */
        public static function password_meter($atts)
        {
        }
        /**
         * Password reset handler submit button.
         *
         * @param $atts array shortcode param
         *
         * @return string HTML submit button
         */
        function password_reset_submit($atts)
        {
        }
        /** Singleton poop */
        static function get_instance()
        {
        }
    }
}
namespace ProfilePress {
    class Custom_Settings_Page_Api
    {
        protected function __construct($main_content_config = [], $option_name = '', $page_header = '')
        {
        }
        /**
         * set this as late as possible.
         *
         * @param $db_options
         */
        public function set_db_options($db_options)
        {
        }
        public function option_name($val)
        {
        }
        public function form_method($val)
        {
        }
        public function tab($val)
        {
        }
        public function main_content($val)
        {
        }
        public function add_view_classes($classes)
        {
        }
        public function add_wrap_classes($classes)
        {
        }
        public function remove_nonce_field()
        {
        }
        public function remove_page_form_tag()
        {
        }
        public function remove_white_design()
        {
        }
        public function remove_h2_header()
        {
        }
        public function header_without_frills()
        {
        }
        public function sidebar($val)
        {
        }
        public function page_header($val)
        {
        }
        /**
         * Construct the settings page tab.
         *
         * array(
         *  array('url' => '', 'label' => ''),
         *  array('url' => '', 'label' => ''),
         *  );
         *
         */
        public function settings_page_tab()
        {
        }
        /**
         * Construct the settings page sidebar.
         *
         * array(
         *      array(
         *          'section_title' => 'Documentation',
         *          'content'       => '',
         *`     );
         * );
         *
         */
        public function setting_page_sidebar()
        {
        }
        /**
         * Helper function to recursively sanitize POSTed data.
         *
         * @param $data
         *
         * @return string|array
         */
        public static function sanitize_data($data)
        {
        }
        /**
         * Persist the form data to database.
         *
         * @return \WP_Error|Void
         */
        public function persist_plugin_settings()
        {
        }
        /**
         * Do settings page error
         */
        public function do_settings_errors()
        {
        }
        public function settings_page_heading()
        {
        }
        public function nonce_field()
        {
        }
        /**
         * Get current page URL.
         *
         * @return string
         */
        public function current_page_url()
        {
        }
        /**
         * Main settings page markup.
         *
         * @param bool $return_output
         */
        public function _settings_page_main_content_area($return_output = false)
        {
        }
        /**
         * @param $args
         *
         * @return string
         */
        public function metax_box_instance($args)
        {
        }
        /**
         * @param int $id
         * @param string $name
         * @param string $value
         * @param string $class
         * @param string $placeholder
         * @param string $data_key
         */
        public function _text_field($id, $name, $value, $class = 'regular-text', $placeholder = '', $data_key = '')
        {
        }
        /**
         * Useful if u wanna use any text/html in the settings page.
         *
         * @param mixed $data
         *
         * @return string
         */
        public function _arbitrary($db_options, $key, $args)
        {
        }
        public function _color($db_options, $key, $args)
        {
        }
        /**
         * Renders the text field
         *
         * @param array $db_options addons DB options
         * @param string $key array key of class argument
         * @param array $args class args
         *
         * @return string
         */
        public function _text($db_options, $key, $args)
        {
        }
        public function obfuscate_string($string)
        {
        }
        /**
         * Renders the custom field block
         *
         * @param array $db_options addons DB options
         * @param string $key array key of class argument
         * @param array $args class args
         *
         * @return string
         */
        public function _custom_field_block($db_options, $key, $args)
        {
        }
        /**
         * Renders the number field
         *
         * @param array $db_options addons DB options
         * @param string $key array key of class argument
         * @param array $args class args
         *
         * @return string
         */
        public function _number($db_options, $key, $args)
        {
        }
        /**
         * Renders the password field
         *
         * @param array $db_options addons DB options
         * @param string $key array key of class argument
         * @param array $args class args
         *
         * @return string
         */
        public function _password($db_options, $key, $args)
        {
        }
        /**
         * Renders the number text field
         *
         * @param array $db_options addons DB options
         * @param string $key array key of class argument
         * @param array $args class args
         *
         * @return string
         */
        public function _hidden($db_options, $key, $args)
        {
        }
        /**
         * Renders the textarea field
         *
         * @param array $db_options addons DB options
         * @param string $key array key of class argument
         * @param array $args class args
         *
         * @return string
         */
        public function _textarea($db_options, $key, $args)
        {
        }
        /**
         * Renders the textarea field
         *
         * @param array $db_options addons DB options
         * @param string $key array key of class argument
         * @param array $args class args
         *
         * @return string
         */
        public function _codemirror($db_options, $key, $args)
        {
        }
        /**
         * Renders the textarea field
         *
         * @param array $db_options addons DB options
         * @param string $key array key of class argument
         * @param array $args class args
         *
         * @return string
         */
        public function _wp_editor($db_options, $key, $args)
        {
        }
        /**
         * Renders the email field
         *
         * @param array $db_options addons DB options
         * @param string $key array key of class argument
         * @param array $args class args
         *
         * @return string
         */
        public function _email_editor($db_options, $key, $args)
        {
        }
        /**
         * Renders the select dropdown
         *
         * @param array $db_options addons DB options
         * @param string $key array key of class argument
         * @param array $args class args
         *
         * @return string
         */
        public function _select($db_options, $key, $args)
        {
        }
        protected function select2_selected($db_options, $field_key, $option_value, $default_values = [])
        {
        }
        /**
         * Renders the select2 dropdown
         *
         * @param array $db_options addons DB options
         * @param string $key array key of class argument
         * @param array $args class args
         *
         * @return string
         */
        public function _select2($db_options, $key, $args)
        {
        }
        /**
         * Renders the checkbox field
         *
         * @param array $db_options addons DB options
         * @param string $key array key of class argument
         * @param array $args class args
         *
         * @return string
         */
        public function _checkbox($db_options, $key, $args)
        {
        }
        /**
         * Section header
         *
         * @param string $section_title
         * @param mixed $args
         *
         * @return string
         */
        public function _header($args)
        {
        }
        /**
         * Section header without the frills (title and toggle button).
         *
         * @return string
         */
        public function _header_without_frills($args)
        {
        }
        /**
         * Section footer.
         *
         * @return string
         */
        public function _footer($disable_submit_button = null)
        {
        }
        /**
         * Section footer without "save changes" button.
         *
         * @return string
         */
        public function _footer_without_button()
        {
        }
        /**
         * Build the settings page.
         *
         * @param bool $exclude_sidebar set to true to remove sidebar markup (.column-2)
         *
         * @return mixed|void
         */
        public function build($exclude_sidebar = false, $exclude_top_tav_nav = false)
        {
        }
        /**
         * For building settings page with vertical sidebar tab menus.
         */
        public function build_sidebar_tab_style()
        {
        }
        /**
         * Custom_Settings_Page_Api
         *
         * @param array $main_content_config
         * @param string $option_name
         * @param string $page_header
         *
         * @return Custom_Settings_Page_Api
         */
        public static function instance($main_content_config = [], $option_name = '', $page_header = '')
        {
        }
    }
}
namespace {
    class ProperP_Shogun
    {
        public function __construct()
        {
        }
        public function plugins_api_result($res, $action, $args)
        {
        }
        public function add_plugin_favs($plugin_slug, $res)
        {
        }
        /**
         * @return self
         */
        public static function get_instance()
        {
        }
    }
    class FuseWPAdminNotice
    {
        public function __construct()
        {
        }
        public function dismiss_admin_notice()
        {
        }
        public function admin_notice()
        {
        }
        public function current_admin_url()
        {
        }
        public function is_plugin_installed()
        {
        }
        public function is_plugin_active()
        {
        }
        public function notice_css()
        {
        }
        public static function instance()
        {
        }
    }
    class PPressBFnote
    {
        public function __construct()
        {
        }
        public function dismiss_admin_notice()
        {
        }
        public function admin_notice()
        {
        }
        public function notice_css()
        {
        }
        public static function instance()
        {
        }
    }
}
namespace ProfilePress\Core {
    class RegisterScripts
    {
        public function __construct()
        {
        }
        public static function asset_suffix()
        {
        }
        function admin_css()
        {
        }
        function public_css()
        {
        }
        function public_js()
        {
        }
        function admin_js($hook)
        {
        }
        /**
         * @return self
         */
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\NavigationMenuLinks {
    class PP_Nav_Items
    {
        public $db_id = 0;
        public $object = 'ppnavlog';
        public $object_id;
        public $menu_item_parent = 0;
        public $type = 'custom';
        public $title;
        public $url;
        public $target = '';
        public $attr_title = '';
        public $classes = array();
        public $xfn = '';
    }
    class Init
    {
        public static function init()
        {
        }
    }
    class Backend
    {
        public function __construct()
        {
        }
        public function link_elements()
        {
        }
        /**
         * @param array $item_types Menu item types.
         *
         * @return array
         */
        public function register_customize_nav_menu_item_types($item_types)
        {
        }
        /**
         * @param array $items List of nav menu items.
         * @param string $type Nav menu type.
         * @param string $object Nav menu object.
         * @param integer $page Page number.
         *
         * @return array
         */
        public function register_customize_nav_menu_items($items = array(), $type = '', $object = '', $page = 0)
        {
        }
        public function add_nav_menu_metabox()
        {
        }
        public function nav_menu_metabox($object)
        {
        }
        public function nav_menu_type_label($menu_item)
        {
        }
        public static function get_instance()
        {
        }
    }
    class Frontend
    {
        public function __construct()
        {
        }
        /**
         * Used to return the correct title for the double login/logout menu item
         */
        public function loginout_title($title)
        {
        }
        public function setup_nav_menu_item($item)
        {
        }
        /**
         * Remove navigation item with URL and title that are the same.
         *
         * @param array $sorted_menu_items
         *
         * @return mixed
         */
        public function wp_nav_menu_objects($sorted_menu_items)
        {
        }
        /**
         * @return null|Frontend
         */
        public static function get_instance()
        {
        }
    }
}
namespace ProfilePress\Core\Themes\Shortcode {
    interface ThemeInterface
    {
        public function get_name();
        public function get_structure();
        public function get_css();
    }
    interface RegistrationThemeInterface extends \ProfilePress\Core\Themes\Shortcode\ThemeInterface
    {
        public function success_message();
    }
    interface EditProfileThemeInterface extends \ProfilePress\Core\Themes\Shortcode\RegistrationThemeInterface
    {
    }
}
namespace ProfilePress\Core\Themes\Shortcode\Editprofile {
    class Perfecto implements \ProfilePress\Core\Themes\Shortcode\EditProfileThemeInterface
    {
        public function get_name()
        {
        }
        public function get_structure()
        {
        }
        public function success_message()
        {
        }
        public function get_css()
        {
        }
    }
    class Boson implements \ProfilePress\Core\Themes\Shortcode\EditProfileThemeInterface
    {
        public function get_name()
        {
        }
        public function success_message()
        {
        }
        public function get_structure()
        {
        }
        public function get_css()
        {
        }
    }
}
namespace ProfilePress\Core\Themes\Shortcode {
    class ThemesRepository
    {
        public static function defaultThemes()
        {
        }
        public static function freeThemes()
        {
        }
        public static function premiumThemes()
        {
        }
        /**
         * All Optin themes available.
         *
         * @return mixed
         */
        public static function get_all()
        {
        }
        /**
         * Get form themes of a given type.
         *
         * @param string $form_type
         *
         * @return mixed
         */
        public static function get_by_type($form_type)
        {
        }
        /**
         * Get form theme by name.
         *
         * @param string $name
         *
         * @return mixed
         */
        public static function get_by_name($name)
        {
        }
        /**
         * Add form theme to theme repository.
         *
         * @param mixed $data
         *
         * @return void
         */
        public static function add($data)
        {
        }
    }
}
namespace ProfilePress\Core\Themes\Shortcode\Registration {
    class Fzbuk implements \ProfilePress\Core\Themes\Shortcode\RegistrationThemeInterface
    {
        public function get_name()
        {
        }
        public function success_message()
        {
        }
        public function get_structure()
        {
        }
        public function get_css()
        {
        }
    }
    class Boson implements \ProfilePress\Core\Themes\Shortcode\RegistrationThemeInterface
    {
        public function get_name()
        {
        }
        public function success_message()
        {
        }
        public function get_structure()
        {
        }
        public function get_css()
        {
        }
    }
    class PerfectoLite implements \ProfilePress\Core\Themes\Shortcode\RegistrationThemeInterface
    {
        public function get_name()
        {
        }
        public function get_structure()
        {
        }
        public function success_message()
        {
        }
        public function get_css()
        {
        }
    }
}
namespace ProfilePress\Core\Themes\Shortcode {
    interface MelangeThemeInterface extends \ProfilePress\Core\Themes\Shortcode\ThemeInterface
    {
        public function registration_success_message();
        public function password_reset_success_message();
        public function edit_profile_success_message();
    }
}
namespace ProfilePress\Core\Themes\Shortcode\Melange {
    class Lucid implements \ProfilePress\Core\Themes\Shortcode\MelangeThemeInterface
    {
        public function get_name()
        {
        }
        public function get_structure()
        {
        }
        public function get_css()
        {
        }
        public function registration_success_message()
        {
        }
        public function password_reset_success_message()
        {
        }
        public function edit_profile_success_message()
        {
        }
    }
}
namespace ProfilePress\Core\Themes\Shortcode {
    interface PasswordResetThemeInterface extends \ProfilePress\Core\Themes\Shortcode\ThemeInterface
    {
        public function success_message();
        public function password_reset_handler();
    }
}
namespace ProfilePress\Core\Themes\Shortcode\Passwordreset {
    class Perfecto implements \ProfilePress\Core\Themes\Shortcode\PasswordResetThemeInterface
    {
        public function get_name()
        {
        }
        public function success_message()
        {
        }
        public function password_reset_handler()
        {
        }
        public function get_structure()
        {
        }
        public function get_css()
        {
        }
    }
    class Fzbuk implements \ProfilePress\Core\Themes\Shortcode\PasswordResetThemeInterface
    {
        public function get_name()
        {
        }
        public function success_message()
        {
        }
        public function get_structure()
        {
        }
        public function password_reset_handler()
        {
        }
        public function get_css()
        {
        }
    }
}
namespace ProfilePress\Core\Themes\Shortcode\Userprofile {
    class Daisy implements \ProfilePress\Core\Themes\Shortcode\ThemeInterface
    {
        public function get_name()
        {
        }
        public function get_structure()
        {
        }
        public function get_css()
        {
        }
    }
    class Dixon implements \ProfilePress\Core\Themes\Shortcode\ThemeInterface
    {
        public function get_name()
        {
        }
        public function get_structure()
        {
        }
        public function get_css()
        {
        }
    }
}
namespace ProfilePress\Core\Themes\Shortcode {
    interface LoginThemeInterface extends \ProfilePress\Core\Themes\Shortcode\ThemeInterface
    {
    }
}
namespace ProfilePress\Core\Themes\Shortcode\Login {
    class Fzbuk implements \ProfilePress\Core\Themes\Shortcode\LoginThemeInterface
    {
        public function get_name()
        {
        }
        public function get_structure()
        {
        }
        public function get_css()
        {
        }
    }
    class PerfectoLite implements \ProfilePress\Core\Themes\Shortcode\LoginThemeInterface
    {
        public function get_name()
        {
        }
        public function get_structure()
        {
        }
        public function get_css()
        {
        }
    }
}
namespace ProfilePress\Core\Themes\DragDrop {
    interface ThemeInterface
    {
        public function form_structure();
        public function form_css();
    }
    abstract class AbstractTheme implements \ProfilePress\Core\Themes\DragDrop\ThemeInterface
    {
        public int $form_id;
        public string $form_type;
        public string $tag_name;
        public string $asset_image_url;
        public function __construct($form_id, $form_type)
        {
        }
        public function is_show_social_login()
        {
        }
        /**
         * Array of fields whose settings in form builder should not be modified. Should be as is.
         */
        public function disallowed_settings_fields()
        {
        }
        public function minified_form_css()
        {
        }
        public function get_meta($key)
        {
        }
        public function remember_me_checkbox_remove_label($field_setting)
        {
        }
        public function remember_me_checkbox_wrapper($tag, $field_setting, $form_id, $form_type)
        {
        }
        public function default_metabox_settings()
        {
        }
        public function metabox_settings($settings, $form_type, $DragDropBuilderInstance)
        {
        }
        public function submit_button_settings($settings)
        {
        }
        public function appearance_settings($settings)
        {
        }
        public function color_settings($settings)
        {
        }
        /**
         * Each fields default values.
         *
         * @return array
         */
        public function default_fields_settings()
        {
        }
        public function form_wrapper_shortcode($atts, $content)
        {
        }
        public function field_listing()
        {
        }
        /**
         * @return ProfileFieldListing
         */
        public function profile_listing()
        {
        }
        public function get_profile_field($field_key, $parse_shortcode = false)
        {
        }
        public function form_submit_button()
        {
        }
        public function social_profile_icons()
        {
        }
        public static function get_instance($form_id, $form_type)
        {
        }
    }
    abstract class AbstractBuildScratch extends \ProfilePress\Core\Themes\DragDrop\AbstractTheme
    {
        public function __construct($form_id, $form_type)
        {
        }
        public function add_field_icon($output, $raw_field_setting)
        {
        }
        /**
         * @param $field
         * @param FieldBase $fieldBaseInstance
         *
         * @return mixed
         */
        public function add_field_properties($field, $fieldBaseInstance)
        {
        }
        public function social_login_buttons()
        {
        }
        public function default_metabox_settings()
        {
        }
        public function appearance_settings($settings)
        {
        }
        public function metabox_settings($settings, $form_type, $DragDropBuilderInstance)
        {
        }
        public function field_layout_description()
        {
        }
        public function submit_button_layout_description()
        {
        }
        public function submit_button_settings($settings)
        {
        }
        public function form_structure()
        {
        }
        public function form_links()
        {
        }
        public function form_css()
        {
        }
    }
}
namespace ProfilePress\Core\Themes\DragDrop\EditProfile {
    class BuildScratch extends \ProfilePress\Core\Themes\DragDrop\AbstractBuildScratch
    {
        public static function default_field_listing()
        {
        }
    }
    class Tulip extends \ProfilePress\Core\Themes\DragDrop\AbstractBuildScratch
    {
        public static function default_field_listing()
        {
        }
        public function default_metabox_settings()
        {
        }
    }
}
namespace ProfilePress\Core\Themes\DragDrop {
    class ThemesRepository
    {
        public static function defaultThemes()
        {
        }
        public static function freeThemes()
        {
        }
        public static function premiumThemes()
        {
        }
        /**
         * All Optin themes available.
         *
         * @return mixed
         */
        public static function get_all()
        {
        }
        /**
         * Get form themes of a given type.
         *
         * @param string $form_type
         *
         * @return mixed
         */
        public static function get_by_type($form_type)
        {
        }
        /**
         * Get form theme by name.
         *
         * @param string $name
         *
         * @return mixed
         */
        public static function get_by_name($name)
        {
        }
        /**
         * Add form theme to theme repository.
         *
         * @param mixed $data
         *
         * @return void
         */
        public static function add($data)
        {
        }
    }
}
namespace ProfilePress\Core\Themes\DragDrop\Registration {
    class BuildScratch extends \ProfilePress\Core\Themes\DragDrop\AbstractBuildScratch
    {
        public static function default_field_listing()
        {
        }
    }
    class Tulip extends \ProfilePress\Core\Themes\DragDrop\AbstractBuildScratch
    {
        public static function default_field_listing()
        {
        }
        public function default_metabox_settings()
        {
        }
    }
    class PerfectoLite extends \ProfilePress\Core\Themes\DragDrop\AbstractTheme
    {
        public static function default_field_listing()
        {
        }
        public function appearance_settings($settings)
        {
        }
        public function color_settings($settings)
        {
        }
        public function default_metabox_settings()
        {
        }
        public function form_structure()
        {
        }
        public function form_css()
        {
        }
    }
}
namespace ProfilePress\Core\Themes\DragDrop {
    class FieldListing
    {
        public function __construct($form_id, $form_type, $is_buildscratch = false)
        {
        }
        public function shortcode_field_wrap_start($wrapper = '')
        {
        }
        public function shortcode_field_wrap_end($wrapper = '')
        {
        }
        public function defaults($defaults)
        {
        }
        protected function is_field_required($field_setting)
        {
        }
        protected function label_structure($field_setting, $field_id)
        {
        }
        public function is_avatar($field_type)
        {
        }
        public function is_cover_image($field_type)
        {
        }
        public function is_recaptcha($field_type)
        {
        }
        public function is_recaptcha_v3($field_type)
        {
        }
        public function forge()
        {
        }
    }
    abstract class AbstractMemberDirectoryTheme extends \ProfilePress\Core\Themes\DragDrop\AbstractTheme
    {
        use \ProfilePress\Core\Themes\DragDrop\MemberDirectoryTrait;
        public function __construct($form_id, $form_type)
        {
        }
        abstract function form_wrapper_class();
        abstract function directory_structure();
        public function form_structure()
        {
        }
        public function js_script()
        {
        }
        public function default_metabox_settings()
        {
        }
        public function appearance_settings($settings)
        {
        }
        public function color_settings($settings)
        {
        }
        public function metabox_settings($settings, $form_type, $DragDropBuilderInstance)
        {
        }
        protected function search_filter_query_params()
        {
        }
        public function md_standard_sort_fields() : array
        {
        }
        public function get_sort_field_label($field)
        {
        }
        protected function sort_method_dropdown_menu()
        {
        }
        protected function filter_structure($show_filter_fields = false)
        {
        }
        protected function filter_structure__select_field($query_params, $field_key, $label_name, $options, $is_multiple)
        {
        }
        protected function search_form()
        {
        }
        protected function search_filter_sort_structure()
        {
        }
        protected function is_result_after_search_enabled()
        {
        }
        protected function get_results_text()
        {
        }
        protected function get_single_result_text()
        {
        }
        protected function get_no_result_text()
        {
        }
        protected function get_default_result_number_per_page()
        {
        }
        /**
         * @return MemberDirectoryListing
         */
        protected function directory_listing($user_id = false)
        {
        }
        public function form_css()
        {
        }
    }
}
namespace ProfilePress\Core\Themes\DragDrop\PasswordReset {
    class BuildScratch extends \ProfilePress\Core\Themes\DragDrop\AbstractBuildScratch
    {
        public static function default_field_listing()
        {
        }
    }
    class Tulip extends \ProfilePress\Core\Themes\DragDrop\AbstractBuildScratch
    {
        public static function default_field_listing()
        {
        }
        public function default_metabox_settings()
        {
        }
    }
}
namespace ProfilePress\Core\Themes\DragDrop\UserProfile {
    class Dixon extends \ProfilePress\Core\Themes\DragDrop\AbstractTheme
    {
        public static function default_field_listing()
        {
        }
        public function appearance_settings($settings)
        {
        }
        public function color_settings($settings)
        {
        }
        public function metabox_settings($settings, $form_type, $DragDropBuilderInstance)
        {
        }
        public function default_metabox_settings()
        {
        }
        protected function social_links_block()
        {
        }
        public function form_structure()
        {
        }
        public function form_css()
        {
        }
    }
    class DefaultTemplate extends \ProfilePress\Core\Themes\DragDrop\AbstractTheme
    {
        public function __construct($form_id, $form_type)
        {
        }
        public static function default_field_listing()
        {
        }
        public function default_metabox_settings()
        {
        }
        public function appearance_settings($settings)
        {
        }
        public function color_settings($settings)
        {
        }
        public function profile_tabs()
        {
        }
        public function profile_tabs_section()
        {
        }
        public function profile_content_main()
        {
        }
        public function profile_content_posts()
        {
        }
        public function profile_content_comments()
        {
        }
        public function form_structure()
        {
        }
        public function form_css()
        {
        }
    }
}
namespace ProfilePress\Core\Themes\DragDrop {
    class ProfileFieldListing
    {
        public function __construct($form_id)
        {
        }
        public function defaults($defaults)
        {
        }
        public function item_wrap_start_tag($tag)
        {
        }
        public function item_wrap_end_tag($tag)
        {
        }
        public function title_start_tag($tag)
        {
        }
        public function title_end_tag($tag)
        {
        }
        public function info_start_tag($tag)
        {
        }
        public function info_end_tag($tag)
        {
        }
        public function has_field_data()
        {
        }
        public function forge()
        {
        }
        public function output()
        {
        }
    }
}
namespace ProfilePress\Core\Themes\DragDrop\Login {
    class BuildScratch extends \ProfilePress\Core\Themes\DragDrop\AbstractBuildScratch
    {
        public static function default_field_listing()
        {
        }
    }
    class Tulip extends \ProfilePress\Core\Themes\DragDrop\AbstractBuildScratch
    {
        public static function default_field_listing()
        {
        }
        public function default_metabox_settings()
        {
        }
    }
    class PerfectoLite extends \ProfilePress\Core\Themes\DragDrop\AbstractTheme
    {
        public static function default_field_listing()
        {
        }
        public function appearance_settings($settings)
        {
        }
        public function color_settings($settings)
        {
        }
        public function default_metabox_settings()
        {
        }
        public function form_structure()
        {
        }
        public function form_css()
        {
        }
    }
}
namespace ProfilePress\Core\Themes\DragDrop {
    class MemberDirectoryListing
    {
        public function __construct($directory_id, $user_id = false)
        {
        }
        public function defaults($defaults) : \ProfilePress\Core\Themes\DragDrop\MemberDirectoryListing
        {
        }
        public function forge() : \ProfilePress\Core\Themes\DragDrop\MemberDirectoryListing
        {
        }
        public function output() : string
        {
        }
    }
}
namespace ProfilePress\Core\Themes\DragDrop\MemberDirectory {
    class Gerbera extends \ProfilePress\Core\Themes\DragDrop\AbstractMemberDirectoryTheme
    {
        public static function default_field_listing()
        {
        }
        public function default_metabox_settings()
        {
        }
        public function appearance_settings($settings)
        {
        }
        public function metabox_settings($settings, $form_type, $DragDropBuilderInstance)
        {
        }
        public function form_wrapper_class()
        {
        }
        /**
         * @return false|string|void
         */
        public function directory_structure()
        {
        }
        public function form_css()
        {
        }
    }
    class DefaultTemplate extends \ProfilePress\Core\Themes\DragDrop\AbstractMemberDirectoryTheme
    {
        public static function default_field_listing()
        {
        }
        public function default_metabox_settings()
        {
        }
        public function appearance_settings($settings)
        {
        }
        public function metabox_settings($settings, $form_type, $DragDropBuilderInstance)
        {
        }
        public function color_settings($settings)
        {
        }
        public function form_wrapper_class()
        {
        }
        /**
         * @return false|string|void
         */
        public function directory_structure()
        {
        }
        public function form_css()
        {
        }
    }
}
namespace ProfilePress\Core\Widgets {
    class Init
    {
        public static function init()
        {
        }
    }
    class UserPanel extends \WP_Widget
    {
        public function __construct()
        {
        }
        /**
         * Display Widget.
         *
         * @param array $args
         * @param array $instance
         */
        public function widget($args, $instance)
        {
        }
        public function form($instance)
        {
        }
        /**
         * Sanitize widget form values as they are saved.
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         * @see WP_Widget::update()
         *
         */
        public function update($new_instance, $old_instance)
        {
        }
    }
    class TabbedWidgetDependency
    {
        /**
         * Wrapper function for login authentication
         *
         * @param $username         string      login username
         * @param $password         string      login password
         *
         * @return string
         */
        static function login($username, $password)
        {
        }
        /**
         * Process password reset
         *
         * @param $user_login
         *
         * @return bool|string
         */
        static function retrieve_password_process($user_login)
        {
        }
        /**
         * Register the user - tabbed widget
         *
         * @param string $username
         * @param string $password
         * @param string $email
         *
         * @return \WP_Error|string
         */
        public static function registration($username, $password, $email)
        {
        }
        /**
         * @param $username
         * @param $password
         * @param $email
         *
         * @return mixed
         */
        public static function validate_tab_registration($username, $password, $email)
        {
        }
    }
    class Form extends \WP_Widget
    {
        public function __construct()
        {
        }
        public function widget($args, $instance)
        {
        }
        /**
         * Back-end widget form.
         *
         * @param array $instance
         *
         * @return void
         */
        public function form($instance)
        {
        }
        public function update($new_instance, $old_instance)
        {
        }
    }
    class TabbedWidget extends \WP_Widget
    {
        public $widget_status;
        /**
         * Register widget with WordPress.
         */
        function __construct()
        {
        }
        public function process_form()
        {
        }
        public function script()
        {
        }
        public function widget($args, $instance)
        {
        }
        public function form($instance)
        {
        }
        public function update($new_instance, $old_instance)
        {
        }
    }
}
namespace ProfilePress\Core\Membership {
    class CheckoutFields
    {
        const DB_OPTION_NAME = 'ppress_checkout_fields';
        const ACCOUNT_EMAIL_ADDRESS = 'ppmb_email';
        const ACCOUNT_CONFIRM_EMAIL_ADDRESS = 'ppmb_email2';
        const ACCOUNT_USERNAME = 'ppmb_username';
        const ACCOUNT_PASSWORD = 'ppmb_password';
        const ACCOUNT_CONFIRM_PASSWORD = 'ppmb_password2';
        const ACCOUNT_WEBSITE = 'ppmb_website';
        const ACCOUNT_NICKNAME = 'ppmb_nickname';
        const ACCOUNT_DISPLAY_NAME = 'ppmb_display_name';
        const ACCOUNT_FIRST_NAME = 'ppmb_first_name';
        const ACCOUNT_LAST_NAME = 'ppmb_last_name';
        const ACCOUNT_BIO = 'ppmb_bio';
        /** we using ppress because the constants is the usermeta key/id */
        const BILLING_ADDRESS = 'ppress_billing_address';
        const BILLING_CITY = 'ppress_billing_city';
        const BILLING_COUNTRY = 'ppress_billing_country';
        const BILLING_STATE = 'ppress_billing_state';
        const BILLING_POST_CODE = 'ppress_billing_postcode';
        const BILLING_PHONE_NUMBER = 'ppress_billing_phone';
        const VAT_NUMBER = 'ppress_vat_number';
        public static function logged_in_hidden_fields()
        {
        }
        public static function standard_account_info_fields()
        {
        }
        public static function standard_billing_fields()
        {
        }
        public static function standard_custom_fields()
        {
        }
        public static function account_info_fields()
        {
        }
        public static function billing_fields()
        {
        }
        public static function get_field_id($field_id, $payment_method = '')
        {
        }
        public static function render_field($field_id, $is_required = false, $extra_attr = [], $payment_method = '')
        {
        }
    }
    class CurrencyFormatter
    {
        /**
         * CurrencyFormatter constructor.
         *
         * @param string $amount
         * @param string $currency_code
         */
        public function __construct($amount, $currency_code = '')
        {
        }
        /**
         * Formats the amount for display.
         * Does not apply the currency code.
         *
         * @param bool $decimals
         *
         * @return self
         */
        public function format($decimals = true)
        {
        }
        /**
         * Formats the amount for display.
         * Does not apply the currency code.
         *
         * @param bool $decimals
         *
         * @return self
         */
        public function sanitize($decimals = true)
        {
        }
        /**
         * Applies the currency prefix/suffix to the amount.
         *
         * @return self
         */
        public function apply_symbol()
        {
        }
        /**
         * Current working amount.
         *
         * @return mixed
         */
        public function val()
        {
        }
    }
    class Init
    {
        public static function init()
        {
        }
        public static function cancel_subs_on_user_delete($user_id)
        {
        }
        public static function log_last_login($user_login_or_id)
        {
        }
    }
}
namespace ProfilePress\Core\Membership\Emails {
    abstract class AbstractMembershipEmail
    {
        use \ProfilePress\Core\Membership\Emails\EmailDataTrait;
        /**
         * @param OrderEntity $order
         *
         * @return mixed
         */
        public function get_order_placeholders_values($order, $adminview = false)
        {
        }
        /**
         * @param SubscriptionEntity $subscription
         *
         * @return mixed
         */
        public function get_subscription_placeholders_values($subscription, $adminview = false)
        {
        }
        /**
         * @param string $content
         * @param array $placeholders
         * @param OrderEntity|SubscriptionEntity $order_or_sub
         *
         * @return array|string|string[]
         */
        public function parse_placeholders($content, $placeholders, $order_or_sub)
        {
        }
        /**
         * @return static
         */
        public static function init()
        {
        }
    }
    class SubscriptionAfterExpiredNotification extends \ProfilePress\Core\Membership\Emails\AbstractMembershipEmail
    {
        const ID = 'subscription_after_expired_reminder';
        public function __construct()
        {
        }
        /**
         * @return void
         */
        public function dispatch_email()
        {
        }
    }
    class SubscriptionCompletedNotification extends \ProfilePress\Core\Membership\Emails\AbstractMembershipEmail
    {
        const ID = 'subscription_completed_notification';
        public function __construct()
        {
        }
        /**
         * @param SubscriptionEntity $subscription
         *
         * @return void
         */
        public function dispatch_email($subscription)
        {
        }
    }
    class NewOrderReceipt extends \ProfilePress\Core\Membership\Emails\AbstractMembershipEmail
    {
        const ID = 'new_order_receipt';
        public function __construct()
        {
        }
        /**
         * @param OrderEntity $order
         *
         * @return void
         */
        public function dispatch_email($order)
        {
        }
    }
    class RenewalOrderReceipt extends \ProfilePress\Core\Membership\Emails\AbstractMembershipEmail
    {
        const ID = 'renewal_order_receipt';
        public function __construct()
        {
        }
        /**
         * @param OrderEntity $order
         *
         * @return void
         */
        public function dispatch_email($order)
        {
        }
    }
    class NewOrderAdminNotification extends \ProfilePress\Core\Membership\Emails\AbstractMembershipEmail
    {
        const ID = 'new_order_admin_notification';
        public function __construct()
        {
        }
        /**
         * @param OrderEntity $order
         *
         * @return void
         */
        public function dispatch_email($order)
        {
        }
    }
    class SubscriptionExpirationReminder extends \ProfilePress\Core\Membership\Emails\AbstractMembershipEmail
    {
        const ID = 'subscription_expiration_reminder';
        public function __construct()
        {
        }
        /**
         * @return void
         */
        public function dispatch_email()
        {
        }
    }
    class SubscriptionCancelledNotification extends \ProfilePress\Core\Membership\Emails\AbstractMembershipEmail
    {
        const ID = 'subscription_cancelled_notification';
        public function __construct()
        {
        }
        /**
         * @param SubscriptionEntity $subscription
         *
         * @return void
         */
        public function dispatch_email($subscription, $old_status)
        {
        }
    }
    class SubscriptionExpiredNotification extends \ProfilePress\Core\Membership\Emails\AbstractMembershipEmail
    {
        const ID = 'subscription_expired_notification';
        public function __construct()
        {
        }
        /**
         * @param SubscriptionEntity $subscription
         *
         * @return void
         */
        public function dispatch_email($subscription)
        {
        }
    }
    class SubscriptionRenewalReminder extends \ProfilePress\Core\Membership\Emails\AbstractMembershipEmail
    {
        const ID = 'subscription_renewal_reminder';
        public function __construct()
        {
        }
        /**
         * @return void
         */
        public function dispatch_email()
        {
        }
    }
}
namespace ProfilePress\Core\Membership\Repositories {
    interface RepositoryInterface
    {
        public function add(\ProfilePress\Core\Membership\Models\ModelInterface $data);
        public function update(\ProfilePress\Core\Membership\Models\ModelInterface $data);
        public function delete($id);
        public function retrieve($id);
        public function updateColumn($id, $column, $value);
        public function retrieveColumn($id, $column);
        public function record_count();
    }
    abstract class BaseRepository implements \ProfilePress\Core\Membership\Repositories\RepositoryInterface
    {
        protected $table;
        public function wpdb()
        {
        }
        /**
         * Update a column in table.
         *
         * @param int $id
         * @param string $column
         * @param string $value
         *
         * @return false|int
         */
        public function updateColumn($id, $column, $value)
        {
        }
        /**
         * Retrieve a column in DB table.
         *
         * @param int $id
         * @param string $column
         *
         * @return string|null
         */
        public function retrieveColumn($id, $column)
        {
        }
        /**
         * @return string|null
         */
        public function record_count()
        {
        }
        /**
         * @return static
         */
        public static function init()
        {
        }
    }
    class CustomerRepository extends \ProfilePress\Core\Membership\Repositories\BaseRepository
    {
        protected $table;
        public function __construct()
        {
        }
        /**
         * @param CustomerEntity $data
         *
         * @return false|int
         */
        public function add(\ProfilePress\Core\Membership\Models\ModelInterface $data)
        {
        }
        /**
         * @param CustomerEntity $data
         *
         * @return false|int
         */
        public function update(\ProfilePress\Core\Membership\Models\ModelInterface $data)
        {
        }
        /**
         * @param $id
         *
         * @return int|false
         */
        public function delete($id)
        {
        }
        /**
         * @param $id
         *
         * @return false|CustomerEntity
         */
        public function retrieve($id)
        {
        }
        /**
         * @param int $plan_id
         * @param array $status
         * @param array $extra_args
         *
         * @return array|CustomerEntity
         */
        public function retrieveBySubscription($plan_id, $status = [], $extra_args = [])
        {
        }
        /**
         * @param $user_id
         *
         * @return CustomerEntity
         */
        public function retrieveByUserID($user_id)
        {
        }
        /**
         * @param $args
         * @param $count
         *
         * @return CustomerEntity[]|string
         */
        public function retrieveBy($args = array(), $count = false)
        {
        }
        public function get_count_by_status($status)
        {
        }
    }
    class PlanRepository extends \ProfilePress\Core\Membership\Repositories\BaseRepository
    {
        protected $table;
        public function __construct()
        {
        }
        /**
         * @param PlanEntity $data
         *
         * @return false|int
         */
        public function add(\ProfilePress\Core\Membership\Models\ModelInterface $data)
        {
        }
        /**
         * @param PlanEntity $data
         *
         * @return false|int
         */
        public function update(\ProfilePress\Core\Membership\Models\ModelInterface $data)
        {
        }
        /**
         * @param $id
         *
         * @return int|false
         */
        public function delete($id)
        {
        }
        /**
         * @param $id
         *
         * @return PlanEntity
         */
        public function retrieve($id)
        {
        }
        /**
         * @param int $limit
         * @param int $current_page
         *
         * @return PlanEntity[]|array
         */
        public function retrieveAll($limit = 0, $current_page = 1)
        {
        }
    }
    class CouponRepository extends \ProfilePress\Core\Membership\Repositories\BaseRepository
    {
        protected $table;
        public function __construct()
        {
        }
        /**
         * @param CouponEntity $data
         *
         * @return false|int
         */
        public function add(\ProfilePress\Core\Membership\Models\ModelInterface $data)
        {
        }
        /**
         * @param CouponEntity $data
         *
         * @return false|int
         */
        public function update(\ProfilePress\Core\Membership\Models\ModelInterface $data)
        {
        }
        /**
         * @param $id
         *
         * @return int|false
         */
        public function delete($id)
        {
        }
        /**
         * @param $code
         *
         * @return CouponEntity
         */
        public function retrieveByCode($code)
        {
        }
        /**
         * @param $id
         *
         * @return CouponEntity
         */
        public function retrieve($id)
        {
        }
        /**
         * @param int $limit
         * @param int $current_page
         *
         * @return CouponEntity[]|array
         */
        public function retrieveAll($limit = 0, $current_page = 1)
        {
        }
    }
    class SubscriptionRepository extends \ProfilePress\Core\Membership\Repositories\BaseRepository
    {
        protected $table;
        public function __construct()
        {
        }
        /**
         * @param SubscriptionEntity $data
         *
         * @return false|int
         */
        public function add(\ProfilePress\Core\Membership\Models\ModelInterface $data)
        {
        }
        /**
         * @param SubscriptionEntity $data
         *
         * @return false|int
         */
        public function update(\ProfilePress\Core\Membership\Models\ModelInterface $data)
        {
        }
        /**
         * @param $id
         *
         * @return int|false
         */
        public function delete($id)
        {
        }
        public function delete_pending_subs($customer_id, $plan_id)
        {
        }
        /**
         * @param $id
         *
         * @return SubscriptionEntity
         */
        public function retrieve($id)
        {
        }
        /**
         * @param $args
         * @param $count
         *
         * @return array|SubscriptionEntity[]
         */
        public function retrieveBy($args = [], $count = false)
        {
        }
        public function get_count_by_status($status)
        {
        }
    }
    class OrderRepository extends \ProfilePress\Core\Membership\Repositories\BaseRepository
    {
        protected $table;
        public function __construct()
        {
        }
        /**
         * @param OrderEntity $data
         *
         * @return false|int
         */
        public function add(\ProfilePress\Core\Membership\Models\ModelInterface $data)
        {
        }
        /**
         * @param OrderEntity $data
         *
         * @return false|int
         */
        public function update(\ProfilePress\Core\Membership\Models\ModelInterface $data)
        {
        }
        /**
         * @param $id
         *
         * @return int|false
         */
        public function delete($id)
        {
        }
        public function delete_pending_orders($customer_id, $plan_id)
        {
        }
        /**
         * @param $id
         *
         * @return OrderEntity
         */
        public function retrieve($id)
        {
        }
        /**
         * @param $order_key
         *
         * @return OrderEntity
         */
        public function retrieveByOrderKey($order_key)
        {
        }
        /**
         * @param $args
         * @param $count
         *
         * @return OrderEntity[]|string|int
         */
        public function retrieveBy($args = array(), $count = false)
        {
        }
        public function get_customer_total_spend($customer_id)
        {
        }
        /**
         * @param int $order_id
         * @param string $meta_key
         * @param string $meta_value
         * @param bool $unique
         *
         * @return int|false Meta ID on success, false on failure.
         */
        public function add_meta_data($order_id, $meta_key, $meta_value, $unique = false)
        {
        }
        /**
         * @param int $order_id
         * @param string $meta_key
         * @param string $meta_value
         * @param string $prev_value
         *
         * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
         */
        public function update_meta_data($order_id, $meta_key, $meta_value, $prev_value = '')
        {
        }
        /**
         * @param int $order_id
         * @param string $meta_key
         * @param string $meta_value
         * @param bool $delete_all
         *
         * @return bool True on success, false on failure.
         */
        public function delete_meta_data($order_id, $meta_key, $meta_value = '', $delete_all = false)
        {
        }
        /**
         * @param $order_id
         *
         * @return bool
         */
        public function delete_all_meta_data($order_id)
        {
        }
        /**
         * @param $order_id
         * @param string $meta_key
         * @param bool $single
         *
         * @return array|false|mixed
         */
        public function get_meta_data($order_id, $meta_key = '', $single = true)
        {
        }
        public function get_count_by_status($status)
        {
        }
    }
    class GroupRepository extends \ProfilePress\Core\Membership\Repositories\BaseRepository
    {
        const DB_KEY = 'ppress_plan_group';
        /**
         * @param GroupEntity $data
         *
         * @return bool|int
         */
        public function add(\ProfilePress\Core\Membership\Models\ModelInterface $data)
        {
        }
        /**
         * @param GroupEntity $data
         *
         * @return false
         */
        public function update(\ProfilePress\Core\Membership\Models\ModelInterface $data)
        {
        }
        /**
         * @param $id
         *
         * @return bool
         */
        public function delete($id)
        {
        }
        /**
         * @param $id
         *
         * @return GroupEntity
         */
        public function retrieve($id)
        {
        }
        /**
         * @param int $limit
         * @param int $current_page
         * @param bool $count
         *
         * @return GroupEntity[]|array|int
         */
        public function retrieveAll($limit = 0, $current_page = 1, $order = 'DESC', $count = false)
        {
        }
    }
}
namespace ProfilePress\Core\Membership\Models {
    interface ModelInterface
    {
        public function exists();
    }
    abstract class AbstractModel
    {
        public abstract function exists();
        public function __set($key, $value)
        {
        }
        public function __get($key)
        {
        }
        public function __isset($key)
        {
        }
    }
}
namespace ProfilePress\Core\Membership\Models\Order {
    /**
     * @property int $id
     * @property string $order_key
     * @property int $plan_id
     * @property int $customer_id
     * @property int $subscription_id
     * @property string $order_type
     * @property string $transaction_id
     * @property string $payment_method
     * @property string $status
     * @property string $coupon_code
     * @property string $subtotal
     * @property string $tax
     * @property string $tax_rate
     * @property string $discount
     * @property string $total
     * @property array $billing_address
     * @property array $billing_city
     * @property array $billing_state
     * @property array $billing_postcode
     * @property array $billing_country
     * @property array $billing_phone
     * @property string $mode
     * @property string $currency
     * @property string $ip_address
     * @property string $date_created
     * @property string $date_completed
     */
    class OrderEntity extends \ProfilePress\Core\Membership\Models\AbstractModel implements \ProfilePress\Core\Membership\Models\ModelInterface
    {
        const EU_VAT_NUMBER = 'eu_vat_number';
        const EU_VAT_COUNTRY_CODE = 'eu_vat_country_code';
        const EU_VAT_COMPANY_NAME = 'eu_vat_company_name';
        const EU_VAT_COMPANY_ADDRESS = 'eu_vat_company_address';
        const EU_VAT_NUMBER_IS_VALID = 'eu_vat_number_is_valid';
        const EU_VAT_IS_REVERSE_CHARGED = 'eu_vat_is_reverse_charged';
        /**
         * Order ID
         *
         * @var int
         */
        protected $id = 0;
        protected $plan_id = 0;
        protected $subscription_id = 0;
        /**
         * The payment method mode the order was made in
         *
         * @var string
         */
        protected $mode = \ProfilePress\Core\Membership\Models\Order\OrderMode::LIVE;
        protected $order_type = \ProfilePress\Core\Membership\Models\Order\OrderType::NEW_ORDER;
        /**
         * The Unique order Key
         *
         * @var string
         */
        protected $order_key = '';
        protected $discount = '0';
        protected $tax = '0';
        protected $tax_rate = '0';
        protected $subtotal = '0';
        protected $total = '0';
        protected $coupon_code = '';
        /**
         * The date the order was created
         *
         * @var string
         */
        protected $date_created = '';
        /**
         * The date the payment was marked as 'complete'
         *
         * @var string
         */
        protected $date_completed = '';
        /**
         * The status of the payment
         *
         * @var string
         */
        protected $status = \ProfilePress\Core\Membership\Models\Order\OrderStatus::PENDING;
        /**
         * The customer ID that made the order
         *
         * @var int
         */
        protected $customer_id = 0;
        protected $ip_address = '';
        protected $billing_address = '';
        protected $billing_city = '';
        protected $billing_state = '';
        protected $billing_country = '';
        protected $billing_postcode = '';
        protected $billing_phone = '';
        /**
         * The transaction ID returned by the payment method
         *
         * @var string
         */
        protected $transaction_id = '';
        /**
         * The payment method used to process the order
         *
         * @var string
         */
        protected $payment_method = '';
        /**
         * The currency the order was made with
         *
         * @var string
         */
        protected $currency = '';
        public function __construct($data = [])
        {
        }
        /**
         * @return bool
         */
        public function exists()
        {
        }
        public function get_id()
        {
        }
        /**
         * @param $transaction_id
         *
         * @return false|int
         */
        public function complete_order($transaction_id = '')
        {
        }
        /**
         * @return false|int
         */
        public function fail_order()
        {
        }
        public function get_payment_method_title()
        {
        }
        /**
         * @return false|int
         */
        public function refund_order()
        {
        }
        public function update_status($order_status)
        {
        }
        public function set_status($status)
        {
        }
        public function set_mode($mode)
        {
        }
        public function set_order_key($value)
        {
        }
        public function set_currency($currency)
        {
        }
        /**
         * @return false|int
         */
        public function save()
        {
        }
        public function get_customer_full_address()
        {
        }
        public function get_customer_tax_id()
        {
        }
        public function get_subtotal()
        {
        }
        public function get_tax()
        {
        }
        public function get_tax_rate()
        {
        }
        public function get_total()
        {
        }
        public function get_discount()
        {
        }
        public function get_order_number()
        {
        }
        public function get_order_key()
        {
        }
        public function get_reduced_order_key()
        {
        }
        public function get_order_id()
        {
        }
        public function get_plan_id()
        {
        }
        public function get_subscription_id()
        {
        }
        /**
         * @return SubscriptionEntity
         */
        public function get_subscription()
        {
        }
        public function get_customer_id()
        {
        }
        /**
         * @return CustomerEntity
         */
        public function get_customer()
        {
        }
        /**
         * @return PlanEntity
         */
        public function get_plan()
        {
        }
        public function get_customer_email()
        {
        }
        /**
         * @return string
         */
        public function get_transaction_id()
        {
        }
        public function get_linked_transaction_id()
        {
        }
        public function get_plan_purchase_note()
        {
        }
        public function key_is_valid($key)
        {
        }
        public function is_new_order()
        {
        }
        public function is_renewal_order()
        {
        }
        public function is_completed()
        {
        }
        public function is_failed()
        {
        }
        public function is_pending()
        {
        }
        public function is_refunded()
        {
        }
        public function is_refundable()
        {
        }
        /**
         * @return string
         */
        public function get_refund_url()
        {
        }
        public function update_transaction_id($transaction_id)
        {
        }
        /**
         * @return array
         */
        public function get_notes()
        {
        }
        /**
         * @param $note
         *
         * @return false|int
         */
        public function add_note($note)
        {
        }
        /**
         * @param $note_id
         *
         * @return bool
         */
        public function delete_note($note_id)
        {
        }
        /**
         * @param $meta_key
         *
         * @return array|false|mixed
         */
        public function get_meta($meta_key)
        {
        }
        /**
         * @param $meta_key
         * @param $meta_value
         *
         * @return false|int
         */
        public function add_meta($meta_key, $meta_value)
        {
        }
        /**
         * @param $meta_key
         * @param $meta_value
         *
         * @return bool|int
         */
        public function update_meta($meta_key, $meta_value)
        {
        }
        /**
         * @param $meta_key
         *
         * @return bool
         */
        public function delete_meta($meta_key)
        {
        }
    }
    class CartEntity
    {
        public $plan_id;
        public $change_plan_sub_id;
        public $prorated_price;
        public $sub_total;
        public $coupon_code;
        public $discount_amount;
        public $discount_percentage;
        public $tax_rate;
        public $tax_rate_decimal;
        public $tax_amount;
        public $total;
        // recurring vars
        public $initial_amount;
        public $initial_tax;
        public $initial_tax_rate;
        public $recurring_tax_rate;
        public $recurring_amount;
        public $recurring_tax;
        public $expiration_date;
    }
}
namespace ProfilePress\Core\Membership\Models {
    interface FactoryInterface
    {
        public static function make($data);
        public static function fromId($id);
    }
}
namespace ProfilePress\Core\Membership\Models\Order {
    class OrderFactory implements \ProfilePress\Core\Membership\Models\FactoryInterface
    {
        /**
         * @param $data
         *
         * @return OrderEntity
         */
        public static function make($data)
        {
        }
        /**
         * @param $id
         *
         * @return OrderEntity
         */
        public static function fromId($id)
        {
        }
        /**
         * @param $id
         *
         * @return OrderEntity|false
         */
        public static function fromTransactionId($id)
        {
        }
        /**
         * @param $order_key
         *
         * @return OrderEntity
         */
        public static function fromOrderKey($order_key)
        {
        }
    }
    class OrderStatus
    {
        const COMPLETED = 'completed';
        const PENDING = 'pending';
        const REFUNDED = 'refunded';
        const FAILED = 'failed';
        public static function get_all()
        {
        }
        public static function get_label($status)
        {
        }
    }
    class OrderType
    {
        const NEW_ORDER = 'new';
        const RENEWAL_ORDER = 'renewal';
        const UPGRADE = 'upgrade';
        const DOWNGRADE = 'downgrade';
        public static function get_all()
        {
        }
        public static function get_label($status)
        {
        }
    }
    class OrderMode
    {
        const LIVE = 'live';
        const TEST = 'test';
        public static function get_all()
        {
        }
        public static function get_label($status)
        {
        }
    }
}
namespace ProfilePress\Core\Membership\Models\Group {
    class GroupFactory implements \ProfilePress\Core\Membership\Models\FactoryInterface
    {
        /**
         * @param $data
         *
         * @return GroupEntity
         */
        public static function make($data)
        {
        }
        /**
         * @param $id
         *
         * @return GroupEntity
         */
        public static function fromId($id)
        {
        }
    }
    /**
     * @property int $id
     * @property string $name
     * @property string $plans_display_field
     * @property int[] $plan_ids
     */
    class GroupEntity extends \ProfilePress\Core\Membership\Models\AbstractModel implements \ProfilePress\Core\Membership\Models\ModelInterface
    {
        protected $id = 0;
        protected $name = '';
        protected $plan_ids = [];
        protected $plans_display_field = 'radio';
        public function __construct($data = [])
        {
        }
        /**
         * @return bool
         */
        public function exists()
        {
        }
        /**
         * @return int
         */
        public function get_id()
        {
        }
        /**
         * @return string
         */
        public function get_name()
        {
        }
        public function get_plans_display_field()
        {
        }
        /**
         * @return int[]
         */
        public function get_plan_ids()
        {
        }
        /**
         * @return int|null
         */
        public function get_default_plan_id()
        {
        }
        /**
         * @return false|string
         */
        public function get_checkout_url()
        {
        }
        /**
         * @return false|int
         */
        public function save()
        {
        }
    }
}
namespace ProfilePress\Core\Membership\Models\Plan {
    /**
     * @property int $id
     * @property string $name
     * @property string $user_role
     * @property string $order_note
     * @property string $description
     * @property string $price
     * @property string $billing_frequency
     * @property string $subscription_length
     * @property int $total_payments
     * @property string $signup_fee
     * @property string $free_trial
     */
    class PlanEntity extends \ProfilePress\Core\Membership\Models\AbstractModel implements \ProfilePress\Core\Membership\Models\ModelInterface
    {
        const PLAN_EXTRAS = 'plan_extras';
        protected $id = 0;
        protected $name = '';
        protected $description = '';
        protected $user_role = '';
        protected $order_note = '';
        protected $price = '0';
        protected $billing_frequency = \ProfilePress\Core\Membership\Models\Subscription\SubscriptionBillingFrequency::MONTHLY;
        protected $subscription_length = 'renew_indefinitely';
        // 0 indicates renew indefinitely.
        protected $total_payments = 0;
        protected $signup_fee = '0';
        protected $free_trial = \ProfilePress\Core\Membership\Models\Subscription\SubscriptionTrialPeriod::DISABLED;
        protected $status = 'false';
        protected $meta_data = [];
        public function __construct($data = [])
        {
        }
        /**
         * @return bool
         */
        public function exists()
        {
        }
        public function get_id()
        {
        }
        public function get_name()
        {
        }
        public function is_active()
        {
        }
        public function is_recurring()
        {
        }
        /**
         * If subscription plan, do we want to setup payment gateway subscription for automatic renewal / recurring payments?
         *
         * @return bool
         */
        public function is_auto_renew() : bool
        {
        }
        public function is_lifetime()
        {
        }
        public function has_free_trial()
        {
        }
        public function has_signup_fee()
        {
        }
        public function get_description()
        {
        }
        /**
         * @return string
         */
        public function get_price()
        {
        }
        public function get_billing_frequency()
        {
        }
        public function get_subscription_length()
        {
        }
        public function get_total_payments()
        {
        }
        /**
         * @return string
         */
        public function get_signup_fee()
        {
        }
        public function get_free_trial()
        {
        }
        public function get_edit_plan_url()
        {
        }
        /**
         * @return false|int
         */
        public function save()
        {
        }
        /**
         * @return false|string
         */
        public function get_checkout_url()
        {
        }
        /**
         * @return bool
         */
        public function has_downloads()
        {
        }
        public function get_downloads()
        {
        }
        /**
         * @param string $extra_key
         *
         * @return false|mixed
         */
        public function get_plan_extras($extra_key = '')
        {
        }
        public function update_meta($meta_key, $meta_value)
        {
        }
        /**
         * @param $meta_key
         *
         * @return false|mixed
         */
        public function get_meta($meta_key)
        {
        }
        /**
         * @param $meta_key
         *
         * @return false|int
         */
        public function delete_meta($meta_key)
        {
        }
        /**
         * @return false|int
         */
        public function activate()
        {
        }
        /**
         * @return false|int
         */
        public function deactivate()
        {
        }
        /**
         * @return int|false
         */
        public function get_group_id()
        {
        }
    }
    class PlanFactory implements \ProfilePress\Core\Membership\Models\FactoryInterface
    {
        /**
         * @param $data
         *
         * @return PlanEntity
         */
        public static function make($data)
        {
        }
        /**
         * @param $id
         *
         * @return PlanEntity
         */
        public static function fromId($id)
        {
        }
    }
}
namespace ProfilePress\Core\Membership\Models\Coupon {
    /**
     * @property int $id
     * @property string $code
     * @property string $description
     * @property string $coupon_type
     * @property string $coupon_application
     * @property string $is_onetime_use
     * @property string $amount
     * @property string $unit
     * @property array $plan_ids
     * @property int $usage_limit
     * @property string $status
     * @property string $start_date
     * @property string $end_date
     */
    class CouponEntity extends \ProfilePress\Core\Membership\Models\AbstractModel implements \ProfilePress\Core\Membership\Models\ModelInterface
    {
        protected $id = 0;
        protected $code = '';
        protected $description = '';
        protected $coupon_type = \ProfilePress\Core\Membership\Models\Coupon\CouponType::RECURRING;
        protected $coupon_application = \ProfilePress\Core\Membership\Models\Coupon\CouponApplication::NEW_PURCHASE;
        protected $amount = 0;
        protected $unit = \ProfilePress\Core\Membership\Models\Coupon\CouponUnit::PERCENTAGE;
        protected $plan_ids = [];
        protected $usage_limit = '';
        protected $status = 'true';
        protected $is_onetime_use = 'false';
        protected $start_date = '';
        protected $end_date = '';
        public function __construct($data = [])
        {
        }
        /**
         * @return bool
         */
        public function exists()
        {
        }
        public function is_active()
        {
        }
        public function is_onetime_use()
        {
        }
        public function is_recurring()
        {
        }
        public function is_expired()
        {
        }
        protected function set_coupon_application($val)
        {
        }
        public function get_coupon_type()
        {
        }
        public function get_coupon_application()
        {
        }
        public function get_id()
        {
        }
        /**
         * @return string
         */
        public function get_description()
        {
        }
        /**
         * @return string
         */
        public function get_amount()
        {
        }
        protected function set_plan_ids($value)
        {
        }
        public function get_plan_ids()
        {
        }
        public function get_usage_limit()
        {
        }
        public function get_start_date()
        {
        }
        public function get_end_date()
        {
        }
        /**
         * Check if a coupon is valid
         *
         * @param int $plan_id
         * @param string $order_type
         *
         * @return bool
         */
        public function is_valid($plan_id = 0, $order_type = \ProfilePress\Core\Membership\Models\Order\OrderType::NEW_ORDER)
        {
        }
        /**
         * Return how many times coupon has been used. update this after every successful order
         *
         * @return int
         */
        public function get_usage_count()
        {
        }
        /**
         * @return false|int
         */
        public function save()
        {
        }
        /**
         * @return false|int
         */
        public function activate()
        {
        }
        /**
         * @return false|int
         */
        public function deactivate()
        {
        }
    }
    class CouponApplication
    {
        const NEW_PURCHASE = 'acquisition';
        const EXISTING_PURCHASE = 'retention';
        const ANY_PURCHASE = 'any';
    }
    class CouponUnit
    {
        const PERCENTAGE = 'percent';
        const FLAT = 'flat';
    }
    class CouponFactory implements \ProfilePress\Core\Membership\Models\FactoryInterface
    {
        /**
         * @param $data
         *
         * @return CouponEntity
         */
        public static function make($data)
        {
        }
        /**
         * @param $id
         *
         * @return CouponEntity
         */
        public static function fromId($id)
        {
        }
        /**
         * @param $code
         *
         * @return CouponEntity
         */
        public static function fromCode($code)
        {
        }
    }
    class CouponType
    {
        const RECURRING = 'recurring';
        const ONE_TIME = 'one_time';
        const FIRST_PAYMENT_ONLY = 'one_time';
    }
}
namespace ProfilePress\Core\Membership\Models\Subscription {
    class SubscriptionStatus
    {
        const ACTIVE = 'active';
        const PENDING = 'pending';
        const CANCELLED = 'cancelled';
        const EXPIRED = 'expired';
        const TRIALLING = 'trialling';
        const COMPLETED = 'completed';
        public static function get_all()
        {
        }
        public static function get_label($status)
        {
        }
    }
    class SubscriptionFactory implements \ProfilePress\Core\Membership\Models\FactoryInterface
    {
        /**
         * @param $data
         *
         * @return SubscriptionEntity
         */
        public static function make($data)
        {
        }
        /**
         * @param $id
         *
         * @return SubscriptionEntity
         */
        public static function fromId($id)
        {
        }
        /**
         * @param $profile_id
         *
         * @return SubscriptionEntity|false
         */
        public static function fromProfileId($profile_id)
        {
        }
    }
    /**
     * @property int $id
     * @property int $parent_order_id
     * @property int $plan_id
     * @property int $customer_id
     * @property string $billing_frequency
     * @property string $initial_amount
     * @property string $recurring_amount
     * @property string $initial_tax
     * @property string $initial_tax_rate
     * @property string $recurring_tax
     * @property string $recurring_tax_rate
     * @property int $total_payments
     * @property string $trial_period
     * @property string $profile_id
     * @property string $status
     * @property array $notes
     * @property string $created_date
     * @property string $expiration_date
     */
    class SubscriptionEntity extends \ProfilePress\Core\Membership\Models\AbstractModel implements \ProfilePress\Core\Membership\Models\ModelInterface
    {
        const DB_META_KEY = 'sbmeta';
        /**
         * Subscription ID
         *
         * @var int
         */
        protected $id = 0;
        /**
         * Subscription Parent order ID
         *
         * @var int
         */
        protected $parent_order_id = 0;
        /**
         * The Plan ID that this subscription is for.
         *
         * @var int
         */
        protected $plan_id = 0;
        /**
         * Customer ID with this subscription.
         *
         * @var int
         */
        protected $customer_id = 0;
        /**
         * Billing frequency
         *
         * @var string
         */
        protected $billing_frequency = \ProfilePress\Core\Membership\Models\Subscription\SubscriptionBillingFrequency::MONTHLY;
        /**
         * @var string
         */
        protected $initial_amount = '0';
        /**
         * @var string
         */
        protected $recurring_amount = '0';
        /**
         * @var string
         */
        protected $initial_tax = '0';
        /**
         * @var string
         */
        protected $initial_tax_rate = '0';
        /**
         * @var string
         */
        protected $recurring_tax = '0';
        /**
         * @var string
         */
        protected $recurring_tax_rate = '0';
        /**
         * @var int
         */
        protected $total_payments = 0;
        /**
         * @var string
         */
        protected $trial_period = \ProfilePress\Core\Membership\Models\Subscription\SubscriptionTrialPeriod::DISABLED;
        /**
         * @var string
         */
        protected $profile_id = '';
        /**
         * @var string
         */
        protected $status = \ProfilePress\Core\Membership\Models\Subscription\SubscriptionStatus::PENDING;
        /**
         * @var string
         */
        protected $notes = [];
        /**
         * @var string
         */
        protected $created_date = '';
        /**
         * @var string
         */
        protected $expiration_date = '';
        public function __construct($data = [])
        {
        }
        /**
         * @return bool
         */
        public function exists()
        {
        }
        public function get_id()
        {
        }
        /**
         * Check if a subscription is active and not expired.
         *
         * @return bool
         */
        public function is_active()
        {
        }
        public function is_expired()
        {
        }
        public function is_pending()
        {
        }
        public function is_cancelled()
        {
        }
        public function is_completed()
        {
        }
        public function is_recurring()
        {
        }
        public function is_lifetime()
        {
        }
        public function has_trial()
        {
        }
        public function get_parent_order_id()
        {
        }
        /**
         * @return PlanEntity
         */
        public function get_plan()
        {
        }
        public function get_plan_id()
        {
        }
        public function get_customer_id()
        {
        }
        public function get_customer()
        {
        }
        /**
         * Total sub initial amount including tax.
         *
         * @return string
         */
        public function get_initial_amount()
        {
        }
        /**
         * Total sub recurring amount including tax.
         *
         * @return string
         */
        public function get_recurring_amount()
        {
        }
        public function get_initial_tax()
        {
        }
        public function get_initial_tax_rate()
        {
        }
        public function get_recurring_tax()
        {
        }
        public function get_recurring_tax_rate()
        {
        }
        public function get_total_payments()
        {
        }
        public function get_completed_order_count()
        {
        }
        public function set_trial_period($period)
        {
        }
        public function get_profile_id()
        {
        }
        protected function set_status($status)
        {
        }
        public function get_status()
        {
        }
        public function get_status_label()
        {
        }
        public function get_payment_method()
        {
        }
        public function get_formatted_expiration_date()
        {
        }
        public function get_notes()
        {
        }
        public function add_note($note)
        {
        }
        public function get_subscription_terms()
        {
        }
        /**
         * Retrieve all subscription related payments.
         *
         * @return OrderEntity[]|string
         */
        public function get_all_orders()
        {
        }
        /**
         * @return OrderEntity|false
         */
        public function get_last_order()
        {
        }
        public function add_plan_role_to_customer()
        {
        }
        public function remove_plan_role_from_customer()
        {
        }
        /**
         * @param $profile_id
         *
         * @return false|int
         */
        public function activate_subscription($profile_id = '')
        {
        }
        /**
         * @param $profile_id
         *
         * @return false|int
         */
        public function enable_subscription_trial($profile_id = '')
        {
        }
        public function update_profile_id($val)
        {
        }
        /**
         * @param $subscription_status
         *
         * @return false|int
         */
        public function update_status($subscription_status)
        {
        }
        /**
         * Returns the number of times the subscription has been billed
         *
         * @return int
         */
        public function get_times_billed()
        {
        }
        /**
         * Determines if subscription can be cancelled
         *
         * This method is filtered by payment methods in order to return true on subscriptions
         * that can be cancelled with a profile ID through the merchant processor
         *
         * @return bool
         */
        public function can_cancel()
        {
        }
        public function has_cancellation_requested()
        {
        }
        public function add_cancellation_requested()
        {
        }
        public function delete_cancellation_requested()
        {
        }
        /**
         * @param bool $gateway_cancel set to true to cancel sub in gateway too.
         *
         * @return false|void
         */
        public function cancel($gateway_cancel = false, $cancel_immediately = false)
        {
        }
        /**
         * @return false|void
         */
        public function complete()
        {
        }
        /**
         * @param bool $check_expiration
         *
         * @return false|void
         */
        public function expire($check_expiration = false, $addBuffer = false)
        {
        }
        /**
         * @param $change_expiry_date
         * @param int $expiration_date timestamp in UTC
         *
         * @return void
         */
        public function renew($change_expiry_date = true, $expiration_date = '')
        {
        }
        public function maybe_complete_subscription()
        {
        }
        /**
         * @param $args
         *
         * @return false|int
         */
        public function add_renewal_order($args)
        {
        }
        /**
         * @return false|int
         */
        public function save()
        {
        }
        public function get_meta_flag_id()
        {
        }
        /**
         * @param $meta_key
         * @param $meta_value
         *
         * @return int|false
         */
        public function update_meta($meta_key, $meta_value)
        {
        }
        /**
         * @param $meta_key
         *
         * @return string|null
         */
        public function get_meta($meta_key)
        {
        }
        public function delete_meta($meta_key)
        {
        }
    }
    class SubscriptionBillingFrequency
    {
        const MONTHLY = 'monthly';
        const WEEKLY = 'weekly';
        const DAILY = 'daily';
        const QUARTERLY = '3_month';
        const EVERY_6_MONTHS = '6_month';
        const YEARLY = '1_year';
        const ONE_TIME = 'lifetime';
        const LIFETIME = 'lifetime';
        public static function get_all()
        {
        }
        public static function get_label($status)
        {
        }
    }
    class SubscriptionTrialPeriod
    {
        const DISABLED = 'disabled';
        const THREE_DAYS = '3_day';
        const FIVE_DAYS = '5_day';
        const ONE_WEEK = '1_week';
        const TWO_WEEKS = '2_week';
        const THREE_WEEKS = '3_week';
        const ONE_MONTH = '1_month';
        public static function get_all()
        {
        }
        public static function get_label($status)
        {
        }
    }
}
namespace ProfilePress\Core\Membership\Models\Customer {
    class CustomerStatus
    {
        const ACTIVE = 'active';
        const INACTIVE = 'inactive';
        public static function get_all()
        {
        }
        public static function get_label($status)
        {
        }
    }
    /**
     * @property int $id
     * @property string $user_id
     * @property int $purchase_count
     * @property string $total_spend
     * @property string $private_note
     * @property string $date_created
     */
    class CustomerEntity extends \ProfilePress\Core\Membership\Models\AbstractModel implements \ProfilePress\Core\Membership\Models\ModelInterface
    {
        protected $id = 0;
        protected $user_id = 0;
        protected $total_spend = 0;
        protected $purchase_count = 0;
        protected $private_note = '';
        protected $last_login = '';
        protected $date_created = '';
        /** @var false|\WP_User|null */
        protected $wp_user = null;
        public function __construct($data = [])
        {
        }
        /**
         * @return bool
         */
        public function exists()
        {
        }
        public function user_exists()
        {
        }
        public function get_id()
        {
        }
        public function get_user_id()
        {
        }
        public function get_first_name()
        {
        }
        public function get_last_name()
        {
        }
        public function get_name()
        {
        }
        public function get_email()
        {
        }
        public function get_wp_user()
        {
        }
        /**
         * @return string
         */
        public function get_private_note()
        {
        }
        public function get_last_login()
        {
        }
        public function get_date_created()
        {
        }
        public function is_active($include_trial = true)
        {
        }
        /**
         * @param array $args
         * @param bool $count
         *
         * @return OrderEntity[]|int
         */
        public function get_orders($args = [], $count = false)
        {
        }
        /**
         * @param array $status
         * @param array $args
         * @param bool $count
         *
         * @return SubscriptionEntity[]|int
         */
        public function get_subscriptions($status = [], $args = [], $count = false)
        {
        }
        /**
         * @param $include_trial
         *
         * @return SubscriptionEntity[]
         */
        public function get_active_subscriptions($include_trial = true)
        {
        }
        /**
         * @param null $plan_id
         * @param bool $return_sub set to true to return the act
         *
         * @return bool|SubscriptionEntity
         */
        public function has_active_subscription($plan_id = null, $return_sub = false)
        {
        }
        /**
         * @param int $group_id
         *
         * @return bool
         */
        public function has_active_group_subscription($group_id)
        {
        }
        /**
         * Check if customer has any subscription regardless of the subscription status.
         *
         * @param $plan_id
         * @param array $status
         *
         * @return bool
         */
        public function has_any_status_subscription($plan_id, $status = [])
        {
        }
        /**
         * @param string|null $field if specified, retrieve a single field
         *
         * @return array|string
         */
        public function get_billing_details($field = null)
        {
        }
        /**
         * @return false|int
         */
        public function save()
        {
        }
        /**
         * Recalculate stats for this customer.
         */
        public function recalculate_stats()
        {
        }
        /**
         * @param $meta_key
         * @param bool $single
         *
         * @return array|false|mixed
         */
        public function get_meta($meta_key, $single = true)
        {
        }
        /**
         * @param $meta_key
         * @param $meta_value
         *
         * @return false|int
         */
        public function add_meta($meta_key, $meta_value)
        {
        }
        /**
         * @param $meta_key
         * @param $meta_value
         *
         * @return bool|int
         */
        public function update_meta($meta_key, $meta_value)
        {
        }
        /**
         * @param $meta_key
         * @param $meta_value
         *
         * @return bool
         */
        public function delete_meta($meta_key, $meta_value = '')
        {
        }
    }
    class CustomerFactory implements \ProfilePress\Core\Membership\Models\FactoryInterface
    {
        /**
         * @param $data
         *
         * @return CustomerEntity
         */
        public static function make($data)
        {
        }
        /**
         * @param $id
         *
         * @return CustomerEntity
         */
        public static function fromId($id)
        {
        }
        /**
         * @param $user_id
         *
         * @return CustomerEntity
         */
        public static function fromUserId($user_id)
        {
        }
    }
}
namespace ProfilePress\Core\Membership\PaymentMethods {
    interface PaymentMethodInterface
    {
        public function get_id();
        public function get_title();
        public function get_description();
        public function get_method_title();
        public function get_method_description();
        public function is_enabled();
        public function supports($feature);
        public function get_icon();
        public function has_fields();
        public function payment_fields();
    }
    /**
     * @property int $id
     * @property string $title
     * @property string $description
     * @property string $method_title
     * @property string $method_description
     * @property string $order_button_text
     * @property bool $has_fields
     * @property string $icon
     */
    abstract class AbstractPaymentMethod implements \ProfilePress\Core\Membership\PaymentMethods\PaymentMethodInterface
    {
        const DEFAULT_CC_FORM = 'credit_card_form_support';
        const REFUNDS = 'refunds_support';
        const SUBSCRIPTIONS = 'subscriptions_support';
        const SUBSCRIPTION_CANCELLATION = 'subscription_cancellation_support';
        const TITLE_DB_OPTION_NAME = 'title';
        const DESCRIPTION_DB_OPTION_NAME = 'description';
        /**
         * @var string Method Unique Identifier.
         */
        protected $id;
        /**
         * @var bool Useful if method should not show up on checkout page
         */
        protected $backend_only = false;
        /**
         * Gateway title for the frontend.
         *
         * @var string
         */
        protected $title;
        /**
         * Gateway description for the frontend.
         *
         * @var string
         */
        protected $description;
        /**
         * Gateway title.
         *
         * @var string
         */
        protected $method_title = '';
        /**
         * Gateway description.
         *
         * @var string
         */
        protected $method_description = '';
        /**
         * True if the gateway shows fields on the checkout.
         *
         * @var bool
         */
        protected $has_fields = false;
        /**
         * Icon for the gateway.
         *
         * @var string
         */
        protected $icon;
        protected $supports = [];
        public function __construct()
        {
        }
        public function __set($key, $value)
        {
        }
        public function __get($key)
        {
        }
        public function webhook_callback()
        {
        }
        public function is_enabled()
        {
        }
        public function is_backend_only()
        {
        }
        /**
         * Get Gateway  Id.
         *
         * @return string The email id.
         */
        public function get_id()
        {
        }
        /**
         * Return the gateway's title.
         *
         * @return string
         */
        public function get_title()
        {
        }
        /**
         * Return the gateway's description.
         *
         * @return string
         */
        public function get_description()
        {
        }
        /**
         * Return the title for admin screens.
         *
         * @return string
         */
        public function get_method_title()
        {
        }
        /**
         * Return the description for admin screens.
         *
         * @return string
         */
        public function get_method_description()
        {
        }
        public function admin_settings()
        {
        }
        /**
         * Check if the gateway has fields on the checkout.
         *
         * @return bool
         */
        public function has_fields()
        {
        }
        /**
         * Return the gateway's icon.
         *
         * @return string
         */
        public function get_icon()
        {
        }
        public function get_admin_page_url()
        {
        }
        public static function get_payment_method_admin_page_url($payment_method)
        {
        }
        public function get_webhook_url()
        {
        }
        /**
         * Get setting value.
         *
         * @param $setting
         */
        public function get_value($setting, $default = false)
        {
        }
        /**
         * If There are no payment fields show the description if set.
         * Override this in your gateway if you have some.
         */
        public function payment_fields()
        {
        }
        /**
         * Useful for enqueuing frontend assets.
         *
         * @return void
         */
        public function enqueue_frontend_assets()
        {
        }
        public function should_validate_billing_details($val)
        {
        }
        abstract function process_webhook();
        /**
         * Validate frontend fields.
         *
         * Validate payment fields on the frontend.
         *
         * @return bool|\WP_Error
         */
        abstract function validate_fields();
        /**
         * Process Payment.
         *
         * Process the payment. Override this in your gateway. When implemented, this should.
         * return the success and redirect in an array. e.g:
         *
         *        return array(
         *            'result'   => 'success',
         *            'redirect' => $this->get_return_url( $order )
         *        );
         *
         * @param int $order_id Order ID.
         *
         * @return mixed|void
         */
        abstract function process_payment($order_id, $subscription_id, $customer_id);
        /**
         * Process refund.
         *
         * If the payment gateway declares 'refunds' support, this will allow it to refund a passed in amount.
         *
         * @param int $order_id Order ID.
         * @param string $amount Refund amount.
         * @param string $reason Refund reason.
         *
         * @return boolean
         */
        public function process_refund($order_id, $amount = null, $reason = '')
        {
        }
        /**
         * Get a link to the transaction on the 3rd party gateway site (if applicable).
         *
         * @param string $transaction_id
         * @param OrderEntity $order
         *
         * @return string transaction URL, or empty string.
         */
        public function link_transaction_id($transaction_id, $order)
        {
        }
        /**
         * Get subscription profile Link.
         *
         * @param string $profile_id The profile id.
         * @param SubscriptionEntity $subscription
         *
         * @return string $profile_link The profile link link.
         */
        public function link_profile_id($profile_id, $subscription)
        {
        }
        public function supports($feature)
        {
        }
        public function credit_card_form()
        {
        }
        protected function billing_address_form()
        {
        }
        /**
         * Determines if a subscription can be cancelled through the gateway
         */
        public function can_cancel($ret, $subscription)
        {
        }
        /**
         * Returns an array of subscription statuses that can be cancelled
         *
         * @return array
         */
        public function get_cancellable_statuses()
        {
        }
        /**
         * @param SubscriptionEntity $subscription
         *
         * @return bool|void
         */
        public function cancel_sub_on_completion($subscription)
        {
        }
        /**
         * Cancels a subscription. If possible, cancel at the period end. If not possible, cancel immediately.
         *
         * @param SubscriptionEntity $subscription
         *
         * @return bool
         */
        public function cancel($subscription)
        {
        }
        /**
         * Cancels a subscription immediately.
         *
         * @param SubscriptionEntity $subscription
         *
         * @return bool
         */
        public function cancel_immediately($subscription)
        {
        }
        /**
         * Get the return url (thank you page).
         *
         * @param $order_key
         *
         * @return string
         */
        public function get_success_url($order_key = '')
        {
        }
        /**
         * @param $order_key
         *
         * @return string
         */
        public function get_cancel_url($order_key = '')
        {
        }
        public static function get_instance()
        {
        }
    }
    class PaymentMethods
    {
        public function __construct()
        {
        }
        /**
         * @return AbstractPaymentMethod[]
         */
        public function registered_methods()
        {
        }
        /**
         * @return PaymentMethodInterface[]
         */
        public function get_all($sort = false)
        {
        }
        /**
         * Returns payment method ID and title.
         *
         * @return PaymentMethodInterface[]
         */
        public function get_enabled_methods($include_backend_only = false)
        {
        }
        /**
         * @return false|string
         */
        public function get_default_method()
        {
        }
        /**
         * @param $id
         *
         * @return AbstractPaymentMethod|false
         */
        public function get_by_id($id)
        {
        }
        public static function get_instance()
        {
        }
    }
    interface WebhookHandlerInterface
    {
        public function handle($event_data);
    }
    class StoreGateway extends \ProfilePress\Core\Membership\PaymentMethods\AbstractPaymentMethod
    {
        public function __construct()
        {
        }
        public function admin_settings()
        {
        }
        /** fulfill contract */
        public function validate_fields()
        {
        }
        /** fulfill contract */
        public function process_payment($order_id, $subscription_id, $customer_id)
        {
        }
        /** fulfill contract */
        public function process_webhook()
        {
        }
    }
}
namespace ProfilePress\Core\Membership\PaymentMethods\BankTransfer {
    class BankTransfer extends \ProfilePress\Core\Membership\PaymentMethods\AbstractPaymentMethod
    {
        public function __construct()
        {
        }
        protected function is_billing_fields_removed()
        {
        }
        public function admin_settings()
        {
        }
        /**
         * @return bool|\WP_Error
         */
        public function validate_fields()
        {
        }
        /**
         * Disable billing validation.
         *
         * @param $val
         *
         * @return bool
         */
        public function should_validate_billing_details($val)
        {
        }
        protected function billing_address_form()
        {
        }
        public function process_payment($order_id, $subscription_id, $customer_id)
        {
        }
        /**
         * @param OrderEntity $order
         *
         * @return void
         */
        public function frontend_bank_account_details($order)
        {
        }
        public function process_webhook()
        {
        }
    }
}
namespace ProfilePress\Core\Membership\PaymentMethods\Stripe {
    class APIClass
    {
        /**
         * Configures the Stripe API before each request.
         */
        public static function _setup()
        {
        }
        /**
         * @return StripeClient
         */
        public static function stripeClient()
        {
        }
        /**
         * @param $account_id
         *
         * @return array|WP_Error
         *
         * @throws \Exception
         */
        public function get_account($account_id)
        {
        }
    }
    class PaymentHelpers
    {
        public static function add_coupon_to_bucket($stripe_coupon_id)
        {
        }
        public static function empty_coupon_bucket()
        {
        }
        public static function delete_coupon($stripe_coupon_id)
        {
        }
        /**
         * @return string
         */
        public static function get_signup_fee_label()
        {
        }
        public static function stripe_amount_to_ppress_amount($amount, $currency = '')
        {
        }
        /**
         * @param string $freeTrial
         *
         * @return int
         */
        public static function free_trial_days_count($freeTrial)
        {
        }
        /**
         * @param SubscriptionEntity $subscription
         *
         * @return mixed
         *
         * @throws \Exception
         */
        public static function get_product_price($subscription, $statement_descriptor)
        {
        }
        /**
         * @param CustomerEntity $customer
         *
         * @return int
         *
         * @throws \Exception
         */
        public static function get_stripe_customer_id($customer)
        {
        }
        public static function is_zero_decimal_currency($currency = '')
        {
        }
        public static function process_amount($price, $currency = '')
        {
        }
        public static function application_fee_percent()
        {
        }
        public static function application_fee_amount($order_total)
        {
        }
        public static function has_application_fee()
        {
        }
    }
}
namespace ProfilePress\Core\Membership\PaymentMethods\Stripe\WebhookHandlers {
    class CustomerSubscriptionDeleted implements \ProfilePress\Core\Membership\PaymentMethods\WebhookHandlerInterface
    {
        public function handle($event_data)
        {
        }
    }
    class CheckoutSessionCompleted implements \ProfilePress\Core\Membership\PaymentMethods\WebhookHandlerInterface
    {
        public function handle($event_data)
        {
        }
    }
    class ChargeRefunded implements \ProfilePress\Core\Membership\PaymentMethods\WebhookHandlerInterface
    {
        public function handle($event_data)
        {
        }
    }
    class CheckoutSessionAsyncPaymentSucceeded implements \ProfilePress\Core\Membership\PaymentMethods\WebhookHandlerInterface
    {
        public function handle($event_data)
        {
        }
    }
    class CustomerSubscriptionCreated implements \ProfilePress\Core\Membership\PaymentMethods\WebhookHandlerInterface
    {
        public function handle($event_data)
        {
        }
    }
    class InvoicePaymentSucceeded implements \ProfilePress\Core\Membership\PaymentMethods\WebhookHandlerInterface
    {
        public function handle($event_data)
        {
        }
        /**
         * @param OrderEntity $order
         * @param string $stripe_customer_id
         *
         * @return void
         */
        public function set_customer_default_payment_method($order, $stripe_customer_id)
        {
        }
    }
    class CheckoutSessionAsyncPaymentFailed implements \ProfilePress\Core\Membership\PaymentMethods\WebhookHandlerInterface
    {
        public function handle($event_data)
        {
        }
    }
    class PaymentIntentSucceeded implements \ProfilePress\Core\Membership\PaymentMethods\WebhookHandlerInterface
    {
        public function handle($event_data)
        {
        }
    }
    class CustomerSubscriptionUpdated implements \ProfilePress\Core\Membership\PaymentMethods\WebhookHandlerInterface
    {
        public function handle($event_data)
        {
        }
    }
}
namespace ProfilePress\Core\Membership\PaymentMethods\Stripe {
    class WebhookHelpers
    {
        public static function valid_events()
        {
        }
        public static function webhook_url()
        {
        }
        public static function add_update_endpoint()
        {
        }
        /**
         * Determines if the current payment mode has an up-to-date webhook endpoint.
         *
         * @return mixed|bool
         */
        public static function exists()
        {
        }
        /**
         * Determines if a Stripe webhook endpoint is still valid.
         *
         * @param mixed $endpoint
         *
         * @return bool True if the webhook does not need to be updated.
         *
         */
        public static function is_valid($endpoint)
        {
        }
        /**
         * Retrieves a Stripe webhook endpoint.
         *
         * @param string $endpoint_id Stripe endpoint ID.
         *
         * @return array
         *
         * @throws \Exception
         */
        public static function get($endpoint_id)
        {
        }
        /**
         * Creates a Stripe webhook endpoint with the current plugin and site settings.
         *
         * @return false|mixed
         *
         * @throws \Exception
         */
        public static function create()
        {
        }
        /**
         * Creates a Stripe webhook endpoint with the current plugin and site settings.
         *
         * @param $endpoint_id
         * @param bool $without_persist
         *
         * @return void
         * @throws \Exception
         */
        public static function delete($endpoint_id, $without_persist = false)
        {
        }
        /**
         * Updates a Stripe webhook endpoint with the current site and plugin settings.
         *
         * @param $endpoint
         *
         * @return array
         * @throws \Exception
         */
        public static function update($endpoint)
        {
        }
        /**
         * Returns a list of Stripe webhook events that are supported by the plugin.
         *
         * @return array
         *
         */
        public static function get_event_whitelist()
        {
        }
    }
    class Helpers
    {
        public static function check_keys_exist()
        {
        }
        public static function get_publishable_key()
        {
        }
        public static function get_secret_key()
        {
        }
        public static function get_webhook_secret()
        {
        }
        public static function get_account_user_id()
        {
        }
        public static function get_connect_url($redirect_url = '')
        {
        }
        public static function get_disconnect_url($admin_url)
        {
        }
        public static function get_connect_button($redirect_url = '')
        {
        }
        public static function get_account_information($redirect_url)
        {
        }
    }
    class Stripe extends \ProfilePress\Core\Membership\PaymentMethods\AbstractPaymentMethod
    {
        public function __construct()
        {
        }
        public function has_fields()
        {
        }
        protected function is_offsite_checkout_style()
        {
        }
        protected function is_billing_fields_removed()
        {
        }
        public function output_connection_error()
        {
        }
        public function save_stripe_connect()
        {
        }
        public function maybe_update_webhook()
        {
        }
        public function disconnect_stripe_account()
        {
        }
        public function admin_connection_status_block()
        {
        }
        public function admin_settings()
        {
        }
        /**
         * @return bool|WP_Error
         */
        public function validate_fields()
        {
        }
        /**
         * Disable billing validation.
         *
         * @param $val
         *
         * @return bool
         */
        public function should_validate_billing_details($val)
        {
        }
        public function get_statement_descriptor()
        {
        }
        public function link_transaction_id($transaction_id, $order)
        {
        }
        public function link_profile_id($profile_id, $subscription)
        {
        }
        /**
         * Determines if the subscription can be cancelled
         *
         * @param $ret
         *
         * @param SubscriptionEntity $subscription
         *
         * @return      bool
         */
        public function can_cancel($ret, $subscription)
        {
        }
        /**
         * Cancels a subscription immediately.
         *
         * @param SubscriptionEntity $subscription
         *
         * @return bool
         */
        public function cancel_immediately($subscription)
        {
        }
        /**
         * Cancels a subscription at period end, unless the status of the subscription is failing. If failing, cancel immediately.
         *
         * @param SubscriptionEntity $subscription
         *
         * @return bool
         */
        public function cancel($subscription)
        {
        }
        public function enqueue_frontend_assets()
        {
        }
        protected function billing_address_form()
        {
        }
        public function credit_card_form()
        {
        }
        /**
         * @param OrderEntity $order
         * @param SubscriptionEntity $subscription
         *
         * @return array
         */
        public function get_order_metadata($order, $subscription)
        {
        }
        /**
         * @param $response
         * @param CartEntity $cart_vars
         *
         * @return void
         */
        public function filter_update_order_review_response($response, $cart_vars)
        {
        }
        /**
         * @param array $actions
         * @param SubscriptionEntity $sub
         * @param AbstractPaymentMethod|false $payment_method
         *
         * @return mixed
         */
        public function manage_subscription_button($actions, $sub, $payment_method)
        {
        }
        /**
         * @param string $action
         * @param SubscriptionEntity $sub
         *
         * @return void
         */
        public function handle_manage_subscription_action($action, $sub)
        {
        }
        /**
         * @param OrderEntity $order
         * @param CustomerEntity $customer
         * @param SubscriptionEntity $subscription
         * @param PlanEntity $plan
         *
         * @return CheckoutResponse
         */
        public function process_offsite_payment($order, $customer, $subscription, $plan)
        {
        }
        /**
         * @param $customer_id
         * @param $checkout_metadata
         *
         * @return array
         *
         * @throws \Exception
         */
        public function create_setup_intent($customer_id, $checkout_metadata)
        {
        }
        public function process_payment($order_id, $subscription_id, $customer_id)
        {
        }
        public function process_refund($order_id, $amount = null, $reason = '')
        {
        }
        public function process_webhook()
        {
        }
    }
}
namespace ProfilePress\Core\Membership\DigitalProducts {
    class Init
    {
        public function __construct()
        {
        }
        public static function get_instance()
        {
        }
    }
    class DownloadHandler
    {
        public function __construct()
        {
        }
        public function process_download()
        {
        }
        public static function get_instance()
        {
        }
    }
    class UploadHandler
    {
        public function __construct()
        {
        }
        /**
         * Stop WordPress from creating different image sizes.
         *
         * @param $result
         * @param $path
         *
         * @return bool
         */
        public function file_is_displayable_image($result, $path)
        {
        }
        public function allowed_mime_types($existing_mimes)
        {
        }
        public function create_protection_files($force = false, $method = false)
        {
        }
        /**
         * Change upload dir for downloadable files.
         *
         * @param array $pathdata Array of paths.
         *
         * @return array
         */
        public function upload_dir($pathdata)
        {
        }
        /**
         * Change filename for WooCommerce uploads and prepend unique chars for security.
         *
         * @param string $full_filename Original filename.
         * @param string $ext Extension of file.
         * @param string $dir Directory path.
         *
         * @return string New filename with unique hash.
         */
        public function update_filename($full_filename, $ext, $dir)
        {
        }
        /**
         * Change filename to append random text.
         *
         * @param string $full_filename Original filename with extension.
         * @param string $ext Extension.
         *
         * @return string Modified filename.
         */
        public function unique_filename($full_filename, $ext)
        {
        }
        public function get_upload_dir()
        {
        }
        public function scan_folders($path = '', $return = array())
        {
        }
        /**
         * Check if uploads directory is protected.
         *
         * @return bool
         */
        protected function is_uploads_directory_protected()
        {
        }
        /**
         * Notice about uploads directory begin unprotected.
         */
        public function admin_notice()
        {
        }
        public static function get_instance()
        {
        }
    }
    class DownloadService
    {
        public function get_download_file_url($order_key, $file_index, $expiry = 0)
        {
        }
        /**
         * Generates a token for a given URL.
         *
         * @param string $url URL to generate a token for.
         *
         * @return string Token for the URL.
         */
        function get_download_token($url = '')
        {
        }
        public function get_url_token_parameters()
        {
        }
        /**
         * Generate a token for a URL and match it against the existing token to make
         * sure the URL hasn't been tampered with.
         *
         * @param string $url URL to test.
         *
         * @return bool
         */
        function validate_url_token($url = '')
        {
        }
        /**
         * @param $args
         *
         * @return bool|int
         */
        public function add_download_log($args)
        {
        }
        public function get_download_file_name($plan_id, $file_url)
        {
        }
        public function get_download_log_count($order_id = 0)
        {
        }
        public function get_download_log($limit = 0, $current_page = 1, $order_id = 0)
        {
        }
        /**
         * @param $order_id
         * @param $plan_id
         * @param $file_url
         *
         * @return false|int
         */
        public function get_downloads_count($order_id, $plan_id, $file_url)
        {
        }
        /**
         * Checks if a file is at its download limit
         *
         * This limit refers to the maximum number of times files connected to a plan can be downloaded.
         *
         * @param int $plan_id
         * @param int $order_id Order ID.
         * @param string $file_url
         *
         * @return bool
         *
         */
        public function is_file_at_download_limit($plan_id, $order_id, $file_url)
        {
        }
        /**
         * @param $order_id
         * @param $plan_id
         * @param $file_url
         *
         * @return mixed|string
         */
        public function get_downloads_remaining($order_id, $plan_id, $file_url)
        {
        }
        /**
         * @return self
         */
        public static function init()
        {
        }
    }
}
namespace ProfilePress\Core\Membership\Controllers {
    abstract class BaseController
    {
        /**
         * @return static
         */
        public static function get_instance()
        {
        }
    }
    class SubscriptionPlanController extends \ProfilePress\Core\Membership\Controllers\BaseController
    {
        /**
         * @param int|PlanEntity $id_or_obj
         */
        public function activate_plan($id_or_obj)
        {
        }
        /**
         * @param int|PlanEntity $id_or_obj
         */
        public function deactivate_plan($id_or_obj)
        {
        }
        /**
         * @param int|PlanEntity $id_or_obj
         */
        public function delete_plan($id_or_obj)
        {
        }
        /**
         * @param int|PlanEntity $id_or_obj
         */
        public function duplicate_plan($id_or_obj)
        {
        }
    }
    class CheckoutResponse
    {
        public $is_success = false;
        public $redirect_url = '';
        public $gateway_response = '';
        public $error_message = '';
        public function set_is_success($val)
        {
        }
        public function set_redirect_url($val)
        {
        }
        public function set_gateway_response($val)
        {
        }
        public function set_error_message($val)
        {
        }
        public function get_generic_error_message()
        {
        }
    }
    trait CheckoutTrait
    {
        public function cleanup_posted_data($POST)
        {
        }
        public function alert_message($messages, $type = 'error')
        {
        }
        public function should_skip_validation($field_key, $field_settings)
        {
        }
        public function validate_required_field($field_key, $field_type)
        {
        }
        /**
         * @param int $customer_id
         * @param CartEntity $cart_vars
         *
         * @return int|\WP_Error
         */
        public function create_subscription($customer_id, $cart_vars)
        {
        }
        /**
         * @param $customer_id
         * @param CartEntity $cart_vars
         *
         * @return int|\WP_Error
         */
        public function create_order($customer_id, $cart_vars)
        {
        }
        /**
         * @return int|\WP_Error
         */
        public function register_update_user()
        {
        }
        public function save_eu_vat_details($payment_method_id, $order_id)
        {
        }
    }
    class CheckoutController extends \ProfilePress\Core\Membership\Controllers\BaseController
    {
        use \ProfilePress\Core\Membership\Controllers\CheckoutTrait;
        public function __construct()
        {
        }
        public function contextual_state_field()
        {
        }
        public function validate_checkout_coupon()
        {
        }
        public function process_checkout_login()
        {
        }
        public function apply_discount()
        {
        }
        public function remove_discount()
        {
        }
        public function process_checkout()
        {
        }
        public function update_order_review()
        {
        }
        public function redirect_to_referrer_after_checkout()
        {
        }
    }
    class FrontendController extends \ProfilePress\Core\Membership\Controllers\BaseController
    {
        public function __construct()
        {
        }
        public function change_plan_shim()
        {
        }
        /**
         * Prevent caching on dynamic pages.
         */
        public function prevent_caching()
        {
        }
        /**
         * Set additional nocache headers.
         *
         * @param array $headers {
         *     Header names and field values.
         *
         * @type string $Expires Expires header.
         * @type string $Cache -Control Cache-Control header.
         * }
         * @return array
         * @see wp_get_nocache_headers()
         *
         */
        public function additional_nocache_headers($headers)
        {
        }
        /**
         * Sets a browser cookie that tells WP Engine to exclude a page from server caching.
         *
         * @see https://wpengine.com/support/cache/#Default_Cache_Exclusions
         * @see https://wpengine.com/support/determining-wp-engine-environment/
         *
         * @return void
         */
        public function exclude_page_from_wpe_server_cache()
        {
        }
        /**
         * Sets a browser cookie that tells Pantheon to exclude a page from server caching.
         *
         * @see https://docs.pantheon.io/cookies#disable-caching-for-specific-pages
         *
         * @return void
         */
        public function exclude_page_from_pantheon_server_cache()
        {
        }
    }
    class CheckoutSessionData
    {
        const COUPON_CODE = 'ppress_checkout_coupon_code';
        const TAX_RATE = 'ppress_checkout_tax_rate';
        const EU_VAT_NUMBER = 'ppress_checkout_eu_vat_number';
        const ORDER_TYPE = 'ppress_checkout_order_type';
        /**
         * @param $plan_id
         *
         * @return false|string
         */
        public static function get_order_type($plan_id)
        {
        }
        /**
         * @param $plan_id
         *
         * @return false|string
         */
        public static function get_coupon_code($plan_id)
        {
        }
        public static function get_tax_rate($plan_id)
        {
        }
        public static function get_tax_country($plan_id)
        {
        }
        public static function get_tax_state($plan_id)
        {
        }
        /**
         * @param $plan_id
         * @param $vat_number
         *
         * @return mixed
         */
        public static function get_eu_vat_number_details($plan_id, $vat_number)
        {
        }
    }
}
namespace ProfilePress\Core\Membership\Services {
    class OrderService
    {
        const ORDER_NOTE_META_KEY = 'order_notes';
        /**
         * @param CartEntity $cart_vars
         *
         * @return bool
         */
        public function is_free_checkout($cart_vars)
        {
        }
        /**
         * Should payment form be displayed when initial payment of a subscription zero amount?
         *
         * @param CartEntity $cart_vars
         *
         * @return bool
         */
        public function is_disable_payment_for_zero_initial_subscription_payment($cart_vars)
        {
        }
        public function customer_has_trialled($plan_id = '')
        {
        }
        /**
         * Handle calculating a percentage/fraction (proration) we should charge the
         * user for based on the current day of the month before their next bill cycle.
         * To use yourself, implement a getSubscription method which returns an object
         * containing current_period_start and current_period_end DateTime objects.
         *
         * @param string|\Datetime $currentPeriodStart
         * @param string|\Datetime $currentPeriodEnd
         *
         * @return  float
         */
        protected function prorateUpcomingBillingCycle($currentPeriodStart, $currentPeriodEnd)
        {
        }
        /**
         * @param int $from_sub_id Subscription ID being upgraded from
         * @param int $to_plan_id Plan ID being upgraded to
         * @param string $old_price
         * @param string $new_price
         *
         * @return mixed|string|null
         *
         * @see https://gist.github.com/cballou/774c5a15f9771314f0d1
         *
         */
        protected function get_time_based_pro_rated_upgrade_cost($from_sub_id, $to_plan_id, $old_price, $new_price)
        {
        }
        /**
         * Calculate the prorated cost to upgrade a subscription
         *
         * Calculations are based on the time remaining on a subscription instead of a price comparison.
         *
         * @param int $from_sub_id
         * @param int $to_plan_id
         *
         * @return string The prorated cost to upgrade the subscription
         */
        public function get_pro_rated_upgrade_cost($from_sub_id, $to_plan_id)
        {
        }
        /**
         * @param $args
         *
         * @return CartEntity
         */
        public function checkout_order_calculation($args)
        {
        }
        public function get_customer_orders_url($customer_id, $order_status = false)
        {
        }
        /**
         * Generate an order key
         *
         * @return string The order key.
         */
        public function generate_order_key()
        {
        }
        /**
         * @param $order_id
         *
         * @return false|int
         */
        public function delete_order($order_id)
        {
        }
        /**
         * @param $order_id
         *
         * @return array
         */
        public function get_order_notes($order_id)
        {
        }
        public function add_order_note($order_id, $note)
        {
        }
        /**
         * @param int $meta_id Note meta ID
         *
         * @return bool
         */
        public function delete_order_note_by_id($meta_id)
        {
        }
        public function delete_all_order_notes($order_id)
        {
        }
        /**
         * @param array $args
         * @param SubscriptionEntity $subscription
         *
         * @return false|int
         */
        public function record_subscription_renewal_order($args, $subscription)
        {
        }
        /**
         * @param $order_id
         *
         * @return bool
         */
        public function process_order_refund($order_id)
        {
        }
        public function frontend_view_order_url($order_key)
        {
        }
        public function admin_view_order_url($order_id_or_key)
        {
        }
        /**
         * @return self
         */
        public static function init()
        {
        }
    }
    class SubscriptionService
    {
        public function get_plan_expiration_datetime($plan_id)
        {
        }
        /**
         * @param $sub_id
         *
         * @return false|int
         */
        public function delete_subscription($sub_id)
        {
        }
        public function frontend_view_sub_url($subscription_id)
        {
        }
        public function admin_view_sub_url($subscription_id)
        {
        }
        /**
         * @return self
         */
        public static function init()
        {
        }
    }
    class TaxService
    {
        protected $tax_options = [];
        public function __construct()
        {
        }
        public function is_tax_enabled()
        {
        }
        public function get_tax_label($country)
        {
        }
        public function is_eu_vat_enabled()
        {
        }
        public function is_price_inclusive_tax()
        {
        }
        public function is_vat_number_validation_active()
        {
        }
        public function calculate_tax_based_on_setting()
        {
        }
        /**
         * @return string
         */
        public function eu_vat_same_country_rule_setting()
        {
        }
        public function get_tax_rates()
        {
        }
        public function get_eu_countries()
        {
        }
        public function is_eu_countries($country)
        {
        }
        public function get_eu_vat_rates()
        {
        }
        public function get_fallback_tax_rate()
        {
        }
        public function get_vat_number_field_label($default = '')
        {
        }
        /**
         * @param $country
         * @param $state
         *
         * @return float|int|string
         */
        public function get_country_tax_rate($country, $state = '')
        {
        }
        public function is_reverse_charged($order_id)
        {
        }
        /**
         * @return self
         */
        public static function init()
        {
        }
    }
    class CouponService
    {
        public function get_coupon_percentage_fee($percentage, $subtotal)
        {
        }
        /**
         * Checks whether a customer has used a particular discount code.
         *
         * This is used to prevent users from spamming discount codes.
         *
         * @param int $customer_id
         * @param string $code The discount code to check against the customer ID.
         *
         * @return bool
         */
        function customer_has_used_discount($customer_id, $code)
        {
        }
        /**
         * Returns a formatted discount amount with a '%' sign appended (percentage-based) or with the
         * currency sign added to the amount (flat discount rate).
         *
         * @param string $amount Discount amount.
         * @param string $type Discount amount - either 'percentage' or 'flat'.
         *
         * @return string
         */
        function format_discount_display($amount, $type)
        {
        }
        public function validate_discount($code, $plan_id = 0, $order_type = \ProfilePress\Core\Membership\Models\Order\OrderType::NEW_ORDER)
        {
        }
        /**
         * @return self
         */
        public static function init()
        {
        }
    }
    class Calculator
    {
        /** @var BigDecimal */
        protected $result;
        protected $scale;
        protected $roundingMode;
        public function __construct($leftOperand, $scale = 8, $roundingMode = \ProfilePressVendor\Brick\Math\RoundingMode::HALF_UP)
        {
        }
        public function plus($rightOperand)
        {
        }
        public function minus($rightOperand)
        {
        }
        public function multipliedBy($rightOperand)
        {
        }
        public function dividedBy($rightOperand)
        {
        }
        public function isNegativeOrZero()
        {
        }
        public function isNegative()
        {
        }
        public function isEqualTo($val)
        {
        }
        public function isZero()
        {
        }
        public function isGreaterThan($val)
        {
        }
        public function isLessThan($val)
        {
        }
        public function isGreaterThanZero()
        {
        }
        public function toScale($scale, $roundingMode = null)
        {
        }
        /**
         * @return BigDecimal
         */
        public function result()
        {
        }
        public function val()
        {
        }
        /**
         * @return self
         */
        public static function init($leftOperand)
        {
        }
    }
}
namespace ProfilePress\Core\Membership\Services\EUVATChecker {
    class EuVatApi
    {
        const API_URL = 'https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';
        /**
         * Check a VAT number against the supplied country code.
         *
         * @param string $vat_number The VAT number to check.
         * @param string $country_code The country code.
         *
         * @return  EUVATAPIResponse
         */
        public static function check_vat($vat_number, $country_code)
        {
        }
    }
    class EuVatApiResponse
    {
        const NO_VAT_NUMBER = 1;
        const NO_COUNTRY_CODE = 2;
        const INVALID_VAT_NUMBER = 3;
        const INVALID_COUNTRY_CODE = 4;
        const VAT_NUMBER_INVALID_FOR_COUNTRY = 5;
        const INVALID_INPUT = 6;
        const API_ERROR = 7;
        public $vat_number = '';
        public $country_code = '';
        public $valid = false;
        public $name = '';
        public $address = '';
        /**
         * The error code if there was an error.
         *
         * @var string
         */
        public $error;
        /**
         * Constructs a new result object for the supplied VAT number and country code.
         *
         * @param string $vat_number The VAT number the result applies to.
         * @param string $country_code The two letter country code.
         */
        public function __construct($vat_number, $country_code)
        {
        }
        /**
         * Is the VAT number valid?
         */
        public function is_valid()
        {
        }
        public function get_error_message()
        {
        }
        public function __toString()
        {
        }
    }
}
namespace ProfilePress\Core\Membership\Services {
    class CustomerService
    {
        /**
         * @param $customer_id
         *
         * @return false|int
         */
        public function delete_customer($customer_id)
        {
        }
        /**
         * @return self
         */
        public static function init()
        {
        }
    }
}
namespace ProfilePress\Core\Membership {
    class StatSync
    {
        public function __construct()
        {
        }
        public function core_actions($user_id = false, $customer_id = false)
        {
        }
        /**
         * @return self
         */
        public static function init()
        {
        }
    }
}
namespace ProfilePress\Core\AdminBarDashboardAccess {
    class Init
    {
        public function __construct()
        {
        }
        public function db_options()
        {
        }
        public function menu_tab($tabs)
        {
        }
        public function change_page_title($title)
        {
        }
        public function settings_page_callback()
        {
        }
        public static function save_options()
        {
        }
        /**
         * Helper function to recursively sanitize POSTed data.
         *
         * @param $data
         *
         * @return string|array
         */
        public static function sanitize_data($data)
        {
        }
        /**
         * Callback to disable admin bar.
         *
         * @param $show_admin_bar
         *
         * @return bool
         */
        public function admin_bar_control($show_admin_bar)
        {
        }
        /**
         * Disable dashboard access.
         *
         * @return bool|void
         */
        public function dashboard_access_control()
        {
        }
        /**
         * Call to disable dashboard access.
         */
        public function disable_dashboard_access()
        {
        }
        public static function get_instance()
        {
        }
    }
}
namespace {
    /** Plugin DB settings data */
    function ppress_db_data()
    {
    }
    function ppress_update_settings($key, $value)
    {
    }
    /**
     * Array of WooCommerce billing fields.
     *
     * @return array
     */
    function ppress_woocommerce_billing_fields()
    {
    }
    /**
     * Array of WooCommerce billing fields.
     *
     * @return array
     */
    function ppress_woocommerce_shipping_fields()
    {
    }
    /**
     * Array of WooCommerce billing and shipping fields.
     *
     * @return array
     */
    function ppress_woocommerce_billing_shipping_fields()
    {
    }
    /**
     * @param string $key
     * @param bool $default
     * @param bool $is_empty set to true to return the default if value is empty
     *
     * @return mixed
     */
    function ppress_settings_by_key($key = '', $default = \false, $is_empty = \false)
    {
    }
    function ppress_get_setting($key = '', $default = \false, $is_empty = \false)
    {
    }
    /**
     * Send email.
     *
     * @param string|array $to
     * @param $subject
     * @param $message
     *
     * @return bool|WP_Error
     */
    function ppress_send_email($to, $subject, $message)
    {
    }
    function ppress_welcome_msg_content_default()
    {
    }
    function ppress_new_user_admin_notification_message_default()
    {
    }
    function ppress_passwordless_login_message_default()
    {
    }
    function ppress_user_moderation_msg_default($type)
    {
    }
    function ppress_password_reset_content_default()
    {
    }
    /**
     * Return the url to redirect to after login authentication
     *
     * @return bool|string
     */
    function ppress_login_redirect()
    {
    }
    /**
     * Return the url to redirect to after successful reset / change of password.
     *
     * @return bool|string
     */
    function ppress_password_reset_redirect()
    {
    }
    /**
     * Return the url to frontend myprofile page.
     *
     * @return bool|string
     */
    function ppress_profile_url()
    {
    }
    /**
     * @param $username_or_id
     *
     * @return string
     */
    function ppress_get_frontend_profile_url($username_or_id)
    {
    }
    /**
     * Return ProfilePress edit profile page URL or WP default profile URL as fallback
     *
     * @return bool|string
     */
    function ppress_edit_profile_url()
    {
    }
    function ppress_my_account_url()
    {
    }
    /**
     * Return ProfilePress password reset url.
     *
     * @return string
     */
    function ppress_password_reset_url()
    {
    }
    /**
     * Get ProfilePress login page URL or WP default login url if it isn't set.
     *
     * @param $redirect
     *
     * @return string
     */
    function ppress_login_url($redirect = '')
    {
    }
    /**
     * Get ProfilePress login page URL or WP default login url if it isn't set.
     */
    function ppress_registration_url()
    {
    }
    /**
     * Return the URL of the currently view page.
     *
     * @return string
     */
    function ppress_get_current_url()
    {
    }
    /**
     * Return currently viewed page url without query string.
     *
     * @return string
     */
    function ppress_get_current_url_raw()
    {
    }
    /**
     * Return currently viewed page url with query string.
     *
     * @return string
     */
    function ppress_get_current_url_query_string()
    {
    }
    /**
     * @return string blog URL without scheme
     */
    function ppress_site_url_without_scheme()
    {
    }
    /**
     * Append an option to a select dropdown
     *
     * @param string $option option to add
     * @param string $select select dropdown
     *
     * @return string
     */
    function ppress_append_option_to_select($option, $select)
    {
    }
    /**
     * Blog name or domain name if name doesn't exist
     *
     * @return string
     */
    function ppress_site_title()
    {
    }
    /**
     * Check if an admin settings page is ProfilePress'
     *
     * @return bool
     */
    function ppress_is_admin_page()
    {
    }
    /**
     * Return admin email
     *
     * @return string
     */
    function ppress_admin_email()
    {
    }
    /**
     * Checks whether the given user ID exists.
     *
     * @param string $user_id ID of user
     *
     * @return null|int The user's ID on success, and null on failure.
     */
    function ppress_user_id_exist($user_id)
    {
    }
    /**
     * Get a user's username by their ID
     *
     * @param int $user_id
     *
     * @return bool|string
     */
    function ppress_get_username_by_id($user_id)
    {
    }
    /**
     * front-end profile slug.
     *
     * @return string
     */
    function ppress_get_profile_slug()
    {
    }
    /**
     * Filter form field attributes for unofficial attributes.
     *
     * @param array $atts supplied shortcode attributes
     *
     * @return mixed
     *
     */
    function ppress_other_field_atts($atts)
    {
    }
    /**
     * Create an index.php file to prevent directory browsing.
     *
     * @param string $location folder path to create the file in.
     */
    function ppress_create_index_file($location)
    {
    }
    /**
     * Get front-end do password reset form url.
     *
     * @param string $user_login
     * @param string $key
     *
     * @return string
     */
    function ppress_get_do_password_reset_url($user_login, $key)
    {
    }
    /**
     * Return true if a field key exist/is multi selectable dropdown.
     *
     * @param $field_key
     *
     * @return bool
     */
    function ppress_is_select_field_multi_selectable($field_key)
    {
    }
    /**
     * Return username/username of a user using the user's nicename to do the DB search.
     *
     * @param string $slug
     *
     * @return bool|null|string
     */
    function ppress_is_slug_nice_name($slug)
    {
    }
    /**
     * Return array of editable roles.
     *
     * @param $remove_admin
     *
     * @return mixed
     */
    function ppress_get_editable_roles($remove_admin = \true)
    {
    }
    function ppress_wp_roles_key_value($remove_admin = \true)
    {
    }
    function ppress_wp_new_user_notification($user_id, $deprecated = \null, $notify = '')
    {
    }
    /**
     * Does registration form has username requirement disabled?
     *
     * @param int $form_id
     * @param bool $is_melange
     *
     * @return bool
     */
    function ppress_is_signup_form_username_disabled($form_id, $is_melange = \false)
    {
    }
    /**
     * Generate url to reset user's password.
     *
     * @param string $user_login
     *
     * @return string
     */
    function ppress_generate_password_reset_url($user_login)
    {
    }
    function ppress_nonce_action_string()
    {
    }
    /**
     * Return array of countries.
     *
     * @param string $country_code
     *
     * @return mixed|string
     */
    function ppress_array_of_world_countries($country_code = '')
    {
    }
    /**
     * @param $country
     *
     * @return mixed
     */
    function ppress_array_of_world_states($country = '')
    {
    }
    function ppress_get_country_title($country)
    {
    }
    function ppress_get_country_state_title($state, $country)
    {
    }
    function ppress_create_nonce()
    {
    }
    function ppress_nonce_field()
    {
    }
    function ppress_verify_nonce()
    {
    }
    function ppress_verify_ajax_nonce()
    {
    }
    /**
     * Returns a more compact md5 hashing.
     *
     * @param $string
     *
     * @return false|string
     */
    function ppress_md5($string)
    {
    }
    /**
     * Generate unique ID
     *
     * @param int $length
     *
     * @return string
     */
    function ppress_generate_unique_id($length = 10)
    {
    }
    function ppress_minify_css($buffer)
    {
    }
    function ppress_minify_js($code)
    {
    }
    function ppress_minify_html($html)
    {
    }
    function ppress_get_ip_address()
    {
    }
    /**
     * Admin email address to receive admin notification.
     *
     * @return mixed
     */
    function ppress_get_admin_notification_emails()
    {
    }
    /**
     * @return WP_Filesystem_Base|false
     */
    function ppress_file_system()
    {
    }
    function ppress_get_file($file)
    {
    }
    function ppress_get_error_log($type = 'debug')
    {
    }
    function ppress_log_error($message, $type = 'debug')
    {
    }
    function ppress_clear_error_log($type = 'debug')
    {
    }
    function ppressPOST_var($key, $default = \false, $empty = \false, $bucket = \false)
    {
    }
    function ppressGET_var($key, $default = \false, $empty = \false)
    {
    }
    function ppress_var($bucket, $key, $default = \false, $empty = \false)
    {
    }
    function ppress_var_obj($bucket, $key, $default = \false, $empty = \false)
    {
    }
    /**
     * Normalize unamed shortcode
     *
     * @param array $atts
     *
     * @return mixed
     */
    function ppress_normalize_attributes($atts)
    {
    }
    function ppress_dnd_field_key_description()
    {
    }
    function ppress_reserved_field_keys()
    {
    }
    function ppress_is_boolean($maybe_bool)
    {
    }
    function ppress_filter_empty_array($values)
    {
    }
    /**
     * Check if HTTP status code is successful.
     *
     * @param int $code
     *
     * @return bool
     */
    function ppress_is_http_code_success($code)
    {
    }
    /**
     * Converts date/time which should be in UTC to timestamp.
     *
     * strtotime uses the default timezone set in PHP which may or may not be UTC.
     *
     * @param $time
     *
     * @return false|int
     */
    function ppress_strtotime_utc($time)
    {
    }
    function ppress_array_flatten($array)
    {
    }
    /**
     * Sanitizes a string key.
     *
     * Keys are used as internal identifiers. Lowercase alphanumeric characters and underscores are allowed.
     *
     * @param string $key String key
     *
     * @return string Sanitized key
     */
    function ppress_sanitize_key($key)
    {
    }
    function ppress_woocommerce_field_transform($cf_key, $cf_value)
    {
    }
    function ppress_custom_fields_key_value_pair($remove_default = \false)
    {
    }
    function ppress_standard_fields_key_value_pair($remove_default = \false)
    {
    }
    function ppress_standard_custom_fields_key_value_pair($remove_default = \false)
    {
    }
    /**
     * @param int|bool $user_id
     *
     * @return bool
     */
    function ppress_user_has_cover_image($user_id = \false)
    {
    }
    /**
     * @param int|bool $user_id
     *
     * @return string|bool
     */
    function ppress_get_cover_image_url($user_id = \false)
    {
    }
    function ppress_is_my_own_profile()
    {
    }
    function ppress_is_my_account_page()
    {
    }
    function ppress_social_network_fields()
    {
    }
    function ppress_social_login_networks()
    {
    }
    function ppress_mb_function($function_names, $args)
    {
    }
    function ppress_recursive_trim($item)
    {
    }
    function ppress_check_type_and_ext($file, $accepted_mime_types = [], $accepted_file_ext = [])
    {
    }
    function ppress_decode_html_strip_tags($val)
    {
    }
    function ppress_content_http_redirect($myURL)
    {
    }
    function ppress_do_admin_redirect($url)
    {
    }
    function ppress_is_json($str)
    {
    }
    function ppress_clean($var, $callback = 'sanitize_textarea_field')
    {
    }
    /**
     * @param $s
     *
     * @return bool
     * @see https://stackoverflow.com/a/23810738/2648410
     */
    function ppress_is_base64($s)
    {
    }
    /**
     * @param int $plan_id Plan ID or Subscription ID if change plan URL
     * @param bool $is_change_plan set to true to return checkout url to change plan
     *
     * @return false|string
     */
    function ppress_plan_checkout_url($plan_id, $is_change_plan = \false)
    {
    }
    /**
     * Generate unique ID for each optin form.
     *
     * @param int $length
     *
     * @return string
     */
    function ppress_generateUniqueId($length = 10)
    {
    }
    function ppress_render_view($template, $vars = [], $parentDir = '')
    {
    }
    function ppress_post_content_has_shortcode($tag = '', $post = \null)
    {
    }
    function ppress_maybe_define_constant($name, $value)
    {
    }
    function ppress_upgrade_urls_affilify($url)
    {
    }
    function ppress_cache_transform($cache_key, $callback)
    {
    }
    function ppress_form_has_field($form_id, $form_type, $field_shortcode_tag)
    {
    }
    function ppress_custom_profile_field_search_replace($message, $user)
    {
    }
    /**
     * @param $plan_id
     *
     * @return PlanEntity
     */
    function ppress_get_plan($plan_id)
    {
    }
    /**
     * @param $payment_method_id
     *
     * @return false|AbstractPaymentMethod
     */
    function ppress_get_payment_method($payment_method_id)
    {
    }
    /**
     * Check if website has an active membership plan.
     *
     * @return bool
     */
    function ppress_is_any_active_plan()
    {
    }
    /**
     * Check if website has an active payment method.
     *
     * @return bool
     */
    function ppress_is_any_enabled_payment_method()
    {
    }
    /**
     * Check if website has an active coupon.
     *
     * @return bool
     */
    function ppress_is_any_active_coupon()
    {
    }
    function ppress_get_currency()
    {
    }
    /**
     * Get full list of currency codes.
     *
     * Currency symbols and names should follow the Unicode CLDR recommendation (http://cldr.unicode.org/translation/currency-names)
     *
     * @return array
     */
    function ppress_get_currencies()
    {
    }
    /**
     * Get all available Currency symbols.
     *
     * Currency symbols and names should follow the Unicode CLDR recommendation (http://cldr.unicode.org/translation/currency-names)
     *
     * @return array
     */
    function ppress_get_currency_symbols()
    {
    }
    /**
     * Get Currency symbol.
     *
     * Currency symbols and names should follow the Unicode CLDR recommendation (http://cldr.unicode.org/translation/currency-names)
     *
     * @param string $currency Currency. (default: '').
     *
     * @return string
     */
    function ppress_get_currency_symbol($currency = '')
    {
    }
    /**
     * Get the name of a currency
     *
     * @param string $code The currency code
     *
     * @return string The currency's name
     */
    function ppress_get_currency_name($code = '')
    {
    }
    /**
     * Accepts an amount (ideally from the database, unmodified) and formats it
     * for display. The amount itself is formatted and the currency prefix/suffix
     * is applied and positioned.
     *
     * @param string $amount
     * @param string $currency
     *
     * @return string
     *
     */
    function ppress_display_amount($amount, $currency = '')
    {
    }
    /**
     *
     * @param $amount
     *
     * @return string
     */
    function ppress_sanitize_amount($amount)
    {
    }
    /**
     * Converts price, fee or amount in cent to decimal
     *
     * @param $amount
     *
     * @return string
     */
    function ppress_cent_to_decimal($amount)
    {
    }
    /**
     * Converts price, fee or amount in decimal to cent
     *
     * @param $amount
     *
     * @return string
     */
    function ppress_decimal_to_cent($amount)
    {
    }
    /**
     * Force https for urls.
     *
     * @param mixed $content
     *
     * @return string
     */
    function ppress_force_https_url($content)
    {
    }
    function ppress_is_test_mode()
    {
    }
    function ppress_get_payment_mode()
    {
    }
    /**
     * Converts a date/time to UTC
     */
    function ppress_local_datetime_to_utc($date, $format = 'Y-m-d H:i:s')
    {
    }
    /**
     * Formats UTC datetime according to WordPress date/time format and using WordPress site timezone.
     *
     * Expects time/timestamp to be in UTC
     *
     * @param string $timestamp timestamp or datetime in UTC
     *
     * @param string $format
     *
     * @return string datetime in WP timezone
     */
    function ppress_format_date_time($timestamp, $format = '')
    {
    }
    /**
     * Formats UTC date according to WordPress date format and using WordPress site timezone.
     *
     * @param string $timestamp timestamp or datetime in UTC
     * @param string $format
     *
     * @return string date in WP timezone
     */
    function ppress_format_date($timestamp, $format = '')
    {
    }
    function ppress_update_payment_method_setting($key, $value)
    {
    }
    function ppress_get_payment_method_setting($key = '', $default = \false, $is_empty = \false)
    {
    }
    function ppress_get_file_downloads_setting($key = '', $default = \false, $is_empty = \false)
    {
    }
    /**
     * @return PPRESS_Session
     */
    function ppress_session()
    {
    }
    function ppress_is_checkout()
    {
    }
    function ppress_is_success_page()
    {
    }
    /**
     * @param $order_key
     * @param $payment_method
     *
     * @return string
     */
    function ppress_get_success_url($order_key = '', $payment_method = '')
    {
    }
    /**
     * @param $order_key
     *
     * @return string
     */
    function ppress_get_cancel_url($order_key = '')
    {
    }
    function ppress_business_name()
    {
    }
    function ppress_business_address($default = '')
    {
    }
    function ppress_business_city($default = '')
    {
    }
    function ppress_business_country($default = '')
    {
    }
    function ppress_business_state($default = '')
    {
    }
    function ppress_business_postal_code($default = '')
    {
    }
    function ppress_business_full_address()
    {
    }
    function ppress_business_tax_id($default = '')
    {
    }
    /**
     * Check if a user/customer has an active subscription to a membership plan.
     *
     * @param int $user_id
     * @param int $plan_id
     * @param bool $by_customer_id
     *
     * @return bool
     */
    function ppress_has_active_subscription($user_id, $plan_id, $by_customer_id = \false)
    {
    }
    /**
     * Checks whether function is disabled.
     *
     * @param string $function Name of the function.
     *
     * @return bool Whether or not function is disabled.
     */
    function ppress_is_func_disabled($function)
    {
    }
    /**
     * Ignore the time limit set by the server (likely from php.ini.)
     *
     * This is usually only necessary during upgrades and exports. If you need to
     * use this function directly, please be careful in doing so.
     *
     * The $time_limit parameter is filterable, but infinite values are not allowed
     * so any erroneous processes are able to terminate normally.
     *
     * @param boolean $ignore_user_abort Whether to call ignore_user_about( true )
     * @param int $time_limit How long to set the time limit to. Cannot be 0. Default 6 hours.
     */
    function ppress_set_time_limit($ignore_user_abort = \true, $time_limit = 21600)
    {
    }
    /**
     * @param $user_id
     *
     * @return int|WP_Error
     */
    function ppress_create_customer($user_id)
    {
    }
    /**
     * Subscribe a customer to a membership plan while creating the corresponding order and subscription entity.
     *
     * @param int $plan_id
     * @param int $customer_id
     * @param array $order_data
     * @param bool $send_receipt
     *
     * @return array|WP_Error
     */
    function ppress_subscribe_user_to_plan($plan_id, $customer_id, $order_data = [], $send_receipt = \false)
    {
    }
    function ppress_is_redirect_to_referrer_after_checkout()
    {
    }
}
namespace {
    \define('PROFILEPRESS_SYSTEM_FILE_PATH', __FILE__);
    \define('PPRESS_VERSION_NUMBER', '4.15.23');
}
