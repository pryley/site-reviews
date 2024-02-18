<?php
/**
 * Generated stub declarations for Elementor.
 * https://elementor.com
 * https://github.com/arifpavel/elementor-stubs
 */

namespace ElementorPro\Base {
    trait Base_Widget_Trait
    {
        public function is_editable()
        {
        }
        public function get_categories()
        {
        }
        public function get_widget_css_config($widget_name)
        {
        }
        public function get_responsive_widgets_config()
        {
        }
    }
    trait On_Import_Trait
    {
        /**
         * On import update dynamic content (e.g. post and term IDs).
         *
         * @since 3.8.0
         *
         * @param array              $element_config The config of the passed element.
         * @param array              $data           The data that requires updating/replacement when imported.
         * @param array|Element_Base $controls       The available controls.
         *
         * @return array
         */
        public static function on_import_update_dynamic_content(array $element_config, array $data, $controls = null) : array
        {
        }
        /**
         * Check if a control requires updating, and do so if needed.
         *
         * @param array $element_config
         * @param array $data
         * @param array $control
         * @param array $available_control_types
         *
         * @return array
         */
        private static function on_import_update_control(array $element_config, array $data, array $control, array $available_control_types) : array
        {
        }
        /**
         * Returns the data type that is required for updating.
         *
         * @param array $data
         * @param string $control_name
         *
         * @return array
         */
        private static function on_import_get_required_data(array $data, string $control_name) : array
        {
        }
        /**
         * Are the control values post IDs?
         *
         * @param string $control_name
         *
         * @return bool
         */
        private static function on_import_check_post_type(string $control_name) : bool
        {
        }
        /**
         * Update the value for the dynamic control.
         *
         * @param array $element_config
         * @param array $data
         * @param string $control_name
         * @param $current_value
         *
         * @return array
         */
        private static function on_import_update_control_value(array $element_config, array $data, string $control_name, $current_value) : array
        {
        }
    }
    abstract class Base_Widget extends \Elementor\Widget_Base
    {
        use \ElementorPro\Base\Base_Widget_Trait;
        use \ElementorPro\Base\On_Import_Trait;
    }
}
namespace ElementorPro\Modules\Woocommerce\Traits {
    trait Product_Id_Trait
    {
        public function get_product($product_id = false)
        {
        }
        public function get_product_variation($product_id = false)
        {
        }
    }
}
namespace ElementorPro\Modules\Woocommerce\Widgets {
    abstract class Base_Widget extends \ElementorPro\Base\Base_Widget
    {
        use \ElementorPro\Modules\Woocommerce\Traits\Product_Id_Trait;
        protected $gettext_modifications;
        public function get_categories()
        {
        }
        protected function get_devices_default_args()
        {
        }
        protected function add_columns_responsive_control()
        {
        }
        /**
         * Is WooCommerce Feature Active.
         *
         * Checks whether a specific WooCommerce feature is active. These checks can sometimes look at multiple WooCommerce
         * settings at once so this simplifies and centralizes the checking.
         *
         * @since 3.5.0
         *
         * @param string $feature
         * @return bool
         */
        protected function is_wc_feature_active($feature)
        {
        }
        /**
         * Get Custom Border Type Options
         *
         * Return a set of border options to be used in different WooCommerce widgets.
         *
         * This will be used in cases where the Group Border Control could not be used.
         *
         * @since 3.5.0
         *
         * @return array
         */
        public static function get_custom_border_type_options()
        {
        }
        /**
         * Init Gettext Modifications
         *
         * Should be overridden by a method in the Widget class.
         *
         * @since 3.5.0
         */
        protected function init_gettext_modifications()
        {
        }
        /**
         * Filter Gettext.
         *
         * Filter runs when text is output to the page using the translation functions (`_e()`, `__()`, etc.)
         * used to apply text changes from the widget settings.
         *
         * This allows us to make text changes without having to ovveride WooCommerce templates, which would
         * lead to dev tax to keep all the templates up to date with each future WC release.
         *
         * @since 3.5.0
         *
         * @param string $translation
         * @param string $text
         * @param string $domain
         * @return string
         */
        public function filter_gettext($translation, $text, $domain)
        {
        }
    }
}
namespace ElementorPro\Modules\Woocommerce\Widgets {
    class Product_Rating extends \ElementorPro\Modules\Woocommerce\Widgets\Base_Widget
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
        protected function register_controls()
        {
        }
        protected function render()
        {
        }
        public function render_plain_content()
        {
        }
        public function get_group_name()
        {
        }
    }
}
