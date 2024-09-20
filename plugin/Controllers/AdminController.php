<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\ApproveReview;
use GeminiLabs\SiteReviews\Commands\EnqueueAdminAssets;
use GeminiLabs\SiteReviews\Commands\ExportRatings;
use GeminiLabs\SiteReviews\Commands\ImportRatings;
use GeminiLabs\SiteReviews\Commands\RegisterTinymcePopups;
use GeminiLabs\SiteReviews\Commands\TogglePinned;
use GeminiLabs\SiteReviews\Commands\ToggleStatus;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Defaults\ColumnFilterbyDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Install;
use GeminiLabs\SiteReviews\License;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Migrate;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Modules\Translation;
use GeminiLabs\SiteReviews\Request;

class AdminController extends AbstractController
{
    /**
     * @action site-reviews/route/get/admin/approve
     */
    public function approveReview(Request $request): void
    {
        $postId = Arr::get($request->data, 0);
        $review = glsr_get_review($postId);
        if ($review->isValid()) {
            $command = $this->execute(new ApproveReview($review));
            if ($command->successful()) {
                glsr(Notice::class)->store(); // because of the redirect
            }
        }
        wp_redirect(glsr_admin_url());
        exit;
    }

    /**
     * @action in_plugin_update_message-{glsr()->basename}
     */
    public function displayUpdateWarning(array $data): void
    {
        $version = Arr::get($data, 'new_version');
        $parts = explode('.', $version);
        $newVersion = Arr::getAs('int', $parts, 0, 0);
        if ($newVersion > (int) glsr()->version('major')) {
            glsr()->render('views/partials/update-warning');
        }
    }

    /**
     * @action admin_enqueue_scripts
     */
    public function enqueueAssets(): void
    {
        $this->execute(new EnqueueAdminAssets());
    }

    /**
     * @filter plugin_action_links_site-reviews/site-reviews.php
     */
    public function filterActionLinks(array $links): array
    {
        if (glsr()->hasPermission('settings')) {
            $links['settings'] = glsr(Builder::class)->a([
                'href' => glsr_admin_url('settings'),
                'text' => _x('Settings', 'admin-text', 'site-reviews'),
            ]);
        }
        if (glsr()->hasPermission('documentation')) {
            $links['documentation'] = glsr(Builder::class)->a([
                'href' => glsr_admin_url('documentation'),
                'text' => _x('Help', 'admin-text', 'site-reviews'),
            ]);
        }
        return $links;
    }

    /**
     * @filter export_args
     */
    public function filterExportArgs(array $args): array
    {
        if (in_array(Arr::get($args, 'content'), ['all', glsr()->post_type])) {
            $this->execute(new ExportRatings(glsr()->args($args)));
        }
        return $args;
    }

    /**
     * @filter screen_options_show_submit
     */
    public function filterScreenOptionsButton(bool $showButton): bool
    {
        global $post_type_object, $title, $typenow;
        if (!str_starts_with($typenow, glsr()->post_type)) {
            return $showButton;
        }
        $submit = get_submit_button(_x('Apply', 'admin-text', 'site-reviews'), 'primary', 'screen-options-apply', false);
        $close = glsr(Builder::class)->button([
            'aria-controls' => 'screen-options-wrap',
            'class' => 'button button-secondary glsr-screen-meta-toggle',
            'text' => _x('Close Panel', 'admin-text', 'site-reviews'),
            'type' => 'button',
        ]);
        echo glsr(Builder::class)->p([
            'style' => 'display:inline-flex;gap:6px;',
            'text' => $submit.$close,
        ]);
        return false; // don't display the default submit button
    }

    /**
     * @filter mce_external_plugins
     */
    public function filterTinymcePlugins(array $plugins): array
    {
        if (glsr()->can('edit_posts')) {
            $plugins['glsr_shortcode'] = glsr()->url('assets/scripts/mce-plugin.js');
        }
        return $plugins;
    }

    /**
     * @action admin_init
     */
    public function onActivation(): void
    {
        if (empty(get_option(glsr()->prefix.'activated'))) {
            glsr(Install::class)->run(); // this hard-resets role permissions
            glsr(Migrate::class)->run();
            update_option(glsr()->prefix.'activated', true);
            glsr()->action('activated');
        }
    }

    /**
     * @action deactivate_{glsr()->basename}
     */
    public function onDeactivation(bool $isNetworkDeactivation): void
    {
        glsr(Install::class)->deactivate($isNetworkDeactivation);
    }

    /**
     * @action import_end
     */
    public function onImportEnd(): void
    {
        $this->execute(new ImportRatings());
    }

    /**
     * @action admin_head
     */
    public function printInlineStyle(): void
    {
        echo '<style type="text/css">a[href="edit.php?post_type=site-review&page='.Str::dashCase(glsr()->prefix).'addons"]:not(.current),a[href="edit.php?post_type=site-review&page='.Str::dashCase(glsr()->prefix).'addons"]:focus,a[href="edit.php?post_type=site-review&page='.Str::dashCase(glsr()->prefix).'addons"]:hover{color:#F6E05E!important;}</style>';
    }

    /**
     * @action admin_init
     */
    public function registerTinymcePopups(): void
    {
        $this->execute(new RegisterTinymcePopups());
    }

    /**
     * @action in_admin_header
     */
    public function renderPageHeader(): void
    {
        global $post_type_object, $title;
        if (!$this->isReviewAdminScreen()) {
            return;
        }
        $buttons = [];
        $screen = glsr_current_screen();
        if (glsr()->post_type === $screen->post_type && !glsr(License::class)->isPremium()) {
            $buttons['premium'] = [
                'class' => 'components-button is-primary glsr-try-premium',
                'href' => 'https://niftyplugins.com/plugins/site-reviews-premium/',
                'target' => '_blank',
                'text' => _x('Try Premium', 'admin-text', 'site-reviews'),
            ];
        }
        if (in_array($screen->base, ['edit', 'post'])) {
            $buttons['new'] = [
                'class' => 'components-button is-secondary glsr-new-post',
                'data-new' => '',
                'href' => admin_url("post-new.php?post_type={$screen->post_type}"),
                'text' => Arr::get($post_type_object, 'labels.add_new'),
            ];
        }
        $buttons = glsr()->filterArray('page-header/buttons', $buttons);
        glsr()->render('views/partials/page-header', [
            'buttons' => $buttons,
            'hasScreenOptions' => in_array($screen->base, ['edit', 'edit-tags', 'post']),
            'logo' => file_get_contents(glsr()->path('assets/images/mascot.svg')),
            'title' => esc_html($title),
        ]);
    }

    /**
     * @action media_buttons
     */
    public function renderTinymceButton(string $editorId): void
    {
        $allowedEditors = glsr()->filterArray('tinymce/editor-ids', ['content'], $editorId);
        if ('post' !== glsr_current_screen()->base || !in_array($editorId, $allowedEditors)) {
            return;
        }
        $shortcodes = [];
        foreach (glsr()->retrieveAs('array', 'mce', []) as $shortcode => $values) {
            $shortcodes[$shortcode] = $values;
        }
        if (!empty($shortcodes)) {
            $shortcodes = wp_list_sort($shortcodes, 'label', 'ASC', true); // preserve keys
            glsr()->render('partials/editor/tinymce', [
                'shortcodes' => $shortcodes,
            ]);
        }
    }

    /**
     * @action admin_init
     */
    public function scheduleMigration(): void
    {
        if (defined('GLSR_UNIT_TESTS')) {
            return;
        }
        if (!$this->isReviewAdminScreen()) {
            return;
        }
        if (glsr(Queue::class)->isPending('queue/migration')) {
            return;
        }
        if (!glsr(Migrate::class)->isMigrationNeeded() && !glsr(Database::class)->isMigrationNeeded()) {
            return;
        }
        glsr(Queue::class)->once(time() + MINUTE_IN_SECONDS, 'queue/migration');
    }

    /**
     * @action site-reviews/route/ajax/filter-assigned_post
     */
    public function searchAssignedPostsAjax(Request $request): void
    {
        $search = glsr(Sanitizer::class)->sanitizeText($request->search);
        $results = glsr(Database::class)->searchAssignedPosts($search)->results();
        wp_send_json_success([
            'items' => $results,
        ]);
    }

    /**
     * @action site-reviews/route/ajax/filter-assigned_user
     */
    public function searchAssignedUsersAjax(Request $request): void
    {
        $search = glsr(Sanitizer::class)->sanitizeText($request->search);
        $results = glsr(Database::class)->searchAssignedUsers($search)->results();
        array_walk($results, function ($user) {
            $user->name = glsr(Sanitizer::class)->sanitizeUserName($user->name, $user->nickname);
        });
        wp_send_json_success([
            'items' => $results,
        ]);
    }

    /**
     * @action site-reviews/route/ajax/filter-author
     */
    public function searchAuthorsAjax(Request $request): void
    {
        $search = glsr(Sanitizer::class)->sanitizeText($request->search);
        $results = glsr(Database::class)->searchUsers($search)->results();
        array_walk($results, function ($user) {
            $user->name = glsr(Sanitizer::class)->sanitizeUserName($user->name, $user->nickname);
        });
        wp_send_json_success([
            'items' => $results,
        ]);
    }

    /**
     * @action site-reviews/route/ajax/search-posts
     */
    public function searchPostsAjax(Request $request): void
    {
        $search = glsr(Sanitizer::class)->sanitizeText($request->search);
        $results = glsr(Database::class)->searchPosts($search)->render();
        wp_send_json_success([
            'empty' => '<div>'._x('Nothing found.', 'admin-text', 'site-reviews').'</div>',
            'items' => $results,
        ]);
    }

    /**
     * @action site-reviews/route/ajax/search-strings
     */
    public function searchStringsAjax(Request $request): void
    {
        $search = glsr(Sanitizer::class)->sanitizeText($request->search);
        $exclude = Arr::consolidate($request->exclude);
        $results = glsr(Translation::class)
            ->search($search)
            ->exclude()
            ->exclude($exclude)
            ->renderResults();
        wp_send_json_success([
            'empty' => '<div>'._x('Nothing found.', 'admin-text', 'site-reviews').'</div>',
            'items' => $results,
        ]);
    }

    /**
     * @action site-reviews/route/ajax/search-users
     */
    public function searchUsersAjax(Request $request): void
    {
        $search = glsr(Sanitizer::class)->sanitizeText($request->search);
        $results = glsr(Database::class)->searchUsers($search)->render();
        wp_send_json_success([
            'empty' => '<div>'._x('Nothing found.', 'admin-text', 'site-reviews').'</div>',
            'items' => $results,
        ]);
    }

    /**
     * @action site-reviews/route/ajax/toggle-filters
     */
    public function toggleFiltersAjax(Request $request): void
    {
        if ($userId = get_current_user_id()) {
            $filters = array_keys(glsr(ColumnFilterbyDefaults::class)->defaults());
            $enabled = glsr(Sanitizer::class)->sanitizeArrayString($request->enabled);
            $enabled = array_intersect($filters, $enabled);
            update_user_meta($userId, 'edit_'.glsr()->post_type.'_filters', $enabled);
        }
        wp_send_json_success();
    }

    /**
     * @action site-reviews/route/ajax/toggle-pinned
     */
    public function togglePinnedAjax(Request $request): void
    {
        $command = $this->execute(new TogglePinned($request));
        glsr()->action('cache/flush', $command->review); // @phpstan-ignore-line
        wp_send_json_success($command->response());
    }

    /**
     * @action site-reviews/route/ajax/toggle-status
     */
    public function toggleStatusAjax(Request $request): void
    {
        $command = $this->execute(new ToggleStatus($request));
        wp_send_json_success($command->response());
    }
}
