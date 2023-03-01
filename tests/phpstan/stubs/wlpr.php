<?php

namespace Wlpr\App\Helpers {
    class Base
    {
        public function __construct($config = array())
        {
        }
        public function globalPoints($amount)
        {
        }
        function getRedeemPointAmount($point)
        {
        }
        function getRedeemPointDisplayPrice($point)
        {
        }
        function getEarnPointAmount($point)
        {
        }
        public function getCartMaxDiscountPercentage($maxDiscount)
        {
        }
        function getPointForDiscount($discountPrice)
        {
        }
        public function getUsersPoints($userEmail)
        {
        }
        public function getPointLabel()
        {
        }
        public function roundPoints($points)
        {
        }
        public function addEarnPoint($email, $points, $eventType, $data = null, $orderId = null)
        {
        }
        function reducePoints($userEmail, $points, $eventType, $data = array(), $orderId = null)
        {
        }
        public function getPointEarnedForOrderReceived($orderId)
        {
        }
        function checkExcludeUserRole($email = '')
        {
        }
        public function eventTypeDescription($eventType, $event = null)
        {
        }
        function activityTypeDescription($item)
        {
        }
    }
    class Point extends \Wlpr\App\Helpers\Base
    {
        public static $instance = null;
        public function __construct($config = array())
        {
        }
        public static function getInstance(array $config = array())
        {
        }
        function getUsersDiscountValue($userEmail)
        {
        }
        function getDiscountForRedeemingPoints($applying = false, $existingDiscountAmounts = null, $forDisplay = false)
        {
        }
        function deductRedeemedPoints($orderId, $order = '')
        {
        }
        function addPointEarned($orderId, $order = '')
        {
        }
        function getPointsEarnedForPurchase($order)
        {
        }
        function checkSignUpPoint($email)
        {
        }
        public function getDiscountMethod()
        {
        }
        public function getCouponApplyMethod()
        {
        }
        public function isLoyalDiscountApplied()
        {
        }
        public function isValidEmailForRedeem($email)
        {
        }
    }
    class Loyalty
    {
        /**
         * point instance
         * @param array $config
         * @return Point|null
         * @since 1.0.0
         */
        public static function point($config = array())
        {
        }
    }
}
namespace Wlpr\App\Models {
    abstract class Base
    {
        protected $table = NULL, $primary_key = NULL, $fields = array();
        protected static $db;
        function __construct()
        {
        }
        function getTableName()
        {
        }
        function getTablePrefix()
        {
        }
        function create()
        {
        }
        abstract function beforeTableCreation();
        abstract function runTableCreation();
        abstract function afterTableCreation();
        function saveData($data)
        {
        }
        function insertRow($data)
        {
        }
        function formatData($data)
        {
        }
        protected function createTable($query, $add_charset = true)
        {
        }
        protected function getCollation()
        {
        }
        function getWhere($where, $select = '*', $single = true)
        {
        }
        function rawQuery($query, $single = true)
        {
        }
        function getAll($select = '*')
        {
        }
        function updateRow($data, $where = array())
        {
        }
        function deleteRow($where)
        {
        }
        function getByKey($key)
        {
        }
        function getPrimaryKey()
        {
        }
    }
    class PointAction extends \Wlpr\App\Models\Base
    {
        function __construct()
        {
        }
        function beforeTableCreation()
        {
        }
        function runTableCreation()
        {
        }
        function afterTableCreation()
        {
        }
        function earn_point_action($user_email, $action = 'order-placed', $args = array())
        {
        }
        public function redeem_point_action($user_email, $action, $args = array())
        {
        }
        function get_expiring_points($date_expire_before)
        {
        }
        function get_email_expire_points($email_expire_date, $date_expire_before, $expire_since = '')
        {
        }
        function get_expire_email_list()
        {
        }
    }
}
