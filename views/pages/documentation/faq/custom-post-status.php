<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-custom-post-status">
            <span class="title">How do I display reviews assigned to a post which uses a custom post_status?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-custom-post-status" class="inside">
        <p>When you use the <code>assigned_posts</code> option to display reviews that have been assigned to a post, Site Reviews will only display the reviews if the assigned post is published (i.e. the post's <code>post_status</code> is "publish").</p>
        <p>If you have assigned reviews to a Custom Post Type which uses a custom post status, then you can use these hooks to tell Site Reviews that the custom post status should be treated the same as the "publish" post status.</p>
        <pre><code class="language-php">/**
 * @param bool $isPublished
 * @param int|\WP_Post $postId
 * @return bool
 */
add_filter('site-reviews/post/is-published', function ($isPublished, $postId) {
    if ('work' !== get_post_type($postId)) { // check the custom post type
        return $isPublished;
    }
    if ('completed' !== get_post_status($postId)) { // check the custom post status
        return $isPublished;
    }
    return true;
}, 10, 2);</code></pre>

        <pre><code class="language-php">/**
 * @param array $postStatuses
 * @param string $searchType
 * @return array
 */
add_filter('site-reviews/search/posts/post_status', function ($postStatuses, $searchType) {
    if ('assigned_posts' === $searchType) {
        $postStatuses[] = 'completed';
    }
    return $postStatuses;
}, 10, 2);</code></pre>
    </div>
</div>
