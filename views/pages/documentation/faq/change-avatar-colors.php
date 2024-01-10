<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-change-avatar-initials">
            <span class="title">How do I change the initials avatar colors?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-change-avatar-initials" class="inside">
        <pre><code class="language-php">/**
 * Modifies the colors used by the initials avatars.
 * Paste this in your active theme's functions.php file.
 * @param array $colors
 * @return array
 */
add_filter('site-reviews/avatar/colors', function ($colors) {
    $colors = [
        [ // blue
            'background' => '#e3effb',
            'color' => '#134d92',
        ],
        [ // green
            'background' => '#e1f0ee',
            'color' => '#125960',
        ],
        [ // pink
            'background' => '#ffeff7',
            'color' => '#ba3a80',
        ],
        [ // red
            'background' => '#fcece3',
            'color' => '#a14326',
        ],
        [ // yellow
            'background' => '#faf7d9',
            'color' => '#da9640',
        ],
    ];
    return $colors;
});</code></pre>
    </div>
</div>
