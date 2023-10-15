<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Commands\EnqueuePublicAssets;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Modules\Style;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class PublicController extends Controller
{
    /**
     * @action wp_enqueue_scripts
     */
    public function enqueueAssets(): void
    {
        $this->execute(new EnqueuePublicAssets());
    }

    /**
     * @action site-reviews/route/ajax/fetch-paged-reviews
     */
    public function fetchPagedReviewsAjax(Request $request): void
    {
        glsr()->store(glsr()->paged_handle, $request);
        $html = glsr(SiteReviewsShortcode::class)
            ->normalize($request->cast('atts', 'array'))
            ->buildReviewsHtml();
        $response = [
            'pagination' => $html->getPagination($wrap = false),
            'reviews' => $html->getReviews(),
        ];
        wp_send_json_success($response);
    }

    /**
     * @param string $tag
     * @param string $handle
     * @param string $src
     * @return string
     * @filter script_loader_tag
     */
    public function filterEnqueuedScriptTags($tag, $handle, $src)
    {
        $async = [
            glsr()->id.'/turnstile',
        ];
        $defer = [
            glsr()->id.'/hcaptcha',
            glsr()->id.'/google-recaptcha',
            glsr()->id.'/turnstile',
        ];
        if (in_array($handle, glsr()->filterArray('async-scripts', $async))) {
            $tag = str_replace(' src=', ' async src=', $tag);
        }
        if (in_array($handle, glsr()->filterArray('defer-scripts', $defer))) {
            $tag = str_replace(' src=', ' defer src=', $tag);
        }
        if (glsr()->id.'/friendlycaptcha-module' === $handle) {
            $tag = sprintf('<script type="module" src="%s" async defer></script>', esc_url($src));
        }
        if (glsr()->id.'/friendlycaptcha-nomodule' === $handle) {
            $tag = sprintf('<script nomodule src="%s" async defer></script>', esc_url($src));
        }
        return $tag;
    }

    /**
     * @filter site-reviews/config/forms/review-form
     */
    public function filterFieldOrder(array $config): array
    {
        $order = array_keys($config);
        $order = glsr()->filterArray('review-form/order', $order);
        return array_intersect_key(array_merge(array_flip($order), $config), $config);
    }

    /**
     * @filter site-reviews/render/view
     */
    public function filterRenderView(string $view): string
    {
        return glsr(Style::class)->view($view);
    }

    /**
     * @action site-reviews/builder
     */
    public function modifyBuilder(Builder $builder): void
    {
        $reflection = new \ReflectionClass($builder);
        if ('Builder' === $reflection->getShortName()) { // only modify public fields
            call_user_func_array([glsr(Style::class), 'modifyField'], [$builder]);
        }
    }

    /**
     * @action wp_footer
     */
    public function renderSchema(): void
    {
        if (empty(glsr_get_option('schema.integration.plugin'))) {
            glsr(Schema::class)->render();
        }
    }

    /**
     * @action site-reviews/route/public/submit-review
     */
    public function submitReview(Request $request): void
    {
        $command = $this->execute(new CreateReview($request));
        if ($command->success()) {
            wp_safe_redirect($command->referer()); // @todo add review ID to referer?
            exit;
        }
    }

    /**
     * @action site-reviews/route/ajax/submit-review
     */
    public function submitReviewAjax(Request $request): void
    {
        $command = $this->execute(new CreateReview($request));
        if ($command->success()) {
            wp_send_json_success($command->response());
        }
        wp_send_json_error($command->response());
    }
}
