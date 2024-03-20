<?php defined('ABSPATH') || exit; ?>

<p class="glsr-heading">text</p>
<p>The "text" option allows you to change the summary text. Available template tags to use are, "{rating}" which represents the calculated average rating, "{max}" which represents the maximum star rating available, and "{num}" which represents the total number of reviews. However, rather than using this option to change the summary text it's recommended to instead create a custom translation for it in the <code><a href="<?php echo glsr_admin_url('settings', 'strings'); ?>">Settings &rarr; Strings</a></code> page. That way, you will be able to customize both the singular (1 review) and plural (2 reviews) summary texts.</p>
<p>The default text value is: <code>{rating} out of {max} stars (based on {num} reviews)</code></p>
<div class="shortcode-example">
    <pre><code class="language-shortcode">[site_reviews_summary text="{num} customer reviews"]</code></pre>
</div>
