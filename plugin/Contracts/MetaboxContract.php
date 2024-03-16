<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface MetaboxContract
{
    public function register(\WP_Post $post): void;

    public function render(\WP_Post $post): void;
}
