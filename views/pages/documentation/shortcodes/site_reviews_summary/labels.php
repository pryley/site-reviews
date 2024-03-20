<?php defined('ABSPATH') || exit; ?>

<p class="glsr-heading">labels</p>
<p>The "labels" option allows you to enter custom labels for the percentage bar levels (from high to low), each level should be separated with a comma. However, rather than using this option to change the labels it's recommended to instead create a custom translation for them in the <code><a href="<?php echo glsr_admin_url('settings', 'strings'); ?>">Settings &rarr; Strings</a></code> page.</p>
<p>The default labels value is: <code>Excellent,Very good,Average,Poor,Terrible</code></p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews_summary labels="5 star,4 star,3 star,2 star,1 star"]</code></pre>
</div>
