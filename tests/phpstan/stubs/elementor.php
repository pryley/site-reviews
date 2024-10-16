<?php
/**
 * Generated stub declarations for Elementor.
 * https://elementor.com
 * https://github.com/arifpavel/elementor-stubs
 */

namespace Elementor\Core\Base {
    /**
     * Base Object
     *
     * Base class that provides basic settings handling functionality.
     *
     * @since 2.3.0
     */
    class Base_Object
    {
        /**
         * Settings.
         *
         * Holds the object settings.
         *
         * @access private
         *
         * @var array
         */
        private $settings;
        /**
         * Get Settings.
         *
         * @since 2.3.0
         * @access public
         *
         * @param string $setting Optional. The key of the requested setting. Default is null.
         *
         * @return mixed An array of all settings, or a single value if `$setting` was specified.
         */
        public final function get_settings($setting = null)
        {
        }
        /**
         * Set settings.
         *
         * @since 2.3.0
         * @access public
         *
         * @param array|string $key   If key is an array, the settings are overwritten by that array. Otherwise, the
         *                            settings of the key will be set to the given `$value` param.
         *
         * @param mixed        $value Optional. Default is null.
         */
        public final function set_settings($key, $value = null)
        {
        }
        /**
         * Delete setting.
         *
         * Deletes the settings array or a specific key of the settings array if `$key` is specified.
         * @since 2.3.0
         * @access public
         *
         * @param string $key Optional. Default is null.
         */
        public function delete_setting($key = null)
        {
        }
        public final function merge_properties(array $default_props, array $custom_props, array $allowed_props_keys = [])
        {
        }
        /**
         * Get items.
         *
         * Utility method that receives an array with a needle and returns all the
         * items that match the needle. If needle is not defined the entire haystack
         * will be returned.
         *
         * @since 2.3.0
         * @access protected
         * @static
         *
         * @param array  $haystack An array of items.
         * @param string $needle   Optional. Needle. Default is null.
         *
         * @return mixed The whole haystack or the needle from the haystack when requested.
         */
        protected static final function get_items(array $haystack, $needle = null)
        {
        }
        /**
         * Get init settings.
         *
         * Used to define the default/initial settings of the object. Inheriting classes may implement this method to define
         * their own default/initial settings.
         *
         * @since 2.3.0
         * @access protected
         *
         * @return array
         */
        protected function get_init_settings()
        {
        }
        /**
         * Ensure settings.
         *
         * Ensures that the `$settings` member is initialized
         *
         * @since 2.3.0
         * @access private
         */
        private function ensure_settings()
        {
        }
        /**
         * Has Own Method
         *
         * Used for check whether the method passed as a parameter was declared in the current instance or inherited.
         * If a base_class_name is passed, it checks whether the method was declared in that class. If the method's
         * declaring class is the class passed as $base_class_name, it returns false. Otherwise (method was NOT declared
         * in $base_class_name), it returns true.
         *
         * Example #1 - only $method_name is passed:
         * The initial declaration of `register_controls()` happens in the `Controls_Stack` class. However, all
         * widgets which have their own controls declare this function as well, overriding the original
         * declaration. If `has_own_method()` would be called by a Widget's class which implements `register_controls()`,
         * with 'register_controls' passed as the first parameter - `has_own_method()` will return true. If the Widget
         * does not declare `register_controls()`, `has_own_method()` will return false.
         *
         * Example #2 - both $method_name and $base_class_name are passed
         * In this example, the widget class inherits from a base class `Widget_Base`, and the base implements
         * `register_controls()` to add certain controls to all widgets inheriting from it. `has_own_method()` is called by
         * the widget, with the string 'register_controls' passed as the first parameter, and 'Elementor\Widget_Base' (its full name
         * including the namespace) passed as the second parameter. If the widget class implements `register_controls()`,
         * `has_own_method` will return true. If the widget class DOESN'T implement `register_controls()`, it will return
         * false (because `Widget_Base` is the declaring class for `register_controls()`, and not the class that called
         * `has_own_method()`).
         *
         * @since 3.1.0
         *
         * @param string $method_name
         * @param string $base_class_name
         *
         * @return bool True if the method was declared by the current instance, False if it was inherited.
         */
        public function has_own_method($method_name, $base_class_name = null)
        {
        }
    }
}
namespace Elementor\Core\Files {
    abstract class Base
    {
    }
}
namespace Elementor\Core\Files\CSS {
    /**
     * Elementor post CSS file.
     *
     * Elementor CSS file handler class is responsible for generating the single
     * post CSS file.
     *
     * @since 1.2.0
     */
    class Post extends Base
    {
        /**
         * Get unique element selector.
         *
         * Retrieve the unique selector for any given element.
         *
         * @since 1.2.0
         * @access public
         *
         * @param \Elementor\Element_Base $element The element.
         *
         * @return string Unique element selector.
         */
        public function get_element_unique_selector(\Elementor\Element_Base $element)
        {
        }
    }
    /**
     * Elementor CSS file.
     *
     * Elementor CSS file handler class is responsible for generating CSS files.
     *
     * @since 1.2.0
     * @abstract
     */
    abstract class Base extends \Elementor\Core\Files\Base
    {
        /**
         * Get stylesheet.
         *
         * Retrieve the CSS file stylesheet instance.
         *
         * @since 1.2.0
         * @access public
         *
         * @return \Elementor\Stylesheet The stylesheet object.
         */
        public function get_stylesheet()
        {
        }
    }
}
namespace Elementor {
    /**
     * Elementor stylesheet.
     *
     * Elementor stylesheet handler class responsible for setting up CSS rules and
     * properties, and all the CSS `@media` rule with supported viewport width.
     *
     * @since 1.0.0
     */
    class Stylesheet
    {
        /**
         * Add rules.
         *
         * Add a new CSS rule to the rules list.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string       $selector    CSS selector.
         * @param array|string $style_rules Optional. Style rules. Default is `null`.
         * @param array        $query       Optional. Media query. Default is `null`.
         *
         * @return \Elementor\Stylesheet The current stylesheet class instance.
         */
        public function add_rules($selector, $style_rules = null, array $query = null)
        {
        }
    }
}
namespace Elementor {
    /**
     * Elementor controls stack.
     *
     * An abstract class that provides the needed properties and methods to
     * manage and handle controls in the editor panel to inheriting classes.
     *
     * @since 1.4.0
     * @abstract
     */
    abstract class Controls_Stack extends \Elementor\Core\Base\Base_Object
    {
        /**
         * Responsive 'desktop' device name.
         *
         * @deprecated 3.4.0
         */
        const RESPONSIVE_DESKTOP = 'desktop';
        /**
         * Responsive 'tablet' device name.
         *
         * @deprecated 3.4.0
         */
        const RESPONSIVE_TABLET = 'tablet';
        /**
         * Responsive 'mobile' device name.
         *
         * @deprecated 3.4.0
         */
        const RESPONSIVE_MOBILE = 'mobile';
        /**
         * Generic ID.
         *
         * Holds the unique ID.
         *
         * @access private
         *
         * @var string
         */
        private $id;
        private $active_settings;
        private $parsed_active_settings;
        /**
         * Parsed Dynamic Settings.
         *
         * @access private
         *
         * @var null|array
         */
        private $parsed_dynamic_settings;
        /**
         * Raw Data.
         *
         * Holds all the raw data including the element type, the child elements,
         * the user data.
         *
         * @access private
         *
         * @var null|array
         */
        private $data;
        /**
         * The configuration.
         *
         * Holds the configuration used to generate the Elementor editor. It includes
         * the element name, icon, categories, etc.
         *
         * @access private
         *
         * @var null|array
         */
        private $config;
        /**
         * The additional configuration.
         *
         * Holds additional configuration that has been set using `set_config` method.
         * The `config` property is not modified directly while using the method because
         * it's used to check whether the initial config already loaded (in `get_config`).
         * After the initial config loaded, the additional config is merged into it.
         *
         * @access private
         *
         * @var null|array
         */
        private $additional_config = [];
        /**
         * Current section.
         *
         * Holds the current section while inserting a set of controls sections.
         *
         * @access private
         *
         * @var null|array
         */
        private $current_section;
        /**
         * Current tab.
         *
         * Holds the current tab while inserting a set of controls tabs.
         *
         * @access private
         *
         * @var null|array
         */
        private $current_tab;
        /**
         * Current popover.
         *
         * Holds the current popover while inserting a set of controls.
         *
         * @access private
         *
         * @var null|array
         */
        private $current_popover;
        /**
         * Injection point.
         *
         * Holds the injection point in the stack where the control will be inserted.
         *
         * @access private
         *
         * @var null|array
         */
        private $injection_point;
        /**
         * Data sanitized.
         *
         * @access private
         *
         * @var bool
         */
        private $settings_sanitized = false;
        /**
         * Element render attributes.
         *
         * Holds all the render attributes of the element. Used to store data like
         * the HTML class name and the class value, or HTML element ID name and value.
         *
         * @access private
         *
         * @var array
         */
        private $render_attributes = [];
        /**
         * Get element name.
         *
         * Retrieve the element name.
         *
         * @since 1.4.0
         * @access public
         * @abstract
         *
         * @return string The name.
         */
        public abstract function get_name();
        /**
         * Get unique name.
         *
         * Some classes need to use unique names, this method allows you to create
         * them. By default it retrieves the regular name.
         *
         * @since 1.6.0
         * @access public
         *
         * @return string Unique name.
         */
        public function get_unique_name()
        {
        }
        /**
         * Get element ID.
         *
         * Retrieve the element generic ID.
         *
         * @since 1.4.0
         * @access public
         *
         * @return string The ID.
         */
        public function get_id()
        {
        }
        /**
         * Get element ID.
         *
         * Retrieve the element generic ID as integer.
         *
         * @since 1.8.0
         * @access public
         *
         * @return string The converted ID.
         */
        public function get_id_int()
        {
        }
        /**
         * Get the type.
         *
         * Retrieve the type, e.g. 'stack', 'section', 'widget' etc.
         *
         * @since 1.4.0
         * @access public
         * @static
         *
         * @return string The type.
         */
        public static function get_type()
        {
        }
        /**
         * @since 2.9.0
         * @access public
         *
         * @return bool
         */
        public function is_editable()
        {
        }
        /**
         * Get current section.
         *
         * When inserting new controls, this method will retrieve the current section.
         *
         * @since 1.7.1
         * @access public
         *
         * @return null|array Current section.
         */
        public function get_current_section()
        {
        }
        /**
         * Get current tab.
         *
         * When inserting new controls, this method will retrieve the current tab.
         *
         * @since 1.7.1
         * @access public
         *
         * @return null|array Current tab.
         */
        public function get_current_tab()
        {
        }
        /**
         * Get controls.
         *
         * Retrieve all the controls or, when requested, a specific control.
         *
         * @since 1.4.0
         * @access public
         *
         * @param string $control_id The ID of the requested control. Optional field,
         *                           when set it will return a specific control.
         *                           Default is null.
         *
         * @return mixed Controls list.
         */
        public function get_controls($control_id = null)
        {
        }
        /**
         * Get active controls.
         *
         * Retrieve an array of active controls that meet the condition field.
         *
         * If specific controls was given as a parameter, retrieve active controls
         * from that list, otherwise check for all the controls available.
         *
         * @since 1.4.0
         * @since 2.0.9 Added the `controls` and the `settings` parameters.
         * @access public
         * @deprecated 3.0.0
         *
         * @param array $controls Optional. An array of controls. Default is null.
         * @param array $settings Optional. Controls settings. Default is null.
         *
         * @return array Active controls.
         */
        public function get_active_controls(array $controls = null, array $settings = null)
        {
        }
        /**
         * Get controls settings.
         *
         * Retrieve the settings for all the controls that represent them.
         *
         * @since 1.5.0
         * @access public
         *
         * @return array Controls settings.
         */
        public function get_controls_settings()
        {
        }
        /**
         * Add new control to stack.
         *
         * Register a single control to allow the user to set/update data.
         *
         * This method should be used inside `register_controls()`.
         *
         * @since 1.4.0
         * @access public
         *
         * @param string $id      Control ID.
         * @param array  $args    Control arguments.
         * @param array  $options Optional. Control options. Default is an empty array.
         *
         * @return bool True if control added, False otherwise.
         */
        public function add_control($id, array $args, $options = [])
        {
        }
        /**
         * Remove control from stack.
         *
         * Unregister an existing control and remove it from the stack.
         *
         * @since 1.4.0
         * @access public
         *
         * @param string $control_id Control ID.
         *
         * @return bool|\WP_Error
         */
        public function remove_control($control_id)
        {
        }
        /**
         * Update control in stack.
         *
         * Change the value of an existing control in the stack. When you add new
         * control you set the `$args` parameter, this method allows you to update
         * the arguments by passing new data.
         *
         * @since 1.4.0
         * @since 1.8.1 New `$options` parameter added.
         *
         * @access public
         *
         * @param string $control_id Control ID.
         * @param array  $args       Control arguments. Only the new fields you want
         *                           to update.
         * @param array  $options    Optional. Some additional options. Default is
         *                           an empty array.
         *
         * @return bool
         */
        public function update_control($control_id, array $args, array $options = [])
        {
        }
        /**
         * Get stack.
         *
         * Retrieve the stack of controls.
         *
         * @since 1.9.2
         * @access public
         *
         * @return array Stack of controls.
         */
        public function get_stack()
        {
        }
        /**
         * Get position information.
         *
         * Retrieve the position while injecting data, based on the element type.
         *
         * @since 1.7.0
         * @access public
         *
         * @param array $position {
         *     The injection position.
         *
         *     @type string $type     Injection type, either `control` or `section`.
         *                            Default is `control`.
         *     @type string $at       Where to inject. If `$type` is `control` accepts
         *                            `before` and `after`. If `$type` is `section`
         *                            accepts `start` and `end`. Default values based on
         *                            the `type`.
         *     @type string $of       Control/Section ID.
         *     @type array  $fallback Fallback injection position. When the position is
         *                            not found it will try to fetch the fallback
         *                            position.
         * }
         *
         * @return bool|array Position info.
         */
        public final function get_position_info(array $position)
        {
        }
        /**
         * Get control key.
         *
         * Retrieve the key of the control based on a given index of the control.
         *
         * @since 1.9.2
         * @access public
         *
         * @param string $control_index Control index.
         *
         * @return int Control key.
         */
        public final function get_control_key($control_index)
        {
        }
        /**
         * Get control index.
         *
         * Retrieve the index of the control based on a given key of the control.
         *
         * @since 1.7.6
         * @access public
         *
         * @param string $control_key Control key.
         *
         * @return false|int Control index.
         */
        public final function get_control_index($control_key)
        {
        }
        /**
         * Get section controls.
         *
         * Retrieve all controls under a specific section.
         *
         * @since 1.7.6
         * @access public
         *
         * @param string $section_id Section ID.
         *
         * @return array Section controls
         */
        public final function get_section_controls($section_id)
        {
        }
        /**
         * Add new group control to stack.
         *
         * Register a set of related controls grouped together as a single unified
         * control. For example grouping together like typography controls into a
         * single, easy-to-use control.
         *
         * @since 1.4.0
         * @access public
         *
         * @param string $group_name Group control name.
         * @param array  $args       Group control arguments. Default is an empty array.
         * @param array  $options    Optional. Group control options. Default is an
         *                           empty array.
         */
        public final function add_group_control($group_name, array $args = [], array $options = [])
        {
        }
        /**
         * Get scheme controls.
         *
         * Retrieve all the controls that use schemes.
         *
         * @since 1.4.0
         * @access public
         * @deprecated 3.0.0
         *
         * @return array Scheme controls.
         */
        public final function get_scheme_controls()
        {
        }
        /**
         * Get style controls.
         *
         * Retrieve style controls for all active controls or, when requested, from
         * a specific set of controls.
         *
         * @since 1.4.0
         * @since 2.0.9 Added the `settings` parameter.
         * @access public
         * @deprecated 3.0.0
         *
         * @param array $controls Optional. Controls list. Default is null.
         * @param array $settings Optional. Controls settings. Default is null.
         *
         * @return array Style controls.
         */
        public final function get_style_controls(array $controls = null, array $settings = null)
        {
        }
        /**
         * Get tabs controls.
         *
         * Retrieve all the tabs assigned to the control.
         *
         * @since 1.4.0
         * @access public
         *
         * @return array Tabs controls.
         */
        public final function get_tabs_controls()
        {
        }
        /**
         * Add new responsive control to stack.
         *
         * Register a set of controls to allow editing based on user screen size.
         * This method registers one or more controls per screen size/device, depending on the current Responsive Control
         * Duplication Mode. There are 3 control duplication modes:
         * * 'off' - Only a single control is generated. In the Editor, this control is duplicated in JS.
         * * 'on' - Multiple controls are generated, one control per enabled device/breakpoint + a default/desktop control.
         * * 'dynamic' - If the control includes the `'dynamic' => 'active' => true` property - the control is duplicated,
         *               once for each device/breakpoint + default/desktop.
         *               If the control doesn't include the `'dynamic' => 'active' => true` property - the control is not duplicated.
         *
         * @since 1.4.0
         * @access public
         *
         * @param string $id      Responsive control ID.
         * @param array  $args    Responsive control arguments.
         * @param array  $options Optional. Responsive control options. Default is
         *                        an empty array.
         */
        public final function add_responsive_control($id, array $args, $options = [])
        {
        }
        /**
         * Update responsive control in stack.
         *
         * Change the value of an existing responsive control in the stack. When you
         * add new control you set the `$args` parameter, this method allows you to
         * update the arguments by passing new data.
         *
         * @since 1.4.0
         * @access public
         *
         * @param string $id      Responsive control ID.
         * @param array  $args    Responsive control arguments.
         * @param array  $options Optional. Additional options.
         */
        public final function update_responsive_control($id, array $args, array $options = [])
        {
        }
        /**
         * Remove responsive control from stack.
         *
         * Unregister an existing responsive control and remove it from the stack.
         *
         * @since 1.4.0
         * @access public
         *
         * @param string $id Responsive control ID.
         */
        public final function remove_responsive_control($id)
        {
        }
        /**
         * Get class name.
         *
         * Retrieve the name of the current class.
         *
         * @since 1.4.0
         * @access public
         *
         * @return string Class name.
         */
        public final function get_class_name()
        {
        }
        /**
         * Get the config.
         *
         * Retrieve the config or, if non set, use the initial config.
         *
         * @since 1.4.0
         * @access public
         *
         * @return array|null The config.
         */
        public final function get_config()
        {
        }
        /**
         * Set a config property.
         *
         * Set a specific property of the config list for this controls-stack.
         *
         * @since 3.5.0
         * @access public
         */
        public function set_config($key, $value)
        {
        }
        /**
         * Get frontend settings keys.
         *
         * Retrieve settings keys for all frontend controls.
         *
         * @since 1.6.0
         * @access public
         *
         * @return array Settings keys for each control.
         */
        public final function get_frontend_settings_keys()
        {
        }
        /**
         * Get controls pointer index.
         *
         * Retrieve pointer index where the next control should be added.
         *
         * While using injection point, it will return the injection point index.
         * Otherwise index of the last control plus one.
         *
         * @since 1.9.2
         * @access public
         *
         * @return int Controls pointer index.
         */
        public function get_pointer_index()
        {
        }
        /**
         * Get the raw data.
         *
         * Retrieve all the items or, when requested, a specific item.
         *
         * @since 1.4.0
         * @access public
         *
         * @param string $item Optional. The requested item. Default is null.
         *
         * @return mixed The raw data.
         */
        public function get_data($item = null)
        {
        }
        /**
         * @since 2.0.14
         * @access public
         */
        public function get_parsed_dynamic_settings($setting = null, $settings = null)
        {
        }
        /**
         * Get active settings.
         *
         * Retrieve the settings from all the active controls.
         *
         * @since 1.4.0
         * @since 2.1.0 Added the `controls` and the `settings` parameters.
         * @access public
         *
         * @param array $controls Optional. An array of controls. Default is null.
         * @param array $settings Optional. Controls settings. Default is null.
         *
         * @return array Active settings.
         */
        public function get_active_settings($settings = null, $controls = null)
        {
        }
        /**
         * Get settings for display.
         *
         * Retrieve all the settings or, when requested, a specific setting for display.
         *
         * Unlike `get_settings()` method, this method retrieves only active settings
         * that passed all the conditions, rendered all the shortcodes and all the dynamic
         * tags.
         *
         * @since 2.0.0
         * @access public
         *
         * @param string $setting_key Optional. The key of the requested setting.
         *                            Default is null.
         *
         * @return mixed The settings.
         */
        public function get_settings_for_display($setting_key = null)
        {
        }
        /**
         * Parse dynamic settings.
         *
         * Retrieve the settings with rendered dynamic tags.
         *
         * @since 2.0.0
         * @access public
         *
         * @param array $settings     Optional. The requested setting. Default is null.
         * @param array $controls     Optional. The controls array. Default is null.
         * @param array $all_settings Optional. All the settings. Default is null.
         *
         * @return array The settings with rendered dynamic tags.
         */
        public function parse_dynamic_settings($settings, $controls = null, $all_settings = null)
        {
        }
        /**
         * Get frontend settings.
         *
         * Retrieve the settings for all frontend controls.
         *
         * @since 1.6.0
         * @access public
         *
         * @return array Frontend settings.
         */
        public function get_frontend_settings()
        {
        }
        /**
         * Filter controls settings.
         *
         * Receives controls, settings and a callback function to filter the settings by
         * and returns filtered settings.
         *
         * @since 1.5.0
         * @access public
         *
         * @param callable $callback The callback function.
         * @param array    $settings Optional. Control settings. Default is an empty
         *                           array.
         * @param array    $controls Optional. Controls list. Default is an empty
         *                           array.
         *
         * @return array Filtered settings.
         */
        public function filter_controls_settings(callable $callback, array $settings = [], array $controls = [])
        {
        }
        /**
         * Get Responsive Control Device Suffix
         *
         * @deprecated 3.7.6
         * @param array $control
         * @return string $device suffix
         */
        protected function get_responsive_control_device_suffix($control)
        {
        }
        /**
         * Whether the control is visible or not.
         *
         * Used to determine whether the control is visible or not.
         *
         * @since 1.4.0
         * @access public
         *
         * @param array $control The control.
         * @param array $values  Optional. Condition values. Default is null.
         *
         * @return bool Whether the control is visible.
         */
        public function is_control_visible($control, $values = null)
        {
        }
        /**
         * Start controls section.
         *
         * Used to add a new section of controls. When you use this method, all the
         * registered controls from this point will be assigned to this section,
         * until you close the section using `end_controls_section()` method.
         *
         * This method should be used inside `register_controls()`.
         *
         * @since 1.4.0
         * @access public
         *
         * @param string $section_id Section ID.
         * @param array  $args       Section arguments Optional.
         */
        public function start_controls_section($section_id, array $args = [])
        {
        }
        /**
         * End controls section.
         *
         * Used to close an existing open controls section. When you use this method
         * it stops adding new controls to this section.
         *
         * This method should be used inside `register_controls()`.
         *
         * @since 1.4.0
         * @access public
         */
        public function end_controls_section()
        {
        }
        /**
         * Start controls tabs.
         *
         * Used to add a new set of tabs inside a section. You should use this
         * method before adding new individual tabs using `start_controls_tab()`.
         * Each tab added after this point will be assigned to this group of tabs,
         * until you close it using `end_controls_tabs()` method.
         *
         * This method should be used inside `register_controls()`.
         *
         * @since 1.4.0
         * @access public
         *
         * @param string $tabs_id Tabs ID.
         * @param array  $args    Tabs arguments.
         */
        public function start_controls_tabs($tabs_id, array $args = [])
        {
        }
        /**
         * End controls tabs.
         *
         * Used to close an existing open controls tabs. When you use this method it
         * stops adding new controls to this tabs.
         *
         * This method should be used inside `register_controls()`.
         *
         * @since 1.4.0
         * @access public
         */
        public function end_controls_tabs()
        {
        }
        /**
         * Start controls tab.
         *
         * Used to add a new tab inside a group of tabs. Use this method before
         * adding new individual tabs using `start_controls_tab()`.
         * Each tab added after this point will be assigned to this group of tabs,
         * until you close it using `end_controls_tab()` method.
         *
         * This method should be used inside `register_controls()`.
         *
         * @since 1.4.0
         * @access public
         *
         * @param string $tab_id Tab ID.
         * @param array  $args   Tab arguments.
         */
        public function start_controls_tab($tab_id, $args)
        {
        }
        /**
         * End controls tab.
         *
         * Used to close an existing open controls tab. When you use this method it
         * stops adding new controls to this tab.
         *
         * This method should be used inside `register_controls()`.
         *
         * @since 1.4.0
         * @access public
         */
        public function end_controls_tab()
        {
        }
        /**
         * Start popover.
         *
         * Used to add a new set of controls in a popover. When you use this method,
         * all the registered controls from this point will be assigned to this
         * popover, until you close the popover using `end_popover()` method.
         *
         * This method should be used inside `register_controls()`.
         *
         * @since 1.9.0
         * @access public
         */
        public final function start_popover()
        {
        }
        /**
         * End popover.
         *
         * Used to close an existing open popover. When you use this method it stops
         * adding new controls to this popover.
         *
         * This method should be used inside `register_controls()`.
         *
         * @since 1.9.0
         * @access public
         */
        public final function end_popover()
        {
        }
        /**
         * Add render attribute.
         *
         * Used to add attributes to a specific HTML element.
         *
         * The HTML tag is represented by the element parameter, then you need to
         * define the attribute key and the attribute key. The final result will be:
         * `<element attribute_key="attribute_value">`.
         *
         * Example usage:
         *
         * `$this->add_render_attribute( 'wrapper', 'class', 'custom-widget-wrapper-class' );`
         * `$this->add_render_attribute( 'widget', 'id', 'custom-widget-id' );`
         * `$this->add_render_attribute( 'button', [ 'class' => 'custom-button-class', 'id' => 'custom-button-id' ] );`
         *
         * @since 1.0.0
         * @access public
         *
         * @param array|string $element   The HTML element.
         * @param array|string $key       Optional. Attribute key. Default is null.
         * @param array|string $value     Optional. Attribute value. Default is null.
         * @param bool         $overwrite Optional. Whether to overwrite existing
         *                                attribute. Default is false, not to overwrite.
         *
         * @return self Current instance of the element.
         */
        public function add_render_attribute($element, $key = null, $value = null, $overwrite = false)
        {
        }
        /**
         * Get Render Attributes
         *
         * Used to retrieve render attribute.
         *
         * The returned array is either all elements and their attributes if no `$element` is specified, an array of all
         * attributes of a specific element or a specific attribute properties if `$key` is specified.
         *
         * Returns null if one of the requested parameters isn't set.
         *
         * @since 2.2.6
         * @access public
         * @param string $element
         * @param string $key
         *
         * @return array
         */
        public function get_render_attributes($element = '', $key = '')
        {
        }
        /**
         * Set render attribute.
         *
         * Used to set the value of the HTML element render attribute or to update
         * an existing render attribute.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array|string $element The HTML element.
         * @param array|string $key     Optional. Attribute key. Default is null.
         * @param array|string $value   Optional. Attribute value. Default is null.
         *
         * @return self Current instance of the element.
         */
        public function set_render_attribute($element, $key = null, $value = null)
        {
        }
        /**
         * Remove render attribute.
         *
         * Used to remove an element (with its keys and their values), key (with its values),
         * or value/s from an HTML element's render attribute.
         *
         * @since 2.7.0
         * @access public
         *
         * @param string $element       The HTML element.
         * @param string $key           Optional. Attribute key. Default is null.
         * @param array|string $values   Optional. Attribute value/s. Default is null.
         */
        public function remove_render_attribute($element, $key = null, $values = null)
        {
        }
        /**
         * Get render attribute string.
         *
         * Used to retrieve the value of the render attribute.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $element The element.
         *
         * @return string Render attribute string, or an empty string if the attribute
         *                is empty or not exist.
         */
        public function get_render_attribute_string($element)
        {
        }
        /**
         * Print render attribute string.
         *
         * Used to output the rendered attribute.
         *
         * @since 2.0.0
         * @access public
         *
         * @param array|string $element The element.
         */
        public function print_render_attribute_string($element)
        {
        }
        /**
         * Print element template.
         *
         * Used to generate the element template on the editor.
         *
         * @since 2.0.0
         * @access public
         */
        public function print_template()
        {
        }
        /**
         * On import update dynamic content (e.g. post and term IDs).
         *
         * @since 3.8.0
         *
         * @param array      $config   The config of the passed element.
         * @param array      $data     The data that requires updating/replacement when imported.
         * @param array|null $controls The available controls.
         *
         * @return array Element data.
         */
        public static function on_import_update_dynamic_content(array $config, array $data, $controls = null) : array
        {
        }
        /**
         * Start injection.
         *
         * Used to inject controls and sections to a specific position in the stack.
         *
         * When you use this method, all the registered controls and sections will
         * be injected to a specific position in the stack, until you stop the
         * injection using `end_injection()` method.
         *
         * @since 1.7.1
         * @access public
         *
         * @param array $position {
         *     The position where to start the injection.
         *
         *     @type string $type Injection type, either `control` or `section`.
         *                        Default is `control`.
         *     @type string $at   Where to inject. If `$type` is `control` accepts
         *                        `before` and `after`. If `$type` is `section`
         *                        accepts `start` and `end`. Default values based on
         *                        the `type`.
         *     @type string $of   Control/Section ID.
         * }
         */
        public final function start_injection(array $position)
        {
        }
        /**
         * End injection.
         *
         * Used to close an existing opened injection point.
         *
         * When you use this method it stops adding new controls and sections to
         * this point and continue to add controls to the regular position in the
         * stack.
         *
         * @since 1.7.1
         * @access public
         */
        public final function end_injection()
        {
        }
        /**
         * Get injection point.
         *
         * Retrieve the injection point in the stack where new controls and sections
         * will be inserted.
         *
         * @since 1.9.2
         * @access public
         *
         * @return array|null An array when an injection point is defined, null
         *                    otherwise.
         */
        public final function get_injection_point()
        {
        }
        /**
         * Register controls.
         *
         * Used to add new controls to any element type. For example, external
         * developers use this method to register controls in a widget.
         *
         * Should be inherited and register new controls using `add_control()`,
         * `add_responsive_control()` and `add_group_control()`, inside control
         * wrappers like `start_controls_section()`, `start_controls_tabs()` and
         * `start_controls_tab()`.
         *
         * @since 1.4.0
         * @access protected
         * @deprecated 3.1.0 Use `Controls_Stack::register_controls()` instead
         */
        protected function _register_controls()
        {
        }
        /**
         * Register controls.
         *
         * Used to add new controls to any element type. For example, external
         * developers use this method to register controls in a widget.
         *
         * Should be inherited and register new controls using `add_control()`,
         * `add_responsive_control()` and `add_group_control()`, inside control
         * wrappers like `start_controls_section()`, `start_controls_tabs()` and
         * `start_controls_tab()`.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Get default data.
         *
         * Retrieve the default data. Used to reset the data on initialization.
         *
         * @since 1.4.0
         * @access protected
         *
         * @return array Default data.
         */
        protected function get_default_data()
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function get_init_settings()
        {
        }
        /**
         * Get initial config.
         *
         * Retrieve the current element initial configuration - controls list and
         * the tabs assigned to the control.
         *
         * @since 2.9.0
         * @access protected
         *
         * @return array The initial config.
         */
        protected function get_initial_config()
        {
        }
        /**
         * Get initial config.
         *
         * Retrieve the current element initial configuration - controls list and
         * the tabs assigned to the control.
         *
         * @since 1.4.0
         * @deprecated 2.9.0 use `get_initial_config()` instead
         * @access protected
         *
         * @return array The initial config.
         */
        protected function _get_initial_config()
        {
        }
        /**
         * Get section arguments.
         *
         * Retrieve the section arguments based on section ID.
         *
         * @since 1.4.0
         * @access protected
         *
         * @param string $section_id Section ID.
         *
         * @return array Section arguments.
         */
        protected function get_section_args($section_id)
        {
        }
        /**
         * Render element.
         *
         * Generates the final HTML on the frontend.
         *
         * @since 2.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render element in static mode.
         *
         * If not inherent will call the base render.
         */
        protected function render_static()
        {
        }
        /**
         * Determine the render logic.
         */
        protected function render_by_mode()
        {
        }
        /**
         * Print content template.
         *
         * Used to generate the content template on the editor, using a
         * Backbone JavaScript template.
         *
         * @access protected
         * @since 2.0.0
         *
         * @param string $template_content Template content.
         */
        protected function print_template_content($template_content)
        {
        }
        /**
         * Render element output in the editor.
         *
         * Used to generate the live preview, using a Backbone JavaScript template.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
        /**
         * Render element output in the editor.
         *
         * Used to generate the live preview, using a Backbone JavaScript template.
         *
         * @since 2.0.0
         * @deprecated 2.9.0 use `content_template()` instead
         * @access protected
         */
        protected function _content_template()
        {
        }
        /**
         * Initialize controls.
         *
         * Register the all controls added by `register_controls()`.
         *
         * @since 2.0.0
         * @access protected
         */
        protected function init_controls()
        {
        }
        protected function handle_control_position(array $args, $control_id, $overwrite)
        {
        }
        /**
         * Initialize the class.
         *
         * Set the raw data, the ID and the parsed settings.
         *
         * @since 2.9.0
         * @access protected
         *
         * @param array $data Initial data.
         */
        protected function init($data)
        {
        }
        /**
         * Initialize the class.
         *
         * Set the raw data, the ID and the parsed settings.
         *
         * @since 1.4.0
         * @deprecated 2.9.0 use `init()` instead
         * @access protected
         *
         * @param array $data Initial data.
         */
        protected function _init($data)
        {
        }
        /**
         * Sanitize initial data.
         *
         * Performs settings cleaning and sanitization.
         *
         * @since 2.1.5
         * @access private
         *
         * @param array $settings Settings to sanitize.
         * @param array $controls Optional. An array of controls. Default is an
         *                        empty array.
         *
         * @return array Sanitized settings.
         */
        private function sanitize_settings(array $settings, array $controls = [])
        {
        }
        /**
         * Controls stack constructor.
         *
         * Initializing the control stack class using `$data`. The `$data` is required
         * for a normal instance. It is optional only for internal `type instance`.
         *
         * @since 1.4.0
         * @access public
         *
         * @param array $data Optional. Control stack data. Default is an empty array.
         */
        public function __construct(array $data = [])
        {
        }
    }
}
namespace Elementor\Core {
    /**
     * Elementor documents manager.
     *
     * Elementor documents manager handler class is responsible for registering and
     * managing Elementor documents.
     *
     * @since 2.0.0
     */
    class Documents_Manager
    {
        /**
         * Registered types.
         *
         * Holds the list of all the registered types.
         *
         * @since 2.0.0
         * @access protected
         *
         * @var Document[]
         */
        protected $types = [];
        /**
         * Registered documents.
         *
         * Holds the list of all the registered documents.
         *
         * @since 2.0.0
         * @access protected
         *
         * @var Document[]
         */
        protected $documents = [];
        /**
         * Current document.
         *
         * Holds the current document.
         *
         * @since 2.0.0
         * @access protected
         *
         * @var Document
         */
        protected $current_doc;
        /**
         * Switched data.
         *
         * Holds the current document when changing to the requested post.
         *
         * @since 2.0.0
         * @access protected
         *
         * @var array
         */
        protected $switched_data = [];
        protected $cpt = [];
        /**
         * Documents manager constructor.
         *
         * Initializing the Elementor documents manager.
         *
         * @since 2.0.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Register ajax actions.
         *
         * Process ajax action handles when saving data and discarding changes.
         *
         * Fired by `elementor/ajax/register_actions` action.
         *
         * @since 2.0.0
         * @access public
         *
         * @param Ajax $ajax_manager An instance of the ajax manager.
         */
        public function register_ajax_actions($ajax_manager)
        {
        }
        /**
         * Register default types.
         *
         * Registers the default document types.
         *
         * @since 2.0.0
         * @access public
         */
        public function register_default_types()
        {
        }
        /**
         * Register document type.
         *
         * Registers a single document.
         *
         * @since 2.0.0
         * @access public
         *
         * @param string $type  Document type name.
         * @param string $class The name of the class that registers the document type.
         *                      Full name with the namespace.
         *
         * @return Documents_Manager The updated document manager instance.
         */
        public function register_document_type($type, $class)
        {
        }
        /**
         * Get document.
         *
         * Retrieve the document data based on a post ID.
         *
         * @since 2.0.0
         * @access public
         *
         * @param int  $post_id    Post ID.
         * @param bool $from_cache Optional. Whether to retrieve cached data. Default is true.
         *
         * @return false|Document Document data or false if post ID was not entered.
         */
        public function get($post_id, $from_cache = true)
        {
        }
        /**
         * Get document or autosave.
         *
         * Retrieve either the document or the autosave.
         *
         * @since 2.0.0
         * @access public
         *
         * @param int $id      Optional. Post ID. Default is `0`.
         * @param int $user_id Optional. User ID. Default is `0`.
         *
         * @return false|Document The document if it exist, False otherwise.
         */
        public function get_doc_or_auto_save($id, $user_id = 0)
        {
        }
        /**
         * Get document for frontend.
         *
         * Retrieve the document for frontend use.
         *
         * @since 2.0.0
         * @access public
         *
         * @param int $post_id Optional. Post ID. Default is `0`.
         *
         * @return false|Document The document if it exist, False otherwise.
         */
        public function get_doc_for_frontend($post_id)
        {
        }
        /**
         * Get document type.
         *
         * Retrieve the type of any given document.
         *
         * @since  2.0.0
         * @access public
         *
         * @param string $type
         *
         * @param string $fallback
         *
         * @return Document|bool The type of the document.
         */
        public function get_document_type($type, $fallback = 'post')
        {
        }
        /**
         * Get document types.
         *
         * Retrieve the all the registered document types.
         *
         * @since  2.0.0
         * @access public
         *
         * @param array $args      Optional. An array of key => value arguments to match against
         *                               the properties. Default is empty array.
         * @param string $operator Optional. The logical operation to perform. 'or' means only one
         *                               element from the array needs to match; 'and' means all elements
         *                               must match; 'not' means no elements may match. Default 'and'.
         *
         * @return Document[] All the registered document types.
         */
        public function get_document_types($args = [], $operator = 'and')
        {
        }
        /**
         * Get document types with their properties.
         *
         * @return array A list of properties arrays indexed by the type.
         */
        public function get_types_properties()
        {
        }
        /**
         * Create a document.
         *
         * Create a new document using any given parameters.
         *
         * @since 2.0.0
         * @access public
         *
         * @param string $type      Document type.
         * @param array  $post_data An array containing the post data.
         * @param array  $meta_data An array containing the post meta data.
         *
         * @return Document The type of the document.
         */
        public function create($type, $post_data = [], $meta_data = [])
        {
        }
        /**
         * Remove user edit capabilities if document is not editable.
         *
         * Filters the user capabilities to disable editing in admin.
         *
         * @param array $allcaps An array of all the user's capabilities.
         * @param array $caps    Actual capabilities for meta capability.
         * @param array $args    Optional parameters passed to has_cap(), typically object ID.
         *
         * @return array
         */
        public function remove_user_edit_cap($allcaps, $caps, $args)
        {
        }
        /**
         * Filter Post Row Actions.
         *
         * Let the Document to filter the array of row action links on the Posts list table.
         *
         * @param array $actions
         * @param \WP_Post $post
         *
         * @return array
         */
        public function filter_post_row_actions($actions, $post)
        {
        }
        /**
         * Save document data using ajax.
         *
         * Save the document on the builder using ajax, when saving the changes, and refresh the editor.
         *
         * @since 2.0.0
         * @access public
         *
         * @param $request Post ID.
         *
         * @throws \Exception If current user don't have permissions to edit the post or the post is not using Elementor.
         *
         * @return array The document data after saving.
         */
        public function ajax_save($request)
        {
        }
        /**
         * Ajax discard changes.
         *
         * Load the document data from an autosave, deleting unsaved changes.
         *
         * @since 2.0.0
         * @access public
         *
         * @param $request
         *
         * @return bool True if changes discarded, False otherwise.
         */
        public function ajax_discard_changes($request)
        {
        }
        public function ajax_get_document_config($request)
        {
        }
        /**
         * Switch to document.
         *
         * Change the document to any new given document type.
         *
         * @since 2.0.0
         * @access public
         *
         * @param Document $document The document to switch to.
         */
        public function switch_to_document($document)
        {
        }
        /**
         * Restore document.
         *
         * Rollback to the original document.
         *
         * @since 2.0.0
         * @access public
         */
        public function restore_document()
        {
        }
        /**
         * Get current document.
         *
         * Retrieve the current document.
         *
         * @since 2.0.0
         * @access public
         *
         * @return Document The current document.
         */
        public function get_current()
        {
        }
        public function localize_settings($settings)
        {
        }
        private function register_types()
        {
        }
        /**
         * Get create new post URL.
         *
         * Retrieve a custom URL for creating a new post/page using Elementor.
         *
         * @param string $post_type Optional. Post type slug. Default is 'page'.
         * @param string|null $template_type Optional. Query arg 'template_type'. Default is null.
         *
         * @return string A URL for creating new post using Elementor.
         */
        public static function get_create_new_post_url($post_type = 'page', $template_type = null)
        {
        }
    }
}
namespace Elementor {
    /**
     * Elementor base control.
     *
     * An abstract class for creating new controls in the panel.
     *
     * @since 1.0.0
     * @abstract
     */
    abstract class Base_Control extends \Elementor\Core\Base\Base_Object
    {
        /**
         * Base settings.
         *
         * Holds all the base settings of the control.
         *
         * @access private
         *
         * @var array
         */
        private $_base_settings = ['label' => '', 'description' => '', 'show_label' => true, 'label_block' => false, 'separator' => 'default'];
        /**
         * Get features.
         *
         * Retrieve the list of all the available features. Currently Elementor uses only
         * the `UI` feature.
         *
         * @since 1.5.0
         * @access public
         * @static
         *
         * @return array Features array.
         */
        public static function get_features()
        {
        }
        /**
         * Get control type.
         *
         * Retrieve the control type.
         *
         * @since 1.5.0
         * @access public
         * @abstract
         */
        public abstract function get_type();
        /**
         * Control base constructor.
         *
         * Initializing the control base class.
         *
         * @since 1.5.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Enqueue control scripts and styles.
         *
         * Used to register and enqueue custom scripts and styles used by the control.
         *
         * @since 1.5.0
         * @access public
         */
        public function enqueue()
        {
        }
        /**
         * Control content template.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * Note that the content template is wrapped by Base_Control::print_template().
         *
         * @since 1.5.0
         * @access public
         * @abstract
         */
        public abstract function content_template();
        /**
         * Print control template.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.5.0
         * @access public
         */
        public final function print_template()
        {
        }
        /**
         * Get default control settings.
         *
         * Retrieve the default settings of the control. Used to return the default
         * settings while initializing the control.
         *
         * @since 1.5.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        public static function get_assets($setting)
        {
        }
    }
}
namespace Elementor {
    /**
     * Elementor element base.
     *
     * An abstract class to register new Elementor elements. It extended the
     * `Controls_Stack` class to inherit its properties.
     *
     * This abstract class must be extended in order to register new elements.
     *
     * @since 1.0.0
     * @abstract
     */
    abstract class Element_Base extends \Elementor\Controls_Stack
    {
        /**
         * Child elements.
         *
         * Holds all the child elements of the element.
         *
         * @access private
         *
         * @var Element_Base[]
         */
        private $children;
        /**
         * Element default arguments.
         *
         * Holds all the default arguments of the element. Used to store additional
         * data. For example WordPress widgets use this to store widget names.
         *
         * @access private
         *
         * @var array
         */
        private $default_args = [];
        /**
         * Is type instance.
         *
         * Whether the element is an instance of that type or not.
         *
         * @access private
         *
         * @var bool
         */
        private $is_type_instance = true;
        /**
         * Depended scripts.
         *
         * Holds all the element depended scripts to enqueue.
         *
         * @since 1.9.0
         * @access private
         *
         * @var array
         */
        private $depended_scripts = [];
        /**
         * Depended styles.
         *
         * Holds all the element depended styles to enqueue.
         *
         * @since 1.9.0
         * @access private
         *
         * @var array
         */
        private $depended_styles = [];
        /**
         * Add script depends.
         *
         * Register new script to enqueue by the handler.
         *
         * @since 1.9.0
         * @access public
         *
         * @param string $handler Depend script handler.
         */
        public function add_script_depends($handler)
        {
        }
        /**
         * Add style depends.
         *
         * Register new style to enqueue by the handler.
         *
         * @since 1.9.0
         * @access public
         *
         * @param string $handler Depend style handler.
         */
        public function add_style_depends($handler)
        {
        }
        /**
         * Get script dependencies.
         *
         * Retrieve the list of script dependencies the element requires.
         *
         * @since 1.3.0
         * @access public
         *
         * @return array Element scripts dependencies.
         */
        public function get_script_depends()
        {
        }
        /**
         * Enqueue scripts.
         *
         * Registers all the scripts defined as element dependencies and enqueues
         * them. Use `get_script_depends()` method to add custom script dependencies.
         *
         * @since 1.3.0
         * @access public
         */
        public final function enqueue_scripts()
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the element requires.
         *
         * @since 1.9.0
         * @access public
         *
         * @return array Element styles dependencies.
         */
        public function get_style_depends()
        {
        }
        /**
         * Enqueue styles.
         *
         * Registers all the styles defined as element dependencies and enqueues
         * them. Use `get_style_depends()` method to add custom style dependencies.
         *
         * @since 1.9.0
         * @access public
         */
        public final function enqueue_styles()
        {
        }
        /**
         * @since 1.0.0
         * @deprecated 2.6.0
         * @access public
         * @static
         */
        public static final function add_edit_tool()
        {
        }
        /**
         * @since 2.2.0
         * @deprecated 2.6.0
         * @access public
         * @static
         */
        public static final function is_edit_buttons_enabled()
        {
        }
        /**
         * Get default child type.
         *
         * Retrieve the default child type based on element data.
         *
         * Note that not all elements support children.
         *
         * @since 1.0.0
         * @access protected
         * @abstract
         *
         * @param array $element_data Element data.
         *
         * @return Element_Base
         */
        protected abstract function _get_default_child_type(array $element_data);
        /**
         * Before element rendering.
         *
         * Used to add stuff before the element.
         *
         * @since 1.0.0
         * @access public
         */
        public function before_render()
        {
        }
        /**
         * After element rendering.
         *
         * Used to add stuff after the element.
         *
         * @since 1.0.0
         * @access public
         */
        public function after_render()
        {
        }
        /**
         * Get element title.
         *
         * Retrieve the element title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Element title.
         */
        public function get_title()
        {
        }
        /**
         * Get element icon.
         *
         * Retrieve the element icon.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Element icon.
         */
        public function get_icon()
        {
        }
        public function get_help_url()
        {
        }
        public function get_custom_help_url()
        {
        }
        /**
         * Whether the reload preview is required.
         *
         * Used to determine whether the reload preview is required or not.
         *
         * @since 1.0.0
         * @access public
         *
         * @return bool Whether the reload preview is required.
         */
        public function is_reload_preview_required()
        {
        }
        /**
         * @since 2.3.1
         * @access protected
         */
        protected function should_print_empty()
        {
        }
        /**
         * Get child elements.
         *
         * Retrieve all the child elements of this element.
         *
         * @since 1.0.0
         * @access public
         *
         * @return Element_Base[] Child elements.
         */
        public function get_children()
        {
        }
        /**
         * Get default arguments.
         *
         * Retrieve the element default arguments. Used to return all the default
         * arguments or a specific default argument, if one is set.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $item Optional. Default is null.
         *
         * @return array Default argument(s).
         */
        public function get_default_args($item = null)
        {
        }
        /**
         * Add new child element.
         *
         * Register new child element to allow hierarchy.
         *
         * @since 1.0.0
         * @access public
         * @param array $child_data Child element data.
         * @param array $child_args Child element arguments.
         *
         * @return Element_Base|false Child element instance, or false if failed.
         */
        public function add_child(array $child_data, array $child_args = [])
        {
        }
        /**
         * Add link render attributes.
         *
         * Used to add link tag attributes to a specific HTML element.
         *
         * The HTML link tag is represented by the element parameter. The `url_control` parameter
         * needs to be an array of link settings in the same format they are set by Elementor's URL control.
         *
         * Example usage:
         *
         * `$this->add_link_attributes( 'button', $settings['link'] );`
         *
         * @since 2.8.0
         * @access public
         *
         * @param array|string $element   The HTML element.
         * @param array $url_control      Array of link settings.
         * @param bool $overwrite         Optional. Whether to overwrite existing
         *                                attribute. Default is false, not to overwrite.
         *
         * @return Element_Base Current instance of the element.
         */
        public function add_link_attributes($element, array $url_control, $overwrite = false)
        {
        }
        /**
         * Print element.
         *
         * Used to generate the element final HTML on the frontend and the editor.
         *
         * @since 1.0.0
         * @access public
         */
        public function print_element()
        {
        }
        /**
         * Get the element raw data.
         *
         * Retrieve the raw element data, including the id, type, settings, child
         * elements and whether it is an inner element.
         *
         * The data with the HTML used always to display the data, but the Elementor
         * editor uses the raw data without the HTML in order not to render the data
         * again.
         *
         * @since 1.0.0
         * @access public
         *
         * @param bool $with_html_content Optional. Whether to return the data with
         *                                HTML content or without. Used for caching.
         *                                Default is false, without HTML.
         *
         * @return array Element raw data.
         */
        public function get_raw_data($with_html_content = false)
        {
        }
        public function get_data_for_save()
        {
        }
        /**
         * Get unique selector.
         *
         * Retrieve the unique selector of the element. Used to set a unique HTML
         * class for each HTML element. This way Elementor can set custom styles for
         * each element.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Unique selector.
         */
        public function get_unique_selector()
        {
        }
        /**
         * Is type instance.
         *
         * Used to determine whether the element is an instance of that type or not.
         *
         * @since 1.0.0
         * @access public
         *
         * @return bool Whether the element is an instance of that type.
         */
        public function is_type_instance()
        {
        }
        /**
         * On import update dynamic content (e.g. post and term IDs).
         *
         * @since 3.8.0
         *
         * @param array      $config   The config of the passed element.
         * @param array      $data     The data that requires updating/replacement when imported.
         * @param array|null $controls The available controls.
         *
         * @return array Element data.
         */
        public static function on_import_update_dynamic_content(array $config, array $data, $controls = null) : array
        {
        }
        /**
         * Add render attributes.
         *
         * Used to add attributes to the current element wrapper HTML tag.
         *
         * @since 1.3.0
         * @access protected
         * @deprecated 3.1.0
         */
        protected function _add_render_attributes()
        {
        }
        /**
         * Add render attributes.
         *
         * Used to add attributes to the current element wrapper HTML tag.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function add_render_attributes()
        {
        }
        /**
         * Register the Transform controls in the advanced tab of the element.
         *
         * Previously registered under the Widget_Common class, but registered a more fundamental level now to enable access from other widgets.
         *
         * @since 3.9.0
         * @access protected
         * @return void
         */
        protected function register_transform_section($element_selector = '')
        {
        }
        /**
         * Add Hidden Device Controls
         *
         * Adds controls for hiding elements within certain devices' viewport widths. Adds a control for each active device.
         *
         * @since 3.4.0
         * @access protected
         */
        protected function add_hidden_device_controls()
        {
        }
        /**
         * Get default data.
         *
         * Retrieve the default element data. Used to reset the data on initialization.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Default data.
         */
        protected function get_default_data()
        {
        }
        /**
         * Print element content.
         *
         * Output the element final HTML on the frontend.
         *
         * @since 1.0.0
         * @access protected
         * @deprecated 3.1.0
         */
        protected function _print_content()
        {
        }
        /**
         * Print element content.
         *
         * Output the element final HTML on the frontend.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function print_content()
        {
        }
        /**
         * Get initial config.
         *
         * Retrieve the current element initial configuration.
         *
         * Adds more configuration on top of the controls list and the tabs assigned
         * to the control. This method also adds element name, type, icon and more.
         *
         * @since 2.9.0
         * @access protected
         *
         * @return array The initial config.
         */
        protected function get_initial_config()
        {
        }
        /**
         * A Base method for sanitizing the settings before save.
         * This method is meant to be overridden by the element.
         */
        protected function on_save(array $settings)
        {
        }
        /**
         * Get child type.
         *
         * Retrieve the element child type based on element data.
         *
         * @since 2.0.0
         * @access private
         *
         * @param array $element_data Element ID.
         *
         * @return Element_Base|false Child type or false if type not found.
         */
        private function get_child_type($element_data)
        {
        }
        /**
         * Initialize children.
         *
         * Initializing the element child elements.
         *
         * @since 2.0.0
         * @access private
         */
        private function init_children()
        {
        }
        /**
         * Element base constructor.
         *
         * Initializing the element base class using `$data` and `$args`.
         *
         * The `$data` parameter is required for a normal instance because of the
         * way Elementor renders data when initializing elements.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array      $data Optional. Element data. Default is an empty array.
         * @param array|null $args Optional. Element default arguments. Default is null.
         **/
        public function __construct(array $data = [], array $args = null)
        {
        }
    }
    /**
     * Elementor widget base.
     *
     * An abstract class to register new Elementor widgets. It extended the
     * `Element_Base` class to inherit its properties.
     *
     * This abstract class must be extended in order to register new widgets.
     *
     * @since 1.0.0
     * @abstract
     */
    abstract class Widget_Base extends \Elementor\Element_Base
    {
        /**
         * Whether the widget has content.
         *
         * Used in cases where the widget has no content. When widgets uses only
         * skins to display dynamic content generated on the server. For example the
         * posts widget in Elementor Pro. Default is true, the widget has content
         * template.
         *
         * @access protected
         *
         * @var bool
         */
        protected $_has_template_content = true;
        private $is_first_section = true;
        /**
         * Registered Runtime Widgets.
         *
         * Registering in runtime all widgets that are being used on the page.
         *
         * @since 3.3.0
         * @access public
         * @static
         *
         * @var array
         */
        public static $registered_runtime_widgets = [];
        public static $registered_inline_css_widgets = [];
        private static $widgets_css_data_manager;
        private static $responsive_widgets_data_manager;
        /**
         * Get element type.
         *
         * Retrieve the element type, in this case `widget`.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @return string The type.
         */
        public static function get_type()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve the widget icon.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget icon.
         */
        public function get_icon()
        {
        }
        /**
         * Get widget keywords.
         *
         * Retrieve the widget keywords.
         *
         * @since 1.0.10
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        /**
         * Get widget categories.
         *
         * Retrieve the widget categories.
         *
         * @since 1.0.10
         * @access public
         *
         * @return array Widget categories.
         */
        public function get_categories()
        {
        }
        /**
         * Widget base constructor.
         *
         * Initializing the widget base class.
         *
         * @since 1.0.0
         * @access public
         *
         * @throws \Exception If arguments are missing when initializing a full widget
         *                   instance.
         *
         * @param array      $data Widget data. Default is an empty array.
         * @param array|null $args Optional. Widget default arguments. Default is null.
         */
        public function __construct($data = [], $args = null)
        {
        }
        /**
         * Get stack.
         *
         * Retrieve the widget stack of controls.
         *
         * @since 1.9.2
         * @access public
         *
         * @param bool $with_common_controls Optional. Whether to include the common controls. Default is true.
         *
         * @return array Widget stack of controls.
         */
        public function get_stack($with_common_controls = true)
        {
        }
        /**
         * Get widget controls pointer index.
         *
         * Retrieve widget pointer index where the next control should be added.
         *
         * While using injection point, it will return the injection point index. Otherwise index of the last control of the
         * current widget itself without the common controls, plus one.
         *
         * @since 1.9.2
         * @access public
         *
         * @return int Widget controls pointer index.
         */
        public function get_pointer_index()
        {
        }
        /**
         * Show in panel.
         *
         * Whether to show the widget in the panel or not. By default returns true.
         *
         * @since 1.0.0
         * @access public
         *
         * @return bool Whether to show the widget in the panel or not.
         */
        public function show_in_panel()
        {
        }
        /**
         * Hide on search.
         *
         * Whether to hide the widget on search in the panel or not. By default returns false.
         *
         * @access public
         *
         * @return bool Whether to hide the widget when searching for widget or not.
         */
        public function hide_on_search()
        {
        }
        /**
         * Start widget controls section.
         *
         * Used to add a new section of controls to the widget. Regular controls and
         * skin controls.
         *
         * Note that when you add new controls to widgets they must be wrapped by
         * `start_controls_section()` and `end_controls_section()`.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $section_id Section ID.
         * @param array  $args       Section arguments Optional.
         */
        public function start_controls_section($section_id, array $args = [])
        {
        }
        /**
         * Register the Skin Control if the widget has skins.
         *
         * An internal method that is used to add a skin control to the widget.
         * Added at the top of the controls section.
         *
         * @since 2.0.0
         * @access private
         */
        private function register_skin_control()
        {
        }
        /**
         * Register widget skins - deprecated prefixed method
         *
         * @since 1.7.12
         * @access protected
         * @deprecated 3.1.0
         */
        protected function _register_skins()
        {
        }
        /**
         * Register widget skins.
         *
         * This method is activated while initializing the widget base class. It is
         * used to assign skins to widgets with `add_skin()` method.
         *
         * Usage:
         *
         *    protected function register_skins() {
         *        $this->add_skin( new Skin_Classic( $this ) );
         *    }
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_skins()
        {
        }
        /**
         * Get initial config.
         *
         * Retrieve the current widget initial configuration.
         *
         * Adds more configuration on top of the controls list, the tabs assigned to
         * the control, element name, type, icon and more. This method also adds
         * widget type, keywords and categories.
         *
         * @since 2.9.0
         * @access protected
         *
         * @return array The initial widget config.
         */
        protected function get_initial_config()
        {
        }
        /**
         * @since 2.3.1
         * @access protected
         */
        protected function should_print_empty()
        {
        }
        /**
         * Print widget content template.
         *
         * Used to generate the widget content template on the editor, using a
         * Backbone JavaScript template.
         *
         * @since 2.0.0
         * @access protected
         *
         * @param string $template_content Template content.
         */
        protected function print_template_content($template_content)
        {
        }
        /**
         * Parse text editor.
         *
         * Parses the content from rich text editor with shortcodes, oEmbed and
         * filtered data.
         *
         * @since 1.0.0
         * @access protected
         *
         * @param string $content Text editor content.
         *
         * @return string Parsed content.
         */
        protected function parse_text_editor($content)
        {
        }
        /**
         * Safe print parsed text editor.
         *
         * @uses static::parse_text_editor.
         *
         * @access protected
         *
         * @param string $content Text editor content.
         */
        protected final function print_text_editor($content)
        {
        }
        /**
         * Get HTML wrapper class.
         *
         * Retrieve the widget container class. Can be used to override the
         * container class for specific widgets.
         *
         * @since 2.0.9
         * @access protected
         */
        protected function get_html_wrapper_class()
        {
        }
        /**
         * Add widget render attributes.
         *
         * Used to add attributes to the current widget wrapper HTML tag.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function add_render_attributes()
        {
        }
        /**
         * Add lightbox data to image link.
         *
         * Used to add lightbox data attributes to image link HTML.
         *
         * @since 2.9.1
         * @access public
         *
         * @param string $link_html Image link HTML.
         * @param string $id Attachment id.
         *
         * @return string Image link HTML with lightbox data attributes.
         */
        public function add_lightbox_data_to_image_link($link_html, $id)
        {
        }
        /**
         * Add Light-Box attributes.
         *
         * Used to add Light-Box-related data attributes to links that open media files.
         *
         * @param array|string $element         The link HTML element.
         * @param int $id                       The ID of the image
         * @param string $lightbox_setting_key  The setting key that dictates weather to open the image in a lightbox
         * @param string $group_id              Unique ID for a group of lightbox images
         * @param bool $overwrite               Optional. Whether to overwrite existing
         *                                      attribute. Default is false, not to overwrite.
         *
         * @return Widget_Base Current instance of the widget.
         * @since 2.9.0
         * @access public
         *
         */
        public function add_lightbox_data_attributes($element, $id = null, $lightbox_setting_key = null, $group_id = null, $overwrite = false)
        {
        }
        /**
         * Render widget output on the frontend.
         *
         * Used to generate the final HTML displayed on the frontend.
         *
         * Note that if skin is selected, it will be rendered by the skin itself,
         * not the widget.
         *
         * @since 1.0.0
         * @access public
         */
        public function render_content()
        {
        }
        protected function is_widget_first_render($widget_name)
        {
        }
        /**
         * Render widget plain content.
         *
         * Elementor saves the page content in a unique way, but it's not the way
         * WordPress saves data. This method is used to save generated HTML to the
         * database as plain content the WordPress way.
         *
         * When rendering plain content, it allows other WordPress plugins to
         * interact with the content - to search, check SEO and other purposes. It
         * also allows the site to keep working even if Elementor is deactivated.
         *
         * Note that if the widget uses shortcodes to display the data, the best
         * practice is to return the shortcode itself.
         *
         * Also note that if the widget don't display any content it should return
         * an empty string. For example Elementor Pro Form Widget uses this method
         * to return an empty string because there is no content to return. This way
         * if Elementor Pro will be deactivated there won't be any form to display.
         *
         * @since 1.0.0
         * @access public
         */
        public function render_plain_content()
        {
        }
        /**
         * Before widget rendering.
         *
         * Used to add stuff before the widget `_wrapper` element.
         *
         * @since 1.0.0
         * @access public
         */
        public function before_render()
        {
        }
        /**
         * After widget rendering.
         *
         * Used to add stuff after the widget `_wrapper` element.
         *
         * @since 1.0.0
         * @access public
         */
        public function after_render()
        {
        }
        /**
         * Get the element raw data.
         *
         * Retrieve the raw element data, including the id, type, settings, child
         * elements and whether it is an inner element.
         *
         * The data with the HTML used always to display the data, but the Elementor
         * editor uses the raw data without the HTML in order not to render the data
         * again.
         *
         * @since 1.0.0
         * @access public
         *
         * @param bool $with_html_content Optional. Whether to return the data with
         *                                HTML content or without. Used for caching.
         *                                Default is false, without HTML.
         *
         * @return array Element raw data.
         */
        public function get_raw_data($with_html_content = false)
        {
        }
        /**
         * Print widget content.
         *
         * Output the widget final HTML on the frontend.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function print_content()
        {
        }
        /**
         * Print a setting content without escaping.
         *
         * Script tags are allowed on frontend according to the WP theme securing policy.
         *
         * @param string $setting
         * @param null $repeater_name
         * @param null $index
         */
        public final function print_unescaped_setting($setting, $repeater_name = null, $index = null)
        {
        }
        /**
         * Get default data.
         *
         * Retrieve the default widget data. Used to reset the data on initialization.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Default data.
         */
        protected function get_default_data()
        {
        }
        /**
         * Get default child type.
         *
         * Retrieve the widget child type based on element data.
         *
         * @since 1.0.0
         * @access protected
         *
         * @param array $element_data Widget ID.
         *
         * @return array|false Child type or false if it's not a valid widget.
         */
        protected function _get_default_child_type(array $element_data)
        {
        }
        /**
         * Get repeater setting key.
         *
         * Retrieve the unique setting key for the current repeater item. Used to connect the current element in the
         * repeater to it's settings model and it's control in the panel.
         *
         * PHP usage (inside `Widget_Base::render()` method):
         *
         *    $tabs = $this->get_settings( 'tabs' );
         *    foreach ( $tabs as $index => $item ) {
         *        $tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );
         *        $this->add_inline_editing_attributes( $tab_title_setting_key, 'none' );
         *        echo '<div ' . $this->get_render_attribute_string( $tab_title_setting_key ) . '>' . $item['tab_title'] . '</div>';
         *    }
         *
         * @since 1.8.0
         * @access protected
         *
         * @param string $setting_key      The current setting key inside the repeater item (e.g. `tab_title`).
         * @param string $repeater_key     The repeater key containing the array of all the items in the repeater (e.g. `tabs`).
         * @param int $repeater_item_index The current item index in the repeater array (e.g. `3`).
         *
         * @return string The repeater setting key (e.g. `tabs.3.tab_title`).
         */
        protected function get_repeater_setting_key($setting_key, $repeater_key, $repeater_item_index)
        {
        }
        /**
         * Add inline editing attributes.
         *
         * Define specific area in the element to be editable inline. The element can have several areas, with this method
         * you can set the area inside the element that can be edited inline. You can also define the type of toolbar the
         * user will see, whether it will be a basic toolbar or an advanced one.
         *
         * Note: When you use wysiwyg control use the advanced toolbar, with textarea control use the basic toolbar. Text
         * control should not have toolbar.
         *
         * PHP usage (inside `Widget_Base::render()` method):
         *
         *    $this->add_inline_editing_attributes( 'text', 'advanced' );
         *    echo '<div ' . $this->get_render_attribute_string( 'text' ) . '>' . $this->get_settings( 'text' ) . '</div>';
         *
         * @since 1.8.0
         * @access protected
         *
         * @param string $key     Element key.
         * @param string $toolbar Optional. Toolbar type. Accepted values are `advanced`, `basic` or `none`. Default is
         *                        `basic`.
         */
        protected function add_inline_editing_attributes($key, $toolbar = 'basic')
        {
        }
        /**
         * Add new skin.
         *
         * Register new widget skin to allow the user to set custom designs. Must be
         * called inside the `register_skins()` method.
         *
         * @since 1.0.0
         * @access public
         *
         * @param Skin_Base $skin Skin instance.
         */
        public function add_skin(\Elementor\Skin_Base $skin)
        {
        }
        /**
         * Get single skin.
         *
         * Retrieve a single skin based on skin ID, from all the skin assigned to
         * the widget. If the skin does not exist or not assigned to the widget,
         * return false.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $skin_id Skin ID.
         *
         * @return string|false Single skin, or false.
         */
        public function get_skin($skin_id)
        {
        }
        /**
         * Get current skin ID.
         *
         * Retrieve the ID of the current skin.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Current skin.
         */
        public function get_current_skin_id()
        {
        }
        /**
         * Get current skin.
         *
         * Retrieve the current skin, or if non exist return false.
         *
         * @since 1.0.0
         * @access public
         *
         * @return Skin_Base|false Current skin or false.
         */
        public function get_current_skin()
        {
        }
        /**
         * Remove widget skin.
         *
         * Unregister an existing skin and remove it from the widget.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $skin_id Skin ID.
         *
         * @return \WP_Error|true Whether the skin was removed successfully from the widget.
         */
        public function remove_skin($skin_id)
        {
        }
        /**
         * Get widget skins.
         *
         * Retrieve all the skin assigned to the widget.
         *
         * @since 1.0.0
         * @access public
         *
         * @return Skin_Base[]
         */
        public function get_skins()
        {
        }
        /**
         * Get group name.
         *
         * Some widgets need to use group names, this method allows you to create them.
         * By default it retrieves the regular name.
         *
         * @since 3.3.0
         * @access public
         *
         * @return string Unique name.
         */
        public function get_group_name()
        {
        }
        /**
         * Get Inline CSS dependencies.
         *
         * Retrieve a list of inline CSS dependencies that the element requires.
         *
         * @since 3.3.0
         * @access public
         *
         * @return array.
         */
        public function get_inline_css_depends()
        {
        }
        /**
         * @param string $plugin_title  Plugin's title
         * @param string $since         Plugin version widget was deprecated
         * @param string $last          Plugin version in which the widget will be removed
         * @param string $replacement   Widget replacement
         */
        protected function deprecated_notice($plugin_title, $since, $last = '', $replacement = '')
        {
        }
        public function register_runtime_widget($widget_name)
        {
        }
        public function get_widget_css_config($widget_name)
        {
        }
        public function get_css_config()
        {
        }
        public function get_responsive_widgets_config()
        {
        }
        public function get_responsive_widgets()
        {
        }
        /**
         * Get Responsive Widgets Data Manager.
         *
         * Retrieve the data manager that handles widgets that are using media queries for custom-breakpoints values.
         *
         * @since 3.5.0
         * @access protected
         *
         * @return Responsive_Widgets_Data_Manager
         */
        protected function get_responsive_widgets_data_manager()
        {
        }
        /**
         * Is Custom Breakpoints Widget.
         *
         * Checking if there are active custom-breakpoints and if the widget use them.
         *
         * @since 3.5.0
         * @access protected
         *
         * @return boolean
         */
        protected function is_custom_breakpoints_widget()
        {
        }
        private function get_widget_css()
        {
        }
        private function is_inline_css_mode()
        {
        }
        private function print_widget_css()
        {
        }
        private function get_widgets_css_data_manager()
        {
        }
    }
    /**
     * Group control interface.
     *
     * An interface for Elementor group control.
     *
     * @since 1.0.0
     */
    interface Group_Control_Interface
    {
        /**
         * Get group control type.
         *
         * Retrieve the group control type.
         *
         * @since 1.0.0
         * @access public
         * @static
         */
        public static function get_type();
    }
    /**
     * Elementor group control base.
     *
     * An abstract class for creating new group controls in the panel.
     *
     * @since 1.0.0
     * @abstract
     */
    abstract class Group_Control_Base implements \Elementor\Group_Control_Interface
    {
        /**
         * Arguments.
         *
         * Holds all the group control arguments.
         *
         * @access private
         *
         * @var array Group control arguments.
         */
        private $args = [];
        /**
         * Options.
         *
         * Holds all the group control options.
         *
         * Currently supports only the popover options.
         *
         * @access private
         *
         * @var array Group control options.
         */
        private $options;
        /**
         * Get options.
         *
         * Retrieve group control options. If options are not set, it will initialize default options.
         *
         * @since 1.9.0
         * @access public
         *
         * @param array $option Optional. Single option.
         *
         * @return mixed Group control options. If option parameter was not specified, it will
         *               return an array of all the options. If single option specified, it will
         *               return the option value or `null` if option does not exists.
         */
        public final function get_options($option = null)
        {
        }
        /**
         * Add new controls to stack.
         *
         * Register multiple controls to allow the user to set/update data.
         *
         * @since 1.0.0
         * @access public
         *
         * @param Controls_Stack $element   The element stack.
         * @param array          $user_args The control arguments defined by the user.
         * @param array          $options   Optional. The element options. Default is
         *                                  an empty array.
         */
        public final function add_controls(\Elementor\Controls_Stack $element, array $user_args, array $options = [])
        {
        }
        /**
         * Get arguments.
         *
         * Retrieve group control arguments.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array Group control arguments.
         */
        public final function get_args()
        {
        }
        /**
         * Get fields.
         *
         * Retrieve group control fields.
         *
         * @since 1.2.2
         * @access public
         *
         * @return array Control fields.
         */
        public final function get_fields()
        {
        }
        /**
         * Get controls prefix.
         *
         * Retrieve the prefix of the group control, which is `{{ControlName}}_`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control prefix.
         */
        public function get_controls_prefix()
        {
        }
        /**
         * Get group control classes.
         *
         * Retrieve the classes of the group control.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Group control classes.
         */
        public function get_base_group_classes()
        {
        }
        /**
         * Init fields.
         *
         * Initialize group control fields.
         *
         * @abstract
         * @since 1.2.2
         * @access protected
         */
        protected abstract function init_fields();
        /**
         * Get default options.
         *
         * Retrieve the default options of the group control. Used to return the
         * default options while initializing the group control.
         *
         * @since 1.9.0
         * @access protected
         *
         * @return array Default group control options.
         */
        protected function get_default_options()
        {
        }
        /**
         * Get child default arguments.
         *
         * Retrieve the default arguments for all the child controls for a specific group
         * control.
         *
         * @since 1.2.2
         * @access protected
         *
         * @return array Default arguments for all the child controls.
         */
        protected function get_child_default_args()
        {
        }
        /**
         * Filter fields.
         *
         * Filter which controls to display, using `include`, `exclude` and the
         * `condition` arguments.
         *
         * @since 1.2.2
         * @access protected
         *
         * @return array Control fields.
         */
        protected function filter_fields()
        {
        }
        /**
         * Add group arguments to field.
         *
         * Register field arguments to group control.
         *
         * @since 1.2.2
         * @access protected
         *
         * @param string $control_id Group control id.
         * @param array  $field_args Group control field arguments.
         *
         * @return array
         */
        protected function add_group_args_to_field($control_id, $field_args)
        {
        }
        /**
         * Prepare fields.
         *
         * Process group control fields before adding them to `add_control()`.
         *
         * @since 1.2.2
         * @access protected
         *
         * @param array $fields Group control fields.
         *
         * @return array Processed fields.
         */
        protected function prepare_fields($fields)
        {
        }
        /**
         * Init options.
         *
         * Initializing group control options.
         *
         * @since 1.9.0
         * @access private
         */
        private function init_options()
        {
        }
        /**
         * Init arguments.
         *
         * Initializing group control base class.
         *
         * @since 1.2.2
         * @access protected
         *
         * @param array $args Group control settings value.
         */
        protected function init_args($args)
        {
        }
        /**
         * Get default arguments.
         *
         * Retrieve the default arguments of the group control. Used to return the
         * default arguments while initializing the group control.
         *
         * @since 1.2.2
         * @access private
         *
         * @return array Control default arguments.
         */
        private function get_default_args()
        {
        }
        /**
         * Add condition prefix.
         *
         * Used to add the group prefix to controls with conditions, to
         * distinguish them from other controls with the same name.
         *
         * This way Elementor can apply condition logic to a specific control in a
         * group control.
         *
         * @since 1.2.0
         * @access private
         *
         * @param array $field Group control field.
         *
         * @return array Group control field.
         */
        private function add_condition_prefix($field)
        {
        }
        private function add_conditions_prefix($conditions)
        {
        }
        /**
         * Handle selectors.
         *
         * Used to process the CSS selector of group control fields. When using
         * group control, Elementor needs to apply the selector to different fields.
         * This method handles the process.
         *
         * In addition, it handles selector values from other fields and process the
         * css.
         *
         * @since 1.2.2
         * @access private
         *
         * @param array $selectors An array of selectors to process.
         *
         * @return array Processed selectors.
         */
        private function handle_selectors($selectors)
        {
        }
        /**
         * Start popover.
         *
         * Starts a group controls popover.
         *
         * @since 1.9.1
         * @access private
         * @param Controls_Stack $element Element.
         */
        private function start_popover(\Elementor\Controls_Stack $element)
        {
        }
    }
    /**
     * Elementor typography control.
     *
     * A base control for creating typography control. Displays input fields to define
     * the content typography including font size, font family, font weight, text
     * transform, font style, line height and letter spacing.
     *
     * @since 1.0.0
     */
    class Group_Control_Typography extends \Elementor\Group_Control_Base
    {
        /**
         * Fields.
         *
         * Holds all the typography control fields.
         *
         * @since 1.0.0
         * @access protected
         * @static
         *
         * @var array Typography control fields.
         */
        protected static $fields;
        /**
         * Scheme fields keys.
         *
         * Holds all the typography control scheme fields keys.
         * Default is an array containing `font_family` and `font_weight`.
         *
         * @since 1.0.0
         * @access private
         * @static
         *
         * @var array Typography control scheme fields keys.
         */
        private static $_scheme_fields_keys = ['font_family', 'font_weight'];
        /**
         * Get scheme fields keys.
         *
         * Retrieve all the available typography control scheme fields keys.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @return array Scheme fields keys.
         */
        public static function get_scheme_fields_keys()
        {
        }
        /**
         * Get typography control type.
         *
         * Retrieve the control type, in this case `typography`.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @return string Control type.
         */
        public static function get_type()
        {
        }
        /**
         * Init fields.
         *
         * Initialize typography control fields.
         *
         * @since 1.2.2
         * @access protected
         *
         * @return array Control fields.
         */
        protected function init_fields()
        {
        }
        /**
         * Prepare fields.
         *
         * Process typography control fields before adding them to `add_control()`.
         *
         * @since 1.2.3
         * @access protected
         *
         * @param array $fields Typography control fields.
         *
         * @return array Processed fields.
         */
        protected function prepare_fields($fields)
        {
        }
        /**
         * Add group arguments to field.
         *
         * Register field arguments to typography control.
         *
         * @since 1.2.2
         * @access protected
         *
         * @param string $control_id Typography control id.
         * @param array  $field_args Typography control field arguments.
         *
         * @return array Field arguments.
         */
        protected function add_group_args_to_field($control_id, $field_args)
        {
        }
        /**
         * Get default options.
         *
         * Retrieve the default options of the typography control. Used to return the
         * default options while initializing the typography control.
         *
         * @since 1.9.0
         * @access protected
         *
         * @return array Default typography control options.
         */
        protected function get_default_options()
        {
        }
    }
}
namespace Elementor {
    /**
     * Elementor controls manager.
     *
     * Elementor controls manager handler class is responsible for registering and
     * initializing all the supported controls, both regular controls and the group
     * controls.
     *
     * @since 1.0.0
     */
    class Controls_Manager
    {
        /**
         * Content tab.
         */
        const TAB_CONTENT = 'content';
        /**
         * Style tab.
         */
        const TAB_STYLE = 'style';
        /**
         * Advanced tab.
         */
        const TAB_ADVANCED = 'advanced';
        /**
         * Responsive tab.
         */
        const TAB_RESPONSIVE = 'responsive';
        /**
         * Layout tab.
         */
        const TAB_LAYOUT = 'layout';
        /**
         * Settings tab.
         */
        const TAB_SETTINGS = 'settings';
        /**
         * Text control.
         */
        const TEXT = 'text';
        /**
         * Number control.
         */
        const NUMBER = 'number';
        /**
         * Textarea control.
         */
        const TEXTAREA = 'textarea';
        /**
         * Select control.
         */
        const SELECT = 'select';
        /**
         * Switcher control.
         */
        const SWITCHER = 'switcher';
        /**
         * Button control.
         */
        const BUTTON = 'button';
        /**
         * Hidden control.
         */
        const HIDDEN = 'hidden';
        /**
         * Heading control.
         */
        const HEADING = 'heading';
        /**
         * Raw HTML control.
         */
        const RAW_HTML = 'raw_html';
        /**
         * Notice control.
         */
        const NOTICE = 'notice';
        /**
         * Deprecated Notice control.
         */
        const DEPRECATED_NOTICE = 'deprecated_notice';
        /**
         * Alert control.
         */
        const ALERT = 'alert';
        /**
         * Popover Toggle control.
         */
        const POPOVER_TOGGLE = 'popover_toggle';
        /**
         * Section control.
         */
        const SECTION = 'section';
        /**
         * Tab control.
         */
        const TAB = 'tab';
        /**
         * Tabs control.
         */
        const TABS = 'tabs';
        /**
         * Divider control.
         */
        const DIVIDER = 'divider';
        /**
         * Color control.
         */
        const COLOR = 'color';
        /**
         * Media control.
         */
        const MEDIA = 'media';
        /**
         * Slider control.
         */
        const SLIDER = 'slider';
        /**
         * Dimensions control.
         */
        const DIMENSIONS = 'dimensions';
        /**
         * Choose control.
         */
        const CHOOSE = 'choose';
        /**
         * WYSIWYG control.
         */
        const WYSIWYG = 'wysiwyg';
        /**
         * Code control.
         */
        const CODE = 'code';
        /**
         * Font control.
         */
        const FONT = 'font';
        /**
         * Image dimensions control.
         */
        const IMAGE_DIMENSIONS = 'image_dimensions';
        /**
         * WordPress widget control.
         */
        const WP_WIDGET = 'wp_widget';
        /**
         * URL control.
         */
        const URL = 'url';
        /**
         * Repeater control.
         */
        const REPEATER = 'repeater';
        /**
         * Icon control.
         */
        const ICON = 'icon';
        /**
         * Icons control.
         */
        const ICONS = 'icons';
        /**
         * Gallery control.
         */
        const GALLERY = 'gallery';
        /**
         * Structure control.
         */
        const STRUCTURE = 'structure';
        /**
         * Select2 control.
         */
        const SELECT2 = 'select2';
        /**
         * Date/Time control.
         */
        const DATE_TIME = 'date_time';
        /**
         * Box shadow control.
         */
        const BOX_SHADOW = 'box_shadow';
        /**
         * Text shadow control.
         */
        const TEXT_SHADOW = 'text_shadow';
        /**
         * Entrance animation control.
         */
        const ANIMATION = 'animation';
        /**
         * Hover animation control.
         */
        const HOVER_ANIMATION = 'hover_animation';
        /**
         * Exit animation control.
         */
        const EXIT_ANIMATION = 'exit_animation';
        /**
         * Controls.
         *
         * Holds the list of all the controls. Default is `null`.
         *
         * @since 1.0.0
         * @access private
         *
         * @var Base_Control[]
         */
        private $controls = null;
        /**
         * Control groups.
         *
         * Holds the list of all the control groups. Default is an empty array.
         *
         * @since 1.0.0
         * @access private
         *
         * @var Group_Control_Base[]
         */
        private $control_groups = [];
        /**
         * Control stacks.
         *
         * Holds the list of all the control stacks. Default is an empty array.
         *
         * @since 1.0.0
         * @access private
         *
         * @var array
         */
        private $stacks = [];
        /**
         * Tabs.
         *
         * Holds the list of all the tabs.
         *
         * @since 1.0.0
         * @access private
         * @static
         *
         * @var array
         */
        private static $tabs;
        /**
         * Init tabs.
         *
         * Initialize control tabs.
         *
         * @since 1.6.0
         * @access private
         * @static
         */
        private static function init_tabs()
        {
        }
        /**
         * Get tabs.
         *
         * Retrieve the tabs of the current control.
         *
         * @since 1.6.0
         * @access public
         * @static
         *
         * @return array Control tabs.
         */
        public static function get_tabs()
        {
        }
        /**
         * Add tab.
         *
         * This method adds a new tab to the current control.
         *
         * @since 1.6.0
         * @access public
         * @static
         *
         * @param string $tab_name  Tab name.
         * @param string $tab_label Tab label.
         */
        public static function add_tab($tab_name, $tab_label = '')
        {
        }
        public static function get_groups_names()
        {
        }
        public static function get_controls_names()
        {
        }
        /**
         * Register controls.
         *
         * This method creates a list of all the supported controls by requiring the
         * control files and initializing each one of them.
         *
         * The list of supported controls includes the regular controls and the group
         * controls.
         *
         * External developers can register new controls by hooking to the
         * `elementor/controls/controls_registered` action.
         *
         * @since 3.1.0
         * @access private
         */
        private function register_controls()
        {
        }
        /**
         * Register control.
         *
         * This method adds a new control to the controls list. It adds any given
         * control to any given control instance.
         *
         * @since 1.0.0
         * @access public
         * @deprecated 3.5.0 Use `$this->register()` instead.
         *
         * @param string       $control_id       Control ID.
         * @param Base_Control $control_instance Control instance, usually the
         *                                       current instance.
         */
        public function register_control($control_id, \Elementor\Base_Control $control_instance)
        {
        }
        /**
         * Register control.
         *
         * This method adds a new control to the controls list. It adds any given
         * control to any given control instance.
         *
         * @since 3.5.0
         * @access public
         *
         * @param Base_Control $control_instance Control instance, usually the current instance.
         * @param string       $control_id       Control ID. Deprecated parameter.
         *
         * @return void
         */
        public function register(\Elementor\Base_Control $control_instance, $control_id = null)
        {
        }
        /**
         * Unregister control.
         *
         * This method removes control from the controls list.
         *
         * @since 1.0.0
         * @access public
         * @deprecated 3.5.0 Use `$this->unregister()` instead.
         *
         * @param string $control_id Control ID.
         *
         * @return bool True if the control was removed, False otherwise.
         */
        public function unregister_control($control_id)
        {
        }
        /**
         * Unregister control.
         *
         * This method removes control from the controls list.
         *
         * @since 3.5.0
         * @access public
         *
         * @param string $control_id Control ID.
         *
         * @return bool Whether the controls has been unregistered.
         */
        public function unregister($control_id)
        {
        }
        /**
         * Get controls.
         *
         * Retrieve the controls list from the current instance.
         *
         * @since 1.0.0
         * @access public
         *
         * @return Base_Control[] Controls list.
         */
        public function get_controls()
        {
        }
        /**
         * Get control.
         *
         * Retrieve a specific control from the current controls instance.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $control_id Control ID.
         *
         * @return bool|Base_Control Control instance, or False otherwise.
         */
        public function get_control($control_id)
        {
        }
        /**
         * Get controls data.
         *
         * Retrieve all the registered controls and all the data for each control.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array {
         *    Control data.
         *
         *    @type array $name Control data.
         * }
         */
        public function get_controls_data()
        {
        }
        /**
         * Render controls.
         *
         * Generate the final HTML for all the registered controls using the element
         * template.
         *
         * @since 1.0.0
         * @access public
         */
        public function render_controls()
        {
        }
        /**
         * Get control groups.
         *
         * Retrieve a specific group for a given ID, or a list of all the control
         * groups.
         *
         * If the given group ID is wrong, it will return `null`. When the ID valid,
         * it will return the group control instance. When no ID was given, it will
         * return all the control groups.
         *
         * @since 1.0.10
         * @access public
         *
         * @param string $id Optional. Group ID. Default is null.
         *
         * @return null|Group_Control_Base|Group_Control_Base[]
         */
        public function get_control_groups($id = null)
        {
        }
        /**
         * Add group control.
         *
         * This method adds a new group control to the control groups list. It adds
         * any given group control to any given group control instance.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string             $id       Group control ID.
         * @param Group_Control_Base $instance Group control instance, usually the
         *                                     current instance.
         *
         * @return Group_Control_Base Group control instance.
         */
        public function add_group_control($id, $instance)
        {
        }
        /**
         * Enqueue control scripts and styles.
         *
         * Used to register and enqueue custom scripts and styles used by the control.
         *
         * @since 1.0.0
         * @access public
         */
        public function enqueue_control_scripts()
        {
        }
        /**
         * Open new stack.
         *
         * This method adds a new stack to the control stacks list. It adds any
         * given stack to the current control instance.
         *
         * @since 1.0.0
         * @access public
         *
         * @param Controls_Stack $controls_stack Controls stack.
         */
        public function open_stack(\Elementor\Controls_Stack $controls_stack)
        {
        }
        /**
         * Remove existing stack from the stacks cache
         *
         * Removes the stack of a passed instance from the Controls Manager's stacks cache.
         *
         * @param Controls_Stack $controls_stack
         * @return void
         */
        public function delete_stack(\Elementor\Controls_Stack $controls_stack)
        {
        }
        /**
         * Add control to stack.
         *
         * This method adds a new control to the stack.
         *
         * @since 1.0.0
         * @access public
         *
         * @param Controls_Stack $element      Element stack.
         * @param string         $control_id   Control ID.
         * @param array          $control_data Control data.
         * @param array          $options      Optional. Control additional options.
         *                                     Default is an empty array.
         *
         * @return bool True if control added, False otherwise.
         */
        public function add_control_to_stack(\Elementor\Controls_Stack $element, $control_id, $control_data, $options = [])
        {
        }
        /**
         * Remove control from stack.
         *
         * This method removes a control a the stack.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $stack_id   Stack ID.
         * @param array|string $control_id The ID of the control to remove.
         *
         * @return bool|\WP_Error True if the stack was removed, False otherwise.
         */
        public function remove_control_from_stack($stack_id, $control_id)
        {
        }
        /**
         * Get control from stack.
         *
         * Retrieve a specific control for a given a specific stack.
         *
         * If the given control does not exist in the stack, or the stack does not
         * exist, it will return `WP_Error`. Otherwise, it will retrieve the control
         * from the stack.
         *
         * @since 1.1.0
         * @access public
         *
         * @param string $stack_id   Stack ID.
         * @param string $control_id Control ID.
         *
         * @return array|\WP_Error The control, or an error.
         */
        public function get_control_from_stack($stack_id, $control_id)
        {
        }
        /**
         * Update control in stack.
         *
         * This method updates the control data for a given stack.
         *
         * @since 1.1.0
         * @access public
         *
         * @param Controls_Stack $element      Element stack.
         * @param string         $control_id   Control ID.
         * @param array          $control_data Control data.
         * @param array          $options      Optional. Control additional options.
         *                                     Default is an empty array.
         *
         * @return bool True if control updated, False otherwise.
         */
        public function update_control_in_stack(\Elementor\Controls_Stack $element, $control_id, $control_data, array $options = [])
        {
        }
        /**
         * Get stacks.
         *
         * Retrieve a specific stack for the list of stacks.
         *
         * If the given stack is wrong, it will return `null`. When the stack valid,
         * it will return the the specific stack. When no stack was given, it will
         * return all the stacks.
         *
         * @since 1.7.1
         * @access public
         *
         * @param string $stack_id Optional. stack ID. Default is null.
         *
         * @return null|array A list of stacks.
         */
        public function get_stacks($stack_id = null)
        {
        }
        /**
         * Get element stack.
         *
         * Retrieve a specific stack for the list of stacks from the current instance.
         *
         * @since 1.0.0
         * @access public
         *
         * @param Controls_Stack $controls_stack  Controls stack.
         *
         * @return null|array Stack data if it exist, `null` otherwise.
         */
        public function get_element_stack(\Elementor\Controls_Stack $controls_stack)
        {
        }
        /**
         * Add custom CSS controls.
         *
         * This method adds a new control for the "Custom CSS" feature. The free
         * version of elementor uses this method to display an upgrade message to
         * Elementor Pro.
         *
         * @since 1.0.0
         * @access public
         *
         * @param Controls_Stack $controls_stack .
         * @param string $tab
         * @param array $additional_messages
         *
         */
        public function add_custom_css_controls(\Elementor\Controls_Stack $controls_stack, $tab = self::TAB_ADVANCED, $additional_messages = [])
        {
        }
        /**
         * Add Page Transitions controls.
         *
         * This method adds a new control for the "Page Transitions" feature. The Core
         * version of elementor uses this method to display an upgrade message to
         * Elementor Pro.
         *
         * @param Controls_Stack $controls_stack .
         * @param string $tab
         * @param array $additional_messages
         *
         * @return void
         */
        public function add_page_transitions_controls(\Elementor\Controls_Stack $controls_stack, $tab = self::TAB_ADVANCED, $additional_messages = [])
        {
        }
        public function get_teaser_template($texts)
        {
        }
        /**
         * Get Responsive Control Device Suffix
         *
         * @param array $control
         * @return string $device suffix
         */
        public static function get_responsive_control_device_suffix(array $control) : string
        {
        }
        /**
         * Add custom attributes controls.
         *
         * This method adds a new control for the "Custom Attributes" feature. The free
         * version of elementor uses this method to display an upgrade message to
         * Elementor Pro.
         *
         * @since 2.8.3
         * @access public
         *
         * @param Controls_Stack $controls_stack.
         */
        public function add_custom_attributes_controls(\Elementor\Controls_Stack $controls_stack)
        {
        }
    }
    /**
     * Elementor widgets manager.
     *
     * Elementor widgets manager handler class is responsible for registering and
     * initializing all the supported Elementor widgets.
     *
     * @since 1.0.0
     */
    class Widgets_Manager
    {
        /**
         * Widget types.
         *
         * Holds the list of all the widget types.
         *
         * @since 1.0.0
         * @access private
         *
         * @var Widget_Base[]
         */
        private $_widget_types = null;
        /**
         * Init widgets.
         *
         * Initialize Elementor widgets manager. Include all the the widgets files
         * and register each Elementor and WordPress widget.
         *
         * @since 2.0.0
         * @access private
         */
        private function init_widgets()
        {
        }
        /**
         * Register WordPress widgets.
         *
         * Add native WordPress widget to the list of registered widget types.
         *
         * Exclude the widgets that are in Elementor widgets black list. Theme and
         * plugin authors can filter the black list.
         *
         * @since 2.0.0
         * @access private
         */
        private function register_wp_widgets()
        {
        }
        /**
         * Require files.
         *
         * Require Elementor widget base class.
         *
         * @since 2.0.0
         * @access private
         */
        private function require_files()
        {
        }
        private function pluck_default_controls($controls)
        {
        }
        /**
         * Register widget type.
         *
         * Add a new widget type to the list of registered widget types.
         *
         * @since 1.0.0
         * @access public
         * @deprecated 3.5.0 Use `$this->register()` instead.
         *
         * @param Widget_Base $widget Elementor widget.
         *
         * @return true True if the widget was registered.
         */
        public function register_widget_type(\Elementor\Widget_Base $widget)
        {
        }
        /**
         * Register a new widget type.
         *
         * @param \Elementor\Widget_Base $widget_instance Elementor Widget.
         *
         * @return true True if the widget was registered.
         * @since 3.5.0
         * @access public
         *
         */
        public function register(\Elementor\Widget_Base $widget_instance)
        {
        }
        /**
         * Unregister widget type.
         *
         * Removes widget type from the list of registered widget types.
         *
         * @since 1.0.0
         * @access public
         * @deprecated 3.5.0 Use `$this->unregister()` instead.
         *
         * @param string $name Widget name.
         *
         * @return true True if the widget was unregistered, False otherwise.
         */
        public function unregister_widget_type($name)
        {
        }
        /**
         * Unregister widget type.
         *
         * Removes widget type from the list of registered widget types.
         *
         * @since 3.5.0
         * @access public
         *
         * @param string $name Widget name.
         *
         * @return boolean Whether the widget was unregistered.
         */
        public function unregister($name)
        {
        }
        /**
         * Get widget types.
         *
         * Retrieve the registered widget types list.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $widget_name Optional. Widget name. Default is null.
         *
         * @return Widget_Base|Widget_Base[]|null Registered widget types.
         */
        public function get_widget_types($widget_name = null)
        {
        }
        /**
         * Get widget types config.
         *
         * Retrieve all the registered widgets with config for each widgets.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array Registered widget types with each widget config.
         */
        public function get_widget_types_config()
        {
        }
        public function ajax_get_widget_types_controls_config(array $data)
        {
        }
        public function ajax_get_widgets_default_value_translations(array $data = [])
        {
        }
        /**
         * Ajax render widget.
         *
         * Ajax handler for Elementor render_widget.
         *
         * Fired by `wp_ajax_elementor_render_widget` action.
         *
         * @since 1.0.0
         * @access public
         *
         * @throws \Exception If current user don't have permissions to edit the post.
         *
         * @param array $request Ajax request.
         *
         * @return array {
         *     Rendered widget.
         *
         *     @type string $render The rendered HTML.
         * }
         */
        public function ajax_render_widget($request)
        {
        }
        /**
         * Ajax get WordPress widget form.
         *
         * Ajax handler for Elementor editor get_wp_widget_form.
         *
         * Fired by `wp_ajax_elementor_editor_get_wp_widget_form` action.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $request Ajax request.
         *
         * @return bool|string Rendered widget form.
         */
        public function ajax_get_wp_widget_form($request)
        {
        }
        /**
         * Render widgets content.
         *
         * Used to generate the widget templates on the editor using Underscore JS
         * template, for all the registered widget types.
         *
         * @since 1.0.0
         * @access public
         */
        public function render_widgets_content()
        {
        }
        /**
         * Get widgets frontend settings keys.
         *
         * Retrieve frontend controls settings keys for all the registered widget
         * types.
         *
         * @since 1.3.0
         * @access public
         *
         * @return array Registered widget types with settings keys for each widget.
         */
        public function get_widgets_frontend_settings_keys()
        {
        }
        /**
         * Enqueue widgets scripts.
         *
         * Enqueue all the scripts defined as a dependency for each widget.
         *
         * @since 1.3.0
         * @access public
         */
        public function enqueue_widgets_scripts()
        {
        }
        /**
         * Enqueue widgets styles
         *
         * Enqueue all the styles defined as a dependency for each widget
         *
         * @access public
         */
        public function enqueue_widgets_styles()
        {
        }
        /**
         * Retrieve inline editing configuration.
         *
         * Returns general inline editing configurations like toolbar types etc.
         *
         * @access public
         * @since 1.8.0
         *
         * @return array {
         *     Inline editing configuration.
         *
         *     @type array $toolbar {
         *         Toolbar types and the actions each toolbar includes.
         *         Note: Wysiwyg controls uses the advanced toolbar, textarea controls
         *         uses the basic toolbar and text controls has no toolbar.
         *
         *         @type array $basic    Basic actions included in the edit tool.
         *         @type array $advanced Advanced actions included in the edit tool.
         *     }
         * }
         */
        public function get_inline_editing_config()
        {
        }
        /**
         * Widgets manager constructor.
         *
         * Initializing Elementor widgets manager.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Register ajax actions.
         *
         * Add new actions to handle data after an ajax requests returned.
         *
         * @since 2.0.0
         * @access public
         *
         * @param Ajax $ajax_manager
         */
        public function register_ajax_actions(\Elementor\Core\Common\Modules\Ajax\Module $ajax_manager)
        {
        }
    }
    /**
     * Elementor plugin.
     *
     * The main plugin handler class is responsible for initializing Elementor. The
     * class registers and all the components required to run the plugin.
     *
     * @since 1.0.0
     */
    class Plugin
    {
        const ELEMENTOR_DEFAULT_POST_TYPES = ['page', 'post'];
        /**
         * Instance.
         *
         * Holds the plugin instance.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @var Plugin
         */
        public static $instance = null;
        /**
         * Database.
         *
         * Holds the plugin database handler which is responsible for communicating
         * with the database.
         *
         * @since 1.0.0
         * @access public
         *
         * @var DB
         */
        public $db;
        /**
         * Controls manager.
         *
         * Holds the plugin controls manager handler is responsible for registering
         * and initializing controls.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Controls_Manager
         */
        public $controls_manager;
        /**
         * Documents manager.
         *
         * Holds the documents manager.
         *
         * @since 2.0.0
         * @access public
         *
         * @var Documents_Manager
         */
        public $documents;
        /**
         * Schemes manager.
         *
         * Holds the plugin schemes manager.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Schemes_Manager
         */
        public $schemes_manager;
        /**
         * Elements manager.
         *
         * Holds the plugin elements manager.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Elements_Manager
         */
        public $elements_manager;
        /**
         * Widgets manager.
         *
         * Holds the plugin widgets manager which is responsible for registering and
         * initializing widgets.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Widgets_Manager
         */
        public $widgets_manager;
        /**
         * Revisions manager.
         *
         * Holds the plugin revisions manager which handles history and revisions
         * functionality.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Revisions_Manager
         */
        public $revisions_manager;
        /**
         * Images manager.
         *
         * Holds the plugin images manager which is responsible for retrieving image
         * details.
         *
         * @since 2.9.0
         * @access public
         *
         * @var Images_Manager
         */
        public $images_manager;
        /**
         * Maintenance mode.
         *
         * Holds the maintenance mode manager responsible for the "Maintenance Mode"
         * and the "Coming Soon" features.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Maintenance_Mode
         */
        public $maintenance_mode;
        /**
         * Page settings manager.
         *
         * Holds the page settings manager.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Page_Settings_Manager
         */
        public $page_settings_manager;
        /**
         * Dynamic tags manager.
         *
         * Holds the dynamic tags manager.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Dynamic_Tags_Manager
         */
        public $dynamic_tags;
        /**
         * Settings.
         *
         * Holds the plugin settings.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Settings
         */
        public $settings;
        /**
         * Role Manager.
         *
         * Holds the plugin role manager.
         *
         * @since 2.0.0
         * @access public
         *
         * @var Core\RoleManager\Role_Manager
         */
        public $role_manager;
        /**
         * Admin.
         *
         * Holds the plugin admin.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Admin
         */
        public $admin;
        /**
         * Tools.
         *
         * Holds the plugin tools.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Tools
         */
        public $tools;
        /**
         * Preview.
         *
         * Holds the plugin preview.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Preview
         */
        public $preview;
        /**
         * Editor.
         *
         * Holds the plugin editor.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Editor
         */
        public $editor;
        /**
         * Frontend.
         *
         * Holds the plugin frontend.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Frontend
         */
        public $frontend;
        /**
         * Heartbeat.
         *
         * Holds the plugin heartbeat.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Heartbeat
         */
        public $heartbeat;
        /**
         * System info.
         *
         * Holds the system info data.
         *
         * @since 1.0.0
         * @access public
         *
         * @var System_Info_Module
         */
        public $system_info;
        /**
         * Template library manager.
         *
         * Holds the template library manager.
         *
         * @since 1.0.0
         * @access public
         *
         * @var TemplateLibrary\Manager
         */
        public $templates_manager;
        /**
         * Skins manager.
         *
         * Holds the skins manager.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Skins_Manager
         */
        public $skins_manager;
        /**
         * Files manager.
         *
         * Holds the plugin files manager.
         *
         * @since 2.1.0
         * @access public
         *
         * @var Files_Manager
         */
        public $files_manager;
        /**
         * Assets manager.
         *
         * Holds the plugin assets manager.
         *
         * @since 2.6.0
         * @access public
         *
         * @var Assets_Manager
         */
        public $assets_manager;
        /**
         * Icons Manager.
         *
         * Holds the plugin icons manager.
         *
         * @access public
         *
         * @var Icons_Manager
         */
        public $icons_manager;
        /**
         * WordPress widgets manager.
         *
         * Holds the WordPress widgets manager.
         *
         * @since 1.0.0
         * @access public
         *
         * @var WordPress_Widgets_Manager
         */
        public $wordpress_widgets_manager;
        /**
         * Modules manager.
         *
         * Holds the plugin modules manager.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Modules_Manager
         */
        public $modules_manager;
        /**
         * Beta testers.
         *
         * Holds the plugin beta testers.
         *
         * @since 1.0.0
         * @access public
         *
         * @var Beta_Testers
         */
        public $beta_testers;
        /**
         * Inspector.
         *
         * Holds the plugin inspector data.
         *
         * @since 2.1.2
         * @access public
         *
         * @var Inspector
         */
        public $inspector;
        /**
         * @var Admin_Menu_Manager
         */
        public $admin_menu_manager;
        /**
         * Common functionality.
         *
         * Holds the plugin common functionality.
         *
         * @since 2.3.0
         * @access public
         *
         * @var CommonApp
         */
        public $common;
        /**
         * Log manager.
         *
         * Holds the plugin log manager.
         *
         * @access public
         *
         * @var Log_Manager
         */
        public $logger;
        /**
         * Dev tools.
         *
         * Holds the plugin dev tools.
         *
         * @access private
         *
         * @var Dev_Tools
         */
        private $dev_tools;
        /**
         * Upgrade manager.
         *
         * Holds the plugin upgrade manager.
         *
         * @access public
         *
         * @var Core\Upgrade\Manager
         */
        public $upgrade;
        /**
         * Tasks manager.
         *
         * Holds the plugin tasks manager.
         *
         * @var Core\Upgrade\Custom_Tasks_Manager
         */
        public $custom_tasks;
        /**
         * Kits manager.
         *
         * Holds the plugin kits manager.
         *
         * @access public
         *
         * @var Core\Kits\Manager
         */
        public $kits_manager;
        /**
         * @var \Elementor\Data\V2\Manager
         */
        public $data_manager_v2;
        /**
         * Legacy mode.
         *
         * Holds the plugin legacy mode data.
         *
         * @access public
         *
         * @var array
         */
        public $legacy_mode;
        /**
         * App.
         *
         * Holds the plugin app data.
         *
         * @since 3.0.0
         * @access public
         *
         * @var App\App
         */
        public $app;
        /**
         * WordPress API.
         *
         * Holds the methods that interact with WordPress Core API.
         *
         * @since 3.0.0
         * @access public
         *
         * @var Wp_Api
         */
        public $wp;
        /**
         * Experiments manager.
         *
         * Holds the plugin experiments manager.
         *
         * @since 3.1.0
         * @access public
         *
         * @var Experiments_Manager
         */
        public $experiments;
        /**
         * Uploads manager.
         *
         * Holds the plugin uploads manager responsible for handling file uploads
         * that are not done with WordPress Media.
         *
         * @since 3.3.0
         * @access public
         *
         * @var Uploads_Manager
         */
        public $uploads_manager;
        /**
         * Breakpoints manager.
         *
         * Holds the plugin breakpoints manager.
         *
         * @since 3.2.0
         * @access public
         *
         * @var Breakpoints_Manager
         */
        public $breakpoints;
        /**
         * Assets loader.
         *
         * Holds the plugin assets loader responsible for conditionally enqueuing
         * styles and script assets that were pre-enabled.
         *
         * @since 3.3.0
         * @access public
         *
         * @var Assets_Loader
         */
        public $assets_loader;
        /**
         * Clone.
         *
         * Disable class cloning and throw an error on object clone.
         *
         * The whole idea of the singleton design pattern is that there is a single
         * object. Therefore, we don't want the object to be cloned.
         *
         * @access public
         * @since 1.0.0
         */
        public function __clone()
        {
        }
        /**
         * Wakeup.
         *
         * Disable unserializing of the class.
         *
         * @access public
         * @since 1.0.0
         */
        public function __wakeup()
        {
        }
        /**
         * Instance.
         *
         * Ensures only one instance of the plugin class is loaded or can be loaded.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @return Plugin An instance of the class.
         */
        public static function instance()
        {
        }
        /**
         * Init.
         *
         * Initialize Elementor Plugin. Register Elementor support for all the
         * supported post types and initialize Elementor components.
         *
         * @since 1.0.0
         * @access public
         */
        public function init()
        {
        }
        /**
         * Get install time.
         *
         * Retrieve the time when Elementor was installed.
         *
         * @since 2.6.0
         * @access public
         * @static
         *
         * @return int Unix timestamp when Elementor was installed.
         */
        public function get_install_time()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function on_rest_api_init()
        {
        }
        /**
         * Init components.
         *
         * Initialize Elementor components. Register actions, run setting manager,
         * initialize all the components that run elementor, and if in admin page
         * initialize admin components.
         *
         * @since 1.0.0
         * @access private
         */
        private function init_components()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function init_common()
        {
        }
        /**
         * Get Legacy Mode
         *
         * @since 3.0.0
         * @deprecated 3.1.0 Use `Plugin::$instance->experiments->is_feature_active()` instead
         *
         * @param string $mode_name Optional. Default is null
         *
         * @return bool|bool[]
         */
        public function get_legacy_mode($mode_name = null)
        {
        }
        /**
         * Add custom post type support.
         *
         * Register Elementor support for all the supported post types defined by
         * the user in the admin screen and saved as `elementor_cpt_support` option
         * in WordPress `$wpdb->options` table.
         *
         * If no custom post type selected, usually in new installs, this method
         * will return the two default post types: `page` and `post`.
         *
         * @since 1.0.0
         * @access private
         */
        private function add_cpt_support()
        {
        }
        /**
         * Register autoloader.
         *
         * Elementor autoloader loads all the classes needed to run the plugin.
         *
         * @since 1.6.0
         * @access private
         */
        private function register_autoloader()
        {
        }
        /**
         * Plugin Magic Getter
         *
         * @since 3.1.0
         * @access public
         *
         * @param $property
         * @return mixed
         * @throws \Exception
         */
        public function __get($property)
        {
        }
        /**
         * Plugin constructor.
         *
         * Initializing Elementor plugin.
         *
         * @since 1.0.0
         * @access private
         */
        private function __construct()
        {
        }
        public static final function get_title()
        {
        }
    }
}
