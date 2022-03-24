<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface MetaboxContract
{
    /**
     * @param \WP_Post $post
     * @return void
     */
    public function register($post);

    /**
     * @param \WP_Post $post
     * @return void
     */
    public function render($post);
}
