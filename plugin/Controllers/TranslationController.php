<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Controllers\EditorController\Labels;
use GeminiLabs\SiteReviews\Helpers\Arr;
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
     * @return void
     * @action plugins_loaded
     */
    public function addTranslationFilters()
    {
        if (empty(glsr(Translation::class)->translations())) {
            return;
        }
        add_filter('gettext',                                         [$this, 'filterGettext'], 9, 3);
        add_filter('site-reviews/gettext/site-reviews',               [$this, 'filterGettextSiteReviews'], 10, 2);
        add_filter('gettext_with_context',                            [$this, 'filterGettextWithContext'], 9, 4);
        add_filter('site-reviews/gettext_with_context/site-reviews',  [$this, 'filterGettextWithContextSiteReviews'], 10, 3);
        add_filter('ngettext',                                        [$this, 'filterNgettext'], 9, 5);
        add_filter('site-reviews/ngettext/site-reviews',              [$this, 'filterNgettextSiteReviews'], 10, 4);
        add_filter('ngettext_with_context',                           [$this, 'filterNgettextWithContext'], 9, 6);
        add_filter('site-reviews/ngettext_with_context/site-reviews', [$this, 'filterNgettextWithContextSiteReviews'], 10, 5);
    }

    /**
     * @param array $messages
     * @return array
     * @filter bulk_post_updated_messages
     */
    public function filterBulkUpdateMessages($messages, array $counts)
    {
        $messages = Arr::consolidateArray($messages);
        $messages[Application::POST_TYPE] = [
            'updated' => _n('%s review updated.', '%s reviews updated.', $counts['updated'], 'site-reviews'),
            'locked' => _n('%s review not updated, somebody is editing it.', '%s reviews not updated, somebody is editing them.', $counts['locked'], 'site-reviews'),
            'deleted' => _n('%s review permanently deleted.', '%s reviews permanently deleted.', $counts['deleted'], 'site-reviews'),
            'trashed' => _n('%s review moved to the Trash.', '%s reviews moved to the Trash.', $counts['trashed'], 'site-reviews'),
            'untrashed' => _n('%s review restored from the Trash.', '%s reviews restored from the Trash.', $counts['untrashed'], 'site-reviews'),
        ];
        return $messages;
    }

    /**
     * @param string $translation
     * @param string $text
     * @param string $domain
     * @return string
     * @filter gettext
     */
    public function filterGettext($translation, $text, $domain)
    {
        return apply_filters('site-reviews/gettext/'.$domain, $translation, $text);
    }

    /**
     * @param string $translation
     * @param string $text
     * @return string
     * @filter site-reviews/gettext/site-reviews
     */
    public function filterGettextSiteReviews($translation, $text)
    {
        return $this->translator->translate($translation, Application::ID, [
            'single' => $text,
        ]);
    }

    /**
     * @param string $translation
     * @param string $text
     * @param string $context
     * @param string $domain
     * @return string
     * @filter gettext_with_context
     */
    public function filterGettextWithContext($translation, $text, $context, $domain)
    {
        return apply_filters('site-reviews/gettext_with_context/'.$domain, $translation, $text, $context);
    }

    /**
     * @param string $translation
     * @param string $text
     * @param string $context
     * @return string
     * @filter site-reviews/gettext_with_context/site-reviews
     */
    public function filterGettextWithContextSiteReviews($translation, $text, $context)
    {
        return $this->translator->translate($translation, Application::ID, [
            'context' => $context,
            'single' => $text,
        ]);
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $plural
     * @param int $number
     * @param string $domain
     * @return string
     * @filter ngettext
     */
    public function filterNgettext($translation, $single, $plural, $number, $domain)
    {
        return apply_filters('site-reviews/ngettext/'.$domain, $translation, $single, $plural, $number);
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $plural
     * @param int $number
     * @return string
     * @filter site-reviews/ngettext/site-reviews
     */
    public function filterNgettextSiteReviews($translation, $single, $plural, $number)
    {
        return $this->translator->translate($translation, Application::ID, [
            'number' => $number,
            'plural' => $plural,
            'single' => $single,
        ]);
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $plural
     * @param int $number
     * @param string $context
     * @param string $domain
     * @return string
     * @filter ngettext_with_context
     */
    public function filterNgettextWithContext($translation, $single, $plural, $number, $context, $domain)
    {
        return apply_filters('site-reviews/ngettext_with_context/'.$domain, $translation, $single, $plural, $number, $context);
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $plural
     * @param int $number
     * @param string $context
     * @return string
     * @filter site-reviews/ngettext_with_context/site-reviews
     */
    public function filterNgettextWithContextSiteReviews($translation, $single, $plural, $number, $context)
    {
        return $this->translator->translate($translation, Application::ID, [
            'context' => $context,
            'number' => $number,
            'plural' => $plural,
            'single' => $single,
        ]);
    }

    /**
     * @param array $postStates
     * @param \WP_Post $post
     * @return array
     * @filter display_post_states
     */
    public function filterPostStates($postStates, $post)
    {
        $postStates = Arr::consolidateArray($postStates);
        if (Application::POST_TYPE == Arr::get($post, 'post_type') && array_key_exists('pending', $postStates)) {
            $postStates['pending'] = __('Unapproved', 'site-reviews');
        }
        return $postStates;
    }

    /**
     * @param string $translation
     * @param string $text
     * @return string
     * @filter site-reviews/gettext/default
     * @filter site-reviews/gettext_with_context/default
     */
    public function filterPostStatusLabels($translation, $text)
    {
        return $this->canModifyTranslation()
            ? glsr(Labels::class)->filterPostStatusLabels($translation, $text)
            : $translation;
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $plural
     * @param int $number
     * @return string
     * @filter site-reviews/ngettext/default
     */
    public function filterPostStatusText($translation, $single, $plural, $number)
    {
        if ($this->canModifyTranslation()) {
            $strings = [
                'Published' => __('Approved', 'site-reviews'),
                'Pending' => __('Unapproved', 'site-reviews'),
            ];
            foreach ($strings as $search => $replace) {
                if (!Str::contains($single, $search)) {
                    continue;
                }
                return $this->translator->getTranslation([
                    'number' => $number,
                    'plural' => str_replace($search, $replace, $plural),
                    'single' => str_replace($search, $replace, $single),
                ]);
            }
        }
        return $translation;
    }

    /**
     * @return void
     * @action admin_enqueue_scripts
     */
    public function translatePostStatusLabels()
    {
        if ($this->canModifyTranslation()) {
            glsr(Labels::class)->translatePostStatusLabels();
        }
    }

    /**
     * @return bool
     */
    protected function canModifyTranslation()
    {
        $screen = glsr_current_screen();
        return Application::POST_TYPE == $screen->post_type 
            && in_array($screen->base, ['edit', 'post']);
    }
}
