<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Session;

class Form
{
	/**
	 * @param string $id
	 * @return string
	 */
	public function buildFields( $id )
	{
		return array_reduce( $this->getFields( $id ), function( $carry, $field ) {
			return $carry.$field;
		});
	}

	/**
	 * @param string $id
	 * @return array
	 */
	public function getFields( $id )
	{
		$fields = [];
		$values = apply_filters( 'site-reviews/form/fields', glsr()->config( 'forms/'.$id ));
		foreach( $values as $name => $field ) {
			$fields[] = new Field( wp_parse_args( $field, ['name' => $name] ));
		}
		return $fields;
	}

	/**
	 * @param string $id
	 * @return void
	 */
	public function renderFields( $id )
	{
		echo $this->buildFields( $id );
	}
}
