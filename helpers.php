<?php

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\BlackHole;
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\RatingManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Exceptions\BindingResolutionException;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Backtrace;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Dump;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Partial;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Reviews;

defined('ABSPATH') || exit;

/*
 * Alternate method of using the functions without having to use `function_exists()`
 * Example: apply_filters('glsr_get_reviews', [], ['assigned_posts' => 'post_id']);
 */
add_action('plugins_loaded', function () {
    $hooks = [
        'glsr_create_review' => 2,
        'glsr_debug' => 10,
        'glsr_get' => 4,
        'glsr_get_option' => 4,
        'glsr_get_options' => 1,
        'glsr_get_ratings' => 2,
        'glsr_get_review' => 2,
        'glsr_get_reviews' => 2,
        'glsr_log' => 3,
        'glsr_star_rating' => 4,
        'glsr_trace' => 2,
        'glsr_update_review' => 3,
    ];
    foreach ($hooks as $function => $acceptedArgs) {
        add_filter($function, function () use ($function) {
            $args = func_get_args();
            array_shift($args); // remove the fallback value
            return call_user_func_array($function, $args);
        }, 10, $acceptedArgs);
    }
});

/**
 * @return mixed
 */
function glsr($alias = null, array $parameters = [])
{
    if (is_null($alias)) {
        return Application::load();
    }
    try {
        return Application::load()->make($alias, $parameters);
    } catch (BindingResolutionException $e) {
        glsr_log()->error($e->getMessage());
        return Application::load()->make(BlackHole::class, compact('alias'));
    }
}

function glsr_admin_url(string $page = '', string $tab = '', string $sub = ''): string
{
    if ('welcome' === $page) {
        $page = glsr()->id.'-welcome';
        $args = array_filter(compact('page', 'tab'));
        return add_query_arg($args, admin_url('index.php'));
    }
    if (!empty($page)) {
        $page = Str::dashCase(glsr()->prefix.$page);
    }
    $post_type = glsr()->post_type;
    $args = array_filter(compact('post_type', 'page', 'tab', 'sub'));
    return add_query_arg($args, admin_url('edit.php'));
}

/**
 * @param string|array $attrs
 */
function glsr_admin_link(string $path = '', $attrs = [], string $expand = ''): string
{
    $parts = explode('.', $path);
    $parts = array_slice(array_pad(array_filter($parts, 'is_string'), 3, ''), 0, 3);
    $text = trim(is_string($attrs) ? $attrs : ($attrs['text'] ?? ''));
    if (empty($text)) {
        $texts = [
            'addons' => _x('Addons', 'admin-text', 'site-reviews'),
            'api' => _x('API', 'admin-text', 'site-reviews'),
            'console' => _x('Console', 'admin-text', 'site-reviews'),
            'documentation' => _x('Help & Support', 'admin-text', 'site-reviews'),
            'faq' => _x('FAQ', 'admin-text', 'site-reviews'),
            'forms' => _x('Forms', 'admin-text', 'site-reviews'),
            'functions' => _x('Functions', 'admin-text', 'site-reviews'),
            'general' => _x('General', 'admin-text', 'site-reviews'),
            'hooks' => _x('Hooks', 'admin-text', 'site-reviews'),
            'integrations' => _x('Integrations', 'admin-text', 'site-reviews'),
            'licenses' => _x('Licenses', 'admin-text', 'site-reviews'),
            'profilepress' => 'ProfilePress',
            'reviews' => _x('Reviews', 'admin-text', 'site-reviews'),
            'scheduled' => _x('Scheduled Actions', 'admin-text', 'site-reviews'),
            'schema' => _x('Schema', 'admin-text', 'site-reviews'),
            'settings' => _x('Settings', 'admin-text', 'site-reviews'),
            'shortcodes' => _x('Shortcodes', 'admin-text', 'site-reviews'),
            'strings' => _x('Strings', 'admin-text', 'site-reviews'),
            'support' => _x('Support', 'admin-text', 'site-reviews'),
            'surecart' => 'SureCart',
            'system-info' => _x('System Info', 'admin-text', 'site-reviews'),
            'tools' => _x('Tools', 'admin-text', 'site-reviews'),
            'ultimatemember' => 'Ultimate Member',
            'woocommerce' => 'WooCommerce',
        ];
        $textParts = array_filter([
            $texts[$parts[0]] ?? ucfirst($parts[0]),
            $texts[$parts[1]] ?? ucfirst($parts[1]),
            $texts[$parts[2]] ?? ucfirst($parts[2]),
        ]);
        $text = implode(' &rarr; ', $textParts);
    }
    $url = call_user_func_array('glsr_admin_url', $parts);
    $attrs = Arr::consolidate($attrs);
    $attrs['href'] = $url;
    $attrs['text'] = $text ?: _x('All Reviews', 'admin-text', 'site-reviews');
    return glsr(Builder::class)->a(wp_parse_args($attrs, [
        'data-expand' => $expand,
    ]));
}

/**
 * @return Review|false
 */
function glsr_create_review(array $values = [])
{
    $values = Arr::removeEmptyValues($values);
    $request = new Request($values);
    $review = false;
    glsr()->store('glsr_create_review', true);
    $command = new CreateReview($request);
    if ($command->isRequestValid()) {
        $review = glsr(ReviewManager::class)->create($command);
    }
    glsr()->discard('glsr_create_review');
    return $review;
}

/**
 * @return WP_Screen|object
 */
function glsr_current_screen()
{
    if (function_exists('get_current_screen')) {
        $screen = get_current_screen();
    }
    return empty($screen)
        ? (object) array_fill_keys(['action', 'base', 'id', 'post_type'], '')
        : $screen;
}

/**
 * @param mixed ...$vars
 */
function glsr_debug(...$vars): void
{
    if (1 === count($vars)) {
        $dump = glsr(Dump::class)->dump($vars[0]);
        $value = htmlspecialchars($dump, ENT_QUOTES, 'UTF-8');
        printf('<div class="glsr-debug"><pre>%s</pre></div>', $value);
    } else {
        echo '<div class="glsr-debug-group">';
        foreach ($vars as $var) {
            glsr_debug($var);
        }
        echo '</div>';
    }
}

/**
 * @param string|int $path
 * @param mixed      $fallback
 *
 * @return mixed
 */
function glsr_get($array, $path = '', $fallback = '')
{
    return Arr::get($array, $path, $fallback);
}

/**
 * @param mixed $fallback
 *
 * @return mixed
 */
function glsr_get_option(string $path = '', $fallback = '', string $cast = '')
{
    return glsr(OptionManager::class)->get(Str::prefix($path, 'settings.'), $fallback, $cast);
}

function glsr_get_options(): array
{
    return glsr(OptionManager::class)->get('settings');
}

function glsr_get_ratings(array $args = []): Arguments
{
    $counts = glsr(RatingManager::class)->ratings($args);
    return new Arguments([
        'average' => glsr(Rating::class)->average($counts),
        'maximum' => Rating::max(),
        'minimum' => Rating::min(),
        'ranking' => glsr(Rating::class)->ranking($counts),
        'ratings' => $counts,
        'reviews' => array_sum($counts),
    ]);
}

function glsr_get_review($postId): Review
{
    return glsr(ReviewManager::class)->get(Cast::toInt($postId));
}

function glsr_get_reviews(array $args = []): Reviews
{
    return glsr(ReviewManager::class)->reviews($args);
}

/**
 * @param mixed ...$args
 */
function glsr_log(...$args): Console
{
    $console = glsr(Console::class);
    return !empty($args)
        ? call_user_func_array([$console, 'debug'], $args)
        : $console;
}

function glsr_premium_link(string $path, $attrs = []): string
{
    $url = glsr_premium_url($path);
    $texts = [
        'license-keys' => _x('License Keys', 'admin-text', 'site-reviews'),
        'site-reviews-actions' => _x('Review Actions', 'admin-text', 'site-reviews'),
        'site-reviews-authors' => _x('Review Authors', 'admin-text', 'site-reviews'),
        'site-reviews-filters' => _x('Review Filters', 'admin-text', 'site-reviews'),
        'site-reviews-forms' => _x('Review Forms', 'admin-text', 'site-reviews'),
        'site-reviews-images' => _x('Review Images', 'admin-text', 'site-reviews'),
        'site-reviews-notifications' => _x('Review Notifications', 'admin-text', 'site-reviews'),
        'site-reviews-premium' => _x('Site Reviews Premium', 'admin-text', 'site-reviews'),
        'site-reviews-themes' => _x('Review Themes', 'admin-text', 'site-reviews'),
    ];
    $text = trim(is_string($attrs) ? $attrs : ($attrs['text'] ?? ''));
    $text = $text ?: ($texts[$path] ?? $url);
    $attrs = Arr::consolidate($attrs);
    $attrs['href'] = $url;
    $attrs['target'] = '_blank';
    $attrs['text'] = $text;
    return glsr(Builder::class)->a($attrs);
}

function glsr_premium_url(string $path = '/'): string
{
    $baseUrl = 'https://niftyplugins.com';
    $paths = [
        'account' => '/account/',
        'addons' => '/plugins/',
        'license-keys' => '/account/license-keys/',
        'site-reviews-actions' => '/plugins/site-reviews-actions/',
        'site-reviews-authors' => '/plugins/site-reviews-authors/',
        'site-reviews-filters' => '/plugins/site-reviews-filters/',
        'site-reviews-forms' => '/plugins/site-reviews-forms/',
        'site-reviews-images' => '/plugins/site-reviews-images/',
        'site-reviews-notifications' => '/plugins/site-reviews-notifications/',
        'site-reviews-premium' => 'https://site-reviews.com/premium/',
        'site-reviews-themes' => '/plugins/site-reviews-themes/',
        'support' => '/account/support/',
    ];
    $urlPath = trim($paths[$path] ?? $path);
    if (str_starts_with($urlPath, 'http')) {
        return esc_url($urlPath);
    }
    $urlPath = trailingslashit(ltrim($urlPath, '/'));
    return esc_url(trailingslashit($baseUrl).$urlPath);
}

/**
 * @param string|int $path
 * @param mixed      $value
 */
function glsr_set(array $data, $path, $value): array
{
    return Arr::set($data, $path, $value);
}

/**
 * @param mixed    $rating
 * @param int|null $reviews
 */
function glsr_star_rating($rating, $reviews = 0, array $args = []): string
{
    return glsr(Partial::class)->build('star-rating', [
        'args' => $args,
        'rating' => $rating,
        'reviews' => $reviews,
    ]);
}

function glsr_trace(int $limit = 5): void
{
    glsr_log(glsr(Backtrace::class)->trace($limit));
}

/**
 * @return Review|false
 */
function glsr_update_review(int $postId, array $values = [])
{
    glsr()->store('glsr_update_review', true);
    $result = glsr(ReviewManager::class)->update($postId, $values);
    glsr()->discard('glsr_update_review');
    return $result;
}

function glsr_user_count(): int
{
    if (function_exists('get_user_count')) {
        return get_user_count();
    }
    return Arr::getAs('int', count_users(), 'total_users');
}
