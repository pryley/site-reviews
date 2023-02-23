<?php defined('ABSPATH') || exit; ?>

<p class="glsr-heading">pagination</p>
<p>Include the "pagination" option to display reviews in multiple pages (i.e. Page 1, Page 2, etc.). The value can be "true", "ajax", "loadmore", or "false". The "ajax" value allows you to load the next page of reviews without loading a new page. The "loadmore" value will display a "Load More" button to insert the next page of reviews at the end of the existing reviews. When using pagination, only one [site_reviews] shortcode can be used on a page at a time.</p>
<p>The default pagination value is: <code>false</code></p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews pagination="ajax"]</code></pre>
</div>
