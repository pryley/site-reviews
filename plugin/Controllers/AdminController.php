<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\EnqueueAdminAssets;
use GeminiLabs\SiteReviews\Commands\RegisterTinymcePopups;
use GeminiLabs\SiteReviews\Database\CountsManager;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Migrate;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\System;
use GeminiLabs\SiteReviews\Role;

class AdminController extends Controller
{
    /**
     * @return void
     * @action admin_enqueue_scripts
     */
    public function enqueueAssets()
    {
        $command = new EnqueueAdminAssets([
            'pointers' => [[
                'content' => __('You can pin exceptional reviews so that they are always shown first.', 'site-reviews'),
                'id' => 'glsr-pointer-pinned',
                'position' => [
                    'edge' => 'right',
                    'align' => 'middle',
                ],
                'screen' => Application::POST_TYPE,
                'target' => '#misc-pub-pinned',
                'title' => __('Pin Your Reviews', 'site-reviews'),
            ]],
        ]);
        $this->execute($command);
    }

    /**
     * @return array
     * @filter plugin_action_links_site-reviews/site-reviews.php
     */
    public function filterActionLinks(array $links)
    {
        $links['documentation'] = glsr(Builder::class)->a(__('Help', 'site-reviews'), [
            'href' => admin_url('edit.php?post_type='.Application::POST_TYPE.'&page=documentation'),
        ]);
        $links['settings'] = glsr(Builder::class)->a(__('Settings', 'site-reviews'), [
            'href' => admin_url('edit.php?post_type='.Application::POST_TYPE.'&page=settings'),
        ]);
        return $links;
    }

    /**
     * @param array $capabilities
     * @param string $capability
     * @return array
     * @filter map_meta_cap
     */
    public function filterCreateCapability($capabilities, $capability)
    {
        if ($capability == 'create_'.Application::POST_TYPE) {
            $capabilities[] = 'do_not_allow';
        }
        return $capabilities;
    }

    /**
     * @param array $items
     * @return array
     * @filter dashboard_glance_items
     */
    public function filterDashboardGlanceItems($items)
    {
        $postCount = wp_count_posts(Application::POST_TYPE);
        if (empty($postCount->publish)) {
            return $items;
        }
        $text = _n('%s Review', '%s Reviews', $postCount->publish, 'site-reviews');
        $text = sprintf($text, number_format_i18n($postCount->publish));
        $items = Arr::consolidateArray($items);
        $items[] = glsr()->can('edit_posts')
            ? glsr(Builder::class)->a($text, [
                'class' => 'glsr-review-count',
                'href' => 'edit.php?post_type='.Application::POST_TYPE,
            ])
            : glsr(Builder::class)->span($text, [
                'class' => 'glsr-review-count',
            ]);
        return $items;
    }

    /**
     * @param array $plugins
     * @return array
     * @filter mce_external_plugins
     */
    public function filterTinymcePlugins($plugins)
    {
        if (glsr()->can('edit_posts')) {
            $plugins = Arr::consolidateArray($plugins);
            $plugins['glsr_shortcode'] = glsr()->url('assets/scripts/mce-plugin.js');
        }
        return $plugins;
    }

    /**
     * @return void
     * @action admin_init
     */
    public function registerTinymcePopups()
    {
        $command = new RegisterTinymcePopups([
            'site_reviews' => esc_html__('Recent Reviews', 'site-reviews'),
            'site_reviews_form' => esc_html__('Submit a Review', 'site-reviews'),
            'site_reviews_summary' => esc_html__('Summary of Reviews', 'site-reviews'),
        ]);
        $this->execute($command);
    }

    /**
     * @param string $editorId
     * @return void|null
     * @action media_buttons
     */
    public function renderTinymceButton($editorId)
    {
        $allowedEditors = apply_filters('site-reviews/tinymce/editor-ids', ['content'], $editorId);
        if ('post' != glsr_current_screen()->base || !in_array($editorId, $allowedEditors)) {
            return;
        }
        $shortcodes = [];
        foreach (glsr()->mceShortcodes as $shortcode => $values) {
            $shortcodes[$shortcode] = $values;
        }
        if (empty($shortcodes)) {
            return;
        }
        glsr()->render('partials/editor/tinymce', [
            'shortcodes' => $shortcodes,
        ]);
    }

    /**
     * @return void
     */
    public function routerClearConsole()
    {
        glsr(Console::class)->clear();
        glsr(Notice::class)->addSuccess(__('Console cleared.', 'site-reviews'));
    }

    /**
     * @return void
     */
    public function routerCountReviews()
    {
        glsr(CountsManager::class)->updateAll();
        glsr(Notice::class)->clear()->addSuccess(__('Recalculated rating counts.', 'site-reviews'));
    }

    /**
     * @return void
     */
    public function routerDownloadConsole()
    {
        $this->download(Application::ID.'-console.txt', glsr(Console::class)->get());
    }

    /**
     * @return void
     */
    public function routerDownloadSystemInfo()
    {
        $this->download(Application::ID.'-system-info.txt', glsr(System::class)->get());
    }

    /**
     * @return void
     */
    public function routerExportSettings()
    {
        $this->download(Application::ID.'-settings.json', glsr(OptionManager::class)->json());
    }

    /**
     * @return void
     */
    public function routerFetchConsole()
    {
        glsr(Notice::class)->addSuccess(__('Console reloaded.', 'site-reviews'));
    }

    /**
     * @return void
     */
    public function routerImportSettings()
    {
        $file = $_FILES['import-file'];
        if (UPLOAD_ERR_OK !== $file['error']) {
            return glsr(Notice::class)->addError($this->getUploadError($file['error']));
        }
        if ('application/json' !== $file['type'] || !Str::endsWith('.json', $file['name'])) {
            return glsr(Notice::class)->addError(__('Please use a valid Site Reviews settings file.', 'site-reviews'));
        }
        $settings = json_decode(file_get_contents($file['tmp_name']), true);
        if (empty($settings)) {
            return glsr(Notice::class)->addWarning(__('There were no settings found to import.', 'site-reviews'));
        }
        glsr(OptionManager::class)->set(glsr(OptionManager::class)->normalize($settings));
        glsr(Notice::class)->addSuccess(__('Settings imported.', 'site-reviews'));
    }

    /**
     * @return void
     */
    public function routerMigrateReviews()
    {
        glsr(Migrate::class)->runAll();
        glsr(Notice::class)->clear()->addSuccess(__('The plugin has been migrated to the latest version.', 'site-reviews'));
    }

    /**
     * @return void
     */
    public function routerResetPermissions()
    {
        glsr(Role::class)->resetAll();
        glsr(Notice::class)->clear()->addSuccess(__('The permissions have been reset.', 'site-reviews'));
    }

    /**
     * @return void
     * @action admin_init
     */
    public function runMigrations()
    {
        if (glsr(Migrate::class)->isMigrationNeeded()) {
            glsr(Migrate::class)->run();
            glsr(CountsManager::class)->updateAll();
        }
    }

    /**
     * @param int $errorCode
     * @return string
     */
    protected function getUploadError($errorCode)
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE => __('The uploaded file exceeds the upload_max_filesize directive in php.ini.', 'site-reviews'),
            UPLOAD_ERR_FORM_SIZE => __('The uploaded file is too big.', 'site-reviews'),
            UPLOAD_ERR_PARTIAL => __('The uploaded file was only partially uploaded.', 'site-reviews'),
            UPLOAD_ERR_NO_FILE => __('No file was uploaded.', 'site-reviews'),
            UPLOAD_ERR_NO_TMP_DIR => __('Missing a temporary folder.', 'site-reviews'),
            UPLOAD_ERR_CANT_WRITE => __('Failed to write file to disk.', 'site-reviews'),
            UPLOAD_ERR_EXTENSION => __('A PHP extension stopped the file upload.', 'site-reviews'),
        ];
        return Arr::get($errors, $errorCode, __('Unknown upload error.', 'site-reviews'));
    }
}
