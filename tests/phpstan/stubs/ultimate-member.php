<?php

namespace {
    /**
     * Main UM Class
     *
     * @class UM
     * @version 2.0
     *
     * @method UM_bbPress_API bbPress_API()
     * @method UM_Followers_API Followers_API()
     * @method UM_Friends_API Friends_API()
     * @method UM_Instagram_API Instagram_API()
     * @method UM_Mailchimp Mailchimp()
     * @method UM_Messaging_API Messaging_API()
     * @method UM_myCRED myCRED()
     * @method UM_Notices Notices()
     * @method UM_Notifications_API Notifications_API()
     * @method UM_Online Online()
     * @method UM_Profile_Completeness_API Profile_Completeness_API()
     * @method UM_reCAPTCHA reCAPTCHA()
     * @method UM_Reviews Reviews()
     * @method UM_Activity_API Activity_API()
     * @method UM_Social_Login_API Social_Login_API()
     * @method UM_User_Tags User_Tags()
     * @method UM_Verified_Users_API Verified_Users_API()
     * @method UM_WooCommerce_API WooCommerce_API()
     * @method UM_Terms_Conditions Terms_Conditions()
     * @method UM_Private_Content Private_Content()
     * @method UM_User_Locations User_Locations()
     * @method UM_Photos_API Photos_API()
     * @method UM_Groups Groups()
     * @method UM_Frontend_Posting Frontend_Posting()
     * @method UM_Notes Notes()
     * @method UM_User_Bookmarks User_Bookmarks()
     * @method UM_Unsplash Unsplash()
     * @method UM_ForumWP ForumWP()
     * @method UM_Profile_Tabs Profile_Tabs()
     * @method UM_JobBoardWP JobBoardWP()
     * @method UM_Google_Authenticator Google_Authenticator()
     */
    final class UM extends UM_Functions
    {
        /**
         * @since 2.0
         *
         * @return um\core\Query
         */
        function query()
        {
        }
    }
    /**
     * Class UM_Functions
     */
    class UM_Functions
    {
    }
}
namespace um\core {
    /**
     * Class Member_Directory
     * @package um\core
     */
    class Member_Directory
    {
    }
    /**
     * Class Member_Directory_Meta
     * @package um\core
     */
    class Member_Directory_Meta extends Member_Directory
    {
        /**
         * @var array
         */
        public $joins = array();
        /**
         * @var array
         */
        public $where_clauses = array();
        /**
         * @var array
         */
        public $roles = array();
        /**
         * @var bool
         */
        public $general_meta_joined = false;
        /**
         * @var string
         */
        public $sql_order = '';
    }
    /**
     * Class Query
     * @package um\core
     */
    class Query
    {
        /**
         * Capture selected value
         *
         * @param string $key
         * @param string|null $array_key
         * @param mixed $fallback
         * @return int|mixed|null|string
         */
        function get_meta_value($key, $array_key = null, $fallback = false)
        {
        }
    }
}
namespace {
    /**
     * Function for calling UM methods and variables
     *
     * @since 2.0
     *
     * @return UM
     */
    function UM()
    {
    }
    /**
     * Default avatar URL
     *
     * @return string
     */
    function um_get_default_avatar_uri()
    {
    }
    /**
     * Gets the requested user
     *
     * @return int|false
     */
    function um_get_requested_user()
    {
    }
    /**
     * Check if we are on a UM Core Page or not
     *
     * Default um core pages slugs
     * 'user', 'login', 'register', 'members', 'logout', 'account', 'password-reset'
     *
     * @param string $page UM core page slug
     *
     * @return bool
     */
    function um_is_core_page($page)
    {
    }
    /**
     * Display a link to profile page
     *
     * @param int|bool $user_id
     *
     * @return bool|string
     */
    function um_user_profile_url($user_id = false)
    {
    }
}
