<?php

namespace GeminiLabs\SiteReviews\Integrations\Breakdance;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\ShortcodeOptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Helpers\Svg;

class Controller extends AbstractController
{
    /**
     * Breakdance does not provide a way to create a multi-select dropdown
     * populated by an AJAX callback on init AND search. To get around this,
     * we use the post_chooser control and override the callback with our own.
     *
     * @action breakdance_ajax_breakdance_get_posts:1
     * @action wp_ajax_breakdance_get_posts:1
     * @action wp_ajax_nopriv_breakdance_get_posts:1
     */
    public function interceptGetPostsQuery(): void
    {
        if (!$this->verifyRequest()) {
            return;
        }
        $option = Str::removePrefix(filter_input(INPUT_POST, 'postType'), glsr()->prefix);
        $search = filter_input(INPUT_POST, 'search');
        $params = compact('option', 'search');
        $results = call_user_func([glsr(ShortcodeOptionManager::class), $option], $params);
        $replacements = [ // the post_chooser control requires integer keys
            'post_id' => -10,
            'parent_id' => -20,
            'user_id' => -30,
            'author_id' => -40,
            'profile_id' => -50,
        ];
        if (in_array($option, ['assigned_posts'])) {
            $thumbnailFn = fn ($id) => get_the_post_thumbnail_url($id, 'thumbnail');
        } elseif (in_array($option, ['assigned_users', 'author'])) {
            $thumbnailFn = fn ($id) => get_avatar_url($id);
        }
        $data = $this->prepareResponse($results, $thumbnailFn ?? null, $replacements);
        if (in_array($option, ['post_id'])) {
            array_walk($data, function (&$item) {
                $item['title'] = "{$item['id']}: {$item['title']}";
            });
        }
        wp_send_json_success($data, 200);
        exit; // @phpstan-ignore-line
    }

    /**
     * @action unofficial_i_am_kevin_geary_master_of_all_things_css_and_html
     */
    public function printInlineStyles(): void
    {
        $icon = Svg::encoded('assets/images/icon-static.svg');
        echo '<style>'.
            '.breakdance-control-wrapper:has(+ .breakdance-control-wrapper [data-test-id$="_description_alert"] .breakdance-alert-box) {'.
                'margin-bottom: 7px;'.
            '}'.
            '[data-test-id$="_description_alert"] .breakdance-alert-box {'.
                'padding-inline: 13px;',
                '.v-alert__border--left {'.
                    'display: none;'.
                '}',
            '}',
            '.breakdance-add-panel__element:has(.glsr-icon) .breakdance-element-badge {'.
                'padding: 0;'.
                '&::after {'.
                    'background-image: url("'.$icon.'");'.
                    'content: "";'.
                    'height: var(--text-lg);'.
                    'width: var(--text-lg);'.
                '}'.
            '}'.
        '</style>';
    }

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
        return; // We can't use this yet...
        $input = filter_input_array(INPUT_POST, [ // @phpstan-ignore-line
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
        \Breakdance\AJAX\register_handler(
            glsr()->prefix.'breakdance_author',
            fn () => $this->fetchAuthor($include),
            'edit'
        );
        \Breakdance\AJAX\register_handler(
            glsr()->prefix.'breakdance_post_id',
            fn () => $this->fetchPostId($include),
            'edit'
        );
    }

    /**
     * Unfortunately we can't use this yet because Breakdance does not support
     * AJAX searching in multiselect controls.
     */
    protected function fetchAssignedPosts(array $include): array
    {
        // $include = array_filter($include, fn ($id) => is_numeric($id) || in_array($id, [
        //     'parent_id',
        //     'post_id',
        // ]));
        return [];
    }

    /**
     * Unfortunately we can't use this yet because Breakdance does not support
     * AJAX searching in multiselect controls.
     */
    protected function fetchAssignedTerms(array $include): array
    {
        return [];
    }

    /**
     * Unfortunately we can't use this yet because Breakdance does not support
     * AJAX searching in multiselect controls.
     */
    protected function fetchAssignedUsers(array $include): array
    {
        // $include = array_filter($include, fn ($id) => is_numeric($id) || in_array($id, [
        //     'author_id',
        //     'profile_id',
        //     'user_id',
        // ]));
        return [];
    }

    /**
     * Unfortunately we can't use this yet because Breakdance does not support
     * AJAX searching in multiselect controls.
     */
    protected function fetchAuthor(array $include): array
    {
        // $include = array_filter($include, fn ($id) => is_numeric($id) || in_array($id, [
        //     'user_id',
        // ]));
        return [];
    }

    /**
     * Unfortunately we can't use this yet because Breakdance does not support
     * AJAX searching in multiselect controls.
     */
    protected function fetchPostId(array $include): array
    {
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

    protected function verifyRequest(): bool
    {
        if (!str_starts_with((string) filter_input(INPUT_POST, 'postType'), glsr()->prefix)) {
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
