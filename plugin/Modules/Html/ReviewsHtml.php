<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use ArrayObject;
use GeminiLabs\SiteReviews\Modules\Html\Partial;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Reviews;

class ReviewsHtml extends ArrayObject
{
	/**
	 * @var array
	 */
	public $args;

	/**
	 * @var string
	 */
	public $navigation;

	/**
	 * @var array
	 */
	public $reviews;

	public function __construct( array $reviews, $maxPageCount, array $args )
	{
		$this->args = $args;
		$this->reviews = $reviews;
		$this->navigation = glsr( Partial::class )->build( 'pagination', [
			'total' => $maxPageCount,
		]);
		parent::__construct( $reviews, ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS );
	}

	/**
	 * @return string
	 */
	public function __get( $key )
	{
		return array_key_exists( $key, $this->reviews )
			? $this->reviews[$key]
			: '';
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return glsr( Template::class )->build( 'templates/reviews', [
			'context' => [
				'assigned_to' => $this->args['assigned_to'],
				'class' => $this->getClass(),
				'id' => $this->args['id'],
				'navigation' => $this->getNavigation(),
				'reviews' => implode( PHP_EOL, $this->reviews ),
			],
		]);
	}

	/**
	 * @return string
	 */
	protected function getClass()
	{
		$defaults = [
			'glsr-reviews', 'glsr-default',
		];
		if( $this->args['pagination'] == 'ajax' ) {
			$defaults[] = 'glsr-ajax-pagination';
		}
		$classes = explode( ' ', $this->args['class'] );
		$classes = array_unique( array_merge( $defaults, $classes ));
		return implode( ' ', $classes );
	}

	/**
	 * @return string
	 */
	protected function getNavigation()
	{
		return wp_validate_boolean( $this->args['pagination'] )
			? $this->navigation
			: '';
	}
}
