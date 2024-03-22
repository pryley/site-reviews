<?php defined('ABSPATH') || exit; 

if (glsr()->addon('site-reviews-filters')) {
    $filters = array_keys(glsr('site-reviews-filters')->config('forms/filters-form'));
    sort($filters);
    $filters = implode(',', $filters);
} else {
    $filters = 'filter_by_media,filter_by_rating,search_for,sort_by';
}

?>

<p class="glsr-heading">filters</p>
<div class="components-notice is-warning">
    <p class="components-notice__content">The <a href="<?php echo glsr_admin_url('addons'); ?>">Review Filters</a> addon is required to use this shortcode option.</p>
</div>
<p>Include the "filters" option to display the review filters and search bar above the reviews. If you want to display all of the filters, you can also just enter <code>true</code> as the value instead of typing them all in.</p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews filters="<?php echo $filters; ?>"]</code></pre>
</div>
