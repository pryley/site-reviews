<?php

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
namespace {
    \define('__BREAKDANCE_VERSION', '2.3.0-rc.2');
}
