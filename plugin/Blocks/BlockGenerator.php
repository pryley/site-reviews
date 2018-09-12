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
	 * @return void
	 */
	public function register( $block )
	{
		if( !function_exists( 'register_block_type' ))return;
		register_block_type( Application::ID.'/'.$block, [
			'attributes' => $this->attributes(),
			'render_callback' => [$this, 'render'],
			'script' => Application::ID.'/'.$block,
		]);
		wp_register_script(
			Application::ID.'/'.$block,
			glsr()->url( 'assets/scripts/block-'.$block.'.js' ),
			['wp-blocks', 'wp-i18n', 'wp-element'],
			glsr()->version
		);
	}

	/**
	 * @return void
	 */
	abstract public function render( array $attributes );
}
