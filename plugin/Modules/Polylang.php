<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Database\OptionManager;

class Polylang
{
	/**
	 * @param int|string $postId
	 * @return \WP_Post|void|null
	 */
	public function getPost( $postId )
	{
		$postId = trim( $postId );
		if( empty( $postId ) || !is_numeric( $postId ))return;
		if( $this->isEnabled() ) {
			$polylangPostId = pll_get_post( $postId, pll_get_post_language( get_the_ID() ));
		}
		if( !empty( $polylangPostId )) {
			$postId = $polylangPostId;
		}
		return get_post( intval( $postId ));
	}

	/**
	 * @return array
	 */
	public function getPostIds( array $postIds )
	{
		if( !$this->isEnabled() ) {
			return $postIds;
		}
		$newPostIds = [];
		foreach( $this->cleanIds( $postIds ) as $postId ) {
			$newPostIds = array_merge(
				$newPostIds,
				array_values( pll_get_post_translations( $postId ))
			);
		}
		return $this->cleanIds( $newPostIds );
	}

	/**
	 * @return bool
	 */
	public function isActive()
	{
		return function_exists( 'PLL' )
			&& function_exists( 'pll_get_post' )
			&& function_exists( 'pll_get_post_language' )
			&& function_exists( 'pll_get_post_translations' );
	}

	/**
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->isActive()
			&& glsr( OptionManager::class )->get( 'settings.general.support.polylang' ) == 'yes';
	}

	/**
	 * @return bool
	 */
	public function isSupported()
	{
		return defined( 'POLYLANG_VERSION' )
			&& version_compare( POLYLANG_VERSION, '2.3', '>=' );
	}

	/**
	 * @return array
	 */
	protected function cleanIds( array $postIds )
	{
		return array_filter( array_unique( $postIds ));
	}
}

