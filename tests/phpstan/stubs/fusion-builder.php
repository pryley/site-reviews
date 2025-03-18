<?php

namespace {
    /**
     * Builder Elements Class.
     *
     * @since 1.1.0
     */
    abstract class Fusion_Element
    {
        /**
         * FB options class object.
         *
         * @static
         * @access protected
         * @since 1.1.0
         * @var Fusion_Builder_Options|null
         */
        protected static $fb_options;
        /**
         * First add on or not.
         *
         * @static
         * @access protected
         * @since 1.1.0
         * @var boolean
         */
        protected static $first_addon = \true;
        /**
         * Dynamic CSS class object.
         *
         * @static
         * @access protected
         * @since 1.1.0
         * @var bool
         */
        protected static $dynamic_css_helpers;
        /**
         * Options array.
         * THis holds ALL OPTIONS from ALL ELEMENTS.
         *
         * @static
         * @access protected
         * @since 1.1.0
         * @var array
         */
        protected static $global_options = [];
        /**
         * Element ID.
         *
         * @access protected
         * @since 2.0
         * @var string|int
         */
        protected $element_id;
        /**
         * An array of the shortcode defaults.
         *
         * @access protected
         * @since 3.0
         * @var array
         */
        protected $defaults;
        /**
         * Whether it has rendered already or not.
         *
         * @access protected
         * @since 3.0
         * @var bool
         */
        protected $has_rendered = \false;
        /**
         * The element arguments.
         *
         * @var array
         */
        protected $args = [];
        /**
         * Dynamic CSS for creating style CSS.
         *
         * @var array
         */
        public $dynamic_css = [];
        /**
         * The class constructor
         *
         * @access private
         */
        public function __construct()
        {
        }
        /**
         * Add CSS to dynamic CSS.
         *
         * @access protected
         * @since 2.0
         */
        public function load_css()
        {
        }
        /**
         * Add CSS to dynamic CSS.
         *
         * @access protected
         * @since 3.0
         */
        public function add_css_files()
        {
        }
        /**
         * Adds settings to element options panel.
         *
         * @access protected
         * @since 1.1
         */
        protected function add_options()
        {
        }
        /**
         * Checks location of child class.
         *
         * @access protected
         * @since 1.1
         */
        protected function get_dir()
        {
        }
        /**
         * Adds scripts to the dynamic JS.
         *
         * @access protected
         * @since 1.1.0
         */
        protected function add_scripts()
        {
        }
        /**
         * Adds dynamic styling to dynamic CSS.
         *
         * @access protected
         * @since 1.1
         */
        protected function add_styling()
        {
        }
        /**
         * Fires on render.
         *
         * @access protected
         * @since 3.2
         */
        protected function on_render()
        {
        }
        /**
         * Fires on first render only.
         *
         * @access protected
         * @since 3.2
         */
        protected function on_first_render()
        {
        }
        /**
         * Ensure scripts are loaded for live editor.
         *
         * @access public
         * @since 3.2
         */
        public function live_editor_scripts()
        {
        }
        /**
         * Sets the ID for the element.
         *
         * @access protected
         * @param int $count Count of element or ID.
         * @since 2.0
         */
        protected function set_element_id($count)
        {
        }
        /**
         * Gets the ID for the element.
         *
         * @access protected
         * @since 2.0
         * @return string
         */
        protected function get_element_id()
        {
        }
        /**
         * Returns the $global_options property.
         *
         * @static
         * @access public
         * @since 1.1.0
         * @return array
         */
        public static function get_all_options()
        {
        }
        /**
         * Check if a param is default.
         *
         * @access public
         * @since 3.0
         * @param string $param Param name.
         * @return bool
         */
        public function is_default($param)
        {
        }
        /**
         * Add CSS property to overall array.
         *
         * @access protected
         * @since 3.0
         * @param mixed  $selectors CSS selector, array or string.
         * @param string $property CSS property.
         * @param string $value CSS value.
         * @param bool   $important Whether it is important or not.
         * @return void
         */
        protected function add_css_property($selectors = [], $property = '', $value = '', $important = \false)
        {
        }
        /**
         * Get a string with each of the option as a CSS variable, if the option is not default.
         *
         * @since 3.9
         * @param array $options  The array with the options ids.
         * @return string
         */
        protected function get_css_vars_for_options($options)
        {
        }
        /**
         * Get a string with custom CSS variables, created from array key => value pairs.
         *
         * @since 3.9
         * @param array   $options The array with the custom css vars. The key
         *   represents the option name, while the value represents the custom value.
         * @param boolean $prefix Whether to alter the variable name or not.
         * @return string
         */
        protected function get_custom_css_vars($options, $prefix = \true)
        {
        }
        /**
         * Get font styling vars, created from get_font_styling helper.
         *
         * @since 3.9
         * @param string $key Typography options key.
         * @return string
         */
        protected function get_font_styling_vars($key)
        {
        }
        /**
         * Get declaration for typography vars with the given values.
         *
         * @since 3.9
         * @param string $title_tag An HTML tag, Ex: 'h2', 'h3', 'div'.. etc.
         * @param array  $name_value_map The key is a css property, the array value is the CSS value.
         * @return string
         */
        protected function get_heading_font_vars($title_tag, $name_value_map)
        {
        }
        /**
         * Get aspect ratio vars.
         *
         * @since 3.9
         * @return string
         */
        protected function get_aspect_ratio_vars()
        {
        }
        /**
         * Get from ACF repeater for parent and child elements.
         *
         * @since 3.9
         * @param array  $dynamic_data The dynamic data.
         * @param array  $args The arguments.
         * @param string $content The content.
         * @param bool   $rendered Whether or not is rendered.
         * @return string
         */
        public static function get_acf_repeater($dynamic_data, $args, $content, $rendered = \true)
        {
        }
        /**
         * Add CSS property to overall array.
         *
         * @access protected
         * @since 3.0
         * @return string
         */
        protected function parse_css()
        {
        }
        /**
         * Filter the post ID for use as dynamic data source.
         *
         * @access public
         * @since 1.0
         * @return array
         */
        public function post_dynamic_data()
        {
        }
    }
    /**
     * The main FusionBuilder class.
     *
     * @package fusion-builder
     * @since 2.0
     */
    /**
     * Main FusionBuilder Class.
     *
     * @since 1.0
     */
    class FusionBuilder
    {
    }
}
namespace {
    /**
     * Add an element to $fusion_builder_elements array.
     *
     * @param array $module The element we're loading.
     */
    function fusion_builder_map($module)
    {
    }
    /**
     * Instantiates the FusionBuilder class.
     * Make sure the class is properly set-up.
     * The FusionBuilder class is a singleton
     * so we can directly access the one true FusionBuilder object using this function.
     *
     * @return object FusionBuilder
     */
    function FusionBuilder()
    {
    }
    /**
     * Taxonomies.
     *
     * @since 1.0
     * @param string $taxonomy           The taxonomy.
     * @param bool   $empty_choice       If this is an empty choice or not.
     * @param string $empty_choice_label The label for empty choices.
     * @param int    $max_cat           The maximum number of tags to return.
     * @return array
     */
    function fusion_builder_shortcodes_categories($taxonomy, $empty_choice = \false, $empty_choice_label = \false, $max_cat = 0)
    {
    }
    /**
     * Auto activate Avada Builder element. To be used by addon plugins.
     *
     * @since 1.0.4
     * @param string $shortcode Shortcode tag.
     */
    function fusion_builder_auto_activate_element($shortcode)
    {
    }
    /**
     * Merges the front-end editor data into map.
     *
     * @since 2.0
     * @param  string $class_name class for shortcode.
     * @param  array  $map     Array map for shortcode.
     * @param  string $context Parent or child level.
     * @return array
     */
    function fusion_builder_frontend_data($class_name, $map, $context = '')
    {
    }
    /**
     * Checks if on an editor page.
     *
     * @since 2.0
     * @return boolean Whether or not it is a fusion editor page.
     */
    function is_fusion_editor()
    {
    }
}
namespace {
    \define('FUSION_BUILDER_VERSION', '3.11.7');
}
