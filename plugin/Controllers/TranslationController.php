<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Contracts\ControllerContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\HookProxy;
use GeminiLabs\SiteReviews\Modules\Translation;
use GeminiLabs\SiteReviews\Modules\Translator;

class TranslationController implements ControllerContract
{
    use HookProxy;

    /**
     * @var Translator
     */
    public $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param array[] $messages
     * @param int[]   $counts
     *
     * @return array[]
     *
     * @filter bulk_post_updated_messages
     */
    public function filterBulkUpdateMessages(array $messages, array $counts): array
    {
        $messages[glsr()->post_type] = [
            'updated' => _nx('%s review updated.', '%s reviews updated.', $counts['updated'], 'admin-text', 'site-reviews'),
            'locked' => _nx('%s review not updated, somebody is editing it.', '%s reviews not updated, somebody is editing them.', $counts['locked'], 'admin-text', 'site-reviews'),
            'deleted' => _nx('%s review permanently deleted.', '%s reviews permanently deleted.', $counts['deleted'], 'admin-text', 'site-reviews'),
            'trashed' => _nx('%s review moved to the Trash.', '%s reviews moved to the Trash.', $counts['trashed'], 'admin-text', 'site-reviews'),
            'untrashed' => _nx('%s review restored from the Trash.', '%s reviews restored from the Trash.', $counts['untrashed'], 'admin-text', 'site-reviews'),
        ];
        return $messages;
    }

    /**
     * @filter gettext_{glsr()->id}
     */
    public function filterGettext(string $translation, string $single): string
    {
        return $this->translator->translate($translation, glsr()->id, [
            'single' => $single,
        ]);
    }

    /**
     * @filter gettext_with_context_{glsr()->id}
     */
    public function filterGettextWithContext(string $translation, string $single, string $context): string
    {
        if (str_contains($context, Translation::CONTEXT_ADMIN_KEY)) {
            return $translation;
        }
        return $this->translator->translate($translation, glsr()->id, [
            'context' => $context,
            'single' => $single,
        ]);
    }

    /**
     * @filter ngettext_{glsr()->id}
     */
    public function filterNgettext(string $translation, string $single, string $plural, int $number): string
    {
        return $this->translator->translate($translation, glsr()->id, [
            'number' => $number,
            'plural' => $plural,
            'single' => $single,
        ]);
    }

    /**
     * @filter ngettext_with_context_{glsr()->id}
     */
    public function filterNgettextWithContext(string $translation, string $single, string $plural, int $number, string $context): string
    {
        if (str_contains($context, Translation::CONTEXT_ADMIN_KEY)) {
            return $translation;
        }
        return $this->translator->translate($translation, glsr()->id, [
            'context' => $context,
            'number' => $number,
            'plural' => $plural,
            'single' => $single,
        ]);
    }

    /**
     * @param string[] $states
     *
     * @filter display_post_states
     */
    public function filterPostStates(array $states): array
    {
        if (!$this->canModifyTranslation()) {
            return $states;
        }
        if (array_key_exists('pending', $states)) {
            $states['pending'] = _x('Unapproved', 'admin-text', 'site-reviews');
        }
        return $states;
    }

    /**
     * Used to modify the post status text in the Publish metabox.
     * 
     * @filter gettext_default
     */
    public function filterPostStatusLabels(?string $translation, ?string $single): string
    {
        if (!$this->canModifyTranslation()) {
            return (string) $translation;
        }
        $replacements = [
            'Pending' => _x('Unapproved', 'admin-text', 'site-reviews'),
            'Pending Review' => _x('Unapproved', 'admin-text', 'site-reviews'),
            'Published' => _x('Approved', 'admin-text', 'site-reviews'),
            'Save as Pending' => _x('Save as Unapproved', 'admin-text', 'site-reviews'),
        ];
        return (string) ($replacements[$single] ?? $translation);
    }

    /**
     * @action current_screen
     */
    public function translatePostStatusLabels(): void
    {
        if (!$this->canModifyTranslation()) {
            return;
        }
        global $wp_post_statuses;
        $labels = [
            'pending' => [
                'label' => _x('Unapproved', 'admin-text', 'site-reviews'),
                'label_count' => _x('Unapproved <span class="count">(%s)</span>', 'admin-text', 'site-reviews'),
            ],
            'publish' => [
                'label' => _x('Approved', 'admin-text', 'site-reviews'),
                'label_count' => _x('Approved <span class="count">(%s)</span>', 'admin-text', 'site-reviews'),
            ],
        ];
        foreach ($labels as $key => $values) {
            $wp_post_statuses[$key]->label = $values['label'];
            $wp_post_statuses[$key]->label_count[0] = $values['label_count'];
            $wp_post_statuses[$key]->label_count[1] = $values['label_count'];
            $wp_post_statuses[$key]->label_count['singular'] = $values['label_count'];
            $wp_post_statuses[$key]->label_count['plural'] = $values['label_count'];
        }
    }

    /**
     * @action admin_print_scripts-post.php
     */
    public function translatePostStatusLabelsInScripts(): void
    {
        if (!$this->canModifyTranslation()) {
            return;
        }
        $pattern = '/^([^{]+)(.+)([^}]+)$/';
        $script = Arr::get(wp_scripts(), 'registered.post.extra.data');
        preg_match($pattern, $script, $matches);
        if (4 === count($matches) && $i10n = json_decode($matches[2], true)) {
            $i10n['privatelyPublished'] = _x('Privately Approved', 'admin-text', 'site-reviews');
            $i10n['publish'] = _x('Approve', 'admin-text', 'site-reviews');
            $i10n['published'] = _x('Approved', 'admin-text', 'site-reviews');
            $i10n['publishOn'] = _x('Approve on:', 'admin-text', 'site-reviews');
            $i10n['publishOnPast'] = _x('Approved on:', 'admin-text', 'site-reviews');
            $i10n['savePending'] = _x('Save as Unapproved', 'admin-text', 'site-reviews');
            $script = $matches[1].json_encode($i10n).$matches[3];
            wp_scripts()->registered['post']['extra']['data'] = $script;
        }
    }

    protected function canModifyTranslation(): bool
    {
        $screen = glsr_current_screen();
        return glsr()->post_type === $screen->post_type && in_array($screen->base, ['edit', 'post']);
    }
}
