<?php

namespace GeminiLabs\SiteReviews\Blocks;

use GeminiLabs\SiteReviews\Application;

abstract class BlockGenerator
{
	/**
	 * @return array
	 */
	public function attributes()
	{
		return [];
	}

	/**
	 * @return array
	 */
	public function normalize( array $attributes )
	{
		if( !isset( $attributes['assigned_to'] )) {
			return $attributes;
		}
		if( $attributes['assigned_to'] == 'post_id' ) {
			$attributes['assigned_to'] = $attributes['post_id'];
		}
		else if( $attributes['assigned_to'] == 'parent_id' ) {
			$attributes['assigned_to'] = wp_get_post_parent_id( $attributes['post_id'] );
		}
		return $attributes;
	}

	/**
	 * @return void
	 */
	public function register( $block )
	{
		if( !function_exists( 'register_block_type' ))return;
		register_block_type( Application::ID.'/'.$block, [
			'attributes' => $this->attributes(),
			'editor_script' => Application::ID.'/blocks',
			'editor_style' => Application::ID.'/blocks',
			'render_callback' => [$this, 'render'],
			'style' => Application::ID,
		]);
	}

	/**
	 * @return void
	 */
	abstract public function render( array $attributes );
}
