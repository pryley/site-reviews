<?php

namespace Wlr\App\Helpers {
    class Base
    {
        public static $woocommerce_helper, $input, $user_by_email, $point_user, $user_model, $earn_campaign_transaction_model;
        public static $user_level = array();
        public static $next_level = array();
        public static $action_reward_by_id;
        public static $user_reward_by_coupon = array();
        public function __construct($config = array())
        {
        }
        public static function readMoreLessContent($message, $read_key, $length, $read_more_text, $read_less_text, $id_prefix = 'read-more-less', $class = '')
        {
        }
        function getTotalEarning($action_type = '', $ignore_condition = array(), $extra = array(), $is_product_level = false)
        {
        }
        function is_valid_action($action_type)
        {
        }
        function isEligibleForEarn($action_type, $extra = array())
        {
        }
        function getSocialActionList()
        {
        }
        function checkUserEarnedInCampaignFromOrder($order_id, $campaign_id)
        {
        }
        function processCampaignMessage($action_type, $rule, $earning)
        {
        }
        protected function camelCaseAction($action_type)
        {
        }
        function processShortCodes($short_codes, $message)
        {
        }
        function getUserRewardTransaction($code, $order_id)
        {
        }
        function isAllowEarningWhenCoupon($is_cart = true, $order = '')
        {
        }
        function is_loyalty_coupon($code)
        {
        }
        function getUserRewardByCoupon($code)
        {
        }
        public function roundPoints($points)
        {
        }
        function getPointOrRewardText($point, $available_rewards, $with_label = false)
        {
        }
        public function getPointLabel($point, $label_translate = true)
        {
        }
        public function getRewardLabel($reward_count = 0)
        {
        }
        function getUserPoint($email)
        {
        }
        function getPointUserByEmail($user_email)
        {
        }
        function getPointBalanceByEmail($user_email)
        {
        }
        function getCustomerEmail($user_email, $order = null)
        {
        }
        // Customer details page
        function getUserRewardCount($user_email, $status = '')
        {
        }
        function getUserTotalTransactionAmount($user_email)
        {
        }
        function getRewardById($reward_id)
        {
        }
        function checkSocialShare($data)
        {
        }
        function getCartEarnMessageDesign($message = '')
        {
        }
        public static function setImageIcon($img, $icon, $attributes)
        {
        }
        function getCartRedeemMessageDesign($message = '')
        {
        }
        function getThankfulPageDesign($message = '')
        {
        }
        /**
         * get level details
         *
         * @param $id
         *
         * @return object|null
         */
        function getLevel($id)
        {
        }
        function isPro()
        {
        }
        function getNextLevel($to_point = 0, $level_id = 0)
        {
        }
        function addExtraTransaction($action, $user_email, $params = array())
        {
        }
        function isValidExtraAction($action_type)
        {
        }
        function getExtraActionList()
        {
        }
        function getProductActionList()
        {
        }
        function getCartActionList()
        {
        }
        function getReferralUrl($code = '')
        {
        }
        public function get_coupon_expiry_date($expiry_date, $as_timestamp = false)
        {
        }
        function addExtraRewardAction($action_type, $reward, $action_data)
        {
        }
        function get_unique_refer_code($ref_code = '', $recursive = false, $email = '')
        {
        }
        function get_random_code()
        {
        }
        function getActionName($action_type)
        {
        }
        function add_note($data)
        {
        }
        function getAchievementName($achievement_key)
        {
        }
        function addExtraPointAction($action_type, $point, $action_data, $trans_type = 'credit', $is_update_used_point = false, $force_update_earn_campaign = false, $update_earn_total_point = true)
        {
        }
        function isValidPointLedgerExtraAction($action_type)
        {
        }
        function updatePointLedger($data = array(), $point_action = 'credit', $is_update = true)
        {
        }
        function addCustomerToLoyalty($email, $action = 'signin')
        {
        }
        function isIncludingTax()
        {
        }
    }
}
namespace Wlr\App\Premium\Helpers {
    class ProductReview extends \Wlr\App\Helpers\Base
    {
        public static $instance = null;
        public static $number_order_made_with_product_ids = array();
        public function __construct($config = array())
        {
        }
        function getTotalEarnPoint($point, $rule, $data)
        {
        }
        function checkProductReview($data)
        {
        }
        function get_orders_ids_by_product_id($product_id, $email, $limit = 1)
        {
        }
        function getTotalEarnReward($reward, $rule, $data)
        {
        }
        function applyEarnProductReview($action_data)
        {
        }
        public static function getInstance(array $config = array())
        {
        }
        function processMessage($point_rule, $earning)
        {
        }
    }
}
namespace Wlr\App\Helpers {
    class EarnCampaign extends \Wlr\App\Helpers\Base
    {
        public static $instance = null;
        public static $single_campaign = [];
        public $earn_campaign, $available_conditions = [];
        public function __construct($config = [])
        {
        }
        function getCampaign($campaign)
        {
        }
        public function getAvailableConditions()
        {
        }
        function getCampaignReward($data)
        {
        }
        function isActive()
        {
        }
        function processCampaignCondition($data, $is_product_level = false)
        {
        }
        function getConditions()
        {
        }
        protected function hasConditions()
        {
        }
        function processCampaignRewards($data)
        {
        }
        protected function processCampaignAction($action_type, $type, $campaign, $data)
        {
        }
        function getCampaignPoint($data)
        {
        }
        function getActionEarning($cart_action_list, $extra)
        {
        }
        function processOrderReturn($order_id)
        {
        }
        public static function getInstance(array $config = [])
        {
        }
        function processOrderEarnPoint($order_id)
        {
        }
        function applyEarnCampaign($action_data)
        {
        }
        function addEarnCampaignPoint($action_type, $point, $campaign_id, $action_data)
        {
        }
        function addEarnCampaignReward($action_type, $reward, $campaign_id, $action_data, $force_generate_coupon = false)
        {
        }
        function processLogData($log_type, $action_type, $point, $reward_name, $action_data)
        {
        }
        /**
         * Check free product out of stock status.
         *
         * @param boolean $status Instant coupon apply.
         * @param object $reward Reward data.
         *
         * @return bool
         */
        function checkFreeProductCoupon($status, $reward)
        {
        }
        function addPointValue($reward_list)
        {
        }
        function concatRewards($reward_list)
        {
        }
        function getPointEarnedFromOrder($order_id, $email = '')
        {
        }
        function changeDisplayDate($campaign)
        {
        }
        function getCampaignPointReward($active_campaigns)
        {
        }
    }
}
namespace Wlr\App\Premium\Helpers {
    class Referral extends \Wlr\App\Helpers\EarnCampaign
    {
        public static $instance = null;
        public function __construct($config = array())
        {
        }
        public static function getInstance(array $config = array())
        {
        }
        function processReferralEarnPoint($order_id)
        {
        }
        function getAdvocateEmail($friend_email)
        {
        }
        function applyReferralEarnCampaign($action_data)
        {
        }
        function isEligibleReferralForEarn($action_type, $extra = array())
        {
        }
        function getTotalReferralEarning($action_type = '', $ignore_condition = array(), $extra = array(), $is_product_level = false)
        {
        }
        function getCampaignReferral($data, $referral_type, $earn_type = 'point')
        {
        }
        protected function processReferralCampaignAction($action_type, $type, $campaign, $data, $referral_type)
        {
        }
        function getTotalReferralEarn($point, $rule, $data, $referral_type, $earn_type)
        {
        }
        function checkReferralData($data)
        {
        }
        function checkReferralTable($data, $order_count = 1)
        {
        }
        function doReferralCheck($action_data, $referral_type = 'friend')
        {
        }
        function isValidRefCode($ref_code)
        {
        }
    }
}
namespace Wlr\App\Helpers {
    class Woocommerce
    {
        public static $instance = null;
        static $reward_name = array();
        protected static $products = array();
        protected static $options = array();
        protected static $banned_user = array();
        public static function hasAdminPrivilege()
        {
        }
        public static function create_nonce($action = -1)
        {
        }
        public static function verify_nonce($nonce, $action = -1)
        {
        }
        public static function getCleanHtml($html)
        {
        }
        /**
         * Get the available date periods for loyalty rules.
         *
         * @return array The array of available date periods.
         *
         * @since 1.0.0
         */
        public static function getDatePeriod()
        {
        }
        function isFullyDiscounted()
        {
        }
        function get_login_user_email()
        {
        }
        function get_email_by_id($id)
        {
        }
        function getRole($user)
        {
        }
        function beforeSaveDate($date, $format = 'Y-m-d H:i:s')
        {
        }
        function convert_wp_time_to_utc($datetime, $format = 'Y-m-d H:i:s', $modify = '')
        {
        }
        function get_wp_time_zone()
        {
        }
        function convertDateFormat($date, $format = '')
        {
        }
        function beforeDisplayDate($date, $format = '')
        {
        }
        function convert_utc_to_wp_time($datetime, $format = 'Y-m-d H:i:s', $modify = '')
        {
        }
        function getActionTypes()
        {
        }
        public static function getInstance(array $config = array())
        {
        }
        public static function getAllActionTypes()
        {
        }
        /**
         * Get reward discount types.
         *
         * @return array Returns an array of reward discount types.
         */
        public static function getRewardDiscountTypes()
        {
        }
        public static function getUserRoles()
        {
        }
        public function getPaymentMethod()
        {
        }
        static function getPaymentMethodList()
        {
        }
        /**
         * Get the list of reward conditions.
         *
         * @return array
         */
        public static function getRewardAcceptConditions()
        {
        }
        /**
         * Get the list of campaign conditions.
         *
         * @return array The list of campaign conditions.
         */
        public static function getCampaignConditionList()
        {
        }
        public static function getActionAcceptConditions()
        {
        }
        function isCartEmpty($cart = '')
        {
        }
        function getCart($cart = null)
        {
        }
        function isMethodExists($object, $method_name)
        {
        }
        function getCartItems($cart = '')
        {
        }
        public function getCartItem($key = '')
        {
        }
        function setCartProductPrice($cart_item_object, $price)
        {
        }
        function arrayKeyLast($array = array())
        {
        }
        function getCartSubtotal($cart_data = null)
        {
        }
        public static function getOrderStatuses()
        {
        }
        public static function format_order_statuses($statuses)
        {
        }
        function getOrderItemsQty($order)
        {
        }
        function getOrderItems($order = null)
        {
        }
        function getOrderSubtotal($order_data = null)
        {
        }
        function getOrder($order = null)
        {
        }
        function getOrderId($order = null)
        {
        }
        function getOrderTotal($order)
        {
        }
        function getOrderItemsId($order)
        {
        }
        function getItemId($item)
        {
        }
        function getOrderStatus($order = null)
        {
        }
        function getSession($key, $default = null)
        {
        }
        function setSession($key, $data)
        {
        }
        function getProductAttributes($product)
        {
        }
        function getProductId($product)
        {
        }
        function getAttributeVariation($attribute)
        {
        }
        function getAttributeOption($attribute)
        {
        }
        function getAttributeName($attribute)
        {
        }
        function getProductCategories($product)
        {
        }
        function productTypeIs($product, $type)
        {
        }
        function getProductParentId($product)
        {
        }
        function getProduct($product_id)
        {
        }
        function getProductSku($product)
        {
        }
        function isProductInSale($product)
        {
        }
        function getParentProduct($product)
        {
        }
        function getProductTags($product)
        {
        }
        function combineProductArrays($products, $additional_products)
        {
        }
        public function add_to_cart($product_id = 0, $quantity = 1, $variation_id = 0, $variation = array(), $cart_item_data = array())
        {
        }
        public function remove_cart_item($_cart_item_key)
        {
        }
        public function get_cart_item($cart_item_key)
        {
        }
        public function get_variant_ids($product_id)
        {
        }
        function get_loyalty_rest_url($action_name, $blog_id = null)
        {
        }
        function get_referral_code()
        {
        }
        function set_referral_code($referral_code)
        {
        }
        function initWoocommerceSession()
        {
        }
        function hasSession()
        {
        }
        function setSessionCookie($value)
        {
        }
        function current_offset()
        {
        }
        function isJson($string)
        {
        }
        function _log($message)
        {
        }
        function getOptions($key = '', $default = '')
        {
        }
        function isValidCoupon($coupon_code)
        {
        }
        function hasDiscount($discount_code)
        {
        }
        /**
         * Get cart applied coupons.
         *
         * @return array
         */
        function getAppliedCoupons()
        {
        }
        function getProductPrice($product, $item = null, $is_redeem = false, $orderCurrency = '')
        {
        }
        function getCurrentCurrency($currency = '')
        {
        }
        function getCurrentLanguage($lang = '')
        {
        }
        function getProductIdBasedOnCurrentLanguage($prod_id, $lang)
        {
        }
        function getOrderLanguage($order_id)
        {
        }
        function getOrderMetaData($order_id, $meta_key, $default_value = '')
        {
        }
        function isHPOSEnabled()
        {
        }
        function isBannedUser($user_email = "")
        {
        }
        function getPluginBasedOrderLanguage($order_id)
        {
        }
        function getOrdersThroughWPQuery($args = array())
        {
        }
        function getOrderPostType($key_only = false)
        {
        }
        function getOrderStatusList()
        {
        }
        function getOrdersThroughWCOrderQuery($args)
        {
        }
        function changeToQueryStatus($status_list)
        {
        }
        function generateCustomPOTFile($translate_strings = array(), $project_title = "WPLoyalty - Dynamic content", $file_name = "wployalty_custom.pot", $text_domain = "wp-loyalty-rules")
        {
        }
        function getPOTFirstString($project_title = "", $text_domain = 'wp-loyalty-rules')
        {
        }
        public static function getDirFileLists($folder = '', $levels = 100, $exclusions = array())
        {
        }
        function getVariantsOfProducts($product_ids)
        {
        }
        function getProductChildren($product)
        {
        }
        function getCustomPrice($amount, $with_symbol = true, $currency = '')
        {
        }
        function convertPrice($amount, $with_symbol = true, $currency = '')
        {
        }
        function getDefaultWoocommerceCurrency($currency = '')
        {
        }
        /* HPOS*/
        function getCurrencySymbols($currency = '')
        {
        }
        function getDisplayCurrency($currency = '')
        {
        }
        function updateOrderMetaData($order_id, $meta_key, $value)
        {
        }
        function getOrderLink($order_id, $is_admin_side = true)
        {
        }
        function getOrderEmail($order)
        {
        }
        function numberFormatI18n($point)
        {
        }
        function canShowBirthdateField()
        {
        }
        function checkStatusNewRewardSection()
        {
        }
        function renameTemplateOverwritedFiles($templates = array())
        {
        }
        public static function isBlockEnabled()
        {
        }
        public static function isCartBlock()
        {
        }
        public static function isCheckoutBlock()
        {
        }
        /**
         * Add a schedule for a hook.
         *
         * @param string $hook The hook name to schedule.
         * @param string $time Optional. The time to start the schedule. Default is '+1 hours'.
         * @param string $recurrence Optional. The recurrence of the schedule. Default is 'hourly'.
         *
         * @return void
         */
        public static function addSchedule($hook, $time = '+1 hours', $recurrence = 'hourly')
        {
        }
        /**
         * Adds a One-Time schedule for a hook.
         *
         * @param string $hook The hook name to schedule.
         * @param array $args The arguments to be passed to the schedule.
         * @param string $time Optional. The time ahead for the schedule. Default is 1 hour.
         *
         * @return void
         */
        public static function addOneTimeSchedule($hook, $args = [], $time = '+1 hours')
        {
        }
        /**
         * Removes a scheduled event.
         *
         * @param string $hook The unique identifier for the scheduled event.
         *
         * @return void
         */
        public static function removeSchedule($hook)
        {
        }
    }
}
