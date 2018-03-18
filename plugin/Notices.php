<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

class Notices
{
	/**
	 * Add a notice
	 *
	 * @param string       $type
	 * @param string|array|\WP_Error $message
	 */
	public function add( $type, $message, array $args = [] )
	{
		if( empty( $type ) || empty( $message ))return;
		global $wp_settings_errors;
		$defaults = [
			'dismissible' => true,
			'inline' => true,
			'messages' => [],
			'type' => '',
		];
		$args = (object) shortcode_atts( $defaults, $args );
		$args->messages = is_wp_error( $message )
			? (array) $message->get_error_message()
			: (array) $message;
		$args->type = in_array( $type, ['error','warning','success'] )
			? $type
			: 'success';
		$wp_settings_errors[] = [
			'setting' => glsr_app()->id,
			'code' => '',
			'message' => $args,
			'type' => ($args->type == 'error' ? 'error' : 'updated'),
		];
	}

	/**
	 * Add an error notice
	 *
	 * @param string|array|\WP_Error $message
	 */
	public function addError( $message, array $args = [] )
	{
		$this->add( 'error', $message, $args );
	}

	/**
	 * Add a success notice
	 *
	 * @param string|array $message
	 */
	public function addSuccess( $message, array $args = [] )
	{
		$this->add( 'success', $message, $args );
	}

	/**
	 * Add a warning notice
	 *
	 * @param string|array $message
	 */
	public function addWarning( $message, array $args = [] )
	{
		$this->add( 'warning', $message, $args );
	}

	/**
	 * Show all notices
	 *
	 * @param bool $sanitize
	 * @param bool $hide_on_update
	 */
	public function show( $print = true, $sanitize = false, $hide_on_update = false )
	{
		global $wp_settings_errors;

		$settings_updated = filter_input( INPUT_GET, 'settings-updated' );

		if( $hide_on_update && !empty( $settings_updated ))return;

		$settings_errors = get_settings_errors( glsr_app()->id, $sanitize );

		// make sure each notice is unique
		$unique_notices = array_map( 'unserialize', array_unique( array_map( 'serialize', $settings_errors )));

		if( empty( $unique_notices ))return;

		// Empty $wp_settings_errors in case ajax is being used (we don't want to keep old notices)
		$wp_settings_errors = [];
		$notices = [];

		foreach( $unique_notices as $key => $notice ) {
			if( is_string( $notice['message'] )) {
				$notices[] = $this->returnNotice( $notice['type'], true, true, $notice['message'] );
			}
			else {
				$notices[] = $this->returnNotice(
					$notice['message']->type,
					$notice['message']->inline,
					$notice['message']->dismissible,
					$notice['message']->messages
				);
			}
		}

		$notices = implode( '', $notices );

		if( $print ) {
			echo $notices;
		}
		else {
			return $notices;
		}
	}

	/**
	 * Print a notice to the page
	 *
	 * @param string $type
	 * @param bool   $inline
	 * @param bool   $dismissible
	 * @param mixed  $messages
	 */
	public function printNotice( $type, $inline, $dismissible, $messages )
	{
		global $wp_version;

		$type = $type == 'updated' ? 'success' : $type;

		// WP 4.0 support
		if( version_compare( $wp_version, '4.1', '<' )) {
			$type = $type == 'error' ? 'error error' : "$type updated";
		}

		if( is_string( $messages )) {
			$messages = (array) $messages;
		}

		array_walk( $messages, function( &$message ) {
			$value = is_wp_error( $message )
				? $message->get_error_message()
				: $message;
			$message = sprintf( '<p>%s</p>', $value );
		});

		printf( '<div class="notice notice-%s%s%s">%s</div>',
			$type,
			$inline ? ' inline' : '',
			$dismissible ? ' is-dismissible' : '',
			implode( '', $messages )
		);
	}

	/**
	 * Return a notice
	 *
	 * @param string $type
	 * @param bool   $inline
	 * @param bool   $dismissible
	 * @param mixed  $messages
	 */
	public function returnNotice( $type, $inline, $dismissible, $messages )
	{
		ob_start();
		$this->printNotice( $type, $inline, $dismissible, $messages );
		return ob_get_clean();
	}
}
