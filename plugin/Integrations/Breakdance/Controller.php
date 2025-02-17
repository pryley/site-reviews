<?php

namespace GeminiLabs\SiteReviews\Integrations\Breakdance;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\ShortcodeOptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Controller extends AbstractController
{
    /**
     * @action init
     */
    public function registerDesignControls(): void
    {
        $controls = \EssentialElements\Formdesignoptions::designControls();
        $atomV1FormDesign = reset($controls);
        if ('other' === Arr::get($atomV1FormDesign, 'children.5.slug')) {
            unset($atomV1FormDesign['children'][5]);
        }
        \Breakdance\Elements\PresetSections\PresetSectionsController::getInstance()->register(
            "GLSR\\FormDesign",
            $atomV1FormDesign,
            true
        );
    }

    /**
     * Breakdance loads an element by filtering the result of get_declared_classes
     * and checking if the class is_subclass_of \Breakdance\Elements\Element.
     *
     * Controls are added dynamically from the Shortcode class to enforce consistancy
     * across builder integrations. As a result, we need to bypass the Element Studio
     * and we do this by setting the save location to $onlyForAdvancedUsers and
     * $excludeFromElementStudio.
     *
     * @action breakdance_loaded:5
     */
    public function registerElements(): void
    {
        $pluginDir = dirname(plugin_basename(glsr()->file));
        \Breakdance\Elements\registerCategory(glsr()->id, glsr()->name);
        \Breakdance\ElementStudio\registerSaveLocation(
            "{$pluginDir}/assets/breakdance/elements",
            'GLSR_Breakdance',
            'element',
            'Site Reviews Elements',
            true, // onlyForAdvancedUsers
            true // excludeFromElementStudio
        );
        \Breakdance\ElementStudio\registerSaveLocation(
            "{$pluginDir}/assets/breakdance/macros",
            'GLSR_Breakdance',
            'macro',
            'Site Reviews Macros',
            true, // onlyForAdvancedUsers
            true // excludeFromElementStudio
        );
        \Breakdance\ElementStudio\registerSaveLocation(
            "{$pluginDir}/assets/breakdance/presets",
            'GLSR_Breakdance',
            'preset',
            'Site Reviews Presets',
            true, // onlyForAdvancedUsers
            true // excludeFromElementStudio
        );
    }

    /**
     * Unfortunately we can't use this yet because Breakdance does not support
     * AJAX searching in multiselect controls.
     *
     * @action breakdance_loaded
     */
    public function registerRoutes(): void
    {
        $input = filter_input_array(INPUT_POST, [
            'requestData' => [
                'context' => [
                    'filter' => fn ($value) => is_numeric($value)
                        ? intval($value)
                        : filter_var($value, FILTER_SANITIZE_STRING),
                    'flags' => FILTER_REQUIRE_ARRAY,
                ],
            ],
        ]);
        $include = Arr::consolidate($input['requestData']['context'] ?? []);
        \Breakdance\AJAX\register_handler(
            glsr()->prefix.'breakdance_assigned_posts',
            fn () => $this->fetchAssignedPosts($include),
            'edit'
        );
        \Breakdance\AJAX\register_handler(
            glsr()->prefix.'breakdance_assigned_terms',
            fn () => $this->fetchAssignedTerms($include),
            'edit'
        );
        \Breakdance\AJAX\register_handler(
            glsr()->prefix.'breakdance_assigned_users',
            fn () => $this->fetchAssignedUsers($include),
            'edit'
        );
    }

    /**
     * Breakdance does not provide a way to create a multi-select dropdown
     * populated by an AJAX callback on init AND search. To get around this,
     * we use the post_chooser control and override the callback with our own.
     *
     * @action breakdance_ajax_breakdance_get_posts:1
     * @action wp_ajax_breakdance_get_posts:1
     * @action wp_ajax_nopriv_breakdance_get_posts:1
     */
    public function searchAssignedPosts(): void
    {
        if (!$this->verifyRequest('assigned_posts')) {
            return;
        }
        $search = filter_input(INPUT_POST, 'search');
        if (!empty($search)) {
            $results = glsr(ShortcodeOptionManager::class)->assignedPosts([
                'search' => $search,
            ]);
        } else {
            $results = [
                'post_id' => esc_html_x('The Current Page', 'admin-text', 'site-reviews'),
                'parent_id' => esc_html_x('The Parent Page', 'admin-text', 'site-reviews'),
            ] + glsr(Database::class)->posts([
                // @see MainController::parseAssignedPostTypesInQuery
                'post_type' => glsr()->prefix.'assigned_posts',
                'posts_per_page' => 50,
            ]);
        }
        $thumbnailFn = fn ($id) => get_the_post_thumbnail_url($id, 'thumbnail');
        $replacements = [ // the post_chooser control requires integer keys
            'post_id' => -10,
            'parent_id' => -20,
        ];
        $data = $this->prepareResponse($results, $thumbnailFn, $replacements);
        wp_send_json_success($data, 200);
        exit; // @phpstan-ignore-line
    }

    /**
     * Breakdance does not provide a way to create a multi-select dropdown
     * populated by an AJAX callback on init AND search. To get around this,
     * we use the post_chooser control and override the callback with our own.
     *
     * @action breakdance_ajax_breakdance_get_posts:1
     * @action wp_ajax_breakdance_get_posts:1
     * @action wp_ajax_nopriv_breakdance_get_posts:1
     */
    public function searchAssignedTerms(): void
    {
        if (!$this->verifyRequest('assigned_terms')) {
            return;
        }
        $search = filter_input(INPUT_POST, 'search');
        if (!empty($search)) {
            $results = glsr(ShortcodeOptionManager::class)->assignedTerms([
                'search' => $search,
            ]);
        } else {
            $results = glsr(Database::class)->terms([
                'number' => 50,
            ]);
        }
        $data = $this->prepareResponse($results);
        wp_send_json_success($data, 200);
        exit; // @phpstan-ignore-line
    }

    /**
     * Breakdance does not provide a way to create a multi-select dropdown
     * populated by an AJAX callback on init AND search. To get around this,
     * we use the post_chooser control and override the callback with our own.
     *
     * @action breakdance_ajax_breakdance_get_posts:1
     * @action wp_ajax_breakdance_get_posts:1
     * @action wp_ajax_nopriv_breakdance_get_posts:1
     */
    public function searchAssignedUsers(): void
    {
        if (!$this->verifyRequest('assigned_users')) {
            return;
        }
        $search = filter_input(INPUT_POST, 'search');
        if (!empty($search)) {
            $results = glsr(ShortcodeOptionManager::class)->assignedUsers([
                'search' => $search,
            ]);
        } else {
            $results = [
                'user_id' => esc_html_x('The Logged In User', 'admin-text', 'site-reviews'),
                'author_id' => esc_html_x('The Page Author', 'admin-text', 'site-reviews'),
                'profile_id' => esc_html_x('The Profile User', 'admin-text', 'site-reviews'),
            ] + glsr(Database::class)->users([
                'number' => 50,
            ]);
        }
        $thumbnailFn = fn ($id) => get_avatar_url($id);
        $replacements = [ // the post_chooser control requires integer keys
            'user_id' => -10,
            'author_id' => -20,
            'profile_id' => -30,
        ];
        $data = $this->prepareResponse($results, $thumbnailFn, $replacements);
        wp_send_json_success($data, 200);
        exit; // @phpstan-ignore-line
    }

    /**
     * Breakdance does not provide a way to create a dropdown with AJAX search.
     * To get around this, we use the post_chooser control and override the
     * callback with our own.
     *
     * @action breakdance_ajax_breakdance_get_posts:1
     * @action wp_ajax_breakdance_get_posts:1
     * @action wp_ajax_nopriv_breakdance_get_posts:1
     */
    public function searchPostId(): void
    {
        if (!$this->verifyRequest('post_id')) {
            return;
        }
        $search = filter_input(INPUT_POST, 'search');
        if (!empty($search)) {
            $results = glsr(ShortcodeOptionManager::class)->postId([
                'search' => $search,
            ]);
        } else {
            $results = glsr(Database::class)->posts([
                'post_type' => glsr()->post_type,
                'posts_per_page' => 50,
            ]);
        }
        $data = $this->prepareResponse($results);
        array_walk($data, function (&$item) {
            $item['title'] = "{$item['id']}: {$item['title']}";
        });
        wp_send_json_success($data, 200);
        exit; // @phpstan-ignore-line
    }

    /**
     * Unfortunately we can't use this yet because Breakdance does not support
     * AJAX searching in multiselect controls.
     */
    protected function fetchAssignedPosts(array $include): array
    {
        $include = array_filter($include, fn ($id) => is_numeric($id) || in_array($id, [
            'parent_id',
            'post_id',
        ]));
        // ...
        return [];
    }

    /**
     * Unfortunately we can't use this yet because Breakdance does not support
     * AJAX searching in multiselect controls.
     */
    protected function fetchAssignedTerms(array $include): array
    {
        // ...
        return [];
    }

    /**
     * Unfortunately we can't use this yet because Breakdance does not support
     * AJAX searching in multiselect controls.
     */
    protected function fetchAssignedUsers(array $include): array
    {
        $include = array_filter($include, fn ($id) => is_numeric($id) || in_array($id, [
            'author_id',
            'profile_id',
            'user_id',
        ]));
        // ...
        return [];
    }

    protected function prepareResponse(array $results, ?callable $thumbnailFn = null, array $replacements = []): array
    {
        if (!$thumbnailFn) {
            $thumbnailFn = fn () => false;
        }
        $callback = fn ($id, $title) => [
            'id' => $replacements[$id] ?? $id,
            'title' => $title,
            'thumbnail' => $thumbnailFn($id),
        ];
        return array_map($callback, array_keys($results), $results);
    }

    protected function verifyRequest(string $type): bool
    {
        if (glsr()->prefix.$type !== filter_input(INPUT_POST, 'postType')) {
            return false;
        }
        if (!\Breakdance\Permissions\hasMinimumPermission('edit')) {
            return false;
        }
        $nonceTick = check_ajax_referer(\Breakdance\AJAX\get_nonce_key_for_ajax_requests(), false, false);
        if (!$nonceTick) {
            return false;
        }
        if (2 === $nonceTick) {
            $refreshNonce = \Breakdance\AJAX\get_nonce_for_ajax_requests();
            header('Breakdance-Refresh-Nonce:'.$refreshNonce);
        }
        return true;
    }
}
