<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\RegisterGutenbergBlocks;
use GeminiLabs\SiteReviews\Controllers\Controller;
use GeminiLabs\SiteReviews\Helper;

class GutenbergController extends Controller
{
	/**
	 * @return array
	 * @filter block_categories
	 */
	public function filterBlockCategories( array $categories )
	{
		$categories[] = [
			'slug' => Application::ID,
			'title' => glsr()->name,
		];
		return $categories;
	}

	/**
	 * @return void
	 * @action init
	 */
	public function registerBlocks()
	{
		$blocks = [
			'form', 'reviews', 'summary',
		];
		foreach( $blocks as $block ) {
			$id = str_replace( '_reviews', '', Application::ID.'_'.$block );
			$blockClass = glsr( Helper::class )->buildClassName( $id.'-block', 'Blocks' );
			if( !class_exists( $blockClass )) {
				glsr_log()->error( sprintf( 'Class missing (%s)', $blockClass ));
				continue;
			}
			glsr( $blockClass )->register( $block );
		}
	}
}
