<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class ValidateSettings extends AbstractCommand
{
    public array $options;
    protected array $input;

    /**
     * @param array $options the saved settings merged with the posted values
     * @param array $input   the raw post
     */
    public function __construct(array $options, array $input)
    {
        $this->input = $input;
        $this->options = $options;
    }

    public function handle(): void
    {
        $this->normalizeMessageFallbacks();
        $this->normalizeMultiValues();
        $this->sanitizeSettings();
        $this->sanitizeStrings();
    }

    protected function missingPlaceholders(array $string): array
    {
        $missing = [];
        foreach (['%d', '%s'] as $needle) {
            foreach (['s', 'p'] as $form) { // singular and plural
                $original = $string[$form.'1'] ?? '';
                $custom = $string[$form.'2'] ?? '';
                if (str_contains($original, $needle) && !str_contains($custom, $needle)) {
                    $missing[$needle][] = $custom;
                }
            }
        }
        return $missing;
    }

    /**
     * Restore the default message when a posted message is empty.
     */
    protected function normalizeMessageFallbacks(): void
    {
        $general = $this->posted('settings.general');
        if (empty($general)) {
            return;
        }
        $defaults = glsr()->defaults()['settings']['general'];
        foreach (['notification_message', 'request_verification_message'] as $message) {
            if ('' === trim($general[$message] ?? '')) {
                $this->options['settings']['general'][$message] = $defaults[$message];
            }
        }
    }

    /**
     * Ensure empty array fields are always saved as an array.
     */
    protected function normalizeMultiValues(): void
    {
        foreach (glsr()->settings() as $key => $field) {
            if ('checkbox' !== ($field['type'] ?? '') && !str_starts_with($field['sanitizer'], 'array-')) {
                continue;
            }
            $section = substr($key, 0, (int) strrpos($key, '.'));
            if (empty($this->posted($section))) {
                continue;
            }
            $this->options = Arr::set($this->options, $key, Arr::get($this->input, $key, []));
        }
    }

    /**
     * The posted values of a section, or an empty array when the section's tab was not saved.
     */
    protected function posted(string $section): array
    {
        return Arr::consolidate(Arr::get($this->input, $section));
    }

    /**
     * Sanitize every setting with the sanitizer named for it in config/settings.php.
     */
    protected function sanitizeSettings(): void
    {
        $values = Arr::flatten($this->options);
        $sanitizers = wp_list_pluck(glsr()->settings(), 'sanitizer');
        $options = (new Sanitizer($values, $sanitizers))->run();
        $this->options = Arr::unflatten($options);
    }

    /**
     * Sanitize the Custom Text strings and display a notice for missing string placeholders.
     */
    protected function sanitizeStrings(): void
    {
        $strings = $this->posted('settings.strings');
        if (empty($strings)) {
            return;
        }
        $allowedTags = [
            'a' => ['class' => [], 'href' => [], 'target' => []],
            'span' => ['class' => []],
        ];
        $missing = [];
        $sanitized = [];
        foreach ($strings as $string) {
            if (!isset($string['s1'])) {
                continue; // all entries should have this key
            }
            $string['s2'] = wp_kses($string['s2'] ?? '', $allowedTags);
            if (isset($string['p1'])) {
                $string['p2'] = wp_kses($string['p2'] ?? '', $allowedTags);
            }
            $missing = array_merge_recursive($missing, $this->missingPlaceholders($string));
            $sanitized[] = $string;
        }
        foreach ($missing as $needle => $values) {
            glsr(Notice::class)->addError(
                /* translators: %s: the missing placeholder tag */
                sprintf(_x('You forgot to include the %s placeholder tags in your Custom Text.', 'admin-text', 'site-reviews'), "<code>{$needle}</code>"),
                $values
            );
        }
        $this->options['settings']['strings'] = $sanitized;
    }
}
