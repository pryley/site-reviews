<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Database\QueryBuilder;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Style;

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
		return glsr( Template::class )->build( 'templates/pagination', [
			'context' => [
				'links' => apply_filters( 'site-reviews/paginate_links', $this->buildLinks(), $this->args ),
				'loader' => '<div class="glsr-loader"></div>',
				'screen_reader_text' => __( 'Site Reviews navigation', 'site-reviews' ),
			],
		]);
	}

	/**
	 * @return string
	 */
	protected function buildLinks()
	{
		$args = glsr( Style::class )->paginationArgs([
			'current' => $this->args['paged'],
			'total' => $this->args['total'],
		]);
		if( is_front_page() ) {
			unset( $args['format'] );
		}
		if( $args['type'] == 'array' ) {
			$args['type'] = 'plain';
		}
		return paginate_links( $args );
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
