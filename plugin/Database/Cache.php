<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Application;

class Cache
{
    /**
     * @return array
     */
    public function getCloudflareIps()
    {
        if (false === ($ipAddresses = get_transient(Application::ID.'_cloudflare_ips'))) {
            $ipAddresses = array_fill_keys(['v4', 'v6'], []);
            foreach (array_keys($ipAddresses) as $version) {
                $url = 'https://www.cloudflare.com/ips-'.$version;
                $response = wp_remote_get($url, ['sslverify' => false]);
                if (is_wp_error($response)) {
                    glsr_log()->error($response->get_error_message());
                    continue;
                }
                if ('200' != ($statusCode = wp_remote_retrieve_response_code($response))) {
                    glsr_log()->error('Unable to connect to '.$url.' ['.$statusCode.']');
                    continue;
                }
                $ipAddresses[$version] = array_filter(
                    (array) preg_split('/\R/', wp_remote_retrieve_body($response))
                );
            }
            set_transient(Application::ID.'_cloudflare_ips', $ipAddresses, WEEK_IN_SECONDS);
        }
        return $ipAddresses;
    }

    /**
     * @param string $metaKey
     * @return array
     */
    public function getReviewCountsFor($metaKey)
    {
        $counts = wp_cache_get(Application::ID, $metaKey.'_count');
        if (false === $counts) {
            $counts = [];
            $results = glsr(SqlQueries::class)->getReviewCountsFor($metaKey);
            foreach ($results as $result) {
                $counts[$result->name] = $result->num_posts;
            }
            wp_cache_set(Application::ID, $counts, $metaKey.'_count');
        }
        return $counts;
    }

    /**
     * @return string
     */
    public function getRemotePostTest()
    {
        if (false === ($test = get_transient(Application::ID.'_remote_post_test'))) {
            $response = wp_remote_post('https://api.wordpress.org/stats/php/1.0/');
            $test = !is_wp_error($response) && in_array($response['response']['code'], range(200, 299))
                ? 'Works'
                : 'Does not work';
            set_transient(Application::ID.'_remote_post_test', $test, WEEK_IN_SECONDS);
        }
        return $test;
    }
}
