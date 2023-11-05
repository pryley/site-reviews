<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Defaults\PostStatusLabelsDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Translation;
use GeminiLabs\SiteReviews\Modules\Translator;

class TranslationController
{
    /**
     * @var Translator
     */
    public $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param array $messages
     * @filter bulk_post_updated_messages
     */
    public function filterBulkUpdateMessages($messages, array $counts): array
    {
        $messages = Arr::consolidate($messages);
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
     * Used to ensure that System Info is in English
     * @param string $translation
     * @param string $single
     * @return string
     * @filter gettext_default
     */
    public function filterEnglishTranslation($translation, $single)
    {
        return $single;
    }

    /**
     * @param string $translation
     * @param string $single
     * @filter gettext_{glsr()->id}
     */
    public function filterGettext($translation, $single)
    {
        $translation = Cast::toString($translation);
        return $this->translator->translate($translation, glsr()->id, [
            'single' => Cast::toString($single),
        ]);
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $context
     * @filter gettext_with_context_{glsr()->id}
     */
    public function filterGettextWithContext($translation, $single, $context): string
    {
        $context = Cast::toString($context);
        $translation = Cast::toString($translation);
        if (str_contains($context, Translation::CONTEXT_ADMIN_KEY)) {
            return $translation;
        }
        return $this->translator->translate($translation, glsr()->id, [
            'context' => $context,
            'single' => Cast::toString($single),
        ]);
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $plural
     * @param int $number
     * @filter ngettext_{glsr()->id}
     */
    public function filterNgettext($translation, $single, $plural, $number): string
    {
        $translation = Cast::toString($translation);
        return $this->translator->translate($translation, glsr()->id, [
            'number' => Cast::toInt($number),
            'plural' => Cast::toString($plural),
            'single' => Cast::toString($single),
        ]);
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $plural
     * @param int $number
     * @param string $context
     * @filter ngettext_with_context_{glsr()->id}
     */
    public function filterNgettextWithContext($translation, $single, $plural, $number, $context): string
    {
        $context = Cast::toString($context);
        $translation = Cast::toString($translation);
        if (str_contains($context, Translation::CONTEXT_ADMIN_KEY)) {
            return $translation;
        }
        return $this->translator->translate($translation, glsr()->id, [
            'context' => $context,
            'number' => Cast::toInt($number),
            'plural' => Cast::toString($plural),
            'single' => Cast::toString($single),
        ]);
    }

    /**
     * @param array $states
     * @param \WP_Post $post
     * @filter display_post_states
     */
    public function filterPostStates($states, $post): array
    {
        $states = Arr::consolidate($states);
        if (get_post_type($post) === glsr()->post_type && array_key_exists('pending', $states)) {
            $states['pending'] = _x('Unapproved', 'admin-text', 'site-reviews');
        }
        return $states;
    }

    /**
     * @param string $translation
     * @param string $single
     * @filter gettext_default
     */
    public function filterPostStatusLabels($translation, $single): string
    {
        $translation = Cast::toString($translation);
        if ($this->canModifyTranslation()) {
            $replacements = $this->statusLabels();
            if (array_key_exists($single, $replacements)) {
                return $replacements[$single];
            }
        }
        return $translation;
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $plural
     * @param int $number
     * @filter ngettext_default
     */
    public function filterPostStatusText($translation, $single, $plural, $number): string
    {
        if ($this->canModifyTranslation()) {
            $strings = [
                'Published' => _x('Approved', 'admin-text', 'site-reviews'),
                'Pending' => _x('Unapproved', 'admin-text', 'site-reviews'),
            ];
            $single = Cast::toString($single);
            $plural = Cast::toString($plural);
            foreach ($strings as $search => $replace) {
                if (!str_contains($single, $search)) {
                    continue;
                }
                return $this->translator->getTranslation([
                    'number' => Cast::toInt($number),
                    'plural' => str_replace($search, $replace, $plural),
                    'single' => str_replace($search, $replace, $single),
                ]);
            }
        }
        return Cast::toString($translation);
    }

    /**
     * @action admin_print_scripts-post.php
     */
    public function translatePostStatusLabels(): void
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

    /**
     * Store the labels to avoid unnecessary loops.
     */
    protected function statusLabels(): array
    {
        static $labels;
        if (empty($labels)) {
            $labels = glsr(PostStatusLabelsDefaults::class)->defaults();
        }
        return $labels;
    }
}
