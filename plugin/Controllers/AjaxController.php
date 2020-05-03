<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\ToggleStatus;
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
    public function routerDetectIpAddress()
    {
        glsr(AdminController::class)->routerDetectIpAddress();
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
                $data['btn_okay'] = [esc_attr_x('Okay', 'admin-text', 'site-reviews')];
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
    public function routerMigratePlugin()
    {
        glsr(AdminController::class)->routerMigratePlugin();
        wp_send_json_success([
            'notices' => glsr(Notice::class)->get(),
        ]);
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
        glsr(Notice::class)->clear()->addSuccess(_x('The permissions have been reset, please reload the page for them to take effect.', 'admin-text', 'site-reviews'));
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
            'empty' => '<div>'._x('Nothing found.', 'admin-text', 'site-reviews').'</div>',
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
            'empty' => '<div>'._x('Nothing found.', 'admin-text', 'site-reviews').'</div>',
            'items' => $results,
        ]);
    }

    /**
     * @return void
     */
    public function routerSubmitReview(array $request)
    {
        $command = new CreateReview($request);
        $review = $this->execute($command);
        $data = [
            'errors' => glsr()->sessionGet($command->form_id.'errors', false),
            'message' => glsr()->sessionGet($command->form_id.'message', ''),
            'recaptcha' => glsr()->sessionGet($command->form_id.'recaptcha', false),
            'redirect' => $command->redirect(),
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
        $args = [
            'paged' => Arr::get($request, 'page', false),
            'pagedUrl' => '',
            'pagination' => 'ajax',
            'schema' => false,
        ];
        if (!$args['paged']) {
            $homePath = untrailingslashit(parse_url(home_url(), PHP_URL_PATH));
            $urlPath = untrailingslashit(parse_url(Arr::get($request, 'url'), PHP_URL_PATH));
            $urlQuery = [];
            parse_str(parse_url(Arr::get($request, 'url'), PHP_URL_QUERY), $urlQuery);
            $args['paged'] = (int) Arr::get($urlQuery, glsr()->constant('PAGED_QUERY_VAR'), 1);
            $args['pagedUrl'] = $homePath === $urlPath
                ? trailingslashit(home_url())
                : trailingslashit(home_url($urlPath));
        }
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
        wp_send_json_success([
            'notices' => glsr(Notice::class)->get(),
            'pinned' => $this->execute(new TogglePinned($request)),
        ]);
    }

    /**
     * @return void
     */
    public function routerToggleStatus(array $request)
    {
        wp_send_json_success($this->execute(
            new ToggleStatus(Arr::get($request, 'post_id'), Arr::get($request, 'status'))
        ));
    }
}
