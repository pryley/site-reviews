<?php

namespace Bricks {
    // Exit if accessed directly
    /**
     * Convert Gutenberg blocks to Bricks elements and vice versa
     */
    class Blocks
    {
        /**
         * In order to convert Gutenberg content into a flat Bricks data, this object needs to be instantiated
         */
        public function __construct()
        {
        }
        /**
         * Load post gutenberg blocks
         *
         * @param int $post_id
         */
        public static function load_blocks($post_id)
        {
        }
        /**
         * Prepare Bricks elements instances that are possible to be converted
         */
        public static function load_elements()
        {
        }
        /**
         * Convert gutenberg post content into bricks data
         *
         * @param int $post_id
         */
        public function convert_blocks_to_bricks($post_id)
        {
        }
        /**
         * Convert Gutenberg block to Bricks element
         *
         * To populate Bricks with existing Gutenberg blocks.
         *
         * Supported blocks (Gutenberg blockName > Bricks element['name']):
         * - core/columns, core/buttons, core/group > container
         * - core/heading       > heading
         * - core/paragraph     > text
         * - core/list          > text
         * - core/buttons       > button
         * - core/image         > image
         * - core/html          > html
         * - core/code          > code
         * - core/preformatted  > code
         * - core/video         > video
         * - core-embed/youtube > video
         * - core-embed/vimeo   > video
         * - core/audio         > audio
         * - core/shortcode     > shortcode
         * - core/search        > search
         */
        public function convert_block_to_element($block, $parent_id = 0)
        {
        }
        /**
         * Add common block settings to Bricks data
         *
         * @param array $settings Bricks element settings.
         * @param array $attributes GT block attributes.
         *
         * @return array
         */
        public function add_common_block_settings($settings, $attributes)
        {
        }
        /**
         * Generate blocks HTML string from Bricks content elements (to store as post_content)
         *
         * @param array $elements Array of all Bricks elements on a section.
         * @param int   $post_id The post ID.
         *
         * @return string
         *
         * @since 1.0
         */
        public static function serialize_bricks_to_blocks($elements, $post_id)
        {
        }
    }
    // Exit if accessed directly
    class Maintenance
    {
        public function __construct()
        {
        }
        /**
         * Get the current maintenance mode
         */
        public static function get_mode()
        {
        }
        /**
         * Initialize and return the Maintenance class
         */
        public static function get_instance()
        {
        }
        /**
         * Determine whether or not to enforce maintenance mode
         */
        public function apply_maintenance_mode()
        {
        }
        /**
         * Flag whether or not a user custom template should be used
         * After checking user capabilities and maintenance mode
         * Should use only after 'wp' action 'apply_maintenance_mode' method
         *
         * @since 1.9.5
         */
        public static function use_custom_template()
        {
        }
        public function set_user_maintenance_template($active_templates)
        {
        }
        public function add_maintenance_mode_indicator_to_admin_bar($admin_bar)
        {
        }
    }
    // Exit if accessed directly
    abstract class Settings_Base
    {
        public $setting_type;
        // page, template
        public $controls;
        public $control_groups;
        public function __construct($type = '')
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function get_controls()
        {
        }
        public function get_control_groups()
        {
        }
        /**
         * Get all controls data (controls and control_groups)
         *
         * @since 1.0
         */
        public function get_controls_data()
        {
        }
    }
    // Exit if accessed directly
    class Settings_Page extends \Bricks\Settings_Base
    {
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
    }
    // Exit if accessed directly
    class Settings_Template extends \Bricks\Settings_Base
    {
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
    }
    // Exit if accessed directly
    class Theme
    {
        public $capabilities;
        public $database;
        public $helpers;
        public $cli;
        public $breakpoints;
        public $blocks;
        public $revisions;
        public $license;
        public $theme_styles;
        public $custom_fonts;
        public $settings;
        public $setup;
        public $search;
        public $ajax;
        public $svg;
        public $templates;
        public $heartbeat;
        public $converter;
        public $maintenance;
        public $admin;
        public $feedback;
        public $api;
        public $elements;
        public $woocommerce;
        public $polylang;
        public $integrations_form;
        public $wpml;
        public $instagram;
        public $rank_math;
        public $yoast;
        public $builder;
        public $frontend;
        public $assets;
        public $interactions;
        public $popups;
        public $conditions;
        public $auth_redirects;
        public $query_filters;
        public $query_filters_indexer;
        public $query_filters_fields;
        /**
         * The one and only Theme instance
         *
         * @var Theme
         */
        public static $instance = null;
        /**
         * Autoload and init components
         */
        public function __construct()
        {
        }
        /**
         * Init components
         */
        public function init()
        {
        }
        /**
         * Main Theme instance
         *
         * Ensure only one instance of Theme exists at any given time.
         *
         * @return object Theme The one and only Theme instance
         */
        public static function instance()
        {
        }
    }
    // Exit if accessed Woocommerce_directly
    class Woocommerce_Theme_Styles
    {
        public function __construct()
        {
        }
        /**
         * Add Woo Theme style control groups
         */
        public function set_groups($control_groups)
        {
        }
        /**
         * Add Woo Theme style controls
         */
        public function set_controls($controls)
        {
        }
    }
    // Exit if accessed directly
    abstract class Element
    {
        /**
         * Gutenberg block name: 'core/heading', etc.
         *
         * Mapping of Gutenberg block to Bricks element to load block post_content in Bricks and save Bricks data as WordPress post_content.
         */
        public $block = null;
        // Builder
        public $element;
        public $category;
        public $name;
        public $label;
        public $keywords;
        public $icon;
        public $controls;
        public $control_groups;
        public $control_options;
        public $css_selector;
        public $scripts = [];
        public $post_id = 0;
        public $draggable = true;
        // false to prevent dragging over entire element in builder
        public $deprecated = false;
        // true to hide element in panel (editing of existing deprecated element still works)
        public $panel_condition = [];
        // array conditions to show the element in the panel
        // Frontend
        public $id;
        public $tag = 'div';
        public $attributes = [];
        public $settings;
        public $theme_styles = [];
        public $is_frontend = false;
        /**
         * Custom attributes
         *
         * true: renders custom attributes on element '_root' (= default)
         * false: handle custom attributes in element render_attributes( 'xxx', true ) function (e.g. Nav Menu)
         *
         * @since 1.3
         */
        public $custom_attributes = true;
        /**
         * Nestable elements
         *
         * @since 1.5
         */
        public $nestable = false;
        // true to allow to insert child elements (e.g. Container, Div)
        public $nestable_item;
        // First child of nestable element (Use as blueprint for nestable children & when adding repeater item)
        public $nestable_children;
        // Array of children elements that are added inside nestable element when it's added to the canvas.
        public $nestable_html = '';
        // Nestable HTML with placeholder for element 'children'
        public $vue_component;
        // Set specific Vue component to render element in builder (e.g. 'bricks-nestable' for Section, Container, Div)
        public $original_query = '';
        public $support_masonry = false;
        // @since 1.11.1
        public function __construct($element = null)
        {
        }
        /**
         * Populate element data (when element is requested)
         *
         * Builder: Load all elements
         * Frontend: Load only requested elements
         *
         * @since 1.0
         */
        public function load()
        {
        }
        /**
         * Add element-specific WordPress actions to run in constructor
         *
         * @since 1.0
         */
        public function add_actions()
        {
        }
        /**
         * Add element-specific WordPress filters to run in constructor
         *
         * E.g. 'nav_menu_item_title' filter in Element_Nav_Menu
         *
         * @since 1.0
         */
        public function add_filters()
        {
        }
        /**
         * Set default CSS selector of each control with 'css' property
         *
         * To target specific element child tag (such as 'a' in 'button' etc.)
         * Avoids having to set CSS selector manually for each element control.
         *
         * @since 1.0
         */
        public function set_css_selector($custom_css_selector)
        {
        }
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        /**
         * Return element tag
         *
         * Default: 'div'
         * Next:    $tag set in theme styles
         * Last:    $tag set in element settings
         *
         * Custom tag: Check element 'tag' and 'customTag' settings.
         *
         * @since 1.4
         */
        public function get_tag()
        {
        }
        /**
         * Element-specific control groups
         *
         * @since 1.0
         */
        public function set_control_groups()
        {
        }
        /**
         * Element-specific controls
         *
         * @since 1.0
         */
        public function set_controls()
        {
        }
        /**
         * Control groups used by all elements under 'style' tab
         *
         * @since 1.0
         */
        public function set_common_control_groups()
        {
        }
        /**
         * Controls used by all elements under 'style' tab
         *
         * @since 1.0
         */
        public function set_controls_before()
        {
        }
        /**
         * Controls used by all elements under 'style' tab
         *
         * @since 1.0
         */
        public function set_controls_after()
        {
        }
        /**
         * Builder: Helper function to get HTML tag validation rules
         *
         * @since 1.10.3
         */
        public function get_in_builder_html_tag_validation_rules()
        {
        }
        /**
         * Get default data
         *
         * @since 1.0
         */
        public function get_default_data()
        {
        }
        /**
         * Builder: Element placeholder HTML
         *
         * @since 1.0
         */
        public final function render_element_placeholder($data = [], $type = 'info')
        {
        }
        /**
         * Return element attribute: id
         *
         * @since 1.5
         *
         * @since 1.7.1: Parse dynamic data for _cssId (same for _cssClasses)
         */
        public function get_element_attribute_id()
        {
        }
        /**
         * Set element root attributes (element ID, classes, etc.)
         *
         * @since 1.4
         */
        public function set_root_attributes()
        {
        }
        /**
         * Return true if element has 'css' settings
         *
         * @return boolean
         *
         * @since 1.5
         */
        public function has_css_settings($settings)
        {
        }
        /**
         * Convert the global classes ids into the classes names
         *
         * @param array $class_ids The global classes ids.
         *
         * @return array
         */
        public static function get_element_global_classes($class_ids)
        {
        }
        /**
         * Set HTML element attribute + value(s)
         *
         * @param string       $key         Element identifier.
         * @param string       $attribute   Attribute to set value(s) for.
         * @param string|array $value       Set single value (string) or values (array).
         *
         * @since 1.0
         */
        public function set_attribute($key, $attribute, $value = null)
        {
        }
        /**
         * Set link attributes
         *
         * Helper to set attributes for control type 'link'
         *
         * @since 1.0
         *
         * @param string $attribute_key Desired key for set_attribute.
         * @param string $link_settings Element control type 'link' settings.
         */
        public function set_link_attributes($attribute_key, $link_settings)
        {
        }
        /**
         * Maybe set aria-current="page" attribute to the link if it points to the current page.
         *
         * Example: nav-nested active nav item background color.
         *
         * NOTE: url_to_postid() returns 0 if URL contains the port like https://bricks.local:49581/blog/
         *
         * @since 1.8
         * @since 1.11: Add 'taxonomy' link type.
         */
        public function maybe_set_aria_current($link_settings, $attribute_key)
        {
        }
        /**
         * Remove attribute
         *
         * @param string      $key        Element identifier.
         * @param string      $attribute  Attribute to remove.
         * @param string|null $value Set to remove single value instead of entire attribute.
         *
         * @since 1.0
         */
        public function remove_attribute($key, $attribute, $value = null)
        {
        }
        /**
         * Render HTML attributes for specific element
         *
         * @param string  $key                   Attribute identifier.
         * @param boolean $add_custom_attributes true to get custom atts for elements where we don't add them to the wrapper (Nav Menu).
         *
         * @since 1.0
         */
        public function render_attributes($key, $add_custom_attributes = false)
        {
        }
        /**
         * Calculate element custom attributes based on settings (dynamic data too)
         *
         * @since 1.3
         */
        public function get_custom_attributes($settings = [])
        {
        }
        public static function stringify_attributes($attributes = [])
        {
        }
        /**
         * Enqueue element-specific styles and scripts
         *
         * @since 1.0
         */
        public function enqueue_scripts()
        {
        }
        /**
         * Element HTML render
         *
         * @since 1.0
         */
        public function render()
        {
        }
        /**
         * Element HTML render in builder via x-template
         *
         * @since 1.0
         */
        public static function render_builder()
        {
        }
        /**
         * Builder: Get nestable item
         *
         * Use as blueprint for nestable children & when adding repeater item.
         *
         * @since 1.5
         */
        public function get_nestable_item()
        {
        }
        /**
         * Builder: Array of child elements added when inserting new nestable element
         *
         * @since 1.5
         */
        public function get_nestable_children()
        {
        }
        /**
         * Frontend: Lazy load (images, videos)
         *
         * Global settings 'disableLazyLoad': Disable lazy load altogether
         * Page settings 'disableLazyLoad': Disable lazy load on this page (@since 1.8.6)
         * Element settings 'disableLazyLoad': Carousel, slider, testimonials (= bricksSwiper) (@since 1.4)
         *
         * @since 1.0
         */
        public function lazy_load()
        {
        }
        /**
         * Enqueue element scripts & styles, set attributes, render
         *
         * @since 1.0
         */
        public function init()
        {
        }
        /**
         * Calculate column width
         */
        public function calc_column_width($columns_count = 1, $max = false)
        {
        }
        /**
         * Column width calculator
         *
         * @param int $columns Number of columns.
         * @param int $count   Total amount of items.
         */
        public function column_width($columns, $count)
        {
        }
        /**
         * Post fields
         *
         * Shared between elements: Carousel, Posts, Products, etc.
         *
         * @since 1.0
         */
        public function get_post_fields()
        {
        }
        /**
         * Post content
         *
         * Shared between elements: Carousel, Posts
         *
         * @since 1.0
         */
        public function get_post_content()
        {
        }
        /**
         * Post overlay
         *
         * Shared between elements: Carousel, Posts
         *
         * @since 1.0
         */
        public function get_post_overlay()
        {
        }
        /**
         * Get swiper controls
         *
         * Elements: Carousel, Slider, Team Members.
         *
         * @since 1.0
         */
        public static function get_swiper_controls()
        {
        }
        /**
         * Render swiper nav: Navigation (arrows) & pagination (dots)
         *
         * Elements: Carousel, Slider, Team Members.
         *
         * @param array $options SwiperJS options.
         *
         * @since 1.4
         */
        public function render_swiper_nav($options = false)
        {
        }
        /**
         * Custom loop builder controls
         *
         * Shared between Container, Template, ...
         *
         * @since 1.3.7
         */
        public function get_loop_builder_controls($group = '')
        {
        }
        /**
         * Render the query loop trail
         *
         * Trail enables infinite scroll
         *
         * @since 1.5
         *
         * @param Query  $query    The query object.
         * @param string $node_key The element key to add the query data attributes (used in the posts element).
         *
         * @return string
         */
        public function render_query_loop_trail($query, $node_key = '')
        {
        }
        /**
         * Get the dynamic data for a specific tag
         *
         * @param string $tag Dynamic data tag.
         * @param string $context text, image, media, link.
         * @param array  $args Needed to set size for avatar image.
         * @param string $post_id Post ID.
         *
         * @return mixed
         */
        public function render_dynamic_data_tag($tag = '', $context = 'text', $args = [], $post_id = 0)
        {
        }
        /**
         * Render dynamic data tags on a string
         *
         * @param string $content
         *
         * @return mixed
         */
        public function render_dynamic_data($content = '')
        {
        }
        /**
         * Set Post ID
         *
         * @param int $post_id
         *
         * @return void
         */
        public function set_post_id($post_id = 0)
        {
        }
        /**
         * Setup query for templates according to 'templatePreviewType'
         *
         * To alter builder template and template preview query. NOT the frontend!
         *
         * 1. Set element $post_id
         * 2. Populate query_args from"Populate content" settings and set it to global $wp_query
         *
         * @param integer $post_id
         *
         * @since 1.0
         */
        public function setup_query($post_id = 0)
        {
        }
        /**
         * Restore custom query after element render()
         *
         * @since 1.0
         */
        public function restore_query()
        {
        }
        /**
         * Render control 'icon' HTML (either font icon 'i' or 'svg' HTML)
         *
         * @param array $icon Contains either 'icon' CSS class or 'svg' URL data.
         * @param array $attributes Additional icon HTML attributes.
         *
         * @see ControlIcon.vue
         * @return string SVG HMTL string
         *
         * @since 1.2.1
         */
        public static function render_icon($icon, $attributes = [])
        {
        }
        /**
         * Add attributes to SVG HTML string
         *
         * @since 1.4
         */
        public static function render_svg($svg = '', $attributes = [])
        {
        }
        /**
         * Change query if we are previewing a CPT archive template (set in-builder via "Populated Content")
         *
         * @since 1.4
         */
        public function maybe_set_preview_query($query_vars, $settings, $element_id)
        {
        }
        /**
         * Is layout element: Section, Container, Block, Div
         *
         * For element control visibility in builder (flex controls, shape divider, etc.)
         *
         * @return boolean
         *
         * @since 1.5
         */
        public function is_layout_element()
        {
        }
        /**
         * Generate breakpoint-specific @media rules for nav menu & mobile menu toggle
         *
         * If not set to 'always' or 'never'
         *
         * @since 1.5.1
         */
        public function generate_mobile_menu_inline_css($settings = [], $breakpoint = '')
        {
        }
        /**
         * Return true if any of the element classes contains a match
         *
         * @param array $values_to_check Array of values to check the global class settings for.
         *
         * @see image.php 'popupOverlay', video.php 'overlay', etc.
         *
         * @since 1.7.1
         */
        public function element_classes_have($values_to_check = [])
        {
        }
        /**
         * Enqueue Masonry scripts
         *
         * @since 1.11.1
         */
        public function maybe_enqueue_masonry_scripts()
        {
        }
        /**
         * Support masonry layout
         *
         * @since 1.11.1
         */
        public function support_masonry_element()
        {
        }
        public function enabled_masonry()
        {
        }
        public function maybe_masonry_trail_nodes()
        {
        }
    }
    // Exit if accessed directly
    class Product_Additional_Information extends \Bricks\Element
    {
        public $category = 'woocommerce_product';
        public $name = 'product-additional-information';
        public $icon = 'ti-info';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Product_Add_To_Cart extends \Bricks\Element
    {
        public $category = 'woocommerce_product';
        public $name = 'product-add-to-cart';
        public $icon = 'ti-shopping-cart';
        public function enqueue_scripts()
        {
        }
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        /**
         * Add custom text and/or icon to the button
         *
         * @param string     $text
         * @param WC_Product $product
         *
         * @since 1.6
         */
        public function add_to_cart_text($text, $product)
        {
        }
        /**
         * TODO: Needs description
         *
         * @since 1.6
         */
        public function avoid_esc_html($safe_text, $text)
        {
        }
        /**
         * Set AJAX add to cart data attribute: data-bricks-ajax-add-to-cart
         *
         * @since 1.6.1
         */
        public function maybe_set_ajax_add_to_cart_data_attribute()
        {
        }
    }
    // Exit if accessed directly
    class Product_Gallery extends \Bricks\Element
    {
        public $category = 'woocommerce_product';
        public $name = 'product-gallery';
        public $icon = 'ti-gallery';
        public $scripts = ['bricksWooProductGallery'];
        public $product = false;
        public function enqueue_scripts()
        {
        }
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        /**
         * Get product gallery HTML
         *
         * @since 1.9
         * @return string
         */
        public function product_gallery_html()
        {
        }
        /**
         * Render Bricks product gallery thumbnails
         *
         * @since 1.9
         *
         * @return string
         */
        public function bricks_product_gallery_thumbnails()
        {
        }
        /**
         * Set gallery image size for the current product gallery
         *
         * hook: woocommerce_gallery_image_size
         *
         * @see woocommerce/includes/wc-template-functions.php
         *
         * @since 1.8
         */
        public function set_gallery_image_size($size)
        {
        }
        /**
         * Set gallery thumbnail size for the current product gallery
         *
         * hook: woocommerce_gallery_thumbnail_size
         *
         * @see woocommerce/includes/wc-template-functions.php
         *
         * @since 1.8
         */
        public function set_gallery_thumbnail_size($size)
        {
        }
        /**
         * Set gallery full size for the current product gallery (Lightbox)
         *
         * hook: woocommerce_gallery_full_size
         *
         * @see woocommerce/includes/wc-template-functions.php
         *
         * @since 1.8
         */
        public function set_gallery_full_size($size)
        {
        }
        public function add_image_class_prevent_lazy_loading($attr, $attachment_id, $image_size, $main_image)
        {
        }
    }
    // Exit if accessed directly
    class Product_Price extends \Bricks\Element
    {
        public $category = 'woocommerce_product';
        public $name = 'product-price';
        public $icon = 'ti-money';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Products_Archive_Description extends \Bricks\Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-products-archive-description';
        public $icon = 'ti-wordpress';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woo_Element extends \Bricks\Element
    {
        public $category = 'woocommerce';
        /**
         * Generate standard controls for:
         * margin, padding, background-color, border, box-shadow, typography
         *
         * @param string $field_key - The field key to use for the control.
         * @param string $selector - The selector to apply the control to.
         * @param string $types (optional) - Array of control types to generate controls for.
         *
         * @return array
         */
        protected function generate_standard_controls($field_key, $selector, $types = [])
        {
        }
        /**
         * Insert group key to controls
         *
         * @param array  $controls
         * @param string $group
         *
         * @return array
         */
        protected function controls_grouping($controls, $group)
        {
        }
        /**
         * Woo Phase 3
         */
        protected function get_woo_form_fields_controls($selector = '')
        {
        }
        /**
         * Woo Phase 3
         */
        protected function get_woo_form_submit_controls()
        {
        }
        protected function get_woo_form_fieldset_controls()
        {
        }
        /**
         * Get order
         *
         * Get order from 'previewOrderId' setting
         *
         * Default: Last order
         *
         * @return WC_Order|false
         */
        protected function get_order($template = 'view-order')
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Account_Addresses extends \Bricks\Woo_Element
    {
        public $name = 'woocommerce-account-addresses';
        public $icon = 'fa fa-address-book';
        public $panel_condition = ['templateType', '=', 'wc_account_addresses'];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Product_Upsells extends \Bricks\Element
    {
        public $category = 'woocommerce_product';
        public $name = 'product-upsells';
        public $icon = 'ti-stats-up';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public function render_heading($heading = '')
        {
        }
        /**
         * Output cart cross-sells
         *
         * NOTE: Similar to original function but here to make sure it runs outside the checkout page and with product cross sells with cart empty.
         *
         * @see woocommerce/includes/wc-template-functions.php
         *
         * @param  array  $product_ids Array of product IDs.
         * @param  int    $limit (default: 2).
         * @param  int    $columns (default: 2).
         * @param  string $orderby (default: 'rand').
         * @param  string $order (default: 'desc').
         *
         * @since 1.4
         */
        public function woocommerce_cross_sell_display($product_ids = [], $limit = 2, $columns = 2, $orderby = 'rand', $order = 'desc')
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Account_Orders extends \Bricks\Woo_Element
    {
        public $name = 'woocommerce-account-orders';
        public $icon = 'ti-layout-list-thumb-alt';
        public $panel_condition = ['templateType', '=', 'wc_account_orders'];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Cart_Collaterals extends \Bricks\Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-cart-collaterals';
        public $icon = 'ti-money';
        public $panel_condition = ['templateType', '=', ['wc_cart', 'wc_cart_empty']];
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public function proceed_to_checkout_button($label)
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Cart_Items extends \Bricks\Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-cart-items';
        public $icon = 'ti-shopping-cart';
        public $panel_condition = ['templateType', '=', 'wc_cart'];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public function woocommerce_cart_item_thumbnail($thumbnail, $cart_item, $cart_item_key)
        {
        }
        public function woocommerce_cart_item_permalink($permalink, $cart_item, $cart_item_key)
        {
        }
    }
    // Exit if accessed directly
    class Product_Rating extends \Bricks\Element
    {
        public $category = 'woocommerce_product';
        public $name = 'product-rating';
        public $icon = 'ti-medall';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Product_Meta extends \Bricks\Element
    {
        public $category = 'woocommerce_product';
        public $name = 'product-meta';
        public $icon = 'ti-receipt';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Products_Orderby extends \Bricks\Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-products-orderby';
        public $icon = 'ti-exchange-vertical';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public function woocommerce_catalog_orderby($orderby)
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Account_Form_Reset_Password extends \Bricks\Woo_Element
    {
        public $name = 'woocommerce-account-form-reset-password';
        public $icon = 'fas fa-key';
        public $panel_condition = ['templateType', '=', 'wc_account_reset_password'];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Account_Page extends \Bricks\Woo_Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-account-page';
        public $icon = 'ti-user';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Product_Content extends \Bricks\Element
    {
        public $category = 'woocommerce_product';
        public $name = 'product-content';
        public $icon = 'ti-wordpress';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Product_Tabs extends \Bricks\Element
    {
        public $category = 'woocommerce_product';
        public $name = 'product-tabs';
        public $icon = 'ti-layout-tab';
        public $css_selector = '.woocommerce-tabs';
        public $rerender = false;
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Account_Form_Login extends \Bricks\Woo_Element
    {
        public $name = 'woocommerce-account-form-login';
        public $icon = 'fa fa-address-card';
        public $panel_condition = ['templateType', '=', 'wc_account_form_login'];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        /**
         * NOTE: Not in use as impossible to render only login or register form inside Woo template
         */
        public function __render()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Checkout_Order_Review extends \Bricks\Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-checkout-order-review';
        public $icon = 'ti-view-list-alt';
        public $panel_condition = ['templateType', '=', 'wc_form_checkout'];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Checkout_Coupon extends \Bricks\Woo_Element
    {
        public $name = 'woocommerce-checkout-coupon';
        public $icon = 'ti-ticket';
        // NOTE: Don't limit to Checkout template only as user might add it on the Checkout page directly, outside the checkout form
        // public $panel_condition = [ 'templateType', '=', 'wc_form_checkout' ];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Products_Filters extends \Bricks\Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-products-filter';
        public $icon = 'ti-filter';
        public $scripts = ['bricksWooProductsFilter'];
        // Helper property
        public $products_element;
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public function render_control_dropdown($filter, $filter_by)
        {
        }
        public function render_control_radio($filter, $filter_by)
        {
        }
        public function render_control_checkbox($filter, $filter_by)
        {
        }
        public function render_control_list($filter, $filter_by)
        {
        }
        public function render_control_box($filter, $filter_by)
        {
        }
        public function render_control_stars($filter, $filter_by)
        {
        }
        public function render_control_slider($filter, $filter_by)
        {
        }
        public function render_control_reset($filter)
        {
        }
        public function render_control_search($filter, $filter_by)
        {
        }
        public function get_filters_list($filter_type)
        {
        }
        public function get_filter_options($filter, $filter_by)
        {
        }
        /**
         * If the products element is filtering the main query, return those specific terms
         *
         * @return array
         */
        public function get_terms_include($taxonomy)
        {
        }
        /**
         * Helper method to get the tax_query terms per taxonomy
         *
         * @since 1.5
         */
        public function get_tax_query_values($condition, $key, $tax_values)
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Checkout_Customer_Details extends \Bricks\Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-checkout-customer-details';
        public $icon = 'ti-user';
        public $panel_condition = ['templateType', '=', 'wc_form_checkout'];
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public function remove_checkout_billing_fields($field, $key)
        {
        }
        public function remove_checkout_shipping_fields($field, $key)
        {
        }
        public function woocommerce_form_field_args($args, $key, $value)
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Products_Pagination extends \Bricks\Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-products-pagination';
        public $icon = 'ti-angle-double-right';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public function woocommerce_pagination_args($args)
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Checkout_Order_Payment extends \Bricks\Woo_Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-checkout-order-payment';
        public $icon = 'ti-menu-alt';
        public $panel_condition = ['templateType', '=', 'wc_form_pay'];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Checkout_Login extends \Bricks\Woo_Element
    {
        public $name = 'woocommerce-checkout-login';
        public $icon = 'fa fa-address-card';
        // Not limit to Checkout template only, user might use this on Checkout page directly (outside of checkout form)
        // public $panel_condition = [ 'templateType', '=', 'wc_form_checkout' ];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Account_Form_Edit_Account extends \Bricks\Woo_Element
    {
        public $name = 'woocommerce-account-form-edit-account';
        public $icon = 'fas fa-user-edit';
        public $panel_condition = ['templateType', '=', 'wc_account_form_edit_account'];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Cart_Coupon extends \Bricks\Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-cart-coupon';
        public $icon = 'ti-ticket';
        public $panel_condition = ['templateType', '=', 'wc_cart'];
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Product_Related extends \Bricks\Element
    {
        public $category = 'woocommerce_product';
        public $name = 'product-related';
        public $icon = 'ti-layers';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public function render_heading($heading = '')
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Checkout_Order_Table extends \Bricks\Woo_Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-checkout-order-table';
        public $icon = 'ti-menu-alt';
        public $panel_condition = ['templateType', '=', 'wc_form_pay'];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Checkout_Thankyou extends \Bricks\Woo_Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-checkout-thankyou';
        public $icon = 'ti-check-box';
        public $panel_condition = ['templateType', '=', 'wc_thankyou'];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Mini_Cart extends \Bricks\Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-mini-cart';
        public $icon = 'ti-shopping-cart';
        public $scripts = ['bricksWooRefreshCartFragments'];
        /**
         * Enqueue wc-cart-fragments script if WooCommerce version is >= 7.8
         *
         * @since 1.8.1
         *
         * @see https://developer.woocommerce.com/2023/06/13/woocommerce-7-8-released/#mini-cart-performance-improvement
         */
        public function enqueue_scripts()
        {
        }
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        /**
         * NOTE: Not in use in order to show .cart-detail (fragments)
         */
        public static function not_in_use_render_builder()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Account_Downloads extends \Bricks\Woo_Element
    {
        public $name = 'woocommerce-account-downloads';
        public $icon = 'ti-download';
        public $panel_condition = ['templateType', '=', 'wc_account_downloads'];
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Products_Total_Results extends \Bricks\Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-products-total-results';
        public $icon = 'ti-info';
        public function get_label()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Template_Hook extends \Bricks\Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-template-hook';
        public $icon = 'fas fa-anchor';
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Product_Title extends \Bricks\Element
    {
        public $category = 'woocommerce_product';
        public $name = 'product-title';
        public $icon = 'ti-text';
        public $tag = 'h1';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public static function render_builder()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Account_View_Order extends \Bricks\Woo_Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-account-view-order';
        public $icon = 'ti-layout-list-thumb';
        public $panel_condition = ['templateType', '=', 'wc_account_view_order'];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Account_Form_Edit_Address extends \Bricks\Woo_Element
    {
        public $name = 'woocommerce-account-form-edit-address';
        public $icon = 'ti ti-pencil-alt';
        public $panel_condition = ['templateType', '=', 'wc_account_form_edit_address'];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Breadcrumbs extends \Bricks\Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-breadcrumbs';
        public $icon = 'ti-line-dashed';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        /**
         * Custom home URL: 'woocommerce_breadcrumb_home_url' filter callback
         *
         * @since 1.10.1
         */
        public function custom_home_url($url)
        {
        }
    }
    // Exit if accessed directly
    class Product_Stock extends \Bricks\Element
    {
        public $category = 'woocommerce_product';
        public $name = 'product-stock';
        public $icon = 'ti-package';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public function woocommerce_get_availability($availability, $product)
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Account_Form_Register extends \Bricks\Woo_Element
    {
        public $name = 'woocommerce-account-form-register';
        public $icon = 'fas fa-user-plus';
        public $panel_condition = ['templateType', '=', 'wc_account_form_login'];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Product_Short_Description extends \Bricks\Element
    {
        public $category = 'woocommerce_product';
        public $name = 'product-short-description';
        public $icon = 'ti-paragraph';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Custom_Render_Element extends \Bricks\Element
    {
        /**
         * Set Bricks query instance
         *
         * @param Bricks\Query $bricks_query
         */
        public function set_bricks_query($bricks_query)
        {
        }
        /**
         * Start the iteration
         *
         * @see includes/query.php render() method
         */
        public function start_iteration()
        {
        }
        /**
         * Set the loop object for the current iteration.
         *
         * @param object $object
         */
        public function set_loop_object($object)
        {
        }
        /**
         * Move to the next iteration
         */
        public function next_iteration()
        {
        }
        /**
         * End the iteration
         */
        public function end_iteration()
        {
        }
        /**
         * Set loop object type to 'post'
         * Posts element, Carousel element, Products element, Related Posts element are supported post loop only.
         */
        public function set_loop_object_type($object_type, $object, $query_id)
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Products extends \Bricks\Custom_Render_Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-products';
        public $icon = 'ti-archive';
        public $css_selector = '.product';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render_grid_widgets($zone)
        {
        }
        public function woocommerce_catalog_orderby($orderby)
        {
        }
        public function render()
        {
        }
        public function render_fields($image_classes, $post, $post_index)
        {
        }
    }
    // Exit if accessed directly
    class Product_Reviews extends \Bricks\Element
    {
        public $category = 'woocommerce_product';
        public $name = 'product-reviews';
        public $icon = 'ti-pencil-alt';
        public $scripts = ['bricksWooStarRating'];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Account_Form_Lost_Password extends \Bricks\Woo_Element
    {
        public $name = 'woocommerce-account-form-lost-password';
        public $icon = 'fas fa-passport';
        public $panel_condition = ['templateType', '=', 'wc_account_form_lost_password'];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Notice extends \Bricks\Element
    {
        public $category = 'woocommerce';
        public $name = 'woocommerce-notice';
        public $icon = 'ti-announcement';
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        /**
         * Populate some notices for the builder and template preview or return the WooCommerce notices
         *
         * @return string
         */
        public function get_woo_notices_or_populate_builder_notices()
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce_Helpers
    {
        /**
         * Product query controls (products, related products, upsells)
         *
         * @param array $args Arguments to merge (e.g. control 'group').
         */
        public static function get_product_query_controls($args = false)
        {
        }
        /**
         * Default order by control options
         */
        public static function get_default_orderby_control_options()
        {
        }
        public static function get_filters_list($flat = true)
        {
        }
        /**
         * Is product archive page
         *
         * @return boolean
         */
        public static function is_archive_product()
        {
        }
        /**
         * Calculate the filters query args based on the URL parameters and element settings
         * DO NOT early return!
         * WooCommerce query
         *
         * https://github.com/woocommerce/woocommerce/wiki/wc_get_products-and-WC_Product_Query
         * https://docs.woocommerce.com/wc-apidocs/class-WC_Product.html
         *
         * @since 1.5
         *
         * @param array  $settings The element settings.
         * @param array  $bricks_query_var Generated query_vars from Bricks_Query.
         * @param string $element_name The element name.
         * @return array
         */
        public static function filters_query_args($settings, $bricks_query_var = [], $element_name = '')
        {
        }
        /**
         * Set query args for price filter
         *
         * @param array $args
         * @return array
         */
        public static function set_price_query_args($args)
        {
        }
        /**
         * Gets the first element from a flat list that contains a products query (Products element or Query Loop builder set to products)
         *
         * @since 1.5
         *
         * @param array $data
         * @return array|boolean
         */
        public static function get_products_element($data = [])
        {
        }
        /**
         * Get the products query based on a Products element present in the content of a page
         *
         * @param string $post_id
         * @return WP_Query|boolean false if products element not found
         */
        public static function get_products_element_query($post_id)
        {
        }
        /**
         * Helper function to set the cart variables for better builder preview
         *
         * @return void
         */
        public static function maybe_init_cart_context()
        {
        }
        /**
         * Maybe add products to the cart if cart is empty for better builder preview
         *
         * @since 1.5
         *
         * @return void
         */
        public static function maybe_populate_cart_contents()
        {
        }
        /**
         * Maybe load the cart - render using WP REST API
         *
         * @since 1.5
         */
        public static function maybe_load_cart()
        {
        }
        /**
         * Add or remove actions in the repeated_wc_template_hooks
         *
         * Used in {do_action} which the action is inside the repeated_wc_template_hooks hooks
         * To avoid duplicate ouput which already exists in Bricks elements
         *
         * @since 1.7
         *
         * @param string $template required (ex: 'content-single-product', 'content-product').
         * @param string $action remove, add.
         * @param string $hook optional.
         *
         * @return void
         */
        public static function execute_actions_in_wc_template($template = '', $action = 'remove', $hook = '')
        {
        }
        /**
         * All woo template hooks that might be causing duplicated ouput when using together with Bricks WooCommerce elements
         *
         * @see woocommerce/includes/wc-template-hooks.php
         *
         * @since 1.7
         *
         * @param string $template
         *
         * @return array
         */
        public static function repeated_wc_template_hooks($template = '')
        {
        }
        /**
         * Find the template hooks array by using the action name.
         *
         * @since 1.7
         *
         * @param string $action
         *
         * @return array
         */
        public static function get_repeated_wc_template_hooks_by_action($action = '')
        {
        }
        /**
         * Bricks helper function to render the product rating.
         * single-product/rating.php
         *
         * @param WC_Product $product Product instance.
         * @param array      $params  Keys: show_empty_stars, hide_reviews_link, $wrapper.
         * @param bool       $render  Render (echo) or return.
         *
         * @since 1.8
         */
        public static function render_product_rating($product = null, $params = [], $render = true)
        {
        }
        /**
         * Hooked to woocommerce_product_get_rating_html
         *
         * @since 1.8
         */
        public static function show_empty_stars($html, $rating, $count)
        {
        }
        /**
         * Get product stock amount value
         *
         * Previously in get_stock_html(), but refactored into a separate function for reusability and readability.
         * Bare in mind if the product is not managed stock, the value will be 0 even stock status is instock.
         *
         * @param \WC_Product $product
         * @return int
         *
         * @since 1.6.1
         * @since 1.11.1: Moved here from provider-woo.php.
         */
        public static function get_stock_amount($product)
        {
        }
        /**
         * Similar function to wc_format_stock_for_display but adapted to be possible to use the stock sum up of the product variations
         *
         * @since 1.5.7
         * @since 1.11.1: Moved here from provider-woo.php.
         */
        public static function format_stock_for_display($product, $stock_amount)
        {
        }
    }
    // Exit if accessed directly
    /**
     * Page settings
     * Template settings
     */
    class Settings
    {
        public static $controls = [];
        public function __construct()
        {
        }
        /**
         * Set settings controls when saving a Bricks template (on the quick edit interface)
         *
         * @since 1.5.6
         */
        public function set_controls_in_admin()
        {
        }
        public static function set_controls()
        {
        }
        /**
         * Get page/template controls data (controls and control groups)
         *
         * @param string $type page/template.
         */
        public static function get_controls_data($type = '')
        {
        }
    }
    // Exit if accessed directly
    class Templates
    {
        public static $template_images = [];
        // All template IDs used on requested URL (@since 1.8.1)
        public static $rendered_template_ids_on_page = [];
        // All generated inline CSS identifiers (@since 1.9.1)
        public static $generated_inline_identifier = [];
        public function __construct()
        {
        }
        /**
         * Register custom post types
         *
         * post_type: bricks_template
         * taxonomies: template_tag, template_bundle
         *
         * @since 1.0
         */
        public function register_post_type()
        {
        }
        /**
         * Render shortcode: [bricks_template]
         */
        public function render_shortcode($attributes = [])
        {
        }
        /**
         * Generate the inline CSS for template rendered in shortcode element
         */
        public static function generate_inline_css($template_id, $elements)
        {
        }
        /**
         * Keep the timestamp of the latest change in the templates post type to force the cache flush
         *
         * @param int $post_id Post ID.
         */
        public function flush_templates_cache($post_id)
        {
        }
        /**
         * Check if remote site can get templates
         *
         * @see Api::get_templates()
         * @return array Array with 'error' key on error. Array with 'site', 'password', 'licenseKey' on success.
         *
         * @since 1.0
         */
        public static function can_get_templates($parameters)
        {
        }
        /**
         * Create template
         *
         * @since 1.0
         */
        public static function get_remote_template_settings()
        {
        }
        /**
         * Builder templates: Get all remote templates data (templates, authors, bundles, tags)
         *
         * @return array
         *
         * @since 1.0
         */
        public static function get_remote_templates_data()
        {
        }
        /**
         * Get templates query based on custom args
         *
         * @since 1.0
         *
         * @param array $custom_args
         * @return WP_Query
         */
        public static function get_templates_query($custom_args = [])
        {
        }
        /**
         * Get all the template IDs of a specific type
         */
        public static function get_templates_by_type($template_type = '')
        {
        }
        /**
         * Get my templates
         *
         * @since 1.0
         */
        public static function get_templates($custom_args = [])
        {
        }
        /**
         * Get template authors
         *
         * @since 1.0
         *
         * @return array
         */
        public static function get_template_authors()
        {
        }
        /**
         * Get template bundles
         *
         * @since 1.0
         */
        public static function get_template_bundles()
        {
        }
        /**
         * Get template tags
         *
         * @since 1.0
         */
        public static function get_template_tags()
        {
        }
        /**
         * Get template type via post_meta
         *
         * @param int $post_id
         *
         * @since 1.0
         */
        public static function get_template_type($post_id = 0)
        {
        }
        /**
         * Get template by ID
         *
         * @since 1.0
         */
        public static function get_template_by_id($template_id, $custom_args = [])
        {
        }
        /**
         * Builder: Create template
         *
         * @since 1.0
         */
        public function create_template()
        {
        }
        /**
         * Builder: Save template
         *
         * @since 1.0
         */
        public function save_template()
        {
        }
        /**
         * Builder: Move template to trash
         *
         * @since 1.0
         */
        public function delete_template()
        {
        }
        /**
         * Admin & builder: Import template
         *
         * @since 1.0
         */
        public function import_template()
        {
        }
        /**
         * STEP: Check global class setting key for occurence of pseudo element to create pseudo element in local installtion
         *
         * @since 1.7.1
         */
        public static function template_import_create_missing_pseudo_classes($pseudo_classes, $setting_keys = [])
        {
        }
        /**
         * Export template as JSON file
         *
         * @param int $template_id Provided if bulk action export.
         * @see: admin.php:export_templates()
         * @since 1.0
         *
         * @return array
         */
        public static function export_template($template_id = 0)
        {
        }
        /**
         * Check if setting value has image/svg properties
         *
         * @since 1.3.2
         */
        public static function is_image($setting)
        {
        }
        /**
         * Recursive function: Import remote element images from template data
         *
         * @since 1.3.2
         */
        public static function import_images($settings, $import_images)
        {
        }
        public static function import_image($image, $import_images)
        {
        }
        /**
         * Builder: Convert template data to new container layout structure
         *
         * @since 1.2
         *
         * @return void
         */
        public function convert_template()
        {
        }
        /**
         * Get the Templates list for the Template element (for the moment only Section and Content/Single template types)
         */
        public static function get_templates_list($template_types = '', $exclude_template_id = '')
        {
        }
        /**
         * Get IDs of all templates
         *
         * @see admin.php get_converter_items()
         * @see files.php get_css_files_list()
         *
         * @param array $custom_args array Custom get_posts() arguments (@since 1.8; @see get_css_files_list).
         *
         * @since 1.4
         */
        public static function get_all_template_ids($custom_args = [])
        {
        }
        /**
         * Remove templates from /wp-sitemap.xml if not set to "Public templates" in Bricks settings
         *
         * @since 1.4
         */
        public function remove_templates_from_wp_sitemap($post_types)
        {
        }
        /**
         * Remove template taxonomies from /wp-sitemap.xml if not set to "Public templates" in Bricks settings
         *
         * @since 1.8
         */
        public function remove_template_taxonomies_from_wp_sitemap($taxonomies)
        {
        }
        /**
         * Frontend: Assign templates to hooks
         *
         * @since 1.9.1
         */
        public function assign_templates_to_hooks()
        {
        }
        /**
         * Check if template should be run on hook
         *
         * @since 1.9.2
         *
         * @param array $arranged_conditions
         *
         * @return bool
         */
        public static function run_template_on_hook($arranged_conditions = [])
        {
        }
    }
    // Exit if accessed directly
    class Query_Filters_Indexer
    {
        const INDEXER_OPTION_KEY = 'bricks_indexer_running';
        public function __construct()
        {
        }
        /**
         * Singleton - Get the instance of this class
         */
        public static function get_instance()
        {
        }
        // Register hooks
        public function register_hooks()
        {
        }
        /**
         * Add a new cron interval every 5 minutes
         */
        public function add_cron_interval($schedules)
        {
        }
        /**
         * Check if the indexer is running: To avoid multiple indexer and incorrect indexing
         */
        public function indexer_is_running()
        {
        }
        /**
         * Retrieve jobs from the database, and continue indexing them
         * Should be run every 5 minutes, might be triggered manually via do_action( 'bricks_indexer' )
         * Will not do anything if the indexer is already running to avoid multiple indexer and incorrect indexing
         */
        public function continue_index_jobs()
        {
        }
        /**
         * Trigger bricks_indexer action
         * Should be called via wp_remote_post
         */
        public function background_index_job()
        {
        }
        /**
         * Add index job for an element
         * Condition:
         * - If active job exists, do nothing
         */
        public function add_job($element, $remove_active_jobs = false)
        {
        }
        /**
         * Get active job for an element
         */
        public function get_active_job_for_element($filter_id)
        {
        }
        /**
         * Remove job
         */
        public function remove_job($job)
        {
        }
        /**
         * Remove all jobs
         *
         * @since 1.11
         */
        public function remove_all_jobs()
        {
        }
        /**
         * Get all jobs
         */
        public function get_jobs()
        {
        }
        /**
         * Get the progress text for the indexing process
         * - Use in the admin settings page
         */
        public function get_overall_progress()
        {
        }
        /**
         * Check if server resource limits are reached
         * Default: 85% memory usage, and 20s time usage
         * - Majority of servers have 30s time limit, save 10s for other processes
         */
        public static function resource_limit_reached()
        {
        }
        /**
         * Dispatch a background job (unblocking) to reindex query filters
         */
        public static function trigger_background_job()
        {
        }
    }
    // Exit if accessed directly
    class Ajax
    {
        public function __construct()
        {
        }
        /**
         * Check if current endpoint is Bricks AJAX endpoint
         *
         * @param string $action E.g. 'get_template_elements_by_id' or 'form_submit'
         *
         * @since 1.11
         *
         * @return bool
         */
        public static function is_current_endpoint($action)
        {
        }
        /**
         * Builder: Generate code signature
         *
         * @since 1.9.7
         */
        public function generate_code_signature()
        {
        }
        /**
         * Decode stringified JSON data
         *
         * @since 1.0
         */
        public static function decode($data, $run_wp_slash = true)
        {
        }
        /**
         * Form element: Regenerate nonce
         *
         * @since 1.9.6
         */
        public function regenerate_form_nonce()
        {
        }
        /**
         *
         * Query Sort/Filter: Regenerate nonce
         *
         * @since 1.11
         */
        public function regenerate_query_nonce()
        {
        }
        /**
         * Verify nonce (AJAX call)
         *
         * wp-admin: 'bricks-nonce-admin'
         * builder:  'bricks-nonce-builder'
         * frontend: 'bricks-nonce' (= default)
         *
         * @return void
         */
        public static function verify_nonce($nonce = 'bricks-nonce')
        {
        }
        /**
         * Verify request: nonce and user access
         *
         * Check for builder in order to not trigger on wp_auth_check
         *
         * @since 1.0
         */
        public static function verify_request($nonce = 'bricks-nonce')
        {
        }
        /**
         * Save color palette
         *
         * @since 1.0
         */
        public function save_color_palette()
        {
        }
        /**
         * Save panel width
         *
         * @since 1.0
         */
        public function save_panel_width()
        {
        }
        /**
         * Save builder state 'off' (enabled by default)
         *
         * @since 1.3.2
         */
        public function save_builder_scale_off()
        {
        }
        /**
         * Save builder width locked state (disabled by default)
         *
         * Only apply for bas breakpoint. Allows users on smaller screen not having to set a custom width on every page load.
         *
         * @since 1.3.2
         */
        public function save_builder_width_locked()
        {
        }
        /**
         * Get pages
         *
         * @since 1.0
         */
        public function get_pages()
        {
        }
        /**
         * Create new page
         *
         * @since 1.0
         */
        public function create_new_page()
        {
        }
        /**
         * Duplicate page or post in the builder (Bricks or WordPress)
         *
         * @since 1.9.8
         */
        public function duplicate_content()
        {
        }
        /**
         * Render element HTML from settings
         *
         * builder.php (query_content_type_for_elements_html to generate HTML for builder load)
         * AJAX call / REST API call: In-builder (getHTML for PHP-rendered elements)
         *
         * @since 1.0
         */
        public static function render_element($data)
        {
        }
        /**
         * Generate the HTML based on the builder content data (post Id or content)
         *
         * Used to feed Rank Math SEO & Yoast content analysis (@since 1.11)
         *
         * NOTE: This method doesn't generate any styles!
         */
        public function get_html_from_content()
        {
        }
        /**
         * Get template elements by template ID
         *
         * To generate global classes CSS in builder.
         *
         * @since 1.8.2
         */
        public function get_template_elements_by_id()
        {
        }
        /**
         * Add/remove global element
         *
         * @since 1.0
         */
        public function save_global_element()
        {
        }
        /**
         * Update global elements options in database
         */
        public function update_global_elements($new_value, $old_value, $option)
        {
        }
        /**
         * Query control: Get posts
         *
         * @since 1.0
         */
        public function get_posts()
        {
        }
        /**
         * Get users
         *
         * @since 1.2.2
         *
         * @return void
         */
        public function get_users()
        {
        }
        /**
         * Get terms
         *
         * @since 1.0
         */
        public function get_terms_options()
        {
        }
        /**
         * Render Bricks data for static header/content/footer and query loop preview HTML in builder
         *
         * @since 1.0
         */
        public static function render_data()
        {
        }
        /**
         * Don't check for chnage when creating revision as all that changed is the postmeta
         *
         * @since 1.7
         */
        public function dont_check_for_revision_changes()
        {
        }
        /**
         * Save post
         *
         * @since 1.0
         */
        public function save_post()
        {
        }
        /**
         * Save generated template screenshot
         *
         * @since 1.10
         */
        public function save_template_screenshot()
        {
        }
        /**
         * Sanitize Bricks postmeta
         */
        public function sanitize_bricks_postmeta($meta_value, $meta_key, $object_type)
        {
        }
        /**
         * Sanitize Bricks postmeta page settings
         *
         * @since 1.9.9
         */
        public function sanitize_bricks_postmeta_page_settings($meta_value, $meta_key, $object_type)
        {
        }
        /**
         * Update postmeta: Prevent user without builder access from updating Bricks postmeta
         */
        public function update_bricks_postmeta($check, $object_id, $meta_key, $meta_value, $prev_value)
        {
        }
        /**
         * Create autosave
         *
         * @since 1.0
         */
        public static function create_autosave()
        {
        }
        /**
         * Get bulider URL
         *
         * To reload builder with newly saved postName/postTitle (page settigns)
         *
         * @since 1.0
         */
        public function get_builder_url()
        {
        }
        /**
         * Publish post
         *
         * @since 1.0
         */
        public function publish_post()
        {
        }
        /**
         * Get image metadata
         *
         * @since 1.0
         */
        public function get_image_metadata()
        {
        }
        /**
         * Get Image Id from a custom field
         *
         * @since 1.0
         */
        public function get_image_from_custom_field()
        {
        }
        /**
         * Download image to WordPress media libary (Unsplash)
         *
         * @since 1.0
         */
        public function download_image()
        {
        }
        /**
         * Parse content through dynamic data logic
         *
         * @since 1.5.1
         */
        public function get_dynamic_data_preview_content()
        {
        }
        /**
         * Get latest remote templates data in builder (PopupTemplates.vue)
         *
         * @since 1.0
         */
        public function get_remote_templates_data()
        {
        }
        /**
         * Builder: Get "My templates" from db
         *
         * @since 1.4
         */
        public function get_my_templates_data()
        {
        }
        /**
         * Get current user
         *
         * Verify logged-in user when builder is loaded on the frontend.
         *
         * @since 1.5
         */
        public function get_current_user_id()
        {
        }
        /**
         * Delete bricks query loop random seed transient
         *
         * @since 1.7.1
         */
        public function query_loop_delete_random_seed_transient()
        {
        }
        /**
         * Get custom shape divider (SVG) from attachment ID
         *
         * Only allow to select SVG files from the media library for security reasons.
         *
         * @since 1.8.6
         */
        public function get_custom_shape_divider()
        {
        }
        public function restore_global_class()
        {
        }
        /**
         * Delete global classes permanently
         *
         * @since 1.11
         */
        public function delete_global_classes_permanently()
        {
        }
    }
    // Exit if accessed directly
    class Theme_Styles
    {
        public static $styles = [];
        public static $active_id;
        public static $active_settings = [];
        public static $control_options = [];
        public static $control_groups = [];
        public static $controls = [];
        public function __construct()
        {
        }
        public static function set_controls()
        {
        }
        public static function load_set_styles($post_id = 0)
        {
        }
        /**
         * Load theme styles
         */
        public static function load_styles()
        {
        }
        /**
         * Get control groups
         */
        public static function get_control_groups()
        {
        }
        /**
         * Get all theme style controls
         */
        public static function get_controls()
        {
        }
        /**
         * Get controls data
         */
        public static function get_controls_data()
        {
        }
        /**
         * Create new styles (create new one or import styles from file)
         */
        public function create_styles()
        {
        }
        /**
         * Delete custom style from db (by style ID)
         */
        public function delete_style()
        {
        }
        /**
         * Get active theme style according to theme style conditions
         *
         * @param int     $post_id Template ID.
         * @param boolean $return_id Set to true to return active theme style ID for this template (needed on template import).
         */
        public static function set_active_style($post_id = 0, $return_id = false)
        {
        }
    }
    // Exit if accessed directly
    /**
     * Custom Fonts Upload
     *
     * Font naming convention: custom_font_{font_id}
     *
     * @since 1.0
     */
    class Custom_Fonts
    {
        public static $fonts = false;
        public static $font_face_rules = '';
        public function __construct()
        {
        }
        /**
         * Generate custom font-face rules when viewing/editing "Custom fonts" in admin area
         *
         * @since 1.7.2
         */
        public function generate_custom_font_face_rules()
        {
        }
        /**
         * Add inline style for custom @font-face rules
         *
         * @since 1.7.2
         */
        public function add_inline_style_font_face_rules()
        {
        }
        /**
         * Get all custom fonts (in-builder & assets generation)
         */
        public static function get_custom_fonts()
        {
        }
        /**
         * Generate custom font-face rules
         *
         * Load all font-faces. Otherwise always forced to select font-family + font-weight (@since 1.5)
         *
         * @param int $font_id Custom font ID.
         *
         * @return string Font-face rules for $font_id.
         */
        public static function generate_font_face_rules($font_id = 0)
        {
        }
        public function admin_enqueue_scripts()
        {
        }
        public function add_meta_boxes()
        {
        }
        /**
         * Enable font file uploads for the following mime types: .TTF, .woff, .woff2 (specified in 'get_custom_fonts_mime_types' function below)
         *
         * .EOT only supported in IE (https://caniuse.com/?search=eot)
         *
         * https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Common_types
         */
        public function upload_mimes($mime_types)
        {
        }
        public function render_meta_boxes($post)
        {
        }
        public static function render_font_faces_meta_box($font_face = [], $font_variant = 400)
        {
        }
        public function save_font_faces()
        {
        }
        public function manage_columns($columns)
        {
        }
        public function render_columns($column, $post_id)
        {
        }
        public function post_row_actions($actions, $post)
        {
        }
        public function register_post_type()
        {
        }
    }
    // Exit if accessed directly
    class Database
    {
        public static $posts_per_page = 0;
        public static $active_templates = [
            'header' => 0,
            'footer' => 0,
            'content' => 0,
            'section' => 0,
            // Use in "Template" element
            'archive' => 0,
            'error' => 0,
            'search' => 0,
            'password_protection' => 0,
            // @since 1.11.1
            'popup' => [],
        ];
        public static $default_template_types = [
            'header',
            'footer',
            'archive',
            'search',
            'error',
            'wc_archive',
            'wc_product',
            'wc_cart',
            'wc_cart_empty',
            'wc_form_checkout',
            'wc_form_pay',
            'wc_thankyou',
            'wc_order_receipt',
            // Woo Phase 3
            'wc_account_dashboard',
            'wc_account_orders',
            'wc_account_view_order',
            'wc_account_downloads',
            'wc_account_addresses',
            'wc_account_form_edit_address',
            'wc_account_form_edit_account',
            'wc_account_form_login',
            'wc_account_form_lost_password',
            'wc_account_form_lost_password_confirmation',
            'wc_account_reset_password',
        ];
        public static $header_position = 'top';
        public static $global_data = [];
        public static $page_data = ['preview_or_post_id' => 0, 'language' => ''];
        public static $global_settings = [];
        public static $page_settings = [];
        public static $adobe_fonts = [];
        public function __construct()
        {
        }
        /**
         * Support autoupdate
         *
         * To always show "Enable/disable auto-updates" link for Bricks.
         * Otherwise, link only shows when an update is available.
         */
        public function wp_prepare_themes_for_js($prepared_themes)
        {
        }
        /**
         * Log every save of empty global classes to debug where it's coming from
         *
         * Triggered in Bricks via:
         *
         * ajax.php:      wp_ajax_bricks_save_post (save post in builder)
         * templates.php: wp_ajax_bricks_import_template (template import)
         * converter.php: wp_ajax_bricks_run_converter (run converter from Bricks settings)
         *
         * @link https://developer.wordpress.org/reference/hooks/update_option_option/
         *
         * @since 1.7
         */
        public function update_option_bricks_global_classes($old_value, $new_value, $option_name)
        {
        }
        /**
         * Customize WP Main Query: Set all query_vars by user for archive/search/error template pages
         * So the pagination will not encounter 404 errors
         *
         * @since 1.9.1
         */
        public function set_main_archive_query($query)
        {
        }
        /**
         * Set active templates for use throughout the theme
         */
        public static function set_active_templates($post_id = 0)
        {
        }
        /**
         * Finds the most suitable template id for a specific context
         *
         * @param array  $template_ids Organized by type.
         * @param string $template_part header, footer or content.
         * @param string $content_type What type of content is expected: content, archive, search, error.
         * @param string $post_id Current post_id or preview_id.
         * @param string $preview_type If template, and populate content is set.
         * @param array  $excluded_ids Array of template IDs to exclude from consideration. (@since 1.11.1)
         */
        public static function find_template_id($template_ids, $template_part, $content_type, $post_id, $preview_type, $excluded_ids = [])
        {
        }
        /**
         * Find all the templates available for a specific context based on the template conditions
         *
         * @param array  $template_ids List of templates per template type.
         * @param string $template_part header, footer or content.
         */
        public static function find_templates($template_ids, $template_part, $post_id, $preview_type)
        {
        }
        /**
         * Undocumented function
         */
        public static function get_all_templates_by_type()
        {
        }
        /**
         * Set default header/footer template
         *
         * If no template with matching templateCondition(s) has been set.
         *
         * Can be disabled via admin setting 'defaultTemplatesDisabled'.
         *
         * @since 1.0
         */
        public static function set_default_template($template_type = '')
        {
        }
        /**
         * Helper function to screen a set of template or theme style conditions and check if they apply given the context
         *
         * @param array  $found Holds array of found object IDs (the key is the score).
         * @param string $object_id Could be template_id or the style_id.
         * @param array  $conditions Template or Theme Style conditions.
         * @param int    $post_id Real or Preview).
         * @param string $preview_type The preview type (single, search, archive, etc.).
         *
         * @return array Found conditions array ($score => $object_id)
         */
        public static function screen_conditions($found, $object_id, $conditions, $post_id, $preview_type)
        {
        }
        /**
         * Check if header or footer is disabled (via page settings) for the current context
         *
         * Page setting keys: headerDisabled, footerDisabled
         *
         * @return bool
         * @since 1.5.4
         */
        public static function is_template_disabled($template_type)
        {
        }
        /**
         * Get template elements
         *
         * @since 1.0
         */
        public static function get_template_data($content_type)
        {
        }
        /**
         * Get Bricks data by post_id and content_area (header/content/footer)
         *
         * @since 1.0
         */
        public static function get_data($post_id = 0, $content_area = '')
        {
        }
        /**
         * Get the Bricks data key for a specific template type (header/content/footer)
         *
         * @since 1.5.1
         *
         * @param string $content_area
         * @return string
         */
        public static function get_bricks_data_key($content_area = '')
        {
        }
        /**
         * Get global settings from options table
         *
         * @since 1.0
         */
        public static function get_setting($key, $default = false)
        {
        }
        /**
         * Get global data from options table
         *
         * @since 1.0
         */
        public static function get_global_data()
        {
        }
        /**
         * Set page data needed for AJAX calls (builder)
         *
         * @since 1.3
         */
        public static function set_ajax_page_data()
        {
        }
        /**
         * Get page data from post meta
         *
         * @since 1.0
         */
        public static function set_page_data($post_id = 0)
        {
        }
        /**
         * Return current page type, not considering AJAX calls
         *
         * @param object $object Queried object.
         *
         * @since 1.8
         */
        public static function get_current_page_type($object)
        {
        }
        /**
         * Recursively retrieve nested template data
         *
         * @return array
         *
         * @since 1.9.1
         */
        public static function get_nested_template_data($bricks_data = [])
        {
        }
        /**
         * Retrieve template data from template elements
         *
         * @since 1.9.1
         */
        public static function get_template_elements_data($elements = [])
        {
        }
        /**
         * Get elements sequence in builder
         *
         * This is used to determine the order of elements in the builder.
         *
         * @since 1.9.1
         *
         * @return array (sequence of ids)
         */
        public static function elements_sequence_in_builder($elements)
        {
        }
        /**
         * Get sequence of ids by children
         *
         * @since 1.9.1
         */
        public static function get_ids_by_children($elements, $parent_element)
        {
        }
        /**
         * Get the element by id from elements array
         *
         * @since 1.9.1
         */
        public static function get_element_by_id($element_id, $elements)
        {
        }
    }
    // Exit if accessed directly
    class Woocommerce
    {
        public static $product_categories = [];
        public static $product_tags = [];
        public static $is_active = false;
        public function __construct()
        {
        }
        /**
         * Dont't show WooCommerce outdated template files admin notice
         *
         * Show custom Bricks message instead.
         *
         * @since 1.9.8
         */
        public function show_admin_notice($true, $notice)
        {
        }
        /**
         * Show custom message for outdated WooCommerce template files
         *
         * @since 1.9.8
         */
        public function admin_notice_outdated_template_files()
        {
        }
        /**
         * My account endpoint template preview: Redirect to actual my account endpoint
         *
         * To render entire my account area (navigation + content)
         *
         * @since 1.9
         */
        public function template_redirect()
        {
        }
        /**
         * Woo Phase 3: Get #brx-content HTML as rendered on the frontend
         *
         * To render complete my account (navigation + content)
         * and move dynamic drag & drop area into my account content div.
         *
         * @since 1.9
         */
        public function builder_dynamic_wrapper($dynamic_area = [])
        {
        }
        /**
         * Woo Phase 3 - Check if current page is my account dashboard page
         *
         * @see includes/wc-template-functions.php woocommerce_account_content()
         */
        public static function is_wc_account_dashboard()
        {
        }
        /**
         * My account: Render Bricks template data if available
         *
         * @since 1.9
         */
        public function add_my_account_content()
        {
        }
        /**
         * Woo Phase 3 - Set account navigation active class in builder
         *
         * @since 1.9
         */
        public function woocommerce_account_menu_item_classes($classes, $endpoint)
        {
        }
        /**
         * Sync Woocommerce product flexslider with Bricks thumbnail slider
         *
         * @since 1.9
         */
        public function single_product_carousel_options($options)
        {
        }
        /**
         * Checkout: Make sure the removed billing/shipping fields in the WooCommerce checkout customer details element are set to be not required
         *
         * @since 1.5.7
         */
        public function woocommerce_checkout_fields($fields)
        {
        }
        /**
         * Cart or checkout build with Bricks: Remove 'wordpress' post class to avoid auto-containing Bricks content
         *
         * @since 1.5.5
         */
        public function post_class($classes, $class, $post_id)
        {
        }
        /**
         * If WooCommerce is not used, make sure the single and archive Woo templates are not used
         *
         * @since 1.5.1
         *
         * @param string $template
         * @return string
         */
        public function no_woo_template_include($template)
        {
        }
        /**
         * Sale badge HTML
         *
         * Show text or percentage.
         */
        public function badge_sale($html, $post, $product)
        {
        }
        public static function badge_new()
        {
        }
        /**
         * Product review submit button: Add 'button' class to apply Woo button styles
         */
        public function product_review_comment_form_args($comment_form)
        {
        }
        /**
         * WooCommerce support sets WC_Template_Loader::$theme_support = true
         */
        public function add_theme_support()
        {
        }
        /**
         * Get products terms (categories, tags) for in-builder product query controls
         */
        public function set_products_terms()
        {
        }
        /**
         * Get terms for a given product taxonomy
         */
        public static function get_products_terms($taxonomy = null)
        {
        }
        /**
         * Check if WooCommerce plugin is active
         *
         * @return boolean
         */
        public static function is_woocommerce_active()
        {
        }
        /**
         * Init WooCommerce theme styles
         */
        public function init_theme_styles()
        {
        }
        /**
         * Init WooCommerce elements
         */
        public function init_elements()
        {
        }
        public function quantity_input_field_add_minus_button()
        {
        }
        public function quantity_input_field_add_plus_button()
        {
        }
        public function breadcrumb_separator($defaults)
        {
        }
        /**
         * Add search breadbrumb in the product archive if using Bricks search filter
         *
         * @param array         $crumbs
         * @param WC_Breadcrumb $crumbs_obj
         * @return array
         */
        public function add_breadcrumbs_from_filters($crumbs, $crumbs_obj)
        {
        }
        /**
         * Bypass Builder post type check because page set to WooCommerce Shop fails
         *
         * @return boolean
         */
        public function bypass_builder_post_type_check($supported_post_types, $current_post_type)
        {
        }
        /**
         * Builder: Set single product template & populate content (if needed)
         */
        public function maybe_set_template_preview_content()
        {
        }
        /**
         * Cart page / Checkout page: Return no title if rendered via Bricks template
         *
         * @since 1.8
         */
        public function default_page_title($post_title, $post_id)
        {
        }
        /**
         * Set aria-current="page" for WooCommerce
         *
         * @since 1.8
         */
        public function maybe_set_aria_current_page($set, $url)
        {
        }
        public static function get_wc_endpoint_from_url($url)
        {
        }
        /**
         * Builder: Add body classes to Woo templates
         *
         * @param array $classes
         */
        public function add_body_class($classes)
        {
        }
        /**
         * On the builder, move up WooCommerce specific elements
         *
         * @since 1.2.1
         *
         * @param string $category
         * @param int    $post_id
         * @param string $post_type
         *
         * @return string
         */
        public function set_first_element_category($category, $post_id, $post_type)
        {
        }
        /**
         * Page marked as shop - is_shop() - has a global $post_id set to the first product (like is_home)
         *
         * In builder or when setting the active templates we need to replace the active post id by the page id
         *
         * @param int $post_id
         */
        public function maybe_set_post_id($post_id)
        {
        }
        /**
         * Add WooCommerce element link selectors to allow Theme Styles for the links
         *
         * @since 1.5.7
         */
        public function link_css_selectors($selectors)
        {
        }
        /**
         * NOTE: Not in use as we renamed the 'PhotoSwipe' class to 'Photoswipe5' to avoid conflicts with WooCommerce Photoswipe 4
         */
        public function unload_photoswipe5_lightbox_assets()
        {
        }
        /**
         * Remove WooCommerce scripts on non-WooCommerce pages
         *
         * @since 1.2.1
         */
        public function wp_enqueue_scripts()
        {
        }
        /**
         * Before Bricks searchs for the right template, set the content_type if needed
         *
         * @param string $content_type
         * @param int    $post_id
         */
        public static function set_content_type($content_type, $post_id)
        {
        }
        /**
         * All WooCommerce templates in Bricks
         *
         * @since 1.11.1
         * @return array
         */
        public static function get_woo_templates()
        {
        }
        /**
         * Add template types to control options
         *
         * @param array $control_options
         * @return array
         *
         * @since 1.4
         */
        public function add_template_types($control_options)
        {
        }
        /**
         * Remove "Template Conditions" & "Populate Content" panel controls for WooCommerce Cart & Checkout template parts
         *
         * @param array $settings
         * @return array
         *
         * @since 1.4
         */
        public function remove_template_conditions($settings)
        {
        }
        /**
         * Get template data by template type
         *
         * For woocommerce templates inside Bricks theme.
         *
         * Return template data rendered via Bricks template shortcode.
         *
         * @since 1.8: Return template ID if render is false (to not trigger any hooks when we are not rendering the template)
         * Example: do_shortcode will be execute in post_class filter, which will trigger the do_shortcode action,
         * and causing wc_print_notices to be executed in post_class filter before the actual template is rendered.
         * Resulted actual template rendering empty notices. (wc_print_notices() will erase the notices after it is executed)
         *
         * @see /includes/woocommerce/cart/cart.php (wc_cart), etc.
         *
         * @since 1.4
         */
        public static function get_template_data_by_type($type = '', $render = true)
        {
        }
        /**
         * Add Archive Product content type
         *
         * Note: Not in use
         *
         * @param array $types
         */
        public function add_content_types($types)
        {
        }
        /**
         * Setup the products query loop in the products archive, including is_shop page (frontend only)
         *
         * @param array  $data Elements list.
         * @param string $post_id Post ID.
         */
        public function setup_query($data, $post_id)
        {
        }
        public function reset_query($sections, $post_id)
        {
        }
        /**
         * Update the mini-cart fragments
         *
         * @param array $fragments
         */
        public function update_mini_cart($fragments)
        {
        }
        /**
         * Check if the query loop is on Woo products, and if yes, check if we should merge the main query
         *
         * @since 1.5
         *
         * @param boolean $merge
         * @param string  $element_id
         * @return boolean
         */
        public function maybe_merge_query($merge, $element_id)
        {
        }
        /**
         * Add products query vars to the query loop
         *
         * @since 1.5
         *
         * @param array  $query_vars
         * @param array  $settings
         * @param string $element_id
         * @return boolean
         */
        public function set_products_query_vars($query_vars, $settings, $element_id, $element_name)
        {
        }
        /**
         * Adds the cart contents query to the Query Loop builder
         *
         * @param array $control_options
         * @return array
         */
        public function add_control_options($control_options)
        {
        }
        /**
         * Returns the cart contents query
         *
         * @param array $results
         * @param Query $query
         * @return array
         */
        public function run_cart_query($results, $query)
        {
        }
        /**
         * Sets the loop object (to WP_Post) in each query loop iteration
         *
         * @param array  $loop_object
         * @param string $loop_key
         * @param Query  $query
         * @return array
         */
        public function set_loop_object($loop_object, $loop_key, $query)
        {
        }
        /**
         * Returns the loop object id (for the cart query)
         *
         * @since 1.5.3
         */
        public function set_loop_object_id($object_id, $object, $query_id)
        {
        }
        /**
         * Returns the loop object type (for the cart query)
         *
         * @since 1.5.3
         */
        public function set_loop_object_type($object_type, $object, $query_id)
        {
        }
        /**
         * Check if user enabled single ajax add to cart
         *
         * @return bool
         * @since 1.6.1
         */
        public static function enabled_ajax_add_to_cart()
        {
        }
        /**
         * Get global AJAX show notice setting
         *
         * @return string
         * @since 1.9
         */
        public static function global_ajax_show_notice()
        {
        }
        /**
         * Get global AJAX scroll to notice setting
         *
         * @return string
         * @since 1.9
         */
        public static function global_ajax_scroll_to_notice()
        {
        }
        /**
         * Get global AJAX reset text after setting
         *
         * @return int
         * @since 1.9
         */
        public static function global_ajax_reset_text_after()
        {
        }
        /**
         * Get global AJAX adding text setting
         *
         * @return string
         * @since 1.9.2
         */
        public static function global_ajax_adding_text()
        {
        }
        /**
         * Get global AJAX added text setting
         *
         * @return string
         * @since 1.9.2
         */
        public static function global_ajax_added_text()
        {
        }
        /**
         * Get global AJAX error action setting
         *
         * - Redirect to product page (default)
         * - Show notice
         *
         * @return string
         * @since 1.11
         */
        public static function global_ajax_error_action()
        {
        }
        /**
         * Get global AJAX error scroll to notice setting
         *
         * @return string
         * @since 1.11
         */
        public static function global_ajax_error_scroll_to_notice()
        {
        }
        /**
         * AJAX Add to cart
         * Support product types: simple, variable, grouped
         *
         * @since 1.6.1
         *
         * @see woocommerce/includes/class-wc-ajax.php add_to_cart()
         */
        public function add_to_cart()
        {
        }
        /**
         * Same as WC_AJAX::get_refreshed_fragments() but without the cart_hash and cart_url fragments
         *
         * @since 1.8.4
         */
        public static function get_refreshed_fragments()
        {
        }
        /**
         * Take over the native WooCommerce AJAX add to cart button
         *
         * @since 1.8.5
         */
        public function overwrite_native_ajax_add_to_cart($args, $product)
        {
        }
        /**
         * Check if use bricks woo notice element
         *
         * @since 1.8.1
         * @return bool
         */
        public static function use_bricks_woo_notice_element()
        {
        }
        /**
         * Remove all native woocommerce notices hooks if use Bricks woo notice element
         *
         * So user can control the location of notices via the Bricks woo notice element.
         *
         * @since 1.8.1
         * @since 1.11.1: Included logic for Woo Checkout Coupon & Login Element
         * @see woocommerce/includes/wc-template-hooks.php Notices
         */
        public static function maybe_remove_native_woocommerce_notices_hooks()
        {
        }
        /**
         * Remove WooCommerce hook actions to avoid duplicate content
         *
         * @since 1.7
         *
         * @param string   $action
         * @param array    $filters
         * @param string   $context
         * @param \WP_Post $post
         */
        public function maybe_remove_woo_hook_actions($action, $filters, $context, $post)
        {
        }
        /**
         * Restore WooCommerce hooks
         *
         * @since 1.7
         *
         * @param string   $action
         * @param array    $filters
         * @param string   $context
         * @param \WP_Post $post
         * @param mixed    $value
         */
        public function maybe_restore_woo_hook_actions($action, $filters, $context, $post, $value)
        {
        }
        /**
         * Add bricks-woo-{template} body class to the body
         * Add woocommerce body classes for templates (builder or preview)
         *
         * Woo Phase 3
         */
        public function maybe_set_body_class($classes)
        {
        }
        /**
         * Add .woocommerce class to the main tag (#brx-content) when previewing woo templates in frontend OR if the current page is my account page
         *
         * Otherwise not all Woo CSS & JS is applied. In builder, we add this class inside TheDynamicArea.vue
         *
         * Woo Phase 3
         */
        public function template_preview_main_classes($attributes)
        {
        }
        /**
         * Check if use quantity in loop
         *
         * @return bool
         * @since 1.9
         */
        public static function use_quantity_in_loop()
        {
        }
        /**
         * Add quantity input field to loop
         *
         * Support product types: simple
         *
         * @since 1.9
         */
        public function add_quantity_input_field($html, $product)
        {
        }
        /**
         * Get $args for password reset form via
         *
         * Used in Account page & reset password form template.
         *
         * @see Woo core lost_password()
         * @since 1.9
         */
        public static function get_reset_password_args()
        {
        }
        /**
         * @since 1.11.1
         */
        public static function use_bricks_woo_checkout_coupon_element()
        {
        }
        /**
         * @since 1.11.1
         */
        public static function use_bricks_woo_checkout_login_element()
        {
        }
    }
    // Exit if accessed directly
    class Builder
    {
        public static $dynamic_data = [];
        // key: DD tag; value: DD tag value (@since 1.7.1)
        public static $html_attributes = [];
        // key: header, main, footer, element ID; value: array with element attributes (@since 1.10)
        public function __construct()
        {
        }
        /**
         * Remove 'admin-bar' inline styles
         *
         * Necessary for WordPress 6.4+ as html {margin-top: 32px !important} causes gap in builder.
         *
         * @since 1.9.3
         */
        public function remove_admin_bar_inline_styles()
        {
        }
        /**
         * Don't cache headers or browser history buffer in builder
         *
         * To fix browser back button issue.
         *
         * https://developer.mozilla.org/en-US/docs/Web/HTTP/Caching
         * https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
         *
         * "at present any pages using Cache-Control: no-store will not be eligible for bfcache."
         * - https://web.dev/bfcache/#minimize-use-of-cache-control-no-store
         *
         * @since 1.6.2
         */
        public function dont_cache_headers()
        {
        }
        /**
         * Remove admin bar and CSS
         *
         * @since 1.0
         */
        public function show_admin_bar()
        {
        }
        /**
         * Set a different language locale in builder if user has specified a different admin language
         *
         * @since 1.1.2
         */
        public function maybe_set_locale($locale)
        {
        }
        /**
         * Set language direction in builder (panels)
         *
         * Apply only to main window (toolbar & panels). Canvas should use frontend direction.
         *
         * @since 1.5
         */
        public function set_language_direction()
        {
        }
        /**
         * Canvas: Add element x-template render scripts to wp_footer
         */
        public function element_x_templates()
        {
        }
        /**
         * Before site wrapper (opening tag to render builder)
         *
         * @since 1.0
         */
        public function before_site_wrapper()
        {
        }
        /**
         * After site wrapper (closing tag to render builder)
         *
         * @since 1.0
         */
        public function after_site_wrapper()
        {
        }
        /**
         * Enqueue styles and scripts
         *
         * @since 1.0
         */
        public function enqueue_scripts()
        {
        }
        /**
         * Enqueue inline styles for static areas
         *
         * NOTE: Not in use (handled in StaticArea.vue line198). Keep for future reference.
         *
         * @since 1.8.2 (#862jzhynp)
         */
        public function static_area_styles()
        {
        }
        /**
         * Get WordPress data for use in builder x-template (to reduce AJAX calls)
         *
         * @return array
         *
         * @since 1.0
         */
        public static function get_wordpress_data()
        {
        }
        /**
         * Get all fonts
         *
         * - Adobe fonts (@since 1.7.1)
         * - Custom fonts
         * - Google fonts
         * - Standard fonts
         *
         * @since 1.2.1
         *
         * @return array
         */
        public static function get_fonts()
        {
        }
        /**
         * Get standard (web safe) fonts
         *
         * @since 1.0
         *
         * @return array
         */
        public static function get_standard_fonts()
        {
        }
        /**
         * Get Google fonts
         *
         * Return fonts array with 'family' & 'variants' (to update font-weight for each font in builder)
         *
         * @since 1.0
         *
         * @return array
         */
        public static function get_google_fonts()
        {
        }
        /**
         * Template placeholder image (if importImages set to false)
         *
         * @since 1.0
         */
        public static function get_template_placeholder_image()
        {
        }
        /**
         * Template preview data
         *
         * @since 1.0
         */
        public static function get_template_preview_data($post_id)
        {
        }
        /**
         * Post thumbnail data (for use in _background control)
         *
         * @since 1.0
         */
        public function get_post_thumbnail()
        {
        }
        /**
         * Custom TinyMCE settings for builder
         *
         * @since 1.0
         */
        public function tiny_mce_before_init($in)
        {
        }
        /**
         * WordPress editor
         *
         * Without tag button, "Add media" button (use respective elements instead)
         *
         * @since 1.0
         */
        public function get_wp_editor()
        {
        }
        /**
         * Add 'superscript' & 'subscript' button to TinyMCE in builder
         *
         * @since 1.4
         */
        public function add_editor_buttons($buttons)
        {
        }
        /**
         * Builder strings
         *
         * @since 1.0
         */
        public static function i18n()
        {
        }
        /**
         * Custom save messages
         *
         * @since 1.0
         *
         * @return array
         */
        public function save_messages()
        {
        }
        /**
         * Get icon font classes
         *
         * @since 1.0
         *
         * @return array
         */
        public static function get_icon_font_classes()
        {
        }
        /**
         * Based on post_type or template type select the first elements category to show up on builder.
         */
        public function get_first_elements_category($post_id = 0)
        {
        }
        /**
         * Default color palette (https://www.materialui.co/colors)
         *
         * Only used if no custom colorPalette is saved in db.
         *
         * @since 1.0
         *
         * @return array
         */
        public static function default_color_palette()
        {
        }
        /**
         * Check permissions for a certain user to access the Bricks builder
         *
         * @since 1.0
         */
        public function template_redirect()
        {
        }
        /**
         * Get page data for builder
         *
         * @since 1.0
         *
         * @return array
         */
        public static function builder_data($post_id)
        {
        }
        /**
         * Return array with HTML string of every single element for initial fast builder render
         *
         * @since 1.0
         */
        public static function query_content_type_for_elements_html($elements, $post_id)
        {
        }
        /**
         * Screens all elements and try to convert dynamic data to enhance builder experience
         *
         * @param array $elements
         * @param int   $post_id
         */
        public static function render_dynamic_data_on_elements($elements, $post_id)
        {
        }
        /**
         * On the settings array, if _background exists and is set to image, get the image URL
         * Needed when setting element background image
         *
         * @param array $settings
         * @param int   $post_id
         */
        public static function render_dynamic_data_on_settings($settings, $post_id)
        {
        }
        /**
         * Builder: Force Bricks template to avoid conflicts with other builders (Elementor PRO, etc.)
         */
        public function template_include($template)
        {
        }
        /**
         * Helper function to check if a AJAX or REST API call comes from inside the builder
         *
         * NOTE: Use bricks_is_builder_call() to check if AJAX/REST API call inside the builder
         *
         * @since 1.5.5
         *
         * @return boolean
         */
        public static function is_builder_call()
        {
        }
        /**
         * Return the maximum number of query loop results to display in the builder
         *
         * @since 1.11
         */
        public static function get_query_max_results()
        {
        }
        /**
         * Get query max results info
         *
         * @since 1.11
         */
        public static function get_query_max_results_info()
        {
        }
    }
    // Exit if accessed directly
    class Conditions
    {
        public static $groups = [];
        public static $options = [];
        public function __construct()
        {
        }
        public function init()
        {
        }
        /**
         * Set condition groups
         *
         * @return void
         *
         * @since 1.8.4
         */
        public function set_groups()
        {
        }
        /**
         * Set condition options
         *
         * @return void
         *
         * @since 1.8.4
         */
        public function set_options()
        {
        }
        /**
         * Return all controls (builder)
         *
         * @return array
         */
        public static function get_controls_data()
        {
        }
        /**
         * Transform dynamic data tag
         *
         * Add ':value' to ACF true_false tag to get unlocalized value.
         *
         * @since 1.9.9
         */
        public static function maybe_transform_dynamic_tag($dynamic_tag)
        {
        }
        /**
         * Convert boolean-like strings to actual booleans for proper true/false comparisions
         *
         * @since 1.7
         */
        public static function boolean_converter(&$value, &$required)
        {
        }
        /**
         * Check element conditions
         *
         * At least one condition set must be fulfilled for the element to be rendered.
         *
         * Inside a condition all items must evaluate to true.
         *
         * @return boolean true = render element | false = don't render element
         *
         * @since 1.5.4
         */
        public static function check($conditions, $instance)
        {
        }
    }
    // Exit if accessed directly
    /**
     * Breakpoints
     *
     * Default behavior: From largest to smallest breakpoints via max-width rules.
     *
     * Mobile-first possible via custom breakpoints:
     * Set a small breakpoint as 'base' to use min-width rules.
     *
     * Custom breakpoints @since 1.5.1
     */
    class Breakpoints
    {
        public static $breakpoints = [];
        public static $base_key = 'desktop';
        public static $base_width = 0;
        public static $is_mobile_first = false;
        public function __construct()
        {
        }
        /**
         * Calculate the breakpoints on init to get the proper breakpoints translations
         *
         * @since 1.5.1
         */
        public static function init_breakpoints()
        {
        }
        /**
         * Automatically regenerate Bricks CSS files after theme update
         *
         * @since 1.5.1
         */
        public function admin_notice_regenerate_bricks_css_files()
        {
        }
        /**
         * Regenerate Bricks CSS files (via Bricks > Settings > General)
         *
         * E.g. frontend.min.css, element & woo CSS files, etc.
         *
         * Manual trigger: "Regenerate CSS files" button
         * Auto trigger: After theme update (compare version number in db against current theme version)
         *
         * @since 1.5.1
         */
        public static function regenerate_bricks_css_files()
        {
        }
        /**
         * Create breakpoint
         *
         * @since 1.5.1
         */
        public function update_breakpoints()
        {
        }
        /**
         * Update @media rules for breakpoint in CSS files
         *
         * When changing default breakpoint width OR default breakpoint reset.
         */
        public static function update_media_rule_width_in_css_files($current_width = 0, $new_width = 0, $default_width = 0)
        {
        }
        /**
         * Default breakpoints
         *
         * - desktop (default base breakpoint)
         * - tablet_portrait
         * - mobile_landscape
         * - mobile_portrait
         *
         * @return Array
         */
        public static function get_default_breakpoints()
        {
        }
        /**
         * Get all breakpoints (default & custom)
         */
        public static function get_breakpoints()
        {
        }
        /**
         * Get breakpoint by key
         */
        public static function get_breakpoint_by($key = 'key', $value = '')
        {
        }
    }
    // Exit if accessed directly
    /**
     * WP CLI commands for Bricks
     *
     * https://wp-cli.org/
     *
     * @since 1.8.1
     */
    class CLI
    {
        public function __construct()
        {
        }
        public function regenerate_assets()
        {
        }
    }
    // Exit if accessed directly
    class Search
    {
        public function __construct()
        {
        }
        /**
         * Helper: Check if is_search() OR Bricks infinite scroll REST API search results
         *
         * @since 1.5.7
         */
        public function is_search($query)
        {
        }
        /**
         * Search 'posts' and 'postmeta' tables
         *
         * https://adambalee.com/search-wordpress-by-custom-fields-without-a-plugin/
         * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_join
         *
         * @since 1.3.7
         */
        public function search_postmeta_table($join, $query)
        {
        }
        /**
         * Modify search query
         *
         * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_where
         *
         * @since 1.3.7
         */
        public function modify_search_for_postmeta($where, $query)
        {
        }
        /**
         * Prevent duplicates
         *
         * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_distinct
         *
         * @since 1.3.7
         */
        public function search_distinct($where, $query)
        {
        }
    }
    // Exit if accessed directly
    /**
     * Builder access 'bricks_full_access' or 'bricks_edit_content'
     *
     * Set per user role under 'Bricks > Settings > Builder access OR by editing a user profile individually.
     *
     * 'bricks_edit_content' capability can't:
     *
     * - Add, clone, delete, save elements/templates
     * - Resize elements (width, height)
     * - Adjust element spacing (padding, margin)
     * - Access custom context menu
     * - Edit any CSS controls (property 'css' check)
     * - Edit any controls under "Style" tab
     * - Edit any controls with 'fullAccess' set to true
     * - Delete revisions
     * - Edit template settings
     * - Edit any page settings except 'SEO' (default panel)
     */
    class Capabilities
    {
        const FULL_ACCESS = 'bricks_full_access';
        const EDIT_CONTENT = 'bricks_edit_content';
        const UPLOAD_SVG = 'bricks_upload_svg';
        const EXECUTE_CODE = 'bricks_execute_code';
        const BYPASS_MAINTENANCE = 'bricks_bypass_maintenance';
        const FORM_SUBMISSION_ACCESS = 'bricks_form_submission_access';
        // @since 1.11
        // Allow to disable for individual user (@since 1.6)
        const NO_ACCESS = 'bricks_no_access';
        const UPLOAD_SVG_OFF = 'bricks_upload_svg_off';
        const EXECUTE_CODE_OFF = 'bricks_execute_code_off';
        const BYPASS_MAINTENANCE_OFF = 'bricks_bypass_maintenance_off';
        const FORM_SUBMISSION_ACCESS_OFF = 'bricks_form_submission_access_off';
        // @since 1.11
        // To run set_user_capabilities only once
        public static $capabilities_set = false;
        // Builder access (default: no access)
        public static $full_access = false;
        public static $edit_content = false;
        public static $no_access = true;
        // Upload SVG & execute code (default: false)
        public static $upload_svg = false;
        public static $execute_code = false;
        // Bypass maintenance mode (default: false)
        public static $bypass_maintenance = false;
        // Default values for the new capability (default: false) (@since 1.11)
        public static $form_submission_access = false;
        public function __construct()
        {
        }
        /**
         * Set capabilities of logged in user
         *
         * - builder access
         * - upload svg
         * - exectute code
         * - bypass maintenance (@since 1.9.4)
         * - form submission access (@since 1.11)
         *
         * @since 1.6
         */
        public static function set_user_capabilities()
        {
        }
        public function manage_users_columns($columns)
        {
        }
        public function manage_users_custom_column($output, $column_name, $user_id)
        {
        }
        /**
         * Check current user capability to use builder (full access OR edit content)
         *
         * @return boolean
         */
        public static function current_user_can_use_builder($post_id = 0)
        {
        }
        /**
         * Check if current user has full access
         *
         * @return boolean
         */
        public static function current_user_has_full_access()
        {
        }
        /**
         * Check if current user has no access
         *
         * @return boolean
         * @since 1.6
         */
        public static function current_user_has_no_access()
        {
        }
        /**
         * Logged-in user can upload SVGs
         *
         * current_user_can not working on multisite for super admin.
         *
         * @return boolean
         * @since 1.6
         */
        public static function current_user_can_upload_svg()
        {
        }
        /**
         * Logged-in user can execute code
         *
         * current_user_can not working on multisite for super admin.
         *
         * @return boolean
         * @since 1.6
         */
        public static function current_user_can_execute_code()
        {
        }
        /**
         * Logged-in user can bypass maintenance mode
         *
         * current_user_can not working on multisite for super admin.
         *
         * @return boolean
         * @since 1.9.4
         */
        public static function current_user_can_bypass_maintenance_mode()
        {
        }
        /**
         * Logged-in user can access form submissions
         *
         * current_user_can not working on multisite for super admin.
         *
         * @return boolean
         * @since 1.11
         */
        public static function current_user_can_form_submission_access()
        {
        }
        /**
         * Reset user role capabilities for Bricks
         */
        public static function set_defaults()
        {
        }
        /**
         * Capabilities for access to the builder
         *
         * @return array
         */
        public static function builder_caps()
        {
        }
        public static function save_builder_capabilities($capabilities = [])
        {
        }
        public static function save_capabilities($capability, $add_to_roles = [])
        {
        }
        /**
         * Update Bricks-specific user capabilities:
         *
         * - bricks_cap_builder_access
         * - bricks_cap_upload_svg
         * - bricks_cap_execute_code
         * - bricks_cap_bypass_maintenance (@since 1.9.4)
         * - bricks_cap_form_submission_access (@since 1.11)
         */
        public function update_user_profile($user_id)
        {
        }
        public function user_profile($user)
        {
        }
    }
}
namespace Bricks {
    // Exit if accessed directly
    class Element_Slider extends \Bricks\Element
    {
        public $category = 'media';
        public $name = 'slider';
        public $icon = 'ti-layout-slider';
        public $scripts = ['bricksSwiper'];
        public $draggable = false;
        public $loop_index = 0;
        public function get_label()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public function render_repeater_item($slide)
        {
        }
    }
    // Exit if accessed directly
    class Element_Tabs extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'tabs';
        public $icon = 'ti-layout-tab';
        public $scripts = ['bricksTabs'];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Post_Table_Of_Contents extends \Bricks\Element
    {
        public $category = 'single';
        public $name = 'post-toc';
        public $icon = 'ti-list';
        public $css_selector = '.toc-list';
        public $scripts = ['bricksTableOfContents'];
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Pie_Chart extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'pie-chart';
        public $icon = 'ti-pie-chart';
        public $scripts = ['bricksPieChart'];
        public function get_label()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Code extends \Bricks\Element
    {
        public $block = ['core/code', 'core/preformatted'];
        public $category = 'general';
        public $name = 'code';
        public $icon = 'ion-ios-code';
        public $scripts = ['bricksPrettify'];
        public function enqueue_scripts()
        {
        }
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public function get_code_snippet($code, $language)
        {
        }
        public function convert_element_settings_to_block($settings)
        {
        }
        public function convert_block_to_element_settings($block, $attributes)
        {
        }
    }
    // Exit if accessed directly
    class Element_Icon extends \Bricks\Element
    {
        public $category = 'basic';
        public $name = 'icon';
        public $icon = 'ti-star';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Text_Link extends \Bricks\Element
    {
        public $block = 'core/paragraph';
        public $category = 'basic';
        public $name = 'text-link';
        public $icon = 'ti-link';
        public $tag = 'a';
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public static function _render_builder()
        {
        }
    }
    // Exit if accessed directly
    class Element_Container extends \Bricks\Element
    {
        public $category = 'layout';
        public $name = 'container';
        public $icon = 'ti-layout-width-default';
        public $vue_component = 'bricks-nestable';
        public $nestable = true;
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        public function set_controls()
        {
        }
        /**
         * Return shape divider HTML
         */
        public static function get_shape_divider_html($settings = [])
        {
        }
        /**
         * Return background video HTML
         */
        public function get_background_video_html($settings)
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Filter_Element extends \Bricks\Element
    {
        public $category = 'filter';
        public $input_name = '';
        public $filter_type = '';
        public $filtered_source = [];
        public $choices_source = [];
        public $data_source = [];
        public $populated_options = [];
        public $page_filter_value = [];
        public $query_settings = [];
        public $target_query_results_count = 0;
        public function get_keywords()
        {
        }
        public function set_controls_after()
        {
        }
        /**
         * Retrieve the standard controls for filter inputs for frontend
         */
        public function get_common_filter_settings()
        {
        }
        /**
         * Determine whether this input is a filter input
         * Will be overriden by each input if needed
         *
         * @return boolean
         */
        public function is_filter_input()
        {
        }
        /**
         * Check if this filter has indexing job in progress
         *
         * @since 1.10
         */
        public function is_indexing()
        {
        }
        public function prepare_sources()
        {
        }
        public function set_data_source_from_taxonomy()
        {
        }
        /**
         * Similar to set_data_source_from_custom_field, but separate for easier maintenance in the future
         */
        public function set_data_source_from_wp_field()
        {
        }
        public function set_data_source_from_custom_field()
        {
        }
        /**
         * Set options with count
         * DO NOT use this method if no count is needed as it will generate more queries.
         *
         * Used in: filter-checkbox, filter-radio, filter-select
         */
        public function set_options_with_count()
        {
        }
        /**
         * For filter-select, filter-radio, filter-checkbox
         *
         * @since 1.11
         */
        public function get_option_text_with_count($option)
        {
        }
        /**
         * For filter-select, filter-radio
         */
        public function setup_sort_options()
        {
        }
        /**
         * Sort the terms hierarchically
         */
        public static function sort_terms_hierarchically(&$data_source, &$new_source, $parentId = 0)
        {
        }
        /**
         * Now we need to flatten the arrays.
         * If no children, just push to $flattern and set depth to 0
         * If has children, push the childrens to $flattern and set depth to its parent depth + 1 (recursively).
         * The children must be placed under its parent
         * Then save all nested children's value_id to children_ids key of its parent (recursively)
         */
        public static function flatten_terms_hierarchically(&$source, &$flattern, $parentId = 0, $depth = 0)
        {
        }
        /**
         * Some of the flattened terms may have children_ids
         * But we need to merge the children_ids to its parent recursively
         * Not in Beta
         */
        public static function update_children_ids(&$flattened_terms, &$updated_data_source)
        {
        }
        /**
         * Return query filter controls
         *
         * If element support query filters.
         *
         * Only common controls are returned.
         * Each element might add or remove controls.
         *
         * @since 1.9.6
         */
        public function get_filter_controls()
        {
        }
    }
    // Exit if accessed directly
    class Filter_Range extends \Bricks\Filter_Element
    {
        public $name = 'filter-range';
        public $icon = 'ti-arrows-horizontal';
        public $filter_type = 'range';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Post_Title extends \Bricks\Element
    {
        public $category = 'single';
        public $name = 'post-title';
        public $icon = 'ti-text';
        public $tag = 'h3';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Filter_Checkbox extends \Bricks\Filter_Element
    {
        public $name = 'filter-checkbox';
        public $icon = 'ti-check-box';
        public $filter_type = 'checkbox';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function is_filter_input()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Shortcode extends \Bricks\Element
    {
        public $block = 'core/shortcode';
        public $category = 'wordpress';
        public $name = 'shortcode';
        public $icon = 'ti-shortcode';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public function convert_element_settings_to_block($settings)
        {
        }
        public function convert_block_to_element_settings($block, $attributes)
        {
        }
    }
    // Exit if accessed directly
    class Element_Pricing_Tables extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'pricing-tables';
        public $icon = 'ti-money';
        public $css_selector = '.pricing-table';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Animated_Typing extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'animated-typing';
        public $icon = 'ti-more';
        public $scripts = ['bricksAnimatedTyping'];
        public function get_label()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public static function render_builder()
        {
        }
    }
    // Exit if accessed directly
    class Pagination extends \Bricks\Element
    {
        public $category = 'wordpress';
        public $name = 'pagination';
        public $icon = 'ti-angle-double-right';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public function pagination_args($args)
        {
        }
    }
    // Exit if accessed directly
    class Filter_Search extends \Bricks\Filter_Element
    {
        public $name = 'filter-search';
        public $icon = 'ti-search';
        public $filter_type = 'search';
        public $css_selector = 'input';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Accordion extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'accordion';
        public $icon = 'ti-layout-accordion-merged';
        public $scripts = ['bricksAccordion'];
        public $css_selector = '.accordion-item';
        public $loop_index = 0;
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public function render_repeater_item($accordion, $title_tag, $icon, $icon_expanded)
        {
        }
    }
    // Exit if accessed directly
    class Element_Post_Navigation extends \Bricks\Element
    {
        public $category = 'single';
        public $name = 'post-navigation';
        public $icon = 'ti-layout-menu-separated';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Post_Sharing extends \Bricks\Element
    {
        public $category = 'single';
        public $name = 'post-sharing';
        public $icon = 'ti-share';
        public $css_selector = 'a';
        public function get_label()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Related_Posts extends \Bricks\Custom_Render_Element
    {
        public $category = 'single';
        public $name = 'related-posts';
        public $icon = 'ti-pin-alt';
        public $css_selector = 'li';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Icon_Box extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'icon-box';
        public $icon = 'ti-check-box';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public static function render_builder()
        {
        }
    }
    // Exit if accessed directly
    class Breadcrumbs extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'breadcrumbs';
        public $icon = 'ti-layout-menu-separated';
        public static $link_format = '<a class="item" href="%s">%s</a>';
        public static $current_span_format = '<span class="item" aria-current="page">%s</span>';
        public static $separator_span_format = '<span class="separator">%s</span>';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        /**
         * Main function to generate the breadcrumbs
         *
         * @return string
         *
         * @since 1.8.1
         */
        public function bricks_breadcrumbs()
        {
        }
        /**
         * Populate breadcrumbs parent links
         *
         * @param int    $parent
         * @param string $type
         * @return array
         *
         * @since 1.8.1
         */
        public function populate_breadcrumbs_parent_links($parent, $type)
        {
        }
    }
    // Exit if accessed directly
    class Element_Map extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'map';
        public $icon = 'ti-location-pin';
        public $scripts = ['bricksMap'];
        public $draggable = false;
        public function get_label()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Divider extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'divider';
        public $icon = 'ti-layout-line-solid';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Accordion_Nested extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'accordion-nested';
        public $icon = 'ti-layout-accordion-merged';
        public $scripts = ['bricksAccordion'];
        public $nestable = true;
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function get_nestable_item()
        {
        }
        public function get_nestable_children()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Tabs_Nested extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'tabs-nested';
        public $icon = 'ti-layout-tab';
        public $scripts = ['bricksTabs'];
        public $nestable = true;
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        /**
         * Get child elements
         *
         * @return array Array of child elements.
         *
         * @since 1.5
         */
        public function get_nestable_children()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Alert extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'alert';
        public $icon = 'ti-alert';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Countdown extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'countdown';
        public $icon = 'ti-timer';
        public $css_selector = '.field';
        public $scripts = ['bricksCountdown'];
        public function get_label()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Heading extends \Bricks\Element
    {
        public $block = 'core/heading';
        public $category = 'basic';
        public $name = 'heading';
        public $icon = 'ti-text';
        public $tag = 'h3';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        // NOTE: Render separator in ContentEditable.js (@since 1.11)
        public static function render_builder()
        {
        }
        public function convert_element_settings_to_block($settings)
        {
        }
        public function convert_block_to_element_settings($block, $attributes)
        {
        }
    }
    // Exit if accessed directly
    class Element_Counter extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'counter';
        public $icon = 'ti-dashboard';
        public $scripts = ['bricksCounter'];
        public function get_label()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Slider_Nested extends \Bricks\Element
    {
        public $category = 'media';
        public $name = 'slider-nested';
        public $icon = 'ti-layout-slider';
        public $scripts = ['bricksSplide'];
        public $nestable = true;
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function get_nestable_item()
        {
        }
        public function get_nestable_children()
        {
        }
        /**
         * Render arrows (use custom HTML solution as splideJS only accepts SVG path via 'arrowPath')
         */
        public function render_arrows()
        {
        }
        /**
         * Render individual slides
         */
        public function render()
        {
        }
    }
    class Element_Div extends \Bricks\Element_Container
    {
        public $category = 'layout';
        public $name = 'div';
        public $icon = 'ti-layout-width-default-alt';
        public $nestable = true;
        public function get_label()
        {
        }
    }
    class Element_Section extends \Bricks\Element_Container
    {
        public $category = 'layout';
        public $name = 'section';
        public $icon = 'ti-layout-accordion-separated';
        public $tag = 'section';
        public $nestable = true;
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        /**
         * Get child elements
         *
         * @return array Array of child elements.
         *
         * @since 1.5
         */
        public function get_nestable_children()
        {
        }
    }
    // Exit if accessed directly
    class Element_Search extends \Bricks\Element
    {
        public $block = 'core/search';
        public $category = 'wordpress';
        public $name = 'search';
        public $icon = 'ti-search';
        public $css_selector = 'form';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public function convert_element_settings_to_block($settings)
        {
        }
        public function convert_block_to_element_settings($block, $attributes)
        {
        }
    }
    // Exit if accessed directly
    class Element_List extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'list';
        public $icon = 'ti-list';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Text_Basic extends \Bricks\Element
    {
        public $block = 'core/paragraph';
        public $category = 'basic';
        public $name = 'text-basic';
        public $icon = 'ti-align-justify';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public static function render_builder()
        {
        }
        public function convert_element_settings_to_block($settings)
        {
        }
        // NOTE: Convert block to element settings: Use Bricks "Rich Text" element instead
        // public function convert_block_to_element_settings( $block, $attributes ) {}
    }
    // Exit if accessed directly
    class Filter_Select extends \Bricks\Filter_Element
    {
        public $name = 'filter-select';
        public $icon = 'ti-widget-alt';
        public $filter_type = 'select';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Post_Reading_Progress_Bar extends \Bricks\Element
    {
        public $category = 'single';
        public $name = 'post-reading-progress-bar';
        public $icon = 'ti-line-double';
        public $scripts = ['bricksPostReadingProgressBar'];
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    class Element_Block extends \Bricks\Element_Container
    {
        public $category = 'layout';
        public $name = 'block';
        public $icon = 'ti-layout-width-full';
        public $nestable = true;
        public function get_label()
        {
        }
    }
    // Exit if accessed directly
    class Element_Post_Taxonomy extends \Bricks\Element
    {
        public $category = 'single';
        public $name = 'post-taxonomy';
        public $icon = 'ti-clip';
        public $css_selector = '.bricks-button';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Post_Meta extends \Bricks\Element
    {
        public $category = 'single';
        public $name = 'post-meta';
        public $icon = 'ti-receipt';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Logo extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'logo';
        public $icon = 'ti-home';
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Image_Gallery extends \Bricks\Element
    {
        public $block = 'core/gallery';
        public $category = 'media';
        public $name = 'image-gallery';
        public $icon = 'ti-gallery';
        public $scripts = ['bricksIsotope'];
        public function get_label()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function set_controls()
        {
        }
        public function get_normalized_image_settings($settings)
        {
        }
        public function render()
        {
        }
        public function convert_element_settings_to_block($settings)
        {
        }
        public function convert_block_to_element_settings($block, $attributes)
        {
        }
    }
    // Exit if accessed directly
    class Element_Social_Icons extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'social-icons';
        public $icon = 'ti-twitter';
        public $css_selector = 'li.has-link a, li.no-link';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_WordPress extends \Bricks\Element
    {
        public $category = 'wordpress';
        public $name = 'wordpress';
        public $icon = 'ti-wordpress';
        public $css_selector = 'ul';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Html extends \Bricks\Element
    {
        public $block = 'core/html';
        public $category = 'general';
        public $name = 'html';
        public $icon = 'ti-html5';
        public $deprecated = true;
        // NOTE Undocumented
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public static function render_builder()
        {
        }
        public function convert_element_settings_to_block($settings)
        {
        }
        public function convert_block_to_element_settings($block, $attributes)
        {
        }
    }
    // Exit if accessed directly
    class Element_Video extends \Bricks\Element
    {
        public $block = ['core/video', 'core-embed/youtube', 'core-embed/vimeo'];
        public $category = 'basic';
        public $name = 'video';
        public $icon = 'ti-video-clapper';
        public $scripts = ['bricksVideo'];
        public $draggable = false;
        public function get_label()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public function convert_element_settings_to_block($settings)
        {
        }
        public function convert_block_to_element_settings($block, $attributes)
        {
        }
        /**
         * Helper function to parse the settings when videoType = meta
         *
         * @return array
         */
        public function get_normalized_video_settings($settings = [])
        {
        }
        /**
         * Get the video image image URL
         *
         * @param array $settings
         *
         * @since 1.7.2
         */
        public function get_preview_image_url($settings = [])
        {
        }
        /**
         * Get the image by control key
         *
         * Similar to get_normalized_image_settings() in the image element.
         *
         * Might be a fix image, a dynamic data tag or external URL.
         *
         * @since 1.8.5
         *
         * @return array
         */
        public function get_video_image_by_key($control_key = '')
        {
        }
    }
    // Exit if accessed directly
    class Element_Testimonials extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'testimonials';
        public $icon = 'ti-comment-alt';
        public $css_selector = '.swiper-slide';
        public $scripts = ['bricksSwiper'];
        public $draggable = false;
        public function get_label()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Nav_Menu extends \Bricks\Element
    {
        public $category = 'wordpress';
        public $name = 'nav-menu';
        public $icon = 'ti-menu';
        public $custom_attributes = false;
        public $scripts = ['bricksSubmenuListeners', 'bricksSubmenuPosition'];
        public $wp_nav_menu_items = [];
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        /**
         * Render menu item & their sub menus recursively
         *
         * When using Nav menu inside dropdown content.
         *
         * @since 1.8
         */
        public function render_menu_items_of_parent_id($parent_id)
        {
        }
        public function render()
        {
        }
        /**
         * Add submenu toggle icon
         * Render mega menu (desktop menu)
         */
        public function walker_nav_menu_start_el($output, $item, $depth, $args)
        {
        }
        /**
         * Mega menu:  Add .brx-has-megamenu && .menu-item-has-children
         * Multilevel: Add .brx-has-multilevel && .menu-item-has-children
         * Builder:    Add .current-menu-item
         *
         * @since 1.5.3
         */
        public function nav_menu_css_class($classes, $menu_item, $args, $depth)
        {
        }
        /**
         * Return template ID of mega menu
         *
         * @since 1.8
         */
        public function get_mega_menu_template_id($menu_item_id)
        {
        }
        /**
         * Return true if multilevel is enabled
         *
         * @since 1.8
         */
        public function is_multilevel($menu_item_id)
        {
        }
    }
    // Exit if accessed directly
    class Element_Dropdown extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'dropdown';
        public $icon = 'ti-arrow-circle-down';
        public $scripts = ['bricksSubmenuPosition'];
        public $nestable = true;
        public $tag = 'li';
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function get_nestable_children()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Filter_DatePicker extends \Bricks\Filter_Element
    {
        public $name = 'filter-datepicker';
        public $icon = 'ti-calendar';
        public $filter_type = 'datepicker';
        public $min_date = null;
        // timestamp
        public $max_date = null;
        // timestamp
        public function get_label()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function is_filter_input()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Post_Reading_Time extends \Bricks\Element
    {
        public $category = 'single';
        public $name = 'post-reading-time';
        public $icon = 'ti-time';
        public $scripts = ['bricksPostReadingTime'];
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Audio extends \Bricks\Element
    {
        public $block = 'core/audio';
        public $category = 'media';
        public $name = 'audio';
        public $icon = 'ti-volume';
        public $scripts = ['bricksAudio'];
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public function convert_element_settings_to_block($settings)
        {
        }
        public function convert_block_to_element_settings($block, $attributes)
        {
        }
    }
    // Exit if accessed directly
    class Element_Post_Author extends \Bricks\Element
    {
        public $category = 'single';
        public $name = 'post-author';
        public $icon = 'ti-user';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Offcanvas extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'offcanvas';
        public $icon = 'ti-layout-sidebar-left';
        public $scripts = ['bricksOffcanvas'];
        public $nestable = true;
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        public function set_controls()
        {
        }
        public function get_nestable_children()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Filter_Submit extends \Bricks\Filter_Element
    {
        public $name = 'filter-submit';
        public $icon = 'ti-mouse';
        public $filter_type = 'apply';
        public $button_type = 'button';
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Instagram_Feed extends \Bricks\Element
    {
        public $category = 'media';
        public $name = 'instagram-feed';
        public $icon = 'ion-logo-instagram';
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Post_Comments extends \Bricks\Element
    {
        public $category = 'single';
        public $name = 'post-comments';
        public $icon = 'ti-comments';
        public function get_label()
        {
        }
        public function enqueue_scripts()
        {
        }
        /**
         * Author HTML tag
         *
         * @since 1.10
         */
        public function comment_author_link($comment_author_tag)
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Team_Members extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'team-members';
        public $icon = 'ti-id-badge';
        public $tag = 'ul';
        public function get_label()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Carousel extends \Bricks\Custom_Render_Element
    {
        public $category = 'media';
        public $name = 'carousel';
        public $icon = 'ti-layout-slider-alt';
        public $css_selector = '.swiper-slide';
        public $scripts = ['bricksSwiper'];
        public $draggable = false;
        public function get_label()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Form extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'form';
        public $icon = 'ti-layout-cta-left';
        public $tag = 'form';
        public $scripts = ['bricksForm'];
        public function get_label()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        /**
         * Split a text into key-value pairs
         *
         * This function takes a text and splits it into key-value pairs using the colon (:) as the delimiter.
         * If the text contains a colon, it will split the text into two parts: the key and the value.
         * If the text does not contain a colon, it will use the entire text as both the key and the value.
         *
         * @param string $text The text to split.
         * @return array An array containing the key-value pairs.
         *
         * @since 1.10.2
         */
        public function split_text($text)
        {
        }
        /**
         * Generate recaptcha HTML
         *
         * @since 1.5
         */
        public function generate_recaptcha_html()
        {
        }
        /**
         * Generate hCaptcha HTML
         *
         * @since 1.9.2
         */
        public function generate_hcaptcha_html()
        {
        }
        /**
         * Generate Turnstile HTML
         *
         * @since 1.9.2
         */
        public function generate_turnstile_html()
        {
        }
    }
    // Exit if accessed directly
    class Element_Template extends \Bricks\Element
    {
        public $block = 'core/template';
        public $category = 'general';
        public $name = 'template';
        public $icon = 'ti-layers';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        /**
         * Builder: Helper function to add data to builder render call (AJAX or REST API)
         *
         * @since 1.5
         *
         * @param boolean $template_id
         * @return array
         */
        public static function get_builder_call_additional_data($template_id)
        {
        }
    }
    // Exit if accessed directly
    class Filter_Active_Filters extends \Bricks\Filter_Element
    {
        public $name = 'filter-active-filters';
        public $icon = 'ti-filter';
        public $filter_type = 'active-filters';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Svg extends \Bricks\Element
    {
        public $category = 'media';
        public $name = 'svg';
        public $icon = 'ti-vector';
        public $tag = 'svg';
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Progress_Bar extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'progress-bar';
        public $icon = 'ti-line-double';
        public $css_selector = '.bar';
        public $scripts = ['bricksProgressBar'];
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Rating extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'rating';
        public $icon = 'ti-star';
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Posts extends \Bricks\Custom_Render_Element
    {
        public $category = 'wordpress';
        public $name = 'posts';
        public $icon = 'ti-layout-media-overlay';
        public $css_selector = '.bricks-layout-inner';
        public $scripts = ['bricksIsotope'];
        // @var array Arguments passed to WP_Query.
        public $query_vars = null;
        public function get_label()
        {
        }
        public function enqueue_scripts()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Sidebar extends \Bricks\Element
    {
        public $category = 'wordpress';
        public $name = 'sidebar';
        public $icon = 'ti-layout-sidebar-right';
        public function get_label()
        {
        }
        /**
         * Load required WP styles on the frontend
         *
         * @since 1.8
         */
        public function enqueue_scripts()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Post_Content extends \Bricks\Element
    {
        public $category = 'single';
        public $name = 'post-content';
        public $icon = 'ti-wordpress';
        public function enqueue_scripts()
        {
        }
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Text extends \Bricks\Element
    {
        public $block = ['core/paragraph', 'core/list'];
        public $category = 'basic';
        public $name = 'text';
        public $icon = 'ti-align-left';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public static function render_builder()
        {
        }
        public function convert_element_settings_to_block($settings)
        {
        }
        public function convert_block_to_element_settings($block, $attributes)
        {
        }
    }
    // Exit if accessed directly
    class Element_Back_To_Top extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'back-to-top';
        public $icon = 'ti-arrow-up';
        public $nestable = true;
        public $tag = 'button';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function get_nestable_children()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Image extends \Bricks\Element
    {
        public $block = 'core/image';
        public $category = 'basic';
        public $name = 'image';
        public $icon = 'ti-image';
        public $tag = 'figure';
        public $custom_attributes = false;
        public function get_label()
        {
        }
        /**
         * Enqueue PhotoSwipe lightbox script file as needed (frontend only)
         *
         * @since 1.3.4
         */
        public function enqueue_scripts()
        {
        }
        public function set_controls()
        {
        }
        public function get_mask_url($settings)
        {
        }
        protected function set_mask_attributes($mask_url, $mask_settings)
        {
        }
        public function get_normalized_image_settings($settings)
        {
        }
        public function render()
        {
        }
        public function get_block_html($settings)
        {
        }
        public function convert_element_settings_to_block($settings)
        {
        }
        /**
         * Not done yet: Custom block alt & caption strings have to be extracted from $block['innerHTML']
         */
        public function convert_block_to_element_settings($block, $attributes)
        {
        }
    }
    // Exit if accessed directly
    class Element_Facebook_Page extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'facebook-page';
        public $icon = 'ti-facebook';
        public $scripts = ['bricksFacebookSDK'];
        public $draggable = false;
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Filter_Radio extends \Bricks\Filter_Element
    {
        public $name = 'filter-radio';
        public $icon = 'ti-control-record';
        public $filter_type = 'radio';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Post_Excerpt extends \Bricks\Element
    {
        public $category = 'single';
        public $name = 'post-excerpt';
        public $icon = 'ti-paragraph';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        /**
         * Render taxonomy or author description
         *
         * @param string $description
         *
         * @since 1.6.2
         */
        public function render_description($description)
        {
        }
        /**
         * Render post excerpt
         *
         * @since 1.6.2
         */
        public function render_post_excerpt()
        {
        }
        /**
         * Render no excerpt
         *
         * @since 1.6.2
         */
        public function render_no_excerpt()
        {
        }
    }
    // Exit if accessed directly
    class Element_Nav_Nested extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'nav-nested';
        public $icon = 'ti-menu';
        public $tag = 'nav';
        public $scripts = ['bricksNavNested', 'bricksSubmenuListeners', 'bricksSubmenuPosition'];
        public $nestable = true;
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        public function set_control_groups()
        {
        }
        public function set_controls()
        {
        }
        public function get_nestable_children()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Toggle extends \Bricks\Element
    {
        public $category = 'general';
        public $name = 'toggle';
        public $icon = 'ti-hand-point-up';
        public $scripts = ['bricksToggle'];
        public function get_label()
        {
        }
        public function get_keywords()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
    }
    // Exit if accessed directly
    class Element_Button extends \Bricks\Element
    {
        public $block = 'core/button';
        public $category = 'basic';
        public $name = 'button';
        public $icon = 'ti-control-stop';
        public $tag = 'span';
        public function get_label()
        {
        }
        public function set_controls()
        {
        }
        public function render()
        {
        }
        public static function render_builder()
        {
        }
        public function convert_element_settings_to_block($settings)
        {
        }
        public function convert_block_to_element_settings($block, $attributes)
        {
        }
    }
    // Exit if accessed directly
    class License
    {
        public static $license_key = '';
        public static $license_status = '';
        public static $remote_base_url = 'https://bricksbuilder.io/api/commerce/';
        public function __construct()
        {
        }
        /**
         * Check remotely if newer version of Bricks is available
         *
         * @param string $transient Transient for WordPress theme updates.
         */
        public static function check_for_update($transient)
        {
        }
        /**
         * Check license status when loading builder
         *
         * @see template_redirect
         */
        public static function license_is_valid()
        {
        }
        /**
         * Get license status (stored locally in transient: bricks_license_status)
         *
         * If transient has expired (after 168h) then get it remotely from Bricks server.
         *
         * @return array
         */
        public static function get_license_status()
        {
        }
        /**
         * Save license status in transient (expires after 168 hours)
         */
        public static function set_license_status($license_status)
        {
        }
        /**
         * Activate license under "Bricks > License" (AJAX call on "Activate license" click)
         *
         * Also runs via PHP in 'get_license_status' to avoid having to deactivate & reactivate license (when cloning staging site, etc.)
         *
         * @return array
         */
        public static function activate_license()
        {
        }
        /**
         * Deactivate license
         *
         * @return void
         *
         * @since 1.0
         */
        public static function deactivate_license()
        {
        }
        /**
         * Admin notice to activate license
         *
         * @return null/string
         */
        public static function admin_notices_license_activation()
        {
        }
        /**
         * Admin notice to activate license
         *
         * @return null/string
         */
        public static function admin_notices_license_mismatch()
        {
        }
    }
    // Exit if accessed directly
    /**
     * TODO: Can't convert globalElements into nestable elements
     * As each global element is considered one individual array item.
     * So nestable elements like 'slider-nested' aren't allowed be to be saved as a global element!
     */
    class Converter
    {
        public function __construct()
        {
        }
        /**
         * Get all items that need to run through converter
         *
         * - themeStyles
         * - globalSettings
         * - globalClasses
         * - globalElements
         * - template IDs (+ their page settings)
         * - post IDs (+ their page settings)
         *
         * @since 1.4
         */
        public function get_converter_items()
        {
        }
        /**
         * Run converter
         *
         * @since 1.4 Convert element IDs & class names for 1.4 ('bricks-element-' to 'brxe-')
         * @since 1.5 Convert elements to nestable elements
         */
        public function run_converter()
        {
        }
        public static function convert_container_to_section_block_element($elements = [])
        {
        }
    }
    // Exit if accessed directly
    /**
     * Element and popup interactions
     *
     * @since 1.6
     */
    class Interactions
    {
        public static $global_class_interactions = [];
        public static $control_options = [];
        public function __construct()
        {
        }
        /**
         * Get interaction controls
         *
         * @return array
         *
         * @since 1.6
         */
        public static function get_controls_data()
        {
        }
        /**
         * Set interaction controls once initially
         *
         * @since 1.6.2
         *
         * @return void
         */
        public function set_controls()
        {
        }
        /**
         * Get global classes with interaction settings (once initially) to merge with element setting interactions in add_data_attributes()
         *
         * @since 1.6
         */
        public static function get_global_class_interactions()
        {
        }
        /**
         * Add element interactions via HTML data attributes to element root node
         *
         * Can originate from global class and/or element settings.
         *
         * @since 1.6
         */
        public function add_data_attributes($attributes, $element)
        {
        }
        /**
         * Add template (e.g. popup) interaction settings to template root node
         *
         * @since 1.6
         */
        public function add_to_template_root($attributes, $template_id)
        {
        }
    }
    // Exit if accessed directly
    // To create new nonces when user gets logged out
    class Heartbeat
    {
        /**
         * WordPress REST API help docs:
         *
         * https://developer.wordpress.org/plugins/javascript/heartbeat-api/
         */
        public function __construct()
        {
        }
        /**
         * Enqueue styles and scripts
         *
         * @since 1.0
         */
        public function enqueue_scripts()
        {
        }
        /**
         * Heartbeat settings
         *
         * @since 1.0
         *
         * @param array $settings Heartbeat settings.
         */
        public function heartbeat_settings($settings)
        {
        }
        /**
         * Receive Heartbeat data and respond
         *
         * Processes data received via a Heartbeat request, and returns additional data to pass back to the front end.
         *
         * @since 1.0
         *
         * @param array $response Heartbeat response data to pass back to front end.
         * @param array $data Data received from the front end (unslashed).
         *
         * @return array Heartbeat received response.
         */
        public function heartbeat_received($response, $data)
        {
        }
        /**
         * Refresh builder and Heartbeat nonce
         *
         * @since 1.0
         *
         * @param array $response Heartbeat response.
         * @param array $data Data received.
         *
         * @return array Newly created new nonces.
         */
        public function refresh_nonces($response, $data)
        {
        }
    }
    // Exit if accessed directly
    /**
     * Handling Bricks' password protection logic
     *
     * @since 1.11.1
     */
    class Password_Protection
    {
        /**
         * Populate an empty password protection template with default content
         *
         * @param int $post_id Template post ID.
         * @return bool Whether the template was populated.
         */
        public static function populate_template($post_id)
        {
        }
        /**
         * Determine if the password protection template should be rendered
         *
         * @param int $template_id The ID of the password protection template.
         * @return bool
         */
        public static function is_active($template_id)
        {
        }
        /**
         * Check if the user has a valid password cookie.
         *
         * @param int $template_id The ID of the password protection template.
         * @return bool
         */
        public static function get_template_password($template_id)
        {
        }
        /**
         * Validate the submitted password against the template's password.
         *
         * @param int    $template_id
         * @param string $submitted_password
         * @return bool
         */
        public static function validate_password($template_id, $submitted_password)
        {
        }
        /**
         * Set the password cookie for the given template.
         *
         * @param int    $template_id
         * @param string $password
         */
        public static function set_password_cookie($template_id, $password)
        {
        }
        /**
         * Verify that the form exists in the template and is set up for password protection.
         *
         * @param string $form_id
         * @param int    $template_id
         * @return bool
         */
        public static function verify_form_in_template($form_id, $template_id)
        {
        }
        /**
         * Check if a specific template part should be excluded.
         *
         * @param string $template_part The template part to check ('header', 'footer', or 'popup').
         * @param int    $template_id The ID of the password protection template.
         * @return bool Whether the template part should be excluded.
         */
        public static function should_exclude_template_part($template_part, $template_id)
        {
        }
    }
    // Exit if accessed directly
    /**
     * Popups
     *
     * @since 1.6
     */
    class Popups
    {
        public static $controls = [];
        public static $generated_template_settings_inline_css_ids = [];
        public static $looping_popup_html = '';
        public static $ajax_popup_contents = [];
        public static $looping_ajax_popup_ids = [];
        public static $enqueue_ajax_loader = false;
        public function __construct()
        {
        }
        public static function get_controls()
        {
        }
        /**
         * Set popup controls once initially
         *
         * For builder theme style & template settings panel.
         *
         * No need to run on hook as it does not contain any db data.
         */
        public static function set_controls()
        {
        }
        /**
         * Build query loop popup HTML and store under self::$looping_popup_html
         *
         * Render in footer when executing render_popups()
         *
         * Included inline styles.
         *
         * @param int $popup_id
         *
         * @return void
         *
         * @since 1.7.1
         */
        public static function build_looping_popup_html($popup_id)
        {
        }
        /**
         * Generate popup HTML
         *
         * @param int $popup_id
         *
         * @return string
         *
         * @since 1.7.1
         */
        public static function generate_popup_html($popup_id)
        {
        }
        /**
         * Check if there is any popup to render and adds popup HTML to the footer
         *
         * @since 1.6
         *
         * @return void
         */
        public static function render_popups()
        {
        }
    }
    // Exit if accessed directly
    class Setup
    {
        public static $control_options = [];
        /**
         * Set Google Maps API key stored in Bricks settings for ACF
         *
         * Avoids having to add this ACF action manually into child theme.
         *
         * https://www.advancedcustomfields.com/blog/google-maps-api-settings/
         */
        public function acf_set_google_maps_api_key($api)
        {
        }
        public function __construct()
        {
        }
        /**
         * Body classes
         *
         * @since 1.0
         */
        public function body_class($classes)
        {
        }
        /**
         * Opening body tag
         *
         * @since 1.5
         */
        public function body_tag()
        {
        }
        public function init_control_options()
        {
        }
        /**
         * Custom document title
         *
         * @since 1.0
         */
        public function pre_get_document_title($title)
        {
        }
        /**
         * Performance enhancement Bricks settings
         *
         * @since 1.0
         */
        public function init_performance()
        {
        }
        public function init()
        {
        }
        public function disable_emojis()
        {
        }
        public function disable_emojis_tinymce($plugins)
        {
        }
        /**
         * Frontend only: Remove Gutenberg blocks stylesheet file
         */
        public function deregister_styles()
        {
        }
        public function wp_default_scripts($scripts)
        {
        }
        /**
         * First things first
         *
         * @since 1.0
         */
        public function after_setup_theme()
        {
        }
        /**
         * On theme activation
         *
         * @param string   $old_name Old theme name.
         * @param WP_Theme $old_theme Instance of the old theme.
         * @since 1.0
         */
        public function after_switch_theme($old_name, $old_theme)
        {
        }
        /**
         * On theme deactivation
         *
         * Delete license data transient (hack to manually flush license data before transient expires)
         *
         * TODO: Add redirect after theme deactivation to collect feedback via 'https://codex.wordpress.org/Plugin_API/Action_Reference/switch_theme'
         *
         * @since 1.0
         */
        public function switch_theme()
        {
        }
        /**
         * Register styles and scripts to enqueue in builder and frontend respectively
         *
         * @since 1.0
         */
        public function enqueue_scripts()
        {
        }
        /**
         * Gallery shortcode default size
         *
         * @since 1.0
         */
        public function shortcode_atts_gallery($output, $pairs, $atts)
        {
        }
        /**
         * Sidebars
         *
         * @since 1.0
         */
        public static function widgets_init()
        {
        }
        /**
         * WP admin bar: Add menu bar item "Edit with Bricks"
         *
         * @since 1.0
         */
        public function admin_bar_menu(\WP_Admin_Bar $wp_admin_bar)
        {
        }
        /**
         * Post type display: Add "Edit with Bricks" link for post type without "editor" support
         *
         * @since 1.10.2
         */
        public function edit_form_top()
        {
        }
        /**
         * Nav menu classes
         *
         * @since 1.0
         */
        public function nav_menu_css_class($classes, $item, $args, $depth)
        {
        }
        /**
         * Custom script attributes (async and defer)
         *
         * https://www.growingwiththeweb.com/2014/02/async-vs-defer-attributes.html
         *
         * @since 1.0
         */
        public function custom_script_attributes($tag, $handle, $src)
        {
        }
        /**
         * Return map styles from https://snazzymaps.com/explore for Map element
         *
         * @param string $style Style name (@since 1.9.3).
         *
         * @since 1.0
         */
        public static function get_map_styles($style = '')
        {
        }
        /**
         * Get default color (same as SCSS color vars)
         *
         * For consistent element 'default' color setting
         *
         * @since 1.0
         */
        public static function get_default_color($color_name)
        {
        }
        /**
         * Control options
         *
         * @param string $key Single option key to return specific option.
         *
         * @since 1.0
         */
        public static function get_control_options($key = '')
        {
        }
        /**
         * Return a list of taxonomies
         */
        public static function get_taxonomies_options()
        {
        }
        /**
         * Get image size options for control select options
         *
         * @since 1.0
         */
        public static function get_image_sizes()
        {
        }
        /**
         * Get image size options for control select options
         *
         * @since 1.0
         */
        public static function get_image_sizes_options()
        {
        }
        /**
         * Limit the max. number of query loop results (builder-only)
         *
         * @since 1.11
         */
        public function builder_query_max_results($result, $query_instance)
        {
        }
    }
    // Exit if accessed directly
    class Revisions
    {
        /**
         * Bricks-specific revisions for header, content and footer data saved in post meta table
         */
        public function __construct()
        {
        }
        /**
         * Get all revisions of a specific post via AJAX
         *
         * @uses get_revisions()
         *
         * @since 1.0
         */
        public static function ajax_get_revisions()
        {
        }
        /**
         * Get revision data
         *
         * @since 1.0
         */
        public static function ajax_get_revision_data()
        {
        }
        /**
         * Delete specific revision
         *
         * @uses get_revisions()
         *
         * @return array Post revisions.
         *
         * @since 1.0
         */
        public static function ajax_delete_revision()
        {
        }
        /**
         * Delete all revisions of specific post
         *
         * @return array Post revisions.
         *
         * @since 1.0
         */
        public static function ajax_delete_all_revisions_of_post_id()
        {
        }
        /**
         * Get all revisions of a specific post
         *
         * @param int   $post_id
         * @param array $query_args
         *
         * @since 1.0
         */
        public static function get_revisions($post_id, $query_args = [])
        {
        }
        /**
         * Add revisions to all Bricks builder enabled post types
         *
         * @since 1.0
         */
        public static function add_revisions_to_all_bricks_enabled_post_types()
        {
        }
        /**
         * Max. number of revisions to store in db
         *
         * @param int    $num
         * @param string $post
         *
         * @since 1.0
         *
         * @return int
         */
        public static function wp_revisions_to_keep($num, $post)
        {
        }
    }
    // Exit if accessed directly
    class Api
    {
        const API_NAMESPACE = 'bricks/v1';
        /**
         * WordPress REST API help docs:
         *
         * https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
         * https://developer.wordpress.org/rest-api/extending-the-rest-api/modifying-responses/
         */
        public function __construct()
        {
        }
        /**
         * Custom REST API endpoints
         */
        public function rest_api_init_custom_endpoints()
        {
        }
        /**
         * Return element HTML retrieved via Fetch API
         *
         * @since 1.5
         */
        public static function render_element($request)
        {
        }
        /**
         * Element render permission check
         *
         * @since 1.5
         */
        public function render_element_permissions_check($request)
        {
        }
        /**
         * Return all templates data in one call (templates, authors, bundles, tags, theme style)
         *
         * @param  array $data
         * @return array
         *
         * @since 1.0
         */
        public function get_templates_data($data)
        {
        }
        /**
         * Return templates array OR specific template by array index
         *
         * @since 1.0
         *
         * @param  array $data
         *
         * @return array
         */
        public function get_templates($data)
        {
        }
        /**
         * Get API endpoint
         *
         * Use /api to get Bricks Community Templates
         * Default: Use /wp-json (= default WP REST API prefix)
         *
         * @param string $endpoint API endpoint.
         * @param string $base_url Base URL.
         *
         * @since 1.0
         *
         * @return string
         */
        public static function get_endpoint($endpoint = 'get-templates', $base_url = BRICKS_REMOTE_URL)
        {
        }
        /**
         * Get the Bricks REST API url
         *
         * @since 1.5
         *
         * @return string
         */
        public static function get_rest_api_url()
        {
        }
        /**
         * Check if current endpoint is Bricks API endpoint
         *
         * @param string $endpoint E.g. 'render_element' or 'load_query_page' for our infinite scroll.
         *
         * @since 1.8.1
         *
         * @return bool
         */
        public static function is_current_endpoint($endpoint)
        {
        }
        /**
         * Get template authors
         *
         * @since 1.0
         *
         * @return array
         */
        public static function get_template_authors()
        {
        }
        /**
         * Get template bundles
         *
         * @since 1.0
         *
         * @return array
         */
        public static function get_template_bundles()
        {
        }
        /**
         * Get template tags
         *
         * @since 1.0
         *
         * @return array
         */
        public static function get_template_tags()
        {
        }
        /**
         * Get news feed
         *
         * NOTE: Not in use.
         *
         * @return array
         */
        public static function get_feed()
        {
        }
        /**
         * Query loop: Infinite scroll permissions callback
         *
         * @since 1.5
         */
        public function render_query_page_permissions_check($request)
        {
        }
        /**
         * Query loop: Infinite scroll callback
         *
         * @since 1.5
         */
        public function render_query_page($request)
        {
        }
        /**
         * AJAX popup callback
         *
         * @since 1.9.4
         */
        public function render_popup_content($request)
        {
        }
        /**
         * Ajax Popup permissions callback
         *
         * @since 1.9.4
         */
        public function render_popup_content_permissions_check($request)
        {
        }
        /**
         * Similar like render_query_page() but for AJAX query result
         *
         * For load more, AJAX pagination, infinite scroll, sort, filter, live search.
         *
         * @since 1.9.6
         */
        public function render_query_result($request)
        {
        }
        /**
         * Query loop: Query result permissions callback
         *
         * @since 1.9.6
         */
        public function render_query_result_permissions_check($request)
        {
        }
    }
    // Exit if accessed directly
    class Query_Filters
    {
        const INDEX_TABLE_NAME = 'bricks_filters_index';
        const ELEMENT_TABLE_NAME = 'bricks_filters_element';
        const INDEX_JOB_TABLE_NAME = 'bricks_filters_index_job';
        // @since 1.10
        const DB_CHECK_TRANSIENT = 'bricks_filters_db_check';
        // @since 1.10
        const OPTION_SUFFIX = '_db_version';
        // @since 1.10
        const ELEMENT_DB_VERSION = '1.1';
        // code version @since 1.10
        const INDEX_DB_VERSION = '1.0';
        // code version @since 1.10
        const INDEX_JOB_DB_VERSION = '1.0';
        // @since 1.10
        public static $filter_object_ids = [];
        /**
         * Structure for $active_filters
         * key: query_id
         * value: array of filter_info
         * filter_info: array of filter_id, query_id, settings, value, instance_name (sort_option_info, query_vars, query_type will be added after running generate_query_vars_from_active_filters)
         *
         * @since 1.11
         */
        public static $active_filters = [];
        public static $page_filters = [];
        public static $selected_filters = [];
        // @since 1.11
        public static $query_vars_before_merge = [];
        public static $is_saving_post = false;
        public function __construct()
        {
        }
        /**
         * Singleton - Get the instance of this class
         */
        public static function get_instance()
        {
        }
        public static function get_table_name($table_name = 'index')
        {
        }
        public static function check_managed_db_access()
        {
        }
        /**
         * Create custom database table for storing filter index
         */
        public function maybe_create_tables()
        {
        }
        /**
         * Check if all database tables are updated
         * Used in admin settings page
         *
         * @since 1.10
         */
        public static function all_db_updated()
        {
        }
        /**
         * Check if the required tables exist
         *
         * @since 1.10
         */
        public function tables_check()
        {
        }
        /**
         * Return array of element names that have filter settings.
         *
         * Pagination is one of them but it's filter setting handled in /includes/elements/pagination.php set_ajax_attributes()
         */
        public static function filter_controls_elements()
        {
        }
        /**
         * Dynamic update elements names
         * - These elements will be updated dynamically when the filter AJAX is called
         */
        public static function dynamic_update_elements()
        {
        }
        /**
         * Indexable elements names
         * - These elements will be indexed in the index table
         */
        public static function indexable_elements()
        {
        }
        /**
         * Force render filter elements in filter API endpoint.
         *
         * Otherwise, filter elements will not be re-rendered in filter API endpoint as element condition fails.
         *
         * @since 1.9.8
         */
        public function filter_element_render($render, $element_instance)
        {
        }
        /**
         * Set page filters manually on wp hook:
         * Example: In archive page, taxonomy page, etc.
         */
        public function set_page_filters_from_wp_query()
        {
        }
        /**
         * Set active filters via URL parameters
         *
         * NOTE: This feature is only available if the element table is updated to 1.1
         *
         * @since 1.11
         */
        public function set_active_filters_from_url()
        {
        }
        /**
         * Generate query vars from active filters
         *
         * @since 1.11
         */
        public function add_active_filters_query_vars()
        {
        }
        /**
         * Populate selected_filters from active filters
         * - An array with query_id as key and active_filters' IDs as value
         * - Will be used in the frontent
         *
         * @since 1.11
         */
        public function set_selected_filters_from_active_filters()
        {
        }
        /**
         * Hook into update_post_metadata, if filter element found, update the index table
         */
        public function maybe_update_element($check, $object_id, $meta_key, $meta_value, $prev_value)
        {
        }
        /**
         * Check & update element table structure. (@since 1.11) - Should move to a separate button to trigger the update.
         * Remove index DB table and recreate it. (Ensure index table structure is up-to-date)
         * Retrieve all indexable elements from element table.
         * Index based on the element settings.
         */
        public function reindex()
        {
        }
        /**
         * Generate index records for a given taxonomy
         */
        public static function generate_taxonomy_index_rows($all_posts_ids, $taxonomy)
        {
        }
        /**
         * Remove rows from database
         */
        public static function remove_index_rows($args = [])
        {
        }
        /**
         * Insert rows into database
         */
        public static function insert_index_rows($rows)
        {
        }
        /**
         * Generate index records for a given custom field
         */
        public static function generate_custom_field_index_rows($post_id, $meta_key, $provider = 'none')
        {
        }
        /**
         * Generate index records for a given post field.
         *
         * @param array  $posts Array of post objects
         * @param string $post_field The post field to be used
         */
        public static function generate_post_field_index_rows($posts, $post_field)
        {
        }
        /**
         * Set page filters
         *
         * @since 1.11
         */
        public static function set_page_filters($page_filters)
        {
        }
        /**
         * Set active filters
         *
         * @since 1.11
         */
        public static function set_active_filters($filters = [], $post_id = 0, $query_id = '')
        {
        }
        /**
         * Convert it to the correct format based on the filter type & sanitize value
         *
         * NOTE: Can't use the WP core sanitize_text_field fucntion as it can cause utf-8 characters missing.
         *
         * @since 1.11
         */
        public static function sanitize_filter_value($filter_value, $filter_type, $filter_settings)
        {
        }
        /**
         * Get active filters by element ID
         *
         * @since 1.11
         */
        public static function get_active_filter_by_element_id($element_id = '', $query_id = '')
        {
        }
        /**
         * Generate query vars from active filters
         * active filters - filters that are set by the user
         *
         * @since 1.11
         */
        public static function generate_query_vars_from_active_filters($query_id = '')
        {
        }
        /**
         * Use page_filters to generate tax_query
         * We need this in REST endpoint as we unable to identify which taxonomy is used in the page
         *
         * @since 1.11
         */
        public static function generate_query_vars_from_page_filters()
        {
        }
        /**
         * Identify if the page_filters should be applied in the query_vars
         * Special handling for tax_query
         *
         * @since 1.11
         */
        public static function should_apply_page_filters($query_vars)
        {
        }
        /**
         * Updated filters to be used in frontend after each filter ajax request
         */
        public static function get_updated_filters($filters = [], $post_id = 0)
        {
        }
        /**
         * Get filtered data from index table
         */
        public static function get_filtered_data_from_index($filter_id = '', $object_ids = [])
        {
        }
        /**
         * Get all possible object ids from a query
         * To be used in get_filtered_data()
         * Each query_id will only be queried once
         *
         * @param string $query_id
         * @return array $all_posts_ids
         */
        public static function get_filter_object_ids($query_id = '', $source = 'history', $additonal_query_vars = [])
        {
        }
        /**
         * Generate index when a post is saved
         */
        public function save_post($post_id, $post)
        {
        }
        /**
         * Remove index when a post is deleted
         */
        public function delete_post($post_id)
        {
        }
        /**
         * Set is_saving_post to true when a post is assigned to a parent to avoid reindexing
         * Triggered when using wp_insert_post()
         */
        public function wp_insert_post_parent($post_parent, $post_id, $new_postarr, $postarr)
        {
        }
        /**
         * Generate index when a post is assigened to a term
         * Triggered when using wp_set_post_terms() or wp_set_object_terms()
         */
        public function set_object_terms($object_id)
        {
        }
        /**
         * Core function to index a post based on all active indexable filter elements
         *
         * @param int $post_id
         */
        public function index_post($post_id)
        {
        }
        /**
         * Update indexed records when a term is amended (slug, name)
         */
        public function edited_term($term_id, $tt_id, $taxonomy)
        {
        }
        /**
         * Update indexed records when a term is deleted
         */
        public function delete_term($term_id, $tt_id, $taxonomy, $deleted_term)
        {
        }
    }
    // Exit if accessed directly
    class Query
    {
        // Element ID
        public $element_id = '';
        // Element name to be used in WooCommerce (@since 1.11.1)
        public $element_name = '';
        // Element settings
        public $settings = [];
        // Query vars
        public $query_vars = [];
        // Type of object queried: 'post', 'term', 'user'
        public $object_type = 'post';
        // Query result (WP_Posts | WP_Term_Query | WP_User_Query | Other)
        public $query_result;
        // Query results total
        public $count = 0;
        // Query results total pages
        public $max_num_pages = 1;
        // Is looping
        public $is_looping = false;
        // When looping, keep the iteration index
        public $loop_index = 0;
        // When looping, keep the object
        public $loop_object = null;
        // Store query history (including those destroyed)
        public static $query_history = [];
        /**
         * Class constructor
         *
         * @param array $element
         */
        public function __construct($element = [])
        {
        }
        /**
         * Get query instance by element ID from the query history
         *
         * @since 1.9.1
         */
        public static function get_query_by_element_id($element_id = '', $is_dynamic_data = false)
        {
        }
        /**
         * Add current query instance to query history
         *
         * @since 1.9.1
         */
        public function add_to_history()
        {
        }
        /**
         * Generate a unique identifier for the query history
         *
         * Use combination of element_id, nested_query_object_type, nested_query_element_id, nested_loop_object_id.
         *
         * @since 1.9.1
         */
        public static function generate_query_history_id($element_id)
        {
        }
        /**
         * Add query to global store
         */
        public function register_query()
        {
        }
        /**
         * Calling unset( $query ) does not destroy query quickly enough
         *
         * Have to call the 'destroy' method explicitly before unset.
         */
        public function __destruct()
        {
        }
        /**
         * Use the destroy method to remove the query from the global store
         *
         * @return void
         */
        public function destroy()
        {
        }
        /**
         * Get the query cache
         *
         * @since 1.5
         *
         * @return mixed
         */
        public function get_query_cache()
        {
        }
        /**
         * Set the query cache
         *
         * @since 1.5
         *
         * @return void
         */
        public function set_query_cache($object)
        {
        }
        /**
         * Prepare query_vars for the Query before running it
         * Remove unwanted keys, set defaults, populate correct query vars, etc.
         * Static method to be used by other classes. (Bricks\Database)
         *
         * @since 1.8
         */
        public static function prepare_query_vars_from_settings($settings = [], $fallback_element_id = '', $element_name = '')
        {
        }
        /**
         * Perform the query (maybe cache)
         *
         * Set $this->query_result, $this->count, $this->max_num_pages
         *
         * @return void (@since 1.8)
         */
        public function run()
        {
        }
        /**
         * Run WP_Term_Query
         *
         * @see https://developer.wordpress.org/reference/classes/wp_term_query/
         *
         * @return array Terms (WP_Term)
         */
        public function run_wp_term_query()
        {
        }
        /**
         * Run WP_User_Query
         *
         * @see https://developer.wordpress.org/reference/classes/wp_user_query/
         *
         * @return WP_User_Query (@since 1.8)
         */
        public function run_wp_user_query()
        {
        }
        /**
         * Run WP_Query
         *
         * @return object
         */
        public function run_wp_query()
        {
        }
        /**
         * Get the page number for a query based on the query var "paged"
         *
         * @since 1.5
         *
         * @return integer
         */
        public static function get_paged_query_var($query_vars)
        {
        }
        /**
         * Parse the Meta Query vars through the DD logic
         *
         * @Since 1.5
         *
         * @param array $query_vars
         * @return array
         */
        public static function parse_meta_query_vars($query_vars)
        {
        }
        /**
         * Parse the Orderby vars
         *
         * @since 1.11.1
         */
        public static function parse_orderby_vars($query_vars, $object_type)
        {
        }
        /**
         * Set 'tax_query' vars (e.g. Carousel, Posts, Related Posts)
         *
         * Include & exclude terms of different taxonomies
         *
         * @since 1.3.2
         */
        public static function set_tax_query_vars($query_vars)
        {
        }
        /**
         * Modifies $query offset variable to make pagination work in combination with offset.
         *
         * @see https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
         * Note that the link recommends exiting the filter if $query->is_paged returns false,
         * but then max_num_pages on the first page is incorrect.
         *
         * @param \WP_Query $query WordPress query.
         */
        public function set_pagination_with_offset($query)
        {
        }
        /**
         * Handle term pagination
         *
         * @since 1.9.8
         */
        public static function get_term_pagination_query_var($query_vars)
        {
        }
        /**
         * By default, WordPress includes offset posts into the final post count.
         * This method excludes them.
         *
         * @see https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
         * Note that the link recommends exiting the filter if $query->is_paged returns false,
         * but then max_num_pages on the first page is incorrect.
         *
         * @param int       $found_posts Found posts.
         * @param \WP_Query $query WordPress query.
         * @return int Modified found posts.
         */
        public function fix_found_posts_with_offset($found_posts, $query)
        {
        }
        /**
         * Set the initial loop index (needed for the infinite scroll)
         *
         * @since 1.5
         */
        public function init_loop_index()
        {
        }
        /**
         * Main render function
         *
         * @param string  $callback to render each item.
         * @param array   $args callback function args.
         * @param boolean $return_array whether returns a string or an array of all the iterations.
         */
        public function render($callback, $args, $return_array = false)
        {
        }
        public static function parse_dynamic_data($content, $post_id)
        {
        }
        /**
         * Reset the global $post to the parent query or the global $wp_query
         *
         * @since 1.5
         *
         * @return void
         */
        public function reset_postdata()
        {
        }
        /**
         * Get the current Query object
         *
         * @return Query
         */
        public static function get_query_object($query_id = false)
        {
        }
        /**
         * Get the current Query object type
         *
         * @return string
         */
        public static function get_query_object_type($query_id = '')
        {
        }
        /**
         * Get the object of the current loop iteration
         *
         * @return mixed
         */
        public static function get_loop_object($query_id = '')
        {
        }
        /**
         * Get the object ID of the current loop iteration
         *
         * @return mixed
         */
        public static function get_loop_object_id($query_id = '')
        {
        }
        /**
         * Get the object type of the current loop iteration
         *
         * @return mixed
         */
        public static function get_loop_object_type($query_id = '')
        {
        }
        /**
         * Get the current loop iteration index
         *
         * @since 1.10: Add $query_id to get the loop index of a specific query
         *
         * @return mixed
         */
        public static function get_loop_index($query_id = '')
        {
        }
        /**
         * Get a unique identifier for the current looping query
         *
         * @param string $type 'query', 'interaction', 'popup'
         * @return string
         * @since 1.10
         */
        public static function get_looping_unique_identifier($type = 'query')
        {
        }
        /**
         * Check if the render function is looping (in the current query)
         *
         * @param string $element_id Checks if the element_id matches the element that is set to loop (e.g. container).
         *
         * @return boolean
         */
        public static function is_looping($element_id = '', $query_id = '')
        {
        }
        /**
         * Get query object created for a specific element ID
         *
         * @param string $element_id
         * @return mixed
         */
        public static function get_query_for_element_id($element_id = '')
        {
        }
        /**
         * Get element ID of query loop element
         *
         * @param object $query Defaults to current query.
         *
         * @since 1.4
         *
         * @return string|boolean Element ID or false
         */
        public static function get_query_element_id($query = '')
        {
        }
        /**
         * Get the current looping level
         *
         * @return int
         * @since 1.10
         */
        public static function get_looping_level()
        {
        }
        /**
         * Get the direct parent loop ID
         *
         * @since 1.10
         */
        public static function get_parent_loop_id()
        {
        }
        /**
         * Check if there is any active query looping (nested queries) and if yes, return the query ID of the most deep query
         *
         * @return mixed
         */
        public static function is_any_looping()
        {
        }
        /**
         * Convert a list of option strings taxonomy::term_id into a list of term_ids
         */
        public static function convert_terms_to_ids($terms = [])
        {
        }
        public function get_no_results_content()
        {
        }
        /**
         * Check if the query is using random seed
         * Use random seed when: 'orderby' is 'rand' && 'randomSeedTtl' > 0
         * Default: 60 minutes
         *
         * @param array $query_vars
         * @return boolean
         * @since 1.9.8
         */
        public static function use_random_seed($query_vars = [])
        {
        }
        /**
         * Get the random seed statement for the query
         *
         * @param string $element_id
         * @param array  $query_vars
         * @return string
         * @since 1.9.8
         */
        public static function get_random_seed_statement($element_id = '', $query_vars = [])
        {
        }
        /**
         * Use random seed to make sure the order is the same for all queries of the same element
         *
         * The transient is also deleted when the random seed setting inside the query loop control is changed.
         *
         * @param string $order_statement
         * @return string
         * @since 1.7.1
         */
        public function set_bricks_query_loop_random_order_seed($order_statement)
        {
        }
        /**
         * All query arguments that can be set for the archive query
         * https://developer.wordpress.org/reference/classes/wp_query/#parameters
         *
         * @return array
         *
         * @since 1.8
         */
        public static function archive_query_arguments()
        {
        }
        /**
         * All bricks query object types that can be set for the archive query.
         * If there is custom query by user and it might be used as archive query, should be added here.
         *
         * @return array
         *
         * @since 1.8
         */
        public static function archive_query_supported_object_types()
        {
        }
        /**
         * Merge two query vars arrays, instead of using wp_parse_args
         *
         * wp_parse_args will only set those values that are not already set in the original array.
         *
         * @param array $original_query_vars
         * @param array $merging_query_vars
         * @param bool  $meta_query_logic (@since 1.11.1)
         * @return array
         *
         * @see https://developer.wordpress.org/reference/functions/wp_parse_args/
         *
         * @since 1.9.4
         */
        public static function merge_query_vars($original_query_vars = [], $merging_query_vars = [], $meta_query_logic = false)
        {
        }
        /**
         * Special case for merging 'tax_query' and 'meta_query' vars
         *
         * Only merge if the 'taxonomy' or 'key' are identical.
         *
         * @since 1.9.6
         */
        public static function merge_tax_or_meta_query_vars($original_tax_query, $merging_tax_query, $type = 'tax')
        {
        }
        /**
         * Merging filter's orderby vars to the original orderby vars
         *
         * Filter's orderby vars as priority.
         *
         * @since 1.11.1
         */
        public static function merge_query_filter_orderby($original_orderby, $merging_orderby)
        {
        }
    }
    // Exit if accessed directly
    class Helpers
    {
        /**
         * Get template data from post meta
         *
         * @since 1.0
         */
        public static function get_template_settings($post_id)
        {
        }
        /**
         * Store template settings
         *
         * @since 1.0
         */
        public static function set_template_settings($post_id, $settings)
        {
        }
        /**
         * Remove template settings from store
         *
         * @since 1.0
         */
        public static function delete_template_settings($post_id)
        {
        }
        /**
         * Get individual template setting by key
         *
         * @since 1.0
         */
        public static function get_template_setting($key, $post_id)
        {
        }
        /**
         * Store a specific template setting
         *
         * @since 1.0
         */
        public static function set_template_setting($post_id, $key, $setting_value)
        {
        }
        /**
         * Get terms
         *
         * @param string $taxonomy Taxonomy name.
         * @param string $post_type Post type name.
         * @param string $include_all Includes meta terms like "All terms (taxonomy name)".
         *
         * @since 1.0
         */
        public static function get_terms_options($taxonomy = null, $post_type = null, $include_all = false)
        {
        }
        /**
         * Get users (for templatePreview)
         *
         * @param array $args Query args.
         * @param bool  $show_role Show user role.
         *
         * @uses templatePreviewAuthor
         *
         * @since 1.0
         */
        public static function get_users_options($args, $show_role = false)
        {
        }
        /**
         * Get post edit link with appended query string to trigger builder
         *
         * @since 1.0
         */
        public static function get_builder_edit_link($post_id = 0)
        {
        }
        /**
         * Get supported post types
         *
         * @since 1.0
         *
         * @return array
         */
        public static function get_supported_post_types()
        {
        }
        /**
         * Get registered post types
         *
         * Key: Post type name
         * Value: Post type label
         *
         * @since 1.0
         *
         * @return array
         */
        public static function get_registered_post_types()
        {
        }
        /**
         * Is current post type supported by builder
         *
         * @since 1.0
         *
         * @return boolean
         */
        public static function is_post_type_supported($post_id = 0)
        {
        }
        /**
         * Return page-specific title
         *
         * @param int  $post_id
         * @param bool $context
         *
         * @see https://developer.wordpress.org/reference/functions/get_the_archive_title/
         *
         * @since 1.0
         *
         * @return string
         */
        public static function get_the_title($post_id = 0, $context = false)
        {
        }
        /**
         * Get the queried object which could also be set if previewing a template
         *
         * @see: https://developer.wordpress.org/reference/functions/get_queried_object/
         *
         * @param int $post_id
         *
         * @return WP_Term|WP_User|WP_Post|WP_Post_Type
         */
        public static function get_queried_object($post_id)
        {
        }
        /**
         * Calculate the excerpt of a post (product, or any other cpt)
         *
         * @param WP_Post $post
         * @param int     $excerpt_length
         * @param string  $excerpt_more
         * @param boolean $keep_html
         */
        public static function get_the_excerpt($post, $excerpt_length, $excerpt_more = null, $keep_html = false)
        {
        }
        /**
         * Trim a text string to a certain number of words.
         *
         * @since 1.6.2
         *
         * @param string  $text
         * @param int     $length
         * @param string  $more
         * @param boolean $keep_html
         */
        public static function trim_words($text, $length, $more = null, $keep_html = false, $wpautop = true)
        {
        }
        /**
         * Posts navigation
         *
         * @return string
         *
         * @since 1.0
         */
        public static function posts_navigation($current_page, $total_pages)
        {
        }
        /**
         * Pagination within post
         *
         * To add ul > li structure as 'link_before' & 'link_after' are not working.
         *
         * @since 1.8
         */
        public static function page_break_navigation()
        {
        }
        /**
         * Element placeholder HTML
         *
         * @since 1.0
         */
        public static function get_element_placeholder($data = [], $type = 'info')
        {
        }
        /**
         * Retrieves the element, the complete set of elements and the template/page ID where element belongs to
         *
         * NOTE: This function does not check for global element settings.
         *
         * @since 1.5
         */
        public static function get_element_data($post_id, $element_id)
        {
        }
        /**
         * Get element settings (for use in AJAX functions such as form submit, pagination, etc.)
         *
         * @since 1.0
         */
        public static function get_element_settings($post_id = 0, $element_id = 0, $global_id = 0)
        {
        }
        /**
         * Get data of specific global element
         *
         * @param array $element
         *
         * @return boolean|array false if no global element found, else return the global element data.
         *
         * @since 1.3.5
         */
        public static function get_global_element($element = [], $key = '')
        {
        }
        /**
         * Get posts options (max 50 results)
         *
         * @since 1.0
         */
        public static function get_posts_by_post_id($query_args = [])
        {
        }
        /**
         * Get a list of supported content types for template preview
         *
         * @return array
         */
        public static function get_supported_content_types()
        {
        }
        /**
         * Get editor mode of requested page
         *
         * @param int $post_id
         *
         * @since 1.0
         */
        public static function get_editor_mode($post_id = 0)
        {
        }
        /**
         * Check if post/page/cpt renders with Bricks
         *
         * @param int $post_id / $queried_object_id The post ID.
         *
         * @return boolean
         */
        public static function render_with_bricks($post_id = 0)
        {
        }
        /**
         * Get Bricks data for requested page
         *
         * @param int    $post_id The post ID.
         * @param string $type header, content, footer.
         *
         * @since 1.3.4
         *
         * @return boolean|array
         */
        public static function get_bricks_data($post_id = 0, $type = 'content')
        {
        }
        public static function delete_bricks_data_by_post_id($post_id = 0)
        {
        }
        /**
         * Generate random hash
         *
         * Default: 6 characters long
         *
         * @return string
         *
         * @since 1.0
         */
        public static function generate_hash($string, $length = 6)
        {
        }
        public static function generate_random_id($echo = true)
        {
        }
        /**
         * Generate new & unique IDs for Bricks elements
         *
         * @since 1.9.8
         */
        public static function generate_new_element_ids($elements = [])
        {
        }
        /**
         * Get file contents from file system
         *
         * .svg, .json (Google fonts), etc.
         *
         * @since 1.8.1
         */
        public static function file_get_contents($file_path, ...$args)
        {
        }
        /**
         * Return WP dashboard Bricks settings url
         *
         * @since 1.0
         */
        public static function settings_url($params = '')
        {
        }
        /**
         * Return Bricks Academy link
         *
         * @since 1.0
         */
        public static function article_link($path, $text)
        {
        }
        /**
         * Return the edit post link (ot the preview post link)
         *
         * @param int $post_id The post ID.
         *
         * @since 1.2.1
         */
        public static function get_preview_post_link($post_id)
        {
        }
        /**
         * Dev helper to var dump nicely formatted
         *
         * @since 1.0
         */
        public static function pre_dump($data)
        {
        }
        /**
         * Dev helper to error log array values
         *
         * @since 1.0
         */
        public static function log($data)
        {
        }
        /**
         * Logs a message if WordPress debug is enabled.
         *
         * @param string $message The message to log.
         *
         * @since 1.10.2 (for WPML)
         */
        public static function maybe_log($message)
        {
        }
        /**
         * Custom wp_remote_get function
         */
        public static function remote_get($url, $args = [])
        {
        }
        /**
         * Custom wp_remote_post function
         *
         * @since 1.3.5
         */
        public static function remote_post($url, $args = [])
        {
        }
        /**
         * Generate swiperJS breakpoint data-options (carousel, testimonial)
         *
         * Set slides to show & scroll per breakpoint.
         * Swiper breakpoint values use "min-width". so descent breakpoints from largest to smallest.
         *
         * https://swiperjs.com/swiper-api#param-breakpoints
         *
         * @since 1.3.5
         *
         * @since 1.5.1: removed old 'responsive' repeater controls due to custom breakpoints
         */
        public static function generate_swiper_breakpoint_data_options($settings)
        {
        }
        /**
         * Generate swiperJS autoplay options (carousel, slider, testimonial)
         *
         * @since 1.5.7
         */
        public static function generate_swiper_autoplay_options($settings)
        {
        }
        /**
         * Verifies the integrity of the data using a hash
         *
         * @param string $hash The hash to compare against.
         * @param mixed  $data The data to verify.
         *
         * @since 1.9.7
         *
         * @return bool True if the data is valid, false otherwise.
         */
        public static function verify_code_signature($hash, $data)
        {
        }
        /**
         * Code element settings: code, + executeCode
         * Query Loop settings: useQueryEditor + queryEditor
         */
        public static function sanitize_element_php_code($post_id, $element_id, $code, $signature)
        {
        }
        /**
         * Check if code execution is enabled
         *
         * Via filter or Bricks setting.
         *
         * @since 1.9.7: Code execution is disabled by default.
         *
         * @return boolean
         */
        public static function code_execution_enabled()
        {
        }
        /**
         * Sanitize value
         */
        public static function sanitize_value($value)
        {
        }
        /**
         * Sanitize Bricks data
         *
         * During template import, etc.
         *
         * @since 1.3.7
         */
        public static function sanitize_bricks_data($elements)
        {
        }
        /**
         * Set is_frontend = false to a element
         *
         * Use: $elements = array_map( 'Bricks\Helpers::set_is_frontend_to_false', $elements );
         *
         * @since 1.4
         */
        public static function set_is_frontend_to_false($element)
        {
        }
        /**
         * Get post IDs of all Bricks-enabled post types
         *
         * @see admin.php get_converter_items()
         * @see files.php get_css_files_list()
         *
         * @param array $custom_args Custom get_posts() arguments (@since 1.8; @see get_css_files_list).
         *
         * @since 1.4
         */
        public static function get_all_bricks_post_ids($custom_args = [])
        {
        }
        /**
         * Search & replace: Works for strings & arrays
         *
         * @param string $search  The value being searched for.
         * @param string $replace The replacement value that replaces found search values.
         * @param string $data    The string or array being searched and replaced on, otherwise known as the haystack.
         *
         * @see templates.php import_template()
         *
         * @since 1.4
         */
        public static function search_replace($search, $replace, $data)
        {
        }
        /**
         * Google fonts are disabled (via filter OR Bricks setting)
         *
         * @see https://academy.bricksbuilder.io/article/filter-bricks-assets-load_webfonts
         *
         * @since 1.4
         */
        public static function google_fonts_disabled()
        {
        }
        /**
         * Sort variable Google Font axis (all lowercase before all uppercase)
         *
         * https://developers.google.com/fonts/docs/css2#strictness
         *
         * @since 1.8
         */
        public static function google_fonts_get_axis_rank($axis)
        {
        }
        /**
         * Stringify HTML attributes
         *
         * @param array $attributes key = attribute key; value = attribute value (string|array).
         *
         * @see bricks/header/attributes
         * @see bricks/footer/attributes
         * @see bricks/popup/attributes
         *
         * @return string
         *
         * @since 1.5
         */
        public static function stringify_html_attributes($attributes)
        {
        }
        /**
         * Validate HTML attributes
         *
         * NOTE: Not in use yet to prevent breaking changes of unintended removal of attributes.
         *
         * @since 1.10.2
         */
        public static function is_valid_html_attribute_name($attribute_name)
        {
        }
        /**
         * Return element attribute 'id'
         *
         * @since 1.5.1
         *
         * @since 1.7.1: Parse dynamic data for _cssId (same for _cssClasses)
         */
        public static function get_element_attribute_id($id, $settings)
        {
        }
        /**
         * Based on the current user capabilities, check if the new elements could be changed on save (AJAX::save_post())
         *
         * If user can only edit the content:
         *  - Check if the number of elements is the same
         *  - Check if the new element already existed before
         *
         * If user cannot execute code:
         *  - Replace any code element (with execution enabled) by the saved element,
         *  - or disable the execution (in case the element is new)
         *
         * @since 1.5.4
         *
         * @param array  $new_elements Array of elements.
         * @param int    $post_id The post ID.
         * @param string $area 'header', 'content', 'footer'.
         *
         * @return array Array of elements
         */
        public static function security_check_elements_before_save($new_elements, $post_id, $area)
        {
        }
        /**
         * Parse CSS & return empty string if checks are not fulfilled
         *
         * @since 1.6.2
         */
        public static function parse_css($css)
        {
        }
        /**
         * Save global classes in options table
         *
         * Skip saving empty global classes array.
         *
         * Triggered in:
         *
         * ajax.php:      wp_ajax_bricks_save_post (save post in builder)
         * templates.php: wp_ajax_bricks_import_template (template import)
         * converter.php: wp_ajax_bricks_run_converter (run converter from Bricks settings)
         *
         * @since 1.7
         *
         * @param array  $global_classes
         * @param string $action
         */
        public static function save_global_classes_in_db($global_classes)
        {
        }
        /**
         * Save global variables in options table
         *
         * @param array $global_variables
         * @return mixed
         * @since 1.9.8
         */
        public static function save_global_variables_in_db($global_variables)
        {
        }
        /**
         * Parse TinyMCE editor control data
         *
         * Use instead of applying 'the_content' filter to prevent rendering third-party content in within non "Post Content" elements.
         *
         * Available as static function to use in get_dynamic_data_preview_content as well (DD tag render on canvas)
         *
         * @see accordion, alert, icon-box, slider, tabs, text
         *
         * @since 1.7
         */
        public static function parse_editor_content($content = '')
        {
        }
        /**
         * Check if post_id is a Bricks template
         *
         * Previously used get_post_type( $post_id ) === BRICKS_DB_TEMPLATE_SLUG
         * But this method might accidentally return true if $post_id is a term_id or user_id, etc.
         *
         * @since 1.8
         */
        public static function is_bricks_template($post_id)
        {
        }
        /**
         * Check if current request is Bricks preview
         *
         * @since 1.9.5
         */
        public static function is_bricks_preview()
        {
        }
        /**
         * Check if the element settings contain a specific value
         *
         * Useful if the setting has diffrent keys in different breakpoints.
         *
         * Example: 'overlay', 'overlay:mobile_portrait', 'overlay:tablet_landscape', etc.
         *
         * Usage:
         * Helpers::element_setting_has_value( 'overlay', $settings ); // Check if $settings contains 'overlay' setting in any breakpoint
         * Helpers::element_setting_has_value( 'overlay:mobile', $settings ); // Check if $settings contains 'overlay' setting in mobile breakpoint
         *
         * @since 1.8
         *
         * @param string $key
         * @param array  $settings
         *
         * @return bool
         */
        public static function element_setting_has_value($key = '', $settings = [])
        {
        }
        /**
         * Check if the provided url string is the current landed page
         *
         * @since 1.8
         *
         * @param string $url
         * @return bool
         */
        public static function maybe_set_aria_current_page($url = '')
        {
        }
        /**
         * Check recursively if a post is an ancestor of another post.
         *
         * @since 1.8
         *
         * @param int $post_id
         * @param int $ancestor_id
         * @return bool
         */
        public static function is_post_ancestor($post_id, $ancestor_id)
        {
        }
        /**
         * Parse textarea content to account for dynamic data usage
         *
         * Useful in 'One option / feature per line' situations
         *
         * Examples: Form element options (Checkbox, Select, Radio) or Pricing Tables (Features)
         *
         * @since 1.9
         *
         * @param string $options
         * @return array
         */
        public static function parse_textarea_options($options)
        {
        }
        /**
         * Use user agent string to detect browser
         *
         * @since 1.9.2
         */
        public static function user_agent_to_browser($user_agent)
        {
        }
        /**
         * Use user agent string to detect operating system
         *
         * @since 1.9.2
         * @since 1.10.2 Sequence matters: Start with the most specific one!
         */
        public static function user_agent_to_os($user_agent)
        {
        }
        /**
         * Get user IP address
         *
         * @since 1.9.2
         */
        public static function user_ip_address()
        {
        }
        /**
         * Populate query_vars to be used in Bricks template preview based on "Populate content" settings
         *
         * @since 1.9.1
         */
        public static function get_template_preview_query_vars($post_id)
        {
        }
        /**
         * Populate query vars if the element is not using query type controls
         * Currently used in related-posts element and query_results_count when targeting related-posts element
         *
         * @return array
         * @since 1.9.3
         */
        public static function populate_query_vars_for_element($element_data, $post_id)
        {
        }
        /**
         * Check if Query Filters are enabled (in Bricks settings)
         *
         * @since 1.9.6
         */
        public static function enabled_query_filters()
        {
        }
        /**
         * Check if Query Filters integration is enabled in Bricks settings
         *
         * @since 1.11.1
         */
        public static function enabled_query_filters_integration()
        {
        }
        /**
         * Implode an array safely to avoid PHP warnings
         *
         * @since 1.10
         */
        public static function safe_implode($sep, $value)
        {
        }
        /**
         * Builds a hierarchical tree structure from a flat array of Bricks elements.
         *
         * The tree structure is used to process each element in a depth-first manner to maintain the hierarchy.
         *
         * Each element in the input array should be an associative array with the following structure:
         * - id (string): Unique identifier for the element.
         * - name (string): The name/type of the element (e.g., 'section', 'container', 'heading').
         * - parent (string|int|null): The ID of the parent element, or 0/null if it is a root element.
         * - children (array): An array of child element IDs (strings). Defaults to an empty array.
         * - settings (array): An associative array of settings specific to the element. Defaults to an empty array.
         *
         * Example:
         * [
         *   [
         *     "id" => "puikcj",
         *     "name" => "section",
         *     "parent" => 0,
         *     "children" => ["vtjutb"],
         *     "settings" => []
         *   ],
         *   [
         *     "id" => "vtjutb",
         *     "name" => "container",
         *     "parent" => "puikcj",
         *     "children" => ["jjnqht", "yvldmi", "cvrpll", "paayqb"],
         *     "settings" => []
         *   ],
         *   [
         *     "id" => "jjnqht",
         *     "name" => "heading",
         *     "parent" => "vtjutb",
         *     "children" => [],
         *     "settings" => ["text" => "I am a heading 1"]
         *   ]
         *   // More elements...
         * ]
         *
         * @param array $elements
         * @return array
         *
         * @since 1.10.2
         */
        public static function build_elements_tree($elements)
        {
        }
        /**
         * Get HTML tag name from element settings
         *
         * @param array  $settings Element settings.
         * @param string $default_tag Default tag name.
         *
         * @since 1.10.2
         */
        public static function get_html_tag_from_element_settings($settings, $default_tag)
        {
        }
        /**
         * Sanitize HTML tag
         *
         * @since 1.10.2
         */
        public static function sanitize_html_tag($tag, $default_tag)
        {
        }
        /**
         * Return all allowed HTML tags
         *
         * @since 1.10.3
         */
        public static function get_allowed_html_tags()
        {
        }
        /**
         * Apply wp_filter_post_kses to all string values in an array
         *
         * @since 1.11
         */
        public static function apply_wp_filter_post_kses_to_array(&$item, $key)
        {
        }
    }
    // Exit if accessed directly
    class Admin
    {
        const EDITING_CAP = 'edit_posts';
        public function __construct()
        {
        }
        /**
         * Add meta box: Template type
         *
         * @since 1.0
         */
        public function add_meta_boxes()
        {
        }
        /**
         * Meta box: Template type render
         *
         * @since 1.0
         */
        public function meta_box_template_type($post)
        {
        }
        /**
         * Meta box: Save/delete template type
         *
         * @since 1.0
         */
        public function meta_box_save_post($post_id)
        {
        }
        /**
         * Register dashboard widget
         *
         * NOTE: Not in use, yet.
         *
         * @since 1.0
         */
        public function wp_dashboard_setup()
        {
        }
        /**
         * Render dashboard widget
         *
         * @since 1.0
         */
        public function dashboard_widget()
        {
        }
        /**
         * Post custom column
         *
         * @since 1.0
         */
        public function posts_custom_column($column, $post_id)
        {
        }
        /**
         * Add bulk action "Export"
         *
         * @since 1.0
         */
        public function bricks_template_bulk_action_export($actions)
        {
        }
        /**
         * Handle bulk action "Export"
         *
         * @param string $redirect_url Redirect URL.
         * @param string $doaction     Action to run.
         * @param array  $items        Items to run action on.
         *
         * @since 1.0
         */
        public function bricks_template_handle_bulk_action_export($redirect_url, $doaction, $items)
        {
        }
        /**
         * Export templates
         *
         * @param array $template_ids IDs of templates to export.
         *
         * @since 1.0
         */
        public function export_templates($template_ids)
        {
        }
        /**
         * Import templates form
         *
         * @since 1.0
         */
        public function import_templates_form()
        {
        }
        /**
         * Template type filter dropdown
         *
         * @since 1.9.3
         */
        public function template_type_filter_dropdown()
        {
        }
        /**
         * Template type filter query
         *
         * @since 1.9.3
         */
        public function template_type_filter_query($query)
        {
        }
        /**
         * Import global settings
         *
         * @since 1.0
         */
        public function import_global_settings()
        {
        }
        /**
         * Generate and download JSON file with global settings
         *
         * @since 1.0
         */
        public static function export_global_settings()
        {
        }
        /**
         * Save settings in WP dashboard on form 'save' submit
         *
         * @since 1.0
         */
        public function save_settings()
        {
        }
        /**
         * Reset settings in WP dashboard on form 'reset' submit
         *
         * @since 1.0
         */
        public function reset_settings()
        {
        }
        /**
         * Template columns
         *
         * @since 1.0
         */
        public function bricks_template_posts_columns($columns)
        {
        }
        /**
         * Template custom column
         *
         * @since 1.0
         */
        public function bricks_template_posts_custom_column($column, $post_id)
        {
        }
        /**
         * Set default settings
         *
         * @since 1.0
         */
        public static function set_default_settings()
        {
        }
        public function gutenberg_scripts()
        {
        }
        /**
         * Admin scripts and styles
         *
         * @since 1.0
         */
        public function admin_enqueue_scripts()
        {
        }
        /**
         * Admin menu
         *
         * @since 1.0
         */
        public function admin_menu()
        {
        }
        public function admin_screen_getting_started()
        {
        }
        public function admin_screen_settings()
        {
        }
        public function admin_screen_sidebars()
        {
        }
        public function admin_screen_system_information()
        {
        }
        public function admin_screen_license()
        {
        }
        /**
         * Form submissions admin screen
         *
         * @since 1.9.2
         */
        public function admin_screen_form_submissions()
        {
        }
        /**
         * Admin notice: Show regenerate CSS files notification after Bricks theme update
         *
         * @since 1.3.7
         */
        public static function admin_notice_regenerate_css_files()
        {
        }
        /**
         * Admin notice: Show missing code signatures notification
         *
         * @since 1.9.7
         */
        public static function admin_notice_code_signatures()
        {
        }
        /**
         * Admin notices
         *
         * @since 1.0
         */
        public function admin_notices()
        {
        }
        public static function admin_notice_html($type, $text, $dismissible = true, $extra_classes = '')
        {
        }
        /**
         * Add custom post state: "Bricks"
         *
         * If post has last been saved with Bricks (check post meta value: '_bricks_editor_mode')
         *
         * @param array    $post_states Array of post states.
         * @param \WP_Post $post        Current post object.
         *
         * @since 1.0
         */
        public function add_post_state($post_states, $post)
        {
        }
        /**
         * Add editor body class 'active'
         *
         * @since 1.0
         *
         * @return string
         */
        public function admin_body_class($classes)
        {
        }
        /**
         * Add custom image sizes to WordPress media library in admin area
         *
         * Also used to build dropdown of control 'images' for single image element.
         *
         * @since 1.0
         */
        public function image_size_names_choose($default_sizes)
        {
        }
        /**
         * Make sure 'editor_mode' URL param is not removed from admin URL
         */
        public function admin_url($link)
        {
        }
        /**
         * Save Editor mode based on the admin bar links
         *
         * @see Setup->admin_bar_menu()
         *
         * @since 1.3.7
         */
        public function save_editor_mode()
        {
        }
        /**
         * Builder tab HTML (toggle via builder tab)
         *
         * @since 1.0
         *
         * @return string
         */
        public function builder_tab_html()
        {
        }
        /**
         * "Edit with Bricks" link for post type 'page', 'post' and all other CPTs
         *
         * @since 1.0
         */
        public function row_actions($actions, $post)
        {
        }
        /**
         * Dismiss HTTPS notice
         *
         * @since 1.8.4
         */
        public function dismiss_https_notice()
        {
        }
        /**
         * Delete form submissions table
         *
         * @since 1.9.2
         */
        public function form_submissions_drop_table()
        {
        }
        /**
         * Reset/clear all form submissions table entries (rows)
         *
         * @since 1.9.2
         */
        public function form_submissions_reset_table()
        {
        }
        /**
         * Delete form submissions of form ID
         *
         * @since 1.9.2
         */
        public function form_submissions_delete_form_id()
        {
        }
        /**
         * Show admin notice
         *
         * @param string $message Notice message
         * @param string $type    success|error|warning|info
         * @param string $class   Additional CSS class
         *
         * @since 1.9.1
         */
        public static function show_admin_notice($message, $type = 'success', $class = '')
        {
        }
        /**
         * Dismiss Instagram access token notice
         *
         * @since 1.9.1
         */
        public function dismiss_instagram_access_token_notice()
        {
        }
        /**
         * Reindex query filters
         *
         * @since 1.9.6
         */
        public function reindex_query_filters()
        {
        }
        /**
         * Regenerate code signatures
         *
         * @since 1.9.7
         */
        public function regenerate_code_signatures()
        {
        }
        /**
         * Return query args for code signature regeneration & code review results
         *
         * @see Code review && crawl_and_update_code_signatures below.
         *
         * @since 1.9.7
         */
        public static function get_code_instances_query_args($filter = false)
        {
        }
        /**
         * Update code signatures for all Bricks data & global elemnts
         *
         * @since 1.9.7
         *
         * @param bool $only_regenerate_if_missing If true, only regenerate the signature if it's missing.
         */
        public static function crawl_and_update_code_signatures($only_regenerate_if_missing = false)
        {
        }
        /**
         * Process code and svg elements and queryEditors to add a code signature to element settings
         *
         * @since 1.9.7
         *
         * @param array $elements
         * @param bool  $only_regenerate_if_missing If true, only regenerate the signature if it's missing.
         */
        public static function process_elements_for_signature($elements = [], $only_regenerate_if_missing = false, $strip_slashes = false)
        {
        }
        /**
         * Duplicate page or post in WP admin (Bricks or WordPress)
         *
         * @since 1.9.8
         */
        public function bricks_duplicate_content()
        {
        }
        /**
         * Duplicate page or post incl. taxnomy terms (Bricks or WordPress)
         *
         * Handles Bricks data ID duplication as well.
         *
         * @param int $post_id
         * @return int|bool
         * @since 1.9.8
         */
        public static function duplicate_content($post_id = 0)
        {
        }
        /**
         * Delete template screenshots
         *
         * @since 1.10
         */
        public function delete_template_screenshots()
        {
        }
        /**
         * Manually trigger index job
         *
         * @since 1.10
         */
        public function run_index_job()
        {
        }
        /**
         * Remove all index jobs
         *
         * @since 1.11
         */
        public function remove_all_index_jobs()
        {
        }
        /**
         * System information wp_remote_post test
         *
         * To debug query filter index issue.
         *
         * @since 1.11
         */
        public function system_info_wp_remote_post_test()
        {
        }
    }
    // Exit if accessed directly
    class Assets_Global_Variables
    {
        public function __construct()
        {
        }
        public function updated($old_value, $value)
        {
        }
        public static function generate_css_file($global_variables)
        {
        }
    }
    // Exit if accessed directly
    class Assets_Theme_Styles
    {
        public function __construct()
        {
        }
        /**
         * Theme Styles updated in database: Regenerate CSS files for every theme style
         *
         * @since 1.3.4
         */
        public function updated($mix, $value)
        {
        }
        /**
         * Generate/delete theme style CSS files
         *
         * Naming convention: theme-style-{theme_style_name}.min.css
         *
         * @since 1.3.4
         *
         * @return array File names
         */
        public static function generate_css_file($theme_styles)
        {
        }
    }
    // Exit if accessed directly
    class Assets_Files
    {
        public function __construct()
        {
        }
        /**
         * Auto-regenerate CSS files after theme update
         *
         * Runs after updating the theme via the one-click updater!
         *
         * NOTE: Not in use
         *
         * @since 1.8.1
         */
        public function __upgrader_process_complete($upgrader, $hook_extra)
        {
        }
        /**
         * Auto-regenerate CSS files after theme update
         *
         * Runs after manual theme upload!
         *
         * @since 1.8.1
         */
        public function upgrader_post_install($response, $hook_extra, $result)
        {
        }
        /**
         * Schedule single WP cron job to regenerate CSS files after theme update (one-click & manual upload)
         *
         * Runs 'bricks_regenerate_css_files' after 1 second to make sure the theme is updated.
         *
         * @since 1.8.1
         */
        public static function schedule_css_file_regeneration()
        {
        }
        /**
         * Regenerate CSS files automatically after theme update
         *
         * @since 1.8.1
         */
        public static function regenerate_css_files()
        {
        }
        /**
         * Regenerate CSS file on every post save
         *
         * Catches Bricks builder & WordPress editor saves (CU #3kavbt2)
         *
         * Example: User updates a custom field like ACF color, etc. in WP editor
         *
         * @since 1.5.7
         */
        public function save_post($post_id, $post)
        {
        }
        /**
         * Post deleted: Delete post CSS file
         *
         * @param int    $post_id The post ID.
         * @param object $post The post object.
         *
         * @since 1.3.4
         */
        public function deleted_post($post_id, $post)
        {
        }
        /**
         * Frontend: Load assets (CSS & JS files) on requested page
         *
         * @since 1.3.4
         */
        public function load_css_files()
        {
        }
        /**
         * Check inside template elements and post content for other CSS file needs
         *
         * @since 1.5.7
         */
        public function load_content_extra_css_files($content_elements = [])
        {
        }
        /**
         * Builder: Generate page-specific CSS file (on builder save)
         *
         * @param int    $post_id Post ID.
         * @param string $content_type header/content/footer (to get correct Bricks post meta data).
         * @param array  $elements Array of elements.
         *
         * @return void|string File name
         *
         * @since 1.3.4
         */
        public static function generate_post_css_file($post_id, $content_type, $elements)
        {
        }
        /**
         * Generate individual CSS file
         *
         * @param string $data The type of CSS file to generate: colorPalettes, themeStyles, individual post ID, etc.
         * @param string $index The index of the CSS file to generate (e.g. 0 = colorPalettes, 1 = themeStyles, etc.).
         * @param bool   $return Whether to return the generated CSS file name or not.
         *
         * Trigger 1: Click on "Regenerate CSS files" button under "CSS loading method - External Files" in Bricks settings.
         * Trigger 2: Edit default breakpoint 'width' (@since 1.5.1)
         * Trigger 3: CLI command: wp bricks regenerate_assets (@since 1.8.1)
         * Trigger 4: Theme update (one-click & manual upload) (@since 1.8.1)
         */
        public static function regenerate_css_file($data = false, $index = false, $return = false)
        {
        }
        /**
         * Return CSS files list to frontend for processing one-by-one via AJAX 'bricks_regenerate_css_file'
         *
         * NOTE: Generate global CSS classes inline (need to know which element(s) a global class is actually set for).
         *
         * @return array
         */
        public static function get_css_files_list($return = false)
        {
        }
    }
    // Exit if accessed directly
    class Assets_Global_Elements
    {
        public function __construct()
        {
        }
        public function updated($mix, $value)
        {
        }
        public static function generate_css_file($global_elements)
        {
        }
    }
    // Exit if accessed directly
    class Assets_Global_Custom_Css
    {
        public function __construct()
        {
        }
        public function added($option_name, $value)
        {
        }
        public function updated($old_value, $value, $option_name)
        {
        }
        public static function generate_css_file($global_settings)
        {
        }
    }
    // Exit if accessed directly
    class Assets_Color_Palettes
    {
        public function __construct()
        {
        }
        /**
         * Color palette database option updated: Generate/delete CSS file
         *
         * @since 1.3.4
         */
        public function updated($mix, $value)
        {
        }
        /**
         * Generate/delete color palettes CSS file
         *
         * @since 1.3.4
         *
         * @return void|string File name
         */
        public static function generate_css_file($color_palettes)
        {
        }
    }
    // Exit if accessed directly
    abstract class Style_Base
    {
        public $id;
        public $label;
        public $settings;
        public function __construct()
        {
        }
        public function get_id()
        {
        }
        public function get_label()
        {
        }
        public function get_settings()
        {
        }
        public function get_style_data()
        {
        }
    }
    // Exit if accessed directly
    class Feedback
    {
        public function __construct()
        {
        }
        /**
         * Load feedback script on themes.php admin page only
         */
        public function add_feedback_script($hook_suffix)
        {
        }
        /**
         * Render feedback HTML on themes.php admin page only
         */
        public function render_feedback_form()
        {
        }
    }
    // Exit if accessed directly
    class Svg
    {
        /**
         * Enable SVGs uploads
         *
         * https://enshrined.co.uk/2018/04/29/securing-svg-uploads-in-wordpress/
         */
        public function __construct()
        {
        }
        /**
         * Enable SVG uploads
         *
         * @since 1.0
         */
        public function svg_enable_upload($mimes)
        {
        }
        /**
         * Disable real MIME check (introduced in WordPress 4.7.1)
         *
         * https://wordpress.stackexchange.com/a/252296/44794
         *
         * @since 1.0
         */
        public function disable_real_mime_check($data, $file, $filename, $mimes)
        {
        }
        /**
         * Remove img width and height attributes for SVG files, which are set to 1px
         *
         * @since 1.0
         */
        public function svg_one_pixel_fix($image, $attachment_id, $size, $icon)
        {
        }
        public function maybe_sanitize_svg($file)
        {
        }
        /**
         * Uses https://github.com/darylldoyle/svg-sanitizer library
         *
         * @param array $file
         */
        protected function sanitize($file)
        {
        }
        /**
         * Checks if content is gzipped
         *
         * @param string $contents
         *
         * @return boolean
         */
        protected function is_file_gzipped($contents)
        {
        }
        public static function load_libraries()
        {
        }
    }
    // Exit if accessed directly
    /**
     * Responsible for handling the custom redirection logic for authentication-related pages.
     *
     * Login page
     * Registration page
     * Lost password page
     * Reset password page
     *
     * @since 1.9.2
     */
    class Auth_Redirects
    {
        public function __construct()
        {
        }
        /**
         * Main function to handle authentication redirects
         *
         * Depending on the current URL and the action parameter, decides which page to redirect to.
         */
        public function handle_auth_redirects()
        {
        }
        /**
         * Clears the bypass cookie when the user logs in.
         */
        public function clear_bypass_auth_cookie()
        {
        }
        /**
         * Modifies the password reset email to use the custom reset password page URL.
         *
         * This modification only occurs if:
         * 1. A custom reset password page is set
         * 2. The WordPress auth URL behavior is not set to default
         *
         * @since 1.11
         *
         * @param string $message    The current email message.
         * @param string $key        The password reset key.
         * @param string $user_login The username for the user.
         * @param object $user_data  WP_User object.
         *
         * @return string The modified email message.
         */
        public function modify_reset_password_email($message, $key, $user_login, $user_data)
        {
        }
    }
    // Exit if accessed directly
    class Compatibility
    {
        public function __construct()
        {
        }
        public static function register()
        {
        }
        /**
         * Learndash Course Grid Add One: Load assets if shortcode found
         *
         * wp_enqueue_scripts for learndash_course_grid_load_resources() only loads pre 2.0 legacy assets from [ld_course_list]
         *
         * @see class-compatibility.php integration for Elementor
         *
         * @since 1.7
         */
        public function learndash_course_grid_load_assets($course_grids, $post)
        {
        }
        /**
         * LiteSpeed Cache plugin: Ignore Bricks builder
         *
         * Tested with version 3.6.4
         *
         * @return void
         */
        public function litespeed_no_cache()
        {
        }
        /**
         * Weglot: Disable Weglot translations inside the builder
         *
         * @since 1.8.6
         *
         * @return void
         */
        public function weglot_disable_translation()
        {
        }
        /**
         * Check if user has membership access to Bricks content in Helpers::render_with_bricks
         *
         * @since 1.5.4
         */
        public function pmpro_has_membership_access($render)
        {
        }
        /**
         * Yith WooCommerce Product Add-Ons: Dequeue script on builder as it conflicts with Bricks drag & drop
         *
         * @since 1.6.2
         */
        public function yith_wapo_dequeue_script()
        {
        }
    }
    // Exit if accessed directly
    /**
     * Autoloads plugin classes using PSR-4.
     */
    class Autoloader
    {
        /**
         * Handle autoloading of PHP classes
         *
         * @param String $class
         * @return void
         */
        public static function autoload($class)
        {
        }
        public static function load_functions()
        {
        }
        /**
         * Register SPL autoloader
         *
         * @param bool $prepend
         */
        public static function register($prepend = false)
        {
        }
    }
    // Exit if accessed directly
    class Assets
    {
        public static $wp_uploads_dir = '';
        public static $css_dir = '';
        public static $css_url = '';
        public static $global_colors = [];
        public static $google_fonts_urls = [];
        // @since 1.9.9
        public static $inline_css = ['color_vars' => '', 'theme_style' => '', 'global' => '', 'global_classes' => '', 'global_variables' => '', 'page' => '', 'template' => '', 'header' => '', 'content' => '', 'footer' => '', 'popup' => ''];
        public static $elements = [];
        // Set by Assets_Files::generate_post_css_file() method during AJAX (@since 1.3.6)
        public static $post_id = 0;
        /**
         * Store inline CSS per css_type (content, theme_style, etc.) & breakpoint
         *
         * key: css_type
         * subkeys: breakpoints
         * sub-subkeys: css selector
         */
        public static $inline_css_breakpoints = [];
        public static $global_classes_elements = [];
        // Item = Individual unique CSS rules - avoid inline style duplicates (@since 1.8)
        public static $unique_inline_css = [];
        // Dynamic data CSS string (e.g. dynamic data 'featured_image' set in single post template, etc.)
        public static $inline_css_dynamic_data = '';
        // Stores the post_id values for all the templates and pages where we need to fetch the page settings values
        public static $page_settings_post_ids = [];
        // Keep track of the elements inside of a loop that were already styled - avoid duplicates (@since 1.5)
        public static $css_looping_elements = [];
        // Keep track the common selectors inside of a loop that were already styled - avoid duplicates (@since 1.8)
        public static $generated_loop_common_selectors = [];
        // Keep track of the current element that is being styled (@since 1.8)
        public static $current_generating_element = null;
        // Keep track of element IDs that will add data-loop-index attribute (@since 1.8)
        public static $loop_index_elements = [];
        public function __construct()
        {
        }
        public function schedule_global_classes_trash_cleanup()
        {
        }
        /**
         * Cleanup global classes trash
         *
         * NOTE: Not in use. User has to exclitily delete the global classes from the trash.
         *
         * @since 1.11
         */
        public function cleanup_global_classes_trash()
        {
        }
        /**
         * Helper function to set Bricks assets directory & URL
         *
         * In the constructor and on blog switch (multisite).
         *
         * @since 1.9.9
         */
        public static function set_assets_directory()
        {
        }
        /**
         * CSS loading method "External Files": Autoload PHP files
         *
         * @since 1.3.5
         */
        public static function autoload_files()
        {
        }
        /**
         * Load element setting specific scripts (icon fonts, animations, lightbox, etc.)
         *
         * Run for all CSS loading methods.
         *
         * @since 1.3.4
         */
        public static function enqueue_setting_specific_scripts($settings = [])
        {
        }
        /**
         * Minify CSS string (remove line breaks & tabs)
         *
         * @param string $inline_css CSS string.
         *
         * @since 1.3.4
         */
        public static function minify_css($inline_css)
        {
        }
        /**
         * Generate inline CSS
         *
         * Bricks Settings: "CSS loading Method" set to "Inline Styles" (= default)
         *
         * - Color Vars
         * - Theme Styles
         * - Global CSS Classes
         * - Global Custom CSS
         * - Page Custom CSS
         * - Header
         * - Content
         * - Footer
         * - Custom Fonts
         * - Template
         *
         * @param int $post_id Post ID.
         *
         * @return string $inline_css
         */
        public static function generate_inline_css($post_id = 0)
        {
        }
        /**
         * Generates list of global palette colors as CSS vars
         *
         * @param array $color_palettes
         *
         * @return string
         */
        public static function generate_inline_css_color_vars($color_palettes)
        {
        }
        /**
         * Helper function to generate color code based on color array
         *
         * @param array $color
         *
         * @return string
         */
        public static function generate_css_color($color)
        {
        }
        /**
         * Generate theme style CSS string
         *
         * @return string Inline CSS for theme styles.
         */
        public static function generate_inline_css_theme_style($settings = [])
        {
        }
        /**
         * Get global variables
         *
         * @since 1.9.8
         */
        public static function get_global_variables()
        {
        }
        public static function format_variables_as_css($variables)
        {
        }
        /**
         * Generate global classes CSS string
         *
         * @return string Styles for global classes.
         */
        public static function generate_global_classes()
        {
        }
        public static function generate_inline_css_page_settings()
        {
        }
        /**
         * Get page settings scripts
         *
         * @param string $script_key customScriptsHeader, customScriptsBodyHeader, customScriptsBodyFooter.
         *
         * @return string
         */
        public static function get_page_settings_scripts($script_key = '')
        {
        }
        /**
         * Load Adobe & Google fonts according to inline CSS (source of truth) and remove loading wrapper
         */
        public static function load_webfonts($inline_css)
        {
        }
        /**
         * Loop over repeater items to generate CSS for each item (e.g. Slider 'items')
         *
         * @since 1.3.5
         */
        public static function generate_inline_css_from_repeater($settings, $repeater_items, $css_selector, $repeater_control, $css_type)
        {
        }
        /**
         * Generate CSS string from individual setting
         *
         * @return array key: CSS selector. value: array of CSS rules for this CSS selector.
         *
         * @since 1.3.5
         */
        public static function generate_css_rules_from_setting($settings, $setting_key, $setting_value, $controls, $selector, $css_type)
        {
        }
        /**
         * Generate CSS string
         *
         * @param array  $element Array containing all element data (to retrieve element settings and name).
         * @param array  $controls Array containing all element controls (to retrieve CSS selectors and properties).
         * @param string $css_type String global/page/header/content/footer/mobile.
         *
         * @return string (use & process asset-optimization)
         */
        public static function generate_inline_css_from_element($element, $controls, $css_type)
        {
        }
        /**
         * Generate inline CSS for breakpoints of specific type (content, theme_style, etc.)
         *
         * @since 1.3.5
         *
         * @return string
         */
        public static function generate_inline_css_for_breakpoints($css_type, $desktop_css)
        {
        }
        /**
         * Get @media rule for specific breakpoint
         *
         * @param string $bp The breakpoint key to return the @media rule for.
         *
         * @since 1.7.2
         */
        public static function get_at_media_rule_for_breakpoint($bp)
        {
        }
        /**
         * Generate CSS from elements
         *
         * @param array  $elements Array to loop through all the elements to generate CSS string of entire data.
         * @param string $css_type header, footer, content, etc. (see: $inline_css).
         *
         * @return void
         */
        public static function generate_css_from_elements($elements, $css_type)
        {
        }
        public static function generate_css_from_element($element, $css_type)
        {
        }
        /**
         * Add the attribute [data-query-loop-index] to the current style element
         *
         * Only add HTML attribute once per element ID.
         *
         * @since 1.8
         */
        public static function maybe_add_query_loop_index_attribute_to_element()
        {
        }
    }
    // Exit if accessed directly
    class Elements
    {
        public static $elements = [];
        public function __construct()
        {
        }
        public static function init_elements()
        {
        }
        /**
         * Register element (built-in and custom elements via child theme)
         *
         * Element 'name' and 'class' only to load element on frontend when requested.
         */
        public static function register_element($file, $element_name = '', $element_class_name = '')
        {
        }
        /**
         * Load elements on 'wp' hook to get post_id for controls, etc.
         */
        public static function load_elements()
        {
        }
        public static function load_element($element_name)
        {
        }
        /**
         * Get specific element
         *
         * @param array  $element Array containing all element data. Use to retrieve element name.
         * @param string $element_property String to retrieve specific element data. Such as 'controls' for CSS string generation.
         */
        public static function get_element($element, $element_property = '')
        {
        }
    }
    // Exit if accessed directly
    class Frontend
    {
        public static $area = 'content';
        /**
         * Elements requested for rendering
         *
         * key: ID
         * value: element data
         */
        public static $elements = [];
        /**
         * Live search results selectors
         *
         * key: live search ID
         * value: live search results CSS selector
         *
         * @since 1.9.6
         */
        public static $live_search_wrapper_selectors = [];
        public function __construct()
        {
        }
        /**
         * Add header scripts
         *
         * Do not add template JS (we only want to provide content)
         *
         * @since 1.0
         */
        public function add_header_scripts()
        {
        }
        /**
         * Page settings: Add meta description, keywords and robots
         */
        public function add_seo_meta_tags()
        {
        }
        /**
         * Page settings: Set document title
         *
         * @param array $title
         *
         * @see https://developer.wordpress.org/reference/hooks/document_title_parts/
         *
         * @since 1.6.1
         */
        public function set_seo_document_title($title)
        {
        }
        /**
         * Add Facebook Open Graph Meta Data
         *
         * https://ogp.me
         *
         * @since 1.0
         */
        public function add_open_graph_meta_tags()
        {
        }
        /**
         * Add body header scripts
         *
         * NOTE: Do not add template JS (we only want to provide content)
         *
         * @since 1.0
         */
        public function add_body_header_scripts()
        {
        }
        /**
         * Add body footer scripts
         *
         * NOTE: Do not add template JS (only provide content)
         *
         * @since 1.0
         */
        public function add_body_footer_scripts()
        {
        }
        /**
         * Enqueue styles and scripts
         */
        public function enqueue_scripts()
        {
        }
        /**
         * Enqueue inline CSS
         *
         * @since 1.8.2 using wp_footer instead of wp_enqueue_scripts to get all dynamic data styles & global classes
         */
        public function enqueue_inline_css()
        {
        }
        /**
         * Enqueue inline CSS in wp_footer: Global classes (Template element) & dynamic data
         *
         * @since 1.8.2
         */
        public function enqueue_footer_inline_css()
        {
        }
        /**
         * Get element content wrapper
         */
        public static function get_content_wrapper($settings, $fields, $post)
        {
        }
        /**
         * Render element recursively
         *
         * @param array $element
         */
        public static function render_element($element)
        {
        }
        /**
         * Render element 'children' (= nestable element)
         *
         * @param array  $element_instance Instance of the element.
         * @param string $tag Tag name.
         * @param array  $extra_attributes Extra attributes.
         *
         * @since 1.5
         */
        public static function render_children($element_instance = null, $tag = 'div', $extra_attributes = [])
        {
        }
        /**
         * Return rendered elements (header/content/footer)
         *
         * @param array  $elements Array of Bricks elements.
         * @param string $area     header/content/footer.
         *
         * @since 1.2
         */
        public static function render_data($elements = [], $area = 'content')
        {
        }
        /**
         * One Page Navigation Wrapper
         */
        public function one_page_navigation_wrapper()
        {
        }
        /**
         * Lazy load via img data attribute
         *
         * https://developer.wordpress.org/reference/hooks/wp_get_attachment_image_attributes/
         *
         * @param array        $attr Image attributes.
         * @param object       $attachment WP_POST object of image.
         * @param string|array $size Requested image size.
         *
         * @return array
         */
        public function set_image_attributes($attr, $attachment, $size)
        {
        }
        /**
         * Template frontend view: Permanently redirect users without Bricks editing permission to homepage
         *
         * Exclude template pages in search engine results.
         *
         * Overwrite via 'publicTemplates' setting
         *
         * @since 1.9.4: Exclude redirect if maintenance mode activated (to prevent endless redirect)
         */
        public function template_redirect()
        {
        }
        public function add_skip_link()
        {
        }
        /**
         * Remove WP hooks on frontend
         *
         * @since 1.5.5
         */
        public function remove_wp_hooks()
        {
        }
        /**
         * Render header
         *
         * Bricks data exists & header is not disabled on this page.
         *
         * @since 1.3.2
         */
        public function render_header()
        {
        }
        /**
         * Render Bricks content + surrounding 'main' tag
         *
         * For pages rendered with Bricks
         *
         * To allow customizing the 'main' tag attributes
         *
         * @since 1.5
         */
        public static function render_content($bricks_data = [], $attributes = [], $html_after_begin = '', $html_before_end = '', $tag = 'main')
        {
        }
        /**
         * Render footer
         *
         * To follow already available 'render_header' function syntax
         *
         * @since 1.5
         */
        public function render_footer()
        {
        }
        /**
         * Remove current menu item classes from anchor links
         *
         * @since 1.11
         */
        public function adjust_menu_item_classes($items, $args)
        {
        }
    }
}
namespace {
    // Exit if accessed directly
    /**
     * Define constants
     *
     * @since 1.0
     */
    \define('BRICKS_VERSION', '1.11.1.1');
    \define('BRICKS_NAME', 'Bricks');
    \define('BRICKS_TEMP_DIR', 'bricks-temp');
    // Template import/export (JSON & ZIP)
    \define('BRICKS_TEMPLATE_SCREENSHOTS_DIR', 'bricks/template-screenshots');
    // Template screenshots (@since 1.10)
    \define('BRICKS_PATH', \trailingslashit(\get_template_directory()));
    // require_once files
    \define('BRICKS_PATH_ASSETS', \trailingslashit(\BRICKS_PATH . 'assets'));
    \define('BRICKS_URL', \trailingslashit(\get_template_directory_uri()));
    // WP enqueue files
    \define('BRICKS_URL_ASSETS', \trailingslashit(\BRICKS_URL . 'assets'));
    \define('BRICKS_REMOTE_URL', 'https://bricksbuilder.io/');
    \define('BRICKS_REMOTE_ACCOUNT', \BRICKS_REMOTE_URL . 'account/');
    \define('BRICKS_BUILDER_PARAM', 'bricks');
    \define('BRICKS_BUILDER_IFRAME_PARAM', 'brickspreview');
    \define('BRICKS_DEFAULT_IMAGE_SIZE', 'large');
    \define('BRICKS_DB_PANEL_WIDTH', 'bricks_panel_width');
    \define('BRICKS_DB_STRUCTURE_WIDTH', 'bricks_structure_width');
    // @since 1.10.2
    \define('BRICKS_DB_BUILDER_SCALE_OFF', 'bricks_builder_scale_off');
    \define('BRICKS_DB_BUILDER_WIDTH_LOCKED', 'bricks_builder_width_locked');
    \define('BRICKS_DB_COLOR_PALETTE', 'bricks_color_palette');
    \define('BRICKS_DB_BREAKPOINTS', 'bricks_breakpoints');
    \define('BRICKS_DB_GLOBAL_SETTINGS', 'bricks_global_settings');
    \define('BRICKS_DB_GLOBAL_ELEMENTS', 'bricks_global_elements');
    \define('BRICKS_DB_GLOBAL_CLASSES', 'bricks_global_classes');
    \define('BRICKS_DB_GLOBAL_CLASSES_CATEGORIES', 'bricks_global_classes_categories');
    \define('BRICKS_DB_GLOBAL_CLASSES_LOCKED', 'bricks_global_classes_locked');
    \define('BRICKS_DB_GLOBAL_CLASSES_TIMESTAMP', 'bricks_global_classes_timestamp');
    \define('BRICKS_GLOBAL_CLASSES_DEFAULT_TRASH_RETENTION_DAYS', 30);
    \define('BRICKS_DB_GLOBAL_CLASSES_TRASH', 'bricks_global_classes_trash');
    \define('BRICKS_DB_GLOBAL_CLASSES_USER', 'bricks_global_classes_user');
    \define('BRICKS_DB_PSEUDO_CLASSES', 'bricks_global_pseudo_classes');
    \define('BRICKS_DB_GLOBAL_VARIABLES', 'bricks_global_variables');
    \define('BRICKS_DB_GLOBAL_VARIABLES_CATEGORIES', 'bricks_global_variables_categories');
    \define('BRICKS_DB_PINNED_ELEMENTS', 'bricks_pinned_elements');
    \define('BRICKS_DB_SIDEBARS', 'bricks_sidebars');
    \define('BRICKS_DB_THEME_STYLES', 'bricks_theme_styles');
    \define('BRICKS_DB_ADOBE_FONTS', 'bricks_adobe_fonts');
    \define('BRICKS_DB_EDITOR_MODE', '_bricks_editor_mode');
    \define('BRICKS_BREAKPOINTS_LAST_GENERATED', 'bricks_breakpoints_last_generated');
    \define('BRICKS_CSS_FILES_LAST_GENERATED', 'bricks_css_files_last_generated');
    \define('BRICKS_CSS_FILES_LAST_GENERATED_TIMESTAMP', 'bricks_css_files_last_generated_timestamp');
    \define('BRICKS_CSS_FILES_ADMIN_NOTICE', 'bricks_css_files_admin_notice');
    \define('BRICKS_CODE_SIGNATURES_LAST_GENERATED', 'bricks_code_signatures_last_generated');
    \define('BRICKS_CODE_SIGNATURES_LAST_GENERATED_TIMESTAMP', 'bricks_code_signatures_last_generated_timestamp');
    \define('BRICKS_CODE_SIGNATURES_ADMIN_NOTICE', 'bricks_code_signatures_admin_notice');
    \define('BRICKS_LOCK_CODE_SIGNATURES', \false);
    /**
     * Syntax since 1.2 (container element)
     *
     * Pre 1.2: '_bricks_page_{$content_type}'
     */
    \define('BRICKS_DB_PAGE_HEADER', '_bricks_page_header_2');
    \define('BRICKS_DB_PAGE_CONTENT', '_bricks_page_content_2');
    \define('BRICKS_DB_PAGE_FOOTER', '_bricks_page_footer_2');
    \define('BRICKS_DB_PAGE_SETTINGS', '_bricks_page_settings');
    \define('BRICKS_DB_REMOTE_TEMPLATES', 'bricks_remote_templates');
    \define('BRICKS_DB_TEMPLATE_SLUG', 'bricks_template');
    \define('BRICKS_DB_TEMPLATE_TAX_BUNDLE', 'template_bundle');
    \define('BRICKS_DB_TEMPLATE_TAX_TAG', 'template_tag');
    \define('BRICKS_DB_TEMPLATE_TYPE', '_bricks_template_type');
    \define('BRICKS_DB_TEMPLATE_SETTINGS', '_bricks_template_settings');
    \define('BRICKS_DB_CUSTOM_FONTS', 'bricks_fonts');
    \define('BRICKS_DB_CUSTOM_FONT_FACES', 'bricks_font_faces');
    \define('BRICKS_DB_CUSTOM_FONT_FACE_RULES', 'bricks_font_face_rules');
    // @since 1.7.2
    \define('BRICKS_EXPORT_TEMPLATES', 'brick_export_templates');
    \define('BRICKS_ADMIN_PAGE_URL_LICENSE', \admin_url('admin.php?page=bricks-license'));
    \define('BRICKS_AUTH_CHECK_INTERVAL', 30);
    \define('BRICKS_DEBUG', \false);
    \define('BRICKS_MAX_REVISIONS_TO_KEEP', 100);
    \define('BRICKS_MULTISITE_USE_MAIN_SITE_COLOR_PALETTE', \false);
    \define('BRICKS_MULTISITE_USE_MAIN_SITE_CLASSES', \false);
    \define('BRICKS_MULTISITE_USE_MAIN_SITE_CLASSES_CATEGORIES', \false);
    \define('BRICKS_MULTISITE_USE_MAIN_SITE_VARIABLES', \false);
    \define('BRICKS_MULTISITE_USE_MAIN_SITE_VARIABLES_CATEGORIES', \false);
    \define('BRICKS_MULTISITE_USE_MAIN_SITE_GLOBAL_ELEMENTS', \false);
    \define('BRICKS_ASSETS_SUFFIX', '');
    /**
     * Builder check
     *
     * @since 1.0
     */
    function bricks_is_builder()
    {
    }
    function bricks_is_builder_iframe()
    {
    }
    function bricks_is_builder_main()
    {
    }
    function bricks_is_frontend()
    {
    }
    /**
     * Is AJAX call check
     *
     * @since 1.0
     */
    function bricks_is_ajax_call()
    {
    }
    /**
     * Is WP REST API call check
     *
     * @since 1.5
     */
    function bricks_is_rest_call()
    {
    }
    /**
     * Is builder call (AJAX OR REST API)
     *
     * @since 1.5
     */
    function bricks_is_builder_call()
    {
    }
    /**
     * Render dynamic data tags inside of a content string
     *
     * Example: Inside an executing Code element, custom plugin, etc.
     *
     * Academy: https://academy.bricksbuilder.io/article/function-bricks_render_dynamic_data/
     *
     * @since 1.5.5
     *
     * @param string $content The content (including dynamic data tags).
     * @param int    $post_id The post ID.
     * @param string $context text, image, link, etc.
     *
     * @return string
     */
    function bricks_render_dynamic_data($content, $post_id = 0, $context = 'text')
    {
    }
    /**
     * Comments list
     *
     * @since 1.0
     */
    function bricks_list_comments($comment, $args, $depth)
    {
    }
    /**
     * Move comment form textarea to the bottom
     *
     * @since 1.0
     */
    function bricks_comment_form_fields_order($fields)
    {
    }
    /**
     * Add custom fields to menu item (Appearance > Menus)
     *
     * Much better than using the Walker_Nav_Menu_Edit class ;)
     *
     * https://make.wordpress.org/core/2020/02/25/wordpress-5-4-introduces-new-hooks-to-add-custom-fields-to-menu-items/
     *
     * @since 1.8
     */
    function bricks_nav_menu_item_custom_fields($item_id, $item)
    {
    }
    /**
     * Save the menu item postmeta
     *
     * Mega menu (= selected Bricks template ID )
     * Multilevel menu
     *
     * @param int $menu_id
     * @param int $menu_item_db_id
     *
     * @since 1.8
     */
    function bricks_update_nav_menu_item($menu_id, $menu_item_db_id)
    {
    }
}
