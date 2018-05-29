<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class Field
{
	protected $field;

	public function __construct( array $field = [] )
	{
		$this->field = $this->normalize( $field );
		// convert depends to "data-depends"
		// extract defaults
		// build/render

		// $rows = $this->build( 'pages/settings/'.$id, [
		// 	'database_key' => OptionManager::databaseKey(),
		// 	'settings' => glsr()->getDefaults(),
		// ]);
	}

	/**
	 * @return string
	 */
	public function build()
	{
		glsr_debug( $this->field );
	}

	/**
	 * @return void
	 */
	public function render()
	{
		echo $this->build();
	}

	/**
	 * @return array
	 */
	protected function normalize( array $field )
	{
		return $field;
	}
}
