<?php

namespace LWS\WOOREWARDS\Abstracts {
    /** Provide an associative array of category [code => label]. */
    interface ICategorisable
    {
        public function getCategories();
    }
    /** The class must provide function to register it. */
    interface IRegistrable
    {
        /** Declare a new kind of Registrable.
         *  @param $classname (string) the class to instanciate.
         *  @param $filepath (string) full path to class declaration file to include at need.
         *  @param $unregister (bool) default: false, to remove a Registrable.
         *  @param $typeOverride (false|string) default:false, to override an existant Registrable set the original classname @see getClass.
         **/
        public static function register($classname, $filepath, $unregister = false, $typeOverride = false);
        public static function getRegistered();
        public static function getRegisteredByName($name);
        /** @return (string) class name.
         *  Usually return \get_class($this).
         *  But in case the Registrable override another one, it should return the original classname. */
        function getClassname();
    }
    /** Base class for each way to earn points.
     *  To be used, an Event must be declare by calling Event::register @see IRegistrable
     *
     *  The final purpose of an Event is to generate points @see addPoint()
     *
     *  Each pool is in charge to :
     * * install its selected events @see _install()
     * * save specific settings @ss _save()
     * * load specific data @see _fromPost()
     *
     *  Anyway, an event is available for information or selection and so can be instanciated from anywhere.
     *  */
    abstract class Event implements \LWS\WOOREWARDS\Abstracts\ICategorisable, \LWS\WOOREWARDS\Abstracts\IRegistrable
    {
        const POST_TYPE = 'lws-wre-event';
        private static $s_events = array();
        /** Inhereted Event already instanciated from WP_Post, $this->id is availble. It is up to you to load any extra configuration. */
        protected abstract function _fromPost(\WP_Post $post);
        /** Event already saved as WP_Post, $this->id is availble. It is up to you to save any extra configuration. */
        protected abstract function _save($id);
        /** @return a human readable type for UI */
        public abstract function getDisplayType();
        /** Add hook to grab events and add points. */
        protected abstract function _install();
        /** To be overridden.
         *  @return (bool) if event support that rule.
         *  Cooldown: do not earn points if the same event triggered
         *  too many time in a period. */
        function isRuleSupportedCooldown()
        {
        }
        /** @param userId (int)
         *  @param $defaultUnset (bool) the value returned if no cooldown defined;
         *  default is true, so no cooldown means that event is always available.
         *  @return (bool) true if cooldown is not already full
         *  for the given user. */
        function isCool($userId, $defaultUnset = true, $defaultNoUser = false)
        {
        }
        function getCooldownText()
        {
        }
        /** @return false or object with properties {count, period[, interval]} */
        function getCooldownInfo($withDateInterval = false)
        {
        }
        function setCooldownInfo($count = 0, $period = 0.0)
        {
        }
        /** Rolling period or periodic date range */
        public function isCooldownRolling()
        {
        }
        public function setCooldownRolling($yes = true)
        {
        }
        /** If format is false, return a DateTimeImmutable.
         *  Else return a string with date representation.
         *  Or return false if no date set or cooldown is rolling. */
        public function getCooldownResetDateTime($format = false, $timezone = false)
        {
        }
        /** store a datetimeimmutable
         *  @param $datetime: DateTime|DateTimeImmutable|string|int
         *  An integer is assumed UTC timestamp,
         *  A string is assumed in website timestamp. */
        public function setCooldownResetDateTime($datetime)
        {
        }
        public function updateCooldownResetDateTime($datetime, $cooldownInfo, $save = false)
        {
        }
        /** If additionnal info should be displayed in settings form. */
        protected function getCooldownTooltips($text)
        {
        }
        public static $recurrentAllowed = false;
        function isRepeatAllowed()
        {
        }
        /** @return false or object with properties {count, period[, interval]} */
        function getRepeatInfo($withDateInterval = false)
        {
        }
        function setRepeatInfo($count = 0, $period = 0.0)
        {
        }
        public static $delayAllowed = false;
        function isDelayAllowed()
        {
        }
        /** returns a Duration instance */
        public function getDelay()
        {
        }
        /** @param $days (false|int|Duration) */
        public function setDelay($days = false)
        {
        }
        /** Override to allow max triggers, default is false */
        public function isMaxTriggersAllowed()
        {
        }
        public function getMaxTriggers()
        {
        }
        public function setMaxTriggers($maxTriggers)
        {
        }
        public function incrTriggerCount($userId)
        {
        }
        public function getTriggerCount($userId)
        {
        }
        /** if feature not enabled or used, always return true.
         *  else, a user is required to trigger the event
         *  so we can check the counter.
         *  @param $options (array|WP_User|int) the user earning points, an array have to include an entry 'user'.
         *  @param $doIncrement (bool) if true and can be triggered, then increment the counter.
         *  @return (bool) true if no restriction set or event trigger count is not reached. */
        public function canBeTriggered($options, $doIncrement = false)
        {
        }
        /** @return array of data to feed the form @see getForm.
         *  Each key should be the name of an input balise. */
        function getData()
        {
        }
        /** Provided to be overriden.
         *  @param $context usage of returned inputs, default is an edition in editlist.
         *  @return (string) the inside of a form (without any form balise).
         *  @notice in override, dedicated option name must be type specific @see getDataKeyPrefix()
         *  dedicated DOM must declare css attribute for hidden/show editlist behavior
         *  @code
         *      class='lws_woorewards_system_choice {$this->getType()}'
         *  @endcode
         *  You can use several placeholder balises to insert DOM in middle of previous form (take care to keep for anyone following).
         *  For each fieldset (numbered from 0, 1...) @see str_replace @see getFieldsetPlaceholder()
         *  @code
         *  <!-- [fieldset-1-head:{$this->getType()}] -->
         *  <!-- [fieldset-1-foot:{$this->getType()}] -->
         *  @endcode */
        function getForm($context = 'editlist')
        {
        }
        /** Provided to be overriden.
         *  Back from the form, set and save data from @see getForm
         *  @param $source origin of form values. Expect 'editlist' or 'post'. If 'post' we will apply the stripSlashes().
         *  @return true if ok, (false|string|WP_Error) false or an error description on failure. */
        function submit($form = array(), $source = 'editlist')
        {
        }
        protected function getFieldsetBegin($index, $title = '', $css = '', $withPlaceholder = true)
        {
        }
        protected function getFieldsetEnd($index, $withPlaceholder = true)
        {
        }
        /** @see getForm insert that balise at top and bottom of each fieldset.
         * @return (string) html */
        protected function getFieldsetPlaceholder($top, $index)
        {
        }
        protected function getDataKeyPrefix()
        {
        }
        /** Provided to be overriden.
         *  @param $context usage of text. Default is 'backend' for admin, expect 'frontend' for customer.
         *  @return (string) what this does. */
        function getDescription($context = 'backend')
        {
        }
        /** Purpose of an event: earn point for a pool.
         *  Call this function when an earning event occurs.
         *  @param $options (array|WP_User|int) the user earning points, an array have to include an entry 'user'.
         *  @param $reason (string) the cause of the earning.
         *  @param $pointCount (int) number of point earned, usually 1 since it is up to the pool to multiply it. */
        protected function addPoint($options, $reason = '', $pointCount = 1, $origin2 = false)
        {
        }
        public function install()
        {
        }
        public static function fromPost(\WP_Post $post)
        {
        }
        /** @param $type (string|array) a registered type or an item of getRegistered(). */
        static function instanciate($type)
        {
        }
        public function save(\LWS\WOOREWARDS\Core\Pool &$pool)
        {
        }
        /** @see https://wpml.org/documentation/support/string-package-translation
         * Known wpml bug: kind first letter must be uppercase */
        function getPackageWPML($full = false)
        {
        }
        /** @alias for getTitle(false, true) */
        public function getTitleAsReason()
        {
        }
        public function getTitle($fallback = true, $forceTranslate = false)
        {
        }
        public function setTitle($title = '')
        {
        }
        public function delete()
        {
        }
        /** Declare a new kind of event. */
        public static function register($classname, $filepath, $unregister = false, $typeOverride = false)
        {
        }
        public static function getRegistered()
        {
        }
        public static function getRegisteredByName($name)
        {
        }
        public function unsetPool()
        {
        }
        public function setPool(&$pool)
        {
        }
        public function getPool()
        {
        }
        public function getOrLoadPool()
        {
        }
        public function getPoolId()
        {
        }
        public function getPoolName()
        {
        }
        public function getPoolType()
        {
        }
        public function getPoolStatus()
        {
        }
        public function getStackName()
        {
        }
        public function getId()
        {
        }
        public function detach()
        {
        }
        /** @param $classname full class with namespace. */
        public static function formatType($classname = false)
        {
        }
        public function getType()
        {
        }
        function getClassname()
        {
        }
        public function setGainAlt($text)
        {
        }
        /** @param $default (bool|null) true return the default/fallback text,
         *  false return the user text, never the fallback,
         *  null return try to translate and replace `[currency]` by the point symbol. */
        public function getGainAlt($default = null)
        {
        }
        /** If only number, it should be greater than 0.
         *  If expression, is have to be valid, regardless the result. */
        public function isValidGain($lazy = false)
        {
        }
        /** Add decoration for display purpose, e.g. cooldown info. */
        protected function shapeGain($value)
        {
        }
        /** @return (string|number) value as set by the user.
         *  No expression resolution.
         *  @param $shape (bool) add decoration for display purpose, e.g. cooldown info. */
        public function getGainRaw($shape = false)
        {
        }
        /** provided for convenience, resolve any expression, then multiply.
         *  or return default (or zero) as fallback. */
        public function getFinalGain($multiply = 1, $options = array(), $quiet = false)
        {
        }
        /** @return number, resolve expression if any.
         *  Even for bad expression, zero is used as fallback.
         *  If no user given, use the current user.
         *  @param $quiet (bool) if true, no log if expression cannot be resolved. */
        public function getGain($options = array(), $quiet = false)
        {
        }
        private $_gainTimestamp = null;
        /** @see getGain
         *  @param $timestamp anything else than null.
         *  If timestamp changed since last call, compute gain again.
         *  Else return last saved value. */
        public function getGainWithCache($options = array(), $quiet = false, $timestamp = false)
        {
        }
        /** @return (string) to show to customers.
         *  @param $altFallback )(bool) if true, if expression cannot be resolve,
         *  return the alternative gain text. Else return the original value.
         *  Try to resolve the expression as is.
         *  But if it is not possible, fallback on a userdefined string
         *  or a default text at last. */
        public function getGainForDisplay($options = array(), $altFallback = true)
        {
        }
        public function setGain($value)
        {
        }
        /** Multiplier is registered by Pool, it is applied to the points generated by the event.
         *  @deprecated 4.9 obsolete, @see getGain, @see getGainForDisplay */
        public function getMultiplier($context = 'edit')
        {
        }
        /** @deprecated 4.9 obsolete, @see setGain */
        public function setMultiplier($multiplier)
        {
        }
        public function setName($name)
        {
        }
        public function getName($pool = null)
        {
        }
        /** Provided for convenience.
         * To get new properties with default value. */
        protected function getSinglePostMeta($postId, $meta, $default = false)
        {
        }
        /** To be overriden to provide choice to administrator. */
        function getInformation()
        {
        }
        /** Event categories, used to filter out events from pool options.
         *  @return array with category_id => category_label. */
        public function getCategories()
        {
        }
    }
}
namespace LWS\WOOREWARDS\Events {
    /** Earn points for product review.
     * That must be the first review of that customer on that product.
     * The customer must have ordered the product before. */
    class ProductReview extends \LWS\WOOREWARDS\Abstracts\Event
    {
        function getInformation()
        {
        }
        function isRuleSupportedCooldown()
        {
        }
        public function isMaxTriggersAllowed()
        {
        }
        /** If additionnal info should be displayed in settings form. */
        protected function getCooldownTooltips($text)
        {
        }
        function getData()
        {
        }
        /** add help about how it works */
        function getForm($context = 'editlist')
        {
        }
        function submit($form = array(), $source = 'editlist')
        {
        }
        public function setPurchaseRequired($yes = false)
        {
        }
        function isPurchaseRequired()
        {
        }
        /** Inhereted Event already instanciated from WP_Post, $this->id is availble. It is up to you to load any extra configuration. */
        protected function _fromPost(\WP_Post $post)
        {
        }
        /** Event already saved as WP_Post, $this->id is availble. It is up to you to save any extra configuration. */
        protected function _save($id)
        {
        }
        /** @return a human readable type for UI */
        public function getDisplayType()
        {
        }
        /** Add hook to grab events and add points. */
        protected function _install()
        {
        }
        function delayedApproval($comment)
        {
        }
        function review($comment_id, $comment)
        {
        }
        /** When a registered customer comment a product for the very first time. */
        function trigger($comment_id, $comment_approved)
        {
        }
        protected function oncekey()
        {
        }
        protected function process($comment, $force = false)
        {
        }
        /** Never call, only to have poedit/wpml able to extract the sentance. */
        private function poeditDeclare()
        {
        }
        protected function isValid($comment, $delayed = false)
        {
        }
        /** @return true if customer already purchase product.
         * Order should ends to a term.
         * So tested post_status IN ('wc-completed', 'wc-processing', 'wc-refunded')
         * Other status (that are omited) are:
         * * wc-pending, wc-on-hold : still running.
         * * wc-cancelled, wc-failed : never finalised. */
        protected function isProductOrdered($comment)
        {
        }
        /** @return (false|object{userId, productId} */
        protected function getValidComment($comment_id, $comment_approved)
        {
        }
        /** Event categories, used to filter out events from pool options.
         *  @return array with category_id => category_label. */
        public function getCategories()
        {
        }
    }
}
