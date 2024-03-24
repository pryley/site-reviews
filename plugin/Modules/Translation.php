<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\Sepia\PoParser\Parser;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class Translation
{
    public const CONTEXT_ADMIN_KEY = 'admin-text';
    public const SEARCH_THRESHOLD = 3;

    protected array $entries = [];

    protected array $results = [];

    /**
     * Returns all saved custom strings with translation context.
     */
    public function all(): array
    {
        $strings = $this->strings();
        $entries = $this->filter($strings, $this->entries())->results();
        array_walk($strings, function (&$entry) use ($entries) {
            $entry['desc'] = array_key_exists($entry['id'], $entries)
                ? $this->getEntryString($entries[$entry['id']], 'msgctxt')
                : '';
        });
        return $strings;
    }

    public function entries(): array
    {
        if (empty($this->entries)) {
            $potFile = glsr()->path(glsr()->languages.'/'.glsr()->id.'.pot');
            $entries = $this->extractEntriesFromPotFile($potFile, glsr()->id);
            $entries = glsr()->filterArray('translation/entries', $entries);
            $this->entries = $entries;
        }
        return $this->entries;
    }

    /**
     * @return static
     */
    public function exclude(?array $entriesToExclude = null, ?array $entries = null)
    {
        return $this->filter($entriesToExclude, $entries, false);
    }

    public function extractEntriesFromPotFile(string $potFile, string $domain, array $entries = []): array
    {
        try {
            $potEntries = $this->normalize(Parser::parseFile($potFile)->getEntries());
            foreach ($potEntries as $key => $entry) {
                if (str_contains(Arr::get($entry, 'msgctxt'), static::CONTEXT_ADMIN_KEY)) {
                    continue;
                }
                $entry['domain'] = $domain; // the text-domain of the entry
                $entries[html_entity_decode($key, ENT_COMPAT, 'UTF-8')] = $entry;
            }
        } catch (\Exception $e) {
            glsr_log()->error($e->getMessage());
        }
        return $entries;
    }

    /**
     * @return static
     */
    public function filter(?array $filterWith = null, ?array $entries = null, bool $intersect = true)
    {
        if (!is_array($entries)) {
            $entries = $this->results;
        }
        if (!is_array($filterWith)) {
            $filterWith = $this->strings();
        }
        $keys = array_flip(wp_list_pluck($filterWith, 'id'));
        $this->results = $intersect
            ? array_intersect_key($entries, $keys)
            : array_diff_key($entries, $keys);
        return $this;
    }

    public function isInvalid(array $entry): bool
    {
        return !empty($entry['s1']) && (
            false === Arr::searchByKey($entry['s1'], $this->entries(), 'msgid')
                && false === Arr::searchByKey(htmlentities2($entry['s1']), $this->entries(), 'msgid')
        );
    }

    public function render(string $template, array $entry): string
    {
        $data = array_combine(array_map(fn ($key) => "data.{$key}", array_keys($entry)), $entry);
        $data['data.class'] = '';
        $data['data.error'] = '';
        if ($this->isInvalid($entry)) {
            $data['data.class'] = 'is-invalid';
            $data['data.error'] = _x('This custom translation is no longer valid as the original text has been changed or removed.', 'admin-text', 'site-reviews');
        }
        return glsr(Template::class)->build("partials/strings/{$template}", [
            'context' => array_map('esc_html', $data),
        ]);
    }

    /**
     * Returns a rendered string of all saved custom strings with translation context.
     */
    public function renderAll(): string
    {
        $rendered = '';
        foreach ($this->all() as $index => $entry) {
            $entry['index'] = $index;
            $entry['prefix'] = OptionManager::databaseKey();
            $rendered .= $this->render($entry['type'], $entry);
        }
        return $rendered;
    }

    public function renderResults(bool $resetAfterRender = true): string
    {
        $rendered = '';
        foreach ($this->results as $id => $entry) {
            $data = [
                'desc' => $this->getEntryString($entry, 'msgctxt'),
                'id' => $id,
                'p1' => $this->getEntryString($entry, 'msgid_plural'),
                's1' => $this->getEntryString($entry, 'msgid'),
            ];
            $text = !empty($data['p1'])
                ? sprintf('%s | %s', $data['s1'], $data['p1'])
                : $data['s1'];
            $rendered .= $this->render('result', [
                'domain' => $this->getEntryString($entry, 'domain'),
                'entry' => wp_json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'text' => wp_strip_all_tags($text),
            ]);
        }
        if ($resetAfterRender) {
            $this->reset();
        }
        return $rendered;
    }

    public function reset(): void
    {
        $this->results = [];
    }

    public function results(): array
    {
        $results = $this->results;
        $this->reset();
        return $results;
    }

    /**
     * @return static
     */
    public function search(string $needle = '')
    {
        $this->reset();
        $needle = trim(strtolower($needle));
        foreach ($this->entries() as $key => $entry) {
            $single = strtolower($this->getEntryString($entry, 'msgid'));
            $plural = strtolower($this->getEntryString($entry, 'msgid_plural'));
            if (strlen($needle) < static::SEARCH_THRESHOLD) {
                if (in_array($needle, [$single, $plural])) {
                    $this->results[$key] = $entry;
                }
            } elseif (str_contains(sprintf('%s %s', $single, $plural), $needle)) {
                $this->results[$key] = $entry;
            }
        }
        return $this;
    }

    /**
     * Store the strings to avoid unnecessary loops.
     */
    public function strings(): array
    {
        static $strings;
        if (empty($strings)) {
            // we need to bypass the filter hooks because this is run before the settings are initiated
            $settings = get_option(OptionManager::databaseKey());
            $strings = Arr::getAs('array', $settings, 'settings.strings');
            $strings = $this->normalizeStrings($strings);
        }
        return $strings;
    }

    protected function getEntryString(array $entry, string $key): string
    {
        return isset($entry[$key])
            ? implode('', (array) $entry[$key])
            : '';
    }

    protected function normalize(array $entries): array
    {
        $keys = [
            'msgctxt', 'msgid', 'msgid_plural', 'msgstr', 'msgstr[0]', 'msgstr[1]',
        ];
        array_walk($entries, function (&$entry) use ($keys) {
            foreach ($keys as $key) {
                $entry = $this->normalizeEntryString($entry, $key);
            }
        });
        return $entries;
    }

    protected function normalizeEntryString(array $entry, string $key): array
    {
        if (isset($entry[$key])) {
            $entry[$key] = $this->getEntryString($entry, $key);
        }
        return $entry;
    }

    protected function normalizeStrings(array $strings): array
    {
        $defaultString = array_fill_keys(['id', 's1', 's2', 'p1', 'p2'], '');
        $strings = array_filter($strings, 'is_array');
        foreach ($strings as &$string) {
            $string['type'] = isset($string['p1']) ? 'plural' : 'single';
            $string = wp_parse_args($string, $defaultString);
        }
        return array_filter($strings, fn ($string) => !empty($string['id']));
    }
}
