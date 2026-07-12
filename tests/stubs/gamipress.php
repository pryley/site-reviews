<?php

namespace {
    /**
     * Retrieve object meta field for an object.
     *
     * @since 1.0.0
     *
     * @param int    $object_id Post ID.
     * @param string $meta_key     Optional. The meta key to retrieve. By default, returns
     *                        data for all keys. Default empty.
     * @param bool   $single  Optional. Whether to return a single value. Default false.
     * @return mixed Will be an array if $single is false. Will be value of meta data
     *               field if $single is true.
     */
    function ct_get_object_meta($object_id, $meta_key = '', $single = false)
    {}
    /**
     * Get registered achievement type slugs
     *
     * @since  1.0.0
     *
     * @return array An array of all our registered achievement type slugs (empty array if none)
     */
    function gamipress_get_achievement_types_slugs()
    {}
    /**
     * Get registered rank type slugs
     *
     * @since  1.3.1
     *
     * @return array An array of all our registered rank type slugs (empty array if none)
     */
    function gamipress_get_rank_types_slugs()
    {}
    /**
     * Get GamiPress Requirement Type Slugs
     *
     * @since  1.0.5
     *
     * @return array An array of all our registered requirement type slugs (empty array if none)
     */
    function gamipress_get_requirement_types_slugs()
    {}
    /**
     * Handle each of our activity triggers
     *
     * If method is called directly, pass an array of arguments with next items:
     * array(
     *  'event'         => 'gamipress_login',
     *  'user_id'       => 1,
     *  'specific_id'   => 100 // Just if is an specific trigger
     * )
     *
     * @since   1.0.0
     * @updated 1.4.3 Added the ability to be called directly
     *
     * @return mixed    Returns an array will all achievements awarded or false if none has been awarded
     */
    function gamipress_trigger_event(...$args)
    {}
}
