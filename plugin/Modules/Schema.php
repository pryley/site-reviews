<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\RatingManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Schema\BaseType;
use GeminiLabs\SiteReviews\Modules\Schema\UnknownType;
use GeminiLabs\SiteReviews\Review;

class Schema
{
    protected array $args;
    protected array $keyValues = [];
    protected array $ratingCounts;

    /**
     * @var \GeminiLabs\SiteReviews\Reviews|array
     */
    protected $reviews;

    /**
     * @param \GeminiLabs\SiteReviews\Reviews|array $reviews
     */
    public function build(array $args = [], $reviews = []): array
    {
        $this->args = $args;
        $this->reviews = $reviews;
        $schema = $this->buildSummary($args);
        if (!empty($schema)) {
            $reviewSchema = $this->buildReviews();
            array_walk($reviewSchema, function (&$review) {
                unset($review['@context']);
                unset($review['itemReviewed']);
            });
        }
        if (!empty($reviewSchema)) {
            $schema['review'] = $reviewSchema;
        }
        return $schema;
    }

    public function buildSummary(array $args = [], array $ratings = []): array
    {
        if (!empty($args)) {
            $this->args = $args;
        }
        $buildSummary = Helper::buildMethodName('buildSummaryFor', $this->getSchemaOptionValue('type'));
        $count = array_sum($this->getRatingCounts($ratings));
        if (!glsr()->filterBool('schema/is-empty', empty($count))) {
            $schema = Helper::ifTrue(method_exists($this, $buildSummary),
                [$this, $buildSummary],
                [$this, 'buildSummaryForCustom']
            );
            $schema->aggregateRating(
                $this->getSchemaType('AggregateRating')
                    ->ratingValue($this->getRatingValue())
                    ->reviewCount($count)
                    ->bestRating(glsr()->constant('MAX_RATING', Rating::class))
                    ->worstRating(glsr()->constant('MIN_RATING', Rating::class))
            );
            $schema = $schema->toArray();
            $type = $schema['@type'];
            return glsr()->filterArray("schema/{$type}", $schema, $args);
        }
        return [];
    }

    public function buildSummaryForCustom(): BaseType
    {
        return $this->buildSchemaValues($this->getSchemaType(), [
            'description', 'identifier', 'image', 'name', 'url',
        ]);
    }

    public function buildSummaryForLocalBusiness(): BaseType
    {
        return $this->buildSchemaValues($this->buildSummaryForCustom(), [
            'address', 'priceRange', 'telephone',
        ]);
    }

    public function buildSummaryForProduct(): BaseType
    {
        $offerType = $this->getSchemaOption('offerType', 'AggregateOffer');
        $offers = $this->buildSchemaValues($this->getSchemaType($offerType), [
            'highPrice', 'lowPrice', 'price', 'priceCurrency',
        ]);
        $schema = $this->buildSummaryForCustom();
        if (empty($schema->toArray()['@id'])) {
            $schema->setProperty('identifier', $this->getSchemaOptionValue('url').'#product'); // this is converted to @id
        }
        return $schema->doIf(!empty($offers->getProperties()), function ($schema) use ($offers) {
            $schema->offers($offers);
        });
    }

    public function render(): void
    {
        if ($schemas = glsr()->retrieve('schemas', [])) {
            printf('<script type="application/ld+json" class="%s-schema">%s</script>',
                glsr()->id,
                (string) wp_json_encode(
                    glsr()->filterArray('schema/all', $schemas),
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                )
            );
        }
    }

    public function store(array $schema): void
    {
        if (!empty($schema)) {
            $schemas = Arr::consolidate(glsr()->retrieve('schemas'));
            $schemas[] = $schema;
            $schemas = array_map('unserialize', array_unique(array_map('serialize', $schemas)));
            glsr()->store('schemas', $schemas);
        }
    }

    protected function buildReview(Review $review): array
    {
        $schema = $this->getSchemaType('Review')
            ->doIf(!in_array('title', $this->args['hide']), function ($schema) use ($review) {
                $schema->name($review->title);
            })
            ->doIf(!in_array('excerpt', $this->args['hide']), function ($schema) use ($review) {
                $schema->reviewBody($review->content);
            })
            ->datePublished(new \DateTime($review->date))
            ->author($this->getSchemaType('Person')->name($review->author()))
            // ->url($this->getSchemaOptionValue('url')."#review-{$review->ID}")
            ->itemReviewed($this->getSchemaType()->name($this->getSchemaOptionValue('name')));
        if (!empty($review->rating)) {
            $schema->reviewRating(
                $this->getSchemaType('Rating')
                    ->ratingValue($review->rating)
                    ->bestRating(glsr()->constant('MAX_RATING', Rating::class))
                    ->worstRating(glsr()->constant('MIN_RATING', Rating::class))
            );
        }
        return glsr()->filterArray('schema/review', $schema->toArray(), $review, $this->args);
    }

    protected function buildReviews(): array
    {
        $reviews = [];
        foreach ($this->reviews as $review) {
            // Only include critic reviews that have been directly produced by your site, not reviews from third-party sites or syndicated reviews.
            // @see https://developers.google.com/search/docs/data-types/review
            if ('local' === $review->type) {
                $reviews[] = $this->buildReview($review);
            }
        }
        return $reviews;
    }

    protected function buildSchemaValues(BaseType $schema, array $values = []): BaseType
    {
        foreach ($values as $value) {
            $option = $this->getSchemaOptionValue($value);
            if (!empty($option)) {
                $schema->$value($option);
            }
        }
        return $schema;
    }

    protected function getRatingCounts(array $ratings = []): array
    {
        if (empty($this->ratingCounts)) {
            $this->ratingCounts = Helper::ifTrue(!empty($ratings), $ratings, function () {
                return glsr(RatingManager::class)->ratings($this->args);
            });
        }
        return $this->ratingCounts;
    }

    protected function getRatingValue(): float
    {
        return (float) glsr(Rating::class)->average($this->getRatingCounts());
    }

    protected function getSchemaOption(string $option, string $fallback): string
    {
        $option = strtolower($option);
        if ($schemaOption = trim((string) get_post_meta(intval(get_the_ID()), "schema_{$option}", true))) {
            return $schemaOption;
        }
        $setting = glsr(OptionManager::class)->get("settings.schema.{$option}");
        if (is_array($setting)) {
            return $this->getSchemaOptionDefault($setting, $fallback);
        }
        return Helper::ifEmpty($setting, $fallback, $strict = true);
    }

    protected function getSchemaOptionDefault(array $setting, string $fallback): string
    {
        $setting = wp_parse_args($setting, [
            'custom' => '',
            'default' => $fallback,
        ]);
        return Helper::ifTrue('custom' === $setting['default'],
            $setting['custom'],
            $setting['default']
        );
    }

    protected function getSchemaOptionValue(string $option, string $fallback = 'post'): string
    {
        if (array_key_exists($option, $this->keyValues)) {
            return (string) $this->keyValues[$option];
        }
        $value = $this->getSchemaOption($option, $fallback);
        if ($value !== $fallback) {
            return $this->setAndGetKeyValue($option, $value);
        }
        $method = Helper::buildMethodName('getThing', $option);
        if (!method_exists($this, $method)) {
            return '';
        }
        return $this->setAndGetKeyValue($option, $this->$method());
    }

    protected function getSchemaType(?string $type = null): BaseType
    {
        if (!is_string($type)) {
            $type = $this->getSchemaOption('type', 'LocalBusiness');
        }
        $className = Helper::buildClassName($type, 'Modules\Schema');
        return Helper::ifTrue(class_exists($className),
            fn () => new $className(),
            fn () => new UnknownType($type)
        );
    }

    protected function getThingDescription(): string
    {
        if (is_archive()) {
            $text = get_the_archive_description();
        } elseif (is_singular()) {
            $post = get_post();
            $text = Arr::get($post, 'post_excerpt');
            if (empty($text)) {
                $text = Arr::get($post, 'post_content');
            }
        }
        if (!empty($text)) {
            if (function_exists('excerpt_remove_blocks')) {
                $text = excerpt_remove_blocks($text);
            }
            $text = strip_shortcodes($text);
            $text = wpautop($text);
            $text = wptexturize($text);
            $text = wp_strip_all_tags($text);
            $text = str_replace(']]>', ']]&gt;', $text);
            return wp_trim_words($text, apply_filters('excerpt_length', 55));
        }
        return '';
    }

    protected function getThingImage(): string
    {
        if (is_singular()) {
            return (string) get_the_post_thumbnail_url(null, 'large');
        }
        // You will need to use the "site-reviews/schema/<schema_type>"
        // filter hook to set the image for archive pages.
        return '';
    }

    protected function getThingName(): string
    {
        if (is_archive()) {
            return wp_strip_all_tags(get_the_archive_title());
        }
        if (is_singular()) {
            return get_the_title();
        }
        return '';
    }

    protected function getThingUrl(): string
    {
        $queried = get_queried_object();
        if (is_singular()) {
            $url = (string) get_the_permalink();
        } elseif (is_category()) {
            $url = get_category_link($queried);
        } elseif (is_tag()) {
            $url = get_tag_link($queried);
        } elseif (is_author()) {
            $url = get_author_posts_url($queried->ID);
        } elseif (is_post_type_archive()) {
            $url = get_post_type_archive_link($queried->name);
        } elseif (is_tax()) {
            $url = get_term_link($queried);
        }
        if (!empty($url) && !is_wp_error($url)) {
            return (string) $url;
        }
        return '';
    }

    protected function setAndGetKeyValue(string $option, string $value): string
    {
        $this->keyValues[$option] = $value;
        return $value;
    }
}
