<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-order-elementor-posts">
            <span class="title">How do I order the Elementor Posts widget by rating or ranking?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-order-elementor-posts" class="inside">
        <p>Site Reviews provides three meta keys that can be used for sorting pages that have reviews assigned to them.</p>
        <p>The <code>_glsr_average</code> meta key contains the average rating of the page.</p>
        <p>The <code>_glsr_ranking</code> meta key contains the page rank determined by a bayesian ranking algorithm (the exact same way that films are ranked on IMDB). To understand why sorting by rank may be preferable to sorting by average rating, please see: <a target="_blank" href="https://imgs.xkcd.com/comics/tornadoguard.png">The problem with averaging star ratings</a>.</p>
        <p>The <code>_glsr_reviews</code> meta key contains the number of reviews that have been assigned to the page.</p>
        <p>Here is an example of how you can use these meta keys to sort the posts in an Elementor Posts widget by ranking or rating, regardless of whether or not they have reviews assigned to them:</p>

        <p class="glsr-heading">Step 1</p>
        <p>Add the following code snippet your your website:</p>
        <pre><code class="language-php">add_action('elementor/query/sort_by_ranking', function ($query) {
    $query->set('meta_query', [
        'relation' => 'OR',
        ['key' => '_glsr_ranking', 'compare' => 'NOT EXISTS'], // this comes first!
        ['key' => '_glsr_ranking', 'compare' => 'EXISTS'],
    ]);
    $query->set('orderby', 'meta_value_num');
    $query->set('order', 'DESC');
});

add_action('elementor/query/sort_by_rating', function ($query) {
    $query->set('meta_query', [
        'relation' => 'OR',
        ['key' => '_glsr_rating', 'compare' => 'NOT EXISTS'], // this comes first!
        ['key' => '_glsr_rating', 'compare' => 'EXISTS'],
    ]);
    $query->set('orderby', 'meta_value_num');
    $query->set('order', 'DESC');
});</code></pre>

        <p class="glsr-heading">Step 2</p>
        <p>Add one of the following values to the "Query > Query ID" option in the Elementor Posts widget settings:</p>
        <ul>
            <li><code>sort_by_ranking</code></li>
            <li><code>sort_by_rating</code></li>
        </ul>

    </div>
</div>
