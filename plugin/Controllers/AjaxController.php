<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\ChangeStatus;
use GeminiLabs\SiteReviews\Commands\TogglePinned;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Html\Partials\SiteReviews as SiteReviewsPartial;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Translation;
use GeminiLabs\SiteReviews\Role;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class AjaxController extends Controller
{
    /**
     * @return void
     */
    public function routerChangeStatus(array $request)
    {
        wp_send_json_success($this->execute(new ChangeStatus($request)));
    }

    /**
     * @return void
     */
    public function routerClearConsole()
    {
        glsr(AdminController::class)->routerClearConsole();
        wp_send_json_success([
            'console' => glsr(Console::class)->get(),
            'notices' => glsr(Notice::class)->get(),
        ]);
    }

    /**
     * @return void
     */
    public function routerCountReviews()
    {
        glsr(AdminController::class)->routerCountReviews();
        wp_send_json_success([
            'notices' => glsr(Notice::class)->get(),
        ]);
    }

    /**
     * @return void
     */
    public function routerMigrateReviews()
    {
        glsr(AdminController::class)->routerMigrateReviews();
        wp_send_json_success([
            'notices' => glsr(Notice::class)->get(),
        ]);
    }

    /**
     * @return void
     */
    public function routerDismissNotice(array $request)
    {
        glsr(NoticeController::class)->routerDismissNotice($request);
        wp_send_json_success();
    }

    /**
     * @return void
     */
    public function routerMceShortcode(array $request)
    {
        $shortcode = $request['shortcode'];
        $response = false;
        if (array_key_exists($shortcode, glsr()->mceShortcodes)) {
            $data = glsr()->mceShortcodes[$shortcode];
            if (!empty($data['errors'])) {
                $data['btn_okay'] = [esc_html__('Okay', 'site-reviews')];
            }
            $response = [
                'body' => $data['fields'],
                'close' => $data['btn_close'],
                'ok' => $data['btn_okay'],
                'shortcode' => $shortcode,
                'title' => $data['title'],
            ];
        }
        wp_send_json_success($response);
    }

    /**
     * @return void
     */
    public function routerFetchConsole()
    {
        glsr(AdminController::class)->routerFetchConsole();
        wp_send_json_success([
            'console' => glsr(Console::class)->get(),
            'notices' => glsr(Notice::class)->get(),
        ]);
    }

    /**
     * @return void
     */
    public function routerResetPermissions()
    {
        glsr(Role::class)->resetAll();
        glsr(Notice::class)->clear()->addSuccess(__('The permissions have been reset, please reload the page for them to take effect.', 'site-reviews'));
        wp_send_json_success([
            'notices' => glsr(Notice::class)->get(),
        ]);
    }

    /**
     * @return void
     */
    public function routerSearchPosts(array $request)
    {
        $results = glsr(Database::class)->searchPosts($request['search']);
        wp_send_json_success([
            'empty' => '<div>'.__('Nothing found.', 'site-reviews').'</div>',
            'items' => $results,
        ]);
    }

    /**
     * @return void
     */
    public function routerSearchTranslations(array $request)
    {
        if (empty($request['exclude'])) {
            $request['exclude'] = [];
        }
        $results = glsr(Translation::class)
            ->search($request['search'])
            ->exclude()
            ->exclude($request['exclude'])
            ->renderResults();
        wp_send_json_success([
            'empty' => '<div>'.__('Nothing found.', 'site-reviews').'</div>',
            'items' => $results,
        ]);
    }

    /**
     * @return void
     */
    public function routerSubmitReview(array $request)
    {
        $command = glsr(PublicController::class)->routerSubmitReview($request);
        $redirect = trim(strval(get_post_meta($command->post_id, 'redirect_to', true)));
        $redirect = apply_filters('site-reviews/review/redirect', $redirect, $command);
        $data = [
            'errors' => glsr()->sessionGet($command->form_id.'errors', false),
            'message' => glsr()->sessionGet($command->form_id.'message', ''),
            'recaptcha' => glsr()->sessionGet($command->form_id.'recaptcha', false),
            'redirect' => $redirect,
        ];
        if (false === $data['errors']) {
            glsr()->sessionClear();
            wp_send_json_success($data);
        }
        wp_send_json_error($data);
    }

    /**
     * @return void
     */
    public function routerFetchPagedReviews(array $request)
    {
        $homePath = untrailingslashit(parse_url(home_url(), PHP_URL_PATH));
        $urlPath = untrailingslashit(parse_url(Arr::get($request, 'url'), PHP_URL_PATH));
        $urlQuery = [];
        parse_str(parse_url(Arr::get($request, 'url'), PHP_URL_QUERY), $urlQuery);
        $pagedUrl = $homePath === $urlPath
            ? home_url()
            : home_url($urlPath);
        $args = [
            'paged' => (int) Arr::get($urlQuery, glsr()->constant('PAGED_QUERY_VAR'), 1),
            'pagedUrl' => trailingslashit($pagedUrl),
            'pagination' => 'ajax',
            'schema' => false,
        ];
        $atts = (array) json_decode(Arr::get($request, 'atts'));
        $atts = glsr(SiteReviewsShortcode::class)->normalizeAtts($atts);
        $html = glsr(SiteReviewsPartial::class)->build(wp_parse_args($args, $atts));
        return wp_send_json_success([
            'pagination' => $html->getPagination(),
            'reviews' => $html->getReviews(),
        ]);
    }

    /**
     * @return void
     */
    public function routerTogglePinned(array $request)
    {
        $isPinned = $this->execute(new TogglePinned($request));
        wp_send_json_success([
            'notices' => glsr(Notice::class)->get(),
            'pinned' => $isPinned,
        ]);
    }
}
