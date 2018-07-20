<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Controllers\Controller;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Handlers\EnqueuePublicAssets;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Modules\Style;
use GeminiLabs\SiteReviews\Modules\Validator\ValidateReview;

class PublicController extends Controller
{
	/**
	 * @return void
	 * @action wp_enqueue_scripts
	 */
	public function enqueueAssets()
	{
		(new EnqueuePublicAssets)->handle();
	}

	/**
	 * @param string $tag
	 * @param string $handle
	 * @return string
	 * @filter script_loader_tag
	 */
	public function filterEnqueuedScripts( $tag, $handle )
	{
		return $handle == Application::ID.'/google-recaptcha'
			&& glsr( OptionManager::class )->get( 'settings.submissions.recaptcha.integration' ) == 'custom'
			? str_replace( ' src=', ' async defer src=', $tag )
			: $tag;
	}

	/**
	 * @return array
	 * @filter query_vars
	 */
	public function filterQueryVars( array $vars )
	{
		$vars[] = Application::PAGED_QUERY_VAR;
		return $vars;
	}

	/**
	 * @param string $view
	 * @return string
	 * @filter site-reviews/render/view
	 */
	public function filterRenderView( $view )
	{
		return glsr( Style::class )->filterView( $view );
	}

	/**
	 * @return void
	 * @action site-reviews/builder
	 */
	public function modifyBuilder( Builder $instance )
	{
		call_user_func_array( [glsr( Style::class ), 'modifyField'], [&$instance] );
	}

	/**
	 * @return void
	 * @action wp_footer
	 */
	public function renderSchema()
	{
		glsr( Schema::class )->render();
	}

	/**
	 * @return mixed
	 */
	public function routerSubmitReview( array $request )
	{
		$validated = glsr( ValidateReview::class )->validate( $request );
		if( !empty( $validated->error ) || $validated->recaptchaIsUnset )return;
		$this->execute( new CreateReview( $validated->request ));
	}
}
