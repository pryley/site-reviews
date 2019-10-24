<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Handlers\EnqueuePublicAssets;
use GeminiLabs\SiteReviews\Helpers\Arr;
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
        (new EnqueuePublicAssets())->handle();
    }

    /**
     * @param string $tag
     * @param string $handle
     * @return string
     * @filter script_loader_tag
     */
    public function filterEnqueuedScripts($tag, $handle)
    {
        $scripts = [Application::ID.'/google-recaptcha'];
        if (in_array($handle, apply_filters('site-reviews/async-scripts', $scripts))) {
            $tag = str_replace(' src=', ' async src=', $tag);
        }
        if (in_array($handle, apply_filters('site-reviews/defer-scripts', $scripts))) {
            $tag = str_replace(' src=', ' defer src=', $tag);
        }
        return $tag;
    }

    /**
     * @return array
     * @filter site-reviews/config/forms/submission-form
     */
    public function filterFieldOrder(array $config)
    {
        $order = (array) apply_filters('site-reviews/submission-form/order', array_keys($config));
        return array_intersect_key(array_merge(array_flip($order), $config), $config);
    }

    /**
     * @param string $view
     * @return string
     * @filter site-reviews/render/view
     */
    public function filterRenderView($view)
    {
        return glsr(Style::class)->filterView($view);
    }

    /**
     * @return void
     * @action site-reviews/builder
     */
    public function modifyBuilder(Builder $instance)
    {
        call_user_func_array([glsr(Style::class), 'modifyField'], [$instance]);
    }

    /**
     * @return void
     * @action wp_footer
     */
    public function renderSchema()
    {
        glsr(Schema::class)->render();
    }

    /**
     * @return CreateReview
     */
    public function routerSubmitReview(array $request)
    {
        $validated = glsr(ValidateReview::class)->validate($request);
        $command = new CreateReview($validated->request);
        if (empty($validated->error) && !$validated->recaptchaIsUnset) {
            $this->execute($command);
        }
        return $command;
    }
}
