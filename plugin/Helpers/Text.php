<?php

namespace GeminiLabs\SiteReviews\Helpers;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Sanitizers\SanitizeTextHtml;

class Text
{
    public static function excerpt(string $text, int $limit = 55, bool $splitWords = true): string
    {
        $map = [];
        $text = static::normalize($text);
        // replace tags with placeholder
        $text = preg_replace_callback('|<([a-z+])[^>]*?>.*?</\\1>|siu', function ($match) use (&$map) {
            $map[] = $match[0];
            return '⍈';
        }, $text);
        $excerptLength = $limit;
        if ($splitWords) {
            $excerpt = static::words($text, $limit);
            $excerptLength = mb_strlen($excerpt);
        }
        $paragraphs = static::extractParagraphs($text, $excerptLength);
        $text = implode(PHP_EOL, $paragraphs);
        $i = 0;
        // replace placeholder with tags
        $text = preg_replace_callback('|⍈|u', function ($match) use (&$i, $map) {
            return $map[$i++];
        }, $text);
        return $text;
    }

    public static function initials(string $name, string $initialPunctuation = ''): string
    {
        preg_match_all('/(?<=\s|\b)\p{L}/u', (string) $name, $matches); // match the first letter of each word in the name
        $result = (string) array_reduce($matches[0], function ($carry, $word) use ($initialPunctuation) {
            $initial = mb_substr($word, 0, 1, 'UTF-8');
            $initial = mb_strtoupper($initial, 'UTF-8');
            return $carry.$initial.$initialPunctuation;
        });
        return trim($result);
    }

    /**
     * @param string $nameFormat  first|first_initial|last_initial|initials
     * @param string $initialType  period|period_space|space
     */
    public static function name(string $name, string $nameFormat = '', string $initialType = 'space'): string
    {
        $names = preg_split('/\W/u', $name, 0, PREG_SPLIT_NO_EMPTY);
        $firstName = (string) array_shift($names);
        $lastName = (string) array_pop($names);
        $nameFormat = Str::restrictTo('first,first_initial,last_initial,initials', $nameFormat, '');
        $initialType = Str::restrictTo('period,period_space,space', $initialType, 'space');
        $initialTypes = [
            'period' => '.',
            'period_space' => '. ',
            'space' => ' ',
        ];
        $initialPunctuation = $initialTypes[$initialType];
        if ('initials' == $nameFormat) {
            return static::initials($name, $initialPunctuation);
        }
        $firstNameInitial = static::initials($firstName).$initialPunctuation;
        $lastNameInitial = $lastName ? static::initials($lastName).$initialPunctuation : '';
        $nameFormats = [
            'first' => $firstName,
            'first_initial' => $firstNameInitial.$lastName,
            'last' => $lastName,
            'last_initial' => $firstName.' '.$lastNameInitial,
        ];
        return trim((string) Arr::get($nameFormats, $nameFormat, $name));
    }

    public static function normalize(string $text): string
    {
        $text = (new SanitizeTextHtml($text))->run();
        $text = strip_shortcodes($text);
        $text = excerpt_remove_blocks($text); // just in case...
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = normalize_whitespace($text); // normalize EOL characters and strip duplicate whitespace.
        $text = preg_replace('/\R{1,}/u', PHP_EOL.PHP_EOL, $text); // replace all line-breaks with a double line break
        $text = wptexturize($text); // replace common plain text characters with formatted entities.
        $text = ent2ncr($text); // convert named entities into numbered entities.
        $text = convert_chars($text); // converts lone & characters into &#038;
        $text = convert_invalid_entities($text); // convert invalid Unicode references range to valid range.
        $text = convert_smilies($text); // convert text smilies to emojis.
        $text = html_entity_decode($text);
        return $text;
    }

    public static function text(string $text): string
    {
        $text = static::normalize($text);
        $text = preg_split('/\R+/um', $text); // split text by line-breaks
        $text = array_map('trim', $text); // trim paragraphs
        $text = implode(PHP_EOL.PHP_EOL, $text);
        return wpautop($text);
    }

    public static function wordCount(string $text): int
    {
        $text = wp_strip_all_tags($text, true);
        if (!extension_loaded('intl')) {
            return count(preg_split('/[^\p{L}\p{N}\']+/u', $text));
        }
        $text = \Normalizer::normalize($text);
        $iterator = \IntlRuleBasedBreakIterator::createWordInstance('');
        $iterator->setText($text);
        $wordCount = 0;
        foreach ($iterator->getPartsIterator() as $part) {
            if (\IntlBreakIterator::WORD_NONE !== $iterator->getRuleStatus()) {
                ++$wordCount;
            }
        }
        return $wordCount;
    }

    public static function words(string $text, int $limit = 0): string
    {
        $stringLength = extension_loaded('intl')
            ? static::excerptIntlSplit($text, $limit)
            : static::excerptSplit($text, $limit);
        return mb_substr($text, 0, $stringLength);
    }

    protected static function excerptIntlSplit(string $text, int $limit): int
    {
        $text = \Normalizer::normalize($text);
        $iterator = \IntlRuleBasedBreakIterator::createWordInstance('');
        $iterator->setText($text);
        $stringLength = 0;
        $wordCount = 0;
        foreach ($iterator->getPartsIterator() as $part) {
            $stringLength += mb_strlen($part);
            if (\IntlBreakIterator::WORD_NONE === $iterator->getRuleStatus()) {
                continue;
            }
            if (++$wordCount === $limit) {
                break;
            }
        }
        return $stringLength;
    }

    protected static function excerptSplit(string $text, int $limit): int
    {
        preg_match('/^\s*+(?:\S++\s*+){1,'.$limit.'}/u', $text, $matches);
        if (mb_strlen($text) === mb_strlen($matches[0] ?? '')) {
            return mb_strlen($text);
        }
        return mb_strlen(rtrim($matches[0]));
    }

    protected static function extractParagraphs(string $text, int $length): array
    {
        $paragraphs = preg_split('/\R+/um', $text);
        $paragraphs = array_map('trim', $paragraphs);
        $lastIndex = count($paragraphs) - 1;
        foreach ($paragraphs as $index => &$paragraph) {
            $paragraphLength = mb_strlen($paragraph);
            if ($length > $paragraphLength || ($length === $paragraphLength && $index === $lastIndex)) {
                $paragraph = sprintf('<p>%s</p>', $paragraph);
                $length -= $paragraphLength;
                continue;
            }
            if ($length > 0) {
                $hidden = mb_substr($paragraph, $length);
                $visible = mb_substr($paragraph, 0, $length);
                $paragraph = glsr(Builder::class)->p([
                    'class' => 'glsr-hidden-text',
                    'data-show-less' => __('Show less', 'site-reviews'),
                    'data-show-more' => __('Show more', 'site-reviews'),
                    'data-trigger' => glsr_get_option('reviews.excerpts_action') ?: 'expand',
                    'text' => sprintf('%s<span class="glsr-hidden">%s</span>', $visible, $hidden),
                ]);
                $length = 0;
                continue;
            }
            $paragraph = glsr(Builder::class)->p([
                'class' => 'glsr-hidden',
                'text' => $paragraph,
            ]);
        }
        return $paragraphs;
    }
}
