<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Database\QueryBuilder;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class Pagination implements PartialContract
{
	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @return void|string
	 */
	public function build( array $args = [] )
	{
		$this->args = $this->normalize( $args );
		if( $this->args['total'] < 2 )return;
		$links = $this->buildLinksForDeprecatedThemes();
		if( empty( $links )) {
			$links = $this->buildLinks();
		}
		$links = apply_filters( 'site-reviews/reviews/navigation_links', $links, $this->args );
		if( empty( $links ))return;
		return $this->buildTemplate( $links );
	}

	/**
	 * @return string
	 */
	protected function buildLinks()
	{
		$paginateArgs = [
			'before_page_number' => '<span class="meta-nav screen-reader-text">'.__( 'Page', 'site-reviews' ).' </span>',
			'current' => $this->args['paged'],
			'format' => '?'.Application::PAGED_QUERY_VAR.'=%#%',
			'mid_size' => 1,
			'next_text' => __( 'Next &rarr;', 'site-reviews' ),
			'prev_text' => __( '&larr; Previous', 'site-reviews' ),
			'total' => $this->args['total'],
		];
		if( is_front_page() ) {
			unset( $paginateArgs['format'] );
		}
		return paginate_links( $paginateArgs );
	}

	/**
	 * @return void|string
	 */
	protected function buildLinksForDeprecatedThemes()
	{
		$theme = wp_get_theme()->get( 'TextDomain' );
		if( !in_array( $theme, ['twentyten','twentyeleven','twentytwelve','twentythirteen'] ))return;
		$links = '';
		if( $this->args['paged'] > 1 ) {
			$links.= sprintf( '<div class="nav-previous"><a href="%s"><span class="meta-nav">&larr;</span> %s</a></div>',
				$this->buildUrlForDeprecatedThemes(-1),
				__( 'Previous', 'site-reviews' )
			);
		}
		if( $this->args['paged'] < $this->args['total'] ) {
			$links.= sprintf( '<div class="nav-next"><a href="%s">%s <span class="meta-nav">&rarr;</span></a></div>',
				$this->buildUrlForDeprecatedThemes(1),
				__( 'Next', 'site-reviews' )
			);
		}
		return $links;
	}

	/**
	 * @param string $links
	 * @return string
	 */
	protected function buildTemplate( $links )
	{
		$theme = wp_get_theme()->get( 'TextDomain' );
		$class = 'navigation pagination';
		$screenReaderTemplate = '<h2 class="screen-reader-text">%2$s</h2>';
		$screenReaderText = __( 'Site Reviews navigation', 'site-reviews' );
		$innerTemplate = $screenReaderTemplate.'<div class="nav-links">%3$s</div>';
		if( in_array( $theme, ['twentyten', 'twentyeleven', 'twentytwelve'] )) {
			$innerTemplate = '%3$s';
		}
		else if( $theme == 'twentyfourteen' ) {
			$class = str_replace( 'pagination', 'paging-navigation', $class );
			$innerTemplate = $screenReaderTemplate.'<div class="pagination loop-pagination">%3$s</div>';
		}
		$template = '<nav class="%1$s" role="navigation">'.$innerTemplate.'</nav>';
		$template = apply_filters( 'navigation_markup_template', $template, $class );
		$template = sprintf( $template, $class, $screenReaderText, $links );
		return glsr( Builder::class )->div( $template.'<div class="glsr-loader"></div>', [
			'class' => 'glsr-navigation',
		]);
	}

	/**
	 * @param int $pageIncrement
	 * @return string
	 */
	protected function buildUrlForDeprecatedThemes( $pageIncrement )
	{
		if( is_front_page() ) {
			return get_pagenum_link( $this->args['paged'] + $pageIncrement );
		}
		return add_query_arg( Application::PAGED_QUERY_VAR, $this->args['paged'] + $pageIncrement, get_pagenum_link() );
	}

	/**
	 * @return array
	 */
	protected function normalize( array $args )
	{
		return wp_parse_args( $args, [
			'paged' => glsr( QueryBuilder::class )->getPaged(),
			'total' => 1,
		]);
	}
}
