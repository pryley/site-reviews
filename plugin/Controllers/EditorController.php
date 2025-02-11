<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnValueType;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\UpdatedMessageDefaults;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

class EditorController extends AbstractController
{
    /**
     * @filter wp_editor_settings
     */
    public function filterEditorSettings(array $settings): array
    {
        if ($this->isReviewEditor()) {
            $settings = [
                'media_buttons' => false,
                'quicktags' => false,
                'textarea_rows' => 12,
                'tinymce' => false,
            ];
        }
        return $settings;
    }

    /**
     * Modify the WP_Editor html to allow autosizing without breaking the `editor-expand` script.
     *
     * @filter the_editor
     */
    public function filterEditorTextarea(?string $output): string
    {
        $output = (string) $output;
        if ($this->isReviewEditor()) {
            $output = str_replace('<textarea', '<div id="ed_toolbar"></div><textarea', $output);
        }
        return $output;
    }

    /**
     * @filter is_protected_meta
     */
    public function filterIsProtectedMeta(bool $protected, string $metaKey, string $metaType): bool
    {
        if ('post' === $metaType && Str::startsWith($metaKey, ['_custom_', '_'.glsr()->prefix])) {
            if ('delete-meta' === filter_input(INPUT_POST, 'action')) {
                return false; // allow delete but not update
            }
            if (glsr()->post_type === get_post_type()) {
                return false; // display the field in the Custom Fields metabox
            }
        }
        return $protected;
    }

    /**
     * @param array[] $messages
     *
     * @filter post_updated_messages
     */
    public function filterUpdateMessages(array $messages): array
    {
        $post = get_post();
        if (!$post instanceof \WP_Post) {
            return $messages;
        }
        $strings = glsr(UpdatedMessageDefaults::class)->defaults();
        $restored = filter_input(INPUT_GET, 'revision');
        if ($revisionTitle = wp_post_revision_title(intval($restored), false)) {
            $restored = sprintf($strings['restored'], $revisionTitle);
        }
        $scheduled_date = date_i18n('M j, Y @ H:i', strtotime($post->post_date));
        $messages[glsr()->post_type] = [
            1 => $strings['updated'],
            4 => $strings['updated'],
            5 => $restored,
            6 => $strings['published'],
            7 => $strings['saved'],
            8 => $strings['submitted'],
            9 => sprintf($strings['scheduled'], "<strong>{$scheduled_date}</strong>"),
            10 => $strings['draft_updated'],
            50 => $strings['approved'],
            51 => $strings['unapproved'],
            52 => $strings['reverted'],
        ];
        return $messages;
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
                'ok' => $data['btn_okay'],
                'shortcode' => $shortcode,
                'title' => $data['title'],
            ];
        }
        wp_send_json_success($response);
    }

    /**
     * @action edit_form_top
     */
    public function renderReviewNotice(\WP_Post $post): void
    {
        if (!$this->isReviewEditor()) {
            return;
        }
        if (Review::isReview($post) && !Review::isEditable($post)) {
            glsr(Notice::class)->addWarning(sprintf(
                _x('Publicly responding to third-party %s reviews is disabled.', 'admin-text', 'site-reviews'),
                glsr(ColumnValueType::class)->handle(glsr(ReviewManager::class)->get($post->ID))
            ));
            glsr(Template::class)->render('partials/editor/notice', [
                'context' => [
                    'notices' => glsr(Notice::class)->get(),
                ],
            ]);
        }
    }
}
