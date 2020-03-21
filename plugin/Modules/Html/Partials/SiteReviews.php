<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Date;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;
use GeminiLabs\SiteReviews\Modules\Html\ReviewsHtml;
use GeminiLabs\SiteReviews\Modules\Multilingual;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Reviews;
use IntlRuleBasedBreakIterator;
use WP_Post;

class SiteReviews
{
    /**
     * @var array
     */
    public $args;

    /**
     * @var Review
     */
    public $current;

    /**
     * @var array
     */
    public $options;

    /**
     * @var Reviews
     */
    protected $reviews;

    /**
     * @param Reviews|null $reviews
     * @return ReviewsHtml
     */
    public function build(array $args = [], $reviews = null)
    {
        $this->args = glsr(SiteReviewsDefaults::class)->merge($args);
        $this->options = Arr::flattenArray(glsr(OptionManager::class)->all());
        $this->reviews = $reviews instanceof Reviews
            ? $reviews
            : glsr(ReviewManager::class)->get($this->args);
        $this->generateSchema();
        return $this->buildReviews();
    }

    /**
     * @return ReviewHtml
     */
    public function buildReview(Review $review)
    {
        $review = apply_filters('site-reviews/review/build/before', $review);
        $this->current = $review;
        $renderedFields = [];
        foreach ($review as $key => $value) {
            $method = Helper::buildMethodName($key, 'buildOption');
            $field = method_exists($this, $method)
                ? $this->$method($key, $value)
                : false;
            $field = apply_filters('site-reviews/review/build/'.$key, $field, $value, $review, $this);
            if (false === $field) {
                continue;
            }
            $renderedFields[$key] = $field;
        }
        $this->wrap($renderedFields, $review);
        $renderedFields = apply_filters('site-reviews/review/build/after', $renderedFields, $review, $this);
        $this->current = null;
        return new ReviewHtml($review, (array) $renderedFields);
    }

    /**
     * @return ReviewsHtml
     */
    public function buildReviews()
    {
        $renderedReviews = [];
        foreach ($this->reviews as $index => $review) {
            $renderedReviews[] = $this->buildReview($review);
        }
        return new ReviewsHtml($renderedReviews, $this->reviews->max_num_pages, $this->args);
    }

    /**
     * @return void
     */
    public function generateSchema()
    {
        if (!wp_validate_boolean($this->args['schema'])) {
            return;
        }
        glsr(Schema::class)->store(
            glsr(Schema::class)->build($this->args)
        );
    }

    /**
     * @param string $text
     * @return string
     */
    public function getExcerpt($text)
    {
        $limit = intval($this->getOption('settings.reviews.excerpts_length', 55));
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
     * @param string $key
     * @param string $path
     * @return bool
     */
    public function isHidden($key, $path = '')
    {
        $isOptionEnabled = !empty($path)
            ? $this->isOptionEnabled($path)
            : true;
        return in_array($key, $this->args['hide']) || !$isOptionEnabled;
    }

    /**
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function isHiddenOrEmpty($key, $value)
    {
        return $this->isHidden($key) || empty($value);
    }

    /**
     * @param string $text
     * @return string
     */
    public function normalizeText($text)
    {
        $text = wp_kses($text, wp_kses_allowed_html());
        $text = convert_smilies(strip_shortcodes($text));
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = preg_replace('/(\R){2,}/u', '$1', $text);
        if ($this->isOptionEnabled('settings.reviews.excerpts')) {
            $text = $this->getExcerpt($text);
        }
        return wptexturize(nl2br($text));
    }

    /**
     * @param string $key
     * @param string $value
     * @return void|string
     */
    protected function buildOptionAssignedTo($key, $value)
    {
        if ($this->isHidden($key, 'settings.reviews.assigned_links')) {
            return;
        }
        $post = get_post(glsr(Multilingual::class)->getPostId($value));
        if (empty($post->ID)) {
            return;
        }
        $permalink = glsr(Builder::class)->a(get_the_title($post->ID), [
            'href' => get_the_permalink($post->ID),
        ]);
        $assignedTo = sprintf(__('Review of %s', 'site-reviews'), $permalink);
        return '<span>'.$assignedTo.'</span>';
    }

    /**
     * @param string $key
     * @param string $value
     * @return void|string
     */
    protected function buildOptionAuthor($key, $value)
    {
        if (!$this->isHidden($key)) {
            $name = Str::convertName(
                $value,
                glsr_get_option('reviews.name.format'),
                glsr_get_option('reviews.name.initial')
            );
            return '<span>'.$name.'</span>';
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @return void|string
     */
    protected function buildOptionAvatar($key, $value)
    {
        if ($this->isHidden($key, 'settings.reviews.avatars')) {
            return;
        }
        $size = $this->getOption('settings.reviews.avatars_size', 40);
        return glsr(Builder::class)->img([
            'height' => $size,
            'src' => $this->generateAvatar($value),
            'style' => sprintf('width:%1$spx; height:%1$spx;', $size),
            'width' => $size,
        ]);
    }

    /**
     * @param string $key
     * @param string $value
     * @return void|string
     */
    protected function buildOptionContent($key, $value)
    {
        $text = $this->normalizeText($value);
        if (!$this->isHiddenOrEmpty($key, $text)) {
            return '<p>'.$text.'</p>';
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @return void|string
     */
    protected function buildOptionDate($key, $value)
    {
        if ($this->isHidden($key)) {
            return;
        }
        $dateFormat = $this->getOption('settings.reviews.date.format', 'default');
        if ('relative' == $dateFormat) {
            $date = glsr(Date::class)->relative($value);
        } else {
            $format = 'custom' == $dateFormat
                ? $this->getOption('settings.reviews.date.custom', 'M j, Y')
                : glsr(OptionManager::class)->getWP('date_format', 'F j, Y');
            $date = date_i18n($format, strtotime($value));
        }
        return '<span>'.$date.'</span>';
    }

    /**
     * @param string $key
     * @param string $value
     * @return void|string
     */
    protected function buildOptionRating($key, $value)
    {
        if (!$this->isHiddenOrEmpty($key, $value)) {
            return glsr_star_rating($value);
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @return void|string
     */
    protected function buildOptionResponse($key, $value)
    {
        if ($this->isHiddenOrEmpty($key, $value)) {
            return;
        }
        $title = sprintf(__('Response from %s', 'site-reviews'), get_bloginfo('name'));
        $text = $this->normalizeText($value);
        $text = '<p><strong>'.$title.'</strong></p><p>'.$text.'</p>';
        $response = glsr(Builder::class)->div($text, ['class' => 'glsr-review-response-inner']);
        $background = glsr(Builder::class)->div(['class' => 'glsr-review-response-background']);
        return $response.$background;
    }

    /**
     * @param string $key
     * @param string $value
     * @return void|string
     */
    protected function buildOptionTitle($key, $value)
    {
        if ($this->isHidden($key)) {
            return;
        }
        if (empty($value)) {
            $value = __('No Title', 'site-reviews');
        }
        return '<h3>'.$value.'</h3>';
    }

    /**
     * @param string $avatarUrl
     * @return string
     */
    protected function generateAvatar($avatarUrl)
    {
        if (!$this->isOptionEnabled('settings.reviews.avatars_regenerate') || 'local' != $this->current->review_type) {
            return $avatarUrl;
        }
        if ($this->current->user_id) {
        $authorIdOrEmail = get_the_author_meta('ID', $this->current->user_id);
        }
        if (empty($authorIdOrEmail)) {
            $authorIdOrEmail = $this->current->email;
        }
        if ($newAvatar = get_avatar_url($authorIdOrEmail)) {
            return $newAvatar;
        }
        return $avatarUrl;
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
     * @param string $path
     * @param mixed $fallback
     * @return mixed
     */
    protected function getOption($path, $fallback = '')
    {
        if (array_key_exists($path, $this->options)) {
            return $this->options[$path];
        }
        return $fallback;
    }

    /**
     * @param string $path
     * @return bool
     */
    protected function isOptionEnabled($path)
    {
        return 'yes' == $this->getOption($path);
    }

    /**
     * @return void
     */
    protected function wrap(array &$renderedFields, Review $review)
    {
        $renderedFields = apply_filters('site-reviews/review/wrap', $renderedFields, $review, $this);
        array_walk($renderedFields, function (&$value, $key) use ($review) {
            $value = apply_filters('site-reviews/review/wrap/'.$key, $value, $review);
            if (empty($value)) {
                return;
            }
            $value = glsr(Builder::class)->div($value, [
                'class' => 'glsr-review-'.$key,
            ]);
        });
    }
}
