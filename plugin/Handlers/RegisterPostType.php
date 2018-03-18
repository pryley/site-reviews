<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Commands\RegisterPostType as Command;

class RegisterPostType
{
	/**
	 * @return void
	 */
	public function handle( Command $command )
	{
		if( in_array( $command->postType, get_post_types( ['_builtin' => true] )))return;
		register_post_type( $command->postType, $command->args );
		glsr()->postTypeColumns = wp_parse_args( glsr()->postTypeColumns, [
			$command->postType => $command->columns,
		]);
	}
}
