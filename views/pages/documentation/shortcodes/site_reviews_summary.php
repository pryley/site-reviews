<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="shortcode-site_reviews_summary">
            <span class="title">Display the reviews summary</span>
            <span class="badge code">[site_reviews_summary]</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="shortcode-site_reviews_summary" class="inside">
        <h3>This shortcode displays a summary of your reviews.</h3>
        <div class="notice inline notice-info notice-alt">
            <p>Each example below demonstrates a different shortcode option. If you need to use multiple options, simply combine the options together (separated with a space) in the same shortcode.</p>
        </div>

        <p class="glsr-heading">assigned_posts</p>
        <p>Include the "assigned_posts" option to limit reviews to those assigned posts, pages, or other public post types. Accepted values are a numerical <a href="https://pagely.com/blog/find-post-id-wordpress/">WordPress Post ID</a>, <code>post_id</code> which automatically uses the ID of the current page, or <code>parent_id</code> which automatically uses the ID of the parent page. Separate multiple values with a comma.</p>
        <p><span class="required">Important:</span> If you are using this shortcode together with the [site_reviews] shortcode, make sure to set the same option value for both shortcodes.</p>
        <p>The default assigned_posts value is: <code>""</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews_summary assigned_posts="post_id"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_summary</span> <span class="attr-name">assigned_posts</span>=<span class="attr-value">"post_id"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">assigned_terms</p>
        <p>Include the "assigned_terms" option to limit reviews to those assigned categories. Accepted values are either a category ID or slug. Separate multiple values with a comma.</p>
        <p><span class="required">Important:</span> If you are using this shortcode together with the [site_reviews] shortcode, make sure to set the same option value for both shortcodes.</p>
        <p>The default assigned_terms value is: <code>""</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews_summary assigned_terms="13,14"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_summary</span> <span class="attr-name">assigned_terms</span>=<span class="attr-value">"13,14"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">assigned_users</p>
        <p>Include the "assigned_users" option to limit reviews to those assigned users. Accepted values are either a user ID or username. Separate multiple values with a comma.</p>
        <p><span class="required">Important:</span> If you are using this shortcode together with the [site_reviews] shortcode, make sure to set the same option value for both shortcodes.</p>
        <p>The default assigned_users value is: <code>""</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews_summary assigned_users="1,2"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_summary</span> <span class="attr-name">assigned_users</span>=<span class="attr-value">"1,2"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">class</p>
        <p>Include the "class" option to add custom CSS classes to the shortcode.</p>
        <p>The default class value is: <code>""</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews_summary class="my-reviews-summary full-width"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_summary</span> <span class="attr-name">class</span>=<span class="attr-value">"my-reviews-summary full-width"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">hide</p>
        <p>By default the shortcode displays all fields. Use the "hide" option to hide any specific fields you don't want to show. Include the "if_empty" value to hide the shortcode when there are no reviews to summarise. If all fields are hidden, the shortcode will not be displayed.</p>
        <p>The default hide value is: <code>""</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews_summary hide="bars,if_empty,rating,stars,summary"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_summary</span> <span class="attr-name">hide</span>=<span class="attr-value">"bars,if_empty,rating,stars,summary"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">id</p>
        <p>Include the "id" option to add a custom HTML id attribute to the shortcode.</p>
        <p>The default id value is: <code>""</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews_summary id="type-some-random-text-here"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_summary</span> <span class="attr-name">id</span>=<span class="attr-value">"type-some-random-text-here"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">labels</p>
        <p>The "labels" option allows you to enter custom labels for the percentage bar levels (from high to low), each level should be separated with a comma. However, rather than using this option to change the labels it's recommended to instead create a custom translation for them in the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-translations'); ?>">Settings &rarr; Translations</a></code> page.</p>
        <p>The default labels value is: <code>"Excellent,Very good,Average,Poor,Terrible"</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews_summary labels="5 star,4 star,3 star,2 star,1 star"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_summary</span> <span class="attr-name">labels</span>=<span class="attr-value">"5 star,4 star,3 star,2 star,1 star"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">rating</p>
        <p>Include the "rating" option to set the <em>minimum</em> star-rating of reviews to use.</p>
        <p><span class="required">Important:</span> If you are using this shortcode together with the [site_reviews] shortcode, make sure to set the same option value for both shortcodes.</p>
        <p>The default rating value is: <code>"1"</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews_summary rating="3"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_summary</span> <span class="attr-name">rating</span>=<span class="attr-value">"3"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">schema</p>
        <p>Include the "schema" option to enable the aggregate rating schema for your reviews in Google. The difference between this and the schema option in the [site_reviews] shortcode is that this one only generates the aggregate ratings schema, while the other generates both the aggregate ratings schema and the review schema for each individual review that is visible on the page. If you have the choice, enable this option on the [site_reviews] shortcode instead.</p>
        <p><span class="required">Important:</span> This option should only be used once on a page to avoid duplicate schemas; keep this in mind if you are using more than one [site_reviews] and/or [site_reviews_summary] shortcodes on the same page.</p>
        <p>The default schema value is: <code>"false"</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews_summary schema="true"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_summary</span> <span class="attr-name">schema</span>=<span class="attr-value">"true"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">text</p>
        <p>The "text" option allows you to change the summary text. Available template tags to use are, "{rating}" which represents the calculated average rating, "{max}" which represents the maximum star rating available, and "{num}" which represents the total number of reviews. However, rather than using this option to change the summary text it's recommended to instead create a custom translation for it in the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-translations'); ?>">Settings &rarr; Translations</a></code> page. That way, you will be able to customize both the singular (1 review) and plural (2 reviews) summary texts.</p>
        <p>The default text value is: <code>"{rating} out of {max} stars (based on {num} reviews)"</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews_summary text="{num} customer reviews"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_summary</span> <span class="attr-name">text</span>=<span class="attr-value">"{num} customer reviews"</span><span class="tag">]</span></code></pre>
        </div>
    </div>
</div>
