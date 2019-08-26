<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Database\OptionManager;
use Akismet as AkismetPlugin;

class Akismet
{
    /**
     * @return bool
     */
    public function isSpam(array $review)
    {
        if (!$this->isActive()) {
            return false;
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
            if (is_array($value) || in_array($key, ['HTTP_COOKIE', 'HTTP_COOKIE2', 'PHP_AUTH_PW'])) {
                continue;
            }
            $submission[$key] = $value;
        }
        return $this->check(apply_filters('site-reviews/akismet/submission', $submission, $review));
    }

    /**
     * @return bool
     */
    protected function check(array $submission)
    {
        $response = AkismetPlugin::http_post($this->buildQuery($submission), 'comment-check');
        return apply_filters('site-reviews/akismet/is-spam',
            'true' == $response[1],
            $submission,
            $response
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
            : (bool) AkismetPlugin::get_api_key();
        return apply_filters('site-reviews/akismet/is-active', $check);
    }
}
