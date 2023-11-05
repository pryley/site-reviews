<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface MetaboxContract
{
    /**
     * @param \WP_Post $post
     */
    public function register($post): void;

    /**
     * @param \WP_Post $post
     */
    public function render($post): void;
}
