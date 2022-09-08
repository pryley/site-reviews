<?php

namespace GeminiLabs\SiteReviews\Helpers;

use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class Text
{
    /**
     * @param string $text
     * @param int $limit
     * @param bool $splitWords
     * @return string
     */
    public static function excerpt($text, $limit = 55, $splitWords = true)
    {
        $text = static::normalize($text);
        $excerptLength = $limit;
        if ($splitWords) {
            $excerpt = static::words($text, $limit);
            $excerptLength = mb_strlen($excerpt);
        }
        $paragraphs = preg_split('/\R+/um', $text);
        $paragraphs = array_map('trim', $paragraphs);
        foreach ($paragraphs as &$paragraph) {
            $paragraphLength = mb_strlen($paragraph);
            if ($excerptLength >= $paragraphLength) {
                $paragraph = sprintf('<p>%s</p>', $paragraph);
                $excerptLength -= $paragraphLength;
                continue;
            }
            if ($excerptLength > 0) {
                $hidden = mb_substr($paragraph, $excerptLength);
                $visible = mb_substr($paragraph, 0, $excerptLength);
                $paragraph = glsr(Builder::class)->p([
                    'class' => 'glsr-hidden-text',
                    'data-show-less' => __('Show less', 'site-reviews'),
                    'data-show-more' => __('Show more', 'site-reviews'),
                    'data-trigger' => glsr_get_option('reviews.excerpts_action') ?: 'expand',
                    'text' => sprintf('%s<span class="glsr-hidden">%s</span>', $visible, $hidden),
                ]);
                $excerptLength = 0;
                continue;
            }
            $paragraph = sprintf('<p class="glsr-hidden">%s</p>', $paragraph);
        }
        $text = implode(PHP_EOL, $paragraphs);
        return $text;
    }

    /**
     * @param string $name
     * @param string $initialPunctuation
     * @return string
     */
    public static function initials($name, $initialPunctuation = '')
    {
        preg_match_all('/(?<=\s|\b)\pL/u', (string) $name, $matches);
        $result = (string) array_reduce($matches[0], function ($carry, $word) use ($initialPunctuation) {
            $initial = mb_substr($word, 0, 1, 'UTF-8');
            $initial = mb_strtoupper($initial, 'UTF-8');
            return $carry.$initial.$initialPunctuation;
        });
        return trim($result);
    }

    /**
     * @param string $name
     * @param string $nameFormat  first|first_initial|last_initial|initials
     * @param string $initialType  period|period_space|space
     * @return string
     */
    public static function name($name, $nameFormat = '', $initialType = 'space')
    {
        $names = preg_split('/\W/u', $name, 0, PREG_SPLIT_NO_EMPTY);
        $firstName = array_shift($names);
        $lastName = array_pop($names);
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


    /**
     * @param string $text
     * @return string
     */
    public static function normalize($text)
    {
        $allowedHtml = wp_kses_allowed_html();
        $allowedHtml['mark'] = []; // allow using the <mark> tag to highlight text
        $text = Cast::toString($text);
        $text = wp_kses($text, $allowedHtml);
        $text = strip_shortcodes($text);
        $text = excerpt_remove_blocks($text); // just in case...
        $text = convert_smilies($text);
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = preg_replace('/\R{1,}/u', PHP_EOL.PHP_EOL, $text); // replace all multi line-breaks with a double line break
        $text = preg_replace('/ {1,}/u', ' ', $text); // replace all multiple spaces with a single space
        $text = wptexturize($text);
        return $text;
    }

    /**
     * @param string $text
     * @return string
     */
    public static function text($text)
    {
        $text = static::normalize($text);
        $text = preg_split('/\R+/um', $text);
        $text = array_map('trim', $text); // trim paragraphs
        $text = implode(PHP_EOL.PHP_EOL, $text);
        return wpautop($text);
    }

    /**
     * @param string $text
     * @param int $limit
     * @return string
     */
    public static function words($text, $limit = 0)
    {
        $stringLength = extension_loaded('intl')
            ? static::excerptIntlSplit($text, $limit)
            : static::excerptSplit($text, $limit);
        return mb_substr($text, 0, $stringLength);
    }

    /**
     * @param string $text
     * @param int $limit
     * @return int
     */
    protected static function excerptIntlSplit($text, $limit)
    {
        $text = \Normalizer::normalize($text);
        $iter = \IntlRuleBasedBreakIterator::createWordInstance('');
        $iter->setText($text);
        $words = $iter->getPartsIterator();
        $stringLength = 0;
        $wordCount = 0;
        foreach ($words as $word) {
            $stringLength += mb_strlen($word);
            if (\IntlChar::isspace($word) || \IntlChar::ispunct($word)) {
                continue;
            }
            if (++$wordCount === $limit) {
                break;
            }
        }
        return $stringLength;
    }

    /**
     * @param string $text
     * @param int $limit
     * @return int
     */
    protected static function excerptSplit($text, $limit)
    {
        preg_match('/^\s*+(?:\S++\s*+){1,'.$limit.'}/u', $text, $matches);
        if (mb_strlen($text) === mb_strlen($matches[0] ?? '')) {
            return mb_strlen($text);
        }
        return mb_strlen(rtrim($matches[0]));
    }
}
