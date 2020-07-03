<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Commands\TogglePinned;
use GeminiLabs\SiteReviews\Commands\ToggleStatus;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Partials\SiteReviews as SiteReviewsPartial;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Translation;
use GeminiLabs\SiteReviews\Request;
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
    public function routerDismissNotice(Request $request)
    {
        glsr(NoticeController::class)->routerDismissNotice($request);
        wp_send_json_success();
    }

    /**
     * @return void
     */
    public function routerMceShortcode(Request $request)
    {
        $shortcode = $request->shortcode;
        $response = false;
        if ($data = glsr()->retrieve('mce.'.$shortcode, false)) {
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
    public function routerMigratePlugin(Request $request)
    {
        glsr(AdminController::class)->routerMigratePlugin($request);
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
    public function routerFetchPagedReviews(Request $request)
    {
        $args = [
            'page' => $request->get('page', 0),
            'pageUrl' => '',
            'pagination' => 'ajax',
            'schema' => false,
        ];
        if (!$args['page']) {
            $urlPath = Url::path($request->url);
            $args['page'] = glsr(Helper::class)->getPageNumber($request->url);
            $args['pageUrl'] = Url::path(home_url()) === $urlPath
                ? Url::home()
                : Url::home($urlPath);
        }
        $atts = glsr(SiteReviewsShortcode::class)->normalizeAtts(Arr::consolidate($request->atts));
        $html = glsr(SiteReviewsPartial::class)->build(wp_parse_args($args, $atts));
        return wp_send_json_success([
            'pagination' => $html->getPagination(),
            'reviews' => $html->getReviews(),
        ]);
    }

    /**
     * @return void
     */
    public function routerResetPermissions()
    {
        glsr(Role::class)->resetAll();
        $reloadLink = glsr(Builder::class)->a([
            'text' => _x('reload the page', 'admin-text', 'site-reviews'),
            'href' => 'javascript:window.location.reload(1)',
        ]);
        glsr(Notice::class)->clear()->addSuccess(
            sprintf(_x('The permissions have been reset, please %s for them to take effect.', 'admin-text', 'site-reviews'), $reloadLink)
        );
        wp_send_json_success([
            'notices' => glsr(Notice::class)->get(),
        ]);
    }

    /**
     * @return void
     */
    public function routerSearchPosts(Request $request)
    {
        $results = glsr(Database::class)->searchPosts($request->search);
        wp_send_json_success([
            'empty' => '<div>'._x('Nothing found.', 'admin-text', 'site-reviews').'</div>',
            'items' => $results,
        ]);
    }

    /**
     * @return void
     */
    public function routerSearchTranslations(Request $request)
    {
        if (empty($request->exclude)) {
            $request->exclude = [];
        }
        $results = glsr(Translation::class)
            ->search($request->search)
            ->exclude()
            ->exclude($request->exclude)
            ->renderResults();
        wp_send_json_success([
            'empty' => '<div>'._x('Nothing found.', 'admin-text', 'site-reviews').'</div>',
            'items' => $results,
        ]);
    }

    /**
     * @return void
     */
    public function routerSubmitReview(Request $request)
    {
        $command = new CreateReview($request->toArray());
        $review = $this->execute($command);
        $data = [
            'errors' => glsr()->sessionGet($command->form_id.'errors', false),
            'html' => (string) $review,
            'message' => glsr()->sessionGet($command->form_id.'message', ''),
            'recaptcha' => glsr()->sessionGet($command->form_id.'recaptcha', false),
            'redirect' => $command->redirect(),
            'review' => (array) $review,
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
    public function routerTogglePinned(Request $request)
    {
        wp_send_json_success([
            'notices' => glsr(Notice::class)->get(),
            'pinned' => $this->execute(new TogglePinned($request->toArray())),
        ]);
    }

    /**
     * @return void
     */
    public function routerToggleStatus(Request $request)
    {
        wp_send_json_success(
            $this->execute(new ToggleStatus($request->post_id, $request->status))
        );
    }
}
