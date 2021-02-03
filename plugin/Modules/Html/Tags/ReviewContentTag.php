<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use IntlRuleBasedBreakIterator;

class ReviewContentTag extends ReviewTag
{
    /**
     * @param string $text
     * @return string
     */
    public function excerpt($text)
    {
        $limit = Cast::toInt(glsr_get_option('reviews.excerpts_length', 55));
        $split = extension_loaded('intl')
            ? $this->excerptIntlSplit($text, $limit)
            : $this->excerptSplit($text, $limit);
        $hiddenText = substr($text, $split);
        if (!empty($hiddenText)) {
            $showMore = glsr(Builder::class)->span($hiddenText, [
                'class' => 'glsr-hidden glsr-hidden-text',
                'data-show-less' => __('Show less', 'site-reviews'),
                'data-show-more' => __('Show more', 'site-reviews'),
                'data-trigger' => glsr_get_option('reviews.excerpts_action') ?: 'excerpt',
            ]);
            $text = ltrim(substr($text, 0, $split)).$showMore;
        }
        return $text;
    }

    /**
     * @param string $text
     * @param int $limit
     * @return int
     */
    protected function excerptIntlSplit($text, $limit)
    {
        $words = IntlRuleBasedBreakIterator::createWordInstance('');
        $words->setText($text);
        $count = 0;
        foreach ($words as $offset) {
            if (IntlRuleBasedBreakIterator::WORD_NONE === $words->getRuleStatus()) {
                continue;
            }
            ++$count;
            if ($count != $limit) {
                continue;
            }
            return $offset;
        }
        return strlen($text);
    }

    /**
     * @param string $text
     * @param int $limit
     * @return int
     */
    protected function excerptSplit($text, $limit)
    {
        if (str_word_count($text, 0) > $limit) {
            $words = array_keys(str_word_count($text, 2));
            return $words[$limit];
        }
        return strlen($text);
    }

    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden()) {
            return $this->wrap($this->normalizeText($value), 'p');
        }
    }

    /**
     * @param string $text
     * @return string
     */
    protected function normalizeText($text)
    {
        $allowedHtml = wp_kses_allowed_html();
        $allowedHtml['mark'] = []; // allow using the <mark> tag to highlight text
        $text = wp_kses($text, $allowedHtml);
        $text = convert_smilies(strip_shortcodes($text));
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = preg_replace('/(\v){2,}/u', '$1', $text);
        if (glsr_get_option('reviews.excerpts', false, 'bool')) {
            $text = $this->excerpt($text);
        }
        $text = wptexturize(nl2br($text));
        $text = preg_replace('/(\v|\s){1,}/u', ' ', $text); // replace all multiple-space and carriage return characters with a space
        return $text;
    }
}
