<?php

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
         * Get widget number.
         *
         * Get the first three numbers of the element converted ID.
         *
         * @since 3.16
         * @access public
         *
         * @return string The widget number.
         */
        public function get_widget_number() : string
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
         * @deprecated 3.7.6 Use `Elementor\Controls_Manager::get_responsive_control_device_suffix()` instead.
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
        public function is_control_visible($control, $values = null, $controls = null)
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
         * @deprecated 3.1.0 Use `register_controls()` method instead.
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
         * @deprecated 2.9.0 Use `get_initial_config()` method instead.
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
         * @deprecated 2.9.0 Use `content_template()` method instead.
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
         * @deprecated 2.9.0 Use `init()` method instead.
         * @access protected
         *
         * @param array $data Initial data.
         */
        protected function _init($data)
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
namespace Elementor\Core\Settings\Base {
    /**
     * Elementor settings base model.
     *
     * Elementor settings base model handler class is responsible for registering
     * and managing Elementor settings base models.
     *
     * @since 1.6.0
     * @abstract
     */
    abstract class Model extends \Elementor\Controls_Stack
    {
        /**
         * Get panel page settings.
         *
         * Retrieve the page setting for the current panel.
         *
         * @since 1.6.0
         * @access public
         * @abstract
         */
        public abstract function get_panel_page_settings();
    }
    abstract class CSS_Model extends \Elementor\Core\Settings\Base\Model
    {
        /**
         * Get CSS wrapper selector.
         *
         * Retrieve the wrapper selector for the current panel.
         *
         * @since 1.6.0
         * @access public
         * @abstract
         */
        public abstract function get_css_wrapper_selector();
    }
}
namespace Elementor\Core\Settings\Page {
    /**
     * Elementor page settings model.
     *
     * Elementor page settings model handler class is responsible for registering
     * and managing Elementor page settings models.
     *
     * @since 1.6.0
     */
    class Model extends \Elementor\Core\Settings\Base\CSS_Model
    {
        /**
         * Model constructor.
         *
         * Initializing Elementor page settings model.
         *
         * @since 1.6.0
         * @access public
         *
         * @param array $data Optional. Model data. Default is an empty array.
         */
        public function __construct(array $data = [])
        {
        }
        /**
         * Get model name.
         *
         * Retrieve page settings model name.
         *
         * @since 1.6.0
         * @access public
         *
         * @return string Model name.
         */
        public function get_name()
        {
        }
        /**
         * Get model unique name.
         *
         * Retrieve page settings model unique name.
         *
         * @since 1.6.0
         * @access public
         *
         * @return string Model unique name.
         */
        public function get_unique_name()
        {
        }
        /**
         * Get CSS wrapper selector.
         *
         * Retrieve the wrapper selector for the page settings model.
         *
         * @since 1.6.0
         * @access public
         *
         * @return string CSS wrapper selector.
         */
        public function get_css_wrapper_selector()
        {
        }
        /**
         * Get panel page settings.
         *
         * Retrieve the panel setting for the page settings model.
         *
         * @since 1.6.0
         * @access public
         *
         * @return array {
         *    Panel settings.
         *
         *    @type string $title The panel title.
         * }
         */
        public function get_panel_page_settings()
        {
        }
        /**
         * On export post meta.
         *
         * When exporting data, check if the post is not using page template and
         * exclude it from the exported Elementor data.
         *
         * @since 1.6.0
         * @access public
         *
         * @param array $element_data Element data.
         *
         * @return array Element data to be exported.
         */
        public function on_export($element_data)
        {
        }
        /**
         * Register model controls.
         *
         * Used to add new controls to the page settings model.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
    }
}
namespace Elementor\Core\Settings\Base {
    /**
     * Elementor settings base manager.
     *
     * Elementor settings base manager handler class is responsible for registering
     * and managing Elementor settings base managers.
     *
     * @since 1.6.0
     * @abstract
     */
    abstract class Manager
    {
        /**
         * Settings base manager constructor.
         *
         * Initializing Elementor settings base manager.
         *
         * @since 1.6.0
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
         * Fired by `elementor/ajax/register_actions` action.
         *
         * @since 2.0.0
         * @access public
         *
         * @param Ajax $ajax_manager
         */
        public function register_ajax_actions($ajax_manager)
        {
        }
        /**
         * Get model for config.
         *
         * Retrieve the model for settings configuration.
         *
         * @since 1.6.0
         * @access public
         * @abstract
         *
         * @return Model The model object.
         */
        public abstract function get_model_for_config();
        /**
         * Get manager name.
         *
         * Retrieve settings manager name.
         *
         * @since 1.6.0
         * @access public
         * @abstract
         */
        public abstract function get_name();
        /**
         * Get model.
         *
         * Retrieve the model for any given model ID.
         *
         * @since 1.6.0
         * @access public
         *
         * @param int $id Optional. Model ID. Default is `0`.
         *
         * @return Model The model.
         */
        public final function get_model($id = 0)
        {
        }
        /**
         * Ajax request to save settings.
         *
         * Save settings using an ajax request.
         *
         * @since 1.6.0
         * @access public
         *
         * @param array $request Ajax request.
         *
         * @return array Ajax response data.
         */
        public final function ajax_save_settings($request)
        {
        }
        /**
         * Save settings.
         *
         * Save settings to the database.
         *
         * @since 1.6.0
         * @access public
         *
         * @param array $settings Settings.
         * @param int   $id       Optional. Post ID. Default is `0`.
         */
        public function save_settings(array $settings, $id = 0)
        {
        }
        /**
         * On Elementor init.
         *
         * Add editor template for the settings
         *
         * Fired by `elementor/init` action.
         *
         * @since 2.3.0
         * @access public
         */
        public function on_elementor_editor_init()
        {
        }
        /**
         * Get saved settings.
         *
         * Retrieve the saved settings from the database.
         *
         * @since 1.6.0
         * @access protected
         * @abstract
         *
         * @param int $id Post ID.
         */
        protected abstract function get_saved_settings($id);
        /**
         * Save settings to DB.
         *
         * Save settings to the database.
         *
         * @since 1.6.0
         * @access protected
         * @abstract
         *
         * @param array $settings Settings.
         * @param int   $id       Post ID.
         */
        protected abstract function save_settings_to_db(array $settings, $id);
        /**
         * Get special settings names.
         *
         * Retrieve the names of the special settings that are not saved as regular
         * settings. Those settings have a separate saving process.
         *
         * @since 1.6.0
         * @access protected
         *
         * @return array Special settings names.
         */
        protected function get_special_settings_names()
        {
        }
        /**
         * Ajax before saving settings.
         *
         * Validate the data before saving it and updating the data in the database.
         *
         * @since 1.6.0
         * @access public
         *
         * @param array $data Post data.
         * @param int   $id   Post ID.
         */
        public function ajax_before_save_settings(array $data, $id)
        {
        }
        /**
         * Print the setting template content in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.6.0
         * @access protected
         *
         * @param string $name Settings panel name.
         */
        protected function print_editor_template_content($name)
        {
        }
    }
    abstract class CSS_Manager extends \Elementor\Core\Settings\Base\Manager
    {
        /**
         * Get CSS file name.
         *
         * Retrieve CSS file name for the settings base css manager.
         *
         * @since 2.8.0
         * @access protected
         * @abstract
         *
         * @return string CSS file name
         */
        protected abstract function get_css_file_name();
        /**
         * Get model for CSS file.
         *
         * Retrieve the model for the CSS file.
         *
         * @since 2.8.0
         * @access protected
         * @abstract
         *
         * @param CSS_File $css_file The requested CSS file.
         *
         * @return CSS_Model
         *
         */
        protected abstract function get_model_for_css_file(\Elementor\Core\Files\CSS\Base $css_file);
        /**
         * Get CSS file for update.
         *
         * Retrieve the CSS file before updating it.
         *
         * @since 2.8.0
         * @access protected
         * @abstract
         *
         * @param int $id Post ID.
         *
         * @return CSS_File
         *
         */
        protected abstract function get_css_file_for_update($id);
        /**
         * Settings base manager constructor.
         *
         * Initializing Elementor settings base css manager.
         *
         * @since 2.8.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Save settings.
         *
         * Save settings to the database and update the CSS file.
         *
         * @since 2.8.0
         * @access public
         *
         * @param array $settings Settings.
         * @param int   $id       Optional. Post ID. Default is `0`.
         */
        public function save_settings(array $settings, $id = 0)
        {
        }
        /**
         * Add settings CSS rules.
         *
         * Add new CSS rules to the settings manager.
         *
         * Fired by `elementor/css-file/{$name}/parse` action.
         *
         * @since 2.8.0
         * @access public
         *
         * @param CSS_File $css_file The requested CSS file.
         *
         */
        public function add_settings_css_rules(\Elementor\Core\Files\CSS\Base $css_file)
        {
        }
    }
}
namespace Elementor\Core\Settings\Page {
    /**
     * Elementor page settings manager.
     *
     * Elementor page settings manager handler class is responsible for registering
     * and managing Elementor page settings managers.
     *
     * @since 1.6.0
     */
    class Manager extends \Elementor\Core\Settings\Base\CSS_Manager
    {
        /**
         * Meta key for the page settings.
         */
        const META_KEY = '_elementor_page_settings';
        /**
         * Get manager name.
         *
         * Retrieve page settings manager name.
         *
         * @since 1.6.0
         * @access public
         *
         * @return string Manager name.
         */
        public function get_name()
        {
        }
        /**
         * Get model for config.
         *
         * Retrieve the model for settings configuration.
         *
         * @since 1.6.0
         * @access public
         *
         * @return BaseModel The model object.
         */
        public function get_model_for_config()
        {
        }
        /**
         * Ajax before saving settings.
         *
         * Validate the data before saving it and updating the data in the database.
         *
         * @since 1.6.0
         * @access public
         *
         * @param array $data Post data.
         * @param int   $id   Post ID.
         *
         * @throws \Exception If invalid post returned using the `$id`.
         * @throws \Exception If current user don't have permissions to edit the post.
         */
        public function ajax_before_save_settings(array $data, $id)
        {
        }
        /**
         * @inheritDoc
         *
         * Override parent because the page setting moved to document.settings.
         */
        protected function print_editor_template_content($name)
        {
        }
        /**
         * Save settings to DB.
         *
         * Save page settings to the database, as post meta data.
         *
         * @since 1.6.0
         * @access protected
         *
         * @param array $settings Settings.
         * @param int   $id       Post ID.
         */
        protected function save_settings_to_db(array $settings, $id)
        {
        }
        /**
         * Get CSS file for update.
         *
         * Retrieve the CSS file before updating it.
         *
         * This method overrides the parent method to disallow updating CSS files for pages.
         *
         * @since 1.6.0
         * @access protected
         *
         * @param int $id Post ID.
         *
         * @return false Disallow The updating CSS files for pages.
         */
        protected function get_css_file_for_update($id)
        {
        }
        /**
         * Get saved settings.
         *
         * Retrieve the saved settings from the post meta.
         *
         * @since 1.6.0
         * @access protected
         *
         * @param int $id Post ID.
         *
         * @return array Saved settings.
         */
        protected function get_saved_settings($id)
        {
        }
        /**
         * Get CSS file name.
         *
         * Retrieve CSS file name for the page settings manager.
         *
         * @since 1.6.0
         * @access protected
         *
         * @return string CSS file name.
         */
        protected function get_css_file_name()
        {
        }
        /**
         * Get model for CSS file.
         *
         * Retrieve the model for the CSS file.
         *
         * @since 1.6.0
         * @access protected
         *
         * @param Base $css_file The requested CSS file.
         *
         * @return BaseModel The model object.
         */
        protected function get_model_for_css_file(\Elementor\Core\Files\CSS\Base $css_file)
        {
        }
        /**
         * Get special settings names.
         *
         * Retrieve the names of the special settings that are not saved as regular
         * settings. Those settings have a separate saving process.
         *
         * @since 1.6.0
         * @access protected
         *
         * @return array Special settings names.
         */
        protected function get_special_settings_names()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param $post_id
         * @param $status
         */
        public function save_post_status($post_id, $status)
        {
        }
    }
}
namespace Elementor\Core\Settings\General {
    /**
     * This file is deprecated, use Plugin::$instance->kits_manager->get_active_kit_for_frontend() instead.
     * it changed to support call like this: Manager::get_settings_managers( 'general' )->get_model()->get_settings( 'elementor_default_generic_fonts' )
     *
     * @deprecated 3.0.0 Use `Plugin::$instance->kits_manager->get_active_kit_for_frontend()` instead.
     */
    class Model
    {
        /**
         * @deprecated 3.0.0
         */
        public function get_name()
        {
        }
        /**
         * @deprecated 3.0.0
         */
        public function get_panel_page_settings()
        {
        }
        /**
         * @deprecated 3.0.0
         */
        public function get_tabs_controls()
        {
        }
        /**
         * @deprecated 3.0.0
         */
        public function get_frontend_settings()
        {
        }
        /**
         * @deprecated 3.0.0
         */
        public function get_controls()
        {
        }
        /**
         * @deprecated 3.0.0
         */
        public function get_settings($setting = null)
        {
        }
    }
    /**
     * This class is deprecated, use Plugin::$instance->kits_manager->get_active_kit_for_frontend() instead.
     * it changed to support call like this: Manager::get_settings_managers( 'general' )->get_model()->get_settings( 'elementor_default_generic_fonts' )
     *
     * @deprecated 3.0.0 Use `Plugin::$instance->kits_manager->get_active_kit_for_frontend()` instead.
     */
    class Manager extends \Elementor\Core\Settings\Base\CSS_Manager
    {
        /**
         * Meta key for the general settings.
         *
         * @deprecated 3.0.0
         */
        const META_KEY = '_elementor_general_settings';
        /**
         * General settings manager constructor.
         *
         * Initializing Elementor general settings manager.
         *
         * @since 1.6.0
         * @deprecated 3.0.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Get manager name.
         *
         * Retrieve general settings manager name.
         *
         * @since 1.6.0
         * @deprecated 3.0.0
         * @access public
         *
         * @return string Manager name.
         */
        public function get_name()
        {
        }
        /**
         * Get model for config.
         *
         * Retrieve the model for settings configuration.
         *
         * @since 1.6.0
         * @deprecated 3.0.0
         * @access public
         *
         * @return BaseModel The model object.
         */
        public function get_model_for_config()
        {
        }
        /**
         * @deprecated 3.0.0
         */
        protected function get_saved_settings($id)
        {
        }
        /**
         * Get CSS file name.
         *
         * Retrieve CSS file name for the general settings manager.
         *
         * @since 1.6.0
         * @deprecated 3.0.0
         * @access protected
         * @return string
         *
         * @return string CSS file name.
         */
        protected function get_css_file_name()
        {
        }
        /**
         * @deprecated 3.0.0
         */
        protected function save_settings_to_db(array $settings, $id)
        {
        }
        /**
         * @deprecated 3.0.0
         */
        protected function get_model_for_css_file(\Elementor\Core\Files\CSS\Base $css_file)
        {
        }
        /**
         * @deprecated 3.0.0
         */
        protected function get_css_file_for_update($id)
        {
        }
    }
}
namespace Elementor\Core\Settings\EditorPreferences {
    class Model extends \Elementor\Core\Settings\Base\Model
    {
        /**
         * Get element name.
         *
         * Retrieve the element name.
         *
         * @return string The name.
         * @since 2.8.0
         * @access public
         *
         */
        public function get_name()
        {
        }
        /**
         * Get panel page settings.
         *
         * Retrieve the page setting for the current panel.
         *
         * @since 2.8.0
         * @access public
         */
        public function get_panel_page_settings()
        {
        }
        /**
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
    }
    class Manager extends \Elementor\Core\Settings\Base\Manager
    {
        const META_KEY = 'elementor_preferences';
        /**
         * Get model for config.
         *
         * Retrieve the model for settings configuration.
         *
         * @since 2.8.0
         * @access public
         *
         * @return BaseModel The model object.
         *
         */
        public function get_model_for_config()
        {
        }
        /**
         * Get manager name.
         *
         * Retrieve settings manager name.
         *
         * @since 2.8.0
         * @access public
         */
        public function get_name()
        {
        }
        /**
         * Get saved settings.
         *
         * Retrieve the saved settings from the database.
         *
         * @since 2.8.0
         * @access protected
         *
         * @param int $id.
         * @return array
         *
         */
        protected function get_saved_settings($id)
        {
        }
        /**
         * Save settings to DB.
         *
         * Save settings to the database.
         *
         * @param array $settings Settings.
         * @param int $id Post ID.
         * @since 2.8.0
         * @access protected
         *
         */
        protected function save_settings_to_db(array $settings, $id)
        {
        }
    }
}
namespace Elementor\Core\Settings {
    /**
     * Elementor settings manager.
     *
     * Elementor settings manager handler class is responsible for registering and
     * managing Elementor settings managers.
     *
     * @since 1.6.0
     */
    class Manager
    {
        /**
         * Add settings manager.
         *
         * Register a single settings manager to the registered settings managers.
         *
         * @since 1.6.0
         * @access public
         * @static
         *
         * @param Base\Manager $manager Settings manager.
         */
        public static function add_settings_manager(\Elementor\Core\Settings\Base\Manager $manager)
        {
        }
        /**
         * Get settings managers.
         *
         * Retrieve registered settings manager(s).
         *
         * If no parameter passed, it will retrieve all the settings managers. For
         * any given parameter it will retrieve a single settings manager if one
         * exist, or `null` otherwise.
         *
         * @since 1.6.0
         * @access public
         * @static
         *
         * @param string $manager_name Optional. Settings manager name. Default is
         *                             null.
         *
         * @return Base\Manager|Base\Manager[] Single settings manager, if it exists,
         *                                     null if it doesn't exists, or the all
         *                                     the settings managers if no parameter
         *                                     defined.
         */
        public static function get_settings_managers($manager_name = null)
        {
        }
        /**
         * Get settings managers config.
         *
         * Retrieve the settings managers configuration.
         *
         * @since 1.6.0
         * @access public
         * @static
         *
         * @return array The settings managers configuration.
         */
        public static function get_settings_managers_config()
        {
        }
        /**
         * Get settings frontend config.
         *
         * Retrieve the settings managers frontend configuration.
         *
         * @since 1.6.0
         * @access public
         * @static
         *
         * @return array The settings managers frontend configuration.
         */
        public static function get_settings_frontend_config()
        {
        }
        /**
         * Run settings managers.
         *
         * Register builtin Elementor settings managers.
         *
         * @since 1.6.0
         * @access public
         * @static
         */
        public static function run()
        {
        }
    }
}
namespace Elementor\Core\Files {
    abstract class Base
    {
        const UPLOADS_DIR = 'elementor/';
        const DEFAULT_FILES_DIR = 'css/';
        const META_KEY = '';
        /**
         * @since 2.1.0
         * @access public
         * @static
         */
        public static function get_base_uploads_dir()
        {
        }
        /**
         * @since 2.1.0
         * @access public
         * @static
         */
        public static function get_base_uploads_url()
        {
        }
        /**
         * Use a create function for PhpDoc (@return static).
         *
         * @return static
         */
        public static function create()
        {
        }
        /**
         * @since 2.1.0
         * @access public
         */
        public function __construct($file_name)
        {
        }
        /**
         * @since 2.1.0
         * @access public
         */
        public function set_files_dir($files_dir)
        {
        }
        /**
         * @since 2.1.0
         * @access public
         */
        public function set_file_name($file_name)
        {
        }
        /**
         * @since 2.1.0
         * @access public
         */
        public function get_file_name()
        {
        }
        /**
         * @since 2.1.0
         * @access public
         */
        public function get_url()
        {
        }
        /**
         * Get Path
         *
         * Returns the local path of the generated file.
         *
         * @since 3.5.0
         * @access public
         *
         * @return string
         */
        public function get_path()
        {
        }
        /**
         * @since 2.1.0
         * @access public
         */
        public function get_content()
        {
        }
        /**
         * @since 2.1.0
         * @access public
         */
        public function update()
        {
        }
        /**
         * @since 2.1.0
         * @access public
         */
        public function update_file()
        {
        }
        /**
         * @since 2.1.0
         * @access public
         */
        public function write()
        {
        }
        /**
         * @since 2.1.0
         * @access public
         */
        public function delete()
        {
        }
        /**
         * Get meta data.
         *
         * Retrieve the CSS file meta data. Returns an array of all the data, or if
         * custom property is given it will return the property value, or `null` if
         * the property does not exist.
         *
         * @since 2.1.0
         * @access public
         *
         * @param string $property Optional. Custom meta data property. Default is
         *                         null.
         *
         * @return array|null An array of all the data, or if custom property is
         *                    given it will return the property value, or `null` if
         *                    the property does not exist.
         */
        public function get_meta($property = null)
        {
        }
        /**
         * @since 2.1.0
         * @access protected
         * @abstract
         */
        protected abstract function parse_content();
        /**
         * Load meta.
         *
         * Retrieve the file meta data.
         *
         * @since 2.1.0
         * @access protected
         */
        protected function load_meta()
        {
        }
        /**
         * Update meta.
         *
         * Update the file meta data.
         *
         * @since 2.1.0
         * @access protected
         *
         * @param array $meta New meta data.
         */
        protected function update_meta($meta)
        {
        }
        /**
         * Delete meta.
         *
         * Delete the file meta data.
         *
         * @since 2.1.0
         * @access protected
         */
        protected function delete_meta()
        {
        }
        /**
         * @since 2.1.0
         * @access protected
         */
        protected function get_default_meta()
        {
        }
    }
}
namespace Elementor\Core\Responsive\Files {
    class Frontend extends \Elementor\Core\Files\Base
    {
        const META_KEY = 'elementor-custom-breakpoints-files';
        /**
         * @since 2.1.0
         * @access public
         */
        public function __construct($file_name, $template_file = null)
        {
        }
        /**
         * @since 2.1.0
         * @access public
         */
        public function parse_content()
        {
        }
        /**
         * Load meta.
         *
         * Retrieve the file meta data.
         *
         * @since 2.1.0
         * @access protected
         */
        protected function load_meta()
        {
        }
        /**
         * Update meta.
         *
         * Update the file meta data.
         *
         * @since 2.1.0
         * @access protected
         *
         * @param array $meta New meta data.
         */
        protected function update_meta($meta)
        {
        }
        /**
         * Delete meta.
         *
         * Delete the file meta data.
         *
         * @since 2.1.0
         * @access protected
         */
        protected function delete_meta()
        {
        }
    }
}
namespace Elementor\Core\Responsive {
    /**
     * Elementor responsive.
     *
     * Elementor responsive handler class is responsible for setting up Elementor
     * responsive breakpoints.
     *
     * @since 1.0.0
     * @deprecated 3.2.0
     */
    class Responsive
    {
        /**
         * The Elementor breakpoint prefix.
         *
         * @deprecated 3.2.0
         */
        const BREAKPOINT_OPTION_PREFIX = 'viewport_';
        /**
         * Get default breakpoints.
         *
         * Retrieve the default responsive breakpoints.
         *
         * @since 1.0.0
         * @deprecated 3.2.0 Use `Elementor\Core\Breakpoints\Manager::get_default_config()` instead.
         * @access public
         * @static
         *
         * @return array Default breakpoints.
         */
        public static function get_default_breakpoints()
        {
        }
        /**
         * Get editable breakpoints.
         *
         * Retrieve the editable breakpoints.
         *
         * @since 1.0.0
         * @deprecated 3.2.0
         * @access public
         * @static
         *
         * @return array Editable breakpoints.
         */
        public static function get_editable_breakpoints()
        {
        }
        /**
         * Get breakpoints.
         *
         * Retrieve the responsive breakpoints.
         *
         * @since 1.0.0
         * @deprecated 3.2.0
         * @access public
         * @static
         *
         * @return array Responsive breakpoints.
         */
        public static function get_breakpoints()
        {
        }
        /**
         * @since 2.1.0
         * @deprecated 3.2.0 Use `Plugin::$instance->breakpoints->has_custom_breakpoints()` instead.
         * @access public
         * @static
         */
        public static function has_custom_breakpoints()
        {
        }
        /**
         * @since 2.1.0
         * @deprecated 3.2.0 Use `Elementor\Core\Breakpoints\Manager::get_stylesheet_templates_path()` instead.
         * @access public
         * @static
         */
        public static function get_stylesheet_templates_path()
        {
        }
        /**
         * @since 2.1.0
         * @deprecated 3.2.0 Use `Elementor\Core\Breakpoints\Manager::compile_stylesheet_templates()` instead.
         * @access public
         * @static
         */
        public static function compile_stylesheet_templates()
        {
        }
    }
}
namespace Elementor\Core\Base {
    /**
     * Elementor module.
     *
     * An abstract class that provides the needed properties and methods to
     * manage and handle modules in inheriting classes.
     *
     * @since 1.7.0
     * @abstract
     */
    abstract class Module extends \Elementor\Core\Base\Base_Object
    {
        /**
         * Module instance.
         *
         * Holds the module instance.
         *
         * @since 1.7.0
         * @access protected
         *
         * @var Module
         */
        protected static $_instances = [];
        /**
         * Get module name.
         *
         * Retrieve the module name.
         *
         * @since 1.7.0
         * @access public
         * @abstract
         *
         * @return string Module name.
         */
        public abstract function get_name();
        /**
         * Instance.
         *
         * Ensures only one instance of the module class is loaded or can be loaded.
         *
         * @since 1.7.0
         * @access public
         * @static
         *
         * @return $this An instance of the class.
         */
        public static function instance()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         * @static
         */
        public static function is_active()
        {
        }
        /**
         * Class name.
         *
         * Retrieve the name of the class.
         *
         * @since 1.7.0
         * @access public
         * @static
         */
        public static function class_name()
        {
        }
        public static function get_experimental_data()
        {
        }
        /**
         * Clone.
         *
         * Disable class cloning and throw an error on object clone.
         *
         * The whole idea of the singleton design pattern is that there is a single
         * object. Therefore, we don't want the object to be cloned.
         *
         * @since 1.7.0
         * @access public
         */
        public function __clone()
        {
        }
        /**
         * Wakeup.
         *
         * Disable unserializing of the class.
         *
         * @since 1.7.0
         * @access public
         */
        public function __wakeup()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_reflection()
        {
        }
        /**
         * Add module component.
         *
         * Add new component to the current module.
         *
         * @since 1.7.0
         * @access public
         *
         * @param string $id       Component ID.
         * @param mixed  $instance An instance of the component.
         */
        public function add_component($id, $instance)
        {
        }
        /**
         * @since 2.3.0
         * @access public
         * @return Module[]
         */
        public function get_components()
        {
        }
        /**
         * Get module component.
         *
         * Retrieve the module component.
         *
         * @since 1.7.0
         * @access public
         *
         * @param string $id Component ID.
         *
         * @return mixed An instance of the component, or `false` if the component
         *               doesn't exist.
         */
        public function get_component($id)
        {
        }
        /**
         * Get assets url.
         *
         * @since 2.3.0
         * @access protected
         *
         * @param string $file_name
         * @param string $file_extension
         * @param string $relative_url Optional. Default is null.
         * @param string $add_min_suffix Optional. Default is 'default'.
         *
         * @return string
         */
        protected final function get_assets_url($file_name, $file_extension, $relative_url = null, $add_min_suffix = 'default')
        {
        }
        /**
         * Get js assets url
         *
         * @since 2.3.0
         * @access protected
         *
         * @param string $file_name
         * @param string $relative_url Optional. Default is null.
         * @param string $add_min_suffix Optional. Default is 'default'.
         *
         * @return string
         */
        protected final function get_js_assets_url($file_name, $relative_url = null, $add_min_suffix = 'default')
        {
        }
        /**
         * Get css assets url
         *
         * @since 2.3.0
         * @access protected
         *
         * @param string $file_name
         * @param string $relative_url         Optional. Default is null.
         * @param string $add_min_suffix       Optional. Default is 'default'.
         * @param bool   $add_direction_suffix Optional. Default is `false`
         *
         * @return string
         */
        protected final function get_css_assets_url($file_name, $relative_url = null, $add_min_suffix = 'default', $add_direction_suffix = false)
        {
        }
        /**
         * Get Frontend File URL
         *
         * Returns the URL for the CSS file to be loaded in the front end. If requested via the second parameter, a custom
         * file is generated based on a passed template file name. Otherwise, the URL for the default CSS file is returned.
         *
         * @since 3.24.0
         *
         * @access public
         *
         * @param string $file_name
         * @param boolean $has_custom_breakpoints
         *
         * @return string frontend file URL
         */
        public function get_frontend_file_url($file_name, $has_custom_breakpoints)
        {
        }
        /**
         * Get assets base url
         *
         * @since 2.6.0
         * @access protected
         *
         * @return string
         */
        protected function get_assets_base_url()
        {
        }
        /**
         * Get assets relative url
         *
         * @since 2.3.0
         * @access protected
         *
         * @return string
         */
        protected function get_assets_relative_url()
        {
        }
        /**
         * Get the module's associated widgets.
         *
         * @return string[]
         */
        protected function get_widgets()
        {
        }
        /**
         * Initialize the module related widgets.
         */
        public function init_widgets()
        {
        }
        public function __construct()
        {
        }
    }
    abstract class Background_Task_Manager extends \Elementor\Core\Base\Module
    {
        /**
         * @var Background_Task
         */
        protected $task_runner;
        public abstract function get_action();
        public abstract function get_plugin_name();
        public abstract function get_plugin_label();
        public abstract function get_task_runner_class();
        public abstract function get_query_limit();
        protected abstract function start_run();
        public function on_runner_start()
        {
        }
        public function on_runner_complete($did_tasks = false)
        {
        }
        public function get_task_runner()
        {
        }
        // TODO: Replace with a db settings system.
        protected function add_flag($flag)
        {
        }
        protected function get_flag($flag)
        {
        }
        protected function delete_flag($flag)
        {
        }
        protected function get_start_action_url()
        {
        }
        protected function get_continue_action_url()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace Elementor\Core\Upgrade {
    class Custom_Tasks_Manager extends \Elementor\Core\Base\Background_Task_Manager
    {
        const TASKS_OPTION_KEY = 'elementor_custom_tasks';
        const QUERY_LIMIT = 100;
        public function get_name()
        {
        }
        public function get_action()
        {
        }
        public function get_plugin_name()
        {
        }
        public function get_plugin_label()
        {
        }
        public function get_task_runner_class()
        {
        }
        public function get_query_limit()
        {
        }
        protected function start_run()
        {
        }
        public function get_tasks_class()
        {
        }
        public function get_tasks_requested_to_run()
        {
        }
        public function clear_tasks_requested_to_run()
        {
        }
        public function add_tasks_requested_to_run($tasks = [])
        {
        }
        public function __construct()
        {
        }
    }
    /**
     * Elementor upgrades.
     *
     * Elementor upgrades handler class is responsible for updating different
     * Elementor versions.
     *
     * @since 1.0.0
     */
    class Upgrades
    {
        public static function _on_each_version($updater)
        {
        }
        /**
         * Upgrade Elementor 0.3.2
         *
         * Change the image widget link URL, setting is to `custom` link.
         *
         * @since 2.0.0
         * @static
         * @access public
         */
        public static function _v_0_3_2()
        {
        }
        /**
         * Upgrade Elementor 0.9.2
         *
         * Change the icon widget, icon-box widget and the social-icons widget,
         * setting their icon padding size to an empty string.
         *
         * Change the image widget, setting the image size to full image size.
         *
         * @since 2.0.0
         * @static
         * @access public
         */
        public static function _v_0_9_2()
        {
        }
        /**
         * Upgrade Elementor 0.11.0
         *
         * Change the button widget sizes, setting up new button sizes.
         *
         * @since 2.0.0
         * @static
         * @access public
         */
        public static function _v_0_11_0()
        {
        }
        /**
         * Upgrade Elementor 2.0.0
         *
         * Fix post titles for old autosave drafts that saved with the format 'Auto Save 2018-03-18 17:24'.
         *
         * @static
         * @since 2.0.0
         * @access public
         */
        public static function _v_2_0_0()
        {
        }
        /**
         * Upgrade Elementor 2.0.1
         *
         * Fix post titles for old autosave drafts that saved with the format 'Auto Save...'.
         *
         * @since 2.0.2
         * @static
         * @access public
         */
        public static function _v_2_0_1()
        {
        }
        /**
         * Upgrade Elementor 2.0.10
         *
         * Fix post titles for old autosave drafts that saved with the format 'Auto Save...'.
         * Fix also Translated titles.
         *
         * @since 2.0.10
         * @static
         * @access public
         */
        public static function _v_2_0_10()
        {
        }
        public static function _v_2_1_0()
        {
        }
        /**
         * @param Updater $updater
         *
         * @return bool
         */
        public static function _v_2_3_0_widget_image($updater)
        {
        }
        /**
         * @param Updater $updater
         *
         * @return bool
         */
        public static function _v_2_3_0_template_type($updater)
        {
        }
        /**
         * Set FontAwesome Migration needed flag
         */
        public static function _v_2_6_0_fa4_migration_flag()
        {
        }
        /**
         * migrate Icon control string value to Icons control array value
         *
         * @param array $element
         * @param array $args
         *
         * @return mixed
         */
        public static function _migrate_icon_fa4_value($element, $args)
        {
        }
        /**
         * Set FontAwesome 5 value Migration on for button widget
         *
         * @param Updater $updater
         */
        public static function _v_2_6_6_fa4_migration_button($updater)
        {
        }
        /**
         *  Update database to separate page from post.
         *
         * @param Updater $updater
         *
         * @param string $type
         *
         * @return bool
         */
        public static function rename_document_base_to_wp($updater, $type)
        {
        }
        /**
         *  Update database to separate page from post.
         *
         * @param Updater $updater
         *
         * @return bool
         */
        // Because the query is slow on large sites, temporary don't upgrade.
        /*  public static function _v_2_7_0_rename_document_types_to_wp( $updater ) {
                return self::rename_document_base_to_wp( $updater, 'post' ) || self::rename_document_base_to_wp( $updater, 'page' );
            }*/
        // Upgrade code was fixed & moved to _v_2_7_1_remove_old_usage_data.
        /* public static function _v_2_7_0_remove_old_usage_data() {} */
        // Upgrade code moved to _v_2_7_1_recalc_usage_data.
        /* public static function _v_2_7_0_recalc_usage_data( $updater ) {} */
        /**
         * Don't use the old data anymore.
         * Since 2.7.1 the key was changed from `elementor_elements_usage` to `elementor_controls_usage`.
         */
        public static function _v_2_7_1_remove_old_usage_data()
        {
        }
        /**
         * Recalc usage.
         *
         * @param Updater $updater
         *
         * @return bool
         */
        public static function recalc_usage_data($updater)
        {
        }
        public static function _v_2_7_1_recalc_usage_data($updater)
        {
        }
        public static function _v_2_8_3_recalc_usage_data($updater)
        {
        }
        /**
         * Move general & lightbox settings to active kit and all it's revisions.
         *
         * @param Updater $updater
         *
         * @return bool
         */
        public static function _v_3_0_0_move_general_settings_to_kit($updater)
        {
        }
        public static function _v_3_2_0_migrate_breakpoints_to_new_system($updater, $include_revisions = true)
        {
        }
        public static function _v_3_4_8_fix_font_awesome_default_value_from_1_to_yes()
        {
        }
        public static function _v_3_5_0_remove_old_elementor_scheme()
        {
        }
        public static function _v_3_8_0_fix_php8_image_custom_size()
        {
        }
        public static function _v_3_16_0_container_updates($updater)
        {
        }
        public static function _v_3_17_0_site_settings_updates()
        {
        }
        /**
         * Upgrade Elementor 3.26.0 - Delete old experiments from the DB.
         *
         * @since 3.26.0
         * @static
         * @access public
         */
        public static function _v_3_26_0()
        {
        }
        public static function remove_remote_info_api_data()
        {
        }
        /**
         * @param \wpdb $wpdb
         * @param string $element_type
         *
         * @return array
         */
        public static function get_post_ids_by_element_type($updater, string $element_type) : array
        {
        }
    }
}
namespace Elementor\Core\Base\BackgroundProcess {
    /**
     * https://github.com/A5hleyRich/wp-background-processing GPL v2.0
     *
     * WP Async Request
     *
     * @package WP-Background-Processing
     */
    /**
     * Abstract WP_Async_Request class.
     *
     * @abstract
     */
    abstract class WP_Async_Request
    {
        /**
         * Prefix
         *
         * (default value: 'wp')
         *
         * @var string
         * @access protected
         */
        protected $prefix = 'wp';
        /**
         * Action
         *
         * (default value: 'async_request')
         *
         * @var string
         * @access protected
         */
        protected $action = 'async_request';
        /**
         * Identifier
         *
         * @var mixed
         * @access protected
         */
        protected $identifier;
        /**
         * Data
         *
         * (default value: [])
         *
         * @var array
         * @access protected
         */
        protected $data = [];
        /**
         * Initiate new async request
         */
        public function __construct()
        {
        }
        /**
         * Set data used during the request
         *
         * @param array $data Data.
         *
         * @return $this
         */
        public function data($data)
        {
        }
        /**
         * Dispatch the async request
         *
         * @return array|\WP_Error
         */
        public function dispatch()
        {
        }
        /**
         * Get query args
         *
         * @return array
         */
        protected function get_query_args()
        {
        }
        /**
         * Get query URL
         *
         * @return string
         */
        protected function get_query_url()
        {
        }
        /**
         * Get post args
         *
         * @return array
         */
        protected function get_post_args()
        {
        }
        /**
         * Maybe handle
         *
         * Check for correct nonce and pass to handler.
         */
        public function maybe_handle()
        {
        }
        /**
         * Handle
         *
         * Override this method to perform any actions required
         * during the async request.
         */
        protected abstract function handle();
    }
    /**
     * https://github.com/A5hleyRich/wp-background-processing GPL v2.0
     *
     * WP Background Process
     *
     * @package WP-Background-Processing
     */
    /**
     * Abstract WP_Background_Process class.
     *
     * @abstract
     * @extends WP_Async_Request
     */
    abstract class WP_Background_Process extends \Elementor\Core\Base\BackgroundProcess\WP_Async_Request
    {
        /**
         * Action
         *
         * (default value: 'background_process')
         *
         * @var string
         * @access protected
         */
        protected $action = 'background_process';
        /**
         * Start time of current process.
         *
         * (default value: 0)
         *
         * @var int
         * @access protected
         */
        protected $start_time = 0;
        /**
         * Cron_hook_identifier
         *
         * @var mixed
         * @access protected
         */
        protected $cron_hook_identifier;
        /**
         * Cron_interval_identifier
         *
         * @var mixed
         * @access protected
         */
        protected $cron_interval_identifier;
        /**
         * Initiate new background process
         */
        public function __construct()
        {
        }
        /**
         * Dispatch
         *
         * @access public
         * @return array|\WP_Error
         */
        public function dispatch()
        {
        }
        /**
         * Push to queue
         *
         * @param mixed $data Data.
         *
         * @return $this
         */
        public function push_to_queue($data)
        {
        }
        /**
         * Save queue
         *
         * @return $this
         */
        public function save()
        {
        }
        /**
         * Update queue
         *
         * @param string $key Key.
         * @param array  $data Data.
         *
         * @return $this
         */
        public function update($key, $data)
        {
        }
        /**
         * Delete queue
         *
         * @param string $key Key.
         *
         * @return $this
         */
        public function delete($key)
        {
        }
        /**
         * Generate key
         *
         * Generates a unique key based on microtime. Queue items are
         * given a unique key so that they can be merged upon save.
         *
         * @param int $length Length.
         *
         * @return string
         */
        protected function generate_key($length = 64)
        {
        }
        /**
         * Maybe process queue
         *
         * Checks whether data exists within the queue and that
         * the process is not already running.
         */
        public function maybe_handle()
        {
        }
        /**
         * Is queue empty
         *
         * @return bool
         */
        protected function is_queue_empty()
        {
        }
        /**
         * Is process running
         *
         * Check whether the current process is already running
         * in a background process.
         */
        protected function is_process_running()
        {
        }
        /**
         * Lock process
         *
         * Lock the process so that multiple instances can't run simultaneously.
         * Override if applicable, but the duration should be greater than that
         * defined in the time_exceeded() method.
         */
        protected function lock_process()
        {
        }
        /**
         * Unlock process
         *
         * Unlock the process so that other instances can spawn.
         *
         * @return $this
         */
        protected function unlock_process()
        {
        }
        /**
         * Get batch
         *
         * @return \stdClass Return the first batch from the queue
         */
        protected function get_batch()
        {
        }
        /**
         * Handle
         *
         * Pass each queue item to the task handler, while remaining
         * within server memory and time limit constraints.
         */
        protected function handle()
        {
        }
        /**
         * Memory exceeded
         *
         * Ensures the batch process never exceeds 90%
         * of the maximum WordPress memory.
         *
         * @return bool
         */
        protected function memory_exceeded()
        {
        }
        /**
         * Get memory limit
         *
         * @return int
         */
        protected function get_memory_limit()
        {
        }
        /**
         * Time exceeded.
         *
         * Ensures the batch never exceeds a sensible time limit.
         * A timeout limit of 30s is common on shared hosting.
         *
         * @return bool
         */
        protected function time_exceeded()
        {
        }
        /**
         * Complete.
         *
         * Override if applicable, but ensure that the below actions are
         * performed, or, call parent::complete().
         */
        protected function complete()
        {
        }
        /**
         * Schedule cron healthcheck
         *
         * @access public
         * @param mixed $schedules Schedules.
         * @return mixed
         */
        public function schedule_cron_healthcheck($schedules)
        {
        }
        /**
         * Handle cron healthcheck
         *
         * Restart the background process if not already running
         * and data exists in the queue.
         */
        public function handle_cron_healthcheck()
        {
        }
        /**
         * Schedule event
         */
        protected function schedule_event()
        {
        }
        /**
         * Clear scheduled event
         */
        protected function clear_scheduled_event()
        {
        }
        /**
         * Cancel Process
         *
         * Stop processing queue items, clear cronjob and delete batch.
         *
         */
        public function cancel_process()
        {
        }
        /**
         * Task
         *
         * Override this method to perform any actions required on each
         * queue item. Return the modified item for further processing
         * in the next pass through. Or, return false to remove the
         * item from the queue.
         *
         * @param mixed $item Queue item to iterate over.
         *
         * @return mixed
         */
        protected abstract function task($item);
    }
}
namespace Elementor\Core\Base {
    /**
     * WC_Background_Process class.
     */
    abstract class Background_Task extends \Elementor\Core\Base\BackgroundProcess\WP_Background_Process
    {
        protected $current_item;
        /**
         * Dispatch updater.
         *
         * Updater will still run via cron job if this fails for any reason.
         */
        public function dispatch()
        {
        }
        public function query_col($sql)
        {
        }
        public function should_run_again($updated_rows)
        {
        }
        public function get_current_offset()
        {
        }
        public function get_limit()
        {
        }
        public function set_total()
        {
        }
        /**
         * Complete
         *
         * Override if applicable, but ensure that the below actions are
         * performed, or, call parent::complete().
         */
        protected function complete()
        {
        }
        public function continue_run()
        {
        }
        /**
         * @return mixed
         */
        public function get_current_item()
        {
        }
        /**
         * Get batch.
         *
         * @return \stdClass Return the first batch from the queue.
         */
        protected function get_batch()
        {
        }
        /**
         * Handle cron healthcheck
         *
         * Restart the background process if not already running
         * and data exists in the queue.
         */
        public function handle_cron_healthcheck()
        {
        }
        /**
         * Schedule fallback event.
         */
        protected function schedule_event()
        {
        }
        /**
         * Is the updater running?
         *
         * @return boolean
         */
        public function is_running()
        {
        }
        /**
         * See if the batch limit has been exceeded.
         *
         * @return bool
         */
        protected function batch_limit_exceeded()
        {
        }
        /**
         * Handle.
         *
         * Pass each queue item to the task handler, while remaining
         * within server memory and time limit constraints.
         */
        protected function handle()
        {
        }
        /**
         * Use the protected `is_process_running` method as a public method.
         * @return bool
         */
        public function is_process_locked()
        {
        }
        public function handle_immediately($callbacks)
        {
        }
        /**
         * Task
         *
         * Override this method to perform any actions required on each
         * queue item. Return the modified item for further processing
         * in the next pass through. Or, return false to remove the
         * item from the queue.
         *
         * @param array $item
         *
         * @return array|bool
         */
        protected function task($item)
        {
        }
        /**
         * Schedule cron healthcheck.
         *
         * @param array $schedules Schedules.
         * @return array
         */
        public function schedule_cron_healthcheck($schedules)
        {
        }
        /**
         * See if the batch limit has been exceeded.
         *
         * @return bool
         */
        public function is_memory_exceeded()
        {
        }
        /**
         * Delete all batches.
         *
         * @return self
         */
        public function delete_all_batches()
        {
        }
        /**
         * Kill process.
         *
         * Stop processing queue items, clear cronjob and delete all batches.
         */
        public function kill_process()
        {
        }
        public function set_current_item($item)
        {
        }
        protected function format_callback_log($item)
        {
        }
        /**
         * @var \Elementor\Core\Base\Background_Task_Manager
         */
        protected $manager;
        public function __construct($manager)
        {
        }
    }
}
namespace Elementor\Core\Upgrade {
    class Task extends \Elementor\Core\Base\Background_Task
    {
        /**
         * @var DB_Upgrades_Manager
         */
        protected $manager;
        protected function format_callback_log($item)
        {
        }
        public function set_limit($limit)
        {
        }
    }
    class Custom_Tasks
    {
        public static function opt_in_recalculate_usage($updater)
        {
        }
        public static function opt_in_send_tracking_data()
        {
        }
    }
}
namespace Elementor\Core\Base {
    abstract class DB_Upgrades_Manager extends \Elementor\Core\Base\Background_Task_Manager
    {
        protected $current_version = null;
        protected $query_limit = 100;
        public abstract function get_new_version();
        public abstract function get_version_option_name();
        public abstract function get_upgrades_class();
        public abstract function get_updater_label();
        public function get_task_runner_class()
        {
        }
        public function get_query_limit()
        {
        }
        public function set_query_limit($limit)
        {
        }
        public function get_current_version()
        {
        }
        public function should_upgrade()
        {
        }
        public function on_runner_start()
        {
        }
        public function on_runner_complete($did_tasks = false)
        {
        }
        protected function clear_cache()
        {
        }
        public function admin_notice_start_upgrade()
        {
        }
        public function admin_notice_upgrade_is_running()
        {
        }
        public function admin_notice_upgrade_is_completed()
        {
        }
        /**
         * @access protected
         */
        protected function start_run()
        {
        }
        protected function update_db_version()
        {
        }
        public function get_upgrade_callbacks()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace Elementor\Core\Upgrade {
    class Manager extends \Elementor\Core\Base\DB_Upgrades_Manager
    {
        /**
         * @deprecated 3.17.0
         */
        const INSTALLS_HISTORY_META = 'elementor_install_history';
        public static function get_install_history_meta()
        {
        }
        // todo: remove in future releases
        public function should_upgrade()
        {
        }
        public function get_name()
        {
        }
        public function get_action()
        {
        }
        public function get_plugin_name()
        {
        }
        public function get_plugin_label()
        {
        }
        public function get_updater_label()
        {
        }
        public function get_new_version()
        {
        }
        public function get_version_option_name()
        {
        }
        public function get_upgrades_class()
        {
        }
        public static function get_installs_history()
        {
        }
        public static function install_compare($version, $operator)
        {
        }
        protected function update_db_version()
        {
        }
        public static function is_new_installation() : bool
        {
        }
    }
    class Updater extends \Elementor\Core\Base\Background_Task
    {
        /**
         * @var DB_Upgrades_Manager
         */
        protected $manager;
        protected function format_callback_log($item)
        {
        }
        public function set_limit($limit)
        {
        }
    }
    class Upgrade_Utils
    {
        /**
         *  _update_widget_settings
         *
         * @param string $widget_id  widget type id
         * @param Updater $updater   updater instance
         * @param array $changes     array containing updating control_ids, callback and other data needed by the callback
         *
         * @return bool
         */
        public static function _update_widget_settings($widget_id, $updater, $changes)
        {
        }
    }
}
namespace Elementor\Core {
    /**
     * Elementor modules manager.
     *
     * Elementor modules manager handler class is responsible for registering and
     * managing Elementor modules.
     *
     * @since 1.6.0
     */
    class Modules_Manager
    {
        /**
         * Modules manager constructor.
         *
         * Initializing the Elementor modules manager.
         *
         * @since 1.6.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Get modules names.
         *
         * Retrieve the modules names.
         *
         * @since 2.0.0
         * @access public
         *
         * @return string[] Modules names.
         */
        public function get_modules_names()
        {
        }
        /**
         * Get modules.
         *
         * Retrieve all the registered modules or a specific module.
         *
         * @since 2.0.0
         * @access public
         *
         * @param string $module_name Module name.
         *
         * @return null|Module|Module[] All the registered modules or a specific module.
         */
        public function get_modules($module_name)
        {
        }
        /**
         * Get modules namespace prefix.
         *
         * Retrieve the modules namespace prefix.
         *
         * @since 2.0.0
         * @access protected
         *
         * @return string Modules namespace prefix.
         */
        protected function get_modules_namespace_prefix()
        {
        }
    }
}
namespace Elementor\Core\Logger\Loggers {
    interface Logger_Interface
    {
        const LEVEL_INFO = 'info';
        const LEVEL_NOTICE = 'notice';
        const LEVEL_WARNING = 'warning';
        const LEVEL_ERROR = 'error';
        const LOG_NAME = 'elementor_log';
        /**
         * @param string $message
         * @param string $type
         * @param array  $meta
         *
         * @return void
         */
        public function log($message, $type = self::LEVEL_INFO, $meta = []);
        /**
         * @param string $message
         * @param array $meta
         *
         * @return void
         */
        public function info($message, $meta = []);
        /**
         * @param string $message
         * @param array $meta
         *
         * @return void
         */
        public function notice($message, $meta = []);
        /**
         * @param string $message
         * @param array $meta
         *
         * @return void
         */
        public function warning($message, $meta = []);
        /**
         * @param string $message
         * @param array $meta
         *
         * @return void
         */
        public function error($message, $meta = []);
        /**
         * @param int $max_entries
         * @param bool $table use <td> in format
         *
         * @return array [ 'key' => [ 'total_count' => int, 'count' => int, 'entries' => Log_Item[] ] ]
         */
        public function get_formatted_log_entries($max_entries, $table = true);
    }
    abstract class Base implements \Elementor\Core\Logger\Loggers\Logger_Interface
    {
        protected abstract function save_log(\Elementor\Core\Logger\Items\Log_Item_Interface $item);
        /**
         * @return Log_Item_Interface[]
         */
        public abstract function get_log();
        public function log($item, $type = self::LEVEL_INFO, $args = [])
        {
        }
        public function info($message, $args = [])
        {
        }
        public function notice($message, $args = [])
        {
        }
        public function warning($message, $args = [])
        {
        }
        public function error($message, $args = [])
        {
        }
        public function get_formatted_log_entries($max_entries, $table = true)
        {
        }
    }
    class Db extends \Elementor\Core\Logger\Loggers\Base
    {
        public function save_log(\Elementor\Core\Logger\Items\Log_Item_Interface $item)
        {
        }
        public function clear()
        {
        }
        public function get_log()
        {
        }
    }
}
namespace Elementor\Core\Logger {
    class Manager extends \Elementor\Core\Base\Module
    {
        protected $loggers = [];
        protected $default_logger = '';
        public function get_name()
        {
        }
        public function shutdown($last_error = null, $should_exit = false)
        {
        }
        public function rest_error_handler($error_number, $error_message, $error_file, $error_line)
        {
        }
        public function register_error_handler()
        {
        }
        public function add_system_info_report()
        {
        }
        /**
         * Javascript log.
         *
         * Log Elementor errors and save them in the database.
         *
         * Fired by `wp_ajax_elementor_js_log` action.
         *
         */
        public function js_log()
        {
        }
        public function register_logger($name, $class)
        {
        }
        public function set_default_logger($name)
        {
        }
        public function register_default_loggers()
        {
        }
        /**
         * @param string $name
         *
         * @return Logger_Interface
         */
        public function get_logger($name = '')
        {
        }
        /**
         * @param string $message
         * @param array  $args
         *
         * @return void
         */
        public function log($message, $args = [])
        {
        }
        /**
         * @param string $message
         * @param array $args
         *
         * @return void
         */
        public function info($message, $args = [])
        {
        }
        /**
         * @param string $message
         * @param array $args
         *
         * @return void
         */
        public function notice($message, $args = [])
        {
        }
        /**
         * @param string $message
         * @param array $args
         *
         * @return void
         */
        public function warning($message, $args = [])
        {
        }
        /**
         * @param string $message
         * @param array $args
         *
         * @return void
         */
        public function error($message, $args = [])
        {
        }
        public function __construct()
        {
        }
    }
}
namespace Elementor\Modules\System_Info\Reporters {
    /**
     * Elementor base reporter.
     *
     * A base abstract class that provides the needed properties and methods to
     * manage and handle reporter in inheriting classes.
     *
     * @since 2.9.0
     * @abstract
     */
    abstract class Base
    {
        /**
         * Reporter properties.
         *
         * Holds the list of all the properties of the report.
         *
         * @access protected
         * @static
         *
         * @var array
         */
        protected $_properties;
        /**
         * Get report title.
         *
         * Retrieve the title of the report.
         *
         * @since 2.9.0
         * @access public
         * @abstract
         */
        public abstract function get_title();
        /**
         * Get report fields.
         *
         * Retrieve the required fields for the report.
         *
         * @since 2.9.0
         * @access public
         * @abstract
         */
        public abstract function get_fields();
        /**
         * Is report enabled.
         *
         * Whether the report is enabled.
         *
         * @since 2.9.0
         * @access public
         *
         * @return bool Whether the report is enabled.
         */
        public function is_enabled()
        {
        }
        public function print_html()
        {
        }
        public function print_html_label($label)
        {
        }
        public function print_raw($tabs_count)
        {
        }
        /**
         * Get report.
         *
         * Retrieve the report with all it's containing fields.
         *
         * @since 2.9.0
         * @access public
         *
         * @return \WP_Error | array {
         *    Report fields.
         *
         *    @type string $name Field name.
         *    @type string $label Field label.
         * }
         */
        public final function get_report($format = '')
        {
        }
        /**
         * Get properties keys.
         *
         * Retrieve the keys of the properties.
         *
         * @since 2.9.0
         * @access public
         * @static
         *
         * @return array {
         *    Property keys.
         *
         *    @type string $name   Property name.
         *    @type string $fields Property fields.
         * }
         */
        public static function get_properties_keys()
        {
        }
        /**
         * Filter possible properties.
         *
         * Retrieve possible properties filtered by property keys.
         *
         * @since 2.9.0
         * @access public
         * @static
         *
         * @param array $properties Properties to filter.
         *
         * @return array Possible properties filtered by property keys.
         */
        public static final function filter_possible_properties($properties)
        {
        }
        /**
         * Set properties.
         *
         * Add/update properties to the report.
         *
         * @since 2.9.0
         * @access public
         *
         * @param array $key   Property key.
         * @param array $value Optional. Property value. Default is `null`.
         */
        public final function set_properties($key, $value = null)
        {
        }
        /**
         * Reporter base constructor.
         *
         * Initializing the reporter base class.
         *
         * @since 2.9.0
         * @access public
         *
         * @param array $properties Optional. Properties to filter. Default is `null`.
         */
        public function __construct($properties = null)
        {
        }
    }
}
namespace Elementor\Core\Logger {
    /**
     * Elementor Log reporter.
     *
     * Elementor log reporter handler class is responsible for generating the
     * debug reports.
     *
     * @since 2.4.0
     */
    class Log_Reporter extends \Elementor\Modules\System_Info\Reporters\Base
    {
        const MAX_ENTRIES = 20;
        const CLEAR_LOG_ACTION = 'elementor-clear-log';
        public function get_title()
        {
        }
        public function get_fields()
        {
        }
        public function print_html_label($log_label)
        {
        }
        public function get_log_entries()
        {
        }
        public function get_raw_log_entries()
        {
        }
    }
}
namespace Elementor\Core\Logger\Items {
    /**
     * Interface Log_Item_Interface
     *
     * @package Elementor\Core\Logger
     *
     * @property string $date
     * @property string $type
     * @property string $message
     * @property int $times
     * @property array $meta
     * @property array $times_dates
     * @property array $args
     *
     */
    interface Log_Item_Interface extends \JsonSerializable
    {
        const MAX_LOG_ENTRIES = 42;
        /**
         * Log_Item_Interface constructor.
         *
         * @param array $args
         */
        public function __construct($args);
        /**
         * @param string $name
         *
         * @return string
         */
        public function __get($name);
        /**
         * @return string
         */
        public function __toString();
        /**
         * @param $str
         * @return Log_Item_Interface | null
         */
        public static function from_json($str);
        /**
         * @param string $format
         * @return string
         */
        public function format($format = 'html');
        /**
         * @return string
         */
        public function get_fingerprint();
        /**
         * @param Log_Item_Interface $item
         * @param bool               $truncate
         */
        public function increase_times($item, $truncate = true);
        /**
         * @return string
         */
        public function get_name();
    }
    class Base implements \Elementor\Core\Logger\Items\Log_Item_Interface
    {
        const FORMAT = 'date [type] message [meta]';
        const TRACE_FORMAT = '#key: file(line): class type function()';
        const TRACE_LIMIT = 5;
        protected $date;
        protected $type;
        protected $message;
        protected $meta = [];
        protected $times = 0;
        protected $times_dates = [];
        protected $args = [];
        public function __construct($args)
        {
        }
        public function __get($name)
        {
        }
        public function __toString()
        {
        }
        #[\ReturnTypeWillChange]
        public function jsonSerialize()
        {
        }
        public function deserialize($properties)
        {
        }
        /**
         * @return Log_Item_Interface | null
         */
        public static function from_json($str)
        {
        }
        public function to_formatted_string($output_format = 'html')
        {
        }
        public function get_fingerprint()
        {
        }
        public function increase_times($item, $truncate = true)
        {
        }
        public function format($format = 'html')
        {
        }
        public function get_name()
        {
        }
    }
    class File extends \Elementor\Core\Logger\Items\Base
    {
        const FORMAT = 'date [type X times][file:line] message [meta]';
        protected $file;
        protected $line;
        public function __construct($args)
        {
        }
        #[\ReturnTypeWillChange]
        public function jsonSerialize()
        {
        }
        public function deserialize($properties)
        {
        }
        public function get_name()
        {
        }
    }
    class JS extends \Elementor\Core\Logger\Items\File
    {
        const FORMAT = 'JS: date [type X times][file:line:column] message [meta]';
        protected $column;
        public function __construct($args)
        {
        }
        #[\ReturnTypeWillChange]
        public function jsonSerialize()
        {
        }
        public function deserialize($properties)
        {
        }
        public function get_name()
        {
        }
    }
    class PHP extends \Elementor\Core\Logger\Items\File
    {
        const FORMAT = 'PHP: date [type X times][file::line] message [meta]';
        public function get_name()
        {
        }
    }
}
namespace Elementor\Core\Base {
    /**
     * Base App
     *
     * Base app utility class that provides shared functionality of apps.
     *
     * @since 2.3.0
     */
    abstract class App extends \Elementor\Core\Base\Module
    {
        /**
         * Print config.
         *
         * Used to print the app and its components settings as a JavaScript object.
         *
         * @param string $handle Optional
         *
         * @since 2.3.0
         * @since 2.6.0 added the `$handle` parameter
         * @access protected
         */
        protected final function print_config($handle = null)
        {
        }
    }
}
namespace Elementor\Core\App {
    /**
     * This App class was introduced for backwards compatibility with 3rd parties.
     */
    class App extends \Elementor\Core\Base\App
    {
        const PAGE_ID = 'elementor-app';
        public function get_name()
        {
        }
    }
}
namespace Elementor\App\Modules\ImportExport {
    /**
     * This App class exists for backwards compatibility with 3rd parties.
     *
     * @deprecated 3.8.0
     */
    class Module extends \Elementor\Core\Base\Module
    {
        /**
         * @deprecated 3.8.0
         */
        const VERSION = '1.0.0';
        /**
         * @deprecated 3.8.0
         */
        public $import;
        /**
         * @deprecated 3.8.0
         */
        public function get_name()
        {
        }
    }
}
namespace Elementor\Core\App\Modules\KitLibrary {
    /**
     * This App class exists for backwards compatibility with 3rd parties.
     *
     * @deprecated 3.8.0
     */
    class Module extends \Elementor\Core\Base\Module
    {
        /**
         * @deprecated 3.8.0
         */
        const VERSION = '1.0.0';
        /**
         * @deprecated 3.8.0
         */
        public function get_name()
        {
        }
    }
}
namespace Elementor\Core\Common\Modules\Connect\Apps {
    abstract class Base_App
    {
        const OPTION_NAME_PREFIX = 'elementor_connect_';
        const OPTION_CONNECT_SITE_KEY = self::OPTION_NAME_PREFIX . 'site_key';
        const SITE_URL = 'https://my.elementor.com/connect/v1';
        const API_URL = 'https://my.elementor.com/api/connect/v1';
        const HTTP_RETURN_TYPE_OBJECT = 'object';
        const HTTP_RETURN_TYPE_ARRAY = 'array';
        protected $data = [];
        protected $auth_mode = '';
        /**
         * @var Http
         */
        protected $http;
        /**
         * @since 2.3.0
         * @access protected
         * @abstract
         * TODO: make it public.
         */
        protected abstract function get_slug();
        /**
         * @since 2.8.0
         * @access public
         * TODO: make it abstract.
         */
        public function get_title()
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         * @abstract
         */
        protected abstract function update_settings();
        /**
         * @since 2.3.0
         * @access public
         * @static
         */
        public static function get_class_name()
        {
        }
        /**
         * @access public
         * @abstract
         */
        public function render_admin_widget()
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function get_option_name()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function admin_notice()
        {
        }
        public function get_app_token_from_cli_token($cli_token)
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function action_authorize()
        {
        }
        public function action_reset()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function action_get_token()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function action_disconnect()
        {
        }
        /**
         * @since 2.8.0
         * @access public
         */
        public function action_reconnect()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function get_admin_url($action, $params = [])
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function is_connected()
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function init()
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function init_data()
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function after_connect()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function get($key, $default = null)
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function set($key, $value = null)
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function delete($key = null)
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function add($key, $value, $default = '')
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function add_notice($content, $type = 'success')
        {
        }
        /**
         * @param       $action
         * @param array $request_body
         * @param false $as_array
         *
         * @return mixed|\WP_Error
         */
        protected function request($action, $request_body = [], $as_array = false)
        {
        }
        /**
         * Get Base Connect Info
         *
         * Returns an array of connect info.
         *
         * @return array
         */
        protected function get_base_connect_info()
        {
        }
        /**
         * Get all the connect information
         *
         * @return array
         */
        protected function get_connect_info()
        {
        }
        /**
         * @param $endpoint
         *
         * @return array
         */
        protected function generate_authentication_headers($endpoint)
        {
        }
        /**
         * Send an http request
         *
         * @param       $method
         * @param       $endpoint
         * @param array $args
         * @param array $options
         *
         * @return mixed|\WP_Error
         */
        protected function http_request($method, $endpoint, $args = [], $options = [])
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function get_api_url()
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function get_remote_site_url()
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function get_remote_authorize_url()
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function redirect_to_admin_page($url = '')
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function set_client_id()
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function set_request_state()
        {
        }
        protected function get_popup_success_event_data()
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function print_popup_close_script($url)
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function disconnect()
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        public function get_site_key()
        {
        }
        protected function redirect_to_remote_authorize_url()
        {
        }
        protected function get_auth_redirect_uri()
        {
        }
        protected function print_notices($notices)
        {
        }
        protected function get_app_info()
        {
        }
        protected function print_app_info()
        {
        }
        public function set_auth_mode($mode)
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function __construct()
        {
        }
    }
    abstract class Base_User_App extends \Elementor\Core\Common\Modules\Connect\Apps\Base_App
    {
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function update_settings()
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function init_data()
        {
        }
    }
    abstract class Common_App extends \Elementor\Core\Common\Modules\Connect\Apps\Base_User_App
    {
        const OPTION_CONNECT_COMMON_DATA_KEY = self::OPTION_NAME_PREFIX . 'common_data';
        protected static $common_data = null;
        /**
         * @since 2.3.0
         * @access public
         */
        public function get_option_name()
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function init_data()
        {
        }
        public function action_reset()
        {
        }
    }
    class Library extends \Elementor\Core\Common\Modules\Connect\Apps\Common_App
    {
        public function get_title()
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function get_slug()
        {
        }
        public function get_template_content($id)
        {
        }
        public function localize_settings($settings)
        {
        }
        public function library_connect_popup_seen()
        {
        }
        /**
         * @param \Elementor\Core\Common\Modules\Ajax\Module $ajax_manager
         */
        public function register_ajax_actions($ajax_manager)
        {
        }
        /**
         * After Connect
         *
         * After Connecting to the library, re-fetch the library data to get it up to date.
         *
         * @since 3.7.0
         */
        protected function after_connect()
        {
        }
        protected function get_app_info()
        {
        }
        protected function get_popup_success_event_data()
        {
        }
        protected function init()
        {
        }
    }
}
namespace Elementor\Core\App\Modules\KitLibrary\Connect {
    /**
     * This App class exists for backwards compatibility with 3rd parties.
     *
     * @deprecated 3.8.0
     */
    class Kit_Library extends \Elementor\Core\Common\Modules\Connect\Apps\Library
    {
        /**
         * @deprecated 3.8.0
         */
        public function is_connected()
        {
        }
    }
}
namespace Elementor\Core\App\Modules\Onboarding {
    /**
     * This App class exists for backwards compatibility with 3rd parties.
     *
     * @deprecated 3.8.0
     */
    class Module extends \Elementor\Core\Base\Module
    {
        /**
         * @deprecated 3.8.0
         */
        const VERSION = '1.0.0';
        /**
         * @deprecated 3.8.0
         */
        public function get_name()
        {
        }
    }
}
namespace Elementor\Core\Frontend {
    class Render_Mode_Manager
    {
        const QUERY_STRING_PARAM_NAME = 'render_mode';
        const QUERY_STRING_POST_ID = 'post_id';
        const QUERY_STRING_NONCE_PARAM_NAME = 'render_mode_nonce';
        const NONCE_ACTION_PATTERN = 'render_mode_{post_id}';
        /**
         * @param $post_id
         * @param $render_mode_name
         *
         * @return string
         */
        public static function get_base_url($post_id, $render_mode_name)
        {
        }
        /**
         * @param $post_id
         *
         * @return string
         */
        public static function get_nonce_action($post_id)
        {
        }
        /**
         * Register a new render mode into the render mode manager.
         *
         * @param $class_name
         *
         * @return $this
         * @throws \Exception
         */
        public function register_render_mode($class_name)
        {
        }
        /**
         * Get the current render mode.
         *
         * @return Render_Mode_Base
         */
        public function get_current()
        {
        }
        /**
         * Render_Mode_Manager constructor.
         *
         * @throws \Exception
         */
        public function __construct()
        {
        }
    }
}
namespace Elementor\Core\Frontend\RenderModes {
    abstract class Render_Mode_Base
    {
        const ENQUEUE_SCRIPTS_PRIORITY = 10;
        const ENQUEUE_STYLES_PRIORITY = 10;
        /**
         * @var int
         */
        protected $post_id;
        /**
         * @var Document
         */
        protected $document;
        /**
         * Render_Mode_Base constructor.
         *
         * @param $post_id
         */
        public function __construct($post_id)
        {
        }
        /**
         * Returns the key name of the class.
         *
         * @return string
         * @throws \Exception
         */
        public static function get_name()
        {
        }
        /**
         * @param $post_id
         *
         * @return string
         * @throws \Exception
         */
        public static function get_url($post_id)
        {
        }
        /**
         * Runs before the render, by default load scripts and styles.
         */
        public function prepare_render()
        {
        }
        /**
         * By default do not do anything.
         */
        protected function enqueue_scripts()
        {
        }
        /**
         * By default do not do anything.
         */
        protected function enqueue_styles()
        {
        }
        /**
         * Check if the current user has permissions for the current render mode.
         *
         * @return bool
         */
        public function get_permissions_callback()
        {
        }
        /**
         * Checks if the current render mode is static render, By default returns false.
         *
         * @return bool
         */
        public function is_static()
        {
        }
        /**
         * @return Document
         */
        public function get_document()
        {
        }
    }
    class Render_Mode_Normal extends \Elementor\Core\Frontend\RenderModes\Render_Mode_Base
    {
        /**
         * @return string
         */
        public static function get_name()
        {
        }
        /**
         * Anyone can access the normal render mode.
         *
         * @return bool
         */
        public function get_permissions_callback()
        {
        }
    }
}
namespace Elementor\Core\Frontend {
    class Performance
    {
        public static function set_use_style_controls(bool $bool) : void
        {
        }
        public static function is_use_style_controls() : bool
        {
        }
        public static function should_optimize_controls()
        {
        }
    }
}
namespace Elementor {
    /**
     * Elementor has validation interface.
     *
     * @param array $control_data The value to validate.
     * @return bool True on valid, throws an exception on error.
     * @throws \Exception If validation fails.
     */
    interface Has_Validation
    {
        public function validate(array $control_data) : bool;
    }
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
        /**
         * Update value of control that needs to be updated after import.
         *
         * @param mixed $value
         * @param array $control_args
         * @param array $config
         *
         * @return mixed
         */
        public function on_import_update_settings($value, array $control_args, array $config)
        {
        }
    }
    /**
     * Elementor base data control.
     *
     * An abstract class for creating new data controls in the panel.
     *
     * @since 1.5.0
     * @abstract
     */
    abstract class Base_Data_Control extends \Elementor\Base_Control
    {
        public function __construct()
        {
        }
        /**
         * Get data control default value.
         *
         * Retrieve the default value of the data control. Used to return the default
         * values while initializing the data control.
         *
         * @since 1.5.0
         * @access public
         *
         * @return string Control default value.
         */
        public function get_default_value()
        {
        }
        /**
         * Get data control value.
         *
         * Retrieve the value of the data control from a specific Controls_Stack settings.
         *
         * @since 1.5.0
         * @access public
         *
         * @param array $control  Control
         * @param array $settings Element settings
         *
         * @return mixed Control values.
         */
        public function get_value($control, $settings)
        {
        }
        /**
         * Parse dynamic tags.
         *
         * Iterates through all the controls and renders all the dynamic tags.
         *
         * @since 2.0.0
         * @access public
         *
         * @param string $dynamic_value    The dynamic tag text.
         * @param array  $dynamic_settings The dynamic tag settings.
         *
         * @return string|string[]|mixed A string or an array of strings with the
         *                               return value from each tag callback function.
         */
        public function parse_tags($dynamic_value, $dynamic_settings)
        {
        }
        /**
         * Get data control style value.
         *
         * Retrieve the style of the control. Used when adding CSS rules to the control
         * while extracting CSS from the `selectors` data argument.
         *
         * @since 1.5.0
         * @since 2.3.3 New `$control_data` parameter added.
         * @access public
         *
         * @param string $css_property  CSS property.
         * @param string $control_value Control value.
         * @param array  $control_data Control Data.
         *
         * @return string Control style value.
         */
        public function get_style_value($css_property, $control_value, array $control_data)
        {
        }
        /**
         * Get data control unique ID.
         *
         * Retrieve the unique ID of the control. Used to set a uniq CSS ID for the
         * element.
         *
         * @since 1.5.0
         * @access protected
         *
         * @param string $input_type Input type. Default is 'default'.
         *
         * @return string Unique ID.
         */
        protected function get_control_uid($input_type = 'default')
        {
        }
        /**
         * Safe Print data control unique ID.
         *
         * Retrieve the unique ID of the control. Used to set a unique CSS ID for the
         * element.
         *
         * @access protected
         *
         * @param string $input_type Input type. Default is 'default'.
         */
        protected function print_control_uid($input_type = 'default')
        {
        }
    }
    /**
     * Elementor repeater control.
     *
     * A base control for creating repeater control. Repeater control allows you to
     * build repeatable blocks of fields. You can create, for example, a set of
     * fields that will contain a title and a WYSIWYG text - the user will then be
     * able to add "rows", and each row will contain a title and a text. The data
     * can be wrapper in custom HTML tags, designed using CSS, and interact using JS
     * or external libraries.
     *
     * @since 1.0.0
     */
    class Control_Repeater extends \Elementor\Base_Data_Control implements \Elementor\Has_Validation
    {
        /**
         * Get repeater control type.
         *
         * Retrieve the control type, in this case `repeater`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get repeater control default value.
         *
         * Retrieve the default value of the data control. Used to return the default
         * values while initializing the repeater control.
         *
         * @since 2.0.0
         * @access public
         *
         * @return array Control default value.
         */
        public function get_default_value()
        {
        }
        /**
         * Get repeater control default settings.
         *
         * Retrieve the default settings of the repeater control. Used to return the
         * default settings while initializing the repeater control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Get repeater control value.
         *
         * Retrieve the value of the repeater control from a specific Controls_Stack.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $control  Control
         * @param array $settings Controls_Stack settings
         *
         * @return mixed Control values.
         */
        public function get_value($control, $settings)
        {
        }
        /**
         * Import repeater.
         *
         * Used as a wrapper method for inner controls while importing Elementor
         * template JSON file, and replacing the old data.
         *
         * @since 1.8.0
         * @access public
         *
         * @param array $settings     Control settings.
         * @param array $control_data Optional. Control data. Default is an empty array.
         *
         * @return array Control settings.
         */
        public function on_import($settings, $control_data = [])
        {
        }
        /**
         * Render repeater control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
        public function validate(array $control_data) : bool
        {
        }
    }
}
namespace Elementor\Core\Kits\Controls {
    class Repeater extends \Elementor\Control_Repeater
    {
        const CONTROL_TYPE = 'global-style-repeater';
        /**
         * Get control type.
         *
         * Retrieve the control type, in this case `global-style-repeater`.
         *
         * @since 3.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get repeater control default settings.
         *
         * Retrieve the default settings of the repeater control. Used to return the
         * default settings while initializing the repeater control.
         *
         * @since 3.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Render repeater control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 3.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
}
namespace Elementor\Core\Kits {
    class Manager
    {
        const OPTION_ACTIVE = 'elementor_active_kit';
        const OPTION_PREVIOUS = 'elementor_previous_kit';
        const E_HASH_COMMAND_OPEN_SITE_SETTINGS = 'e:run:panel/global/open';
        public function get_active_id()
        {
        }
        public function get_previous_id()
        {
        }
        public function get_kit($kit_id)
        {
        }
        public function get_active_kit()
        {
        }
        public function get_active_kit_for_frontend()
        {
        }
        /**
         * Checks if specific post is a kit.
         *
         * @param $post_id
         *
         * @return bool
         */
        public function is_kit($post_id)
        {
        }
        /**
         * Init kit controls.
         *
         * A temp solution in order to avoid init kit group control from within another group control.
         *
         * After moving the `default_font` to the kit, the Typography group control cause initialize the kit controls at: https://github.com/elementor/elementor/blob/e6e1db9eddef7e3c1a5b2ba0c2338e2af2a3bfe3/includes/controls/groups/typography.php#L91
         * and because the group control is a singleton, its args are changed to the last kit group control.
         */
        public function init_kit_controls()
        {
        }
        public function get_current_settings($setting = null)
        {
        }
        public function create(array $kit_data = [], array $kit_meta_data = [])
        {
        }
        public function create_new_kit($kit_name = '', $settings = [], $active = true)
        {
        }
        public function create_default()
        {
        }
        /**
         * Create a default kit if needed.
         *
         * This action runs on activation hook, all the Plugin components do not exists and
         * the Document manager and Kits manager instances cannot be used.
         *
         * @return int|void|\WP_Error
         */
        public static function create_default_kit()
        {
        }
        /**
         * @param $imported_kit_id int The id of the imported kit that should be deleted.
         * @param $active_kit_id int The id of the kit that should set as 'active_kit' after the deletion.
         * @param $previous_kit_id int The id of the kit that should set as 'previous_kit' after the deletion.
         * @return void
         */
        public function revert(int $imported_kit_id, int $active_kit_id, int $previous_kit_id)
        {
        }
        /**
         * @param Documents_Manager $documents_manager
         */
        public function register_document($documents_manager)
        {
        }
        public function localize_settings($settings)
        {
        }
        public function preview_enqueue_styles()
        {
        }
        public function frontend_before_enqueue_styles()
        {
        }
        public function render_panel_html()
        {
        }
        public function get_kit_for_frontend()
        {
        }
        public function update_kit_settings_based_on_option($key, $value)
        {
        }
        /**
         * Convert Scheme to Default Global
         *
         * If a control has a scheme property, convert it to a default Global.
         *
         * @param $scheme - Control scheme property
         * @return array - Control/group control args
         * @since 3.0.0
         * @access public
         */
        public function convert_scheme_to_global($scheme)
        {
        }
        public function register_controls()
        {
        }
        public function is_custom_colors_enabled()
        {
        }
        public function is_custom_typography_enabled()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace Elementor {
    /**
     * Elementor sub controls stack.
     *
     * An abstract class that can be used to divide a large ControlsStack into small parts.
     *
     * @abstract
     */
    abstract class Sub_Controls_Stack
    {
        /**
         * @var Controls_Stack
         */
        protected $parent;
        /**
         * Get self ID.
         *
         * Retrieve the self ID.
         *
         * @access public
         * @abstract
         */
        public abstract function get_id();
        /**
         * Get self title.
         *
         * Retrieve the self title.
         *
         * @access public
         * @abstract
         */
        public abstract function get_title();
        /**
         * Constructor.
         *
         * Initializing the base class by setting parent stack.
         *
         * @access public
         * @param Controls_Stack $parent
         */
        public function __construct($parent)
        {
        }
        /**
         * Get control ID.
         *
         * Retrieve the control ID. Note that the sub controls stack may have a special prefix
         * to distinguish them from regular controls, and from controls in other
         * sub stack.
         *
         * By default do nothing, and return the original id.
         *
         * @access protected
         *
         * @param string $control_base_id Control base ID.
         *
         * @return string Control ID.
         */
        protected function get_control_id($control_base_id)
        {
        }
        /**
         * Add new control.
         *
         * Register a single control to allow the user to set/update data.
         *
         * @access public
         *
         * @param string $id   Control ID.
         * @param array  $args Control arguments.
         * @param array  $options
         *
         * @return bool True if added, False otherwise.
         */
        public function add_control($id, $args, $options = [])
        {
        }
        /**
         * Update control.
         *
         * Change the value of an existing control.
         *
         * @access public
         *
         * @param string $id      Control ID.
         * @param array  $args    Control arguments. Only the new fields you want to update.
         * @param array  $options Optional. Some additional options.
         */
        public function update_control($id, $args, array $options = [])
        {
        }
        /**
         * Remove control.
         *
         * Unregister an existing control.
         *
         * @access public
         *
         * @param string $id Control ID.
         */
        public function remove_control($id)
        {
        }
        /**
         * Add new group control.
         *
         * Register a set of related controls grouped together as a single unified
         * control.
         *
         * @access public
         *
         * @param string $group_name Group control name.
         * @param array  $args       Group control arguments. Default is an empty array.
         * @param array  $options
         *
         */
        public function add_group_control($group_name, $args, $options = [])
        {
        }
        /**
         * Add new responsive control.
         *
         * Register a set of controls to allow editing based on user screen size.
         *
         * @access public
         *
         * @param string $id   Responsive control ID.
         * @param array  $args Responsive control arguments.
         * @param array  $options
         */
        public function add_responsive_control($id, $args, $options = [])
        {
        }
        /**
         * Update responsive control.
         *
         * Change the value of an existing responsive control.
         *
         * @access public
         *
         * @param string $id   Responsive control ID.
         * @param array  $args Responsive control arguments.
         */
        public function update_responsive_control($id, $args)
        {
        }
        /**
         * Remove responsive control.
         *
         * Unregister an existing responsive control.
         *
         * @access public
         *
         * @param string $id Responsive control ID.
         */
        public function remove_responsive_control($id)
        {
        }
        /**
         * Start controls section.
         *
         * Used to add a new section of controls to the stack.
         *
         * @access public
         *
         * @param string $id   Section ID.
         * @param array  $args Section arguments.
         */
        public function start_controls_section($id, $args = [])
        {
        }
        /**
         * End controls section.
         *
         * Used to close an existing open controls section.
         *
         * @access public
         */
        public function end_controls_section()
        {
        }
        /**
         * Start controls tabs.
         *
         * Used to add a new set of tabs inside a section.
         *
         * @access public
         *
         * @param string $id Control ID.
         */
        public function start_controls_tabs($id)
        {
        }
        public function start_controls_tab($id, $args)
        {
        }
        /**
         * End controls tabs.
         *
         * Used to close an existing open controls tabs.
         *
         * @access public
         */
        public function end_controls_tab()
        {
        }
        /**
         * End controls tabs.
         *
         * Used to close an existing open controls tabs.
         *
         * @access public
         */
        public function end_controls_tabs()
        {
        }
    }
}
namespace Elementor\Core\Kits\Documents\Tabs {
    abstract class Tab_Base extends \Elementor\Sub_Controls_Stack
    {
        /**
         * @var Kit
         */
        protected $parent;
        protected abstract function register_tab_controls();
        public function get_group()
        {
        }
        public function get_icon()
        {
        }
        public function get_help_url()
        {
        }
        public function get_additional_tab_content()
        {
        }
        public function register_controls()
        {
        }
        public function on_save($data)
        {
        }
        /**
         * Before Save
         *
         * Allows for modifying the kit data before it is saved to the database.
         *
         * @param array $data
         * @return array
         */
        public function before_save(array $data)
        {
        }
        protected function register_tab()
        {
        }
        protected function add_default_globals_notice()
        {
        }
    }
    class Theme_Style_Form_Fields extends \Elementor\Core\Kits\Documents\Tabs\Tab_Base
    {
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_icon()
        {
        }
        public function get_help_url()
        {
        }
        protected function register_tab_controls()
        {
        }
    }
    class Settings_Custom_CSS extends \Elementor\Core\Kits\Documents\Tabs\Tab_Base
    {
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_icon()
        {
        }
        public function get_help_url()
        {
        }
        protected function register_tab_controls()
        {
        }
    }
    class Theme_Style_Typography extends \Elementor\Core\Kits\Documents\Tabs\Tab_Base
    {
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_icon()
        {
        }
        public function get_help_url()
        {
        }
        public function register_tab_controls()
        {
        }
    }
    class Global_Colors extends \Elementor\Core\Kits\Documents\Tabs\Tab_Base
    {
        const COLOR_PRIMARY = 'globals/colors?id=primary';
        const COLOR_SECONDARY = 'globals/colors?id=secondary';
        const COLOR_TEXT = 'globals/colors?id=text';
        const COLOR_ACCENT = 'globals/colors?id=accent';
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_icon()
        {
        }
        public function get_help_url()
        {
        }
        protected function register_tab_controls()
        {
        }
    }
    class Global_Typography extends \Elementor\Core\Kits\Documents\Tabs\Tab_Base
    {
        const TYPOGRAPHY_PRIMARY = 'globals/typography?id=primary';
        const TYPOGRAPHY_SECONDARY = 'globals/typography?id=secondary';
        const TYPOGRAPHY_TEXT = 'globals/typography?id=text';
        const TYPOGRAPHY_ACCENT = 'globals/typography?id=accent';
        const TYPOGRAPHY_NAME = 'typography';
        const TYPOGRAPHY_GROUP_PREFIX = self::TYPOGRAPHY_NAME . '_';
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_icon()
        {
        }
        public function get_help_url()
        {
        }
        protected function register_tab_controls()
        {
        }
    }
    class Settings_Site_Identity extends \Elementor\Core\Kits\Documents\Tabs\Tab_Base
    {
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_icon()
        {
        }
        public function get_help_url()
        {
        }
        protected function register_tab_controls()
        {
        }
        public function on_save($data)
        {
        }
    }
    class Settings_Lightbox extends \Elementor\Core\Kits\Documents\Tabs\Tab_Base
    {
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_icon()
        {
        }
        public function get_help_url()
        {
        }
        protected function register_tab_controls()
        {
        }
    }
    class Theme_Style_Buttons extends \Elementor\Core\Kits\Documents\Tabs\Tab_Base
    {
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_icon()
        {
        }
        public function get_help_url()
        {
        }
        protected function register_tab_controls()
        {
        }
    }
    class Theme_Style_Images extends \Elementor\Core\Kits\Documents\Tabs\Tab_Base
    {
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_icon()
        {
        }
        public function get_help_url()
        {
        }
        protected function register_tab_controls()
        {
        }
    }
    class Settings_Layout extends \Elementor\Core\Kits\Documents\Tabs\Tab_Base
    {
        const ACTIVE_BREAKPOINTS_CONTROL_ID = 'active_breakpoints';
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_icon()
        {
        }
        public function get_help_url()
        {
        }
        protected function register_tab_controls()
        {
        }
        /**
         * Before Save
         *
         * Runs Before the Kit document is saved.
         *
         * For backwards compatibility, when the mobile and tablet breakpoints are updated, we also update the
         * old breakpoint settings ('viewport_md', 'viewport_lg' ) with the saved values + 1px. The reason 1px
         * is added is because the old breakpoints system was min-width based, and the new system introduced in
         * Elementor v3.2.0 is max-width based.
         *
         * @since 3.2.0
         *
         * @param array $data
         * @return array $data
         */
        public function before_save(array $data)
        {
        }
        public function on_save($data)
        {
        }
    }
    class Settings_Background extends \Elementor\Core\Kits\Documents\Tabs\Tab_Base
    {
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_icon()
        {
        }
        public function get_help_url()
        {
        }
        protected function register_tab_controls()
        {
        }
    }
    class Settings_Page_Transitions extends \Elementor\Core\Kits\Documents\Tabs\Tab_Base
    {
        const TAB_ID = 'settings-page-transitions';
        public function get_id()
        {
        }
        public function get_title()
        {
        }
        public function get_group()
        {
        }
        public function get_icon()
        {
        }
        public function get_help_url()
        {
        }
        protected function register_tab_controls()
        {
        }
    }
}
namespace Elementor\Core\Base {
    /**
     * Elementor document.
     *
     * An abstract class that provides the needed properties and methods to
     * manage and handle documents in inheriting classes.
     *
     * @since 2.0.0
     * @abstract
     */
    abstract class Document extends \Elementor\Controls_Stack
    {
        /**
         * Document type meta key.
         */
        const TYPE_META_KEY = '_elementor_template_type';
        const PAGE_META_KEY = '_elementor_page_settings';
        const BUILT_WITH_ELEMENTOR_META_KEY = '_elementor_edit_mode';
        const CACHE_META_KEY = '_elementor_element_cache';
        /**
         * Document publish status.
         */
        const STATUS_PUBLISH = 'publish';
        /**
         * Document draft status.
         */
        const STATUS_DRAFT = 'draft';
        /**
         * Document private status.
         */
        const STATUS_PRIVATE = 'private';
        /**
         * Document autosave status.
         */
        const STATUS_AUTOSAVE = 'autosave';
        /**
         * Document pending status.
         */
        const STATUS_PENDING = 'pending';
        /**
         * Document post data.
         *
         * Holds the document post data.
         *
         * @since 2.0.0
         * @access protected
         *
         * @var \WP_Post WordPress post data.
         */
        protected $post;
        /**
         * @since 2.1.0
         * @access protected
         * @static
         */
        protected static function get_editor_panel_categories()
        {
        }
        /**
         * Get properties.
         *
         * Retrieve the document properties.
         *
         * @since 2.0.0
         * @access public
         * @static
         *
         * @return array Document properties.
         */
        public static function get_properties()
        {
        }
        /**
         * @since 2.1.0
         * @access public
         * @static
         */
        public static function get_editor_panel_config()
        {
        }
        public static function get_filtered_editor_panel_categories() : array
        {
        }
        /**
         * Get element title.
         *
         * Retrieve the element title.
         *
         * @since 2.0.0
         * @access public
         * @static
         *
         * @return string Element title.
         */
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        public static function get_add_new_title()
        {
        }
        /**
         * Get property.
         *
         * Retrieve the document property.
         *
         * @since 2.0.0
         * @access public
         * @static
         *
         * @param string $key The property key.
         *
         * @return mixed The property value.
         */
        public static function get_property($key)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         * @static
         */
        public static function get_class_full_name()
        {
        }
        public static function get_create_url()
        {
        }
        public function get_name()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_unique_name()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function get_post_type_title()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_main_id()
        {
        }
        /**
         * @return null|Lock_Behavior
         */
        public static function get_lock_behavior_v2()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param $data
         *
         * @throws \Exception If the widget was not found.
         *
         * @return string
         */
        public function render_element($data)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_main_post()
        {
        }
        public function get_container_attributes()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_wp_preview_url()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_exit_to_dashboard_url()
        {
        }
        /**
         * Get All Post Type URL
         *
         * Get url of the page which display all the posts of the current active document's post type.
         *
         * @since 3.7.0
         *
         * @return string $url
         */
        public function get_all_post_type_url()
        {
        }
        /**
         * Get Main WP dashboard URL.
         *
         * @since 3.7.0
         *
         * @return string $url
         */
        protected function get_main_dashboard_url()
        {
        }
        /**
         * Get auto-saved post revision.
         *
         * Retrieve the auto-saved post revision that is newer than current post.
         *
         * @since 2.0.0
         * @access public
         *
         *
         * @return bool|Document
         */
        public function get_newer_autosave()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function is_autosave()
        {
        }
        /**
         * Check if the current document is a 'revision'
         *
         * @return bool
         */
        public function is_revision()
        {
        }
        /**
         * Checks if the current document status is 'trash'.
         *
         * @return bool
         */
        public function is_trash()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param int  $user_id
         * @param bool $create
         *
         * @return bool|Document
         */
        public function get_autosave($user_id = 0, $create = false)
        {
        }
        /**
         * Add/Remove edit link in dashboard.
         *
         * Add or remove an edit link to the post/page action links on the post/pages list table.
         *
         * Fired by `post_row_actions` and `page_row_actions` filters.
         *
         * @access public
         *
         * @param array    $actions An array of row action links.
         *
         * @return array An updated array of row action links.
         */
        public function filter_admin_row_actions($actions)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function is_editable_by_current_user()
        {
        }
        /**
         * @since 2.9.0
         * @access protected
         */
        protected function get_initial_config()
        {
        }
        /**
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param $data
         *
         * @return bool
         */
        public function save($data)
        {
        }
        public function refresh_post()
        {
        }
        /**
         * @param array $new_settings
         *
         * @return static
         */
        public function update_settings(array $new_settings)
        {
        }
        /**
         * Is built with Elementor.
         *
         * Check whether the post was built with Elementor.
         *
         * @since 2.0.0
         * @access public
         *
         * @return bool Whether the post was built with Elementor.
         */
        public function is_built_with_elementor()
        {
        }
        /**
         * Mark the post as "built with elementor" or not.
         *
         * @param bool $is_built_with_elementor
         *
         * @return $this
         */
        public function set_is_built_with_elementor($is_built_with_elementor)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         * @static
         *
         * @return mixed
         */
        public function get_edit_url()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_preview_url()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param string $key
         *
         * @return array
         */
        public function get_json_meta($key)
        {
        }
        public function update_json_meta($key, $value)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param null $data
         * @param bool $with_html_content
         *
         * @return array
         */
        public function get_elements_raw_data($data = null, $with_html_content = false)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param string $status
         *
         * @return array
         */
        public function get_elements_data($status = self::STATUS_PUBLISH)
        {
        }
        /**
         * Get document setting from DB.
         *
         * @return array
         */
        public function get_db_document_settings()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function convert_to_elementor()
        {
        }
        /**
         * @since 2.1.3
         * @access public
         */
        public function print_elements_with_wrapper($elements_data = null)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_css_wrapper_selector()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_panel_page_settings()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_post()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_permalink()
        {
        }
        /**
         * @since 2.0.8
         * @access public
         */
        public function get_content($with_css = false)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function delete()
        {
        }
        public function force_delete()
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
         * Update dynamic settings in the document for import.
         *
         * @param array $settings The settings of the document.
         * @param array $config Import config to update the settings.
         *
         * @return array
         */
        public function on_import_update_settings(array $settings, array $config) : array
        {
        }
        /**
         * Save editor elements.
         *
         * Save data from the editor to the database.
         *
         * @since 2.0.0
         * @access protected
         *
         * @param array $elements
         */
        protected function save_elements($elements)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param int $user_id Optional. User ID. Default value is `0`.
         *
         * @return bool|int
         */
        public function get_autosave_id($user_id = 0)
        {
        }
        public function save_version()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function save_template_type()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function get_template_type()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param string $key Meta data key.
         *
         * @return mixed
         */
        public function get_main_meta($key)
        {
        }
        /**
         * @since 2.0.4
         * @access public
         *
         * @param string $key   Meta data key.
         * @param mixed $value Meta data value.
         *
         * @return bool|int
         */
        public function update_main_meta($key, $value)
        {
        }
        /**
         * @since 2.0.4
         * @access public
         *
         * @param string $key   Meta data key.
         * @param string $value Optional. Meta data value. Default is an empty string.
         *
         * @return bool
         */
        public function delete_main_meta($key, $value = '')
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param string $key Meta data key.
         *
         * @return mixed
         */
        public function get_meta($key)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param string $key   Meta data key.
         * @param mixed  $value Meta data value.
         *
         * @return bool|int
         */
        public function update_meta($key, $value)
        {
        }
        /**
         * @since 2.0.3
         * @access public
         *
         * @param string $key   Meta data key.
         * @param string $value Meta data value.
         *
         * @return bool
         */
        public function delete_meta($key, $value = '')
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_last_edited()
        {
        }
        /**
         * @return bool
         */
        public function is_saving()
        {
        }
        /**
         * @param $is_saving
         *
         * @return $this
         */
        public function set_is_saving($is_saving)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param array $data
         *
         * @throws \Exception If the post does not exist.
         */
        public function __construct(array $data = [])
        {
        }
        /*
         * Get Export Data
         *
         * Filters a document's data on export
         *
         * @since 3.2.0
         * @access public
         *
         * @return array The data to export
         */
        public function get_export_data()
        {
        }
        public function get_export_summary()
        {
        }
        /*
         * Get Import Data
         *
         * Filters a document's data on import
         *
         * @since 3.2.0
         * @access public
         *
         * @return array The data to import
         */
        public function get_import_data(array $data)
        {
        }
        /**
         * Import
         *
         * Allows to import an external data to a document
         *
         * @since 3.2.0
         * @access public
         *
         * @param array $data
         */
        public function import(array $data)
        {
        }
        public function process_element_import_export(\Elementor\Controls_Stack $element, $method, $element_data = null)
        {
        }
        protected function get_export_metadata()
        {
        }
        protected function get_remote_library_config()
        {
        }
        /**
         * @since 2.0.4
         * @access protected
         *
         * @param $settings
         */
        protected function save_settings($settings)
        {
        }
        /**
         * @since 2.1.3
         * @access protected
         */
        protected function print_elements($elements_data)
        {
        }
        protected function do_print_elements($elements_data)
        {
        }
        public function update_runtime_elements($elements_data = null)
        {
        }
        public function set_document_cache($value)
        {
        }
        protected function delete_cache()
        {
        }
        protected function register_document_controls()
        {
        }
        protected function get_post_statuses()
        {
        }
        protected function get_have_a_look_url()
        {
        }
        public function handle_revisions_changed($post_has_changed, $last_revision, $post)
        {
        }
    }
}
namespace Elementor\Core\DocumentTypes {
    abstract class PageBase extends \Elementor\Core\Base\Document
    {
        /**
         * Get Properties
         *
         * Return the document configuration properties.
         *
         * @since 2.0.8
         * @access public
         * @static
         *
         * @return array
         */
        public static function get_properties()
        {
        }
        /**
         * @since 2.1.2
         * @access protected
         * @static
         */
        protected static function get_editor_panel_categories()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_css_wrapper_selector()
        {
        }
        /**
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         * @static
         * @param Document $document
         */
        public static function register_hide_title_control($document)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         * @static
         * @param Document $document
         */
        public static function register_style_controls($document)
        {
        }
        public static function get_labels() : array
        {
        }
        /**
         * @since 2.0.0
         * @access public
         * @static
         * @param Document $document
         */
        public static function register_post_fields_control($document)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param array $data
         *
         * @throws \Exception
         */
        public function __construct(array $data = [])
        {
        }
        protected function get_remote_library_config()
        {
        }
    }
}
namespace Elementor\Core\Kits\Documents {
    class Kit extends \Elementor\Core\DocumentTypes\PageBase
    {
        public function __construct(array $data = [])
        {
        }
        public static function get_properties()
        {
        }
        public static function get_type()
        {
        }
        public static function get_title()
        {
        }
        /**
         * @return Tabs\Tab_Base[]
         */
        public function get_tabs()
        {
        }
        /**
         * Retrieve a tab by ID.
         *
         * @param $id
         *
         * @return Tabs\Tab_Base
         */
        public function get_tab($id)
        {
        }
        protected function get_have_a_look_url()
        {
        }
        public static function get_editor_panel_config()
        {
        }
        public function get_css_wrapper_selector()
        {
        }
        public function save($data)
        {
        }
        /**
         * Register a kit settings menu.
         *
         * @param $id
         * @param $class
         */
        public function register_tab($id, $class)
        {
        }
        /**
         * @inheritDoc
         */
        protected function get_initial_config()
        {
        }
        /**
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        protected function get_post_statuses()
        {
        }
        public function add_repeater_row($control_id, $item)
        {
        }
    }
}
namespace Elementor\Core\Admin\UI\Components {
    class Button extends \Elementor\Core\Base\Base_Object
    {
        /**
         * @inheritDoc
         */
        public function get_name()
        {
        }
        public function print_button()
        {
        }
        public function __construct(array $options)
        {
        }
    }
}
namespace Elementor\Core\Admin {
    class Admin_Notices extends \Elementor\Core\Base\Module
    {
        const DEFAULT_EXCLUDED_PAGES = ['plugins.php', 'plugin-install.php', 'plugin-editor.php'];
        // For testing purposes
        public function get_elementor_version()
        {
        }
        public function print_admin_notice(array $options, $exclude_pages = self::DEFAULT_EXCLUDED_PAGES)
        {
        }
        public function admin_notices()
        {
        }
        /**
         * @since 2.9.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Get module name.
         *
         * Retrieve the module name.
         *
         * @since  2.9.0
         * @access public
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
    }
}
namespace Elementor\Core\Admin\Notices {
    abstract class Base_Notice
    {
        /**
         * Determine if the notice should be printed or not.
         *
         * @return boolean
         */
        public abstract function should_print();
        /**
         * Returns the config of the notice itself.
         * based on that config the notice will be printed.
         *
         * @see \Elementor\Core\Admin\Admin_Notices::admin_notices
         *
         * @return array
         */
        public abstract function get_config();
    }
    class Elementor_Dev_Notice extends \Elementor\Core\Admin\Notices\Base_Notice
    {
        /**
         * Notice ID.
         */
        const ID = 'elementor_dev_promote';
        /**
         * Plugin slug to install.
         */
        const PLUGIN_SLUG = 'elementor-beta';
        /**
         * Plugin name.
         */
        const PLUGIN_NAME = 'elementor-beta/elementor-beta.php';
        /**
         * @inheritDoc
         */
        public function should_print()
        {
        }
        /**
         * @inheritDoc
         */
        public function get_config()
        {
        }
        /**
         * Return all the plugins names.
         *
         * This method is protected so it can be mocked in tests.
         *
         * @return array
         */
        protected function get_plugins()
        {
        }
    }
}
namespace Elementor\Core\Admin\Menu {
    abstract class Base extends \Elementor\Core\Base\Base_Object
    {
        protected abstract function get_init_args();
        public function __construct()
        {
        }
        public function get_args($arg = null)
        {
        }
        public function add_submenu($submenu_args)
        {
        }
        protected function get_init_options()
        {
        }
        protected function register_default_submenus()
        {
        }
        protected function register()
        {
        }
    }
    class Main extends \Elementor\Core\Admin\Menu\Base
    {
        protected function get_init_args()
        {
        }
        protected function get_init_options()
        {
        }
        protected function register_default_submenus()
        {
        }
        protected function register()
        {
        }
    }
    class Admin_Menu_Manager
    {
        public function register($item_slug, \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item $item)
        {
        }
        public function unregister($item_slug)
        {
        }
        public function get($item_slug)
        {
        }
        public function get_all()
        {
        }
        public function register_actions()
        {
        }
    }
}
namespace Elementor\Core\Admin\Menu\Interfaces {
    interface Admin_Menu_Item
    {
        public function get_capability();
        public function get_label();
        public function get_parent_slug();
        public function is_visible();
    }
    interface Admin_Menu_Item_Has_Position
    {
        public function get_position();
    }
    interface Admin_Menu_Item_With_Page extends \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item
    {
        public function get_page_title();
        public function render();
    }
}
namespace Elementor\Core\Admin {
    // TODO: Move this class to pro version for better architecture.
    class Canary_Deployment extends \Elementor\Core\Base\Module
    {
        const CURRENT_VERSION = ELEMENTOR_VERSION;
        const PLUGIN_BASE = ELEMENTOR_PLUGIN_BASE;
        /**
         * Get module name.
         *
         * Retrieve the module name.
         *
         * @since  2.6.0
         * @access public
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
        /**
         * Check version.
         *
         * @since 2.6.0
         * @access public
         *
         * @param object $transient Plugin updates data.
         *
         * @return object Plugin updates data.
         */
        public function check_version($transient)
        {
        }
        protected function get_canary_deployment_remote_info($force)
        {
        }
        /**
         * @since 2.6.0
         * @access public
         */
        public function __construct()
        {
        }
    }
    class Admin extends \Elementor\Core\Base\App
    {
        /**
         * Get module name.
         *
         * Retrieve the module name.
         *
         * @since 2.3.0
         * @access public
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
        /**
         * @since 2.2.0
         * @access public
         */
        public function maybe_redirect_to_getting_started()
        {
        }
        /**
         * Enqueue admin scripts.
         *
         * Registers all the admin scripts and enqueues them.
         *
         * Fired by `admin_enqueue_scripts` action.
         *
         * @since 1.0.0
         * @access public
         */
        public function enqueue_scripts()
        {
        }
        /**
         * Enqueue admin styles.
         *
         * Registers all the admin styles and enqueues them.
         *
         * Fired by `admin_enqueue_scripts` action.
         *
         * @since 1.0.0
         * @access public
         */
        public function enqueue_styles()
        {
        }
        /**
         * Print switch mode button.
         *
         * Adds a switch button in post edit screen (which has cpt support). To allow
         * the user to switch from the native WordPress editor to Elementor builder.
         *
         * Fired by `edit_form_after_title` action.
         *
         * @since 1.0.0
         * @access public
         *
         * @param \WP_Post $post The current post object.
         */
        public function print_switch_mode_button($post)
        {
        }
        /**
         * Save post.
         *
         * Flag the post mode when the post is saved.
         *
         * Fired by `save_post` action.
         *
         * @since 1.0.0
         * @access public
         *
         * @param int $post_id Post ID.
         */
        public function save_post($post_id)
        {
        }
        /**
         * Add Elementor post state.
         *
         * Adds a new "Elementor" post state to the post table.
         *
         * Fired by `display_post_states` filter.
         *
         * @since 1.8.0
         * @access public
         *
         * @param array    $post_states An array of post display states.
         * @param \WP_Post $post        The current post object.
         *
         * @return array A filtered array of post display states.
         */
        public function add_elementor_post_state($post_states, $post)
        {
        }
        /**
         * Body status classes.
         *
         * Adds CSS classes to the admin body tag.
         *
         * Fired by `admin_body_class` filter.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $classes Space-separated list of CSS classes.
         *
         * @return string Space-separated list of CSS classes.
         */
        public function body_status_classes($classes)
        {
        }
        /**
         * Plugin action links.
         *
         * Adds action links to the plugin list table
         *
         * Fired by `plugin_action_links` filter.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $links An array of plugin action links.
         *
         * @return array An array of plugin action links.
         */
        public function plugin_action_links($links)
        {
        }
        /**
         * Plugin row meta.
         *
         * Adds row meta links to the plugin list table
         *
         * Fired by `plugin_row_meta` filter.
         *
         * @since 1.1.4
         * @access public
         *
         * @param array  $plugin_meta An array of the plugin's metadata, including
         *                            the version, author, author URI, and plugin URI.
         * @param string $plugin_file Path to the plugin file, relative to the plugins
         *                            directory.
         *
         * @return array An array of plugin row meta links.
         */
        public function plugin_row_meta($plugin_meta, $plugin_file)
        {
        }
        /**
         * Admin footer text.
         *
         * Modifies the "Thank you" text displayed in the admin footer.
         *
         * Fired by `admin_footer_text` filter.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $footer_text The content that will be printed.
         *
         * @return string The content that will be printed.
         */
        public function admin_footer_text($footer_text)
        {
        }
        /**
         * Register dashboard widgets.
         *
         * Adds a new Elementor widgets to WordPress dashboard.
         *
         * Fired by `wp_dashboard_setup` action.
         *
         * @since 1.9.0
         * @access public
         */
        public function register_dashboard_widgets()
        {
        }
        /**
         * Displays the Elementor dashboard widget.
         *
         * Fired by `wp_add_dashboard_widget` function.
         *
         * @since 1.9.0
         * @access public
         */
        public function elementor_dashboard_overview_widget()
        {
        }
        /**
         * Displays the Elementor dashboard widget - header section.
         * Fired by `elementor_dashboard_overview_widget` function.
         *
         * @param bool $show_versions
         * @param bool $is_create_post_enabled
         *
         * @return void
         * @since 3.12.0
         */
        public static function elementor_dashboard_overview_header(bool $show_versions = true, bool $is_create_post_enabled = true)
        {
        }
        /**
         * Displays the Elementor dashboard widget - recently edited section.
         * Fired by `elementor_dashboard_overview_widget` function.
         *
         * @param array $args
         * @param bool $show_heading
         *
         * @return void
         * @since 3.12.0
         */
        public static function elementor_dashboard_overview_recently_edited(array $args = [], bool $show_heading = true)
        {
        }
        /**
         * Displays the Elementor dashboard widget - news and updates section.
         * Fired by `elementor_dashboard_overview_widget` function.
         *
         * @param int $limit_feed
         * @param bool $show_heading
         *
         * @return void
         * @since 3.12.0
         * @access public
         */
        public static function elementor_dashboard_overview_news_updates(int $limit_feed = 0, bool $show_heading = true)
        {
        }
        /**
         * Displays the Elementor dashboard widget - footer section.
         * Fired by `elementor_dashboard_overview_widget` function.
         *
         * @since 3.12.0
         */
        public static function elementor_dashboard_overview_footer()
        {
        }
        /**
         * Get elementor dashboard overview widget footer actions.
         *
         * Retrieves the footer action links displayed in elementor dashboard widget.
         *
         * @since 3.12.0
         * @access public
         */
        public static function static_get_dashboard_overview_widget_footer_actions()
        {
        }
        /**
         * Admin action new post.
         *
         * When a new post action is fired the title is set to 'Elementor' and the post ID.
         *
         * Fired by `admin_action_elementor_new_post` action.
         *
         * @since 1.9.0
         * @access public
         */
        public function admin_action_new_post()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function add_new_template_template()
        {
        }
        public function add_new_floating_elements_template()
        {
        }
        public function enqueue_new_floating_elements_scripts()
        {
        }
        /**
         * @access public
         */
        public function enqueue_new_template_scripts()
        {
        }
        /**
         * @since 2.6.0
         * @access public
         */
        public function add_beta_tester_template()
        {
        }
        /**
         * @access public
         */
        public function enqueue_beta_tester_scripts()
        {
        }
        public function init_floating_elements()
        {
        }
        /**
         * @access public
         */
        public function init_new_template()
        {
        }
        public function version_update_warning($current_version, $new_version)
        {
        }
        /**
         * @access public
         */
        public function init_beta_tester($current_screen)
        {
        }
        /**
         * Admin constructor.
         *
         * Initializing Elementor in WordPress admin.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function get_init_settings()
        {
        }
        public function register_ajax_hints($ajax_manager)
        {
        }
        public function ajax_set_image_optimization_campaign($request)
        {
        }
        public function ajax_site_mailer_campaign($request)
        {
        }
    }
    class Feedback extends \Elementor\Core\Base\Module
    {
        /**
         * @since 2.2.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Get module name.
         *
         * Retrieve the module name.
         *
         * @since  1.7.0
         * @access public
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
        /**
         * Enqueue feedback dialog scripts.
         *
         * Registers the feedback dialog scripts and enqueues them.
         *
         * @since 1.0.0
         * @access public
         */
        public function enqueue_feedback_dialog_scripts()
        {
        }
        /**
         * Print deactivate feedback dialog.
         *
         * Display a dialog box to ask the user why he deactivated Elementor.
         *
         * Fired by `admin_footer` filter.
         *
         * @since 1.0.0
         * @access public
         */
        public function print_deactivate_feedback_dialog()
        {
        }
        /**
         * Ajax elementor deactivate feedback.
         *
         * Send the user feedback when Elementor is deactivated.
         *
         * Fired by `wp_ajax_elementor_deactivate_feedback` action.
         *
         * @since 1.0.0
         * @access public
         */
        public function ajax_elementor_deactivate_feedback()
        {
        }
    }
}
namespace Elementor\Core\DynamicTags {
    /**
     * Elementor base tag.
     *
     * An abstract class to register new Elementor tags.
     *
     * @since 2.0.0
     * @abstract
     */
    abstract class Base_Tag extends \Elementor\Controls_Stack
    {
        /**
         * @since 2.0.0
         * @access public
         * @static
         */
        public static final function get_type()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         * @abstract
         */
        public abstract function get_categories();
        /**
         * @since 2.0.0
         * @access public
         * @abstract
         */
        public abstract function get_group();
        /**
         * @since 2.0.0
         * @access public
         * @abstract
         */
        public abstract function get_title();
        /**
         * @since 2.0.0
         * @access public
         * @abstract
         *
         * @param array $options
         */
        public abstract function get_content(array $options = []);
        /**
         * @since 2.0.0
         * @access public
         * @abstract
         */
        public abstract function get_content_type();
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_panel_template_setting_key()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function is_settings_required()
        {
        }
        /**
         * @since 2.0.9
         * @access public
         */
        public function get_editor_config()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function print_panel_template()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public final function get_unique_name()
        {
        }
        /**
         * @since 2.0.0
         * @access protected
         */
        protected function register_advanced_section()
        {
        }
        /**
         * @since 2.0.0
         * @access protected
         */
        protected final function init_controls()
        {
        }
    }
    /**
     * Elementor base data tag.
     *
     * An abstract class to register new Elementor data tags.
     *
     * @since 2.0.0
     * @abstract
     */
    abstract class Data_Tag extends \Elementor\Core\DynamicTags\Base_Tag
    {
        /**
         * @since 2.0.0
         * @access protected
         * @abstract
         *
         * @param array $options
         */
        protected abstract function get_value(array $options = []);
        /**
         * @since 2.0.0
         * @access public
         */
        public final function get_content_type()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param array $options
         *
         * @return mixed
         */
        public function get_content(array $options = [])
        {
        }
    }
}
namespace Elementor\Core\Files\CSS {
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
         * Elementor CSS file generated status.
         *
         * The parsing result after generating CSS file.
         */
        const CSS_STATUS_FILE = 'file';
        /**
         * Elementor inline CSS status.
         *
         * The parsing result after generating inline CSS.
         */
        const CSS_STATUS_INLINE = 'inline';
        /**
         * Elementor CSS empty status.
         *
         * The parsing result when an empty CSS returned.
         */
        const CSS_STATUS_EMPTY = 'empty';
        /**
         * \Elementor\Stylesheet object.
         *
         * Holds the CSS file stylesheet instance.
         *
         * @access protected
         *
         * @var \Elementor\Stylesheet
         */
        protected $stylesheet_obj;
        /**
         * Get CSS file name.
         *
         * Retrieve the CSS file name.
         *
         * @since 1.6.0
         * @access public
         * @abstract
         */
        public abstract function get_name();
        protected function is_global_parsing_supported()
        {
        }
        /**
         * Use external file.
         *
         * Whether to use external CSS file of not. When there are new schemes or settings
         * updates.
         *
         * @since 1.9.0
         * @access protected
         *
         * @return bool True if the CSS requires an update, False otherwise.
         */
        protected function use_external_file()
        {
        }
        /**
         * Update the CSS file.
         *
         * Delete old CSS, parse the CSS, save the new file and update the database.
         *
         * This method also sets the CSS status to be used later on in the render posses.
         *
         * @since 1.2.0
         * @access public
         */
        public function update()
        {
        }
        /**
         * @since 2.1.0
         * @access public
         */
        public function write()
        {
        }
        /**
         * @since 3.0.0
         * @access public
         */
        public function delete()
        {
        }
        /**
         * Get Responsive Control Duplication Mode
         *
         * @since 3.4.0
         *
         * @return string
         */
        protected function get_responsive_control_duplication_mode()
        {
        }
        /**
         * Enqueue CSS.
         *
         * Either enqueue the CSS file in Elementor or add inline style.
         *
         * This method is also responsible for loading the fonts.
         *
         * @since 1.2.0
         * @access public
         */
        public function enqueue()
        {
        }
        /**
         * Print CSS.
         *
         * Output the final CSS inside the `<style>` tags and all the frontend fonts in
         * use.
         *
         * @since 1.9.4
         * @access public
         */
        public function print_css()
        {
        }
        /**
         * Add control rules.
         *
         * Parse the CSS for all the elements inside any given control.
         *
         * This method recursively renders the CSS for all the selectors in the control.
         *
         * @since 1.2.0
         * @access public
         *
         * @param array    $control        The controls.
         * @param array    $controls_stack The controls stack.
         * @param callable $value_callback Callback function for the value.
         * @param array    $placeholders   Placeholders.
         * @param array    $replacements   Replacements.
         * @param array    $values         Global Values.
         */
        public function add_control_rules(array $control, array $controls_stack, callable $value_callback, array $placeholders, array $replacements, array $values = [])
        {
        }
        protected function unit_has_custom_selector($control, $value)
        {
        }
        /**
         * @param array    $control
         * @param mixed    $value
         * @param array    $controls_stack
         * @param callable $value_callback
         * @param string   $placeholder
         * @param string   $parser_control_name
         *
         * @return string
         */
        public function parse_property_placeholder(array $control, $value, array $controls_stack, $value_callback, $placeholder, $parser_control_name = null)
        {
        }
        /**
         * Get the fonts.
         *
         * Retrieve the list of fonts.
         *
         * @since 1.9.0
         * @access public
         *
         * @return array Fonts.
         */
        public function get_fonts()
        {
        }
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
        /**
         * Add controls stack style rules.
         *
         * Parse the CSS for all the elements inside any given controls stack.
         *
         * This method recursively renders the CSS for all the child elements in the stack.
         *
         * @since 1.6.0
         * @access public
         *
         * @param Controls_Stack $controls_stack The controls stack.
         * @param array          $controls       Controls array.
         * @param array          $values         Values array.
         * @param array          $placeholders   Placeholders.
         * @param array          $replacements   Replacements.
         * @param array          $all_controls   All controls.
         */
        public function add_controls_stack_style_rules(\Elementor\Controls_Stack $controls_stack, array $controls, array $values, array $placeholders, array $replacements, array $all_controls = null)
        {
        }
        /**
         * Get file handle ID.
         *
         * Retrieve the file handle ID.
         *
         * @since 1.2.0
         * @access protected
         * @abstract
         *
         * @return string CSS file handle ID.
         */
        protected abstract function get_file_handle_id();
        /**
         * Render CSS.
         *
         * Parse the CSS.
         *
         * @since 1.2.0
         * @access protected
         * @abstract
         */
        protected abstract function render_css();
        protected function get_default_meta()
        {
        }
        /**
         * Get enqueue dependencies.
         *
         * Retrieve the name of the stylesheet used by `wp_enqueue_style()`.
         *
         * @since 1.2.0
         * @access protected
         *
         * @return array Name of the stylesheet.
         */
        protected function get_enqueue_dependencies()
        {
        }
        /**
         * Get inline dependency.
         *
         * Retrieve the name of the stylesheet used by `wp_add_inline_style()`.
         *
         * @since 1.2.0
         * @access protected
         *
         * @return string Name of the stylesheet.
         */
        protected function get_inline_dependency()
        {
        }
        /**
         * Is update required.
         *
         * Whether the CSS requires an update. When there are new schemes or settings
         * updates.
         *
         * @since 1.2.0
         * @access protected
         *
         * @return bool True if the CSS requires an update, False otherwise.
         */
        protected function is_update_required()
        {
        }
        /**
         * Parse CSS.
         *
         * Parsing the CSS file.
         *
         * @since 1.2.0
         * @access protected
         */
        protected function parse_content()
        {
        }
        /**
         * Add control style rules.
         *
         * Register new style rules for the control.
         *
         * @since 1.6.0
         * @access private
         *
         * @param array $control      The control.
         * @param array $values       Values array.
         * @param array $controls     The controls stack.
         * @param array $placeholders Placeholders.
         * @param array $replacements Replacements.
         */
        protected function add_control_style_rules(array $control, array $values, array $controls, array $placeholders, array $replacements)
        {
        }
        /**
         * Add repeater control style rules.
         *
         * Register new style rules for the repeater control.
         *
         * @since 2.0.0
         * @access private
         *
         * @param Controls_Stack $controls_stack   The control stack.
         * @param array          $repeater_control The repeater control.
         * @param array          $repeater_values  Repeater values array.
         * @param array          $placeholders     Placeholders.
         * @param array          $replacements     Replacements.
         */
        protected function add_repeater_control_style_rules(\Elementor\Controls_Stack $controls_stack, array $repeater_control, array $repeater_values, array $placeholders, array $replacements)
        {
        }
        /**
         * Add dynamic control style rules.
         *
         * Register new style rules for the dynamic control.
         *
         * @since 2.0.0
         * @access private
         *
         * @param array  $control The control.
         * @param string $value   The value.
         */
        protected function add_dynamic_control_style_rules(array $control, $value)
        {
        }
        protected final function get_active_controls(\Elementor\Controls_Stack $controls_stack, array $controls = null, array $settings = null)
        {
        }
        public final function get_style_controls(\Elementor\Controls_Stack $controls_stack, array $controls = null, array $settings = null)
        {
        }
    }
    /**
     * Elementor post CSS file.
     *
     * Elementor CSS file handler class is responsible for generating the single
     * post CSS file.
     *
     * @since 1.2.0
     */
    class Post extends \Elementor\Core\Files\CSS\Base
    {
        /**
         * Elementor post CSS file prefix.
         */
        const FILE_PREFIX = 'post-';
        const META_KEY = '_elementor_css';
        protected function is_global_parsing_supported()
        {
        }
        /**
         * Post CSS file constructor.
         *
         * Initializing the CSS file of the post. Set the post ID and initiate the stylesheet.
         *
         * @since 1.2.0
         * @access public
         *
         * @param int $post_id Post ID.
         */
        public function __construct($post_id)
        {
        }
        /**
         * Get CSS file name.
         *
         * Retrieve the CSS file name.
         *
         * @since 1.6.0
         * @access public
         *
         * @return string CSS file name.
         */
        public function get_name()
        {
        }
        /**
         * Get post ID.
         *
         * Retrieve the ID of current post.
         *
         * @since 1.2.0
         * @access public
         *
         * @return int Post ID.
         */
        public function get_post_id()
        {
        }
        /**
         * Get unique element selector.
         *
         * Retrieve the unique selector for any given element.
         *
         * @since 1.2.0
         * @access public
         *
         * @param Element_Base $element The element.
         *
         * @return string Unique element selector.
         */
        public function get_element_unique_selector(\Elementor\Element_Base $element)
        {
        }
        /**
         * Load meta data.
         *
         * Retrieve the post CSS file meta data.
         *
         * @since 1.2.0
         * @access protected
         *
         * @return array Post CSS file meta data.
         */
        protected function load_meta()
        {
        }
        /**
         * Update meta data.
         *
         * Update the global CSS file meta data.
         *
         * @since 1.2.0
         * @access protected
         *
         * @param array $meta New meta data.
         */
        protected function update_meta($meta)
        {
        }
        /**
         * Delete meta.
         *
         * Delete the file meta data.
         *
         * @since  2.1.0
         * @access protected
         */
        protected function delete_meta()
        {
        }
        /**
         * Get post data.
         *
         * Retrieve raw post data from the database.
         *
         * @since 1.9.0
         * @access protected
         *
         * @return array Post data.
         */
        protected function get_data()
        {
        }
        /**
         * Render CSS.
         *
         * Parse the CSS for all the elements.
         *
         * @since 1.2.0
         * @access protected
         */
        protected function render_css()
        {
        }
        /**
         * Enqueue CSS.
         *
         * Enqueue the post CSS file in Elementor.
         *
         * This method ensures that the post was actually built with elementor before
         * enqueueing the post CSS file.
         *
         * @since 1.2.2
         * @access public
         */
        public function enqueue()
        {
        }
        /**
         * Add controls-stack style rules.
         *
         * Parse the CSS for all the elements inside any given controls stack.
         *
         * This method recursively renders the CSS for all the child elements in the stack.
         *
         * @since 1.6.0
         * @access public
         *
         * @param Controls_Stack $controls_stack The controls stack.
         * @param array          $controls       Controls array.
         * @param array          $values         Values array.
         * @param array          $placeholders   Placeholders.
         * @param array          $replacements   Replacements.
         * @param array          $all_controls   All controls.
         */
        public function add_controls_stack_style_rules(\Elementor\Controls_Stack $controls_stack, array $controls, array $values, array $placeholders, array $replacements, array $all_controls = null)
        {
        }
        /**
         * Get enqueue dependencies.
         *
         * Retrieve the name of the stylesheet used by `wp_enqueue_style()`.
         *
         * @since 1.2.0
         * @access protected
         *
         * @return array Name of the stylesheet.
         */
        protected function get_enqueue_dependencies()
        {
        }
        /**
         * Get inline dependency.
         *
         * Retrieve the name of the stylesheet used by `wp_add_inline_style()`.
         *
         * @since 1.2.0
         * @access protected
         *
         * @return string Name of the stylesheet.
         */
        protected function get_inline_dependency()
        {
        }
        /**
         * Get file handle ID.
         *
         * Retrieve the handle ID for the post CSS file.
         *
         * @since 1.2.0
         * @access protected
         *
         * @return string CSS file handle ID.
         */
        protected function get_file_handle_id()
        {
        }
        /**
         * Render styles.
         *
         * Parse the CSS for any given element.
         *
         * @since 1.2.0
         * @access protected
         *
         * @param Element_Base $element The element.
         */
        protected function render_styles(\Elementor\Element_Base $element)
        {
        }
    }
    abstract class Post_Local_Cache extends \Elementor\Core\Files\CSS\Post
    {
        protected abstract function get_post_id_for_data();
        public function is_update_required()
        {
        }
        protected function load_meta()
        {
        }
        protected function delete_meta()
        {
        }
        protected function update_meta($meta)
        {
        }
        protected function get_data()
        {
        }
    }
}
namespace Elementor\Core\DynamicTags {
    class Dynamic_CSS extends \Elementor\Core\Files\CSS\Post_Local_Cache
    {
        protected function get_post_id_for_data()
        {
        }
        protected function is_global_parsing_supported()
        {
        }
        protected function render_styles(\Elementor\Element_Base $element)
        {
        }
        /**
         * Dynamic_CSS constructor.
         *
         * @since 2.0.13
         * @access public
         *
         * @param int $post_id Post ID
         * @param Post_CSS $post_css_file
         */
        public function __construct($post_id, \Elementor\Core\Files\CSS\Post $post_css_file)
        {
        }
        /**
         * @since 2.0.13
         * @access public
         */
        public function get_name()
        {
        }
        /**
         * Get Responsive Control Duplication Mode
         *
         * @since 3.4.0
         *
         * @return string
         */
        protected function get_responsive_control_duplication_mode()
        {
        }
        /**
         * @since 2.0.13
         * @access protected
         */
        protected function use_external_file()
        {
        }
        /**
         * @since 2.0.13
         * @access protected
         */
        protected function get_file_handle_id()
        {
        }
        /**
         * @since 2.0.13
         * @access public
         */
        public function add_controls_stack_style_rules(\Elementor\Controls_Stack $controls_stack, array $controls, array $values, array $placeholders, array $replacements, array $all_controls = null)
        {
        }
    }
    /**
     * Elementor tag.
     *
     * An abstract class to register new Elementor tag.
     *
     * @since 2.0.0
     * @abstract
     */
    abstract class Tag extends \Elementor\Core\DynamicTags\Base_Tag
    {
        const WRAPPED_TAG = false;
        /**
         * @since 2.0.0
         * @access public
         *
         * @param array $options
         *
         * @return string
         */
        public function get_content(array $options = [])
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public final function get_content_type()
        {
        }
        /**
         * @since 2.0.9
         * @access public
         */
        public function get_editor_config()
        {
        }
        /**
         * @since 2.0.0
         * @access protected
         */
        protected function register_advanced_section()
        {
        }
    }
    class Manager
    {
        const TAG_LABEL = 'elementor-tag';
        const MODE_RENDER = 'render';
        const MODE_REMOVE = 'remove';
        const DYNAMIC_SETTING_KEY = '__dynamic__';
        /**
         * Dynamic tags manager constructor.
         *
         * Initializing Elementor dynamic tags manager.
         *
         * @since 2.0.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Parse dynamic tags text.
         *
         * Receives the dynamic tag text, and returns a single value or multiple values
         * from the tag callback function.
         *
         * @since 2.0.0
         * @access public
         *
         * @param string   $text           Dynamic tag text.
         * @param array    $settings       The dynamic tag settings.
         * @param callable $parse_callback The functions that renders the dynamic tag.
         *
         * @return string|string[]|mixed A single string or an array of strings with
         *                               the return values from each tag callback
         *                               function.
         */
        public function parse_tags_text($text, array $settings, callable $parse_callback)
        {
        }
        /**
         * Parse dynamic tag text.
         *
         * Receives the dynamic tag text, and returns the value from the callback
         * function.
         *
         * @since 2.0.0
         * @access public
         *
         * @param string   $tag_text       Dynamic tag text.
         * @param array    $settings       The dynamic tag settings.
         * @param callable $parse_callback The functions that renders the dynamic tag.
         *
         * @return string|array|mixed If the tag was not found an empty string or an
         *                            empty array will be returned, otherwise the
         *                            return value from the tag callback function.
         */
        public function parse_tag_text($tag_text, array $settings, callable $parse_callback)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param string $tag_text
         *
         * @return array|null
         */
        public function tag_text_to_tag_data($tag_text)
        {
        }
        /**
         * Dynamic tag to text.
         *
         * Retrieve the shortcode that represents the dynamic tag.
         *
         * @since 2.0.0
         * @access public
         *
         * @param Base_Tag $tag An instance of the dynamic tag.
         *
         * @return string The shortcode that represents the dynamic tag.
         */
        public function tag_to_text(\Elementor\Core\DynamicTags\Base_Tag $tag)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         * @param string $tag_id
         * @param string $tag_name
         * @param array  $settings
         *
         * @return string
         */
        public function tag_data_to_tag_text($tag_id, $tag_name, array $settings = [])
        {
        }
        /**
         * @since 2.0.0
         * @access public
         * @param string $tag_id
         * @param string $tag_name
         * @param array  $settings
         *
         * @return Tag|null
         */
        public function create_tag($tag_id, $tag_name, array $settings = [])
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param       $tag_id
         * @param       $tag_name
         * @param array $settings
         *
         * @return null|string
         */
        public function get_tag_data_content($tag_id, $tag_name, array $settings = [])
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param $tag_name
         *
         * @return mixed|null
         */
        public function get_tag_info($tag_name)
        {
        }
        /**
         * @since 2.0.9
         * @access public
         */
        public function get_tags()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         * @deprecated 3.5.0 Use `register()` method instead.
         *
         * @param string $class
         */
        public function register_tag($class)
        {
        }
        /**
         * Register a new Dynamic Tag.
         *
         * @param Base_Tag $dynamic_tag_instance
         *
         * @return void
         * @since  3.5.0
         * @access public
         *
         */
        public function register(\Elementor\Core\DynamicTags\Base_Tag $dynamic_tag_instance)
        {
        }
        /**
         * @since 2.0.9
         * @access public
         * @deprecated 3.5.0 Use `unregister()` method instead.
         *
         * @param string $tag_name
         */
        public function unregister_tag($tag_name)
        {
        }
        /**
         * Unregister a dynamic tag.
         *
         * @since 3.5.0
         * @access public
         *
         * @param string $tag_name Dynamic Tag name to unregister.
         *
         * @return void
         */
        public function unregister($tag_name)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param       $group_name
         * @param array $group_settings
         */
        public function register_group($group_name, array $group_settings)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function print_templates()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_tags_config()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_config()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @throws \Exception If post ID is missing.
         * @throws \Exception If current user don't have permissions to edit the post.
         */
        public function ajax_render_tags($data)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param $mode
         */
        public function set_parsing_mode($mode)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_parsing_mode()
        {
        }
        /**
         * @since 2.1.0
         * @access public
         * @param Post $css_file
         */
        public function after_enqueue_post_css($css_file)
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function register_ajax_actions(\Elementor\Core\Common\Modules\Ajax\Module $ajax)
        {
        }
    }
}
namespace Elementor\Core\Utils\Svg {
    /**
     * Elementor SVG Sanitizer.
     *
     * A class that is responsible for sanitizing SVG files.
     *
     * @since 3.16.0
     */
    class Svg_Sanitizer
    {
        /**
         * Sanitize File
         *
         * @since 3.16.0
         * @access public
         *
         * @param $filename
         * @return bool
         */
        public function sanitize_file($filename)
        {
        }
        /**
         * Sanitize
         *
         * @since 3.16.0
         * @access public
         *
         * @param $content
         * @return bool|string
         */
        public function sanitize($content)
        {
        }
    }
}
namespace Elementor\Core\Utils {
    class Static_Collection
    {
        /**
         * The current Collection instance.
         *
         * @var Collection
         */
        protected $collection;
        /**
         * Return only unique values.
         *
         * @var bool
         */
        protected $unique_values = false;
        /**
         * @inheritDoc
         */
        public function __construct(array $items = [], $unique_values = false)
        {
        }
        /**
         * Since this class is a wrapper, every call will be forwarded to wrapped class.
         * Most of the collection methods returns a new collection instance, and therefore
         * it will be assigned as the current collection instance after executing any method.
         *
         * @param string $name
         * @param array $arguments
         */
        public function __call($name, $arguments)
        {
        }
    }
    class Str
    {
        /**
         * Convert a non-latin URL to an IDN one.
         * Note: Max length is 64 chars.
         *
         * @param string $url - A URL to encode.
         *
         * @return string - IDN encoded URL ( e.g. `http://é.com` will be encoded to `http://xn--9ca.com` ).
         */
        public static function encode_idn_url($url)
        {
        }
        /**
         * Checks if a string ends with a given substring
         *
         * @param $haystack
         * @param $needle
         * @return bool
         */
        public static function ends_with($haystack, $needle)
        {
        }
    }
    class Collection implements \ArrayAccess, \Countable, \IteratorAggregate
    {
        /**
         * The items contained in the collection.
         *
         * @var array
         */
        protected $items;
        /**
         * Collection constructor.
         *
         * @param array $items
         */
        public function __construct(array $items = [])
        {
        }
        /**
         * @param array $items
         *
         * @return static
         */
        public static function make(array $items = [])
        {
        }
        /**
         * @param callable|null $callback
         *
         * @return $this
         */
        public function filter(callable $callback = null)
        {
        }
        /**
         * @param $items
         *
         * @return $this
         */
        public function merge($items)
        {
        }
        /**
         * Union the collection with the given items.
         *
         * @param array $items
         *
         * @return $this
         */
        public function union(array $items)
        {
        }
        /**
         * Merge array recursively
         *
         * @param $items
         *
         * @return $this
         */
        public function merge_recursive($items)
        {
        }
        /**
         * Replace array recursively
         *
         * @param $items
         *
         * @return $this
         */
        public function replace_recursive($items)
        {
        }
        /**
         * Implode the items
         *
         * @param $glue
         *
         * @return string
         */
        public function implode($glue)
        {
        }
        /**
         * Run a map over each of the items.
         *
         * @param  callable  $callback
         * @return $this
         */
        public function map(callable $callback)
        {
        }
        /**
         * Run a callback over each of the items.
         *
         * @param callable $callback
         * @return $this
         */
        public function each(callable $callback)
        {
        }
        /**
         * @param callable $callback
         * @param null     $initial
         *
         * @return mixed|null
         */
        public function reduce(callable $callback, $initial = null)
        {
        }
        /**
         * @param callable $callback
         *
         * @return $this
         */
        public function map_with_keys(callable $callback)
        {
        }
        /**
         * Get all items except for those with the specified keys.
         *
         * @param array $keys
         *
         * @return $this
         */
        public function except(array $keys)
        {
        }
        /**
         * Get the items with the specified keys.
         *
         * @param array $keys
         *
         * @return $this
         */
        public function only(array $keys)
        {
        }
        /**
         * Run over the collection to get specific prop from the collection item.
         *
         * @param $key
         *
         * @return $this
         */
        public function pluck($key)
        {
        }
        /**
         * Group the collection items by specific key in each collection item.
         *
         * @param $group_by
         *
         * @return $this
         */
        public function group_by($group_by)
        {
        }
        /**
         * Sort keys
         *
         * @param false $descending
         *
         * @return $this
         */
        public function sort_keys($descending = false)
        {
        }
        /**
         * Get specific item from the collection.
         *
         * @param      $key
         * @param null $default
         *
         * @return mixed|null
         */
        public function get($key, $default = null)
        {
        }
        /**
         * Get the first item.
         *
         * @param null $default
         *
         * @return mixed|null
         */
        public function first($default = null)
        {
        }
        /**
         * Find an element from the items.
         *
         * @param callable $callback
         * @param null     $default
         *
         * @return mixed|null
         */
        public function find(callable $callback, $default = null)
        {
        }
        /**
         * @param callable|string|int $value
         *
         * @return bool
         */
        public function contains($value)
        {
        }
        /**
         * Run array_diff between the collection and other array or collection.
         *
         * @param $filter
         *
         * @return $this
         */
        public function diff($filter)
        {
        }
        /**
         * Make sure all the values inside the array are uniques.
         *
         * @param null|string|string[] $keys
         *
         * @return $this
         */
        public function unique($keys = null)
        {
        }
        /**
         * @return array
         */
        public function keys()
        {
        }
        /**
         * @return bool
         */
        public function is_empty()
        {
        }
        /**
         * @return array
         */
        public function all()
        {
        }
        /**
         * @return array
         */
        public function values()
        {
        }
        /**
         * Support only one level depth.
         *
         * @return $this
         */
        public function flatten()
        {
        }
        /**
         * @param ...$values
         *
         * @return $this
         */
        public function push(...$values)
        {
        }
        public function prepend(...$values)
        {
        }
        public function some(callable $callback)
        {
        }
        /**
         * @param mixed $offset
         *
         * @return bool
         */
        #[\ReturnTypeWillChange]
        public function offsetExists($offset)
        {
        }
        /**
         * @param mixed $offset
         *
         * @return mixed
         */
        #[\ReturnTypeWillChange]
        public function offsetGet($offset)
        {
        }
        /**
         * @param mixed $offset
         * @param mixed $value
         */
        #[\ReturnTypeWillChange]
        public function offsetSet($offset, $value)
        {
        }
        /**
         * @param mixed $offset
         */
        #[\ReturnTypeWillChange]
        public function offsetUnset($offset)
        {
        }
        /**
         * @return \ArrayIterator|\Traversable
         */
        #[\ReturnTypeWillChange]
        public function getIterator()
        {
        }
        /**
         * @return int|void
         */
        #[\ReturnTypeWillChange]
        public function count()
        {
        }
    }
    class Assets_Config_Provider extends \Elementor\Core\Utils\Collection
    {
        /**
         * @param callable $path_resolver
         *
         * @return $this
         */
        public function set_path_resolver(callable $path_resolver)
        {
        }
        /**
         * Load asset config from a file into the collection.
         *
         * @param $key
         * @param $path
         *
         * @return $this
         */
        public function load($key, $path = null)
        {
        }
    }
}
namespace elementor\core\utils {
    class Hints
    {
        const INFO = 'info';
        const SUCCESS = 'success';
        const WARNING = 'warning';
        const DANGER = 'danger';
        const DEFINED = 'defined';
        const DISMISSED = 'dismissed';
        const CAPABILITY = 'capability';
        const PLUGIN_INSTALLED = 'plugin_installed';
        const PLUGIN_ACTIVE = 'plugin_active';
        /**
         * get_notice_types
         * @return string[]
         */
        public static function get_notice_types() : array
        {
        }
        /**
         * get_hints
         *
         * @param $hint_key
         *
         * @return array|string[]|\string[][]
         */
        public static function get_hints($hint_key = null) : array
        {
        }
        /**
         * get_notice_icon
         * @return string
         */
        public static function get_notice_icon() : string
        {
        }
        /**
         * get_notice_template
         *
         * Print or Retrieve the notice template.
         * @param array $notice
         * @param bool $return
         *
         * @return string|void
         */
        public static function get_notice_template(array $notice, bool $return = false)
        {
        }
        /**
         * get_plugin_install_url
         * @param $plugin_slug
         *
         * @return string
         */
        public static function get_plugin_install_url($plugin_slug) : string
        {
        }
        /**
         * get_plugin_activate_url
         * @param $plugin_slug
         *
         * @return string
         */
        public static function get_plugin_activate_url($plugin_slug) : string
        {
        }
        /**
         * is_dismissed
         * @param $key
         *
         * @return bool
         */
        public static function is_dismissed($key) : bool
        {
        }
        /**
         * should_display_hint
         * @param $hint_key
         *
         * @return bool
         */
        public static function should_display_hint($hint_key) : bool
        {
        }
        /**
         * is_plugin_installed
         * @param $plugin
         *
         * @return bool
         */
        public static function is_plugin_installed($plugin) : bool
        {
        }
        /**
         * is_plugin_active
         * @param $plugin
         *
         * @return bool
         */
        public static function is_plugin_active($plugin) : bool
        {
        }
        /**
         * get_plugin_action_url
         * @param $plugin
         *
         * @return string
         */
        public static function get_plugin_action_url($plugin) : string
        {
        }
        /**
         * get_notice_allowed_html
         * @return array[]
         */
        public static function get_notice_allowed_html() : array
        {
        }
    }
}
namespace Elementor\Core\Utils {
    class Plugins_Manager
    {
        public function __construct($upgrader = null)
        {
        }
        /**
         * Install plugin or an array of plugins.
         *
         * @since 3.6.2
         *
         * @param string|array $plugins
         * @return array [ 'succeeded' => [] , 'failed' => [] ]
         */
        public function install($plugins)
        {
        }
        /**
         * Activate plugin or array off plugins.
         *
         * @since 3.6.2
         *
         * @param array|string $plugins
         * @return array [ 'succeeded' => [] , 'failed' => [] ]
         */
        public function activate($plugins)
        {
        }
    }
    /**
     * Force translation to use a specific locale.
     *
     * A hacky class to force any translation functions in the call-stack between the
     * `force()` & `reset()` methods to use a specific locale.
     */
    class Force_Locale
    {
        public function __construct($new_locale, $original_locale = null)
        {
        }
        /**
         * Force the translations to use a specific locale.
         *
         * @return void
         */
        public function force()
        {
        }
        /**
         * Restore the original locale and cleanup filters, etc.
         *
         * @return void
         */
        public function restore()
        {
        }
    }
    class Http extends \WP_Http
    {
        /**
         * Pass multiple urls to implements a fallback machine when one of the urls
         * is sending an error or not exists anymore.
         *
         * @param array $urls
         * @param array $args
         *
         * @return array|\WP_Error|null
         */
        public function request_with_fallback(array $urls, $args = [])
        {
        }
    }
}
namespace Elementor\Core\Utils {
    abstract class Isolation_Manager
    {
        /**
         * @param string $adapter_name
         */
        public static function get_adapter(string $adapter_name)
        {
        }
    }
    class Version
    {
        const PART_MAJOR_1 = 'major1';
        const PART_MAJOR_2 = 'major2';
        const PART_PATCH = 'patch';
        const PART_STAGE = 'stage';
        /**
         * First number of a version 0.x.x
         *
         * @var string
         */
        public $major1;
        /**
         * Second number of a version x.0.x
         *
         * @var string
         */
        public $major2;
        /**
         * Third number of a version x.x.0
         *
         * @var string
         */
        public $patch;
        /**
         * The stage of a version x.x.x-stage.
         * e.g: x.x.x-dev1, x.x.x-beta3, x.x.x-rc
         *
         * @var string|null
         */
        public $stage;
        /**
         * Version constructor.
         *
         * @param $major1
         * @param $major2
         * @param $patch
         * @param $stage
         */
        public function __construct($major1, $major2, $patch, $stage = null)
        {
        }
        /**
         * Create Version instance.
         *
         * @param string $major1
         * @param string $major2
         * @param string $patch
         * @param null   $stage
         *
         * @return static
         */
        public static function create($major1 = '0', $major2 = '0', $patch = '0', $stage = null)
        {
        }
        /**
         * Checks if the current version string is valid.
         *
         * @param $version
         *
         * @return bool
         */
        public static function is_valid_version($version)
        {
        }
        /**
         * Creates a Version instance from a string.
         *
         * @param      $version
         * @param bool $should_validate
         *
         * @return static
         * @throws \Exception
         */
        public static function create_from_string($version, $should_validate = true)
        {
        }
        /**
         * Compare the current version instance with another version.
         *
         * @param        $operator
         * @param        $version
         * @param string $part
         *
         * @return bool
         * @throws \Exception
         */
        public function compare($operator, $version, $part = self::PART_STAGE)
        {
        }
        /**
         * Implode the version and return it as string.
         *
         * @return string
         */
        public function __toString()
        {
        }
    }
    /**
     * Elementor exceptions.
     *
     * Elementor exceptions handler class is responsible for handling exceptions.
     *
     * @since 2.0.0
     */
    class Exceptions
    {
        /**
         * HTTP status code for bad request error.
         */
        const BAD_REQUEST = 400;
        /**
         * HTTP status code for unauthorized access error.
         */
        const UNAUTHORIZED = 401;
        /**
         * HTTP status code for forbidden access error.
         */
        const FORBIDDEN = 403;
        /**
         * HTTP status code for resource that could not be found.
         */
        const NOT_FOUND = 404;
        /**
         * HTTP status code for internal server error.
         */
        const INTERNAL_SERVER_ERROR = 500;
    }
    class Assets_Translation_Loader
    {
        public static function for_handles(array $handles, $domain = null, $replace_callback = null)
        {
        }
    }
}
namespace Elementor\Core {
    /**
     * This class is responsible for the interaction with WordPress Core API.
     * The main benefit is making it easy to mock in testing
     * and it can help to create unit tests without the hustle of mocking WordPress itself.
     */
    class Wp_Api
    {
        /**
         * @return Collection
         */
        public function get_plugins()
        {
        }
        /**
         * @return Collection
         */
        public function get_active_plugins()
        {
        }
        /**
         * @return object|array
         */
        public function plugins_api($action, $args)
        {
        }
        /**
         * @return bool
         */
        public function is_plugin_active($plugin)
        {
        }
        /**
         * @return bool|int|null|true
         */
        public function activate_plugin($plugin)
        {
        }
        public function wp_attachment_is_image($post = null)
        {
        }
    }
}
namespace Elementor {
    /**
     * Elementor settings page.
     *
     * An abstract class that provides the needed properties and methods to handle
     * WordPress dashboard settings pages in inheriting classes.
     *
     * @since 1.0.0
     * @abstract
     */
    abstract class Settings_Page
    {
        /**
         * Settings page ID.
         */
        const PAGE_ID = '';
        /**
         * Create tabs.
         *
         * Return the settings page tabs, sections and fields.
         *
         * @since 1.5.0
         * @access protected
         * @abstract
         */
        protected abstract function create_tabs();
        /**
         * Get settings page title.
         *
         * Retrieve the title for the settings page.
         *
         * @since 1.5.0
         * @access protected
         * @abstract
         */
        protected abstract function get_page_title();
        /**
         * Get settings page URL.
         *
         * Retrieve the URL of the settings page.
         *
         * @since 1.5.0
         * @access public
         * @static
         *
         * @return string Settings page URL.
         */
        public static final function get_url()
        {
        }
        /**
         * Get settings tab URL.
         *
         * Retrieve the URL of a specific tab in the settings page.
         *
         * @since 3.23.0
         * @access public
         * @static
         *
         * @param string $tab_id The ID of the settings tab.
         *
         * @return string Settings tab URL.
         */
        public static final function get_settings_tab_url($tab_id) : string
        {
        }
        /**
         * Settings page constructor.
         *
         * Initializing Elementor settings page.
         *
         * @since 1.5.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Get tabs.
         *
         * Retrieve the settings page tabs, sections and fields.
         *
         * @since 1.5.0
         * @access public
         *
         * @return array Settings page tabs, sections and fields.
         */
        public final function get_tabs()
        {
        }
        /**
         * Add tab.
         *
         * Register a new tab to a settings page.
         *
         * @since 1.5.0
         * @access public
         *
         * @param string $tab_id   Tab ID.
         * @param array  $tab_args Optional. Tab arguments. Default is an empty array.
         */
        public final function add_tab($tab_id, array $tab_args = [])
        {
        }
        /**
         * Add section.
         *
         * Register a new section to a tab.
         *
         * @since 1.5.0
         * @access public
         *
         * @param string $tab_id       Tab ID.
         * @param string $section_id   Section ID.
         * @param array  $section_args Optional. Section arguments. Default is an
         *                             empty array.
         */
        public final function add_section($tab_id, $section_id, array $section_args = [])
        {
        }
        /**
         * Add field.
         *
         * Register a new field to a section.
         *
         * @since 1.5.0
         * @access public
         *
         * @param string $tab_id     Tab ID.
         * @param string $section_id Section ID.
         * @param string $field_id   Field ID.
         * @param array  $field_args Field arguments.
         */
        public final function add_field($tab_id, $section_id, $field_id, array $field_args)
        {
        }
        /**
         * Add fields.
         *
         * Register multiple fields to a section.
         *
         * @since 1.5.0
         * @access public
         *
         * @param string $tab_id     Tab ID.
         * @param string $section_id Section ID.
         * @param array  $fields     {
         *    An array of fields.
         *
         *    @type string $field_id   Field ID.
         *    @type array  $field_args Field arguments.
         * }
         */
        public final function add_fields($tab_id, $section_id, array $fields)
        {
        }
        /**
         * Register settings fields.
         *
         * In each tab register his inner sections, and in each section register his
         * inner fields.
         *
         * @since 1.5.0
         * @access public
         */
        public final function register_settings_fields()
        {
        }
        /**
         * Display settings page.
         *
         * Output the content for the settings page.
         *
         * @since 1.5.0
         * @access public
         */
        public function display_settings_page()
        {
        }
        public function get_usage_fields()
        {
        }
    }
}
namespace Elementor\Core\RoleManager {
    class Role_Manager extends \Elementor\Settings_Page
    {
        const PAGE_ID = 'elementor-role-manager';
        const ROLE_MANAGER_OPTION_NAME = 'exclude_user_roles';
        const ROLE_MANAGER_ADVANCED = 'role-manager';
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_role_manager_options()
        {
        }
        public function get_role_manager_advanced_options()
        {
        }
        public function get_user_advanced_options()
        {
        }
        /**
         * @since 2.0.0
         * @access protected
         */
        protected function get_page_title()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function register_admin_menu(\Elementor\Core\Admin\Menu\Admin_Menu_Manager $admin_menu)
        {
        }
        /**
         * @since 2.0.0
         * @access protected
         */
        protected function create_tabs()
        {
        }
        public function save_advanced_options($input)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function display_settings_page()
        {
        }
        public function add_json_enable_control($role_slug)
        {
        }
        public function add_custom_html_enable_control($role_slug)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_go_pro_link_html()
        {
        }
        public function get_go_pro_link_content()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function get_user_restrictions_array()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         *
         * @param $capability
         *
         * @return bool
         */
        public function user_can($capability)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         */
        public function __construct()
        {
        }
    }
    class Role_Manager_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page
    {
        public function __construct(\Elementor\Core\RoleManager\Role_Manager $role_manager)
        {
        }
        public function is_visible()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_label()
        {
        }
        public function get_page_title()
        {
        }
        public function get_capability()
        {
        }
        public function render()
        {
        }
    }
}
namespace Elementor\Core\Common {
    /**
     * App
     *
     * Elementor's common app that groups shared functionality, components and configuration
     *
     * @since 2.3.0
     */
    class App extends \Elementor\Core\Base\App
    {
        /**
         * App constructor.
         *
         * @since 2.3.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Init components
         *
         * Initializing common components.
         *
         * @since 2.3.0
         * @access public
         */
        public function init_components()
        {
        }
        /**
         * Get name.
         *
         * Retrieve the app name.
         *
         * @since 2.3.0
         * @access public
         *
         * @return string Common app name.
         */
        public function get_name()
        {
        }
        /**
         * Register scripts.
         *
         * Register common scripts.
         *
         * @since 2.3.0
         * @access public
         */
        public function register_scripts()
        {
        }
        /**
         * Register styles.
         *
         * Register common styles.
         *
         * @since 2.3.0
         * @access public
         */
        public function register_styles()
        {
        }
        /**
         * Add template.
         *
         * @since 2.3.0
         * @access public
         *
         * @param string $template Can be either a link to template file or template
         *                         HTML content.
         * @param string $type     Optional. Whether to handle the template as path
         *                         or text. Default is `path`.
         */
        public function add_template($template, $type = 'path')
        {
        }
        /**
         * Print Templates
         *
         * Prints all registered templates.
         *
         * @since 2.3.0
         * @access public
         */
        public function print_templates()
        {
        }
        /**
         * Get init settings.
         *
         * Define the default/initial settings of the common app.
         *
         * @since 2.3.0
         * @access protected
         *
         * @return array
         */
        protected function get_init_settings()
        {
        }
    }
}
namespace Elementor\Core\Common\Modules\EventTracker {
    class Personal_Data extends \Elementor\Core\Base\Base_Object
    {
        const WP_KEY = 'elementor-event-tracker';
        /**
         * Personal_Data constructor.
         */
        public function __construct()
        {
        }
    }
    /**
     * Event Tracker Module Class
     *
     * @since 3.6.0
     */
    class Module extends \Elementor\Core\Base\Module
    {
        public function get_name()
        {
        }
        /**
         * Get init settings.
         *
         * @since 3.6.0
         * @access protected
         *
         * @return array
         */
        protected function get_init_settings()
        {
        }
        public function __construct()
        {
        }
    }
    class DB extends \Elementor\Core\Base\Base_Object
    {
        const TABLE_NAME = 'e_events';
        const DB_VERSION_OPTION_KEY = 'elementor_events_db_version';
        const CURRENT_DB_VERSION = '1.0.0';
        /**
         * Get Table Name
         *
         * Returns the Events database table's name with the `wpdb` prefix.
         *
         * @since 3.6.0
         *
         * @return string
         */
        public function get_table_name()
        {
        }
        /**
         * Prepare Database for Entry
         *
         * The events database should have a limit of up to 1000 event entries stored daily.
         * Before adding a new entry to the database, we make sure that the limit of 1000 events is not reached.
         * If there are 1000 or more entries in the DB, we delete the earliest-inserted entry before inserting a new one.
         *
         * @since 3.6.0
         */
        public function prepare_db_for_entry()
        {
        }
        /**
         * Create Entry
         *
         * Adds an event entry to the database.
         *
         * @since 3.6.0
         */
        public function create_entry($event_data)
        {
        }
        /**
         * Get Event IDs From DB
         *
         * Fetches the IDs of all events saved in the database.
         *
         * @since 3.6.0
         *
         * @return array|object|null
         */
        public function get_event_ids_from_db()
        {
        }
        /**
         * Reset Table
         *
         * Empties the contents of the Events DB table.
         *
         * @since 3.6.0
         */
        public static function reset_table()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace Elementor\Data\V2\Base {
    /**
     * TODO: Utilize 'WP_REST_Controller' as much as possible.
     */
    abstract class Controller extends \WP_REST_Controller
    {
        /**
         * Loaded endpoint(s).
         *
         * @var \Elementor\Data\V2\Base\Endpoint[]
         */
        public $endpoints = [];
        /**
         * Index endpoint.
         *
         * @var \Elementor\Data\V2\Base\Endpoint\Index
         */
        public $index_endpoint = null;
        /**
         * Loaded processor(s).
         *
         * @var \Elementor\Data\V2\Base\Processor[][]
         */
        public $processors = [];
        public static function get_default_namespace()
        {
        }
        public static function get_default_version()
        {
        }
        /**
         * Get controller name.
         *
         * @return string
         */
        public abstract function get_name();
        /**
         * Register endpoints.
         */
        public function register_endpoints()
        {
        }
        public function register_routes()
        {
        }
        /**
         * Get parent controller name.
         *
         * @note: If `get_parent_name()` provided, controller will work as sub-controller.
         *
         * @returns null|string
         */
        public function get_parent_name()
        {
        }
        /**
         * Get full controller name.
         *
         * The method exist to handle situations when parent exist, to include the parent name.
         *
         * @return string
         */
        public function get_full_name()
        {
        }
        /**
         * Get controller namespace.
         *
         * @return string
         */
        public function get_namespace()
        {
        }
        /**
         * Get controller reset base.
         *
         * @return string
         */
        public function get_base_route()
        {
        }
        /**
         * Get controller route.
         *
         * @return string
         */
        public function get_controller_route()
        {
        }
        /**
         * Retrieves rest route(s) index for current controller.
         *
         * @return \WP_REST_Response|\WP_Error
         */
        public function get_controller_index()
        {
        }
        /**
         * Get items args of index endpoint.
         *
         * Is method is used when `get_collection_params()` is not enough, and need of knowing the methods is required.
         *
         * @param string $methods
         *
         * @return array
         */
        public function get_items_args($methods)
        {
        }
        /**
         * Get item args of index endpoint.
         *
         * @param string $methods
         *
         * @return array
         */
        public function get_item_args($methods)
        {
        }
        /**
         * Get permission callback.
         *
         * Default controller permission callback.
         * By default endpoint will inherit the permission callback from the controller.
         *
         * @param \WP_REST_Request $request
         *
         * @return bool
         */
        public function get_permission_callback($request)
        {
        }
        /**
         * Checks if a given request has access to create items.
         **
         *
         * @param \WP_REST_Request $request Full details about the request.
         *
         * @return true|\WP_Error True if the request has access to create items, WP_Error object otherwise.
         */
        public function create_items_permissions_check($request)
        {
        }
        /**
         * Checks if a given request has access to update items.
         *
         * @param \WP_REST_Request $request Full details about the request.
         *
         * @return true|\WP_Error True if the request has access to update the item, WP_Error object otherwise.
         */
        public function update_items_permissions_check($request)
        {
        }
        /**
         * Checks if a given request has access to delete items.
         *
         * @param \WP_REST_Request $request Full details about the request.
         *
         * @return true|\WP_Error True if the request has access to delete the item, WP_Error object otherwise.
         */
        public function delete_items_permissions_check($request)
        {
        }
        public function get_items($request)
        {
        }
        /**
         * Creates multiple items.
         *
         * @param \WP_REST_Request $request Full data about the request.
         *
         * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
         */
        public function create_items($request)
        {
        }
        /**
         * Updates multiple items.
         *
         * @param \WP_REST_Request $request Full data about the request.
         *
         * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
         */
        public function update_items($request)
        {
        }
        /**
         * Delete multiple items.
         *
         * @param \WP_REST_Request $request Full data about the request.
         *
         * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
         */
        public function delete_items($request)
        {
        }
        /**
         * Get the parent controller.
         *
         * @return \Elementor\Data\V2\Base\Controller|null
         */
        public function get_parent()
        {
        }
        /**
         * Get sub controller(s).
         *
         * @return \Elementor\Data\V2\Base\Controller[]
         */
        public function get_sub_controllers()
        {
        }
        /**
         * Get processors.
         *
         * @param string $command
         *
         * @return \Elementor\Data\V2\Base\Processor[]
         */
        public function get_processors($command)
        {
        }
        /**
         * Register processors.
         */
        public function register_processors()
        {
        }
        /**
         * Register index endpoint.
         */
        protected function register_index_endpoint()
        {
        }
        /**
         * Register endpoint.
         *
         * @param \Elementor\Data\V2\Base\Endpoint $endpoint
         *
         * @return \Elementor\Data\V2\Base\Endpoint
         */
        protected function register_endpoint(\Elementor\Data\V2\Base\Endpoint $endpoint)
        {
        }
        /**
         * Register a processor.
         *
         * That will be later attached to the endpoint class.
         *
         * @param Processor $processor
         *
         * @return \Elementor\Data\V2\Base\Processor $processor_instance
         */
        protected function register_processor(\Elementor\Data\V2\Base\Processor $processor)
        {
        }
        /**
         * Register.
         *
         * Endpoints & processors.
         */
        protected function register()
        {
        }
        /**
         * Get collection params by 'additionalProperties' context.
         *
         * @param string $context
         *
         * @return array
         */
        protected function get_collection_params_by_additional_props_context($context)
        {
        }
        /**
         * Controller constructor.
         *
         * Register endpoints on 'rest_api_init'.
         */
        public function __construct()
        {
        }
    }
}
namespace Elementor\Core\Common\Modules\EventTracker\Data {
    class Controller extends \Elementor\Data\V2\Base\Controller
    {
        public function get_name()
        {
        }
        public function register_endpoints()
        {
        }
        /**
         * Get Permissions Callback
         *
         * This endpoint should only accept POST requests, and currently we only track site administrator actions.
         *
         * @since 3.6.0
         *
         * @param \WP_REST_Request $request
         * @return bool
         */
        public function get_permission_callback($request)
        {
        }
        /**
         * Create Items
         *
         * Receives a request for adding an event data entry into the database. If the request contains event data, this
         * method initiates creation of a database entry with the event data in the Events DB table.
         *
         * @since 3.6.0
         *
         * @param \WP_REST_Request $request
         * @return bool
         */
        public function create_items($request)
        {
        }
    }
}
namespace Elementor\Core\Common\Modules\Finder {
    /**
     * Base Category
     *
     * Base class for Elementor Finder categories.
     */
    abstract class Base_Category extends \Elementor\Core\Base\Base_Object
    {
        /**
         * Get title.
         *
         * @since 2.3.0
         * @abstract
         * @access public
         *
         * @return string
         */
        public abstract function get_title();
        /**
         * Get a unique category ID.
         *
         * TODO: Make abstract.
         *
         * @since 3.5.0
         * @deprecated 3.5.0
         * @access public
         *
         * @return string
         */
        public function get_id()
        {
        }
        /**
         * Get category items.
         *
         * @since 2.3.0
         * @abstract
         * @access public
         *
         * @param array $options
         *
         * @return array
         */
        public abstract function get_category_items(array $options = []);
        /**
         * Is dynamic.
         *
         * Determine if the category is dynamic.
         *
         * @since 2.3.0
         * @access public
         *
         * @return bool
         */
        public function is_dynamic()
        {
        }
        /**
         * Get init settings.
         *
         * @since 2.3.0
         * @access protected
         *
         * @return array
         */
        protected function get_init_settings()
        {
        }
    }
    /**
     * Finder Module
     *
     * Responsible for initializing Elementor Finder functionality
     */
    class Module extends \Elementor\Core\Base\Module
    {
        /**
         * Module constructor.
         *
         * @since 2.3.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Get name.
         *
         * @since 2.3.0
         * @access public
         *
         * @return string
         */
        public function get_name()
        {
        }
        /**
         * Add template.
         *
         * @since 2.3.0
         * @access public
         */
        public function add_template()
        {
        }
        /**
         * Register ajax actions.
         *
         * @since 2.3.0
         * @access public
         *
         * @param Ajax $ajax
         */
        public function register_ajax_actions(\Elementor\Core\Common\Modules\Ajax\Module $ajax)
        {
        }
        /**
         * Ajax get category items.
         *
         * @since 2.3.0
         * @access public
         *
         * @param array $data
         *
         * @return array
         */
        public function ajax_get_category_items(array $data)
        {
        }
        /**
         * Get init settings.
         *
         * @since 2.3.0
         * @access protected
         *
         * @return array
         */
        protected function get_init_settings()
        {
        }
    }
    class Categories_Manager
    {
        /**
         * Add category.
         *
         * @since 2.3.0
         * @deprecated 3.5.0 Use `register()` method instead.
         * @access public
         *
         * @param string        $category_name
         * @param Base_Category $category
         *
         * @deprecated 3.5.0 Use `register()` method instead.
         */
        public function add_category($category_name, \Elementor\Core\Common\Modules\Finder\Base_Category $category)
        {
        }
        /**
         * Register finder category.
         *
         * @since 3.5.0
         * @access public
         *
         * @param Base_Category $finder_category_instance An Instance of a category.
         * @param string        $finder_category_name     A Category name. Deprecated parameter.
         *
         * @return void
         */
        public function register(\Elementor\Core\Common\Modules\Finder\Base_Category $finder_category_instance, $finder_category_name = null)
        {
        }
        /**
         * Unregister a finder category.
         *
         * @param string $finder_category_name - Category to unregister.
         *
         * @return void
         * @since 3.6.0
         * @access public
         */
        public function unregister($finder_category_name)
        {
        }
        /**
         * Get categories.
         *
         * Retrieve the registered categories, or a specific category if the category name
         * is provided as a parameter.
         *
         * @since 2.3.0
         * @access public
         *
         * @param string $category Category name.
         *
         * @return Base_Category|Base_Category[]|null
         */
        public function get_categories($category = '')
        {
        }
    }
}
namespace Elementor\Core\Common\Modules\Finder\Categories {
    /**
     * Settings Category
     *
     * Provides items related to Elementor's settings.
     */
    class Settings extends \Elementor\Core\Common\Modules\Finder\Base_Category
    {
        /**
         * Get title.
         *
         * @since 2.3.0
         * @access public
         *
         * @return string
         */
        public function get_title()
        {
        }
        public function get_id()
        {
        }
        /**
         * Get category items.
         *
         * @since 2.3.0
         * @access public
         *
         * @param array $options
         *
         * @return array
         */
        public function get_category_items(array $options = [])
        {
        }
    }
    /**
     * Create Category
     *
     * Provides items related to creation of new posts/pages/templates etc.
     */
    class Create extends \Elementor\Core\Common\Modules\Finder\Base_Category
    {
        /**
         * Get title.
         *
         * @since 2.3.0
         * @access public
         *
         * @return string
         */
        public function get_title()
        {
        }
        public function get_id()
        {
        }
        /**
         * Get category items.
         *
         * @since 2.3.0
         * @access public
         *
         * @param array $options
         *
         * @return array
         */
        public function get_category_items(array $options = [])
        {
        }
    }
    /**
     * Tools Category
     *
     * Provides items related to Elementor's tools.
     */
    class Tools extends \Elementor\Core\Common\Modules\Finder\Base_Category
    {
        /**
         * Get title.
         *
         * @since 2.3.0
         * @access public
         *
         * @return string
         */
        public function get_title()
        {
        }
        public function get_id()
        {
        }
        /**
         * Get category items.
         *
         * @since 2.3.0
         * @access public
         *
         * @param array $options
         *
         * @return array
         */
        public function get_category_items(array $options = [])
        {
        }
    }
    /**
     * Edit Category
     *
     * Provides items related to editing of posts/pages/templates etc.
     */
    class Edit extends \Elementor\Core\Common\Modules\Finder\Base_Category
    {
        /**
         * Get title.
         *
         * @since 2.3.0
         * @access public
         *
         * @return string
         */
        public function get_title()
        {
        }
        public function get_id()
        {
        }
        /**
         * Is dynamic.
         *
         * Determine if the category is dynamic.
         *
         * @since 2.3.0
         * @access public
         *
         * @return bool
         */
        public function is_dynamic()
        {
        }
        /**
         * Get category items.
         *
         * @since 2.3.0
         * @access public
         *
         * @param array $options
         *
         * @return array
         */
        public function get_category_items(array $options = [])
        {
        }
    }
    /**
     * General Category
     *
     * Provides general items related to Elementor Admin.
     */
    class General extends \Elementor\Core\Common\Modules\Finder\Base_Category
    {
        /**
         * Get title.
         *
         * @since 2.3.0
         * @access public
         *
         * @return string
         */
        public function get_title()
        {
        }
        public function get_id()
        {
        }
        /**
         * Get category items.
         *
         * @since 2.3.0
         * @access public
         *
         * @param array $options
         *
         * @return array
         */
        public function get_category_items(array $options = [])
        {
        }
    }
    /**
     * Site Category
     *
     * Provides general site items.
     */
    class Site extends \Elementor\Core\Common\Modules\Finder\Base_Category
    {
        /**
         * Get title.
         *
         * @since 2.3.0
         * @access public
         *
         * @return string
         */
        public function get_title()
        {
        }
        public function get_id()
        {
        }
        /**
         * Get category items.
         *
         * @since 2.3.0
         * @access public
         *
         * @param array $options
         *
         * @return array
         */
        public function get_category_items(array $options = [])
        {
        }
    }
}
namespace Elementor\Core\Common\Modules\Connect {
    class Module extends \Elementor\Core\Base\Module
    {
        const ACCESS_LEVEL_CORE = 0;
        const ACCESS_LEVEL_PRO = 1;
        const ACCESS_LEVEL_EXPERT = 20;
        const ACCESS_TIER_FREE = 'free';
        const ACCESS_TIER_ESSENTIAL = 'essential';
        const ACCESS_TIER_ESSENTIAL_OCT_2023 = 'essential-oct2023';
        const ACCESS_TIER_ADVANCED = 'advanced';
        const ACCESS_TIER_EXPERT = 'expert';
        const ACCESS_TIER_AGENCY = 'agency';
        const ACCESS_TIER_PRO_LEGACY = 'pro';
        /**
         * @since 2.3.0
         * @access public
         */
        public function get_name()
        {
        }
        /**
         * @var array
         */
        protected $registered_apps = [];
        /**
         * Apps Instances.
         *
         * Holds the list of all the apps instances.
         *
         * @since 2.3.0
         * @access protected
         *
         * @var Base_App[]
         */
        protected $apps = [];
        /**
         * Registered apps categories.
         *
         * Holds the list of all the registered apps categories.
         *
         * @since 2.3.0
         * @access protected
         *
         * @var array
         */
        protected $categories = [];
        protected $admin_page;
        /**
         * @since 2.3.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Register default apps.
         *
         * Registers the default apps.
         *
         * @since 2.3.0
         * @access public
         */
        public function init()
        {
        }
        /**
         * Register app.
         *
         * Registers an app.
         *
         * @since 2.3.0
         * @access public
         *
         * @param string $slug App slug.
         * @param string $class App full class name.
         *
         * @return self The updated apps manager instance.
         */
        public function register_app($slug, $class)
        {
        }
        /**
         * Get app instance.
         *
         * Retrieve the app instance.
         *
         * @since 2.3.0
         * @access public
         *
         * @param $slug
         *
         * @return Base_App|null
         */
        public function get_app($slug)
        {
        }
        /**
         * @since 2.3.0
         * @access public
         * @return Base_App[]
         */
        public function get_apps()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function register_category($slug, $args)
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function get_categories()
        {
        }
        /**
         * @param string $context Where this subscription plan should be shown.
         *
         * @return array
         */
        public function get_subscription_plans($context = '')
        {
        }
    }
    class Admin
    {
        const PAGE_ID = 'elementor-connect';
        public static $url = '';
        /**
         * @since 2.3.0
         * @access public
         */
        public function register_admin_menu(\Elementor\Core\Admin\Menu\Admin_Menu_Manager $admin_menu)
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function on_load_page()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function __construct()
        {
        }
    }
}
namespace Elementor\Core\Common\Modules\Connect\Apps {
    class Connect extends \Elementor\Core\Common\Modules\Connect\Apps\Common_App
    {
        public function get_title()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        protected function get_slug()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function render_admin_widget()
        {
        }
    }
}
namespace Elementor\Core\Common\Modules\Connect {
    class Connect_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page
    {
        public function is_visible()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_label()
        {
        }
        public function get_page_title()
        {
        }
        public function get_capability()
        {
        }
        public function render()
        {
        }
    }
}
namespace Elementor\Core\Common\Modules\Ajax {
    /**
     * Elementor ajax manager.
     *
     * Elementor ajax manager handler class is responsible for handling Elementor
     * ajax requests, ajax responses and registering actions applied on them.
     *
     * @since 2.0.0
     */
    class Module extends \Elementor\Core\Base\Module
    {
        const NONCE_KEY = 'elementor_ajax';
        /**
         * Ajax manager constructor.
         *
         * Initializing Elementor ajax manager.
         *
         * @since 2.0.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Get module name.
         *
         * Retrieve the module name.
         *
         * @since  1.7.0
         * @access public
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
        /**
         * Register ajax action.
         *
         * Add new actions for a specific ajax request and the callback function to
         * be handle the response.
         *
         * @since 2.0.0
         * @access public
         *
         * @param string   $tag      Ajax request name/tag.
         * @param callable $callback The callback function.
         */
        public function register_ajax_action($tag, $callback)
        {
        }
        /**
         * Handle ajax request.
         *
         * Verify ajax nonce, and run all the registered actions for this request.
         *
         * Fired by `wp_ajax_elementor_ajax` action.
         *
         * @since 2.0.0
         * @access public
         */
        public function handle_ajax_request()
        {
        }
        /**
         * Get current action data.
         *
         * Retrieve the data for the current ajax request.
         *
         * @since 2.0.1
         * @access public
         *
         * @return bool|mixed Ajax request data if action exist, False otherwise.
         */
        public function get_current_action_data()
        {
        }
        /**
         * Create nonce.
         *
         * Creates a cryptographic token to
         * give the user an access to Elementor ajax actions.
         *
         * @since 2.3.0
         * @access public
         *
         * @return string The nonce token.
         */
        public function create_nonce()
        {
        }
        /**
         * Verify request nonce.
         *
         * Whether the request nonce verified or not.
         *
         * @since 2.3.0
         * @access public
         *
         * @return bool True if request nonce verified, False otherwise.
         */
        public function verify_request_nonce()
        {
        }
        protected function get_init_settings()
        {
        }
    }
}
namespace Elementor\Core\Behaviors\Interfaces {
    interface Lock_Behavior
    {
        /**
         * @return bool
         */
        public function is_locked();
        /**
         * @return array {
         *
         *    @type bool $is_locked
         *
         *    @type array $badge {
         *         @type string $icon
         *         @type string $text
         *     }
         *
         *    @type array $content {
         *         @type string $heading
         *         @type string $description
         *   }
         *
         *    @type array $button {
         *         @type string $text
         *         @type string $url
         *   }
         *
         * }
         */
        public function get_config();
    }
}
namespace Elementor\Core\Isolation {
    interface Plugin_Status_Adapter_Interface
    {
        public function is_plugin_installed($plugin_path) : bool;
        public function get_install_plugin_url($plugin_path) : string;
        public function get_activate_plugin_url($plugin_path) : string;
    }
    class Plugin_Status_Adapter implements \Elementor\Core\Isolation\Plugin_Status_Adapter_Interface
    {
        public \Elementor\Core\Isolation\Wordpress_Adapter_Interface $wordpress_adapter;
        public function __construct(\Elementor\Core\Isolation\Wordpress_Adapter_Interface $wordpress_adapter)
        {
        }
        public function is_plugin_installed($plugin_path) : bool
        {
        }
        public function get_install_plugin_url($plugin_path) : string
        {
        }
        public function get_activate_plugin_url($plugin_path) : string
        {
        }
    }
    interface Wordpress_Adapter_Interface
    {
        public function get_plugins();
        public function is_plugin_active($plugin_path);
        public function wp_nonce_url($url, $action);
        public function self_admin_url($path);
        public function get_pages($args);
        public function get_query($args);
        public function get_option($option_key);
        public function add_option($option_key, $option_value);
        public function update_option($option_key, $option_value);
        public function get_user_preferences($preference_key);
        public function set_user_preferences($preference_key, $value);
        public function is_new_installation();
        public function add_query_arg($args, $url);
        public function has_custom_logo();
        public function current_user_can($capability, $args);
        public function get_post_status($post_id);
    }
    class Wordpress_Adapter implements \Elementor\Core\Isolation\Wordpress_Adapter_Interface
    {
        public function get_plugins() : array
        {
        }
        public function is_plugin_active($plugin_path) : bool
        {
        }
        public function wp_nonce_url($url, $action) : string
        {
        }
        public function self_admin_url($path) : string
        {
        }
        /**
         * Retrieves an array of pages (or hierarchical post type items).
         *
         * @return WP_Post[]|false Array of pages (or hierarchical post type items). Boolean false if the
         *                         specified post type is not hierarchical or the specified status is not
         *                         supported by the post type.
         */
        public function get_pages($args) : ?array
        {
        }
        /**
         * Creates and returns a wp query instance.
         *
         * @return \WP_Query
         */
        public function get_query($args) : ?\WP_Query
        {
        }
        public function get_option($option_key)
        {
        }
        public function update_option($option_key, $option_value) : void
        {
        }
        public function add_option($option_key, $option_value) : void
        {
        }
        public function get_user_preferences($preference_key)
        {
        }
        public function set_user_preferences($preference_key, $value)
        {
        }
        public function is_new_installation()
        {
        }
        public function add_query_arg($args, $url) : string
        {
        }
        public function has_custom_logo() : bool
        {
        }
        public function current_user_can($capability, $args) : bool
        {
        }
        public function get_post_status($post_id) : string
        {
        }
    }
    interface Elementor_Counter_Adapter_Interface
    {
        public function get_count($key) : ?int;
        public function set_count($key, $count = 0) : void;
        public function increment($key) : void;
        public function is_key_allowed($key) : bool;
    }
    interface Elementor_Adapter_Interface
    {
        public function get_kit_settings();
        public function get_main_post();
        public function is_active_kit_default() : bool;
        public function get_count($key) : ?int;
        public function set_count($key, $count = 0) : void;
        public function increment($key) : void;
        public function is_key_allowed($key) : bool;
        public function get_template_type($template_id) : string;
        public function get_tier() : string;
    }
    class Elementor_Adapter implements \Elementor\Core\Isolation\Elementor_Adapter_Interface
    {
        public function get_kit_settings()
        {
        }
        public function get_main_post()
        {
        }
        public function is_active_kit_default() : bool
        {
        }
        public function get_count($key) : ?int
        {
        }
        public function set_count($key, $count = 0) : void
        {
        }
        public function increment($key) : void
        {
        }
        public function is_key_allowed($key) : bool
        {
        }
        public function get_template_type($template_id) : string
        {
        }
        public function get_tier() : string
        {
        }
    }
}
namespace Elementor\Core\Page_Assets {
    /**
     * Elementor assets loader.
     *
     * A class that is responsible for conditionally enqueuing styles and script assets that were pre-enabled.
     *
     * @since 3.3.0
     */
    class Loader extends \Elementor\Core\Base\Module
    {
        public function get_name() : string
        {
        }
        public function get_assets() : array
        {
        }
        /**
         * @param array $assets {
         *     @type array 'styles'
         *     @type array 'scripts'
         * }
         */
        public function enable_assets(array $assets_data) : void
        {
        }
        /**
         * @param array $assets {
         *     @type array 'styles'
         *     @type array 'scripts'
         * }
         */
        public function add_assets(array $assets) : void
        {
        }
        /**
         * @deprecated 3.22.0
         */
        public function enqueue_assets() : void
        {
        }
        public function __construct()
        {
        }
    }
}
namespace Elementor\Core\Page_Assets\Data_Managers {
    /**
     * Elementor Assets Data.
     *
     * @since 3.3.0
     */
    abstract class Base
    {
        const ASSETS_DATA_KEY = '_elementor_assets_data';
        /**
         * @var array
         */
        protected $assets_data;
        /**
         * @var string
         */
        protected $content_type;
        /**
         * @var string
         */
        protected $assets_category;
        /**
         * Get Asset Content.
         *
         * Responsible for extracting the asset data from a certain file.
         * Will be triggered automatically when the asset data does not exist, or when the asset version was changed.
         *
         * @since 3.3.0
         * @access public
         *
         * @return string
         */
        protected abstract function get_asset_content();
        /**
         * Get Asset Key.
         *
         * The asset data will be saved in the DB under this key.
         *
         * @since 3.3.0
         * @access protected
         *
         * @return string
         */
        protected function get_key()
        {
        }
        /**
         * Get Relative Version.
         *
         * The asset data will be re-evaluated according the version number.
         *
         * @since 3.3.0
         * @access protected
         *
         * @return string
         */
        protected function get_version()
        {
        }
        /**
         * Get Asset Path.
         *
         * The asset data will be extracted from the file path.
         *
         * @since 3.3.0
         * @access protected
         *
         * @return string
         */
        protected function get_file_path()
        {
        }
        /**
         * Get Config Data.
         *
         * Holds a unique data relevant for the specific assets category type.
         *
         * @since 3.3.0
         * @access protected
         *
         * @return string|array
         */
        protected function get_config_data($key = '')
        {
        }
        /**
         * Set Asset Data.
         *
         * Responsible for setting the current asset data.
         *
         * @since 3.3.0
         * @access protected
         *
         * @return void
         */
        protected function set_asset_data($asset_key)
        {
        }
        /**
         * Save Asset Data.
         *
         * Responsible for saving the asset data in the DB.
         *
         * @since 3.3.0
         * @access protected
         *
         * @param string $asset_key
         *
         * @return void
         */
        protected function save_asset_data($asset_key)
        {
        }
        /**
         * Is Asset Version Changed.
         *
         * Responsible for comparing the saved asset data version to the current relative version.
         *
         * @since 3.3.0
         * @access protected
         *
         * @param string $asset_key
         *
         * @return boolean
         */
        protected function is_asset_version_changed($version)
        {
        }
        /**
         * Get File Data.
         *
         * Getting a file content or size.
         *
         * @since 3.3.0
         * @access protected
         *
         * @param string $data_type (exists|content|size)
         * @param string $file_key - In case that the same file data is needed for multiple assets (like a JSON file), the file data key should be the same for all shared assets to make sure that the file is being read only once.
         *
         * @return string|number
         */
        protected function get_file_data($data_type, $file_key = '')
        {
        }
        /**
         * Get Saved Assets Data.
         *
         * Getting the assets data from the DB.
         *
         * @since 3.3.0
         * @access protected
         *
         * @return array
         */
        protected function get_saved_assets_data()
        {
        }
        /**
         * Get Config.
         *
         * Getting the assets data config.
         *
         * @since 3.5.0
         * @access protected
         *
         * @return array
         */
        protected function get_config($data)
        {
        }
        /**
         * Init Asset Data.
         *
         * Initialize the asset data and handles the asset content updates when needed.
         *
         * @since 3.3.0
         * @access public
         *
         * @param array $config {
         *     @type string 'key'
         *     @type string 'version'
         *     @type string 'file_path'
         *     @type array 'data'
         * }
         *
         * @return void
         */
        public function init_asset_data($config)
        {
        }
        /**
         * Get Asset Data From Config.
         *
         * Getting the asset data content from config.
         *
         * @since 3.3.0
         * @access public
         *
         * @param array $config {
         *     @type string 'key'
         *     @type string 'version'
         *     @type string 'file_path'
         *     @type array 'data'
         * }
         *
         * @return mixed
         */
        public function get_asset_data_from_config(array $config)
        {
        }
        /**
         * Get Asset Data.
         *
         * Getting the asset data content.
         *
         * @since 3.5.0
         * @access public
         *
         * @param array $data
         *
         * @return mixed
         */
        public function get_asset_data(array $data)
        {
        }
        public function __construct()
        {
        }
    }
}
namespace Elementor\Core\Page_Assets\Data_Managers\Font_Icon_Svg {
    /**
     * Elementor Font Icon Svg Base.
     *
     * @since 3.4.0
     */
    class Base extends \Elementor\Core\Page_Assets\Data_Managers\Base
    {
        protected $content_type = 'svg';
        protected $assets_category = 'font-icon';
        protected function get_asset_content()
        {
        }
    }
    /**
     * E-Icons Svg.
     *
     * @since 3.5.0
     */
    class E_Icons extends \Elementor\Core\Page_Assets\Data_Managers\Font_Icon_Svg\Base
    {
        const LIBRARY_CURRENT_VERSION = '5.13.0';
        protected function get_config($icon)
        {
        }
        protected function get_asset_content()
        {
        }
    }
    /**
     * Elementor Font Icon Svg Manager.
     *
     * @since 3.4.0
     */
    class Manager extends \Elementor\Core\Base\Base_Object
    {
        public static function get_font_icon_svg_data($icon)
        {
        }
        public static function get_font_family($icon_library)
        {
        }
    }
    /**
     * Font Awesome Icon Svg.
     *
     * @since 3.4.0
     */
    class Font_Awesome extends \Elementor\Core\Page_Assets\Data_Managers\Font_Icon_Svg\Base
    {
        const LIBRARY_CURRENT_VERSION = '5.15.3';
        protected function get_config($icon)
        {
        }
        protected function get_asset_content()
        {
        }
    }
}
namespace Elementor\Core\Files\CSS {
    /**
     * Elementor global CSS file.
     *
     * Elementor CSS file handler class is responsible for generating the global CSS
     * file.
     *
     * @since 1.2.0
     */
    class Global_CSS extends \Elementor\Core\Files\CSS\Base
    {
        /**
         * Elementor global CSS file handler ID.
         */
        const FILE_HANDLER_ID = 'elementor-global';
        const META_KEY = '_elementor_global_css';
        /**
         * Get CSS file name.
         *
         * Retrieve the CSS file name.
         *
         * @since 1.6.0
         * @access public
         *
         * @return string CSS file name.
         */
        public function get_name()
        {
        }
        /**
         * Get file handle ID.
         *
         * Retrieve the handle ID for the global post CSS file.
         *
         * @since 1.2.0
         * @access protected
         *
         * @return string CSS file handle ID.
         */
        protected function get_file_handle_id()
        {
        }
        /**
         * Render CSS.
         *
         * Parse the CSS for all the widgets and all the scheme controls.
         *
         * @since 1.2.0
         * @access protected
         */
        protected function render_css()
        {
        }
        /**
         * Get inline dependency.
         *
         * Retrieve the name of the stylesheet used by `wp_add_inline_style()`.
         *
         * @since 1.2.0
         * @access protected
         *
         * @return string Name of the stylesheet.
         */
        protected function get_inline_dependency()
        {
        }
        /**
         * Is update required.
         *
         * Whether the CSS requires an update. When there are new schemes or settings
         * updates.
         *
         * @since 1.2.0
         * @access protected
         *
         * @return bool True if the CSS requires an update, False otherwise.
         */
        protected function is_update_required()
        {
        }
    }
    /**
     * Elementor post preview CSS file.
     *
     * Elementor CSS file handler class is responsible for generating the post
     * preview CSS file.
     *
     * @since 1.9.0
     */
    class Post_Preview extends \Elementor\Core\Files\CSS\Post_Local_Cache
    {
        /**
         * Post preview CSS file constructor.
         *
         * Initializing the CSS file of the post preview. Set the post ID and the
         * parent ID and initiate the stylesheet.
         *
         * @since 1.9.0
         * @access public
         *
         * @param int $post_id Post ID.
         */
        public function __construct($post_id)
        {
        }
        protected function get_post_id_for_data()
        {
        }
        /**
         * Get file handle ID.
         *
         * Retrieve the handle ID for the previewed post CSS file.
         *
         * @since 1.9.0
         * @access protected
         *
         * @return string CSS file handle ID.
         */
        protected function get_file_handle_id()
        {
        }
    }
}
namespace Elementor\Core\Files {
    /**
     * Elementor files manager.
     *
     * Elementor files manager handler class is responsible for creating files.
     *
     * @since 1.2.0
     */
    class Manager
    {
        /**
         * Files manager constructor.
         *
         * Initializing the Elementor files manager.
         *
         * @since 1.2.0
         * @access public
         */
        public function __construct()
        {
        }
        public function get($class, $args)
        {
        }
        /**
         * On post delete.
         *
         * Delete post CSS immediately after a post is deleted from the database.
         *
         * Fired by `deleted_post` action.
         *
         * @since 1.2.0
         * @access public
         *
         * @param string $post_id Post ID.
         */
        public function on_delete_post($post_id)
        {
        }
        /**
         * On export post meta.
         *
         * When exporting data using WXR, skip post CSS file meta key. This way the
         * export won't contain the post CSS file data used by Elementor.
         *
         * Fired by `wxr_export_skip_postmeta` filter.
         *
         * @since 1.2.0
         * @access public
         *
         * @param bool   $skip     Whether to skip the current post meta.
         * @param string $meta_key Current meta key.
         *
         * @return bool Whether to skip the post CSS meta.
         */
        public function on_export_post_meta($skip, $meta_key)
        {
        }
        /**
         * Clear cache.
         *
         * Delete all meta containing files data. And delete the actual
         * files from the upload directory.
         *
         * @since 1.2.0
         * @access public
         */
        public function clear_cache()
        {
        }
        public function clear_custom_image_sizes()
        {
        }
        /**
         * Register Ajax Actions
         *
         * Deprecated - use the Uploads Manager instead.
         *
         * @deprecated 3.5.0
         *
         * @param Ajax $ajax
         */
        public function register_ajax_actions(\Elementor\Core\Common\Modules\Ajax\Module $ajax)
        {
        }
        /**
         * Ajax Unfiltered Files Upload
         *
         * Deprecated - use the Uploads Manager instead.
         *
         * @deprecated 3.5.0
         */
        public function ajax_unfiltered_files_upload()
        {
        }
        /**
         * Generate CSS.
         *
         * Generates CSS for all posts built with Elementor.
         *
         * @since 3.25.0
         * @access public
         */
        public function generate_css()
        {
        }
        public function register_endpoints()
        {
        }
    }
}
namespace Elementor\Core\Files\Fonts {
    class Google_Font
    {
        const FOLDER_BASE = 'elementor/google-fonts';
        const FOLDER_CSS = 'css';
        const FOLDER_FONTS = 'fonts';
        const AVAILABLE_FOLDERS = [self::FOLDER_CSS, self::FOLDER_FONTS];
        const UA_STRING = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36';
        const TYPE_DEFAULT = 'default';
        const TYPE_EARLYACCESS = 'earlyaccess';
        public static function enqueue(string $font_name, string $font_type = self::TYPE_DEFAULT) : bool
        {
        }
        public static function clear_cache()
        {
        }
    }
}
namespace Elementor\Core\Files\Assets {
    /**
     * Files Upload Handler
     *
     * @deprecated 3.5.0 Use `Elementor\Core\Files\Uploads_Manager` class instead.
     */
    abstract class Files_Upload_Handler
    {
        /**
         * @deprecated 3.5.0
         */
        const OPTION_KEY = 'elementor_unfiltered_files_upload';
        /**
         * @deprecated 3.5.0
         */
        public abstract function get_mime_type();
        /**
         * @deprecated 3.5.0
         */
        public abstract function get_file_type();
        /**
         * Is Enabled
         *
         * @deprecated 3.5.0 Use `Elementor\Plugin::$instance->uploads_manager->are_unfiltered_uploads_enabled()` instead.
         *
         * @return bool
         */
        public static final function is_enabled()
        {
        }
        /**
         * @deprecated 3.5.0 Use `Elementor\Plugin::$instance->uploads_manager->are_unfiltered_uploads_enabled()` instead.
         */
        public final function support_unfiltered_files_upload($existing_mimes)
        {
        }
        /**
         * handle_upload_prefilter
         *
         * @deprecated 3.5.0 Use `Elementor\Plugin::$instance->uploads_manager->handle_elementor_wp_media_upload()` instead.
         *
         * @param $file
         *
         * @return mixed
         */
        public function handle_upload_prefilter($file)
        {
        }
        /**
         * is_file_should_handled
         *
         * @deprecated 3.5.0
         *
         * @param $file
         *
         * @return bool
         */
        protected function is_file_should_handled($file)
        {
        }
        /**
         * file_sanitizer_can_run
         *
         * @deprecated 3.5.0 Use `Elementor\Core\Files\File_Types\Svg::file_sanitizer_can_run()` instead.
         *
         * @return bool
         */
        public static function file_sanitizer_can_run()
        {
        }
        /**
         * Check filetype and ext
         *
         * A workaround for upload validation which relies on a PHP extension (fileinfo)
         * with inconsistent reporting behaviour.
         * ref: https://core.trac.wordpress.org/ticket/39550
         * ref: https://core.trac.wordpress.org/ticket/40175
         *
         * @deprecated 3.5.0 Use `Elementor\Plugin::$instance->uploads_manager->check_filetype_and_ext()` instead.
         *
         * @param $data
         * @param $file
         * @param $filename
         * @param $mimes
         *
         * @return mixed
         */
        public function check_filetype_and_ext($data, $file, $filename, $mimes)
        {
        }
    }
}
namespace Elementor\Core\Files\Assets\Svg {
    /**
     * SVG Handler
     *
     * @deprecated 3.5.0 Use `Elementor\Core\Files\File_Types\Svg` instead, accessed by calling: `Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' );`
     */
    class Svg_Handler extends \Elementor\Core\Files\Assets\Files_Upload_Handler
    {
        /**
         * Inline svg attachment meta key
         *
         * @deprecated 3.5.0
         */
        const META_KEY = '_elementor_inline_svg';
        /**
         * @deprecated 3.5.0
         */
        const SCRIPT_REGEX = '/(?:\\w+script|data):/xi';
        /**
         * @deprecated 3.5.0
         */
        public static function get_name()
        {
        }
        /**
         * get_meta
         *
         * @deprecated 3.5.0
         *
         * @return mixed
         */
        protected function get_meta()
        {
        }
        /**
         * update_meta
         *
         * @deprecated 3.5.0
         *
         * @param $meta
         */
        protected function update_meta($meta)
        {
        }
        /**
         * delete_meta
         *
         * @deprecated 3.5.0
         */
        protected function delete_meta()
        {
        }
        /**
         * @deprecated 3.5.0
         */
        public function get_mime_type()
        {
        }
        /**
         * @deprecated 3.5.0
         */
        public function get_file_type()
        {
        }
        /**
         * delete_meta_cache
         *
         * @deprecated 3.5.0 Use `Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' )->delete_meta_cache()` instead.
         */
        public function delete_meta_cache()
        {
        }
        /**
         * get_inline_svg
         *
         * @deprecated 3.5.0 Use `Elementor\Core\Files\File_Types\Svg::get_inline_svg()` instead.
         *
         * @param $attachment_id
         *
         * @return bool|mixed|string
         */
        public static function get_inline_svg($attachment_id)
        {
        }
        /**
         * sanitize_svg
         *
         * @deprecated 3.5.0 Use `Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' )->delete_meta_cache()->sanitize_svg()` instead.
         *
         * @param $filename
         *
         * @return bool
         */
        public function sanitize_svg($filename)
        {
        }
        /**
         * sanitizer
         *
         * @deprecated 3.5.0 Use `Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' )->sanitizer()` instead.
         *
         * @param $content
         *
         * @return bool|string
         */
        public function sanitizer($content)
        {
        }
        /**
         * wp_prepare_attachment_for_js
         *
         * @deprecated 3.5.0 Use `Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' )->wp_prepare_attachment_for_js()` instead.
         *
         * @param $attachment_data
         * @param $attachment
         * @param $meta
         *
         * @return mixed
         */
        public function wp_prepare_attachment_for_js($attachment_data, $attachment, $meta)
        {
        }
        /**
         * set_attachment_id
         *
         * @deprecated 3.5.0
         *
         * @param $attachment_id
         *
         * @return int
         */
        public function set_attachment_id($attachment_id)
        {
        }
        /**
         * get_attachment_id
         *
         * @deprecated 3.5.0
         *
         * @return int
         */
        public function get_attachment_id()
        {
        }
        /**
         * set_svg_meta_data
         *
         * @deprecated 3.5.0 Use `Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' )->set_svg_meta_data()` instead.
         *
         * @return mixed
         */
        public function set_svg_meta_data($data, $id)
        {
        }
        /**
         * handle_upload_prefilter
         *
         * @deprecated 3.5.0 Use `Elementor\Plugin::$instance->uploads_manager->handle_elementor_wp_media_upload()` instead.
         *
         * @param $file
         *
         * @return mixed
         */
        public function handle_upload_prefilter($file)
        {
        }
    }
}
namespace Elementor\Core\Files\Assets\Json {
    /**
     * Json Handler
     *
     * @deprecated 3.5.0 Use `Elementor\Core\Files\File_Types\Svg` instead, accessed by calling `Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' );`
     */
    class Json_Handler extends \Elementor\Core\Files\Assets\Files_Upload_Handler
    {
        /**
         * @deprecated 3.5.0
         */
        public static function get_name()
        {
        }
        /**
         * @deprecated 3.5.0
         */
        public function get_mime_type()
        {
        }
        /**
         * @deprecated 3.5.0
         */
        public function get_file_type()
        {
        }
    }
}
namespace Elementor\Core\Files\Assets {
    /**
     * Elementor files manager.
     *
     * Elementor files manager handler class is responsible for creating files.
     *
     * @since 2.6.0
     */
    class Manager
    {
        /**
         * Holds registered asset types
         * @var array
         */
        protected $asset_types = [];
        /**
         * Assets manager constructor.
         *
         * Initializing the Elementor assets manager.
         *
         * @access public
         */
        public function __construct()
        {
        }
        public function get_asset($name)
        {
        }
        /**
         * Add Asset
         * @param $instance
         */
        public function add_asset($instance)
        {
        }
    }
}
namespace Elementor\Core\Files\File_Types {
    /**
     * Elementor File Types Base.
     *
     * The File Types Base class provides base methods used by all file type handler classes.
     * These methods are used in file upl
     *
     * @since 3.3.0
     */
    abstract class Base extends \Elementor\Core\Base\Base_Object
    {
        /**
         * Get File Extension
         *
         * Returns the file type's file extension
         *
         * @since 3.3.0
         *
         * @return string - file extension
         */
        public abstract function get_file_extension();
        /**
         * Get Mime Type
         *
         * Returns the file type's mime type
         *
         * @since 3.5.0
         *
         * @return string - file extension
         */
        public abstract function get_mime_type();
        /**
         * Validate File
         *
         * This method give file types the chance to run file-type-specific validations before returning the file for upload.
         *
         * @since 3.3.0
         *
         * @param $file
         * @return bool|\WP_Error
         */
        public function validate_file($file)
        {
        }
        /**
         * Is Upload Allowed
         *
         * This method returns whether the file type is allowed to be uploaded, even if unfiltered uploads are disabled.
         *
         * @since 3.3.0
         *
         * @return bool
         */
        public function is_upload_allowed()
        {
        }
    }
    class Json extends \Elementor\Core\Files\File_Types\Base
    {
        /**
         * Get File Extension
         *
         * Returns the file type's file extension
         *
         * @since 3.3.0
         *
         * @return string - file extension
         */
        public function get_file_extension()
        {
        }
        /**
         * Get Mime Type
         *
         * Returns the file type's mime type
         *
         * @since 3.5.0
         *
         * @return string mime type
         */
        public function get_mime_type()
        {
        }
    }
    /**
     * Elementor File Types Base.
     *
     * The File Types Base class provides base methods used by all file type handler classes.
     * These methods are used in file upl
     *
     * @since 3.3.0
     */
    class Zip extends \Elementor\Core\Files\File_Types\Base
    {
        /**
         * Get File Extension
         *
         * Returns the file type's file extension
         *
         * @since 3.3.0
         *
         * @return string - file extension
         */
        public function get_file_extension()
        {
        }
        /**
         * Get Mime Type
         *
         * Returns the file type's mime type
         *
         * @since 3.5.0
         *
         * @return string mime type
         */
        public function get_mime_type()
        {
        }
        /**
         * Get File Property Name
         *
         * Get the property name to look for in the $_FILES superglobal
         *
         * @since 3.3.0
         *
         * @return string
         */
        public function get_file_property_name()
        {
        }
        /**
         * Extract
         *
         * Performs the extraction of the zip files to a temporary directory.
         * Returns an error if for some reason the ZipArchive utility isn't available.
         * Otherwise, Returns an array containing the temporary extraction directory, and the list of extracted files.
         *
         * @since 3.3.0
         *
         * @param string $file_path
         * @param array|null $allowed_file_types
         * @return array|\WP_Error
         */
        public function extract($file_path, $allowed_file_types)
        {
        }
    }
    class Svg extends \Elementor\Core\Files\File_Types\Base
    {
        /**
         * Inline svg attachment meta key
         */
        const META_KEY = '_elementor_inline_svg';
        const SCRIPT_REGEX = '/(?:\\w+script|data):/xi';
        /**
         * Get File Extension
         *
         * Returns the file type's file extension
         *
         * @since 3.5.0
         * @access public
         *
         * @return string - file extension
         */
        public function get_file_extension()
        {
        }
        /**
         * Get Mime Type
         *
         * Returns the file type's mime type
         *
         * @since 3.5.0
         * @access public
         *
         * @return string mime type
         */
        public function get_mime_type()
        {
        }
        /**
         * Sanitize SVG
         *
         * @since 3.5.0
         * @access public
         *
         * @param $filename
         * @return bool
         */
        public function sanitize_svg($filename)
        {
        }
        /**
         * Validate File
         *
         * @since 3.3.0
         * @access public
         *
         * @param $file
         * @return bool|\WP_Error
         */
        public function validate_file($file)
        {
        }
        /**
         * Sanitizer
         *
         * @since 3.5.0
         * @access public
         *
         * @param $content
         * @return bool|string
         */
        public function sanitizer($content)
        {
        }
        /**
         * WP Prepare Attachment For J
         *
         * Runs on the `wp_prepare_attachment_for_js` filter.
         *
         * @since 3.5.0
         * @access public
         *
         * @param $attachment_data
         * @param $attachment
         * @param $meta
         *
         * @return mixed
         */
        public function wp_prepare_attachment_for_js($attachment_data, $attachment, $meta)
        {
        }
        /**
         * Set Svg Meta Data
         *
         * Adds dimensions metadata to uploaded SVG files, since WordPress doesn't do it.
         *
         * @since 3.5.0
         * @access public
         *
         * @return mixed
         */
        public function set_svg_meta_data($data, $id)
        {
        }
        /**
         * Delete Meta Cache
         *
         * Deletes the Inline SVG post meta entry.
         *
         * @since 3.5.0
         * @access public
         */
        public function delete_meta_cache()
        {
        }
        /**
         * File Sanitizer Can Run
         *
         * Checks if the classes required for the file sanitizer are in memory.
         *
         * @since 3.5.0
         * @access public
         * @static
         *
         * @return bool
         */
        public static function file_sanitizer_can_run()
        {
        }
        /**
         * Get Inline SVG
         *
         * @since 3.5.0
         * @access public
         * @static
         *
         * @param $attachment_id
         * @return bool|mixed|string
         */
        public static function get_inline_svg($attachment_id)
        {
        }
        public function __construct()
        {
        }
    }
}
namespace Elementor\Core\Files {
    /**
     * Elementor uploads manager.
     *
     * Elementor uploads manager handler class is responsible for handling file uploads that are not done with WP Media.
     *
     * @since 3.3.0
     */
    class Uploads_Manager extends \Elementor\Core\Base\Base_Object
    {
        const UNFILTERED_FILE_UPLOADS_KEY = 'elementor_unfiltered_files_upload';
        const INVALID_FILE_CONTENT = 'Invalid Content In File';
        /**
         * Register File Types
         *
         * To Add a new file type to Elementor, with its own handling logic, you need to add it to the $file_types array here.
         *
         * @since 3.3.0
         * @access public
         */
        public function register_file_types()
        {
        }
        /**
         * Extract and Validate Zip
         *
         * This method accepts a $file array (which minimally should include a 'tmp_name')
         *
         * @since 3.3.0
         * @access public
         *
         * @param string $file_path
         * @param array $allowed_file_types
         * @return array|\WP_Error
         */
        public function extract_and_validate_zip($file_path, $allowed_file_types = null)
        {
        }
        /**
         * Handle Elementor Upload
         *
         * This method receives a $file array. If the received file is a Base64 string, the $file array should include a
         * 'fileData' property containing the string, which is decoded and has its contents stored in a temporary file.
         * If the $file parameter passed is a standard $file array, the 'name' and 'tmp_name' properties are used for
         * validation.
         *
         * The file goes through validation; if it passes validation, the file is returned. Otherwise, an error is returned.
         *
         * @since 3.3.0
         * @access public
         *
         * @param array $data
         * @param array $allowed_file_extensions Optional. an array of file types that are allowed to pass validation for each
         * upload.
         * @return array|\WP_Error
         */
        public function handle_elementor_upload(array $data, $allowed_file_extensions = null)
        {
        }
        /**
         * are Unfiltered Uploads Enabled
         *
         * @since 3.5.0
         * @access public
         *
         * @return bool
         */
        public static final function are_unfiltered_uploads_enabled()
        {
        }
        /**
         * Handle Elementor WP Media Upload
         *
         * Runs on the 'wp_handle_upload_prefilter' filter.
         *
         * @since 3.2.0
         * @access public
         *
         * @param $file
         * @return mixed
         */
        public function handle_elementor_wp_media_upload($file)
        {
        }
        /**
         * Get File Type Handler
         *
         * Initialize the proper file type handler according to the file extension
         * and assign it to the file type handlers array.
         *
         * @since 3.3.0
         * @access public
         *
         * @param string|null $file_extension - file extension
         * @return File_Type_Base[]|File_Type_Base
         */
        public function get_file_type_handlers($file_extension = null)
        {
        }
        /**
         * Check filetype and ext
         *
         * A workaround for upload validation which relies on a PHP extension (fileinfo)
         * with inconsistent reporting behaviour.
         * ref: https://core.trac.wordpress.org/ticket/39550
         * ref: https://core.trac.wordpress.org/ticket/40175
         *
         * @since 3.5.0
         * @access public
         *
         * @param $data
         * @param $file
         * @param $filename
         * @param $mimes
         *
         * @return mixed
         */
        public function check_filetype_and_ext($data, $file, $filename, $mimes)
        {
        }
        /**
         * Remove File Or Directory
         *
         * Directory is deleted recursively with all of its contents (subdirectories and files).
         *
         * @since 3.3.0
         * @access public
         *
         * @param string $path
         */
        public function remove_file_or_dir($path)
        {
        }
        /**
         * Create Temp File
         *
         * Create a random temporary file.
         *
         * @since 3.3.0
         * @access public
         *
         * @param string $file_content
         * @param string $file_name
         * @return string|\WP_Error
         */
        public function create_temp_file($file_content, $file_name)
        {
        }
        /**
         * Get Temp Directory
         *
         * Get the temporary files directory path. If the directory does not exist, this method creates it.
         *
         * @since 3.3.0
         * @access public
         *
         * @return string $temp_dir
         */
        public function get_temp_dir()
        {
        }
        /**
         * Create Unique Temp Dir
         *
         * Create a unique temporary directory
         *
         * @since 3.3.0
         * @access public
         *
         * @return string the new directory path
         */
        public function create_unique_dir()
        {
        }
        /**
         * Register Ajax Actions
         *
         * Runs on the 'elementor/ajax/register_actions' hook. Receives the AJAX module as a parameter and registers
         * callbacks for specified action IDs.
         *
         * @since 3.5.0
         * @access public
         *
         * @param Ajax $ajax
         */
        public function register_ajax_actions(\Elementor\Core\Common\Modules\Ajax\Module $ajax)
        {
        }
        /**
         * Set Unfiltered Files Upload
         *
         * @since 3.5.0
         * @access public
         */
        public function enable_unfiltered_files_upload()
        {
        }
        public function ajax_enqueue_google_fonts($data) : bool
        {
        }
        /**
         * Support Unfiltered File Uploads
         *
         * When uploading a file within Elementor, this method adds the registered
         * file types to WordPress' allowed mimes list. This will only happen if the user allowed unfiltered file uploads
         * in Elementor's settings in the admin dashboard.
         *
         * @since 3.5.0
         * @access public
         *
         * @param array $allowed_mimes
         * @return array allowed mime types
         */
        public final function support_unfiltered_elementor_file_uploads($allowed_mimes)
        {
        }
        /**
         * Set Elementor Upload State
         *
         * @since 3.5.0
         * @access public
         *
         * @param $state
         */
        public function set_elementor_upload_state($state)
        {
        }
        /**
         * Is Elementor Media Upload
         *
         * Checks whether the current request includes uploading files via Elementor which are not destined for the Media
         * Library.
         *
         * @since 3.5.0
         * @access public
         *
         * @return bool
         */
        public function is_elementor_media_upload()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace Elementor\Core\Breakpoints {
    class Manager extends \Elementor\Core\Base\Module
    {
        const BREAKPOINT_SETTING_PREFIX = 'viewport_';
        const BREAKPOINT_KEY_MOBILE = 'mobile';
        const BREAKPOINT_KEY_MOBILE_EXTRA = 'mobile_extra';
        const BREAKPOINT_KEY_TABLET = 'tablet';
        const BREAKPOINT_KEY_TABLET_EXTRA = 'tablet_extra';
        const BREAKPOINT_KEY_LAPTOP = 'laptop';
        const BREAKPOINT_KEY_DESKTOP = 'desktop';
        const BREAKPOINT_KEY_WIDESCREEN = 'widescreen';
        public function get_name()
        {
        }
        /**
         * Get Breakpoints
         *
         * Retrieve the array containing instances of all breakpoints existing in the system, or a single breakpoint if a
         * name is passed.
         *
         * @since 3.2.0
         *
         * @param $breakpoint_name
         * @return Breakpoint[]|Breakpoint
         */
        public function get_breakpoints($breakpoint_name = null)
        {
        }
        /**
         * Get Active Breakpoints
         *
         * Retrieve the array of --enabled-- breakpoints, or a single breakpoint if a name is passed.
         *
         * @since 3.2.0
         *
         * @param string|null $breakpoint_name
         * @return Breakpoint[]|Breakpoint
         */
        public function get_active_breakpoints($breakpoint_name = null)
        {
        }
        /**
         * Get Active Devices List
         *
         * Retrieve an array containing the keys of all active devices, including 'desktop'.
         *
         * @since 3.2.0
         *
         * @param array $args
         * @return array
         */
        public function get_active_devices_list($args = [])
        {
        }
        /** Has Custom Breakpoints
         *
         * Checks whether there are currently custom breakpoints saved in the database.
         * Returns true if there are breakpoint values saved in the active kit.
         * If the user has activated any additional custom breakpoints (mobile extra, tablet extra, laptop, widescreen) -
         * that is considered as having custom breakpoints.
         *
         * @since 3.2.0
         *
         * @return boolean
         */
        public function has_custom_breakpoints()
        {
        }
        /**
         * Get Device Min Breakpoint
         *
         * For a given device, return the minimum possible breakpoint. Except for the cases of mobile and widescreen
         * devices, A device's min breakpoint is determined by the previous device's max breakpoint + 1px.
         *
         * @since 3.2.0
         *
         * @param string $device_name
         * @return int the min breakpoint of the passed device
         */
        public function get_device_min_breakpoint($device_name)
        {
        }
        /**
         * Get Desktop Min Breakpoint
         *
         * Returns the minimum possible breakpoint for the default (desktop) device.
         *
         * @since 3.2.0
         *
         * @return int the min breakpoint of the passed device
         */
        public function get_desktop_min_point()
        {
        }
        public function refresh()
        {
        }
        /**
         * Get Responsive Icons Classes Map
         *
         * If a $device parameter is passed, this method retrieves the device's icon class list (the ones attached to the `<i>`
         * element). If no parameter is passed, it returns an array of devices containing each device's icon class list.
         *
         * This method was created because 'mobile_extra' and 'tablet_extra' breakpoint icons need to be tilted by 90
         * degrees, and this tilt is achieved in CSS via the class `eicon-tilted`.
         *
         * @since 3.4.0
         *
         * @return array|string
         */
        public function get_responsive_icons_classes_map($device = null)
        {
        }
        /**
         * Get Default Config
         *
         * Retrieve the default breakpoints config array. The 'selector' property is used for CSS generation (the
         * Stylesheet::add_device() method).
         *
         * @return array
         */
        public static function get_default_config()
        {
        }
        /**
         * Get Breakpoints Config
         *
         * Iterates over an array of all of the system's breakpoints (both active and inactive), queries each breakpoint's
         * class instance, and generates an array containing data on each breakpoint: its label, current value, direction
         * ('min'/'max') and whether it is enabled or not.
         *
         * @return array
         */
        public function get_breakpoints_config()
        {
        }
        /**
         * Get Responsive Control Duplication Mode
         *
         * Retrieve the value of the $responsive_control_duplication_mode private class variable.
         * See the variable's PHPDoc for details.
         *
         * @since 3.4.0
         * @access public
         */
        public function get_responsive_control_duplication_mode()
        {
        }
        /**
         * Set Responsive Control Duplication Mode
         *
         * Sets  the value of the $responsive_control_duplication_mode private class variable.
         * See the variable's PHPDoc for details.
         *
         * @since 3.4.0
         *
         * @access public
         * @param string $mode
         */
        public function set_responsive_control_duplication_mode($mode)
        {
        }
        /**
         * Get Stylesheet Templates Path
         *
         * @since 3.2.0
         * @access public
         * @static
         */
        public static function get_stylesheet_templates_path()
        {
        }
        /**
         * Compile Stylesheet Templates
         *
         * @since 3.2.0
         * @access public
         * @static
         */
        public static function compile_stylesheet_templates()
        {
        }
    }
    class Breakpoint extends \Elementor\Core\Base\Base_Object
    {
        /**
         * Get Name
         *
         * @since 3.2.0
         *
         * @return string
         */
        public function get_name()
        {
        }
        /**
         * Is Enabled
         *
         * Check if the breakpoint is enabled or not. The breakpoint instance receives this data from
         * the Breakpoints Manager.
         *
         * @return bool $is_enabled class variable
         */
        public function is_enabled()
        {
        }
        /**
         * Get Label
         *
         * Retrieve the breakpoint label.
         *
         * @since 3.2.0
         *
         * @return string $label class variable
         */
        public function get_label()
        {
        }
        /**
         * Get Value
         *
         * Retrieve the saved breakpoint value.
         *
         * @since 3.2.0
         *
         * @return int $value class variable
         */
        public function get_value()
        {
        }
        /**
         * Is Custom
         *
         * Check if the breakpoint's value is a custom or default value.
         *
         * @since 3.2.0
         *
         * @return bool $is_custom class variable
         */
        public function is_custom()
        {
        }
        /**
         * Get Default Value
         *
         * Returns the Breakpoint's default value.
         *
         * @since 3.2.0
         *
         * @return int $default_value class variable
         */
        public function get_default_value()
        {
        }
        /**
         * Get Direction
         *
         * Returns the Breakpoint's direction ('min'/'max').
         *
         * @since 3.2.0
         *
         * @return string $direction class variable
         */
        public function get_direction()
        {
        }
        public function __construct($args)
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
         * Retrieve a document after checking it exist and allowed to edit.
         *
         * @since 3.13.0
         *
         * @param int $post_id The post ID of the document.
         *
         * @return Document
         * @throws \Exception
         */
        public function get_with_permissions($id) : \Elementor\Core\Base\Document
        {
        }
        /**
         * A `void` version for `get_with_permissions`.
         *
         * @param $id
         * @return void
         * @throws \Exception
         */
        public function check_permissions($id)
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
         * @param $request
         *
         * @return bool True if changes discarded, False otherwise.
         * @throws \Exception
         *
         * @since 2.0.0
         * @access public
         *
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
        public function register_rest_routes()
        {
        }
    }
}
namespace Elementor\Core\DocumentTypes {
    class Post extends \Elementor\Core\DocumentTypes\PageBase
    {
        /**
         * Get Properties
         *
         * Return the post document configuration properties.
         *
         * @access public
         * @static
         *
         * @return array
         */
        public static function get_properties()
        {
        }
        /**
         * Get Type
         *
         * Return the post document type.
         *
         * @return string
         */
        public static function get_type()
        {
        }
        /**
         * Get Title
         *
         * Return the post document title.
         *
         * @access public
         * @static
         *
         * @return string
         */
        public static function get_title()
        {
        }
        /**
         * Get Plural Title
         *
         * Return the post document plural title.
         *
         * @access public
         * @static
         *
         * @return string
         */
        public static function get_plural_title()
        {
        }
    }
    class Page extends \Elementor\Core\DocumentTypes\PageBase
    {
        const URL_TYPE = 'site_settings';
        const SITE_IDENTITY_TAB = 'settings-site-identity';
        /**
         * Get Properties
         *
         * Return the page document configuration properties.
         *
         * @access public
         * @static
         *
         * @return array
         */
        public static function get_properties()
        {
        }
        /**
         * Get Type
         *
         * Return the page document type.
         *
         * @return string
         */
        public static function get_type()
        {
        }
        /**
         * Get Title
         *
         * Return the page document title.
         *
         * @access public
         * @static
         *
         * @return string
         */
        public static function get_title()
        {
        }
        /**
         * Get Plural Title
         *
         * Return the page document plural title.
         *
         * @access public
         * @static
         *
         * @return string
         */
        public static function get_plural_title()
        {
        }
        public static function get_site_settings_url_config($active_tab_id = null)
        {
        }
        public static function get_create_new_editor_page_url($active_tab = null) : string
        {
        }
        public static function get_elementor_page()
        {
        }
    }
}
namespace Elementor\Core\Base\Traits {
    trait Shared_Widget_Controls_Trait
    {
        protected $border_width_range = ['min' => 0, 'max' => 10, 'step' => 1];
        protected function add_html_tag_control(string $name, string $default = 'h2') : void
        {
        }
        /**
         * Remove any child arrays where all properties are empty
         */
        protected function clean_array($input_array = [])
        {
        }
        protected function get_link_attributes($link = [], $other_attributes = [])
        {
        }
        protected function add_icons_per_row_control(string $name = 'icons_per_row', $options = ['2' => '2', '3' => '3'], string $default = '3', $label = '', $selector_custom_property = '--e-link-in-bio-icon-columns') : void
        {
        }
        protected function add_slider_control(string $name, array $args = []) : void
        {
        }
        protected function add_borders_control(string $prefix, array $show_border_args = [], array $border_width_args = [], array $border_color_args = []) : void
        {
        }
        protected function get_shape_divider($side = 'bottom')
        {
        }
        protected function print_shape_divider($side = 'bottom')
        {
        }
        protected function get_configured_breakpoints($add_desktop = 'true')
        {
        }
        protected function add_hover_animation_control(string $name, array $args = []) : void
        {
        }
    }
}
namespace Elementor\Core\Base\Providers {
    class Social_Network_Provider
    {
        public const FACEBOOK = 'Facebook';
        public const TWITTER = 'X (Twitter)';
        public const INSTAGRAM = 'Instagram';
        public const LINKEDIN = 'LinkedIn';
        public const PINTEREST = 'Pinterest';
        public const YOUTUBE = 'YouTube';
        public const TIKTOK = 'TikTok';
        public const WHATSAPP = 'WhatsApp';
        public const APPLEMUSIC = 'Apple Music';
        public const SPOTIFY = 'Spotify';
        public const SOUNDCLOUD = 'SoundCloud';
        public const BEHANCE = 'Behance';
        public const DRIBBBLE = 'Dribbble';
        public const VIMEO = 'Vimeo';
        public const WAZE = 'Waze';
        public const MESSENGER = 'Messenger';
        public const TELEPHONE = 'Telephone';
        public const EMAIL = 'Email';
        public const URL = 'Url';
        public const FILE_DOWNLOAD = 'File Download';
        public const SMS = 'SMS';
        public const VIBER = 'VIBER';
        public const SKYPE = 'Skype';
        public const VCF = 'Save contact (vCard)';
        public static function get_social_networks_icons() : array
        {
        }
        public static function get_icon_mapping(string $platform) : string
        {
        }
        public static function get_name_mapping(string $platform) : string
        {
        }
        public static function get_text_mapping(string $platform) : string
        {
        }
        public static function get_social_networks_text($providers = []) : array
        {
        }
        public static function build_messenger_link(string $username)
        {
        }
        public static function build_email_link(array $data, string $prefix)
        {
        }
        public static function build_viber_link(string $action, string $number)
        {
        }
    }
}
namespace Elementor\Core\Base\Elements_Iteration_Actions {
    abstract class Base
    {
        /**
         * The current document that the Base class instance was created from.
         */
        protected $document;
        /**
         * Indicates if the methods are being triggered on page save or at render time (value will be either 'save' or 'render').
         *
         * @var string
         */
        protected $mode = '';
        /**
         * Is Action Needed.
         *
         * Runs only at runtime and used as a flag to determine if all methods should run on page render.
         * If returns false, all methods will run only on page save.
         * If returns true, all methods will run on both page render and on save.
         *
         * @since 3.3.0
         * @access public
         *
         * @return bool
         */
        public abstract function is_action_needed();
        /**
         * Unique Element Action.
         *
         * Will be triggered for each unique page element - section / column / widget unique type (heading, icon etc.).
         *
         * @since 3.3.0
         * @access public
         *
         * @return void
         */
        public function unique_element_action(\Elementor\Element_Base $element_data)
        {
        }
        /**
         * Element Action.
         *
         * Will be triggered for each page element - section / column / widget.
         *
         * @since 3.3.0
         * @access public
         *
         * @return void
         */
        public function element_action(\Elementor\Element_Base $element_data)
        {
        }
        /**
         * After Elements Iteration.
         *
         * Will be triggered after all page elements iteration has ended.
         *
         * @since 3.3.0
         * @access public
         *
         * @return void
         */
        public function after_elements_iteration()
        {
        }
        public function set_mode($mode)
        {
        }
        public function __construct($document)
        {
        }
    }
    class Assets extends \Elementor\Core\Base\Elements_Iteration_Actions\Base
    {
        const ASSETS_META_KEY = '_elementor_page_assets';
        public function element_action(\Elementor\Element_Base $element_data)
        {
        }
        public function is_action_needed()
        {
        }
        public function after_elements_iteration()
        {
        }
        public function __construct($document)
        {
        }
    }
}
namespace Elementor\Core\Editor {
    class Notice_Bar extends \Elementor\Core\Base\Base_Object
    {
        protected function get_init_settings()
        {
        }
        public function get_upgrade_text()
        {
        }
        public function get_description()
        {
        }
        public final function get_notice()
        {
        }
        protected function render_action($type)
        {
        }
        public function render()
        {
        }
        public function __construct()
        {
        }
        public function set_notice_dismissed()
        {
        }
        public function register_ajax_actions(\Elementor\Core\Common\Modules\Ajax\Module $ajax)
        {
        }
    }
    class Promotion
    {
        /**
         * @return array
         */
        public function get_elements_promotion()
        {
        }
    }
    /**
     * Elementor editor.
     *
     * Elementor editor handler class is responsible for initializing Elementor
     * editor and register all the actions needed to display the editor.
     *
     * @since 1.0.0
     */
    class Editor
    {
        /**
         * User capability required to access Elementor editor.
         */
        const EDITING_CAPABILITY = 'edit_posts';
        /**
         * The const is deprecated, it remains here for backward compatibility.
         *
         * @deprecated Use App_Bar_Module::EXPERIMENT_NAME instead
         */
        const EDITOR_V2_EXPERIMENT_NAME = \Elementor\Modules\EditorAppBar\Module::EXPERIMENT_NAME;
        /**
         * @var Notice_Bar
         */
        public $notice_bar;
        /**
         * @var Promotion
         */
        public $promotion;
        /**
         * Init.
         *
         * Initialize Elementor editor. Registers all needed actions to run Elementor,
         * removes conflicting actions etc.
         *
         * Fired by `admin_action_elementor` action.
         *
         * @since 1.0.0
         * @access public
         *
         * @param bool $die Optional. Whether to die at the end. Default is `true`.
         */
        public function init($die = true)
        {
        }
        /**
         * Retrieve post ID.
         *
         * Get the ID of the current post.
         *
         * @since 1.8.0
         * @access public
         *
         * @return int Post ID.
         */
        public function get_post_id()
        {
        }
        /**
         * Redirect to new URL.
         *
         * Used as a fallback function for the old URL structure of Elementor page
         * edit URL.
         *
         * Fired by `template_redirect` action.
         *
         * @since 1.6.0
         * @access public
         */
        public function redirect_to_new_url()
        {
        }
        /**
         * Whether the edit mode is active.
         *
         * Used to determine whether we are in the edit mode.
         *
         * @since 1.0.0
         * @access public
         *
         * @param int $post_id Optional. Post ID. Default is `null`, the current
         *                     post ID.
         *
         * @return bool Whether the edit mode is active.
         */
        public function is_edit_mode($post_id = null)
        {
        }
        /**
         * Lock post.
         *
         * Mark the post as currently being edited by the current user.
         *
         * @since 1.0.0
         * @access public
         *
         * @param int $post_id The ID of the post being edited.
         */
        public function lock_post($post_id)
        {
        }
        /**
         * Get locked user.
         *
         * Check what user is currently editing the post.
         *
         * @since 1.0.0
         * @access public
         *
         * @param int $post_id The ID of the post being edited.
         *
         * @return \WP_User|false User information or false if the post is not locked.
         */
        public function get_locked_user($post_id)
        {
        }
        /**
         * NOTICE: This method not in use, it's here for backward compatibility.
         *
         * Print Editor Template.
         *
         * Include the wrapper template of the editor.
         *
         * @since 2.2.0
         * @access public
         */
        public function print_editor_template()
        {
        }
        /**
         * Enqueue scripts.
         *
         * Registers all the editor scripts and enqueues them.
         *
         * @since 1.0.0
         * @access public
         */
        public function enqueue_scripts()
        {
        }
        /**
         * Enqueue styles.
         *
         * Registers all the editor styles and enqueues them.
         *
         * @since 1.0.0
         * @access public
         */
        public function enqueue_styles()
        {
        }
        /**
         * Editor head trigger.
         *
         * Fires the 'elementor/editor/wp_head' action in the head tag in Elementor
         * editor.
         *
         * @since 1.0.0
         * @access public
         */
        public function editor_head_trigger()
        {
        }
        /**
         * WP footer.
         *
         * Prints Elementor editor with all the editor templates, and render controls,
         * widgets and content elements.
         *
         * Fired by `wp_footer` action.
         *
         * @since 1.0.0
         * @access public
         */
        public function wp_footer()
        {
        }
        /**
         * Set edit mode.
         *
         * Used to update the edit mode.
         *
         * @since 1.0.0
         * @access public
         *
         * @param bool $edit_mode Whether the edit mode is active.
         */
        public function set_edit_mode($edit_mode)
        {
        }
        /**
         * Editor constructor.
         *
         * Initializing Elementor editor and redirect from old URL structure of
         * Elementor editor.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * @since 2.2.0
         * @access public
         */
        public function filter_wp_link_query_args($query)
        {
        }
        /**
         * @since 2.2.0
         * @access public
         */
        public function filter_wp_link_query($results)
        {
        }
        public function set_post_id($post_id)
        {
        }
        /**
         * Get elements presets.
         *
         * @return array
         */
        public function get_elements_presets()
        {
        }
    }
}
namespace Elementor\Core\Editor\Loader {
    interface Editor_Loader_Interface
    {
        /**
         * Init function purpose is to prepare some stuff that should be available for other methods
         * and register some hooks
         *
         * @return void
         */
        public function init();
        /**
         * Register all the scripts for the editor.
         *
         * @return void
         */
        public function register_scripts();
        /**
         * Enqueue all the scripts for the editor.
         *
         * @return void
         */
        public function enqueue_scripts();
        /**
         * Register all the styles for the editor.
         *
         * @return void
         */
        public function register_styles();
        /**
         * Enqueue all the styles for the editor.
         *
         * @return void
         */
        public function enqueue_styles();
        /**
         * Print the actual initial html for the editor, later on, the scripts takeover and renders the JS apps.
         *
         * @return void
         */
        public function print_root_template();
        /**
         * Register additional templates that are required for the marionette part of the application
         *
         * @return void
         */
        public function register_additional_templates();
    }
    abstract class Editor_Base_Loader implements \Elementor\Core\Editor\Loader\Editor_Loader_Interface
    {
        /**
         * @var Collection
         */
        protected $config;
        /**
         * @var Assets_Config_Provider
         */
        protected $assets_config_provider;
        /**
         * @param Collection $config
         * @param Assets_Config_Provider $assets_config_provider\
         */
        public function __construct(\Elementor\Core\Utils\Collection $config, \Elementor\Core\Utils\Assets_Config_Provider $assets_config_provider)
        {
        }
        /**
         * @return void
         */
        public function register_scripts()
        {
        }
        /**
         * @return void
         */
        public function enqueue_scripts()
        {
        }
        /**
         * @return void
         */
        public function register_styles()
        {
        }
        /**
         * @return void
         */
        public function enqueue_styles()
        {
        }
        /**
         * @return void
         */
        public function register_additional_templates()
        {
        }
    }
}
namespace Elementor\Core\Editor\Loader\V1 {
    class Editor_V1_Loader extends \Elementor\Core\Editor\Loader\Editor_Base_Loader
    {
        /**
         * @return void
         */
        public function init()
        {
        }
        /**
         * @return void
         */
        public function register_scripts()
        {
        }
        /**
         * @return void
         */
        public function enqueue_scripts()
        {
        }
        /**
         * @return void
         */
        public function print_root_template()
        {
        }
        /**
         * @return void
         */
        public function register_additional_templates()
        {
        }
    }
}
namespace Elementor\Core\Editor\Loader {
    class Editor_Loader_Factory
    {
        /**
         * @return Editor_Loader_Interface
         */
        public static function create()
        {
        }
    }
}
namespace Elementor\Core\Editor\Loader\V2 {
    class Editor_V2_Loader extends \Elementor\Core\Editor\Loader\Editor_Base_Loader
    {
        const APP_PACKAGE = 'editor';
        const ENV_PACKAGE = 'env';
        /**
         * Packages that should only be registered, unless some other asset depends on them.
         */
        const LIBS = ['editor-responsive', 'editor-v1-adapters', self::ENV_PACKAGE, 'http', 'icons', 'locations', 'menus', 'query', 'schema', 'store', 'ui', 'utils', 'wp-media'];
        /**
         * Additional dependencies for packages that rely on global variables, rather than
         * an explicit npm dependency (e.g. `window.elementor`, `window.wp`, etc.).
         */
        const ADDITIONAL_DEPS = ['editor-v1-adapters' => ['elementor-web-cli'], 'wp-media' => ['media-models']];
        /**
         * @return void
         */
        public function init()
        {
        }
        /**
         * @return void
         */
        public function register_scripts()
        {
        }
        /**
         * @return void
         */
        public function enqueue_scripts()
        {
        }
        /**
         * @return void
         */
        public function register_styles()
        {
        }
        /**
         * @return void
         */
        public function enqueue_styles()
        {
        }
        /**
         * @return void
         */
        public function print_root_template()
        {
        }
        public static function get_packages_to_enqueue() : array
        {
        }
    }
}
namespace Elementor\Core\Editor\Loader\Common {
    class Editor_Common_Scripts_Settings
    {
        public static function get()
        {
        }
    }
}
namespace Elementor\Core\Editor\Data\Globals {
    class Controller extends \Elementor\Data\V2\Base\Controller
    {
        public function get_name()
        {
        }
        public function register_endpoints()
        {
        }
        public function get_collection_params()
        {
        }
        public function get_permission_callback($request)
        {
        }
        protected function register_index_endpoint()
        {
        }
    }
}
namespace Elementor\Data\V2\Base {
    /**
     * Class purpose is to separate routing logic into one file.
     */
    abstract class Base_Route
    {
        const AVAILABLE_METHODS = [\WP_REST_Server::READABLE, \WP_REST_Server::CREATABLE, \WP_REST_Server::EDITABLE, \WP_REST_Server::DELETABLE, \WP_REST_Server::ALLMETHODS];
        /**
         * Controller of current endpoint.
         *
         * @var \Elementor\Data\V2\Base\Controller
         */
        protected $controller;
        /**
         * Current route, effect only in case the endpoint behave like sub-endpoint.
         *
         * @var string
         */
        protected $route;
        /**
         * All register routes.
         *
         * @var array
         */
        protected $routes = [];
        /**
         * Registered item route.
         *
         * @var array|null
         */
        protected $item_route = null;
        protected $id_arg_name = 'id';
        protected $id_arg_type_regex = '[\\d]+';
        /**
         * Get base route.
         * This method should always return the base route starts with '/' and ends without '/'.
         *
         * @return string
         */
        public function get_base_route()
        {
        }
        /**
         * Get permission callback.
         *
         * By default get permission callback from the controller.
         *
         * @param \WP_REST_Request $request Full data about the request.
         *
         * @return boolean
         */
        public function get_permission_callback($request)
        {
        }
        /**
         * Retrieves a collection of items.
         *
         * @param \WP_REST_Request $request Full data about the request.
         *
         * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
         */
        protected function get_items($request)
        {
        }
        /**
         * Retrieves one item from the collection.
         *
         * @param string $id
         * @param \WP_REST_Request $request Full data about the request.
         *
         * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
         */
        protected function get_item($id, $request)
        {
        }
        /**
         * Creates multiple items.
         *
         * @param \WP_REST_Request $request Full data about the request.
         *
         * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
         */
        protected function create_items($request)
        {
        }
        /**
         * Creates one item.
         *
         * @param string $id id of request item.
         * @param \WP_REST_Request $request Full data about the request.
         *
         * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
         */
        protected function create_item($id, $request)
        {
        }
        /**
         * Updates multiple items.
         *
         * @param \WP_REST_Request $request Full data about the request.
         *
         * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
         */
        protected function update_items($request)
        {
        }
        /**
         * Updates one item.
         *
         * @param string $id id of request item.
         * @param \WP_REST_Request $request Full data about the request.
         *
         * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
         */
        protected function update_item($id, $request)
        {
        }
        /**
         * Delete multiple items.
         *
         * @param \WP_REST_Request $request Full data about the request.
         *
         * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
         */
        protected function delete_items($request)
        {
        }
        /**
         * Delete one item.
         *
         * @param string $id id of request item.
         * @param \WP_REST_Request $request Full data about the request.
         *
         * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
         */
        protected function delete_item($id, $request)
        {
        }
        /**
         * Register the endpoint.
         *
         * By default: register get items route.
         */
        protected function register()
        {
        }
        protected function register_route($route = '', $methods = \WP_REST_Server::READABLE, $args = [])
        {
        }
        /**
         * Register items route.
         *
         * @param string $methods
         * @param array $args
         */
        public function register_items_route($methods = \WP_REST_Server::READABLE, $args = [])
        {
        }
        /**
         * Register item route.
         *
         * @param string $route
         * @param array $args
         * @param string $methods
         */
        public function register_item_route($methods = \WP_REST_Server::READABLE, $args = [], $route = '/')
        {
        }
        /**
         * Base callback.
         * All reset requests from the client should pass this function.
         *
         * @param string $methods
         * @param \WP_REST_Request $request
         * @param bool $is_multi
         * @param array $args
         *
         * @return mixed|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
         */
        public function base_callback($methods, $request, $is_multi = false, $args = [])
        {
        }
        /**
         * Constructor.
         *
         * run `$this->register()`.
         *
         * @param \Elementor\Data\V2\Base\Controller $controller
         * @param string $route
         */
        protected function __construct(\Elementor\Data\V2\Base\Controller $controller, $route)
        {
        }
    }
    abstract class Endpoint extends \Elementor\Data\V2\Base\Base_Route
    {
        /**
         * Current parent.
         *
         * @var \Elementor\Data\V2\Base\Controller|\Elementor\Data\V2\Base\Endpoint
         */
        protected $parent;
        /**
         * Loaded sub endpoint(s).
         *
         * @var \Elementor\Data\V2\Base\Endpoint[]
         */
        protected $sub_endpoints = [];
        /**
         * Get endpoint name.
         *
         * @return string
         */
        public abstract function get_name();
        /**
         *
         * Get endpoint format.
         * The formats that generated using this function, will be used only be `Data\Manager::run()`.
         *
         * @return string
         */
        public abstract function get_format();
        /**
         * Get controller.
         *
         * @return \Elementor\Data\V2\Base\Controller
         */
        public function get_controller()
        {
        }
        /**
         * Get current parent.
         *
         * @return \Elementor\Data\V2\Base\Controller|\Elementor\Data\V2\Base\Endpoint
         */
        public function get_parent()
        {
        }
        /**
         * Get public name.
         *
         * @return string
         */
        public function get_public_name()
        {
        }
        /**
         * Get full command name ( including index ).
         *
         * @return string
         */
        public function get_full_command()
        {
        }
        /**
         * Get name ancestry format, example: 'alpha/beta/delta'.
         *
         * @return string
         */
        public function get_name_ancestry()
        {
        }
        /**
         * Register sub endpoint.
         *
         * @param \Elementor\Data\V2\Base\Endpoint $endpoint
         *
         * @return \Elementor\Data\V2\Base\Endpoint
         */
        public function register_sub_endpoint(\Elementor\Data\V2\Base\Endpoint $endpoint)
        {
        }
        /**
         * Endpoint constructor.
         *
         * @param \Elementor\Data\V2\Base\Controller|\Elementor\Data\V2\Base\Endpoint $parent
         * @param string $route
         */
        public function __construct($parent, $route = '/')
        {
        }
    }
}
namespace Elementor\Core\Editor\Data\Globals\Endpoints {
    abstract class Base extends \Elementor\Data\V2\Base\Endpoint
    {
        protected function register()
        {
        }
        public function get_items($request)
        {
        }
        /**
         * @inheritDoc
         * @throws \Elementor\Data\V2\Base\Exceptions\Error_404
         */
        public function get_item($id, $request)
        {
        }
        public function create_item($id, $request)
        {
        }
        protected abstract function get_kit_items();
        /**
         * @param array $item frontend format.
         * @return array backend format.
         */
        protected abstract function convert_db_format($item);
    }
    class Colors extends \Elementor\Core\Editor\Data\Globals\Endpoints\Base
    {
        public function get_name()
        {
        }
        public function get_format()
        {
        }
        protected function get_kit_items()
        {
        }
        protected function convert_db_format($item)
        {
        }
    }
    class Typography extends \Elementor\Core\Editor\Data\Globals\Endpoints\Base
    {
        public function get_name()
        {
        }
        public function get_format()
        {
        }
        protected function get_kit_items()
        {
        }
        protected function convert_db_format($item)
        {
        }
    }
}
namespace Elementor\Core\Debug\Classes {
    abstract class Inspection_Base
    {
        /**
         * @return bool
         */
        public abstract function run();
        /**
         * @return string
         */
        public abstract function get_name();
        /**
         * @return string
         */
        public abstract function get_message();
        /**
         * @return string
         */
        public function get_header_message()
        {
        }
        /**
         * @return string
         */
        public abstract function get_help_doc_url();
    }
    class Htaccess extends \Elementor\Core\Debug\Classes\Inspection_Base
    {
        public function __construct()
        {
        }
        public function run()
        {
        }
        public function get_name()
        {
        }
        public function get_message()
        {
        }
        public function get_help_doc_url()
        {
        }
    }
    class Shop_Page_Edit extends \Elementor\Core\Debug\Classes\Inspection_Base
    {
        public function run()
        {
        }
        public function get_name()
        {
        }
        public function get_message()
        {
        }
        public function get_help_doc_url()
        {
        }
        public function get_header_message()
        {
        }
    }
    class Theme_Missing extends \Elementor\Core\Debug\Classes\Inspection_Base
    {
        public function run()
        {
        }
        public function get_name()
        {
        }
        public function get_message()
        {
        }
        public function get_help_doc_url()
        {
        }
    }
}
namespace Elementor\Core\Debug {
    class Inspector
    {
        protected $is_enabled = false;
        protected $log = [];
        /**
         * @since 2.1.2
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * @since 2.1.3
         * @access public
         */
        public function is_enabled()
        {
        }
        /**
         * @since 2.1.3
         * @access public
         */
        public function register_admin_tools_fields(\Elementor\Tools $tools)
        {
        }
        /**
         * @since 2.1.2
         * @access public
         */
        public function parse_template_path($template)
        {
        }
        /**
         * @since 2.1.2
         * @access public
         */
        public function add_log($module, $title, $url = '')
        {
        }
        /**
         * @since 2.1.2
         * @access public
         */
        public function add_menu_in_admin_bar(\WP_Admin_Bar $wp_admin_bar)
        {
        }
    }
    class Loading_Inspection_Manager
    {
        public static $_instance = null;
        public static function instance()
        {
        }
        public function register_inspections()
        {
        }
        /**
         * @param Inspection_Base $inspection
         */
        public function register_inspection($inspection)
        {
        }
        public function run_inspections()
        {
        }
    }
}
namespace Elementor\App {
    class App extends \Elementor\Core\Base\App
    {
        const PAGE_ID = 'elementor-app';
        /**
         * Get module name.
         *
         * Retrieve the module name.
         *
         * @since 3.0.0
         * @access public
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
        public function get_base_url()
        {
        }
        public function fix_submenu($menu)
        {
        }
        public function is_current()
        {
        }
        public function admin_init()
        {
        }
        protected function get_init_settings()
        {
        }
        public function enqueue_app_loader()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace Elementor\App\AdminMenuItems {
    class Theme_Builder_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item
    {
        public function is_visible()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_label()
        {
        }
        public function get_capability()
        {
        }
    }
}
namespace Elementor\App\Modules\SiteEditor {
    /**
     * Site Editor Module
     *
     * Responsible for initializing Elementor App functionality
     */
    class Module extends \Elementor\Core\Base\Module
    {
        /**
         * Get name.
         *
         * @access public
         *
         * @return string
         */
        public function get_name()
        {
        }
        public function add_menu_in_admin_bar($admin_bar_config)
        {
        }
        public function __construct()
        {
        }
    }
}
namespace Elementor\App\Modules\KitLibrary {
    class Module extends \Elementor\Core\Base\Module
    {
        /**
         * Get name.
         *
         * @access public
         *
         * @return string
         */
        public function get_name()
        {
        }
        /**
         * Module constructor.
         */
        public function __construct()
        {
        }
    }
}
namespace Elementor\App\Modules\KitLibrary\Connect {
    class Kit_Library extends \Elementor\Core\Common\Modules\Connect\Apps\Library
    {
        const DEFAULT_BASE_ENDPOINT = 'https://my.elementor.com/api/v1/kits-library';
        const FALLBACK_BASE_ENDPOINT = 'https://ms-8874.elementor.com/api/v1/kits-library';
        public function get_title()
        {
        }
        public function get_all($args = [])
        {
        }
        public function get_by_id($id)
        {
        }
        public function get_taxonomies()
        {
        }
        public function get_manifest($id)
        {
        }
        public function download_link($id)
        {
        }
        protected function get_api_url()
        {
        }
        /**
         * Get all the connect information
         *
         * @return array
         */
        protected function get_connect_info()
        {
        }
        protected function init()
        {
        }
    }
}
namespace Elementor\App\Modules\KitLibrary\Data {
    abstract class Base_Controller extends \Elementor\Data\V2\Base\Controller
    {
        /**
         * @return Repository
         */
        public function get_repository()
        {
        }
    }
}
namespace Elementor\App\Modules\KitLibrary\Data\Kits {
    class Controller extends \Elementor\App\Modules\KitLibrary\Data\Base_Controller
    {
        public function get_name()
        {
        }
        public function get_items($request)
        {
        }
        public function get_item($request)
        {
        }
        public function get_collection_params()
        {
        }
        public function register_endpoints()
        {
        }
        public function get_permission_callback($request)
        {
        }
    }
}
namespace Elementor\App\Modules\KitLibrary\Data\Kits\Endpoints {
    /**
     * @property Controller $controller
     */
    class Download_Link extends \Elementor\Data\V2\Base\Endpoint
    {
        public function get_name()
        {
        }
        public function get_format()
        {
        }
        protected function register()
        {
        }
        public function get_item($id, $request)
        {
        }
    }
    /**
     * @property Controller $controller
     */
    class Favorites extends \Elementor\Data\V2\Base\Endpoint
    {
        public function get_name()
        {
        }
        public function get_format()
        {
        }
        protected function register()
        {
        }
        public function create_item($id, $request)
        {
        }
        public function delete_item($id, $request)
        {
        }
    }
}
namespace Elementor\App\Modules\KitLibrary\Data\Taxonomies {
    class Controller extends \Elementor\App\Modules\KitLibrary\Data\Base_Controller
    {
        public function get_name()
        {
        }
        public function get_collection_params()
        {
        }
        public function get_items($request)
        {
        }
        public function get_permission_callback($request)
        {
        }
    }
}
namespace Elementor\App\Modules\KitLibrary\Data {
    class Repository
    {
        /**
         * There is no label for subscription plan with access_level=0 + it should not
         * be translated.
         */
        const SUBSCRIPTION_PLAN_FREE_TAG = 'Free';
        const TAXONOMIES_KEYS = ['tags', 'categories', 'main_category', 'third_category', 'features', 'types'];
        const KITS_CACHE_KEY = 'elementor_remote_kits';
        const KITS_TAXONOMIES_CACHE_KEY = 'elementor_remote_kits_taxonomies';
        const KITS_CACHE_TTL_HOURS = 12;
        const KITS_TAXONOMIES_CACHE_TTL_HOURS = 12;
        /**
         * @var Kit_Library
         */
        protected $api;
        /**
         * @var User_Favorites
         */
        protected $user_favorites;
        /**
         * @var Collection
         */
        protected $subscription_plans;
        /**
         * Get all kits.
         *
         * @param false $force_api_request
         *
         * @return Collection
         */
        public function get_all($force_api_request = false)
        {
        }
        /**
         * Get specific kit.
         *
         * @param       $id
         * @param array $options
         *
         * @return array|null
         */
        public function find($id, $options = [])
        {
        }
        /**
         * @param false $force_api_request
         *
         * @return Collection
         */
        public function get_taxonomies($force_api_request = false)
        {
        }
        /**
         * @param $id
         *
         * @return array
         */
        public function get_download_link($id)
        {
        }
        /**
         * @param $id
         *
         * @return array
         * @throws \Exception
         */
        public function add_to_favorites($id)
        {
        }
        /**
         * @param $id
         *
         * @return array
         * @throws \Exception
         */
        public function remove_from_favorites($id)
        {
        }
        /**
         * @param Kit_Library    $kit_library
         * @param User_Favorites $user_favorites
         * @param Collection     $subscription_plans
         */
        public function __construct(\Elementor\App\Modules\KitLibrary\Connect\Kit_Library $kit_library, \Elementor\Modules\Library\User_Favorites $user_favorites, \Elementor\Core\Utils\Collection $subscription_plans)
        {
        }
        public static function clear_cache()
        {
        }
    }
}
namespace Elementor\App\Modules\KitLibrary {
    class Kit_Library_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item
    {
        public function is_visible()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_label()
        {
        }
        public function get_capability()
        {
        }
    }
}
namespace Elementor\App\Modules\Onboarding {
    class Features_Usage
    {
        const ONBOARDING_FEATURES_OPTION = '_elementor_onboarding_features';
        public function register()
        {
        }
        public function save_onboarding_features($raw_post_data)
        {
        }
    }
    /**
     * Onboarding Module
     *
     * Responsible for initializing Elementor App functionality
     *
     * @since 3.6.0
     */
    class Module extends \Elementor\Core\Base\Module
    {
        const VERSION = '1.0.0';
        const ONBOARDING_OPTION = 'elementor_onboarded';
        /**
         * Get name.
         *
         * @since 3.6.0
         * @access public
         *
         * @return string
         */
        public function get_name()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace Elementor {
    /**
     * Elementor maintenance.
     *
     * Elementor maintenance handler class is responsible for setting up Elementor
     * activation and uninstallation hooks.
     *
     * @since 1.0.0
     */
    class Maintenance
    {
        /**
         * Activate Elementor.
         *
         * Set Elementor activation hook.
         *
         * Fired by `register_activation_hook` when the plugin is activated.
         *
         * @since 1.0.0
         * @access public
         * @static
         */
        public static function activation($network_wide)
        {
        }
        public static function insert_defaults_options()
        {
        }
        public static function deactivation()
        {
        }
        /**
         * Uninstall Elementor.
         *
         * Set Elementor uninstallation hook.
         *
         * Fired by `register_uninstall_hook` when the plugin is uninstalled.
         *
         * @since 1.0.0
         * @access public
         * @static
         */
        public static function uninstall()
        {
        }
        /**
         * Init.
         *
         * Initialize Elementor Maintenance.
         *
         * @since 1.0.0
         * @access public
         * @static
         */
        public static function init()
        {
        }
    }
    /**
     * Elementor "Settings" page in WordPress Dashboard.
     *
     * Elementor settings page handler class responsible for creating and displaying
     * Elementor "Settings" page in WordPress dashboard.
     *
     * @since 1.0.0
     */
    class Settings extends \Elementor\Settings_Page
    {
        /**
         * Settings page ID for Elementor settings.
         */
        const PAGE_ID = 'elementor';
        /**
         * Upgrade menu priority.
         */
        const MENU_PRIORITY_GO_PRO = 502;
        /**
         * Settings page field for update time.
         */
        const UPDATE_TIME_FIELD = '_elementor_settings_update_time';
        /**
         * Settings page general tab slug.
         */
        const TAB_GENERAL = 'general';
        /**
         * Settings page style tab slug.
         */
        const TAB_STYLE = 'style';
        /**
         * Settings page integrations tab slug.
         */
        const TAB_INTEGRATIONS = 'integrations';
        /**
         * Settings page advanced tab slug.
         */
        const TAB_ADVANCED = 'advanced';
        /**
         * Settings page performance tab slug.
         */
        const TAB_PERFORMANCE = 'performance';
        const ADMIN_MENU_PRIORITY = 10;
        public \Elementor\Modules\Home\Module $home_module;
        /**
         * Register admin menu.
         *
         * Add new Elementor Settings admin menu.
         *
         * Fired by `admin_menu` action.
         *
         * @since 1.0.0
         * @access public
         */
        public function register_admin_menu()
        {
        }
        public function display_home_screen()
        {
        }
        /**
         * Reorder the Elementor menu items in admin.
         * Based on WC.
         *
         * @since 2.4.0
         *
         * @param array $menu_order Menu order.
         * @return array
         */
        public function menu_order($menu_order)
        {
        }
        /**
         * Go Elementor Pro.
         *
         * Redirect the Elementor Pro page the clicking the Elementor Pro menu link.
         *
         * Fired by `admin_init` action.
         *
         * @since 2.0.3
         * @access public
         */
        public function handle_external_redirects()
        {
        }
        /**
         * On admin init.
         *
         * Preform actions on WordPress admin initialization.
         *
         * Fired by `admin_init` action.
         *
         * @since 2.0.0
         * @access public
         */
        public function on_admin_init()
        {
        }
        /**
         * Change "Settings" menu name.
         *
         * Update the name of the Settings admin menu from "Elementor" to "Settings".
         *
         * Fired by `admin_menu` action.
         *
         * @since 1.0.0
         * @access public
         */
        public function admin_menu_change_name()
        {
        }
        /**
         * Update CSS print method.
         *
         * Clear post CSS cache.
         *
         * Fired by `add_option_elementor_css_print_method` and
         * `update_option_elementor_css_print_method` actions.
         *
         * @since 1.7.5
         * @access public
         * @deprecated 3.0.0 Use `Plugin::$instance->files_manager->clear_cache()` method instead.
         */
        public function update_css_print_method()
        {
        }
        /**
         * Create tabs.
         *
         * Return the settings page tabs, sections and fields.
         *
         * @since 1.5.0
         * @access protected
         *
         * @return array An array with the settings page tabs, sections and fields.
         */
        protected function create_tabs()
        {
        }
        /**
         * Get settings page title.
         *
         * Retrieve the title for the settings page.
         *
         * @since 1.5.0
         * @access protected
         *
         * @return string Settings page title.
         */
        protected function get_page_title()
        {
        }
        public function add_generator_tag_settings($settings)
        {
        }
        /**
         * Settings page constructor.
         *
         * Initializing Elementor "Settings" page.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct()
        {
        }
    }
    /**
     * Elementor "Tools" page in WordPress Dashboard.
     *
     * Elementor settings page handler class responsible for creating and displaying
     * Elementor "Tools" page in WordPress dashboard.
     *
     * @since 1.0.0
     */
    class Tools extends \Elementor\Settings_Page
    {
        const CAPABILITY = 'manage_options';
        /**
         * Settings page ID for Elementor tools.
         */
        const PAGE_ID = 'elementor-tools';
        /**
         * Clear cache.
         *
         * Delete post meta containing the post CSS file data. And delete the actual
         * CSS files from the upload directory.
         *
         * Fired by `wp_ajax_elementor_clear_cache` action.
         *
         * @since 1.0.0
         * @access public
         */
        public function ajax_elementor_clear_cache()
        {
        }
        /**
         * Recreate kit.
         *
         * Recreate default kit (only when default kit does not exist).
         *
         * Fired by `wp_ajax_elementor_recreate_kit` action.
         *
         * @since 1.0.0
         * @access public
         */
        public function ajax_elementor_recreate_kit()
        {
        }
        /**
         * Replace URLs.
         *
         * Sends an ajax request to replace old URLs to new URLs. This method also
         * updates all the Elementor data.
         *
         * Fired by `wp_ajax_elementor_replace_url` action.
         *
         * @since 1.1.0
         * @access public
         */
        public function ajax_elementor_replace_url()
        {
        }
        /**
         * Elementor version rollback.
         *
         * Rollback to previous Elementor version.
         *
         * Fired by `admin_post_elementor_rollback` action.
         *
         * @since 1.5.0
         * @access public
         */
        public function post_elementor_rollback()
        {
        }
        /**
         * Tools page constructor.
         *
         * Initializing Elementor "Tools" page.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Create tabs.
         *
         * Return the tools page tabs, sections and fields.
         *
         * @since 1.5.0
         * @access protected
         *
         * @return array An array with the page tabs, sections and fields.
         */
        protected function create_tabs()
        {
        }
        /**
         * Get tools page title.
         *
         * Retrieve the title for the tools page.
         *
         * @since 1.5.0
         * @access protected
         *
         * @return string Tools page title.
         */
        protected function get_page_title()
        {
        }
        /**
         * Check if the current user can access the version control tab and rollback versions.
         *
         * @return bool
         */
        public static function can_user_rollback_versions()
        {
        }
        /**
         * Check if the beta tester should be displayed.
         *
         * @since 3.19.0
         *
         * @return bool
         */
        public function display_beta_tester() : bool
        {
        }
    }
    /**
     * Elementor settings validations.
     *
     * Elementor settings validations handler class is responsible for validating settings
     * fields.
     *
     * @since 1.0.0
     */
    class Settings_Validations
    {
        /**
         * Validate HTML field.
         *
         * Sanitize content for allowed HTML tags and remove backslashes before quotes.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param string $input Input field.
         *
         * @return string Input field.
         */
        public static function html($input)
        {
        }
        /**
         * Validate checkbox list.
         *
         * Make sure that an empty checkbox list field will return an array.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param mixed $input Input field.
         *
         * @return mixed Input field.
         */
        public static function checkbox_list($input)
        {
        }
        /**
         * Current Time
         *
         * Used to return current time
         *
         * @since 2.5.0
         * @access public
         * @static
         *
         * @param mixed $input Input field.
         *
         * @return int
         */
        public static function current_time($input)
        {
        }
        /**
         * Clear cache.
         *
         * Delete post meta containing the post CSS file data. And delete the actual
         * CSS files from the upload directory.
         *
         * @since 1.4.8
         * @access public
         * @static
         *
         * @param mixed $input Input field.
         *
         * @return mixed Input field.
         */
        public static function clear_cache($input)
        {
        }
    }
    /**
     * Elementor settings controls.
     *
     * Elementor settings controls handler class responsible for creating the final
     * HTML for various input field types used in Elementor settings pages.
     *
     * @since 1.0.0
     */
    class Settings_Controls
    {
        /**
         * Render settings control.
         *
         * Generates the final HTML on the frontend for any given field based on
         * the field type (text, select, checkbox, raw HTML, etc.).
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param array $field Optional. Field data. Default is an empty array.
         */
        public static function render($field = [])
        {
        }
    }
}
namespace Elementor\Includes\Settings\AdminMenuItems {
    class Get_Help_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page
    {
        const URL = 'https://go.elementor.com/docs-admin-menu/';
        public function is_visible()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_label()
        {
        }
        public function get_page_title()
        {
        }
        public function get_capability()
        {
        }
        public function render()
        {
        }
    }
    class Admin_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page
    {
        public function __construct(\Elementor\Settings $settings_page)
        {
        }
        public function is_visible()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_label()
        {
        }
        public function get_page_title()
        {
        }
        public function get_position()
        {
        }
        public function get_capability()
        {
        }
        public function render()
        {
        }
    }
    class Getting_Started_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page
    {
        public function is_visible()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_label()
        {
        }
        public function get_page_title()
        {
        }
        public function get_capability()
        {
        }
        public function render()
        {
        }
    }
    class Tools_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page
    {
        public function __construct(\Elementor\Tools $tools_page)
        {
        }
        public function is_visible()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_label()
        {
        }
        public function get_page_title()
        {
        }
        public function get_capability()
        {
        }
        public function render()
        {
        }
    }
}
namespace Elementor {
    /**
     * Elementor beta testers.
     *
     * Elementor beta testers handler class is responsible for the Beta Testers
     * feature that allows developers to test Elementor beta versions.
     *
     * @since 1.5.0
     */
    class Beta_Testers
    {
        const NEWSLETTER_TERMS_URL = 'https://go.elementor.com/beta-testers-newsletter-terms';
        const NEWSLETTER_PRIVACY_URL = 'https://go.elementor.com/beta-testers-newsletter-privacy';
        const BETA_TESTER_SIGNUP = 'beta_tester_signup';
        /**
         * Check version.
         *
         * Checks whether a beta version exist, and retrieve the version data.
         *
         * Fired by `pre_set_site_transient_update_plugins` filter, before WordPress
         * runs the plugin update checker.
         *
         * @since 1.5.0
         * @access public
         *
         * @param array $transient Plugin version data.
         *
         * @return array Plugin version data.
         */
        public function check_version($transient)
        {
        }
        /**
         * Beta testers constructor.
         *
         * Initializing Elementor beta testers.
         *
         * @since 1.5.0
         * @access public
         */
        public function __construct()
        {
        }
    }
    /**
     * Elementor preview.
     *
     * Elementor preview handler class is responsible for initializing Elementor in
     * preview mode.
     *
     * @since 1.0.0
     */
    class Preview extends \Elementor\Core\Base\App
    {
        /**
         * The priority of the preview enqueued styles.
         */
        const ENQUEUED_STYLES_PRIORITY = 20;
        /**
         * Get module name.
         *
         * Retrieve the module name.
         *
         * @since 3.0.0
         * @access public
         * @abstract
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
        /**
         * Init.
         *
         * Initialize Elementor preview mode.
         *
         * Fired by `template_redirect` action.
         *
         * @since 1.0.0
         * @access public
         */
        public function init()
        {
        }
        /**
         * Retrieve post ID.
         *
         * Get the ID of the current post.
         *
         * @since 1.8.0
         * @access public
         *
         * @return int Post ID.
         */
        public function get_post_id()
        {
        }
        /**
         * Is Preview.
         *
         * Whether current request is the elementor preview iframe.
         * The flag is not related to a specific post or edit permissions.
         *
         * @since 2.9.5
         * @access public
         *
         * @return bool
         */
        public function is_preview()
        {
        }
        /**
         * Whether preview mode is active.
         *
         * Used to determine whether we are in the preview mode (iframe).
         *
         * @since 1.0.0
         * @access public
         *
         * @param int $post_id Optional. Post ID. Default is `0`.
         *
         * @return bool Whether preview mode is active.
         */
        public function is_preview_mode($post_id = 0)
        {
        }
        /**
         * Builder wrapper.
         *
         * Used to add an empty HTML wrapper for the builder, the javascript will add
         * the content later.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $content The content of the builder.
         *
         * @return string HTML wrapper for the builder.
         */
        public function builder_wrapper($content)
        {
        }
        public function rocket_loader_filter($tag, $handle, $src)
        {
        }
        /**
         * Elementor Preview footer scripts and styles.
         *
         * Handle styles and scripts from frontend.
         *
         * Fired by `wp_footer` action.
         *
         * @since 2.0.9
         * @access public
         */
        public function wp_footer()
        {
        }
        /**
         * Preview constructor.
         *
         * Initializing Elementor preview.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct()
        {
        }
    }
    /**
     * Elementor shapes.
     *
     * Elementor shapes handler class is responsible for setting up the supported
     * shape dividers.
     *
     * @since 1.3.0
     */
    class Shapes
    {
        /**
         * The exclude filter.
         */
        const FILTER_EXCLUDE = 'exclude';
        /**
         * The include filter.
         */
        const FILTER_INCLUDE = 'include';
        /**
         * Get shapes.
         *
         * Retrieve a shape from the lists of supported shapes. If no shape specified
         * it will return all the supported shapes.
         *
         * @since 1.3.0
         * @access public
         * @static
         *
         * @param array $shape Optional. Specific shape. Default is `null`.
         *
         * @return array The specified shape or a list of all the supported shapes.
         */
        public static function get_shapes($shape = null)
        {
        }
        /**
         * Filter shapes.
         *
         * Retrieve shapes filtered by a specific condition, from the list of
         * supported shapes.
         *
         * @since 1.3.0
         * @access public
         * @static
         *
         * @param string $by     Specific condition to filter by.
         * @param string $filter Optional. Comparison condition to filter by.
         *                       Default is `include`.
         *
         * @return array A list of filtered shapes.
         */
        public static function filter_shapes($by, $filter = self::FILTER_INCLUDE)
        {
        }
        /**
         * Get shape path.
         *
         * For a given shape, retrieve the file path.
         *
         * @since 1.3.0
         * @access public
         * @static
         *
         * @param string $shape       The shape.
         * @param bool   $is_negative Optional. Whether the file name is negative or
         *                            not. Default is `false`.
         *
         * @return string Shape file path.
         */
        public static function get_shape_path($shape, $is_negative = false)
        {
        }
        /**
         * Get Additional Shapes For Config
         *
         * Used to set additional shape paths for editor
         *
         * @since 2.5.0
         *
         * @return array|bool
         */
        public static function get_additional_shapes_for_config()
        {
        }
    }
}
namespace Elementor\TemplateLibrary\Forms {
    class New_Template_Form extends \Elementor\Controls_Stack
    {
        public function get_name()
        {
        }
        /**
         * @throws \Exception
         */
        public function render()
        {
        }
    }
}
namespace Elementor\TemplateLibrary\Classes {
    /**
     * Elementor template library import images.
     *
     * Elementor template library import images handler class is responsible for
     * importing remote images used by the template library.
     *
     * @since 1.0.0
     */
    class Import_Images
    {
        /**
         * Import image.
         *
         * Import a single image from a remote server, upload the image WordPress
         * uploads folder, create a new attachment in the database and updates the
         * attachment metadata.
         *
         * @since 1.0.0
         * @since 3.2.0 New `$parent_post_id` option added
         * @access public
         *
         * @param array $attachment The attachment.
         * @param int $parent_post_id Optional
         *
         * @return false|array Imported image data, or false.
         */
        public function import($attachment, $parent_post_id = null)
        {
        }
        /**
         * Template library import images constructor.
         *
         * Initializing the images import class used by the template library through
         * the WordPress Filesystem API.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct()
        {
        }
    }
}
namespace Elementor\TemplateLibrary {
    /**
     * Elementor template library manager.
     *
     * Elementor template library manager handler class is responsible for
     * initializing the template library.
     *
     * @since 1.0.0
     */
    class Manager
    {
        /**
         * Registered template sources.
         *
         * Holds a list of all the supported sources with their instances.
         *
         * @access protected
         *
         * @var Source_Base[]
         */
        protected $_registered_sources = [];
        // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
        /**
         * @var Wordpress_Adapter_Interface
         */
        protected $wordpress_adapter = null;
        /**
         * @var Elementor_Adapter_Interface
         */
        protected $elementor_adapter = null;
        /**
         * Template library manager constructor.
         *
         * Initializing the template library manager by registering default template
         * sources and initializing ajax calls.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function add_actions()
        {
        }
        /**
         * Get `Import_Images` instance.
         *
         * Retrieve the instance of the `Import_Images` class.
         *
         * @since 1.0.0
         * @access public
         *
         * @return Import_Images Imported images instance.
         */
        public function get_import_images_instance()
        {
        }
        public function set_wordpress_adapter(\Elementor\Core\Isolation\Wordpress_Adapter_Interface $wordpress_adapter)
        {
        }
        public function set_elementor_adapter(\Elementor\Core\Isolation\Elementor_Adapter_Interface $elementor_adapter) : void
        {
        }
        /**
         * Register template source.
         *
         * Used to register new template sources displayed in the template library.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $source_class The name of source class.
         * @param array  $args         Optional. Class arguments. Default is an
         *                             empty array.
         *
         * @return \WP_Error|true True if the source was registered, `WP_Error`
         *                        otherwise.
         */
        public function register_source($source_class, $args = [])
        {
        }
        /**
         * Unregister template source.
         *
         * Remove an existing template sources from the list of registered template
         * sources.
         *
         * @since 1.0.0
         * @deprecated 2.7.0
         * @access public
         *
         * @param string $id The source ID.
         *
         * @return bool Whether the source was unregistered.
         */
        public function unregister_source($id)
        {
        }
        /**
         * Get registered template sources.
         *
         * Retrieve registered template sources.
         *
         * @since 1.0.0
         * @access public
         *
         * @return Source_Base[] Registered template sources.
         */
        public function get_registered_sources()
        {
        }
        /**
         * Get template source.
         *
         * Retrieve single template sources for a given template ID.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $id The source ID.
         *
         * @return false|Source_Base Template sources if one exist, False otherwise.
         */
        public function get_source($id)
        {
        }
        /**
         * Get templates.
         *
         * Retrieve all the templates from all the registered sources.
         *
         * @param array $filter_sources
         * @param bool $force_update
         * @return array
         */
        public function get_templates(array $filter_sources = [], bool $force_update = false) : array
        {
        }
        /**
         * Get library data.
         *
         * Retrieve the library data.
         *
         * @since 1.9.0
         * @access public
         *
         * @param array $args Library arguments.
         *
         * @return array Library data.
         */
        public function get_library_data(array $args)
        {
        }
        /**
         * Save template.
         *
         * Save new or update existing template on the database.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $args Template arguments.
         *
         * @return \WP_Error|int The ID of the saved/updated template.
         */
        public function save_template(array $args)
        {
        }
        /**
         * Update template.
         *
         * Update template on the database.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $template_data New template data.
         *
         * @return \WP_Error|Source_Base Template sources instance if the templates
         *                               was updated, `WP_Error` otherwise.
         */
        public function update_template(array $template_data)
        {
        }
        /**
         * Update templates.
         *
         * Update template on the database.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $args Template arguments.
         *
         * @return \WP_Error|true True if templates updated, `WP_Error` otherwise.
         */
        public function update_templates(array $args)
        {
        }
        /**
         * Get template data.
         *
         * Retrieve the template data.
         *
         * @since 1.5.0
         * @access public
         *
         * @param array $args Template arguments.
         *
         * @return \WP_Error|bool|array ??
         */
        public function get_template_data(array $args)
        {
        }
        /**
         * Delete template.
         *
         * Delete template from the database.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $args Template arguments.
         *
         * @return \WP_Post|\WP_Error|false|null Post data on success, false or null
         *                                       or 'WP_Error' on failure.
         */
        public function delete_template(array $args)
        {
        }
        /**
         * Export template.
         *
         * Export template to a file after ensuring it is a valid Elementor template
         * and checking user permissions for private posts.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $args Template arguments.
         *
         * @return mixed Whether the export succeeded or failed.
         */
        public function export_template(array $args)
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function direct_import_template()
        {
        }
        /**
         * Import template.
         *
         * Import template from a file.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $data
         *
         * @return mixed Whether the export succeeded or failed.
         */
        public function import_template(array $data)
        {
        }
        /**
         * Enable JSON Template Upload
         *
         * Runs on the 'elementor/files/allow-file-type/json' Uploads Manager filter.
         *
         * @since 3.5.0
         * @access public
         *
         * return bool
         */
        public function enable_json_template_upload()
        {
        }
        /**
         * Mark template as favorite.
         *
         * Add the template to the user favorite templates.
         *
         * @since 1.9.0
         * @access public
         *
         * @param array $args Template arguments.
         *
         * @return mixed Whether the template marked as favorite.
         */
        public function mark_template_as_favorite($args)
        {
        }
        public function import_from_json(array $args)
        {
        }
        /**
         * Init ajax calls.
         *
         * Initialize template library ajax calls for allowed ajax requests.
         *
         * @since 2.3.0
         * @access public
         *
         * @param Ajax $ajax
         */
        public function register_ajax_actions(\Elementor\Core\Common\Modules\Ajax\Module $ajax)
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function handle_direct_actions()
        {
        }
    }
    /**
     * Elementor template library source base.
     *
     * Elementor template library source base handler class is responsible for
     * initializing all the methods controlling the source of Elementor templates.
     *
     * @since 1.0.0
     * @abstract
     */
    abstract class Source_Base
    {
        /**
         * Get template ID.
         *
         * Retrieve the template ID.
         *
         * @since 1.0.0
         * @access public
         * @abstract
         */
        public abstract function get_id();
        /**
         * Get template title.
         *
         * Retrieve the template title.
         *
         * @since 1.0.0
         * @access public
         * @abstract
         */
        public abstract function get_title();
        /**
         * Register template data.
         *
         * Used to register custom template data like a post type, a taxonomy or any
         * other data.
         *
         * @since 1.0.0
         * @access public
         * @abstract
         */
        public abstract function register_data();
        /**
         * Get templates.
         *
         * Retrieve templates from the template library.
         *
         * @since 1.0.0
         * @access public
         * @abstract
         *
         * @param array $args Optional. Filter templates list based on a set of
         *                    arguments. Default is an empty array.
         */
        public abstract function get_items($args = []);
        /**
         * Get template.
         *
         * Retrieve a single template from the template library.
         *
         * @since 1.0.0
         * @access public
         * @abstract
         *
         * @param int $template_id The template ID.
         */
        public abstract function get_item($template_id);
        /**
         * Get template data.
         *
         * Retrieve a single template data from the template library.
         *
         * @since 1.5.0
         * @access public
         * @abstract
         *
         * @param array $args Custom template arguments.
         */
        public abstract function get_data(array $args);
        /**
         * Delete template.
         *
         * Delete template from the database.
         *
         * @since 1.0.0
         * @access public
         * @abstract
         *
         * @param int $template_id The template ID.
         */
        public abstract function delete_template($template_id);
        /**
         * Save template.
         *
         * Save new or update existing template on the database.
         *
         * @since 1.0.0
         * @access public
         * @abstract
         *
         * @param array $template_data The template data.
         */
        public abstract function save_item($template_data);
        /**
         * Update template.
         *
         * Update template on the database.
         *
         * @since 1.0.0
         * @access public
         * @abstract
         *
         * @param array $new_data New template data.
         */
        public abstract function update_item($new_data);
        /**
         * Export template.
         *
         * Export template to a file.
         *
         * @since 1.0.0
         * @access public
         * @abstract
         *
         * @param int $template_id The template ID.
         */
        public abstract function export_template($template_id);
        /**
         * Template library source base constructor.
         *
         * Initializing the template library source base by registering custom
         * template data.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Mark template as favorite.
         *
         * Update user meta containing his favorite templates. For a given template
         * ID, add the template to the favorite templates or remove it from the
         * favorites, based on the `favorite` parameter.
         *
         * @since 1.9.0
         * @access public
         *
         * @param int  $template_id The template ID.
         * @param bool $favorite    Optional. Whether the template is marked as
         *                          favorite, or not. Default is true.
         *
         * @return int|bool User meta ID if the key didn't exist, true on successful
         *                  update, false on failure.
         */
        public function mark_as_favorite($template_id, $favorite = true)
        {
        }
        /**
         * Get current user meta.
         *
         * Retrieve Elementor meta data for the current user.
         *
         * @since 1.9.0
         * @access public
         *
         * @param string $item Optional. User meta key. Default is null.
         *
         * @return null|array An array of user meta data, or null otherwise.
         */
        public function get_user_meta($item = null)
        {
        }
        /**
         * Update current user meta.
         *
         * Update user meta data based on meta key an value.
         *
         * @since 1.9.0
         * @access public
         *
         * @param string $key   Optional. User meta key.
         * @param mixed  $value Optional. User meta value.
         *
         * @return int|bool User meta ID if the key didn't exist, true on successful
         *                  update, false on failure.
         */
        public function update_user_meta($key, $value)
        {
        }
        /**
         * Replace elements IDs.
         *
         * For any given Elementor content/data, replace the IDs with new randomly
         * generated IDs.
         *
         * @since 1.0.0
         * @access protected
         *
         * @param array $content Any type of Elementor data.
         *
         * @return mixed Iterated data.
         */
        protected function replace_elements_ids($content)
        {
        }
        /**
         * Get Elementor library user meta prefix.
         *
         * Retrieve user meta prefix used to save Elementor data.
         *
         * @since 1.9.0
         * @access protected
         *
         * @return string User meta prefix.
         */
        protected function get_user_meta_prefix()
        {
        }
        /**
         * Process content for export/import.
         *
         * Process the content and all the inner elements, and prepare all the
         * elements data for export/import.
         *
         * @since 1.5.0
         * @access protected
         *
         * @param array  $content A set of elements.
         * @param string $method  Accepts either `on_export` to export data or
         *                        `on_import` to import data.
         *
         * @return mixed Processed content data.
         */
        protected function process_export_import_content($content, $method)
        {
        }
        /**
         * Process single element content for export/import.
         *
         * Process any given element and prepare the element data for export/import.
         *
         * @since 1.5.0
         * @access protected
         *
         * @param Controls_Stack $element
         * @param string         $method
         *
         * @return array Processed element data.
         */
        protected function process_element_export_import_content(\Elementor\Controls_Stack $element, $method)
        {
        }
    }
    /**
     * Elementor template library remote source.
     *
     * Elementor template library remote source handler class is responsible for
     * handling remote templates from Elementor.com servers.
     *
     * @since 1.0.0
     */
    class Source_Remote extends \Elementor\TemplateLibrary\Source_Base
    {
        const API_TEMPLATES_URL = 'https://my.elementor.com/api/connect/v1/library/templates';
        const TEMPLATES_DATA_TRANSIENT_KEY_PREFIX = 'elementor_remote_templates_data_';
        public function __construct()
        {
        }
        public function add_actions()
        {
        }
        /**
         * Get remote template ID.
         *
         * Retrieve the remote template ID.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string The remote template ID.
         */
        public function get_id()
        {
        }
        /**
         * Get remote template title.
         *
         * Retrieve the remote template title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string The remote template title.
         */
        public function get_title()
        {
        }
        /**
         * Register remote template data.
         *
         * Used to register custom template data like a post type, a taxonomy or any
         * other data.
         *
         * @since 1.0.0
         * @access public
         */
        public function register_data()
        {
        }
        /**
         * Get remote templates.
         *
         * Retrieve remote templates from Elementor.com servers.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $args Optional. Not used in remote source.
         *
         * @return array Remote templates.
         */
        public function get_items($args = [])
        {
        }
        /**
         * Get remote template.
         *
         * Retrieve a single remote template from Elementor.com servers.
         *
         * @since 1.0.0
         * @access public
         *
         * @param int $template_id The template ID.
         *
         * @return array Remote template.
         */
        public function get_item($template_id)
        {
        }
        /**
         * Save remote template.
         *
         * Remote template from Elementor.com servers cannot be saved on the
         * database as they are retrieved from remote servers.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $template_data Remote template data.
         *
         * @return \WP_Error
         */
        public function save_item($template_data)
        {
        }
        /**
         * Update remote template.
         *
         * Remote template from Elementor.com servers cannot be updated on the
         * database as they are retrieved from remote servers.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $new_data New template data.
         *
         * @return \WP_Error
         */
        public function update_item($new_data)
        {
        }
        /**
         * Delete remote template.
         *
         * Remote template from Elementor.com servers cannot be deleted from the
         * database as they are retrieved from remote servers.
         *
         * @since 1.0.0
         * @access public
         *
         * @param int $template_id The template ID.
         *
         * @return \WP_Error
         */
        public function delete_template($template_id)
        {
        }
        /**
         * Export remote template.
         *
         * Remote template from Elementor.com servers cannot be exported from the
         * database as they are retrieved from remote servers.
         *
         * @since 1.0.0
         * @access public
         *
         * @param int $template_id The template ID.
         *
         * @return \WP_Error
         */
        public function export_template($template_id)
        {
        }
        /**
         * Get remote template data.
         *
         * Retrieve the data of a single remote template from Elementor.com servers.
         *
         * @since 1.5.0
         * @access public
         *
         * @param array  $args    Custom template arguments.
         * @param string $context Optional. The context. Default is `display`.
         *
         * @return array|\WP_Error Remote Template data.
         */
        public function get_data(array $args, $context = 'display')
        {
        }
        /**
         * Get templates data from a transient or from a remote request.
         * In any of the following 2 conditions, the remote request will be triggered:
         * 1. Force update - "$force_update = true" parameter was passed.
         * 2. The data saved in the transient is empty or not exist.
         *
         * @param bool $force_update
         * @return array
         */
        protected function get_templates_data(bool $force_update) : array
        {
        }
        /**
         * Get the templates from a remote server and set a transient.
         *
         * @param string $editor_layout_type
         * @return array
         */
        protected function get_templates(string $editor_layout_type) : array
        {
        }
        /**
         * Fetch templates from the remote server.
         *
         * @param string $editor_layout_type
         * @return array|false
         */
        protected function get_templates_remotely(string $editor_layout_type)
        {
        }
        /**
         * Prepare the body arguments for the remote request.
         * @param string $editor_layout_type
         *
         * @return array
         */
        protected function get_templates_body_args(string $editor_layout_type) : array
        {
        }
        /**
         * @since 2.2.0
         * @access private
         */
        protected function prepare_template(array $template_data)
        {
        }
        public function clear_cache()
        {
        }
    }
    /**
     * Elementor template library local source.
     *
     * Elementor template library local source handler class is responsible for
     * handling local Elementor templates saved by the user locally on his site.
     *
     * @since 1.0.0
     */
    class Source_Local extends \Elementor\TemplateLibrary\Source_Base
    {
        /**
         * Elementor template-library post-type slug.
         */
        const CPT = 'elementor_library';
        /**
         * Elementor template-library taxonomy slug.
         */
        const TAXONOMY_TYPE_SLUG = 'elementor_library_type';
        /**
         * Elementor template-library category slug.
         */
        const TAXONOMY_CATEGORY_SLUG = 'elementor_library_category';
        /**
         * Elementor template-library meta key.
         *
         * @deprecated 2.3.0 Use `Elementor\Core\Base\Document::TYPE_META_KEY` const instead.
         */
        const TYPE_META_KEY = '_elementor_template_type';
        /**
         * Elementor template-library temporary files folder.
         */
        const TEMP_FILES_DIR = 'elementor/tmp';
        /**
         * Elementor template-library bulk export action name.
         */
        const BULK_EXPORT_ACTION = 'elementor_export_multiple_templates';
        const ADMIN_MENU_SLUG = 'edit.php?post_type=elementor_library';
        const ADMIN_MENU_PRIORITY = 10;
        const ADMIN_SCREEN_ID = 'edit-elementor_library';
        /**
         * @since 2.3.0
         * @access public
         * @static
         * @return array
         */
        public static function get_template_types()
        {
        }
        /**
         * Get local template type.
         *
         * Retrieve the template type from the post meta.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param int $template_id The template ID.
         *
         * @return mixed The value of meta data field.
         */
        public static function get_template_type($template_id)
        {
        }
        /**
         * Is base templates screen.
         *
         * Whether the current screen base is edit and the post type is template.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @return bool True on base templates screen, False otherwise.
         */
        public static function is_base_templates_screen()
        {
        }
        /**
         * Add template type.
         *
         * Register new template type to the list of supported local template types.
         *
         * @since 1.0.3
         * @access public
         * @static
         *
         * @param string $type Template type.
         */
        public static function add_template_type($type)
        {
        }
        /**
         * Remove template type.
         *
         * Remove existing template type from the list of supported local template
         * types.
         *
         * @since 1.8.0
         * @access public
         * @static
         *
         * @param string $type Template type.
         */
        public static function remove_template_type($type)
        {
        }
        public static function get_admin_url($relative = false)
        {
        }
        /**
         * Get local template ID.
         *
         * Retrieve the local template ID.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string The local template ID.
         */
        public function get_id()
        {
        }
        /**
         * Get local template title.
         *
         * Retrieve the local template title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string The local template title.
         */
        public function get_title()
        {
        }
        /**
         * Register local template data.
         *
         * Used to register custom template data like a post type, a taxonomy or any
         * other data.
         *
         * The local template class registers a new `elementor_library` post type
         * and an `elementor_library_type` taxonomy. They are used to store data for
         * local templates saved by the user on his site.
         *
         * @since 1.0.0
         * @access public
         */
        public function register_data()
        {
        }
        public function admin_title($admin_title, $title)
        {
        }
        public function replace_admin_heading()
        {
        }
        /**
         * Get local templates.
         *
         * Retrieve local templates saved by the user on his site.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $args Optional. Filter templates based on a set of
         *                    arguments. Default is an empty array.
         *
         * @return array Local templates.
         */
        public function get_items($args = [])
        {
        }
        /**
         * Save local template.
         *
         * Save new or update existing template on the database.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $template_data Local template data.
         *
         * @return \WP_Error|int The ID of the saved/updated template, `WP_Error` otherwise.
         */
        public function save_item($template_data)
        {
        }
        protected function is_valid_template_type($type)
        {
        }
        // For testing purposes only, in order to be able to mock the `WP_CLI` constant.
        protected function is_wp_cli()
        {
        }
        /**
         * Update local template.
         *
         * Update template on the database.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $new_data New template data.
         *
         * @return \WP_Error|true True if template updated, `WP_Error` otherwise.
         */
        public function update_item($new_data)
        {
        }
        /**
         * Get local template.
         *
         * Retrieve a single local template saved by the user on his site.
         *
         * @since 1.0.0
         * @access public
         *
         * @param int $template_id The template ID.
         *
         * @return array Local template.
         */
        public function get_item($template_id)
        {
        }
        /**
         * Get template data.
         *
         * Retrieve the data of a single local template saved by the user on his site.
         *
         * @since 1.5.0
         * @access public
         *
         * @param array $args Custom template arguments.
         *
         * @return array Local template data.
         */
        public function get_data(array $args)
        {
        }
        /**
         * Delete local template.
         *
         * Delete template from the database.
         *
         * @since 1.0.0
         * @access public
         *
         * @param int $template_id The template ID.
         *
         * @return \WP_Post|\WP_Error|false|null Post data on success, false or null
         *                                       or 'WP_Error' on failure.
         */
        public function delete_template($template_id)
        {
        }
        /**
         * Export local template.
         *
         * Export template to a file.
         *
         * @since 1.0.0
         * @access public
         *
         * @param int $template_id The template ID.
         *
         * @return \WP_Error WordPress error if template export failed.
         */
        public function export_template($template_id)
        {
        }
        /**
         * Export multiple local templates.
         *
         * Export multiple template to a ZIP file.
         *
         * @since 1.6.0
         * @access public
         *
         * @param array $template_ids An array of template IDs.
         *
         * @return \WP_Error WordPress error if export failed.
         */
        public function export_multiple_templates(array $template_ids)
        {
        }
        /**
         * Import local template.
         *
         * Import template from a file.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $name - The file name
         * @param string $path - The file path
         * @return \WP_Error|array An array of items on success, 'WP_Error' on failure.
         */
        public function import_template($name, $path)
        {
        }
        /**
         * Post row actions.
         *
         * Add an export link to the template library action links table list.
         *
         * Fired by `post_row_actions` filter.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array    $actions An array of row action links.
         * @param \WP_Post $post    The post object.
         *
         * @return array An updated array of row action links.
         */
        public function post_row_actions($actions, \WP_Post $post)
        {
        }
        /**
         * Admin import template form.
         *
         * The import form displayed in "My Library" screen in WordPress dashboard.
         *
         * The form allows the user to import template in json/zip format to the site.
         *
         * Fired by `admin_footer` action.
         *
         * @since 1.0.0
         * @access public
         */
        public function admin_import_template_form()
        {
        }
        /**
         * Block template frontend
         *
         * Don't display the single view of the template library post type in the
         * frontend, for users that don't have the proper permissions.
         *
         * Fired by `template_redirect` action.
         *
         * @since 1.0.0
         * @access public
         */
        public function block_template_frontend()
        {
        }
        /**
         * Is template library supports export.
         *
         * whether the template library supports export.
         *
         * Template saved by the user locally on his site, support export by default
         * but this can be changed using a filter.
         *
         * @since 1.0.0
         * @access public
         *
         * @param int $template_id The template ID.
         *
         * @return bool Whether the template library supports export.
         */
        public function is_template_supports_export($template_id)
        {
        }
        /**
         * Remove Elementor post state.
         *
         * Remove the 'elementor' post state from the display states of the post.
         *
         * Used to remove the 'elementor' post state from the template library items.
         *
         * Fired by `display_post_states` filter.
         *
         * @since 1.8.0
         * @access public
         *
         * @param array    $post_states An array of post display states.
         * @param \WP_Post $post        The current post object.
         *
         * @return array Updated array of post display states.
         */
        public function remove_elementor_post_state_from_library($post_states, $post)
        {
        }
        /**
         * On template save.
         *
         * Run this method when template is being saved.
         *
         * Fired by `save_post` action.
         *
         * @since 1.0.1
         * @access public
         *
         * @param int      $post_id Post ID.
         * @param \WP_Post $post    The current post object.
         */
        public function on_save_post($post_id, \WP_Post $post)
        {
        }
        /**
         * Bulk export action.
         *
         * Adds an 'Export' action to the Bulk Actions drop-down in the template
         * library.
         *
         * Fired by `bulk_actions-edit-elementor_library` filter.
         *
         * @since 1.6.0
         * @access public
         *
         * @param array $actions An array of the available bulk actions.
         *
         * @return array An array of the available bulk actions.
         */
        public function admin_add_bulk_export_action($actions)
        {
        }
        /**
         * Add bulk export action.
         *
         * Handles the template library bulk export action.
         *
         * Fired by `handle_bulk_actions-edit-elementor_library` filter.
         *
         * @since 1.6.0
         * @access public
         *
         * @param string $redirect_to The redirect URL.
         * @param string $action      The action being taken.
         * @param array  $post_ids    The items to take the action on.
         */
        public function admin_export_multiple_templates($redirect_to, $action, $post_ids)
        {
        }
        /**
         * Print admin tabs.
         *
         * Used to output the template library tabs with their labels.
         *
         * Fired by `views_edit-elementor_library` filter.
         *
         * @since 2.0.0
         * @access public
         *
         * @param array $views An array of available list table views.
         *
         * @return array An updated array of available list table views.
         */
        public function admin_print_tabs($views)
        {
        }
        /**
         * Maybe render blank state.
         *
         * When the template library has no saved templates, display a blank admin page offering
         * to create the very first template.
         *
         * Fired by `manage_posts_extra_tablenav` action.
         *
         * @since 2.0.0
         * @access public
         *
         * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
         * @param array $args
         */
        public function maybe_render_blank_state($which, array $args = [])
        {
        }
        /**
         * Print Blank State Template
         *
         * When the an entity (CPT, Taxonomy...etc) has no saved items, print a blank admin page offering
         * to create the very first item.
         *
         * This method is public because it needs to be accessed from outside the Source_Local
         *
         * @since 3.1.0
         * @access public
         *
         * @param string $current_type_label The Entity title
         * @param string $href The URL for the 'Add New' button
         * @param string $description The sub title describing the Entity (Post Type, Taxonomy, etc.)
         */
        public function print_blank_state_template($current_type_label, $href, $description)
        {
        }
        /**
         * Add filter by category.
         *
         * In the templates library, add a filter by Elementor library category.
         *
         * @access public
         *
         * @param string $post_type The post type slug.
         */
        public function add_filter_by_category($post_type)
        {
        }
        /**
         * Filter template types in admin query.
         *
         * Update the template types in the main admin query.
         *
         * Fired by `parse_query` action.
         *
         * @since 2.4.0
         * @access public
         *
         * @param \WP_Query $query The `WP_Query` instance.
         */
        public function admin_query_filter_types(\WP_Query $query)
        {
        }
        /**
         * @since 2.0.6
         * @access public
         */
        public function admin_columns_content($column_name, $post_id)
        {
        }
        /**
         * @since 2.0.6
         * @access public
         */
        public function admin_columns_headers($posts_columns)
        {
        }
        public function get_current_tab_group($default = '')
        {
        }
        /**
         * Template library local source constructor.
         *
         * Initializing the template library local source base by registering custom
         * template data and running custom actions.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct()
        {
        }
    }
}
namespace Elementor\Includes\TemplateLibrary\Sources\AdminMenuItems {
    class Templates_Categories_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item
    {
        public function is_visible()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_label()
        {
        }
        public function get_capability()
        {
        }
    }
    class Saved_Templates_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item
    {
        public function is_visible()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_label()
        {
        }
        public function get_capability()
        {
        }
    }
    class Add_New_Template_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item
    {
        public function is_visible()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_label()
        {
        }
        public function get_capability()
        {
        }
    }
}
namespace Elementor\Includes\TemplateLibrary\Data {
    class Controller extends \Elementor\Data\V2\Base\Controller
    {
        public function get_name()
        {
        }
        public function register_endpoints()
        {
        }
        protected function register_index_endpoint()
        {
        }
        public function get_permission_callback($request)
        {
        }
    }
}
namespace Elementor\Includes\TemplateLibrary\Data\Endpoints {
    class Templates extends \Elementor\Data\V2\Base\Endpoint
    {
        protected function register()
        {
        }
        public function get_name()
        {
        }
        public function get_format()
        {
        }
        public function get_items($request)
        {
        }
        public function create_items($request)
        {
        }
    }
}
namespace Elementor {
    /**
     * Elementor maintenance mode.
     *
     * Elementor maintenance mode handler class is responsible for the Elementor
     * "Maintenance Mode" and the "Coming Soon" features.
     *
     * @since 1.4.0
     */
    class Maintenance_Mode
    {
        /**
         * The options prefix.
         */
        const OPTION_PREFIX = 'elementor_maintenance_mode_';
        /**
         * The maintenance mode.
         */
        const MODE_MAINTENANCE = 'maintenance';
        /**
         * The coming soon mode.
         */
        const MODE_COMING_SOON = 'coming_soon';
        /**
         * Get elementor option.
         *
         * Retrieve elementor option from the database.
         *
         * @since 1.4.0
         * @access public
         * @static
         *
         * @param string $option  Option name. Expected to not be SQL-escaped.
         * @param mixed  $default Optional. Default value to return if the option
         *                        does not exist. Default is false.
         *
         * @return bool False if value was not updated and true if value was updated.
         */
        public static function get($option, $default = false)
        {
        }
        /**
         * Set elementor option.
         *
         * Update elementor option in the database.
         *
         * @since 1.4.0
         * @access public
         * @static
         *
         * @param string $option Option name. Expected to not be SQL-escaped.
         * @param mixed  $value  Option value. Must be serializable if non-scalar.
         *                       Expected to not be SQL-escaped.
         *
         * @return bool False if value was not updated and true if value was updated.
         */
        public static function set($option, $value)
        {
        }
        /**
         * Body class.
         *
         * Add "Maintenance Mode" CSS classes to the body tag.
         *
         * Fired by `body_class` filter.
         *
         * @since 1.4.0
         * @access public
         *
         * @param array $classes An array of body classes.
         *
         * @return array An array of body classes.
         */
        public function body_class($classes)
        {
        }
        /**
         * Template redirect.
         *
         * Redirect to the "Maintenance Mode" template.
         *
         * Fired by `template_redirect` action.
         *
         * @since 1.4.0
         * @access public
         */
        public function template_redirect()
        {
        }
        /**
         * Register settings fields.
         *
         * Adds new "Maintenance Mode" settings fields to Elementor admin page.
         *
         * The method need to receive the an instance of the Tools settings page
         * to add the new maintenance mode functionality.
         *
         * Fired by `elementor/admin/after_create_settings/{$page_id}` action.
         *
         * @since 1.4.0
         * @access public
         *
         * @param Tools $tools An instance of the Tools settings page.
         */
        public function register_settings_fields(\Elementor\Tools $tools)
        {
        }
        /**
         * Add menu in admin bar.
         *
         * Adds "Maintenance Mode" items to the WordPress admin bar.
         *
         * Fired by `admin_bar_menu` filter.
         *
         * @since 1.4.0
         * @access public
         *
         * @param \WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference.
         */
        public function add_menu_in_admin_bar(\WP_Admin_Bar $wp_admin_bar)
        {
        }
        /**
         * Print style.
         *
         * Adds custom CSS to the HEAD html tag. The CSS that emphasise the maintenance
         * mode with red colors.
         *
         * Fired by `admin_head` and `wp_head` filters.
         *
         * @since 1.4.0
         * @access public
         */
        public function print_style()
        {
        }
        public function on_update_mode($old_value, $value)
        {
        }
        /**
         * Maintenance mode constructor.
         *
         * Initializing Elementor maintenance mode.
         *
         * @since 1.4.0
         * @access public
         */
        public function __construct()
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
         * @deprecated 3.0.0
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
        public function initialize_container()
        {
        }
        /**
         * Get the Elementor container or resolve a dependency.
         */
        public function elementor_container($abstract = null)
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
         * @since 2.3.0
         * @access public
         */
        public function init_common()
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
        public static final function get_title()
        {
        }
    }
    /**
     * Elementor conditions.
     *
     * Elementor conditions handler class introduce the compare conditions and the
     * check conditions methods.
     *
     * @since 1.0.0
     */
    class Conditions
    {
        /**
         * Compare conditions.
         *
         * Whether the two values comply the comparison operator.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param mixed  $left_value  First value to compare.
         * @param mixed  $right_value Second value to compare.
         * @param string $operator    Comparison operator.
         *
         * @return bool Whether the two values complies the comparison operator.
         */
        public static function compare($left_value, $right_value, $operator)
        {
        }
        /**
         * Check conditions.
         *
         * Whether the comparison conditions comply.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param array $conditions The conditions to check.
         * @param array $comparison The comparison parameter.
         *
         * @return bool Whether the comparison conditions comply.
         */
        public static function check(array $conditions, array $comparison)
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
         * Register widget type.
         *
         * Add a new widget type to the list of registered widget types.
         *
         * @since 1.0.0
         * @access public
         * @deprecated 3.5.0 Use `register()` method instead.
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
         * @return bool True if the widget was registered.
         * @since 3.5.0
         * @access public
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
         * @deprecated 3.5.0 Use `unregister()` method instead.
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
        /**
         * @throws \Exception
         */
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
         * @throws \Exception
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
         * Widgets with styles.
         *
         * This method returns the list of all the widgets in the `/includes/`
         * folder that have styles.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array The names of the widgets that have styles.
         */
        public function widgets_with_styles() : array
        {
        }
        /**
         * Widgets with responsive styles.
         *
         * This method returns the list of all the widgets in the `/includes/`
         * folder that have responsive styles.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array The names of the widgets that have responsive styles.
         */
        public function widgets_with_responsive_styles() : array
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
        /**
         * @param $experiment_name
         * @param $classes
         * @return void
         */
        public function register_promoted_active_widgets(string $experiment_name, array $classes) : void
        {
        }
    }
    /**
     * Elementor skins manager.
     *
     * Elementor skins manager handler class is responsible for registering and
     * initializing all the supported skins.
     *
     * @since 1.0.0
     */
    class Skins_Manager
    {
        /**
         * Add new skin.
         *
         * Register a single new skin for a widget.
         *
         * @since 1.0.0
         * @access public
         *
         * @param Widget_Base $widget Elementor widget.
         * @param Skin_Base   $skin   Elementor skin.
         *
         * @return true True if skin added.
         */
        public function add_skin(\Elementor\Widget_Base $widget, \Elementor\Skin_Base $skin)
        {
        }
        /**
         * Remove a skin.
         *
         * Unregister an existing skin from a widget.
         *
         * @since 1.0.0
         * @access public
         *
         * @param Widget_Base $widget  Elementor widget.
         * @param string      $skin_id Elementor skin ID.
         *
         * @return true|\WP_Error True if skin removed, `WP_Error` otherwise.
         */
        public function remove_skin(\Elementor\Widget_Base $widget, $skin_id)
        {
        }
        /**
         * Get skins.
         *
         * Retrieve all the skins assigned for a specific widget.
         *
         * @since 1.0.0
         * @access public
         *
         * @param Widget_Base $widget Elementor widget.
         *
         * @return false|array Skins if the widget has skins, False otherwise.
         */
        public function get_skins(\Elementor\Widget_Base $widget)
        {
        }
        /**
         * Skins manager constructor.
         *
         * Initializing Elementor skins manager by requiring the skin base class.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct()
        {
        }
    }
    /**
     * Elementor WordPress widgets manager.
     *
     * Elementor WordPress widgets manager handler class is responsible for
     * registering and initializing all the supported controls, both regular
     * controls and the group controls.
     *
     * @since 1.5.0
     */
    class WordPress_Widgets_Manager
    {
        /**
         * WordPress widgets manager constructor.
         *
         * Initializing the WordPress widgets manager in Elementor editor.
         *
         * @since 1.5.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Before enqueue scripts.
         *
         * Prints custom scripts required to run WordPress widgets in Elementor
         * editor.
         *
         * Fired by `elementor/editor/before_enqueue_scripts` action.
         *
         * @since 1.5.0
         * @access public
         */
        public function before_enqueue_scripts()
        {
        }
        /**
         * WordPress widgets footer.
         *
         * Prints WordPress widgets scripts in Elementor editor footer.
         *
         * Fired by `elementor/editor/footer` action.
         *
         * @since 1.5.0
         * @access public
         */
        public function footer()
        {
        }
    }
    /**
     * Elementor icons manager.
     *
     * Elementor icons manager handler class
     *
     * @since 2.4.0
     */
    class Icons_Manager
    {
        const NEEDS_UPDATE_OPTION = 'icon_manager_needs_update';
        const FONT_ICON_SVG_CLASS_NAME = 'e-font-icon-svg';
        const LOAD_FA4_SHIM_OPTION_KEY = 'elementor_load_fa4_shim';
        const ELEMENTOR_ICONS_VERSION = '5.35.0';
        /**
         * @param $icon
         * @param $attributes
         * @param $tag
         * @return bool|mixed|string
         */
        public static function try_get_icon_html($icon, $attributes = [], $tag = 'i')
        {
        }
        /**
         * register styles
         *
         * Used to register all icon types stylesheets so they could be enqueued later by widgets
         */
        public function register_styles()
        {
        }
        /**
         * Get Icon Manager Tabs
         * @return array
         */
        public static function get_icon_manager_tabs()
        {
        }
        public static function enqueue_shim()
        {
        }
        public static function get_icon_manager_tabs_config()
        {
        }
        /**
         * @deprecated 3.8.0
         */
        public static function render_svg_symbols()
        {
        }
        public static function get_icon_svg_data($icon)
        {
        }
        /**
         * Get font awesome svg.
         * @param $icon array [ 'value' => string, 'library' => string ]
         *
         * @return bool|mixed|string
         */
        public static function get_font_icon_svg($icon, $attributes = [])
        {
        }
        public static function render_uploaded_svg_icon($value)
        {
        }
        public static function render_font_icon($icon, $attributes = [], $tag = 'i')
        {
        }
        /**
         * Render Icon
         *
         * Used to render Icon for \Elementor\Controls_Manager::ICONS
         * @param array $icon             Icon Type, Icon value
         * @param array $attributes       Icon HTML Attributes
         * @param string $tag             Icon HTML tag, defaults to <i>
         *
         * @return mixed|string
         */
        public static function render_icon($icon, $attributes = [], $tag = 'i')
        {
        }
        /**
         * Font Awesome 4 to font Awesome 5 Value Migration
         *
         * used to convert string value of Icon control to array value of Icons control
         * ex: 'fa fa-star' => [ 'value' => 'fas fa-star', 'library' => 'fa-solid' ]
         *
         * @param $value
         *
         * @return array
         */
        public static function fa4_to_fa5_value_migration($value)
        {
        }
        /**
         * on_import_migration
         * @param array $element        settings array
         * @param string $old_control   old control id
         * @param string $new_control   new control id
         * @param bool $remove_old      boolean whether to remove old control or not
         *
         * @return array
         */
        public static function on_import_migration(array $element, $old_control = '', $new_control = '', $remove_old = false)
        {
        }
        /**
         * is_migration_allowed
         * @return bool
         */
        public static function is_migration_allowed()
        {
        }
        /**
         * Register_Admin Settings
         *
         * adds Font Awesome migration / update admin settings
         * @param Settings $settings
         */
        public function register_admin_settings(\Elementor\Settings $settings)
        {
        }
        public function register_admin_tools_settings(\Elementor\Tools $settings)
        {
        }
        /**
         * Get redirect URL when upgrading font awesome.
         *
         * @return string
         */
        public function get_upgrade_redirect_url()
        {
        }
        /**
         * Ajax Upgrade to FontAwesome 5
         */
        public function ajax_upgrade_to_fa5()
        {
        }
        /**
         * Add Update Needed Flag
         * @param array $settings
         *
         * @return array;
         */
        public function add_update_needed_flag($settings)
        {
        }
        public function enqueue_fontawesome_css()
        {
        }
        /**
         * @deprecated 3.1.0
         */
        public function add_admin_strings()
        {
        }
        /**
         * Icons Manager constructor
         */
        public function __construct()
        {
        }
    }
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
         * Gaps control.
         */
        const GAPS = 'gaps';
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
         * Register control.
         *
         * This method adds a new control to the controls list. It adds any given
         * control to any given control instance.
         *
         * @since 1.0.0
         * @access public
         * @deprecated 3.5.0 Use `register()` method instead.
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
         * @deprecated 3.5.0 Use `unregister()` method instead.
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
         * Has Stacks Cache Been Cleared.
         * @since 3.13.0
         * @access public
         * @return bool True if the CSS requires to clear the controls stack cache, False otherwise.
         */
        public function has_stacks_cache_been_cleared()
        {
        }
        /**
         * Clear stack.
         * This method clears the stack.
         * @since 3.13.0
         * @access public
         */
        public function clear_stack_cache()
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
         * @return null|array Stack data if it exists, `null` otherwise.
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
        public function add_custom_attributes_controls(\Elementor\Controls_Stack $controls_stack, string $tab = self::TAB_ADVANCED)
        {
        }
        public function add_display_conditions_controls(\Elementor\Controls_Stack $controls_stack)
        {
        }
        public function add_motion_effects_promotion_control(\Elementor\Controls_Stack $controls_stack)
        {
        }
    }
    /**
     * Elementor images manager.
     *
     * Elementor images manager handler class is responsible for retrieving image
     * details.
     *
     * @since 1.0.0
     */
    class Images_Manager
    {
        /**
         * Get images details.
         *
         * Retrieve details for all the images.
         *
         * Fired by `wp_ajax_elementor_get_images_details` action.
         *
         * @since 1.0.0
         * @access public
         */
        public function get_images_details()
        {
        }
        /**
         * Get image details.
         *
         * Retrieve single image details.
         *
         * Fired by `wp_ajax_elementor_get_image_details` action.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string       $id            Image attachment ID.
         * @param string|array $size          Image size. Accepts any valid image
         *                                    size, or an array of width and height
         *                                    values in pixels (in that order).
         * @param string       $is_first_time Set 'true' string to force reloading
         *                                    all image sizes.
         *
         * @return array URLs with different image sizes.
         */
        public function get_details($id, $size, $is_first_time)
        {
        }
        /**
         * Get Light-Box Image Attributes
         *
         * Used to retrieve an array of image attributes to be used for displaying an image in Elementor's Light Box module.
         *
         * @param int $id       The ID of the image
         *
         * @return array An array of image attributes including `title` and `description`.
         * @since 2.9.0
         * @access public
         */
        public function get_lightbox_image_attributes($id)
        {
        }
        /**
         * Images manager constructor.
         *
         * Initializing Elementor images manager.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct()
        {
        }
    }
    /**
     * Elementor elements manager.
     *
     * Elementor elements manager handler class is responsible for registering and
     * initializing all the supported elements.
     *
     * @since 1.0.0
     */
    class Elements_Manager
    {
        /**
         * Elements constructor.
         *
         * Initializing Elementor elements manager.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Create element instance.
         *
         * This method creates a new element instance for any given element.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array        $element_data Element data.
         * @param array        $element_args Optional. Element arguments. Default is
         *                                   an empty array.
         * @param Element_Base $element_type Optional. Element type. Default is null.
         *
         * @return Element_Base|null Element instance if element created, or null
         *                           otherwise.
         */
        public function create_element_instance(array $element_data, array $element_args = [], \Elementor\Element_Base $element_type = null)
        {
        }
        /**
         * Get element categories.
         *
         * Retrieve the list of categories the element belongs to.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array Element categories.
         */
        public function get_categories()
        {
        }
        /**
         * Add element category.
         *
         * Register new category for the element.
         *
         * @since 1.7.12
         * @access public
         *
         * @param string $category_name       Category name.
         * @param array  $category_properties Category properties.
         */
        public function add_category($category_name, $category_properties)
        {
        }
        /**
         * Register element type.
         *
         * Add new type to the list of registered types.
         *
         * @since 1.0.0
         * @access public
         *
         * @param Element_Base $element Element instance.
         *
         * @return bool Whether the element type was registered.
         */
        public function register_element_type(\Elementor\Element_Base $element)
        {
        }
        /**
         * Unregister element type.
         *
         * Remove element type from the list of registered types.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $name Element name.
         *
         * @return bool Whether the element type was unregister, or not.
         */
        public function unregister_element_type($name)
        {
        }
        /**
         * Get element types.
         *
         * Retrieve the list of all the element types, or if a specific element name
         * was provided retrieve his element types.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $element_name Optional. Element name. Default is null.
         *
         * @return null|Element_Base|Element_Base[] Element types, or a list of all the element
         *                             types, or null if element does not exist.
         */
        public function get_element_types($element_name = null)
        {
        }
        /**
         * Get element types config.
         *
         * Retrieve the config of all the element types.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array Element types config.
         */
        public function get_element_types_config()
        {
        }
        /**
         * Render elements content.
         *
         * Used to generate the elements templates on the editor.
         *
         * @since 1.0.0
         * @access public
         */
        public function render_elements_content()
        {
        }
        public function enqueue_elements_styles()
        {
        }
    }
    /**
     * Elementor user.
     *
     * Elementor user handler class is responsible for checking if the user can edit
     * with Elementor and displaying different admin notices.
     *
     * @since 1.0.0
     */
    class User
    {
        /**
         * Holds the admin notices key.
         *
         * @var string Admin notices key.
         */
        const ADMIN_NOTICES_KEY = 'elementor_admin_notices';
        /**
         * Holds the editor introduction screen key.
         *
         * @var string Introduction key.
         */
        const INTRODUCTION_KEY = 'elementor_introduction';
        /**
         * Holds the beta tester key.
         *
         * @var string Beta tester key.
         */
        const BETA_TESTER_META_KEY = 'elementor_beta_tester';
        /**
         * Holds the URL of the Beta Tester Opt-in API.
         *
         * @since 1.0.0
         *
         * @var string API URL.
         */
        const BETA_TESTER_API_URL = 'https://my.elementor.com/api/v1/beta_tester/';
        /**
         * Holds the dismissed editor notices key.
         *
         * @since 3.19.0
         *
         * @var string Editor notices key.
         */
        const DISMISSED_EDITOR_NOTICES_KEY = 'elementor_dismissed_editor_notices';
        /**
         * Init.
         *
         * Initialize Elementor user.
         *
         * @since 1.0.0
         * @access public
         * @static
         */
        public static function init()
        {
        }
        /**
         * @since 2.1.0
         * @access public
         * @static
         */
        public static function register_ajax_actions(\Elementor\Core\Common\Modules\Ajax\Module $ajax)
        {
        }
        /**
         * Is current user can edit.
         *
         * Whether the current user can edit the post.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param int $post_id Optional. The post ID. Default is `0`.
         *
         * @return bool Whether the current user can edit the post.
         */
        public static function is_current_user_can_edit($post_id = 0)
        {
        }
        /**
         * Is current user can access elementor.
         *
         * Whether the current user role is not excluded by Elementor Settings.
         *
         * @since 2.1.7
         * @access public
         * @static
         *
         * @return bool True if can access, False otherwise.
         */
        public static function is_current_user_in_editing_black_list()
        {
        }
        /**
         * Is current user can edit post type.
         *
         * Whether the current user can edit the given post type.
         *
         * @since 1.9.0
         * @access public
         * @static
         *
         * @param string $post_type the post type slug to check.
         *
         * @return bool True if can edit, False otherwise.
         */
        public static function is_current_user_can_edit_post_type($post_type)
        {
        }
        /**
         * Get user notices.
         *
         * Retrieve the list of notices for the current user.
         *
         * @since 2.0.0
         * @access public
         * @static
         *
         * @return array A list of user notices.
         */
        public static function get_user_notices()
        {
        }
        /**
         * Is admin notice viewed.
         *
         * Whether the admin notice was viewed by the current user.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param int $notice_id The notice ID.
         *
         * @return bool Whether the admin notice was viewed by the user.
         */
        public static function is_user_notice_viewed($notice_id)
        {
        }
        /**
         * Checks whether the current user is allowed to upload JSON files.
         *
         * Note: The 'json-upload' capability is managed by the Role Manager as a part of its blacklist restrictions.
         * In this context, we are negating the user's permission check to use it as a whitelist, allowing uploads.
         *
         * @return bool Whether the current user can upload JSON files.
         */
        public static function is_current_user_can_upload_json()
        {
        }
        public static function is_current_user_can_use_custom_html()
        {
        }
        /**
         * Set admin notice as viewed.
         *
         * Flag the admin notice as viewed by the current user, using an authenticated ajax request.
         *
         * Fired by `wp_ajax_elementor_set_admin_notice_viewed` action.
         *
         * @since 1.0.0
         * @access public
         * @static
         */
        public static function ajax_set_admin_notice_viewed()
        {
        }
        /**
         * @param $notice_id
         * @param $is_viewed
         * @param $meta
         *
         * @return void
         */
        public static function set_user_notice($notice_id, $is_viewed = true, $meta = null)
        {
        }
        /**
         * @since 2.1.0
         * @access public
         * @static
         */
        public static function set_introduction_viewed(array $data)
        {
        }
        /**
         * @throws \Exception
         */
        public static function register_as_beta_tester(array $data)
        {
        }
        /**
         * @param string $key
         *
         * @return array|mixed|string
         * @since  2.1.0
         * @access public
         * @static
         */
        public static function get_introduction_meta($key = '')
        {
        }
        /**
         * Get a user option with default value as fallback.
         *
         * @param string $option  - Option key.
         * @param int    $user_id - User ID
         * @param mixed  $default - Default fallback value.
         *
         * @return mixed
         */
        public static function get_user_option_with_default($option, $user_id, $default)
        {
        }
        /**
         * Get dismissed editor notices.
         *
         * Retrieve the list of dismissed editor notices for the current user.
         *
         * @since 3.19.0
         * @access public
         * @static
         *
         * @return array A list of dismissed editor notices.
         */
        public static function get_dismissed_editor_notices()
        {
        }
        /**
         * Set dismissed editor notices for the current user.
         *
         * @since 3.19.0
         * @access public
         * @static
         *
         * @param array $data Editor notices.
         *
         * @return void
         */
        public static function set_dismissed_editor_notices(array $data)
        {
        }
    }
}
namespace Elementor\Includes {
    class EditorAssetsAPI
    {
        protected array $config;
        const ASSETS_DATA_TRANSIENT_KEY = 'ASSETS_DATA_TRANSIENT_KEY';
        const ASSETS_DATA_URL = 'ASSETS_DATA_URL';
        const ASSETS_DATA_KEY = 'ASSETS_DATA_KEY';
        public function __construct(array $config)
        {
        }
        public function config($key) : string
        {
        }
        public function get_assets_data($force_request = false) : array
        {
        }
    }
}
namespace Elementor {
    /**
     * Elementor rollback.
     *
     * Elementor rollback handler class is responsible for rolling back Elementor to
     * previous version.
     *
     * @since 1.5.0
     */
    class Rollback
    {
        /**
         * Package URL.
         *
         * Holds the package URL.
         *
         * @since 1.5.0
         * @access protected
         *
         * @var string Package URL.
         */
        protected $package_url;
        /**
         * Version.
         *
         * Holds the version.
         *
         * @since 1.5.0
         * @access protected
         *
         * @var string Package URL.
         */
        protected $version;
        /**
         * Plugin name.
         *
         * Holds the plugin name.
         *
         * @since 1.5.0
         * @access protected
         *
         * @var string Plugin name.
         */
        protected $plugin_name;
        /**
         * Plugin slug.
         *
         * Holds the plugin slug.
         *
         * @since 1.5.0
         * @access protected
         *
         * @var string Plugin slug.
         */
        protected $plugin_slug;
        /**
         * Rollback constructor.
         *
         * Initializing Elementor rollback.
         *
         * @since 1.5.0
         * @access public
         *
         * @param array $args Optional. Rollback arguments. Default is an empty array.
         */
        public function __construct($args = [])
        {
        }
        /**
         * Apply package.
         *
         * Change the plugin data when WordPress checks for updates. This method
         * modifies package data to update the plugin from a specific URL containing
         * the version package.
         *
         * @since 1.5.0
         * @access protected
         */
        protected function apply_package()
        {
        }
        /**
         * Upgrade.
         *
         * Run WordPress upgrade to rollback Elementor to previous version.
         *
         * @since 1.5.0
         * @access protected
         */
        protected function upgrade()
        {
        }
        /**
         * Run.
         *
         * Rollback Elementor to previous versions.
         *
         * @since 1.5.0
         * @access public
         */
        public function run()
        {
        }
    }
    /**
     * Elementor control base multiple.
     *
     * An abstract class for creating new controls in the panel that return
     * more than a single value. Each value of the multi-value control will
     * be returned as an item in a `key => value` array.
     *
     * @since 1.0.0
     * @abstract
     */
    abstract class Control_Base_Multiple extends \Elementor\Base_Data_Control
    {
        /**
         * Get multiple control default value.
         *
         * Retrieve the default value of the multiple control. Used to return the default
         * values while initializing the multiple control.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array Control default value.
         */
        public function get_default_value()
        {
        }
        /**
         * Get multiple control value.
         *
         * Retrieve the value of the multiple control from a specific Controls_Stack settings.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $control  Control
         * @param array $settings Settings
         *
         * @return mixed Control values.
         */
        public function get_value($control, $settings)
        {
        }
        /**
         * Get multiple control style value.
         *
         * Retrieve the style of the control. Used when adding CSS rules to the control
         * while extracting CSS from the `selectors` data argument.
         *
         * @since 1.0.5
         * @since 2.3.3 New `$control_data` parameter added.
         * @access public
         *
         * @param string $css_property  CSS property.
         * @param array $control_value Control value.
         * @param array  $control_data Control Data.
         *
         * @return array Control style value.
         */
        public function get_style_value($css_property, $control_value, array $control_data)
        {
        }
    }
    /**
     * Elementor control base units.
     *
     * An abstract class for creating new unit controls in the panel.
     *
     * @since 1.0.0
     * @abstract
     */
    abstract class Control_Base_Units extends \Elementor\Control_Base_Multiple
    {
        /**
         * Get units control default value.
         *
         * Retrieve the default value of the units control. Used to return the default
         * values while initializing the units control.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array Control default value.
         */
        public function get_default_value()
        {
        }
        /**
         * Get units control default settings.
         *
         * Retrieve the default settings of the units control. Used to return the default
         * settings while initializing the units control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Print units control settings.
         *
         * Used to generate the units control template in the editor.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function print_units_template()
        {
        }
        public function get_style_value($css_property, $control_value, array $control_data)
        {
        }
    }
    /**
     * Elementor slider control.
     *
     * A base control for creating slider control. Displays a draggable range slider.
     * The slider control can optionally have a number of unit types (`size_units`)
     * for the user to choose from. The control also accepts a range argument that
     * allows you to set the `min`, `max` and `step` values per unit type.
     *
     * @since 1.0.0
     */
    class Control_Slider extends \Elementor\Control_Base_Units
    {
        /**
         * Get slider control type.
         *
         * Retrieve the control type, in this case `slider`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get slider control default values.
         *
         * Retrieve the default value of the slider control. Used to return the default
         * values while initializing the slider control.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array Control default value.
         */
        public function get_default_value()
        {
        }
        /**
         * Get slider control default settings.
         *
         * Retrieve the default settings of the slider control. Used to return the
         * default settings while initializing the slider control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Render slider control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor base UI control.
     *
     * An abstract class for creating new UI controls in the panel.
     *
     * @abstract
     */
    abstract class Base_UI_Control extends \Elementor\Base_Control
    {
        /**
         * Get features.
         *
         * Retrieve the list of all the available features.
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
    }
    /**
     * Elementor tabs control.
     *
     * A base control for creating tabs control. Displays a tabs header for `tab`
     * controls.
     *
     * Note: Do not use it directly, instead use: `$widget->start_controls_tabs()`
     * and in the end `$widget->end_controls_tabs()`.
     *
     * @since 1.0.0
     */
    class Control_Tabs extends \Elementor\Base_UI_Control
    {
        /**
         * Get tabs control type.
         *
         * Retrieve the control type, in this case `tabs`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Render tabs control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor WYSIWYG control.
     *
     * A base control for creating WYSIWYG control. Displays a WordPress WYSIWYG
     * (TinyMCE) editor.
     *
     * @since 1.0.0
     */
    class Control_Wysiwyg extends \Elementor\Base_Data_Control
    {
        /**
         * Get wysiwyg control type.
         *
         * Retrieve the control type, in this case `wysiwyg`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Render wysiwyg control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
        /**
         * Retrieve textarea control default settings.
         *
         * Get the default settings of the textarea control. Used to return the
         * default settings while initializing the textarea control.
         *
         * @since 2.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
    }
    /**
     * Elementor code control.
     *
     * A base control for creating code control. Displays a code editor textarea.
     * Based on Ace editor (@see https://ace.c9.io/).
     *
     * @since 1.0.0
     */
    class Control_Code extends \Elementor\Base_Data_Control
    {
        /**
         * Get code control type.
         *
         * Retrieve the control type, in this case `code`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get code control default settings.
         *
         * Retrieve the default settings of the code control. Used to return the default
         * settings while initializing the code control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Render code control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor icon control.
     *
     * A base control for creating an icon control. Displays a font icon select box
     * field. The control accepts `include` or `exclude` arguments to set a partial
     * list of icons.
     *
     * @since 1.0.0
     * @deprecated 2.6.0 Use `Control_Icons` class instead.
     */
    class Control_Icon extends \Elementor\Base_Data_Control
    {
        /**
         * Get icon control type.
         *
         * Retrieve the control type, in this case `icon`.
         *
         * @since 1.0.0
         * @deprecated 2.6.0 Use `Control_Icons` class instead.
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get icons.
         *
         * Retrieve all the available icons.
         *
         * @since 1.0.0
         * @deprecated 2.6.0 Use `Control_Icons` class instead.
         * @access public
         * @static
         *
         * @return array Available icons.
         */
        public static function get_icons()
        {
        }
        /**
         * Get icons control default settings.
         *
         * Retrieve the default settings of the icons control. Used to return the default
         * settings while initializing the icons control.
         *
         * @since 1.0.0
         * @deprecated 2.6.0 Use `Control_Icons` class instead.
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Render icons control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @deprecated 2.6.0 Use `Control_Icons` class instead.
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor Deprecated Notice control.
     *
     * A base control specific for creating Deprecation Notices control.
     * Displays a warning notice in the panel.
     *
     * @since 2.6.0
     */
    class Control_Deprecated_Notice extends \Elementor\Base_UI_Control
    {
        /**
         * Get deprecated-notice control type.
         *
         * Retrieve the control type, in this case `deprecated_notice`.
         *
         * @since 2.6.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Render deprecated notice control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 2.6.0
         * @access public
         */
        public function content_template()
        {
        }
        /**
         * Get deprecated-notice control default settings.
         *
         * Retrieve the default settings of the deprecated notice control. Used to return the
         * default settings while initializing the deprecated notice control.
         *
         * @since 2.6.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
    }
    /**
     * Elementor choose control.
     *
     * A base control for creating choose control. Displays radio buttons styled as
     * groups of buttons with icons for each option.
     *
     * @since 1.0.0
     */
    class Control_Choose extends \Elementor\Base_Data_Control
    {
        /**
         * Get choose control type.
         *
         * Retrieve the control type, in this case `choose`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Render choose control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
        /**
         * Get choose control default settings.
         *
         * Retrieve the default settings of the choose control. Used to return the
         * default settings while initializing the choose control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
    }
    /**
     * Elementor gallery control.
     *
     * A base control for creating gallery chooser control. Based on the WordPress
     * media library galleries. Used to select images from the WordPress media library.
     *
     * @since 1.0.0
     */
    class Control_Gallery extends \Elementor\Base_Data_Control
    {
        /**
         * Get gallery control type.
         *
         * Retrieve the control type, in this case `gallery`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Import gallery images.
         *
         * Used to import gallery control files from external sites while importing
         * Elementor template JSON file, and replacing the old data.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $settings Control settings
         *
         * @return array Control settings.
         */
        public function on_import($settings)
        {
        }
        /**
         * Render gallery control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
        /**
         * Get gallery control default settings.
         *
         * Retrieve the default settings of the gallery control. Used to return the
         * default settings while initializing the gallery control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Get gallery control default values.
         *
         * Retrieve the default value of the gallery control. Used to return the default
         * values while initializing the gallery control.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array Control default value.
         */
        public function get_default_value()
        {
        }
    }
    /**
     * Elementor image dimensions control.
     *
     * A base control for creating image dimension control. Displays image width
     * input, image height input and an apply button.
     *
     * @since 1.0.0
     */
    class Control_Image_Dimensions extends \Elementor\Control_Base_Multiple
    {
        /**
         * Get image dimensions control type.
         *
         * Retrieve the control type, in this case `image_dimensions`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get image dimensions control default values.
         *
         * Retrieve the default value of the image dimensions control. Used to return the
         * default values while initializing the image dimensions control.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array Control default value.
         */
        public function get_default_value()
        {
        }
        /**
         * Get image dimensions control default settings.
         *
         * Retrieve the default settings of the image dimensions control. Used to return
         * the default settings while initializing the image dimensions control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Render image dimensions control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    abstract class Base_Icon_Font
    {
        /**
         * Get Icon type.
         *
         * Retrieve the icon type.
         *
         * @access public
         * @abstract
         */
        public abstract function get_type();
        /**
         * Enqueue Icon scripts and styles.
         *
         * Used to register and enqueue custom scripts and styles used by the Icon.
         *
         * @access public
         */
        public abstract function enqueue();
        /**
         * get_css_prefix
         * @return string
         */
        public abstract function get_css_prefix();
        public abstract function get_icons();
        public function __construct()
        {
        }
    }
    /**
     * Elementor number control.
     *
     * A base control for creating a number control. Displays a simple number input.
     *
     * @since 1.0.0
     */
    class Control_Number extends \Elementor\Base_Data_Control
    {
        /**
         * Get number control type.
         *
         * Retrieve the control type, in this case `number`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get number control default settings.
         *
         * Retrieve the default settings of the number control. Used to return the
         * default settings while initializing the number control.
         *
         * @since 1.5.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Render number control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor text shadow control.
     *
     * A base control for creating text shadows control. Displays input fields for
     * horizontal shadow, vertical shadow, shadow blur and shadow color.
     *
     * @since 1.6.0
     */
    class Control_Text_Shadow extends \Elementor\Control_Base_Multiple
    {
        /**
         * Get text shadow control type.
         *
         * Retrieve the control type, in this case `text_shadow`.
         *
         * @since 1.6.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get text shadow control default values.
         *
         * Retrieve the default value of the text shadow control. Used to return the
         * default values while initializing the text shadow control.
         *
         * @since 1.6.0
         * @access public
         *
         * @return array Control default value.
         */
        public function get_default_value()
        {
        }
        /**
         * Get text shadow control sliders.
         *
         * Retrieve the sliders of the text shadow control. Sliders are used while
         * rendering the control output in the editor.
         *
         * @since 1.6.0
         * @access public
         *
         * @return array Control sliders.
         */
        public function get_sliders()
        {
        }
        /**
         * Render text shadow control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.6.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor animation control.
     *
     * A base control for creating entrance animation control. Displays a select box
     * with the available entrance animation effects @see Control_Animation::get_animations() .
     *
     * @since 1.0.0
     */
    class Control_Animation extends \Elementor\Base_Data_Control
    {
        /**
         * Get control type.
         *
         * Retrieve the animation control type.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Retrieve default control settings.
         *
         * Get the default settings of the control. Used to return the default
         * settings while initializing the control.
         *
         * @since 2.5.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Get animations list.
         *
         * Retrieve the list of all the available animations.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @return array Control type.
         */
        public static function get_animations()
        {
        }
        public static function get_default_animations()
        {
        }
        /**
         * Render animations control template.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
        public static function get_assets($setting)
        {
        }
    }
    /**
     * Elementor exit animation control.
     *
     * A control for creating exit animation. Displays a select box
     * with the available exit animation effects @see Control_Exit_Animation::get_animations() .
     *
     * @since 2.5.0
     */
    class Control_Exit_Animation extends \Elementor\Control_Animation
    {
        /**
         * Get control type.
         *
         * Retrieve the animation control type.
         *
         * @since 2.5.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get animations list.
         *
         * Retrieve the list of all the available animations.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @return array Control type.
         */
        public static function get_animations()
        {
        }
        public static function get_default_animations() : array
        {
        }
        public static function get_assets($setting)
        {
        }
    }
    /**
     * Elementor box shadow control.
     *
     * A base control for creating box shadows control. Displays input fields for
     * horizontal shadow, vertical shadow, shadow blur, shadow spread and shadow
     * color.
     *
     * @since 1.0.0
     */
    class Control_Box_Shadow extends \Elementor\Control_Base_Multiple
    {
        /**
         * Get box shadow control type.
         *
         * Retrieve the control type, in this case `box_shadow`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get box shadow control default value.
         *
         * Retrieve the default value of the box shadow control. Used to return the
         * default values while initializing the box shadow control.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array Control default value.
         */
        public function get_default_value()
        {
        }
        /**
         * Get box shadow control sliders.
         *
         * Retrieve the sliders of the box shadow control. Sliders are used while
         * rendering the control output in the editor.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array Control sliders.
         */
        public function get_sliders()
        {
        }
        /**
         * Render box shadow control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor dimension control.
     *
     * A base control for creating dimension control. Displays input fields for top,
     * right, bottom, left and the option to link them together.
     *
     * @since 1.0.0
     */
    class Control_Dimensions extends \Elementor\Control_Base_Units
    {
        /**
         * Get dimensions control type.
         *
         * Retrieve the control type, in this case `dimensions`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get dimensions control default values.
         *
         * Retrieve the default value of the dimensions control. Used to return the
         * default values while initializing the dimensions control.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array Control default value.
         */
        public function get_default_value()
        {
        }
        public function get_singular_name()
        {
        }
        /**
         * Get dimensions control default settings.
         *
         * Retrieve the default settings of the dimensions control. Used to return the
         * default settings while initializing the dimensions control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        protected function get_dimensions()
        {
        }
        /**
         * Render dimensions control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor divider control.
     *
     * A base control for creating divider control. Displays horizontal line in
     * the panel.
     *
     * @since 2.0.0
     */
    class Control_Divider extends \Elementor\Base_UI_Control
    {
        /**
         * Get divider control type.
         *
         * Retrieve the control type, in this case `divider`.
         *
         * @since 2.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Render divider control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 2.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor hidden control.
     *
     * A base control for creating hidden control. Used to save additional data in
     * the database without a visual presentation in the panel.
     *
     * @since 1.0.0
     */
    class Control_Hidden extends \Elementor\Base_Data_Control
    {
        /**
         * Get hidden control type.
         *
         * Retrieve the control type, in this case `hidden`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Render hidden control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor alert control.
     *
     * A base control for creating alerts in the Editor panels.
     *
     * @since 3.19.0
     */
    class Control_Alert extends \Elementor\Base_UI_Control
    {
        /**
         * Get alert control type.
         *
         * Retrieve the control type, in this case `alert`.
         *
         * @since 3.19.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Render alert control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 3.19.0
         * @access public
         */
        public function content_template()
        {
        }
        /**
         * Get alert control default settings.
         *
         * Retrieve the default settings of the alert control. Used to return the
         * default settings while initializing the alert control.
         *
         * @since 3.19.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
    }
    /**
     * Elementor heading control.
     *
     * A base control for creating heading control. Displays a text heading between
     * controls in the panel.
     *
     * @since 1.0.0
     */
    class Control_Heading extends \Elementor\Base_UI_Control
    {
        /**
         * Get heading control type.
         *
         * Retrieve the control type, in this case `heading`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get heading control default settings.
         *
         * Retrieve the default settings of the heading control. Used to return the
         * default settings while initializing the heading control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Render heading control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor popover toggle control.
     *
     * A base control for creating a popover toggle control. By default displays a toggle
     * button to open and close a popover.
     *
     * @since 1.9.0
     */
    class Control_Popover_Toggle extends \Elementor\Base_Data_Control
    {
        /**
         * Get popover toggle control type.
         *
         * Retrieve the control type, in this case `popover_toggle`.
         *
         * @since 1.9.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get popover toggle control default settings.
         *
         * Retrieve the default settings of the popover toggle control. Used to
         * return the default settings while initializing the popover toggle
         * control.
         *
         * @since 1.9.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Render popover toggle control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.9.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor tab control.
     *
     * A base control for creating tab control. Displays a tab header for a set of
     * controls.
     *
     * Note: Do not use it directly, instead use: `$widget->start_controls_tab()`
     * and in the end `$widget->end_controls_tab()`.
     *
     * @since 1.0.0
     */
    class Control_Tab extends \Elementor\Base_UI_Control
    {
        /**
         * Get tab control type.
         *
         * Retrieve the control type, in this case `tab`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Render tab control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor date/time control.
     *
     * A base control for creating date time control. Displays a date/time picker
     * based on the Flatpickr library @see https://chmln.github.io/flatpickr/ .
     *
     * @since 1.0.0
     */
    class Control_Date_Time extends \Elementor\Base_Data_Control
    {
        /**
         * Get date time control type.
         *
         * Retrieve the control type, in this case `date_time`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get date time control default settings.
         *
         * Retrieve the default settings of the date time control. Used to return the
         * default settings while initializing the date time control.
         *
         * @since 1.8.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Render date time control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
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
    }
    class Group_Control_Flex_Item extends \Elementor\Group_Control_Base
    {
        protected static $fields;
        public static function get_type()
        {
        }
        protected function init_fields()
        {
        }
        protected function get_default_options()
        {
        }
    }
    /**
     * Elementor border control.
     *
     * A base control for creating border control. Displays input fields to define
     * border type, border width and border color.
     *
     * @since 1.0.0
     */
    class Group_Control_Border extends \Elementor\Group_Control_Base
    {
        /**
         * Fields.
         *
         * Holds all the border control fields.
         *
         * @since 1.0.0
         * @access protected
         * @static
         *
         * @var array Border control fields.
         */
        protected static $fields;
        /**
         * Get border control type.
         *
         * Retrieve the control type, in this case `border`.
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
         * Initialize border control fields.
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
         * Get default options.
         *
         * Retrieve the default options of the border control. Used to return the
         * default options while initializing the border control.
         *
         * @since 1.9.0
         * @access protected
         *
         * @return array Default border control options.
         */
        protected function get_default_options()
        {
        }
    }
    /**
     * Elementor text shadow control.
     *
     * A base control for creating text shadow control. Displays input fields to define
     * the text shadow including the horizontal shadow, vertical shadow, shadow blur and
     * shadow color.
     *
     * @since 1.6.0
     */
    class Group_Control_Text_Shadow extends \Elementor\Group_Control_Base
    {
        /**
         * Fields.
         *
         * Holds all the text shadow control fields.
         *
         * @since 1.6.0
         * @access protected
         * @static
         *
         * @var array Text shadow control fields.
         */
        protected static $fields;
        /**
         * Get text shadow control type.
         *
         * Retrieve the control type, in this case `text-shadow`.
         *
         * @since 1.6.0
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
         * Initialize text shadow control fields.
         *
         * @since 1.6.0
         * @access protected
         *
         * @return array Control fields.
         */
        protected function init_fields()
        {
        }
        /**
         * Get default options.
         *
         * Retrieve the default options of the text shadow control. Used to return the
         * default options while initializing the text shadow control.
         *
         * @since 1.9.0
         * @access protected
         *
         * @return array Default text shadow control options.
         */
        protected function get_default_options()
        {
        }
    }
    /**
     * Elementor image size control.
     *
     * A base control for creating image size control. Displays input fields to define
     * one of the default image sizes (thumbnail, medium, medium_large, large) or custom
     * image dimensions.
     *
     * @since 1.0.0
     */
    class Group_Control_Image_Size extends \Elementor\Group_Control_Base
    {
        /**
         * Fields.
         *
         * Holds all the image size control fields.
         *
         * @since 1.2.2
         * @access protected
         * @static
         *
         * @var array Image size control fields.
         */
        protected static $fields;
        /**
         * Get image size control type.
         *
         * Retrieve the control type, in this case `image-size`.
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
         * Get attachment image HTML.
         *
         * Retrieve the attachment image HTML code.
         *
         * Note that some widgets use the same key for the media control that allows
         * the image selection and for the image size control that allows the user
         * to select the image size, in this case the third parameter should be null
         * or the same as the second parameter. But when the widget uses different
         * keys for the media control and the image size control, when calling this
         * method you should pass the keys.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param array  $settings       Control settings.
         * @param string $image_size_key Optional. Settings key for image size.
         *                               Default is `image`.
         * @param string $image_key      Optional. Settings key for image. Default
         *                               is null. If not defined uses image size key
         *                               as the image key.
         *
         * @return string Image HTML.
         */
        public static function get_attachment_image_html($settings, $image_size_key = 'image', $image_key = null)
        {
        }
        /**
         * Safe print attachment image HTML.
         *
         * @uses get_attachment_image_html.
         *
         * @access public
         * @static
         *
         * @param array  $settings       Control settings.
         * @param string $image_size_key Optional. Settings key for image size.
         *                               Default is `image`.
         * @param string $image_key      Optional. Settings key for image. Default
         *                               is null. If not defined uses image size key
         *                               as the image key.
         */
        public static function print_attachment_image_html(array $settings, $image_size_key = 'image', $image_key = null)
        {
        }
        /**
         * Get all image sizes.
         *
         * Retrieve available image sizes with data like `width`, `height` and `crop`.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @return array An array of available image sizes.
         */
        public static function get_all_image_sizes()
        {
        }
        /**
         * Get attachment image src.
         *
         * Retrieve the attachment image source URL.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param string $attachment_id  The attachment ID.
         * @param string $image_size_key Settings key for image size.
         * @param array  $settings       Control settings.
         *
         * @return string Attachment image source URL.
         */
        public static function get_attachment_image_src($attachment_id, $image_size_key, array $settings)
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
         * Init fields.
         *
         * Initialize image size control fields.
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
         * Process image size control fields before adding them to `add_control()`.
         *
         * @since 1.2.2
         * @access protected
         *
         * @param array $fields Image size control fields.
         *
         * @return array Processed fields.
         */
        protected function prepare_fields($fields)
        {
        }
        /**
         * Get default options.
         *
         * Retrieve the default options of the image size control. Used to return the
         * default options while initializing the image size control.
         *
         * @since 1.9.0
         * @access protected
         *
         * @return array Default image size control options.
         */
        protected function get_default_options()
        {
        }
    }
    class Group_Control_Flex_Container extends \Elementor\Group_Control_Base
    {
        protected static $fields;
        public static function get_type()
        {
        }
        protected function init_fields()
        {
        }
        protected function get_default_options()
        {
        }
    }
    /**
     * Elementor box shadow control.
     *
     * A base control for creating box shadow control. Displays input fields to define
     * the box shadow including the horizontal shadow, vertical shadow, shadow blur,
     * shadow spread, shadow color and the position.
     *
     * @since 1.2.2
     */
    class Group_Control_Box_Shadow extends \Elementor\Group_Control_Base
    {
        /**
         * Fields.
         *
         * Holds all the box shadow control fields.
         *
         * @since 1.2.2
         * @access protected
         * @static
         *
         * @var array Box shadow control fields.
         */
        protected static $fields;
        /**
         * Get box shadow control type.
         *
         * Retrieve the control type, in this case `box-shadow`.
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
         * Initialize box shadow control fields.
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
         * Get default options.
         *
         * Retrieve the default options of the box shadow control. Used to return the
         * default options while initializing the box shadow control.
         *
         * @since 1.9.0
         * @access protected
         *
         * @return array Default box shadow control options.
         */
        protected function get_default_options()
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
        public static function get_font_variable_ranges()
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
    /**
     * Elementor background control.
     *
     * A base control for creating background control. Displays input fields to define
     * the background color, background image, background gradient or background video.
     *
     * @since 1.2.2
     */
    class Group_Control_Background extends \Elementor\Group_Control_Base
    {
        /**
         * Fields.
         *
         * Holds all the background control fields.
         *
         * @since 1.2.2
         * @access protected
         * @static
         *
         * @var array Background control fields.
         */
        protected static $fields;
        /**
         * Get background control type.
         *
         * Retrieve the control type, in this case `background`.
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
         * Get background control types.
         *
         * Retrieve available background types.
         *
         * @since 1.2.2
         * @access public
         * @static
         *
         * @return array Available background types.
         */
        public static function get_background_types()
        {
        }
        /**
         * Init fields.
         *
         * Initialize background control fields.
         *
         * @since 1.2.2
         * @access public
         *
         * @return array Control fields.
         */
        public function init_fields()
        {
        }
        /**
         * Get child default args.
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
         * Filter which controls to display, using `include`, `exclude`, `condition`
         * and `of_type` arguments.
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
         * Prepare fields.
         *
         * Process background control fields before adding them to `add_control()`.
         *
         * @since 1.2.2
         * @access protected
         *
         * @param array $fields Background control fields.
         *
         * @return array Processed fields.
         */
        protected function prepare_fields($fields)
        {
        }
        /**
         * Get default options.
         *
         * Retrieve the default options of the background control. Used to return the
         * default options while initializing the background control.
         *
         * @since 1.9.0
         * @access protected
         *
         * @return array Default background control options.
         */
        protected function get_default_options()
        {
        }
    }
    /**
     * Elementor CSS Filter control.
     *
     * A base control for applying css filters. Displays sliders to define the
     * values of different CSS filters including blur, brightens, contrast,
     * saturation and hue.
     *
     * @since 2.1.0
     */
    class Group_Control_Css_Filter extends \Elementor\Group_Control_Base
    {
        /**
         * Prepare fields.
         *
         * Process css_filter control fields before adding them to `add_control()`.
         *
         * @since 2.1.0
         * @access protected
         *
         * @param array $fields CSS filter control fields.
         *
         * @return array Processed fields.
         */
        protected static $fields;
        /**
         * Get CSS filter control type.
         *
         * Retrieve the control type, in this case `css-filter`.
         *
         * @since 2.1.0
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
         * Initialize CSS filter control fields.
         *
         * @since 2.1.0
         * @access protected
         *
         * @return array Control fields.
         */
        protected function init_fields()
        {
        }
        /**
         * Get default options.
         *
         * Retrieve the default options of the CSS filter control. Used to return the
         * default options while initializing the CSS filter control.
         *
         * @since 2.1.0
         * @access protected
         *
         * @return array Default CSS filter control options.
         */
        protected function get_default_options()
        {
        }
    }
    /**
     * Elementor text stroke control.
     *
     * A group control for creating a stroke effect on text. Displays input fields to define
     * the text stroke and color stroke.
     *
     * @since 3.5.0
     */
    class Group_Control_Text_Stroke extends \Elementor\Group_Control_Base
    {
        /**
         * Fields.
         *
         * Holds all the text stroke control fields.
         *
         * @since 3.5.0
         * @access protected
         * @static
         *
         * @var array Text Stroke control fields.
         */
        protected static $fields;
        /**
         * Get text stroke control type.
         *
         * Retrieve the control type, in this case `text-stroke`.
         *
         * @since 3.5.0
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
         * Initialize text stroke control fields.
         *
         * @since 3.5.0
         * @access protected
         *
         * @return array Control fields.
         */
        protected function init_fields()
        {
        }
        /**
         * Get default options.
         *
         * Retrieve the default options of the text stroke control. Used to return the
         * default options while initializing the text stroke control.
         *
         * @since 3.5.0
         * @access protected
         *
         * @return array Default text stroke control options.
         */
        protected function get_default_options()
        {
        }
    }
    class Group_Control_Grid_Container extends \Elementor\Group_Control_Base
    {
        protected static $fields;
        public static function get_type()
        {
        }
        protected function init_fields()
        {
        }
        protected function get_responsive_unit_defaults()
        {
        }
        protected function get_responsive_autoflow_defaults()
        {
        }
        protected function get_default_options()
        {
        }
    }
    /**
     * Elementor section control.
     *
     * A base control for creating section control. Displays a header that
     * functions as a toggle to show or hide a set of controls.
     *
     * Note: Do not use it directly, instead use `$widget->start_controls_section()`
     * and `$widget->end_controls_section()` to wrap a set of controls.
     *
     * @since 1.0.0
     */
    class Control_Section extends \Elementor\Base_UI_Control
    {
        /**
         * Get section control type.
         *
         * Retrieve the control type, in this case `section`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Render section control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor switcher control.
     *
     * A base control for creating switcher control. Displays an on/off switcher,
     * basically a fancy UI representation of a checkbox.
     *
     * @since 1.0.0
     */
    class Control_Switcher extends \Elementor\Base_Data_Control
    {
        /**
         * Get switcher control type.
         *
         * Retrieve the control type, in this case `switcher`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Render switcher control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
        /**
         * Get switcher control default settings.
         *
         * Retrieve the default settings of the switcher control. Used to return the
         * default settings while initializing the switcher control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
    }
    /**
     * Elementor URL control.
     *
     * A base control for creating url control. Displays a URL input with the
     * ability to set the target of the link to `_blank` to open in a new tab.
     *
     * @since 1.0.0
     */
    class Control_URL extends \Elementor\Control_Base_Multiple
    {
        /**
         * Get url control type.
         *
         * Retrieve the control type, in this case `url`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get url control default values.
         *
         * Retrieve the default value of the url control. Used to return the default
         * values while initializing the url control.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array Control default value.
         */
        public function get_default_value()
        {
        }
        /**
         * Get url control default settings.
         *
         * Retrieve the default settings of the url control. Used to return the default
         * settings while initializing the url control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Render url control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor hover animation control.
     *
     * A base control for creating hover animation control. Displays a select box
     * with the available hover animation effects @see Control_Hover_Animation::get_animations()
     *
     * @since 1.0.0
     */
    class Control_Hover_Animation extends \Elementor\Base_Data_Control
    {
        /**
         * Animations.
         *
         * Holds all the available hover animation effects of the control.
         *
         * @access private
         * @static
         *
         * @var array
         */
        protected static $_animations;
        /**
         * Get hover animation control type.
         *
         * Retrieve the control type, in this case `hover_animation`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get animations.
         *
         * Retrieve the available hover animation effects.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @return array Available hover animation.
         */
        public static function get_animations()
        {
        }
        public static function get_default_animations() : array
        {
        }
        /**
         * Render hover animation control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
        /**
         * Get hover animation control default settings.
         *
         * Retrieve the default settings of the hover animation control. Used to return
         * the default settings while initializing the hover animation control.
         *
         * @since 1.0.0
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
    /**
     * Elementor font control.
     *
     * A base control for creating font control. Displays font select box. The
     * control allows you to set a list of fonts.
     *
     * @since 1.0.0
     */
    class Control_Font extends \Elementor\Base_Data_Control
    {
        /**
         * Get font control type.
         *
         * Retrieve the control type, in this case `font`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get font control default settings.
         *
         * Retrieve the default settings of the font control. Used to return the default
         * settings while initializing the font control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Render font control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor structure control.
     *
     * A base control for creating structure control. A private control for section
     * columns structure.
     *
     * @since 1.0.0
     */
    class Control_Structure extends \Elementor\Base_Data_Control
    {
        /**
         * Get structure control type.
         *
         * Retrieve the control type, in this case `structure`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Render structure control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
        /**
         * Get structure control default settings.
         *
         * Retrieve the default settings of the structure control. Used to return the
         * default settings while initializing the structure control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
    }
    /**
     * Elementor Icons control.
     *
     * A base control for creating a Icons chooser control.
     * Used to select an Icon.
     *
     * Usage: @see https://developers.elementor.com/elementor-controls/icons-control
     *
     * @since 2.6.0
     */
    class Control_Icons extends \Elementor\Control_Base_Multiple
    {
        /**
         * Get media control type.
         *
         * Retrieve the control type, in this case `media`.
         *
         * @access public
         * @since 2.6.0
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get Icons control default values.
         *
         * Retrieve the default value of the Icons control. Used to return the default
         * values while initializing the Icons control.
         *
         * @access public
         * @since 2.6.0
         * @return array Control default value.
         */
        public function get_default_value()
        {
        }
        /**
         * Render Icons control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 2.6.0
         * @access public
         */
        public function content_template()
        {
        }
        public function render_media_skin()
        {
        }
        public function render_inline_skin()
        {
        }
        /**
         * Get Icons control default settings.
         *
         * Retrieve the default settings of the Icons control. Used to return the default
         * settings while initializing the Icons control.
         *
         * @since 2.6.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Support SVG Import
         *
         * @deprecated 3.5.0
         *
         * @param $mimes
         * @return mixed
         */
        public function support_svg_import($mimes)
        {
        }
        public function on_import($settings)
        {
        }
    }
    /**
     * Elementor gap control.
     *
     * A base control for creating a gap control. Displays input fields for two values,
     * row/column, height/width and the option to link them together.
     *
     * @since 3.13.0
     */
    class Control_Gaps extends \Elementor\Control_Dimensions
    {
        /**
         * Get gap control type.
         *
         * Retrieve the control type, in this case `gap`.
         *
         * @since 3.13.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get gap control default values.
         *
         * Retrieve the default value of the gap control. Used to return the default
         * values while initializing the gap control.
         *
         * @since 3.13.0
         * @access public
         *
         * @return array Control default value.
         */
        public function get_default_value()
        {
        }
        public function get_singular_name()
        {
        }
        protected function get_dimensions()
        {
        }
        public function get_value($control, $settings)
        {
        }
    }
    /**
     * Elementor WordPress widget control.
     *
     * A base control for creating WordPress widget control. Displays native
     * WordPress widgets. This a private control for internal use.
     *
     * @since 1.0.0
     */
    class Control_WP_Widget extends \Elementor\Base_Data_Control
    {
        /**
         * Get WordPress widget control type.
         *
         * Retrieve the control type, in this case `wp_widget`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get WordPress widget control default values.
         *
         * Retrieve the default value of the WordPress widget control. Used to return the
         * default values while initializing the WordPress widget control.
         *
         * @since 1.4.3
         * @access public
         *
         * @return array Control default value.
         */
        public function get_default_value()
        {
        }
        /**
         * Render WordPress widget control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor color control.
     *
     * A base control for creating color control. Displays a color picker field with
     * an alpha slider. Includes a customizable color palette that can be preset by
     * the user. Accepts a `scheme` argument that allows you to set a value from the
     * active color scheme as the default value returned by the control.
     *
     * @since 1.0.0
     */
    class Control_Color extends \Elementor\Base_Data_Control
    {
        /**
         * Get color control type.
         *
         * Retrieve the control type, in this case `color`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Render color control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
        /**
         * Get color control default settings.
         *
         * Retrieve the default settings of the color control. Used to return the default
         * settings while initializing the color control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
    }
    /**
     * Elementor textarea control.
     *
     * A base control for creating textarea control. Displays a classic textarea.
     *
     * @since 1.0.0
     */
    class Control_Textarea extends \Elementor\Base_Data_Control
    {
        /**
         * Get textarea control type.
         *
         * Retrieve the control type, in this case `textarea`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get textarea control default settings.
         *
         * Retrieve the default settings of the textarea control. Used to return the
         * default settings while initializing the textarea control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Render textarea control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor raw HTML control.
     *
     * A base control for creating raw HTML control. Displays HTML markup between
     * controls in the panel.
     *
     * @since 1.0.0
     */
    class Control_Raw_Html extends \Elementor\Base_UI_Control
    {
        /**
         * Get raw html control type.
         *
         * Retrieve the control type, in this case `raw_html`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Render raw html control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
        /**
         * Get raw html control default settings.
         *
         * Retrieve the default settings of the raw html control. Used to return the
         * default settings while initializing the raw html control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
    }
    /**
     * Elementor select control.
     *
     * A base control for creating select control. Displays a simple select box.
     * It accepts an array in which the `key` is the option value and the `value` is
     * the option name.
     *
     * @since 1.0.0
     */
    class Control_Select extends \Elementor\Base_Data_Control
    {
        /**
         * Get select control type.
         *
         * Retrieve the control type, in this case `select`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get select control default settings.
         *
         * Retrieve the default settings of the select control. Used to return the
         * default settings while initializing the select control.
         *
         * @since 2.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Render select control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor Notice control.
     *
     * A base control specific for creating Notices in the Editor panels.
     *
     * @since 3.19.0
     */
    class Control_Notice extends \Elementor\Base_UI_Control
    {
        /**
         * Get notice control type.
         *
         * Retrieve the control type, in this case `notice`.
         *
         * @since 3.19.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Render notice control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 3.19.0
         * @access public
         */
        public function content_template()
        {
        }
        /**
         * Get notice control default settings.
         *
         * Retrieve the default settings of the notice control. Used to return the
         * default settings while initializing the notice control.
         *
         * @since 3.19.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
    }
    /**
     * Elementor media control.
     *
     * A base control for creating a media chooser control. Based on the WordPress
     * media library. Used to select an image from the WordPress media library.
     *
     * @since 1.0.0
     */
    class Control_Media extends \Elementor\Control_Base_Multiple
    {
        /**
         * Get media control type.
         *
         * Retrieve the control type, in this case `media`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get media control default values.
         *
         * Retrieve the default value of the media control. Used to return the default
         * values while initializing the media control.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array Control default value.
         */
        public function get_default_value()
        {
        }
        /**
         * Import media images.
         *
         * Used to import media control files from external sites while importing
         * Elementor template JSON file, and replacing the old data.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $settings Control settings
         *
         * @return array Control settings.
         */
        public function on_import($settings)
        {
        }
        /**
         * Support SVG and JSON Import
         *
         * Called by the 'upload_mimes' filter. Adds SVG and JSON mime types to the list of WordPress' allowed mime types.
         *
         * @since 3.4.6
         * @deprecated 3.5.0
         *
         * @param $mimes
         * @return mixed
         */
        public function support_svg_and_json_import($mimes)
        {
        }
        /**
         * Enqueue media control scripts and styles.
         *
         * Used to register and enqueue custom scripts and styles used by the media
         * control.
         *
         * @since 1.0.0
         * @access public
         */
        public function enqueue()
        {
        }
        /**
         * Render media control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
        /**
         * Get media control default settings.
         *
         * Retrieve the default settings of the media control. Used to return the default
         * settings while initializing the media control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Get media control image title.
         *
         * Retrieve the `title` of the image selected by the media control.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param array $attachment Media attachment.
         *
         * @return string Image title.
         */
        public static function get_image_title($attachment)
        {
        }
        /**
         * Get media control image alt.
         *
         * Retrieve the `alt` value of the image selected by the media control.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param array $instance Media attachment.
         *
         * @return string Image alt.
         */
        public static function get_image_alt($instance)
        {
        }
        public function get_style_value($css_property, $control_value, array $control_data)
        {
        }
        public static function sanitise_text($string)
        {
        }
    }
    /**
     * Elementor select2 control.
     *
     * A base control for creating select2 control. Displays a select box control
     * based on select2 jQuery plugin @see https://select2.github.io/ .
     * It accepts an array in which the `key` is the value and the `value` is the
     * option name. Set `multiple` to `true` to allow multiple value selection.
     *
     * @since 1.0.0
     */
    class Control_Select2 extends \Elementor\Base_Data_Control
    {
        /**
         * Get select2 control type.
         *
         * Retrieve the control type, in this case `select2`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get select2 control default settings.
         *
         * Retrieve the default settings of the select2 control. Used to return the
         * default settings while initializing the select2 control.
         *
         * @since 1.8.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Render select2 control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
    }
    /**
     * Elementor text control.
     *
     * A base control for creating text control. Displays a simple text input.
     *
     * @since 1.0.0
     */
    class Control_Text extends \Elementor\Base_Data_Control
    {
        /**
         * Get text control type.
         *
         * Retrieve the control type, in this case `text`.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Render text control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.0.0
         * @access public
         */
        public function content_template()
        {
        }
        /**
         * Get text control default settings.
         *
         * Retrieve the default settings of the text control. Used to return the
         * default settings while initializing the text control.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
    }
    /**
     * Elementor button control.
     *
     * A base control for creating a button control. Displays a button that can
     * trigger an event.
     *
     * @since 1.9.0
     */
    class Control_Button extends \Elementor\Base_UI_Control
    {
        /**
         * Get button control type.
         *
         * Retrieve the control type, in this case `button`.
         *
         * @since 1.9.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
        /**
         * Get button control default settings.
         *
         * Retrieve the default settings of the button control. Used to
         * return the default settings while initializing the button
         * control.
         *
         * @since 1.9.0
         * @access protected
         *
         * @return array Control default settings.
         */
        protected function get_default_settings()
        {
        }
        /**
         * Render button control output in the editor.
         *
         * Used to generate the control HTML in the editor using Underscore JS
         * template. The variables for the class are available using `data` JS
         * object.
         *
         * @since 1.9.0
         * @access public
         */
        public function content_template()
        {
        }
    }
}
namespace Elementor\Container {
    /**
     * Elementor Container.
     *
     * Elementor container handler class is responsible for the containerization
     * of manager classes and their dependencies.
     *
     * @since 3.24.0
     */
    class Container
    {
        protected static $instance;
        public static function initialize_instance()
        {
        }
        public static function get_instance()
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
         * Whether the element returns dynamic content.
         *
         * set to determine whether to cache the element output or not.
         *
         * @since 3.22.0
         * @access protected
         *
         * @return bool Whether to cache the element output.
         */
        protected function is_dynamic_content() : bool
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
         * Get panel presets.
         *
         * Used for displaying the widget in the panel multiple times, but with different defaults values,
         * icon, title etc.
         *
         * @since 3.16.0
         * @access public
         *
         * @return array
         */
        public function get_panel_presets()
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
        protected function should_render_shortcode()
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
         * @deprecated 3.1.0 Use `add_render_attribute()` method instead.
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
        protected function register_transform_section($element_selector = '', $transform_selector_class = ' > .elementor-widget-container')
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
         * @deprecated 3.1.0 Use `print_content()` method instead.
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
        public function has_widget_inner_wrapper() : bool
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
}
namespace Elementor\Includes\Elements {
    class Container extends \Elementor\Element_Base
    {
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Container constructor.
         *
         * @param array      $data
         * @param array|null $args
         *
         * @return void
         */
        public function __construct(array $data = [], array $args = null)
        {
        }
        /**
         * Get the element type.
         *
         * @return string
         */
        public static function get_type()
        {
        }
        /**
         * Get the element name.
         *
         * @return string
         */
        public function get_name()
        {
        }
        /**
         * Get the element display name.
         *
         * @return string
         */
        public function get_title()
        {
        }
        /**
         * Get the element display icon.
         *
         * @return string
         */
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function get_panel_presets()
        {
        }
        /**
         * Override the render attributes to add a custom wrapper class.
         *
         * @return void
         */
        protected function add_render_attributes()
        {
        }
        /**
         * Override the initial element config to display the Container in the panel.
         *
         * @return array
         */
        protected function get_initial_config()
        {
        }
        /**
         * Render the element JS template.
         *
         * @return void
         */
        protected function content_template()
        {
        }
        /**
         * Render the video background markup.
         *
         * @return void
         */
        protected function render_video_background()
        {
        }
        /**
         * Render the Container's shape divider.
         * TODO: Copied from `section.php`.
         *
         * Used to generate the shape dividers HTML.
         *
         * @param string $side - Shape divider side, used to set the shape key.
         *
         * @return void
         */
        protected function render_shape_divider($side)
        {
        }
        /**
         * Print safe HTML tag for the element based on the element settings.
         *
         * @return void
         */
        protected function print_html_tag()
        {
        }
        /**
         * Before rendering the container content. (Print the opening tag, etc.)
         *
         * @return void
         */
        public function before_render()
        {
        }
        /**
         * After rendering the Container content. (Print the closing tag, etc.)
         *
         * @return void
         */
        public function after_render()
        {
        }
        protected function is_boxed_container(array $settings)
        {
        }
        /**
         * Override the default child type to allow widgets & containers as children.
         *
         * @param array $element_data
         *
         * @return \Elementor\Element_Base|\Elementor\Widget_Base|null
         */
        protected function _get_default_child_type(array $element_data)
        {
        }
        /**
         * Register the Container's layout controls.
         *
         * @return void
         */
        protected function register_container_layout_controls()
        {
        }
        /**
         * Register the Container's items layout controls.
         *
         * @return void
         */
        protected function register_items_layout_controls()
        {
        }
        /**
         * Register the Container's layout tab.
         *
         * @return void
         */
        protected function register_layout_tab()
        {
        }
        /**
         * Register the Container's background controls.
         *
         * @return void
         */
        protected function register_background_controls()
        {
        }
        /**
         * Register the Container's background overlay controls.
         *
         * @return void
         */
        protected function register_background_overlay_controls()
        {
        }
        /**
         * Register the Container's border controls.
         *
         * @return void
         */
        protected function register_border_controls()
        {
        }
        /**
         * Register the Container's shape dividers controls.
         * TODO: Copied from `section.php`.
         *
         * @return void
         */
        protected function register_shape_dividers_controls()
        {
        }
        /**
         * Register the Container's style tab.
         *
         * @return void
         */
        protected function register_style_tab()
        {
        }
        /**
         * Register the Container's advanced style controls.
         *
         * @return void
         */
        protected function register_advanced_controls()
        {
        }
        /**
         * Register the Container's motion effects controls.
         *
         * @return void
         */
        protected function register_motion_effects_controls()
        {
        }
        /**
         * Register the Container's responsive controls.
         *
         * @return void
         */
        protected function register_responsive_controls()
        {
        }
        /**
         * Register the Container's advanced tab.
         *
         * @return void
         */
        protected function register_advanced_tab()
        {
        }
        protected function hook_sticky_notice_into_transform_section()
        {
        }
        /**
         * Register the Container's controls.
         *
         * @return void
         */
        protected function register_controls()
        {
        }
        public function on_import($element)
        {
        }
        /**
         * convert slider to gaps control for the 3.16 upgrade script
         *
         * @param $element
         * @return array
         */
        public static function slider_to_gaps_converter($element)
        {
        }
    }
}
namespace Elementor {
    /**
     * Elementor repeater element.
     *
     * Elementor repeater handler class is responsible for initializing the repeater.
     *
     * @since 1.0.0
     */
    class Repeater extends \Elementor\Element_Base
    {
        /**
         * Repeater constructor.
         *
         * Initializing Elementor repeater element.
         *
         * @since 1.0.7
         * @access public
         *
         * @param array      $data Optional. Element data. Default is an empty array.
         * @param array|null $args Optional. Element default arguments. Default is null.
         *
         */
        public function __construct(array $data = [], array $args = null)
        {
        }
        /**
         * Get repeater name.
         *
         * Retrieve the repeater name.
         *
         * @since 1.0.7
         * @access public
         *
         * @return string Repeater name.
         */
        public function get_name()
        {
        }
        /**
         * Get repeater type.
         *
         * Retrieve the repeater type.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @return string Repeater type.
         */
        public static function get_type()
        {
        }
        /**
         * Add new repeater control to stack.
         *
         * Register a repeater control to allow the user to set/update data.
         *
         * This method should be used inside `register_controls()`.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $id      Repeater control ID.
         * @param array  $args    Repeater control arguments.
         * @param array  $options Optional. Repeater control options. Default is an
         *                        empty array.
         *
         * @return bool True if repeater control added, False otherwise.
         */
        public function add_control($id, array $args, $options = [])
        {
        }
        /**
         * Get repeater fields.
         *
         * Retrieve the fields from the current repeater control.
         *
         * @since 1.5.0
         * @deprecated 2.1.0 Use `get_controls()` method instead.
         * @access public
         *
         * @return array Repeater fields.
         */
        public function get_fields()
        {
        }
        /**
         * Get default child type.
         *
         * Retrieve the repeater child type based on element data.
         *
         * Note that repeater does not support children, therefore it returns false.
         *
         * @since 1.0.0
         * @access protected
         *
         * @param array $element_data Element ID.
         *
         * @return false Repeater default child type or False if type not found.
         */
        protected function _get_default_child_type(array $element_data)
        {
        }
        protected function handle_control_position(array $args, $control_id, $overwrite)
        {
        }
    }
    /**
     * Elementor section element.
     *
     * Elementor section handler class is responsible for initializing the section
     * element.
     *
     * @since 1.0.0
     */
    class Element_Section extends \Elementor\Element_Base
    {
        /**
         * Get element type.
         *
         * Retrieve the element type, in this case `section`.
         *
         * @since 2.1.0
         * @access public
         * @static
         *
         * @return string The type.
         */
        public static function get_type()
        {
        }
        /**
         * Get section name.
         *
         * Retrieve the section name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Section name.
         */
        public function get_name()
        {
        }
        /**
         * Get section title.
         *
         * Retrieve the section title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Section title.
         */
        public function get_title()
        {
        }
        /**
         * Get section icon.
         *
         * Retrieve the section icon.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Section icon.
         */
        public function get_icon()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get presets.
         *
         * Retrieve a specific preset columns for a given columns count, or a list
         * of all the preset if no parameters passed.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param int $columns_count Optional. Columns count. Default is null.
         * @param int $preset_index  Optional. Preset index. Default is null.
         *
         * @return array Section presets.
         */
        public static function get_presets($columns_count = null, $preset_index = null)
        {
        }
        /**
         * Initialize presets.
         *
         * Initializing the section presets and set the number of columns the
         * section can have by default. For example a column can have two columns
         * 50% width each one, or three columns 33.33% each one.
         *
         * Note that Elementor sections have default section presets but the user
         * can set custom number of columns and define custom sizes for each column.
         * @since 1.0.0
         * @access public
         * @static
         */
        public static function init_presets()
        {
        }
        /**
         * Get initial config.
         *
         * Retrieve the current section initial configuration.
         *
         * Adds more configuration on top of the controls list, the tabs assigned to
         * the control, element name, type, icon and more. This method also adds
         * section presets.
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
         * Register section controls.
         *
         * Used to add new controls to the section element.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render section output in the editor.
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
         * Before section rendering.
         *
         * Used to add stuff before the section element.
         *
         * @since 1.0.0
         * @access public
         */
        public function before_render()
        {
        }
        /**
         * After section rendering.
         *
         * Used to add stuff after the section element.
         *
         * @since 1.0.0
         * @access public
         */
        public function after_render()
        {
        }
        /**
         * Add section render attributes.
         *
         * Used to add attributes to the current section wrapper HTML tag.
         *
         * @since 1.3.0
         * @access protected
         */
        protected function add_render_attributes()
        {
        }
        /**
         * Get default child type.
         *
         * Retrieve the section child type based on element data.
         *
         * @since 1.0.0
         * @access protected
         *
         * @param array $element_data Element ID.
         *
         * @return Element_Base Section default child type.
         */
        protected function _get_default_child_type(array $element_data)
        {
        }
        /**
         * Get HTML tag.
         *
         * Retrieve the section element HTML tag.
         *
         * @since 1.5.3
         * @access private
         *
         * @return string Section HTML tag.
         */
        protected function get_html_tag()
        {
        }
        /**
         * Print section shape divider.
         *
         * Used to generate the shape dividers HTML.
         *
         * @since 1.3.0
         * @access private
         *
         * @param string $side Shape divider side, used to set the shape key.
         */
        protected function print_shape_divider($side)
        {
        }
    }
    /**
     * Elementor column element.
     *
     * Elementor column handler class is responsible for initializing the column
     * element.
     *
     * @since 1.0.0
     */
    class Element_Column extends \Elementor\Element_Base
    {
        /**
         * Get column name.
         *
         * Retrieve the column name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Column name.
         */
        public function get_name()
        {
        }
        /**
         * Get element type.
         *
         * Retrieve the element type, in this case `column`.
         *
         * @since 2.1.0
         * @access public
         * @static
         *
         * @return string The type.
         */
        public static function get_type()
        {
        }
        /**
         * Get column title.
         *
         * Retrieve the column title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Column title.
         */
        public function get_title()
        {
        }
        /**
         * Get column icon.
         *
         * Retrieve the column icon.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Column icon.
         */
        public function get_icon()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get initial config.
         *
         * Retrieve the current section initial configuration.
         *
         * Adds more configuration on top of the controls list, the tabs assigned to
         * the control, element name, type, icon and more. This method also adds
         * section presets.
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
         * Register column controls.
         *
         * Used to add new controls to the column element.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render column output in the editor.
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
         * Before column rendering.
         *
         * Used to add stuff before the column element.
         *
         * @since 1.0.0
         * @access public
         */
        public function before_render()
        {
        }
        /**
         * After column rendering.
         *
         * Used to add stuff after the column element.
         *
         * @since 1.0.0
         * @access public
         */
        public function after_render()
        {
        }
        /**
         * Add column render attributes.
         *
         * Used to add attributes to the current column wrapper HTML tag.
         *
         * @since 1.3.0
         * @access protected
         */
        protected function add_render_attributes()
        {
        }
        /**
         * Get default child type.
         *
         * Retrieve the column child type based on element data.
         *
         * @since 1.0.0
         * @access protected
         *
         * @param array $element_data Element ID.
         *
         * @return Element_Base|false Column default child type.
         */
        protected function _get_default_child_type(array $element_data)
        {
        }
    }
    /**
     * Elementor embed.
     *
     * Elementor embed handler class is responsible for Elementor embed functionality.
     * The class holds the supported providers with their embed patters, and handles
     * their custom properties to create custom HTML with the embedded content.
     *
     * @since 1.5.0
     */
    class Embed
    {
        /**
         * Get video properties.
         *
         * Retrieve the video properties for a given video URL.
         *
         * @since 1.5.0
         * @access public
         * @static
         *
         * @param string $video_url Video URL.
         *
         * @return null|array The video properties, or null.
         */
        public static function get_video_properties($video_url)
        {
        }
        /**
         * Get embed URL.
         *
         * Retrieve the embed URL for a given video.
         *
         * @since 1.5.0
         * @access public
         * @static
         *
         * @param string $video_url        Video URL.
         * @param array  $embed_url_params Optional. Embed parameters. Default is an
         *                                 empty array.
         * @param array  $options          Optional. Embed options. Default is an
         *                                 empty array.
         *
         * @return null|array The video properties, or null.
         */
        public static function get_embed_url($video_url, array $embed_url_params = [], array $options = [])
        {
        }
        /**
         * Get embed HTML.
         *
         * Retrieve the final HTML of the embedded URL.
         *
         * @since 1.5.0
         * @access public
         * @static
         *
         * @param string $video_url        Video URL.
         * @param array  $embed_url_params Optional. Embed parameters. Default is an
         *                                 empty array.
         * @param array  $options          Optional. Embed options. Default is an
         *                                 empty array.
         * @param array  $frame_attributes Optional. IFrame attributes. Default is an
         *                                 empty array.
         *
         * @return string The embed HTML.
         */
        public static function get_embed_html($video_url, array $embed_url_params = [], array $options = [], array $frame_attributes = [])
        {
        }
        /**
         * Get oembed data from the cache.
         * if not exists in the cache it will fetch from provider and then save to the cache.
         *
         * @param $oembed_url
         * @param $cached_post_id
         *
         * @return array|null
         */
        public static function get_oembed_data($oembed_url, $cached_post_id)
        {
        }
        /**
         * Fetch oembed data from oembed provider.
         *
         * @param $oembed_url
         *
         * @return array|null
         */
        public static function fetch_oembed_data($oembed_url)
        {
        }
        /**
         * @param $oembed_url
         * @param null|string|int $cached_post_id
         *
         * @return string|null
         */
        public static function get_embed_thumbnail_html($oembed_url, $cached_post_id = null)
        {
        }
    }
    /**
     * Elementor database.
     *
     * Elementor database handler class is responsible for communicating with the
     * DB, save and retrieve Elementor data and meta data.
     *
     * @since 1.0.0
     */
    class DB
    {
        /**
         * Current DB version of the editor.
         */
        const DB_VERSION = '0.4';
        /**
         * Post publish status.
         *
         * @deprecated 3.1.0 Use `Document::STATUS_PUBLISH` const instead.
         */
        const STATUS_PUBLISH = \Elementor\Core\Base\Document::STATUS_PUBLISH;
        /**
         * Post draft status.
         *
         * @deprecated 3.1.0 Use `Document::STATUS_DRAFT` const instead.
         */
        const STATUS_DRAFT = \Elementor\Core\Base\Document::STATUS_DRAFT;
        /**
         * Post private status.
         *
         * @deprecated 3.1.0 Use `Document::STATUS_PRIVATE` const instead.
         */
        const STATUS_PRIVATE = \Elementor\Core\Base\Document::STATUS_PRIVATE;
        /**
         * Post autosave status.
         *
         * @deprecated 3.1.0 Use `Document::STATUS_AUTOSAVE` const instead.
         */
        const STATUS_AUTOSAVE = \Elementor\Core\Base\Document::STATUS_AUTOSAVE;
        /**
         * Post pending status.
         *
         * @deprecated 3.1.0 Use `Document::STATUS_PENDING` const instead.
         */
        const STATUS_PENDING = \Elementor\Core\Base\Document::STATUS_PENDING;
        /**
         * Switched post data.
         *
         * Holds the switched post data.
         *
         * @since 1.5.0
         * @access protected
         *
         * @var array Switched post data. Default is an empty array.
         */
        protected $switched_post_data = [];
        /**
         * Switched data.
         *
         * Holds the switched data.
         *
         * @since 2.0.0
         * @access protected
         *
         * @var array Switched data. Default is an empty array.
         */
        protected $switched_data = [];
        /**
         * Get builder.
         *
         * Retrieve editor data from the database.
         *
         * @since 1.0.0
         * @deprecated 3.1.0 Use `Plugin::$instance->documents->get( $post_id )->get_elements_raw_data( null, true )` OR `Plugin::$instance->documents->get_doc_or_auto_save( $post_id )->get_elements_raw_data( null, true )` instead.
         * @access public
         *
         * @param int     $post_id           Post ID.
         * @param string  $status            Optional. Post status. Default is `publish`.
         *
         * @return array Editor data.
         */
        public function get_builder($post_id, $status = \Elementor\Core\Base\Document::STATUS_PUBLISH)
        {
        }
        /**
         * Get JSON meta.
         *
         * Retrieve post meta data, and return the JSON decoded data.
         *
         * @since 1.0.0
         * @access protected
         *
         * @param int    $post_id Post ID.
         * @param string $key     The meta key to retrieve.
         *
         * @return array Decoded JSON data from post meta.
         */
        protected function _get_json_meta($post_id, $key)
        {
        }
        /**
         * Is using Elementor.
         *
         * Set whether the page is using Elementor or not.
         *
         * @since 1.5.0
         * @deprecated 3.1.0 Use `Plugin::$instance->documents->get( $post_id )->set_is_build_with_elementor( $is_elementor )` instead.
         * @access public
         *
         * @param int  $post_id      Post ID.
         * @param bool $is_elementor Optional. Whether the page is elementor page.
         *                           Default is true.
         */
        public function set_is_elementor_page($post_id, $is_elementor = true)
        {
        }
        /**
         * Save plain text.
         *
         * Retrieves the raw content, removes all kind of unwanted HTML tags and saves
         * the content as the `post_content` field in the database.
         *
         * @since 1.9.0
         * @access public
         *
         * @param int $post_id Post ID.
         */
        public function save_plain_text($post_id)
        {
        }
        /**
         * Iterate data.
         *
         * Accept any type of Elementor data and a callback function. The callback
         * function runs recursively for each element and his child elements.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array    $data_container Any type of elementor data.
         * @param callable $callback       A function to iterate data by.
         * @param array    $args           Array of args pointers for passing parameters in & out of the callback
         *
         * @return mixed Iterated data.
         */
        public function iterate_data($data_container, $callback, $args = [])
        {
        }
        /**
         * Safely copy Elementor meta.
         *
         * Make sure the original page was built with Elementor and the post is not
         * auto-save. Only then copy elementor meta from one post to another using
         * `copy_elementor_meta()`.
         *
         * @since 1.9.2
         * @access public
         *
         * @param int $from_post_id Original post ID.
         * @param int $to_post_id   Target post ID.
         */
        public function safe_copy_elementor_meta($from_post_id, $to_post_id)
        {
        }
        /**
         * Copy Elementor meta.
         *
         * Duplicate the data from one post to another.
         *
         * Consider using `safe_copy_elementor_meta()` method instead.
         *
         * @since 1.1.0
         * @access public
         *
         * @param int $from_post_id Original post ID.
         * @param int $to_post_id   Target post ID.
         */
        public function copy_elementor_meta($from_post_id, $to_post_id)
        {
        }
        /**
         * Is built with Elementor.
         *
         * Check whether the post was built with Elementor.
         *
         * @since 1.0.10
         * @deprecated 3.2.0 Use `Plugin::$instance->documents->get( $post_id )->is_built_with_elementor()` instead.
         * @access public
         *
         * @param int $post_id Post ID.
         *
         * @return bool Whether the post was built with Elementor.
         */
        public function is_built_with_elementor($post_id)
        {
        }
        /**
         * Switch to post.
         *
         * Change the global WordPress post to the requested post.
         *
         * @since 1.5.0
         * @access public
         *
         * @param int $post_id Post ID to switch to.
         */
        public function switch_to_post($post_id)
        {
        }
        /**
         * Restore current post.
         *
         * Rollback to the previous global post, rolling back from `DB::switch_to_post()`.
         *
         * @since 1.5.0
         * @access public
         */
        public function restore_current_post()
        {
        }
        /**
         * Switch to query.
         *
         * Change the WordPress query to a new query with the requested
         * query variables.
         *
         * @since 2.0.0
         * @access public
         *
         * @param array $query_vars New query variables.
         * @param bool  $force_global_post
         */
        public function switch_to_query($query_vars, $force_global_post = false)
        {
        }
        /**
         * Restore current query.
         *
         * Rollback to the previous query, rolling back from `DB::switch_to_query()`.
         *
         * @since 2.0.0
         * @access public
         */
        public function restore_current_query()
        {
        }
        /**
         * Get plain text.
         *
         * Retrieve the post plain text.
         *
         * @since 1.9.0
         * @access public
         *
         * @param int $post_id Post ID.
         *
         * @return string Post plain text.
         */
        public function get_plain_text($post_id)
        {
        }
        /**
         * Get plain text from data.
         *
         * Retrieve the post plain text from any given Elementor data.
         *
         * @since 1.9.2
         * @access public
         *
         * @param array $data Post ID.
         *
         * @return string Post plain text.
         */
        public function get_plain_text_from_data($data)
        {
        }
    }
    /**
     * Elementor fonts.
     *
     * Elementor fonts handler class is responsible for registering the supported
     * fonts used by Elementor.
     *
     * @since 1.0.0
     */
    class Fonts
    {
        /**
         * The system font name.
         */
        const SYSTEM = 'system';
        /**
         * The google font name.
         */
        const GOOGLE = 'googlefonts';
        /**
         * The google early access font name.
         */
        const EARLYACCESS = 'earlyaccess';
        /**
         * The local font name.
         */
        const LOCAL = 'local';
        /**
         * Get font Groups.
         *
         * Retrieve the list of font groups.
         *
         * @since 1.9.4
         * @access public
         * @static
         *
         * @return array Supported font groups/types.
         */
        public static function get_font_groups()
        {
        }
        /**
         * Get fonts.
         *
         * Retrieve the list of supported fonts.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @return array Supported fonts.
         */
        public static function get_fonts()
        {
        }
        /**
         * Get font type.
         *
         * Retrieve the font type for a given font.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param string $name Font name.
         *
         * @return string|false Font type, or false if font doesn't exist.
         */
        public static function get_font_type($name)
        {
        }
        /**
         * Get fonts by group.
         *
         * Retrieve all the fonts belong to specific group.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param array $groups Optional. Font group. Default is an empty array.
         *
         * @return array Font type, or false if font doesn't exist.
         */
        public static function get_fonts_by_groups($groups = [])
        {
        }
        public static function is_google_fonts_enabled() : bool
        {
        }
        public static function get_font_display_setting()
        {
        }
        public static function reset_local_cache()
        {
        }
    }
    /**
     * Elementor heartbeat.
     *
     * Elementor heartbeat handler class is responsible for initializing Elementor
     * heartbeat. The class communicates with WordPress Heartbeat API while working
     * with Elementor.
     *
     * @since 1.0.0
     */
    class Heartbeat
    {
        /**
         * Heartbeat received.
         *
         * Locks the Heartbeat response received when editing with Elementor.
         *
         * Fired by `heartbeat_received` filter.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $response The Heartbeat response.
         * @param array $data     The `$_POST` data sent.
         *
         * @return array Heartbeat response received.
         */
        public function heartbeat_received($response, $data)
        {
        }
        /**
         * Refresh nonces.
         *
         * Filter the nonces to send to the editor when editing with Elementor. Used
         * to refresh the nonce when the nonce expires while editing. This way the
         * user doesn't need to log-in again as Elementor fetches the new nonce from
         * the server using ajax.
         *
         * Fired by `wp_refresh_nonces` filter.
         *
         * @since 1.8.0
         * @access public
         *
         * @param array $response The no-priv Heartbeat response object or array.
         * @param array $data     The `$_POST` data sent.
         *
         * @return array Refreshed nonces.
         */
        public function refresh_nonces($response, $data)
        {
        }
        /**
         * Heartbeat constructor.
         *
         * Initializing Elementor heartbeat.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct()
        {
        }
    }
    /**
     * Elementor utils.
     *
     * Elementor utils handler class is responsible for different utility methods
     * used by Elementor.
     *
     * @since 1.0.0
     */
    class Utils
    {
        const DEPRECATION_RANGE = 0.4;
        const EDITOR_BREAK_LINES_OPTION_KEY = 'elementor_editor_break_lines';
        /**
         * A list of safe tags for `validate_html_tag` method.
         */
        const ALLOWED_HTML_WRAPPER_TAGS = ['a', 'article', 'aside', 'button', 'div', 'footer', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'main', 'nav', 'p', 'section', 'span'];
        const EXTENDED_ALLOWED_HTML_TAGS = ['iframe' => ['iframe' => ['allow' => true, 'allowfullscreen' => true, 'frameborder' => true, 'height' => true, 'loading' => true, 'name' => true, 'referrerpolicy' => true, 'sandbox' => true, 'src' => true, 'width' => true]], 'svg' => ['svg' => ['aria-hidden' => true, 'aria-labelledby' => true, 'class' => true, 'height' => true, 'role' => true, 'viewbox' => true, 'width' => true, 'xmlns' => true], 'g' => ['fill' => true], 'title' => ['title' => true], 'path' => ['d' => true, 'fill' => true]], 'image' => ['img' => ['srcset' => true, 'sizes' => true]]];
        /**
         * Variables for free to pro upsale modal promotions
         */
        const ANIMATED_HEADLINE = 'animated_headline';
        const CTA = 'cta';
        const VIDEO_PLAYLIST = 'video_playlist';
        const TESTIMONIAL_WIDGET = 'testimonial_widget';
        const IMAGE_CAROUSEL = 'image_carousel';
        /**
         * Is WP CLI.
         *
         * @return bool
         */
        public static function is_wp_cli()
        {
        }
        /**
         * Is script debug.
         *
         * Whether script debug is enabled or not.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @return bool True if it's a script debug is active, false otherwise.
         */
        public static function is_script_debug()
        {
        }
        public static function is_elementor_debug()
        {
        }
        /**
         * Whether elementor test mode is enabled or not.
         *
         * @return bool
         */
        public static function is_elementor_tests()
        {
        }
        /**
         * Get pro link.
         *
         * Retrieve the link to Elementor Pro.
         *
         * @since 1.7.0
         * @access public
         * @static
         *
         * @param string $link URL to Elementor pro.
         *
         * @return string Elementor pro link.
         */
        public static function get_pro_link($link)
        {
        }
        /**
         * Replace URLs.
         *
         * Replace old URLs to new URLs. This method also updates all the Elementor data.
         *
         * @since 2.1.0
         * @static
         * @access public
         *
         * @param $from
         * @param $to
         *
         * @return string
         * @throws \Exception
         */
        public static function replace_urls($from, $to)
        {
        }
        /**
         * Is post supports Elementor.
         *
         * Whether the post supports editing with Elementor.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param int $post_id Optional. Post ID. Default is `0`.
         *
         * @return string True if post supports editing with Elementor, false otherwise.
         */
        public static function is_post_support($post_id = 0)
        {
        }
        /**
         * Is post type supports Elementor.
         *
         * Whether the post type supports editing with Elementor.
         *
         * @since 2.2.0
         * @access public
         * @static
         *
         * @param string $post_type Post Type.
         *
         * @return string True if post type supports editing with Elementor, false otherwise.
         */
        public static function is_post_type_support($post_type)
        {
        }
        /**
         * Get placeholder image source.
         *
         * Retrieve the source of the placeholder image.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @return string The source of the default placeholder image used by Elementor.
         */
        public static function get_placeholder_image_src()
        {
        }
        /**
         * Generate random string.
         *
         * Returns a string containing a hexadecimal representation of random number.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @return string Random string.
         */
        public static function generate_random_string()
        {
        }
        /**
         * Do not cache.
         *
         * Tell WordPress cache plugins not to cache this request.
         *
         * @since 1.0.0
         * @access public
         * @static
         */
        public static function do_not_cache()
        {
        }
        /**
         * Get timezone string.
         *
         * Retrieve timezone string from the WordPress database.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @return string Timezone string.
         */
        public static function get_timezone_string()
        {
        }
        /**
         * Get create new post URL.
         *
         * Retrieve a custom URL for creating a new post/page using Elementor.
         *
         * @since 1.9.0
         * @access public
         * @deprecated 3.3.0 Use `Plugin::$instance->documents->get_create_new_post_url()` instead.
         * @static
         *
         * @param string $post_type Optional. Post type slug. Default is 'page'.
         * @param string|null $template_type Optional. Query arg 'template_type'. Default is null.
         *
         * @return string A URL for creating new post using Elementor.
         */
        public static function get_create_new_post_url($post_type = 'page', $template_type = null)
        {
        }
        /**
         * Get post autosave.
         *
         * Retrieve an autosave for any given post.
         *
         * @since 1.9.2
         * @access public
         * @static
         *
         * @param int $post_id Post ID.
         * @param int $user_id Optional. User ID. Default is `0`.
         *
         * @return \WP_Post|false Post autosave or false.
         */
        public static function get_post_autosave($post_id, $user_id = 0)
        {
        }
        /**
         * Is CPT supports custom templates.
         *
         * Whether the Custom Post Type supports templates.
         *
         * @since 2.0.0
         * @access public
         * @static
         *
         * @return bool True is templates are supported, False otherwise.
         */
        public static function is_cpt_custom_templates_supported()
        {
        }
        /**
         * @since 2.1.2
         * @access public
         * @static
         */
        public static function array_inject($array, $key, $insert)
        {
        }
        /**
         * Render html attributes
         *
         * @access public
         * @static
         * @param array $attributes
         *
         * @return string
         */
        public static function render_html_attributes(array $attributes)
        {
        }
        /**
         * Safe print html attributes
         *
         * @access public
         * @static
         * @param array $attributes
         */
        public static function print_html_attributes(array $attributes)
        {
        }
        public static function get_meta_viewport($context = '')
        {
        }
        /**
         * Add Elementor Config js vars to the relevant script handle,
         * WP will wrap it with <script> tag.
         * To make sure this script runs thru the `script_loader_tag` hook, use a known handle value.
         * @param string $handle
         * @param string $js_var
         * @param mixed $config
         */
        public static function print_js_config($handle, $js_var, $config)
        {
        }
        public static function handle_deprecation($item, $version, $replacement = null)
        {
        }
        /**
         * Checks a control value for being empty, including a string of '0' not covered by PHP's empty().
         *
         * @param mixed $source
         * @param bool|string $key
         *
         * @return bool
         */
        public static function is_empty($source, $key = false)
        {
        }
        public static function has_pro()
        {
        }
        /**
         * Convert HTMLEntities to UTF-8 characters
         *
         * @param $string
         * @return string
         */
        public static function urlencode_html_entities($string)
        {
        }
        /**
         * Parse attributes that come as a string of comma-delimited key|value pairs.
         * Removes Javascript events and unescaped `href` attributes.
         *
         * @param string $attributes_string
         *
         * @param string $delimiter Default comma `,`.
         *
         * @return array
         */
        public static function parse_custom_attributes($attributes_string, $delimiter = ',')
        {
        }
        public static function find_element_recursive($elements, $id)
        {
        }
        /**
         * Change Submenu First Item Label
         *
         * Overwrite the label of the first submenu item of an admin menu item.
         *
         * Fired by `admin_menu` action.
         *
         * @since 3.1.0
         *
         * @param $menu_slug
         * @param $new_label
         * @access public
         */
        public static function change_submenu_first_item_label($menu_slug, $new_label)
        {
        }
        /**
         * Validate an HTML tag against a safe allowed list.
         *
         * @param string $tag
         *
         * @return string
         */
        public static function validate_html_tag($tag)
        {
        }
        /**
         * Safe print a validated HTML tag.
         *
         * @param string $tag
         */
        public static function print_validated_html_tag($tag)
        {
        }
        /**
         * Print internal content (not user input) without escaping.
         */
        public static function print_unescaped_internal_string($string)
        {
        }
        /**
         * Get recently edited posts query.
         *
         * Returns `WP_Query` of the recent edited posts.
         * By default max posts ( $args['posts_per_page'] ) is 3.
         *
         * @param array $args
         *
         * @return \WP_Query
         */
        public static function get_recently_edited_posts_query($args = [])
        {
        }
        public static function print_wp_kses_extended($string, array $tags)
        {
        }
        public static function is_elementor_path($path)
        {
        }
        /**
         * @param $file
         * @param mixed ...$args
         * @return false|string
         */
        public static function file_get_contents($file, ...$args)
        {
        }
        public static function get_super_global_value($super_global, $key)
        {
        }
        /**
         * Return specific object property value if exist from array of keys.
         *
         * @param $array
         * @param $keys
         * @return key|false
         */
        public static function get_array_value_by_keys($array, $keys)
        {
        }
        public static function get_cached_callback($callback, $cache_key, $cache_time = 24 * HOUR_IN_SECONDS)
        {
        }
        public static function is_sale_time() : bool
        {
        }
        public static function safe_throw(string $message)
        {
        }
        public static function has_invalid_post_permissions($post) : bool
        {
        }
    }
    /**
     * Elementor tracker.
     *
     * Elementor tracker handler class is responsible for sending non-sensitive plugin
     * data to Elementor servers for users that actively allowed data tracking.
     *
     * @since 1.0.0
     */
    class Tracker
    {
        /**
         * Init.
         *
         * Initialize Elementor tracker.
         *
         * @since 1.0.0
         * @access public
         * @static
         */
        public static function init()
        {
        }
        /**
         * Check for settings opt-in.
         *
         * Checks whether the site admin has opted-in for data tracking, or not.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param string $new_value Allowed tracking value.
         *
         * @return string Return `yes` if tracking allowed, `no` otherwise.
         */
        public static function check_for_settings_optin($new_value)
        {
        }
        /**
         * Send tracking data.
         *
         * Decide whether to send tracking data, or not.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param bool $override
         */
        public static function send_tracking_data($override = false)
        {
        }
        /**
         * Is allow track.
         *
         * Checks whether the site admin has opted-in for data tracking, or not.
         *
         * @since 1.0.0
         * @access public
         * @static
         */
        public static function is_allow_track()
        {
        }
        /**
         * Handle tracker actions.
         *
         * Check if the user opted-in or opted-out and update the database.
         *
         * Fired by `admin_init` action.
         *
         * @since 1.0.0
         * @access public
         * @static
         */
        public static function handle_tracker_actions()
        {
        }
        /**
         * @since 2.2.0
         * @access public
         * @static
         */
        public static function is_notice_shown()
        {
        }
        public static function set_opt_in($value)
        {
        }
        /**
         * Get non elementor post usages.
         *
         * Retrieve the number of posts that not using elementor.
         * @return array The number of posts using not used by Elementor grouped by post types
         *               and post status.
         */
        public static function get_non_elementor_posts_usage()
        {
        }
        /**
         * Get posts usage.
         *
         * Retrieve the number of posts using Elementor.
         *
         * @since 2.0.0
         * @access public
         * @static
         *
         * @return array The number of posts using Elementor grouped by post types
         *               and post status.
         */
        public static function get_posts_usage()
        {
        }
        /**
         * Get library usage.
         *
         * Retrieve the number of Elementor library items saved.
         *
         * @since 2.0.0
         * @access public
         * @static
         *
         * @return array The number of Elementor library items grouped by post types
         *               and meta value.
         */
        public static function get_library_usage()
        {
        }
        /**
         * Get usage of general settings.
         * 'Elementor->Settings->General'.
         *
         * @return array
         */
        public static function get_settings_general_usage()
        {
        }
        /**
         * Get usage of advanced settings.
         * 'Elementor->Settings->Advanced'.
         *
         * @return array
         */
        public static function get_settings_advanced_usage()
        {
        }
        /**
         * Get usage of performance settings.
         * 'Elementor->Settings->Performance'.
         *
         * @return array
         */
        public static function get_settings_performance_usage()
        {
        }
        /**
         * Get usage of experiments settings.
         *
         * 'Elementor->Settings->Experiments'.
         *
         * @return array
         */
        public static function get_settings_experiments_usage()
        {
        }
        /**
         * Get usage of general tools.
         * 'Elementor->Tools->General'.
         *
         * @return array
         */
        public static function get_tools_general_usage()
        {
        }
        /**
         * Get usage of 'version control' tools.
         * 'Elementor->Tools->Version Control'.
         *
         * @return array
         */
        public static function get_tools_version_control_usage()
        {
        }
        /**
         * Get usage of 'maintenance' tools.
         * 'Elementor->Tools->Maintenance'.
         *
         * @return array
         */
        public static function get_tools_maintenance_usage()
        {
        }
        /**
         * Get library usage extend.
         *
         * Retrieve the number of Elementor library items saved.
         *
         * @return array The number of Elementor library items grouped by post types, post status
         *               and meta value.
         */
        public static function get_library_usage_extend()
        {
        }
        public static function get_events()
        {
        }
        /**
         * Get the tracking data
         *
         * Retrieve tracking data and apply filter
         *
         * @access public
         * @static
         *
         * @param bool $is_first_time
         *
         * @return array
         */
        public static function get_tracking_data($is_first_time = false)
        {
        }
    }
    /**
     * Elementor API.
     *
     * Elementor API handler class is responsible for communicating with Elementor
     * remote servers retrieving templates data and to send uninstall feedback.
     *
     * @since 1.0.0
     */
    class Api
    {
        /**
         * Elementor library option key.
         */
        const LIBRARY_OPTION_KEY = 'elementor_remote_info_library';
        /**
         * Elementor feed option key.
         */
        const FEED_OPTION_KEY = 'elementor_remote_info_feed_data';
        const TRANSIENT_KEY_PREFIX = 'elementor_remote_info_api_data_';
        /**
         * API info URL.
         *
         * Holds the URL of the info API.
         *
         * @access public
         * @static
         *
         * @var string API info URL.
         */
        public static $api_info_url = 'https://my.elementor.com/api/v1/info/';
        public static function get_site_key()
        {
        }
        /**
         * Get upgrade notice.
         *
         * Retrieve the upgrade notice if one exists, or false otherwise.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @return array|false Upgrade notice, or false none exist.
         */
        public static function get_upgrade_notice()
        {
        }
        public static function get_admin_notice()
        {
        }
        public static function get_canary_deployment_info($force = false)
        {
        }
        public static function get_promotion_widgets()
        {
        }
        /**
         * Get templates data.
         *
         * Retrieve the templates data from a remote server.
         *
         * @since 2.0.0
         * @access public
         * @static
         *
         * @param bool $force_update Optional. Whether to force the data update or
         *                                     not. Default is false.
         *
         * @return array The templates data.
         */
        public static function get_library_data($force_update = false)
        {
        }
        /**
         * Get feed data.
         *
         * Retrieve the feed info data from remote elementor server.
         *
         * @since 1.9.0
         * @access public
         * @static
         *
         * @param bool $force_update Optional. Whether to force the data update or
         *                                     not. Default is false.
         *
         * @return array Feed data.
         */
        public static function get_feed_data($force_update = false)
        {
        }
        public static function get_deactivation_data()
        {
        }
        public static function get_uninstalled_data()
        {
        }
        /**
         * Get template content.
         *
         * Retrieve the templates content received from a remote server.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param int $template_id The template ID.
         *
         * @return object|\WP_Error The template content.
         */
        public static function get_template_content($template_id)
        {
        }
        /**
         * Send Feedback.
         *
         * Fires a request to Elementor server with the feedback data.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param string $feedback_key  Feedback key.
         * @param string $feedback_text Feedback text.
         *
         * @return array The response of the request.
         */
        public static function send_feedback($feedback_key, $feedback_text)
        {
        }
        /**
         * Ajax reset API data.
         *
         * Reset Elementor library API data using an ajax call.
         *
         * @since 1.0.0
         * @access public
         * @static
         */
        public static function ajax_reset_api_data()
        {
        }
        /**
         * Init.
         *
         * Initialize Elementor API.
         *
         * @since 1.0.0
         * @access public
         * @static
         */
        public static function init()
        {
        }
    }
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
         * Parse CSS rules.
         *
         * Goes over the list of CSS rules and generates the final CSS.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param array $rules CSS rules.
         *
         * @return string Parsed rules.
         */
        public static function parse_rules(array $rules)
        {
        }
        /**
         * Parse CSS properties.
         *
         * Goes over the selector properties and generates the CSS of the selector.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param array $properties CSS properties.
         *
         * @return string Parsed properties.
         */
        public static function parse_properties(array $properties)
        {
        }
        /**
         * Add device.
         *
         * Add a new device to the devices list.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $device_name      Device name.
         * @param string $device_max_point Device maximum point.
         *
         * @return Stylesheet The current stylesheet class instance.
         */
        public function add_device($device_name, $device_max_point)
        {
        }
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
         * @return Stylesheet The current stylesheet class instance.
         */
        public function add_rules($selector, $style_rules = null, array $query = null)
        {
        }
        /**
         * Add raw CSS.
         *
         * Add a raw CSS rule.
         *
         * @since 1.0.8
         * @access public
         *
         * @param string $css    The raw CSS.
         * @param string $device Optional. The device. Default is empty.
         *
         * @return Stylesheet The current stylesheet class instance.
         */
        public function add_raw_css($css, $device = '')
        {
        }
        /**
         * Get CSS rules.
         *
         * Retrieve the CSS rules.
         *
         * @since 1.0.5
         * @access public
         *
         * @param string $device   Optional. The device. Default is empty.
         * @param string $selector Optional. CSS selector. Default is empty.
         * @param string $property Optional. CSS property. Default is empty.
         *
         * @return null|array CSS rules, or `null` if not rules found.
         */
        public function get_rules($device = null, $selector = null, $property = null)
        {
        }
        /**
         * To string.
         *
         * This magic method responsible for parsing the rules into one CSS string.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string CSS style.
         */
        public function __toString()
        {
        }
    }
    /**
     * Elementor compatibility.
     *
     * Elementor compatibility handler class is responsible for compatibility with
     * external plugins. The class resolves different issues with non-compatible
     * plugins.
     *
     * @since 1.0.0
     */
    class Compatibility
    {
        /**
         * Register actions.
         *
         * Run Elementor compatibility with external plugins using custom filters and
         * actions.
         *
         * @since 1.0.0
         * @access public
         * @static
         */
        public static function register_actions()
        {
        }
        public static function return_active()
        {
        }
        public static function clear_3rd_party_cache()
        {
        }
        /**
         * Add new button to gutenberg.
         *
         * Insert new "Elementor" button to the gutenberg editor to create new post
         * using Elementor page builder.
         *
         * @since 1.9.0
         * @access public
         * @static
         */
        public static function add_new_button_to_gutenberg()
        {
        }
        /**
         * Init.
         *
         * Initialize Elementor compatibility with external plugins.
         *
         * Fired by `init` action.
         *
         * @since 1.0.0
         * @access public
         * @static
         */
        public static function init()
        {
        }
        public static function filter_library_post_type($post_types)
        {
        }
        /**
         * Save polylang meta.
         *
         * Copy elementor data while polylang creates a translation copy.
         *
         * Fired by `pll_copy_post_metas` filter.
         *
         * @since 1.6.0
         * @access public
         * @static
         *
         * @param array $keys List of custom fields names.
         * @param bool  $sync True if it is synchronization, false if it is a copy.
         * @param int   $from ID of the post from which we copy information.
         * @param int   $to   ID of the post to which we paste information.
         *
         * @return array List of custom fields names.
         */
        public static function save_polylang_meta($keys, $sync, $from, $to)
        {
        }
        /**
         * Process post meta before WP importer.
         *
         * Normalize Elementor post meta on import, We need the `wp_slash` in order
         * to avoid the unslashing during the `add_post_meta`.
         *
         * Fired by `wp_import_post_meta` filter.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param array $post_meta Post meta.
         *
         * @return array Updated post meta.
         */
        public static function on_wp_import_post_meta($post_meta)
        {
        }
        /**
         * Is WP Importer Before 0.7
         *
         * Checks if WP Importer is installed, and whether its version is older than 0.7.
         *
         * @return bool
         */
        public static function is_wp_importer_before_0_7()
        {
        }
        /**
         * Process post meta before WXR importer.
         *
         * Normalize Elementor post meta on import with the new WP_importer, We need
         * the `wp_slash` in order to avoid the unslashing during the `add_post_meta`.
         *
         * Fired by `wxr_importer.pre_process.post_meta` filter.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @param array $post_meta Post meta.
         *
         * @return array Updated post meta.
         */
        public static function on_wxr_importer_pre_process_post_meta($post_meta)
        {
        }
    }
    /**
     * Elementor autoloader.
     *
     * Elementor autoloader handler class is responsible for loading the different
     * classes needed to run the plugin.
     *
     * @since 1.6.0
     */
    class Autoloader
    {
        /**
         * Run autoloader.
         *
         * Register a function as `__autoload()` implementation.
         *
         * @param string $default_path
         * @param string $default_namespace
         *
         * @since 1.6.0
         * @access public
         * @static
         */
        public static function run($default_path = '', $default_namespace = '')
        {
        }
        /**
         * Get classes aliases.
         *
         * Retrieve the classes aliases names.
         *
         * @since 1.6.0
         * @access public
         * @static
         *
         * @return array Classes aliases.
         */
        public static function get_classes_aliases()
        {
        }
        public static function get_classes_map()
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
         * Get widget upsale data.
         *
         * Retrieve the widget promotion data.
         *
         * @since 3.18.0
         * @access protected
         *
         * @return array|null Widget promotion data.
         */
        protected function get_upsale_data()
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
         * Register widget skins - deprecated prefixed method
         *
         * @since 1.7.12
         * @access protected
         * @deprecated 3.1.0 Use `register_skins()` method instead.
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
         * @param string $lightbox_setting_key  The setting key that dictates whether to open the image in a lightbox
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
         * @param string $plugin_title  Plugin's title
         * @param string $since         Plugin version widget was deprecated
         * @param string $last          Plugin version in which the widget will be removed
         * @param string $replacement   Widget replacement
         */
        protected function deprecated_notice($plugin_title, $since, $last = '', $replacement = '')
        {
        }
        /**
         * Init controls.
         *
         * Reset the `is_first_section` flag to true, so when the Stacks are cleared
         * all the controls will be registered again with their skins and settings.
         *
         * @since 3.14.0
         * @access protected
         */
        protected function init_controls()
        {
        }
        public function register_runtime_widget($widget_name)
        {
        }
        /**
         * Mark widget as deprecated.
         *
         * Use `get_deprecation_message()` method to print the message control at specific location in register_controls().
         *
         * @param $version string           The version of Elementor that deprecated the widget.
         * @param $message string         A message regarding the deprecation.
         * @param $replacement string   The widget that should be used instead.
         */
        protected function add_deprecation_message($version, $message, $replacement)
        {
        }
    }
    /**
     * Elementor skin base.
     *
     * An abstract class to register new skins for Elementor widgets. Skins allows
     * you to add new templates, set custom controls and more.
     *
     * To register new skins for your widget use the `add_skin()` method inside the
     * widget's `register_skins()` method.
     *
     * @since 1.0.0
     * @abstract
     */
    abstract class Skin_Base extends \Elementor\Sub_Controls_Stack
    {
        /**
         * Parent widget.
         *
         * Holds the parent widget of the skin. Default value is null, no parent widget.
         *
         * @access protected
         *
         * @var Widget_Base|null
         */
        protected $parent = null;
        /**
         * Skin base constructor.
         *
         * Initializing the skin base class by setting parent widget and registering
         * controls actions.
         *
         * @since 1.0.0
         * @access public
         * @param Widget_Base $parent
         */
        public function __construct(\Elementor\Widget_Base $parent)
        {
        }
        /**
         * Render skin.
         *
         * Generates the final HTML on the frontend.
         *
         * @since 1.0.0
         * @access public
         * @abstract
         */
        public abstract function render();
        /**
         * Render element in static mode.
         *
         * If not inherent will call the base render.
         */
        public function render_static()
        {
        }
        /**
         * Determine the render logic.
         */
        public function render_by_mode()
        {
        }
        /**
         * Register skin controls actions.
         *
         * Run on init and used to register new skins to be injected to the widget.
         * This method is used to register new actions that specify the location of
         * the skin in the widget.
         *
         * Example usage:
         * `add_action( 'elementor/element/{widget_id}/{section_id}/before_section_end', [ $this, 'register_controls' ] );`
         *
         * @since 1.0.0
         * @access protected
         */
        protected function _register_controls_actions()
        {
        }
        /**
         * Get skin control ID.
         *
         * Retrieve the skin control ID. Note that skin controls have special prefix
         * to distinguish them from regular controls, and from controls in other
         * skins.
         *
         * @since 1.0.0
         * @access protected
         *
         * @param string $control_base_id Control base ID.
         *
         * @return string Control ID.
         */
        protected function get_control_id($control_base_id)
        {
        }
        /**
         * Get skin settings.
         *
         * Retrieve all the skin settings or, when requested, a specific setting.
         *
         * @since 1.0.0
         * @TODO: rename to get_setting() and create backward compatibility.
         *
         * @access public
         *
         * @param string $control_base_id Control base ID.
         *
         * @return mixed
         */
        public function get_instance_value($control_base_id)
        {
        }
        /**
         * Start skin controls section.
         *
         * Used to add a new section of controls to the skin.
         *
         * @since 1.3.0
         * @access public
         *
         * @param string $id   Section ID.
         * @param array  $args Section arguments.
         */
        public function start_controls_section($id, $args = [])
        {
        }
        /**
         * Add new skin control.
         *
         * Register a single control to the allow the user to set/update skin data.
         *
         * @param string $id   Control ID.
         * @param array  $args Control arguments.
         * @param array  $options
         *
         * @return bool True if skin added, False otherwise.
         * @since 3.0.0 New `$options` parameter added.
         * @access public
         *
         */
        public function add_control($id, $args = [], $options = [])
        {
        }
        /**
         * Update skin control.
         *
         * Change the value of an existing skin control.
         *
         * @since 1.3.0
         * @since 1.8.1 New `$options` parameter added.
         *
         * @access public
         *
         * @param string $id      Control ID.
         * @param array  $args    Control arguments. Only the new fields you want to update.
         * @param array  $options Optional. Some additional options.
         */
        public function update_control($id, $args, array $options = [])
        {
        }
        /**
         * Add new responsive skin control.
         *
         * Register a set of controls to allow editing based on user screen size.
         *
         * @param string $id   Responsive control ID.
         * @param array  $args Responsive control arguments.
         * @param array  $options
         *
         * @since  1.0.5
         * @access public
         *
         */
        public function add_responsive_control($id, $args, $options = [])
        {
        }
        /**
         * Start skin controls tab.
         *
         * Used to add a new tab inside a group of tabs.
         *
         * @since 1.5.0
         * @access public
         *
         * @param string $id   Control ID.
         * @param array  $args Control arguments.
         */
        public function start_controls_tab($id, $args)
        {
        }
        /**
         * Start skin controls tabs.
         *
         * Used to add a new set of tabs inside a section.
         *
         * @since 1.5.0
         * @access public
         *
         * @param string $id Control ID.
         */
        public function start_controls_tabs($id)
        {
        }
        /**
         * Add new group control.
         *
         * Register a set of related controls grouped together as a single unified
         * control.
         *
         * @param string $group_name Group control name.
         * @param array  $args       Group control arguments. Default is an empty array.
         * @param array  $options
         *
         * @since  1.0.0
         * @access public
         *
         */
        public final function add_group_control($group_name, $args = [], $options = [])
        {
        }
        /**
         * Set parent widget.
         *
         * Used to define the parent widget of the skin.
         *
         * @since 1.0.0
         * @access public
         *
         * @param Widget_Base $parent Parent widget.
         */
        public function set_parent($parent)
        {
        }
    }
    /**
     * Elementor tabs widget.
     *
     * Elementor widget that displays vertical or horizontal tabs with different
     * pieces of content.
     *
     * @since 1.0.0
     */
    class Widget_Tabs extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve tabs widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve tabs widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve tabs widget icon.
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function show_in_panel() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register tabs widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render tabs widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render tabs widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
    /**
     * Elementor spacer widget.
     *
     * Elementor widget that inserts a space that divides various elements.
     *
     * @since 1.0.0
     */
    class Widget_Spacer extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve spacer widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve spacer widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve spacer widget icon.
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
         * Get widget categories.
         *
         * Retrieve the list of categories the spacer widget belongs to.
         *
         * Used to determine where to display the widget in the editor.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array Widget categories.
         */
        public function get_categories()
        {
        }
        /**
         * Get widget keywords.
         *
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register spacer widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render spacer widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render spacer widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
    /**
     * Elementor icon widget.
     *
     * Elementor widget that displays an icon from over 600+ icons.
     *
     * @since 1.0.0
     */
    class Widget_Icon extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve icon widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve icon widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve icon widget icon.
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
         * Get widget categories.
         *
         * Retrieve the list of categories the icon widget belongs to.
         *
         * Used to determine where to display the widget in the editor.
         *
         * @since 2.0.0
         * @access public
         *
         * @return array Widget categories.
         */
        public function get_categories()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get widget keywords.
         *
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Register icon widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render icon widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render icon widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
    /**
     * Elementor HTML widget.
     *
     * Elementor widget that insert a custom HTML code into the page.
     *
     */
    class Widget_Read_More extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve Read More widget name.
         *
         * @since 2.4.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve Read More widget title.
         *
         * @since 2.4.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve Read More widget icon.
         *
         * @since 2.4.0
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.4.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register HTML widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render Read More widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render Read More widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
    /**
     * Elementor star rating widget.
     *
     * Elementor widget that displays star rating.
     *
     * @since 2.3.0
     */
    class Widget_Star_Rating extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve star rating widget name.
         *
         * @since 2.3.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve star rating widget title.
         *
         * @since 2.3.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve star rating widget icon.
         *
         * @since 2.3.0
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.3.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        /**
         * Hide widget from panel.
         *
         * Hide the star rating widget from the panel.
         *
         * @since 3.17.0
         * @return bool
         */
        public function show_in_panel() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register star rating widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function get_rating()
        {
        }
        /**
         * Print the actual stars and calculate their filling.
         *
         * Rating type is float to allow stars-count to be a fraction.
         * Floored-rating type is int, to represent the rounded-down stars count.
         * In the `for` loop, the index type is float to allow comparing with the rating value.
         *
         * @since 2.3.0
         * @access protected
         */
        protected function render_stars($icon)
        {
        }
        /**
         * @since 2.3.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
    /**
     * Elementor progress widget.
     *
     * Elementor widget that displays an escalating progress bar.
     *
     * @since 1.0.0
     */
    class Widget_Progress extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve progress widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve progress widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve progress widget icon.
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register progress widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render progress widget output on the frontend.
         * Make sure value does no exceed 100%.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render progress widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
    /**
     * Elementor icon list widget.
     *
     * Elementor widget that displays a bullet list with any chosen icons and texts.
     *
     * @since 1.0.0
     */
    class Widget_Icon_List extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve icon list widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve icon list widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve icon list widget icon.
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        /**
         * Register icon list widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render icon list widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render icon list widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
        public function on_import($element)
        {
        }
    }
    /**
     * Elementor common widget.
     *
     * Elementor base widget that gives you all the advanced options of the basic
     * widget.
     *
     * @since 1.0.0
     */
    class Widget_Common_Base extends \Elementor\Widget_Base
    {
        const WRAPPER_SELECTOR = '{{WRAPPER}} .elementor-widget-container';
        const WRAPPER_SELECTOR_CHILD = '{{WRAPPER}} > .elementor-widget-container';
        const WRAPPER_SELECTOR_HOVER = '{{WRAPPER}}:hover .elementor-widget-container';
        const WRAPPER_SELECTOR_HOVER_CHILD = '{{WRAPPER}}:hover > .elementor-widget-container';
        const MASK_SELECTOR_DEFAULT = '{{WRAPPER}}:not( .elementor-widget-image ) .elementor-widget-container';
        const MASK_SELECTOR_IMG = '{{WRAPPER}}.elementor-widget-image .elementor-widget-container img';
        const TRANSFORM_SELECTOR_CLASS = ' > .elementor-widget-container';
        const MARGIN = 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
        /**
         * Get widget name.
         *
         * Retrieve common widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Show in panel.
         *
         * Whether to show the common widget in the panel or not.
         *
         * @since 1.0.0
         * @access public
         *
         * @return bool Whether to show the widget in the panel.
         */
        public function show_in_panel()
        {
        }
        /**
         * Get Responsive Device Args
         *
         * Receives an array of device args, and duplicates it for each active breakpoint.
         * Returns an array of device args.
         *
         * @since 3.4.7
         * @deprecated 3.7.0 Not needed anymore because responsive conditioning in the Editor was fixed in v3.7.0.
         * @access protected
         *
         * @param array $args arguments to duplicate per breakpoint
         * @param array $devices_to_exclude
         *
         * @return array responsive device args
         */
        protected function get_responsive_device_args(array $args, array $devices_to_exclude = [])
        {
        }
        /**
         * Register common widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
    }
    class Widget_Common_Optimized extends \Elementor\Widget_Common_Base
    {
        const WRAPPER_SELECTOR = '{{WRAPPER}}';
        const WRAPPER_SELECTOR_CHILD = '{{WRAPPER}}';
        const WRAPPER_SELECTOR_HOVER = '{{WRAPPER}}:hover';
        const WRAPPER_SELECTOR_HOVER_CHILD = '{{WRAPPER}}:hover';
        const MASK_SELECTOR_DEFAULT = '{{WRAPPER}}:not( .elementor-widget-image )';
        const MASK_SELECTOR_IMG = '{{WRAPPER}}.elementor-widget-image img';
        const TRANSFORM_SELECTOR_CLASS = '';
        const MARGIN = 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} calc(var(--kit-widget-spacing, 0px) + {{BOTTOM}}{{UNIT}}) {{LEFT}}{{UNIT}};';
        public function get_name()
        {
        }
    }
    /**
     * Elementor shortcode widget.
     *
     * Elementor widget that insert any shortcodes into the page.
     *
     * @since 1.0.0
     */
    class Widget_Shortcode extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve shortcode widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve shortcode widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve shortcode widget icon.
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        /**
         * Whether the reload preview is required or not.
         *
         * Used to determine whether the reload preview is required.
         *
         * @since 1.0.0
         * @access public
         *
         * @return bool Whether the reload preview is required.
         */
        public function is_reload_preview_required()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register shortcode widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render shortcode widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render shortcode widget as plain content.
         *
         * Override the default behavior by printing the shortcode instead of rendering it.
         *
         * @since 1.0.0
         * @access public
         */
        public function render_plain_content()
        {
        }
        /**
         * Render shortcode widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
    /**
     * Elementor menu anchor widget.
     *
     * Elementor widget that allows to link and menu to a specific position on the
     * page.
     *
     * @since 1.0.0
     */
    class Widget_Menu_Anchor extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve menu anchor widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve menu anchor widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve menu anchor widget icon.
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register menu anchor widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render menu anchor widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render menu anchor widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
        protected function on_save(array $settings)
        {
        }
    }
}
namespace Elementor\Includes\Widgets\Traits {
    trait Button_Trait
    {
        /**
         * Get button sizes.
         *
         * Retrieve an array of button sizes for the button widget.
         *
         * @since 3.4.0
         * @access public
         * @static
         *
         * @return array An array containing button sizes.
         */
        public static function get_button_sizes()
        {
        }
        /**
         * @since 3.4.0
         *
         * @param array $args {
         *     An array of values for the button adjustments.
         *
         *     @type array  $section_condition  Set of conditions to hide the controls.
         *     @type string $button_text  Text contained in button.
         *     @type string $text_control_label  Name for the label of the text control.
         *     @type array $icon_exclude_inline_options  Set of icon types to exclude from icon controls.
         * }
         */
        protected function register_button_content_controls($args = [])
        {
        }
        /**
         * @since 3.4.0
         *
         * @param array $args {
         *     An array of values for the button adjustments.
         *
         *     @type array  $section_condition  Set of conditions to hide the controls.
         *     @type string $alignment_default  Default position for the button.
         *     @type string $alignment_control_prefix_class  Prefix class name for the button position control.
         *     @type string $content_alignment_default  Default alignment for the button content.
         * }
         */
        protected function register_button_style_controls($args = [])
        {
        }
        /**
         * Render button widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @param \Elementor\Widget_Base|null $instance
         *
         * @since  3.4.0
         * @access protected
         */
        protected function render_button(\Elementor\Widget_Base $instance = null)
        {
        }
        /**
         * Render button widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since  3.4.0
         * @access protected
         */
        protected function content_template()
        {
        }
        /**
         * Render button text.
         *
         * Render button widget text.
         *
         * @param \Elementor\Widget_Base|null $instance
         *
         * @since  3.4.0
         * @access protected
         */
        protected function render_text(\Elementor\Widget_Base $instance = null)
        {
        }
        public function on_import($element)
        {
        }
    }
}
namespace Elementor {
    /**
     * Elementor accordion widget.
     *
     * Elementor widget that displays a collapsible display of content in an
     * accordion style, showing only one item at a time.
     *
     * @since 1.0.0
     */
    class Widget_Accordion extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve accordion widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve accordion widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve accordion widget icon.
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        /**
         * Hide widget from panel.
         *
         * Hide the toggle widget from the panel if nested-accordion experiment is active.
         *
         * @since 3.15.0
         * @return bool
         */
        public function show_in_panel() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register accordion widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render accordion widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render accordion widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
    /**
     * Elementor icon box widget.
     *
     * Elementor widget that displays an icon, a headline and a text.
     *
     * @since 1.0.0
     */
    class Widget_Icon_Box extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve icon box widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve icon box widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve icon box widget icon.
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        /**
         * Register icon box widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render icon box widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render icon box widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
        public function on_import($element)
        {
        }
    }
    /**
     * Elementor divider widget.
     *
     * Elementor widget that displays a line that divides different elements in the
     * page.
     *
     * @since 1.0.0
     */
    class Widget_Divider extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve divider widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve divider widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve divider widget icon.
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
         * Get widget categories.
         *
         * Retrieve the list of categories the divider widget belongs to.
         *
         * Used to determine where to display the widget in the editor.
         *
         * @since 2.0.0
         * @access public
         *
         * @return array Widget categories.
         */
        public function get_categories()
        {
        }
        /**
         * Get widget keywords.
         *
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        /**
         * Register divider widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        public function svg_to_data_uri($svg)
        {
        }
        /**
         * Render divider widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
    }
    /**
     * Elementor alert widget.
     *
     * Elementor widget that displays a collapsible display of content in an toggle
     * style, allowing the user to open multiple items.
     *
     * @since 1.0.0
     */
    class Widget_Alert extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve alert widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve alert widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve alert widget icon.
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register alert widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render alert widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render alert widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
    /**
     * Elementor google maps widget.
     *
     * Elementor widget that displays an embedded google map.
     *
     * @since 1.0.0
     */
    class Widget_Google_Maps extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve google maps widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve google maps widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve google maps widget icon.
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
         * Get widget categories.
         *
         * Retrieve the list of categories the google maps widget belongs to.
         *
         * Used to determine where to display the widget in the editor.
         *
         * @since 2.0.0
         * @access public
         *
         * @return array Widget categories.
         */
        public function get_categories()
        {
        }
        /**
         * Get widget keywords.
         *
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register google maps widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render google maps widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render google maps widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
}
namespace Elementor\Modules\ContentSanitizer\Interfaces {
    interface Sanitizable
    {
        public function sanitize($content);
    }
}
namespace Elementor {
    /**
     * Elementor heading widget.
     *
     * Elementor widget that displays an eye-catching headlines.
     *
     * @since 1.0.0
     */
    class Widget_Heading extends \Elementor\Widget_Base implements \Elementor\Modules\ContentSanitizer\Interfaces\Sanitizable
    {
        /**
         * Get widget name.
         *
         * Retrieve heading widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve heading widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve heading widget icon.
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
         * Get widget categories.
         *
         * Retrieve the list of categories the heading widget belongs to.
         *
         * Used to determine where to display the widget in the editor.
         *
         * @since 2.0.0
         * @access public
         *
         * @return array Widget categories.
         */
        public function get_categories()
        {
        }
        /**
         * Get widget keywords.
         *
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Remove data attributes from the html.
         *
         * @param string $content Heading title
         * @return string
         */
        public function sanitize($content) : string
        {
        }
        /**
         * Register heading widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render heading widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render heading widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
    /**
     * Elementor Inner Section widget.
     *
     * Elementor widget that creates nested columns within a section.
     *
     * @since 3.5.0
     */
    class Widget_Inner_Section extends \Elementor\Widget_Base
    {
        /**
         * @inheritDoc
         */
        public static function get_type()
        {
        }
        /**
         * @inheritDoc
         */
        public function get_name()
        {
        }
        /**
         * @inheritDoc
         */
        public function get_title()
        {
        }
        /**
         * @inheritDoc
         */
        public function get_icon()
        {
        }
        /**
         * @inheritDoc
         */
        public function get_categories()
        {
        }
        /**
         * @inheritDoc
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
    }
    /**
     * Elementor counter widget.
     *
     * Elementor widget that displays stats and numbers in an escalating manner.
     *
     * @since 1.0.0
     */
    class Widget_Counter extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve counter widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve counter widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve counter widget icon.
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
         * Retrieve the list of scripts the counter widget depended on.
         *
         * Used to set scripts dependencies required to run the widget.
         *
         * @since 1.3.0
         * @access public
         *
         * @return array Widget scripts dependencies.
         */
        public function get_script_depends()
        {
        }
        /**
         * Get widget keywords.
         *
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register counter widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render counter widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
        /**
         * Render counter widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
    }
    /**
     * Elementor testimonial widget.
     *
     * Elementor widget that displays customer testimonials that show social proof.
     *
     * @since 1.0.0
     */
    class Widget_Testimonial extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve testimonial widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve testimonial widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve testimonial widget icon.
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register testimonial widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render testimonial widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render testimonial widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
        protected function render_testimonial_description()
        {
        }
    }
    /**
     * Elementor image box widget.
     *
     * Elementor widget that displays an image, a headline and a text.
     *
     * @since 1.0.0
     */
    class Widget_Image_Box extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve image box widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve image box widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve image box widget icon.
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register image box widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render image box widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render image box widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
    /**
     * Elementor image carousel widget.
     *
     * Elementor widget that displays a set of images in a rotating carousel or
     * slider.
     *
     * @since 1.0.0
     */
    class Widget_Image_Carousel extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve image carousel widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve image carousel widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve image carousel widget icon.
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        /**
         * Get script dependencies.
         *
         * Retrieve the list of script dependencies the widget requires.
         *
         * @since 3.27.0
         * @access public
         *
         * @return array Widget script dependencies.
         */
        public function get_script_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register image carousel widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render image carousel widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
    }
    /**
     * Elementor text editor widget.
     *
     * Elementor widget that displays a WYSIWYG text editor, just like the WordPress
     * editor.
     *
     * @since 1.0.0
     */
    class Widget_Text_Editor extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve text editor widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve text editor widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve text editor widget icon.
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
         * Get widget categories.
         *
         * Retrieve the list of categories the text editor widget belongs to.
         *
         * Used to determine where to display the widget in the editor.
         *
         * @since 2.0.0
         * @access public
         *
         * @return array Widget categories.
         */
        public function get_categories()
        {
        }
        /**
         * Get widget keywords.
         *
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register text editor widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render text editor widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render text editor widget as plain content.
         *
         * Override the default behavior by printing the content without rendering it.
         *
         * @since 1.0.0
         * @access public
         */
        public function render_plain_content()
        {
        }
        /**
         * Render text editor widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
    /**
     * Elementor image gallery widget.
     *
     * Elementor widget that displays a set of images in an aligned grid.
     *
     * @since 1.0.0
     */
    class Widget_Image_Gallery extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve image gallery widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve image gallery widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve image gallery widget icon.
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        /**
         * Get widget upsale data.
         *
         * Retrieve the widget promotion data.
         *
         * @since 3.18.0
         * @access protected
         *
         * @return array Widget promotion data.
         */
        protected function get_upsale_data()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register image gallery widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render image gallery widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
    }
    /**
     * Elementor social icons widget.
     *
     * Elementor widget that displays icons to social pages like Facebook and Twitter.
     *
     * @since 1.0.0
     */
    class Widget_Social_Icons extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve social icons widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve social icons widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve social icons widget icon.
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register social icons widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render social icons widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render social icons widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
    /**
     * Elementor WordPress widget.
     *
     * Elementor widget that displays all the WordPress widgets.
     *
     * @since 1.0.0
     */
    class Widget_WordPress extends \Elementor\Widget_Base
    {
        public function hide_on_search()
        {
        }
        /**
         * Get widget name.
         *
         * Retrieve WordPress widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve WordPress widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget categories.
         *
         * Retrieve the list of categories the WordPress widget belongs to.
         *
         * Used to determine where to display the widget in the editor.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array Widget categories. Returns either a WordPress category.
         */
        public function get_categories()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve WordPress widget icon.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget icon. Returns either a WordPress icon.
         */
        public function get_icon()
        {
        }
        /**
         * Get widget keywords.
         *
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.26.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        /**
         * Get script dependencies.
         *
         * Retrieve the list of script dependencies the widget requires.
         *
         * @since 3.27.0
         * @access public
         *
         * @return array Widget script dependencies.
         */
        public function get_script_depends() : array
        {
        }
        public function get_help_url()
        {
        }
        /**
         * Whether the reload preview is required or not.
         *
         * Used to determine whether the reload preview is required.
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
         * Retrieve WordPress widget form.
         *
         * Returns the WordPress widget form, to be used in Elementor.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget form.
         */
        public function get_form()
        {
        }
        /**
         * Retrieve WordPress widget instance.
         *
         * Returns an instance of WordPress widget, to be used in Elementor.
         *
         * @since 1.0.0
         * @access public
         *
         * @return \WP_Widget
         */
        public function get_widget_instance()
        {
        }
        /**
         * Retrieve WordPress widget parsed settings.
         *
         * Returns the WordPress widget settings, to be used in Elementor.
         *
         * @access protected
         * @since 2.3.0
         *
         * @return array Parsed settings.
         */
        protected function get_init_settings()
        {
        }
        /**
         * Register WordPress widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render WordPress widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render WordPress widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
        /**
         * WordPress widget constructor.
         *
         * Used to run WordPress widget constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $data Widget data. Default is an empty array.
         * @param array $args Widget arguments. Default is null.
         */
        public function __construct($data = [], $args = null)
        {
        }
        /**
         * Render WordPress widget as plain content.
         *
         * Override the default render behavior, don't render widget content.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $instance Widget instance. Default is empty array.
         */
        public function render_plain_content($instance = [])
        {
        }
    }
    /**
     * Elementor HTML widget.
     *
     * Elementor widget that insert a custom HTML code into the page.
     *
     * @since 1.0.0
     */
    class Widget_Html extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve HTML widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve HTML widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve HTML widget icon.
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function show_in_panel()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register HTML widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render HTML widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render HTML widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
    /**
     * Elementor video widget.
     *
     * Elementor widget that displays a video player.
     *
     * @since 1.0.0
     */
    class Widget_Video extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve video widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve video widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve video widget icon.
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
         * Get widget categories.
         *
         * Retrieve the list of categories the video widget belongs to.
         *
         * Used to determine where to display the widget in the editor.
         *
         * @since 2.0.0
         * @access public
         *
         * @return array Widget categories.
         */
        public function get_categories()
        {
        }
        /**
         * Get widget keywords.
         *
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register video widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        public function print_a11y_text($image_overlay)
        {
        }
        /**
         * Render video widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render video widget as plain content.
         *
         * Override the default behavior, by printing the video URL instead of rendering it.
         *
         * @since 1.4.5
         * @access public
         */
        public function render_plain_content()
        {
        }
        /**
         * Get embed params.
         *
         * Retrieve video widget embed parameters.
         *
         * @since 1.5.0
         * @access public
         *
         * @return array Video embed parameters.
         */
        public function get_embed_params()
        {
        }
        /**
         * Whether the video widget has an overlay image or not.
         *
         * Used to determine whether an overlay image was set for the video.
         *
         * @since 1.0.0
         * @access protected
         *
         * @return bool Whether an image overlay was set for the video.
         */
        protected function has_image_overlay()
        {
        }
    }
    /**
     * Elementor audio widget.
     *
     * Elementor widget that displays an audio player.
     *
     * @since 1.0.0
     */
    class Widget_Audio extends \Elementor\Widget_Base
    {
        /**
         * Current instance.
         *
         * @access protected
         *
         * @var array
         */
        protected $_current_instance = [];
        /**
         * Get widget name.
         *
         * Retrieve audio widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve audio widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve audio widget icon.
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register audio widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render audio widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Filter audio widget oEmbed results.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $html The HTML returned by the oEmbed provider.
         *
         * @return string Filtered audio widget oEmbed HTML.
         */
        public function filter_oembed_result($html)
        {
        }
        /**
         * Render audio widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
    class Widget_Common extends \Elementor\Widget_Common_Base
    {
        public function get_name()
        {
        }
    }
    /**
     * Elementor (new) rating widget.
     *
     * @since 3.17.0
     */
    class Widget_Rating extends \Elementor\Widget_Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function register_controls()
        {
        }
        protected function get_rating_value() : float
        {
        }
        protected function get_rating_scale() : int
        {
        }
        protected function get_icon_marked_width($icon_index) : string
        {
        }
        protected function get_icon_markup() : string
        {
        }
        protected function render()
        {
        }
    }
    /**
     * Elementor sidebar widget.
     *
     * Elementor widget that insert any sidebar into the page.
     *
     * @since 1.0.0
     */
    class Widget_Sidebar extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve sidebar widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve sidebar widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve sidebar widget icon.
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register sidebar widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render sidebar widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render sidebar widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
        /**
         * Render sidebar widget as plain content.
         *
         * Override the default render behavior, don't render sidebar content.
         *
         * @since 1.0.0
         * @access public
         */
        public function render_plain_content()
        {
        }
    }
    /**
     * Elementor image widget.
     *
     * Elementor widget that displays an image into the page.
     *
     * @since 1.0.0
     */
    class Widget_Image extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve image widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve image widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve image widget icon.
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
         * Get widget categories.
         *
         * Retrieve the list of categories the image widget belongs to.
         *
         * Used to determine where to display the widget in the editor.
         *
         * @since 2.0.0
         * @access public
         *
         * @return array Widget categories.
         */
        public function get_categories()
        {
        }
        /**
         * Get widget keywords.
         *
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register image widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render image widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render image widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
        /**
         * Retrieve image widget link URL.
         *
         * @since 3.11.0
         * @access protected
         *
         * @param array $settings
         *
         * @return array|string|false An array/string containing the link URL, or false if no link.
         */
        protected function get_link_url($settings)
        {
        }
    }
    /**
     * Elementor toggle widget.
     *
     * Elementor widget that displays a collapsible display of content in an toggle
     * style, allowing the user to open multiple items.
     *
     * @since 1.0.0
     */
    class Widget_Toggle extends \Elementor\Widget_Base
    {
        /**
         * Get widget name.
         *
         * Retrieve toggle widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve toggle widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve toggle widget icon.
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
         * Retrieve the list of keywords the widget belongs to.
         *
         * @since 2.1.0
         * @access public
         *
         * @return array Widget keywords.
         */
        public function get_keywords()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * Get style dependencies.
         *
         * Retrieve the list of style dependencies the widget requires.
         *
         * @since 3.24.0
         * @access public
         *
         * @return array Widget style dependencies.
         */
        public function get_style_depends() : array
        {
        }
        /**
         * Hide widget from panel.
         *
         * Hide the toggle widget from the panel if nested-accordion experiment is active.
         *
         * @since 3.15.0
         * @return bool
         */
        public function show_in_panel() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        /**
         * Register toggle widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        /**
         * Render toggle widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
        /**
         * Render toggle widget output in the editor.
         *
         * Written as a Backbone JavaScript template and used to generate the live preview.
         *
         * @since 2.9.0
         * @access protected
         */
        protected function content_template()
        {
        }
    }
    /**
     * Elementor button widget.
     *
     * Elementor widget that displays a button with the ability to control every
     * aspect of the button design.
     *
     * @since 1.0.0
     */
    class Widget_Button extends \Elementor\Widget_Base
    {
        use \Elementor\Includes\Widgets\Traits\Button_Trait;
        /**
         * Get widget name.
         *
         * Retrieve button widget name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget name.
         */
        public function get_name()
        {
        }
        /**
         * Get widget title.
         *
         * Retrieve button widget title.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string Widget title.
         */
        public function get_title()
        {
        }
        /**
         * Get widget icon.
         *
         * Retrieve button widget icon.
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
         * Get widget categories.
         *
         * Retrieve the list of categories the button widget belongs to.
         *
         * Used to determine where to display the widget in the editor.
         *
         * @since 2.0.0
         * @access public
         *
         * @return array Widget categories.
         */
        public function get_categories()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function register_controls()
        {
        }
        /**
         * Render button widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         * @access protected
         */
        protected function render()
        {
        }
    }
    /**
     * Elementor frontend.
     *
     * Elementor frontend handler class is responsible for initializing Elementor in
     * the frontend.
     *
     * @since 1.0.0
     */
    class Frontend extends \Elementor\Core\Base\App
    {
        /**
         * The priority of the content filter.
         */
        const THE_CONTENT_FILTER_PRIORITY = 9;
        /**
         * The priority of the frontend enqueued styles.
         */
        const ENQUEUED_STYLES_PRIORITY = 20;
        /**
         * Fonts to enqueue
         *
         * Holds the list of fonts that are being used in the current page.
         *
         * @since 1.9.4
         * @access public
         *
         * @var array Used fonts. Default is an empty array.
         */
        public $fonts_to_enqueue = [];
        /**
         * Holds the class that respond to manage the render mode.
         *
         * @var Render_Mode_Manager
         */
        public $render_mode_manager;
        /**
         * Front End constructor.
         *
         * Initializing Elementor front end. Make sure we are not in admin, not and
         * redirect from old URL structure of Elementor editor.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Get module name.
         *
         * Retrieve the module name.
         *
         * @since 2.3.0
         * @access public
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
        /**
         * Init render mode manager.
         */
        public function init_render_mode()
        {
        }
        /**
         * Init.
         *
         * Initialize Elementor front end. Hooks the needed actions to run Elementor
         * in the front end, including script and style registration.
         *
         * Fired by `template_redirect` action.
         *
         * @since 1.0.0
         * @access public
         */
        public function init()
        {
        }
        public function print_google_fonts_preconnect_tag()
        {
        }
        /**
         * @since 2.0.12
         * @access public
         * @param string|array $class
         */
        public function add_body_class($class)
        {
        }
        /**
         * Add Theme Color Meta Tag
         *
         * @since 3.0.0
         * @access public
         */
        public function add_theme_color_meta_tag()
        {
        }
        /**
         * Body tag classes.
         *
         * Add new elementor classes to the body tag.
         *
         * Fired by `body_class` filter.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $classes Optional. One or more classes to add to the body tag class list.
         *                       Default is an empty array.
         *
         * @return array Body tag classes.
         */
        public function body_class($classes = [])
        {
        }
        /**
         * Add content filter.
         *
         * Remove plain content and render the content generated by Elementor.
         *
         * @since 1.8.0
         * @access public
         */
        public function add_content_filter()
        {
        }
        /**
         * Remove content filter.
         *
         * When the Elementor generated content rendered, we remove the filter to prevent multiple
         * accuracies. This way we make sure Elementor renders the content only once.
         *
         * @since 1.8.0
         * @access public
         */
        public function remove_content_filter()
        {
        }
        /**
         * Registers scripts.
         *
         * Registers all the frontend scripts.
         *
         * Fired by `wp_enqueue_scripts` action.
         *
         * @since 1.2.1
         * @access public
         */
        public function register_scripts()
        {
        }
        /**
         * Registers styles.
         *
         * Registers all the frontend styles.
         *
         * Fired by `wp_enqueue_scripts` action.
         *
         * @since 1.2.0
         * @access public
         */
        public function register_styles()
        {
        }
        /**
         * Enqueue scripts.
         *
         * Enqueue all the frontend scripts.
         *
         * @since 1.0.0
         * @access public
         */
        public function enqueue_scripts()
        {
        }
        /**
         * Enqueue styles.
         *
         * Enqueue all the frontend styles.
         *
         * Fired by `wp_enqueue_scripts` action.
         *
         * @since 1.0.0
         * @access public
         */
        public function enqueue_styles()
        {
        }
        /**
         * Get Frontend File URL
         *
         * Returns the URL for the CSS file to be loaded in the front end. If requested via the second parameter, a custom
         * file is generated based on a passed template file name. Otherwise, the URL for the default CSS file is returned.
         *
         * @since 3.4.5
         *
         * @access public
         *
         * @param string $frontend_file_name
         * @param boolean $custom_file
         *
         * @return string frontend file URL
         */
        public function get_frontend_file_url($frontend_file_name, $custom_file, $css_subfolder = '')
        {
        }
        /**
         * Get Frontend File Path
         *
         * Returns the path for the CSS file to be loaded in the front end. If requested via the second parameter, a custom
         * file is generated based on a passed template file name. Otherwise, the path for the default CSS file is returned.
         *
         * @since 3.5.0
         * @access public
         *
         * @param string $frontend_file_name
         * @param boolean $custom_file
         *
         * @return string frontend file path
         */
        public function get_frontend_file_path($frontend_file_name, $custom_file)
        {
        }
        /**
         * Get Frontend File
         *
         * Returns a frontend file instance.
         *
         * @since 3.5.0
         * @access public
         *
         * @param string $frontend_file_name
         * @param string $file_prefix
         * @param string $template_file_path
         *
         * @return FrontendFile
         */
        public function get_frontend_file($frontend_file_name, $file_prefix = 'custom-', $template_file_path = '')
        {
        }
        /**
         * Elementor footer scripts and styles.
         *
         * Handle styles and scripts that are not printed in the header.
         *
         * Fired by `wp_footer` action.
         *
         * @since 1.0.11
         * @access public
         */
        public function wp_footer()
        {
        }
        /**
         * @return array|array[]
         */
        public function get_list_of_google_fonts_by_type() : array
        {
        }
        /**
         * Print fonts links.
         *
         * Enqueue all the frontend fonts by url.
         *
         * Fired by `wp_head` action.
         *
         * @since 1.9.4
         * @access public
         */
        public function print_fonts_links()
        {
        }
        /**
         * @param array $fonts Stable google fonts ($google_fonts['google']).
         * @return string
         */
        public function get_stable_google_fonts_url(array $fonts) : string
        {
        }
        /**
         * @param array $fonts Early Access google fonts ($google_fonts['early']).
         * @return array
         */
        public function get_early_access_google_font_urls(array $fonts) : array
        {
        }
        /**
         * Enqueue fonts.
         *
         * Enqueue all the frontend fonts.
         *
         * @since 1.2.0
         * @access public
         *
         * @param array $font Fonts to enqueue in the frontend.
         */
        public function enqueue_font($font)
        {
        }
        /**
         * Apply builder in content.
         *
         * Used to apply the Elementor page editor on the post content.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $content The post content.
         *
         * @return string The post content.
         */
        public function apply_builder_in_content($content)
        {
        }
        /**
         * Retrieve builder content.
         *
         * Used to render and return the post content with all the Elementor elements.
         *
         * Note that this method is an internal method, please use `get_builder_content_for_display()`.
         *
         * @since 1.0.0
         * @access public
         *
         * @param int  $post_id  The post ID.
         * @param bool $with_css Optional. Whether to retrieve the content with CSS
         *                       or not. Default is false.
         *
         * @return string The post content.
         */
        public function get_builder_content($post_id, $with_css = false)
        {
        }
        /**
         * Retrieve builder content for display.
         *
         * Used to render and return the post content with all the Elementor elements.
         *
         * @since 1.0.0
         * @access public
         *
         * @param int $post_id The post ID.
         *
         * @param bool $with_css Optional. Whether to retrieve the content with CSS
         *                       or not. Default is false.
         *
         * @return string The post content.
         */
        public function get_builder_content_for_display($post_id, $with_css = false)
        {
        }
        /**
         * Start excerpt flag.
         *
         * Flags when `the_excerpt` is called. Used to avoid enqueueing CSS in the excerpt.
         *
         * @since 1.4.3
         * @access public
         *
         * @param string $excerpt The post excerpt.
         *
         * @return string The post excerpt.
         */
        public function start_excerpt_flag($excerpt)
        {
        }
        /**
         * End excerpt flag.
         *
         * Flags when `the_excerpt` call ended.
         *
         * @since 1.4.3
         * @access public
         *
         * @param string $excerpt The post excerpt.
         *
         * @return string The post excerpt.
         */
        public function end_excerpt_flag($excerpt)
        {
        }
        /**
         * Remove content filters.
         *
         * Remove WordPress default filters that conflicted with Elementor.
         *
         * @since 1.5.0
         * @access public
         */
        public function remove_content_filters()
        {
        }
        /**
         * Has Elementor In Page
         *
         * Determine whether the current page is using Elementor.
         *
         * @since 2.0.9
         *
         * @access public
         * @return bool
         */
        public function has_elementor_in_page()
        {
        }
        public function create_action_hash($action, array $settings = [])
        {
        }
        /**
         * Is the current render mode is static.
         *
         * @return bool
         */
        public function is_static_render_mode()
        {
        }
        /**
         * Get Init Settings
         *
         * Used to define the default/initial settings of the object. Inheriting classes may implement this method to define
         * their own default/initial settings.
         *
         * @since 2.3.0
         *
         * @access protected
         * @return array
         */
        protected function get_init_settings()
        {
        }
        /**
         * Restore content filters.
         *
         * Restore removed WordPress filters that conflicted with Elementor.
         *
         * @since 1.5.0
         * @access public
         */
        public function restore_content_filters()
        {
        }
    }
}
namespace Elementor\Modules\ElementManager {
    class Ajax
    {
        const ELEMENT_MANAGER_PROMOTION_URL = 'https://go.elementor.com/go-pro-element-manager/';
        const FREE_TO_PRO_PERMISSIONS_PROMOTION_URL = 'https://go.elementor.com/go-pro-element-manager-permissions/';
        const PRO_TO_ADVANCED_PERMISSIONS_PROMOTION_URL = 'https://go.elementor.com/go-pro-advanced-element-manager-permissions/';
        public function register_endpoints()
        {
        }
        public function ajax_get_admin_page_data()
        {
        }
        public function ajax_save_disabled_elements()
        {
        }
        public function ajax_get_widgets_usage()
        {
        }
    }
    class Module extends \Elementor\Core\Base\Module
    {
        const PAGE_ID = 'elementor-element-manager';
        public function get_name()
        {
        }
        public function __construct()
        {
        }
        public function enqueue_assets()
        {
        }
        public function print_styles()
        {
        }
    }
    class Options
    {
        public static function get_disabled_elements()
        {
        }
        public static function update_disabled_elements($elements)
        {
        }
        public static function is_element_disabled($element_name)
        {
        }
    }
    class Admin_Menu_App implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page
    {
        public function is_visible()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_label()
        {
        }
        public function get_page_title()
        {
        }
        public function get_capability()
        {
        }
        public function render()
        {
        }
    }
}
namespace Elementor\Modules\ContainerConverter {
    class Module extends \Elementor\Core\Base\Module
    {
        // Event name dispatched by the buttons.
        const EVENT_NAME = 'elementorContainerConverter:convert';
        /**
         * Retrieve the module name.
         *
         * @return string
         */
        public function get_name()
        {
        }
        /**
         * Determine whether the module is active.
         *
         * @return bool
         */
        public static function is_active()
        {
        }
        /**
         * Enqueue the module scripts.
         *
         * @return void
         */
        public function enqueue_scripts()
        {
        }
        /**
         * Enqueue the module styles.
         *
         * @return void
         */
        public function enqueue_styles()
        {
        }
        /**
         * Add a convert button to sections.
         *
         * @param \Elementor\Controls_Stack $controls_stack
         *
         * @return void
         */
        protected function add_section_convert_button(\Elementor\Controls_Stack $controls_stack)
        {
        }
        /**
         * Add a convert button to page settings.
         *
         * @param \Elementor\Controls_Stack $controls_stack
         *
         * @return void
         */
        protected function add_page_convert_button(\Elementor\Controls_Stack $controls_stack)
        {
        }
        /**
         * Checks if document has any Section elements.
         *
         * @param \Elementor\Controls_Stack $controls_stack
         *
         * @return bool
         */
        protected function page_contains_sections($controls_stack)
        {
        }
        /**
         * Initialize the Container-Converter module.
         *
         * @return void
         */
        public function __construct()
        {
        }
    }
}
namespace Elementor\Modules\Home\Transformations\Base {
    abstract class Transformations_Abstract
    {
        protected \Elementor\Core\Isolation\Wordpress_Adapter_Interface $wordpress_adapter;
        protected \Elementor\Core\Isolation\Plugin_Status_Adapter_Interface $plugin_status_adapter;
        protected \Elementor\Core\Isolation\Elementor_Adapter_Interface $elementor_adapter;
        /**
         * @param $args ?array{
         *     wordpress_adapter: Wordpress_Adapter_Interface,
         *     plugin_status_adapter: Plugin_Status_Adapter_Interface,
         *     elementor_adapter: Elementor_Adapter_Interface,
         * } the adapters to use in the transformations
         */
        public function __construct(array $args = [])
        {
        }
        protected function get_tier()
        {
        }
        public abstract function transform(array $home_screen_data) : array;
    }
}
namespace elementor\modules\home\transformations {
    class Filter_Sidebar_Promotion_By_License extends \Elementor\Modules\Home\Transformations\Base\Transformations_Abstract
    {
        public function transform(array $home_screen_data) : array
        {
        }
    }
}
namespace Elementor\Modules\Home\Transformations {
    class Filter_Get_Started_By_License extends \Elementor\Modules\Home\Transformations\Base\Transformations_Abstract
    {
        public bool $has_pro;
        public function __construct($args)
        {
        }
        public function transform(array $home_screen_data) : array
        {
        }
    }
    class Filter_Condition_Introduction_Meta extends \Elementor\Modules\Home\Transformations\Base\Transformations_Abstract
    {
        public array $introduction_meta_data;
        public function __construct($args)
        {
        }
        public function transform(array $home_screen_data) : array
        {
        }
    }
    class Create_New_Page_Url extends \Elementor\Modules\Home\Transformations\Base\Transformations_Abstract
    {
        public function transform(array $home_screen_data) : array
        {
        }
    }
    class Create_Site_Settings_Url extends \Elementor\Modules\Home\Transformations\Base\Transformations_Abstract
    {
        const SITE_SETTINGS_ITEMS = ['Site Settings', 'Site Logo', 'Global Colors', 'Global Fonts'];
        public function transform(array $home_screen_data) : array
        {
        }
    }
}
namespace elementor\modules\home\transformations {
    class Filter_Top_Section_By_License extends \Elementor\Modules\Home\Transformations\Base\Transformations_Abstract
    {
        public bool $has_pro;
        public function __construct(array $args = [])
        {
        }
        public function transform(array $home_screen_data) : array
        {
        }
    }
}
namespace Elementor\Modules\Home\Transformations {
    class Filter_Plugins extends \Elementor\Modules\Home\Transformations\Base\Transformations_Abstract
    {
        const PLUGIN_IS_NOT_INSTALLED_FROM_WPORG = 'not-installed-wporg';
        const PLUGIN_IS_NOT_INSTALLED_NOT_FROM_WPORG = 'not-installed-not-wporg';
        const PLUGIN_IS_INSTALLED_NOT_ACTIVATED = 'installed-not-activated';
        const PLUGIN_IS_ACTIVATED = 'activated';
        public function transform(array $home_screen_data) : array
        {
        }
    }
}
namespace Elementor\Modules\Home\Classes {
    class Transformations_Manager
    {
        protected array $home_screen_data;
        protected \Elementor\Core\Isolation\Wordpress_Adapter_Interface $wordpress_adapter;
        protected \Elementor\Core\Isolation\Plugin_Status_Adapter $plugin_status_adapter;
        protected array $transformation_classes = [];
        public function __construct($home_screen_data)
        {
        }
        public function run_transformations() : array
        {
        }
    }
}
namespace Elementor\Modules\Home {
    class Module extends \Elementor\Core\Base\App
    {
        const PAGE_ID = 'home_screen';
        public function get_name() : string
        {
        }
        public function __construct()
        {
        }
        public function enqueue_home_screen_scripts() : void
        {
        }
        public function is_experiment_active() : bool
        {
        }
        public function add_active_document_to_edit_link($edit_link)
        {
        }
        public static function get_elementor_settings_page_id() : string
        {
        }
    }
    class API
    {
        protected \Elementor\Includes\EditorAssetsAPI $editor_assets_api;
        public function __construct(\Elementor\Includes\EditorAssetsAPI $editor_assets_api)
        {
        }
        public function get_home_screen_items($force_request = false) : array
        {
        }
    }
}
namespace Elementor\Modules\PerformanceLab {
    class Module extends \Elementor\Core\Base\Module
    {
        const PERFORMANCE_LAB_FUNCTION_NAME = 'webp_uploads_img_tag_update_mime_type';
        const PERFORMANCE_LAB_OPTION_NAME = 'site-health/webp-support';
        public function get_name()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace Elementor\Modules\ImageLoadingOptimization {
    class Module extends \Elementor\Core\Base\Module
    {
        /**
         * Get Module name.
         */
        public function get_name()
        {
        }
        /**
         * Constructor.
         */
        public function __construct()
        {
        }
        /**
         * Stop WordPress core fetchpriority logic by setting the wp_high_priority_element_flag flag to false.
         */
        public function stop_core_fetchpriority_high_logic()
        {
        }
        /**
         * Set buffer to handle header and footer content.
         */
        public function set_buffer()
        {
        }
        /**
         * This function ensure that buffer if any is flushed before the content is called.
         * This function behaves more like an action than a filter.
         *
         * @param string $content the content.
         * @return string We simply return the content from parameter.
         */
        public function flush_header_buffer($content)
        {
        }
        /**
         * Callback to handle image optimization logic on buffered content.
         *
         * @param string $buffer Buffered content.
         * @return string Content with optimized images.
         */
        public function handle_buffer_content($buffer)
        {
        }
        /**
         * Apply loading optimization logic on the image.
         *
         * @param mixed $image Original image tag.
         * @return string Optimized image.
         */
        public function loading_optimization_image($image)
        {
        }
    }
}
namespace Elementor\Modules\ElementCache {
    class Module extends \Elementor\Core\Base\Module
    {
        public function get_name()
        {
        }
        public function __construct()
        {
        }
        public function register_admin_fields(\Elementor\Settings $settings)
        {
        }
        public function clear_cache()
        {
        }
    }
}
namespace Elementor\Modules\EditorAppBar {
    class Module extends \Elementor\Core\Base\Module
    {
        const EXPERIMENT_NAME = 'editor_v2';
        // Kept as `editor_v2` for backward compatibility.
        const PACKAGES = ['editor-app-bar', 'editor-documents', 'editor-panels', 'editor-site-navigation'];
        const STYLES = ['editor-v2-app-bar-overrides'];
        public function get_name()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace Elementor\Modules\ElementorCounter {
    class Module extends \Elementor\Core\Base\Module implements \Elementor\Core\Isolation\Elementor_Counter_Adapter_Interface
    {
        const EDITOR_COUNTER_KEY = 'e_editor_counter';
        public function get_name()
        {
        }
        public function __construct(?\Elementor\Core\Isolation\Wordpress_Adapter_Interface $wordpress_adapter = null)
        {
        }
        /**
         * @param self::EDITOR_COUNTER_KEY $key
         *
         * @return int | null
         */
        public function get_count($key) : ?int
        {
        }
        /**
         * @param self::EDITOR_COUNTER_KEY $key
         * @param int $count
         */
        public function set_count($key, $count = 0) : void
        {
        }
        /**
         * @param self::EDITOR_COUNTER_KEY $key
         */
        public function increment($key) : void
        {
        }
        public function is_key_allowed($key) : bool
        {
        }
    }
}
namespace Elementor\Modules\LazyLoad {
    class Module extends \Elementor\Core\Base\Module
    {
        public function get_name()
        {
        }
        public function __construct()
        {
        }
        public function init()
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\Parsers {
    class Style_Parser
    {
        const VALID_STATES = ['hover', 'active', 'focus', null];
        public function __construct(array $schema)
        {
        }
        public static function make(array $schema) : self
        {
        }
        public function without_id()
        {
        }
        /**
         * @param array $style
         * the style object to validate
         *
         * @return array{
         *     0: bool,
         *     1: array<string, mixed>,
         *     2: array<string>
         * }
         */
        public function validate(array $style) : array
        {
        }
        public function validate_meta($meta) : bool
        {
        }
        /**
         * @param array $style
         * the style object to sanitize
         *
         * @return array<string, mixed>
         */
        public function sanitize(array $style) : array
        {
        }
        /**
         * @param array $style
         * the style object to parse
         *
         * @return array{
         *      0: bool,
         *      1: array<string, mixed>,
         *      2: array<string>
         *  }
         */
        public function parse(array $style) : array
        {
        }
    }
    class Props_Parser
    {
        public function __construct(array $schema)
        {
        }
        public static function make(array $schema) : self
        {
        }
        /**
         * @param array $props
         * The key of each item represents the prop name (should match the schema),
         * and the value is the prop value to validate
         *
         * @return array{
         *     0: bool,
         *     1: array<string, mixed>,
         *     2: array<string>
         * }
         */
        public function validate(array $props) : array
        {
        }
        /**
         * @param array $props
         * The key of each item represents the prop name (should match the schema),
         * and the value is the prop value to sanitize
         *
         * @return array<string, mixed>
         */
        public function sanitize(array $props) : array
        {
        }
        /**
         * @param array $props
         * The key of each item represents the prop name (should match the schema),
         * and the value is the prop value to parse
         *
         * @return array{
         *      0: bool,
         *      1: array<string, mixed>,
         *      2: array<string>
         *  }
         */
        public function parse(array $props) : array
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\PropsResolver {
    class Multi_Props
    {
        public static function is($value)
        {
        }
        public static function generate($value)
        {
        }
        public static function get_value($value)
        {
        }
    }
    abstract class Transformer_Base
    {
        public abstract function transform($value, $key);
    }
}
namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Settings {
    class Image_Transformer extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base
    {
        public function transform($value, $key)
        {
        }
    }
    class Image_Src_Transformer extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base
    {
        // This transformer (or rather this prop type) exists only to support dynamic images.
        // Currently, the dynamic tags that return images return it with id & url no matter
        // what, so we need to keep the same structure in the props.
        public function transform($value, $key)
        {
        }
    }
    class Link_Transformer extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base
    {
        public function transform($value, $key) : array
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers {
    class Combine_Array_Transformer extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base
    {
        public function __construct(string $separator)
        {
        }
        public function transform($value, $key)
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Styles {
    class Background_Color_Overlay_Transformer extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base
    {
        public function transform($value, $key)
        {
        }
    }
    class Edge_Sizes_Transformer extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base
    {
        public function __construct(callable $key_generator)
        {
        }
        public function transform($value, $key)
        {
        }
    }
    class Linked_Dimensions_Transformer extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base
    {
        public function transform($value, $key)
        {
        }
    }
    class Background_Transformer extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base
    {
        public function transform($value, $key)
        {
        }
    }
    class Size_Transformer extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base
    {
        public function transform($value, $key)
        {
        }
    }
    class Corner_Sizes_Transformer extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base
    {
        public function __construct(callable $key_generator)
        {
        }
        public function transform($value, $key)
        {
        }
    }
    class Stroke_Transformer extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base
    {
        public function transform($value, $key)
        {
        }
    }
    class Shadow_Transformer extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base
    {
        public function transform($value, $key)
        {
        }
    }
    class Gap_Transformer extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base
    {
        public function transform($value, $key) : array
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers {
    class Primitive_Transformer extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base
    {
        public function transform($value, $key)
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\PropsResolver {
    class Transformers_Registry extends \Elementor\Core\Utils\Collection
    {
        public function register(string $key, \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base $transformer) : self
        {
        }
    }
    class Props_Resolver
    {
        /**
         * Each transformer can return a value that is also a transformable value,
         * which means that it can be transformed again by another transformer.
         * This constant defines the maximum depth of transformations to avoid infinite loops.
         */
        const TRANSFORM_DEPTH_LIMIT = 3;
        const CONTEXT_SETTINGS = 'settings';
        const CONTEXT_STYLES = 'styles';
        public static function for_styles() : self
        {
        }
        public static function for_settings() : self
        {
        }
        public static function reset() : void
        {
        }
        public function resolve(array $schema, array $props) : array
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\DynamicTags {
    class Dynamic_Tags_Module
    {
        public \Elementor\Modules\AtomicWidgets\DynamicTags\Dynamic_Tags_Registry $registry;
        public static function instance($fresh = false) : self
        {
        }
        public static function fresh() : self
        {
        }
        public function register_hooks()
        {
        }
    }
    class Dynamic_Prop_Types_Mapping
    {
        public static function make() : self
        {
        }
        /**
         * @param array<string, Prop_Type> $schema
         *
         * @return array<string, Prop_Type>
         */
        public function get_modified_prop_types(array $schema) : array
        {
        }
    }
    class Dynamic_Tags_Registry
    {
        public function get_tags() : array
        {
        }
        /**
         * @param string $name
         *
         * @return null|array{
         *       name: string,
         *       categories: string[],
         *       label: string,
         *       group: string,
         *       atomic_controls: array,
         *       props_schema: array<string, Transformable_Prop_Type>
         *  }
         */
        public function get_tag(string $name) : ?array
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\PropTypes\Contracts {
    interface Prop_Type extends \JsonSerializable
    {
        public function get_default();
        public function validate($value) : bool;
        public function sanitize($value);
        public function get_meta() : array;
        public function get_meta_item(string $key, $default = null);
        public function get_settings() : array;
        public function get_setting(string $key, $default = null);
    }
    interface Transformable_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type
    {
        public static function get_key() : string;
    }
}
namespace Elementor\Modules\AtomicWidgets\PropTypes\Concerns {
    trait Has_Default
    {
        protected $default = null;
        /**
         * @param $value
         *
         * @return $this
         */
        public function default($value)
        {
        }
        public function get_default()
        {
        }
        public static abstract function generate($value);
    }
    trait Has_Generate
    {
        public static function generate($value)
        {
        }
        public static abstract function get_key() : string;
    }
    trait Has_Meta
    {
        protected array $meta = [];
        /**
         * @param $key
         * @param $value
         *
         * @return $this
         */
        public function meta($key, $value = null)
        {
        }
        public function get_meta() : array
        {
        }
        public function get_meta_item($key, $default = null)
        {
        }
    }
    trait Has_Required_Setting
    {
        protected function is_required() : bool
        {
        }
        public function required()
        {
        }
        public function optional()
        {
        }
        public abstract function get_setting(string $key, $default = null);
        public abstract function setting($key, $value);
    }
    trait Has_Settings
    {
        protected array $settings = [];
        /**
         * @param $key
         * @param $value
         *
         * @return $this
         */
        public function setting($key, $value)
        {
        }
        public function get_settings() : array
        {
        }
        public function get_setting(string $key, $default = null)
        {
        }
    }
    trait Has_Transformable_Validation
    {
        protected function is_transformable($value) : bool
        {
        }
        public static abstract function get_key() : string;
    }
}
namespace Elementor\Modules\AtomicWidgets\PropTypes\Base {
    abstract class Plain_Prop_Type implements \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Transformable_Prop_Type
    {
        const KIND = 'plain';
        use \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Default, \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Generate, \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Meta, \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Required_Setting, \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Settings, \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Transformable_Validation;
        /**
         * @return static
         */
        public static function make()
        {
        }
        public function validate($value) : bool
        {
        }
        public function sanitize($value)
        {
        }
        public function jsonSerialize() : array
        {
        }
        public static abstract function get_key() : string;
        protected abstract function validate_value($value) : bool;
        protected abstract function sanitize_value($value);
    }
}
namespace Elementor\Modules\AtomicWidgets\DynamicTags {
    class Dynamic_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Plain_Prop_Type
    {
        const META_KEY = 'dynamic';
        /**
         * Return a tuple that lets the developer ignore the dynamic prop type in the props schema
         * using `Prop_Type::add_meta()`, e.g. `String_Prop_Type::make()->add_meta( Dynamic_Prop_Type::ignore() )`.
         */
        public static function ignore() : array
        {
        }
        public static function get_key() : string
        {
        }
        public function categories(array $categories)
        {
        }
        public function get_categories()
        {
        }
        protected function validate_value($value) : bool
        {
        }
        protected function sanitize_value($value) : array
        {
        }
    }
    class Dynamic_Transformer extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base
    {
        public function __construct(\Elementor\Core\DynamicTags\Manager $dynamic_manager)
        {
        }
        public function transform($value, $key)
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\Base {
    abstract class Atomic_Control_Base implements \JsonSerializable
    {
        public abstract function get_type() : string;
        public abstract function get_props() : array;
        public static function bind_to(string $prop_name)
        {
        }
        protected function __construct(string $prop_name)
        {
        }
        public function get_bind()
        {
        }
        public function set_label(string $label) : self
        {
        }
        public function set_description(string $description) : self
        {
        }
        public function jsonSerialize() : array
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\Controls\Types {
    class Link_Control extends \Elementor\Modules\AtomicWidgets\Base\Atomic_Control_Base
    {
        public function get_type() : string
        {
        }
        public function set_placeholder(string $placeholder) : self
        {
        }
        public function get_props() : array
        {
        }
    }
    class Select_Control extends \Elementor\Modules\AtomicWidgets\Base\Atomic_Control_Base
    {
        public function get_type() : string
        {
        }
        public function set_options(array $options) : self
        {
        }
        public function get_props() : array
        {
        }
    }
    class Textarea_Control extends \Elementor\Modules\AtomicWidgets\Base\Atomic_Control_Base
    {
        public function get_type() : string
        {
        }
        public function set_placeholder(string $placeholder) : self
        {
        }
        public function get_props() : array
        {
        }
    }
    class Text_Control extends \Elementor\Modules\AtomicWidgets\Base\Atomic_Control_Base
    {
        public function get_type() : string
        {
        }
        public function set_placeholder(string $placeholder) : self
        {
        }
        public function get_props() : array
        {
        }
    }
    class Image_Control extends \Elementor\Modules\AtomicWidgets\Base\Atomic_Control_Base
    {
        public function get_type() : string
        {
        }
        public function get_props() : array
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\Controls {
    class Section implements \JsonSerializable
    {
        public static function make() : self
        {
        }
        public function set_label(string $label) : self
        {
        }
        public function set_description(string $description) : self
        {
        }
        public function set_items(array $items) : self
        {
        }
        public function get_items()
        {
        }
        public function jsonSerialize() : array
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\Styles {
    class Atomic_Widget_Styles
    {
        public function register_hooks()
        {
        }
    }
    class Styles_Renderer
    {
        /**
         * Styles_Renderer constructor.
         *
         * @param array{
         *     breakpoints: array<string, array{direction: 'min' | 'max', value: int, is_enabled: boolean}>
         * } $config
         */
        public function __construct(array $config)
        {
        }
        public static function make(array $config) : self
        {
        }
        /**
         * Render the styles to a CSS string.
         *
         * @param array<int, array{
         *     id: string,
         *     type: string,
         *     variants: array<int, array{
         *         props: array<string, mixed>,
         *         meta: array<string, mixed>
         *     }>
         * }> $styles Array of style definitions.
         *
         * @return string Rendered CSS string.
         */
        public function render(array $styles) : string
        {
        }
    }
    class Utils
    {
        public static function generate_id(string $prefix = '', $existing_ids = []) : string
        {
        }
    }
    class Style_Schema
    {
        public static function get()
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\PropTypes\Concerns {
    trait Has_Atomic_Base
    {
        public function has_widget_inner_wrapper() : bool
        {
        }
        private function get_valid_controls(array $schema, array $controls) : array
        {
        }
        private static function validate_schema(array $schema)
        {
        }
        private function parse_atomic_styles(array $styles) : array
        {
        }
        private function parse_atomic_settings(array $settings) : array
        {
        }
        public function get_atomic_controls()
        {
        }
        public final function get_controls($control_id = null)
        {
        }
        public final function get_data_for_save()
        {
        }
        public final function get_raw_data($with_html_content = false)
        {
        }
        public final function get_stack($with_common_controls = true)
        {
        }
        public final function get_atomic_settings() : array
        {
        }
        public static function get_props_schema() : array
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\Base {
    abstract class Atomic_Element_Base extends \Elementor\Element_Base
    {
        use \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Atomic_Base;
        protected $version = '0.0';
        protected $styles = [];
        public function __construct($data = [], $args = null)
        {
        }
        protected abstract function define_atomic_controls() : array;
        public final function get_initial_config()
        {
        }
        /**
         * @return array<string, Prop_Type>
         */
        protected static abstract function define_props_schema() : array;
    }
}
namespace Elementor\Modules\AtomicWidgets\Elements {
    class Div_Block extends \Elementor\Modules\AtomicWidgets\Base\Atomic_Element_Base
    {
        public static function get_type()
        {
        }
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_style_depends()
        {
        }
        protected function define_atomic_controls() : array
        {
        }
        protected static function define_props_schema() : array
        {
        }
        protected function _get_default_child_type(array $element_data)
        {
        }
        protected function content_template()
        {
        }
        protected function add_render_attributes()
        {
        }
        public function before_render()
        {
        }
        public function after_render()
        {
        }
        /**
         * Print safe HTML tag for the element based on the element settings.
         *
         * @return void
         */
        protected function print_html_tag()
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets {
    class Module extends \Elementor\Core\Base\Module
    {
        const EXPERIMENT_NAME = 'atomic_widgets';
        const PACKAGES = [
            'editor-canvas',
            'editor-controls',
            // TODO: Need to be registered and not enqueued.
            'editor-editing-panel',
            'editor-elements',
            // TODO: Need to be registered and not enqueued.
            'editor-panels',
            'editor-props',
            // TODO: Need to be registered and not enqueued.
            'editor-styles',
            // TODO: Need to be registered and not enqueued.
            'editor-styles-repository',
        ];
        public function get_name()
        {
        }
        public function __construct()
        {
        }
        public function register_styles()
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\PropTypes\Base {
    abstract class Array_Prop_Type implements \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Transformable_Prop_Type
    {
        const KIND = 'array';
        use \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Default, \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Generate, \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Meta, \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Required_Setting, \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Settings, \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Transformable_Validation;
        protected \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type $item_type;
        public function __construct()
        {
        }
        /**
         * @return static
         */
        public static function make()
        {
        }
        /**
         * @param Prop_Type $item_type
         *
         * @return $this
         */
        public function set_item_type(\Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type $item_type)
        {
        }
        public function get_item_type() : \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type
        {
        }
        public function validate($value) : bool
        {
        }
        protected function validate_value($value) : bool
        {
        }
        public function sanitize($value)
        {
        }
        public function sanitize_value($value)
        {
        }
        public function jsonSerialize() : array
        {
        }
        protected abstract function define_item_type() : \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type;
    }
}
namespace Elementor\Modules\AtomicWidgets\PropTypes {
    class Classes_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type
    {
        public static function get_key() : string
        {
        }
        public function define_item_type() : \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\PropTypes\Base {
    abstract class Object_Prop_Type implements \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Transformable_Prop_Type
    {
        const KIND = 'object';
        use \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Default, \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Generate, \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Meta, \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Required_Setting, \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Settings, \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Transformable_Validation;
        /**
         * @var array<Prop_Type>
         */
        protected array $shape;
        public function __construct()
        {
        }
        /**
         * @return static
         */
        public static function make()
        {
        }
        /**
         * @param array $shape
         *
         * @return $this
         */
        public function set_shape(array $shape)
        {
        }
        public function get_shape() : array
        {
        }
        public function get_shape_field($key) : ?\Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type
        {
        }
        public function validate($value) : bool
        {
        }
        protected function validate_value($value) : bool
        {
        }
        public function sanitize($value)
        {
        }
        public function sanitize_value($value)
        {
        }
        public function jsonSerialize() : array
        {
        }
        /**
         * @return array<Prop_Type>
         */
        protected abstract function define_shape() : array;
    }
}
namespace Elementor\Modules\AtomicWidgets\PropTypes {
    class Stroke_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type
    {
        public static function get_key() : string
        {
        }
        protected function define_shape() : array
        {
        }
    }
    class Image_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type
    {
        public static function get_key() : string
        {
        }
        protected function define_shape() : array
        {
        }
        public function default_url(string $url) : self
        {
        }
        public function default_size(string $size) : self
        {
        }
    }
    class Background_Overlay_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type
    {
        public static function get_key() : string
        {
        }
        protected function define_item_type() : \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type
        {
        }
    }
    class Gap_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type
    {
        public static function get_key() : string
        {
        }
        protected function define_shape() : array
        {
        }
    }
    class Color_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Plain_Prop_Type
    {
        public static function get_key() : string
        {
        }
        protected function validate_value($value) : bool
        {
        }
        protected function sanitize_value($value)
        {
        }
    }
    class Background_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type
    {
        public static function get_key() : string
        {
        }
        protected function define_shape() : array
        {
        }
    }
    class Link_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type
    {
        public static function get_key() : string
        {
        }
        protected function define_shape() : array
        {
        }
    }
    class Union_Prop_Type implements \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type
    {
        const KIND = 'union';
        use \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Meta, \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Settings, \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Required_Setting;
        protected $default = null;
        /** @var Array<string, Transformable_Prop_Type> */
        protected array $prop_types = [];
        public static function make() : self
        {
        }
        public static function create_from(\Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Transformable_Prop_Type $prop_type) : self
        {
        }
        public function add_prop_type(\Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Transformable_Prop_Type $prop_type) : self
        {
        }
        public function get_prop_types() : array
        {
        }
        public function get_prop_type($type) : ?\Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Transformable_Prop_Type
        {
        }
        public function default($value, ?string $type = null) : self
        {
        }
        public function get_default()
        {
        }
        public function validate($value) : bool
        {
        }
        public function sanitize($value)
        {
        }
        public function jsonSerialize() : array
        {
        }
    }
    class Shadow_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type
    {
        public static function get_key() : string
        {
        }
        protected function define_shape() : array
        {
        }
    }
    class Color_Gradient_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type
    {
        public static function get_key() : string
        {
        }
        protected function define_shape() : array
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\PropTypes\Primitives {
    /**
     * @mixin Plain_Prop_Type
     */
    trait Supports_Shorthanded_Value
    {
        public function validate($value) : bool
        {
        }
        public function sanitize($value)
        {
        }
        /**
         * @param $value
         *
         * @return $this
         */
        public function default($value)
        {
        }
    }
    class Number_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Plain_Prop_Type
    {
        use \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Supports_Shorthanded_Value;
        public static function get_key() : string
        {
        }
        protected function validate_value($value) : bool
        {
        }
        protected function sanitize_value($value)
        {
        }
    }
    class String_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Plain_Prop_Type
    {
        use \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Supports_Shorthanded_Value;
        public static function get_key() : string
        {
        }
        public function enum(array $allowed_values) : self
        {
        }
        public function get_enum()
        {
        }
        public function regex($pattern)
        {
        }
        public function get_regex()
        {
        }
        protected function validate_value($value) : bool
        {
        }
        protected function sanitize_value($value)
        {
        }
    }
    class Boolean_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Plain_Prop_Type
    {
        use \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Supports_Shorthanded_Value;
        public static function get_key() : string
        {
        }
        protected function validate_value($value) : bool
        {
        }
        protected function sanitize_value($value)
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\PropTypes {
    class Border_Width_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type
    {
        public static function get_key() : string
        {
        }
        protected function define_shape() : array
        {
        }
    }
    class Linked_Dimensions_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type
    {
        public static function get_key() : string
        {
        }
        protected function define_shape() : array
        {
        }
    }
    class Image_Attachment_Id_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Plain_Prop_Type
    {
        public static function get_key() : string
        {
        }
        protected function validate_value($value) : bool
        {
        }
        protected function sanitize_value($value) : int
        {
        }
    }
    class Border_Radius_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type
    {
        public static function get_key() : string
        {
        }
        protected function define_shape() : array
        {
        }
    }
    class Size_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type
    {
        const SUPPORTED_UNITS = ['px', 'em', 'rem', '%', 'vh', 'vw', 'vmin', 'vmax'];
        public static function get_key() : string
        {
        }
        protected function define_shape() : array
        {
        }
    }
    class Url_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Plain_Prop_Type
    {
        public static function get_key() : string
        {
        }
        protected function validate_value($value) : bool
        {
        }
        protected function sanitize_value($value)
        {
        }
    }
    class Background_Color_Overlay_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Color_Prop_Type
    {
        public static function get_key() : string
        {
        }
    }
    class Image_Src_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type
    {
        public static function get_key() : string
        {
        }
        protected function define_shape() : array
        {
        }
        protected function validate_value($value) : bool
        {
        }
    }
    class Box_Shadow_Prop_Type extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type
    {
        public static function get_key() : string
        {
        }
        protected function define_item_type() : \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets\Base {
    abstract class Atomic_Widget_Base extends \Elementor\Widget_Base
    {
        use \Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Atomic_Base;
        protected $version = '0.0';
        protected $styles = [];
        public function __construct($data = [], $args = null)
        {
        }
        protected abstract function define_atomic_controls() : array;
        public final function get_initial_config()
        {
        }
        // Removes the wrapper div from the widget.
        public function before_render()
        {
        }
        public function after_render()
        {
        }
        /**
         * @return array<string, Prop_Type>
         */
        protected static abstract function define_props_schema() : array;
    }
    abstract class Style_Transformer_Base
    {
        /**
         * Get the transformer type.
         *
         * @return string
         */
        public static abstract function type() : string;
        /**
         * Transform the value.
         *
         * @param mixed $value
         *
         * @return mixed
         */
        public abstract function transform($value, callable $transform);
    }
}
namespace Elementor\Modules\AtomicWidgets\Widgets {
    class Atomic_Image extends \Elementor\Modules\AtomicWidgets\Base\Atomic_Widget_Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        protected function render()
        {
        }
        protected function define_atomic_controls() : array
        {
        }
        protected static function define_props_schema() : array
        {
        }
    }
    class Atomic_Heading extends \Elementor\Modules\AtomicWidgets\Base\Atomic_Widget_Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        protected function render()
        {
        }
        protected function define_atomic_controls() : array
        {
        }
        protected static function define_props_schema() : array
        {
        }
    }
}
namespace Elementor\Modules\AtomicWidgets {
    class Image_Sizes
    {
        public static function get_keys()
        {
        }
        public static function get_all() : array
        {
        }
    }
}
namespace Elementor\Modules\Favorites {
    class Controller extends \Elementor\Data\V2\Base\Controller
    {
        public function get_name()
        {
        }
        public function create_item($request)
        {
        }
        public function delete_item($request)
        {
        }
        public function create_item_permissions_check($request)
        {
        }
        public function delete_item_permissions_check($request)
        {
        }
        /**
         * Get the favorites module instance.
         *
         * @return Module
         */
        protected function get_module()
        {
        }
        public function register_endpoints()
        {
        }
    }
    abstract class Favorites_Type extends \Elementor\Core\Utils\Static_Collection
    {
        public function __construct(array $items = [])
        {
        }
        /**
         * Get the name of the type.
         *
         * @return mixed
         */
        public abstract function get_name();
        /**
         * Prepare favorites before taking any action.
         *
         * @param Collection|array|string $favorites
         *
         * @return array
         */
        public function prepare($favorites)
        {
        }
    }
}
namespace Elementor\Modules\Favorites\Types {
    class Widgets extends \Elementor\Modules\Favorites\Favorites_Type
    {
        const CATEGORY_SLUG = 'favorites';
        /**
         * Widgets favorites type constructor.
         */
        public function __construct(array $items = [])
        {
        }
        public function get_name()
        {
        }
        public function prepare($favorites)
        {
        }
        /**
         * Get all available widgets.
         *
         * @return string[]
         */
        public function get_available()
        {
        }
        /**
         * Update the categories of a widget inside a filter.
         *
         * @param $document
         */
        public function update_widget_categories($document)
        {
        }
    }
}
namespace Elementor\Modules\Favorites {
    class Module extends \Elementor\Core\Base\Module
    {
        /**
         * List of registered favorites type.
         *
         * @var Favorites_Type[]
         */
        protected $types = [];
        const OPTION_NAME = 'elementor_editor_user_favorites';
        /**
         * The name of the merge action.
         *
         * @var string
         */
        const ACTION_MERGE = 'merge';
        /**
         * The name of the delete action.
         *
         * @var string
         */
        const ACTION_DELETE = 'delete';
        /**
         * Favorites module constructor.
         */
        public function __construct()
        {
        }
        /**
         * Add usage data related to favorites.
         *
         * @param $params
         *
         * @return array
         */
        public function add_tracking_data($params)
        {
        }
        public function get_name()
        {
        }
        /**
         * Get user favorites by type.
         *
         * @param string[]|string $type
         *
         * @return array
         */
        public function get($type = null)
        {
        }
        /**
         * Merge new user favorites to a type.
         *
         * @param string        $type
         * @param array|string  $favorites
         * @param bool          $store
         *
         * @return array|bool
         */
        public function merge($type, $favorites, $store = true)
        {
        }
        /**
         * Delete existing favorites from a type.
         *
         * @param string        $type
         * @param array|string  $favorites
         * @param bool          $store
         *
         * @return array|int
         */
        public function delete($type, $favorites, $store = true)
        {
        }
        /**
         * Update favorites on a type by merging or deleting from it.
         *
         * @param      $type
         * @param      $favorites
         * @param      $action
         * @param bool $store
         *
         * @return array|boolean
         */
        public function update($type, $favorites, $action, $store = true)
        {
        }
        /**
         * Get registered favorites type instance.
         *
         * @param string $type
         *
         * @return Favorites_Type
         */
        public function type_instance($type)
        {
        }
        /**
         * Register a new type class.
         *
         * @param string $class
         */
        public function register($class)
        {
        }
        /**
         * Returns all available types keys.
         *
         * @return string[]
         */
        public function available()
        {
        }
        /**
         * Combine favorites from all types into a single array.
         *
         * @return array
         */
        protected function combined()
        {
        }
        /**
         * Populate all type classes with the stored data.
         */
        protected function populate()
        {
        }
        /**
         * Retrieve stored user favorites types.
         *
         * @return mixed|false
         */
        protected function retrieve()
        {
        }
        /**
         * Update all changes to user favorites type.
         *
         * @return int|bool
         */
        protected function store()
        {
        }
        /**
         * Throw action doesn't exist exception.
         *
         * @param string $action
         */
        public function action_doesnt_exists($action)
        {
        }
    }
}
namespace Elementor\Modules\FloatingButtons\Classes\Render {
    /**
     * Class Contact_Buttons_Render_Base.
     *
     * This is the base class that will hold shared functionality that will be needed by all the various widget versions.
     *
     * @since 3.23.0
     */
    abstract class Contact_Buttons_Render_Base
    {
        protected \Elementor\Modules\FloatingButtons\Base\Widget_Contact_Button_Base $widget;
        protected array $settings;
        public abstract function render() : void;
        public function __construct(\Elementor\Modules\FloatingButtons\Base\Widget_Contact_Button_Base $widget)
        {
        }
        protected function render_chat_button_icon() : void
        {
        }
        protected function render_chat_button() : void
        {
        }
        protected function render_close_button() : void
        {
        }
        protected function render_top_bar() : void
        {
        }
        protected function render_message_bubble_typing_animation() : void
        {
        }
        protected function render_message_bubble_container() : void
        {
        }
        protected function render_message_bubble_powered_by() : void
        {
        }
        protected function render_message_bubble() : void
        {
        }
        protected function render_contact_text() : void
        {
        }
        protected function render_contact_links() : void
        {
        }
        protected function render_contact_section() : void
        {
        }
        protected function render_send_button() : void
        {
        }
        protected function get_formatted_link(array $link, string $prefix) : string
        {
        }
        protected function is_url_link(string $platform) : bool
        {
        }
        protected function render_link_attributes(array $link, string $key)
        {
        }
        protected function build_layout_render_attribute() : void
        {
        }
    }
    /**
     * Class Contact_Buttons_Core_Render.
     *
     * This class handles the rendering of the Contact Buttons widget for the core version.
     *
     * @since 3.23.0
     */
    class Contact_Buttons_Core_Render extends \Elementor\Modules\FloatingButtons\Classes\Render\Contact_Buttons_Render_Base
    {
        public function render() : void
        {
        }
        protected function add_layout_render_attribute($layout_classnames)
        {
        }
        protected function add_content_wrapper_render_attribute()
        {
        }
    }
    /**
     * Class Floating_Bars_Render_Base.
     *
     * This is the base class that will hold shared functionality that will be needed by all the various widget versions.
     *
     * @since 3.23.0
     */
    abstract class Floating_Bars_Render_Base
    {
        protected \Elementor\Modules\FloatingButtons\Base\Widget_Floating_Bars_Base $widget;
        protected array $settings;
        public abstract function render() : void;
        public function __construct(\Elementor\Modules\FloatingButtons\Base\Widget_Floating_Bars_Base $widget)
        {
        }
        protected function add_layout_render_attribute($layout_classnames)
        {
        }
        public static function get_layout_classnames(\Elementor\Modules\FloatingButtons\Base\Widget_Floating_Bars_Base $widget, array $settings) : string
        {
        }
        protected function build_layout_render_attribute() : void
        {
        }
    }
    /**
     * Class Floating_Bars_Core_Render.
     *
     * This class handles the rendering of the Floating Bars widget for the core version.
     *
     * @since 3.23.0
     */
    class Floating_Bars_Core_Render extends \Elementor\Modules\FloatingButtons\Classes\Render\Floating_Bars_Render_Base
    {
        protected function render_announcement_icon() : void
        {
        }
        protected function render_announcement_text() : void
        {
        }
        protected function render_cta_icon() : void
        {
        }
        protected function render_cta_button() : void
        {
        }
        protected function render_close_button() : void
        {
        }
        public function render() : void
        {
        }
    }
}
namespace Elementor\Modules\FloatingButtons {
    class Module extends \Elementor\Core\Base\Module
    {
        const EXPERIMENT_NAME = 'floating-buttons';
        const FLOATING_ELEMENTS_TYPE_META_KEY = '_elementor_floating_elements_type';
        const ROUTER_OPTION_KEY = 'elementor_floating_buttons_router_version';
        const META_CLICK_TRACKING = '_elementor_click_tracking';
        const CLICK_TRACKING_NONCE = 'elementor-conversion-center-click';
        const FLOATING_BUTTONS_DOCUMENT_TYPE = 'floating-buttons';
        const CPT_FLOATING_BUTTONS = 'e-floating-buttons';
        const ADMIN_PAGE_SLUG_CONTACT = 'edit.php?post_type=e-floating-buttons';
        const WIDGET_HAS_CUSTOM_BREAKPOINTS = true;
        public static function is_active() : bool
        {
        }
        public static function get_floating_elements_types()
        {
        }
        // TODO: This is a hidden experiment which needs to remain enabled like this until 3.26 for pro compatibility.
        public static function get_experimental_data()
        {
        }
        public function get_name() : string
        {
        }
        public function get_widgets() : array
        {
        }
        public function __construct()
        {
        }
        public function is_preview_for_document($post_id)
        {
        }
        public function handle_click_tracking()
        {
        }
        public function set_admin_columns_content($column_name, $post_id)
        {
        }
        public function flush_permalinks_on_elementor_version_change()
        {
        }
        public function print_empty_contact_pages_page()
        {
        }
        public function override_admin_bar_add_contact($admin_bar) : void
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/floating-buttons/assets/scss/widgets/*.scss`
         * to `/assets/css/widget-*.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace Elementor\Modules\Library\Traits {
    /**
     * Elementor Library Trait
     *
     * This trait is used by all Library Documents and Landing Pages.
     *
     * @since 3.1.0
     */
    trait Library
    {
        /**
         * Print Admin Column Type
         *
         * Runs on WordPress' 'manage_{custom post type}_posts_custom_column' hook to modify each row's content.
         *
         * @since 3.1.0
         * @access public
         */
        public function print_admin_column_type()
        {
        }
        /**
         * Save document type.
         *
         * Set new/updated document type.
         *
         * @since 3.1.0
         * @access public
         */
        public function save_template_type()
        {
        }
    }
}
namespace Elementor\Modules\FloatingButtons\Documents {
    class Floating_Buttons extends \Elementor\Core\DocumentTypes\PageBase
    {
        use \Elementor\Modules\Library\Traits\Library;
        public static function get_properties()
        {
        }
        public static function get_floating_element_type($post_id)
        {
        }
        public static function is_editing_existing_floating_buttons_page()
        {
        }
        public static function is_creating_floating_buttons_page()
        {
        }
        public static function is_floating_buttons_type_meta_key($post_id)
        {
        }
        public function print_content()
        {
        }
        public function get_location()
        {
        }
        public static function get_type()
        {
        }
        public static function register_post_fields_control($document)
        {
        }
        public static function register_hide_title_control($document)
        {
        }
        public function get_name()
        {
        }
        public function filter_admin_row_actions($actions)
        {
        }
        public static function get_meta_query_for_floating_buttons(string $floating_element_type) : array
        {
        }
        /**
         * Tries to find the post id of the floating element that is set as entire site.
         * If found, returns the post id, otherwise returns 0.
         *
         * @param string $floating_element_type
         *
         * @return int
         */
        public static function get_set_as_entire_site_post_id(string $floating_element_type) : int
        {
        }
        public function set_as_entire_site($actions)
        {
        }
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        public static function get_create_url()
        {
        }
        public function save($data)
        {
        }
        public function admin_columns_content($column_name)
        {
        }
        public function get_edit_url()
        {
        }
        protected function get_remote_library_config()
        {
        }
    }
}
namespace Elementor\Modules\FloatingButtons\AdminMenuItems {
    class Floating_Buttons_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item
    {
        public function is_visible()
        {
        }
        public function get_parent_slug()
        {
        }
        public function get_label()
        {
        }
        public function get_page_title()
        {
        }
        public function get_capability()
        {
        }
    }
    class Floating_Buttons_Empty_View_Menu_Item extends \Elementor\Modules\FloatingButtons\AdminMenuItems\Floating_Buttons_Menu_Item implements \Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page
    {
        public function __construct(callable $render_callback)
        {
        }
        public function render()
        {
        }
    }
}
namespace Elementor\Modules\FloatingButtons\Control {
    class Hover_Animation_Floating_Buttons extends \Elementor\Control_Hover_Animation
    {
        const TYPE = 'hover_animation_contact_buttons';
        public function get_type()
        {
        }
        public static function get_animations()
        {
        }
    }
}
namespace Elementor\Modules\FloatingButtons\Base {
    abstract class Widget_Floating_Bars_Base extends \Elementor\Widget_Base
    {
        const TAB_ADVANCED = 'advanced-tab-floating-bars';
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        public function get_icon() : string
        {
        }
        public function show_in_panel()
        {
        }
        public function hide_on_search()
        {
        }
        protected function get_initial_config() : array
        {
        }
        public static function get_configuration()
        {
        }
        protected function register_controls() : void
        {
        }
        protected function add_announcement_content_section() : void
        {
        }
        protected function add_cta_button_content_section() : void
        {
        }
        protected function add_accessible_name_control() : void
        {
        }
        protected function add_floating_bar_content_section() : void
        {
        }
        protected function add_headlines_content_section() : void
        {
        }
        protected function add_announcement_style_section() : void
        {
        }
        protected function add_cta_button_style_section() : void
        {
        }
        protected function add_floating_bar_background_style_controls() : void
        {
        }
        protected function add_floating_bar_close_button_style_controls() : void
        {
        }
        protected function add_floating_bar_pause_style_controls() : void
        {
        }
        protected function add_floating_bar_style_section() : void
        {
        }
        protected function add_headlines_style_section() : void
        {
        }
        protected function add_advanced_tab() : void
        {
        }
        protected function add_content_tab() : void
        {
        }
        protected function add_style_tab() : void
        {
        }
        protected function render() : void
        {
        }
    }
    abstract class Widget_Contact_Button_Base extends \Elementor\Widget_Base
    {
        use \Elementor\Core\Base\Traits\Shared_Widget_Controls_Trait;
        const TAB_ADVANCED = 'advanced-tab-floating-buttons';
        public function show_in_panel() : bool
        {
        }
        public function get_group_name() : string
        {
        }
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        public function hide_on_search() : bool
        {
        }
        protected function get_initial_config() : array
        {
        }
        public static function get_configuration()
        {
        }
        public function get_icon() : string
        {
        }
        public function get_categories() : array
        {
        }
        protected function register_controls() : void
        {
        }
        protected function add_chat_button_section() : void
        {
        }
        protected function add_top_bar_section() : void
        {
        }
        protected function add_message_bubble_section() : void
        {
        }
        protected function add_contact_section() : void
        {
        }
        protected function get_icon_title_field() : string
        {
        }
        protected function add_send_button_section() : void
        {
        }
        protected function add_content_tab() : void
        {
        }
        protected function add_style_chat_button_section() : void
        {
        }
        protected function add_style_top_bar_section() : void
        {
        }
        protected function add_style_message_bubble_section() : void
        {
        }
        protected function add_style_contact_section() : void
        {
        }
        protected function add_style_resource_links_section() : void
        {
        }
        protected function add_style_info_links_section() : void
        {
        }
        protected function add_style_send_button_section() : void
        {
        }
        protected function chat_box_animation_controls() : void
        {
        }
        protected function add_style_chat_box_section() : void
        {
        }
        protected function add_style_tab() : void
        {
        }
        protected function render() : void
        {
        }
    }
}
namespace Elementor\Modules\FloatingButtons\Widgets {
    /**
     * Elementor Floating Bars Var 1 widget.
     *
     * Elementor widget that displays a banner with icon and link
     *
     * @since 3.23.0
     */
    class Floating_Bars_Var_1 extends \Elementor\Modules\FloatingButtons\Base\Widget_Floating_Bars_Base
    {
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
        public function get_group_name() : string
        {
        }
        public function render() : void
        {
        }
    }
    /**
     * Elementor Contact Buttons widget.
     *
     * Elementor widget that displays contact buttons and a chat-like prompt message.
     *
     * @since 3.23.0
     */
    class Contact_Buttons extends \Elementor\Modules\FloatingButtons\Base\Widget_Contact_Button_Base
    {
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
    }
}
namespace Elementor\Modules\ContentSanitizer {
    class Module extends \Elementor\Core\Base\Module
    {
        const WIDGET_TO_SANITIZE = 'heading';
        public function __construct()
        {
        }
        public function get_name()
        {
        }
        public function sanitize_content($data, $document) : array
        {
        }
    }
}
namespace Elementor\Modules\Gutenberg {
    class Module extends \Elementor\Core\Base\Module
    {
        protected $is_gutenberg_editor_active = false;
        /**
         * @since 2.1.0
         * @access public
         */
        public function get_name()
        {
        }
        /**
         * @since 2.1.0
         * @access public
         * @static
         */
        public static function is_active()
        {
        }
        /**
         * @since 2.1.0
         * @access public
         */
        public function register_elementor_rest_field()
        {
        }
        /**
         * @since 2.1.0
         * @access public
         */
        public function enqueue_assets()
        {
        }
        /**
         * @since 2.1.0
         * @access public
         */
        public function print_admin_js_template()
        {
        }
        /**
         * @since 2.1.0
         * @access public
         */
        public function __construct()
        {
        }
        public function dequeue_assets()
        {
        }
    }
}
namespace Elementor\Modules\DynamicTags {
    /**
     * Elementor dynamic tags module.
     *
     * Elementor dynamic tags module handler class is responsible for registering
     * and managing Elementor dynamic tags modules.
     *
     * @since 2.0.0
     */
    class Module extends \Elementor\Core\Base\Module
    {
        /**
         * Base dynamic tag group.
         */
        const BASE_GROUP = 'base';
        /**
         * Dynamic tags text category.
         */
        const TEXT_CATEGORY = 'text';
        /**
         * Dynamic tags URL category.
         */
        const URL_CATEGORY = 'url';
        /**
         * Dynamic tags image category.
         */
        const IMAGE_CATEGORY = 'image';
        /**
         * Dynamic tags media category.
         */
        const MEDIA_CATEGORY = 'media';
        /**
         * Dynamic tags post meta category.
         */
        const POST_META_CATEGORY = 'post_meta';
        /**
         * Dynamic tags gallery category.
         */
        const GALLERY_CATEGORY = 'gallery';
        /**
         * Dynamic tags number category.
         */
        const NUMBER_CATEGORY = 'number';
        /**
         * Dynamic tags number category.
         */
        const COLOR_CATEGORY = 'color';
        /**
         * Dynamic tags datetime category.
         */
        const DATETIME_CATEGORY = 'datetime';
        /**
         * Dynamic tags module constructor.
         *
         * Initializing Elementor dynamic tags module.
         *
         * @since 2.0.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * Get module name.
         *
         * Retrieve the dynamic tags module name.
         *
         * @since 2.0.0
         * @access public
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
        /**
         * Get classes names.
         *
         * Retrieve the dynamic tag classes names.
         *
         * @since 2.0.0
         * @access public
         *
         * @return array Tag dynamic tag classes names.
         */
        public function get_tag_classes_names()
        {
        }
        /**
         * Get groups.
         *
         * Retrieve the dynamic tag groups.
         *
         * @since 2.0.0
         * @access public
         *
         * @return array Tag dynamic tag groups.
         */
        public function get_groups()
        {
        }
        /**
         * Register tags.
         *
         * Add all the available dynamic tags.
         *
         * @since 2.0.0
         * @access public
         *
         * @param Manager $dynamic_tags
         */
        public function register_tags($dynamic_tags)
        {
        }
    }
}
namespace Elementor\Modules\Library {
    class User_Favorites
    {
        const USER_META_KEY = 'elementor_library_favorites';
        /**
         * User_Favorites constructor.
         *
         * @param $user_id
         */
        public function __construct($user_id)
        {
        }
        /**
         * @param null  $vendor
         * @param null  $resource
         * @param false $ignore_cache
         *
         * @return array
         */
        public function get($vendor = null, $resource = null, $ignore_cache = false)
        {
        }
        /**
         * @param $vendor
         * @param $resource
         * @param $id
         *
         * @return bool
         */
        public function exists($vendor, $resource, $id)
        {
        }
        /**
         * @param       $vendor
         * @param       $resource
         * @param array $value
         *
         * @return $this
         * @throws \Exception
         */
        public function save($vendor, $resource, $value = [])
        {
        }
        /**
         * @param $vendor
         * @param $resource
         * @param $id
         *
         * @return $this
         * @throws \Exception
         */
        public function add($vendor, $resource, $id)
        {
        }
        /**
         * @param $vendor
         * @param $resource
         * @param $id
         *
         * @return $this
         * @throws \Exception
         */
        public function remove($vendor, $resource, $id)
        {
        }
    }
    /**
     * Elementor library module.
     *
     * Elementor library module handler class is responsible for registering and
     * managing Elementor library modules.
     *
     * @since 2.0.0
     */
    class Module extends \Elementor\Core\Base\Module
    {
        /**
         * Get module name.
         *
         * Retrieve the library module name.
         *
         * @since 2.0.0
         * @access public
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
        /**
         * Library module constructor.
         *
         * Initializing Elementor library module.
         *
         * @since 2.0.0
         * @access public
         */
        public function __construct()
        {
        }
    }
}
namespace Elementor\Modules\Library\Documents {
    /**
     * Elementor library document.
     *
     * Elementor library document handler class is responsible for handling
     * a document of the library type.
     *
     * @since 2.0.0
     */
    abstract class Library_Document extends \Elementor\Core\Base\Document
    {
        // Library Document Trait
        use \Elementor\Modules\Library\Traits\Library;
        /**
         * The taxonomy type slug for the library document.
         */
        const TAXONOMY_TYPE_SLUG = 'elementor_library_type';
        /**
         * Get document properties.
         *
         * Retrieve the document properties.
         *
         * @since 2.0.0
         * @access public
         * @static
         *
         * @return array Document properties.
         */
        public static function get_properties()
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
        public function get_initial_config()
        {
        }
        public function get_content($with_css = false)
        {
        }
    }
    /**
     * Elementor container library document.
     *
     * Elementor container library document handler class is responsible for
     * handling a document of a container type.
     *
     * @since 2.0.0
     */
    class Container extends \Elementor\Modules\Library\Documents\Library_Document
    {
        public static function get_properties()
        {
        }
        /**
         * Get document name.
         *
         * Retrieve the document name.
         *
         * @since 2.0.0
         * @access public
         *
         * @return string Document name.
         */
        public function get_name()
        {
        }
        /**
         * Get document title.
         *
         * Retrieve the document title.
         *
         * @since 2.0.0
         * @access public
         * @static
         *
         * @return string Document title.
         */
        public static function get_title()
        {
        }
        /**
         * Get Type
         *
         * Return the container document type.
         *
         * @return string
         */
        public static function get_type()
        {
        }
    }
    /**
     * Elementor section library document.
     *
     * Elementor section library document handler class is responsible for
     * handling a document of a section type.
     *
     */
    class Not_Supported extends \Elementor\Modules\Library\Documents\Library_Document
    {
        /**
         * Get document properties.
         *
         * Retrieve the document properties.
         *
         * @access public
         * @static
         *
         * @return array Document properties.
         */
        public static function get_properties()
        {
        }
        public static function get_type()
        {
        }
        /**
         * Get document title.
         *
         * Retrieve the document title.
         *
         * @access public
         * @static
         *
         * @return string Document title.
         */
        public static function get_title()
        {
        }
        public function save_template_type()
        {
        }
        public function print_admin_column_type()
        {
        }
        public function filter_admin_row_actions($actions)
        {
        }
    }
    /**
     * Elementor section library document.
     *
     * Elementor section library document handler class is responsible for
     * handling a document of a section type.
     *
     * @since 2.0.0
     */
    class Section extends \Elementor\Modules\Library\Documents\Library_Document
    {
        public static function get_properties()
        {
        }
        public static function get_type()
        {
        }
        /**
         * Get document title.
         *
         * Retrieve the document title.
         *
         * @since 2.0.0
         * @access public
         * @static
         *
         * @return string Document title.
         */
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
    }
    /**
     * Elementor page library document.
     *
     * Elementor page library document handler class is responsible for
     * handling a document of a page type.
     *
     * @since 2.0.0
     */
    class Page extends \Elementor\Modules\Library\Documents\Library_Document
    {
        /**
         * Get document properties.
         *
         * Retrieve the document properties.
         *
         * @since 2.0.0
         * @access public
         * @static
         *
         * @return array Document properties.
         */
        public static function get_properties()
        {
        }
        public static function get_type()
        {
        }
        /**
         * Get document title.
         *
         * Retrieve the document title.
         *
         * @since 2.0.0
         * @access public
         * @static
         *
         * @return string Document title.
         */
        public static function get_title()
        {
        }
        public static function get_plural_title()
        {
        }
        public static function get_add_new_title()
        {
        }
        /**
         * @since 2.1.3
         * @access public
         */
        public function get_css_wrapper_selector()
        {
        }
        /**
         * @since 3.1.0
         * @access protected
         */
        protected function register_controls()
        {
        }
        protected function get_remote_library_config()
        {
        }
    }
}
namespace Elementor\Modules\ElementsColorPicker {
    class Module extends \Elementor\Core\Base\Module
    {
        /**
         * Retrieve the module name.
         *
         * @return string
         */
        public function get_name()
        {
        }
        /**
         * Enqueue the `Color-Thief` library to pick colors from images.
         *
         * @return void
         */
        public function enqueue_scripts()
        {
        }
        /**
         * Module constructor - Initialize the Eye-Dropper module.
         *
         * @return void
         */
        public function __construct()
        {
        }
    }
}
namespace Elementor\Modules\SiteNavigation\Rest_Fields {
    class Page_User_Can
    {
        public function register_rest_field()
        {
        }
        public function get_callback($post)
        {
        }
    }
}
namespace Elementor\Modules\SiteNavigation {
    class Module extends \Elementor\Core\Base\Module
    {
        const PAGES_PANEL_EXPERIMENT_NAME = 'pages_panel';
        /**
         * Initialize the Site navigation module.
         * @return void
         * @throws \Exception
         */
        public function __construct()
        {
        }
        /**
         * Retrieve the module name.
         *
         * @return string
         */
        public function get_name()
        {
        }
    }
}
namespace Elementor\Modules\SiteNavigation\Data {
    class Controller extends \Elementor\Data\V2\Base\Controller
    {
        public function get_name()
        {
        }
        public function get_items_permissions_check($request)
        {
        }
        public function create_items_permissions_check($request) : bool
        {
        }
        public function get_item_permissions_check($request)
        {
        }
        public function create_item_permissions_check($request) : bool
        {
        }
        public function register_endpoints()
        {
        }
        protected function register_index_endpoint()
        {
        }
    }
}
namespace Elementor\Modules\SiteNavigation\Data\Endpoints {
    class Duplicate_Post extends \Elementor\Data\V2\Base\Endpoint
    {
        protected function register()
        {
        }
        public function get_name()
        {
        }
        public function get_format()
        {
        }
        public function create_items($request)
        {
        }
    }
    class Recent_Posts extends \Elementor\Data\V2\Base\Endpoint
    {
        public function register_items_route($methods = \WP_REST_Server::READABLE, $args = [])
        {
        }
        public function get_name()
        {
        }
        public function get_format()
        {
        }
        public function get_items($request)
        {
        }
    }
    class Add_New_Post extends \Elementor\Data\V2\Base\Endpoint
    {
        protected function register()
        {
        }
        public function get_name()
        {
        }
        public function get_format()
        {
        }
        public function create_items($request)
        {
        }
    }
    class Homepage extends \Elementor\Data\V2\Base\Endpoint
    {
        public function get_permission_callback($request)
        {
        }
        public function get_name()
        {
        }
        public function get_format()
        {
        }
        public function get_items($request)
        {
        }
    }
}
namespace Elementor\Modules\AdminTopBar {
    class Module extends \Elementor\Core\Base\App
    {
        /**
         * @return bool
         */
        public static function is_active()
        {
        }
        /**
         * @return string
         */
        public function get_name()
        {
        }
        /**
         * Module constructor.
         */
        public function __construct()
        {
        }
    }
}
namespace Elementor\Modules\LinkInBio\Classes\Render {
    /**
     * Class Render_Base.
     *
     * This is the base class that will hold shared functionality that will be needed by all the various widget versions.
     *
     * @since 3.23.0
     */
    abstract class Render_Base
    {
        use \Elementor\Core\Base\Traits\Shared_Widget_Controls_Trait;
        protected \Elementor\Modules\LinkInBio\Base\Widget_Link_In_Bio_Base $widget;
        protected array $settings;
        public abstract function render() : void;
        public function __construct(\Elementor\Modules\LinkInBio\Base\Widget_Link_In_Bio_Base $widget)
        {
        }
        protected function render_image_links() : void
        {
        }
        protected function render_ctas() : void
        {
        }
        protected function render_icons() : void
        {
        }
        protected function render_bio() : void
        {
        }
        protected function render_footer_bio() : void
        {
        }
        protected function render_identity_image() : void
        {
        }
        protected function get_image_classnames(array $image) : string
        {
        }
        protected function get_formatted_link_based_on_type_for_cta(array $cta) : string
        {
        }
        protected function get_formatted_link_for_icon(array $icon) : string
        {
        }
        protected function build_layout_render_attribute() : void
        {
        }
    }
    /**
     * Class Core_Render.
     *
     * This class handles the rendering of the Link In Bio widget for the core version.
     *
     * @since 3.23.0
     */
    class Core_Render extends \Elementor\Modules\LinkInBio\Classes\Render\Render_Base
    {
        public function render() : void
        {
        }
    }
}
namespace Elementor\Modules\LinkInBio {
    class Module extends \Elementor\Core\Base\Module
    {
        const EXPERIMENT_NAME = 'link-in-bio';
        const WIDGET_HAS_CUSTOM_BREAKPOINTS = true;
        public function get_name() : string
        {
        }
        public function get_widgets() : array
        {
        }
        // TODO: This is a hidden experiment which needs to remain enabled like this until 3.26 for pro compatibility.
        public static function get_experimental_data()
        {
        }
        public function __construct()
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/link-in-bio/assets/scss/widgets/*.scss`
         * to `/assets/css/widget-*.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace Elementor\Modules\LinkInBio\Base {
    abstract class Widget_Link_In_Bio_Base extends \Elementor\Widget_Base
    {
        use \Elementor\Core\Base\Traits\Shared_Widget_Controls_Trait;
        public function get_group_name() : string
        {
        }
        public function get_style_depends() : array
        {
        }
        public static function get_configuration()
        {
        }
        public function get_description_position()
        {
        }
        public function get_icon() : string
        {
        }
        public function get_categories() : array
        {
        }
        public function get_keywords() : array
        {
        }
        public function get_image_position_options() : array
        {
        }
        protected function register_controls() : void
        {
        }
        protected function render() : void
        {
        }
        protected function add_image_links_controls()
        {
        }
        protected function add_cta_controls()
        {
        }
        protected function add_icons_controls() : void
        {
        }
        protected function get_icon_title_field() : string
        {
        }
        protected function add_style_tab() : void
        {
        }
        protected function add_bio_section() : void
        {
        }
        protected function add_identity_section() : void
        {
        }
        protected function add_style_image_links_controls() : void
        {
        }
        protected function add_style_cta_section() : void
        {
        }
        protected function add_style_identity_controls() : void
        {
        }
        protected function add_content_tab() : void
        {
        }
        protected function add_style_bio_controls() : void
        {
        }
        protected function add_style_icons_controls() : void
        {
        }
        protected function add_style_background_controls() : void
        {
        }
        protected function add_identity_image_profile_controls(array $condition) : void
        {
        }
        protected function add_identity_image_cover_control(array $condition) : void
        {
        }
    }
}
namespace Elementor\Modules\LinkInBio\Widgets {
    /**
     * Elementor Link in Bio widget.
     *
     * Elementor widget that displays an image, a bio, up to 4 CTA links and up to 5 icons.
     *
     * @since 3.23.0
     */
    class Link_In_Bio extends \Elementor\Modules\LinkInBio\Base\Widget_Link_In_Bio_Base
    {
        public function get_name() : string
        {
        }
        public function get_title() : string
        {
        }
    }
}
namespace Elementor\Modules\AdminBar {
    class Module extends \Elementor\Core\Base\App
    {
        /**
         * @return bool
         */
        public static function is_active()
        {
        }
        /**
         * @return string
         */
        public function get_name()
        {
        }
        /**
         * Collect the documents that was rendered in the current page.
         *
         * @param Document $document
         * @param $is_excerpt
         */
        public function add_document_to_admin_bar(\Elementor\Core\Base\Document $document, $is_excerpt)
        {
        }
        /**
         * Scripts for module.
         */
        public function enqueue_scripts()
        {
        }
        /**
         * Creates admin bar menu items config.
         *
         * @return array
         */
        public function get_init_settings()
        {
        }
        /**
         * Module constructor.
         */
        public function __construct()
        {
        }
    }
}
namespace Elementor\Modules\Styleguide\Controls {
    class Switcher extends \Elementor\Control_Switcher
    {
        const CONTROL_TYPE = 'global-style-switcher';
        /**
         * Get control type.
         *
         * Retrieve the control type, in this case `global-style-switcher`.
         *
         * @since 3.13.0
         * @access public
         *
         * @return string Control type.
         */
        public function get_type()
        {
        }
    }
}
namespace Elementor\Modules\Styleguide {
    class Module extends \Elementor\Core\Base\Module
    {
        const ASSETS_HANDLE = 'elementor-styleguide';
        const ASSETS_SRC = 'styleguide';
        /**
         * Initialize the Container-Converter module.
         *
         * @return void
         */
        public function __construct()
        {
        }
        /**
         * Retrieve the module name.
         *
         * @return string
         */
        public function get_name()
        {
        }
        /**
         * Enqueue scripts.
         *
         * @return void
         */
        public function enqueue_main_scripts()
        {
        }
        public function enqueue_app_initiator($is_preview = false)
        {
        }
        public function enqueue_styles()
        {
        }
        public function register_controls()
        {
        }
        /**
         * Add the Enable Styleguide Preview controls to Global Colors and Global Fonts.
         *
         * @param $element
         * @param string $section_id
         * @param array $args
         */
        public function add_styleguide_enable_controls($element, $section_id, $args)
        {
        }
    }
}
namespace Elementor\Modules\PageTemplates {
    /**
     * Elementor page templates module.
     *
     * Elementor page templates module handler class is responsible for registering
     * and managing Elementor page templates modules.
     *
     * @since 2.0.0
     */
    class Module extends \Elementor\Core\Base\Module
    {
        /**
         * The of the theme.
         */
        const TEMPLATE_THEME = 'elementor_theme';
        /**
         * Elementor Canvas template name.
         */
        const TEMPLATE_CANVAS = 'elementor_canvas';
        /**
         * Elementor Header & Footer template name.
         */
        const TEMPLATE_HEADER_FOOTER = 'elementor_header_footer';
        /**
         * Print callback.
         *
         * Holds the page template callback content.
         *
         * @since 2.0.0
         * @access protected
         *
         * @var callable
         */
        protected $print_callback;
        /**
         * Get module name.
         *
         * Retrieve the page templates module name.
         *
         * @since 2.0.0
         * @access public
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
        /**
         * Template include.
         *
         * Update the path for the Elementor Canvas template.
         *
         * Fired by `template_include` filter.
         *
         * @since 2.0.0
         * @access public
         *
         * @param string $template The path of the template to include.
         *
         * @return string The path of the template to include.
         */
        public function template_include($template)
        {
        }
        /**
         * Add WordPress templates.
         *
         * Adds Elementor templates to all the post types that support
         * Elementor.
         *
         * Fired by `init` action.
         *
         * @since 2.0.0
         * @access public
         */
        public function add_wp_templates_support()
        {
        }
        /**
         * Add page templates.
         *
         * Add the Elementor page templates to the theme templates.
         *
         * Fired by `theme_{$post_type}_templates` filter.
         *
         * @since 2.0.0
         * @access public
         * @static
         *
         * @param array $page_templates Array of page templates. Keys are filenames,
         *                              checks are translated names.
         *
         * @param \WP_Theme $wp_theme
         * @param \WP_Post $post
         *
         * @return array Page templates.
         */
        public function add_page_templates($page_templates, $wp_theme, $post)
        {
        }
        /**
         * Set print callback.
         *
         * Set the page template callback.
         *
         * @since 2.0.0
         * @access public
         *
         * @param callable $callback
         */
        public function set_print_callback($callback)
        {
        }
        /**
         * Print callback.
         *
         * Prints the page template content using WordPress loop.
         *
         * @since 2.0.0
         * @access public
         */
        public function print_callback()
        {
        }
        /**
         * Print content.
         *
         * Prints the page template content.
         *
         * @since 2.0.0
         * @access public
         */
        public function print_content()
        {
        }
        /**
         * Get page template path.
         *
         * Retrieve the path for any given page template.
         *
         * @since 2.0.0
         * @access public
         *
         * @param string $page_template The page template name.
         *
         * @return string Page template path.
         */
        public function get_template_path($page_template)
        {
        }
        /**
         * Register template control.
         *
         * Adds custom controls to any given document.
         *
         * Fired by `update_post_metadata` action.
         *
         * @since 2.0.0
         * @access public
         *
         * @param Document $document The document instance.
         */
        public function action_register_template_control($document)
        {
        }
        /**
         * Register template control.
         *
         * Adds custom controls to any given document.
         *
         * @since 2.0.0
         * @access public
         *
         * @param Document $document   The document instance.
         * @param string   $control_id Optional. The control ID. Default is `template`.
         */
        public function register_template_control($document, $control_id = 'template')
        {
        }
        // The $options variable is an array of $control_options to overwrite the default
        public function add_template_controls(\Elementor\Core\Base\Document $document, $control_id, $control_options)
        {
        }
        /**
         * Filter metadata update.
         *
         * Filters whether to update metadata of a specific type.
         *
         * Elementor don't allow WordPress to update the parent page template
         * during `wp_update_post`.
         *
         * Fired by `update_{$meta_type}_metadata` filter.
         *
         * @since 2.0.0
         * @access public
         *
         * @param bool   $check     Whether to allow updating metadata for the given type.
         * @param int    $object_id Object ID.
         * @param string $meta_key  Meta key.
         *
         * @return bool Whether to allow updating metadata of a specific type.
         */
        public function filter_update_meta($check, $object_id, $meta_key)
        {
        }
        /**
         * Page templates module constructor.
         *
         * Initializing Elementor page templates module.
         *
         * @since 2.0.0
         * @access public
         */
        public function __construct()
        {
        }
    }
}
namespace Elementor\Modules\Notes {
    class Module extends \Elementor\Core\Base\Module
    {
        public function get_name()
        {
        }
        /**
         * Enqueue the module scripts.
         *
         * @return void
         */
        public function enqueue_scripts()
        {
        }
        /**
         * Enqueue the module styles.
         *
         * @return void
         */
        public function enqueue_styles()
        {
        }
        /**
         * @return bool
         */
        public static function is_active()
        {
        }
        /**
         * Initialize the Notes module.
         *
         * @return void
         */
        public function __construct()
        {
        }
    }
}
namespace Elementor\Modules\CompatibilityTag {
    abstract class Base_Module extends \Elementor\Core\Base\Module
    {
        const MODULE_NAME = 'compatibility-tag';
        /**
         * @return string
         */
        public function get_name()
        {
        }
        /**
         * Add allowed headers to plugins.
         *
         * @param array $headers
         * @param       $compatibility_tag_header
         *
         * @return array
         */
        protected function enable_elementor_headers(array $headers, $compatibility_tag_header)
        {
        }
        /**
         * @return Collection
         */
        protected function get_plugins_to_check()
        {
        }
        /**
         * Append a compatibility message to the update plugin warning.
         *
         * @param array $args
         *
         * @throws \Exception
         */
        protected function on_plugin_update_message(array $args)
        {
        }
        /**
         * @return string
         */
        protected abstract function get_plugin_header();
        /**
         * @return string
         */
        protected abstract function get_plugin_label();
        /**
         * @return string
         */
        protected abstract function get_plugin_name();
        /**
         * @return string
         */
        protected abstract function get_plugin_version();
        /**
         * Base_Module constructor.
         *
         * @throws \Exception
         */
        public function __construct()
        {
        }
    }
    /**
     * Inspired By WooCommerce.
     *
     * @link  https://github.com/woocommerce/woocommerce/blob/master/includes/admin/plugin-updates/class-wc-plugin-updates.php
     */
    class Module extends \Elementor\Modules\CompatibilityTag\Base_Module
    {
        /**
         * This is the header used by extensions to show testing.
         *
         * @var string
         */
        const PLUGIN_VERSION_TESTED_HEADER = 'Elementor tested up to';
        /**
         * @return string
         */
        protected function get_plugin_header()
        {
        }
        /**
         * @return string
         */
        protected function get_plugin_label()
        {
        }
        /**
         * @return string
         */
        protected function get_plugin_name()
        {
        }
        /**
         * @return string
         */
        protected function get_plugin_version()
        {
        }
        /**
         * @return Collection
         */
        protected function get_plugins_to_check()
        {
        }
    }
    class Compatibility_Tag extends \Elementor\Core\Base\Base_Object
    {
        const PLUGIN_NOT_EXISTS = 'plugin_not_exists';
        const HEADER_NOT_EXISTS = 'header_not_exists';
        const INVALID_VERSION = 'invalid_version';
        const INCOMPATIBLE = 'incompatible';
        const COMPATIBLE = 'compatible';
        /**
         * Compatibility_Tag constructor.
         *
         * @param string $header
         */
        public function __construct($header)
        {
        }
        /**
         * Return if plugins is compatible or not.
         *
         * @param Version $version
         * @param array   $plugins_names
         *
         * @return array
         * @throws \Exception
         */
        public function check(\Elementor\Core\Utils\Version $version, array $plugins_names)
        {
        }
    }
    class Compatibility_Tag_Report extends \Elementor\Modules\System_Info\Reporters\Base
    {
        /**
         * @var Compatibility_Tag
         */
        protected $compatibility_tag_service;
        /**
         * @var Version
         */
        protected $plugin_version;
        /**
         * @var string
         */
        protected $plugin_label;
        /**
         * @var array
         */
        protected $plugins_to_check;
        /**
         * Compatibility_Tag_Report constructor.
         *
         * @param $properties
         */
        public function __construct($properties)
        {
        }
        /**
         * The title of the report
         *
         * @return string
         */
        public function get_title()
        {
        }
        /**
         * Report fields
         *
         * @return string[]
         */
        public function get_fields()
        {
        }
        /**
         * Report data.
         *
         * @return string[]
         * @throws \Exception
         */
        public function get_report_data()
        {
        }
        public function get_html_report_data()
        {
        }
        public function get_raw_report_data()
        {
        }
    }
}
namespace Elementor\Modules\GeneratorTag {
    class Module extends \Elementor\Core\Base\Module
    {
        public function get_name()
        {
        }
        public function __construct()
        {
        }
        public function render_generator_tag()
        {
        }
        public function register_admin_settings(\Elementor\Settings $settings)
        {
        }
    }
}
namespace Elementor\Modules\History {
    /**
     * Elementor history module.
     *
     * Elementor history module handler class is responsible for registering and
     * managing Elementor history modules.
     *
     * @since 1.7.0
     */
    class Module extends \Elementor\Core\Base\Module
    {
        /**
         * Get module name.
         *
         * Retrieve the history module name.
         *
         * @since 1.7.0
         * @access public
         *
         * @return string Module name.
         */
        public function get_name()
        {
        }
        /**
         * @since 2.3.0
         * @access public
         */
        public function add_templates()
        {
        }
        /**
         * History module constructor.
         *
         * Initializing Elementor history module.
         *
         * @since 1.7.0
         * @access public
         */
        public function __construct()
        {
        }
    }
    /**
     * Elementor history revisions manager.
     *
     * Elementor history revisions manager handler class is responsible for
     * registering and managing Elementor revisions manager.
     *
     * @since 1.7.0
     */
    class Revisions_Manager
    {
        /**
         * Maximum number of revisions to display.
         */
        const MAX_REVISIONS_TO_DISPLAY = 50;
        /**
         * History revisions manager constructor.
         *
         * Initializing Elementor history revisions manager.
         *
         * @since 1.7.0
         * @access public
         */
        public function __construct()
        {
        }
        /**
         * @since 1.7.0
         * @access public
         * @static
         */
        public static function handle_revision()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         * @static
         *
         * @param $post_content
         * @param $post_id
         *
         * @return string
         */
        public static function avoid_delete_auto_save($post_content, $post_id)
        {
        }
        /**
         * @since 2.0.0
         * @access public
         * @static
         */
        public static function remove_temp_post_content()
        {
        }
        /**
         * @since 1.7.0
         * @access public
         * @static
         *
         * @param int   $post_id
         * @param array $query_args
         * @param bool  $parse_result
         *
         * @return array
         */
        public static function get_revisions($post_id = 0, $query_args = [], $parse_result = true)
        {
        }
        /**
         * @since 1.9.2
         * @access public
         * @static
         */
        public static function update_autosave($autosave_data)
        {
        }
        /**
         * @since 1.7.0
         * @access public
         * @static
         */
        public static function save_revision($revision_id)
        {
        }
        /**
         * @since 1.7.0
         * @access public
         * @static
         */
        public static function restore_revision($parent_id, $revision_id)
        {
        }
        /**
         * @since 2.3.0
         * @access public
         * @static
         *
         * @param $data
         *
         * @return array
         * @throws \Exception
         */
        public static function ajax_get_revision_data(array $data)
        {
        }
        /**
         * @since 1.7.0
         * @access public
         * @static
         */
        public static function add_revision_support_for_all_post_types()
        {
        }
        /**
         * @since 2.0.0
         * @access public
         * @static
         * @param array $return_data
         * @param Document $document
         *
         * @return array
         */
        public static function on_ajax_save_builder_data($return_data, $document)
        {
        }
        /**
         * @since 1.7.0
         * @access public
         * @static
         */
        public static function db_before_save($status, $has_changes)
        {
        }
        public static function document_config($settings, $post_id)
        {
        }
        /**
         * Localize settings.
         *
         * Add new localized settings for the revisions manager.
         *
         * Fired by `elementor/editor/editor_settings` filter.
         *
         * @since 1.7.0
         * @deprecated 3.1.0
         * @access public
         * @static
         */
        public static function editor_settings()
        {
        }
        /**
         * @throws \Exception
         */
        public static function ajax_get_revisions($data)
        {
        }
        /**
         * @since 2.3.0
         * @access public
         * @static
         */
        public static function register_ajax_actions(\Elementor\Core\Common\Modules\Ajax\Module $ajax)
        {
        }
    }
}
namespace Elementor\Modules\NestedTabs {
    class Module extends \Elementor\Core\Base\Module
    {
        public static function is_active()
        {
        }
        public function get_name()
        {
        }
        public function __construct()
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/nested-tabs/assets/scss/frontend.scss`
         * to `/assets/css/widget-nested-tabs.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace Elementor\Modules\NestedElements\Base {
    /**
     * Used to create a new widget that can be nested inside other widgets.
     */
    abstract class Widget_Nested_Base extends \Elementor\Widget_Base
    {
        /**
         * Get default children elements structure.
         *
         * @return array
         */
        protected abstract function get_default_children_elements();
        /**
         * Get repeater title setting key name.
         *
         * @return string
         */
        protected abstract function get_default_repeater_title_setting_key();
        /**
         * Get default children title for the navigator, using `%d` as index in the format.
         *
         * @note The title in this method is used to set the default title for each created child in nested element.
         * for handling the children title for new created widget(s), use `get_default_children_elements()` method,
         * eg:
         * [
         *      'elType' => 'container',
         *      'settings' => [
         *          '_title' => __( 'Tab #1', 'elementor' ),
         *      ],
         * ],
         * @return string
         */
        protected function get_default_children_title()
        {
        }
        /**
         * Get default children placeholder selector, Empty string, means will be added at the end view.
         *
         * @return string
         */
        protected function get_default_children_placeholder_selector()
        {
        }
        protected function get_default_children_container_placeholder_selector()
        {
        }
        protected function is_dynamic_content() : bool
        {
        }
        /**
         * @inheritDoc
         *
         * To support nesting.
         */
        protected function _get_default_child_type(array $element_data)
        {
        }
        /**
         * @inheritDoc
         *
         * Adding new 'defaults' config for handling children elements.
         */
        protected function get_initial_config()
        {
        }
        /**
         * @inheritDoc
         *
         * Each element including its children elements.
         */
        public function get_raw_data($with_html_content = false)
        {
        }
        /**
         * Print child, helper method to print the child element.
         *
         * @param int $index
         */
        public function print_child($index)
        {
        }
        protected function content_template_single_repeater_item()
        {
        }
        public function print_template()
        {
        }
    }
}
namespace Elementor\Modules\NestedTabs\Widgets {
    class NestedTabs extends \Elementor\Modules\NestedElements\Base\Widget_Nested_Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function get_style_depends() : array
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        public function show_in_panel() : bool
        {
        }
        protected function tab_content_container(int $index)
        {
        }
        protected function get_default_children_elements()
        {
        }
        protected function get_default_repeater_title_setting_key()
        {
        }
        protected function get_default_children_title()
        {
        }
        protected function get_default_children_placeholder_selector()
        {
        }
        protected function get_html_wrapper_class()
        {
        }
        protected function register_controls()
        {
        }
        protected function render_tab_titles_html($item_settings) : void
        {
        }
        protected function maybe_render_tab_icons_html($item_settings) : void
        {
        }
        protected function render_tab_containers_html($settings) : void
        {
        }
        /**
         * Print the content area.
         *
         * @param int $index
         * @param array $item_settings
         */
        public function print_child($index, $item_settings = [])
        {
        }
        protected function add_attributes_to_container($container, $item_settings)
        {
        }
        protected function render()
        {
        }
        protected function get_initial_config() : array
        {
        }
        protected function content_template_single_repeater_item()
        {
        }
        protected function content_template()
        {
        }
    }
}
namespace Elementor\Modules\NestedAccordion {
    class Module extends \Elementor\Core\Base\Module
    {
        public static function is_active()
        {
        }
        public function get_name()
        {
        }
        public function __construct()
        {
        }
        /**
         * Register styles.
         *
         * At build time, Elementor compiles `/modules/nested-accordion/assets/scss/frontend.scss`
         * to `/assets/css/widget-nested-accordion.min.css`.
         *
         * @return void
         */
        public function register_styles()
        {
        }
    }
}
namespace Elementor\Modules\NestedAccordion\Widgets {
    /**
     * Elementor Nested Accordion widget.
     *
     * Elementor widget that displays a collapsible display of content in an
     * accordion style.
     *
     * @since 3.15.0
     */
    class Nested_Accordion extends \Elementor\Modules\NestedElements\Base\Widget_Nested_Base
    {
        public function get_name()
        {
        }
        public function get_title()
        {
        }
        public function get_icon()
        {
        }
        public function get_keywords()
        {
        }
        public function get_style_depends() : array
        {
        }
        public function show_in_panel() : bool
        {
        }
        public function has_widget_inner_wrapper() : bool
        {
        }
        protected function item_content_container(int $index)
        {
        }
        protected function get_default_children_elements()
        {
        }
        protected function get_default_repeater_title_setting_key()
        {
        }
        protected function get_default_children_title()
        {
        }
        protected function get_default_children_placeholder_selector()
        {
        }
        protected function get_default_children_container_placeholder_selector()
        {
        }
        protected function get_html_wrapper_class()
        {
        }
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function print_child($index, $item_id = null)
        {
        }
        protected function add_attributes_to_container($container, $item_id)
        {
        }
        protected function get_initial_config() : array
        {
        }
        protected function content_template_single_repeater_item()
        {
        }
        protected function content_template()
        {
        }
    }
}
namespace Elementor\Modules\GlobalClasses {
    class Global_Classes_Injector
    {
        public function register_hooks()
        {
        }
    }
    class Global_Classes implements \JsonSerializable
    {
        public static function make(array $items = [], array $order = [])
        {
        }
        public function get_items()
        {
        }
        public function get_order()
        {
        }
        public function get()
        {
        }
        #[\ReturnTypeWillChange]
        public function jsonSerialize()
        {
        }
    }
    class Global_Classes_Repository
    {
        const META_KEY = '_elementor_global_classes';
        public static function make() : \Elementor\Modules\GlobalClasses\Global_Classes_Repository
        {
        }
        public function all()
        {
        }
        public function get(string $id)
        {
        }
        public function delete(string $id)
        {
        }
        public function put(string $id, array $value)
        {
        }
        public function create(array $value)
        {
        }
        public function arrange(array $value)
        {
        }
    }
    class Global_Classes_REST_API
    {
        const API_NAMESPACE = 'elementor/v1';
        const API_BASE = 'global-classes';
        public function register_hooks()
        {
        }
    }
    class Module extends \Elementor\Core\Base\Module
    {
        const NAME = 'global_classes';
        // TODO: Add global classes package
        const PACKAGES = [];
        public function get_name()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace Elementor\Modules\NestedElements\Controls {
    /**
     * Changing the default repeater control behavior for custom item title defaults.
     * For custom management of nested repeater controls.
     */
    class Control_Nested_Repeater extends \Elementor\Control_Repeater
    {
        const CONTROL_TYPE = 'nested-elements-repeater';
        public function get_type()
        {
        }
    }
}
namespace Elementor\Modules\NestedElements {
    class Module extends \Elementor\Core\Base\Module
    {
        const EXPERIMENT_NAME = 'nested-elements';
        public static function get_experimental_data()
        {
        }
        public function get_name()
        {
        }
        public function __construct()
        {
        }
    }
}
namespace {
    \define('ELEMENTOR_VERSION', '3.27.7');
    \define('ELEMENTOR__FILE__', __FILE__);
    \define('ELEMENTOR_PLUGIN_BASE', \plugin_basename(\ELEMENTOR__FILE__));
    \define('ELEMENTOR_PATH', \plugin_dir_path(\ELEMENTOR__FILE__));
    \define('ELEMENTOR_URL', 'file://' . \ELEMENTOR_PATH);
    \define('ELEMENTOR_MODULES_PATH', \plugin_dir_path(\ELEMENTOR__FILE__) . '/modules');
    \define('ELEMENTOR_ASSETS_PATH', \ELEMENTOR_PATH . 'assets/');
    \define('ELEMENTOR_ASSETS_URL', \ELEMENTOR_URL . 'assets/');
}
