<?php

namespace Breakdance
{
    trait Singleton
    {
        /**
         * @return self
         */
        final public static function getInstance(): self
        {
        }
        // Prevent cloning of the instance
        public function __clone()
        {
        }
        // Prevent deserialization of the instance
        public function __wakeup()
        {
        }
    }
}
namespace Breakdance\Lib\Vendor\League\HTMLToMarkdown
{
    interface ElementInterface
    {
    }
}
namespace Breakdance\AJAX
{
    /**
     * @return string
     */
    function get_nonce_key_for_ajax_requests()
    {
    }
    /**
     * @return string
     */
    function get_nonce_for_ajax_requests()
    {
    }
    /**
     * @param string $route
     * @param callable $callback
     * @param string $minimum_permissions
     * @param bool $any_url
     * @param array $options
     * @param bool $skip_nonce_check
     * @return void
     * @throws Exception
     */
    function register_handler($route, callable $callback, $minimum_permissions = 'never', $any_url = false, $options = [], $skip_nonce_check = false)
    {
    }
}
namespace Breakdance\Elements
{
    class Element implements \Breakdance\Lib\Vendor\League\HTMLToMarkdown\ElementInterface
    {
        /**
         * @return string
         */
        public static function defaultTag()
        {
        }
        /**
         * @return string
         */
        public static function uiIcon()
        {
        }
        /**
         * @return string[]
         */
        public static function tagOptions()
        {
        }
        /**
         * @return string|false
         */
        public static function tagControlPath()
        {
        }
        /**
         * @return string
         */
        public static function template()
        {
        }
        /**
         * @return string
         */
        public static function slug()
        {
        }
        /**
         * @return string|false
         */
        public static function name()
        {
        }
        /**
         * @return string|false
         */
        public static function className()
        {
        }
        /**
         * @return string
         */
        public static function category()
        {
        }
        /**
         * @return mixed|false
         */
        public static function badge()
        {
        }
        /**
         * @return Control[]
         */
        public static function contentControls()
        {
        }
        /**
         * @return Control[]
         */
        public static function designControls()
        {
        }
        /**
         * @return Control[]
         */
        public static function settingsControls()
        {
        }
        /**
         * @return DynamicPropertyPath[]|false|null
         */
        public static function dynamicPropertyPaths()
        {
        }
        /**
         * @return mixed
         */
        public static function defaultProperties()
        {
        }
        /**
         * @return false|string[]
         */
        public static function defaultChildren()
        {
        }
        /**
         * @return ElementAttribute[]|false
         */
        public static function attributes()
        {
        }
        /**
         * @return string
         */
        public static function defaultCSS()
        {
        }
        /**
         * @return string
         */
        public static function cssTemplate()
        {
        }
        /**
         * @return array{scripts?:string[],inlineScripts?:string[],styles?:string[],inlineStyles?:string[],builderCondition?:string,frontendCondition?:string}[]|false
         */
        public static function dependencies()
        {
        }
        /**
         * @return ElementSettings|false
         */
        public static function settings()
        {
        }
        /**
         * @return BuilderActions|false
         */
        public static function actions()
        {
        }
        /**
         * @return array{location:string,cssProperty:string,affectedPropertyPath:string}[]|false
         */
        public static function spacingBars()
        {
        }
        /**
         * @return mixed
         */
        public static function nestingRule()
        {
        }
        /**
         * @return string
         */
        public static function tag()
        {
        }
        /**
         * @param mixed $propertiesData
         * @param mixed $parentPropertiesData
         * @param bool $isBuilder
         * @param int $repeaterItemNodeId
         * @return string
         */
        public static function ssr($propertiesData, $parentPropertiesData = [], $isBuilder = false, $repeaterItemNodeId = null)
        {
        }
        /**
         * @return false|mixed
         */
        public static function addPanelRules()
        {
        }
        public static function requiredPlugins()
        {
        }
        /**
         * @return boolean
         */
        public static function experimental()
        {
        }
        /**
         * @return int
         */
        public static function order()
        {
        }
        /**
         * @return array{name:string,template:string}[]|false
         */
        public static function additionalClasses()
        {
        }
        /**
         * @return mixed|false
         */
        public static function projectManagement()
        {
        }
        /**
         * @return string[]|false
         */
        public static function propertyPathsToWhitelistInFlatProps()
        {
        }
        /**
         * @return string[]|false
         */
        public static function propertyPathsToSsrElementWhenValueChanges()
        {
        }
    }
    /**
     * @param string $slug
     * @param string $label
     * @param array $children
     * @param mixed $options
     * @param boolean $enable_media_queries
     * @param boolean $enable_hover
     * @param array $keywords
     * @return array
     */
    function c($slug, $label, $children, $options, $enable_media_queries, $enable_hover, $keywords = [])
    {
    }
    /**
     * @param string $slug
     * @param string $label
     * @return void
     */
    function registerCategory($slug, $label)
    {
    }
}
namespace Breakdance\Elements\PresetSections
{
    /**
     *
     * @param string $presetSlug
     * @param false|string $label
     * @param false|string $sectionSlug
     * @param false|mixed $options
     * @return array
     */
    function getPresetSection($presetSlug, $label = false, $sectionSlug = false, $options = false)
    {
    }
    class PresetSectionsController
    {
        use \Breakdance\Singleton;
        /**
         * @param string $slug
         * @param Control $section
         * @param boolean $availableInElementStudio
         * @param array{relativePropertyPathsToWhitelistInFlatProps?: string[], relativeDynamicPropertyPaths?: DynamicPropertyPath[], codeHelp?: string}|null $options
         */
        public function register($slug, $section, $availableInElementStudio = false, $options = null)
        {
        }
        /**
         * @return Preset[]
         */
        public function getAvailableInElementStudio()
        {
        }
    }
}
namespace Breakdance\ElementStudio
{
    /**
     * @param string  $directoryPath - e.g: $directoryPath = getDirectoryPathRelativeToPluginFolder(__DIR__) . '/elements'
     * @param string  $namespace
     * @param string $type 'element'|'macro'|'preset'
     * @param string $label
     * @param boolean $onlyForAdvancedUsers
     * @param boolean $excludeFromElementStudio
     * @return void
     */
    function registerSaveLocation($directoryPath, $namespace, $type, $label, $onlyForAdvancedUsers, $excludeFromElementStudio = false)
    {
    }
}
namespace Breakdance\Permissions
{
    /**
     * @param string $minimumPermission
     * @param int|\WP_User $userId
     * @return bool
     * @throws \Exception
     */
    function hasMinimumPermission($minimumPermission, $userId = 0)
    {
    }
}
namespace EssentialElements
{
    class Formdesignoptions extends \Breakdance\Elements\Element
    {
    }
}
namespace {
    \define('__BREAKDANCE_VERSION', '2.3.0-rc.2');
}
