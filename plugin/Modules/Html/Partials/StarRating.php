<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Contracts\PartialContract;

class StarRating implements PartialContract
{
    /**
     * @return void|string
     */
    public function build(array $args = [])
    {
        require_once ABSPATH.'wp-admin/includes/template.php';
        ob_start();
        wp_star_rating($args);
        $stars = ob_get_clean();
        return !glsr()->isAdmin()
            ? str_replace(['star-rating', 'star star'], ['glsr-stars', 'glsr-star glsr-star'], $stars)
            : $stars;
    }
}
