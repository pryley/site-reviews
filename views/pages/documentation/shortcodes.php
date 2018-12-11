<?php defined( 'WPINC' ) || die; ?>

<div id="shortcodes-01" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>[site_reviews]</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>This shortcode displays your most recently submitted reviews.</p>

		<p class="glsr-heading">assigned_to</p>
		<p>Include the "assigned_to" attribute to limit reviews to those assigned to a specific <a href="https://pagely.com/blog/find-post-id-wordpress/">page/post ID</a>. Accepted values are one or more post/page ID's (separated by commas). You can also use <code>post_id</code> which will automatically use the ID of the current page.</p>
		<p>The default assigned_to value is: <code>""</code></p>
		<pre><code>[site_reviews assigned_to="post_id"]</code></pre>

		<p class="glsr-heading">category</p>
		<p>Include the "category" attribute to limit reviews to one or more categories. Separate multiple category IDs or slugs with a comma. Accepted values are either a category ID or slug.</p>
		<p>The default category value is: <code>""</code></p>
		<pre><code>[site_reviews category="13,14"]</code></pre>

		<p class="glsr-heading">class</p>
		<p>Include the "class" attribute to add custom CSS classes to the shortcode.</p>
		<p>The default class value is: <code>""</code></p>
		<pre><code>[site_reviews class="my-reviews full-width"]</code></pre>

		<p class="glsr-heading">count</p>
		<p>Include the "count" attribute to change the number of reviews that are displayed.</p>
		<p>The default count value is: <code>"10"</code></p>
		<pre><code>[site_reviews count="20"]</code></pre>

		<p class="glsr-heading">hide</p>
		<p>Include the "hide" attribute to hide any specific fields you don't want to show. If all fields are hidden, the shortcode will not be displayed.</p>
		<p>The default hide value is: <code>""</code></p>
		<pre><code>[site_reviews hide="assigned_to,author,avatar,content,date,rating,response,title"]</code></pre>

		<p class="glsr-heading">id</p>
		<p>Include the "id" attribute to add a custom HTML id attribute to the shortcode. This is especially useful when using pagination with the ajax option.</p>
		<p>The default id value is: <code>""</code></p>
		<pre><code>[site_reviews id="type-some-random-text-here"]</code></pre>

		<p class="glsr-heading">offset</p>
		<p>Include the "offset" attribute to displace or pass over a number of reviews. For example, [site_reviews&nbsp;count=5&nbsp;offset=2] will show 5 reviews, skipping the first two. It is NOT recommended to use this option with pagination enabled.</p>
		<p>The default offset value is: <code>"0"</code></p>
		<pre><code>[site_reviews offset="1"]</code></pre>

		<p class="glsr-heading">pagination</p>
		<p>Include the "pagination" attribute to display reviews in multiple pages (i.e. Page 1, Page 2, etc.). The value can be "true", "ajax", or "false". The "ajax" value allows you to load the next page of reviews without loading a new page. When using pagination, only one [site_reviews] shortcode can be used on a page at a time.</p>
		<p>The default pagination value is: <code>"false"</code></p>
		<pre><code>[site_reviews pagination="ajax"]</code></pre>

		<p class="glsr-heading">rating</p>
		<p>Include the "rating" attribute to set the <em>minimum</em> star-rating of reviews to display. By default, the shortcode displays all 1-5 star reviews.</p>
		<p>The default rating value is: <code>"1"</code></p>
		<pre><code>[site_reviews rating="4"]</code></pre>

		<p class="glsr-heading">schema</p>
		<p>Include the "schema" attribute to enable rich snippets for your reviews, this is disabled by default. The difference between this and the schema option in the [site_reviews_summary] shortcode is that this one generates both the overall reviews rating schema and the schema for each individual review, while the other only generates the overall reviews rating schema.</p>
		<p>The default schema value is: <code>"false"</code></p>
		<p><span class="required">Important:</span> This attribute should only be used once on a page to avoid duplicate schemas; keep that in mind if you are using more than one [site_reviews] and/or [site_reviews_summary] shortcodes on the same page.</p>
		<pre><code>[site_reviews schema="true"]</code></pre>

		<p class="glsr-heading">title</p>
		<p>Include the "title" attribute to display a custom shortcode heading.</p>
		<p>The default title value is: <code>""</code></p>
		<pre><code>[site_reviews title="Our Reviews"]</code></pre>
	</div>
</div>

<div id="shortcodes-02" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>[site_reviews_form]</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>This shortcode displays the review submission form.</p>

		<p class="glsr-heading">assign_to</p>
		<p>Include the "assign_to" attribute to automatically assign submitted reviews to a specific page or post. Accepted values are one or more <a href="https://pagely.com/blog/find-post-id-wordpress/">page/post ID's</a> (separated by commas). You can also use <code>post_id</code> which will automatically assign reviews to the current page.</p>
		<p>The default assign_to value is: <code>""</code></p>
		<pre><code>[site_reviews_form assign_to="post_id"]</code></pre>

		<p class="glsr-heading">category</p>
		<p>Include the "category" attribute to automatically assign one or more categories to the submitted review. Accepted values are either a category ID or slug.</p>
		<p>The default category value is: <code>""</code></p>
		<pre><code>[site_reviews_form category="13,14"]</code></pre>

		<p class="glsr-heading">class</p>
		<p>Include the "class" attribute to add custom CSS classes to the shortcode form.</p>
		<p>The default class value is: <code>""</code></p>
		<pre><code>[site_reviews_form class="my-reviews-form full-width"]</code></pre>

		<p class="glsr-heading">description</p>
		<p>Include the "description" attribute to display a custom shortcode description.</p>
		<p>The default description value is: <code>""</code></p>
		<pre><code>[site_reviews_form description="Required fields are marked with an asterisk (*)"]</code></pre>

		<p class="glsr-heading">hide</p>
		<p>Include the "hide" attribute to exclude certain fields in the form. If all fields are hidden, the shortcode will not be displayed.</p>
		<p>The default hide value is: <code>""</code></p>
		<pre><code>[site_reviews_form hide="content,email,name,rating,terms,title"]</code></pre>

		<p class="glsr-heading">id</p>
		<p>This shortcode should only be used on a page once. However, if for any reason you need to include more than one on a page, add the "id" attribute to each with some random text to make it a unique shortcode form.</p>
		<p>The default id value is: <code>""</code></p>
		<pre><code>[site_reviews_form id="type-some-random-text-here"]</code></pre>

		<p class="glsr-heading">title</p>
		<p>Include the "title" attribute to display a custom shortcode heading.</p>
		<p>The default title value is: <code>""</code></p>
		<pre><code>[site_reviews_form title="Submit a Review"]</code></pre>
	</div>
</div>

<div id="shortcodes-03" class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>[site_reviews_summary]</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>This shortcode displays a summary of your reviews.</p>

		<p class="glsr-heading">assigned_to</p>
		<p>Include the "assigned_to" attribute to limit the reviews used to calculate the average rating to those assigned to a specific <a href="https://pagely.com/blog/find-post-id-wordpress/">page/post ID</a>. Accepted values are one or more post/page ID's (separated by commas). You can also use <code>post_id</code> which will automatically use the ID of the current page.</p>
		<p><span class="required">Important:</span> If you are using this shortcode together with the [site_reviews] shortcode, make sure you set this attribute value the same for both shortcodes.</p>
		<p>The default assigned_to value is: <code>""</code></p>
		<pre><code>[site_reviews_summary assigned_to="post_id"]</code></pre>

		<p class="glsr-heading">category</p>
		<p>Include the "category" attribute to limit the reviews used to calculate the average rating to one or more categories. Separate multiple category IDs or slugs with a comma. Accepted values are either a category ID or slug.</p>
		<p><span class="required">Important:</span> If you are using this shortcode together with the [site_reviews] shortcode, make sure you set this attribute value the same for both shortcodes.</p>
		<p>The default category value is: <code>""</code></p>
		<pre><code>[site_reviews_summary category="13,14"]</code></pre>

		<p class="glsr-heading">class</p>
		<p>Include the "class" attribute to add custom CSS classes to the shortcode.</p>
		<p>The default class value is: <code>""</code></p>
		<pre><code>[site_reviews_summary class="my-reviews-summary full-width"]</code></pre>

		<p class="glsr-heading">hide</p>
		<p>By default the shortcode displays all fields. Use the "hide" attribute to hide any specific fields you don't want to show. Include the "if_empty" value to hide the shortcode when there are no reviews to summarise. If all fields are hidden, the shortcode will not be displayed.</p>
		<p>The default hide value is: <code>""</code></p>
		<pre><code>[site_reviews_summary hide="bars,if_empty,rating,stars,summary"]</code></pre>

		<p class="glsr-heading">labels</p>
		<p>The "labels" attribute allows you to enter custom labels for the percentage bar levels (from high to low), each level should be separated with a comma. However, rather than using this attribute to change the labels it's recommended to instead create a custom translation for them in the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=settings#!translations' ); ?>">Settings &rarr; Translations</a></code> page.</p>
		<p>The default labels value is: <code>"Excellent,Very good,Average,Poor,Terrible"</code></p>
		<pre><code>[site_reviews_summary labels="5 star,4 star,3 star,2 star,1 star"]</code></pre>

		<p class="glsr-heading">rating</p>
		<p>Include the "rating" attribute to set the <em>minimum</em> star-rating of reviews to use.</p>
		<p>The default rating value is: <code>"1"</code></p>
		<p><span class="required">Important:</span> If you are using this shortcode together with the [site_reviews] shortcode, make sure you set this attribute value the same for both shortcodes.</p>
		<pre><code>[site_reviews_summary rating="3"]</code></pre>

		<p class="glsr-heading">schema</p>
		<p>Include the "schema" attribute to enable rich snippets for your reviews (this is disabled by default). The difference between this and the schema option in the [site_reviews] shortcode is that this one only generates the overall reviews rating schema, while the other generates both the overall reviews rating schema and the schema for each individual review. If you have the choice, better to enable this attribute on the [site_reviews] shortcode instead.</p>
		<p>The default schema value is: <code>"false"</code></p>
		<p><span class="required">Important:</span> This attribute should only be used once on a page to avoid duplicate schemas; keep that in mind if you are using more than one [site_reviews] and/or [site_reviews_summary] shortcodes on the same page.</p>
		<pre><code>[site_reviews_summary schema="true"]</code></pre>

		<p class="glsr-heading">text</p>
		<p>The "text" attribute allows you to change the summary text. Available template tags to use are, "{rating}" which represents the calculated average rating, "{max}" which represents the maximum star rating available, and "{num}" which represents the total number of reviews. However, rather than using this attribute to change the summary text it's recommended to instead create a custom translation for it in the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=settings#!translations' ); ?>">Settings &rarr; Translations</a></code> page. That way, you will be able to customize both the singular (1 review) and plural (2 reviews) summary texts.</p>
		<p>The default text value is: <code>"{rating} out of {max} stars (based on {num} reviews)"</code></p>
		<pre><code>[site_reviews_summary text="{num} customer reviews"]</code></pre>

		<p class="glsr-heading">title</p>
		<p>Include the "title" attribute to display a custom shortcode heading.</p>
		<p>The default title value is: <code>""</code></p>
		<pre><code>[site_reviews_summary title="Overall Rating"]</code></pre>
	</div>
</div>
