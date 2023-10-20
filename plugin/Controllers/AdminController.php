<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\ApproveReview;
use GeminiLabs\SiteReviews\Commands\EnqueueAdminAssets;
use GeminiLabs\SiteReviews\Commands\ExportRatings;
use GeminiLabs\SiteReviews\Commands\ImportRatings;
use GeminiLabs\SiteReviews\Commands\RegisterTinymcePopups;
use GeminiLabs\SiteReviews\Commands\TogglePinned;
use GeminiLabs\SiteReviews\Commands\ToggleStatus;
use GeminiLabs\SiteReviews\Commands\ToggleVerified;
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

class AdminController extends Controller
{
    /**
     * @action site-reviews/route/get/admin/approve
     */
    public function approveReview(Request $request): void
    {
        $postId = Arr::get($request->data, 0);
        $review = glsr_get_review($postId);
        if ($review->isValid() && $this->execute(new ApproveReview($review))) {
            glsr(Notice::class)->store(); // because of the redirect
        }
        wp_redirect(glsr_admin_url());
        exit;
    }

    /**
     * @param array $data
     * @action in_plugin_update_message-{plugin_basename}
     */
    public function displayUpdateWarning($data): void
    {
        $version = Arr::get($data, 'new_version');
        $parts = explode('.', $version);
        $newVersion = Arr::getAs('int', $parts, 0, 0);
        if ($newVersion > (int) glsr()->version('major')) {
            glsr()->render('views/partials/update-warning');
        }
    }

    /**
     * @return void
     * @action admin_enqueue_scripts
     */
    public function enqueueAssets()
    {
        $this->execute(new EnqueueAdminAssets());
    }

    /**
     * @return array
     * @filter plugin_action_links_site-reviews/site-reviews.php
     */
    public function filterActionLinks(array $links)
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
     * @param array $items
     * @return array
     * @filter dashboard_glance_items
     */
    public function filterDashboardGlanceItems($items)
    {
        $postCount = wp_count_posts(glsr()->post_type);
        if (empty($postCount->publish)) {
            return $items;
        }
        $text = _nx('%s Review', '%s Reviews', $postCount->publish, 'admin-text', 'site-reviews');
        $text = sprintf($text, number_format_i18n($postCount->publish));
        $items = Arr::consolidate($items);
        if (glsr()->can('edit_posts')) {
            $items[] = glsr(Builder::class)->a($text, [
                'class' => 'glsr-review-count',
                'href' => glsr_admin_url(),
            ]);
        } else {
            $items[] = glsr(Builder::class)->span($text, [
                'class' => 'glsr-review-count',
            ]);
        }
        return $items;
    }

    /**
     * @param array $args
     * @return array
     * @filter export_args
     */
    public function filterExportArgs($args)
    {
        if (in_array(Arr::get($args, 'content'), ['all', glsr()->post_type])) {
            $this->execute(new ExportRatings(glsr()->args($args)));
        }
        return $args;
    }

    /**
     * @param bool $showButton
     * @return bool
     * @filter screen_options_show_submit
     */
    public function filterScreenOptionsButton($showButton)
    {
        global $post_type_object, $title, $typenow;
        if (!Str::startsWith($typenow, glsr()->post_type)) {
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
     * @param array $plugins
     * @return array
     * @filter mce_external_plugins
     */
    public function filterTinymcePlugins($plugins)
    {
        if (glsr()->can('edit_posts')) {
            $plugins = Arr::consolidate($plugins);
            $plugins['glsr_shortcode'] = glsr()->url('assets/scripts/mce-plugin.js');
        }
        return $plugins;
    }

    /**
     * @return void
     * @action admin_init
     */
    public function onActivation()
    {
        if (empty(get_option(glsr()->prefix.'activated'))) {
            glsr(Install::class)->run();
            glsr(Migrate::class)->run();
            update_option(glsr()->prefix.'activated', true);
        }
    }

    /**
     * @return void
     * @action import_end
     */
    public function onImportEnd()
    {
        $this->execute(new ImportRatings());
    }

    /**
     * @return void
     * @action admin_head
     */
    public function printInlineStyle()
    {
        echo '<style type="text/css">a[href="edit.php?post_type=site-review&page='.Str::dashCase(glsr()->prefix).'addons"]:not(.current),a[href="edit.php?post_type=site-review&page='.Str::dashCase(glsr()->prefix).'addons"]:focus,a[href="edit.php?post_type=site-review&page='.Str::dashCase(glsr()->prefix).'addons"]:hover{color:#F6E05E!important;}</style>';
    }

    /**
     * @return void
     * @action admin_init
     */
    public function registerTinymcePopups()
    {
        $this->execute(new RegisterTinymcePopups([
            'site_reviews' => _x('Latest Reviews', 'admin-text', 'site-reviews'),
            'site_review' => _x('Single Review', 'admin-text', 'site-reviews'),
            'site_reviews_form' => _x('Review Form', 'admin-text', 'site-reviews'),
            'site_reviews_summary' => _x('Rating Summary', 'admin-text', 'site-reviews'),
        ]));
    }

    /**
     * @return void
     * @action in_admin_header
     */
    public function renderPageHeader()
    {
        global $post_type_object, $title, $typenow;
        if (!Str::startsWith($typenow, glsr()->post_type)) {
            return;
        }
        $screen = glsr_current_screen();
        glsr()->render('views/partials/page-header', [
            'hasNewButton' => in_array($screen->base, ['edit', 'post']),
            'hasPremiumButton' => !glsr(License::class)->isLicensed(),
            'hasScreenOptions' => in_array($screen->base, ['edit', 'edit-tags']),
            'logo' => file_get_contents(glsr()->path('assets/images/mascot.svg')),
            'newText' => Arr::get($post_type_object, 'labels.add_new'),
            'newUrl' => admin_url('post-new.php?post_type='.$typenow),
            'title' => esc_html($title),
        ]);
    }

    /**
     * @param string $editorId
     * @return void
     * @action media_buttons
     */
    public function renderTinymceButton($editorId)
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
     * @return void
     * @action admin_init
     */
    public function scheduleMigration()
    {
        if ($this->isReviewAdminScreen()
            && !defined('GLSR_UNIT_TESTS')
            && !glsr(Queue::class)->isPending('queue/migration')) {
            if (glsr(Migrate::class)->isMigrationNeeded() || glsr(Database::class)->isMigrationNeeded()) {
                glsr(Queue::class)->once(time() + MINUTE_IN_SECONDS, 'queue/migration');
            }
        }
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/filter-assigned_post
     */
    public function searchAssignedPostsAjax(Request $request)
    {
        $search = glsr(Sanitizer::class)->sanitizeText($request->search);
        $results = glsr(Database::class)->searchAssignedPosts($search)->results();
        wp_send_json_success([
            'items' => $results,
        ]);
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/filter-assigned_user
     */
    public function searchAssignedUsersAjax(Request $request)
    {
        $search = glsr(Sanitizer::class)->sanitizeText($request->search);
        $results = glsr(Database::class)->searchAssignedUsers($search)->results();
        wp_send_json_success([
            'items' => $results,
        ]);
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/filter-author
     */
    public function searchAuthorsAjax(Request $request)
    {
        $search = glsr(Sanitizer::class)->sanitizeText($request->search);
        $results = glsr(Database::class)->searchUsers($search)->results();
        wp_send_json_success([
            'items' => $results,
        ]);
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/search-posts
     */
    public function searchPostsAjax(Request $request)
    {
        $search = glsr(Sanitizer::class)->sanitizeText($request->search);
        $results = glsr(Database::class)->searchPosts($search)->render();
        wp_send_json_success([
            'empty' => '<div>'._x('Nothing found.', 'admin-text', 'site-reviews').'</div>',
            'items' => $results,
        ]);
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/search-strings
     */
    public function searchStringsAjax(Request $request)
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
     * @return void
     * @action site-reviews/route/ajax/search-users
     */
    public function searchUsersAjax(Request $request)
    {
        $search = glsr(Sanitizer::class)->sanitizeText($request->search);
        $results = glsr(Database::class)->searchUsers($search)->render();
        wp_send_json_success([
            'empty' => '<div>'._x('Nothing found.', 'admin-text', 'site-reviews').'</div>',
            'items' => $results,
        ]);
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/toggle-filters
     */
    public function toggleFiltersAjax(Request $request)
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
     * @return void
     * @action site-reviews/route/ajax/toggle-pinned
     */
    public function togglePinnedAjax(Request $request)
    {
        $command = new TogglePinned($request->toArray());
        $result = $this->execute($command);
        glsr()->action('cache/flush', $command->review);
        wp_send_json_success([
            'notices' => glsr(Notice::class)->get(),
            'pinned' => $result,
        ]);
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/toggle-status
     */
    public function toggleStatusAjax(Request $request)
    {
        $result = $this->execute(new ToggleStatus($request->toArray()));
        wp_send_json_success($result);
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/toggle-verified
     */
    public function toggleVerifiedAjax(Request $request)
    {
        $command = new ToggleVerified($request->toArray());
        $result = $this->execute($command);
        glsr()->action('cache/flush', $command->review);
        wp_send_json_success([
            'notices' => glsr(Notice::class)->get(),
            'verified' => $result,
        ]);
    }
}
