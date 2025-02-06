<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\RegisterTinymcePopups;
use GeminiLabs\SiteReviews\Database\ShortcodeOptionManager;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Request;

class TinymceController extends AbstractController
{
    /**
     * @filter site-reviews/enqueue/admin/localize
     */
    public function filterAdminVariables(array $variables): array
    {
        $variables['shortcodes'] = [];
        $variables['tinymce'] = [
            'glsr_shortcode' => glsr()->url('assets/scripts/mce-plugin.js'),
        ];
        if (!user_can_richedit()) { // @todo why are we checking this?
            return $variables;
        }
        foreach (glsr()->retrieveAs('array', 'mce', []) as $tag => $args) {
            if (empty($args['required'])) {
                continue;
            }
            $variables['shortcodes'][$tag] = $args['required'];
        }
        return $variables;
    }

    /**
     * @action site-reviews/route/ajax/mce-shortcode
     */
    public function mceShortcodeAjax(Request $request): void
    {
        $shortcode = glsr(Sanitizer::class)->sanitizeText($request->shortcode);
        $response = false;
        if ($data = glsr()->retrieve("mce.{$shortcode}", false)) {
            if (!empty($data['errors'])) {
                $data['btn_okay'] = [esc_attr_x('Okay', 'admin-text', 'site-reviews')];
            }
            $response = [
                'body' => $data['fields'],
                'close' => $data['btn_close'],
                'hideOptions' => glsr(ShortcodeOptionManager::class)->hide(compact('shortcode')),
                'ok' => $data['btn_okay'],
                'shortcode' => $shortcode,
                'title' => $data['title'],
            ];
        }
        wp_send_json_success($response);
    }

    /**
     * @action admin_init
     */
    public function registerTinymcePopups(): void
    {
        $this->execute(new RegisterTinymcePopups());
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
}
