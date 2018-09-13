<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use WP_Error;

class Notice
{
	/**
	 * @param string $type
	 * @param string|array|WP_Error $message
	 * @return void
	 */
	public function add( $type, $message, array $args = [] )
	{
		if( empty( array_filter( [$message, $type] )))return;
		$args['message'] = $message;
		$args['type'] = $type;
		add_settings_error( Application::ID, '', json_encode( $this->normalize( $args )));
	}

	/**
	 * @param string|array|WP_Error $message
	 * @return void
	 */
	public function addError( $message, array $args = [] )
	{
		$this->add( 'error', $message, $args );
	}

	/**
	 * @param string|array|WP_Error $message
	 * @return void
	 */
	public function addSuccess( $message, array $args = [] )
	{
		$this->add( 'success', $message, $args );
	}

	/**
	 * @param string|array|WP_Error $message
	 * @return void
	 */
	public function addWarning( $message, array $args = [] )
	{
		$this->add( 'warning', $message, $args );
	}

	/**
	 * @return static
	 */
	public function clear()
	{
		global $wp_settings_errors;
		$wp_settings_errors = [];
		delete_transient( 'settings_errors' );
		return $this;
	}

	/**
	 * @return string
	 */
	public function get()
	{
		$notices = array_map( 'unserialize',
			array_unique( array_map( 'serialize', get_settings_errors( Application::ID )))
		);
		if( empty( $notices ))return;
		return array_reduce( $notices, function( $carry, $notice ) {
			return $carry.$this->buildNotice( json_decode( $notice['message'], true ));
		});
	}

	/**
	 * @return string
	 */
	protected function buildNotice( array $args )
	{
		$messages = array_reduce( $args['messages'], function( $carry, $message ) {
			return $carry.glsr( Builder::class )->p( $message );
		});
		$class = 'notice notice-'.$args['type'];
		if( $args['inline'] ) {
			$class .= ' inline';
		}
		if( $args['dismissible'] ) {
			$class .= ' is-dismissible';
		}
		return glsr( Builder::class )->div( $messages, [
			'class' => $class,
		]);
	}

	/**
	 * @return array
	 */
	protected function normalize( array $args )
	{
		$defaults = [
			'dismissible' => true,
			'inline' => true,
			'message' => '',
			'type' => '',
		];
		$args = shortcode_atts( $defaults, $args );
		if( !in_array( $args['type'], ['error', 'warning', 'success'] )) {
			$args['type'] = 'success';
		}
		$args['messages'] = is_wp_error( $args['message'] )
			? (array)$args['message']->get_error_message()
			: (array)$args['message'];
		unset( $args['message'] );
		return $args;
	}
}
