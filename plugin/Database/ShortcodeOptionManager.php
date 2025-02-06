<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Defaults\ShortcodeApiFetchDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class ShortcodeOptionManager
{
    /**
     * The parameter passed to the called method can be either a shortcode tag,
     * an instantiated shortcode class, or an array with key/values found in
     * ShortcodeApiFetchDefaults::class
     * 
     * @return array
     */
    public function __call(string $name, array $arguments)
    {
        $shortcode = array_shift($arguments);
        if (is_string($shortcode)) {
            $shortcode = glsr()->shortcode($shortcode);
        }
        if ($shortcode instanceof ShortcodeContract) {
            $args = [
                'option' => Str::snakeCase($name),
                'shortcode' => $shortcode->tag,
            ];
        } else {
            $args = Arr::consolidate($shortcode);
        }
        $args = glsr()->args(glsr(ShortcodeApiFetchDefaults::class)->merge($args));
        $method = Helper::buildMethodName($name);
        $results = method_exists($this, $method)
            ? call_user_func([$this, $method], $args)
            : [];
        $results = glsr()->filterArray("shortcode/options/{$name}", $results, $args);
        if (!empty($results) && !empty($args->placeholder)) {
            $results = Arr::prepend($results, esc_attr($args->placeholder), '');
        }
        return $results;
    }

    protected function assignedPosts(Arguments $args): array
    {
        $results = [];
        if (!empty($args->search)
            && !in_array($args->search, ['post_id', 'parent_id'])) {
            $results += glsr(Database::class)->posts([
                // @see MainController::parseAssignedPostTypesInQuery
                'post_type' => glsr()->prefix.'assigned_posts',
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

    protected function assignedTerms(Arguments $args): array
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

    protected function assignedUsers(Arguments $args): array
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

    protected function hide(Arguments $args): array
    {
        if ($shortcode = glsr()->shortcode($args->shortcode)) {
            $fn = fn () => $this->hideOptions(); // @phpstan-ignore-line
            return $fn->bindTo($shortcode, $shortcode)();
        }
        return [];
    }

    protected function pagination(): array
    {
        return [
            'loadmore' => _x('Load More Button', 'admin-text', 'site-reviews'),
            'ajax' => _x('Pagination Links (AJAX)', 'admin-text', 'site-reviews'),
            'true' => _x('Pagination Links (with page reload)', 'admin-text', 'site-reviews'),
        ];
    }

    protected function postId(Arguments $args): array
    {
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

    protected function schema(): array
    {
        return [
            'true' => _x('Enable rich snippets', 'admin-text', 'site-reviews'),
            'false' => _x('Disable rich snippets', 'admin-text', 'site-reviews'),
        ];
    }

    protected function terms(): array
    {
        return [
            'true' => _x('Terms were accepted', 'admin-text', 'site-reviews'),
            'false' => _x('Terms were not accepted', 'admin-text', 'site-reviews'),
        ];
    }

    protected function type(): array
    {
        $types = glsr()->retrieveAs('array', 'review_types', []);
        return 1 < count($types) ? $types : [];
    }
}
