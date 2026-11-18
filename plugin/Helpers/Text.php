<?php

namespace GeminiLabs\SiteReviews\Helpers;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Sanitizers\SanitizeTextHtml;

class Text
{
    /** @var string[] the tags an excerpt keeps */
    protected const INLINE_TAGS = [
        'a', 'abbr', 'b', 'bdi', 'bdo', 'cite', 'code', 'data', 'del', 'dfn', 'em', 'i', 'ins', 'kbd',
        'mark', 'q', 's', 'samp', 'small', 'span', 'strong', 'sub', 'sup', 'time', 'u', 'var', 'wbr',
    ];

    /**
     * Encode a less-than sign that starts no tag.
     */
    public static function escapeLessThan(string $html): string
    {
        $html = wp_kses_no_null($html); // wp_kses() removes these characters before it looks for a tag
        // a tag starts with a letter, a slash, an exclamation mark or a question mark
        return preg_replace('/<(?![a-z\/!?])/i', '&lt;', $html) ?? str_replace('<', '&lt;', $html);
    }

    /**
     * Encode the square brackets and the braces of rendered HTML.
     */
    public static function escapeShortcodes(string $html): string
    {
        $entities = [ // do_shortcode() turns &#91; back into a bracket but leaves &#091; alone
            '&#91;' => '&#091;',
            '&#93;' => '&#093;',
            '[' => '&#091;',
            ']' => '&#093;',
            '{' => '&#123;', // Bricks reads a brace pair as a dynamic data tag
            '}' => '&#125;',
        ];
        // skip script and style elements because browsers don't decode entities in them
        $result = preg_replace_callback(
            '~<(script|style)\b.*?</\1\s*>|&#9[13];|[\[\]{}]~is',
            fn ($matches) => empty($matches[1]) ? $entities[$matches[0]] : $matches[0],
            $html
        );
        if (null === $result) {
            glsr_log()->warning('Text::escapeShortcodes: '.preg_last_error_msg());
            return strtr($html, $entities);
        }
        return $result;
    }

    public static function excerpt(?string $text, int $limit = 55, bool $splitWords = true): string
    {
        $tokens = static::tokens(static::normalize($text));
        $excerptLength = $limit;
        if ($splitWords) {
            $excerpt = static::words(static::decode(static::tokenText($tokens)), $limit);
            $excerptLength = mb_strlen($excerpt);
        }
        $paragraphs = static::extractParagraphs(static::paragraphs($tokens), $excerptLength);
        return implode(\PHP_EOL, $paragraphs);
    }

    public static function initials(?string $name, string $initialPunctuation = ''): string
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
     * @param string $initialType period|period_space|space
     */
    public static function name(?string $name, string $nameFormat = '', string $initialType = 'space'): string
    {
        $names = preg_split('/\W/u', (string) $name, 0, \PREG_SPLIT_NO_EMPTY);
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
        if ('initials' === $nameFormat) {
            return static::initials($name, $initialPunctuation);
        }
        $firstNameInitial = static::initials($firstName).$initialPunctuation;
        $lastNameInitial = $lastName ? static::initials($lastName).$initialPunctuation : '';
        $nameFormats = [
            'first' => $firstName,
            'first_initial' => $firstNameInitial.$lastName,
            'last' => $lastName,
            'last_initial' => "{$firstName} {$lastNameInitial}",
        ];
        return trim((string) Arr::get($nameFormats, $nameFormat, $name));
    }

    public static function normalize(?string $text): string
    {
        $sanitizer = new SanitizeTextHtml($text);
        $text = $sanitizer->run();
        $text = strip_shortcodes($text);
        $text = excerpt_remove_blocks($text); // just in case...
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = normalize_whitespace($text); // normalize EOL characters and strip duplicate whitespace.
        $text = preg_replace('/\R{1,}/u', \PHP_EOL.\PHP_EOL, $text); // replace all line-breaks with a double line break
        $text = wptexturize($text); // replace common plain text characters with formatted entities.
        $text = ent2ncr($text); // convert named entities into numbered entities.
        $text = convert_chars($text); // converts lone & characters into &#038;
        $text = convert_invalid_entities($text); // convert invalid Unicode references range to valid range.
        $text = convert_smilies($text); // convert text smilies to emojis.
        $text = wp_specialchars_decode($text);
        $text = static::escapeLessThan($text); // wp_kses() reads a bracket, a gap and a tag name as a tag
        $text = wp_kses($text, $sanitizer->allowedHtml()); // strip any tag the trailing decode re-formed past the sanitizer.
        return $text;
    }

    public static function text(?string $text): string
    {
        $text = static::normalize($text);
        if ($paragraphs = preg_split('/\R+/um', $text)) { // split text by line-breaks
            $paragraphs = array_map('trim', $paragraphs); // trim paragraphs
            $text = implode(\PHP_EOL.\PHP_EOL, $paragraphs);
        }
        return wpautop($text);
    }

    public static function wordCount(?string $text): int
    {
        $text = wp_strip_all_tags((string) $text, true);
        $iterator = static::iterator($text);
        if (empty($iterator)) {
            return count(preg_split('/[^\p{L}\p{N}\']+/u', $text));
        }
        $wordCount = 0;
        while (\IntlBreakIterator::DONE !== $iterator->next()) {
            if (\IntlBreakIterator::WORD_NONE !== $iterator->getRuleStatus()) {
                ++$wordCount;
            }
        }
        return $wordCount;
    }

    public static function words(?string $text, int $limit = 0): string
    {
        $stringLength = static::excerptIntlSplit((string) $text, $limit);
        return mb_substr((string) $text, 0, $stringLength);
    }

    /**
     * The closing tags of the open tags, the last one first.
     *
     * @param array<int, array{0: string, 1: string, 2?: string}> $open
     *
     * @return array<int, array{0: string, 1: string, 2?: string}>
     */
    protected static function closeTags(array $open): array
    {
        return array_map(
            fn ($token) => ['close', sprintf('</%s>', $token[2] ?? ''), $token[2] ?? ''],
            array_reverse($open)
        );
    }

    /**
     * The HTML of a paragraph before and after its first $length characters. A tag open at
     * the cut is closed before it and opened again after it.
     *
     * @param array<int, array{0: string, 1: string, 2?: string}> $tokens
     *
     * @return array{0: string, 1: string}
     */
    protected static function cutTokens(array $tokens, int $length): array
    {
        $open = [];
        $visible = '';
        foreach ($tokens as $index => $token) {
            if ('text' !== $token[0]) {
                $open = static::openTags($open, $token);
                $visible .= $token[1];
                continue;
            }
            $textLength = mb_strlen(static::decode($token[1]));
            if ($length >= $textLength) {
                $length -= $textLength;
                $visible .= $token[1];
                continue;
            }
            $cut = static::encodedLength($token[1], $length);
            $rest = array_slice($tokens, $index + 1);
            return [
                $visible.mb_substr($token[1], 0, $cut).static::tokenHtml(static::closeTags($open)),
                static::tokenHtml(static::reopenTags($open)).mb_substr($token[1], $cut).static::tokenHtml($rest),
            ];
        }
        return [$visible, ''];
    }

    /**
     * The text as a reader sees it: normalize() leaves the texturized
     * punctuation as numeric entities, which are one character each.
     */
    protected static function decode(string $text): string
    {
        return html_entity_decode($text, \ENT_QUOTES | \ENT_HTML5, 'UTF-8');
    }

    /**
     * The length in $text of its first $length characters as decode() counts them.
     */
    protected static function encodedLength(string $text, int $length): int
    {
        if ($length < 1) {
            return 0;
        }
        preg_match_all('/&(?:#\d+|#x[0-9a-f]+|[a-z][a-z0-9]*);|./isu', $text, $matches);
        $encoded = 0;
        $decoded = 0;
        foreach ($matches[0] as $unit) {
            $encoded += mb_strlen($unit);
            $decoded += mb_strlen(static::decode($unit));
            if ($decoded >= $length) {
                break;
            }
        }
        return $encoded;
    }

    protected static function excerptIntlSplit(string $text, int $limit): int
    {
        $iterator = static::iterator($text);
        if (empty($iterator)) {
            return static::excerptSplit((string) $text, $limit);
        }
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
        $length = mb_strlen($text);
        if ($limit < 1) {
            return $length;
        }
        preg_match('/^\s*+(?:\S++\s*+){1,'.$limit.'}/u', $text, $matches);
        $match = $matches[0] ?? '';
        if ($length === mb_strlen($match)) {
            return $length;
        }
        return mb_strlen(rtrim($match));
    }

    /**
     * The paragraphs of an excerpt: what follows the first $length characters is hidden.
     *
     * @param array<int, array<int, array{0: string, 1: string, 2?: string}>> $paragraphs
     *
     * @return string[]
     */
    protected static function extractParagraphs(array $paragraphs, int $length): array
    {
        $html = [];
        $lastIndex = count($paragraphs) - 1;
        foreach ($paragraphs as $index => $tokens) {
            $paragraphLength = mb_strlen(static::decode(static::tokenText($tokens)));
            if ($length > $paragraphLength || ($length === $paragraphLength && $index === $lastIndex)) {
                $html[] = sprintf('<p>%s</p>', static::tokenHtml($tokens));
                $length -= $paragraphLength;
                continue;
            }
            if ($length > 0) {
                [$visible, $hidden] = static::cutTokens($tokens, $length);
                $html[] = glsr(Builder::class)->p([
                    'class' => 'glsr-hidden-text',
                    'data-show-less' => __('Show less', 'site-reviews'),
                    'data-show-more' => __('Show more', 'site-reviews'),
                    'data-trigger' => glsr_get_option('reviews.excerpts_action') ?: 'expand',
                    'text' => sprintf('%s<span class="glsr-hidden">%s</span>', $visible, $hidden),
                ]);
                $length = 0;
                continue;
            }
            $html[] = glsr(Builder::class)->p([
                'class' => 'glsr-hidden',
                'text' => static::tokenHtml($tokens),
            ]);
        }
        return $html;
    }

    /**
     * @return \IntlRuleBasedBreakIterator|null
     */
    protected static function iterator(string $text)
    {
        if (!extension_loaded('intl')) {
            return null;
        }
        $iterator = \IntlRuleBasedBreakIterator::createWordInstance('');
        if (!$iterator instanceof \IntlRuleBasedBreakIterator) { // has getRuleStatus()
            return null;
        }
        $normalizedText = \Normalizer::normalize($text);
        if (empty($normalizedText)) {
            return null;
        }
        if (!$iterator->setText($normalizedText)) {
            return null;
        }
        return $iterator;
    }
    /**
     * The open tags after a token.
     *
     * @param array<int, array{0: string, 1: string, 2?: string}> $open
     * @param array{0: string, 1: string, 2?: string} $token
     *
     * @return array<int, array{0: string, 1: string, 2?: string}>
     */
    protected static function openTags(array $open, array $token): array
    {
        if ('open' === $token[0]) {
            $open[] = $token;
        } elseif ('close' === $token[0]) {
            array_pop($open); // the processor gives the closing tags in order
        }
        return $open;
    }

    /**
     * The tokens of each paragraph. A tag left open at a line break is closed there and opened
     * again in the next paragraph.
     *
     * @param array<int, array{0: string, 1: string, 2?: string}> $tokens
     *
     * @return array<int, array<int, array{0: string, 1: string, 2?: string}>>
     */
    protected static function paragraphs(array $tokens): array
    {
        $open = [];
        $paragraphs = [[]];
        foreach ($tokens as $token) {
            if ('text' !== $token[0]) {
                $open = static::openTags($open, $token);
                $paragraphs[array_key_last($paragraphs)][] = $token;
                continue;
            }
            foreach (preg_split('/\R+/um', $token[1]) ?: [$token[1]] as $index => $line) {
                if ($index > 0) { // after a line break
                    array_push($paragraphs[array_key_last($paragraphs)], ...static::closeTags($open));
                    $paragraphs[] = static::reopenTags($open);
                }
                $paragraphs[array_key_last($paragraphs)][] = ['text', $line];
            }
        }
        return array_map([static::class, 'trimTokens'], $paragraphs);
    }

    /**
     * The open tags as they are opened again, without the id that only one element may have.
     *
     * @param array<int, array{0: string, 1: string, 2?: string}> $open
     *
     * @return array<int, array{0: string, 1: string, 2?: string}>
     */
    protected static function reopenTags(array $open): array
    {
        foreach ($open as &$token) {
            $token[1] = (string) preg_replace('/ id="[^"]*"/', '', $token[1]);
        }
        return $open;
    }

    /**
     * The token the processor is on.
     *
     * @return array{0: string, 1: string, 2?: string} // the type (break, close, open, text or void), the HTML and the tag name
     */
    protected static function token(\WP_HTML_Processor $processor): array
    {
        $type = $processor->get_token_type();
        if ('#text' === $type) {
            return ['text', $processor->serialize_token()];
        }
        if ('#tag' !== $type) {
            return ['text', '']; // a comment
        }
        $name = strtolower((string) $processor->get_token_name());
        if (!in_array($name, static::INLINE_TAGS, true)) {
            return ['break', \PHP_EOL, $name];
        }
        if ($processor->is_tag_closer()) {
            return ['close', $processor->serialize_token(), $name];
        }
        $type = false === $processor->expects_closer() ? 'void' : 'open';
        return [$type, $processor->serialize_token(), $name];
    }

    /**
     * @param array<int, array{0: string, 1: string, 2?: string}> $tokens
     */
    protected static function tokenHtml(array $tokens): string
    {
        return implode('', array_column($tokens, 1));
    }

    /**
     * The text and the inline tags of normalized HTML, in order. Only the text when the HTML
     * can't be read or holds a tag that is not inline, which then ends its line.
     *
     * @return array<int, array{0: string, 1: string, 2?: string}>
     */
    protected static function tokens(string $html): array
    {
        if (!str_contains($html, '<')) {
            return [['text', $html]]; // no tag
        }
        $processor = \WP_HTML_Processor::create_fragment($html);
        $tokens = [];
        while ($processor && $processor->next_token()) {
            $tokens[] = static::token($processor);
        }
        if (!$processor || null !== $processor->get_last_error()) {
            return [['text', wp_strip_all_tags($html)]];
        }
        if (!in_array('break', array_column($tokens, 0), true)) {
            return $tokens;
        }
        $text = array_filter($tokens, fn ($token) => in_array($token[0], ['break', 'text'], true));
        return [['text', trim(static::tokenHtml($text))]];
    }

    /**
     * @param array<int, array{0: string, 1: string, 2?: string}> $tokens
     */
    protected static function tokenText(array $tokens): string
    {
        return static::tokenHtml(array_filter($tokens, fn ($token) => 'text' === $token[0]));
    }

    /**
     * @param array<int, array{0: string, 1: string, 2?: string}> $tokens
     *
     * @return array<int, array{0: string, 1: string, 2?: string}>
     */
    protected static function trimTokens(array $tokens): array
    {
        $first = array_key_first($tokens);
        $last = array_key_last($tokens);
        if (null !== $first && 'text' === $tokens[$first][0]) {
            $tokens[$first][1] = ltrim($tokens[$first][1]);
        }
        if (null !== $last && 'text' === $tokens[$last][0]) {
            $tokens[$last][1] = rtrim($tokens[$last][1]);
        }
        return $tokens;
    }
}
