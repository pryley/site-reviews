<?php

namespace {
    /**
     * Main FusionBuilder Class.
     *
     * @since 1.0
     */
    class FusionBuilder
    {
    }
    /**
     * Builder Elements Class.
     *
     * @since 1.1.0
     */
    abstract class Fusion_Element
    {
    }
}
namespace {
    /**
     * Auto activate Avada Builder element. To be used by addon plugins.
     *
     * @since 1.0.4
     * @param string $shortcode Shortcode tag.
     */
    function fusion_builder_auto_activate_element( $shortcode )
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
    function fusion_builder_frontend_data( $class_name, $map, $context = '' )
    {
    }
    /**
     * Add an element to $fusion_builder_elements array.
     *
     * @param array $module The element we're loading.
     */
    function fusion_builder_map( $module )
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
    function fusion_builder_shortcodes_categories( $taxonomy, $empty_choice = false, $empty_choice_label = false, $max_cat = 0 )
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
