<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="shortcode-site_reviews">
            <span class="title">Display the reviews</span>
            <span class="badge code">[site_reviews]</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="shortcode-site_reviews" class="inside">
        <h3>This shortcode displays your most recently submitted reviews.</h3>
        <div class="notice inline notice-info notice-alt">
            <p>Each example below demonstrates a different shortcode option. If you need to use multiple options, simply combine the options together (separated with a space) in the same shortcode.</p>
        </div>

        <p class="glsr-heading">assigned_posts</p>
        <p>Include the "assigned_posts" option to limit reviews to those assigned posts, pages, or other public post types. Accepted values are a numerical <a href="https://pagely.com/blog/find-post-id-wordpress/">WordPress Post ID</a>, <code>post_id</code> which automatically uses the ID of the current page, or <code>parent_id</code> which automatically uses the ID of the parent page. Separate multiple values with a comma.</p>
        <p><span class="required">Important:</span> If you are using this shortcode together with the [site_reviews_summary] shortcode, make sure to set the same option value for both shortcodes.</p>
        <p>The default assigned_posts value is: <code>""</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews assigned_posts="post_id"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews <span class="attr-name">assigned_posts</span>=<span class="attr-value">"post_id"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">assigned_terms</p>
        <p>Include the "assigned_terms" option to limit reviews to those assigned categories. Accepted values are either a category ID or slug. Separate multiple values with a comma.</p>
        <p><span class="required">Important:</span> If you are using this shortcode together with the [site_reviews_summary] shortcode, make sure to set the same option value for both shortcodes.</p>
        <p>The default assigned_terms value is: <code>""</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews assigned_terms="13,14"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews</span> <span class="attr-name">assigned_terms</span>=<span class="attr-value">"13,14"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">assigned_users</p>
        <p>Include the "assigned_users" option to limit reviews to those assigned users. Accepted values are either a user ID or username. Separate multiple values with a comma.</p>
        <p><span class="required">Important:</span> If you are using this shortcode together with the [site_reviews_summary] shortcode, make sure to set the same option value for both shortcodes.</p>
        <p>The default assigned_users value is: <code>""</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews assigned_users="1,2"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews</span> <span class="attr-name">assigned_users</span>=<span class="attr-value">"1,2"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">class</p>
        <p>Include the "class" option to add custom CSS classes to the shortcode.</p>
        <p>The default class value is: <code>""</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews class="my-reviews full-width"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews</span> <span class="attr-name">class</span>=<span class="attr-value">"my-reviews full-width"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">display</p>
        <p>Include the "display" option to change the number of reviews that are displayed.</p>
        <p>The default display value is: <code>"5"</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews display="20"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews</span> <span class="attr-name">display</span>=<span class="attr-value">"20"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">fallback</p>
        <p>Include the "fallback" option to change the text that is shown when there are no reviews to display. This option will override the default fallback text if the option is enabled in the plugin settings.</p>
        <p>The default fallback value is: <code>""</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews fallback="No reviews found."]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews</span> <span class="attr-name">fallback</span>=<span class="attr-value">"No reviews found."</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">hide</p>
        <p>Include the "hide" option to hide any specific fields you don't want to show. If all fields are hidden, the shortcode will not be displayed.</p>
        <p>The default hide value is: <code>""</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews hide="assigned_links,author,avatar,content,date,rating,response,title"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews</span> <span class="attr-name">hide</span>=<span class="attr-value">"assigned_links,author,avatar,content,date,rating,response,title"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">id</p>
        <p>Include the "id" option to add a custom HTML id attribute to the shortcode. This may be useful when using pagination with the ajax option.</p>
        <p>The default id value is: <code>""</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews id="type-some-random-text-here"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews</span> <span class="attr-name">id</span>=<span class="attr-value">"type-some-random-text-here"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">offset</p>
        <p>Include the "offset" option to displace or pass over a number of reviews. For example, [site_reviews&nbsp;display=5&nbsp;offset=2] will show 5 reviews, skipping the first two. It is NOT recommended to use this option with pagination enabled.</p>
        <p>The default offset value is: <code>"0"</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews offset="1"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews</span> <span class="attr-name">offset</span>=<span class="attr-value">"1"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">pagination</p>
        <p>Include the "pagination" option to display reviews in multiple pages (i.e. Page 1, Page 2, etc.). The value can be "true", "ajax", or "false". The "ajax" value allows you to load the next page of reviews without loading a new page. When using pagination, only one [site_reviews] shortcode can be used on a page at a time.</p>
        <p>The default pagination value is: <code>"false"</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews pagination="ajax"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews</span> <span class="attr-name">pagination</span>=<span class="attr-value">"ajax"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">rating</p>
        <p>Include the "rating" option to set the <em>minimum</em> star-rating of reviews to display. By default, the shortcode displays all 1-5 star reviews.</p>
        <p><span class="required">Important:</span> If you are using this shortcode together with the [site_reviews_summary] shortcode, make sure to set the same option value for both shortcodes.</p>
        <p>The default rating value is: <code>"1"</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews rating="4"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews</span> <span class="attr-name">rating</span>=<span class="attr-value">"4"</span><span class="tag">]</span></code></pre>
        </div>

        <p class="glsr-heading">schema</p>
        <p>Include the "schema" option to enable the aggregate rating and review schema for your reviews in Google. The difference between this and the schema option in the [site_reviews_summary] shortcode is that this one generates both the aggregate ratings schema and the review schema for each individual review that is visible on the page, while the other only generates the aggregate ratings schema.</p>
        <p><span class="required">Important:</span> This option should only be used once on a page to avoid duplicate schemas; keep this in mind if you are using more than one [site_reviews] and/or [site_reviews_summary] shortcodes on the same page.</p>
        <p>The default schema value is: <code>"false"</code></p>
        <div class="shortcode-example">
            <input type="text" readonly class="code" value='[site_reviews schema="true"]'>
            <pre><code class="syntax-shortcode"><span class="tag">[site_reviews</span> <span class="attr-name">schema</span>=<span class="attr-value">"true"</span><span class="tag">]</span></code></pre>
        </div>
    </div>
</div>
