<?php

defined('WPINC') || die;

/*
 * Alternate method of using the functions without having to use `function_exists()`
 * Example: apply_filters('glsr_get_reviews', [], ['assigned_to' => 'post_id']);
 * @param mixed ...
 * @return mixed
 */
add_filter('plugins_loaded', function () {
    $hooks = array(
        'glsr_calculate_ratings' => 1,
        'glsr_create_review' => 2,
        'glsr_debug' => 10,
        'glsr_get' => 4,
        'glsr_get_option' => 4,
        'glsr_get_options' => 1,
        'glsr_get_review' => 2,
        'glsr_get_reviews' => 2,
        'glsr_log' => 3,
        'glsr_star_rating' => 2,
    );
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
function glsr($alias = null)
{
    $app = \GeminiLabs\SiteReviews\Application::load();
    return !is_null($alias)
        ? $app->make($alias)
        : $app;
}

/**
 * array_column() alternative specifically for PHP v7.0.x.
 * @param $column string
 * @return array
 */
function glsr_array_column(array $array, $column)
{
    $result = array();
    foreach ($array as $subarray) {
        $subarray = (array) $subarray;
        if (!isset($subarray[$column])) {
            continue;
        }
        $result[] = $subarray[$column];
    }
    return $result;
}

/**
 * @return void
 */
function glsr_calculate_ratings()
{
    glsr('Database\CountsManager')->updateAll();
    glsr_log()->notice(__('Recalculated rating counts.', 'site-reviews'));
}

/**
 * @return \GeminiLabs\SiteReviews\Review|false
 */
function glsr_create_review($reviewValues = array())
{
    $review = new \GeminiLabs\SiteReviews\Commands\CreateReview(
        \GeminiLabs\SiteReviews\Helpers\Arr::consolidateArray($reviewValues)
    );
    return glsr('Database\ReviewManager')->create($review);
}

/**
 * @return \WP_Screen|object
 */
function glsr_current_screen()
{
    if (function_exists('get_current_screen')) {
        $screen = get_current_screen();
    }
    return empty($screen)
        ? (object) array_fill_keys(['base', 'id', 'post_type'], null)
        : $screen;
}

/**
 * @param mixed ...$vars
 * @return void
 */
function glsr_debug(...$vars)
{
    if (1 == count($vars)) {
        $value = htmlspecialchars(print_r($vars[0], true), ENT_QUOTES, 'UTF-8');
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
 * @param array $data
 * @param string $path
 * @param mixed $fallback
 * @return mixed
 */
function glsr_get($array, $path = '', $fallback = '')
{
    return \GeminiLabs\SiteReviews\Helpers\Arr::get($array, $path, $fallback);
}

/**
 * @param string $path
 * @param mixed $fallback
 * @param string $cast
 * @return string|array
 */
function glsr_get_option($path = '', $fallback = '', $cast = '')
{
    return is_string($path)
        ? glsr('Database\OptionManager')->get(\GeminiLabs\SiteReviews\Helpers\Str::prefix('settings.', $path), $fallback, $cast)
        : $fallback;
}

/**
 * @return array
 */
function glsr_get_options()
{
    return glsr('Database\OptionManager')->get('settings');
}

/**
 * @param \WP_Post|int $post
 * @return \GeminiLabs\SiteReviews\Review
 */
function glsr_get_review($post)
{
    if (is_numeric($post)) {
        $post = get_post($post);
    }
    if (!($post instanceof WP_Post)) {
        $post = new WP_Post((object) []);
    }
    return glsr('Database\ReviewManager')->single($post);
}

/**
 * @return array
 */
function glsr_get_reviews($args = array())
{
    return glsr('Database\ReviewManager')->get(\GeminiLabs\SiteReviews\Helpers\Arr::consolidateArray($args));
}

/**
 * @return \GeminiLabs\SiteReviews\Modules\Console
 */
function glsr_log()
{
    $args = func_get_args();
    $console = glsr('Modules\Console');
    if ($value = \GeminiLabs\SiteReviews\Helpers\Arr::get($args, '0')) {
        return $console->debug($value, \GeminiLabs\SiteReviews\Helpers\Arr::get($args, '1', []));
    }
    return $console;
}

/**
 * @return string
 */
function glsr_star_rating($rating)
{
    return glsr('Modules\Html\Partial')->build('star-rating', ['rating' => $rating]);
}
