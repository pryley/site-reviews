<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\Vectorface\Whip\Whip;

class Helper
{
    /**
     * @param array|string $name
     */
    public static function buildClassName($name, string $path = ''): string
    {
        if (is_array($name)) {
            $name = implode('-', $name);
        }
        $className = Str::camelCase($name);
        $path = ltrim(str_replace(__NAMESPACE__, '', $path), '\\');
        return !empty($path)
            ? __NAMESPACE__.'\\'.$path.'\\'.$className
            : $className;
    }

    public static function buildMethodName(string ...$name): string
    {
        $name = implode('-', $name);
        return lcfirst(Str::camelCase($name));
    }

    /**
     * @param int|string $version1
     * @param int|string $version2
     */
    public static function compareVersions($version1, $version2, string $operator = '='): bool
    {
        $version1 = implode('.', array_pad(explode('.', $version1), 3, 0));
        $version2 = implode('.', array_pad(explode('.', $version2), 3, 0));
        return version_compare($version1, $version2, $operator ?: '=');
    }

    /**
     * @return mixed
     */
    public static function filterInput(string $key, array $request = [])
    {
        if (isset($request[$key])) {
            return $request[$key];
        }
        $variable = filter_input(INPUT_POST, $key);
        if (is_null($variable) && isset($_POST[$key])) {
            $variable = $_POST[$key];
        }
        return $variable;
    }

    public static function filterInputArray(string $key): array
    {
        $variable = filter_input(INPUT_POST, $key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (empty($variable) && !empty($_POST[$key]) && is_array($_POST[$key])) {
            $variable = $_POST[$key];
        }
        return Cast::toArray($variable);
    }

    public static function getIpAddress(): string
    {
        $setting = glsr()->args(get_option(glsr()->prefix.'ip_proxy'));
        $proxyHeader = $setting->sanitize('proxy_http_header', 'id');
        $trustedProxies = $setting->sanitize('trusted_proxies', 'text-multiline');
        $trustedProxies = explode("\n", $trustedProxies);
        $whitelist = [];
        if (!empty($proxyHeader)) {
            $ipv4 = array_filter($trustedProxies, function ($range) {
                [$ip] = explode('/', $range);
                return !empty(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4));
            });
            $ipv6 = array_filter($trustedProxies, function ($range) {
                [$ip] = explode('/', $range);
                return !empty(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6));
            });
            $whitelist[$proxyHeader] = [
                Whip::IPV4 => $ipv4,
                Whip::IPV6 => $ipv6,
            ];
        }
        $whitelist = glsr()->filterArray('whip/whitelist', $whitelist);
        $whip = new Whip(Whip::REMOTE_ADDR | Whip::CUSTOM_HEADERS, $whitelist);
        if (!empty($proxyHeader)) {
            $whip->addCustomHeader($proxyHeader);
        }
        glsr()->action('whip', $whip);
        if (false !== ($clientAddress = $whip->getValidIpAddress())) {
            return (string) $clientAddress;
        }
        glsr_log()->error('Unable to detect IP address, please see the FAQ page for a possible solution.');
        return 'unknown';
    }

    public static function getPageNumber(?string $fromUrl = null, ?int $fallback = 1): int
    {
        $pagedQueryVar = glsr()->constant('PAGED_QUERY_VAR');
        $pageNum = empty($fromUrl)
            ? filter_input(INPUT_GET, $pagedQueryVar, FILTER_VALIDATE_INT)
            : filter_var(Url::query($fromUrl, $pagedQueryVar), FILTER_VALIDATE_INT);
        if (empty($pageNum)) {
            $pageNum = (int) $fallback;
        }
        return max(1, $pageNum);
    }

    /**
     * @param mixed $post
     */
    public static function getPostId($post): int
    {
        if (empty($post)) {
            return 0;
        }
        if (is_numeric($post) || $post instanceof \WP_Post) {
            $post = get_post($post);
        }
        if ($post instanceof \WP_Post) {
            return $post->ID;
        }
        if ('parent_id' === $post) {
            $parentId = (int) wp_get_post_parent_id(intval(get_the_ID()));
            return glsr()->filterInt('assigned_posts/parent_id', $parentId);
        }
        if ('post_id' === $post) {
            $postId = (int) get_the_ID();
            return glsr()->filterInt('assigned_posts/post_id', $postId);
        }
        if (is_string($post)) {
            $post = sanitize_text_field($post);
            $parts = explode(':', $post);
            if (2 === count($parts)) {
                $posts = get_posts([
                    'fields' => 'ids',
                    'post_name__in' => [$parts[1]],
                    'post_type' => $parts[0],
                    'posts_per_page' => 1,
                ]);
                return Arr::getAs('int', $posts, 0);
            }
        }
        return 0;
    }

    /**
     * @param mixed $term
     */
    public static function getTermTaxonomyId($term): int
    {
        if ($term instanceof \WP_Term) {
            return $term->term_id;
        }
        if (is_numeric($term)) {
            $term = Cast::toInt($term);
        } else {
            $term = sanitize_text_field(Cast::toString($term));
        }
        $tt = term_exists($term, glsr()->taxonomy);
        $ttid = Arr::getAs('int', $tt, 'term_id');
        return glsr()->filterInt('assigned_terms/term_id', $ttid, $term, glsr()->taxonomy);
    }

    /**
     * @param mixed $user
     */
    public static function getUserId($user): int
    {
        if ($user instanceof \WP_User) {
            return $user->ID;
        }
        if ('author_id' === $user) {
            $authorId = Cast::toInt(get_the_author_meta('ID'));
            return glsr()->filterInt('assigned_users/author_id', $authorId);
        }
        if ('profile_id' === $user) {
            $profileId = glsr()->filterInt('assigned_users/profile_id', 0);
            if (empty($profileId) && is_author()) {
                $profileId = get_queried_object_id(); // is_author() ensures this is a User ID
            }
            return $profileId;
        }
        if ('user_id' === $user) {
            return glsr()->filterInt('assigned_users/user_id', get_current_user_id());
        }
        if (is_numeric($user)) {
            $user = get_user_by('id', $user);
            return Arr::getAs('int', $user, 'ID');
        }
        if (is_string($user)) {
            $user = get_user_by('login', sanitize_user($user, true));
            return Arr::getAs('int', $user, 'ID');
        }
        return 0;
    }

    /**
     * @param mixed $value
     * @param mixed $fallback
     *
     * @return mixed
     */
    public static function ifEmpty($value, $fallback, $strict = false)
    {
        $isEmpty = $strict ? empty($value) : static::isEmpty($value);
        return $isEmpty ? $fallback : $value;
    }

    /**
     * @param mixed $ifTrue
     * @param mixed $ifFalse
     *
     * @return mixed
     */
    public static function ifTrue(bool $condition, $ifTrue, $ifFalse = null)
    {
        return $condition ? static::runClosure($ifTrue) : static::runClosure($ifFalse);
    }

    /**
     * @param mixed      $value
     * @param string|int $min
     * @param string|int $max
     */
    public static function inRange($value, $min, $max): bool
    {
        $inRange = filter_var($value, FILTER_VALIDATE_INT, ['options' => [
            'min_range' => intval($min),
            'max_range' => intval($max),
        ]]);
        return false !== $inRange;
    }

    /**
     * @param mixed $value
     */
    public static function isEmpty($value): bool
    {
        if (is_string($value)) {
            return '' === trim($value);
        }
        return !is_numeric($value) && !is_bool($value) && empty($value);
    }

    /**
     * @param int|string $value
     * @param int|string $compareWithValue
     */
    public static function isGreaterThan($value, $compareWithValue): bool
    {
        return static::compareVersions($value, $compareWithValue, '>');
    }

    /**
     * @param int|string $value
     * @param int|string $compareWithValue
     */
    public static function isGreaterThanOrEqual($value, $compareWithValue): bool
    {
        return static::compareVersions($value, $compareWithValue, '>=');
    }

    /**
     * @param int|string $value
     * @param int|string $compareWithValue
     */
    public static function isLessThan($value, $compareWithValue): bool
    {
        return static::compareVersions($value, $compareWithValue, '<');
    }

    /**
     * @param int|string $value
     * @param int|string $compareWithValue
     */
    public static function isLessThanOrEqual($value, $compareWithValue): bool
    {
        return static::compareVersions($value, $compareWithValue, '<=');
    }

    public static function isLocalServer(): bool
    {
        $host = static::ifEmpty(filter_input(INPUT_SERVER, 'HTTP_HOST'), 'localhost');
        $ipAddress = static::ifEmpty(filter_input(INPUT_SERVER, 'SERVER_ADDR'), '::1');
        $result = false;
        if (in_array($ipAddress, ['127.0.0.1', '::1'])
            || !mb_strpos($host, '.')
            || in_array(mb_strrchr($host, '.'), ['.test', '.testing', '.local', '.localhost', '.localdomain'])) {
            $result = true;
        }
        return glsr()->filterBool('is-local-server', $result);
    }

    /**
     * @param mixed $value
     */
    public static function isNotEmpty($value): bool
    {
        return !static::isEmpty($value);
    }

    /**
     * @return int|false
     */
    public static function remoteStatusCheck(string $url)
    {
        $response = wp_safe_remote_head($url, [
            'sslverify' => !static::isLocalServer(),
        ]);
        if (!is_wp_error($response)) {
            return $response['response']['code'];
        }
        return false;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public static function runClosure($value)
    {
        if ($value instanceof \Closure || (is_array($value) && is_callable($value))) {
            return call_user_func($value);
        }
        return $value;
    }

    public static function version(string $version, string $versionLevel = ''): string
    {
        $pattern = '/^v?(\d{1,5})(\.\d++)?(\.\d++)?(.+)?$/i';
        preg_match($pattern, $version, $matches);
        switch ($versionLevel) {
            case 'major':
                $result = Arr::get($matches, 1);
                break;
            case 'minor':
                $result = Arr::get($matches, 1).Arr::get($matches, 2);
                break;
            case 'patch':
                $result = Arr::get($matches, 1).Arr::get($matches, 2).Arr::get($matches, 3);
                break;
            default:
                $result = $version;
                break;
        }
        return $result;
    }
}
