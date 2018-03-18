<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Abstracts\Controller;
use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\SubmitReview;
use GeminiLabs\SiteReviews\Handlers\EnqueueAssets;
use GeminiLabs\SiteReviews\Modules\Validator\ValidateReview;

class PublicController extends Controller
{
	/**
	 * @return void
	 * @action wp_enqueue_scripts
	 */
	public function enqueueAssets()
	{
		(new EnqueueAssets)->handle();
	}

	/**
	 * @return string
	 * @filter script_loader_tag
	 */
	public function filterEnqueuedScripts( $tag, $handle )
	{
		return $handle == Application::ID.'/google-recaptcha'
			&& glsr_get_option( 'reviews-form.recaptcha.integration' ) == 'custom'
			? str_replace( ' src=', ' async defer src=', $tag )
			: $tag;
	}

	/**
	 * Add a variable to query_vars for custom pagination
	 * @param array $vars
	 * @return array
	 * @filter query_vars
	 * @todo remove need for dirty hack
	 */
	public function filterQueryVars( $vars )
	{
		$vars[] = Application::PAGED_QUERY_VAR;
		// dirty hack to fix a form submission with a field that has "name" as name
		if( filter_input( INPUT_POST, 'action' ) == 'submit-review'
			&& !is_null( filter_input( INPUT_POST, 'gotcha' ))) {
			$index = array_search( 'name', $vars, true );
			if( false !== $index ) {
				unset( $vars[$index] );
			}
		}
		return $vars;
	}

	/**
	 * @return mixed
	 */
	public function postSubmitReview( array $request )
	{
		$validated = glsr( ValidateReview::class )->validate( $request );
		if( !empty( $validated->error )) {
			return $validated->request;
		}
		if( $validated->recaptchaIsUnset )return;
		return $this->execute( new SubmitReview( $validated->request ));
	}
}
