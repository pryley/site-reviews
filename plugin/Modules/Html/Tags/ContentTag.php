<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use IntlRuleBasedBreakIterator;

class ContentTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    public function handle($value)
    {
        if (!$this->isHidden()) {
            return $this->wrap($this->normalizeText($value), 'p');
        }
    }

    /**
     * @param string $text
     * @return string
     */
    public function getExcerpt($text)
    {
        $limit = glsr_get_option('reviews.excerpts_length', 55, 'int');
        $split = extension_loaded('intl')
            ? $this->getExcerptIntlSplit($text, $limit)
            : $this->getExcerptSplit($text, $limit);
        $hiddenText = substr($text, $split);
        if (!empty($hiddenText)) {
            $showMore = glsr(Builder::class)->span($hiddenText, [
                'class' => 'glsr-hidden glsr-hidden-text',
                'data-show-less' => __('Show less', 'site-reviews'),
                'data-show-more' => __('Show more', 'site-reviews'),
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
    protected function getExcerptIntlSplit($text, $limit)
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
    protected function getExcerptSplit($text, $limit)
    {
        if (str_word_count($text, 0) > $limit) {
            $words = array_keys(str_word_count($text, 2));
            return $words[$limit];
        }
        return strlen($text);
    }

    /**
     * @param string $text
     * @return string
     */
    protected function normalizeText($text)
    {
        $text = wp_kses($text, wp_kses_allowed_html());
        $text = convert_smilies(strip_shortcodes($text));
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = preg_replace('/(\R){2,}/u', '$1', $text);
        if (glsr_get_option('reviews.excerpts', false, 'bool')) {
            $text = $this->getExcerpt($text);
        }
        return wptexturize(nl2br($text));
    }
}
