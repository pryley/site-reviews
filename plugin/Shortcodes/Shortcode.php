<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;
use GeminiLabs\SiteReviews\Defaults\ShortcodeApiFetchDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Multilingual;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Modules\Style;

abstract class Shortcode implements ShortcodeContract
{
    public array $args = [];
    public string $debug = '';
    public string $description;
    public string $name;
    public string $shortcode;
    public string $type = '';

    public function __construct()
    {
        $this->description = $this->shortcodeDescription();
        $this->name = $this->shortcodeName();
        $this->shortcode = $this->shortcodeTag();
    }

    public function apiFetchResponse(\WP_REST_Request $request): array
    {
        $args = glsr(ShortcodeApiFetchDefaults::class)->merge($request->get_params());
        $args = glsr()->args($args);
        $method = Helper::buildMethodName('get', $args->option, 'options');
        if (method_exists($this, $method)) {
            $values = $this->$method($args);
        } else {
            $values = glsr()->filterArray('shortcode/api-fetch', [], $args, $request);
        }
        $results = [];
        foreach ($values as $id => $title) {
            $results[] = compact('id', 'title');
        }
        return $results;
    }

    public function attributes(array $values, string $source = 'function'): array
    {
        $attributes = $this->defaults()->dataAttributes($values);
        $attributes = wp_parse_args($attributes, [
            'class' => glsr(Style::class)->styleClasses(),
            'data-from' => $source,
            'data-shortcode' => $this->shortcode,
            'id' => Arr::get($values, 'id'),
        ]);
        unset($attributes['data-id']);
        unset($attributes['data-form_id']);
        $attributes = glsr()->filterArray("shortcode/{$this->shortcode}/attributes", $attributes, $this);
        $attributes = array_map('esc_attr', $attributes);
        return $attributes;
    }

    public function build($args = [], string $type = 'shortcode'): string
    {
        $this->normalize($args, $type);
        $template = $this->buildTemplate();
        $attributes = $this->attributes($this->args, $type);
        $html = glsr(Builder::class)->div($template, $attributes);
        return sprintf('%s%s', $this->debug, $html);
    }

    /**
     * @param string|array $args
     */
    public function buildBlock($args = []): string
    {
        return $this->build(wp_parse_args($args), 'block');
    }

    /**
     * @param string|array $args
     */
    public function buildShortcode($args = []): string
    {
        return $this->build(wp_parse_args($args), 'shortcode');
    }

    public function defaults(): DefaultsAbstract
    {
        $classname = str_replace('Shortcodes\\', 'Defaults\\', get_class($this));
        $classname = str_replace('Shortcode', 'Defaults', $classname);
        return glsr($classname);
    }

    public function getConfig(): array
    {
        $config = $this->config();
        return glsr()->filterArray('shortcode/config', $config, $this->shortcode, $this);
    }

    public function getAssignedPostsOptions(Arguments $args): array
    {
        $results = [];
        if (!empty($args->search)
            && !in_array($args->search, ['post_id', 'parent_id'])) {
            $results += glsr(Database::class)->posts([
                'posts_per_page' => 50,
                's' => $args->search,
            ]);
        }
        $include = array_filter($args->include, fn ($id) => !array_key_exists($id, $results));
        if (!empty($include)) {
            $results += glsr(Database::class)->posts([
                'post__in' => $include,
            ]);
        }
        return [
            'post_id' => esc_html_x('The Current Page', 'admin-text', 'site-reviews'),
            'parent_id' => esc_html_x('The Parent Page', 'admin-text', 'site-reviews'),
        ] + $results;
    }

    public function getAssignedTermsOptions(Arguments $args): array
    {
        $results = [];
        if (!empty($args->search)) {
            $results += glsr(Database::class)->terms([
                'number' => 50,
                'search' => $args->search,
            ]);
        }
        if (!empty($args->include)) {
            $results += glsr(Database::class)->terms([
                'term_taxonomy_id' => $args->include,
            ]);
        }
        return $results;
    }

    public function getAssignedUsersOptions(Arguments $args): array
    {
        $results = [];
        if (!empty($args->search)
            && !in_array($args->search, ['author_id', 'profile_id', 'user_id'])) {
            $results += glsr(Database::class)->users([
                'number' => 50,
                'search_wild' => $args->search,
            ]);
        }
        $include = array_filter($args->include, fn ($id) => !array_key_exists($id, $results));
        if (!empty($include)) {
            $results += glsr(Database::class)->users([
                'include' => $include,
            ]);
        }
        return [
            'user_id' => esc_html_x('The Logged In User', 'admin-text', 'site-reviews'),
            'author_id' => esc_html_x('The Page Author', 'admin-text', 'site-reviews'),
            'profile_id' => esc_html_x('The Profile User', 'admin-text', 'site-reviews'),
        ] + $results;
    }

    public function getDisplayOptions(): array
    {
        $options = $this->displayOptions();
        return glsr()->filterArray('shortcode/display-options', $options, $this->shortcode, $this);
    }

    public function getHideOptions(): array
    {
        $options = $this->hideOptions();
        return glsr()->filterArray('shortcode/hide-options', $options, $this->shortcode, $this);
    }

    public function getPaginationOptions(): array
    {
        return [
            'loadmore' => _x('Load More Button', 'admin-text', 'site-reviews'),
            'ajax' => _x('Pagination Links (AJAX)', 'admin-text', 'site-reviews'),
            '1' => _x('Pagination Links (with page reload)', 'admin-text', 'site-reviews'),
        ];
    }

    public function getPostIdOptions(Arguments $args): array
    {
        glsr_log($args);
        $results = [];
        if (!empty($args->search)) {
            $results += glsr(Database::class)->posts([
                'post_type' => glsr()->post_type,
                'posts_per_page' => 50,
                's' => $args->search,
            ]);
        }
        $include = array_filter($args->include, fn ($id) => !array_key_exists($id, $results));
        if (!empty($include)) {
            $results += glsr(Database::class)->posts([
                'post_type' => glsr()->post_type,
                'post__in' => $include,
            ]);
        }
        return $results;
    }

    public function getTermsOptions(): array
    {
        return [
            '1' => _x('Terms were accepted', 'admin-text', 'site-reviews'),
            '0' => _x('Terms were not accepted', 'admin-text', 'site-reviews'),
        ];
    }

    public function getTypeOptions(): array
    {
        $types = glsr()->retrieveAs('array', 'review_types', []);
        return 1 < count($types) ? $types : [];
    }

    public function hasVisibleFields(array $args = []): bool
    {
        if (!empty($args)) {
            $this->normalize($args);
        }
        $defaults = $this->getHideOptions();
        $hide = $this->args['hide'] ?? [];
        $hide = array_flip(Arr::consolidate($hide));
        unset($defaults['if_empty'], $hide['if_empty']);
        return !empty(array_diff_key($defaults, $hide));
    }

    /**
     * @return static
     */
    public function normalize(array $args, string $type = '')
    {
        if (!empty($type)) {
            $this->type = $type;
        }
        $args = glsr()->filterArray('shortcode/args', $args, $this->shortcode);
        $args = $this->defaults()->unguardedRestrict($args);
        foreach ($args as $key => &$value) {
            $method = Helper::buildMethodName('normalize', $key);
            if (method_exists($this, $method)) {
                $value = call_user_func([$this, $method], $value, $args);
            }
        }
        $this->args = $args;
        return $this;
    }

    public function register(): void
    {
        if (!function_exists('add_shortcode')) {
            return;
        }
        $shortcode = (new \ReflectionClass($this))->getShortName();
        $shortcode = Str::snakeCase($shortcode);
        $shortcode = str_replace('_shortcode', '', $shortcode);
        add_shortcode($shortcode, [$this, 'buildShortcode']);
        glsr()->append('shortcodes', get_class($this), $shortcode);
    }

    protected function config(): array
    {
        return [];
    }

    protected function debug(array $data = []): void
    {
        if (empty($this->args['debug']) || 'shortcode' !== $this->type) {
            return;
        }
        $data = wp_parse_args($data, [
            'args' => $this->args,
            'shortcode' => $this->shortcode,
        ]);
        ksort($data);
        ob_start();
        glsr_debug($data);
        $this->debug = ob_get_clean();
    }

    protected function displayOptions(): array
    {
        return [];
    }

    protected function hideOptions(): array
    {
        return [];
    }

    /**
     * @param string $value
     */
    protected function normalizeAssignedPosts($value): string
    {
        $values = Cast::toArray($value);
        $postTypes = [];
        foreach ($values as $postType) {
            if (!is_numeric($postType) && post_type_exists((string) $postType)) {
                $postTypes[] = $postType;
            }
        }
        $values = glsr(Sanitizer::class)->sanitizePostIds($values);
        $values = glsr(Multilingual::class)->getPostIdsForAllLanguages($values);
        $values = array_merge($values, $postTypes);
        return implode(',', $values);
    }

    /**
     * @param string $value
     */
    protected function normalizeAssignedTerms($value): string
    {
        $values = glsr(Sanitizer::class)->sanitizeTermIds($value);
        $values = glsr(Multilingual::class)->getTermIdsForAllLanguages($values);
        return implode(',', $values);
    }

    /**
     * @param string $value
     */
    protected function normalizeAssignedUsers($value): string
    {
        $values = glsr(Sanitizer::class)->sanitizeUserIds($value);
        return implode(',', $values);
    }

    /**
     * @param string|array $value
     */
    protected function normalizeHide($value): array
    {
        $hideKeys = array_keys($this->getHideOptions());
        return array_filter(Cast::toArray($value),
            fn ($value) => in_array($value, $hideKeys)
        );
    }

    /**
     * @param string $value
     */
    protected function normalizeLabels($value): array
    {
        $defaults = [
            __('Excellent', 'site-reviews'),
            __('Very good', 'site-reviews'),
            __('Average', 'site-reviews'),
            __('Poor', 'site-reviews'),
            __('Terrible', 'site-reviews'),
        ];
        $maxRating = Rating::max();
        $defaults = array_pad(array_slice($defaults, 0, $maxRating), $maxRating, '');
        $labels = array_map('trim', explode(',', $value));
        foreach ($defaults as $i => $label) {
            if (!empty($labels[$i])) {
                $defaults[$i] = $labels[$i];
            }
        }
        return array_combine(range($maxRating, 1), $defaults);
    }

    /**
     * @todo make this an abstract method in v8
     */
    protected function shortcodeDescription(): string
    {
        return '';
    }

    /**
     * @todo make this an abstract method in v8
     */
    protected function shortcodeName(): string
    {
        return '';
    }

    protected function shortcodeTag(): string
    {
        return Str::snakeCase(
            str_replace('Shortcode', '', (new \ReflectionClass($this))->getShortName())
        );
    }
}
