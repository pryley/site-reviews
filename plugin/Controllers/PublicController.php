<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Commands\EnqueuePublicAssets;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Partials\SiteReviews;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Modules\Style;
use GeminiLabs\SiteReviews\Request;

class PublicController extends Controller
{
    /**
     * @return void
     * @action wp_enqueue_scripts
     */
    public function enqueueAssets()
    {
        $this->execute(new EnqueuePublicAssets());
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/fetch-paged-reviews
     */
    public function fetchPagedReviewsAjax(Request $request)
    {
        $args = [
            'page' => $request->get('page', 0),
            'pageUrl' => '',
            'pagination' => 'ajax',
            'schema' => false,
        ];
        if (!$args['page']) {
            $urlPath = Url::path($request->url);
            $args['page'] = Helper::getPageNumber($request->url);
            $args['pageUrl'] = Url::path(home_url()) === $urlPath
                ? Url::home()
                : Url::home($urlPath);
        }
        $atts = glsr(SiteReviewsDefaults::class)->merge(Arr::consolidate($request->atts));
        $args = wp_parse_args($args, $atts);
        $html = glsr(SiteReviews::class)->build($args);
        wp_send_json_success([
            'pagination' => $html->getPagination($wrap = false),
            'reviews' => $html->getReviews($wrap = false),
        ]);
    }

    /**
     * @param string $tag
     * @param string $handle
     * @return string
     * @filter script_loader_tag
     */
    public function filterEnqueuedScriptTags($tag, $handle)
    {
        $scripts = [glsr()->id.'/google-recaptcha'];
        if (in_array($handle, glsr()->filterArray('async-scripts', $scripts))) {
            $tag = str_replace(' src=', ' async src=', $tag);
        }
        if (in_array($handle, glsr()->filterArray('defer-scripts', $scripts))) {
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
        $order = array_keys($config);
        $order = glsr()->filterArray('submission-form/order', $order);
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
    public function modifyBuilder(Builder $builder)
    {
        $reflection = new \ReflectionClass($builder);
        if ('Builder' === $reflection->getShortName()) { // only modify public fields
            call_user_func_array([glsr(Style::class), 'modifyField'], [$builder]);
        }
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
     * @return void
     * @action site-reviews/route/public/submit-review
     */
    public function submitReview(Request $request)
    {
        $command = $this->execute(new CreateReview($request));
        if ($command->success()) {
            wp_safe_redirect($command->referer()); // @todo add review ID to referer
            exit;
        }
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/submit-review
     */
    public function submitReviewAjax(Request $request)
    {
        $command = $this->execute(new CreateReview($request));
        if ($command->success()) {
            wp_send_json_success($command->response());
        }
        wp_send_json_error($command->response());
    }
}
