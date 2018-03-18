<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use Exception;
use GeminiLabs\SiteReviews\App;

class Html
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var array
	 */
	protected $forms;

	public function __construct( App $app )
	{
		$this->app   = $app;
		$this->forms = [];
	}

	/**
	 * Add a custom field to the form
	 * @param string $formId
	 * @return \GeminiLabs\SiteReviews\Html\Form|false
	 */
	public function addCustomField( $formId, callable $callback )
	{
		if( !$this->isForm( $formId ))return;
		return $this->forms[$formId]->addCustomField( $callback );
	}

	/**
	 * Add a field to an existing form
	 * @param string $formId
	 * @return \GeminiLabs\SiteReviews\Html\Form|false
	 */
	public function addField( $formId, array $args = [] )
	{
		if( !$this->isForm( $formId ))return;
		return $this->forms[$formId]->addField( $args );
	}

	/**
	 * Create a new form
	 * @param string $formId
	 * @return \GeminiLabs\SiteReviews\Html\Form
	 */
	public function createForm( $formId, array $args = [] )
	{
		if( !$this->isForm( $formId )) {
			$form = $this->app->make( 'Html\Form' )->normalize( $args );
			$this->forms[$formId] = $form;
		}
		return $this->forms[$formId];
	}

	/**
	 * Return dependencies from all forms
	 * @return array
	 */
	public function getDependencies()
	{
		$dependencies = [];
		foreach( $this->forms as $form ) {
			$dependencies = array_unique(
				array_merge( $dependencies, $form->dependencies )
			);
		}
		return $dependencies;
	}

	/**
	 * Return dependencies from a specific form
	 * @return array
	 */
	public function getFormDependencies( $formId )
	{
		return $this->isForm( $formId )
			? $this->forms[$formId]->dependencies
			: [];
	}

	/**
	 * Render a field (outside of a form)
	 * @return void|string
	 */
	public function renderField( array $args = [] )
	{
		$field = $this->app->make( 'Html\Field' )->normalize( $args );
		return $field->render();
	}

	/**
	 * Render a form then remove it
	 * @param string $formId
	 * @return void|string
	 */
	public function renderForm( $formId )
	{
		if( !$this->isForm( $formId ))return;
		$form = $this->forms[$formId];
		unset( $this->forms[$formId] );
		return $form->render();
	}

	/**
	 * Render a partial
	 * @param string $partialName
	 * @return void|string
	 */
	public function renderPartial( $partialName, array $args = [] )
	{
		$partial = $this->app->make( 'Html\Partial' )->normalize( $partialName, $args );
		return $partial->render();
	}

	/**
	 * Render a template
	 * @param string $templatePath
	 * @return void|string
	 */
	public function renderTemplate( $templatePath, array $args = [] )
	{
		$file = $this->app->path . "views/{$templatePath}.php";
		if( !file_exists( $file ))return;
		ob_start();
		include $file;
		$template = ob_get_clean();
		return $this->renderTemplateString( $template, $args );
	}

	/**
	 * Render a template string
	 * @param string $template
	 * @return void|string
	 */
	public function renderTemplateString( $template, array $args = [] )
	{
		if( !empty( $args )) {
			foreach( $args as $key => $value ) {
				$template = str_replace( '{' . $key . '}', $value, $template );
			}
		}
		$template = trim( $template );
		return $template;
	}

	/**
	 * Reset the forms array
	 * @return Html
	 */
	public function reset()
	{
		$this->forms = [];
		return $this;
	}

	/**
	 * Set dependencies of a form
	 * @param string $formId
	 * @param bool|string $overwrite
	 * @return void
	 */
	public function setFormDependencies( $formId, array $dependencies, $overwrite = false )
	{
		if( $this->isForm( $formId ))return;
		if( !$overwrite || $overwrite == 'merge' ) {
			$dependencies = array_unique(
				array_merge( $this->forms[$formId]->dependencies, $dependencies )
			);
		}
		$this->forms[$formId]->dependencies = $dependencies;
	}

	/**
	 * Return a stored form
	 * @return \GeminiLabs\SiteReviews\Html\Form|false
	 */
	public function switchForm( $formId )
	{
		return $this->isForm( $formId )
			? $this->forms[$formId]
			: false;
	}

	/**
	 * Check if a form exists
	 * @param string $formId
	 * @return bool
	 */
	protected function isForm( $formId )
	{
		return isset( $this->forms[$formId] );
	}
}
