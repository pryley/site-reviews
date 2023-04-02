<?php defined('ABSPATH') || exit; ?>
<?php require ABSPATH.WPINC.'/version.php'; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-order-query_loop">
            <span class="title">How do I order a Query Loop block by rating or ranking?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-order-query_loop" class="inside">
    <?php if (version_compare($wp_version, '6.1', '<')): ?>
        <div class="glsr-notice-inline components-notice is-warning">
            <p class="components-notice__content">WordPress v6.1 or higher is required in order to do this.</p>
        </div>
    <?php else: ?>
        <p>The <a href="https://wordpress.org/support/article/query-loop-block/" target="_blank">Query Loop</a> block was introduced in WordPress v5.8 and allows you to display posts based on specified parameters; like a PHP loop without the code. Unfortunately, this block does not (yet) allow you to sort the posts using meta key values, so here is a workaround.</p>
        <p>Site Reviews provides three meta keys that can be used for sorting pages that have reviews assigned to them.</p>
        <p>The <code>_glsr_average</code> meta key contains the average rating of the page.</p>
        <p>The <code>_glsr_ranking</code> meta key contains the page rank determined by a bayesian ranking algorithm (the exact same way that films are ranked on IMDB). To understand why sorting by rank may be preferable to sorting by average rating, please see: <a target="_blank" href="https://imgs.xkcd.com/comics/tornadoguard.png">The problem with averaging star ratings</a>.</p>
        <p>The <code>_glsr_reviews</code> meta key contains the number of reviews that have been assigned to the page.</p>
        <p>Here is how you can change the sorting of a Query Loop block to either the average rating, ranking, or number of reviews.</p>
        <p class="glsr-heading">Step 1</p>
        <p>Add the following code snippet your your website:</p>
        <pre><code class="language-php">/**
 * Changes the sorting of a Query Loop block
 * Paste this code in your theme's functions.php file.
 */
add_filter('pre_render_block', function ($prerender, $block) {
    if ('core/query' !== $block['blockName']) {
        return $prerender;
    }
    $metaKey = $block['attrs']['className'] ?? '';
    $metaKeys = ['_glsr_average', '_glsr_ranking', '_glsr_reviews'];
    if (!in_array($metaKey, $metaKeys)) {
        return $prerender;
    }
    add_filter('query_loop_block_query_vars', function ($query) use ($metaKey) {
        $query['meta_query'] = [
            'relation' => 'OR',
            ['key' => $metaKey, 'compare' => 'NOT EXISTS'], // this comes first!
            ['key' => $metaKey, 'compare' => 'EXISTS'],
        ];
        $query['orderby'] = 'meta_value_num';
        return $query;
    });
}, 10, 2);</code></pre>
        <p class="glsr-heading">Step 2</p>
        <p>Add one of the following values to the Query Loop block's "Additional CSS Class(es)" setting (find it in the Advanced panel of the block settings):</p>
        <ul>
            <li><code>_glsr_average</code></li>
            <li><code>_glsr_ranking</code></li>
            <li><code>_glsr_reviews</code></li>
        </ul>
    <?php endif; ?>
    </div>
</div>
