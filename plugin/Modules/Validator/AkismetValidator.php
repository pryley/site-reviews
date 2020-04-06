<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use Akismet;

class AkismetValidator
{
    /**
     * @return bool
     */
    public function isValid(array $review)
    {
        if (!$this->isActive()) {
            return true;
        }
        $submission = [
            'blog' => glsr(OptionManager::class)->getWP('home'),
            'blog_charset' => glsr(OptionManager::class)->getWP('blog_charset', 'UTF-8'),
            'blog_lang' => get_locale(),
            'comment_author' => $review['name'],
            'comment_author_email' => $review['email'],
            'comment_content' => $review['title']."\n\n".$review['content'],
            'comment_type' => 'review',
            'referrer' => filter_input(INPUT_SERVER, 'HTTP_REFERER'),
            'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
            'user_ip' => $review['ip_address'],
            // 'user_role' => 'administrator',
            // 'is_test' => 1,
        ];
        foreach ($_SERVER as $key => $value) {
            if (!is_array($value) && !in_array($key, ['HTTP_COOKIE', 'HTTP_COOKIE2', 'PHP_AUTH_PW'])) {
                $submission[$key] = $value;
            }
        }
        return $this->validate(
            Arr::consolidate(apply_filters('site-reviews/validate/akismet/submission', $submission, $review))
        );
    }

    /**
     * @return string
     */
    protected function buildQuery(array $data)
    {
        $query = [];
        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }
            if (false === $value) {
                $value = '0';
            }
            $value = trim($value);
            if (!strlen($value)) {
                continue;
            }
            $query[] = urlencode($key).'='.urlencode($value);
        }
        return implode('&', $query);
    }

    /**
     * @return bool
     */
    protected function isActive()
    {
        $check = !glsr(OptionManager::class)->getBool('settings.submissions.akismet')
            || !is_callable(['Akismet', 'get_api_key'])
            || !is_callable(['Akismet', 'http_post'])
            ? false
            : Akismet::get_api_key();
        return wp_validate_boolean(
            apply_filters('site-reviews/validate/akismet/is-active', $check)
        );
    }

    /**
     * @return bool
     */
    protected function validate(array $submission)
    {
        $response = Akismet::http_post($this->buildQuery($submission), 'comment-check');
        $result = 'true' !== $response[1];
        return wp_validate_boolean(
            apply_filters('site-reviews/validate/akismet', $result, $submission, $response)
        );
    }
}
